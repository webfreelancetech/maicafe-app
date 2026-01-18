@extends('layouts.admin')

@section('title', $customer->name . ' - Customer Details')

@section('content')
<div class="page-header">
    <h1>Customer Details</h1>
    <a class="btn" href="{{ route('admin.customers.index') }}" style="background: #f3f4f6; color: #374151;">
        <i class="fas fa-arrow-left"></i> Back to Customers
    </a>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
    <!-- Customer Profile Card -->
    <div>
        <div class="card" style="text-align: center;">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 32px; margin: 0 auto 16px;">
                {{ strtoupper(substr($customer->name, 0, 1)) }}
            </div>
            <h2 style="margin: 0 0 4px;">{{ $customer->name }}</h2>
            <p style="color: #6b7280; margin: 0 0 16px;">Customer #{{ $customer->id }}</p>
            
            @php
                $tierColors = [
                    'bronze' => ['bg' => '#fef3c7', 'color' => '#92400e'],
                    'silver' => ['bg' => '#e5e7eb', 'color' => '#374151'],
                    'gold' => ['bg' => '#fef3c7', 'color' => '#b45309'],
                    'platinum' => ['bg' => '#dbeafe', 'color' => '#1e40af'],
                ];
                $tier = $customer->loyalty_tier ?? 'bronze';
                $tierStyle = $tierColors[$tier] ?? $tierColors['bronze'];
            @endphp
            <span style="background: {{ $tierStyle['bg'] }}; color: {{ $tierStyle['color'] }}; padding: 6px 16px; border-radius: 20px; font-size: 12px; text-transform: uppercase; font-weight: 700;">
                {{ $tier }} Member
            </span>
            
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: left;">
                <p style="margin: 8px 0;"><i class="fas fa-envelope" style="color: #6b7280; width: 24px;"></i> {{ $customer->email }}</p>
                @if($customer->phone)
                <p style="margin: 8px 0;"><i class="fas fa-phone" style="color: #6b7280; width: 24px;"></i> {{ $customer->phone }}</p>
                @endif
                @if($customer->address)
                <p style="margin: 8px 0;"><i class="fas fa-map-marker-alt" style="color: #6b7280; width: 24px;"></i> {{ $customer->address }}</p>
                @endif
                <p style="margin: 8px 0;"><i class="fas fa-calendar" style="color: #6b7280; width: 24px;"></i> Joined {{ $customer->created_at->format('M d, Y') }}</p>
            </div>
            
            @if($customer->loyalty_points)
            <div style="margin-top: 16px; padding: 16px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 8px;">
                <div style="font-size: 28px; font-weight: 700; color: #b45309;">{{ number_format($customer->loyalty_points) }}</div>
                <div style="color: #92400e; font-size: 12px; text-transform: uppercase; font-weight: 600;">Loyalty Points</div>
            </div>
            @endif
        </div>
        
        <!-- Favorite Products -->
        @if($favoriteProducts->count() > 0)
        <div class="card" style="margin-top: 20px;">
            <h3 style="margin-bottom: 16px;"><i class="fas fa-heart" style="color: #ef4444;"></i> Favorite Products</h3>
            @foreach($favoriteProducts as $product)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f3f4f6;">
                <div>
                    <strong>{{ $product->product_name }}</strong>
                    <br><small style="color: #6b7280;">Ordered {{ $product->order_count }} times</small>
                </div>
                <span style="background: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                    {{ $product->total_quantity }} items
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    
    <!-- Right Column -->
    <div>
        <!-- Stats Grid -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
            <div class="card" style="text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: #3b82f6;">{{ $stats['total_orders'] }}</div>
                <div style="color: #6b7280; font-size: 14px;">Total Orders</div>
            </div>
            <div class="card" style="text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: #059669;">£{{ number_format($stats['total_spent'], 2) }}</div>
                <div style="color: #6b7280; font-size: 14px;">Total Spent</div>
            </div>
            <div class="card" style="text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: #8b5cf6;">£{{ number_format($stats['average_order_value'], 2) }}</div>
                <div style="color: #6b7280; font-size: 14px;">Avg. Order Value</div>
            </div>
        </div>
        
        <!-- Order Status Breakdown -->
        <div class="card" style="margin-bottom: 24px;">
            <h3 style="margin-bottom: 16px;">Order Status Breakdown</h3>
            <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 100px; padding: 12px; background: #fef3c7; border-radius: 8px; text-align: center;">
                    <div style="font-size: 24px; font-weight: 700; color: #92400e;">{{ $stats['pending_orders'] }}</div>
                    <div style="font-size: 12px; color: #92400e;">Pending</div>
                </div>
                <div style="flex: 1; min-width: 100px; padding: 12px; background: #d1fae5; border-radius: 8px; text-align: center;">
                    <div style="font-size: 24px; font-weight: 700; color: #065f46;">{{ $stats['completed_orders'] }}</div>
                    <div style="font-size: 12px; color: #065f46;">Completed</div>
                </div>
                <div style="flex: 1; min-width: 100px; padding: 12px; background: #fee2e2; border-radius: 8px; text-align: center;">
                    <div style="font-size: 24px; font-weight: 700; color: #991b1b;">{{ $stats['cancelled_orders'] }}</div>
                    <div style="font-size: 12px; color: #991b1b;">Cancelled</div>
                </div>
            </div>
        </div>
        
        <!-- Order History -->
        <div class="card">
            <h3 style="margin-bottom: 16px;"><i class="fas fa-history" style="color: #3b82f6;"></i> Order History</h3>
            
            @if($orders->count() > 0)
            <div class="order-list">
                @foreach($orders as $order)
                @php
                    $statusColors = [
                        'pending' => ['bg' => '#fef3c7', 'color' => '#92400e'],
                        'confirmed' => ['bg' => '#dbeafe', 'color' => '#1e40af'],
                        'preparing' => ['bg' => '#e0e7ff', 'color' => '#3730a3'],
                        'ready' => ['bg' => '#d1fae5', 'color' => '#065f46'],
                        'out_for_delivery' => ['bg' => '#cffafe', 'color' => '#0e7490'],
                        'completed' => ['bg' => '#d1fae5', 'color' => '#065f46'],
                        'cancelled' => ['bg' => '#fee2e2', 'color' => '#991b1b'],
                    ];
                    $statusStyle = $statusColors[$order->status] ?? ['bg' => '#f3f4f6', 'color' => '#374151'];
                @endphp
                <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <div>
                            <strong style="font-size: 16px;">{{ $order->order_number }}</strong>
                            <br><small style="color: #6b7280;">{{ $order->created_at->format('M d, Y H:i') }}</small>
                        </div>
                        <div style="text-align: right;">
                            <span style="background: {{ $statusStyle['bg'] }}; color: {{ $statusStyle['color'] }}; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                            <br>
                            <span style="background: {{ $order->order_type === 'delivery' ? '#dbeafe' : '#fef3c7' }}; color: {{ $order->order_type === 'delivery' ? '#1e40af' : '#92400e' }}; padding: 2px 8px; border-radius: 4px; font-size: 11px; margin-top: 4px; display: inline-block;">
                                {{ ucfirst($order->order_type ?? 'N/A') }}
                            </span>
                        </div>
                    </div>
                    
                    <div style="background: #f9fafb; border-radius: 6px; padding: 12px; margin-bottom: 12px;">
                        @foreach($order->items as $item)
                        @php
                            $customizations = $item->customizations ?? [];
                            $variant = $customizations['variant'] ?? null;
                            $addons = $customizations['addons'] ?? [];
                        @endphp
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; {{ !$loop->last ? 'margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px dashed #e5e7eb;' : '' }}">
                            <div>
                                <span style="font-weight: 500;">{{ $item->product_name }}</span>
                                <span style="color: #6b7280;"> x{{ $item->quantity }}</span>
                                @if($variant)
                                <br><small style="color: #3b82f6;"><i class="fas fa-ruler"></i> {{ $variant['name'] }}</small>
                                @endif
                                @if(count($addons) > 0)
                                <br><small style="color: #059669;"><i class="fas fa-plus-circle"></i> 
                                    {{ implode(', ', array_column($addons, 'addon_name')) }}
                                </small>
                                @endif
                            </div>
                            <span style="font-weight: 500;">£{{ number_format($item->subtotal, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <a href="{{ route('admin.orders.show', $order) }}" style="color: #3b82f6; font-size: 14px; text-decoration: none;">
                            <i class="fas fa-external-link-alt"></i> View Order
                        </a>
                        <strong style="font-size: 18px; color: #059669;">£{{ number_format($order->total, 2) }}</strong>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div style="margin-top: 16px;">
                {{ $orders->links() }}
            </div>
            @else
            <div style="text-align: center; padding: 40px; color: #6b7280;">
                <i class="fas fa-shopping-bag" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                <p>No orders yet</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
