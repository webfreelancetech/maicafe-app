<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status - Mai Cafe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: #fff;
            overflow: hidden;
        }
        
        .container {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Header */
        .header {
            background: rgba(0,0,0,0.3);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .logo-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #e94560 0%, #c73659 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }
        
        .logo-text {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -1px;
        }
        
        .logo-text span {
            color: #e94560;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 40px;
        }
        
        .current-time {
            text-align: right;
        }
        
        .time {
            font-size: 48px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            line-height: 1;
        }
        
        .date {
            font-size: 16px;
            color: rgba(255,255,255,0.6);
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 30px 40px;
            overflow: hidden;
        }
        
        /* Order Column */
        .order-column {
            background: rgba(255,255,255,0.05);
            border-radius: 24px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .column-header {
            padding: 24px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .column-title {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 28px;
            font-weight: 700;
        }
        
        .column-title i {
            font-size: 32px;
        }
        
        .column-count {
            background: rgba(255,255,255,0.2);
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 20px;
            font-weight: 700;
        }
        
        .column.preparing .column-header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        
        .column.ready .column-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .column-content {
            flex: 1;
            padding: 24px;
            overflow-y: auto;
        }
        
        /* Token Grid */
        .token-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 16px;
        }
        
        .token-card {
            background: rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .column.preparing .token-card {
            border: 3px solid #f59e0b;
            animation: pulse-orange 2s infinite;
        }
        
        .column.ready .token-card {
            border: 3px solid #10b981;
            background: rgba(16, 185, 129, 0.2);
            animation: pulse-green 1s infinite;
        }
        
        @keyframes pulse-orange {
            0%, 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
            50% { box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); }
        }
        
        @keyframes pulse-green {
            0%, 100% { 
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6);
                transform: scale(1);
            }
            50% { 
                box-shadow: 0 0 0 15px rgba(16, 185, 129, 0);
                transform: scale(1.02);
            }
        }
        
        .token-number {
            font-size: 48px;
            font-weight: 900;
            line-height: 1;
            letter-spacing: 2px;
        }
        
        .column.preparing .token-number {
            color: #f59e0b;
        }
        
        .column.ready .token-number {
            color: #10b981;
        }
        
        .token-status {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 8px;
            opacity: 0.7;
        }
        
        /* Empty State */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            opacity: 0.4;
        }
        
        .empty-state i {
            font-size: 64px;
            margin-bottom: 16px;
        }
        
        .empty-state p {
            font-size: 18px;
        }
        
        /* Footer */
        .footer {
            background: rgba(0,0,0,0.3);
            padding: 16px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .footer-message {
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .footer-message i {
            color: #10b981;
            animation: bounce 1s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        
        .refresh-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            opacity: 0.6;
        }
        
        .refresh-info .dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: blink 2s infinite;
        }
        
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        
        /* Ready Animation Overlay */
        .ready-notification {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 60px 100px;
            border-radius: 30px;
            text-align: center;
            z-index: 1000;
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .ready-notification.show {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
        
        .ready-notification .token {
            font-size: 120px;
            font-weight: 900;
            line-height: 1;
        }
        
        .ready-notification .message {
            font-size: 32px;
            margin-top: 16px;
        }
        
        /* Scrollbar */
        .column-content::-webkit-scrollbar {
            width: 8px;
        }
        
        .column-content::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
            border-radius: 4px;
        }
        
        .column-content::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 4px;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            
            .token-number {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-coffee"></i>
                </div>
                <div class="logo-text">Mai<span>Cafe</span></div>
            </div>
            <div class="header-right">
                <div class="current-time">
                    <div class="time" id="currentTime">00:00</div>
                    <div class="date" id="currentDate">{{ now()->format('l, F d, Y') }}</div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Preparing Column -->
            <div class="order-column column preparing">
                <div class="column-header">
                    <div class="column-title">
                        <i class="fas fa-fire"></i>
                        <span>Preparing</span>
                    </div>
                    <div class="column-count" id="preparingCount">{{ $preparingOrders->count() }}</div>
                </div>
                <div class="column-content">
                    <div class="token-grid" id="preparingTokens">
                        @forelse($preparingOrders as $order)
                        <div class="token-card">
                            <div class="token-number">{{ $order->formatted_token }}</div>
                            <div class="token-status">In Progress</div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-mug-hot"></i>
                            <p>No orders preparing</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Ready Column -->
            <div class="order-column column ready">
                <div class="column-header">
                    <div class="column-title">
                        <i class="fas fa-bell"></i>
                        <span>Ready for Pickup!</span>
                    </div>
                    <div class="column-count" id="readyCount">{{ $readyOrders->count() }}</div>
                </div>
                <div class="column-content">
                    <div class="token-grid" id="readyTokens">
                        @forelse($readyOrders as $order)
                        <div class="token-card">
                            <div class="token-number">{{ $order->formatted_token }}</div>
                            <div class="token-status">Please Collect</div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-clock"></i>
                            <p>Orders will appear here when ready</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-message">
                <i class="fas fa-bell"></i>
                <span>Listen for your token number to be called</span>
            </div>
            <div class="refresh-info">
                <span class="dot"></span>
                <span>Auto-updating</span>
            </div>
        </div>
    </div>
    
    <!-- Ready Notification Overlay -->
    <div class="ready-notification" id="readyNotification">
        <div class="token" id="notificationToken">T001</div>
        <div class="message">Your Order is Ready!</div>
    </div>
    
    <script>
        // Update time
        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-GB', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false 
            });
        }
        setInterval(updateTime, 1000);
        updateTime();
        
        // Track ready orders for notification
        let previousReadyTokens = {!! json_encode($readyOrders->pluck('formatted_token')->toArray()) !!};
        
        // Fetch updates
        function fetchUpdates() {
            fetch('{{ route("order-display.data") }}')
                .then(response => response.json())
                .then(data => {
                    updateColumn('preparing', data.preparing);
                    updateColumn('ready', data.ready);
                    
                    // Check for new ready orders
                    const currentReadyTokens = data.ready.map(o => o.token);
                    const newReadyTokens = currentReadyTokens.filter(t => !previousReadyTokens.includes(t));
                    
                    if (newReadyTokens.length > 0) {
                        // Show notification for new ready orders
                        newReadyTokens.forEach((token, index) => {
                            setTimeout(() => showReadyNotification(token), index * 3000);
                        });
                    }
                    
                    previousReadyTokens = currentReadyTokens;
                })
                .catch(error => console.error('Error fetching updates:', error));
        }
        
        function updateColumn(type, orders) {
            const countEl = document.getElementById(type + 'Count');
            const gridEl = document.getElementById(type + 'Tokens');
            
            countEl.textContent = orders.length;
            
            if (orders.length === 0) {
                const emptyIcon = type === 'preparing' ? 'fa-mug-hot' : 'fa-clock';
                const emptyText = type === 'preparing' ? 'No orders preparing' : 'Orders will appear here when ready';
                gridEl.innerHTML = `
                    <div class="empty-state">
                        <i class="fas ${emptyIcon}"></i>
                        <p>${emptyText}</p>
                    </div>
                `;
            } else {
                const statusText = type === 'preparing' ? 'In Progress' : 'Please Collect';
                gridEl.innerHTML = orders.map(order => `
                    <div class="token-card">
                        <div class="token-number">${order.token}</div>
                        <div class="token-status">${statusText}</div>
                    </div>
                `).join('');
            }
        }
        
        function showReadyNotification(token) {
            const notification = document.getElementById('readyNotification');
            const tokenEl = document.getElementById('notificationToken');
            
            tokenEl.textContent = token;
            notification.classList.add('show');
            
            // Play sound if available
            playNotificationSound();
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 2500);
        }
        
        function playNotificationSound() {
            // Create a simple beep sound using Web Audio API
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.value = 800;
                oscillator.type = 'sine';
                gainNode.gain.value = 0.3;
                
                oscillator.start();
                
                setTimeout(() => {
                    oscillator.frequency.value = 1000;
                }, 150);
                
                setTimeout(() => {
                    oscillator.stop();
                }, 300);
            } catch (e) {
                // Audio not supported
            }
        }
        
        // Refresh every 10 seconds
        setInterval(fetchUpdates, 10000);
        
        // Initial load
        fetchUpdates();
    </script>
</body>
</html>
