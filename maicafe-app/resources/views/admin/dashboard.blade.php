@extends('layouts.admin')

@section('title', 'Dashboard - Mai Cafe Admin')

@push('styles')
<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }
    .chart-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }
    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .chart-header h3 {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
    }
    .chart-container {
        height: 300px;
        position: relative;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table thead {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }
    .data-table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .data-table td {
        padding: 16px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 14px;
    }
    .data-table tbody tr:hover {
        background: #f8fafc;
    }
    .data-table tbody tr:last-child td {
        border-bottom: none;
    }
    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 6px;
    }
    @media (max-width: 1024px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
        .table-responsive {
            overflow-x: scroll;
        }
        .data-table {
            min-width: 800px;
        }
    }
</style>
@endpush

@section('content')
<!-- Goal Card -->
<div class="goal-card">
    <div class="goal-content">
        <div class="goal-text">
            <h3>Great! Your goal is almost complete</h3>
            @php
                $targetOrders = 100;
                $progressPercent = min(round(($totalOrders / max($targetOrders, 1)) * 100), 100);
            @endphp
            <p>You have completed {{ $progressPercent }}% of your target. You can view the work details.</p>
            <a href="{{ route('admin.orders.index') }}" class="goal-link">
                View detail <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="goal-progress">
            <div class="goal-progress-circle" style="background: conic-gradient(from 0deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.3) {{ $progressPercent }}%, rgba(255,255,255,0.1) {{ $progressPercent }}%, rgba(255,255,255,0.1) 100%);">
                <div class="goal-progress-inner">
                    {{ $progressPercent }}%
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-title">Total Sales</div>
                <div class="stat-value">{{ number_format($totalOrders) }}</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>1.56%</span>
                </div>
            </div>
            <div class="stat-icon sales">
                <i class="fas fa-shopping-bag"></i>
            </div>
        </div>
        <canvas class="stat-chart" id="salesChart"></canvas>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-title">Total Income</div>
                <div class="stat-value">£{{ number_format($totalRevenue, 2) }}</div>
                <div class="stat-change negative">
                    <i class="fas fa-arrow-down"></i>
                    <span>1.56%</span>
                </div>
            </div>
            <div class="stat-icon income">
                <i class="fas fa-pound-sign"></i>
            </div>
        </div>
        <canvas class="stat-chart" id="incomeChart"></canvas>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-title">Total Visitor</div>
                <div class="stat-value">{{ number_format($totalCustomers) }}</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>1.56%</span>
                </div>
            </div>
            <div class="stat-icon visitor">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <canvas class="stat-chart" id="visitorChart"></canvas>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-title">Total Products</div>
                <div class="stat-value">{{ number_format($totalProducts) }}</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>1.56%</span>
                </div>
            </div>
            <div class="stat-icon products">
                <i class="fas fa-box"></i>
            </div>
        </div>
        <canvas class="stat-chart" id="productsChart"></canvas>
    </div>
</div>

<!-- Charts and Recent Orders -->
<div class="dashboard-grid">
    <!-- Sale by Category Chart -->
    <div class="chart-card">
        <div class="chart-header">
            <h3>Sale by category</h3>
            <div class="card-actions">
                <button class="card-action-btn">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
        </div>
        <div style="margin-bottom: 16px;">
            <div style="font-size: 14px; color: #64748b; margin-bottom: 4px;">Total {{ now()->format('M d, Y') }}</div>
            <div style="font-size: 24px; font-weight: 700; color: #1e293b;">
                £{{ number_format($totalRevenue, 2) }}
                <span class="stat-change positive" style="margin-left: 8px;">
                    <i class="fas fa-arrow-up"></i> 0.56%
                </span>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
    
    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header">
            <h3>Recent orders</h3>
            <div class="card-actions">
                <button class="card-action-btn">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Customer</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                @php
                    $firstItem = $order->items->first();
                @endphp
                <tr>
                    <td>
                        <div class="table-product">
                            @if($firstItem && $firstItem->product && $firstItem->product->image)
                            <img src="{{ asset('storage/' . $firstItem->product->image) }}" alt="{{ $firstItem->product->name }}">
                            @else
                            <div style="width: 40px; height: 40px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-box" style="color: #94a3b8;"></i>
                            </div>
                            @endif
                            <div>
                                <div class="table-product-name">
                                    {{ $firstItem ? ($firstItem->product ? $firstItem->product->name : 'N/A') : 'N/A' }}
                                </div>
                                <div style="font-size: 12px; color: #64748b;">#{{ $order->order_number }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $order->customer_name ?? 'Guest' }}</td>
                    <td>{{ $order->items->sum('quantity') }}</td>
                    <td>£{{ number_format($order->total, 2) }}</td>
                    <td>
                        <span class="badge badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">No orders yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Full Orders List Table -->
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <h3>All Orders</h3>
        <div class="card-actions">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary btn-sm">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order Number</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allOrders as $order)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #1e293b;">#{{ $order->order_number }}</div>
                    </td>
                    <td>
                        <div>
                            <div style="font-weight: 500; color: #1e293b;">{{ $order->customer_name ?? 'Guest' }}</div>
                            @if($order->customer_email)
                            <div style="font-size: 12px; color: #64748b;">{{ $order->customer_email }}</div>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="font-size: 14px; color: #1e293b;">{{ $order->created_at->format('M d, Y') }}</div>
                        <div style="font-size: 12px; color: #64748b;">{{ $order->created_at->format('h:i A') }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 500; color: #1e293b;">{{ $order->items->sum('quantity') }} items</div>
                        <div style="font-size: 12px; color: #64748b;">{{ $order->items->count() }} products</div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: #1e293b; font-size: 16px;">£{{ number_format($order->total, 2) }}</div>
                    </td>
                    <td>
                        <span class="badge badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : ($order->status === 'cancelled' ? 'danger' : 'info')) }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($order->status === 'pending')
                            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="processing">
                                <button type="submit" class="btn btn-sm btn-primary" title="Mark as Processing">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            @endif
                            @if($order->status === 'processing')
                            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-sm btn-success" title="Mark as Completed">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">
                        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block; color: #cbd5e1;"></i>
                        No orders found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($allOrders->hasPages())
    <div style="padding: 16px; border-top: 1px solid #e2e8f0;">
        {{ $allOrders->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Mini Charts for Stats Cards
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { enabled: false }
        },
        scales: {
            x: { display: false },
            y: { display: false }
        },
        elements: {
            point: { radius: 0 },
            line: { borderWidth: 2, tension: 0.4 }
        }
    };

    // Sales Chart
    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                data: [12, 19, 15, 25, 22, 30, 28],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true
            }]
        },
        options: chartOptions
    });

    // Income Chart
    new Chart(document.getElementById('incomeChart'), {
        type: 'line',
        data: {
            labels: ['Mon', 'Wed', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                data: [15, 25, 20, 30, 28],
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                fill: true
            }]
        },
        options: chartOptions
    });

    // Visitor Chart
    new Chart(document.getElementById('visitorChart'), {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                data: [20, 25, 22, 28, 25, 32, 30],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true
            }]
        },
        options: chartOptions
    });

    // Products Chart
    new Chart(document.getElementById('productsChart'), {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
            datasets: [{
                data: [10, 15, 12, 18, 16],
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                fill: true
            }]
        },
        options: chartOptions
    });

    // Category Chart
    @php
        $categories = \App\Models\Category::all();
        $categoryNames = $categories->pluck('name')->toArray();
        $categoryCounts = $categories->pluck('id')->map(function($id) {
            return \App\Models\Product::where('category_id', $id)->count();
        })->toArray();
    @endphp
    new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: {
            labels: @json($categoryNames ?: ['Category 1', 'Category 2', 'Category 3']),
            datasets: [{
                label: 'Products',
                data: @json($categoryCounts ?: [10, 15, 12]),
                backgroundColor: '#3b82f6',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    grid: { color: '#f1f5f9' },
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
</script>
@endpush
