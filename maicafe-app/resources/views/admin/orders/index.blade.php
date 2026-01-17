@extends('layouts.admin')

@section('title', 'Orders - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Orders</h1>
</div>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>£{{ number_format($order->total, 2) }}</td>
                <td>
                    <span class="badge badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td>{{ $order->created_at->format('M d, Y') }}</td>
                <td>
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn" style="padding: 6px 12px; background: #f3f4f6;">
                        <i class="fas fa-eye"></i> View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px; color: #6b7280;">No orders found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 20px;">
        {{ $orders->links() }}
    </div>
</div>
@endsection


