<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Get all active products
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        
        $query = Product::where('is_active', true)
            ->with(['activeVariants', 'category'])
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc');

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by featured
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'products' => collect($products->items())->map(function ($product) {
                    return $this->formatProduct($product);
                }),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ]
        ], 200);
    }

    /**
     * Get a single product with variants and addons
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['activeVariants', 'category', 'activeAddonGroups.activeAddons'])
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $this->formatProduct($product, true)
            ]
        ], 200);
    }

    /**
     * Format product data with variants
     *
     * @param \App\Models\Product $product
     * @param bool $detailed
     * @return array
     */
    protected function formatProduct($product, $detailed = false)
    {
        $data = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'image' => $product->image,
            'is_featured' => $product->is_featured,
            'has_variants' => $product->has_variants,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
        ];

        // Add additional details for single product view
        if ($detailed) {
            $data['gallery'] = $product->gallery;
            $data['customization_options'] = $product->customization_options;
            
            // Include addon groups with their options
            if ($product->relationLoaded('activeAddonGroups')) {
                $data['addon_groups'] = $product->activeAddonGroups->map(function ($group) {
                    return [
                        'id' => $group->id,
                        'name' => $group->name,
                        'description' => $group->description,
                        'selection_type' => $group->selection_type,
                        'is_required' => $group->is_required,
                        'min_selections' => $group->min_selections,
                        'max_selections' => $group->max_selections,
                        'addons' => $group->activeAddons->map(function ($addon) {
                            return [
                                'id' => $addon->id,
                                'name' => $addon->name,
                                'price' => $addon->price,
                            ];
                        }),
                    ];
                });
            }
        }

        if ($product->has_variants && $product->activeVariants->count() > 0) {
            // Product with variants
            $data['price'] = null;
            $data['compare_price'] = null;
            $data['min_price'] = $product->min_price;
            $data['max_price'] = $product->max_price;
            $data['price_range'] = $product->price_range;
            $data['variants'] = $product->activeVariants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'compare_price' => $variant->compare_price,
                    'stock_quantity' => $variant->stock_quantity,
                ];
            });
        } else {
            // Product without variants
            $data['price'] = $product->price;
            $data['compare_price'] = $product->compare_price;
            $data['stock_quantity'] = $product->stock_quantity;
            $data['sku'] = $product->sku;
            $data['variants'] = [];
        }

        return $data;
    }
}
