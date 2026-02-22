<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Counter') - Mai Cafe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: #1e3a5f;
            color: #fff;
            min-height: 100vh;
        }
        
        .header {
            background: #0d2137;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #1e5f74;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        
        .logo {
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            text-decoration: none;
        }
        
        .logo i {
            color: #22c55e;
        }
        
        .nav-links {
            display: flex;
            gap: 8px;
        }
        
        .nav-links a {
            padding: 10px 16px;
            color: #a0a0a0;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .nav-links a:hover,
        .nav-links a.active {
            background: #1e5f74;
            color: #fff;
        }
        
        .nav-links a.active {
            background: #22c55e;
            color: #000;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .current-time {
            font-size: 24px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            color: #22c55e;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: #1e5f74;
            border-radius: 8px;
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            color: #000;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 14px;
        }
        
        .user-role {
            font-size: 11px;
            color: #a0a0a0;
            text-transform: uppercase;
        }
        
        .logout-btn {
            padding: 10px 16px;
            background: transparent;
            border: 2px solid #ef4444;
            color: #ef4444;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .logout-btn:hover {
            background: #ef4444;
            color: #fff;
        }
        
        .main-content {
            padding: 16px;
            min-height: calc(100vh - 70px);
        }
        
        /* Stats bar */
        .stats-bar {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
        }
        
        .stat-item {
            background: #0d2137;
            padding: 16px 24px;
            border-radius: 12px;
            text-align: center;
            flex: 1;
            border: 2px solid transparent;
        }
        
        .stat-item.awaiting { border-color: #f59e0b; }
        .stat-item.paid { border-color: #22c55e; }
        .stat-item.collected { border-color: #3b82f6; }
        .stat-item.pending { border-color: #f97316; }
        
        .stat-value {
            font-size: 36px;
            font-weight: 800;
            line-height: 1;
        }
        
        .stat-item.awaiting .stat-value { color: #f59e0b; }
        .stat-item.paid .stat-value { color: #22c55e; }
        .stat-item.collected .stat-value { color: #3b82f6; }
        .stat-item.pending .stat-value { color: #f97316; }
        
        .stat-label {
            font-size: 12px;
            color: #a0a0a0;
            text-transform: uppercase;
            margin-top: 4px;
            letter-spacing: 1px;
        }
        
        /* Refresh indicator */
        .refresh-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #a0a0a0;
        }
        
        .refresh-indicator .dot {
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        
        /* Alert messages */
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: rgba(34, 197, 94, 0.2);
            border: 1px solid #22c55e;
            color: #22c55e;
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid #ef4444;
            color: #ef4444;
        }
        
        @yield('styles')
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <a href="{{ route('counter.dashboard') }}" class="logo">
                <i class="fas fa-cash-register"></i>
                <span>Counter</span>
            </a>
            <nav class="nav-links">
                <a href="{{ route('counter.sale') }}" class="{{ request()->routeIs('counter.sale') ? 'active' : '' }}">
                    <i class="fas fa-cash-register"></i> New Sale
                </a>
                <a href="{{ route('counter.dashboard') }}" class="{{ request()->routeIs('counter.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave"></i> Payment Queue
                </a>
                <a href="{{ route('counter.orders') }}" class="{{ request()->routeIs('counter.orders*') ? 'active' : '' }}">
                    <i class="fas fa-list"></i> All Orders
                </a>
            </nav>
        </div>
        
        <div class="header-right">
            <div class="refresh-indicator">
                <span class="dot"></span>
                <span>Auto-refresh</span>
            </div>
            <div class="current-time" id="currentTime">00:00:00</div>
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::guard('counter')->user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="user-name">{{ Auth::guard('counter')->user()->name }}</div>
                    <div class="user-role">{{ Auth::guard('counter')->user()->role }}</div>
                </div>
            </div>
            <form action="{{ route('counter.logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
    
    <div class="main-content">
        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
        @endif
        
        @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
        @endif
        
        @yield('content')
    </div>
    
    <script>
        // Update time
        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-GB', { hour12: false });
        }
        setInterval(updateTime, 1000);
        updateTime();
        
        // CSRF token for AJAX
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    </script>
    @stack('scripts')
</body>
</html>
