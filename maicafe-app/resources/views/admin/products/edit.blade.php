@extends('layouts.admin')

@section('title', 'Edit Product - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Edit Product</h1>
    <a class="btn" href="{{ route('admin.products.index') }}" style="background: #f3f4f6; color: #374151;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="name">Product Name*</label>
            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required>
        </div>

        <div class="form-group">
            <label for="category_id">Category*</label>
            <select name="category_id" id="category_id" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }} ({{ ucfirst($category->type) }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4">{{ old('description', $product->description) }}</textarea>
        </div>

        <!-- Variants Toggle -->
        <div class="form-group" style="margin: 24px 0; padding: 16px; background: #f9fafb; border-radius: 8px;">
            <div class="checkbox-group">
                <input type="checkbox" name="has_variants" id="has_variants" value="1" {{ old('has_variants', $product->has_variants) ? 'checked' : '' }} onchange="toggleVariants()">
                <label for="has_variants" style="font-weight: 600;">This product has multiple sizes/variants</label>
            </div>
            <p style="margin: 8px 0 0 24px; color: #6b7280; font-size: 13px;">Enable this if your product comes in different sizes (e.g., Small, Medium, Large) with different prices.</p>
        </div>

        <!-- Standard Pricing (shown when no variants) -->
        <div id="standardPricing">
            <div class="form-grid">
                <div class="form-group">
                    <label for="price">Price*</label>
                    <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $product->price) }}" min="0">
                </div>

                <div class="form-group">
                    <label for="compare_price">Compare Price</label>
                    <input type="number" step="0.01" name="compare_price" id="compare_price" value="{{ old('compare_price', $product->compare_price) }}" min="0">
                </div>
            </div>

            <div class="form-group">
                <label for="stock_quantity">Stock Quantity</label>
                <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0">
            </div>
        </div>

        <!-- Variants Section (shown when has variants) -->
        <div id="variantsSection" style="display: none;">
            <div style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                <label style="font-weight: 600; font-size: 15px;">Product Variants</label>
                <button type="button" class="btn btn-primary" onclick="addVariant()" style="padding: 8px 16px;">
                    <i class="fas fa-plus"></i> Add Variant
                </button>
            </div>
            
            <div id="variantsList">
                <!-- Existing variants will be loaded here -->
            </div>
            
            @error('variants')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Add-ons & Extras Section -->
        @if($addonGroups->count() > 0)
        @php
            $selectedAddonGroups = old('addon_groups', $product->addonGroups->pluck('id')->toArray());
        @endphp
        <div style="margin: 24px 0; padding: 20px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px;">
            <label style="font-weight: 600; font-size: 15px; color: #166534; margin-bottom: 12px; display: block;">
                <i class="fas fa-puzzle-piece"></i> Add-ons & Extras
            </label>
            <p style="color: #15803d; font-size: 13px; margin-bottom: 16px;">Select which add-on groups should be available for this product.</p>
            
            <div class="addon-groups-list">
                @foreach($addonGroups as $group)
                <div class="addon-group-item" style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 16px; margin-bottom: 8px; display: flex; align-items: center; gap: 12px;">
                    <input type="checkbox" name="addon_groups[]" id="addon_group_{{ $group->id }}" value="{{ $group->id }}" 
                        {{ in_array($group->id, $selectedAddonGroups) ? 'checked' : '' }}
                        style="width: 18px; height: 18px; accent-color: #16a34a;">
                    <div style="flex: 1;">
                        <label for="addon_group_{{ $group->id }}" style="font-weight: 600; color: #1f2937; cursor: pointer; margin: 0;">
                            {{ $group->name }}
                        </label>
                        <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">
                            {{ $group->addons->count() }} options: 
                            {{ $group->addons->take(3)->pluck('name')->join(', ') }}{{ $group->addons->count() > 3 ? '...' : '' }}
                            @if($group->is_required)
                            <span style="background: #fef3c7; color: #92400e; padding: 2px 6px; border-radius: 4px; font-size: 10px; margin-left: 8px;">Required</span>
                            @endif
                        </div>
                    </div>
                    <span style="background: {{ $group->selection_type == 'single' ? '#fef3c7' : '#dbeafe' }}; color: {{ $group->selection_type == 'single' ? '#92400e' : '#1e40af' }}; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                        {{ $group->selection_type == 'single' ? 'Single' : 'Multiple' }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="form-group">
            <label for="image">Product Image</label>
            @if($product->image)
            <div style="margin-bottom: 12px;">
                <img src="{{ asset('storage/' . $product->image) }}" alt="Current image" style="max-width: 200px; border-radius: 8px;">
            </div>
            @endif
            <input type="file" name="image" id="image" accept="image/*">
        </div>

        <div class="form-grid">
            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    <label for="is_active">Active</label>
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                    <label for="is_featured">Featured</label>
                </div>
            </div>
        </div>

        <div style="margin-top: 24px;">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Update Product
            </button>
        </div>
    </form>
</div>

<style>
.variant-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 12px;
    position: relative;
}
.variant-card .variant-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}
.variant-card .variant-title {
    font-weight: 600;
    color: #374151;
}
.variant-card .remove-variant {
    background: #fee2e2;
    color: #dc2626;
    border: none;
    padding: 6px 10px;
    border-radius: 4px;
    cursor: pointer;
}
.variant-card .remove-variant:hover {
    background: #fecaca;
}
.variant-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
}
.variant-grid .form-group {
    margin-bottom: 0;
}
.variant-grid label {
    font-size: 12px;
    color: #6b7280;
}
.variant-grid input {
    padding: 8px 10px;
    font-size: 13px;
}
</style>

<script>
let variantIndex = 0;

// Existing variants data from server
const existingVariants = @json($product->variants ?? []);

function toggleVariants() {
    const hasVariants = document.getElementById('has_variants').checked;
    const standardPricing = document.getElementById('standardPricing');
    const variantsSection = document.getElementById('variantsSection');
    const priceInput = document.getElementById('price');
    
    if (hasVariants) {
        standardPricing.style.display = 'none';
        variantsSection.style.display = 'block';
        priceInput.removeAttribute('required');
        
        // Add variants if none exist
        if (document.querySelectorAll('.variant-card').length === 0) {
            if (existingVariants.length > 0) {
                // Load existing variants
                existingVariants.forEach(variant => {
                    addVariantWithData(variant);
                });
            } else {
                // Add default variants for new product with variants
                addVariant('Small');
                addVariant('Medium');
                addVariant('Large');
            }
        }
    } else {
        standardPricing.style.display = 'block';
        variantsSection.style.display = 'none';
        priceInput.setAttribute('required', 'required');
    }
}

function addVariant(defaultName = '') {
    addVariantWithData({
        id: null,
        name: defaultName,
        price: '',
        compare_price: '',
        stock_quantity: 0,
        sku: ''
    });
}

function addVariantWithData(variant) {
    const variantsList = document.getElementById('variantsList');
    const variantHtml = `
        <div class="variant-card" id="variant-${variantIndex}">
            <div class="variant-header">
                <span class="variant-title">Variant: ${variant.name || '#' + (variantIndex + 1)}</span>
                <button type="button" class="remove-variant" onclick="removeVariant(${variantIndex})">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
            <input type="hidden" name="variants[${variantIndex}][id]" value="${variant.id || ''}">
            <div class="variant-grid">
                <div class="form-group">
                    <label>Name*</label>
                    <input type="text" name="variants[${variantIndex}][name]" value="${variant.name || ''}" required placeholder="e.g., Small, Medium, Large">
                </div>
                <div class="form-group">
                    <label>Price*</label>
                    <input type="number" step="0.01" name="variants[${variantIndex}][price]" value="${variant.price || ''}" required min="0" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Compare Price</label>
                    <input type="number" step="0.01" name="variants[${variantIndex}][compare_price]" value="${variant.compare_price || ''}" min="0" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Stock</label>
                    <input type="number" name="variants[${variantIndex}][stock_quantity]" value="${variant.stock_quantity || 0}" min="0">
                </div>
                <div class="form-group">
                    <label>SKU</label>
                    <input type="text" name="variants[${variantIndex}][sku]" value="${variant.sku || ''}" placeholder="Optional">
                </div>
            </div>
        </div>
    `;
    variantsList.insertAdjacentHTML('beforeend', variantHtml);
    variantIndex++;
}

function removeVariant(index) {
    const variant = document.getElementById(`variant-${index}`);
    if (variant) {
        variant.remove();
    }
    
    // Ensure at least one variant exists when has_variants is checked
    if (document.querySelectorAll('.variant-card').length === 0 && document.getElementById('has_variants').checked) {
        addVariant();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleVariants();
});
</script>
@endsection


