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

        body { font-family: 'Inter', sans-serif; background-color: #111827; }
        .glass-effect { background: rgba(17, 24, 39, 0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
        .text-gold { color: var(--color-gold); }
        .border-gold { border-color: var(--color-gold); }
        .bg-gold { background-color: var(--color-gold); }
        
        /* Burger menu animation */
        .burger-line { transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1); }
        .burger.open .burger-line:nth-child(1) { transform: rotate(-45deg) translate(-5px, 6px); }
        .burger.open .burger-line:nth-child(2) { opacity: 0; }
        .burger.open .burger-line:nth-child(3) { transform: rotate(45deg) translate(-5px, -6px); }

        /* Smooth transitions for overlays */
        .fade-in { animation: fadeIn 0.3s ease-out forwards; }
        .fade-out { animation: fadeOut 0.3s ease-in forwards; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }

        /* Custom scrollbar for modal */
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #374151; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #6b7280; border-radius: 10px; }
        
        /* Disabled branch button style */
        .branch-disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #374151; /* bg-gray-700 */
        }
        .branch-disabled:hover {
            border-color: transparent !important;
        }

        /* Draggable overlay style */
        #branch-location-overlay {
            cursor: grab;
        }
        #branch-location-overlay.dragging {
            cursor: grabbing;
        }
    </style>
</head>
<body class="text-white pt-20">

<!-- Initial Data for JS -->
<script>
    window.RALTT_DATA = {
        branches: <?php echo json_encode($branches); ?>,
        userBranch: <?php echo json_encode($user_branch); ?>
    };
</script>

<!-- Header -->
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
        <!-- User Area -->
        <div class="flex items-center gap-4 z-50">
            <button id="search-toggle" class="text-white/80 hover:text-gold transition-colors text-xl w-10 h-10 rounded-full hover:bg-white/10 flex items-center justify-center">
                <i class="fas fa-search"></i>
            </button>
            <div class="hidden lg:block relative group">
                <button class="flex items-center gap-2 p-2 rounded-lg hover:bg-white/10 transition-colors">
                    <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center"><i class="fa fa-user text-gold"></i></div>
                    <i class="fa fa-caret-down text-white/50"></i>
                </button>
                <div class="absolute right-0 top-full mt-2 w-56 bg-gray-800 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 border border-white/10 py-2">
                    <a href="../logged_user/myProfile.php" class="flex items-center gap-3 px-4 py-2.5 text-white/90 hover:bg-gold hover:text-white"><i class="fas fa-user-circle w-5 text-center"></i> My Account</a>
                    <a href="../logged_user/customer_ticket.php" class="flex items-center gap-3 px-4 py-2.5 text-white/90 hover:bg-gold hover:text-white"><i class="fas fa-ticket-alt w-5 text-center"></i> Customer Ticket</a>
                    <div class="h-px bg-white/10 my-2"></div>
                    <a href="../logout.php" class="flex items-center gap-3 px-4 py-2.5 text-white/90 hover:bg-gold hover:text-white"><i class="fas fa-sign-out-alt w-5 text-center"></i> Logout</a>
                </div>
            </div>
            <button id="mobile-menu-toggle" class="lg:hidden burger flex flex-col justify-center items-center w-10 h-10 z-[100]">
                <span class="burger-line w-6 h-0.5 bg-white mb-1.5 rounded-full"></span>
                <span class="burger-line w-6 h-0.5 bg-white mb-1.5 rounded-full"></span>
                <span class="burger-line w-6 h-0.5 bg-white rounded-full"></span>
            </button>
        </div>
    </div>
    <!-- Desktop Search Bar -->
    <div id="desktop-search" class="hidden absolute top-full left-0 w-full bg-gray-900/95 border-t border-white/10 py-4 px-4 shadow-xl">
        <div class="container mx-auto flex items-center gap-4 max-w-2xl bg-gray-800 rounded-full px-5 py-2 border border-gray-600">
            <i class="fa fa-search text-gray-400"></i>
            <input type="text" placeholder="Search for premium tiles and more..." class="flex-1 bg-transparent text-white placeholder-gray-400 outline-none">
            <button id="close-search" class="text-gray-400 hover:text-white">&times;</button>
        </div>
    </div>
</header>

<!-- Mobile Menu -->
<div id="mobile-menu" class="lg:hidden fixed inset-0 z-40 bg-gray-900/95 transform translate-x-full transition-transform duration-300 backdrop-blur-md">
    <div class="flex flex-col h-full pt-24 pb-8 px-6">
        <nav class="flex flex-col space-y-2 flex-1">
            <a href="../logged_user/landing_page.php" class="nav-link"><i class="fas fa-home"></i> Home</a>
            <a href="../logged_user/landing_page.php#premium-tiles" class="nav-link"><i class="fas fa-th-large"></i> Products</a>
            <a href="../logged_user/3dvisualizer.php" class="nav-link"><i class="fas fa-cube"></i> 3D Visualizer</a>
            <a href="user_my_cart.php" class="nav-link"><i class="fas fa-shopping-cart"></i> My Cart</a>
            <div class="pt-6 border-t border-white/10 mt-4">
                <a href="../logged_user/myProfile.php" class="nav-link"><i class="fas fa-user-circle"></i> My Account</a>
                <a href="../logged_user/customer_ticket.php" class="nav-link"><i class="fas fa-ticket-alt"></i> Customer Ticket</a>
                <a href="../logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </nav>
    </div>
    <style>.nav-link { display:flex; align-items:center; gap:1rem; font-weight:600; font-size:1.125rem; padding:1rem; border-radius:0.75rem; transition: all 0.2s; } .nav-link:hover { background-color: rgba(255,255,255,0.1); color: var(--color-gold); } .nav-link i { width: 1.5rem; text-align:center; }</style>
</div>

<!-- Branch Location UI (Draggable) -->
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
    <button id="branch-change-btn" class="text-xs bg-white/10 hover:bg-white/20 text-white font-semibold py-1.5 px-3 rounded-full transition-colors">Change</button>
</div>

<!-- Branch Selection Modal -->
<div id="branch-modal" class="hidden fixed inset-0 z-[100] bg-black/50 flex items-center justify-center p-4">
    <div class="bg-gray-800 text-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-white/10">
        <div class="p-5 text-center border-b border-white/10 relative">
            <h2 class="text-xl font-bold">Select Your Branch</h2>
            <p class="text-sm text-white/60 mt-1">Enable location to choose a branch.</p>
            <button id="close-branch-modal" class="absolute top-3 right-3 w-8 h-8 rounded-full hover:bg-white/10 flex items-center justify-center">&times;</button>
        </div>
        <div id="branch-location-status" class="p-3 text-center text-sm"></div>
        <div id="branch-list" class="p-4 space-y-2 max-h-[60vh] overflow-y-auto custom-scrollbar">
            <!-- Branch items will be injected by JavaScript -->
        </div>
    </div>
</div>

<!-- Page Loading Overlay -->
<div id="page-loading-overlay" class="hidden fixed inset-0 z-[99999] bg-gray-900/80 backdrop-blur-sm flex items-center justify-center">
    <div class="text-center">
        <i class="fas fa-spinner fa-spin text-4xl text-gold mb-4"></i>
        <div class="text-xl font-semibold">Switching Branch...</div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    /**
     * Draggable Element Module
     */
    const DraggableModule = ((elementId) => {
        const el = document.getElementById(elementId);
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
    })('branch-location-overlay');

    /**
     * UI Interaction Module
     */
    const UIModule = (() => {
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const searchToggle = document.getElementById('search-toggle');
        const desktopSearch = document.getElementById('desktop-search');
        const closeSearch = document.getElementById('close-search');

        const toggleMobileMenu = () => {
            mobileMenu.classList.toggle('translate-x-full');
            mobileMenuToggle.classList.toggle('open');
            document.body.style.overflow = mobileMenuToggle.classList.contains('open') ? 'hidden' : '';
        };
        const toggleSearch = () => {
            desktopSearch.classList.toggle('hidden');
            if (!desktopSearch.classList.contains('hidden')) desktopSearch.querySelector('input')?.focus();
        };

        mobileMenuToggle?.addEventListener('click', toggleMobileMenu);
        mobileMenu?.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
             if (mobileMenuToggle.classList.contains('open')) toggleMobileMenu();
        }));
        searchToggle?.addEventListener('click', toggleSearch);
        closeSearch?.addEventListener('click', toggleSearch);
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && !desktopSearch.classList.contains('hidden')) toggleSearch();
        });
    })();

    /**
     * Branch Management Module
     */
    const BranchModule = (() => {
        const { branches, userBranch } = window.RALTT_DATA;
        const changeBtn = document.getElementById('branch-change-btn');
        const modal = document.getElementById('branch-modal');
        const closeModalBtn = document.getElementById('close-branch-modal');
        const branchListContainer = document.getElementById('branch-list');
        const locationStatusEl = document.getElementById('branch-location-status');
        const distanceEl = document.getElementById('branch-distance');
        const loadingOverlay = document.getElementById('page-loading-overlay');
        let geolocationInterval = null;

        const haversineDistance = (lat1, lon1, lat2, lon2) => {
            const R = 6371; // km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon / 2) ** 2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        };
        
        const selectBranch = async (branchId) => {
            modal.classList.add('hidden', 'fade-out');
            loadingOverlay.classList.remove('hidden');
            loadingOverlay.classList.add('fade-in');
            try {
                await fetch('set_branch.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `branch_id=${encodeURIComponent(branchId)}`
                });
                window.location.reload();
            } catch (error) {
                console.error('Error switching branch:', error);
                alert('Failed to switch branch. Please try again.');
                loadingOverlay.classList.add('hidden');
            }
        };

        const renderBranchList = (userCoords = null, enabled = false) => {
            branchListContainer.innerHTML = '';
            const sortedBranches = [...branches].sort((a, b) => {
                if (!userCoords) return 0;
                const distA = haversineDistance(userCoords.latitude, userCoords.longitude, a.lat, a.lng);
                const distB = haversineDistance(userCoords.latitude, userCoords.longitude, b.lat, b.lng);
                return distA - distB;
            });

            sortedBranches.forEach(branch => {
                const isSelected = userBranch && userBranch.id === branch.id;
                const distance = userCoords ? haversineDistance(userCoords.latitude, userCoords.longitude, branch.lat, branch.lng).toFixed(1) + ' km' : null;
                const isClickable = enabled && !isSelected;
                const branchElement = document.createElement('div');
                branchElement.className = `flex items-center justify-between p-4 rounded-lg border-2 transition-all duration-200 ${
                    isSelected ? 'bg-gold border-gold text-white shadow-lg' :
                    isClickable ? 'bg-gray-700 border-transparent hover:border-gold cursor-pointer' :
                    'bg-gray-700 border-transparent branch-disabled'
                }`;
                if (isClickable) branchElement.onclick = () => selectBranch(branch.id);
                branchElement.innerHTML = `
                    <div class="flex items-center gap-3">
                        <i class="fas fa-store ${isSelected ? '' : 'text-gold'}"></i>
                        <span class="font-bold">${branch.name}</span>
                    </div>
                    <div class="text-right">
                        ${distance ? `<span class="text-sm font-semibold">${distance}</span>` : ''}
                        ${isSelected ? `<span class="ml-3 text-xs bg-white/20 px-2 py-1 rounded-full">Current</span>` : ''}
                    </div>
                `;
                branchListContainer.appendChild(branchElement);
            });
        };

        const updateLocationStatus = (message, type = 'info') => {
            const icons = { info: 'fa-spinner fa-spin', success: 'fa-check-circle', error: 'fa-times-circle' };
            const colors = { info: 'bg-blue-900/50 text-blue-300', success: 'bg-green-900/50 text-green-300', error: 'bg-red-900/50 text-red-400' };
            locationStatusEl.className = `p-3 text-center text-sm ${colors[type]}`;
            locationStatusEl.innerHTML = `<i class="fas ${icons[type]} mr-2"></i> ${message}`;
        };

        const openModal = () => {
            modal.classList.remove('hidden', 'fade-out');
            modal.classList.add('fade-in');
            renderBranchList(null, false);
            updateLocationStatus('Requesting location access...', 'info');
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    updateLocationStatus('Location found! Branches are now enabled.', 'success');
                    renderBranchList(pos.coords, true);
                },
                () => {
                    updateLocationStatus('Location is required to change branch. Please enable it in your browser settings.', 'error');
                },
                { timeout: 5000 }
            );
        };
        
        const closeModal = () => {
            modal.classList.add('fade-out');
            setTimeout(() => modal.classList.add('hidden'), 300);
        };

        const checkGeolocation = () => {
             if (!navigator.geolocation || !userBranch) return;
             navigator.geolocation.getCurrentPosition(pos => {
                const dist = haversineDistance(pos.coords.latitude, pos.coords.longitude, userBranch.lat, userBranch.lng);
                distanceEl.textContent = `(${dist.toFixed(1)} km away)`;
             }, () => {
                distanceEl.textContent = '';
             }, { enableHighAccuracy: true });
        };
        
        const startGeolocationWatcher = () => {
            checkGeolocation();
            if(geolocationInterval) clearInterval(geolocationInterval);
            geolocationInterval = setInterval(checkGeolocation, 3000);
        };

        changeBtn?.addEventListener('click', openModal);
        closeModalBtn?.addEventListener('click', closeModal);
        modal?.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
        startGeolocationWatcher();
    })();
});
</script>

<!-- Chatbase Script (Restored) -->
<script>
window.chatbaseConfig = {
    chatbotId: "-vAdaLts54qAK1OtQj9SL",
}
</script>
<script src="https://www.chatbase.co/embed.min.js" id="-vAdaLts54qAK1OtQj9SL" defer></script>

</body>
</html>