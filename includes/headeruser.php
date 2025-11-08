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

// Function to log branch change event
function log_branch_change($user_id, $branch_id, $db_connection) {
    try {
        $stmt = $db_connection->prepare("INSERT INTO customer_branch_trail (user_id, branch_id, event_type) VALUES (?, ?, 'branch_change')");
        $stmt->execute([$user_id, $branch_id]);
    } catch (Exception $e) {
        // Optionally log error
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
        /* ...existing code... */
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

<!-- Header Section -->
<header class="raltt-header fixed top-0 left-0 w-full z-50 glass-effect border-b border-white/10">
    <div class="container mx-auto flex items-center justify-between px-4 h-20">
        <!-- Logo -->
        <a href="../logged_user/landing_page.php" class="flex items-center gap-3 z-50">
            <img src="../images/userlogo.png" alt="RALTT Shop Logo" class="w-14 h-14 object-contain">
            <div class="flex flex-col leading-tight">
                <span class="text-white font-extrabold text-xl tracking-tighter">RALTT SHOP</span>
                <span class="text-gold font-semibold text-sm">Premium Tiles & More</span>
            </div>
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden lg:flex items-center gap-2">
            <a href="../logged_user/landing_page.php" class="font-semibold px-5 py-2 rounded-lg hover:bg-white/10 transition-colors">Home</a>
            <a href="../logged_user/landing_page.php#premium-tiles" class="font-semibold px-5 py-2 rounded-lg hover:bg-white/10 transition-colors">Products</a>
            <a href="../logged_user/3dvisualizer.php" class="font-semibold px-5 py-2 rounded-lg hover:bg-white/10 transition-colors">3D Visualizer</a>
            <a href="user_my_cart.php" class="font-semibold px-5 py-2 rounded-lg hover:bg-white/10 transition-colors">My Cart</a>
        </nav>

        <!-- User Actions -->
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
            <!-- Notification Bell -->
            <div class="relative group">
                <button id="notif-bell-btn" class="relative flex items-center justify-center w-10 h-10 rounded-full hover:bg-white/10 transition-colors focus:outline-none">
                    <i class="fa fa-bell text-gold text-xl"></i>
                    <span id="notif-unread-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full px-1.5 py-0.5 animate-pulse shadow-lg" style="display:none;"></span>
                </button>
                <!-- Dropdown -->
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
                                            '<span>&middot;</span>' +
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
            <!-- User Dropdown -->
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
                    <!-- AR Tile Access Link Added -->
                    <a href="../logged_user/ARTile_Access.php" class="flex items-center gap-3 px-4 py-2.5 text-white/90 hover:bg-gold hover:text-white transition-colors">
                        <i class="fas fa-qrcode w-5 text-center"></i> AR Tile Access
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
            <!-- Mobile Menu Toggle -->
            <button id="mobile-menu-toggle" class="lg:hidden burger flex flex-col justify-center items-center w-10 h-10 z-[100]">
                <span class="burger-line w-6 h-0.5 bg-white mb-1.5 rounded-full"></span>
                <span class="burger-line w-6 h-0.5 bg-white mb-1.5 rounded-full"></span>
                <span class="burger-line w-6 h-0.5 bg-white rounded-full"></span>
            </button>
        </div>
    </div>

    
</header>

<!-- Mobile Menu -->
<div id="mobile-menu" class="lg:hidden fixed inset-0 z-40 bg-gray-900/95 transform translate-x-full transition-transform duration-300 backdrop-blur-md">
    <div class="flex flex-col h-full pt-24 pb-8 px-6">
        <nav class="flex flex-col space-y-2 flex-1">
            <a href="../logged_user/landing_page.php" class="nav-link">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="../logged_user/landing_page.php#premium-tiles" class="nav-link">
                <i class="fas fa-th-large"></i> Products
            </a>
            <a href="../logged_user/3dvisualizer.php" class="nav-link">
                <i class="fas fa-cube"></i> 3D Visualizer
            </a>
            <a href="user_my_cart.php" class="nav-link">
                <i class="fas fa-shopping-cart"></i> My Cart
            </a>
            <div class="pt-6 border-t border-white/10 mt-4">
                <a href="../logged_user/myProfile.php" class="nav-link">
                    <i class="fas fa-user-circle"></i> My Account
                </a>
                <!-- AR Tile Access Link Added for Mobile -->
                <a href="../logged_user/ARTile_Access.php" class="nav-link">
                    <i class="fas fa-qrcode"></i> AR Tile Access
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

<!-- Branch Location Overlay -->
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

<!-- Branch Selection Modal -->
<div id="branch-modal" class="hidden fixed inset-0 z-[100] bg-black/50 flex items-center justify-center p-4">
    <div class="bg-gray-800 text-white rounded-2xl shadow-2xl max-w-5xl w-full overflow-hidden border border-white/10 branch-modal-content">
        <div class="p-5 text-center border-b border-white/10 relative">
            <h2 class="text-xl font-bold">Select Your Branch</h2>
            <p class="text-sm text-white/60 mt-1">Enable location to choose a branch.</p>
            <button id="close-branch-modal" class="absolute top-3 right-3 w-8 h-8 rounded-full hover:bg-white/10 flex items-center justify-center transition-colors">
                &times;
            </button>
        </div>
        
        <div id="branch-location-status" class="p-3 text-center text-sm"></div>
        
        <div class="branch-modal-body flex flex-col md:flex-row">
            <!-- Branch List Section -->
            <div class="w-full md:w-1/3 p-4 border-r border-white/10">
                <div id="branch-list" class="space-y-2 max-h-[60vh] overflow-y-auto custom-scrollbar pr-2">
                    <!-- Branch list will be populated by JavaScript -->
                </div>
            </div>
            
            <!-- Map and Branch Details Section -->
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

<!-- Loading Overlay -->
<div id="page-loading-overlay" class="hidden fixed inset-0 z-[99999] bg-gray-900/80 backdrop-blur-sm flex items-center justify-center">
    <div class="text-center">
        <i class="fas fa-spinner fa-spin text-4xl text-gold mb-4"></i>
        <div class="text-xl font-semibold">Switching Branch...</div>
    </div>
</div>

<script>
// Notification Bell Dropdown (show on click for mobile/desktop)
document.addEventListener('DOMContentLoaded', () => {
    // Notification dropdown logic
    const notifBellBtn = document.getElementById('notif-bell-btn');
    const notifDropdown = document.getElementById('notif-dropdown');
    if (notifBellBtn && notifDropdown) {
        let dropdownOpen = false;
        const openDropdown = () => {
            notifDropdown.classList.remove('hidden');
            dropdownOpen = true;
        };
        const closeDropdown = () => {
            notifDropdown.classList.add('hidden');
            dropdownOpen = false;
        };
        notifBellBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (dropdownOpen) {
                closeDropdown();
            } else {
                openDropdown();
                // Mark notifications as read (AJAX)
                fetch('../connection/mark_notifications_read.php', { method: 'POST' });
                // Optionally, remove badge
                const badge = notifBellBtn.querySelector('span');
                if (badge) badge.style.display = 'none';
            }
        });
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (dropdownOpen && !notifDropdown.contains(e.target) && e.target !== notifBellBtn) {
                closeDropdown();
            }
        });
    }

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
        const searchToggle = document.getElementById('search-toggle');
        const desktopSearch = document.getElementById('desktop-search');
        const closeSearch = document.getElementById('close-search');

        // Mobile Menu Toggle
        const toggleMobileMenu = () => {
            const isOpen = mobileMenuToggle.classList.toggle('open');
            mobileMenu.classList.toggle('translate-x-full');
            document.body.style.overflow = isOpen ? 'hidden' : '';
        };

        // Search Toggle
        const toggleSearch = () => {
            const isHidden = desktopSearch.classList.toggle('hidden');
            if (!isHidden) {
                desktopSearch.querySelector('input')?.focus();
            }
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

        if (searchToggle) {
            searchToggle.addEventListener('click', toggleSearch);
        }

        if (closeSearch) {
            closeSearch.addEventListener('click', toggleSearch);
        }

        // Escape key to close search
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !desktopSearch.classList.contains('hidden')) {
                toggleSearch();
            }
        });
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
                     Math.cos(lat1 * Math.PI / 180) * 
                     Math.cos(lat2 * Math.PI / 180) * 
                     Math.sin(dLon / 2) ** 2;
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

            // Check if on checkout page - disable branch selection
            if (isCheckoutPage) {
                updateLocationStatus('Branch selection is disabled during checkout.', 'error');
                renderBranchList(null, false);
                return;
            }

            // Normal branch selection flow
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
    let errorMessage = 'Location access denied. Please enable location services to change branches.';
    if (error.code === error.TIMEOUT) {
        errorMessage = 'Location request timed out. Please try again.';
    }
    updateLocationStatus(errorMessage, 'error');
    renderBranchList(null, false); // Disable branch selection if no location
    if (selectBranchBtn) {
        selectBranchBtn.disabled = true;
        selectBranchBtn.textContent = 'Enable Location to Select';
    }
},
                { 
                    timeout: 10000,
                    enableHighAccuracy: false 
                }
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
                    if (distanceEl) {
                        distanceEl.textContent = '';
                    }
                },
                { 
                    enableHighAccuracy: false,
                    timeout: 5000 
                }
            );
        };

        // Start geolocation watcher
        const startGeolocationWatcher = () => {
            checkGeolocation();
            if (geolocationInterval) {
                clearInterval(geolocationInterval);
            }
            geolocationInterval = setInterval(checkGeolocation, 30000); // Check every 30 seconds
        };

        // Event Listeners
        if (changeBtn) {
            changeBtn.addEventListener('click', openModal);
        }

        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', closeModal);
        }

        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModal();
                }
            });
        }

        if (selectBranchBtn) {
            selectBranchBtn.addEventListener('click', () => {
                if (selectedBranch && !isCheckoutPage) {
                    selectBranch(selectedBranch.id);
                }
            });
        }

        // Initialize
        startGeolocationWatcher();
    })();
});
</script>

<!-- Chatbase Chatbot -->
<script>
window.chatbaseConfig = {
    chatbotId: "-vAdaLts54qAK1OtQj9SL",
}
</script>
<script src="https://www.chatbase.co/embed.min.js" id="-vAdaLts54qAK1OtQj9SL" defer></script>

</body>
</html>