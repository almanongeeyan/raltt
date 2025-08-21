<?php
// Enforce session and cache control to prevent back navigation after logout
session_start();
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
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
    <title>Rich Anne Lea Tiles Trading - Landing Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>
    <style>
        /* General Styles */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            color: #fff;
        }

        /* Hero Section */
        .landing-hero-section {
            position: relative;
            width: 100vw;
            min-height: 100vh;
            background: url('../images/user/landingpagebackground.PNG') center center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: space-between;
            overflow: hidden;
            padding: 65px 5vw 0 5vw;
            box-sizing: border-box;
        }

        .landing-hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1;
        }

        /* Hero Content Container */
        .landing-hero-content {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 3vw;
        }

        .center-hero-img {
            max-width: 100%;
            max-height: 80vh;
            width: 700px;
            height: auto;
            transform: rotate(40deg);
            filter: drop-shadow(0 4px 15px rgba(0, 0, 0, 0.5));
        }

        /* Text Beside Image */
        .landing-hero-text-overlay {
            flex: 1;
            text-align: left;
            padding-left: 0;
            pointer-events: auto;
        }

        .landing-hero-text-overlay .small-text {
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 1px;
            color: #fff;
            text-shadow: 1px 1px 2px #000;
        }

        .landing-hero-text-overlay .big-text {
            font-size: 3rem;
            font-weight: 900;
            color: #CF8756;
            text-shadow: 2px 2px 8px #000;
            line-height: 1.1;
            margin: 10px 0;
        }

        /* Featured Items Carousel Styles */
        .featured-section {
            background: #f7f7f7;
            color: #222;
            padding: 80px 0;
            text-align: center;
        }

        .featured-section h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 50px;
            letter-spacing: 1px;
            color: #222;
        }

        .featured-carousel {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 32px;
            position: relative;
            max-width: 1800px;
            width: 95vw;
            margin: 0 auto;
        }

        .featured-arrow {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
            color: #888;
            cursor: pointer;
            transition: all 0.2s;
            z-index: 2;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            outline: none;
        }

        .featured-arrow:hover {
            color: #CF8756;
            border-color: #CF8756;
            background: #fdf6f1;
            transform: scale(1.1);
        }

        .featured-items {
            display: flex;
            gap: 32px;
            overflow: hidden;
            width: 100%;
            max-width: 1600px;
            justify-content: center;
            position: relative;
            transition: none;
            padding: 20px 0;
            min-height: 420px;
        }

        /* Slide animations for carousel swaps */
        .slide-left-out { animation: slideLeftOut 0.35s ease both; }
        .slide-left-in { animation: slideLeftIn 0.35s ease both; }
        .slide-right-out { animation: slideRightOut 0.35s ease both; }
        .slide-right-in { animation: slideRightIn 0.35s ease both; }

        @keyframes slideLeftOut {
            0% { transform: translateX(0); opacity: 1; }
            100% { transform: translateX(-40px); opacity: 0; }
        }
        @keyframes slideLeftIn {
            0% { transform: translateX(40px); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideRightOut {
            0% { transform: translateX(0); opacity: 1; }
            100% { transform: translateX(40px); opacity: 0; }
        }
        @keyframes slideRightIn {
            0% { transform: translateX(-40px); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }

        .featured-item {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            padding: 30px 20px;
            width: 220px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: box-shadow 0.3s, transform 0.3s cubic-bezier(0.4, 1.4, 0.6, 1.1);
            border: 1px solid #f0f0f0;
            position: relative;
            z-index: 1;
        }

        .featured-item:hover {
            box-shadow: 0 12px 40px rgba(207, 135, 86, 0.15), 0 4px 15px rgba(0, 0, 0, 0.08);
            transform: translateY(-10px) scale(0.93);
            border-color: #CF8756;
        }

        .featured-img-wrap {
            width: 160px;
            height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fafafa;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .featured-img-wrap img {
            width: 90%;
            height: 90%;
            object-fit: contain;
            border-radius: 15px;
            transition: transform 0.3s ease;
        }

        .featured-item:hover .featured-img-wrap img {
            transform: scale(1.05);
        }

        .featured-item .item-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #333;
            letter-spacing: 0.5px;
        }

        .featured-item .item-price {
            font-size: 1.25rem;
            font-weight: 800;
            color: #CF8756;
            margin-bottom: 20px;
        }

        .featured-item .add-to-cart {
            background: #fff;
            border: 2px solid #CF8756;
            color: #CF8756;
            border-radius: 30px;
            padding: 10px 24px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 6px rgba(207, 135, 86, 0.1);
            outline: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .featured-item .add-to-cart:hover {
            background: #CF8756;
            color: #fff;
            box-shadow: 0 4px 12px rgba(207, 135, 86, 0.3);
            transform: translateY(-2px);
        }

        .featured-pagination {
            margin-top: 35px;
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .featured-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #e0e0e0;
            display: inline-block;
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid #fff;
        }

        .featured-dot.active {
            background: #CF8756;
            box-shadow: 0 2px 8px rgba(207, 135, 86, 0.3);
            border-color: #CF8756;
            transform: scale(1.15);
        }

        /* Tile Categories Section */
        .tile-categories-section {
            background: #270f03ff;
            color: #fff;
            padding: 80px 0;
            text-align: center;
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
            background: url('../images/user/tile_pattern.png') center center/cover no-repeat;
            opacity: 0.1;
            z-index: 1;
        }

        .tile-categories-container {
            position: relative;
            z-index: 2;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
        }

        .section-header {
            margin-bottom: 50px;
        }

        .section-header .small-text {
            font-size: 1.1rem;
            font-weight: 500;
            letter-spacing: 1px;
            opacity: 0.8;
            display: block;
            margin-bottom: 10px;
        }

        .section-header .big-text {
            font-size: 2.5rem;
            font-weight: 900;
            margin: 0;
            line-height: 1.1;
            color: #fff;
        }

        /* Premium Tiles banner above grid */
        .premium-banner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            background: linear-gradient(90deg, rgba(207,135,86,0.25), rgba(125,49,10,0.35));
            border: 1px solid rgba(255,255,255,0.15);
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            border-radius: 16px;
            padding: 16px 20px;
            margin: 0 auto 28px;
            max-width: 900px;
            text-align: left;
        }
        .premium-banner .icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #CF8756;
            color: #fff;
            box-shadow: 0 6px 18px rgba(207,135,86,0.45);
            flex-shrink: 0;
        }
        .premium-banner .title {
            font-size: 1.2rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: 0.4px;
        }
        .premium-banner .subtitle {
            font-size: 0.95rem;
            color: #f0e7e2;
            opacity: 0.95;
        }

        .tile-categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            justify-content: center;
        }

        .tile-category {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }

        .tile-category:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
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
            transition: transform 0.5s;
        }

        .tile-category:hover .tile-category-img img {
            transform: scale(1.05);
        }

        .tile-category-content {
            padding: 20px;
            text-align: center;
            background: #fff;
        }

        .tile-category-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .tile-category-desc {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 15px;
        }

        .explore-btn {
            display: inline-block;
            padding: 8px 20px;
            background: #7D310A;
            color: white;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }

        .explore-btn:hover {
            background: #5D2408;
            transform: translateY(-2px);
        }

        /* Media Queries */
        @media (max-width: 1200px) {
            .featured-items {
                max-width: 900px;
                gap: 20px;
            }
            .featured-carousel {
                max-width: 1100px;
            }
            .featured-item {
                width: 200px;
                padding: 25px 15px;
            }
            .featured-img-wrap {
                width: 140px;
                height: 140px;
            }
            .tile-categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            }
        }

        @media (max-width: 900px) {
            .landing-hero-section {
                flex-direction: column;
                justify-content: center;
                padding-top: 100px;
                padding-bottom: 50px;
            }
            .landing-hero-content {
                flex-direction: column;
                align-items: center;
                gap: 1.5rem;
                padding: 0 2vw;
            }
            .center-hero-img {
                max-height: 45vh;
                transform: rotate(25deg);
                margin-top: 5rem;
            }
            .landing-hero-text-overlay {
                text-align: center;
                padding: 0;
            }
            .landing-hero-text-overlay .big-text {
                font-size: 2.5rem;
            }
            .featured-section h2 {
                font-size: 2rem;
                margin-bottom: 40px;
            }
            .featured-items {
                max-width: 600px;
            }
            .featured-carousel {
                max-width: 700px;
            }
            .tile-categories-container {
                padding: 0 20px;
            }
        }

        @media (max-width: 700px) {
            .featured-items {
                max-width: 400px;
                gap: 16px;
            }
            .featured-carousel {
                max-width: 420px;
            }
            .featured-item {
                width: 180px;
            }
            .featured-carousel {
                gap: 10px;
            }
            .featured-arrow {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
            .featured-img-wrap {
                width: 120px;
                height: 120px;
            }
            .tile-categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media (max-width: 500px) {
            .landing-hero-section {
                padding: 100px 2vw 20px;
                min-height: auto;
            }
            .landing-hero-content {
                padding: 0;
            }
            .center-hero-img {
                max-height: 35vh;
                transform: rotate(15deg);
                margin-top: 1rem;
            }
            .landing-hero-text-overlay .small-text {
                font-size: 1rem;
            }
            .landing-hero-text-overlay .big-text {
                font-size: 1.8rem;
            }
            .featured-items {
                max-width: 250px;
                gap: 0;
            }
            .featured-item {
                width: 220px;
            }
            .featured-section {
                padding: 40px 0;
            }
            .featured-dot {
                width: 8px;
                height: 8px;
            }
            .section-header .big-text {
                font-size: 2rem;
            }
            .tile-category-img {
                height: 160px;
            }
        }
    </style>
</head>

<body>
    <?php
    // Extra check in body to force redirect if session is not valid
    if (!isset($_SESSION['logged_in'])) {
        echo "<script>window.location.href='../connection/tresspass.php';</script>";
        exit();
    }
    ?>
    <script>
    // Detect browser back navigation and force check for session
    window.addEventListener('pageshow', function(event) {
        if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
            fetch(window.location.href, {cache: 'reload', credentials: 'same-origin'})
                .catch(() => {
                    window.location.href = '../connection/tresspass.php';
                });
        }
    });
    </script>
    <section class="landing-hero-section">
        <div class="landing-hero-content">
            <img src="../images/user/landingpagetile1.png" alt="Landing Tile" class="center-hero-img">
            <div class="landing-hero-text-overlay">
                <div class="small-text">STYLE IN YOUR EVERY STEP.</div>
                <div class="big-text">CHOOSE YOUR<br>TILES NOW.</div>
            </div>
        </div>
    </section>

    <section class="featured-section">
        <h2>FEATURED ITEMS</h2>
        <div class="featured-carousel">
            <button class="featured-arrow prev" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
            <div class="featured-items"></div>
            <button class="featured-arrow next" aria-label="Next"><i class="fas fa-chevron-right"></i></button>
        </div>
        <div class="featured-pagination"></div>
    </section>

    <section class="tile-categories-section">
        <div class="tile-categories-container">
            <div class="section-header">
                <span class="small-text">Explore Our Collection</span>
                <h2 class="big-text">Our Tile Selection</h2>
            </div>
            <div class="premium-banner" role="note" aria-label="Premium Tiles">
                <div class="icon" aria-hidden="true"><i class="fa-solid fa-gem"></i></div>
                <div class="text">
                    <div class="title">Premium Tiles</div>
                    <div class="subtitle">Browse our extensive collection of premium tiles for every room in your home or business.</div>
                </div>
            </div>
            
            <div class="tile-categories-grid">
                <div class="tile-category">
                    <div class="tile-category-img">
                        <img src="../images/user/tile3.jpg" alt="Ceramic Tiles">
                    </div>
                    <div class="tile-category-content">
                        <h3 class="tile-category-title">Ceramic Tiles</h3>
                        <p class="tile-category-desc">Durable and versatile ceramic tiles for any space</p>
                        <a href="#" class="explore-btn">Explore</a>
                    </div>
                </div>
                
                <div class="tile-category">
                    <div class="tile-category-img">
                        <img src="../images/user/tile4.jpg" alt="Porcelain Tiles">
                    </div>
                    <div class="tile-category-content">
                        <h3 class="tile-category-title">Porcelain Tiles</h3>
                        <p class="tile-category-desc">Premium quality porcelain for high-end finishes</p>
                        <a href="#" class="explore-btn">Explore</a>
                    </div>
                </div>
                
                <div class="tile-category">
                    <div class="tile-category-img">
                        <img src="../images/user/tile5.jpg" alt="Mosaic Tiles">
                    </div>
                    <div class="tile-category-content">
                        <h3 class="tile-category-title">Mosaic Tiles</h3>
                        <p class="tile-category-desc">Artistic designs for unique decorative accents</p>
                        <a href="#" class="explore-btn">Explore</a>
                    </div>
                </div>
                
                <div class="tile-category">
                    <div class="tile-category-img">
                        <img src="../images/user/tile2.jpg" alt="Natural Stone Tiles">
                    </div>
                    <div class="tile-category-content">
                        <h3 class="tile-category-title">Natural Stone</h3>
                        <p class="tile-category-desc">Elegant natural stone for luxurious spaces</p>
                        <a href="#" class="explore-btn">Explore</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        const featuredItems = [{
            img: '../images/user/tile1.jpg',
            title: 'Premium Ceramic Tile',
            price: '₱1,250',
        }, {
            img: '../images/user/tile2.jpg',
            title: 'Porcelain Tile',
            price: '₱950',
        }, {
            img: '../images/user/tile3.jpg',
            title: 'Mosaic Tile',
            price: '₱1,750',
        }, {
            img: '../images/user/tile4.jpg',
            title: 'Natural Stone Tile',
            price: '₱850',
        }, {
            img: '../images/user/tile5.jpg',
            title: 'Classic Tile',
            price: '₱2,100',
        }];

        const itemsPerPage = () => {
            if (window.innerWidth <= 600) return 1;
            if (window.innerWidth <= 900) return 2;
            if (window.innerWidth <= 1200) return 3;
            return 4;
        };

        let currentPage = 0;
        let animating = false;

        function renderFeaturedItems(direction = 0) {
            const container = document.querySelector('.featured-items');
            if (!container) return;
            const perPage = itemsPerPage();
            const pageCount = Math.ceil(featuredItems.length / perPage);

            if (currentPage < 0) currentPage = pageCount - 1;
            if (currentPage >= pageCount) currentPage = 0;

            const start = currentPage * perPage;
            const end = start + perPage;

            if (direction !== 0 && !animating) {
                animating = true;
                container.classList.add(direction > 0 ? 'slide-left-out' : 'slide-right-out');
                setTimeout(() => {
                    container.classList.remove('slide-left-out', 'slide-right-out');
                    updateFeaturedItems(container, start, end);
                    container.classList.add(direction > 0 ? 'slide-left-in' : 'slide-right-in');
                    setTimeout(() => {
                        container.classList.remove('slide-left-in', 'slide-right-in');
                        animating = false;
                    }, 350);
                }, 350);
            } else {
                updateFeaturedItems(container, start, end);
            }
            renderPagination();
        }

        function updateFeaturedItems(container, start, end) {
            container.innerHTML = '';
            const itemsToRender = featuredItems.slice(start, end);

            // Handle cases where the last page has fewer items
            while (itemsToRender.length < itemsPerPage()) {
                itemsToRender.push({
                    img: '',
                    title: '',
                    price: '',
                    isEmpty: true
                });
            }

            itemsToRender.forEach(item => {
                const div = document.createElement('div');
                div.className = 'featured-item';
                if (item.isEmpty) {
                    div.classList.add('empty');
                } else {
                    div.innerHTML = `
                        <div class="featured-img-wrap">
                            <img src="${item.img}" alt="${item.title}" loading="lazy">
                        </div>
                        <div class="item-title">${item.title}</div>
                        <div class="item-price">${item.price}</div>
                        <button class="add-to-cart"><i class="fa fa-lock"></i> ADD TO CART</button>
                    `;
                }
                container.appendChild(div);
            });
        }

        function renderPagination() {
            const perPage = itemsPerPage();
            const pageCount = Math.ceil(featuredItems.length / perPage);
            const pagination = document.querySelector('.featured-pagination');
            if (!pagination) return;
            pagination.innerHTML = '';
            for (let i = 0; i < pageCount; i++) {
                const dot = document.createElement('span');
                dot.className = 'featured-dot' + (i === currentPage ? ' active' : '');
                dot.title = `Show items ${i * perPage + 1} - ${Math.min((i + 1) * perPage, featuredItems.length)}`;
                dot.onclick = () => {
                    if (animating || i === currentPage) return;
                    const direction = i > currentPage ? 1 : -1;
                    currentPage = i;
                    renderFeaturedItems(direction);
                };
                pagination.appendChild(dot);
            }
        }

        function nextFeatured() {
            if (animating) return;
            const perPage = itemsPerPage();
            const pageCount = Math.ceil(featuredItems.length / perPage);
            currentPage = (currentPage + 1) % pageCount;
            renderFeaturedItems(1);
        }

        function prevFeatured() {
            if (animating) return;
            const perPage = itemsPerPage();
            const pageCount = Math.ceil(featuredItems.length / perPage);
            currentPage = (currentPage - 1 + pageCount) % pageCount;
            renderFeaturedItems(-1);
        }

        window.addEventListener('resize', () => {
            const newPerPage = itemsPerPage();
            const oldPerPage = document.querySelectorAll('.featured-item:not(.empty)').length;
            if (newPerPage !== oldPerPage) {
                currentPage = 0;
                renderFeaturedItems();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const nextBtn = document.querySelector('.featured-arrow.next');
            const prevBtn = document.querySelector('.featured-arrow.prev');
            if (nextBtn) nextBtn.onclick = nextFeatured;
            if (prevBtn) prevBtn.onclick = prevFeatured;
            renderFeaturedItems();
        });
    </script>
</body>

</html>