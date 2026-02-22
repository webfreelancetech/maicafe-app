@extends('counter.layout')

@section('title', 'Payment Queue')

@section('styles')
<style>
    .columns-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 16px;
        height: calc(100vh - 180px);
    }
    
    .column {
        background: #0d2137;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    
    .column-header {
        padding: 16px 20px;
        background: rgba(0,0,0,0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid;
    }
    
    .column.awaiting .column-header { border-color: #f59e0b; }
    .column.paid .column-header { border-color: #22c55e; }
    
    .column-title {
        font-size: 16px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .column.awaiting .column-title { color: #f59e0b; }
    .column.paid .column-title { color: #22c55e; }
    
    .column-count {
        background: rgba(255,255,255,0.1);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 700;
    }
    
    .column-body {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
    }
    
    .order-card {
        background: #1e3a5f;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        border-left: 4px solid;
        transition: all 0.2s;
    }
    
    .order-card:hover {
        transform: translateX(4px);
    }
    
    .order-card.awaiting { border-color: #f59e0b; }
    .order-card.paid { border-color: #22c55e; }
    
    .order-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }
    
    .order-token {
        font-size: 28px;
        font-weight: 900;
        color: #fff;
    }
    
    .order-total {
        font-size: 24px;
        font-weight: 800;
        color: #22c55e;
        background: rgba(34, 197, 94, 0.1);
        padding: 4px 12px;
        border-radius: 8px;
    }
    
    .order-meta {
        display: flex;
        gap: 12px;
        margin-bottom: 12px;
        font-size: 13px;
        color: #a0a0a0;
    }
    
    .order-meta span {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .order-customer {
        background: rgba(255,255,255,0.05);
        padding: 8px 12px;
        border-radius: 8px;
        margin-bottom: 12px;
    }
    
    .customer-name {
        font-weight: 600;
        font-size: 14px;
    }
    
    .customer-phone {
        font-size: 12px;
        color: #a0a0a0;
    }
    
    .order-items {
        margin-bottom: 16px;
    }
    
    .order-items h4 {
        font-size: 11px;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 8px;
        letter-spacing: 1px;
    }
    
    .item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        font-size: 13px;
    }
    
    .item-row:last-child {
        border-bottom: none;
    }
    
    .item-qty {
        background: #22c55e;
        color: #000;
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
        margin-right: 8px;
    }
    
    .item-name {
        flex: 1;
    }
    
    .item-price {
        color: #a0a0a0;
    }
    
    .order-actions {
        display: flex;
        gap: 8px;
    }
    
    .btn {
        flex: 1;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .btn-confirm {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: #fff;
    }
    
    .btn-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(34, 197, 94, 0.4);
    }
    
    .btn-cancel {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
    }
    
    .btn-cancel:hover {
        background: #ef4444;
        color: #fff;
    }
    
    .btn-view {
        background: rgba(59, 130, 246, 0.2);
        color: #3b82f6;
    }
    
    .btn-view:hover {
        background: #3b82f6;
        color: #fff;
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
    
    .empty-state p {
        font-size: 16px;
    }
    
    /* Loading overlay */
    .card-loading {
        position: relative;
        pointer-events: none;
    }
    
    .card-loading::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Date selector */
    .date-selector {
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .date-selector input {
        background: #0d2137;
        border: 2px solid #1e5f74;
        color: #fff;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 14px;
    }
    
    .date-selector input:focus {
        outline: none;
        border-color: #22c55e;
    }
    
    /* Stats currency */
    .stat-currency {
        font-size: 18px;
        opacity: 0.7;
    }
    
    /* Recently paid card (smaller) */
    .column.paid .order-card {
        padding: 12px;
    }
    
    .column.paid .order-token {
        font-size: 20px;
    }
    
    .column.paid .order-total {
        font-size: 16px;
    }
    
    .column.paid .order-items {
        display: none;
    }
    
    .column.paid .order-actions {
        display: none;
    }
    
    .paid-time {
        font-size: 12px;
        color: #22c55e;
        display: flex;
        align-items: center;
        gap: 4px;
    }
</style>
@endsection

@section('content')
<!-- Date Selector -->
<div class="date-selector">
    <label style="color: #a0a0a0;">Date:</label>
    <input type="date" id="dateSelector" value="{{ $date }}" onchange="changeDate(this.value)">
    @if($date !== now()->toDateString())
    <span style="color: #f59e0b; font-size: 14px;">
        <i class="fas fa-exclamation-triangle"></i> Viewing historical data
    </span>
    @endif
</div>

<!-- Stats Bar -->
<div class="stats-bar">
    <div class="stat-item awaiting">
        <div class="stat-value" id="statAwaiting">{{ $stats['awaiting_payment'] }}</div>
        <div class="stat-label">Awaiting Payment</div>
    </div>
    <div class="stat-item paid">
        <div class="stat-value" id="statPaid">{{ $stats['paid_today'] }}</div>
        <div class="stat-label">Paid Today</div>
    </div>
    <div class="stat-item collected">
        <div class="stat-value">
            <span class="stat-currency">£</span>
            <span id="statCollected">{{ number_format($stats['total_collected'], 2) }}</span>
        </div>
        <div class="stat-label">Total Collected</div>
    </div>
    <div class="stat-item pending">
        <div class="stat-value">
            <span class="stat-currency">£</span>
            <span id="statPending">{{ number_format($stats['pending_amount'], 2) }}</span>
        </div>
        <div class="stat-label">Pending Amount</div>
    </div>
</div>

<!-- Main Columns -->
<div class="columns-container">
    <!-- Awaiting Payment Column -->
    <div class="column awaiting">
        <div class="column-header">
            <div class="column-title">
                <i class="fas fa-clock"></i>
                Awaiting Payment
            </div>
            <span class="column-count" id="awaitingCount">{{ $awaitingPaymentOrders->count() }}</span>
        </div>
        <div class="column-body" id="awaitingPaymentList">
            @forelse($awaitingPaymentOrders as $order)
            @include('counter._order_card', ['order' => $order, 'type' => 'awaiting'])
            @empty
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <p>No orders awaiting payment</p>
            </div>
            @endforelse
        </div>
    </div>
    
    <!-- Recently Paid Column -->
    <div class="column paid">
        <div class="column-header">
            <div class="column-title">
                <i class="fas fa-check-circle"></i>
                Recently Paid
            </div>
            <span class="column-count">{{ $recentlyPaidOrders->count() }}</span>
        </div>
        <div class="column-body" id="recentlyPaidList">
            @forelse($recentlyPaidOrders as $order)
            @include('counter._order_card', ['order' => $order, 'type' => 'paid'])
            @empty
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No recent payments</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const baseUrl = '{{ url("/") }}';
    
    function changeDate(date) {
        window.location.href = '{{ route("counter.dashboard") }}?date=' + date;
    }
    
    function confirmPayment(orderId, button) {
        if (!confirm('Confirm payment received for this order?')) return;
        
        const card = button.closest('.order-card');
        card.classList.add('card-loading');
        button.disabled = true;
        
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
                // Show success notification
                showNotification('success', data.message);
                // Refresh the page to update lists
                setTimeout(() => location.reload(), 500);
            } else {
                showNotification('error', data.message || 'Failed to confirm payment');
                card.classList.remove('card-loading');
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Error confirming payment');
            card.classList.remove('card-loading');
            button.disabled = false;
        });
    }
    
    function cancelOrder(orderId, button) {
        if (!confirm('Are you sure you want to cancel this order? This cannot be undone.')) return;
        
        const card = button.closest('.order-card');
        card.classList.add('card-loading');
        button.disabled = true;
        
        fetch(`${baseUrl}/counter/orders/${orderId}/cancel`, {
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
                showNotification('success', data.message);
                setTimeout(() => location.reload(), 500);
            } else {
                showNotification('error', data.message || 'Failed to cancel order');
                card.classList.remove('card-loading');
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Error cancelling order');
            card.classList.remove('card-loading');
            button.disabled = false;
        });
    }
    
    function showNotification(type, message) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type}`;
        notification.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
        notification.style.position = 'fixed';
        notification.style.top = '80px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.minWidth = '300px';
        document.body.appendChild(notification);
        
        setTimeout(() => notification.remove(), 3000);
    }
    
    // Auto-refresh every 30 seconds
    setInterval(() => {
        refreshData();
    }, 30000);
    
    function refreshData() {
        const date = document.getElementById('dateSelector').value;
        
        fetch(`${baseUrl}/counter/dashboard/data?date=${date}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update stats
            document.getElementById('statAwaiting').textContent = data.stats.awaiting_payment;
            document.getElementById('statPaid').textContent = data.stats.paid_today;
            document.getElementById('statCollected').textContent = data.stats.total_collected.toFixed(2);
            document.getElementById('statPending').textContent = data.stats.pending_amount.toFixed(2);
            document.getElementById('awaitingCount').textContent = data.awaiting_payment.length;
            
            // Check if there are new orders awaiting payment
            const currentCount = document.querySelectorAll('#awaitingPaymentList .order-card').length;
            if (data.awaiting_payment.length > currentCount) {
                // Play notification sound or show alert
                showNotification('success', 'New order awaiting payment!');
            }
        })
        .catch(error => console.error('Refresh error:', error));
    }
</script>
@endpush
