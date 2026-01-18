@extends('layouts.admin')

@section('title', 'Order Details - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <div style="display: flex; align-items: center; gap: 16px;">
        @if($order->daily_token)
        <div style="background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); color: #fff; padding: 12px 20px; border-radius: 12px; text-align: center;">
            <div style="font-size: 28px; font-weight: 700; line-height: 1;">{{ $order->formatted_token }}</div>
            <div style="font-size: 11px; opacity: 0.8; margin-top: 2px;">{{ $order->token_date->format('M d') }}</div>
        </div>
        @endif
        <div>
            <h1 style="margin: 0;">Order #{{ $order->order_number }}</h1>
            <small style="color: #6b7280;">{{ $order->created_at->format('M d, Y H:i') }}</small>
        </div>
    </div>
    <a class="btn" href="{{ route('admin.orders.index') }}" style="background: #f3f4f6; color: #374151;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    <!-- Customer Information -->
    <div class="card">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-user" style="color: #3b82f6;"></i> Customer Information</h3>
        @if($order->user)
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 600; font-size: 18px;">
                    {{ strtoupper(substr($order->user->name, 0, 1)) }}
                </div>
                <div>
                    <strong style="font-size: 16px;">{{ $order->user->name }}</strong>
                    <br><small style="color: #6b7280;">Customer ID: #{{ $order->user->id }}</small>
                </div>
            </div>
            <p><i class="fas fa-envelope" style="color: #6b7280; width: 20px;"></i> {{ $order->user->email }}</p>
            @if($order->user->phone)
            <p><i class="fas fa-phone" style="color: #6b7280; width: 20px;"></i> {{ $order->user->phone }}</p>
            @endif
            @if($order->delivery_address)
            <p><i class="fas fa-map-marker-alt" style="color: #6b7280; width: 20px;"></i> {{ $order->delivery_address }}</p>
            @endif
        @else
            <p style="color: #9ca3af;">Guest customer</p>
        @endif
    </div>

    <!-- Order Information -->
    <div class="card">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-receipt" style="color: #3b82f6;"></i> Order Information</h3>
        
        @php
            $statusColors = [
                'pending' => 'badge-warning',
                'confirmed' => 'badge-info',
                'preparing' => 'badge-info',
                'ready' => 'badge-success',
                'out_for_delivery' => 'badge-info',
                'completed' => 'badge-success',
                'cancelled' => 'badge-danger',
            ];
        @endphp
        
        <p><strong>Status:</strong> 
            <span class="badge {{ $statusColors[$order->status] ?? 'badge-info' }}">
                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
            </span>
        </p>
        <p><strong>Order Type:</strong> 
            <span class="badge" style="background: {{ $order->order_type === 'delivery' ? '#dbeafe' : '#fef3c7' }}; color: {{ $order->order_type === 'delivery' ? '#1e40af' : '#92400e' }};">
                {{ ucfirst($order->order_type ?? 'N/A') }}
            </span>
        </p>
        <p><strong>Payment:</strong> {{ ucfirst($order->payment_method ?? 'N/A') }} 
            <span class="badge {{ $order->payment_status === 'paid' ? 'badge-success' : 'badge-warning' }}">
                {{ ucfirst($order->payment_status ?? 'pending') }}
            </span>
        </p>
        <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
        @if($order->notes)
        <p><strong>Notes:</strong> {{ $order->notes }}</p>
        @endif

        <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
            @csrf
            <div class="form-group" style="display: flex; gap: 12px; align-items: center; margin: 0;">
                <label for="status" style="margin: 0; font-weight: 600;">Update Status:</label>
                <select name="status" id="status" style="width: auto; padding: 8px 12px;">
                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                    <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready</option>
                    <option value="out_for_delivery" {{ $order->status === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="btn btn-primary" style="padding: 8px 16px;">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Order Items -->
<div class="card">
    <h3 style="margin-bottom: 16px;"><i class="fas fa-shopping-cart" style="color: #3b82f6;"></i> Order Items</h3>
    
    <div class="order-items-list">
        @foreach($order->items as $item)
        @php
            $customizations = $item->customizations ?? [];
            $variant = $customizations['variant'] ?? null;
            $addons = $customizations['addons'] ?? [];
        @endphp
        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 12px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <strong style="font-size: 16px;">{{ $item->product_name }}</strong>
                        <span style="background: #e5e7eb; color: #374151; padding: 2px 8px; border-radius: 4px; font-size: 12px;">
                            x{{ $item->quantity }}
                        </span>
                    </div>
                    
                    @if($variant)
                    <div style="margin-bottom: 8px;">
                        <span style="background: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 4px; font-size: 12px;">
                            <i class="fas fa-ruler"></i> Size: {{ $variant['name'] }} (£{{ number_format($variant['price'], 2) }})
                        </span>
                    </div>
                    @endif
                    
                    @if(count($addons) > 0)
                    <div style="margin-top: 8px;">
                        <strong style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 6px;">
                            <i class="fas fa-plus-circle"></i> Add-ons:
                        </strong>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                            @foreach($addons as $addon)
                            <span style="background: #f0fdf4; color: #166534; padding: 4px 10px; border-radius: 4px; font-size: 12px; border: 1px solid #bbf7d0;">
                                {{ $addon['addon_name'] }} (+£{{ number_format($addon['price'], 2) }})
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                
                <div style="text-align: right;">
                    <div style="font-size: 12px; color: #6b7280;">Unit Price</div>
                    <div style="font-weight: 600;">£{{ number_format($item->price, 2) }}</div>
                    <div style="font-size: 14px; color: #374151; margin-top: 8px;">
                        Subtotal: <strong>£{{ number_format($item->subtotal, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Order Summary -->
    <div style="background: #fff; border: 2px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-top: 16px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span>Subtotal:</span>
            <span>£{{ number_format($order->subtotal, 2) }}</span>
        </div>
        @if($order->tax > 0)
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span>Tax:</span>
            <span>£{{ number_format($order->tax, 2) }}</span>
        </div>
        @endif
        @if($order->delivery_charge > 0)
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span>Delivery Charge:</span>
            <span>£{{ number_format($order->delivery_charge, 2) }}</span>
        </div>
        @endif
        @if($order->discount > 0)
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; color: #16a34a;">
            <span>Discount:</span>
            <span>-£{{ number_format($order->discount, 2) }}</span>
        </div>
        @endif
        <div style="display: flex; justify-content: space-between; padding-top: 12px; border-top: 2px solid #e5e7eb; margin-top: 8px;">
            <strong style="font-size: 18px;">Total:</strong>
            <strong style="font-size: 18px; color: #3b82f6;">£{{ number_format($order->total, 2) }}</strong>
        </div>
    </div>
</div>
@endsection


