@extends('layouts.admin')

@section('title', 'Create Product - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Create Product</h1>
    <a class="btn" href="{{ route('admin.products.index') }}" style="background: #f3f4f6; color: #374151;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label for="name">Product Name*</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            @error('name')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="category_id">Category*</label>
            <select name="category_id" id="category_id" required>
                <option value="">Select a category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4">{{ old('description') }}</textarea>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="price">Price*</label>
                <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}" required min="0">
                @error('price')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="compare_price">Compare Price</label>
                <input type="number" step="0.01" name="compare_price" id="compare_price" value="{{ old('compare_price') }}" min="0">
                @error('compare_price')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="stock_quantity">Stock Quantity</label>
            <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0">
        </div>

        <div class="form-group">
            <label for="image">Product Image</label>
            <input type="file" name="image" id="image" accept="image/*">
        </div>

        <div class="form-grid">
            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active">Active</label>
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                    <label for="is_featured">Featured</label>
                </div>
            </div>
        </div>

        <div style="margin-top: 24px;">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Create Product
            </button>
        </div>
    </form>
</div>
@endsection


