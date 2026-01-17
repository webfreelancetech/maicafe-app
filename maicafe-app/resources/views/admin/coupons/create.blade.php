@extends('layouts.admin')

@section('title', 'Create Coupon - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Create Coupon</h1>
    <a class="btn" href="{{ route('admin.coupons.index') }}" style="background: #f3f4f6; color: #374151;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <form action="{{ route('admin.coupons.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="code">Coupon Code*</label>
            <input type="text" name="code" id="code" value="{{ old('code') }}" required>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="type">Type*</label>
                <select name="type" id="type" required>
                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                </select>
                @error('type')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="value">Value*</label>
                <input type="number" step="0.01" name="value" id="value" value="{{ old('value') }}" required min="0">
                @error('value')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="usage_limit">Usage Limit</label>
            <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit') }}" min="1">
        </div>

        <div class="form-group">
            <div class="checkbox-group">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active">Active</label>
            </div>
        </div>

        <div style="margin-top: 24px;">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Create Coupon
            </button>
        </div>
    </form>
</div>
@endsection


