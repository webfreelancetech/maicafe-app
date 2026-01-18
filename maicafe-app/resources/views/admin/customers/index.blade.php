@extends('layouts.admin')

@section('title', 'Customers - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Customers</h1>
</div>

<!-- Stats Summary -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px;">
    <div class="stat-card" style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="color: #6b7280; font-size: 14px;">Total Customers</div>
        <div style="font-size: 28px; font-weight: 700; color: #111827;">{{ $customers->total() }}</div>
    </div>
    <div class="stat-card" style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="color: #6b7280; font-size: 14px;">With Orders</div>
        <div style="font-size: 28px; font-weight: 700; color: #3b82f6;">{{ $customers->where('orders_count', '>', 0)->count() }}</div>
    </div>
    <div class="stat-card" style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="color: #6b7280; font-size: 14px;">New This Month</div>
        <div style="font-size: 28px; font-weight: 700; color: #10b981;">{{ $customers->where('created_at', '>=', now()->startOfMonth())->count() }}</div>
    </div>
    <div class="stat-card" style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="color: #6b7280; font-size: 14px;">Total Revenue</div>
        <div style="font-size: 28px; font-weight: 700; color: #8b5cf6;">£{{ number_format($customers->sum('total_spent'), 2) }}</div>
    </div>
</div>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Contact</th>
                <th>Orders</th>
                <th>Total Spent</th>
                <th>Loyalty</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 600;">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                        <div>
                            <strong>{{ $customer->name }}</strong>
                            <br><small style="color: #6b7280;">#{{ $customer->id }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <div>{{ $customer->email }}</div>
                    @if($customer->phone)
                    <small style="color: #6b7280;">{{ $customer->phone }}</small>
                    @endif
                </td>
                <td>
                    @if($customer->orders_count > 0)
                        <span style="background: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 20px; font-weight: 600;">
                            {{ $customer->orders_count }}
                        </span>
                    @else
                        <span style="color: #9ca3af;">No orders</span>
                    @endif
                </td>
                <td>
                    @if($customer->total_spent > 0)
                        <strong style="color: #059669;">£{{ number_format($customer->total_spent, 2) }}</strong>
                    @else
                        <span style="color: #9ca3af;">£0.00</span>
                    @endif
                </td>
                <td>
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
                    <span style="background: {{ $tierStyle['bg'] }}; color: {{ $tierStyle['color'] }}; padding: 4px 10px; border-radius: 4px; font-size: 12px; text-transform: uppercase; font-weight: 600;">
                        {{ $tier }}
                    </span>
                    @if($customer->loyalty_points)
                    <br><small style="color: #6b7280;">{{ $customer->loyalty_points }} pts</small>
                    @endif
                </td>
                <td>
                    {{ $customer->created_at->format('M d, Y') }}
                    <br><small style="color: #6b7280;">{{ $customer->created_at->diffForHumans() }}</small>
                </td>
                <td>
                    <a href="{{ route('admin.customers.show', $customer) }}" class="btn" style="padding: 6px 12px; background: #f3f4f6;">
                        <i class="fas fa-eye"></i> View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px; color: #6b7280;">No customers found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 20px;">
        {{ $customers->links() }}
    </div>
</div>
@endsection


