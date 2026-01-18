<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Display - Mai Cafe</title>
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
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0f3460;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .header h1 i {
            color: #e94560;
        }
        
        .header-info {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        
        .current-time {
            font-size: 32px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
        }
        
        .current-date {
            font-size: 14px;
            color: #a0a0a0;
        }
        
        .header-actions {
            display: flex;
            gap: 12px;
        }
        
        .header-actions a {
            padding: 10px 20px;
            background: #0f3460;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .header-actions a:hover {
            background: #e94560;
        }
        
        .main-content {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            padding: 16px;
            height: calc(100vh - 80px);
        }
        
        .column {
            background: #16213e;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .column-header {
            padding: 16px;
            font-weight: 700;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .column-header .count {
            background: rgba(255,255,255,0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
        }
        
        .column.pending .column-header { background: #f59e0b; color: #000; }
        .column.preparing .column-header { background: #3b82f6; }
        .column.ready .column-header { background: #10b981; }
        .column.completed .column-header { background: #6b7280; }
        
        .column-content {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
        }
        
        .order-card {
            background: #1a1a2e;
            border-radius: 10px;
            margin-bottom: 12px;
            overflow: hidden;
            border: 2px solid transparent;
            transition: all 0.3s;
        }
        
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        
        .column.pending .order-card { border-color: #f59e0b; }
        .column.preparing .order-card { border-color: #3b82f6; }
        .column.ready .order-card { border-color: #10b981; }
        
        .order-card-header {
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255,255,255,0.05);
        }
        
        .token-badge {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 2px;
        }
        
        .column.pending .token-badge { color: #f59e0b; }
        .column.preparing .token-badge { color: #3b82f6; }
        .column.ready .token-badge { color: #10b981; }
        .column.completed .token-badge { color: #6b7280; font-size: 20px; }
        
        .order-meta {
            text-align: right;
        }
        
        .order-type {
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .order-type.delivery { background: #3b82f6; }
        .order-type.pickup { background: #f59e0b; color: #000; }
        
        .order-time {
            font-size: 12px;
            color: #a0a0a0;
            margin-top: 4px;
        }
        
        .order-items {
            padding: 12px 16px;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-name {
            font-weight: 600;
            font-size: 14px;
        }
        
        .item-qty {
            background: #e94560;
            color: #fff;
            padding: 2px 10px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 14px;
        }
        
        .item-details {
            font-size: 12px;
            color: #a0a0a0;
            margin-top: 4px;
        }
        
        .item-variant {
            color: #60a5fa;
        }
        
        .item-addons {
            color: #34d399;
        }
        
        .order-actions {
            padding: 12px 16px;
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 13px;
        }
        
        .action-btn.next {
            background: #10b981;
            color: #fff;
        }
        
        .action-btn.next:hover {
            background: #059669;
        }
        
        .action-btn.complete {
            background: #6b7280;
            color: #fff;
        }
        
        .action-btn.complete:hover {
            background: #4b5563;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 12px;
            opacity: 0.5;
        }
        
        /* Auto-refresh indicator */
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
        
        /* Scrollbar */
        .column-content::-webkit-scrollbar {
            width: 6px;
        }
        
        .column-content::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
        }
        
        .column-content::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
        }
        
        /* Sound notification toggle */
        .sound-toggle {
            background: none;
            border: 2px solid #0f3460;
            color: #fff;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.2s;
        }
        
        .sound-toggle:hover {
            background: #0f3460;
        }
        
        .sound-toggle.active {
            background: #10b981;
            border-color: #10b981;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-utensils"></i> Kitchen Display</h1>
        <div class="header-info">
            <div class="refresh-indicator">
                <span class="dot"></span>
                <span>Auto-refresh</span>
            </div>
            <div>
                <div class="current-time" id="currentTime">00:00:00</div>
                <div class="current-date">{{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}</div>
            </div>
        </div>
        <div class="header-actions">
            <button class="sound-toggle" id="soundToggle" title="Toggle sound notifications">
                <i class="fas fa-volume-up"></i>
            </button>
            <a href="{{ route('admin.orders.index') }}"><i class="fas fa-list"></i> All Orders</a>
            <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
        </div>
    </div>
    
    <div class="main-content">
        <!-- Pending/New Orders -->
        <div class="column pending">
            <div class="column-header">
                <span><i class="fas fa-clock"></i> New Orders</span>
                <span class="count">{{ $pendingOrders->count() }}</span>
            </div>
            <div class="column-content" id="pendingOrders">
                @forelse($pendingOrders as $order)
                @include('admin.orders._kitchen_card', ['order' => $order, 'nextStatus' => 'preparing', 'nextLabel' => 'Start Preparing'])
                @empty
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No new orders</p>
                </div>
                @endforelse
            </div>
        </div>
        
        <!-- Preparing -->
        <div class="column preparing">
            <div class="column-header">
                <span><i class="fas fa-fire"></i> Preparing</span>
                <span class="count">{{ $preparingOrders->count() }}</span>
            </div>
            <div class="column-content" id="preparingOrders">
                @forelse($preparingOrders as $order)
                @include('admin.orders._kitchen_card', ['order' => $order, 'nextStatus' => 'ready', 'nextLabel' => 'Mark Ready'])
                @empty
                <div class="empty-state">
                    <i class="fas fa-fire"></i>
                    <p>No orders preparing</p>
                </div>
                @endforelse
            </div>
        </div>
        
        <!-- Ready -->
        <div class="column ready">
            <div class="column-header">
                <span><i class="fas fa-check-circle"></i> Ready</span>
                <span class="count">{{ $readyOrders->count() }}</span>
            </div>
            <div class="column-content" id="readyOrders">
                @forelse($readyOrders as $order)
                @include('admin.orders._kitchen_card', ['order' => $order, 'nextStatus' => 'completed', 'nextLabel' => 'Complete'])
                @empty
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>No orders ready</p>
                </div>
                @endforelse
            </div>
        </div>
        
        <!-- Completed -->
        <div class="column completed">
            <div class="column-header">
                <span><i class="fas fa-flag-checkered"></i> Completed</span>
                <span class="count">{{ $completedOrders->count() }}</span>
            </div>
            <div class="column-content" id="completedOrders">
                @forelse($completedOrders as $order)
                <div class="order-card">
                    <div class="order-card-header">
                        <span class="token-badge">{{ $order->formatted_token }}</span>
                        <div class="order-meta">
                            <span class="order-type {{ $order->order_type }}">{{ $order->order_type }}</span>
                            <div class="order-time">{{ $order->created_at->format('H:i') }}</div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <i class="fas fa-flag-checkered"></i>
                    <p>No completed orders yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <script>
        // Update time
        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-GB', { hour12: false });
        }
        setInterval(updateTime, 1000);
        updateTime();
        
        // Sound toggle
        let soundEnabled = localStorage.getItem('kitchenSound') !== 'false';
        const soundToggle = document.getElementById('soundToggle');
        
        function updateSoundToggle() {
            soundToggle.classList.toggle('active', soundEnabled);
            soundToggle.innerHTML = soundEnabled ? '<i class="fas fa-volume-up"></i>' : '<i class="fas fa-volume-mute"></i>';
        }
        updateSoundToggle();
        
        soundToggle.addEventListener('click', function() {
            soundEnabled = !soundEnabled;
            localStorage.setItem('kitchenSound', soundEnabled);
            updateSoundToggle();
        });
        
        // Update order status
        function updateOrderStatus(orderId, newStatus) {
            fetch(`/admin/orders/${orderId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => {
                if (response.ok) {
                    // Reload page to reflect changes
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        // Auto-refresh every 30 seconds
        setInterval(function() {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>
