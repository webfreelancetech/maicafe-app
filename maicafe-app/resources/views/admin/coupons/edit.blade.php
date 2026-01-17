@extends('layouts.admin')

@section('title', 'Edit Coupon - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Edit Coupon</h1>
    <a class="btn" href="{{ route('admin.coupons.index') }}" style="background: #f3f4f6; color: #374151;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="code">Coupon Code*</label>
            <input type="text" name="code" id="code" value="{{ old('code', $coupon->code) }}" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="type">Type*</label>
                <select name="type" id="type" required>
                    <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>Percentage</option>
                    <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                </select>
            </div>

            <div class="form-group">
                <label for="value">Value*</label>
                <input type="number" step="0.01" name="value" id="value" value="{{ old('value', $coupon->value) }}" required min="0">
            </div>
        </div>

        <div class="form-group">
            <label for="usage_limit">Usage Limit</label>
            <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" min="1">
        </div>

        <div class="form-group">
            <div class="checkbox-group">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                <label for="is_active">Active</label>
            </div>
        </div>

        <div style="margin-top: 24px;">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Update Coupon
            </button>
        </div>
    </form>
</div>
@endsection


