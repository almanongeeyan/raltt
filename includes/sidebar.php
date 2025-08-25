<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Branch name mapping
$branch_names = [
    1 => 'Deparo',
    2 => 'Vangaurd',
    3 => 'Brixton',
    4 => 'Samaria',
    5 => 'Kiko'
];

// Get branch_id from session (set this during staff login)
$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
$sidebar_logo = 'STAFF';
if ($branch_id && isset($branch_names[$branch_id])) {
    $sidebar_logo = $branch_names[$branch_id];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Admin Page</title>
    <style>
        :root {
            /* Updated color scheme */
            --sidebar-bg: #2B3241;
            --sidebar-text: #bdc3c7;
            --sidebar-accent: #D5591A;
            --sidebar-hover: #D5591A;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --transition-speed: 0.3s;
            --border-radius: 4px;
            --submenu-indent: 15px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f4f4;
            transition: margin-left var(--transition-speed) ease;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            position: fixed;
            transition: width var(--transition-speed) ease;
            overflow: hidden;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
        }

        .sidebar-content {
            padding: 20px 15px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        .sidebar-header {
            text-align: center;
            padding-bottom: 20px;
            margin-bottom: 20px;
            transition: all var(--transition-speed) ease;
        }
        
        .sidebar-logo {
            font-size: 2.2rem;
            font-weight: 800;
            color: white;
            letter-spacing: 1px;
            margin-bottom: 10px;
            transition: all var(--transition-speed) ease;
        }

        .user-info {
            overflow: hidden;
            transition: all var(--transition-speed) ease;
        }

        .user-name {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
            white-space: nowrap;
            color: white;
            transition: all var(--transition-speed) ease;
        }

        .user-role {
            font-size: 0.8rem;
            color: var(--sidebar-accent);
            margin: 3px 0 0;
            white-space: nowrap;
            font-weight: 600;
            transition: all var(--transition-speed) ease;
        }

        .dashboard-title {
            font-size: 1.1rem;
            font-weight: 700;
            padding: 10px 0;
            color: white;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all var(--transition-speed) ease;
        }

        .sidebar-nav {
            flex-grow: 1;
            list-style: none;
            padding: 0;
            margin-top: 15px;
        }

        .sidebar-nav-item {
            margin-bottom: 8px;
            position: relative;
        }

        .sidebar-nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all var(--transition-speed) ease;
            border-radius: var(--border-radius);
            white-space: nowrap;
            cursor: pointer;
        }
        
        .sidebar-nav-link:hover {
            background-color: var(--sidebar-hover);
            color: white;
        }

        .sidebar-nav-link.active {
            background-color: var(--sidebar-accent);
            color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .sidebar-nav-link svg {
            min-width: 20px;
            width: 20px;
            height: 20px;
            margin-right: 12px;
            fill: currentColor;
            transition: margin var(--transition-speed) ease;
        }

        .sidebar-nav-link span {
            transition: opacity var(--transition-speed) ease;
        }

        /* Dropdown menu styles */
        .dropdown-menu {
            list-style: none;
            padding-left: var(--submenu-indent);
            max-height: 0;
            overflow: hidden;
            transition: max-height var(--transition-speed) ease;
        }

        .dropdown-menu.show {
            max-height: 500px; /* Adjust based on content */
        }

        .dropdown-item {
            margin-bottom: 5px;
        }
        
        .dropdown-menu-parent:hover {
            color: white;
        }

        .dropdown-link {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all var(--transition-speed) ease;
            border-radius: var(--border-radius);
            white-space: nowrap;
            background-color: rgba(0, 0, 0, 0.1);
        }

        .dropdown-link:hover {
            background-color: var(--sidebar-hover);
            color: white;
        }

        .dropdown-link.active {
            background-color: var(--sidebar-accent);
            color: white;
        }

        .dropdown-link svg {
            min-width: 18px;
            width: 18px;
            height: 18px;
            margin-right: 10px;
            fill: currentColor;
        }

        .dropdown-arrow {
            margin-left: auto;
            transition: transform var(--transition-speed) ease;
        }

        .dropdown-menu-parent.open .dropdown-arrow {
            transform: rotate(90deg);
        }

        .logout-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all var(--transition-speed) ease;
            border-radius: var(--border-radius);
            white-space: nowrap;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background-color: transparent;
            margin-top: 20px;
        }

        .logout-link:hover {
            background-color: var(--sidebar-hover);
            border-color: var(--sidebar-hover);
            color: white;
        }

        .logout-link svg {
            min-width: 20px;
            width: 20px;
            height: 20px;
            margin-right: 12px;
            fill: currentColor;
            transition: margin var(--transition-speed) ease;
        }

        .logout-link span {
            transition: opacity var(--transition-speed) ease;
        }

        .menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: var(--sidebar-accent);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
            font-size: 1rem;
            transition: all var(--transition-speed) ease;
        }

        .menu-toggle:hover {
            background: #b74716;
        }

        /* Responsive styles */
        @media (max-width: 992px) {
            .sidebar {
                width: var(--sidebar-collapsed-width);
            }

            .sidebar-header, .dashboard-title, .user-info, 
            .sidebar-nav-link span, .logout-link span,
            .dropdown-menu, .dropdown-link span {
                opacity: 0;
                visibility: hidden;
                height: 0;
                margin: 0;
                padding: 0;
            }

            .sidebar-logo {
                font-size: 1.5rem;
                margin-bottom: 0;
            }

            .sidebar-nav-link {
                justify-content: center;
                padding: 12px 0;
            }

            .sidebar-nav-link svg {
                margin-right: 0;
            }

            .logout-link {
                justify-content: center;
                padding: 12px 0;
            }

            .logout-link svg {
                margin-right: 0;
            }

            .menu-toggle {
                display: block;
            }

            body {
                margin-left: var(--sidebar-collapsed-width);
            }

            .sidebar.expanded {
                width: var(--sidebar-width);
            }

            .sidebar.expanded .sidebar-header, 
            .sidebar.expanded .dashboard-title, 
            .sidebar.expanded .user-info, 
            .sidebar.expanded .sidebar-nav-link span, 
            .sidebar.expanded .logout-link span,
            .sidebar.expanded .dropdown-menu,
            .sidebar.expanded .dropdown-link span {
                opacity: 1;
                visibility: visible;
                height: auto;
                margin: initial;
                padding: initial;
            }

            .sidebar.expanded .sidebar-nav-link {
                justify-content: flex-start;
                padding: 12px 15px;
            }

            .sidebar.expanded .sidebar-nav-link svg {
                margin-right: 12px;
            }

            .sidebar.expanded .logout-link {
                justify-content: flex-start;
                padding: 12px 15px;
            }

            .sidebar.expanded .logout-link svg {
                margin-right: 12px;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 0;
                transform: translateX(-100%);
            }

            .sidebar.expanded {
                width: var(--sidebar-width);
                transform: translateX(0);
            }

            body {
                margin-left: 0;
            }

            .menu-toggle {
                left: 10px;
                top: 10px;
            }
        }

        /* Scrollbar styling for sidebar content */
        .sidebar-content::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-content::-webkit-scrollbar-thumb {
            background: var(--sidebar-accent);
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <button class="menu-toggle" id="menuToggle">☰</button>
    
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-content">
            <div class="sidebar-top">
                <div class="sidebar-header">
                    <div class="sidebar-logo"><?php echo htmlspecialchars($sidebar_logo); ?></div>
                    <div class="user-info">
                        <p class="user-name">Rich Anne Lea</p>
                        <p class="user-role"><strong>Tiles Trading</strong></p>
                    </div>
                </div>

                <h2 class="dashboard-title">Admin Dashboard</h2>

                <ul class="sidebar-nav">
                    <li class="sidebar-nav-item">
                        <a href="../staffadmin_access/admin_analytics.php" class="sidebar-nav-link">
                            <!-- Dashboard Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/><path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" fill="none"/></svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <div class="sidebar-nav-link dropdown-menu-parent">
                            <!-- Sales Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 17v-2a4 4 0 014-4h10a4 4 0 014 4v2" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="9" cy="7" r="4" fill="none" stroke="currentColor" stroke-width="2"/></svg>
                            <span>Sales</span>
                            <svg class="dropdown-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item">
                                <a href="../staffadmin_access/admin_orders.php" class="dropdown-link">
                                    <!-- Orders Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M16 3v4M8 3v4" stroke="currentColor" stroke-width="2"/></svg>
                                    <span>Orders</span>
                                </a>
                            </li>
                            <li class="dropdown-item">
                                <a href="../staffadmin_access/admin_transactions.php" class="dropdown-link">
                                    <!-- Transactions Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M17 17v-6a5 5 0 00-10 0v6" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="7" r="4" fill="none" stroke="currentColor" stroke-width="2"/></svg>
                                    <span>Transactions</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-nav-item">
                        <div class="sidebar-nav-link dropdown-menu-parent">
                            <!-- Reports Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M9 17V9M15 17V13" stroke="currentColor" stroke-width="2"/></svg>
                            <span>Reports</span>
                            <svg class="dropdown-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item">
                                <a href="../staffadmin_access/admin_financialreports.php" class="dropdown-link">
                                    <!-- Financial Reports Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M8 17v-6M12 17v-2M16 17v-4" stroke="currentColor" stroke-width="2"/></svg>
                                    <span>Financial Reports</span>
                                </a>
                            </li>
                            <li class="dropdown-item">
                                <a href="../staffadmin_access/admin_salesreports.php" class="dropdown-link">
                                    <!-- Sales Reports Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M7 17v-4M12 17v-7M17 17v-2" stroke="currentColor" stroke-width="2"/></svg>
                                    <span>Sales Reports</span>
                                </a>
                            </li>
                            <li class="dropdown-item">
                                <a href="../staffadmin_access/admin_inventoryreports.php" class="dropdown-link">
                                    <!-- Inventory Reports Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M7 17v-2M12 17v-4M17 17v-6" stroke="currentColor" stroke-width="2"/></svg>
                                    <span>Inventory Reports</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-nav-item">
                        <div class="sidebar-nav-link dropdown-menu-parent">
                            <!-- Maintenance Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21 7l-1-1-4 4 1 1a2 2 0 002.83 0l1.17-1.17a2 2 0 000-2.83zM3 17v2a2 2 0 002 2h2l9-9-4-4-9 9z" fill="none" stroke="currentColor" stroke-width="2"/></svg>
                            <span>Maintenance</span>
                            <svg class="dropdown-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item">
                                <a href="../staffadmin_access/admin_addproduct.php" class="dropdown-link">
                                    <!-- Products Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M16 3v4M8 3v4" stroke="currentColor" stroke-width="2"/></svg>
                                    <span>Products</span>
                                </a>
                            </li>
                            <li class="dropdown-item">
                                <a href="../staffadmin_access/admin_addsupplier.php" class="dropdown-link">
                                    <!-- Suppliers Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" fill="none" stroke="currentColor" stroke-width="2"/><path d="M6 20v-2a4 4 0 014-4h0a4 4 0 014 4v2" fill="none" stroke="currentColor" stroke-width="2"/></svg>
                                    <span>Suppliers</span>
                                </a>
                            </li>
                            <li class="dropdown-item">
                                <a href="#" class="dropdown-link">
                                    <!-- Banner Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><polyline points="4,4 12,12 20,4" fill="none" stroke="currentColor" stroke-width="2"/></svg>
                                    <span>Banner</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-nav-item">
                        <div class="sidebar-nav-link dropdown-menu-parent">
                            <!-- Inquiry Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/><path d="M12 8v4" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="16" r="1" fill="currentColor"/></svg>
                            <span>Inquiry</span>
                            <svg class="dropdown-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item">
                                <a href="#" class="dropdown-link">
                                    <!-- Cancel Request Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2"/><line x1="6" y1="18" x2="18" y2="6" stroke="currentColor" stroke-width="2"/></svg>
                                    <span>Cancel Request</span>
                                </a>
                            </li>
                            <li class="dropdown-item">
                                <a href="#" class="dropdown-link">
                                    <!-- Customer Tickets Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="10" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M7 7V5a2 2 0 012-2h6a2 2 0 012 2v2" stroke="currentColor" stroke-width="2" fill="none"/></svg>
                                    <span>Customer Tickets</span>
                                </a>
                            </li>
                        </ul>
                    </li>
            </div>

            <a href="../connection/logout.php?redirect=index.php" class="logout-link">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h10V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h10v-2H4V5z"/>
                </svg>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <script>
        // Session check every 0.1 second (100ms)
        setInterval(function() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '../connection/check_session.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var resp = xhr.responseText.trim();
                    if (resp !== 'OK') {
                        window.location.href = '../connection/tresspass.php';
                    }
                }
            };
            xhr.send();
        }, 100);
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.sidebar-nav-link, .dropdown-link');
            const dropdownParents = document.querySelectorAll('.dropdown-menu-parent');
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.getElementById('menuToggle');

            // Set active link based on current page
            const currentPath = window.location.pathname.split('/').pop();
            navLinks.forEach(link => {
                if (link.href) {
                    const linkPath = link.href.split('/').pop();
                    if (linkPath === currentPath) {
                        link.classList.add('active');
                        
                        // If it's a dropdown item, open its parent menu
                        const dropdownMenu = link.closest('.dropdown-menu');
                        if (dropdownMenu) {
                            dropdownMenu.classList.add('show');
                            const parent = dropdownMenu.previousElementSibling;
                            if (parent.classList.contains('dropdown-menu-parent')) {
                                parent.classList.add('open');
                            }
                        }
                    }
                }
            });

            // Toggle dropdown menus
            dropdownParents.forEach(parent => {
                parent.addEventListener('click', function(e) {
                    // Don't toggle if we're on mobile with collapsed sidebar
                    if (window.innerWidth <= 992 && !sidebar.classList.contains('expanded')) {
                        return;
                    }
                    
                    e.stopPropagation();
                    this.classList.toggle('open');
                    
                    const dropdownMenu = this.nextElementSibling;
                    if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                        dropdownMenu.classList.toggle('show');
                    }
                });
            });

            // Toggle sidebar on menu button click
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('expanded');
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnMenuToggle = menuToggle.contains(event.target);
                
                if (window.innerWidth <= 576 && !isClickInsideSidebar && !isClickOnMenuToggle) {
                    sidebar.classList.remove('expanded');
                }
            });

            // Handle window resize
            function handleResize() {
                if (window.innerWidth > 992) {
                    sidebar.classList.remove('expanded');
                }
            }

            window.addEventListener('resize', handleResize);
        });
    </script>
</body>
</html>