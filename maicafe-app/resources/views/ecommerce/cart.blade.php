@extends('layouts.ecommerce')

@section('title', 'Cart - Mai Cafe')

@push('styles')
<style>
    body {
        background: #f5f1eb;
    }
    .cart-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px 16px;
    }
    .cart-header {
        margin-bottom: 24px;
    }
    .cart-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #3d2817;
        margin-bottom: 8px;
    }
    .cart-content {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }
    .cart-items-section {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .cart-item {
        display: flex;
        gap: 16px;
        padding: 16px 0;
        border-bottom: 1px solid #e5e7eb;
    }
    .cart-item:last-child {
        border-bottom: none;
    }
    .cart-item-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 12px;
        background: #f5f1eb;
        flex-shrink: 0;
    }
    .cart-item-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .cart-item-name {
        font-size: 16px;
        font-weight: 600;
        color: #3d2817;
        margin-bottom: 8px;
    }
    .cart-item-price {
        font-size: 18px;
        font-weight: 700;
        color: #8b6f47;
    }
    .cart-item-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 12px;
    }
    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 4px;
    }
    .quantity-btn {
        width: 32px;
        height: 32px;
        border: none;
        background: #f5f1eb;
        color: #8b6f47;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .quantity-btn:hover {
        background: #e5e7eb;
    }
    .quantity-btn:active {
        transform: scale(0.95);
    }
    .quantity-value {
        min-width: 40px;
        text-align: center;
        font-weight: 600;
        color: #3d2817;
    }
    .remove-btn {
        padding: 8px 16px;
        background: #ef4444;
        color: #fff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .remove-btn:hover {
        background: #dc2626;
    }
    .cart-summary {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        height: fit-content;
        position: sticky;
        top: 24px;
    }
    .cart-summary h2 {
        font-size: 20px;
        font-weight: 700;
        color: #3d2817;
        margin-bottom: 20px;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 16px;
        color: #6b7280;
    }
    .summary-row.total {
        font-size: 20px;
        font-weight: 700;
        color: #3d2817;
        padding-top: 16px;
        border-top: 2px solid #e5e7eb;
        margin-top: 16px;
    }
    .checkout-btn {
        width: 100%;
        padding: 16px;
        background: #8b6f47;
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 24px;
        transition: all 0.2s;
    }
    .checkout-btn:hover {
        background: #6b5233;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 111, 71, 0.3);
    }
    .checkout-btn:disabled {
        background: #d1d5db;
        cursor: not-allowed;
        transform: none;
    }
    .empty-cart {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .empty-cart-icon {
        font-size: 64px;
        color: #d1d5db;
        margin-bottom: 20px;
    }
    .empty-cart h2 {
        font-size: 24px;
        font-weight: 700;
        color: #3d2817;
        margin-bottom: 12px;
    }
    .empty-cart p {
        font-size: 16px;
        color: #6b7280;
        margin-bottom: 24px;
    }
    .continue-shopping-btn {
        display: inline-block;
        padding: 12px 24px;
        background: #8b6f47;
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .continue-shopping-btn:hover {
        background: #6b5233;
        transform: translateY(-2px);
    }
    
    @media (min-width: 768px) {
        .cart-content {
            grid-template-columns: 1fr 350px;
        }
        .cart-header h1 {
            font-size: 32px;
        }
    }
</style>
@endpush

@section('content')
<div class="cart-container">
    <div class="cart-header">
        <h1>Shopping Cart</h1>
    </div>
    
    <div class="cart-content">
        <div class="cart-items-section">
            <div id="cartItemsContainer">
                <div class="empty-cart">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h2>Your cart is empty</h2>
                    <p>Looks like you haven't added anything to your cart yet.</p>
                    <a href="{{ route('menu') }}" class="continue-shopping-btn">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>
        </div>
        
        <div class="cart-summary" id="cartSummary" style="display: none;">
            <h2>Order Summary</h2>
            <div class="summary-row">
                <span>Subtotal:</span>
                <span id="subtotal">$0.00</span>
            </div>
            <div class="summary-row">
                <span>Tax:</span>
                <span id="tax">$0.00</span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span id="total">$0.00</span>
            </div>
            <button class="checkout-btn" id="checkoutBtn" onclick="proceedToCheckout()">
                Proceed to Checkout
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Get currency symbol from PHP
    const currencySymbol = '{{ $currencySymbol ?? "$" }}';
    const taxRate = {{ $taxRate ?? 0 }};
    
    function loadCart() {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const container = document.getElementById('cartItemsContainer');
        const summary = document.getElementById('cartSummary');
        
        if (cart.length === 0) {
            container.innerHTML = `
                <div class="empty-cart">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h2>Your cart is empty</h2>
                    <p>Looks like you haven't added anything to your cart yet.</p>
                    <a href="{{ route('menu') }}" class="continue-shopping-btn">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            `;
            summary.style.display = 'none';
            return;
        }
        
        // Render cart items
        let html = '';
        let subtotal = 0;
        
        cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            subtotal += itemTotal;
            
            const storageBase = '{{ asset("storage") }}';
            const imageUrl = item.image ? `${storageBase}/${item.image}` : 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100"%3E%3Crect fill="%23f5f1eb" width="100" height="100"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23a68b6b" font-family="Arial" font-size="14"%3ENo Image%3C/text%3E%3C/svg%3E';
            
            html += `
                <div class="cart-item" data-index="${index}">
                    <img src="${imageUrl}" alt="${item.name}" class="cart-item-image" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\'%3E%3Crect fill=\'%23f5f1eb\' width=\'100\' height=\'100\'/%3E%3C/svg%3E'">
                    <div class="cart-item-info">
                        <div>
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-price">${currencySymbol}${parseFloat(item.price).toFixed(2)}</div>
                        </div>
                        <div class="cart-item-actions">
                            <div class="quantity-controls">
                                <button class="quantity-btn" onclick="updateQuantity(${index}, -1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="quantity-value">${item.quantity}</span>
                                <button class="quantity-btn" onclick="updateQuantity(${index}, 1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <button class="remove-btn" onclick="removeItem(${index})">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
        summary.style.display = 'block';
        updateSummary(subtotal);
        updateCartCount();
    }
    
    function updateQuantity(index, change) {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        
        if (cart[index]) {
            cart[index].quantity += change;
            
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart();
            window.dispatchEvent(new Event('storage'));
        }
    }
    
    function removeItem(index) {
        if (confirm('Are you sure you want to remove this item from your cart?')) {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart();
            window.dispatchEvent(new Event('storage'));
        }
    }
    
    function updateSummary(subtotal) {
        const tax = subtotal * (taxRate / 100);
        const total = subtotal + tax;
        
        document.getElementById('subtotal').textContent = `${currencySymbol}${subtotal.toFixed(2)}`;
        document.getElementById('tax').textContent = `${currencySymbol}${tax.toFixed(2)}`;
        document.getElementById('total').textContent = `${currencySymbol}${total.toFixed(2)}`;
    }
    
    function proceedToCheckout() {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        if (cart.length === 0) {
            alert('Your cart is empty!');
            return;
        }
        
        // For now, just show an alert. You can implement actual checkout later
        alert('Checkout functionality will be implemented soon!');
        // TODO: Implement checkout route and page
        // When checkout is implemented, uncomment the line below:
        // window.location.href = '/checkout';
    }
    
    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const count = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
        
        // Update all cart count badges
        const badges = document.querySelectorAll('[id*="CartCount"]');
        badges.forEach(badge => {
            if (badge) {
                badge.textContent = count;
                badge.style.display = count > 0 ? 'flex' : 'none';
            }
        });
    }
    
    // Load cart on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadCart();
        
        // Listen for storage changes (from other tabs/pages)
        window.addEventListener('storage', function() {
            loadCart();
        });
    });
</script>
@endpush
