<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Get all active products with search, filter, and pagination
     *
     * Query Parameters:
     * - search: string (searches in name and description)
     * - category_id: int (filter by category ID)
     * - category: string (filter by category slug)
     * - featured: boolean (filter featured products)
     * - min_price: float (minimum price filter)
     * - max_price: float (maximum price filter)
     * - has_variants: boolean (filter products with/without variants)
     * - in_stock: boolean (filter products in stock)
     * - sort: string (name, price_asc, price_desc, newest, oldest, popular)
     * - per_page: int (items per page, default 15, max 100)
     * - page: int (current page)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Validate and sanitize per_page
        $perPage = min((int) $request->input('per_page', 15), 100);
        $perPage = max($perPage, 1);
        
        $query = Product::where('is_active', true)
            ->with(['activeVariants', 'category']);

        // Search by name and description
        $search = $request->input('search') ?? $request->query('search');
        if (!empty($search)) {
            $searchTerm = trim($search);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by category ID
        $categoryId = $request->input('category_id') ?? $request->query('category_id');
        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        // Filter by category slug
        $categorySlug = $request->input('category') ?? $request->query('category');
        if (!empty($categorySlug)) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Filter by featured
        if ($request->has('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        // Filter by price range (for products without variants)
        if ($request->filled('min_price')) {
            $minPrice = (float) $request->input('min_price');
            $query->where(function ($q) use ($minPrice) {
                $q->where(function ($q2) use ($minPrice) {
                    // Products without variants
                    $q2->where('has_variants', false)
                       ->where('price', '>=', $minPrice);
                })->orWhere(function ($q2) use ($minPrice) {
                    // Products with variants - check if any variant meets the price
                    $q2->where('has_variants', true)
                       ->whereHas('activeVariants', function ($vq) use ($minPrice) {
                           $vq->where('price', '>=', $minPrice);
                       });
                });
            });
        }

        if ($request->filled('max_price')) {
            $maxPrice = (float) $request->input('max_price');
            $query->where(function ($q) use ($maxPrice) {
                $q->where(function ($q2) use ($maxPrice) {
                    $q2->where('has_variants', false)
                       ->where('price', '<=', $maxPrice);
                })->orWhere(function ($q2) use ($maxPrice) {
                    $q2->where('has_variants', true)
                       ->whereHas('activeVariants', function ($vq) use ($maxPrice) {
                           $vq->where('price', '<=', $maxPrice);
                       });
                });
            });
        }

        // Filter by has_variants
        if ($request->has('has_variants')) {
            $query->where('has_variants', $request->boolean('has_variants'));
        }

        // Filter by in stock
        if ($request->has('in_stock') && $request->boolean('in_stock')) {
            $query->where(function ($q) {
                $q->where(function ($q2) {
                    // Products without variants with stock
                    $q2->where('has_variants', false)
                       ->where('stock_quantity', '>', 0);
                })->orWhere(function ($q2) {
                    // Products with variants that have stock
                    $q2->where('has_variants', true)
                       ->whereHas('activeVariants', function ($vq) {
                           $vq->where('stock_quantity', '>', 0);
                       });
                });
            });
        }

        // Sorting
        $sort = $request->input('sort', 'default');
        switch ($sort) {
            case 'name':
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'popular':
                // Could be based on order count or views - for now, featured first
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('sort_order', 'asc');
                break;
            default:
                $query->orderBy('sort_order', 'asc')
                      ->orderBy('name', 'asc');
                break;
        }

        $products = $query->paginate($perPage);

        // Build applied filters for response
        $appliedFilters = [];
        if (!empty($search)) $appliedFilters['search'] = $search;
        if (!empty($categoryId)) $appliedFilters['category_id'] = $categoryId;
        if (!empty($categorySlug)) $appliedFilters['category'] = $categorySlug;
        if ($request->has('featured')) $appliedFilters['featured'] = $request->boolean('featured');
        if ($request->filled('min_price')) $appliedFilters['min_price'] = (float) $request->input('min_price');
        if ($request->filled('max_price')) $appliedFilters['max_price'] = (float) $request->input('max_price');
        if ($request->has('has_variants')) $appliedFilters['has_variants'] = $request->boolean('has_variants');
        if ($request->has('in_stock')) $appliedFilters['in_stock'] = $request->boolean('in_stock');
        if ($sort !== 'default') $appliedFilters['sort'] = $sort;

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
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                    'has_more_pages' => $products->hasMorePages(),
                ],
                'filters' => $appliedFilters,
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
