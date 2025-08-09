<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            background: #000 !important;
            color: #fff;
            margin: 0;
            font-family: Arial, sans-serif;
            font-weight: bold;
        }

        .raltt-header {
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 100;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 48px 0 48px;
            height: 100px;
            box-sizing: border-box;
            gap: 0;
        }

        .raltt-header .logo-area {
            display: flex;
            align-items: center;
            gap: 32px;
            max-width: 320px;
            flex: 0 0 auto;
            margin-right: 32px;
        }

        .raltt-header .logo-img {
            width: 280px;
            height: 230px;
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
            font-size: 1.25rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
        }

        .raltt-header .brand-sub {
            color: #b88b4a;
            font-size: 1rem;
            font-weight: 400;
            margin-top: -2px;
            letter-spacing: 0.2px;
        }

        .raltt-header nav {
            display: flex;
            align-items: center;
            gap: 40px;
            flex: 1 1 0%;
            justify-content: center;
            background: rgba(0, 0, 0, 0.10);
            border-radius: 32px;
            padding: 10px 36px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.10);
            margin: 0 32px;
        }

        .raltt-header nav a {
            color: #fff;
            font-size: 1.08rem;
            font-weight: 700;
            text-decoration: none;
            padding: 6px 12px;
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
            gap: 10px;
            flex: 0 0 auto;
            margin-left: 32px;
        }

        .raltt-header .user-dropdown {
            position: relative;
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

        /* Mobile specific styles */
        .raltt-header .burger,
        .raltt-header .mobile-menu {
            display: none;
        }

        @media (max-width: 900px) {
            .raltt-header {
                padding: 0 8px;
                height: 70px;
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
                width: 100px;
                height: 80px;
            }

            .raltt-header .burger {
                display: flex;
                flex-direction: column;
                justify-content: center;
                width: 36px;
                height: 36px;
                cursor: pointer;
                z-index: 120;
                position: relative;
            }

            .raltt-header .burger span {
                height: 4px;
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
                padding-top: 70px;
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
                font-size: 1.08rem;
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
                font-weight: 700;
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
                font-weight: 700;
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
                font-weight: 700;
            }
        }
        
        @media (max-width: 600px) {
            .raltt-header {
                height: 56px;
            }

            .raltt-header .logo-img {
                width: 70px;
                height: 50px;
            }

            .raltt-header .brand-main {
                font-size: 1rem;
            }

            .raltt-header .brand-sub {
                font-size: 0.85rem;
            }

            .raltt-header .mobile-menu {
                padding-top: 56px;
            }
            
            .raltt-header .mobile-menu a,
            .raltt-header .mobile-menu .dropdown-content a {
                font-size: 0.98rem;
                padding: 9px 0;
            }
        }
    </style>
</head>
<body>

<header class="raltt-header">
    <div class="logo-area">
        <img src="../images/logover2.png" alt="Logo" class="logo-img">
    </div>
    <div class="burger" id="raltt-burger" aria-label="Open menu" tabindex="0">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <nav>
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
        <div class="user-dropdown" tabindex="0">
            <i class="fa fa-user-circle user-icon"></i>
            <i class="fa fa-caret-down" style="color:#fff;font-size:1.1rem;"></i>
            <div class="user-dropdown-content">
                <a href="#"><i class="fa fa-user"></i> Account</a>
                <a href="#"><i class="fa fa-sign-out"></i> Logout</a>
            </div>
        </div>
    </div>
    <div class="mobile-menu" id="raltt-mobile-menu">
        <button id="raltt-back-btn" style="background:none;border:none;color:#fff;font-size:1.2rem;font-weight:700;display:flex;align-items:center;gap:8px;padding:12px 0 12px 12px;width:100%;text-align:left;cursor:pointer;"><i class="fa fa-arrow-left"></i> Back</button>
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
                <a href="#"><i class="fa fa-sign-out"></i> Logout</a>
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
            }
        });
    });
</script>

</body>
</html>