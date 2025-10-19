<?php
// Start session and check if user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ../connection/tresspass.php');
    exit();
}

// Generate Chatbase hash
$chatbaseSecret = '60sp90gtn7uvp2l2xlpe8u05kt4z4lt4';
$chatbaseUserId = $_SESSION['user_id'] ?? session_id();
$chatbaseHash = hash_hmac('sha256', $chatbaseUserId, $chatbaseSecret);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RALTT Shop</title>
    <link rel="icon" type="image/png" href="../images/userlogo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .glass-effect {
            background: rgba(15, 15, 15, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        .gradient-primary {
            background: linear-gradient(135deg, #7d310a 0%, #cf8756 50%, #e8a56a 100%);
        }
        
        .gradient-gold {
            background: linear-gradient(135deg, #b88b4a 0%, #d4af37 50%, #f4e4b8 100%);
        }
        
        .text-gold {
            color: #cf8756;
        }
        
        .border-gold {
            border-color: #cf8756;
        }
        
        .burger-line {
            transition: all 0.3s ease;
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
        
        .dropdown-enter {
            opacity: 0;
            transform: translateY(-10px);
        }
        
        .dropdown-enter-active {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.2s, transform 0.2s;
        }

        /* Search bar animations */
        .search-bar-enter {
            opacity: 0;
            transform: translateY(-10px);
            max-height: 0;
        }
        
        .search-bar-enter-active {
            opacity: 1;
            transform: translateY(0);
            max-height: 100px;
            transition: all 0.3s ease-out;
        }
        
        .search-bar-exit {
            opacity: 1;
            transform: translateY(0);
            max-height: 100px;
        }
        
        .search-bar-exit-active {
            opacity: 0;
            transform: translateY(-10px);
            max-height: 0;
            transition: all 0.3s ease-in;
        }
        
        /* Disabled branch styling */
        .branch-disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .branch-disabled:hover {
            background-color: #f9fafb !important;
            border-color: #d1d5db !important;
        }
    </style>
</head>
<body class="bg-gray-900 text-white" style="margin-top: 4.5rem;">
<script>
// Check session every 1 second
setInterval(function() {
    fetch('../connection/check_session.php', { method: 'POST' })
        .then(response => response.text())
        .then(data => {
            if (data.trim() !== 'OK') {
                window.location.href = '../connection/tresspass.php';
            }
        })
        .catch(() => {
            window.location.href = '../connection/tresspass.php';
        });
}, 1000);
</script>

<?php
require_once __DIR__ . '/../connection/connection.php';
$branches = [];
try {
    $stmt = $db_connection->query("SELECT branch_id AS id, branch_name AS name, latitude AS lat, longitude AS lng FROM branches ORDER BY branch_id ASC");
    $branches = $stmt->fetchAll();
} catch (Exception $e) {
    $branches = [
        [ 'id' => 1, 'name' => 'Deparo',   'lat' => 14.75243153, 'lng' => 121.01763335 ],
        [ 'id' => 2, 'name' => 'Vanguard', 'lat' => 14.75920200, 'lng' => 121.06286101 ],
        [ 'id' => 3, 'name' => 'Brixton',  'lat' => 14.76724928, 'lng' => 121.04104486 ],
        [ 'id' => 4, 'name' => 'Samaria',  'lat' => 14.76580311, 'lng' => 121.06563606 ],
        [ 'id' => 5, 'name' => 'Phase 1',  'lat' => 14.77682717, 'lng' => 121.04841432 ],
    ];
}
$user_branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
$user_branch = null;
foreach ($branches as $b) {
    if ($b['id'] === $user_branch_id) { $user_branch = $b; break; }
}
echo '<script>window.BRANCHES = ' . json_encode($branches) . '; window.USER_BRANCH = ' . json_encode($user_branch) . ';</script>';

// Branch overlay
$branchOverlay = '<div id="branch-location-overlay" class="fixed top-28 left-6 z-50 glass-effect text-white p-4 rounded-2xl shadow-2xl border-gold border cursor-move select-none max-w-xs opacity-95 backdrop-blur-sm">';
$branchOverlay .= '<div class="flex items-center gap-2 mb-1">';
$branchOverlay .= '<span class="w-2 h-2 bg-gold rounded-full"></span>';
$branchOverlay .= '<span class="text-sm font-medium opacity-85">You\'re currently browsing at</span>';
$branchOverlay .= '</div>';
$branchOverlay .= '<div class="flex items-center gap-2">';
$branchOverlay .= '<span id="branch-current" class="font-bold text-lg">';
if ($user_branch) {
    $branchOverlay .= htmlspecialchars($user_branch['name']) . ' Branch';
} else {
    $branchOverlay .= '<i>Locating branch...</i>';
}
$branchOverlay .= '</span>';
$branchOverlay .= '<span id="branch-distance" class="text-xs text-gold opacity-85"></span>';
$branchOverlay .= '<a id="branch-change-link" href="#" class="text-xs text-gold underline cursor-pointer opacity-85 ml-2">Change</a>';
$branchOverlay .= '</div>';
$branchOverlay .= '</div>';
echo $branchOverlay;

// Loading overlay for branch change
echo '<div id="branch-loading-overlay" class="hidden fixed inset-0 z-[100000] bg-gray-900 bg-opacity-85 text-white flex items-center justify-center">';
echo '<div class="text-center">';
echo '<div class="fa fa-spinner fa-spin text-4xl mb-4"></div>';
echo '<div class="text-xl font-semibold">Switching branch...</div>';
echo '</div>';
echo '</div>';
?>

<!-- Header -->
<header class="raltt-header fixed top-0 left-0 w-full z-40 glass-effect border-b border-white border-opacity-10">
    <div class="flex items-center justify-between px-4 lg:px-8 h-16 lg:h-20">
        <!-- Logo -->
        <div class="flex items-center gap-3 z-50">
            <img src="../images/userlogo.png" alt="RALTT Shop" class="w-12 h-12 lg:w-14 lg:h-14 object-contain">
            <div class="flex flex-col leading-tight">
                <span class="text-white font-bold text-lg lg:text-xl tracking-tight">RALTT SHOP</span>
                <span class="text-gold font-medium text-sm lg:text-base">Premium Tiles & More</span>
            </div>
        </div>

        <!-- Desktop Navigation -->
        <div class="hidden lg:flex flex-1 items-center justify-center max-w-4xl mx-8">
            <nav class="flex items-center gap-8 bg-black bg-opacity-20 rounded-full px-8 py-3 shadow-lg">
                <a href="../logged_user/landing_page.php" class="flex items-center gap-2 text-white hover:text-gold transition-colors duration-200 font-semibold px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-10">
                    <i class="fa fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="../logged_user/landing_page.php#premium-tiles" class="flex items-center gap-2 text-white hover:text-gold transition-colors duration-200 font-semibold px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-10">
                    <i class="fa fa-th-large"></i>
                    <span>Products</span>
                </a>
                <a href="../logged_user/3dvisualizer.php" class="flex items-center gap-2 text-white hover:text-gold transition-colors duration-200 font-semibold px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-10">
                    <i class="fa fa-cube"></i>
                    <span>3D Visualizer</span>
                </a>
                <a href="user_my_cart.php" class="flex items-center gap-2 text-white hover:text-gold transition-colors duration-200 font-semibold px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-10">
                    <i class="fa fa-shopping-cart"></i>
                    <span>My Cart</span>
                </a>
            </nav>
        </div>

        <!-- Desktop User Area -->
        <div class="hidden lg:flex items-center gap-6 z-50">
            <!-- Search Icon -->
            <button id="search-toggle" class="text-white hover:text-gold transition-colors duration-200 text-xl p-2 rounded-full hover:bg-white hover:bg-opacity-10">
                <i class="fa fa-search"></i>
            </button>

            <!-- User Dropdown -->
            <div class="relative group">
                <button class="flex items-center gap-3 text-white hover:text-gold transition-colors duration-200 p-2 rounded-lg hover:bg-white hover:bg-opacity-10">
                    <div class="w-10 h-10 gradient-primary rounded-full flex items-center justify-center shadow-lg">
                        <i class="fa fa-user text-white"></i>
                    </div>
                    <span class="font-semibold">Account</span>
                    <i class="fa fa-caret-down"></i>
                </button>
                
                <div class="absolute right-0 top-12 w-48 bg-gray-800 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 dropdown-enter border border-gray-600">
                    <div class="py-2">
                        <a href="../logged_user/myProfile.php" class="flex items-center gap-3 px-4 py-3 text-white hover:bg-gold hover:bg-opacity-20 transition-colors duration-200">
                            <i class="fa fa-user w-5 text-center"></i>
                            <span>My Account</span>
                        </a>
                        <a href="../logged_user/customer_ticket.php" class="flex items-center gap-3 px-4 py-3 text-white hover:bg-gold hover:bg-opacity-20 transition-colors duration-200">
                            <i class="fa fa-ticket w-5 text-center"></i>
                            <span>Customer Ticket</span>
                        </a>
                        <a href="../logout.php" class="flex items-center gap-3 px-4 py-3 text-white hover:bg-gold hover:bg-opacity-20 transition-colors duration-200">
                            <i class="fa fa-sign-out w-5 text-center"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Button - Fixed z-index and visibility -->
        <button id="mobile-menu-toggle" class="lg:hidden burger flex flex-col justify-center items-center w-10 h-10 z-[100] relative">
            <span class="burger-line w-6 h-0.5 bg-white mb-1.5 rounded-full"></span>
            <span class="burger-line w-6 h-0.5 bg-white mb-1.5 rounded-full"></span>
            <span class="burger-line w-6 h-0.5 bg-white rounded-full"></span>
        </button>
    </div>

    <!-- Desktop Search Bar -->
    <div id="desktop-search" class="hidden lg:block search-bar-enter">
        <div class="flex justify-center px-8 pb-4">
            <div class="w-full max-w-2xl bg-gray-800 rounded-full px-6 py-3 flex items-center gap-4 shadow-lg border border-gray-600">
                <i class="fa fa-search text-gray-400"></i>
                <input type="text" placeholder="Search products, categories, brands..." class="flex-1 bg-transparent text-white placeholder-gray-400 outline-none text-lg">
                <button id="close-search" class="text-white hover:text-gold transition-colors duration-200 text-lg p-1 rounded-full hover:bg-white hover:bg-opacity-10">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="lg:hidden fixed inset-0 z-30 bg-gray-900 bg-opacity-95 transform translate-x-full transition-transform duration-300">
        <div class="flex flex-col h-full pt-20 pb-8 px-6">
            <!-- Mobile Search -->
            <div class="mb-6">
                <div class="bg-gray-800 rounded-full px-4 py-3 flex items-center gap-4 border border-gray-600">
                    <i class="fa fa-search text-gray-400"></i>
                    <input type="text" placeholder="Search products..." class="flex-1 bg-transparent text-white placeholder-gray-400 outline-none">
                    <button class="text-white hover:text-gold transition-colors duration-200">
                        <i class="fa fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <nav class="flex flex-col space-y-2 flex-1">
                <a href="../logged_user/landing_page.php" class="flex items-center gap-4 text-white hover:text-gold transition-colors duration-200 font-semibold text-lg py-4 px-4 rounded-xl hover:bg-white hover:bg-opacity-10">
                    <i class="fa fa-home w-6 text-center"></i>
                    <span>Home</span>
                </a>
                <a href="../logged_user/landing_page.php#premium-tiles" class="flex items-center gap-4 text-white hover:text-gold transition-colors duration-200 font-semibold text-lg py-4 px-4 rounded-xl hover:bg-white hover:bg-opacity-10">
                    <i class="fa fa-th-large w-6 text-center"></i>
                    <span>Products</span>
                </a>
                <a href="../logged_user/3dvisualizer.php" class="flex items-center gap-4 text-white hover:text-gold transition-colors duration-200 font-semibold text-lg py-4 px-4 rounded-xl hover:bg-white hover:bg-opacity-10">
                    <i class="fa fa-cube w-6 text-center"></i>
                    <span>3D Visualizer</span>
                </a>
                <a href="user_my_cart.php" class="flex items-center gap-4 text-white hover:text-gold transition-colors duration-200 font-semibold text-lg py-4 px-4 rounded-xl hover:bg-white hover:bg-opacity-10">
                    <i class="fa fa-shopping-cart w-6 text-center"></i>
                    <span>My Cart</span>
                </a>
                
                <!-- Mobile Account Links -->
                <div class="pt-6 border-t border-gray-700 mt-4">
                    <div class="text-xs uppercase text-gray-400 font-semibold px-4 mb-3">Account</div>
                    <a href="../logged_user/myProfile.php" class="flex items-center gap-4 text-white hover:text-gold transition-colors duration-200 font-semibold text-lg py-4 px-4 rounded-xl hover:bg-white hover:bg-opacity-10">
                        <i class="fa fa-user w-6 text-center"></i>
                        <span>My Account</span>
                    </a>
                    <a href="../logged_user/customer_ticket.php" class="flex items-center gap-4 text-white hover:text-gold transition-colors duration-200 font-semibold text-lg py-4 px-4 rounded-xl hover:bg-white hover:bg-opacity-10">
                        <i class="fa fa-ticket w-6 text-center"></i>
                        <span>Customer Ticket</span>
                    </a>
                    <a href="../logout.php" class="flex items-center gap-4 text-white hover:text-gold transition-colors duration-200 font-semibold text-lg py-4 px-4 rounded-xl hover:bg-white hover:bg-opacity-10">
                        <i class="fa fa-sign-out w-6 text-center"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </nav>
        </div>
    </div>
</header>

<!-- Branch Location Scripts -->
<script>
// --- Make branch overlay draggable ---
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('branch-location-overlay');
    if (!overlay) return;
    
    let isDragging = false, startX = 0, startY = 0, origX = 0, origY = 0;
    
    overlay.addEventListener('mousedown', function(e) {
        if (e.button !== 0) return;
        isDragging = true;
        startX = e.clientX;
        startY = e.clientY;
        const rect = overlay.getBoundingClientRect();
        origX = rect.left;
        origY = rect.top;
        overlay.style.transition = 'none';
        document.body.classList.add('select-none', 'overflow-hidden');
    });

    document.addEventListener('mousemove', function(e) {
        if (!isDragging) return;
        const dx = e.clientX - startX;
        const dy = e.clientY - startY;
        overlay.style.left = (origX + dx) + 'px';
        overlay.style.top = (origY + dy) + 'px';
    });

    document.addEventListener('mouseup', function() {
        if (isDragging) {
            isDragging = false;
            overlay.style.transition = '';
            document.body.classList.remove('select-none', 'overflow-hidden');
        }
    });

    // Touch support
    overlay.addEventListener('touchstart', function(e) {
        if (e.touches.length !== 1) return;
        isDragging = true;
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        const rect = overlay.getBoundingClientRect();
        origX = rect.left;
        origY = rect.top;
        overlay.style.transition = 'none';
        document.body.classList.add('select-none', 'overflow-hidden');
    });

    document.addEventListener('touchmove', function(e) {
        if (!isDragging || e.touches.length !== 1) return;
        e.preventDefault();
        const dx = e.touches[0].clientX - startX;
        const dy = e.touches[0].clientY - startY;
        overlay.style.left = (origX + dx) + 'px';
        overlay.style.top = (origY + dy) + 'px';
    }, {passive: false});

    document.addEventListener('touchend', function() {
        if (isDragging) {
            isDragging = false;
            overlay.style.transition = '';
            document.body.classList.remove('select-none', 'overflow-hidden');
        }
    });
});

// --- Branch overlay logic ---
function haversine(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function showNearestBranch(userLat, userLng) {
    if (!window.BRANCHES) return;
    let minDist = Infinity, nearest = null;
    window.BRANCHES.forEach(b => {
        const dist = haversine(userLat, userLng, b.lat, b.lng);
        if (dist < minDist) { minDist = dist; nearest = b; }
    });
    
    if (nearest) {
        const el = document.getElementById('branch-current');
        if (el) {
            el.innerHTML = nearest.name + ' Branch';
        }
        const distEl = document.getElementById('branch-distance');
        if (distEl) {
            distEl.innerHTML = '(' + minDist.toFixed(2) + ' km)';
        }
        if (!window.USER_BRANCH || window.USER_BRANCH.id !== nearest.id) {
            fetch('set_branch.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'branch_id=' + encodeURIComponent(nearest.id)
            }).then(() => {
                window.USER_BRANCH = nearest;
            });
        }
    }
}

// Global variable to track geolocation status
window.geolocationEnabled = false;

// Function to check geolocation status
function checkGeolocationStatus() {
    return new Promise((resolve) => {
        if (!navigator.geolocation) {
            resolve(false);
            return;
        }
        
        navigator.geolocation.getCurrentPosition(
            () => resolve(true),
            () => resolve(false),
            { timeout: 3000 }
        );
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize geolocation status
    checkGeolocationStatus().then(enabled => {
        window.geolocationEnabled = enabled;
    });
    
    // Real-time branch overlay update if geolocation is enabled
    if (window.USER_BRANCH && window.USER_BRANCH.id && navigator.geolocation) {
        navigator.geolocation.watchPosition(function(pos) {
            const userLat = pos.coords.latitude, userLng = pos.coords.longitude;
            const branch = window.BRANCHES.find(b=>b.id===window.USER_BRANCH.id);
            if (branch) {
                const dist = haversine(userLat, userLng, branch.lat, branch.lng);
                const distEl = document.getElementById('branch-distance');
                if (distEl) distEl.innerHTML = '(' + dist.toFixed(2) + ' km)';
            }
        }, function() {}, {enableHighAccuracy:true, maximumAge:10000, timeout:5000});
    }

    const changeLink = document.getElementById('branch-change-link');
    if (changeLink) {
        changeLink.onclick = function(e) {
            e.preventDefault();
            openBranchChangeModal();
        };
    }
});

function openBranchChangeModal() {
    if (!window.BRANCHES) return;
    
    // Remove existing modal if any
    let modal = document.getElementById('branch-change-modal');
    if (modal) modal.remove();
    
    // Create new modal
    modal = document.createElement('div');
    modal.id = 'branch-change-modal';
    modal.className = 'fixed inset-0 z-[99999] bg-black bg-opacity-35 flex items-center justify-center p-4';
    
    let html = `<div class="bg-white text-gray-900 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
        <div class="gradient-primary p-6 text-center text-white relative">
            <button id='close-branch-modal' class='absolute top-4 right-4 text-2xl text-white hover:text-gray-200 transition-colors duration-200 w-8 h-8 flex items-center justify-center rounded-full hover:bg-white hover:bg-opacity-20'>&times;</button>
            <div class="text-2xl font-black tracking-wide">Select Branch</div>
            <div class="mt-2 text-sm opacity-90">Choose your preferred branch location</div>
        </div>
        <div class='p-6 space-y-3 max-h-96 overflow-y-auto'>`;
    
    // Check if geolocation is available and enabled
    const hasGeolocation = navigator.geolocation;
    let locationAvailable = false;
    let userLat = null;
    let userLng = null;
    
    // Try to get current position if geolocation is available
    if (hasGeolocation) {
        navigator.geolocation.getCurrentPosition(
            function(pos) {
                locationAvailable = true;
                userLat = pos.coords.latitude;
                userLng = pos.coords.longitude;
                
                // Update the modal with distances
                updateBranchDistances(userLat, userLng);
            },
            function(error) {
                locationAvailable = false;
                // Show warning but still allow branch selection
                showLocationWarning();
            },
            { timeout: 5000 }
        );
    } else {
        locationAvailable = false;
        showLocationWarning();
    }
    
    // Generate branch list
    window.BRANCHES.forEach(b => {
        let dist = '';
        let isSelected = window.USER_BRANCH && window.USER_BRANCH.id === b.id;
        
        if (isSelected) {
            html += `<div class="flex items-center justify-between w-full p-4 text-base font-semibold rounded-xl gradient-primary text-white shadow-lg border-2 border-gold">
                <span><i class="fa fa-map-marker-alt mr-2"></i>${b.name} Branch${dist}</span>
                <span class='text-xs bg-white bg-opacity-20 px-3 py-1 rounded-full'>Current</span>
                <i class='fa fa-check-circle ml-2'></i>
            </div>`;
        } else {
            html += `<button data-branch="${b.id}" class="branch-select-btn flex items-center justify-between w-full p-4 text-base font-semibold rounded-xl bg-gray-50 text-gray-900 hover:bg-gray-100 transition-colors duration-200 border border-gray-200 hover:border-gold group">
                <span><i class="fa fa-map-marker-alt mr-2 text-gold"></i>${b.name} Branch</span>
                <span class='distance-badge text-xs bg-gold text-white px-3 py-1 rounded-full opacity-50'>Calculating...</span>
            </button>`;
        }
    });
    
    html += `</div></div>`;
    modal.innerHTML = html;
    document.body.appendChild(modal);

    // Add event listeners to branch buttons
    modal.querySelectorAll('.branch-select-btn').forEach(btn => {
        btn.onclick = function() {
            const branchId = this.getAttribute('data-branch');
            const loadingOverlay = document.getElementById('branch-loading-overlay');
            if (loadingOverlay) loadingOverlay.classList.remove('hidden');
            
            fetch('set_branch.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'branch_id=' + encodeURIComponent(branchId)
            }).then(() => {
                window.USER_BRANCH = window.BRANCHES.find(b=>b.id==branchId);
                document.body.removeChild(modal);
                location.reload();
            }).catch(error => {
                console.error('Error switching branch:', error);
                if (loadingOverlay) loadingOverlay.classList.add('hidden');
                alert('Error switching branch. Please try again.');
            });
        };
    });

    // Close modal handlers
    document.getElementById('close-branch-modal').onclick = function() {
        document.body.removeChild(modal);
    };

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            document.body.removeChild(modal);
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('branch-change-modal')) {
            document.body.removeChild(modal);
        }
    });
    
    // Helper function to update distances
    function updateBranchDistances(lat, lng) {
        window.BRANCHES.forEach(b => {
            const dist = haversine(lat, lng, b.lat, b.lng);
            const badge = modal.querySelector(`button[data-branch="${b.id}"] .distance-badge`);
            if (badge) {
                badge.textContent = dist.toFixed(2) + ' km';
                badge.classList.remove('opacity-50');
            }
        });
    }
    
    // Helper function to show location warning
    function showLocationWarning() {
        const warningHtml = `<div class='bg-yellow-50 text-yellow-700 p-3 rounded-lg text-sm text-center border border-yellow-200 mb-3'>
            <i class='fa fa-exclamation-circle mr-2'></i>
            Location access is limited. Distances may not be accurate.
        </div>`;
        modal.querySelector('.p-6').insertAdjacentHTML('afterbegin', warningHtml);
        
        // Set default distances for all branches
        window.BRANCHES.forEach(b => {
            const badge = modal.querySelector(`button[data-branch="${b.id}"] .distance-badge`);
            if (badge) {
                badge.textContent = 'Select';
                badge.classList.remove('opacity-50');
            }
        });
    }
}

// Enhanced Mobile menu and Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const burger = document.getElementById('mobile-menu-toggle');
    
    // Search functionality
    const searchToggle = document.getElementById('search-toggle');
    const desktopSearch = document.getElementById('desktop-search');
    const closeSearch = document.getElementById('close-search');
    
    // Mobile menu toggle
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('translate-x-full');
            burger.classList.toggle('open');
            document.body.style.overflow = burger.classList.contains('open') ? 'hidden' : '';
        });
        
        // Close mobile menu when clicking on links
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.add('translate-x-full');
                burger.classList.remove('open');
                document.body.style.overflow = '';
            });
        });
    }
    
    // Enhanced Search toggle with animations
    if (searchToggle && desktopSearch && closeSearch) {
        let isSearchOpen = false;
        
        searchToggle.addEventListener('click', function() {
            if (!isSearchOpen) {
                // Open search
                desktopSearch.classList.remove('hidden');
                // Force reflow
                desktopSearch.offsetHeight;
                desktopSearch.classList.remove('search-bar-enter');
                desktopSearch.classList.add('search-bar-enter-active');
                isSearchOpen = true;
                
                // Focus input
                const searchInput = desktopSearch.querySelector('input');
                if (searchInput) {
                    setTimeout(() => searchInput.focus(), 300);
                }
            }
        });
        
        closeSearch.addEventListener('click', function() {
            if (isSearchOpen) {
                desktopSearch.classList.remove('search-bar-enter-active');
                desktopSearch.classList.add('search-bar-exit-active');
                
                setTimeout(() => {
                    desktopSearch.classList.add('hidden');
                    desktopSearch.classList.remove('search-bar-exit-active');
                    desktopSearch.classList.add('search-bar-enter');
                    isSearchOpen = false;
                }, 300);
            }
        });
        
        // Close search when clicking outside
        document.addEventListener('click', function(e) {
            if (isSearchOpen && 
                !desktopSearch.contains(e.target) && 
                !searchToggle.contains(e.target)) {
                closeSearch.click();
            }
        });
        
        // Close search on escape key
        document.addEventListener('keydown', function(e) {
            if (isSearchOpen && e.key === 'Escape') {
                closeSearch.click();
            }
        });
    }
    
    // Close mobile menu on window resize to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            mobileMenu.classList.add('translate-x-full');
            burger.classList.remove('open');
            document.body.style.overflow = '';
        }
    });
});
</script>

<!-- Chatbase Script -->
<script>
(function(){if(!window.chatbase||window.chatbase("getState")!=="initialized"){window.chatbase=(...arguments)=>{if(!window.chatbase.q){window.chatbase.q=[]}window.chatbase.q.push(arguments)};window.chatbase=new Proxy(window.chatbase,{get(target,prop){if(prop==="q"){return target.q}return(...args)=>target(prop,...args)}})}const onLoad=function(){const script=document.createElement("script");script.src="https://www.chatbase.co/embed.min.js";script.id="-vAdaLts54qAK1OtQj9SL";script.domain="www.chatbase.co";document.body.appendChild(script)};if(document.readyState==="complete"){onLoad()}else{window.addEventListener("load",onLoad)}})();
</script>
</body>
</html>