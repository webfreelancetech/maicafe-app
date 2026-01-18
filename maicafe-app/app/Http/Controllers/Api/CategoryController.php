<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all active categories
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Category::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc');

        // Optional: Include product count
        if ($request->boolean('with_count')) {
            $query->withCount(['products' => function ($q) {
                $q->where('is_active', true);
            }]);
        }

        $categories = $query->get();

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories->map(function ($category) use ($request) {
                    $data = [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'image' => $category->image,
                        'sort_order' => $category->sort_order,
                    ];

                    if ($request->boolean('with_count')) {
                        $data['products_count'] = $category->products_count;
                    }

                    return $data;
                })
            ]
        ], 200);
    }

    /**
     * Get a single category with its products
     *
     * @param string $slug
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug, Request $request)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        // Get products with pagination and variants
        $perPage = $request->input('per_page', 15);
        $products = $category->products()
            ->where('is_active', true)
            ->with(['activeVariants'])
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'image' => $category->image,
                ],
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
     * Format product data with variants
     *
     * @param \App\Models\Product $product
     * @return array
     */
    protected function formatProduct($product)
    {
        $data = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'image' => $product->image,
            'gallery' => $product->gallery,
            'is_featured' => $product->is_featured,
            'has_variants' => $product->has_variants,
            'customization_options' => $product->customization_options,
        ];

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
