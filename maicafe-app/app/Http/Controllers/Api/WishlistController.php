<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Get user's wishlist
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 15), 100);
        
        $wishlistItems = Wishlist::where('user_id', $request->user()->id)
            ->with(['product' => function ($query) {
                $query->with(['category', 'activeVariants']);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Filter out items where product no longer exists or is inactive
        $items = collect($wishlistItems->items())->filter(function ($item) {
            return $item->product && $item->product->is_active;
        })->map(function ($item) {
            return $this->formatWishlistItem($item);
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'wishlist' => $items,
                'pagination' => [
                    'current_page' => $wishlistItems->currentPage(),
                    'last_page' => $wishlistItems->lastPage(),
                    'per_page' => $wishlistItems->perPage(),
                    'total' => $wishlistItems->total(),
                    'has_more_pages' => $wishlistItems->hasMorePages(),
                ],
            ]
        ], 200);
    }

    /**
     * Add product to wishlist
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
        ]);

        $productId = $validated['product_id'];
        $userId = $request->user()->id;

        // Check if product exists and is active
        $product = Product::where('id', $productId)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or is not available',
            ], 404);
        }

        // Check if already in wishlist
        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in your wishlist',
                'data' => [
                    'wishlist_item' => $this->formatWishlistItem($existing->load('product.category', 'product.activeVariants')),
                ]
            ], 409); // Conflict
        }

        // Add to wishlist
        $wishlistItem = Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        $wishlistItem->load('product.category', 'product.activeVariants');

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist',
            'data' => [
                'wishlist_item' => $this->formatWishlistItem($wishlistItem),
            ]
        ], 201);
    }

    /**
     * Remove product from wishlist
     * 
     * @param Request $request
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $productId)
    {
        $userId = $request->user()->id;

        $wishlistItem = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if (!$wishlistItem) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in your wishlist',
            ], 404);
        }

        $wishlistItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist',
        ], 200);
    }

    /**
     * Toggle product in wishlist (add if not exists, remove if exists)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
        ]);

        $productId = $validated['product_id'];
        $userId = $request->user()->id;

        // Check if product exists and is active
        $product = Product::where('id', $productId)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or is not available',
            ], 404);
        }

        // Check if already in wishlist
        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            // Remove from wishlist
            $existing->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist',
                'data' => [
                    'is_in_wishlist' => false,
                    'product_id' => $productId,
                ]
            ], 200);
        }

        // Add to wishlist
        $wishlistItem = Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        $wishlistItem->load('product.category', 'product.activeVariants');

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist',
            'data' => [
                'is_in_wishlist' => true,
                'product_id' => $productId,
                'wishlist_item' => $this->formatWishlistItem($wishlistItem),
            ]
        ], 200);
    }

    /**
     * Check if product is in user's wishlist
     * 
     * @param Request $request
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Request $request, $productId)
    {
        $userId = $request->user()->id;

        $isInWishlist = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'product_id' => (int) $productId,
                'is_in_wishlist' => $isInWishlist,
            ]
        ], 200);
    }

    /**
     * Check multiple products at once
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkMultiple(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer',
        ]);

        $userId = $request->user()->id;
        $productIds = $validated['product_ids'];

        $wishlistProductIds = Wishlist::where('user_id', $userId)
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();

        $result = [];
        foreach ($productIds as $productId) {
            $result[$productId] = in_array($productId, $wishlistProductIds);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'wishlist_status' => $result,
            ]
        ], 200);
    }

    /**
     * Get wishlist count
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function count(Request $request)
    {
        $userId = $request->user()->id;

        $count = Wishlist::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'count' => $count,
            ]
        ], 200);
    }

    /**
     * Clear entire wishlist
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear(Request $request)
    {
        $userId = $request->user()->id;

        $deleted = Wishlist::where('user_id', $userId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist cleared',
            'data' => [
                'items_removed' => $deleted,
            ]
        ], 200);
    }

    /**
     * Format wishlist item for response
     * 
     * @param Wishlist $item
     * @return array
     */
    protected function formatWishlistItem(Wishlist $item)
    {
        $product = $item->product;
        
        $data = [
            'id' => $item->id,
            'added_at' => $item->created_at->toIso8601String(),
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'image' => $product->image ? url('storage/' . $product->image) : null,
                'is_featured' => $product->is_featured,
                'has_variants' => $product->has_variants,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                    'slug' => $product->category->slug,
                ] : null,
            ],
        ];

        // Add pricing info
        if ($product->has_variants && $product->activeVariants && $product->activeVariants->count() > 0) {
            $data['product']['price'] = null;
            $data['product']['min_price'] = $product->activeVariants->min('price');
            $data['product']['max_price'] = $product->activeVariants->max('price');
            $data['product']['price_range'] = $data['product']['min_price'] == $data['product']['max_price'] 
                ? '£' . number_format($data['product']['min_price'], 2)
                : '£' . number_format($data['product']['min_price'], 2) . ' - £' . number_format($data['product']['max_price'], 2);
        } else {
            $data['product']['price'] = $product->price;
            $data['product']['compare_price'] = $product->compare_price;
        }

        return $data;
    }
}
