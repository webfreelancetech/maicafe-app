@extends('layouts.ecommerce')

@section('title', 'Home - Mai Cafe')

@push('styles')
<style>
    body {
        background: #f5f1eb;
    }
    
    /* Banner Section */
    .banners-wrapper {
        position: relative;
        width: 100vw;
        margin-left: calc(-50vw + 50%);
        margin-right: calc(-50vw + 50%);
        padding: 0;
        overflow: hidden;
    }
    .banners-section {
        position: relative;
        overflow: hidden;
        width: 100%;
        border-radius: 0;
    }
    .banner-slider-container {
        display: flex;
        transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        gap: 0;
        will-change: transform;
        width: 100%;
    }
    .banner-card {
        flex: 0 0 100%;
        min-width: 100%;
        width: 100%;
        border-radius: 0;
        overflow: hidden;
        position: relative;
        box-shadow: none;
    }
    .banner-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        color: #8b6f47;
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        transition: all 0.3s;
    }
    .banner-nav-btn:hover {
        background: #fff;
        transform: translateY(-50%) scale(1.1);
    }
    .banner-nav-btn.prev {
        left: 20px;
    }
    .banner-nav-btn.next {
        right: 20px;
    }
    .banner-dots {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 20px;
        margin-bottom: 20px;
        padding: 0;
    }
    .banner-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #d1d5db;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        padding: 0;
    }
    .banner-dot.active {
        background: #8b6f47;
        width: 24px;
        border-radius: 4px;
    }
    .banner-card {
        position: relative;
        overflow: hidden;
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 60px 40px;
        color: #fff;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
    .banner-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.05) 100%);
        z-index: 1;
    }
    .banner-content {
        position: relative;
        z-index: 2;
        max-width: 600px;
    }
    .banner-content h3 {
        font-size: 18px;
        font-weight: 400;
        font-style: italic;
        margin-bottom: 12px;
        opacity: 0.95;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .banner-content h2 {
        font-size: 42px;
        font-weight: 800;
        margin-bottom: 24px;
        line-height: 1.2;
        text-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }
    .banner-button {
        display: inline-block;
        padding: 14px 28px;
        background: #8b6f47;
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .banner-button:hover {
        background: #6b5233;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.3);
    }
    .banner-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        background: #ef4444;
        color: #fff;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        z-index: 3;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }
    
    /* Categories Section */
    .categories-section {
        padding: 24px 16px;
    }
    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #3d2817;
        margin-bottom: 16px;
        padding: 0 16px;
    }
    .categories-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }
    .category-card {
        background: #fff;
        border-radius: 20px;
        padding: 32px 20px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
        aspect-ratio: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }
    .category-card:active {
        transform: scale(0.95);
    }
    .category-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .category-icon {
        width: 120px;
        height: 120px;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5f1eb;
        border-radius: 20px;
        color: #8b6f47;
        font-size: 48px;
        flex-shrink: 0;
    }
    .category-icon img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 20px;
    }
    .category-name {
        font-size: 14px;
        font-weight: 700;
        color: #3d2817;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* Filter Buttons */
    .filters-section {
        padding: 0 16px 20px;
    }
    .filters-scroll {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .filters-scroll::-webkit-scrollbar {
        display: none;
    }
    .filter-btn {
        padding: 10px 20px;
        border-radius: 20px;
        border: none;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.3s;
        background: #fff;
        color: #3d2817;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .filter-btn.active {
        background: #8b6f47;
        color: #fff;
    }
    .filter-btn:active {
        transform: scale(0.95);
    }
    
    /* Products Section */
    .products-section {
        padding: 0 16px 24px;
    }
    .products-section .section-title {
        padding: 0;
    }
    .products-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    .product-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: transform 0.2s;
        display: flex;
        flex-direction: column;
    }
    .product-card:active {
        transform: scale(0.98);
    }
    .product-image-wrapper {
        background: #f5f1eb;
        padding: 12px;
        border-radius: 16px 16px 0 0;
    }
    .product-image {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 12px;
        background: #f5f1eb;
    }
    .product-info {
        padding: 12px;
        background: #fff;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .product-name {
        font-size: 14px;
        font-weight: 600;
        color: #3d2817;
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.4;
    }
    .product-price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
    }
    .product-price {
        font-size: 16px;
        font-weight: 700;
        color: #3d2817;
    }
    .product-price .old-price {
        font-size: 12px;
        color: #a68b6b;
        text-decoration: line-through;
        margin-left: 6px;
    }
    .add-to-cart-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #8b6f47;
        border: none;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 16px;
        flex-shrink: 0;
    }
    .add-to-cart-btn:active {
        transform: scale(0.9);
    }
    .add-to-cart-btn:hover {
        background: #6b5233;
    }
    
    /* Tablet Styles */
    @media (min-width: 769px) and (max-width: 1023px) {
        .banners-wrapper {
            padding: 0;
        }
        .banner-card {
            flex: 0 0 100%;
            min-width: 100%;
        }
        .banner-slider-container {
            gap: 0;
        }
        .banner-card {
            padding: 80px 60px;
            min-height: 500px;
        }
        .banner-content h2 {
            font-size: 48px;
        }
        .banner-nav-btn {
            width: 45px;
            height: 45px;
            font-size: 20px;
        }
        .categories-section,
        .filters-section,
        .products-section {
            max-width: 100%;
            padding: 24px;
        }
        .categories-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        .category-icon {
            width: 140px;
            height: 140px;
            font-size: 56px;
            margin-bottom: 24px;
        }
        .category-name {
            font-size: 15px;
        }
        .filters-scroll {
            flex-wrap: wrap;
            overflow-x: visible;
        }
        .products-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        .product-image-wrapper {
            padding: 16px;
        }
        .product-image {
            height: 160px;
        }
    }
    
    /* Desktop Styles */
    @media (min-width: 1024px) {
        .banners-wrapper {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }
        .banner-card {
            flex: 0 0 100%;
            min-width: 100%;
            max-width: 100%;
        }
        .banner-slider-container {
            gap: 0;
        }
        .banner-nav-btn {
            width: 50px;
            height: 50px;
            font-size: 22px;
        }
        .banner-card {
            padding: 100px 80px;
            min-height: 600px;
        }
        .banner-content h2 {
            font-size: 56px;
        }
        .banner-content h3 {
            font-size: 20px;
        }
        .categories-section,
        .filters-section,
        .products-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 24px;
        }
        .section-title {
            font-size: 28px;
            margin-bottom: 24px;
        }
        .categories-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .category-card {
            padding: 40px 24px;
        }
        .category-icon {
            width: 160px;
            height: 160px;
            font-size: 64px;
            margin-bottom: 24px;
        }
        .category-name {
            font-size: 16px;
        }
        .filters-scroll {
            flex-wrap: wrap;
            overflow-x: visible;
            gap: 12px;
        }
        .filter-btn {
            padding: 12px 24px;
            font-size: 15px;
            transition: all 0.3s;
        }
        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 24px;
        }
        .product-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }
        .product-image-wrapper {
            padding: 20px;
        }
        .product-image {
            height: 200px;
        }
        .product-info {
            padding: 16px;
        }
        .product-name {
            font-size: 15px;
        }
        .product-price {
            font-size: 18px;
        }
        .add-to-cart-btn {
            width: 40px;
            height: 40px;
            transition: all 0.2s;
        }
        .add-to-cart-btn:hover {
            background: #6b5233;
            transform: scale(1.05);
        }
    }
    
    /* Large Desktop */
    @media (min-width: 1440px) {
        .banners-wrapper {
            padding: 0;
        }
        .banner-card {
            flex: 0 0 100%;
            min-width: 100%;
            max-width: 100%;
        }
        .banner-card {
            padding: 120px 100px;
            min-height: 700px;
        }
        .banner-content h2 {
            font-size: 64px;
        }
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        }
    }
</style>
@endpush

@section('content')
@if(session('success'))
<div style="background: #d1fae5; color: #065f46; padding: 12px; text-align: center; margin: 16px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<!-- Promotional Banners -->
@if($banners->count() > 0)
<div class="banners-wrapper">
    <div class="banners-section" id="bannersSection">
        <div class="banner-slider-container" id="bannerSlider">
            @foreach($banners as $index => $banner)
            <div class="banner-card" @if($banner->image) style="background-image: url('{{ asset('storage/' . $banner->image) }}');" @else style="background: linear-gradient(135deg, #87ceeb 0%, #6bb6ff 100%);" @endif>
                <div class="banner-content">
                    @if($banner->title)
                    <h3>{{ $banner->title }}</h3>
                    @endif
                    @if($banner->subtitle)
                    <h2>{{ $banner->subtitle }}</h2>
                    @endif
                    @if($banner->button_text && $banner->button_link)
                    <a href="{{ $banner->button_link }}" class="banner-button">
                        {{ $banner->button_text }}
                    </a>
                    @endif
                </div>
                @if(!$banner->image && $index % 2 === 1)
                <div class="banner-badge">TASTY BURGER</div>
                @endif
            </div>
            @endforeach
        </div>
        
        @if($banners->count() > 1)
        <button class="banner-nav-btn prev" id="bannerPrev">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="banner-nav-btn next" id="bannerNext">
            <i class="fas fa-chevron-right"></i>
        </button>
        @endif
    </div>
    
    @if($banners->count() > 1)
    <div class="banner-dots" id="bannerDots">
        @foreach($banners as $index => $banner)
        <button class="banner-dot {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}"></button>
        @endforeach
    </div>
    @endif
</div>
@endif

<!-- Categories Section -->
@if($categories->count() > 0)
<div class="categories-section">
    <h2 class="section-title">Categories</h2>
    <div class="categories-grid">
        @foreach($categories as $category)
        <a href="{{ route('menu', ['category' => $category->id]) }}" class="category-card">
            <div class="category-icon" style="background: {{ ['#10b981', '#ef4444', '#f59e0b', '#8b5cf6', '#3b82f6'][$loop->index % 5] }};">
                @if($category->image)
                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                @else
                <i class="fas fa-utensils" style="color: #fff;"></i>
                @endif
            </div>
            <div class="category-name">{{ strtoupper($category->name) }}</div>
        </a>
        @endforeach
    </div>
</div>
@endif

<!-- Filter Buttons -->
<div class="filters-section">
    <div class="filters-scroll">
        <button class="filter-btn active" onclick="filterProducts('all')">All</button>
        <button class="filter-btn" onclick="filterProducts('coffee')">Coffee</button>
        <button class="filter-btn" onclick="filterProducts('drinks')">Drinks</button>
        <button class="filter-btn" onclick="filterProducts('food')">Food</button>
        <button class="filter-btn" onclick="filterProducts('combos')">Combos</button>
        <button class="filter-btn" onclick="filterProducts('desserts')">Desserts</button>
    </div>
</div>

<!-- Featured Products -->
@if($featuredProducts->count() > 0)
<div class="products-section">
    <h2 class="section-title">More Coffee</h2>
    <div class="products-grid" id="productsGrid">
        @foreach($featuredProducts as $product)
        <div class="product-card" data-category="{{ strtolower($product->category->name ?? 'all') }}">
            <div class="product-image-wrapper">
                @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                @else
                <div class="product-image" style="display: flex; align-items: center; justify-content: center; color: #a68b6b;">
                    <i class="fas fa-image" style="font-size: 48px;"></i>
                </div>
                @endif
            </div>
            <div class="product-info">
                <div class="product-name">{{ $product->name }}</div>
                <div class="product-price-row">
                    <div class="product-price">
                        {{ $currencySymbol }}{{ number_format($product->price, 2) }}
                        @if($product->compare_price && $product->compare_price > $product->price)
                        <span class="old-price">{{ $currencySymbol }}{{ number_format($product->compare_price, 2) }}</span>
                        @endif
                    </div>
                    <button class="add-to-cart-btn" onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, '{{ $product->image ?? '' }}')">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Banner Slider Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const bannerSlider = document.getElementById('bannerSlider');
        const bannerPrev = document.getElementById('bannerPrev');
        const bannerNext = document.getElementById('bannerNext');
        const bannerDots = document.querySelectorAll('.banner-dot');
        const banners = document.querySelectorAll('.banner-card');
        
        if (!bannerSlider || banners.length === 0) return;
        
        let currentSlide = 0;
        const totalSlides = banners.length;
        
        function getSlidesPerView() {
            return 1; // Always show one banner at a time for full width
        }
        
        function updateSlider() {
            const slidesPerView = getSlidesPerView();
            const maxSlide = Math.max(0, totalSlides - slidesPerView);
            const cardWidth = 100 / slidesPerView;
            const gapPercent = (12 / bannerSlider.parentElement.offsetWidth) * 100;
            const translateX = -(currentSlide * (cardWidth + (gapPercent / slidesPerView)));
            
            bannerSlider.style.transform = `translateX(${translateX}%)`;
            
            // Update dots
            bannerDots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });
            
            // Show/hide navigation buttons
            if (bannerPrev) bannerPrev.style.display = currentSlide === 0 ? 'none' : 'flex';
            if (bannerNext) bannerNext.style.display = currentSlide >= maxSlide ? 'none' : 'flex';
        }
        
        function nextSlide() {
            const slidesPerView = getSlidesPerView();
            const maxSlide = Math.max(0, totalSlides - slidesPerView);
            if (currentSlide < maxSlide) {
                currentSlide++;
                updateSlider();
            } else if (currentSlide < totalSlides - 1) {
                currentSlide = totalSlides - 1;
                updateSlider();
            }
        }
        
        function prevSlide() {
            if (currentSlide > 0) {
                currentSlide--;
                updateSlider();
            }
        }
        
        function goToSlide(index) {
            const slidesPerView = getSlidesPerView();
            const maxSlide = Math.max(0, totalSlides - slidesPerView);
            currentSlide = Math.min(index, maxSlide);
            updateSlider();
        }
        
        // Navigation buttons
        if (bannerNext) {
            bannerNext.addEventListener('click', nextSlide);
        }
        
        if (bannerPrev) {
            bannerPrev.addEventListener('click', prevSlide);
        }
        
        // Dot navigation
        bannerDots.forEach((dot, index) => {
            dot.addEventListener('click', () => goToSlide(index));
        });
        
        // Touch/Swipe support
        let touchStartX = 0;
        let touchEndX = 0;
        
        bannerSlider.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        
        bannerSlider.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });
        
        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;
            
            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
        }
        
        // Auto-play (optional - can be enabled)
        // let autoPlayInterval = setInterval(nextSlide, 5000);
        // bannerSlider.addEventListener('mouseenter', () => clearInterval(autoPlayInterval));
        // bannerSlider.addEventListener('mouseleave', () => {
        //     autoPlayInterval = setInterval(nextSlide, 5000);
        // });
        
        // Handle resize
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                const slidesPerView = getSlidesPerView();
                const maxSlide = Math.max(0, totalSlides - slidesPerView);
                if (currentSlide > maxSlide) {
                    currentSlide = maxSlide;
                }
                updateSlider();
            }, 250);
        });
        
        // Initialize
        updateSlider();
    });
    
    function filterProducts(category) {
        // Update active button
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');
        
        // Filter products (simple client-side filter)
        const products = document.querySelectorAll('.product-card');
        products.forEach(product => {
            if (category === 'all') {
                product.style.display = 'block';
            } else {
                const productCategory = product.getAttribute('data-category');
                if (productCategory.includes(category)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            }
        });
    }
    
    function addToCart(productId, productName, price, image) {
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const existingItem = cart.find(item => item.id === productId);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                id: productId,
                name: productName,
                price: price,
                image: image,
                quantity: 1
            });
        }
        
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Update cart count
        const count = cart.reduce((sum, item) => sum + item.quantity, 0);
        const badge = document.getElementById('bottomNavCartCount');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        }
        
        // Show feedback
        const btn = event.target.closest('.add-to-cart-btn');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        btn.style.background = '#10b981';
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.style.background = '#8b6f47';
        }, 1000);
        
        // Trigger storage event for other tabs
        window.dispatchEvent(new Event('storage'));
    }
</script>
@endpush
