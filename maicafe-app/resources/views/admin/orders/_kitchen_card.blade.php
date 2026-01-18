<div class="order-card" data-order-id="{{ $order->id }}">
    <div class="order-card-header">
        <span class="token-badge">{{ $order->formatted_token }}</span>
        <div class="order-meta">
            <span class="order-type {{ $order->order_type }}">{{ $order->order_type ?? 'pickup' }}</span>
            <div class="order-time">{{ $order->created_at->format('H:i') }}</div>
        </div>
    </div>
    
    <div class="order-items">
        @foreach($order->items as $item)
        @php
            $customizations = $item->customizations ?? [];
            $variant = $customizations['variant'] ?? null;
            $addons = $customizations['addons'] ?? [];
        @endphp
        <div class="order-item">
            <div>
                <div class="item-name">{{ $item->product_name }}</div>
                <div class="item-details">
                    @if($variant)
                    <span class="item-variant"><i class="fas fa-ruler"></i> {{ $variant['name'] }}</span>
                    @endif
                    @if(count($addons) > 0)
                    <span class="item-addons"><i class="fas fa-plus"></i> {{ implode(', ', array_column($addons, 'addon_name')) }}</span>
                    @endif
                </div>
            </div>
            <span class="item-qty">x{{ $item->quantity }}</span>
        </div>
        @endforeach
    </div>
    
    <div class="order-actions">
        <button class="action-btn next" onclick="updateOrderStatus({{ $order->id }}, '{{ $nextStatus }}')">
            <i class="fas fa-arrow-right"></i> {{ $nextLabel }}
        </button>
    </div>
</div>
