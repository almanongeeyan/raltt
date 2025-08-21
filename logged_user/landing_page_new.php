<?php
// Enforce session and cache control to prevent back navigation after logout
session_start();
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Additional security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\' https: data: \'unsafe-inline\' \'unsafe-eval\'; img-src \'self\' https: data:; style-src \'self\' https: \'unsafe-inline\'; script-src \'self\' https: \'unsafe-inline\' \'unsafe-eval\';');

if (!isset($_SESSION['logged_in'])) {
    header('Location: ../connection/tresspass.php');
    exit();
}
include '../includes/headeruser.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rich Anne Lea Tiles Trading - Landing Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>
    <style>
        /* CSS Variables */
        :root {
            --primary-color: #7d310a;
            --secondary-color: #cf8756;
            --accent-color: #e8a56a;
            --dark-color: #270f03;
            --light-color: #f9f5f2;
            --text-dark: #333;
            --text-light: #777;
            --white: #ffffff;
            --shadow-light: rgba(0, 0, 0, 0.08);
            --shadow-medium: rgba(0, 0, 0, 0.15);
            --shadow-heavy: rgba(207, 135, 86, 0.3);
            --border-radius: 20px;
            --transition: all 0.3s cubic-bezier(0.4, 1.4, 0.6, 1.1);
        }

        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            color: var(--text-dark);
            background-color: var(--light-color);
            line-height: 1.6;
        }

        /* Utility Classes */
        .container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section-padding {
            padding: 80px 0;
        }

        .text-center {
            text-align: center;
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            min-height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                        url('../images/user/landingpagebackground.PNG') center center/cover no-repeat;
            display: flex;
            align-items: center;
            padding: 80px 0 40px;
            overflow: hidden;
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .hero-text {
            color: var(--white);
            z-index: 2;
        }

        .hero-subtitle {
            font-size: clamp(1rem, 2vw, 1.2rem);
            font-weight: 600;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px #000;
            margin-bottom: 1rem;
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 900;
            color: var(--secondary-color);
            text-shadow: 2px 2px 8px #000;
            line-height: 1.1;
            margin-bottom: 1.5rem;
        }

        .hero-description {
            font-size: clamp(1rem, 1.5vw, 1.2rem);
            max-width: 500px;
            line-height: 1.6;
        }

        .hero-image {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero-img {
            max-width: 100%;
            height: auto;
            max-height: 70vh;
            transform: rotate(40deg);
            filter: drop-shadow(0 4px 15px rgba(0, 0, 0, 0.5));
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: rotate(40deg) translateY(0); }
            50% { transform: rotate(40deg) translateY(-20px); }
        }

        /* Featured Section */
        .featured-section {
            background: var(--light-color);
            padding: var(--section-padding);
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-subtitle {
            font-size: clamp(1rem, 1.5vw, 1.1rem);
            font-weight: 500;
            letter-spacing: 1px;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }

        .section-title {
            font-size: clamp(2rem, 4vw, 2.5rem);
            font-weight: 900;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .section-description {
            font-size: clamp(1rem, 1.5vw, 1.1rem);
            color: var(--text-light);
            max-width: 700px;
            margin: 0 auto;
        }

        /* Featured Carousel */
        .featured-carousel {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            overflow: hidden;
        }

        .carousel-container {
            display: flex;
            gap: 2rem;
            transition: transform 0.6s ease;
            padding: 1rem 0;
        }

        .carousel-item {
            flex: 0 0 auto;
            width: 280px;
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 2rem 1.5rem;
            box-shadow: 0 8px 24px var(--shadow-light);
            border: 1px solid #f0f0f0;
            transition: var(--transition);
            cursor: pointer;
        }

        .carousel-item:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 12px 40px var(--shadow-heavy);
            border-color: var(--secondary-color);
        }

        .carousel-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .carousel-item:hover .carousel-img {
            transform: scale(1.05);
        }

        .carousel-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .carousel-price {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
        }

        .carousel-btn {
            width: 100%;
            padding: 12px 24px;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 30px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .carousel-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px var(--shadow-heavy);
        }

        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: var(--white);
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
            color: var(--primary-color);
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 12px var(--shadow-light);
            z-index: 10;
        }

        .carousel-nav:hover {
            background: var(--primary-color);
            color: var(--white);
            transform: scale(1.1);
        }

        .carousel-nav.prev {
            left: -25px;
        }

        .carousel-nav.next {
            right: -25px;
        }

        /* Tile Categories Section */
        .tile-categories-section {
            background: var(--dark-color);
            color: var(--white);
            padding: var(--section-padding);
            position: relative;
            overflow: hidden;
        }

        .tile-categories-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(125, 49, 10, 0.05) 25%, transparent 25%),
                        linear-gradient(-45deg, rgba(125, 49, 10, 0.05) 25%, transparent 25%),
                        linear-gradient(45deg, transparent 75%, rgba(125, 49, 10, 0.05) 75%),
                        linear-gradient(-45deg, transparent 75%, rgba(125, 49, 10, 0.05) 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            opacity: 0.3;
            z-index: 1;
        }

        .tile-categories-section .section-title {
            color: var(--white);
            text-shadow: 
                -1px -1px 0 #000,
                1px -1px 0 #000,
                -1px 1px 0 #000,
                1px 1px 0 #000;
        }

        .tile-categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .tile-category {
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 8px 24px var(--shadow-light);
            transition: var(--transition);
            cursor: pointer;
            border: 1px solid #f0f0f0;
            position: relative;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s forwards;
        }

        .tile-category:nth-child(1) { animation-delay: 0.1s; }
        .tile-category:nth-child(2) { animation-delay: 0.2s; }
        .tile-category:nth-child(3) { animation-delay: 0.3s; }
        .tile-category:nth-child(4) { animation-delay: 0.4s; }
        .tile-category:nth-child(5) { animation-delay: 0.5s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tile-category:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 12px 40px var(--shadow-heavy);
            border-color: var(--secondary-color);
        }

        .tile-category-img {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .tile-category-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .tile-category:hover .tile-category-img img {
            transform: scale(1.1);
        }

        .tile-category-content {
            padding: 1.5rem;
            text-align: center;
            background: var(--white);
        }

        .tile-category-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .tile-category-desc {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .tile-category-btn {
            width: 100%;
            padding: 12px 24px;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .tile-category-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .tile-category-btn:hover::before {
            left: 100%;
        }

        .tile-category-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px var(--shadow-heavy);
        }

        .tile-category-btn i {
            font-size: 0.9rem;
            transition: transform 0.3s ease;
        }

        .tile-category-btn:hover i {
            transform: scale(1.1);
        }

        /* Shop Collection Section */
        .shop-collection-section {
            background: var(--light-color);
            padding: var(--section-padding);
        }

        .shop-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 3rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .filter-sidebar {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: 0 8px 24px var(--shadow-light);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .filter-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .filter-group {
            border-bottom: 1px solid #eee;
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-group:last-child {
            border-bottom: none;
        }

        .filter-group h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
        }

        .filter-options {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .filter-option {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            color: var(--text-light);
            cursor: pointer;
            transition: color 0.2s;
        }

        .filter-option:hover {
            color: var(--primary-color);
        }

        .filter-option input {
            margin-right: 0.75rem;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .filter-apply {
            width: 100%;
            padding: 12px;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 1rem;
        }

        .filter-apply:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .product-main h2 {
            font-size: clamp(2rem, 4vw, 2.5rem);
            font-weight: 900;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .product-card {
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 8px 24px var(--shadow-light);
            transition: var(--transition);
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px var(--shadow-medium);
        }

        .product-label {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--primary-color);
            color: var(--white);
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .product-label.bestseller {
            background: var(--secondary-color);
        }

        .product-label.sale {
            background: #d9534f;
        }

        .product-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-img {
            transform: scale(1.05);
        }

        .product-info {
            padding: 1.5rem;
            text-align: center;
        }

        .product-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .product-price {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }

        .product-btn {
            width: 100%;
            padding: 12px 24px;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .product-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px var(--shadow-heavy);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .hero-content {
                gap: 3rem;
            }
            
            .tile-categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 1.5rem;
            }
            
            .shop-container {
                grid-template-columns: 250px 1fr;
                gap: 2rem;
            }
        }

        @media (max-width: 992px) {
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 2rem;
            }
            
            .hero-img {
                max-height: 50vh;
                transform: rotate(25deg);
            }
            
            .tile-categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1.5rem;
            }
            
            .shop-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .filter-sidebar {
                position: static;
                order: -1;
            }
        }

        @media (max-width: 768px) {
            .section-padding {
                padding: 60px 0;
            }
            
            .hero-section {
                min-height: 80vh;
                padding: 60px 0 30px;
            }
            
            .hero-title {
                font-size: clamp(2rem, 6vw, 3rem);
            }
            
            .carousel-item {
                width: 250px;
                padding: 1.5rem 1rem;
            }
            
            .tile-categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 1rem;
            }
            
            .tile-category-img {
                height: 150px;
            }
            
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 1.5rem;
            }
            
            .carousel-nav {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
            
            .carousel-nav.prev {
                left: -20px;
            }
            
            .carousel-nav.next {
                right: -20px;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding: 0 15px;
            }
            
            .hero-content {
                padding: 0 15px;
            }
            
            .hero-img {
                max-height: 40vh;
                transform: rotate(15deg);
            }
            
            .carousel-item {
                width: 220px;
                padding: 1rem 0.75rem;
            }
            
            .carousel-img {
                height: 150px;
            }
            
            .tile-categories-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .tile-category-img {
                height: 180px;
            }
            
            .product-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .filter-sidebar {
                padding: 1.5rem;
            }
            
            .carousel-nav {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: clamp(1.8rem, 8vw, 2.5rem);
            }
            
            .section-title {
                font-size: clamp(1.5rem, 6vw, 2rem);
            }
            
            .carousel-item {
                width: 200px;
                padding: 0.75rem;
            }
            
            .carousel-img {
                height: 120px;
            }
            
            .tile-category-content {
                padding: 1rem;
            }
            
            .tile-category-title {
                font-size: 1rem;
            }
            
            .tile-category-desc {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <?php
    if (!isset($_SESSION['logged_in'])) {
        echo "<script>window.location.href='../connection/tresspass.php';</script>";
        exit();
    }
    ?>
    
    <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                fetch(window.location.href, { cache: 'reload', credentials: 'same-origin' }).catch(() => {
                    window.location.href = '../connection/tresspass.php';
                });
            }
        });
    </script>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <div class="hero-text">
                <div class="hero-subtitle">STYLE IN YOUR EVERY STEP.</div>
                <h1 class="hero-title">CHOOSE YOUR<br>TILES NOW.</h1>
                <p class="hero-description">
                    Discover our premium collection of tiles that combine elegance,
                    durability, and style to transform any space into a masterpiece.
                </p>
            </div>
            <div class="hero-image">
                <img src="../images/user/landingpagetile1.png" alt="Landing Tile" class="hero-img" />
            </div>
        </div>
    </section>

    <!-- Featured Section -->
    <section class="featured-section">
        <div class="container">
            <div class="section-header">
                <div class="section-subtitle">Premium Selection</div>
                <h2 class="section-title">Featured Items</h2>
                <p class="section-description">
                    Explore our handpicked selection of premium tiles that combine quality
                    craftsmanship with exceptional design for your home or business.
                </p>
            </div>
            
            <div class="featured-carousel">
                <button class="carousel-nav prev" aria-label="Previous">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="carousel-container" id="featuredCarousel"></div>
                <button class="carousel-nav next" aria-label="Next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Tile Categories Section -->
    <section class="tile-categories-section">
        <div class="container">
            <div class="section-header">
                <div class="section-subtitle">Explore Our Collection</div>
                <h2 class="section-title">Our Tile Selection</h2>
                <p class="section-description">
                    From classic ceramics to luxurious natural stone, find the perfect
                    tiles to match your style and needs.
                </p>
            </div>
            
            <div class="tile-categories-grid">
                <div class="tile-category" data-category="ceramic">
                    <div class="tile-category-img">
                        <img src="../images/user/tile1.jpg" alt="Ceramic Tiles" />
                    </div>
                    <div class="tile-category-content">
                        <h3 class="tile-category-title">Ceramic Tiles</h3>
                        <p class="tile-category-desc">
                            Durable and versatile ceramic tiles for any space
                        </p>
                        <button class="tile-category-btn">
                            <i class="fa fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>

                <div class="tile-category" data-category="porcelain">
                    <div class="tile-category-img">
                        <img src="../images/user/tile2.jpg" alt="Porcelain Tiles" />
                    </div>
                    <div class="tile-category-content">
                        <h3 class="tile-category-title">Porcelain Tiles</h3>
                        <p class="tile-category-desc">
                            Premium quality porcelain for high-end finishes
                        </p>
                        <button class="tile-category-btn">
                            <i class="fa fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>

                <div class="tile-category" data-category="mosaic">
                    <div class="tile-category-img">
                        <img src="../images/user/tile3.jpg" alt="Mosaic Tiles" />
                    </div>
                    <div class="tile-category-content">
                        <h3 class="tile-category-title">Mosaic Tiles</h3>
                        <p class="tile-category-desc">
                            Artistic designs for unique decorative accents
                        </p>
                        <button class="tile-category-btn">
                            <i class="fa fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>

                <div class="tile-category" data-category="natural-stone">
                    <div class="tile-category-img">
                        <img src="../images/user/tile4.jpg" alt="Natural Stone Tiles" />
                    </div>
                    <div class="tile-category-content">
                        <h3 class="tile-category-title">Natural Stone</h3>
                        <p class="tile-category-desc">
                            Elegant natural stone for luxurious spaces
                        </p>
                        <button class="tile-category-btn">
                            <i class="fa fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>

                <div class="tile-category" data-category="premium">
                    <div class="tile-category-img">
                        <img src="../images/user/tile5.jpg" alt="Premium Tiles" />
                    </div>
                    <div class="tile-category-content">
                        <h3 class="tile-category-title">Premium Tiles</h3>
                        <p class="tile-category-desc">
                            High-end premium tiles for luxury spaces
                        </p>
                        <button class="tile-category-btn">
                            <i class="fa fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Shop Collection Section -->
    <section class="shop-collection-section">
        <div class="container">
            <div class="shop-container">
                <div class="filter-sidebar">
                    <h3 class="filter-title">Categories</h3>
                    <div class="filter-group">
                        <div class="filter-options">
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="ceramic" />
                                Ceramic Tiles
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="porcelain" />
                                Porcelain Tiles
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="mosaic" />
                                Mosaic Tiles
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="natural-stone" />
                                Natural Stone
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="outdoor" />
                                Outdoor Tiles
                            </label>
                        </div>
                    </div>

                    <h3 class="filter-title">Price Range</h3>
                    <div class="filter-group">
                        <div class="filter-options">
                            <label class="filter-option">
                                <input type="radio" name="price-range" value="under-500" />
                                Under ₱500
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="price-range" value="500-1000" />
                                ₱500 - ₱1000
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="price-range" value="1000-2000" />
                                ₱1000 - ₱2000
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="price-range" value="over-2000" />
                                Over ₱2000
                            </label>
                        </div>
                    </div>

                    <button class="filter-apply">Apply Filters</button>
                </div>

                <div class="product-main">
                    <h2>Premium Tiles</h2>
                    <p>Browse our extensive collection of premium tiles for every room in your home or business.</p>
                    
                    <div class="product-grid">
                        <div class="product-card">
                            <span class="product-label bestseller">Bestseller</span>
                            <img src="../images/user/tile1.jpg" alt="Premium Ceramic Tile" class="product-img" />
                            <div class="product-info">
                                <h3 class="product-title">Premium Ceramic Tile</h3>
                                <div class="product-price">₱1,250</div>
                                <button class="product-btn">
                                    <i class="fa fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>

                        <div class="product-card">
                            <img src="../images/user/tile2.jpg" alt="Porcelain Tile" class="product-img" />
                            <div class="product-info">
                                <h3 class="product-title">Porcelain Tile</h3>
                                <div class="product-price">₱950</div>
                                <button class="product-btn">
                                    <i class="fa fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>

                        <div class="product-card">
                            <span class="product-label">New</span>
                            <img src="../images/user/tile3.jpg" alt="Mosaic Tile" class="product-img" />
                            <div class="product-info">
                                <h3 class="product-title">Mosaic Tile</h3>
                                <div class="product-price">₱1,750</div>
                                <button class="product-btn">
                                    <i class="fa fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>

                        <div class="product-card">
                            <img src="../images/user/tile4.jpg" alt="Natural Stone Tile" class="product-img" />
                            <div class="product-info">
                                <h3 class="product-title">Natural Stone Tile</h3>
                                <div class="product-price">₱850</div>
                                <button class="product-btn">
                                    <i class="fa fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>

                        <div class="product-card">
                            <span class="product-label sale">Sale</span>
                            <img src="../images/user/tile5.jpg" alt="Classic Tile" class="product-img" />
                            <div class="product-info">
                                <h3 class="product-title">Classic Tile</h3>
                                <div class="product-price">₱2,100</div>
                                <button class="product-btn">
                                    <i class="fa fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Featured Items Data
        const featuredItems = [
            {
                img: '../images/user/tile1.jpg',
                title: 'Premium Ceramic Tile',
                price: '₱1,250',
            },
            {
                img: '../images/user/tile2.jpg',
                title: 'Porcelain Tile',
                price: '₱950',
            },
            {
                img: '../images/user/tile3.jpg',
                title: 'Mosaic Tile',
                price: '₱1,750',
            },
            {
                img: '../images/user/tile4.jpg',
                title: 'Natural Stone Tile',
                price: '₱850',
            },
            {
                img: '../images/user/tile5.jpg',
                title: 'Classic Tile',
                price: '₱2,100',
            },
        ];

        // Carousel functionality
        let currentSlide = 0;
        const carousel = document.getElementById('featuredCarousel');
        const prevBtn = document.querySelector('.carousel-nav.prev');
        const nextBtn = document.querySelector('.carousel-nav.next');

        function renderCarousel() {
            if (!carousel) return;
            
            const itemsPerView = window.innerWidth <= 576 ? 1 : 
                                window.innerWidth <= 768 ? 2 : 
                                window.innerWidth <= 992 ? 3 : 4;
            
            carousel.innerHTML = '';
            
            for (let i = 0; i < itemsPerView; i++) {
                const index = (currentSlide + i) % featuredItems.length;
                const item = featuredItems[index];
                
                const itemElement = document.createElement('div');
                itemElement.className = 'carousel-item';
                itemElement.innerHTML = `
                    <img src="${item.img}" alt="${item.title}" class="carousel-img" />
                    <h3 class="carousel-title">${item.title}</h3>
                    <div class="carousel-price">${item.price}</div>
                    <button class="carousel-btn">
                        <i class="fa fa-shopping-cart"></i> Add to Cart
                    </button>
                `;
                
                carousel.appendChild(itemElement);
            }
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % featuredItems.length;
            renderCarousel();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + featuredItems.length) % featuredItems.length;
            renderCarousel();
        }

        // Event Listeners
        if (prevBtn) prevBtn.addEventListener('click', prevSlide);
        if (nextBtn) nextBtn.addEventListener('click', nextSlide);

        // Tile Categories functionality
        document.querySelectorAll('.tile-category').forEach(category => {
            category.addEventListener('click', function() {
                const title = this.querySelector('.tile-category-title').textContent;
                const description = this.querySelector('.tile-category-desc').textContent;
                const categoryType = this.dataset.category;
                
                // Show modal with category information
                Swal.fire({
                    title: title,
                    text: description,
                    icon: 'info',
                    confirmButtonText: 'Explore',
                    confirmButtonColor: '#7d310a',
                    showCancelButton: true,
                    cancelButtonText: 'Close'
                });
            });
        });

        // Add to cart functionality for all buttons
        document.querySelectorAll('.carousel-btn, .tile-category-btn, .product-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const title = this.closest('.carousel-item, .tile-category, .product-card')?.querySelector('.carousel-title, .tile-category-title, .product-title')?.textContent || 'Product';
                
                Swal.fire({
                    title: 'Added to Cart!',
                    text: `${title} has been added to your cart.`,
                    icon: 'success',
                    confirmButtonText: 'Continue Shopping',
                    confirmButtonColor: '#7d310a'
                });
            });
        });

        // Filter functionality
        document.querySelector('.filter-apply').addEventListener('click', function() {
            const selectedCategories = Array.from(document.querySelectorAll('input[name="category"]:checked'))
                .map(input => input.value);
            const selectedPrice = document.querySelector('input[name="price-range"]:checked')?.value;
            
            Swal.fire({
                title: 'Filters Applied!',
                text: `Showing results for: ${selectedCategories.join(', ') || 'All categories'}${selectedPrice ? ` | Price: ${selectedPrice}` : ''}`,
                icon: 'info',
                confirmButtonText: 'OK',
                confirmButtonColor: '#7d310a'
            });
        });

        // Handle image loading errors
        document.querySelectorAll('img').forEach(img => {
            img.addEventListener('error', function() {
                this.style.display = 'none';
                const parent = this.parentElement;
                if (parent) {
                    parent.style.background = 'linear-gradient(45deg, #f0f0f0 25%, transparent 25%), linear-gradient(-45deg, #f0f0f0 25%, transparent 25%)';
                    parent.style.backgroundSize = '20px 20px';
                    parent.style.display = 'flex';
                    parent.style.alignItems = 'center';
                    parent.style.justifyContent = 'center';
                    
                    const placeholder = document.createElement('span');
                    placeholder.style.color = '#999';
                    placeholder.style.fontSize = '0.9rem';
                    placeholder.textContent = 'Image not available';
                    
                    parent.innerHTML = '';
                    parent.appendChild(placeholder);
                }
            });
        });

        // Initialize carousel
        document.addEventListener('DOMContentLoaded', function() {
            renderCarousel();
            
            // Handle window resize
            window.addEventListener('resize', renderCarousel);
        });
    </script>
</body>
</html>
