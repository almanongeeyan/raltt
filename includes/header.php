<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rich Anne Lea Tiles Trading</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>

    <style>
        /* General body styling for font consistency */
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding-top: 80px; /* Adjust based on header height */
        }

        /* Header styling */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 5%; /* Adjusted padding for better spacing */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .header .logo {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            text-decoration: none;
        }

        .header .logo img {
            height: 40px; /* Adjust logo size */
            /* The image itself might have width, but height ensures consistent vertical alignment */
            margin-right: 10px;
        }

        .header .navbar {
            display: flex;
            gap: 2.5rem; /* Increased gap for better spacing */
        }

        .header .navbar a {
            font-size: 1.1rem; /* Slightly larger font */
            color: #555;
            text-decoration: none;
            padding: 0.5rem 0;
            position: relative;
            transition: color 0.3s ease, transform 0.3s ease; /* Smooth transition for hover */
        }

        .header .navbar a:hover {
            color: #EF7232; /* Orange color on hover */
            transform: translateY(-3px); /* Slight upward movement */
        }

        /* Underline animation for navbar links */
        .header .navbar a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 0;
            height: 2px;
            background-color: #EF7232;
            transition: width 0.3s ease;
        }

        .header .navbar a:hover::after {
            width: 100%;
        }

        /* Dropdown specific styles */
        .header .navbar .dropdown {
            position: relative;
        }

        .header .navbar .dropdown .dropbtn {
            font-size: 1.1rem;
            color: #555;
            text-decoration: none;
            padding: 0.5rem 0;
            position: relative;
            background: none;
            border: none;
            cursor: pointer;
            outline: none;
            display: flex;
            align-items: center;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .header .navbar .dropdown .dropbtn i {
            margin-left: 5px;
            transition: transform 0.3s ease;
        }

        .header .navbar .dropdown:hover .dropbtn {
            color: #EF7232;
            transform: translateY(-3px);
        }

        .header .navbar .dropdown:hover .dropbtn i {
            transform: rotate(180deg); /* Rotate arrow on hover */
        }

        .header .navbar .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 220px; /* Increased width for content */
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 8px; /* Rounded corners for dropdown */
            padding: 10px 0;
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .header .navbar .dropdown:hover .dropdown-content {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .header .navbar .dropdown-content a {
            color: #333;
            padding: 12px 20px;
            text-decoration: none;
            display: flex; /* Use flex for icon and text alignment */
            align-items: center;
            text-align: left;
            font-size: 1rem;
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
            white-space: nowrap; /* Prevent text wrapping */
        }

        .header .navbar .dropdown-content a i {
            margin-right: 10px; /* Space between icon and text */
            color: #666; /* Default icon color */
            transition: color 0.3s ease;
        }

        .header .navbar .dropdown-content a:hover {
            background-color: #f1f1f1;
            color: #EF7232;
            transform: translateX(5px); /* Slide effect on hover */
        }

        .header .navbar .dropdown-content a:hover i {
            color: #EF7232; /* Change icon color on hover */
        }

        /* Login button styling */
        .header .login-btn {
            background-color: #A0522D; /* A shade of brown/orange */
            color: #fff;
            padding: 0.7rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1rem;
            display: flex;
            align-items: center;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        .header .login-btn i {
            margin-right: 8px;
        }

        .header .login-btn:hover {
            background-color: #8B4513; /* Darker shade on hover */
            transform: translateY(-2px); /* Lift effect */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* Mobile menu icon */
        .header .fas.fa-bars {
            font-size: 1.8rem;
            color: #333;
            cursor: pointer;
            display: none; /* Hidden on desktop */
        }

        /* Responsive adjustments */
        @media (max-width: 991px) {
            .header .fas.fa-bars {
                display: block; /* Show on mobile */
            }

            .header .navbar {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background-color: #fff;
                border-top: 1px solid #eee;
                flex-direction: column;
                padding: 1rem 0;
                clip-path: polygon(0 0, 100% 0, 100% 0, 0 0); /* Hidden by default */
                transition: clip-path 0.3s ease-in-out;
            }

            .header .navbar.active {
                clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%); /* Show when active */
            }

            .header .navbar a,
            .header .navbar .dropdown .dropbtn {
                margin: 0.5rem 1.5rem;
                text-align: left;
            }

            .header .navbar .dropdown-content {
                position: static; /* Remove absolute positioning for mobile */
                box-shadow: none;
                min-width: unset;
                padding-left: 20px; /* Indent dropdown items */
                transform: translateY(0); /* Reset transform */
                opacity: 1; /* Always visible when parent dropdown is active */
            }

            .header .navbar .dropdown:hover .dropdown-content {
                display: block; /* Ensure dropdown content shows on mobile when parent is hovered/clicked */
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <a href="index.php" class="logo">
            <!-- Replaced placeholder with header-home.png -->
            <img src="images/header.png" alt="RALTT Logo">
            <!-- Removed the text as the image now contains the branding -->
        </a>

        <nav class="navbar">
            <a href="#whats-new">What's New</a>
            <div class="dropdown">
                <button class="dropbtn">Features <i class="fas fa-caret-down"></i></button>
                <div class="dropdown-content">
                    <a href="2d_tile_visualizer.php"><i class="fas fa-cube"></i> 2D Tile Visualizer</a>
                    <a href="referral_code.php"><i class="fas fa-users"></i> Referral Code</a>
                </div>
            </div>
            <a href="product.php">Products</a>
            <a href="#aboutus">About Us</a>
        </nav>

        <a href="login.php" class="login-btn"> <i class="fas fa-user"></i> Login</a>
        <div class="fas fa-bars" id="menu"></div>
    </header>

    <script>
        const menu = document.querySelector('#menu');
        const navbar = document.querySelector('.header .navbar');

        menu.addEventListener('click', () => {
            navbar.classList.toggle('active');
        });

        window.addEventListener('scroll', () => {
            navbar.classList.remove('active');
        });

        // Optional: Close dropdown if clicking outside (for desktop)
        window.addEventListener('click', function(event) {
            if (!event.target.matches('.dropbtn')) {
                const dropdowns = document.getElementsByClassName("dropdown-content");
                for (let i = 0; i < dropdowns.length; i++) {
                    const openDropdown = dropdowns[i];
                    if (openDropdown.style.display === 'block') { // Check if it's currently displayed
                        openDropdown.style.display = 'none';
                        openDropdown.style.opacity = '0';
                        openDropdown.style.transform = 'translateY(10px)';
                    }
                }
            }
        });

        // For mobile, make dropdown toggle on click of dropbtn
        document.querySelectorAll('.dropdown .dropbtn').forEach(button => {
            button.addEventListener('click', function(event) {
                if (window.innerWidth <= 991) { // Apply only for mobile
                    const dropdownContent = this.nextElementSibling;
                    if (dropdownContent.style.display === 'block') {
                        dropdownContent.style.display = 'none';
                        dropdownContent.style.opacity = '0';
                        dropdownContent.style.transform = 'translateY(10px)';
                    } else {
                        dropdownContent.style.display = 'block';
                        dropdownContent.style.opacity = '1';
                        dropdownContent.style.transform = 'translateY(0)';
                    }
                    event.stopPropagation(); // Prevent immediate closing from window click listener
                }
            });
        });
    </script>
</body>

</html>
