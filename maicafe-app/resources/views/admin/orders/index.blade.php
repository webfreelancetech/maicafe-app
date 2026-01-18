@extends('layouts.admin')

@section('title', 'Orders - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Orders</h1>
    <a href="{{ route('admin.orders.kitchen') }}" class="btn btn-primary" style="background: #059669;">
        <i class="fas fa-tv"></i> Kitchen Display
    </a>
</div>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Token</th>
                <th>Order #</th>
                <th>Customer</th>
                <th>Type</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>
                    @if($order->daily_token)
                    <div style="background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); color: #fff; padding: 8px 12px; border-radius: 8px; font-weight: 700; font-size: 16px; text-align: center; min-width: 60px;">
                        {{ $order->formatted_token }}
                    </div>
                    @else
                    <span style="color: #9ca3af;">-</span>
                    @endif
                </td>
                <td>
                    <strong>{{ $order->order_number }}</strong>
                </td>
                <td>
                    @if($order->user)
                        <strong>{{ $order->user->name }}</strong>
                        <br><small style="color: #6b7280;">{{ $order->user->email }}</small>
                        @if($order->user->phone)
                        <br><small style="color: #6b7280;">{{ $order->user->phone }}</small>
                        @endif
                    @else
                        <span style="color: #9ca3af;">Guest</span>
                    @endif
                </td>
                <td>
                    <span class="badge" style="background: {{ $order->order_type === 'delivery' ? '#dbeafe' : '#fef3c7' }}; color: {{ $order->order_type === 'delivery' ? '#1e40af' : '#92400e' }};">
                        {{ ucfirst($order->order_type ?? 'N/A') }}
                    </span>
                </td>
                <td>
                    {{ $order->items->count() }} item(s)
                </td>
                <td><strong>£{{ number_format($order->total, 2) }}</strong></td>
                <td>
                    @php
                        $statusColors = [
                            'pending' => 'badge-warning',
                            'confirmed' => 'badge-info',
                            'preparing' => 'badge-info',
                            'ready' => 'badge-info',
                            'out_for_delivery' => 'badge-info',
                            'completed' => 'badge-success',
                            'cancelled' => 'badge-danger',
                        ];
                    @endphp
                    <span class="badge {{ $statusColors[$order->status] ?? 'badge-info' }}">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </td>
                <td>{{ $order->created_at->format('M d, Y') }}<br><small style="color: #6b7280;">{{ $order->created_at->format('H:i') }}</small></td>
                <td>
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn" style="padding: 6px 12px; background: #f3f4f6;">
                        <i class="fas fa-eye"></i> View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; padding: 40px; color: #6b7280;">No orders found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 20px;">
        {{ $orders->links() }}
    </div>
</div>
@endsection


