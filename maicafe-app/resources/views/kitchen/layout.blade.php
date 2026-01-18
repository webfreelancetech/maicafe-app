<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kitchen') - Mai Cafe</title>
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
            background: #1a1a2e;
            color: #fff;
            min-height: 100vh;
        }
        
        .header {
            background: #16213e;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0f3460;
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
            color: #e94560;
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
            background: #0f3460;
            color: #fff;
        }
        
        .nav-links a.active {
            background: #e94560;
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
            color: #10b981;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: #0f3460;
            border-radius: 8px;
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #e94560 0%, #c73659 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
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
            background: #16213e;
            padding: 16px 24px;
            border-radius: 12px;
            text-align: center;
            flex: 1;
            border: 2px solid transparent;
        }
        
        .stat-item.pending { border-color: #f59e0b; }
        .stat-item.preparing { border-color: #3b82f6; }
        .stat-item.ready { border-color: #10b981; }
        .stat-item.completed { border-color: #6b7280; }
        
        .stat-value {
            font-size: 36px;
            font-weight: 800;
            line-height: 1;
        }
        
        .stat-item.pending .stat-value { color: #f59e0b; }
        .stat-item.preparing .stat-value { color: #3b82f6; }
        .stat-item.ready .stat-value { color: #10b981; }
        .stat-item.completed .stat-value { color: #6b7280; }
        
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
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        
        @yield('styles')
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <a href="{{ route('kitchen.dashboard') }}" class="logo">
                <i class="fas fa-utensils"></i>
                <span>Kitchen</span>
            </a>
            <nav class="nav-links">
                <a href="{{ route('kitchen.dashboard') }}" class="{{ request()->routeIs('kitchen.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tv"></i> Live Orders
                </a>
                <a href="{{ route('kitchen.orders') }}" class="{{ request()->routeIs('kitchen.orders*') ? 'active' : '' }}">
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
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ auth()->user()->role }}</div>
                </div>
            </div>
            <form action="{{ route('kitchen.logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
    
    <div class="main-content">
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
