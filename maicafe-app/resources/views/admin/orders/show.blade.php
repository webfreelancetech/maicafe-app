@extends('layouts.admin')

@section('title', 'Order Details - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Order #{{ $order->order_number }}</h1>
    <a class="btn" href="{{ route('admin.orders.index') }}" style="background: #f3f4f6; color: #374151;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <h3>Order Information</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
        <div>
            <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
            <p><strong>Email:</strong> {{ $order->customer_email }}</p>
            <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
        </div>
        <div>
            <p><strong>Status:</strong> 
                <span class="badge badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                    {{ ucfirst($order->status) }}
                </span>
            </p>
            <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
        </div>
    </div>

    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" style="margin-top: 20px;">
        @csrf
        <div class="form-group" style="display: flex; gap: 12px; align-items: center;">
            <label for="status" style="margin: 0;">Update Status:</label>
            <select name="status" id="status" style="width: auto;">
                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>
</div>

<div class="card">
    <h3>Order Items</h3>
    <table class="data-table" style="margin-top: 20px;">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>£{{ number_format($item->price, 2) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>£{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right; font-weight: 600;">Total:</td>
                <td style="font-weight: 600;">£{{ number_format($order->total, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection


