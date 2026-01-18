<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mai Cafe Admin')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            background: #f8fafc; 
            color: #1e293b;
            overflow-x: hidden;
        }
        
        /* Top Navigation */
        .top-nav {
            position: fixed;
            top: 0;
            left: 260px;
            right: 0;
            height: 70px;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            padding: 0 24px;
            z-index: 999;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .top-nav-left {
            display: flex;
            align-items: center;
            gap: 20px;
            flex: 1;
        }
        .search-box {
            position: relative;
            width: 100%;
            max-width: 400px;
        }
        .search-box input {
            width: 100%;
            padding: 10px 16px 10px 40px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            background: #f8fafc;
            transition: all 0.3s;
        }
        .search-box input:focus {
            outline: none;
            border-color: #3b82f6;
            background: #fff;
        }
        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        .top-nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .nav-icon {
            position: relative;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
            background: #f8fafc;
        }
        .nav-icon:hover {
            background: #f1f5f9;
            color: #3b82f6;
        }
        .nav-icon .badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #ef4444;
            color: #fff;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 600;
        }
        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .user-profile:hover {
            background: #f8fafc;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 16px;
        }
        .user-info {
            display: flex;
            flex-direction: column;
        }
        .user-name {
            font-weight: 600;
            font-size: 14px;
            color: #1e293b;
        }
        .user-role {
            font-size: 12px;
            color: #64748b;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;
            background: #fff;
            border-right: 1px solid #e2e8f0;
            overflow-y: auto;
            z-index: 1000;
        }
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 18px;
        }
        .sidebar-header h1 {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
        }
        .sidebar-nav {
            padding: 12px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            color: #64748b;
            text-decoration: none;
            margin-bottom: 4px;
            transition: all 0.2s;
            font-size: 14px;
            font-weight: 500;
        }
        .nav-item:hover {
            background: #f8fafc;
            color: #3b82f6;
        }
        .nav-item.active {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: #fff;
        }
        .nav-item i {
            width: 20px;
            text-align: center;
            font-size: 18px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 260px;
            margin-top: 70px;
            padding: 24px;
            min-height: calc(100vh - 70px);
        }
        
        /* Dashboard Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
        }
        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
        }
        .stat-icon.sales { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .stat-icon.income { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .stat-icon.visitor { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
        .stat-icon.products { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
        .stat-chart {
            width: 120px;
            height: 60px;
        }
        .stat-title {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 8px;
        }
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }
        .stat-change {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        .stat-change.positive {
            color: #10b981;
        }
        .stat-change.negative {
            color: #ef4444;
        }
        
        /* Goal Card */
        .goal-card {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 12px;
            padding: 24px;
            color: #fff;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }
        .goal-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.6; }
        }
        .goal-content {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 24px;
            align-items: center;
        }
        .goal-text h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .goal-text p {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 16px;
        }
        .goal-progress {
            width: 120px;
            height: 120px;
            position: relative;
        }
        .goal-progress-circle {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: conic-gradient(from 0deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.3) 67%, rgba(255,255,255,0.1) 67%, rgba(255,255,255,0.1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .goal-progress-inner {
            width: 80%;
            height: 80%;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
        }
        .goal-link {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        
        /* Content Cards */
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0;
            margin-bottom: 24px;
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .card-header h3 {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
        }
        .card-actions {
            display: flex;
            gap: 8px;
        }
        .card-action-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #64748b;
            transition: all 0.2s;
        }
        .card-action-btn:hover {
            background: #f8fafc;
            color: #3b82f6;
        }
        
        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th {
            text-align: left;
            padding: 12px;
            background: #f8fafc;
            font-weight: 600;
            color: #475569;
            font-size: 13px;
            border-bottom: 1px solid #e2e8f0;
        }
        .data-table td {
            padding: 16px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
            color: #1e293b;
        }
        .data-table tr:hover {
            background: #f8fafc;
        }
        .table-product {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .table-product img {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
        }
        .table-product-name {
            font-weight: 500;
            color: #1e293b;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #3b82f6;
            color: #fff;
        }
        .btn-primary:hover {
            background: #2563eb;
        }
        
        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .page-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
        }
        
        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }
        
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="number"],
        .form-group input[type="password"],
        .form-group input[type="date"],
        .form-group input[type="datetime-local"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            color: #1e293b;
            background: #fff;
            transition: all 0.2s;
            font-family: inherit;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-group input[type="number"] {
            -moz-appearance: textfield;
        }
        
        .form-group input[type="number"]::-webkit-outer-spin-button,
        .form-group input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #94a3b8;
        }
        
        .form-group select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 40px;
        }
        
        .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            color: #64748b;
            background: #f8fafc;
            cursor: pointer;
        }
        
        .form-group input[type="file"]:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-group input[type="file"]::file-selector-button {
            padding: 8px 16px;
            margin-right: 12px;
            border: none;
            border-radius: 6px;
            background: #3b82f6;
            color: #fff;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .form-group input[type="file"]::file-selector-button:hover {
            background: #2563eb;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #3b82f6;
        }
        
        .checkbox-group label {
            margin: 0;
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }
        
        .form-error {
            color: #ef4444;
            font-size: 13px;
            margin-top: 6px;
            display: block;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .top-nav {
                left: 0;
            }
            .main-content {
                margin-left: 0;
            }
        }
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .goal-content {
                grid-template-columns: 1fr;
            }
            .goal-progress {
                margin: 0 auto;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">MC</div>
            <h1>Mai Cafe</h1>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.products.index') }}" class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="fas fa-box"></i>
                <span>Products</span>
            </a>
            <a href="{{ route('admin.categories.index') }}" class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="fas fa-tag"></i>
                <span>Categories</span>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-bag"></i>
                <span>Orders</span>
            </a>
            <a href="{{ route('admin.stores.index') }}" class="nav-item {{ request()->routeIs('admin.stores.*') ? 'active' : '' }}">
                <i class="fas fa-store"></i>
                <span>Stores</span>
            </a>
            <a href="{{ route('admin.customers.index') }}" class="nav-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Customers</span>
            </a>
            <a href="{{ route('admin.coupons.index') }}" class="nav-item {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                <i class="fas fa-ticket-alt"></i>
                <span>Coupons</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
            <a href="{{ route('admin.banners.index') }}" class="nav-item {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                <i class="fas fa-image"></i>
                <span>Banners</span>
            </a>
            <a href="{{ route('admin.addons.index') }}" class="nav-item {{ request()->routeIs('admin.addons.*') ? 'active' : '' }}">
                <i class="fas fa-puzzle-piece"></i>
                <span>Add-ons & Extras</span>
            </a>
        </nav>
    </div>
    
    <!-- Top Navigation -->
    <div class="top-nav">
        <div class="top-nav-left">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search here...">
            </div>
        </div>
        <div class="top-nav-right">
            <div class="nav-icon">
                <i class="fas fa-bell"></i>
                <span class="badge">1</span>
            </div>
            <div class="nav-icon">
                <i class="fas fa-comment"></i>
                <span class="badge">1</span>
            </div>
            <div class="nav-icon">
                <i class="fas fa-expand"></i>
            </div>
            <div class="nav-icon">
                <i class="fas fa-th"></i>
            </div>
            <div class="user-profile">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                    <div class="user-role">Admin</div>
                </div>
            </div>
            <div class="nav-icon">
                <i class="fas fa-cog"></i>
            </div>
        </div>
    </div>
    
    <div class="main-content">
        @yield('content')
    </div>
    
    @stack('scripts')
</body>
</html>
