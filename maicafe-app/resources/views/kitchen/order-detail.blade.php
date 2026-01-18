@extends('kitchen.layout')

@section('title', 'Order ' . $order->formatted_token)

@section('styles')
<style>
    .order-header {
        background: #16213e;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .order-token {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    
    .token-display {
        background: linear-gradient(135deg, #e94560 0%, #c73659 100%);
        color: #fff;
        padding: 20px 30px;
        border-radius: 12px;
        text-align: center;
    }
    
    .token-display .token {
        font-size: 48px;
        font-weight: 800;
        letter-spacing: 2px;
        line-height: 1;
    }
    
    .token-display .date {
        font-size: 14px;
        opacity: 0.8;
        margin-top: 4px;
    }
    
    .order-info h2 {
        font-size: 18px;
        margin-bottom: 8px;
    }
    
    .order-info .meta {
        display: flex;
        gap: 16px;
        color: #a0a0a0;
        font-size: 14px;
    }
    
    .order-info .meta span {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .status-section {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .current-status {
        text-align: center;
    }
    
    .current-status .label {
        font-size: 12px;
        color: #a0a0a0;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    
    .status-badge {
        display: inline-block;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
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
    
    .back-btn {
        background: #0f3460;
        color: #fff;
        padding: 12px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    
    .back-btn:hover {
        background: #1e40af;
    }
    
    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 16px;
    }
    
    .card {
        background: #16213e;
        border-radius: 12px;
        padding: 24px;
    }
    
    .card-title {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #fff;
    }
    
    .card-title i {
        color: #e94560;
    }
    
    /* Order Items */
    .order-item {
        background: #1a1a2e;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 12px;
        border-left: 4px solid #e94560;
    }
    
    .order-item:last-child {
        margin-bottom: 0;
    }
    
    .item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
    }
    
    .item-name {
        font-size: 18px;
        font-weight: 700;
    }
    
    .item-qty {
        background: #e94560;
        color: #fff;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 18px;
    }
    
    .item-customizations {
        margin-top: 12px;
    }
    
    .customization-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #a0a0a0;
        margin-bottom: 6px;
    }
    
    .customization-item.variant {
        color: #60a5fa;
    }
    
    .customization-item.addon {
        color: #34d399;
    }
    
    .customization-item i {
        width: 16px;
    }
    
    /* Customer Info */
    .customer-info {
        margin-bottom: 20px;
    }
    
    .info-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        font-size: 14px;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-row i {
        color: #a0a0a0;
        width: 20px;
    }
    
    .info-row .label {
        color: #a0a0a0;
        min-width: 80px;
    }
    
    /* Status Actions */
    .status-actions {
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid rgba(255,255,255,0.1);
    }
    
    .status-actions .label {
        font-size: 12px;
        color: #a0a0a0;
        text-transform: uppercase;
        margin-bottom: 12px;
    }
    
    .status-buttons {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .status-btn {
        padding: 14px 20px;
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
        width: 100%;
    }
    
    .status-btn:hover {
        transform: translateY(-1px);
    }
    
    .status-btn.preparing {
        background: #3b82f6;
        color: #fff;
    }
    
    .status-btn.ready {
        background: #10b981;
        color: #fff;
    }
    
    .status-btn.completed {
        background: #6b7280;
        color: #fff;
    }
    
    .status-btn.cancelled {
        background: transparent;
        border: 2px solid #ef4444;
        color: #ef4444;
    }
    
    .status-btn.cancelled:hover {
        background: #ef4444;
        color: #fff;
    }
    
    .status-btn:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }
    
    .type-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }
    
    .type-badge.delivery { background: #3b82f6; color: #fff; }
    .type-badge.pickup { background: #f59e0b; color: #000; }
</style>
@endsection

@section('content')
<!-- Order Header -->
<div class="order-header">
    <div class="order-token">
        <a href="{{ route('kitchen.orders') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <div class="token-display">
            <div class="token">{{ $order->formatted_token }}</div>
            <div class="date">{{ $order->token_date->format('M d, Y') }}</div>
        </div>
        <div class="order-info">
            <h2>Order #{{ $order->order_number }}</h2>
            <div class="meta">
                <span><i class="fas fa-clock"></i> {{ $order->created_at->format('H:i') }}</span>
                <span class="type-badge {{ $order->order_type ?? 'pickup' }}">
                    <i class="fas fa-{{ ($order->order_type ?? 'pickup') === 'delivery' ? 'motorcycle' : 'shopping-bag' }}"></i>
                    {{ ucfirst($order->order_type ?? 'pickup') }}
                </span>
            </div>
        </div>
    </div>
    
    <div class="status-section">
        <div class="current-status">
            <div class="label">Current Status</div>
            <span class="status-badge {{ $order->status }}">
                {{ str_replace('_', ' ', $order->status) }}
            </span>
        </div>
    </div>
</div>

<div class="content-grid">
    <!-- Order Items -->
    <div class="card">
        <h3 class="card-title"><i class="fas fa-utensils"></i> Order Items</h3>
        
        @foreach($order->items as $item)
        @php
            $customizations = $item->customizations ?? [];
            $variant = $customizations['variant'] ?? null;
            $addons = $customizations['addons'] ?? [];
        @endphp
        <div class="order-item">
            <div class="item-header">
                <div class="item-name">{{ $item->product_name }}</div>
                <div class="item-qty">x{{ $item->quantity }}</div>
            </div>
            
            @if($variant || count($addons) > 0)
            <div class="item-customizations">
                @if($variant)
                <div class="customization-item variant">
                    <i class="fas fa-ruler"></i>
                    <strong>Size:</strong> {{ $variant['name'] }}
                </div>
                @endif
                
                @if(count($addons) > 0)
                <div class="customization-item addon">
                    <i class="fas fa-plus-circle"></i>
                    <strong>Add-ons:</strong> {{ implode(', ', array_column($addons, 'addon_name')) }}
                </div>
                @endif
            </div>
            @endif
        </div>
        @endforeach
        
        @if($order->notes)
        <div style="margin-top: 20px; padding: 16px; background: #1a1a2e; border-radius: 8px; border-left: 4px solid #f59e0b;">
            <div style="font-size: 12px; color: #f59e0b; text-transform: uppercase; margin-bottom: 8px;">
                <i class="fas fa-sticky-note"></i> Order Notes
            </div>
            <div style="color: #fff;">{{ $order->notes }}</div>
        </div>
        @endif
    </div>
    
    <!-- Sidebar -->
    <div>
        <!-- Customer Info -->
        <div class="card" style="margin-bottom: 16px;">
            <h3 class="card-title"><i class="fas fa-user"></i> Customer</h3>
            
            @if($order->user)
            <div class="customer-info">
                <div class="info-row">
                    <i class="fas fa-user"></i>
                    <span class="label">Name</span>
                    <strong>{{ $order->user->name }}</strong>
                </div>
                @if($order->user->phone)
                <div class="info-row">
                    <i class="fas fa-phone"></i>
                    <span class="label">Phone</span>
                    <strong>{{ $order->user->phone }}</strong>
                </div>
                @endif
                @if($order->delivery_address)
                <div class="info-row">
                    <i class="fas fa-map-marker-alt"></i>
                    <span class="label">Address</span>
                    <strong>{{ $order->delivery_address }}</strong>
                </div>
                @endif
            </div>
            @else
            <p style="color: #6b7280;">Guest customer</p>
            @endif
        </div>
        
        <!-- Status Update -->
        <div class="card">
            <h3 class="card-title"><i class="fas fa-tasks"></i> Update Status</h3>
            
            <div class="status-buttons">
                @if(!in_array($order->status, ['completed', 'cancelled']))
                    @if(in_array($order->status, ['pending', 'confirmed']))
                    <button class="status-btn preparing" onclick="updateStatus('preparing')">
                        <i class="fas fa-fire"></i> Start Preparing
                    </button>
                    @endif
                    
                    @if($order->status === 'preparing')
                    <button class="status-btn ready" onclick="updateStatus('ready')">
                        <i class="fas fa-check"></i> Mark as Ready
                    </button>
                    @endif
                    
                    @if(in_array($order->status, ['ready', 'out_for_delivery']))
                    <button class="status-btn completed" onclick="updateStatus('completed')">
                        <i class="fas fa-flag-checkered"></i> Complete Order
                    </button>
                    @endif
                    
                    <button class="status-btn cancelled" onclick="updateStatus('cancelled')">
                        <i class="fas fa-times"></i> Cancel Order
                    </button>
                @else
                    <p style="color: #6b7280; text-align: center;">
                        Order is {{ $order->status }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const updateStatusUrl = '{{ route("kitchen.orders.updateStatus", $order) }}';
    
    function updateStatus(newStatus) {
        if (newStatus === 'cancelled' && !confirm('Are you sure you want to cancel this order?')) {
            return;
        }
        
        fetch(updateStatusUrl, {
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
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating order status');
        });
    }
</script>
@endpush
