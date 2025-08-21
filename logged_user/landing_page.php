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
        :root {
            --primary: #7D310A;
            --secondary: #CF8756;
            --dark: #270F03;
            --light: #F7F7F7;
            --text: #222;
            --muted: #777;
            --gap: 16px;
        }

        * { box-sizing: border-box; }

        /* General Styles */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            color: var(--text);
            background: #fff;
        }

        /* Hero Section */
        .landing-hero-section {
            position: relative;
            width: 100vw;
            min-height: 70vh;
            background: linear-gradient(rgba(0,0,0,.6), rgba(0,0,0,.6)), url('../images/user/landingpagebackground.PNG') center center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 65px 5vw 40px 5vw;
        }

        .landing-hero-content { display: grid; grid-template-columns: 1fr 1fr; gap: 3vw; width: 100%; align-items: center; }
        .center-hero-img { max-width: 100%; width: 620px; height: auto; transform: rotate(32deg); filter: drop-shadow(0 4px 15px rgba(0,0,0,.5)); justify-self: center; }
        .landing-hero-text-overlay { color: #fff; }
        .landing-hero-text-overlay .small-text { font-size: 1.1rem; font-weight: 600; letter-spacing: .5px; opacity: .95; }
        .landing-hero-text-overlay .big-text { font-size: clamp(2rem, 4vw, 3rem); font-weight: 900; color: var(--secondary); line-height: 1.1; margin: 10px 0; }

        /* Featured Items Carousel Styles */
        .featured-section { background: var(--light); color: var(--text); padding: 64px 0; text-align: center; }
        .featured-section h2 { font-size: clamp(1.6rem, 2.5vw, 2.4rem); margin: 0 0 28px; font-weight: 900; color: var(--primary); }
        .featured-carousel { display: grid; grid-template-columns: 48px 1fr 48px; align-items: center; gap: 16px; max-width: 1200px; width: 92vw; margin: 0 auto; }
        .featured-arrow { background: #fff; border: 1px solid #ddd; border-radius: 50%; width: 48px; height: 48px; display: grid; place-items: center; color: var(--primary); cursor: pointer; transition: .2s ease; }
        .featured-arrow:hover { background: var(--primary); color: #fff; transform: scale(1.05); }
        .featured-items { display: flex; gap: var(--gap); width: 100%; overflow: hidden; padding: 8px 0; --perPage: 4; }
        .featured-item { width: calc((100% - (var(--gap) * (var(--perPage) - 1))) / var(--perPage)); background: #fff; border-radius: 16px; box-shadow: 0 6px 20px rgba(0,0,0,.08); padding: 18px; display: flex; flex-direction: column; align-items: center; border: 1px solid #f0f0f0; transition: box-shadow .25s ease, transform .25s ease; }
        .featured-item:hover { box-shadow: 0 12px 32px rgba(0,0,0,.14); transform: translateY(-6px); }
        .featured-img-wrap { width: 140px; height: 140px; border-radius: 12px; background: #fafafa; display: grid; place-items: center; margin-bottom: 14px; overflow: hidden; }
        .featured-img-wrap img { width: 90%; height: 90%; object-fit: contain; display: block; }
        .item-title { font-weight: 700; font-size: .98rem; margin: 4px 0 6px; color: #333; text-align: center; }
        .item-price { font-weight: 800; color: var(--secondary); margin-bottom: 12px; }
        .add-to-cart { background: var(--primary); color: #fff; border: 2px solid var(--primary); border-radius: 30px; padding: 8px 16px; font-weight: 700; cursor: pointer; transition: .2s ease; display: inline-flex; align-items: center; gap: 8px; }
        .add-to-cart:hover { background: var(--secondary); border-color: var(--secondary); }
        .featured-pagination { display: flex; justify-content: center; gap: 10px; margin-top: 18px; }
        .featured-dot { width: 9px; height: 9px; border-radius: 50%; background: #ddd; border: 2px solid #fff; cursor: pointer; transition: .2s ease; }
        .featured-dot.active { background: var(--primary); transform: scale(1.1); box-shadow: 0 0 0 3px rgba(125,49,10,.08); }

        /* Slide animations used by JS */
        @keyframes slideLeftOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(-20px); opacity: .95; } }
        @keyframes slideLeftIn { from { transform: translateX(20px); opacity: .95; } to { transform: translateX(0); opacity: 1; } }
        @keyframes slideRightOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(20px); opacity: .95; } }
        @keyframes slideRightIn { from { transform: translateX(-20px); opacity: .95; } to { transform: translateX(0); opacity: 1; } }
        .slide-left-out { animation: slideLeftOut .35s ease forwards; }
        .slide-left-in { animation: slideLeftIn .35s ease forwards; }
        .slide-right-out { animation: slideRightOut .35s ease forwards; }
        .slide-right-in { animation: slideRightIn .35s ease forwards; }

        /* Tile Categories Section */
        .tile-categories-section { background: var(--dark); color: #fff; padding: 64px 0; position: relative; overflow: hidden; }
        .tile-categories-section .section-header { text-align: center; margin: 0 0 28px; }
        .section-header .small-text { display: block; font-size: 1rem; color: #ddd; }
        .section-header .big-text { margin: 6px 0 0; font-size: clamp(1.6rem, 2.5vw, 2.2rem); font-weight: 900; color: #fff; text-shadow: -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000, 1px 1px 0 #000; }
        .tile-categories-container { max-width: 1200px; width: 92vw; margin: 0 auto; }
        .tile-categories-grid { display: flex; flex-wrap: nowrap; gap: 16px; align-items: stretch; }
        .tile-category { display: block; color: inherit; text-decoration: none; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,.15); transition: transform .25s ease, box-shadow .25s ease; flex: 1 1 0; min-width: 0; }
        .tile-category:focus-visible { outline: 3px solid var(--secondary); outline-offset: 2px; }
        .tile-category:hover { transform: translateY(-6px); box-shadow: 0 16px 36px rgba(0,0,0,.22); }
        .tile-category-img { height: 180px; overflow: hidden; }
        .tile-category-img img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .4s ease; }
        .tile-category:hover .tile-category-img img { transform: scale(1.05); }
        .tile-category-content { padding: 16px; text-align: center; background: #fff; }
        .tile-category-title { margin: 0 0 8px; font-weight: 800; color: #333; font-size: 1.05rem; }
        .tile-category-desc { margin: 0 0 12px; color: #666; font-size: .9rem; }
        .explore-btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 30px; background: var(--primary); color: #fff; font-weight: 700; border: none; cursor: pointer; }
        .explore-btn:hover { background: var(--secondary); }

        /* Responsive */
        @media (max-width: 1100px) {
            .landing-hero-content { grid-template-columns: 1fr; text-align: center; }
            .center-hero-img { justify-self: center; transform: rotate(22deg); max-width: 70%; }
        }

        @media (max-width: 900px) {
            .featured-items { --gap: 14px; }
            .tile-category-img { height: 160px; }
        }

        @media (max-width: 700px) {
            .featured-items { --gap: 12px; }
            .featured-img-wrap { width: 120px; height: 120px; }
        }

        @media (max-width: 520px) {
            .featured-img-wrap { width: 96px; height: 96px; }
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
            
            <div class="tile-categories-grid">
                <a class="tile-category" href="#">
                    <div class="tile-category-img">
                        <img src="../images/user/tile3.jpg" alt="Ceramic Tiles">
                    </div>
                    <div class="tile-category-content">
                        <h3 class="tile-category-title">Ceramic Tiles</h3>
                        <p class="tile-category-desc">Durable and versatile ceramic tiles for any space</p>
                        <span class="explore-btn"><i class="fa fa-arrow-right"></i> Explore</span>
                    </div>
                </a>
                
                <a class="tile-category" href="#">
                    <div class="tile-category-img">
                        <img src="../images/user/tile4.jpg" alt="Porcelain Tiles">
                    </div>
                    <div class="tile-category-content">
                        <h3 class="tile-category-title">Porcelain Tiles</h3>
                        <p class="tile-category-desc">Premium quality porcelain for high-end finishes</p>
                        <span class="explore-btn"><i class="fa fa-arrow-right"></i> Explore</span>
                    </div>
                </a>
                
                <a class="tile-category" href="#">
                    <div class="tile-category-img">
                        <img src="../images/user/tile5.jpg" alt="Mosaic Tiles">
                    </div>
                    <div class="tile-category-content">
                        <h3 class="tile-category-title">Mosaic Tiles</h3>
                        <p class="tile-category-desc">Artistic designs for unique decorative accents</p>
                        <span class="explore-btn"><i class="fa fa-arrow-right"></i> Explore</span>
                    </div>
                </a>
                
                <a class="tile-category" href="#">
                    <div class="tile-category-img">
                        <img src="../images/user/tile2.jpg" alt="Natural Stone Tiles">
                    </div>
                    <div class="tile-category-content">
                        <h3 class="tile-category-title">Natural Stone</h3>
                        <p class="tile-category-desc">Elegant natural stone for luxurious spaces</p>
                        <span class="explore-btn"><i class="fa fa-arrow-right"></i> Explore</span>
                    </div>
                </a>
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
            if (window.innerWidth <= 900) return 3; // mobile/tablet: 3 per slide
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

            // reflect perPage into CSS variable for width calc
            container.style.setProperty('--perPage', perPage);

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
                    div.style.visibility = 'hidden';
                } else {
                    div.innerHTML = `
                        <div class=\"featured-img-wrap\">
                            <img src=\"${item.img}\" alt=\"${item.title}\" loading=\"lazy\">
                        </div>
                        <div class=\"item-title\">${item.title}</div>
                        <div class=\"item-price\">${item.price}</div>
                        <button class=\"add-to-cart\"><i class=\"fa fa-shopping-cart\"></i> Add to Cart</button>
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