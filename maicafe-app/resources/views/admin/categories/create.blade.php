@extends('layouts.admin')

@section('title', 'Create Category - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Create Category</h1>
    <a class="btn" href="{{ route('admin.categories.index') }}" style="background: #f3f4f6; color: #374151;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label for="name">Category Name*</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            @error('name')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="type">Category Type*</label>
            <select name="type" id="type" required>
                <option value="cafe" {{ old('type', 'cafe') === 'cafe' ? 'selected' : '' }}>Cafe</option>
                <option value="restaurant" {{ old('type') === 'restaurant' ? 'selected' : '' }}>Restaurant</option>
            </select>
            @error('type')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="image">Category Image*</label>
            <input type="file" name="image" id="image" accept="image/*" required>
            @error('image')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <div class="checkbox-group">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active">Active</label>
            </div>
        </div>

        <div style="margin-top: 24px;">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Create Category
            </button>
        </div>
    </form>
</div>
@endsection


