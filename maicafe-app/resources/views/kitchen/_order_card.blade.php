<div class="order-card" data-order-id="{{ $order->id }}" data-update-url="{{ route('kitchen.orders.updateStatus', $order) }}">
    <div class="order-card-header">
        <span class="token-badge">{{ $order->formatted_token }}</span>
        <div class="order-meta">
            <span class="order-type {{ $order->order_type ?? 'pickup' }}">
                <i class="fas fa-{{ ($order->order_type ?? 'pickup') === 'delivery' ? 'motorcycle' : 'shopping-bag' }}"></i>
                {{ ucfirst($order->order_type ?? 'pickup') }}
            </span>
            <div class="order-time">
                <i class="fas fa-clock"></i> {{ $order->created_at->format('H:i') }}
            </div>
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
            <div class="item-info">
                <div class="item-name">
                    {{ $item->product_name }}
                    @if($item->product && $item->product->category)
                    @php $pType = $item->product->category->type; @endphp
                    <span style="display:inline-block; font-size: 10px; padding: 1px 6px; border-radius: 3px; margin-left: 4px; background: {{ $pType === 'restaurant' ? '#fef3c7' : '#dbeafe' }}; color: {{ $pType === 'restaurant' ? '#92400e' : '#1e40af' }}; font-weight: 600; vertical-align: middle;">
                        {{ ucfirst($pType) }}
                    </span>
                    @endif
                </div>
                @if($variant || count($addons) > 0)
                <div class="item-details">
                    @if($variant)
                    <span class="item-variant">
                        <i class="fas fa-ruler"></i> {{ $variant['name'] }}
                    </span>
                    @endif
                    @if(count($addons) > 0)
                    <span class="item-addons">
                        <i class="fas fa-plus-circle"></i> {{ implode(', ', array_column($addons, 'addon_name')) }}
                    </span>
                    @endif
                </div>
                @endif
            </div>
            <span class="item-qty">{{ $item->quantity }}</span>
        </div>
        @endforeach
    </div>
    
    <div class="order-actions">
        <a href="{{ route('kitchen.orders.show', $order) }}" class="action-btn view" title="View Details">
            <i class="fas fa-eye"></i>
        </a>
        <button class="action-btn next" onclick="updateOrderStatusFromCard(this, '{{ $nextStatus }}')">
            <i class="fas fa-arrow-right"></i> {{ $nextLabel }}
        </button>
    </div>
</div>
