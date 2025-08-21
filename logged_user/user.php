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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rich Anne Lea Tiles Trading - Premium Tiles Collection</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>
    <style>
        :root {
            --primary-color: #CF8756;
            --primary-dark: #A86A42;
            --secondary-color: #25160c;
            --light-bg: #f7f7f7;
            --white: #ffffff;
            --text-dark: #222222;
            --text-light: #666666;
            --card-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            --card-hover-shadow: 0 12px 40px rgba(207, 135, 86, 0.15), 0 4px 15px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            background: var(--light-bg);
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Hero Section */
        .landing-hero-section {
            position: relative;
            width: 100%;
            min-height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.8)), url('../images/user/landingpagebackground.PNG') center center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 80px 5% 5%;
            overflow: hidden;
        }

        .landing-hero-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            max-width: 1200px;
            position: relative;
            z-index: 2;
            flex-wrap: wrap;
        }

        .center-hero-img {
            max-width: 100%;
            width: 500px;
            height: auto;
            transform: rotate(10deg);
            filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.5));
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(10deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
        }

        .landing-hero-text-overlay {
            flex: 1;
            min-width: 300px;
            padding: 20px;
            color: var(--white);
        }

        .landing-hero-text-overlay .small-text {
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 2px;
            color: var(--primary-color);
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .landing-hero-text-overlay .big-text {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 25px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
        }

        .hero-cta {
            display: inline-block;
            background: var(--primary-color);
            color: var(--white);
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(207, 135, 86, 0.3);
        }

        .hero-cta:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(207, 135, 86, 0.4);
        }

        /* Premium Collection Banner */
        .premium-banner {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: var(--white);
            text-align: center;
            padding: 40px 20px;
            margin: 0;
        }

        .premium-banner h2 {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        .premium-banner p {
            font-size: 1.1rem;
            max-width: 800px;
            margin: 0 auto;
            opacity: 0.9;
        }

        /* Featured Section */
        .featured-section {
            background: var(--white);
            padding: 80px 5%;
            text-align: center;
        }

        .section-subtitle {
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 15px;
            display: block;
        }

        .section-title {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 20px;
            color: var(--text-dark);
        }

        .section-description {
            font-size: 1.1rem;
            color: var(--text-light);
            max-width: 700px;
            margin: 0 auto 50px;
        }

        .featured-carousel-container {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
        }

        .featured-carousel {
            overflow: hidden;
            padding: 20px 0;
        }

        .featured-items {
            display: flex;
            transition: transform 0.5s ease;
        }

        .featured-item {
            flex: 0 0 calc(25% - 30px);
            margin: 0 15px;
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            padding: 25px 20px;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 380px;
        }

        .featured-item:hover {
            transform: translateY(-10px);
            box-shadow: var(--card-hover-shadow);
        }

        .featured-img-wrap {
            width: 160px;
            height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fafafa;
            border-radius: 15px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .featured-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .featured-item:hover .featured-img-wrap img {
            transform: scale(1.1);
        }

        .item-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--text-dark);
            text-align: center;
        }

        .item-price {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .add-to-cart {
            background: var(--white);
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 30px;
            padding: 10px 25px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: auto;
        }

        .add-to-cart:hover {
            background: var(--primary-color);
            color: var(--white);
        }

        .carousel-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 30px;
            gap: 15px;
        }

        .carousel-arrow {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--white);
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: var(--text-light);
            cursor: pointer;
            transition: var(--transition);
        }

        .carousel-arrow:hover {
            background: var(--primary-color);
            color: var(--white);
            border-color: var(--primary-color);
        }

        .carousel-pagination {
            display: flex;
            gap: 10px;
        }

        .pagination-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #ddd;
            cursor: pointer;
            transition: var(--transition);
        }

        .pagination-dot.active {
            background: var(--primary-color);
            transform: scale(1.2);
        }

        /* Tile Selection Section */
        .tile-selection-section {
            background: var(--secondary-color);
            padding: 80px 5%;
            text-align: center;
        }

        .tile-selection-section .section-title {
            color: var(--white);
        }

        .tile-selection-section .section-description {
            color: #ccc;
        }

        .tile-selection-grid {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .tile-selection-item {
            background: #3a2212;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            padding: 25px 20px;
            width: 220px;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .tile-selection-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
        }

        .tile-selection-img-wrap {
            width: 100%;
            height: 150px;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .tile-selection-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .tile-selection-item:hover .tile-selection-img-wrap img {
            transform: scale(1.1);
        }

        .tile-selection-item h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 10px;
        }

        .tile-selection-item p {
            font-size: 0.95rem;
            color: #bbb;
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .add-to-cart-button {
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 30px;
            padding: 10px 20px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            justify-content: center;
        }

        .add-to-cart-button:hover {
            background: var(--primary-dark);
        }

        /* Tile Categories Section */
        .tile-categories-section {
            padding: 80px 5%;
            background: var(--light-bg);
        }

        .tile-categories-container {
            display: flex;
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .categories-sidebar {
            flex: 0 0 250px;
            background: var(--white);
            padding: 25px;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            height: fit-content;
        }

        .categories-sidebar h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--primary-color);
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .filter-group {
            margin-bottom: 25px;
        }

        .filter-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .filter-item input {
            margin-right: 10px;
            accent-color: var(--primary-color);
        }

        .filter-item label {
            font-weight: 500;
            color: var(--text-dark);
            cursor: pointer;
        }

        .apply-filters-button {
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 30px;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            transition: var(--transition);
        }

        .apply-filters-button:hover {
            background: var(--primary-dark);
        }

        .tile-grid {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }

        .tile-item {
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }

        .tile-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-hover-shadow);
        }

        .tile-item img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 15px;
        }

        .item-details {
            margin-bottom: 15px;
            flex-grow: 1;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .featured-item {
                flex: 0 0 calc(33.333% - 30px);
            }
            
            .landing-hero-content {
                justify-content: center;
                text-align: center;
            }
            
            .center-hero-img {
                width: 400px;
            }
        }

        @media (max-width: 992px) {
            .featured-item {
                flex: 0 0 calc(50% - 30px);
            }
            
            .tile-categories-container {
                flex-direction: column;
            }
            
            .categories-sidebar {
                flex: 1;
                width: 100%;
            }
            
            .landing-hero-text-overlay .big-text {
                font-size: 2.8rem;
            }
        }

        @media (max-width: 768px) {
            .featured-item {
                flex: 0 0 calc(100% - 30px);
            }
            
            .landing-hero-section {
                min-height: auto;
                padding: 100px 5% 50px;
            }
            
            .center-hero-img {
                width: 300px;
                margin-bottom: 30px;
            }
            
            .landing-hero-text-overlay .big-text {
                font-size: 2.2rem;
            }
            
            .section-title {
                font-size: 2.2rem;
            }
            
            .tile-selection-item {
                width: 100%;
                max-width: 280px;
            }
            
            .premium-banner h2 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 576px) {
            .landing-hero-text-overlay .big-text {
                font-size: 1.8rem;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
            
            .carousel-arrow {
                width: 40px;
                height: 40px;
            }
            
            .tile-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- Hero Section -->
    <section class="landing-hero-section">
        <div class="landing-hero-content">
            <img src="../images/user/landingpagetile1.png" alt="Premium Tiles" class="center-hero-img">
            <div class="landing-hero-text-overlay">
                <div class="small-text">EYE PLEASING. FEEL BETTER.</div>
                <div class="big-text">CHOOSE YOUR<br>PREMIUM TILES NOW.</div>
                <a href="#featured" class="hero-cta">Explore Collection</a>
            </div>
        </div>
    </section>

    <!-- Premium Banner -->
    <section class="premium-banner">
        <h2>Premium Tiles Collection</h2>
        <p>Browse our extensive collection of premium tiles for every room in your home or business. Discover the perfect combination of style, durability, and elegance.</p>
    </section>

    <!-- Featured Section -->
    <section class="featured-section" id="featured">
        <span class="section-subtitle">Premium Selection</span>
        <h2 class="section-title">Featured Items</h2>
        <p class="section-description">
            Explore our handpicked selection of premium tiles that combine quality
            craftsmanship with exceptional design for your home or business.
        </p>

        <div class="featured-carousel-container">
            <div class="featured-carousel">
                <div class="featured-items">
                    <!-- Items will be populated by JavaScript -->
                </div>
            </div>
            
            <div class="carousel-controls">
                <button class="carousel-arrow prev" aria-label="Previous">
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                <div class="carousel-pagination">
                    <!-- Pagination dots will be populated by JavaScript -->
                </div>
                
                <button class="carousel-arrow next" aria-label="Next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </section>
    
    <!-- Tile Selection Section -->
    <section class="tile-selection-section">
        <span class="section-subtitle">Explore Our Collection</span>
        <h2 class="section-title">Our Tile Selection</h2>
        <p class="section-description">
            From classic ceramics to luxurious natural stone, find the perfect tiles to match
            your style and needs.
        </p>
        
        <div class="tile-selection-grid">
            <div class="tile-selection-item">
                <div class="tile-selection-img-wrap">
                    <img src="../images/user/tile1.jpg" alt="Ceramic Tiles">
                </div>
                <h3>Ceramic Tiles</h3>
                <p>Durable and versatile ceramic tiles for any space</p>
                <button class="add-to-cart-button">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
            </div>
            
            <div class="tile-selection-item">
                <div class="tile-selection-img-wrap">
                    <img src="../images/user/tile2.jpg" alt="Porcelain Tiles">
                </div>
                <h3>Porcelain Tiles</h3>
                <p>Premium quality porcelain for high-end finishes</p>
                <button class="add-to-cart-button">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
            </div>
            
            <div class="tile-selection-item">
                <div class="tile-selection-img-wrap">
                    <img src="../images/user/tile3.jpg" alt="Mosaic Tiles">
                </div>
                <h3>Mosaic Tiles</h3>
                <p>Artistic designs for unique decorative accents</p>
                <button class="add-to-cart-button">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
            </div>
            
            <div class="tile-selection-item">
                <div class="tile-selection-img-wrap">
                    <img src="../images/user/tile4.jpg" alt="Natural Stone">
                </div>
                <h3>Natural Stone</h3>
                <p>Elegant natural stone for luxurious spaces</p>
                <button class="add-to-cart-button">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
            </div>
            
            <div class="tile-selection-item">
                <div class="tile-selection-img-wrap">
                    <img src="../images/user/tile5.jpg" alt="Premium Tiles">
                </div>
                <h3>Premium Tiles</h3>
                <p>High-end premium tiles for luxury spaces</p>
                <button class="add-to-cart-button">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
            </div>
        </div>
    </section>

    <!-- Tile Categories Section -->
    <section class="tile-categories-section">
        <h2 class="section-title" style="text-align: center;">Browse Our Categories</h2>
        <p class="section-description" style="text-align: center;">
            Filter through our extensive collection to find exactly what you're looking for
        </p>
        
        <div class="tile-categories-container">
            <div class="categories-sidebar">
                <h3>Categories</h3>
                <div class="filter-group">
                    <div class="filter-item">
                        <input type="checkbox" id="ceramic-tile-checkbox">
                        <label for="ceramic-tile-checkbox">Ceramic Tile</label>
                    </div>
                    <div class="filter-item">
                        <input type="checkbox" id="porcelain-tile-checkbox">
                        <label for="porcelain-tile-checkbox">Porcelain Tile</label>
                    </div>
                    <div class="filter-item">
                        <input type="checkbox" id="mosaic-tile-checkbox">
                        <label for="mosaic-tile-checkbox">Mosaic Tile</label>
                    </div>
                    <div class="filter-item">
                        <input type="checkbox" id="natural-stone-checkbox">
                        <label for="natural-stone-checkbox">Natural Stone</label>
                    </div>
                    <div class="filter-item">
                        <input type="checkbox" id="outdoor-tiles-checkbox">
                        <label for="outdoor-tiles-checkbox">Outdoor Tiles</label>
                    </div>
                </div>
                
                <h3>Price Range</h3>
                <div class="filter-group">
                    <div class="filter-item">
                        <input type="radio" id="price-under500" name="price-range">
                        <label for="price-under500">Under ₱500</label>
                    </div>
                    <div class="filter-item">
                        <input type="radio" id="price-500-1000" name="price-range">
                        <label for="price-500-1000">₱500 - ₱1000</label>
                    </div>
                    <div class="filter-item">
                        <input type="radio" id="price-1000-2000" name="price-range">
                        <label for="price-1000-2000">₱1000 - ₱2000</label>
                    </div>
                    <div class="filter-item">
                        <input type="radio" id="price-over2000" name="price-range">
                        <label for="price-over2000">Over ₱2000</label>
                    </div>
                </div>
                
                <button class="apply-filters-button">Apply Filters</button>
            </div>
            
            <div class="tile-grid">
                <div class="tile-item">
                    <img src="../images/user/tile1.jpg" alt="Premium Ceramic Tile">
                    <div class="item-details">
                        <div class="item-title">Premium Ceramic Tile</div>
                        <div class="item-price">₱1,250</div>
                    </div>
                    <button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                </div>
                
                <div class="tile-item">
                    <img src="../images/user/tile2.jpg" alt="Porcelain Tile">
                    <div class="item-details">
                        <div class="item-title">Porcelain Tile</div>
                        <div class="item-price">₱950</div>
                    </div>
                    <button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                </div>
                
                <div class="tile-item">
                    <img src="../images/user/tile3.jpg" alt="Mosaic Tile">
                    <div class="item-details">
                        <div class="item-title">Mosaic Tile</div>
                        <div class="item-price">₱1,750</div>
                    </div>
                    <button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                </div>
                
                <div class="tile-item">
                    <img src="../images/user/tile4.jpg" alt="Natural Stone Tile">
                    <div class="item-details">
                        <div class="item-title">Natural Stone Tile</div>
                        <div class="item-price">₱850</div>
                    </div>
                    <button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                </div>
                
                <div class="tile-item">
                    <img src="../images/user/tile5.jpg" alt="Classic Tile">
                    <div class="item-details">
                        <div class="item-title">Classic Tile</div>
                        <div class="item-price">₱2,100</div>
                    </div>
                    <button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                </div>
                
                <div class="tile-item">
                    <img src="../images/user/tile1.jpg" alt="Luxury Tile">
                    <div class="item-details">
                        <div class="item-title">Luxury Tile</div>
                        <div class="item-price">₱2,500</div>
                    </div>
                    <button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Featured items data
        const featuredItemsData = [
            { img: '../images/user/tile1.jpg', title: 'Premium Ceramic Tile', price: '₱1,250' },
            { img: '../images/user/tile2.jpg', title: 'Porcelain Tile', price: '₱950' },
            { img: '../images/user/tile3.jpg', title: 'Mosaic Tile', price: '₱1,750' },
            { img: '../images/user/tile4.jpg', title: 'Natural Stone Tile', price: '₱850' },
            { img: '../images/user/tile5.jpg', title: 'Classic Tile', price: '₱2,100' },
            { img: '../images/user/tile1.jpg', title: 'Luxury Ceramic', price: '₱1,800' },
            { img: '../images/user/tile2.jpg', title: 'Designer Porcelain', price: '₱2,200' },
            { img: '../images/user/tile3.jpg', title: 'Artistic Mosaic', price: '₱1,950' }
        ];

        // Carousel functionality
        document.addEventListener('DOMContentLoaded', function() {
            const featuredItemsContainer = document.querySelector('.featured-items');
            const paginationContainer = document.querySelector('.carousel-pagination');
            const prevButton = document.querySelector('.carousel-arrow.prev');
            const nextButton = document.querySelector('.carousel-arrow.next');
            
            let currentSlide = 0;
            let slidesPerView = calculateSlidesPerView();
            const totalSlides = Math.ceil(featuredItemsData.length / slidesPerView);
            
            // Initialize the carousel
            renderFeaturedItems();
            createPagination();
            updateCarousel();
            
            // Event listeners for navigation
            prevButton.addEventListener('click', () => {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                updateCarousel();
            });
            
            nextButton.addEventListener('click', () => {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateCarousel();
            });
            
            // Handle window resize
            window.addEventListener('resize', () => {
                const newSlidesPerView = calculateSlidesPerView();
                if (newSlidesPerView !== slidesPerView) {
                    slidesPerView = newSlidesPerView;
                    currentSlide = 0;
                    updateCarousel();
                }
            });
            
            // Calculate slides per view based on screen width
            function calculateSlidesPerView() {
                if (window.innerWidth < 576) return 1;
                if (window.innerWidth < 768) return 1;
                if (window.innerWidth < 992) return 2;
                if (window.innerWidth < 1200) return 3;
                return 4;
            }
            
            // Render featured items
            function renderFeaturedItems() {
                featuredItemsContainer.innerHTML = '';
                
                featuredItemsData.forEach(item => {
                    const itemElement = document.createElement('div');
                    itemElement.className = 'featured-item';
                    itemElement.innerHTML = `
                        <div class="featured-img-wrap">
                            <img src="${item.img}" alt="${item.title}">
                        </div>
                        <div class="item-title">${item.title}</div>
                        <div class="item-price">${item.price}</div>
                        <button class="add-to-cart">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    `;
                    featuredItemsContainer.appendChild(itemElement);
                });
            }
            
            // Create pagination dots
            function createPagination() {
                paginationContainer.innerHTML = '';
                
                for (let i = 0; i < totalSlides; i++) {
                    const dot = document.createElement('div');
                    dot.className = 'pagination-dot';
                    if (i === currentSlide) dot.classList.add('active');
                    
                    dot.addEventListener('click', () => {
                        currentSlide = i;
                        updateCarousel();
                    });
                    
                    paginationContainer.appendChild(dot);
                }
            }
            
            // Update carousel position and active dot
            function updateCarousel() {
                const itemWidth = document.querySelector('.featured-item').offsetWidth + 30; // width + gap
                featuredItemsContainer.style.transform = `translateX(-${currentSlide * slidesPerView * itemWidth}px)`;
                
                // Update active pagination dot
                document.querySelectorAll('.pagination-dot').forEach((dot, index) => {
                    if (index === currentSlide) {
                        dot.classList.add('active');
                    } else {
                        dot.classList.remove('active');
                    }
                });
            }
        });
    </script>
</body>
</html>