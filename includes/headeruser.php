<?php
// Start session and check if user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ../connection/tresspass.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RALTT Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            background: #000 !important;
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

            .raltt-header .mobile-menu.open {
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

<header class="raltt-header">
    <div class="logo-area">
        <img src="../images/logover2.png" alt="Logo" class="logo-img">
    </div>
    <div class="nav-and-user">
        <nav class="nav">
            <a href="/index.php">Home</a>
            <div class="dropdown" tabindex="0">
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
        </nav>
        <div class="user-area">
            <div class="search-icon" id="raltt-search-icon" tabindex="0">
                <i class="fa fa-search"></i>
            </div>
            <form class="search-bar" id="raltt-search-bar">
                <input type="text" placeholder="Search...">
                <button type="submit"><i class="fa fa-search"></i></button>
                <span id="raltt-search-close">&times;</span>
            </form>
            <div class="user-dropdown" tabindex="0">
                <i class="fa fa-user-circle user-icon"></i>
                <i class="fa fa-caret-down"></i>
                <div class="user-dropdown-content">
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
    });
</script>
</body>
</html>