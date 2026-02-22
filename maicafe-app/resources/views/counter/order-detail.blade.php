@extends('counter.layout')

@section('title', 'Order ' . $order->formatted_token)

@section('styles')
<style>
    .order-header {
        background: #0d2137;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    
    .order-info h1 {
        font-size: 36px;
        font-weight: 900;
        color: #22c55e;
        margin-bottom: 8px;
    }
    
    .order-number {
        font-size: 14px;
        color: #a0a0a0;
        margin-bottom: 16px;
    }
    
    .order-badges {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .badge {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }
    
    .badge.status-awaiting_payment { background: #fef3c7; color: #92400e; }
    .badge.status-pending { background: #fef3c7; color: #92400e; }
    .badge.status-confirmed { background: #dbeafe; color: #1e40af; }
    .badge.status-preparing { background: #e0e7ff; color: #3730a3; }
    .badge.status-ready { background: #d1fae5; color: #065f46; }
    .badge.status-completed { background: #d1fae5; color: #065f46; }
    .badge.status-cancelled { background: #fee2e2; color: #991b1b; }
    
    .badge.payment-pending { background: #f59e0b; color: #000; }
    .badge.payment-paid { background: #22c55e; color: #000; }
    
    .badge.type { background: #3b82f6; color: #fff; }
    
    .order-total-box {
        text-align: right;
    }
    
    .total-label {
        font-size: 12px;
        color: #a0a0a0;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    
    .total-amount {
        font-size: 48px;
        font-weight: 900;
        color: #22c55e;
    }
    
    .order-actions-box {
        margin-top: 20px;
        display: flex;
        gap: 10px;
    }
    
    .btn {
        padding: 14px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
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
    
    .btn-back {
        background: #1e5f74;
        color: #fff;
        text-decoration: none;
    }
    
    .btn-back:hover {
        background: #3b82f6;
    }
    
    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }
    
    .card {
        background: #0d2137;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .card-header {
        padding: 16px 20px;
        background: rgba(0,0,0,0.2);
        border-bottom: 1px solid rgba(255,255,255,0.1);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .card-header i {
        color: #22c55e;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .items-table {
        width: 100%;
    }
    
    .items-table th {
        text-align: left;
        font-size: 11px;
        text-transform: uppercase;
        color: #6b7280;
        padding: 8px 0;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .items-table td {
        padding: 12px 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    
    .items-table tr:last-child td {
        border-bottom: none;
    }
    
    .item-name {
        font-weight: 600;
    }
    
    .item-variant {
        font-size: 12px;
        color: #a0a0a0;
    }
    
    .item-addons {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }
    
    .item-qty {
        background: #22c55e;
        color: #000;
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-weight: 700;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        font-size: 14px;
    }
    
    .summary-row.total {
        border-top: 2px solid rgba(255,255,255,0.1);
        margin-top: 10px;
        padding-top: 16px;
        font-size: 20px;
        font-weight: 700;
    }
    
    .summary-row.total .value {
        color: #22c55e;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        color: #6b7280;
        font-size: 13px;
    }
    
    .info-value {
        font-weight: 500;
        text-align: right;
    }
    
    .payment-confirmed-info {
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid #22c55e;
        border-radius: 8px;
        padding: 16px;
        margin-top: 16px;
    }
    
    .payment-confirmed-info h4 {
        color: #22c55e;
        font-size: 14px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .payment-confirmed-info p {
        font-size: 13px;
        color: #a0a0a0;
    }
</style>
@endsection

@section('content')
<div class="order-header">
    <div class="order-info">
        <h1>{{ $order->formatted_token }}</h1>
        <div class="order-number">Order #{{ $order->order_number }}</div>
        <div class="order-badges">
            <span class="badge status-{{ $order->status }}">{{ str_replace('_', ' ', $order->status) }}</span>
            <span class="badge payment-{{ $order->payment_status }}">Payment: {{ ucfirst($order->payment_status) }}</span>
            <span class="badge type">{{ ucfirst($order->order_type ?? 'Pickup') }}</span>
        </div>
    </div>
    <div class="order-total-box">
        <div class="total-label">Order Total</div>
        <div class="total-amount">£{{ number_format($order->total, 2) }}</div>
        <div class="order-actions-box">
            @if($order->status === 'awaiting_payment' && $order->payment_method === 'pay_at_counter')
            <form action="{{ route('counter.orders.confirmPayment', $order) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-confirm" onclick="return confirm('Confirm payment received?')">
                    <i class="fas fa-check"></i> Confirm Payment
                </button>
            </form>
            <form action="{{ route('counter.orders.cancel', $order) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-cancel" onclick="return confirm('Cancel this order?')">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </form>
            @endif
            <a href="{{ route('counter.dashboard') }}" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

<div class="content-grid">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-shopping-bag"></i>
            Order Items
        </div>
        <div class="card-body">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Price</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->product_name }}</div>
                            @if(!empty($item->customizations['variant']))
                            <div class="item-variant">{{ $item->customizations['variant']['name'] ?? '' }}</div>
                            @endif
                            @if(!empty($item->customizations['addons']))
                            <div class="item-addons">
                                @foreach($item->customizations['addons'] as $addon)
                                + {{ $addon['addon_name'] ?? $addon['name'] ?? 'Addon' }}
                                @if(($addon['quantity'] ?? 1) > 1) x{{ $addon['quantity'] }} @endif
                                (£{{ number_format($addon['total'] ?? $addon['price'] ?? 0, 2) }})
                                @if(!$loop->last), @endif
                                @endforeach
                            </div>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span class="item-qty">{{ $item->quantity }}</span>
                        </td>
                        <td style="text-align: right;">£{{ number_format($item->price, 2) }}</td>
                        <td style="text-align: right; font-weight: 600;">£{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>£{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->tax > 0)
                <div class="summary-row">
                    <span>Tax</span>
                    <span>£{{ number_format($order->tax, 2) }}</span>
                </div>
                @endif
                @if($order->delivery_charge > 0)
                <div class="summary-row">
                    <span>Delivery Charge</span>
                    <span>£{{ number_format($order->delivery_charge, 2) }}</span>
                </div>
                @endif
                @if($order->discount > 0)
                <div class="summary-row" style="color: #22c55e;">
                    <span>Discount @if($order->coupon_code)({{ $order->coupon_code }})@endif</span>
                    <span>-£{{ number_format($order->discount, 2) }}</span>
                </div>
                @endif
                <div class="summary-row total">
                    <span>Total</span>
                    <span class="value">£{{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div>
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <i class="fas fa-user"></i>
                Customer Details
            </div>
            <div class="card-body">
                @if($order->user)
                <div class="info-row">
                    <span class="info-label">Name</span>
                    <span class="info-value">{{ $order->user->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ $order->user->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone</span>
                    <span class="info-value">{{ $order->user->phone ?? 'N/A' }}</span>
                </div>
                @else
                <p style="color: #6b7280;">Guest order</p>
                @endif
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle"></i>
                Order Details
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Order Time</span>
                    <span class="info-value">{{ $order->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Store</span>
                    <span class="info-value">{{ $order->store->name ?? 'Main Store' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Method</span>
                    <span class="info-value">{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</span>
                </div>
                @if($order->notes)
                <div class="info-row">
                    <span class="info-label">Notes</span>
                    <span class="info-value">{{ $order->notes }}</span>
                </div>
                @endif
                
                @if($order->payment_status === 'paid' && $order->payment_confirmed_at)
                <div class="payment-confirmed-info">
                    <h4><i class="fas fa-check-circle"></i> Payment Confirmed</h4>
                    <p>
                        Confirmed at {{ $order->payment_confirmed_at->format('M d, Y H:i') }}
                        @if($order->paymentConfirmedBy)
                        by {{ $order->paymentConfirmedBy->name }}
                        @endif
                    </p>
                    @if($order->payment_reference)
                    <p>Ref: {{ $order->payment_reference }}</p>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
