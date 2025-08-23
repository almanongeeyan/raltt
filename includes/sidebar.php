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
                    <div class="sidebar-logo">STAFF</div>
                    <div class="user-info">
                        <p class="user-name">Rich Anne Lea</p>
                        <p class="user-role">Tiles Trading</p>
                    </div>
                </div>

                <h2 class="dashboard-title">Admin Dashboard</h2>

                <ul class="sidebar-nav">
                    <li class="sidebar-nav-item">
                        <a href="../staffadmin_access/admin_analytics.php" class="sidebar-nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2zM9 17H7v-7h2v7zm4 0h-2v-4h2v4zm4 0h-2v-2h2v2z"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="sidebar-nav-item">
                        <div class="sidebar-nav-link dropdown-menu-parent">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M16 13V3h-2v3H3v14a2 2 0 002 2h14a2 2 0 002-2v-9h-5zM5 20V8h12v5H7v5h8v-3z"/>
                            </svg>
                            <span>Sales</span>
                            <svg class="dropdown-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item">
                                <a href="../staffadmin_access/admin_orders.php" class="dropdown-link">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M16 13V3h-2v3H3v14a2 2 0 002 2h14a2 2 0 002-2v-9h-5zM5 20V8h12v5H7v5h8v-3z"/>
                                    </svg>
                                    <span>Orders</span>
                                </a>
                            </li>
                            <li class="dropdown-item">
                                <a href="../staffadmin_access/admin_completeorder.php" class="dropdown-link">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                    </svg>
                                    <span>Completed Orders</span>
                                </a>
                            </li>
                            <li class="dropdown-item">
                                <a href="../staffadmin_access/admin_financialreports.php" class="dropdown-link">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M3 3h18v18H3V3zm2 2v14h14V5H5zm4 2h2v10H9V7zm4 3h2v7h-2v-7z"/>
                                    </svg>
                                    <span>Financial Reports</span>
                                </a>
                            </li>
                            <li class="dropdown-item">
                                <a href="../staffadmin_access/admin_salesreports.php" class="dropdown-link">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2zm2 4v2h10V7H7zm0 4v2h10v-2H7zm0 4v2h7v-2H7z"/>
                                    </svg>
                                    <span>Sales Reports</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="sidebar-nav-item">
                        <a href="../staffadmin_access/admin_addproduct2.php" class="sidebar-nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M14 6H4a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V8a2 2 0 00-2-2zM4 22V8h10v14H4zm16-8v-2h-2V9h2V7h2v2h2v2h-2v2h2v2h2v2h-2v-2h-2v-2z"/>
                            </svg>
                            <span>Products</span>
                        </a>
                    </li>
                    
                    <li class="sidebar-nav-item">
                        <div class="sidebar-nav-link dropdown-menu-parent">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M22 6h-4V4c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v2H2c-1.1 0-2 .9-2 2v10a2 2 0 002 2h14a2 2 0 002-2v-4h4a2 2 0 002-2V8a2 2 0 00-2-2zm-6-2h-4v2h4V4zM2 18V8h4v10H2zm6 0v-4h8v4H8zm14-4h-4V8h4v6z"/>
                            </svg>
                            <span>Maintenance</span>
                            <svg class="dropdown-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item">
                                <a href="../staffadmin_access/admin_addsupplier.php" class="dropdown-link">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M22 6h-4V4c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v2H2c-1.1 0-2 .9-2 2v10a2 2 0 002 2h14a2 2 0 002-2v-4h4a2 2 0 002-2V8a2 2 0 00-2-2zm-6-2h-4v2h4V4zM2 18V8h4v10H2zm6 0v-4h8v4H8zm14-4h-4V8h4v6z"/>
                                    </svg>
                                    <span>Suppliers</span>
                                </a>
                            </li>
                            <li class="dropdown-item">
                                <a href="../staffadmin_access/admin_inventoryreports.php" class="dropdown-link">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M3 3h18v18H3V3zm2 2v14h14V5H5zm4 2h2v10H9V7zm4 3h2v7h-2v-7z"/>
                                    </svg>
                                    <span>Inventory Reports</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

            <a href="../index.php" class="logout-link">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h10V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h10v-2H4V5z"/>
                </svg>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <script>
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