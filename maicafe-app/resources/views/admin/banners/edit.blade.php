@extends('layouts.admin')

@section('title', 'Edit Banner - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Edit Banner</h1>
    <a class="btn" href="{{ route('admin.banners.index') }}" style="background: #f3f4f6; color: #374151;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="title">Title*</label>
            <input type="text" name="title" id="title" value="{{ old('title', $banner->title) }}" required>
        </div>

        <div class="form-group">
            <label for="subtitle">Subtitle</label>
            <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle', $banner->subtitle) }}">
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="button_text">Button Text</label>
                <input type="text" name="button_text" id="button_text" value="{{ old('button_text', $banner->button_text) }}">
            </div>

            <div class="form-group">
                <label for="button_link">Button Link</label>
                <input type="text" name="button_link" id="button_link" value="{{ old('button_link', $banner->button_link) }}">
            </div>
        </div>

        <div class="form-group">
            <label for="image">Banner Image</label>
            @if($banner->image)
            <div style="margin-bottom: 12px;">
                <img src="{{ asset('storage/' . $banner->image) }}" alt="Current image" style="max-width: 300px; border-radius: 8px;">
            </div>
            @endif
            <input type="file" name="image" id="image" accept="image/*">
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="sort_order">Sort Order</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $banner->sort_order) }}" min="0">
                <small style="color: #6b7280; margin-top: 4px; display: block;">Lower numbers appear first. Banners with same order are sorted by date.</small>
            </div>

            <div class="form-group">
                <div class="checkbox-group" style="margin-top: 28px;">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                    <label for="is_active">Active</label>
                </div>
            </div>
        </div>

        <div style="margin-top: 24px;">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Update Banner
            </button>
        </div>
    </form>
</div>
@endsection


