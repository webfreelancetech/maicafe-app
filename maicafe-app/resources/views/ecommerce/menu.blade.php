@extends('layouts.ecommerce')

@section('title', 'Menu - Mai Cafe')

@push('styles')
<style>
    .menu-page-header {
        background: linear-gradient(135deg, rgba(255, 107, 53, 0.95) 0%, rgba(229, 90, 43, 0.95) 100%);
        color: #fff;
        padding: 80px 0;
        text-align: center;
    }
    .menu-page-header h1 {
        font-size: 48px;
        font-weight: 800;
        margin-bottom: 15px;
    }
    .menu-page-header p {
        font-size: 18px;
        opacity: 0.95;
    }
    .menu-content {
        padding: 60px 0;
        background: #f8f9fa;
    }
    .category-filters {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 40px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .category-filters h2 {
        font-size: 20px;
        margin-bottom: 20px;
        color: #333;
    }
    .filter-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }
    .filter-btn {
        padding: 12px 24px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s;
        border: 2px solid #e5e7eb;
        background: #fff;
        color: #333;
    }
    .filter-btn.active {
        background: #ff6b35;
        color: #fff;
        border-color: #ff6b35;
    }
    .filter-btn:hover:not(.active) {
        background: #f8f9fa;
        border-color: #ff6b35;
        color: #ff6b35;
    }
    .active-filter-notice {
        background: #ecfdf5;
        border-left: 4px solid #ff6b35;
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        color: #065f46;
    }
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
    }
    .product-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }
    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .product-image {
        width: 100%;
        height: 220px;
        object-fit: cover;
    }
    .product-content {
        padding: 25px;
    }
    .product-content h3 {
        font-size: 20px;
        margin-bottom: 10px;
        color: #333;
    }
    .product-content p {
        color: #666;
        font-size: 14px;
        margin-bottom: 15px;
        line-height: 1.6;
    }
    .product-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .product-price {
        font-size: 24px;
        font-weight: 700;
        color: #ff6b35;
    }
    .product-price .old-price {
        font-size: 16px;
        color: #999;
        text-decoration: line-through;
        margin-left: 10px;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 12px;
    }
    .empty-state i {
        font-size: 64px;
        color: #d1d5db;
        margin-bottom: 20px;
    }
    .empty-state h3 {
        font-size: 24px;
        color: #333;
        margin-bottom: 10px;
    }
    .empty-state p {
        color: #666;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="menu-page-header">
    <div class="container">
        <h1>Our Menu</h1>
        <p>Discover our delicious selection of food and beverages</p>
    </div>
</div>

<div class="menu-content">
    <div class="container">
        @if($categories->count() > 0)
        <div class="category-filters">
            <h2><i class="fas fa-filter"></i> Browse by Category</h2>
            <div class="filter-buttons">
                <a href="{{ route('menu') }}" class="filter-btn {{ !request('category') ? 'active' : '' }}">
                    All Categories
                </a>
                @foreach($categories as $category)
                <a href="{{ route('menu', ['category' => $category->id]) }}" class="filter-btn {{ request('category') == $category->id ? 'active' : '' }}">
                    {{ $category->name }}
                </a>
                @endforeach
            </div>
        </div>
        @endif

        @if(request('category'))
            @php
                $selectedCategory = $categories->firstWhere('id', request('category'));
            @endphp
            @if($selectedCategory)
            <div class="active-filter-notice">
                <i class="fas fa-info-circle"></i> Showing products from: <strong>{{ $selectedCategory->name }}</strong>
                <a href="{{ route('menu') }}" style="margin-left: 12px; color: #ff6b35; text-decoration: underline; font-weight: 600;">Clear filter</a>
            </div>
            @endif
        @endif
        
        <div class="products-grid">
            @forelse($products as $product)
            <div class="product-card">
                @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                @endif
                <div class="product-content">
                    <h3>{{ $product->name }}</h3>
                    <p>{{ Str::limit($product->description, 80) }}</p>
                    <div class="product-footer">
                        <div class="product-price">
                            {{ $currencySymbol }}{{ number_format($product->price, 2) }}
                            @if($product->compare_price && $product->compare_price > $product->price)
                            <span class="old-price">{{ $currencySymbol }}{{ number_format($product->compare_price, 2) }}</span>
                            @endif
                        </div>
                        <a href="{{ route('product', $product->slug) }}" class="btn-primary" style="padding: 10px 20px; font-size: 14px;">View</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state" style="grid-column: 1 / -1;">
                <i class="fas fa-box-open"></i>
                <h3>No products available</h3>
                <p>
                    @if(request('category'))
                    Try selecting a different category or 
                    @endif
                    Browse our menu to find something delicious!
                </p>
                @if(request('category'))
                <a href="{{ route('menu') }}" class="btn-primary">View All Products</a>
                @endif
            </div>
            @endforelse
        </div>
        
        @if($products->hasPages())
        <div style="margin-top: 40px; display: flex; justify-content: center;">
            {{ $products->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
