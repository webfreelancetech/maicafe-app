@extends('kitchen.layout')

@section('title', 'All Orders')

@section('styles')
<style>
    .filters-bar {
        background: #16213e;
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
        background: #0f3460;
        border: 2px solid #0f3460;
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
        border-color: #e94560;
    }
    
    .filter-btn {
        background: #e94560;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .filter-btn:hover {
        background: #c73659;
    }
    
    .orders-table {
        background: #16213e;
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
        background: #0f3460;
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
        color: #e94560;
    }
    
    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }
    
    .status-badge.pending { background: #fef3c7; color: #92400e; }
    .status-badge.confirmed { background: #dbeafe; color: #1e40af; }
    .status-badge.preparing { background: #e0e7ff; color: #3730a3; }
    .status-badge.ready { background: #d1fae5; color: #065f46; }
    .status-badge.out_for_delivery { background: #cffafe; color: #0e7490; }
    .status-badge.completed { background: #d1fae5; color: #065f46; }
    .status-badge.cancelled { background: #fee2e2; color: #991b1b; }
    
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
    
    .items-list {
        font-size: 13px;
        color: #a0a0a0;
    }
    
    .items-list .item {
        margin-bottom: 4px;
    }
    
    .items-list .item:last-child {
        margin-bottom: 0;
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
        background: #0f3460;
        color: #fff;
    }
    
    .action-buttons .btn-view:hover {
        background: #1e40af;
    }
    
    .action-buttons .btn-status {
        background: #10b981;
        color: #fff;
    }
    
    .action-buttons .btn-status:hover {
        background: #059669;
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
        background: #0f3460;
        color: #fff;
    }
    
    .pagination-wrapper a:hover {
        background: #e94560;
    }
    
    .pagination-wrapper span.bg-blue-50 {
        background: #e94560 !important;
        color: #fff !important;
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
    <form method="GET" action="{{ route('kitchen.orders') }}" class="filter-group">
        <label for="date">Date:</label>
        <input type="date" name="date" id="date" value="{{ $date }}" class="filter-input">
        
        <label for="status">Status:</label>
        <select name="status" id="status" class="filter-select">
            <option value="">All Statuses</option>
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="preparing" {{ $status === 'preparing' ? 'selected' : '' }}>Preparing</option>
            <option value="ready" {{ $status === 'ready' ? 'selected' : '' }}>Ready</option>
            <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                <th>Items</th>
                <th>Status</th>
                <th>Customer</th>
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
                    <div class="items-list">
                        @foreach($order->items->take(3) as $item)
                        <div class="item">
                            <strong>{{ $item->quantity }}x</strong> {{ Str::limit($item->product_name, 20) }}
                        </div>
                        @endforeach
                        @if($order->items->count() > 3)
                        <div class="item" style="color: #e94560;">+{{ $order->items->count() - 3 }} more</div>
                        @endif
                    </div>
                </td>
                <td>
                    <span class="status-badge {{ $order->status }}">
                        {{ str_replace('_', ' ', $order->status) }}
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
                    <div class="action-buttons">
                        <a href="{{ route('kitchen.orders.show', $order) }}" class="btn btn-view">
                            <i class="fas fa-eye"></i> View
                        </a>
                        @if(!in_array($order->status, ['completed', 'cancelled']))
                        <button class="btn btn-status" data-url="{{ route('kitchen.orders.updateStatus', $order) }}" data-status="{{ $order->status }}" onclick="quickStatusUpdate(this)">
                            <i class="fas fa-arrow-right"></i> Next
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
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
    function quickStatusUpdate(button) {
        const url = button.dataset.url;
        const currentStatus = button.dataset.status;
        
        const statusFlow = {
            'pending': 'preparing',
            'confirmed': 'preparing',
            'preparing': 'ready',
            'ready': 'completed',
            'out_for_delivery': 'completed'
        };
        
        const nextStatus = statusFlow[currentStatus];
        if (!nextStatus) return;
        
        button.disabled = true;
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: nextStatus })
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Response error:', text);
                    throw new Error('Server error');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to update order');
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating order status');
            button.disabled = false;
        });
    }
</script>
@endpush
