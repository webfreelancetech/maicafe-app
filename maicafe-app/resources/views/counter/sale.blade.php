@extends('counter.layout')

@section('title', 'New Sale')

@section('styles')
<style>
    .pos-container {
        display: grid;
        grid-template-columns: 200px 1fr 380px;
        gap: 16px;
        height: calc(100vh - 100px);
    }
    
    /* Categories Sidebar */
    .categories-sidebar {
        background: #0d2137;
        border-radius: 12px;
        padding: 12px;
        overflow-y: auto;
    }
    
    .category-btn {
        width: 100%;
        padding: 14px 16px;
        margin-bottom: 8px;
        background: transparent;
        border: none;
        border-radius: 8px;
        color: #a0a0a0;
        font-size: 14px;
        font-weight: 600;
        text-align: left;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .category-btn:hover {
        background: #1e5f74;
        color: #fff;
    }
    
    .category-btn.active {
        background: #22c55e;
        color: #000;
    }
    
    .category-btn i {
        width: 20px;
        text-align: center;
    }
    
    /* Products Grid */
    .products-section {
        background: #0d2137;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    .products-header {
        padding: 16px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        display: flex;
        gap: 12px;
        align-items: center;
    }
    
    .search-box {
        flex: 1;
        position: relative;
    }
    
    .search-box input {
        width: 100%;
        padding: 12px 16px 12px 44px;
        background: #1e3a5f;
        border: 2px solid #1e5f74;
        border-radius: 8px;
        color: #fff;
        font-size: 14px;
    }
    
    .search-box input:focus {
        outline: none;
        border-color: #22c55e;
    }
    
    .search-box i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
    }
    
    .products-grid {
        flex: 1;
        padding: 16px;
        overflow-y: auto;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 12px;
        align-content: start;
    }
    
    .product-card {
        background: #1e3a5f;
        border-radius: 12px;
        padding: 12px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
    }
    
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    }
    
    .product-card img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 10px;
    }
    
    .product-card .no-image {
        width: 80px;
        height: 80px;
        background: #0d2137;
        border-radius: 8px;
        margin: 0 auto 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        font-size: 24px;
    }
    
    .product-card h4 {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .product-card .price {
        font-size: 16px;
        font-weight: 700;
        color: #22c55e;
    }
    
    /* Cart Section */
    .cart-section {
        background: #0d2137;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    .cart-header {
        padding: 16px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .cart-header h3 {
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .cart-header h3 i {
        color: #22c55e;
    }
    
    .clear-cart-btn {
        background: transparent;
        border: 1px solid #ef4444;
        color: #ef4444;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .clear-cart-btn:hover {
        background: #ef4444;
        color: #fff;
    }
    
    .cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
    }
    
    .cart-item {
        background: #1e3a5f;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 10px;
    }
    
    .cart-item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
    }
    
    .cart-item-name {
        font-weight: 600;
        font-size: 14px;
    }
    
    .cart-item-variant {
        font-size: 12px;
        color: #a0a0a0;
    }
    
    .cart-item-remove {
        background: transparent;
        border: none;
        color: #ef4444;
        cursor: pointer;
        padding: 4px;
        font-size: 14px;
    }
    
    .cart-item-addons {
        font-size: 11px;
        color: #6b7280;
        margin-bottom: 8px;
    }
    
    .cart-item-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .qty-controls {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #0d2137;
        border-radius: 6px;
        padding: 4px;
    }
    
    .qty-btn {
        width: 28px;
        height: 28px;
        border: none;
        border-radius: 4px;
        background: #1e5f74;
        color: #fff;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .qty-btn:hover {
        background: #22c55e;
        color: #000;
    }
    
    .qty-value {
        min-width: 30px;
        text-align: center;
        font-weight: 700;
    }
    
    .cart-item-total {
        font-size: 16px;
        font-weight: 700;
        color: #22c55e;
    }
    
    .cart-empty {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        padding: 40px;
    }
    
    .cart-empty i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
    
    /* Cart Summary */
    .cart-summary {
        padding: 16px;
        border-top: 1px solid rgba(255,255,255,0.1);
        background: rgba(0,0,0,0.2);
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .summary-row.total {
        font-size: 20px;
        font-weight: 700;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid rgba(255,255,255,0.1);
    }
    
    .summary-row.total .value {
        color: #22c55e;
    }
    
    .customer-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-bottom: 12px;
    }
    
    .customer-info input {
        padding: 10px 12px;
        background: #1e3a5f;
        border: 1px solid #1e5f74;
        border-radius: 6px;
        color: #fff;
        font-size: 13px;
    }
    
    .customer-info input:focus {
        outline: none;
        border-color: #22c55e;
    }
    
    .payment-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    
    .pay-btn {
        padding: 16px;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }
    
    .pay-btn i {
        font-size: 24px;
    }
    
    .pay-btn.cash {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: #fff;
    }
    
    .pay-btn.card {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: #fff;
    }
    
    .pay-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    
    .pay-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
    
    /* Product Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.7);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    
    .modal-overlay.active {
        display: flex;
    }
    
    .modal-content {
        background: #0d2137;
        border-radius: 16px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .modal-header {
        padding: 20px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-header h3 {
        font-size: 20px;
        font-weight: 700;
    }
    
    .modal-close {
        background: transparent;
        border: none;
        color: #a0a0a0;
        font-size: 24px;
        cursor: pointer;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .variant-section, .addon-section {
        margin-bottom: 20px;
    }
    
    .section-title {
        font-size: 14px;
        font-weight: 600;
        color: #a0a0a0;
        margin-bottom: 10px;
        text-transform: uppercase;
    }
    
    .variant-options, .addon-options {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .variant-btn, .addon-btn {
        padding: 10px 16px;
        background: #1e3a5f;
        border: 2px solid #1e5f74;
        border-radius: 8px;
        color: #fff;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .variant-btn.selected, .addon-btn.selected {
        background: #22c55e;
        border-color: #22c55e;
        color: #000;
    }
    
    .addon-btn .addon-price {
        font-size: 11px;
        opacity: 0.8;
    }
    
    .modal-footer {
        padding: 20px;
        border-top: 1px solid rgba(255,255,255,0.1);
        display: flex;
        gap: 12px;
        align-items: center;
    }
    
    .modal-qty {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #1e3a5f;
        padding: 8px 12px;
        border-radius: 8px;
    }
    
    .modal-qty button {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 6px;
        background: #1e5f74;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
    }
    
    .modal-qty span {
        min-width: 40px;
        text-align: center;
        font-size: 18px;
        font-weight: 700;
    }
    
    .add-to-cart-btn {
        flex: 1;
        padding: 14px 20px;
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        border: none;
        border-radius: 8px;
        color: #fff;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .add-to-cart-btn:hover {
        transform: translateY(-2px);
    }
    
    /* Success Modal */
    .success-modal {
        text-align: center;
        padding: 40px;
    }
    
    .success-modal .icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 36px;
        color: #fff;
    }
    
    .success-modal h2 {
        font-size: 24px;
        margin-bottom: 10px;
    }
    
    .success-modal .token {
        font-size: 48px;
        font-weight: 900;
        color: #22c55e;
        margin: 20px 0;
    }
    
    .success-modal .total {
        font-size: 20px;
        margin-bottom: 30px;
    }
    
    .success-modal .btn {
        padding: 14px 30px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        margin: 0 8px;
    }
    
    .success-modal .btn-primary {
        background: #22c55e;
        color: #fff;
    }
    
    .success-modal .btn-secondary {
        background: #1e5f74;
        color: #fff;
    }
    
    /* Loading */
    .loading {
        pointer-events: none;
        opacity: 0.7;
    }
</style>
@endsection

@section('content')
<div class="pos-container">
    <!-- Categories Sidebar -->
    <div class="categories-sidebar">
        <button class="category-btn active" data-category="" onclick="selectCategory(this, '')">
            <i class="fas fa-th"></i>
            All Items
        </button>
        @foreach($categories as $category)
        <button class="category-btn" data-category="{{ $category->id }}" onclick="selectCategory(this, {{ $category->id }})">
            <i class="fas fa-tag"></i>
            {{ $category->name }}
        </button>
        @endforeach
    </div>
    
    <!-- Products Section -->
    <div class="products-section">
        <div class="products-header">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search products..." oninput="searchProducts(this.value)">
            </div>
        </div>
        <div class="products-grid" id="productsGrid">
            <!-- Products loaded via AJAX -->
            <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #6b7280;">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p style="margin-top: 12px;">Loading products...</p>
            </div>
        </div>
    </div>
    
    <!-- Cart Section -->
    <div class="cart-section">
        <div class="cart-header">
            <h3><i class="fas fa-shopping-cart"></i> Current Order</h3>
            <button class="clear-cart-btn" onclick="clearCart()">
                <i class="fas fa-trash"></i> Clear
            </button>
        </div>
        
        <div class="cart-items" id="cartItems">
            <div class="cart-empty">
                <i class="fas fa-shopping-basket"></i>
                <p>No items in cart</p>
                <small>Tap a product to add</small>
            </div>
        </div>
        
        <div class="cart-summary">
            <div class="customer-info">
                <input type="text" id="customerName" placeholder="Customer Name (optional)">
                <input type="text" id="customerPhone" placeholder="Phone (optional)">
            </div>
            
            <div class="summary-row">
                <span>Subtotal</span>
                <span id="subtotalDisplay">{{ $currency }}0.00</span>
            </div>
            <div class="summary-row">
                <span>Tax ({{ $taxRate }}%)</span>
                <span id="taxDisplay">{{ $currency }}0.00</span>
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span class="value" id="totalDisplay">{{ $currency }}0.00</span>
            </div>
            
            <div class="payment-buttons">
                <button class="pay-btn cash" id="payCashBtn" onclick="processPayment('cash')" disabled>
                    <i class="fas fa-money-bill-wave"></i>
                    Cash
                </button>
                <button class="pay-btn card" id="payCardBtn" onclick="processPayment('card')" disabled>
                    <i class="fas fa-credit-card"></i>
                    Card
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Product Modal -->
<div class="modal-overlay" id="productModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalProductName">Product Name</h3>
            <button class="modal-close" onclick="closeProductModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="variant-section" id="variantSection" style="display: none;">
                <div class="section-title">Select Size/Variant</div>
                <div class="variant-options" id="variantOptions"></div>
            </div>
            
            <div id="addonSections"></div>
        </div>
        <div class="modal-footer">
            <div class="modal-qty">
                <button onclick="updateModalQty(-1)">-</button>
                <span id="modalQty">1</span>
                <button onclick="updateModalQty(1)">+</button>
            </div>
            <button class="add-to-cart-btn" onclick="addToCartFromModal()">
                <i class="fas fa-plus"></i>
                Add <span id="modalTotal">{{ $currency }}0.00</span>
            </button>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal-overlay" id="successModal">
    <div class="modal-content success-modal">
        <div class="icon">
            <i class="fas fa-check"></i>
        </div>
        <h2>Order Created!</h2>
        <p>Token Number</p>
        <div class="token" id="successToken">T001</div>
        <div class="total">Total: <strong id="successTotal">£0.00</strong></div>
        <div>
            <button class="btn btn-primary" onclick="newOrder()">New Order</button>
            <button class="btn btn-secondary" onclick="printReceipt()">Print Receipt</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const baseUrl = '{{ url("/") }}';
    const currency = '{{ $currency }}';
    const taxRate = {{ $taxRate }};
    
    let cart = [];
    let currentProduct = null;
    let selectedVariant = null;
    let selectedAddons = [];
    let modalQty = 1;
    
    // Load products on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadProducts();
    });
    
    function selectCategory(btn, categoryId) {
        document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        loadProducts(categoryId);
    }
    
    function loadProducts(categoryId = '', search = '') {
        const grid = document.getElementById('productsGrid');
        grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #6b7280;"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';
        
        let url = `${baseUrl}/counter/sale/products?`;
        if (categoryId) url += `category_id=${categoryId}&`;
        if (search) url += `search=${encodeURIComponent(search)}`;
        
        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (data.products.length === 0) {
                    grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #6b7280;"><i class="fas fa-box-open fa-2x"></i><p style="margin-top: 12px;">No products found</p></div>';
                    return;
                }
                
                grid.innerHTML = data.products.map(p => `
                    <div class="product-card" onclick="openProductModal(${p.id})">
                        ${p.image ? `<img src="${p.image}" alt="${p.name}">` : '<div class="no-image"><i class="fas fa-coffee"></i></div>'}
                        <h4>${p.name}</h4>
                        <div class="price">${currency}${p.price.toFixed(2)}</div>
                    </div>
                `).join('');
            })
            .catch(err => {
                console.error(err);
                grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #ef4444;"><i class="fas fa-exclamation-circle fa-2x"></i><p style="margin-top: 12px;">Error loading products</p></div>';
            });
    }
    
    let searchTimeout;
    function searchProducts(query) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const activeCategory = document.querySelector('.category-btn.active').dataset.category;
            loadProducts(activeCategory, query);
        }, 300);
    }
    
    function openProductModal(productId) {
        fetch(`${baseUrl}/counter/sale/product/${productId}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    alert('Product not found');
                    return;
                }
                
                currentProduct = data.product;
                selectedVariant = null;
                selectedAddons = [];
                modalQty = 1;
                
                document.getElementById('modalProductName').textContent = currentProduct.name;
                document.getElementById('modalQty').textContent = modalQty;
                
                // Variants
                const variantSection = document.getElementById('variantSection');
                const variantOptions = document.getElementById('variantOptions');
                
                if (currentProduct.has_variants && currentProduct.variants.length > 0) {
                    variantSection.style.display = 'block';
                    variantOptions.innerHTML = currentProduct.variants.map((v, i) => `
                        <button class="variant-btn ${i === 0 ? 'selected' : ''}" 
                                data-id="${v.id}" 
                                data-name="${v.name}" 
                                data-price="${v.price}"
                                onclick="selectVariant(this)">
                            ${v.name} - ${currency}${v.price.toFixed(2)}
                        </button>
                    `).join('');
                    
                    // Auto-select first variant
                    const firstVariant = currentProduct.variants[0];
                    selectedVariant = { id: firstVariant.id, name: firstVariant.name, price: firstVariant.price };
                } else {
                    variantSection.style.display = 'none';
                }
                
                // Addon groups
                const addonSections = document.getElementById('addonSections');
                if (currentProduct.addon_groups && currentProduct.addon_groups.length > 0) {
                    addonSections.innerHTML = currentProduct.addon_groups.map(group => `
                        <div class="addon-section">
                            <div class="section-title">${group.name} ${group.min_selections > 0 ? '(Required)' : '(Optional)'}</div>
                            <div class="addon-options">
                                ${group.addons.map(addon => `
                                    <button class="addon-btn" 
                                            data-id="${addon.id}"
                                            data-name="${addon.name}"
                                            data-price="${addon.price}"
                                            data-group="${group.id}"
                                            onclick="toggleAddon(this)">
                                        ${addon.name}
                                        <span class="addon-price">+${currency}${addon.price.toFixed(2)}</span>
                                    </button>
                                `).join('')}
                            </div>
                        </div>
                    `).join('');
                } else {
                    addonSections.innerHTML = '';
                }
                
                updateModalTotal();
                document.getElementById('productModal').classList.add('active');
            });
    }
    
    function closeProductModal() {
        document.getElementById('productModal').classList.remove('active');
        currentProduct = null;
    }
    
    function selectVariant(btn) {
        document.querySelectorAll('.variant-btn').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        selectedVariant = {
            id: parseInt(btn.dataset.id),
            name: btn.dataset.name,
            price: parseFloat(btn.dataset.price)
        };
        updateModalTotal();
    }
    
    function toggleAddon(btn) {
        btn.classList.toggle('selected');
        
        const addonId = parseInt(btn.dataset.id);
        const index = selectedAddons.findIndex(a => a.id === addonId);
        
        if (index > -1) {
            selectedAddons.splice(index, 1);
        } else {
            selectedAddons.push({
                id: addonId,
                name: btn.dataset.name,
                price: parseFloat(btn.dataset.price),
                group_id: parseInt(btn.dataset.group)
            });
        }
        
        updateModalTotal();
    }
    
    function updateModalQty(delta) {
        modalQty = Math.max(1, modalQty + delta);
        document.getElementById('modalQty').textContent = modalQty;
        updateModalTotal();
    }
    
    function updateModalTotal() {
        let unitPrice = selectedVariant ? selectedVariant.price : currentProduct.price;
        let addonsTotal = selectedAddons.reduce((sum, a) => sum + a.price, 0);
        let total = (unitPrice + addonsTotal) * modalQty;
        document.getElementById('modalTotal').textContent = currency + total.toFixed(2);
    }
    
    function addToCartFromModal() {
        const unitPrice = selectedVariant ? selectedVariant.price : currentProduct.price;
        const addonsTotal = selectedAddons.reduce((sum, a) => sum + a.price, 0);
        const itemTotal = (unitPrice + addonsTotal) * modalQty;
        
        const cartItem = {
            id: Date.now(), // Unique cart item ID
            product_id: currentProduct.id,
            product_name: currentProduct.name,
            quantity: modalQty,
            unit_price: unitPrice,
            variant_id: selectedVariant ? selectedVariant.id : null,
            variant_name: selectedVariant ? selectedVariant.name : null,
            addons: selectedAddons.map(a => ({
                id: a.id,
                addon_name: a.name,
                price: a.price,
                quantity: 1,
                total: a.price,
                group_id: a.group_id
            })),
            addons_total: addonsTotal,
            item_total: itemTotal
        };
        
        cart.push(cartItem);
        renderCart();
        closeProductModal();
    }
    
    function renderCart() {
        const cartItemsDiv = document.getElementById('cartItems');
        
        if (cart.length === 0) {
            cartItemsDiv.innerHTML = `
                <div class="cart-empty">
                    <i class="fas fa-shopping-basket"></i>
                    <p>No items in cart</p>
                    <small>Tap a product to add</small>
                </div>
            `;
            updateCartSummary();
            return;
        }
        
        cartItemsDiv.innerHTML = cart.map((item, index) => `
            <div class="cart-item">
                <div class="cart-item-header">
                    <div>
                        <div class="cart-item-name">${item.product_name}</div>
                        ${item.variant_name ? `<div class="cart-item-variant">${item.variant_name}</div>` : ''}
                    </div>
                    <button class="cart-item-remove" onclick="removeFromCart(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                ${item.addons.length > 0 ? `
                    <div class="cart-item-addons">
                        ${item.addons.map(a => '+ ' + a.addon_name).join(', ')}
                    </div>
                ` : ''}
                <div class="cart-item-footer">
                    <div class="qty-controls">
                        <button class="qty-btn" onclick="updateCartQty(${index}, -1)">-</button>
                        <span class="qty-value">${item.quantity}</span>
                        <button class="qty-btn" onclick="updateCartQty(${index}, 1)">+</button>
                    </div>
                    <div class="cart-item-total">${currency}${item.item_total.toFixed(2)}</div>
                </div>
            </div>
        `).join('');
        
        updateCartSummary();
    }
    
    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }
    
    function updateCartQty(index, delta) {
        cart[index].quantity = Math.max(1, cart[index].quantity + delta);
        const unitPrice = cart[index].unit_price + cart[index].addons_total;
        cart[index].item_total = unitPrice * cart[index].quantity;
        renderCart();
    }
    
    function clearCart() {
        if (cart.length === 0) return;
        if (!confirm('Clear all items from cart?')) return;
        cart = [];
        renderCart();
    }
    
    function updateCartSummary() {
        const subtotal = cart.reduce((sum, item) => sum + item.item_total, 0);
        const tax = subtotal * (taxRate / 100);
        const total = subtotal + tax;
        
        document.getElementById('subtotalDisplay').textContent = currency + subtotal.toFixed(2);
        document.getElementById('taxDisplay').textContent = currency + tax.toFixed(2);
        document.getElementById('totalDisplay').textContent = currency + total.toFixed(2);
        
        const hasItems = cart.length > 0;
        document.getElementById('payCashBtn').disabled = !hasItems;
        document.getElementById('payCardBtn').disabled = !hasItems;
    }
    
    function processPayment(method) {
        if (cart.length === 0) return;
        
        const buttons = document.querySelectorAll('.pay-btn');
        buttons.forEach(b => b.disabled = true);
        
        const payload = {
            items: cart,
            payment_method: method,
            customer_name: document.getElementById('customerName').value,
            customer_phone: document.getElementById('customerPhone').value,
            notes: ''
        };
        
        fetch(`${baseUrl}/counter/sale/create`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('successToken').textContent = data.order.token;
                document.getElementById('successTotal').textContent = currency + data.order.total.toFixed(2);
                document.getElementById('successModal').classList.add('active');
            } else {
                alert(data.message || 'Failed to create order');
                buttons.forEach(b => b.disabled = false);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error creating order');
            buttons.forEach(b => b.disabled = false);
        });
    }
    
    function newOrder() {
        cart = [];
        renderCart();
        document.getElementById('customerName').value = '';
        document.getElementById('customerPhone').value = '';
        document.getElementById('successModal').classList.remove('active');
    }
    
    function printReceipt() {
        // For now, just close the modal
        // In production, this would trigger receipt printing
        alert('Receipt printing not implemented yet');
    }
</script>
@endpush
