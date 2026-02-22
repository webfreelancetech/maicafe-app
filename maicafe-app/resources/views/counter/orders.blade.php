@extends('counter.layout')

@section('title', 'All Orders')

@section('styles')
<style>
    .filters-bar {
        background: #0d2137;
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .filter-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .filter-group label {
        font-size: 14px;
        color: #a0a0a0;
    }
    
    .filter-select,
    .filter-input {
        background: #1e5f74;
        border: 2px solid #1e5f74;
        color: #fff;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .filter-select:focus,
    .filter-input:focus {
        outline: none;
        border-color: #22c55e;
    }
    
    .filter-btn {
        background: #22c55e;
        color: #000;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .filter-btn:hover {
        background: #16a34a;
    }
    
    .orders-table {
        background: #0d2137;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .orders-table table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .orders-table th,
    .orders-table td {
        padding: 16px 20px;
        text-align: left;
    }
    
    .orders-table th {
        background: #1e5f74;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #a0a0a0;
    }
    
    .orders-table tr:not(:last-child) td {
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .orders-table tr:hover td {
        background: rgba(255,255,255,0.02);
    }
    
    .token-cell {
        font-size: 20px;
        font-weight: 800;
        color: #22c55e;
    }
    
    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }
    
    .status-badge.awaiting_payment { background: #fef3c7; color: #92400e; }
    .status-badge.pending { background: #fef3c7; color: #92400e; }
    .status-badge.confirmed { background: #dbeafe; color: #1e40af; }
    .status-badge.preparing { background: #e0e7ff; color: #3730a3; }
    .status-badge.ready { background: #d1fae5; color: #065f46; }
    .status-badge.out_for_delivery { background: #cffafe; color: #0e7490; }
    .status-badge.completed { background: #d1fae5; color: #065f46; }
    .status-badge.cancelled { background: #fee2e2; color: #991b1b; }
    
    .payment-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }
    
    .payment-badge.pending { background: #f59e0b; color: #000; }
    .payment-badge.paid { background: #22c55e; color: #000; }
    .payment-badge.refunded { background: #ef4444; color: #fff; }
    
    .type-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }
    
    .type-badge.delivery { background: #3b82f6; color: #fff; }
    .type-badge.pickup { background: #f59e0b; color: #000; }
    
    .total-cell {
        font-weight: 700;
        color: #22c55e;
    }
    
    .action-buttons {
        display: flex;
        gap: 8px;
    }
    
    .action-buttons .btn {
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
    }
    
    .action-buttons .btn-view {
        background: #1e5f74;
        color: #fff;
    }
    
    .action-buttons .btn-view:hover {
        background: #3b82f6;
    }
    
    .action-buttons .btn-confirm {
        background: #22c55e;
        color: #000;
    }
    
    .action-buttons .btn-confirm:hover {
        background: #16a34a;
    }
    
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        padding: 20px;
    }
    
    .pagination-wrapper nav > div:first-child {
        display: none;
    }
    
    .pagination-wrapper a,
    .pagination-wrapper span {
        padding: 8px 14px;
        margin: 0 2px;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
    }
    
    .pagination-wrapper a {
        background: #1e5f74;
        color: #fff;
    }
    
    .pagination-wrapper a:hover {
        background: #22c55e;
        color: #000;
    }
    
    .pagination-wrapper span.bg-blue-50 {
        background: #22c55e !important;
        color: #000 !important;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }
    
    .empty-state i {
        font-size: 64px;
        margin-bottom: 16px;
        opacity: 0.3;
    }
</style>
@endsection

@section('content')
<!-- Filters -->
<div class="filters-bar">
    <form method="GET" action="{{ route('counter.orders') }}" class="filter-group">
        <label for="date">Date:</label>
        <input type="date" name="date" id="date" value="{{ $date }}" class="filter-input">
        
        <label for="status">Order Status:</label>
        <select name="status" id="status" class="filter-select">
            <option value="">All Statuses</option>
            <option value="awaiting_payment" {{ $status === 'awaiting_payment' ? 'selected' : '' }}>Awaiting Payment</option>
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="preparing" {{ $status === 'preparing' ? 'selected' : '' }}>Preparing</option>
            <option value="ready" {{ $status === 'ready' ? 'selected' : '' }}>Ready</option>
            <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        
        <label for="payment_status">Payment:</label>
        <select name="payment_status" id="payment_status" class="filter-select">
            <option value="">All</option>
            <option value="pending" {{ $paymentStatus === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>Paid</option>
        </select>
        
        <button type="submit" class="filter-btn">
            <i class="fas fa-filter"></i> Filter
        </button>
    </form>
    
    <div>
        <span style="color: #a0a0a0; font-size: 14px;">
            Showing {{ $orders->count() }} of {{ $orders->total() }} orders
        </span>
    </div>
</div>

<!-- Orders Table -->
<div class="orders-table">
    <table>
        <thead>
            <tr>
                <th>Token</th>
                <th>Time</th>
                <th>Type</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>
                    <span class="token-cell">{{ $order->formatted_token }}</span>
                </td>
                <td>
                    <div style="font-weight: 600;">{{ $order->created_at->format('H:i') }}</div>
                    <div style="font-size: 12px; color: #a0a0a0;">{{ $order->created_at->format('M d') }}</div>
                </td>
                <td>
                    <span class="type-badge {{ $order->order_type ?? 'pickup' }}">
                        {{ ucfirst($order->order_type ?? 'pickup') }}
                    </span>
                </td>
                <td>
                    @if($order->user)
                    <div style="font-weight: 500;">{{ $order->user->name }}</div>
                    <div style="font-size: 12px; color: #a0a0a0;">{{ $order->user->phone ?? '-' }}</div>
                    @else
                    <span style="color: #6b7280;">Guest</span>
                    @endif
                </td>
                <td>
                    <span class="total-cell">£{{ number_format($order->total, 2) }}</span>
                </td>
                <td>
                    <span class="payment-badge {{ $order->payment_status }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                    <div style="font-size: 11px; color: #a0a0a0; margin-top: 4px;">
                        {{ str_replace('_', ' ', $order->payment_method) }}
                    </div>
                </td>
                <td>
                    <span class="status-badge {{ $order->status }}">
                        {{ str_replace('_', ' ', $order->status) }}
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('counter.orders.show', $order) }}" class="btn btn-view">
                            <i class="fas fa-eye"></i> View
                        </a>
                        @if($order->status === 'awaiting_payment' && $order->payment_method === 'pay_at_counter')
                        <button class="btn btn-confirm" onclick="confirmPayment({{ $order->id }}, this)">
                            <i class="fas fa-check"></i> Confirm
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No orders found</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($orders->hasPages())
    <div class="pagination-wrapper">
        {{ $orders->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    const baseUrl = '{{ url("/") }}';
    
    function confirmPayment(orderId, button) {
        if (!confirm('Confirm payment received for this order?')) return;
        
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        fetch(`${baseUrl}/counter/orders/${orderId}/confirm-payment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to confirm payment');
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-check"></i> Confirm';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error confirming payment');
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-check"></i> Confirm';
        });
    }
</script>
@endpush
