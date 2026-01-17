@extends('layouts.admin')

@section('title', 'Coupons - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Coupons</h1>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Coupon
    </a>
</div>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Type</th>
                <th>Value</th>
                <th>Usage</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($coupons as $coupon)
            <tr>
                <td>{{ $coupon->code }}</td>
                <td>{{ ucfirst($coupon->type) }}</td>
                <td>{{ $coupon->type === 'percentage' ? $coupon->value . '%' : '£' . number_format($coupon->value, 2) }}</td>
                <td>{{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}</td>
                <td>
                    <span class="badge {{ $coupon->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn" style="padding: 6px 12px; background: #f3f4f6;">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="padding: 6px 12px;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px; color: #6b7280;">No coupons found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection


