<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rich Anne Lea Tiles Trading</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            light: '#EF7232',
                            DEFAULT: '#A0522D',
                            dark: '#8B4513',
                        },
                        accent: {
                            light: '#F5F5F5',
                            DEFAULT: '#F9F9F9',
                            dark: '#E5E5E5',
                        }
                    },
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    boxShadow: {
                        'header': '0 4px 20px rgba(0, 0, 0, 0.08)',
                        'dropdown': '0 10px 25px rgba(0, 0, 0, 0.1)',
                        'button': '0 4px 12px rgba(160, 82, 45, 0.3)',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-in-out',
                        'slide-down': 'slideDown 0.3s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideDown: {
                            '0%': { transform: 'translateY(-10px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            padding-top: 80px;
        }
        
        /* Enhanced header with gradient */
        .header-gradient {
            background: linear-gradient(135deg, #ffffff 0%, #f8f8f8 100%);
        }
        
        /* Enhanced dropdown animation */
        .dropdown-content {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }
        
        .dropdown:hover .dropdown-content,
        .dropdown.open .dropdown-content {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        /* Enhanced mobile menu animation */
        .mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease;
        }
        
        .mobile-menu.active {
            max-height: 500px;
        }
        
        /* Enhanced login button with glow effect */
        .login-btn {
            background-color: #A0522D;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .login-btn:hover {
            background-color: #8B4513;
            box-shadow: 0 6px 20px rgba(160, 82, 45, 0.4);
            transform: translateY(-2px);
        }
        
        .login-btn:active {
            transform: translateY(0);
        }
        
        /* Ripple effect for login button */
        .login-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }
        
        .login-btn:focus:not(:active)::after {
            animation: ripple 1s ease-out;
        }
        
        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(20, 20);
                opacity: 0;
            }
        }
        
        /* Enhanced nav link hover effect */
        .nav-link {
            position: relative;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: #EF7232;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        /* Enhanced mobile dropdown */
        .mobile-dropdown-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .mobile-dropdown.active .mobile-dropdown-content {
            max-height: 200px;
        }
        
        /* Logo hover effect */
        .logo-hover {
            transition: transform 0.3s ease;
        }
        
        .logo-hover:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-white font-inter">
    <!-- Enhanced Header -->
    <header class="fixed top-0 left-0 right-0 header-gradient shadow-header z-50 border-b border-gray-100">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <!-- Enhanced Logo with hover effect -->
                <a href="index.php" class="flex items-center logo-hover">
                    <img src="images/newlogo.PNG" alt="RALTT Logo" class="h-10 md:h-12">
                </a>

                <!-- Enhanced Desktop Navigation -->
                <nav class="hidden lg:flex items-center space-x-8">
                    <!-- Features Dropdown -->
                    <div class="dropdown relative">
                        <button class="flex items-center text-gray-700 hover:text-primary-light transition-colors duration-300 font-medium py-2 nav-link">
                            Features
                            <i class="fas fa-caret-down ml-2 text-sm transition-transform duration-300 dropdown-arrow"></i>
                        </button>
                        <div class="dropdown-content absolute top-full left-0 mt-2 w-64 bg-white rounded-xl shadow-dropdown overflow-hidden border border-gray-100 animate-slide-down">
                            <a href="2d_visualizer_homepage.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-accent-dark hover:text-primary-light transition-all duration-300 border-b border-gray-100 last:border-b-0">
                                <i class="fas fa-cube mr-3 text-primary-light"></i>
                                <span>3D Tile Visualizer</span>
                            </a>
                            <a href="refferal.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-accent-dark hover:text-primary-light transition-all duration-300">
                                <i class="fas fa-users mr-3 text-primary-light"></i>
                                <span>Referral Code</span>
                            </a>
                        </div>
                    </div>
                    
                    <a href="product_view_homepage.php" class="text-gray-700 hover:text-primary-light transition-colors duration-300 font-medium py-2 nav-link">Products</a>
                    <a href="about_us.php" class="text-gray-700 hover:text-primary-light transition-colors duration-300 font-medium py-2 nav-link">About Us</a>
                </nav>

                <!-- Enhanced Login Button & Mobile Menu Toggle -->
                <div class="flex items-center space-x-4">
                    <a href="user_login_form.php" class="hidden md:flex items-center login-btn text-white px-5 py-2.5 rounded-lg font-medium">
                        <i class="fas fa-user mr-2"></i>
                        Login
                    </a>
                    
                    <!-- Enhanced Mobile Login Button -->
                    <a href="user_login_form.php" class="md:hidden flex items-center login-btn text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-user mr-2"></i>
                        Login
                    </a>
                    
                    <!-- Enhanced Mobile Menu Toggle -->
                    <button id="mobile-menu-toggle" class="lg:hidden text-gray-700 focus:outline-none transition-transform duration-300 hover:scale-110">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Enhanced Mobile Menu -->
            <div id="mobile-menu" class="mobile-menu lg:hidden bg-white border-t border-gray-200">
                <div class="py-4 space-y-2">
                    <!-- Features Dropdown for Mobile -->
                    <div class="mobile-dropdown">
                        <button class="mobile-dropdown-toggle flex items-center justify-between w-full px-4 py-3 text-gray-700 hover:bg-accent rounded-lg transition-colors duration-300 font-medium">
                            <span>Features</span>
                            <i class="fas fa-caret-down transition-transform duration-300"></i>
                        </button>
                        <div class="mobile-dropdown-content pl-8 pr-4 space-y-2">
                            <a href="2d_visualizer_homepage.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-accent-dark hover:text-primary-light rounded-lg transition-all duration-300">
                                <i class="fas fa-cube mr-3 text-primary-light"></i>
                                <span>3D Tile Visualizer</span>
                            </a>
                            <a href="refferal.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-accent-dark hover:text-primary-light rounded-lg transition-all duration-300">
                                <i class="fas fa-users mr-3 text-primary-light"></i>
                                <span>Referral Code</span>
                            </a>
                        </div>
                    </div>
                    
                    <a href="product_view_homepage.php" class="block px-4 py-3 text-gray-700 hover:bg-accent rounded-lg transition-colors duration-300 font-medium">Products</a>
                    <a href="about_us.php" class="block px-4 py-3 text-gray-700 hover:bg-accent rounded-lg transition-colors duration-300 font-medium">About Us</a>
                </div>
            </div>
        </div>
    </header>

    
    <script>
        // Mobile Menu Toggle
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
            // Change icon between bars and times
            const icon = mobileMenuToggle.querySelector('i');
            if (mobileMenu.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
        
        // Mobile Dropdown Toggle
        document.querySelectorAll('.mobile-dropdown-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const dropdown = this.parentElement;
                const content = this.nextElementSibling;
                const icon = this.querySelector('i');
                
                dropdown.classList.toggle('active');
                icon.classList.toggle('rotate-180');
                
                // Close other dropdowns
                document.querySelectorAll('.mobile-dropdown').forEach(otherDropdown => {
                    if (otherDropdown !== dropdown) {
                        otherDropdown.classList.remove('active');
                        otherDropdown.querySelector('.mobile-dropdown-toggle i').classList.remove('rotate-180');
                    }
                });
            });
        });
        
        // Desktop dropdown arrow animation
        document.querySelectorAll('.dropdown').forEach(dropdown => {
            dropdown.addEventListener('mouseenter', function() {
                this.querySelector('.dropdown-arrow').classList.add('rotate-180');
            });
            
            dropdown.addEventListener('mouseleave', function() {
                this.querySelector('.dropdown-arrow').classList.remove('rotate-180');
            });
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('#mobile-menu') && !event.target.closest('#mobile-menu-toggle')) {
                mobileMenu.classList.remove('active');
                const icon = mobileMenuToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
                
                // Close all mobile dropdowns
                document.querySelectorAll('.mobile-dropdown').forEach(dropdown => {
                    dropdown.classList.remove('active');
                    dropdown.querySelector('.mobile-dropdown-toggle i').classList.remove('rotate-180');
                });
            }
        });
        
        // Add ripple effect to login buttons
        document.querySelectorAll('.login-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                // Create ripple element
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = `${size}px`;
                ripple.style.left = `${x}px`;
                ripple.style.top = `${y}px`;
                ripple.classList.add('ripple-effect');
                
                // Add ripple styles
                ripple.style.position = 'absolute';
                ripple.style.borderRadius = '50%';
                ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.7)';
                ripple.style.transform = 'scale(0)';
                ripple.style.animation = 'ripple 0.6s linear';
                ripple.style.pointerEvents = 'none';
                
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                // Remove ripple after animation
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    </script>
</body>
</html>