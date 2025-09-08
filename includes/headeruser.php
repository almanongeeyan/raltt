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
    
    <meta name="chatbase-user-id" content="<?php echo htmlspecialchars($chatbaseUserId); ?>">
    <meta name="chatbase-hash" content="<?php echo htmlspecialchars($chatbaseHash); ?>">




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php if (isset($_SESSION['branch_id'])): ?>
    <meta name="user-branch-id" content="<?php echo htmlspecialchars($_SESSION['branch_id']); ?>">
    <?php endif; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RALTT Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            /* Removed forced black background to allow page-specific backgrounds */
            color: #fff;
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-weight: 500;
        }

        .raltt-header {
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            background: rgba(10,10,10,0.85);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            height: 72px;
            box-sizing: border-box;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            transition: background 0.3s;
        }

        .raltt-header .logo-area {
            display: flex;
            align-items: center;
            gap: 18px;
            max-width: 180px;
            flex: 0 0 auto;
            margin-right: 18px;
            z-index: 200;
        }

        .raltt-header .logo-img {
            width: 250px;
            height: 180px;
            object-fit: contain;
            display: block;
        }

        .raltt-header .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .raltt-header .brand-main {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
        }

        .raltt-header .brand-sub {
            color: #b88b4a;
            font-size: 0.95rem;
            font-weight: 400;
            margin-top: -2px;
            letter-spacing: 0.2px;
        }

        .raltt-header .nav-and-user {
            display: flex;
            flex: 1;
            align-items: center;
            justify-content: center;
            position: relative;
            min-width: 0;
            margin-left: auto;
            margin-right: auto;
            max-width: 1200px;
        }
        
        .raltt-header .nav-and-user.search-active .nav,
        .raltt-header .nav-and-user.search-active .user-dropdown {
            visibility: hidden;
            opacity: 0;
            transition: visibility 0s, opacity 0.3s linear;
        }

        .raltt-header .nav-and-user.search-active .search-bar {
            display: flex;
            visibility: visible;
            opacity: 1;
        }


        .raltt-header nav {
            display: flex;
            align-items: center;
            gap: 40px;
            flex: 1 1 0%;
            justify-content: center;
            background: rgba(0, 0, 0, 0.10);
            border-radius: 32px;
            padding: 8px 22px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.10);
            margin: 0 18px;
            transition: opacity 0.3s, visibility 0.3s;
        }
        
        .raltt-header nav a {
            color: #fff;
            font-size: 1.02rem;
            font-weight: 600;
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 4px;
            transition: background 0.18s, color 0.18s;
            position: relative;
        }

        .raltt-header nav a:hover,
        .raltt-header nav .active {
            background: rgba(255, 255, 255, 0.12);
            color: #ff7a22;
        }

        .raltt-header .dropdown {
            position: relative;
        }

        .raltt-header .dropdown-content {
            display: none;
            position: absolute;
            top: 32px;
            left: 0;
            background: rgba(34, 34, 34, 0.98);
            min-width: 160px;
            border-radius: 6px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.18);
            z-index: 10;
            flex-direction: column;
            padding: 8px 0;
        }

        .raltt-header .dropdown:hover .dropdown-content,
        .raltt-header .dropdown:focus-within .dropdown-content {
            display: flex;
        }

        .raltt-header .dropdown-content a {
            color: #fff;
            padding: 8px 20px;
            font-size: 1rem;
            border-radius: 0;
            background: none;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            font-weight: 700;
        }

        .raltt-header .dropdown-content a:hover {
            background: #ff7a22;
            color: #fff;
        }
        
        .raltt-header .user-area {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 0 0 auto;
            margin-left: 12px;
            position: relative;
            transition: opacity 0.3s, visibility 0.3s;
        }
        
        .raltt-header .search-icon {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #fff;
            margin-right: 6px;
            transition: color 0.18s;
            z-index: 200;
        }
        .raltt-header .search-icon:hover {
            color: #ff7a22;
        }

        .raltt-header .search-bar {
            display: none;
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(34,34,34,0.98);
            border-radius: 32px;
            padding: 6px 18px;
            z-index: 300;
            box-shadow: 0 2px 12px rgba(0,0,0,0.18);
            min-width: 220px;
            max-width: 400px;
            width: 100%;
            transition: all 0.3s;
            align-items: center;
            visibility: hidden;
            opacity: 0;
        }

        .raltt-header .search-bar input {
            border: none;
            background: transparent;
            color: #fff;
            font-size: 1.1rem;
            outline: none;
            width: 80%;
            padding: 6px 0;
        }
        .raltt-header .search-bar button {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.2rem;
            cursor: pointer;
        }
        .raltt-header .search-bar #raltt-search-close {
            margin-left: 10px;
            cursor: pointer;
            font-size: 1.3rem;
        }

        /* Mobile search icon and bar */
        .raltt-header .search-icon-mobile {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            color: #fff;
            margin-left: 10px;
            transition: color 0.18s;
        }
        .raltt-header .search-icon-mobile:hover {
            color: #ff7a22;
        }
        .raltt-header .search-bar-mobile {
            display: none;
            position: relative;
            background: rgba(34,34,34,0.98);
            border-radius: 32px;
            padding: 6px 18px;
            margin: 10px 0 0 0;
            z-index: 300;
            box-shadow: 0 2px 12px rgba(0,0,0,0.18);
            min-width: 180px;
            max-width: 95vw;
            width: 90vw;
            transition: all 0.3s;
            align-self: center;
            align-items: center;
        }
        .raltt-header .search-bar-mobile input {
            border: none;
            background: transparent;
            color: #fff;
            font-size: 1.1rem;
            outline: none;
            width: 70vw;
            max-width: 80vw;
            padding: 6px 0;
        }
        .raltt-header .search-bar-mobile button {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.2rem;
            cursor: pointer;
        }
        .raltt-header .search-bar-mobile #raltt-search-close-mobile {
            margin-left: 10px;
            cursor: pointer;
            font-size: 1.3rem;
        }

        .raltt-header .user-dropdown {
            position: relative;
            transition: opacity 0.3s, visibility 0.3s;
        }

        .raltt-header .user-dropdown-content {
            display: none;
            position: absolute;
            top: 38px;
            right: 0;
            background: rgba(34, 34, 34, 0.98);
            min-width: 160px;
            border-radius: 6px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.18);
            z-index: 10;
            flex-direction: column;
            padding: 8px 0;
        }

        .raltt-header .user-dropdown:hover .user-dropdown-content,
        .raltt-header .user-dropdown:focus-within .user-dropdown-content {
            display: flex;
        }
        
        .raltt-header .user-dropdown-content a {
            color: #fff;
            padding: 8px 20px;
            font-size: 1rem;
            border-radius: 0;
            background: none;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            font-weight: 700;
        }

        .raltt-header .user-dropdown-content a:hover {
            background: #ff7a22;
            color: #fff;
        }
        
        .raltt-header .user-icon {
            font-size: 1.4rem;
            color: #fff;
            cursor: pointer;
        }

        /* Mobile specific styles */
        .raltt-header .burger,
        .raltt-header .mobile-menu {
            display: none;
        }

        .raltt-header .mobile-menu.open {
            display: flex;
        }

        @media (max-width: 900px) {
            .raltt-header {
                padding: 0 4vw;
                height: 56px;
                justify-content: space-between;
                align-items: center;
            }

            .raltt-header nav,
            .raltt-header .user-area {
                display: none;
            }

            .raltt-header .logo-area {
                max-width: 120px;
                margin-right: 0;
            }

            .raltt-header .logo-img {
                width: 70px;
                height: 32px;
            }

            .raltt-header .burger {
                display: flex;
                flex-direction: column;
                justify-content: center;
                width: 32px;
                height: 32px;
                cursor: pointer;
                z-index: 120;
                position: relative;
            }

            .raltt-header .burger span {
                height: 3px;
                width: 100%;
                background: #fff;
                margin: 4px 0;
                border-radius: 2px;
                transition: 0.3s;
                display: block;
            }

            /* Burger animation */
            .raltt-header .burger.open span:nth-child(1) {
                transform: rotate(-45deg) translate(-5px, 6px);
            }

            .raltt-header .burger.open span:nth-child(2) {
                opacity: 0;
            }

            .raltt-header .burger.open span:nth-child(3) {
                transform: rotate(45deg) translate(-5px, -6px);
            }

            .raltt-header .mobile-menu {
                display: none;
                flex-direction: column;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(34, 34, 34, 0.98);
                z-index: 200;
                padding-top: 56px;
                align-items: center;
                gap: 16px;
                transition: 0.3s;
                overflow-y: auto;
            }

            .raltt-header .mobile-menu open {
                display: flex;
            }

            .raltt-header .mobile-menu a {
                color: #fff;
                font-size: 1.01rem;
                padding: 10px 0;
                text-decoration: none;
                width: 100%;
                text-align: center;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
                box-sizing: border-box;
                display: flex;
                align-items: center;
                justify-content: center;
                letter-spacing: 0.02em;
                font-weight: 600;
            }

            .raltt-header .mobile-menu .dropdown-content {
                display: none;
                position: static;
                background: none;
                box-shadow: none;
                min-width: 0;
                border-radius: 0;
                padding: 0;
                width: 100%;
                text-align: center;
            }

            .raltt-header .mobile-menu .dropdown-content a {
                padding-left: 0;
                font-size: 0.92rem;
                justify-content: center;
                padding-top: 6px;
                padding-bottom: 6px;
                gap: 8px;
                font-weight: 600;
            }

            .raltt-header .mobile-menu .dropdown-content a i {
                font-size: 1rem;
            }

            .raltt-header .user-dropdown-mobile {
                display: flex;
                flex-direction: column;
                width: 100%;
                text-align: center;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
                align-items: center;
            }

            .raltt-header .user-dropdown-mobile a {
                padding: 10px 0;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                width: 100%;
                text-align: center;
                font-weight: 600;
            }

            /* Show search icon in mobile menu header */
            .raltt-header .search-icon-mobile {
                display: flex;
            }
            .raltt-header .search-bar-mobile {
                display: none;
            }
        }
        
        @media (max-width: 600px) {
            .raltt-header {
                height: 44px;
            }

            .raltt-header .logo-img {
                width: 70px;
                height: 50px;
            }

            .raltt-header .brand-main {
                font-size: 1rem;
            }

            .raltt-header .brand-sub {
                font-size: 0.78rem;
            }

            .raltt-header .mobile-menu {
                padding-top: 44px;
            }
            
            .raltt-header .mobile-menu a,
            .raltt-header .mobile-menu .dropdown-content a {
                font-size: 0.95rem;
                padding: 8px 0;
            }
        }
    </style>
</head>
<body>
<script>
// Check session every 1 second
setInterval(function() {
    fetch('../connection/check_session.php', { method: 'POST' })
        .then(response => response.text())
        .then(data => {
            // If check_session.php returns anything except 'OK', redirect
            if (data.trim() !== 'OK') {
                window.location.href = '../connection/tresspass.php';
            }
        })
        .catch(() => {
            // On error, redirect as well
            window.location.href = '../connection/tresspass.php';
        });
}, 1000);
</script>

<?php
$branches = [
    [ 'id' => 1, 'name' => 'Deparo',   'lat' => 14.752338, 'lng' => 121.017677 ],
    [ 'id' => 2, 'name' => 'Vanguard', 'lat' => 14.759202, 'lng' => 121.062861 ],
    [ 'id' => 3, 'name' => 'Brixton',  'lat' => 14.583121, 'lng' => 120.979313 ],
    [ 'id' => 4, 'name' => 'Samaria',  'lat' => 14.757048, 'lng' => 121.033621 ],
    [ 'id' => 5, 'name' => 'Kiko',     'lat' => 14.607425, 'lng' => 121.011685 ],
];
$user_branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
$user_branch = null;
foreach ($branches as $b) {
    if ($b['id'] === $user_branch_id) { $user_branch = $b; break; }
}
echo '<script>window.BRANCHES = ' . json_encode($branches) . '; window.USER_BRANCH = ' . json_encode($user_branch) . ';</script>';
$branchOverlay = '<div id="branch-location-overlay" style="position:fixed;top:90px;left:24px;z-index:9999;background:rgba(30,30,30,0.90);color:#fff;padding:12px 22px 12px 16px;border-radius:16px;font-size:15px;box-shadow:0 2px 12px 0 rgba(0,0,0,0.13);pointer-events:auto;max-width:290px;line-height:1.5;font-family:Inter,sans-serif;backdrop-filter:blur(2px);border:1.5px solid #cf8756;opacity:0.97;display:block;cursor:move;user-select:none;">';
$branchOverlay .= '<div style="display:flex;align-items:center;gap:10px;margin-bottom:2px;">';
$branchOverlay .= '<span style="display:inline-block;width:8px;height:8px;background:#cf8756;border-radius:50%;margin-right:10px;"></span>';
$branchOverlay .= '<span style="font-weight:500;opacity:0.85;">You\'re currently browsing at</span>';
$branchOverlay .= '</div>';
$branchOverlay .= '<div style="display:flex;align-items:center;gap:6px;">';
$branchOverlay .= '<span id="branch-current" style="font-weight:700;">';
if ($user_branch) {
    $branchOverlay .= htmlspecialchars($user_branch['name']) . ' Branch';
} else {
    $branchOverlay .= '<i>Locating branch...</i>';
}
$branchOverlay .= '</span>';
$branchOverlay .= '<span id="branch-distance" style="font-size:12px;color:#cf8756;margin-left:4px;opacity:0.85;"></span>';
$branchOverlay .= '<a id="branch-change-link" href="#" style="font-size:11px;color:#e8a56a;margin-left:8px;text-decoration:underline;cursor:pointer;opacity:0.85;pointer-events:auto;">Change</a>';
$branchOverlay .= '</div>';
$branchOverlay .= '</div>';
echo $branchOverlay;
// Loading overlay for branch change
echo '<div id="branch-loading-overlay" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:100000;background:rgba(30,30,30,0.85);color:#fff;align-items:center;justify-content:center;font-family:Inter,sans-serif;"><div style="margin:auto;text-align:center;"><div class="fa fa-spinner fa-spin" style="font-size:2.5rem;margin-bottom:18px;"></div><div style="font-size:1.2rem;font-weight:600;">Switching branch...</div></div></div>';
// ...existing code...
?><header class="raltt-header">
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
        // Get current position
        const rect = overlay.getBoundingClientRect();
        origX = rect.left;
        origY = rect.top;
        overlay.style.transition = 'none';
        document.body.style.userSelect = 'none';
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
            document.body.style.userSelect = '';
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
    });
    document.addEventListener('touchmove', function(e) {
        if (!isDragging || e.touches.length !== 1) return;
        const dx = e.touches[0].clientX - startX;
        const dy = e.touches[0].clientY - startY;
        overlay.style.left = (origX + dx) + 'px';
        overlay.style.top = (origY + dy) + 'px';
    }, {passive:false});
    document.addEventListener('touchend', function() {
        if (isDragging) {
            isDragging = false;
            overlay.style.transition = '';
        }
    });
});
</script>
<script>
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
document.addEventListener('DOMContentLoaded', function() {
    if (window.USER_BRANCH && window.USER_BRANCH.id && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            const userLat = pos.coords.latitude, userLng = pos.coords.longitude;
            const branch = window.BRANCHES.find(b=>b.id===window.USER_BRANCH.id);
            if (branch) {
                const dist = haversine(userLat, userLng, branch.lat, branch.lng);
                const distEl = document.getElementById('branch-distance');
                if (distEl) distEl.innerHTML = '(' + dist.toFixed(2) + ' km)';
            }
        });
    }
    const changeLink = document.getElementById('branch-change-link');
    if (changeLink) {
        changeLink.onclick = function(e) {
            e.preventDefault();
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(pos) {
                    openBranchChangeModal(pos.coords.latitude, pos.coords.longitude);
                }, function() {
                    openBranchChangeModal();
                }, {timeout:5000});
            } else {
                openBranchChangeModal();
            }
        };
    }
});
function openBranchChangeModal(userLat, userLng) {
    if (!window.BRANCHES) return;
    let modal = document.getElementById('branch-change-modal');
    if (modal) modal.remove();
    modal = document.createElement('div');
    modal.id = 'branch-change-modal';
    modal.style = 'position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);z-index:99999;display:flex;align-items:center;justify-content:center;';
    let html = `<div style="background:#fff;color:#222;padding:0;border-radius:22px;min-width:290px;max-width:95vw;box-shadow:0 8px 40px 0 rgba(0,0,0,0.18);font-family:Inter,sans-serif;position:relative;overflow:hidden;">
        <div style="background:linear-gradient(90deg,#7d310a 0%,#cf8756 100%);padding:22px 28px 16px 28px;text-align:center;">
            <button id='close-branch-modal' style='position:absolute;top:18px;right:22px;font-size:22px;background:none;border:none;color:#fff;cursor:pointer;transition:color .2s;' onmouseover="this.style.color='#f9f5f2'" onmouseout="this.style.color='#fff'">&times;</button>
            <div style="font-size:22px;font-weight:900;letter-spacing:0.5px;color:#fff;">Select Branch</div>
            <div style="margin-top:6px;font-size:13px;opacity:0.92;color:#fff;">Choose a branch to display. Distance is shown if location is available.</div>
        </div>
        <div style='padding:22px 28px 18px 28px;display:flex;flex-direction:column;gap:10px;'>`;
    window.BRANCHES.forEach(b => {
            let dist = '';
            if (typeof userLat === 'number' && typeof userLng === 'number') {
                dist = ' (' + haversine(userLat, userLng, b.lat, b.lng).toFixed(2) + ' km)';
            }
            const isSelected = window.USER_BRANCH && window.USER_BRANCH.id === b.id;
            if (isSelected) {
                html += `<div style="display:flex;align-items:center;justify-content:space-between;width:100%;padding:13px 16px;font-size:16px;font-weight:600;border:none;outline:none;cursor:default;border-radius:12px;box-shadow:0 2px 12px 0 rgba(207,135,86,0.10);background:linear-gradient(90deg,#7d310a 0%,#cf8756 100%);color:#fff;gap:10px;opacity:1;position:relative;">
                    <span><i class="fa fa-map-marker-alt" style="margin-right:7px;color:#fff;"></i>${b.name} Branch</span>
                    <span style='font-size:13px;color:#fff;background:rgba(207,135,86,0.95);padding:2px 10px 2px 10px;border-radius:8px;${dist?'':'opacity:0.5;'}'>${dist}</span>
                    <span style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#fff;font-size:18px;"><i class='fa fa-check-circle'></i></span>
                </div>`;
            } else {
                html += `<button data-branch="${b.id}" style="display:flex;align-items:center;justify-content:space-between;width:100%;padding:13px 16px;font-size:16px;font-weight:600;border:none;outline:none;cursor:pointer;border-radius:12px;transition:background .18s,color .18s,box-shadow .18s;margin:0;background:#f9f5f2;color:#7d310a;gap:10px;" onmouseover="this.style.background='linear-gradient(90deg,#cf8756 0%,#e8a56a 100%)';this.style.color='#fff'" onmouseout="this.style.background='#f9f5f2';this.style.color='#7d310a'">
                    <span><i class="fa fa-map-marker-alt" style="margin-right:7px;color:#cf8756;"></i>${b.name} Branch</span>
                    <span style='font-size:13px;color:#fff;background:rgba(207,135,86,0.95);padding:2px 10px 2px 10px;border-radius:8px;${dist?'':'opacity:0.5;'}'>${dist}</span>
                </button>`;
            }
    });
    html += `</div>
    </div>`;
    modal.innerHTML = html;
    document.body.appendChild(modal);
    modal.querySelectorAll('button[data-branch]').forEach(btn => {
        btn.onclick = function() {
            const branchId = this.getAttribute('data-branch');
                // Show loading overlay
                var loadingOverlay = document.getElementById('branch-loading-overlay');
                if (loadingOverlay) loadingOverlay.style.display = 'flex';
            fetch('set_branch.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'branch_id=' + encodeURIComponent(branchId)
            }).then(() => {
                window.USER_BRANCH = window.BRANCHES.find(b=>b.id==branchId);
                document.body.removeChild(modal);
                    // Hide loading overlay after reload
                location.reload();
            });
        };
    });
    document.getElementById('close-branch-modal').onclick = function() {
        document.body.removeChild(modal);
    };
}
</script>
    <div class="logo-area">
        <img src="../images/logover2.png" alt="Logo" class="logo-img">
    </div>
    <div class="nav-and-user">
        <nav class="nav">
            <a href="../logged_user/landing_page.php">Home</a>
            <div class="dropdown" tabindex="0">
                <a href="#" id="productsDropdownToggle">Products <i class="fa fa-caret-down"></i></a>
                <div class="dropdown-content" id="productsDropdownContent">
                    <a href="#" id="floorTilesLink"><i class="fa fa-th-large"></i> Floor Tiles</a>
                    <a href="#"><i class="fa fa-door-open"></i> PVC Doors</a>
                    <a href="#"><i class="fa fa-tint"></i> Sinks</a>
                    <a href="#"><i class="fa fa-grip-horizontal"></i> Tile Vinyl</a>
                    <a href="#"><i class="fa fa-circle-o"></i> Bowls</a>
                </div>
            </div>
            <a href="../logged_user/3dvisualizer.php">3D Visualizer</a>
            <a href="user_my_cart.php">My Cart</a>
        </nav>
        <div class="user-area">
            <div class="user-dropdown" tabindex="0" style="position:relative;">
                <button id="accountIconBtn" style="background:none;border:none;outline:none;cursor:pointer;padding:0;display:flex;align-items:center;gap:8px;">
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:50%;background:linear-gradient(120deg,#cf8756 60%,#e8a56a 100%);box-shadow:0 2px 8px #cf875633;"><i class="fa fa-user" style="font-size:1.45rem;color:#fff;"></i></span>
                    <span style="font-weight:600;color:#fff;">Account</span>
                    <i class="fa fa-caret-down" style="color:#fff;font-size:1.1rem;"></i>
                </button>
                <div class="user-dropdown-content" id="accountDropdownContent" style="right:0;top:44px;min-width:170px;">
                    <a href="#"><i class="fa fa-user"></i> Account</a>
                    <a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>
    <div class="burger" id="raltt-burger" aria-label="Open menu" tabindex="0">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div class="mobile-menu" id="raltt-mobile-menu">
        <div style="display:flex;align-items:center;justify-content:space-between;width:100%;padding:0 12px 0 0;">
            <button id="raltt-back-btn" style="background:none;border:none;color:#fff;font-size:1.2rem;font-weight:700;display:flex;align-items:center;gap:8px;padding:12px 0 12px 12px;width:auto;text-align:left;cursor:pointer;"><i class="fa fa-arrow-left"></i> Back</button>
            <div class="search-icon-mobile" id="raltt-search-icon-mobile" tabindex="0" style="cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1.7rem;color:#fff;margin-left:10px;">
                <i class="fa fa-search"></i>
            </div>
        </div>
        <form class="search-bar-mobile" id="raltt-search-bar-mobile">
            <input type="text" placeholder="Search...">
            <button type="submit"><i class="fa fa-search"></i></button>
            <span id="raltt-search-close-mobile">&times;</span>
        </form>
        <a href="/index.php">Home</a>
        <div class="dropdown" tabindex="0" style="width:100%;">
            <a href="#">Products <i class="fa fa-caret-down"></i></a>
            <div class="dropdown-content">
                <a href="#"><i class="fa fa-th-large"></i> Floor Tiles</a>
                <a href="#"><i class="fa fa-door-open"></i> PVC Doors</a>
                <a href="#"><i class="fa fa-tint"></i> Sinks</a>
                <a href="#"><i class="fa fa-grip-horizontal"></i> Tile Vinyl</a>
                <a href="#"><i class="fa fa-circle-o"></i> Bowls</a>
            </div>
        </div>
        <a href="/feature-2d-visualizer.php">2D Visualizer</a>
        <a href="#">My Favourite</a>
        <a href="#">My Cart</a>
        <div class="user-dropdown-mobile" tabindex="0">
            <a href="#">Account <i class="fa fa-caret-down"></i></a>
            <div class="dropdown-content">
                <a href="#"><i class="fa fa-user"></i> My Account</a>
                <a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a>
            </div>
        </div>
    </div>
</header>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const burger = document.getElementById('raltt-burger');
        const mobileMenu = document.getElementById('raltt-mobile-menu');
        const backBtn = document.getElementById('raltt-back-btn');
        const body = document.body;
        
        const navAndUser = document.querySelector('.nav-and-user');
        const searchIcon = document.getElementById('raltt-search-icon');
        const searchBar = document.getElementById('raltt-search-bar');
        const searchClose = document.getElementById('raltt-search-close');
        
        const searchIconMobile = document.getElementById('raltt-search-icon-mobile');
        const searchBarMobile = document.getElementById('raltt-search-bar-mobile');
        const searchCloseMobile = document.getElementById('raltt-search-close-mobile');

        // Function to toggle the desktop search bar
        function toggleDesktopSearch(show) {
            if (show) {
                navAndUser.classList.add('search-active');
            } else {
                navAndUser.classList.remove('search-active');
            }
        }

        // Desktop search icon click handler
        if (searchIcon && searchBar && searchClose) {
            searchIcon.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleDesktopSearch(true);
                searchBar.querySelector('input').focus();
            });
            searchClose.addEventListener('click', () => {
                toggleDesktopSearch(false);
            });
            document.addEventListener('click', (e) => {
                if (!searchBar.contains(e.target) && !searchIcon.contains(e.target)) {
                    toggleDesktopSearch(false);
                }
            });
        }

        // Mobile menu functionality
        if (backBtn) {
            backBtn.addEventListener('click', () => {
                mobileMenu.classList.remove('open');
                burger.classList.remove('open');
                body.style.overflow = '';
            });
        }

        const mobileDropdowns = document.querySelectorAll('.mobile-menu .dropdown');
        const mobileUserDropdown = document.querySelector('.mobile-menu .user-dropdown-mobile');

        function toggleMobileMenu() {
            const isOpen = mobileMenu.classList.toggle('open');
            burger.classList.toggle('open', isOpen);
            body.style.overflow = isOpen ? 'hidden' : '';
        }

        burger.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMobileMenu();
        });

        mobileDropdowns.forEach(dropdown => {
            const dropdownToggle = dropdown.querySelector('a');
            const dropdownContent = dropdown.querySelector('.dropdown-content');

            dropdownToggle.addEventListener('click', (e) => {
                e.preventDefault();
                dropdownContent.style.display = dropdownContent.style.display === 'flex' ? 'none' : 'flex';
            });
        });
        
        if (mobileUserDropdown) {
            const userDropdownToggle = mobileUserDropdown.querySelector('a');
            const userDropdownContent = mobileUserDropdown.querySelector('.dropdown-content');

            userDropdownToggle.addEventListener('click', (e) => {
                e.preventDefault();
                userDropdownContent.style.display = userDropdownContent.style.display === 'flex' ? 'none' : 'flex';
            });
        }

        mobileMenu.querySelectorAll('a').forEach(link => {
            const parentDropdown = link.closest('.dropdown, .user-dropdown-mobile');
            const isDropdownToggle = parentDropdown && parentDropdown.querySelector('a') === link;

            if (!isDropdownToggle) {
                link.addEventListener('click', () => {
                    mobileMenu.classList.remove('open');
                    burger.classList.remove('open');
                    body.style.overflow = '';
                });
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 900) {
                mobileMenu.classList.remove('open');
                burger.classList.remove('open');
                body.style.overflow = '';
                if (searchBarMobile) searchBarMobile.style.display = 'none';
                toggleDesktopSearch(false); // Hide search on resize
            }
        });

        // Mobile search icon handlers
        if (searchIconMobile && searchBarMobile && searchCloseMobile) {
            searchIconMobile.addEventListener('click', (e) => {
                e.stopPropagation();
                searchBarMobile.style.display = 'flex';
                searchBarMobile.querySelector('input').focus();
            });
            searchCloseMobile.addEventListener('click', () => {
                searchBarMobile.style.display = 'none';
            });
            document.addEventListener('click', (e) => {
                if (!searchBarMobile.contains(e.target) && !searchIconMobile.contains(e.target)) {
                    searchBarMobile.style.display = 'none';
                }
            });
        }
        // --- Custom dropdown expand/collapse for Products (desktop) ---
        const productsDropdownToggle = document.getElementById('productsDropdownToggle');
        const productsDropdownContent = document.getElementById('productsDropdownContent');
        if (productsDropdownToggle && productsDropdownContent) {
            productsDropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                // Toggle dropdown
                if (productsDropdownContent.style.display === 'flex' || productsDropdownContent.style.display === 'block') {
                    productsDropdownContent.style.display = 'none';
                } else {
                    productsDropdownContent.style.display = 'flex';
                }
            });
            // Prevent dropdown links from navigating
            productsDropdownContent.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                });
            });
        }
        // --- Floor Tiles link scroll to Premium Tiles section ---
        const floorTilesLink = document.getElementById('floorTilesLink');
        if (floorTilesLink) {
            floorTilesLink.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = '../logged_user/landing_page.php#premium-tiles';
            });
        }
        // --- Account icon dropdown expand/collapse ---
        const accountIconBtn = document.getElementById('accountIconBtn');
        const accountDropdownContent = document.getElementById('accountDropdownContent');
        if (accountIconBtn && accountDropdownContent) {
            accountDropdownContent.style.display = 'none';
            accountIconBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                accountDropdownContent.style.display = accountDropdownContent.style.display === 'flex' ? 'none' : 'flex';
            });
            document.addEventListener('click', function(e) {
                if (!accountIconBtn.contains(e.target) && !accountDropdownContent.contains(e.target)) {
                    accountDropdownContent.style.display = 'none';
                }
            });
        }
    }); // This was the missing closing brace and parenthesis

// --- Geolocation enforcement (polling every second) ---
function showGeolocationRequired() {
    document.body.innerHTML = '';
    document.body.style.background = '#1a1a1a';
    var div = document.createElement('div');
    div.id = 'geo-required-message';
    div.style = 'display:flex;flex-direction:column;align-items:center;justify-content:center;height:100vh;color:#fff;font-family:Inter,sans-serif;text-align:center;';
    div.innerHTML = '<h1 style="font-size:2.2rem;margin-bottom:18px;">Geolocation Required</h1>' +
        '<p style="font-size:1.1rem;max-width:400px;">You must allow location access to use this website. Please enable geolocation in your browser settings and reload the page.</p>';
    document.body.appendChild(div);
}
function showGeolocationNotSupported() {
    document.body.innerHTML = '';
    document.body.style.background = '#1a1a1a';
    var div = document.createElement('div');
    div.style = 'display:flex;flex-direction:column;align-items:center;justify-content:center;height:100vh;color:#fff;font-family:Inter,sans-serif;text-align:center;';
    div.innerHTML = '<h1 style="font-size:2.2rem;margin-bottom:18px;">Geolocation Not Supported</h1>' +
        '<p style="font-size:1.1rem;max-width:400px;">Your browser does not support geolocation. Please use a compatible browser.</p>';
    document.body.appendChild(div);
}
document.addEventListener('DOMContentLoaded', function() {
    if (!navigator.geolocation) {
        showGeolocationNotSupported();
        return;
    }
    let geoOk = false;
    let wasDenied = false;
    let geoInterval = null;

    function checkGeo() {
        navigator.geolocation.getCurrentPosition(function(pos) {
            if (!geoOk && wasDenied) {
                // Only reload if permission changed from denied to allowed
                location.reload();
            }
            geoOk = true;
            wasDenied = false;
        }, function(error) {
            if (geoOk || error.code === error.PERMISSION_DENIED || error.code === error.POSITION_UNAVAILABLE) {
                showGeolocationRequired();
                wasDenied = true;
            }
            geoOk = false;
        }, {timeout: 5000});
    }

    function startGeoCheck() {
        if (!geoInterval) {
            checkGeo();
            geoInterval = setInterval(checkGeo, 1000);
        }
    }
    function stopGeoCheck() {
        if (geoInterval) {
            clearInterval(geoInterval);
            geoInterval = null;
        }
    }

    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            startGeoCheck();
        } else {
            stopGeoCheck();
        }
    });
    // Start checking only when tab is visible
    if (document.visibilityState === 'visible') {
        startGeoCheck();
    }
});

</script>
<script>
(function(){if(!window.chatbase||window.chatbase("getState")!=="initialized"){window.chatbase=(...arguments)=>{if(!window.chatbase.q){window.chatbase.q=[]}window.chatbase.q.push(arguments)};window.chatbase=new Proxy(window.chatbase,{get(target,prop){if(prop==="q"){return target.q}return(...args)=>target(prop,...args)}})}const onLoad=function(){const script=document.createElement("script");script.src="https://www.chatbase.co/embed.min.js";script.id="-vAdaLts54qAK1OtQj9SL";script.domain="www.chatbase.co";document.body.appendChild(script)};if(document.readyState==="complete"){onLoad()}else{window.addEventListener("load",onLoad)}})();
</script>
</body>
</html>