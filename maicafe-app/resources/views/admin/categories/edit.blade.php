@extends('layouts.admin')

@section('title', 'Edit Category - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Edit Category</h1>
    <a class="btn" href="{{ route('admin.categories.index') }}" style="background: #f3f4f6; color: #374151;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="name">Category Name*</label>
            <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required>
        </div>

        <div class="form-group">
            <label for="image">Category Image</label>
            @if($category->image)
            <div style="margin-bottom: 12px;">
                <img src="{{ asset('storage/' . $category->image) }}" alt="Current image" style="max-width: 200px; border-radius: 8px;">
            </div>
            @endif
            <input type="file" name="image" id="image" accept="image/*">
            <small style="color: #6b7280; font-size: 12px; margin-top: 4px; display: block;">Leave empty to keep current image</small>
        </div>

        <div class="form-group">
            <div class="checkbox-group">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                <label for="is_active">Active</label>
            </div>
        </div>

        <div style="margin-top: 24px;">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Update Category
            </button>
        </div>
    </form>
</div>
@endsection


