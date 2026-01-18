<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>@yield('title', 'Mai Cafe')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f1eb;
            color: #3d2817;
            padding-bottom: 80px;
        }
        
        /* Header with User Greeting */
        .app-header {
            background: #f5f1eb;
            padding: 20px 16px 16px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .user-greeting {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8b6f47 0%, #6b5233 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 18px;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .greeting-text h2 {
            font-size: 20px;
            font-weight: 700;
            color: #3d2817;
            margin-bottom: 2px;
        }
        .greeting-text p {
            font-size: 13px;
            color: #8b6f47;
            font-weight: 500;
        }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .header-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8b6f47;
            position: relative;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .header-icon .badge {
            position: absolute;
            top: -4px;
            right: -4px;
            width: 12px;
            height: 12px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid #fff;
        }
        .menu-toggle {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8b6f47;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        /* Search Bar */
        .search-section {
            padding: 0 16px 16px;
        }
        .search-container {
            display: flex;
            gap: 10px;
        }
        .search-box {
            flex: 1;
            position: relative;
        }
        .search-box input {
            width: 100%;
            padding: 14px 16px 14px 45px;
            border: none;
            border-radius: 12px;
            background: #fff;
            font-size: 14px;
            color: #3d2817;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .search-box input::placeholder {
            color: #a68b6b;
        }
        .search-box input:focus {
            outline: none;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        }
        .search-box i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #8b6f47;
        }
        .filter-btn {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: #8b6f47;
            border: none;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            padding: 8px 0;
        }
        .bottom-nav-items {
            display: flex;
            justify-content: space-around;
            align-items: center;
        }
        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            color: #a68b6b;
            font-size: 11px;
            padding: 8px 12px;
            transition: color 0.3s;
            flex: 1;
            position: relative;
        }
        .bottom-nav-item.active {
            color: #8b6f47;
        }
        .bottom-nav-item i {
            font-size: 22px;
        }
        .bottom-nav-item .badge {
            position: absolute;
            top: 4px;
            right: 8px;
            background: #ef4444;
            color: #fff;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            font-weight: 600;
        }
        
        /* Mobile Menu */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
        }
        .mobile-menu-content {
            position: absolute;
            top: 0;
            right: 0;
            width: 280px;
            height: 100%;
            background: #fff;
            padding: 20px;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
            overflow-y: auto;
        }
        .mobile-menu-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
        }
        .mobile-menu-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #3d2817;
            cursor: pointer;
        }
        .mobile-menu-links {
            list-style: none;
        }
        .mobile-menu-links li {
            margin-bottom: 15px;
        }
        .mobile-menu-links a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px;
            color: #3d2817;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s;
            font-weight: 500;
        }
        .mobile-menu-links a:hover,
        .mobile-menu-links a.active {
            background: #f5f1eb;
            color: #8b6f47;
        }
        
        /* Desktop Header */
        .desktop-header {
            display: none;
        }
        .desktop-top-bar {
            background: #8b6f47;
            color: #fff;
            padding: 12px 0;
        }
        .desktop-top-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .desktop-top-left {
            display: flex;
            gap: 30px;
            align-items: center;
            font-size: 14px;
        }
        .desktop-top-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .desktop-top-bar a {
            color: #fff;
            text-decoration: none;
        }
        .desktop-nav {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .desktop-nav-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
        }
        .desktop-logo {
            font-size: 28px;
            font-weight: 800;
            color: #8b6f47;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .desktop-nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }
        .desktop-nav-links a {
            text-decoration: none;
            color: #3d2817;
            font-weight: 500;
            font-size: 15px;
            transition: color 0.3s;
        }
        .desktop-nav-links a:hover {
            color: #8b6f47;
        }
        .desktop-cart-icon {
            position: relative;
            color: #3d2817;
            font-size: 20px;
            text-decoration: none;
        }
        .desktop-cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: #fff;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
        }
        .desktop-order-btn {
            background: #8b6f47;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
        }
        .desktop-order-btn:hover {
            background: #6b5233;
        }
        
        /* Responsive Breakpoints */
        @media (min-width: 769px) {
            .app-header {
                display: none;
            }
            .bottom-nav {
                display: none;
            }
            .desktop-header {
                display: block;
            }
            body {
                padding-bottom: 0;
            }
        }
        
        @media (min-width: 1024px) {
            .desktop-nav-content {
                padding: 20px 40px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Mobile Header -->
    <div class="app-header">
        <div class="header-top">
            <div class="user-greeting">
                <div class="user-avatar">
                    @auth
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    @else
                        <i class="fas fa-user"></i>
                    @endauth
                </div>
                <div class="greeting-text">
                    <h2>Hello, {{ Auth::user()->name ?? 'Guest' }}!</h2>
                    <p id="greetingTime">Good {{ date('H') < 12 ? 'Morning' : (date('H') < 18 ? 'Afternoon' : 'Evening') }}</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="#" class="header-icon">
                    <i class="fas fa-bell"></i>
                    <span class="badge"></span>
                </a>
                <button class="menu-toggle" id="mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
        <div class="search-section">
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Eg:- Coffee, Drinks etc..." id="searchInput">
                </div>
                <button class="filter-btn" onclick="window.location.href='{{ route('menu') }}'">
                    <i class="fas fa-sliders-h"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Desktop Header -->
    <div class="desktop-header">
        <div class="desktop-top-bar">
            <div class="desktop-top-content">
                <div class="desktop-top-left">
                    <span><i class="fas fa-phone"></i> Call and Order: <a href="tel:910-344-7520" style="font-weight: 600;">910-344-7520</a></span>
                </div>
                <div class="desktop-top-right">
                    @auth
                        <span><i class="fas fa-user"></i> {{ Auth::user()->name }}</span>
                    @else
                        <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Login</a>
                        <a href="{{ route('register') }}"><i class="fas fa-user-plus"></i> Register</a>
                    @endauth
                </div>
            </div>
        </div>
        <div class="desktop-nav">
            <div class="desktop-nav-content">
                <a href="{{ route('home') }}" class="desktop-logo">
                    <i class="fas fa-utensils"></i> Mai Cafe
                </a>
                <div class="desktop-nav-links">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                    <a href="{{ route('menu') }}" class="{{ request()->routeIs('menu') ? 'active' : '' }}">Menu</a>
                    <a href="{{ route('stores') }}" class="{{ request()->routeIs('stores') ? 'active' : '' }}">Stores</a>
                    <a href="{{ route('order-display') }}" class="{{ request()->routeIs('order-display') ? 'active' : '' }}">Order Status</a>
                    @auth
                        <div class="user-dropdown" style="position: relative; display: inline-block;">
                            <a href="#" style="text-decoration: none; color: #3d2817;">
                                <i class="fas fa-user-circle" style="font-size: 20px;"></i>
                            </a>
                            <div class="user-menu" id="desktopUserDropdown">
                                <div class="user-menu-header">
                                    <div style="font-weight: 600;">{{ Auth::user()->name }}</div>
                                    <div style="font-size: 12px; color: #666;">{{ Auth::user()->email }}</div>
                                </div>
                                <a href="#" class="user-menu-item">
                                    <i class="fas fa-user" style="margin-right: 8px;"></i> My Account
                                </a>
                                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="user-menu-item logout" style="width: 100%; text-align: left; border: none; background: none; cursor: pointer;">
                                        <i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                    <a href="{{ route('cart') }}" class="desktop-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="desktop-cart-badge" id="desktopCartCount">0</span>
                    </a>
                    <a href="{{ route('menu') }}" class="desktop-order-btn">Order Now</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-content">
            <div class="mobile-menu-header">
                <h3 style="color: #8b6f47; font-size: 20px;">Mai Cafe</h3>
                <button class="mobile-menu-close" id="mobileMenuClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <ul class="mobile-menu-links">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="{{ route('menu') }}" class="{{ request()->routeIs('menu') ? 'active' : '' }}"><i class="fas fa-utensils"></i> Menu</a></li>
                <li><a href="{{ route('stores') }}"><i class="fas fa-store"></i> Stores</a></li>
                <li><a href="{{ route('order-display') }}"><i class="fas fa-receipt"></i> Order Status</a></li>
                <li><a href="{{ route('cart') }}"><i class="fas fa-shopping-cart"></i> Cart <span id="mobileCartCount" style="margin-left: auto; background: #ef4444; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 11px;">0</span></a></li>
                @auth
                    <li><a href="#"><i class="fas fa-user"></i> My Account</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" style="width: 100%; text-align: left; background: none; border: none; color: #ef4444; cursor: pointer; padding: 12px; display: flex; align-items: center; gap: 15px; font-weight: 500;">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <li><a href="{{ route('register') }}"><i class="fas fa-user-plus"></i> Register</a></li>
                @endauth
            </ul>
        </div>
    </div>
    
    <main>
        @yield('content')
    </main>

    <!-- Bottom Navigation (Mobile) -->
    <nav class="bottom-nav">
        <div class="bottom-nav-items">
            <a href="{{ route('home') }}" class="bottom-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="{{ route('menu') }}" class="bottom-nav-item {{ request()->routeIs('menu') ? 'active' : '' }}">
                <i class="fas fa-utensils"></i>
                <span>Menu</span>
            </a>
            <a href="{{ route('order-display') }}" class="bottom-nav-item {{ request()->routeIs('order-display') ? 'active' : '' }}">
                <i class="fas fa-receipt"></i>
                <span>Status</span>
            </a>
            <a href="{{ route('cart') }}" class="bottom-nav-item {{ request()->routeIs('cart') ? 'active' : '' }}" style="position: relative;">
                <i class="fas fa-shopping-cart"></i>
                <span>Cart</span>
                <span class="badge" id="bottomNavCartCount" style="display: none;">0</span>
            </a>
            @auth
                <a href="#" class="bottom-nav-item">
                    <i class="fas fa-user"></i>
                    <span>Account</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="bottom-nav-item">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </a>
            @endauth
        </div>
    </nav>
    
    @stack('scripts')
    <script>
        // Mobile Menu Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileMenuClose = document.getElementById('mobileMenuClose');
            
            if (mobileMenuToggle && mobileMenu) {
                mobileMenuToggle.addEventListener('click', function() {
                    mobileMenu.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                });
            }
            
            if (mobileMenuClose && mobileMenu) {
                mobileMenuClose.addEventListener('click', function() {
                    mobileMenu.style.display = 'none';
                    document.body.style.overflow = '';
                });
            }
            
            if (mobileMenu) {
                mobileMenu.addEventListener('click', function(e) {
                    if (e.target === mobileMenu) {
                        mobileMenu.style.display = 'none';
                        document.body.style.overflow = '';
                    }
                });
            }

            // Update cart count
            function updateCartCount() {
                const cart = JSON.parse(localStorage.getItem('cart') || '[]');
                const count = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
                const cartCountEl = document.getElementById('bottomNavCartCount');
                const mobileCartCount = document.getElementById('mobileCartCount');
                const desktopCartCount = document.getElementById('desktopCartCount');
                
                if (cartCountEl) {
                    if (count > 0) {
                        cartCountEl.textContent = count;
                        cartCountEl.style.display = 'flex';
                    } else {
                        cartCountEl.style.display = 'none';
                    }
                }
                if (mobileCartCount) {
                    mobileCartCount.textContent = count;
                }
                if (desktopCartCount) {
                    desktopCartCount.textContent = count;
                    desktopCartCount.style.display = count > 0 ? 'flex' : 'none';
                }
            }
            updateCartCount();
            window.addEventListener('storage', updateCartCount);

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        window.location.href = '{{ route('menu') }}?search=' + encodeURIComponent(this.value);
                    }
                });
            }

            // Desktop user dropdown
            const desktopUserToggle = document.querySelector('.desktop-nav-links .user-dropdown a');
            const desktopUserDropdown = document.getElementById('desktopUserDropdown');
            
            if (desktopUserToggle && desktopUserDropdown) {
                desktopUserToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    desktopUserDropdown.classList.toggle('show');
                });
                
                document.addEventListener('click', function(e) {
                    if (!desktopUserToggle.contains(e.target) && !desktopUserDropdown.contains(e.target)) {
                        desktopUserDropdown.classList.remove('show');
                    }
                });
            }
        });
    </script>
</body>
</html>
