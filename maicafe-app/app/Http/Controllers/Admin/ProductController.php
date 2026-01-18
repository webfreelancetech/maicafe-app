<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\HandlesImageUploads;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\AddonGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use HandlesImageUploads;
    public function index()
    {
        $products = Product::with(['category', 'variants'])->latest()->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $addonGroups = AddonGroup::where('is_active', true)->with('addons')->orderBy('name')->get();
        return view('admin.products.create', compact('categories', 'addonGroups'));
    }

    public function store(Request $request)
    {
        $hasVariants = $request->boolean('has_variants');

        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'has_variants' => 'boolean',
        ];

        // If no variants, require base price
        if (!$hasVariants) {
            $rules['price'] = 'required|numeric|min:0';
            $rules['compare_price'] = 'nullable|numeric|min:0';
            $rules['stock_quantity'] = 'nullable|integer|min:0';
        } else {
            // Validate variants
            $rules['variants'] = 'required|array|min:1';
            $rules['variants.*.name'] = 'required|string|max:255';
            $rules['variants.*.price'] = 'required|numeric|min:0';
            $rules['variants.*.compare_price'] = 'nullable|numeric|min:0';
            $rules['variants.*.stock_quantity'] = 'nullable|integer|min:0';
            $rules['variants.*.sku'] = 'nullable|string|max:100';
            $rules['variants.*.is_active'] = 'boolean';
        }

        $validated = $request->validate($rules);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['has_variants'] = $hasVariants;

        if ($request->hasFile('image')) {
            $validated['image'] = $this->storeImageWithSeoName(
                $request->file('image'),
                $validated['name'],
                'products'
            );
        }

        // Set default price for products with variants (use first variant price)
        if ($hasVariants) {
            $validated['price'] = $request->input('variants.0.price', 0);
        }

        DB::transaction(function () use ($validated, $request, $hasVariants) {
            $product = Product::create($validated);

            // Create variants if product has variants
            if ($hasVariants && $request->has('variants')) {
                foreach ($request->input('variants') as $index => $variantData) {
                    $product->variants()->create([
                        'name' => $variantData['name'],
                        'price' => $variantData['price'],
                        'compare_price' => $variantData['compare_price'] ?? null,
                        'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                        'sku' => $variantData['sku'] ?? null,
                        'is_active' => isset($variantData['is_active']) ? true : true,
                        'sort_order' => $index,
                    ]);
                }
            }

            // Sync addon groups
            if ($request->has('addon_groups')) {
                $addonGroupsData = [];
                foreach ($request->input('addon_groups') as $index => $groupId) {
                    $addonGroupsData[$groupId] = ['sort_order' => $index];
                }
                $product->addonGroups()->sync($addonGroupsData);
            } else {
                $product->addonGroups()->detach();
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $addonGroups = AddonGroup::where('is_active', true)->with('addons')->orderBy('name')->get();
        $product->load(['variants', 'addonGroups']);
        return view('admin.products.edit', compact('product', 'categories', 'addonGroups'));
    }

    public function update(Request $request, Product $product)
    {
        $hasVariants = $request->boolean('has_variants');

        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'has_variants' => 'boolean',
        ];

        // If no variants, require base price
        if (!$hasVariants) {
            $rules['price'] = 'required|numeric|min:0';
            $rules['compare_price'] = 'nullable|numeric|min:0';
            $rules['stock_quantity'] = 'nullable|integer|min:0';
        } else {
            // Validate variants
            $rules['variants'] = 'required|array|min:1';
            $rules['variants.*.name'] = 'required|string|max:255';
            $rules['variants.*.price'] = 'required|numeric|min:0';
            $rules['variants.*.compare_price'] = 'nullable|numeric|min:0';
            $rules['variants.*.stock_quantity'] = 'nullable|integer|min:0';
            $rules['variants.*.sku'] = 'nullable|string|max:100';
            $rules['variants.*.is_active'] = 'boolean';
        }

        $validated = $request->validate($rules);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['has_variants'] = $hasVariants;

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $this->storeImageWithSeoName(
                $request->file('image'),
                $validated['name'],
                'products'
            );
        }

        // Set default price for products with variants
        if ($hasVariants) {
            $validated['price'] = $request->input('variants.0.price', 0);
        }

        DB::transaction(function () use ($validated, $request, $product, $hasVariants) {
            $product->update($validated);

            // Handle variants
            if ($hasVariants && $request->has('variants')) {
                // Get existing variant IDs from request
                $existingIds = collect($request->input('variants'))
                    ->pluck('id')
                    ->filter()
                    ->toArray();

                // Delete variants not in the request
                $product->variants()->whereNotIn('id', $existingIds)->delete();

                // Update or create variants
                foreach ($request->input('variants') as $index => $variantData) {
                    $product->variants()->updateOrCreate(
                        ['id' => $variantData['id'] ?? null],
                        [
                            'name' => $variantData['name'],
                            'price' => $variantData['price'],
                            'compare_price' => $variantData['compare_price'] ?? null,
                            'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                            'sku' => $variantData['sku'] ?? null,
                            'is_active' => isset($variantData['is_active']) ? true : true,
                            'sort_order' => $index,
                        ]
                    );
                }
            } else {
                // If switching from variants to no variants, delete all variants
                $product->variants()->delete();
            }

            // Sync addon groups
            if ($request->has('addon_groups')) {
                $addonGroupsData = [];
                foreach ($request->input('addon_groups') as $index => $groupId) {
                    $addonGroupsData[$groupId] = ['sort_order' => $index];
                }
                $product->addonGroups()->sync($addonGroupsData);
            } else {
                $product->addonGroups()->detach();
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        // Variants will be deleted automatically due to cascade
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }
}


