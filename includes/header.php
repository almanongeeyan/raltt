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
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding-top: 80px;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 5%;
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
            height: 40px;
            margin-right: 10px;
        }

        .header .navbar {
            display: flex;
            gap: 2.5rem;
        }

        .header .navbar a {
            font-size: 1.1rem;
            color: #555;
            text-decoration: none;
            padding: 0.5rem 0;
            position: relative;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .header .navbar a:hover {
            color: #EF7232;
            transform: translateY(-3px);
        }

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
            transform: rotate(180deg);
        }

        .header .navbar .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 220px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 8px;
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
            display: flex;
            align-items: center;
            text-align: left;
            font-size: 1rem;
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
            white-space: nowrap;
        }

        .header .navbar .dropdown-content a i {
            margin-right: 10px;
            color: #666;
            transition: color 0.3s ease;
        }

        .header .navbar .dropdown-content a:hover {
            background-color: #f1f1f1;
            color: #EF7232;
            transform: translateX(5px);
        }

        .header .navbar .dropdown-content a:hover i {
            color: #EF7232;
        }

        .header .login-btn {
            background-color: #A0522D;
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
            background-color: #8B4513;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .header .fas.fa-bars {
            font-size: 1.8rem;
            color: #333;
            cursor: pointer;
            display: none;
        }

        /* Responsive adjustments */
        @media (max-width: 991px) {
            .header {
                padding: 1rem 2%;
            }
            .header .logo img {
                height: 32px;
            }
            .header .navbar {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background-color: #fff;
                border-top: 1px solid #eee;
                flex-direction: column;
                align-items: flex-start;
                padding: 1rem 0;
                gap: 0;
                clip-path: polygon(0 0, 100% 0, 100% 0, 0 0);
                transition: clip-path 0.3s ease-in-out;
                z-index: 999;
                display: flex !important; /* Ensure navbar is always rendered */
            }
            .header .navbar.active {
                clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
            }
            .header .navbar a,
            .header .navbar .dropdown .dropbtn {
                margin: 0.5rem 1.5rem;
                text-align: left;
                width: 100%;
                display: block;
            }
            .header .navbar .dropdown-content {
                position: static;
                box-shadow: none;
                min-width: unset;
                padding-left: 20px;
                transform: translateY(0);
                opacity: 1;
                border-radius: 0;
                display: none;
            }
            .header .navbar .dropdown.open .dropdown-content {
                display: block;
            }
            .header .login-btn {
                padding: 0.7rem 1rem;
                font-size: 0.95rem;
            }
            .header .fas.fa-bars {
                display: block;
            }
        }

        @media (max-width: 600px) {
            .header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                padding: 0.7rem 2vw;
                width: 100vw;
                box-sizing: border-box;
                max-width: 100vw;
            }
            .header .logo img {
                max-width: 120px;
                height: auto;
            }
            .header .login-bars-group {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .header .login-btn {
                margin-top: 0;
                width: auto;
                justify-content: center;
                font-size: 0.95rem;
                padding: 0.6rem 1rem;
            }
            .header .fas.fa-bars {
                display: block;
                margin-left: 0;
                font-size: 2rem;
            }
            .header .navbar {
                position: fixed;
                top: 60px;
                left: 0;
                right: 0;
                background-color: #fff;
                border-top: 1px solid #eee;
                flex-direction: column;
                align-items: flex-start;
                padding: 1rem 0;
                gap: 0;
                clip-path: polygon(0 0, 100% 0, 100% 0, 0 0);
                transition: clip-path 0.3s ease-in-out;
                z-index: 999;
                display: flex !important;
                max-width: 100vw;
                width: 100vw;
                box-sizing: border-box;
                overflow-x: hidden;
            }
            .header .navbar.active {
                clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
            }
            .header .navbar a,
            .header .navbar .dropdown .dropbtn {
                font-size: 1rem;
                padding: 0.8rem 1.5rem;
                width: 100%;
                box-sizing: border-box;
            }
            .header .navbar .dropdown-content {
                padding-left: 20px;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <a href="index.php" class="logo">
            <img src="images/header.png" alt="RALTT Logo">
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
            <a href="productviewhomepage.php">Products</a>
            <a href="#aboutus">About Us</a>
        </nav>
        <div class="login-bars-group">
            <div class="fas fa-bars" id="menu"></div>
            <a href="login.php" class="login-btn"> <i class="fas fa-user"></i> Login</a>
        </div>
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

        // For mobile, toggle dropdown on click
        document.querySelectorAll('.dropdown .dropbtn').forEach(button => {
            button.addEventListener('click', function(event) {
                if (window.innerWidth <= 991) {
                    event.preventDefault();
                    const parent = this.parentElement;
                    parent.classList.toggle('open');
                    event.stopPropagation();
                }
            });
        });

        // Optional: Close dropdown if clicking outside (for desktop)
        window.addEventListener('click', function(event) {
            if (!event.target.matches('.dropbtn')) {
                document.querySelectorAll('.dropdown').forEach(dropdown => {
                    dropdown.classList.remove('open');
                });
            }
        });
    </script>
</body>

</html>
