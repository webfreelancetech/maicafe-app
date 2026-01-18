@extends('kitchen.layout')

@section('title', 'Live Orders')

@section('styles')
<style>
    .orders-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        height: calc(100vh - 180px);
    }
    
    .order-column {
        background: #16213e;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    
    .column-header {
        padding: 16px;
        font-weight: 700;
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .column-header .count {
        background: rgba(255,255,255,0.2);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 14px;
    }
    
    .column.pending .column-header { background: #f59e0b; color: #000; }
    .column.preparing .column-header { background: #3b82f6; }
    .column.ready .column-header { background: #10b981; }
    
    .column-content {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
    }
    
    .order-card {
        background: #1a1a2e;
        border-radius: 10px;
        margin-bottom: 12px;
        overflow: hidden;
        border: 2px solid transparent;
        transition: all 0.3s;
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    
    .column.pending .order-card { border-color: #f59e0b; }
    .column.preparing .order-card { border-color: #3b82f6; }
    .column.ready .order-card { border-color: #10b981; }
    
    .order-card-header {
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255,255,255,0.05);
    }
    
    .token-badge {
        font-size: 32px;
        font-weight: 800;
        letter-spacing: 2px;
    }
    
    .column.pending .token-badge { color: #f59e0b; }
    .column.preparing .token-badge { color: #3b82f6; }
    .column.ready .token-badge { color: #10b981; }
    
    .order-meta {
        text-align: right;
    }
    
    .order-type {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 4px;
        text-transform: uppercase;
        font-weight: 700;
    }
    
    .order-type.delivery { background: #3b82f6; }
    .order-type.pickup { background: #f59e0b; color: #000; }
    
    .order-time {
        font-size: 14px;
        color: #a0a0a0;
        margin-top: 4px;
        font-family: 'Courier New', monospace;
    }
    
    .order-items {
        padding: 12px 16px;
    }
    
    .order-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 10px 0;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .order-item:last-child {
        border-bottom: none;
    }
    
    .item-info {
        flex: 1;
    }
    
    .item-name {
        font-weight: 600;
        font-size: 15px;
        margin-bottom: 4px;
    }
    
    .item-qty {
        background: #e94560;
        color: #fff;
        padding: 4px 12px;
        border-radius: 6px;
        font-weight: 800;
        font-size: 16px;
        min-width: 40px;
        text-align: center;
    }
    
    .item-details {
        font-size: 12px;
        color: #a0a0a0;
    }
    
    .item-variant {
        color: #60a5fa;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-right: 8px;
    }
    
    .item-addons {
        color: #34d399;
        display: block;
        margin-top: 2px;
    }
    
    .order-actions {
        padding: 12px 16px;
        display: flex;
        gap: 8px;
    }
    
    .action-btn {
        flex: 1;
        padding: 14px;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .action-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .action-btn.next {
        background: #10b981;
        color: #fff;
    }
    
    .action-btn.next:hover:not(:disabled) {
        background: #059669;
        transform: translateY(-1px);
    }
    
    .action-btn.view {
        background: #0f3460;
        color: #fff;
        flex: 0 0 auto;
        padding: 14px 16px;
    }
    
    .action-btn.view:hover {
        background: #1e40af;
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
    
    /* Scrollbar */
    .column-content::-webkit-scrollbar {
        width: 6px;
    }
    
    .column-content::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.05);
    }
    
    .column-content::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.2);
        border-radius: 3px;
    }
    
    /* Loading overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    
    .loading-overlay.active {
        display: flex;
    }
    
    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #0f3460;
        border-top-color: #e94560;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Toast notification */
    .toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #10b981;
        color: #fff;
        padding: 16px 24px;
        border-radius: 10px;
        font-weight: 600;
        display: none;
        align-items: center;
        gap: 10px;
        z-index: 1001;
        animation: slideUp 0.3s ease-out;
    }
    
    .toast.show {
        display: flex;
    }
    
    .toast.error {
        background: #ef4444;
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection

@section('content')
<!-- Stats Bar -->
<div class="stats-bar">
    <div class="stat-item pending">
        <div class="stat-value" id="statPending">{{ $stats['pending'] }}</div>
        <div class="stat-label">New Orders</div>
    </div>
    <div class="stat-item preparing">
        <div class="stat-value" id="statPreparing">{{ $stats['preparing'] }}</div>
        <div class="stat-label">Preparing</div>
    </div>
    <div class="stat-item ready">
        <div class="stat-value" id="statReady">{{ $stats['ready'] }}</div>
        <div class="stat-label">Ready</div>
    </div>
    <div class="stat-item completed">
        <div class="stat-value" id="statCompleted">{{ $stats['completed'] }}</div>
        <div class="stat-label">Completed Today</div>
    </div>
</div>

<!-- Orders Grid -->
<div class="orders-grid">
    <!-- Pending/New Orders -->
    <div class="order-column column pending">
        <div class="column-header">
            <span><i class="fas fa-clock"></i> New Orders</span>
            <span class="count" id="countPending">{{ $pendingOrders->count() }}</span>
        </div>
        <div class="column-content" id="pendingOrders">
            @forelse($pendingOrders as $order)
                @include('kitchen._order_card', ['order' => $order, 'nextStatus' => 'preparing', 'nextLabel' => 'Start Preparing'])
            @empty
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No new orders</p>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Preparing -->
    <div class="order-column column preparing">
        <div class="column-header">
            <span><i class="fas fa-fire"></i> Preparing</span>
            <span class="count" id="countPreparing">{{ $preparingOrders->count() }}</span>
        </div>
        <div class="column-content" id="preparingOrders">
            @forelse($preparingOrders as $order)
                @include('kitchen._order_card', ['order' => $order, 'nextStatus' => 'ready', 'nextLabel' => 'Mark Ready'])
            @empty
                <div class="empty-state">
                    <i class="fas fa-fire"></i>
                    <p>No orders preparing</p>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Ready -->
    <div class="order-column column ready">
        <div class="column-header">
            <span><i class="fas fa-check-circle"></i> Ready</span>
            <span class="count" id="countReady">{{ $readyOrders->count() }}</span>
        </div>
        <div class="column-content" id="readyOrders">
            @forelse($readyOrders as $order)
                @include('kitchen._order_card', ['order' => $order, 'nextStatus' => 'completed', 'nextLabel' => 'Complete Order'])
            @empty
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>No orders ready</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<!-- Toast Notification -->
<div class="toast" id="toast">
    <i class="fas fa-check-circle"></i>
    <span id="toastMessage">Order updated!</span>
</div>
@endsection

@push('scripts')
<script>
    // Update order status using URL from data attribute
    function updateOrderStatusFromCard(button, newStatus) {
        const card = button.closest('.order-card');
        const url = card.dataset.updateUrl;
        
        if (button) button.disabled = true;
        document.getElementById('loadingOverlay').classList.add('active');
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Response error:', text);
                    throw new Error('Server error: ' + response.status);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast(`${data.order.formatted_token} - Status updated!`);
                // Refresh the page to update all columns
                setTimeout(() => location.reload(), 500);
            } else {
                showToast(data.message || 'Failed to update order', true);
                if (button) button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error: ' + error.message, true);
            if (button) button.disabled = false;
        })
        .finally(() => {
            document.getElementById('loadingOverlay').classList.remove('active');
        });
    }
    
    // Show toast notification
    function showToast(message, isError = false) {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        
        toastMessage.textContent = message;
        toast.classList.toggle('error', isError);
        toast.classList.add('show');
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
    
    // Auto-refresh every 30 seconds
    let refreshInterval = setInterval(() => {
        location.reload();
    }, 30000);
    
    // Play sound for new orders (optional)
    let lastPendingCount = {{ $stats['pending'] }};
    
    function checkNewOrders() {
        fetch('{{ route('kitchen.orders.data') }}')
            .then(response => response.json())
            .then(data => {
                if (data.stats.pending > lastPendingCount) {
                    // New order arrived - could play a sound here
                    lastPendingCount = data.stats.pending;
                }
            });
    }
</script>
@endpush
