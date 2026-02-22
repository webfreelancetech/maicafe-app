<div class="order-card {{ $type }}" id="order-{{ $order->id }}">
    <div class="order-card-header">
        <div class="order-token">{{ $order->formatted_token }}</div>
        <div class="order-total">£{{ number_format($order->total, 2) }}</div>
    </div>
    
    <div class="order-meta">
        <span><i class="fas fa-clock"></i> {{ $order->created_at->format('H:i') }}</span>
        <span>
            <i class="fas fa-{{ $order->order_type === 'delivery' ? 'motorcycle' : 'shopping-bag' }}"></i>
            {{ ucfirst($order->order_type ?? 'pickup') }}
        </span>
        @if($type === 'paid' && $order->payment_confirmed_at)
        <span class="paid-time">
            <i class="fas fa-check"></i>
            Paid at {{ $order->payment_confirmed_at->format('H:i') }}
        </span>
        @endif
    </div>
    
    @if($order->user)
    <div class="order-customer">
        <div class="customer-name">{{ $order->user->name }}</div>
        @if($order->user->phone)
        <div class="customer-phone"><i class="fas fa-phone"></i> {{ $order->user->phone }}</div>
        @endif
    </div>
    @endif
    
    <div class="order-items">
        <h4>Order Items</h4>
        @foreach($order->items as $item)
        <div class="item-row">
            <div>
                <span class="item-qty">{{ $item->quantity }}</span>
                <span class="item-name">{{ $item->product_name }}</span>
                @if(!empty($item->customizations['variant']))
                <small style="color: #a0a0a0;"> - {{ $item->customizations['variant']['name'] ?? '' }}</small>
                @endif
            </div>
            <span class="item-price">£{{ number_format($item->subtotal, 2) }}</span>
        </div>
        @if(!empty($item->customizations['addons']))
        @foreach($item->customizations['addons'] as $addon)
        <div class="item-row" style="padding-left: 32px; font-size: 12px; color: #6b7280;">
            <div>+ {{ $addon['addon_name'] ?? $addon['name'] ?? 'Addon' }} @if(($addon['quantity'] ?? 1) > 1) x{{ $addon['quantity'] }} @endif</div>
            <span>£{{ number_format($addon['total'] ?? $addon['price'] ?? 0, 2) }}</span>
        </div>
        @endforeach
        @endif
        @endforeach
    </div>
    
    @if($type === 'awaiting')
    <div class="order-actions">
        <button class="btn btn-confirm" onclick="confirmPayment({{ $order->id }}, this)">
            <i class="fas fa-check"></i> Confirm Payment
        </button>
        <button class="btn btn-cancel" onclick="cancelOrder({{ $order->id }}, this)">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif
</div>
