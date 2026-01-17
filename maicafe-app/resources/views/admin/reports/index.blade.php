@extends('layouts.admin')

@section('title', 'Reports - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Reports</h1>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; margin-bottom: 24px;">
    <div class="card">
        <h3 style="font-size: 14px; color: #6b7280; margin-bottom: 8px;">Total Sales</h3>
        <p style="font-size: 32px; font-weight: 700; color: #111827;">£{{ number_format($totalSales, 2) }}</p>
    </div>
    <div class="card">
        <h3 style="font-size: 14px; color: #6b7280; margin-bottom: 8px;">Total Orders</h3>
        <p style="font-size: 32px; font-weight: 700; color: #111827;">{{ $totalOrders }}</p>
    </div>
    <div class="card">
        <h3 style="font-size: 14px; color: #6b7280; margin-bottom: 8px;">Average Order Value</h3>
        <p style="font-size: 32px; font-weight: 700; color: #111827;">£{{ number_format($averageOrderValue, 2) }}</p>
    </div>
</div>

<div class="card">
    <h3>Top Products</h3>
    <table class="data-table" style="margin-top: 20px;">
        <thead>
            <tr>
                <th>Product</th>
                <th>Orders</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->order_items_count }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" style="text-align: center; padding: 40px; color: #6b7280;">No data available</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection


