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
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
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
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4">{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="price">Price*</label>
                <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $product->price) }}" required min="0">
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
@endsection


