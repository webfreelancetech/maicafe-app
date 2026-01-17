@extends('layouts.ecommerce')

@section('title', $product->name . ' - Mai Cafe')

@push('styles')
<style>
    body {
        background: #f5f1eb;
    }
    .product-page-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    .breadcrumbs {
        margin-bottom: 24px;
        font-size: 14px;
        color: #6b7280;
    }
    .breadcrumbs a {
        color: #8b6f47;
        text-decoration: none;
    }
    .breadcrumbs a:hover {
        text-decoration: underline;
    }
    .breadcrumbs span {
        margin: 0 8px;
        color: #d1d5db;
    }
    .product-detail-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 40px;
        background: #fff;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .product-image-section {
        position: relative;
    }
    .product-image-wrapper {
        background: #f5f1eb;
        border-radius: 20px;
        padding: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 400px;
    }
    .product-image {
        width: 100%;
        max-width: 500px;
        height: auto;
        border-radius: 16px;
        object-fit: cover;
    }
    .product-details-section {
        display: flex;
        flex-direction: column;
    }
    .product-category {
        font-size: 12px;
        font-weight: 700;
        color: #8b6f47;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 12px;
    }
    .product-title {
        font-size: 36px;
        font-weight: 700;
        color: #3d2817;
        margin-bottom: 24px;
        line-height: 1.2;
    }
    .product-pricing {
        margin-bottom: 24px;
    }
    .price-row {
        display: flex;
        align-items: baseline;
        gap: 16px;
        margin-bottom: 8px;
    }
    .current-price {
        font-size: 32px;
        font-weight: 700;
        color: #3d2817;
    }
    .old-price {
        font-size: 20px;
        color: #9ca3af;
        text-decoration: line-through;
    }
    .discount-badge {
        background: #ef4444;
        color: #fff;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 700;
    }
    .product-description {
        font-size: 16px;
        color: #6b7280;
        line-height: 1.6;
        margin-bottom: 32px;
    }
    .product-actions {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    .quantity-selector {
        display: flex;
        align-items: center;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }
    .quantity-btn {
        width: 48px;
        height: 48px;
        border: none;
        background: #f5f1eb;
        color: #8b6f47;
        font-size: 20px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .quantity-btn:hover {
        background: #e5e7eb;
    }
    .quantity-btn:active {
        transform: scale(0.95);
    }
    .quantity-input {
        width: 60px;
        height: 48px;
        border: none;
        text-align: center;
        font-size: 18px;
        font-weight: 600;
        color: #3d2817;
        background: #fff;
        -moz-appearance: textfield;
    }
    .quantity-input::-webkit-outer-spin-button,
    .quantity-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .add-to-cart-btn {
        flex: 1;
        min-width: 200px;
        padding: 16px 32px;
        background: #8b6f47;
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        transition: all 0.2s;
    }
    .add-to-cart-btn:hover {
        background: #6b5233;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 111, 71, 0.3);
    }
    .add-to-cart-btn:active {
        transform: translateY(0);
    }
    .product-info-badges {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .info-badge {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 16px;
        color: #3d2817;
    }
    .info-badge.in-stock {
        color: #10b981;
    }
    .info-badge i {
        font-size: 20px;
    }
    .info-badge.in-stock i {
        color: #10b981;
    }
    .info-badge.delivery i {
        color: #8b6f47;
    }
    
    /* Related Products Section */
    .related-products-section {
        margin-top: 60px;
        padding-top: 40px;
        border-top: 1px solid #e5e7eb;
    }
    .section-heading {
        font-size: 28px;
        font-weight: 700;
        color: #3d2817;
        margin-bottom: 32px;
        text-align: center;
    }
    .related-products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 24px;
    }
    .related-product-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
        text-decoration: none;
        color: inherit;
    }
    .related-product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }
    .related-product-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: #f5f1eb;
    }
    .related-product-info {
        padding: 16px;
    }
    .related-product-name {
        font-size: 16px;
        font-weight: 600;
        color: #3d2817;
        margin-bottom: 8px;
    }
    .related-product-price {
        font-size: 18px;
        font-weight: 700;
        color: #8b6f47;
    }
    
    /* Mobile Styles */
    @media (max-width: 768px) {
        .product-page-container {
            padding: 20px 16px;
        }
        .product-detail-grid {
            padding: 24px 16px;
            gap: 24px;
        }
        .product-image-wrapper {
            padding: 24px;
            min-height: 300px;
        }
        .product-title {
            font-size: 28px;
        }
        .current-price {
            font-size: 28px;
        }
        .product-actions {
            flex-direction: column;
        }
        .quantity-selector {
            width: 100%;
        }
        .add-to-cart-btn {
            width: 100%;
        }
        .related-products-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
    }
    
    /* Tablet and Desktop */
    @media (min-width: 769px) {
        .product-detail-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="product-page-container">
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <a href="{{ route('index') }}">Home</a>
        <span>/</span>
        @if($product->category)
        <a href="{{ route('menu', ['category' => $product->category_id]) }}">{{ strtoupper($product->category->name) }}</a>
        <span>/</span>
        @endif
        <span>{{ $product->name }}</span>
    </div>
    
    <!-- Product Detail Grid -->
    <div class="product-detail-grid">
        <!-- Product Image Section -->
        <div class="product-image-section">
            <div class="product-image-wrapper">
                @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                @else
                <div style="width: 100%; height: 400px; display: flex; align-items: center; justify-content: center; color: #a68b6b;">
                    <i class="fas fa-image" style="font-size: 64px;"></i>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Product Details Section -->
        <div class="product-details-section">
            @if($product->category)
            <div class="product-category">{{ strtoupper($product->category->name) }}</div>
            @endif
            
            <h1 class="product-title">{{ $product->name }}</h1>
            
            <!-- Pricing -->
            <div class="product-pricing">
                <div class="price-row">
                    <span class="current-price">{{ $currencySymbol }}{{ number_format($product->price, 2) }}</span>
                    @if($product->compare_price && $product->compare_price > $product->price)
                    <span class="old-price">{{ $currencySymbol }}{{ number_format($product->compare_price, 2) }}</span>
                    @php
                        $discount = (($product->compare_price - $product->price) / $product->compare_price) * 100;
                    @endphp
                    <span class="discount-badge">Save -{{ number_format($discount, 0) }}%</span>
                    @endif
                </div>
            </div>
            
            <!-- Description -->
            <div class="product-description">
                {{ $product->description ?: $product->short_description ?: 'Delicious ' . $product->name . ' made with fresh ingredients.' }}
            </div>
            
            <!-- Product Actions -->
            <div class="product-actions">
                <div class="quantity-selector">
                    <button class="quantity-btn" onclick="decreaseQuantity()">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" id="quantityInput" class="quantity-input" value="1" min="1" max="99" onchange="validateQuantity()">
                    <button class="quantity-btn" onclick="increaseQuantity()">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <button class="add-to-cart-btn" onclick="addToCart()">
                    <i class="fas fa-shopping-cart"></i>
                    Add to Cart
                </button>
            </div>
            
            <!-- Product Info Badges -->
            <div class="product-info-badges">
                <div class="info-badge in-stock">
                    <i class="fas fa-check-circle"></i>
                    <span>In Stock</span>
                </div>
                <div class="info-badge delivery">
                    <i class="fas fa-truck"></i>
                    <span>Fast Delivery</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="related-products-section">
        <h2 class="section-heading">Related Products</h2>
        <div class="related-products-grid">
            @foreach($relatedProducts as $relatedProduct)
            <a href="{{ route('product', $relatedProduct->slug) }}" class="related-product-card">
                @if($relatedProduct->image)
                <img src="{{ asset('storage/' . $relatedProduct->image) }}" alt="{{ $relatedProduct->name }}" class="related-product-image">
                @else
                <div class="related-product-image" style="display: flex; align-items: center; justify-content: center; color: #a68b6b;">
                    <i class="fas fa-image" style="font-size: 48px;"></i>
                </div>
                @endif
                <div class="related-product-info">
                    <div class="related-product-name">{{ $relatedProduct->name }}</div>
                    <div class="related-product-price">{{ $currencySymbol }}{{ number_format($relatedProduct->price, 2) }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    const productId = {{ $product->id }};
    const productName = '{{ addslashes($product->name) }}';
    const productPrice = {{ $product->price }};
    const productImage = '{{ $product->image ?? '' }}';
    const currencySymbol = '{{ $currencySymbol }}';
    
    function increaseQuantity() {
        const input = document.getElementById('quantityInput');
        const currentValue = parseInt(input.value) || 1;
        if (currentValue < 99) {
            input.value = currentValue + 1;
        }
    }
    
    function decreaseQuantity() {
        const input = document.getElementById('quantityInput');
        const currentValue = parseInt(input.value) || 1;
        if (currentValue > 1) {
            input.value = currentValue - 1;
        }
    }
    
    function validateQuantity() {
        const input = document.getElementById('quantityInput');
        let value = parseInt(input.value) || 1;
        if (value < 1) value = 1;
        if (value > 99) value = 99;
        input.value = value;
    }
    
    function addToCart() {
        const quantity = parseInt(document.getElementById('quantityInput').value) || 1;
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const existingItem = cart.find(item => item.id === productId);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({
                id: productId,
                name: productName,
                price: productPrice,
                image: productImage,
                quantity: quantity
            });
        }
        
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Update cart count
        const count = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
        const badges = document.querySelectorAll('[id*="CartCount"]');
        badges.forEach(badge => {
            if (badge) {
                badge.textContent = count;
                badge.style.display = count > 0 ? 'flex' : 'none';
            }
        });
        
        // Show success feedback
        const btn = document.querySelector('.add-to-cart-btn');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Added to Cart!';
        btn.style.background = '#10b981';
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.style.background = '#8b6f47';
        }, 2000);
        
        // Trigger storage event for other tabs
        window.dispatchEvent(new Event('storage'));
    }
    
    // Update cart count on page load
    document.addEventListener('DOMContentLoaded', function() {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const count = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
        const badges = document.querySelectorAll('[id*="CartCount"]');
        badges.forEach(badge => {
            if (badge) {
                badge.textContent = count;
                badge.style.display = count > 0 ? 'flex' : 'none';
            }
        });
    });
</script>
@endpush
