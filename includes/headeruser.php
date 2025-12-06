                    <button type="button" onclick="document.getElementById('calculator-modal').classList.remove('hidden'); setTimeout(function(){document.getElementById('calc-modal-inner').classList.remove('scale-95','opacity-0');document.getElementById('calc-modal-inner').classList.add('scale-100','opacity-100');},10);" class="flex items-center gap-3 px-4 py-2.5 text-white/90 hover:bg-gold hover:text-white transition-colors w-full text-left bg-transparent border-0 cursor-pointer">
                        <i class="fas fa-calculator w-5 text-center"></i> Tile Calculator
                    </button>
                    <script>
                    // Ensure canvas resizes when modal is opened from dropdown (mobile/desktop)
                    function openTileCalculatorModal() {
                        var modal = document.getElementById('calculator-modal');
                        var inner = document.getElementById('calc-modal-inner');
                        modal.classList.remove('hidden');
                        setTimeout(function(){
                            inner.classList.remove('scale-95','opacity-0');
                            inner.classList.add('scale-100','opacity-100');
                            // Trigger resizeCanvas if available
                            if (window.dispatchEvent) {
                                window.dispatchEvent(new Event('resize'));
                            }
                        },10);
                    }
                    </script>
<?php
// Start session and check if user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ../connection/tresspass.php');
    exit();
}

// Database connection and branch fetching
require_once __DIR__ . '/../connection/connection.php';
if (!isset($db_connection) && isset($conn)) {
    $db_connection = $conn;
}

// Log branch change event to customer_branch_trail
if (isset($_SESSION['user_id']) && isset($_SESSION['branch_id']) && isset($db_connection)) {
    try {
        $stmt = $db_connection->prepare("INSERT INTO customer_branch_trail (user_id, branch_id, event_type) VALUES (?, ?, 'branch_change')");
        $stmt->execute([$_SESSION['user_id'], $_SESSION['branch_id']]);
    } catch (Exception $e) {
        // Optionally log error
    }
}

$branches = [];
try {
    // Using PDO::FETCH_ASSOC for associative arrays
    $stmt = $db_connection->query("SELECT branch_id AS id, branch_name AS name, latitude AS lat, longitude AS lng FROM branches ORDER BY branch_id ASC");
    $branches = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Fallback data if DB connection fails
    $branches = [
        ['id' => 1, 'name' => 'Deparo',   'lat' => 14.75243153, 'lng' => 121.01763335],
        ['id' => 2, 'name' => 'Vanguard', 'lat' => 14.75920200, 'lng' => 121.06286101],
        ['id' => 3, 'name' => 'Brixton',  'lat' => 14.76724928, 'lng' => 121.04104486],
        ['id' => 4, 'name' => 'Samaria',  'lat' => 14.76580311, 'lng' => 121.06563606],
        ['id' => 5, 'name' => 'Phase 1',  'lat' => 14.77682717, 'lng' => 121.04841432],
    ];
}

// Check if the current page is an order summary or confirmation page
$currentPage = basename($_SERVER['PHP_SELF']);
$isCheckoutPage = in_array($currentPage, ['order_summary.php', 'order_confirmation.php']);

$user_branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
$user_branch = null;
if ($user_branch_id) {
    foreach ($branches as $b) {
        if ($b['id'] === $user_branch_id) {
            $user_branch = $b;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RALTT Shop</title>
    <link rel="icon" type="image/png" href="../images/userlogo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        :root {
            --color-gold: #cf8756;
            --color-gold-dark: #b88b4a;
        }
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #111827; 
        }
        .glass-effect { 
            background: rgba(17, 24, 39, 0.85); 
            backdrop-filter: blur(16px); 
            -webkit-backdrop-filter: blur(16px); 
        }
        .text-gold { color: var(--color-gold); }
        .border-gold { border-color: var(--color-gold); }
        .bg-gold { background-color: var(--color-gold); }
        /* Notification bell styles */
        .fa-bell { transition: color 0.2s; }
        #notif-bell-btn:hover .fa-bell { color: #fff7e6; }
        #notif-bell-btn .animate-pulse {
            animation: pulse 1.2s infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.7); }
            70% { box-shadow: 0 0 0 8px rgba(239,68,68,0.0); }
        }
        #notif-dropdown {
            min-width: 320px;
            max-width: 400px;
            background: #23272f;
            border-radius: 1rem;
            box-shadow: 0 8px 32px 0 rgba(0,0,0,0.25);
            border: 1px solid rgba(255,255,255,0.08);
            z-index: 9999;
        }
        #notif-dropdown ul { padding: 0; margin: 0; list-style: none; }
        #notif-dropdown li { cursor: pointer; border-radius: 0.5rem; }
        #notif-dropdown li.bg-gold\/10 { background: rgba(207,135,86,0.10); }
        #notif-dropdown li:hover { background: rgba(207,135,86,0.18); }
        #notif-dropdown .text-gold { color: var(--color-gold); }
        #notif-dropdown .font-bold { font-weight: 700; }
        #notif-dropdown .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        #notif-dropdown .custom-scrollbar::-webkit-scrollbar-thumb { background: #6b7280; border-radius: 10px; }
        
        /* Burger menu animation */
        .burger-line { 
            transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1); 
        }
        .burger.open .burger-line:nth-child(1) { 
            transform: rotate(-45deg) translate(-5px, 6px); 
        }
        .burger.open .burger-line:nth-child(2) { 
            opacity: 0; 
        }
        .burger.open .burger-line:nth-child(3) { 
            transform: rotate(45deg) translate(-5px, -6px); 
        }
        /* Smooth transitions for overlays */
        .fade-in { 
            animation: fadeIn 0.3s ease-out forwards; 
        }
        .fade-out { 
            animation: fadeOut 0.3s ease-in forwards; 
        }
        @keyframes fadeIn { 
            from { opacity: 0; } 
            to { opacity: 1; } 
        }
        @keyframes fadeOut { 
            from { opacity: 1; } 
            to { opacity: 0; } 
        }
        /* Custom scrollbar for modal */
        .custom-scrollbar::-webkit-scrollbar { 
            width: 8px; 
        }
        .custom-scrollbar::-webkit-scrollbar-track { 
            background: #374151; 
            border-radius: 10px; 
        }
        .custom-scrollbar::-webkit-scrollbar-thumb { 
            background: #6b7280; 
            border-radius: 10px; 
        }
        /* Branch item styles */
        .branch-item {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .branch-item:hover:not(.branch-disabled):not(.branch-selected) {
            border-color: var(--color-gold) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(207, 135, 86, 0.2);
        }
        .branch-selected {
            background-color: var(--color-gold) !important;
            border-color: var(--color-gold) !important;
            color: white !important;
            box-shadow: 0 4px 16px rgba(207, 135, 86, 0.4);
        }
        .branch-disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #374151;
        }
        .branch-disabled:hover {
            border-color: transparent !important;
            transform: none !important;
            box-shadow: none !important;
        }
        /* Draggable overlay style */
        #branch-location-overlay {
            cursor: grab;
        }
        #branch-location-overlay.dragging {
            cursor: grabbing;
        }
        /* Navigation link styles */
        .nav-link { 
            display: flex; 
            align-items: center; 
            gap: 1rem; 
            font-weight: 600; 
            font-size: 1.125rem; 
            padding: 1rem; 
            border-radius: 0.75rem; 
            transition: all 0.2s; 
        } 
        .nav-link:hover { 
            background-color: rgba(255,255,255,0.1); 
            color: var(--color-gold); 
        } 
        .nav-link i { 
            width: 1.5rem; 
            text-align: center; 
        }
        /* Loading overlay */
        #page-loading-overlay {
            background: rgba(17, 24, 39, 0.9);
            backdrop-filter: blur(8px);
        }
        /* Google Maps container */
        #branch-map {
            height: 300px;
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        /* Branch info card */
        .branch-info-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 16px;
            margin-top: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        /* Branch selection modal */
        .branch-modal-content {
            max-height: 85vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .branch-modal-body {
            overflow-y: auto;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        /* Map iframe styling */
        .branch-map-iframe {
            border: none;
            border-radius: 8px;
            width: 100%;
            height: 100%;
        }
        
        /* Calculator Styles */
        /* Calculator Input Styles */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        .deduction-row {
            animation: slideDown 0.2s ease-out forwards;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Visualizer Canvas Styles */
        #room-canvas {
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 20px 20px;
            background-color: #1f2937; /* Gray-800 */
            border-radius: 0.75rem;
            box-shadow: inset 0 0 20px rgba(0,0,0,0.5);
            cursor: default;
        }
        #room-canvas.grabbing {
            cursor: grabbing;
        }
        #room-canvas.grab {
            cursor: grab;
        }


        @media (max-width: 768px) {
            .branch-modal-content {
                max-height: 90vh;
            }
            .branch-modal-body {
                flex-direction: column;
            }
            #branch-map {
                height: 250px;
            }
            .branch-modal-body > div {
                width: 100% !important;
                border-right: none !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
        }
    </style>
</head>
<body class="text-white pt-20">

<script>
    // Pass PHP data to JavaScript safely
    window.RALTT_DATA = {
        branches: <?php echo json_encode($branches); ?>,
        userBranch: <?php echo json_encode($user_branch); ?>,
        isCheckoutPage: <?php echo json_encode($isCheckoutPage); ?>,
        googleMapsApiKey: '<?php echo isset($_ENV['GOOGLE_MAPS_API_KEY']) ? $_ENV['GOOGLE_MAPS_API_KEY'] : ''; ?>'
    };
</script>

<header class="raltt-header fixed top-0 left-0 w-full z-50 glass-effect border-b border-white/10">
    <div class="container mx-auto flex items-center justify-between px-4 h-20">
        <a href="../logged_user/landing_page.php" class="flex items-center gap-3 z-50">
            <img src="../images/userlogo.png" alt="RALTT Shop Logo" class="w-14 h-14 object-contain">
            <div class="flex flex-col leading-tight">
                <span class="text-white font-extrabold text-xl tracking-tighter">RALTT SHOP</span>
                <span class="text-gold font-semibold text-sm">Premium Tiles & More</span>
            </div>
        </a>

        <nav class="hidden lg:flex items-center gap-2">
            <a href="../logged_user/landing_page.php" class="font-semibold px-5 py-2 rounded-lg hover:bg-white/10 transition-colors">Home</a>
            <a href="../logged_user/landing_page.php#premium-tiles" class="font-semibold px-5 py-2 rounded-lg hover:bg-white/10 transition-colors">Products</a>
            <div class="relative group">
                <button class="font-semibold px-5 py-2 rounded-lg hover:bg-white/10 transition-colors flex items-center gap-2 focus:outline-none">
                    <i class="fas fa-cube"></i>
                    3D and AR Tools
                    <i class="fa fa-caret-down text-white/60 ml-1"></i>
                </button>
                <div class="absolute left-0 top-full mt-2 w-56 bg-gray-800 rounded-xl shadow-2xl border border-white/10 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    <a href="../logged_user/3dvisualizer.php" class="flex items-center gap-3 px-4 py-2.5 text-white/90 hover:bg-gold hover:text-white transition-colors">
                        <i class="fas fa-cube w-5 text-center"></i> 3D Visualizer
                    </a>
                    <a href="../logged_user/3dsimulator.php" class="flex items-center gap-3 px-4 py-2.5 text-white/90 hover:bg-gold hover:text-white transition-colors">
                        <i class="fas fa-vr-cardboard w-5 text-center"></i> 3D Simulator
                    </a>
                    <a href="../logged_user/ARTile_Access.php" class="flex items-center gap-3 px-4 py-2.5 text-white/90 hover:bg-gold hover:text-white transition-colors">
                        <i class="fas fa-qrcode w-5 text-center"></i> AR Tile Access
                    </a>
                    <button type="button" onclick="document.getElementById('calculator-modal').classList.remove('hidden'); setTimeout(function(){document.getElementById('calc-modal-inner').classList.remove('scale-95','opacity-0');document.getElementById('calc-modal-inner').classList.add('scale-100','opacity-100');},10);" class="flex items-center gap-3 px-4 py-2.5 text-white/90 hover:bg-gold hover:text-white transition-colors w-full text-left bg-transparent border-0 cursor-pointer">
                        <i class="fas fa-calculator w-5 text-center"></i> Tile Calculator
                    </button>
                </div>
            </div>
            <a href="user_my_cart.php" class="font-semibold px-5 py-2 rounded-lg hover:bg-white/10 transition-colors">My Cart</a>
        </nav>

        <?php
        // Fetch notifications for the logged-in user
        $notifications = [];
        $unread_count = 0;
        if (isset($_SESSION['user_id']) && isset($db_connection)) {
            try {
                $stmt = $db_connection->prepare("SELECT * FROM user_notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
                $stmt->execute([$_SESSION['user_id']]);
                $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $unread_count = 0;
                foreach ($notifications as $n) {
                    if (!$n['is_read']) $unread_count++;
                }
            } catch (Exception $e) {
                // Optionally log error
            }
        }
        ?>
        <div class="flex items-center gap-4 z-50">
            <div class="relative group">
                <button id="notif-bell-btn" class="relative flex items-center justify-center w-10 h-10 rounded-full hover:bg-white/10 transition-colors focus:outline-none">
                    <i class="fa fa-bell text-gold text-xl"></i>
                    <span id="notif-unread-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full px-1.5 py-0.5 animate-pulse shadow-lg" style="display:none;"></span>
                </button>
                <div id="notif-dropdown" class="hidden group-hover:block absolute right-0 top-full mt-2 w-96 max-w-xs bg-gray-800 rounded-xl shadow-2xl border border-white/10 py-2 z-50">
                    <div class="px-4 py-2 border-b border-white/10 flex items-center justify-between">
                        <span class="font-bold text-gold text-lg">Notifications</span>
                        <span id="notif-unread-label" class="text-xs text-red-400 font-semibold" style="display:none;"></span>
                    </div>
                    <div id="notif-list-container">
                        <div class="px-4 py-6 text-center text-white/70">Loading...</div>
                    </div>
                </div>
            </div>
            <script>
            // Real-time notification polling and mark-as-read
            function fetchNotifications() {
                fetch('../connection/get_user_notifications.php')
                    .then(res => res.json())
                    .then(data => {
                        const notifList = document.getElementById('notif-list-container');
                        const unreadCountEl = document.getElementById('notif-unread-count');
                        const unreadLabel = document.getElementById('notif-unread-label');
                        let unreadCount = 0;
                        if (Array.isArray(data.notifications) && data.notifications.length > 0) {
                            var html = '<ul class="max-h-80 overflow-y-auto custom-scrollbar divide-y divide-white/10">';
                            data.notifications.forEach(function(notif) {
                                if (!notif.is_read) unreadCount++;
                                var icon = 'fa-info-circle', color = 'text-gold';
                                switch (notif.notification_type) {
                                    case 'ORDER_STATUS': icon = 'fa-box'; color = 'text-blue-400'; break;
                                    case 'REFERRAL_CLAIMED': icon = 'fa-gift'; color = 'text-green-400'; break;
                                    case 'TICKET_UPDATE': icon = 'fa-ticket-alt'; color = 'text-yellow-400'; break;
                                    case 'MARKETING': icon = 'fa-bullhorn'; color = 'text-pink-400'; break;
                                }
                                html += '<li class="px-4 py-3 flex gap-3 items-start ' + (!notif.is_read ? 'bg-gold/10' : '') + ' hover:bg-gold/20 transition-colors">' +
                                    '<div class="mt-1"><i class="fas ' + icon + ' ' + color + ' text-lg"></i></div>' +
                                    '<div class="flex-1 min-w-0">' +
                                        '<div class="text-sm font-semibold text-white/90 mb-1">' + notif.notification_message + '</div>' +
                                        '<div class="text-xs text-white/50 flex gap-2 items-center">' +
                                            '<span>' + notif.notification_type.replace(/_/g, ' ').toLowerCase() + '</span>' +
                                            '<span>·</span>' +
                                            '<span>' + notif.created_at + '</span>' +
                                            (!notif.is_read ? '<span class="ml-2 text-gold font-bold">New</span>' : '') +
                                        '</div>' +
                                    '</div>' +
                                '</li>';
                            });
                            html += '</ul>';
                            notifList.innerHTML = html;
                        } else {
                            notifList.innerHTML = '<div class="px-4 py-6 text-center text-white/70">No notifications yet.</div>';
                        }
                        if (unreadCount > 0) {
                            unreadCountEl.textContent = unreadCount;
                            unreadCountEl.style.display = '';
                            unreadLabel.textContent = unreadCount + ' unread';
                            unreadLabel.style.display = '';
                        } else {
                            unreadCountEl.style.display = 'none';
                            unreadLabel.style.display = 'none';
                        }
                    });
            }
            // Poll every 10 seconds
            setInterval(fetchNotifications, 10000);
            fetchNotifications();
            // Mark as read when dropdown is opened
            document.getElementById('notif-bell-btn').addEventListener('click', function() {
                fetch('../connection/mark_notifications_read.php')
                    .then(() => setTimeout(fetchNotifications, 500));
            });
            </script>
            <div class="hidden lg:block relative group">
                <button class="flex items-center gap-2 p-2 rounded-lg hover:bg-white/10 transition-colors">
                    <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center">
                        <i class="fa fa-user text-gold"></i>
                    </div>
                    <i class="fa fa-caret-down text-white/50"></i>
                </button>
                <div class="absolute right-0 top-full mt-2 w-56 bg-gray-800 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 border border-white/10 py-2">
                    <a href="../logged_user/myProfile.php" class="flex items-center gap-3 px-4 py-2.5 text-white/90 hover:bg-gold hover:text-white transition-colors">
                        <i class="fas fa-user-circle w-5 text-center"></i> My Account
                    </a>
                    <a href="../logged_user/customer_ticket.php" class="flex items-center gap-3 px-4 py-2.5 text-white/90 hover:bg-gold hover:text-white transition-colors">
                        <i class="fas fa-ticket-alt w-5 text-center"></i> Customer Ticket
                    </a>
                    <div class="h-px bg-white/10 my-2"></div>
                    <a href="../logout.php" class="flex items-center gap-3 px-4 py-2.5 text-white/90 hover:bg-gold hover:text-white transition-colors">
                        <i class="fas fa-sign-out-alt w-5 text-center"></i> Logout
                    </a>
                </div>
            </div>
            <button id="mobile-menu-toggle" class="lg:hidden burger flex flex-col justify-center items-center w-10 h-10 z-[100]">
                <span class="burger-line w-6 h-0.5 bg-white mb-1.5 rounded-full"></span>
                <span class="burger-line w-6 h-0.5 bg-white mb-1.5 rounded-full"></span>
                <span class="burger-line w-6 h-0.5 bg-white rounded-full"></span>
            </button>
        </div>
    </div>

    
</header>

<div id="mobile-menu" class="lg:hidden fixed inset-0 z-40 bg-gray-900/95 transform translate-x-full transition-transform duration-300 backdrop-blur-md">
    <div class="flex flex-col h-full pt-24 pb-8 px-6">
        <nav class="flex flex-col space-y-2 flex-1">
            <a href="../logged_user/landing_page.php" class="nav-link">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="../logged_user/landing_page.php#premium-tiles" class="nav-link">
                <i class="fas fa-th-large"></i> Products
            </a>
            <div class="relative">
                <button class="nav-link flex items-center gap-2 w-full focus:outline-none" onclick="this.nextElementSibling.classList.toggle('hidden')">
                    <i class="fas fa-cube"></i> 3D and AR Tools <i class="fa fa-caret-down text-white/60 ml-1"></i>
                </button>
                <div class="ml-6 mt-1 bg-gray-800 rounded-xl shadow-2xl border border-white/10 py-2 hidden">
                    <a href="../logged_user/3dvisualizer.php" class="flex items-center gap-3 px-4 py-2.5 text-white/90 hover:bg-gold hover:text-white transition-colors">
                        <i class="fas fa-cube w-5 text-center"></i> 3D Visualizer
                    </a>
                    <a href="../logged_user/3dsimulator.php" class="flex items-center gap-3 px-4 py-2.5 text-white/90 hover:bg-gold hover:text-white transition-colors">
                        <i class="fas fa-vr-cardboard w-5 text-center"></i> 3D Simulator
                    </a>
                    <a href="../logged_user/ARTile_Access.php" class="flex items-center gap-3 px-4 py-2.5 text-white/90 hover:bg-gold hover:text-white transition-colors">
                        <i class="fas fa-qrcode w-5 text-center"></i> AR Tile Access
                    </a>
                    <button type="button" onclick="document.getElementById('calculator-modal').classList.remove('hidden'); setTimeout(function(){document.getElementById('calc-modal-inner').classList.remove('scale-95','opacity-0');document.getElementById('calc-modal-inner').classList.add('scale-100','opacity-100');},10);" class="flex items-center gap-3 px-4 py-2.5 text-white/90 hover:bg-gold hover:text-white transition-colors w-full text-left bg-transparent border-0 cursor-pointer">
                        <i class="fas fa-calculator w-5 text-center"></i> Tile Calculator
                    </button>
                </div>
            </div>
            <a href="user_my_cart.php" class="nav-link">
                <i class="fas fa-shopping-cart"></i> My Cart
            </a>
            <div class="pt-6 border-t border-white/10 mt-4">
                <a href="../logged_user/myProfile.php" class="nav-link">
                    <i class="fas fa-user-circle"></i> My Account
                </a>
                <a href="../logged_user/customer_ticket.php" class="nav-link">
                    <i class="fas fa-ticket-alt"></i> Customer Ticket
                </a>
                <a href="../logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </nav>
    </div>
</div>

<div id="branch-location-overlay" class="fixed top-24 left-6 z-40 glass-effect text-white p-3 rounded-full shadow-2xl border border-white/10 flex items-center gap-3 select-none">
    <i class="fas fa-map-marker-alt text-gold text-lg"></i>
    <div>
        <span class="text-xs opacity-70">Browsing at</span>
        <div class="flex items-center gap-2">
            <span id="branch-current" class="font-bold text-sm leading-tight">
                <?php echo $user_branch ? htmlspecialchars($user_branch['name']) : '<i>No Branch Selected</i>'; ?>
            </span>
            <span id="branch-distance" class="text-xs text-gold opacity-80"></span>
        </div>
    </div>
    <button id="branch-change-btn" class="text-xs bg-white/10 hover:bg-white/20 text-white font-semibold py-1.5 px-3 rounded-full transition-colors">
        Change
    </button>
</div>

<div id="branch-modal" class="hidden fixed inset-0 z-[100] bg-black/50 flex items-center justify-center p-4">
    <div class="bg-gray-800 text-white rounded-2xl shadow-2xl max-w-5xl w-full overflow-hidden border border-white/10 branch-modal-content">
        <div class="p-5 text-center border-b border-white/10 relative">
            <h2 class="text-xl font-bold">Select Your Branch</h2>
            <p class="text-sm text-white/60 mt-1">Enable location to choose a branch.</p>
            <button id="close-branch-modal" class="absolute top-3 right-3 w-8 h-8 rounded-full hover:bg-white/10 flex items-center justify-center transition-colors">
                ×
            </button>
        </div>
        
        <div id="branch-location-status" class="p-3 text-center text-sm"></div>
        
        <div class="branch-modal-body flex flex-col md:flex-row">
            <div class="w-full md:w-1/3 p-4 border-r border-white/10">
                <div id="branch-list" class="space-y-2 max-h-[60vh] overflow-y-auto custom-scrollbar pr-2">
                    </div>
            </div>
            
            <div class="w-full md:w-2/3 p-4 flex flex-col">
                <div id="branch-map-container" class="mb-4 flex-1">
                    <iframe id="branch-map-iframe" class="branch-map-iframe" 
                        src=""
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <div id="branch-details" class="branch-info-card mt-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 id="branch-details-name" class="text-lg font-bold text-gold">Select a branch</h3>
                        <span id="branch-details-distance" class="text-sm bg-gold text-white px-2 py-1 rounded-full"></span>
                    </div>
                    <p id="branch-details-address" class="text-white/70 text-sm">Click on a branch to see its location and details</p>
                    <div class="mt-4 flex justify-end">
                        <button id="select-branch-btn" class="bg-gold hover:bg-gold-dark text-white font-semibold py-2 px-4 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            Select Branch
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div id="calculator-modal" class="hidden fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-gray-900 text-white rounded-2xl shadow-2xl w-full max-w-5xl border border-gold/20 transform transition-all scale-95 opacity-0 max-h-[90vh] flex flex-col" id="calc-modal-inner">
        <div class="bg-gray-800 px-6 py-4 border-b border-white/10 relative flex items-center justify-center">
            <div class="text-center">
                <div class="flex items-center justify-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gold/20 flex items-center justify-center">
                        <i class="fas fa-calculator text-gold"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Tile Calculator</h3>
                </div>
                <p class="text-xs text-white/50 mt-1">Estimate tiles with live blueprint</p>
            </div>
            <button id="cancel-calc-btn" class="absolute top-3 right-3 w-8 h-8 rounded-full hover:bg-white/10 flex items-center justify-center transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-6 overflow-y-auto custom-scrollbar flex-1">
            <div class="flex flex-col lg:flex-row gap-6 h-full">
                
                <div class="w-full lg:w-1/2 space-y-6">
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold text-gold uppercase tracking-wider">1. Room Dimensions (cm)</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs text-white/70">Length (cm)</label>
                                <input type="number" id="calc-length" placeholder="e.g. 500" class="w-full bg-gray-800 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:border-gold focus:ring-1 focus:ring-gold outline-none transition-colors" inputmode="numeric" pattern="[0-9]*" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs text-white/70">Width (cm)</label>
                                <input type="number" id="calc-width" placeholder="e.g. 400" class="w-full bg-gray-800 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:border-gold focus:ring-1 focus:ring-gold outline-none transition-colors" inputmode="numeric" pattern="[0-9]*" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-gold uppercase tracking-wider">2. Non-tiled Areas (Optional)</h4>
                        </div>
                        
                        <div id="deductions-container" class="space-y-2">
                            <p id="no-deductions-msg" class="text-xs text-white/30 italic text-center py-2">No non-tiled areas added yet.</p>
                        </div>

                        <button id="add-deduction-btn" class="w-full py-2 border border-dashed border-white/20 rounded-lg text-white/60 hover:text-white hover:border-gold hover:bg-white/5 transition-all text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-plus"></i> Add Non-tiled Area (Door, Sink, etc.)
                        </button>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-sm font-bold text-gold uppercase tracking-wider">3. Select Tile Size</h4>
                        <div class="grid grid-cols-2 gap-3" id="tile-size-selector">
                            <label class="cursor-pointer relative group">
                                <input type="radio" name="tile_size" value="30x30" class="peer hidden" checked>
                                <div class="bg-gray-800 border border-white/10 rounded-lg p-3 text-center transition-all duration-200 ease-out peer-checked:bg-gold peer-checked:text-white peer-checked:border-white/50 peer-checked:shadow-[0_0_15px_rgba(207,135,86,0.4)] group-hover:border-gold/50">
                                    <div class="font-bold text-base">30 x 30</div>
                                    <div class="text-[10px] opacity-70">cm</div>
                                </div>
                            </label>
                            <label class="cursor-pointer relative group">
                                <input type="radio" name="tile_size" value="40x40" class="peer hidden">
                                <div class="bg-gray-800 border border-white/10 rounded-lg p-3 text-center transition-all duration-200 ease-out peer-checked:bg-gold peer-checked:text-white peer-checked:border-white/50 peer-checked:shadow-[0_0_15px_rgba(207,135,86,0.4)] group-hover:border-gold/50">
                                    <div class="font-bold text-base">40 x 40</div>
                                    <div class="text-[10px] opacity-70">cm</div>
                                </div>
                            </label>
                            <label class="cursor-pointer relative group">
                                <input type="radio" name="tile_size" value="30x60" class="peer hidden">
                                <div class="bg-gray-800 border border-white/10 rounded-lg p-3 text-center transition-all duration-200 ease-out peer-checked:bg-gold peer-checked:text-white peer-checked:border-white/50 peer-checked:shadow-[0_0_15px_rgba(207,135,86,0.4)] group-hover:border-gold/50">
                                    <div class="font-bold text-base">30 x 60</div>
                                    <div class="text-[10px] opacity-70">cm</div>
                                </div>
                            </label>
                            <label class="cursor-pointer relative group">
                                <input type="radio" name="tile_size" value="60x60" class="peer hidden">
                                <div class="bg-gray-800 border border-white/10 rounded-lg p-3 text-center transition-all duration-200 ease-out peer-checked:bg-gold peer-checked:text-white peer-checked:border-white/50 peer-checked:shadow-[0_0_15px_rgba(207,135,86,0.4)] group-hover:border-gold/50">
                                    <div class="font-bold text-base">60 x 60</div>
                                    <div class="text-[10px] opacity-70">cm</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-1/2 flex flex-col">
                    <div class="relative flex-1 bg-gray-800 rounded-xl p-4 border border-white/10 flex flex-col min-h-[300px]">
                         <div class="flex justify-between items-center mb-2">
                             <span class="text-xs font-bold text-gold uppercase">Room Blueprint (Click & Drag Deductions)</span>
                             <span class="text-[10px] text-white/40">Auto-Generated</span>
                         </div>
                         
                         <div class="flex-1 relative flex items-center justify-center w-full h-full" id="canvas-container">
                            <canvas id="room-canvas" class="w-full h-full"></canvas>
                            <div id="canvas-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-white/20">
                                <i class="fas fa-pencil-ruler text-4xl mb-2"></i>
                                <p class="text-sm">Enter dimensions to see blueprint</p>
                            </div>
                         </div>

                         <div class="mt-3 flex gap-4 text-[10px] text-white/50 justify-center">
                             <div class="flex items-center gap-1"><div class="w-3 h-3 border border-white/50 bg-white/10"></div> Room Area</div>
                             <div class="flex items-center gap-1"><div class="w-3 h-3 border border-red-400/50 bg-red-400/10"></div> Non-tiled Area</div>
                         </div>
                    </div>

                    <div id="calc-result" class="hidden mt-4 bg-gradient-to-r from-gray-800 to-gray-900 rounded-xl p-5 border border-gold/30 text-center">
                        <p class="text-sm text-white/60 mb-1">You need approximately:</p>
                        <div class="flex items-baseline justify-center gap-2">
                            <span id="result-count" class="text-4xl font-extrabold text-gold">0</span>
                            <span class="text-lg font-bold text-white">Tiles</span>
                        </div>
                        <p class="text-xs text-white/40 mt-2 italic">*Includes suggested 10% wastage allowance</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <button id="save-blueprint-btn" class="hidden w-full bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 rounded-xl shadow-lg transition-all transform active:scale-95 items-center justify-center gap-2">
                            <i class="fas fa-download"></i> Save as Image
                        </button>
                        <button id="calculate-btn" class="w-full bg-gold hover:bg-gold-dark text-white font-bold py-3 rounded-xl shadow-lg shadow-gold/20 transition-all transform active:scale-95 col-span-2">
                            Calculate Now
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div id="page-loading-overlay" class="hidden fixed inset-0 z-[99999] bg-gray-900/80 backdrop-blur-sm flex items-center justify-center">
    <div class="text-center">
        <i class="fas fa-spinner fa-spin text-4xl text-gold mb-4"></i>
        <div class="text-xl font-semibold">Switching Branch...</div>
    </div>
</div>

<script>
// Notification Bell Dropdown (show on click for mobile/desktop)
document.addEventListener('DOMContentLoaded', () => {
    // ...existing code...
    const { branches, userBranch, isCheckoutPage } = window.RALTT_DATA;

    // Google Maps embed URLs for each branch
    const branchMaps = {
        1: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3858.2961451796805!2d121.017676499333!3d14.75233823334116!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b1c722c4d1b9%3A0xc107b82c47609263!2sRich%20Anne%20Tiles!5e0!3m2!1sen!2sph!4v1756129529669!5m2!1sen!2sph",
        2: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3858.297019964697!2d121.06286101292358!3d14.759202001446935!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b919d7d11f69%3A0x288d3d951a8a2522!2sVanguard!5e0!3m2!1sen!2sph!4v1756129529669!5m2!1sen!2sph",
        3: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3859.083321523455!2d120.97931341478523!3d14.583120689801826!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c8d9c22e4c2f%3A0xf6f7f6f7f6f7f6f7!2sBrixtonville%20Subdivision%2C%20Caloocan%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1625000000000!5m2!1sen!2sph",
        4: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15433.080516641774!2d121.03362143009946!3d14.75704764848384!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397bd586a117565%3A0x2832561ce14a3174!2sTala%2C%20Caloocan%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1625000000000!5m2!1sen!2sph",
        5: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3858.627702581635!2d121.01168531478546!3d14.607425189785834!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397bd30f87a8987%3A0x89d25141b714777d!2sPhase%201%2C%20Camarin%20Rd%2C%20Caloocan%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1625000000000!5m2!1sen!2sph"
    };

    /**
     * Draggable Element Module
     */
    const DraggableModule = (() => {
        const el = document.getElementById('branch-location-overlay');
        if (!el) return;

        let isDragging = false;
        let startX, startY, initialLeft, initialTop;

        const startDrag = (e) => {
            isDragging = true;
            el.classList.add('dragging');
            const touch = e.touches ? e.touches[0] : e;
            startX = touch.clientX;
            startY = touch.clientY;
            const rect = el.getBoundingClientRect();
            initialLeft = rect.left;
            initialTop = rect.top;
            
            document.addEventListener('mousemove', onDrag);
            document.addEventListener('mouseup', endDrag);
            document.addEventListener('touchmove', onDrag, { passive: false });
            document.addEventListener('touchend', endDrag);
        };

        const onDrag = (e) => {
            if (!isDragging) return;
            e.preventDefault();
            const touch = e.touches ? e.touches[0] : e;
            const dx = touch.clientX - startX;
            const dy = touch.clientY - startY;
            el.style.left = `${initialLeft + dx}px`;
            el.style.top = `${initialTop + dy}px`;
        };

        const endDrag = () => {
            isDragging = false;
            el.classList.remove('dragging');
            document.removeEventListener('mousemove', onDrag);
            document.removeEventListener('mouseup', endDrag);
            document.removeEventListener('touchmove', onDrag);
            document.removeEventListener('touchend', endDrag);
        };

        el.addEventListener('mousedown', startDrag);
        el.addEventListener('touchstart', startDrag);
    })();

    /**
     * UI Interaction Module
     */
    const UIModule = (() => {
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        // Mobile Menu Toggle
        const toggleMobileMenu = () => {
            const isOpen = mobileMenuToggle.classList.toggle('open');
            mobileMenu.classList.toggle('translate-x-full');
            document.body.style.overflow = isOpen ? 'hidden' : '';
        };

        // Event Listeners
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', toggleMobileMenu);
        }

        if (mobileMenu) {
            mobileMenu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    if (mobileMenuToggle.classList.contains('open')) {
                        toggleMobileMenu();
                    }
                });
            });
        }
    })();

    /**
     * Branch Management Module
     */
    const BranchModule = (() => {
        const changeBtn = document.getElementById('branch-change-btn');
        const modal = document.getElementById('branch-modal');
        const closeModalBtn = document.getElementById('close-branch-modal');
        const branchListContainer = document.getElementById('branch-list');
        const locationStatusEl = document.getElementById('branch-location-status');
        const distanceEl = document.getElementById('branch-distance');
        const loadingOverlay = document.getElementById('page-loading-overlay');
        const selectBranchBtn = document.getElementById('select-branch-btn');
        const branchDetailsName = document.getElementById('branch-details-name');
        const branchDetailsDistance = document.getElementById('branch-details-distance');
        const branchDetailsAddress = document.getElementById('branch-details-address');
        const branchMapIframe = document.getElementById('branch-map-iframe');
        
        let geolocationInterval = null;
        let selectedBranch = null;
        let userCoords = null;

        // Update Google Maps iframe for selected branch
        const updateMapForBranch = (branch) => {
            if (!branch || !branchMapIframe) return;
            
            // Update Google Maps iframe
            if (branchMaps[branch.id]) {
                branchMapIframe.src = branchMaps[branch.id];
            }
            
            // Update branch details
            branchDetailsName.textContent = branch.name;
            
            // Calculate and display distance if user location is available
            if (userCoords) {
                const distance = haversineDistance(
                    userCoords.latitude, 
                    userCoords.longitude, 
                    branch.lat, 
                    branch.lng
                );
                branchDetailsDistance.textContent = `${distance.toFixed(1)} km away`;
            } else {
                branchDetailsDistance.textContent = '';
            }
            
            // Generate address based on branch name
            branchDetailsAddress.textContent = `RALTT Shop ${branch.name} Branch - Premium Tiles & More`;
            
            // Enable select button if not current branch
            const isCurrentBranch = userBranch && userBranch.id === branch.id;
            selectBranchBtn.disabled = isCurrentBranch || isCheckoutPage;
            selectBranchBtn.textContent = isCurrentBranch ? 'Current Branch' : 'Select Branch';
            
            selectedBranch = branch;
        };

        // Calculate distance between coordinates
        const haversineDistance = (lat1, lon1, lat2, lon2) => {
            const R = 6371; // Earth radius in km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) ** 2 + 
                     Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon / 2) ** 2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        };

        // Select branch and reload page
        const selectBranch = async (branchId) => {
            modal.classList.add('hidden', 'fade-out');
            loadingOverlay.classList.remove('hidden');
            loadingOverlay.classList.add('fade-in');
            
            try {
                const response = await fetch('set_branch.php', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/x-www-form-urlencoded' 
                    },
                    body: `branch_id=${encodeURIComponent(branchId)}`
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    throw new Error('Failed to switch branch');
                }
            } catch (error) {
                console.error('Error switching branch:', error);
                alert('Failed to switch branch. Please try again.');
                loadingOverlay.classList.add('hidden');
            }
        };

        // Select branch in the list
        const selectBranchInList = (branchId) => {
            const branch = branches.find(b => b.id === branchId);
            if (!branch) return;
            
            // Update UI to show selected branch
            const branchElements = document.querySelectorAll('.branch-item');
            branchElements.forEach(el => {
                if (parseInt(el.dataset.branchId) === branchId) {
                    el.classList.add('branch-selected');
                    el.classList.remove('bg-gray-700', 'border-transparent');
                } else {
                    el.classList.remove('branch-selected');
                    el.classList.add('bg-gray-700', 'border-transparent');
                }
            });
            
            updateMapForBranch(branch);
        };

        // Render branch list
        const renderBranchList = (coordinates = null, enabled = false) => {
            if (!branchListContainer) return;
            
            branchListContainer.innerHTML = '';
            userCoords = coordinates;

            // Sort branches by distance if coordinates available
            const sortedBranches = [...branches].sort((a, b) => {
                if (!coordinates) return 0;
                const distA = haversineDistance(coordinates.latitude, coordinates.longitude, a.lat, a.lng);
                const distB = haversineDistance(coordinates.latitude, coordinates.longitude, b.lat, b.lng);
                return distA - distB;
            });

            sortedBranches.forEach(branch => {
                const isSelected = userBranch && userBranch.id === branch.id;
                const distance = coordinates ? 
                    haversineDistance(coordinates.latitude, coordinates.longitude, branch.lat, branch.lng).toFixed(1) + ' km' : 
                    null;
                
                const isClickable = enabled && !isCheckoutPage;
                
                const branchElement = document.createElement('div');
                branchElement.className = `branch-item flex items-center justify-between p-4 rounded-lg border-2 transition-all duration-200 ${
                    isSelected ? 
                    'branch-selected' :
                    isClickable ? 
                    'bg-gray-700 border-transparent hover:border-gold cursor-pointer' : 
                    'branch-disabled bg-gray-700 border-transparent'
                }`;
                branchElement.dataset.branchId = branch.id;

                if (isClickable) {
                    branchElement.onclick = () => selectBranchInList(branch.id);
                }

                branchElement.innerHTML = `
                    <div class="flex items-center gap-3">
                        <i class="fas fa-store ${isSelected ? 'text-white' : 'text-gold'}"></i>
                        <span class="font-bold">${branch.name}</span>
                    </div>
                    <div class="text-right">
                        ${distance ? `<span class="text-sm font-semibold">${distance}</span>` : ''}
                        ${isSelected ? `<span class="ml-3 text-xs bg-white/20 px-2 py-1 rounded-full">Current</span>` : ''}
                    </div>
                `;

                branchListContainer.appendChild(branchElement);
            });

            // Select the first branch by default if not already selected
            if (sortedBranches.length > 0 && enabled) {
                const defaultBranch = sortedBranches[0];
                selectBranchInList(defaultBranch.id);
            } else if (userBranch) {
                selectBranchInList(userBranch.id);
            }
        };

        // Update location status message
        const updateLocationStatus = (message, type = 'info') => {
            if (!locationStatusEl) return;
            
            const icons = { 
                info: 'fa-spinner fa-spin', 
                success: 'fa-check-circle', 
                error: 'fa-times-circle' 
            };
            const colors = { 
                info: 'bg-blue-900/50 text-blue-300', 
                success: 'bg-green-900/50 text-green-300', 
                error: 'bg-red-900/50 text-red-400' 
            };
            
            locationStatusEl.className = `p-3 text-center text-sm ${colors[type]}`;
            locationStatusEl.innerHTML = `<i class="fas ${icons[type]} mr-2"></i> ${message}`;
        };

        // Open branch modal
        const openModal = () => {
            if (!modal) return;
            
            modal.classList.remove('hidden', 'fade-out');
            modal.classList.add('fade-in');

            if (isCheckoutPage) {
                updateLocationStatus('Branch selection is disabled during checkout.', 'error');
                renderBranchList(null, false);
                return;
            }

            renderBranchList(null, false);
            updateLocationStatus('Requesting location access...', 'info');

            if (!navigator.geolocation) {
                updateLocationStatus('Geolocation is not supported by this browser.', 'error');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    updateLocationStatus('Location found! Branches are now enabled.', 'success');
                    renderBranchList(pos.coords, true);
                },
                (error) => {
                    let errorMessage = 'Location access denied. Please enable location services.';
                    if (error.code === error.TIMEOUT) errorMessage = 'Location request timed out.';
                    updateLocationStatus(errorMessage, 'error');
                    renderBranchList(null, false);
                    if (selectBranchBtn) {
                        selectBranchBtn.disabled = true;
                        selectBranchBtn.textContent = 'Enable Location to Select';
                    }
                },
                { timeout: 10000, enableHighAccuracy: false }
            );
        };

        // Close branch modal
        const closeModal = () => {
            if (!modal) return;
            modal.classList.add('fade-out');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        };

        // Check user's distance from current branch
        const checkGeolocation = () => {
            if (!navigator.geolocation || !userBranch || !distanceEl) return;
            
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const dist = haversineDistance(
                        pos.coords.latitude, 
                        pos.coords.longitude, 
                        userBranch.lat, 
                        userBranch.lng
                    );
                    distanceEl.textContent = `(${dist.toFixed(1)} km away)`;
                },
                () => {
                    if (distanceEl) distanceEl.textContent = '';
                },
                { enableHighAccuracy: false, timeout: 5000 }
            );
        };

        const startGeolocationWatcher = () => {
            checkGeolocation();
            if (geolocationInterval) clearInterval(geolocationInterval);
            geolocationInterval = setInterval(checkGeolocation, 30000);
        };

        // Event Listeners
        if (changeBtn) changeBtn.addEventListener('click', openModal);
        if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });
        }
        if (selectBranchBtn) {
            selectBranchBtn.addEventListener('click', () => {
                if (selectedBranch && !isCheckoutPage) selectBranch(selectedBranch.id);
            });
        }

        startGeolocationWatcher();
    })();

    /**
     * Tile Calculator + Blueprint Visualizer Module
     */
    const TileCalculatorModule = (() => {
        // --- DOM Elements ---
        const trigger = document.getElementById('calculator-trigger');
        const modal = document.getElementById('calculator-modal');
        const cancelBtn = document.getElementById('cancel-calc-btn');
        const innerModal = document.getElementById('calc-modal-inner');
        const calcBtn = document.getElementById('calculate-btn');
        const saveBtn = document.getElementById('save-blueprint-btn');
        const resultArea = document.getElementById('calc-result');
        const resultCount = document.getElementById('result-count');
        const addDeductionBtn = document.getElementById('add-deduction-btn');
        const deductionsContainer = document.getElementById('deductions-container');
        const noDeductionsMsg = document.getElementById('no-deductions-msg');
        const inputLength = document.getElementById('calc-length');
        const inputWidth = document.getElementById('calc-width');
        
        // --- Canvas Elements ---
        const canvas = document.getElementById('room-canvas');
        const canvasContainer = document.getElementById('canvas-container');
        const canvasPlaceholder = document.getElementById('canvas-placeholder');
        const ctx = canvas ? canvas.getContext('2d') : null;
        
        // --- State ---
        let deductions = []; // Source of truth for deduction objects
        let room = { L: 0, W: 0 }; // Source of truth for room dimensions
        let totalTiles = 0; // Stores the final calculation result

        // Dragging State
        let isDragging = false;
        let draggedDeductionId = null;
        let dragOffsetX = 0;
        let dragOffsetY = 0;

        /**
         * CORE DRAWING FUNCTION
         * @param {CanvasRenderingContext2D} ctx - The context to draw on (live or export)
         * @param {number} width - Canvas width
         * @param {number} height - Canvas height
         * @param {boolean} isExport - If true, draws for export (no UI helpers like dashed lines if not selected, etc.)
         */
        const drawBlueprintScene = (ctx, width, height, isExport = false) => {
            // 1. Clear Canvas
            ctx.clearRect(0, 0, width, height);

            // 2. Validate Dimensions
            const L = room.L;
            const W = room.W;
            
            if (L <= 0 || W <= 0) {
                if (!isExport && canvasPlaceholder) canvasPlaceholder.style.display = 'flex';
                return;
            }
            if (!isExport && canvasPlaceholder) canvasPlaceholder.style.display = 'none';

            // 3. Calculate Scale
            const padding = isExport ? 100 : 40; // More padding for export
            const availW = width - (padding * 2);
            const availH = height - (padding * 2);
            
            // Safety check to avoid division by zero
            if (availW <= 0 || availH <= 0) return;

            const scale = Math.min(availW / L, availH / W);

            const drawW = L * scale;
            const drawH = W * scale;
            
            // Center the drawing
            const startX = (width - drawW) / 2;
            const startY = (height - drawH) / 2;

            // 4. Draw Room Outline
            ctx.beginPath();
            ctx.strokeStyle = '#cf8756'; // Gold
            ctx.lineWidth = isExport ? 5 : 3;
            ctx.lineCap = 'round';
            ctx.fillStyle = 'rgba(255, 255, 255, 0.05)';
            ctx.rect(startX, startY, drawW, drawH);
            ctx.stroke();
            ctx.fill();

            // 5. Draw Main Dimensions
            ctx.fillStyle = 'white';
            ctx.font = isExport ? 'bold 24px Inter' : '12px Inter';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'bottom';
            // Top Width
            ctx.fillText(`${L} cm`, startX + drawW / 2, startY - (isExport ? 15 : 10));
            
            // Side Height
            ctx.save();
            ctx.translate(startX - (isExport ? 15 : 10), startY + drawH / 2);
            ctx.rotate(-Math.PI / 2);
            ctx.textBaseline = 'bottom';
            ctx.fillText(`${W} cm`, 0, 0);
            ctx.restore();

            // 6. Draw Deductions
            deductions.forEach(item => {
                // Calculate dimensions based on current scale
                const itemDrawW = item.L * scale;
                const itemDrawH = item.W * scale;

                // Position Logic:
                // We store normalized relative positions (0.0 to 1.0) or absolute offsets?
                // To simplify, we will use the 'x' and 'y' stored in the item relative to the *previous* drawing.
                // BUT for export, we must scale positions correctly.
                // If item.x/y are pixel coordinates from the small canvas, they won't work on large canvas.
                // Solution: Store normalized coordinates (percentage of room width/height)
                
                let renderX, renderY;

                // If this is the first draw or re-calculation
                if (item.normX === undefined || item.normY === undefined) {
                     // Default to top-left + slight offset
                     item.normX = 0.05;
                     item.normY = 0.05;
                }

                // Convert Normalized Pos to Pixel Pos
                renderX = startX + (item.normX * drawW);
                renderY = startY + (item.normY * drawH);

                // Clamp to boundaries
                renderX = Math.max(startX, Math.min(renderX, startX + drawW - itemDrawW));
                renderY = Math.max(startY, Math.min(renderY, startY + drawH - itemDrawH));

                // Draw Item
                ctx.fillStyle = 'rgba(248, 113, 113, 0.3)'; // Red tint
                ctx.strokeStyle = 'rgba(248, 113, 113, 1)'; // Solid Red
                ctx.lineWidth = isExport ? 3 : 1;
                
                if (!isExport) ctx.setLineDash([2, 2]);
                
                ctx.fillRect(renderX, renderY, itemDrawW, itemDrawH);
                ctx.strokeRect(renderX, renderY, itemDrawW, itemDrawH);
                
                // Label
                ctx.fillStyle = 'rgba(255,255,255,0.9)';
                ctx.font = isExport ? 'bold 16px Inter' : '10px Inter';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(item.type, renderX + itemDrawW / 2, renderY + itemDrawH / 2);

                if (!isExport) ctx.setLineDash([]);

                // Update pixel coordinates for hit detection (only on live view)
                if (!isExport) {
                    item.x = renderX;
                    item.y = renderY;
                    item.drawW = itemDrawW;
                    item.drawH = itemDrawH;
                }
            });

            // Save boundaries for drag constraints (only on live view)
            if (!isExport) {
                room.boundaries = { startX, startY, drawW, drawH, scale };
            }
        };

        // Wrapper function to draw on the main canvas
        const redrawLiveCanvas = () => {
            if (!ctx || !canvas) return;
            drawBlueprintScene(ctx, canvas.width, canvas.height, false);
        };

        // Resize Canvas to fit container
        const resizeCanvas = () => {
            if (!canvas || !canvasContainer) return;
            canvas.width = canvasContainer.offsetWidth;
            canvas.height = canvasContainer.offsetHeight;
            redrawLiveCanvas(); 
        };

        // Get mouse/touch position relative to canvas
        const getMousePos = (e) => {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;

            return {
                x: (clientX - rect.left) * scaleX,
                y: (clientY - rect.top) * scaleY
            };
        };

        // Check if mouse is over any deduction
        const getDeductionAtPos = (pos) => {
            for (let i = deductions.length - 1; i >= 0; i--) {
                const item = deductions[i];
                if (pos.x >= item.x && pos.x <= item.x + item.drawW &&
                    pos.y >= item.y && pos.y <= item.y + item.drawH) {
                    return item;
                }
            }
            return null;
        };

        // --- Drag and Drop Event Handlers ---
        const onDragStart = (e) => {
            const pos = getMousePos(e);
            const hitItem = getDeductionAtPos(pos);

            if (hitItem) {
                isDragging = true;
                draggedDeductionId = hitItem.id;
                dragOffsetX = pos.x - hitItem.x;
                dragOffsetY = pos.y - hitItem.y;
                canvas.classList.add('grabbing');
                e.preventDefault();
            }
        };

        const onDragMove = (e) => {
            const pos = getMousePos(e);
            
            if (!isDragging) {
                canvas.classList.toggle('grab', !!getDeductionAtPos(pos));
            }

            if (!isDragging || !draggedDeductionId) return;
            e.preventDefault();

            const item = deductions.find(d => d.id === draggedDeductionId);
            if (!item || !room.boundaries) return;

            const { startX, startY, drawW, drawH } = room.boundaries;

            // Calculate new PIXEL position
            let newX = pos.x - dragOffsetX;
            let newY = pos.y - dragOffsetY;

            // Constrain movement within the room boundaries
            newX = Math.max(startX, Math.min(newX, startX + drawW - item.drawW));
            newY = Math.max(startY, Math.min(newY, startY + drawH - item.drawH));
            
            // Update stored pixel position for immediate rendering
            item.x = newX;
            item.y = newY;

            // UPDATE NORMALIZED POSITION for resizing/exporting
            // normX = (pixelX - startX) / roomDrawWidth
            item.normX = (newX - startX) / drawW;
            item.normY = (newY - startY) / drawH;
            
            redrawLiveCanvas();
        };

        const onDragEnd = (e) => {
            isDragging = false;
            draggedDeductionId = null;
            canvas.classList.remove('grabbing');
            canvas.classList.remove('grab');
        };

        // Attach Drag Listeners
        if (canvas) {
            canvas.addEventListener('mousedown', onDragStart);
            canvas.addEventListener('mousemove', onDragMove);
            canvas.addEventListener('mouseup', onDragEnd);
            canvas.addEventListener('mouseout', onDragEnd);
            canvas.addEventListener('touchstart', onDragStart, { passive: false });
            canvas.addEventListener('touchmove', onDragMove, { passive: false });
            canvas.addEventListener('touchend', onDragEnd);
            canvas.addEventListener('touchcancel', onDragEnd);
        }

        // Event Listeners for Live Drawing
        if (inputLength && inputWidth) {
            inputLength.addEventListener('input', () => {
                room.L = parseFloat(inputLength.value) || 0;
                redrawLiveCanvas();
            });
            inputWidth.addEventListener('input', () => {
                room.W = parseFloat(inputWidth.value) || 0;
                redrawLiveCanvas();
            });
        }

        // Modal Toggle Logic
        const toggleModal = (show) => {
            if(show) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    innerModal.classList.remove('scale-95', 'opacity-0');
                    innerModal.classList.add('scale-100', 'opacity-100');
                    resizeCanvas(); // Initialize canvas size
                    redrawLiveCanvas();
                }, 10);
            } else {
                innerModal.classList.remove('scale-100', 'opacity-100');
                innerModal.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }
        };

        if(trigger) trigger.addEventListener('click', () => toggleModal(true));
        if(cancelBtn) cancelBtn.addEventListener('click', () => toggleModal(false));
        if(modal) modal.addEventListener('click', (e) => { if(e.target === modal) toggleModal(false); });
        window.addEventListener('resize', resizeCanvas);

        // Add Deduction Row Logic
        const addDeductionRow = () => {
            if (noDeductionsMsg) noDeductionsMsg.style.display = 'none';

            const newId = crypto.randomUUID();
            const newDeduction = {
                id: newId,
                type: 'Door',
                L: 60, // Default size
                W: 90, // Default size
                // NormX/Y will be initialized in drawBlueprintScene
                drawW: 0,
                drawH: 0
            };
            deductions.push(newDeduction);

            const div = document.createElement('div');
            div.className = 'deduction-row flex items-center gap-2 bg-gray-800/50 p-2 rounded-lg border border-white/5';
            div.dataset.id = newId; // Link DOM row to data object
            div.innerHTML = `
                <span class="text-white/40 pl-1 cursor-grab" title="Drag this item on the blueprint"><i class="fas fa-arrows-alt-v"></i></span>
                <select class="deduction-type bg-gray-900 text-white text-xs border border-white/10 rounded p-2 outline-none focus:border-gold w-1/3">
                    <option value="Door">Door</option>
                    <option value="Sink">Sink</option>
                    <option value="Cubicle">Cubicle</option>
                    <option value="Column">Column</option>
                </select>
                <div class="flex-1">
                    <input type="number" value="${newDeduction.L}" placeholder="Length" class="deduction-length w-full bg-gray-900 border border-white/10 rounded p-2 text-xs text-white outline-none focus:border-gold">
                </div>
                <div class="flex-1">
                    <input type="number" value="${newDeduction.W}" placeholder="Width" class="deduction-width w-full bg-gray-900 border border-white/10 rounded p-2 text-xs text-white outline-none focus:border-gold">
                </div>
                <button type="button" class="delete-row-btn text-red-400 hover:text-red-300 p-2 hover:bg-red-400/10 rounded transition-colors">
                    <i class="fas fa-trash-alt"></i>
                </button>
            `;

            // Events for new row
            const updateDeductionData = (e) => {
                const item = deductions.find(d => d.id === newId);
                if (!item) return;
                
                const target = e.target;
                if (target.classList.contains('deduction-length')) item.L = parseFloat(target.value) || 0;
                if (target.classList.contains('deduction-width')) item.W = parseFloat(target.value) || 0;
                if (target.classList.contains('deduction-type')) item.type = target.value;
                
                redrawLiveCanvas();
            };

            div.querySelector('.deduction-length').addEventListener('input', updateDeductionData);
            div.querySelector('.deduction-width').addEventListener('input', updateDeductionData);
            div.querySelector('.deduction-type').addEventListener('change', updateDeductionData);

            div.querySelector('.delete-row-btn').addEventListener('click', function() {
                // Remove from data array
                deductions = deductions.filter(d => d.id !== newId);
                // Remove from DOM
                div.remove();
                if (deductions.length === 0) noDeductionsMsg.style.display = 'block';
                redrawLiveCanvas();
            });

            deductionsContainer.appendChild(div);
            redrawLiveCanvas(); // Redraw with new item
        };

        if (addDeductionBtn) addDeductionBtn.addEventListener('click', addDeductionRow);

        // Calculation Logic
        if(calcBtn) {
            calcBtn.addEventListener('click', () => {
                const mainArea = room.L * room.W;
                let totalDeductionArea = 0;
                
                deductions.forEach(item => {
                    totalDeductionArea += (item.L * item.W);
                });

                const tileOption = document.querySelector('input[name="tile_size"]:checked').value;
                let tileArea = 0;
                switch(tileOption) {
                    case '30x30': tileArea = 30 * 30; break;
                    case '40x40': tileArea = 40 * 40; break;
                    case '30x60': tileArea = 30 * 60; break;
                    case '60x60': tileArea = 60 * 60; break;
                }

                if(room.L > 0 && room.W > 0 && tileArea > 0) {
                    const netArea = Math.max(0, mainArea - totalDeductionArea);
                    const rawTiles = netArea / tileArea;
                    totalTiles = Math.ceil(rawTiles * 1.10); // Store result
                    
                    resultCount.innerText = totalTiles;
                    resultArea.classList.remove('hidden');
                    
                    // Show Save Button and adjust grid
                    saveBtn.classList.remove('hidden');
                    saveBtn.classList.add('flex');
                    calcBtn.classList.remove('col-span-2');
                    calcBtn.classList.add('col-span-1');
                    
                } else {
                    alert('Please enter valid room dimensions.');
                }
            });
        }

        // --- Save as Image Logic (Completely Rewritten) ---
        if (saveBtn) {
            saveBtn.addEventListener('click', () => {
                const exportCanvas = document.createElement('canvas');
                
                // High Resolution for Print/Save
                const WIDTH = 1200; 
                const HEIGHT = 1600; 
                exportCanvas.width = WIDTH;
                exportCanvas.height = HEIGHT;
                
                const ctx = exportCanvas.getContext('2d');
                if (!ctx) return;

                // 1. Background
                ctx.fillStyle = '#111827'; // Brand Dark Color
                ctx.fillRect(0, 0, WIDTH, HEIGHT);

                // 2. Watermark (Diagonal)
                ctx.save();
                ctx.translate(WIDTH / 2, HEIGHT / 2);
                ctx.rotate(-Math.PI / 6); // -30 degrees
                ctx.font = 'bold 150px Inter';
                ctx.fillStyle = 'rgba(255, 255, 255, 0.03)'; // Very faint white
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('RALTT SHOP', 0, 0);
                ctx.restore();

                // 3. Header Section
                ctx.fillStyle = '#cf8756'; // Gold
                ctx.font = 'bold 60px Inter';
                ctx.textAlign = 'center';
                ctx.fillText('RALTT SHOP', WIDTH / 2, 120);
                
                ctx.fillStyle = 'white';
                ctx.font = '30px Inter';
                ctx.fillText('Tile Calculation Blueprint', WIDTH / 2, 170);

                const date = new Date().toLocaleDateString();
                ctx.font = 'italic 20px Inter';
                ctx.fillStyle = '#9ca3af'; // gray-400
                ctx.fillText(`Generated on ${date}`, WIDTH / 2, 210);

                // 4. Draw Blueprint (Center Area)
                // Reserve top 250px for header, bottom 400px for footer
                const blueprintAreaH = HEIGHT - 250 - 400;
                
                // Create a temporary sub-canvas logic by translating context
                ctx.save();
                ctx.translate(0, 250);
                
                // We pass the blueprint drawing logic the available area width/height
                drawBlueprintScene(ctx, WIDTH, blueprintAreaH, true);
                
                ctx.restore();

                // 5. Results Footer Section
                const footerY = HEIGHT - 350;
                
                // Separator Line
                ctx.beginPath();
                ctx.moveTo(100, footerY);
                ctx.lineTo(WIDTH - 100, footerY);
                ctx.strokeStyle = 'rgba(255,255,255,0.1)';
                ctx.lineWidth = 2;
                ctx.stroke();

                // Tile Count
                ctx.fillStyle = '#cf8756'; // Gold
                ctx.font = 'bold 40px Inter';
                ctx.textAlign = 'center';
                ctx.fillText('Estimated Tiles Needed', WIDTH / 2, footerY + 80);

                ctx.fillStyle = 'white';
                ctx.font = 'bold 120px Inter';
                ctx.fillText(totalTiles, WIDTH / 2, footerY + 200);

                // Details
                ctx.font = '24px Inter';
                ctx.fillStyle = '#9ca3af';
                const tileOption = document.querySelector('input[name="tile_size"]:checked').value;
                ctx.fillText(`Tile Size: ${tileOption} cm | Room: ${room.L}x${room.W} cm`, WIDTH / 2, footerY + 250);
                
                ctx.font = 'italic 18px Inter';
                ctx.fillStyle = '#6b7280';
                ctx.fillText('*Includes suggested 10% wastage allowance', WIDTH / 2, footerY + 300);

                // 6. Trigger Download
                const dataURL = exportCanvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.href = dataURL;
                link.download = `RALTT_Blueprint_${room.L}x${room.W}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        }

    })();
});
</script>

<script>
window.chatbaseConfig = {
    chatbotId: "-vAdaLts54qAK1OtQj9SL",
}
</script>
<script src="https://www.chatbase.co/embed.min.js" id="-vAdaLts54qAK1OtQj9SL" defer></script>

</body>
</html>