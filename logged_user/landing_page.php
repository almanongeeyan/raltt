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
header("Content-Security-Policy: default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval'; img-src 'self' https: data:; style-src 'self' https: 'unsafe-inline'; script-src 'self' https: 'unsafe-inline' 'unsafe-eval';");

if (!isset($_SESSION['logged_in'])) {
    header('Location: ../connection/tresspass.php');
    exit();
}
include '../includes/headeruser.php';
?>
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
            --primary-color: #7D310A;
            --secondary-color: #CF8756;
            --accent-color: #E8A56A;
            --dark-color: #270f03;
            --light-color: #f7f7f7;
            --text-dark: #222;
            --text-light: #666;
        }

        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; overflow-x: hidden; color: var(--text-dark); background: var(--light-color); }

        /* Hero Section */
        .landing-hero-section {
            position: relative;
            width: 100vw;
            background: linear-gradient(rgba(0,0,0,.85), rgba(0,0,0,.75)), url('../images/user/landingpagebackground.PNG') center center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: space-between;
            overflow: hidden;
            padding: 65px 5vw 0 5vw;
        }
        .landing-hero-content { position: relative; z-index: 2; flex: 1; display: flex; align-items: center; justify-content: flex-start; gap: 3vw; }
        .center-hero-img { max-width: 100%; max-height: 80vh; width: 700px; height: auto; transform: rotate(40deg); filter: drop-shadow(0 4px 15px rgba(0,0,0,.5)); animation: float 6s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: rotate(40deg) translateY(0); } 50% { transform: rotate(40deg) translateY(-18px); } }
        .landing-hero-text-overlay { flex: 1; text-align: left; padding-left: 0; pointer-events: auto; }
        .landing-hero-text-overlay .small-text { font-size: 1.05rem; font-weight: 600; letter-spacing: 1px; color: #fff; text-shadow: 1px 1px 2px #000; opacity: .95; }
        .landing-hero-text-overlay .big-text { font-size: 3rem; font-weight: 900; color: var(--secondary-color); text-shadow: 2px 2px 8px #000; line-height: 1.1; margin: 10px 0; }
        .landing-hero-text-overlay .description { font-size: 1.05rem; color: #fff; margin-top: 16px; max-width: 520px; line-height: 1.6; margin-bottom: 24px; opacity: .95; }
        .primary-btn { display:inline-block; padding:10px 24px; background:var(--primary-color); color:#fff; border-radius:30px; font-weight:700; text-decoration:none; transition: all .25s; border:none; cursor:pointer; }
        .primary-btn:hover { background: var(--secondary-color); transform: translateY(-2px); box-shadow: 0 6px 16px rgba(207,135,86,.3); }

        /* Featured Section */
        .featured-section { background:#fff; color:var(--text-dark); padding:80px 0; text-align:center; }
        .section-header { margin-bottom: 30px; }
        .section-header .small-text { font-size:.95rem; font-weight:600; color:var(--secondary-color); letter-spacing:1px; text-transform:uppercase; margin-bottom:6px; display:block; }
        .section-header .big-text { font-size:2.6rem; font-weight:900; margin:0; color:var(--text-dark); letter-spacing:.5px; }
        .section-header .description { font-size:1rem; color:var(--text-light); max-width:640px; margin:14px auto 0; line-height:1.6; }

        .featured-carousel { display:flex; align-items:center; justify-content:center; gap:18px; position:relative; max-width:1400px; margin: 28px auto 0; padding: 0 10px; }
        .featured-arrow { background:#fff; border:1px solid #e6e6e6; border-radius:50%; width:50px; height:50px; font-size:1.2rem; color:var(--primary-color); cursor:pointer; transition: all .2s; box-shadow:0 4px 12px rgba(0,0,0,.06); display:flex; align-items:center; justify-content:center; }
        .featured-arrow:hover { background:var(--primary-color); color:#fff; transform: scale(1.08); }
        .featured-items-container { width:100%; max-width:1100px; overflow:hidden; position:relative; }
        .featured-items { display:flex; gap:22px; width:100%; transition: transform .45s cubic-bezier(.4,0,.2,1); padding: 8px 2px; min-height: 390px; }
        .featured-item { background:#fff; border-radius:18px; box-shadow:0 8px 22px rgba(0,0,0,.06); padding:24px 18px; width:220px; flex-shrink:0; display:flex; flex-direction:column; align-items:center; border:1px solid #f2f2f2; opacity:0; transform: translateY(18px); animation: fadeInUp .5s forwards; }
        @keyframes fadeInUp { to { opacity:1; transform: translateY(0);} }
        .featured-item:hover { box-shadow: 0 14px 38px rgba(207,135,86,.18), 0 4px 15px rgba(0,0,0,.06); transform: translateY(-8px); }
        .featured-img-wrap { width:160px; height:160px; display:flex; align-items:center; justify-content:center; background:#fafafa; border-radius:14px; margin-bottom:18px; overflow:hidden; }
        .featured-img-wrap img { width:92%; height:92%; object-fit:contain; transition: transform .35s; }
        .featured-item:hover .featured-img-wrap img { transform: scale(1.05); }
        .item-title { font-size:1.05rem; font-weight:800; margin: 6px 0 6px; color:#333; letter-spacing:.3px; text-align:center; }
        .item-price { font-size:1.15rem; font-weight:900; color:var(--secondary-color); margin-bottom:14px; }
        .add-to-cart { background:#fff; border:2px solid var(--secondary-color); color:var(--secondary-color); border-radius:28px; padding:9px 18px; font-size:.9rem; font-weight:800; cursor:pointer; transition: all .25s; display:flex; align-items:center; gap:8px; }
        .add-to-cart:hover { background:var(--secondary-color); color:#fff; box-shadow:0 6px 14px rgba(207,135,86,.28); transform: translateY(-2px); }

        .featured-pagination { margin-top: 22px; display:flex; justify-content:center; gap:10px; }
        .featured-dot { width:10px; height:10px; border-radius:50%; background:#e0e0e0; border:2px solid #fff; cursor:pointer; transition: all .2s; }
        .featured-dot.active { background: var(--primary-color); transform: scale(1.2); box-shadow: 0 2px 8px rgba(125,49,10,.28); }

        .slide-left-out { animation: slideLeftOut .32s forwards; } .slide-left-in { animation: slideLeftIn .32s forwards; } .slide-right-out { animation: slideRightOut .32s forwards; } .slide-right-in { animation: slideRightIn .32s forwards; }
        @keyframes slideLeftOut { from{transform:translateX(0);opacity:1;} to{transform:translateX(-40px);opacity:0;} }
        @keyframes slideLeftIn { from{transform:translateX(40px);opacity:0;} to{transform:translateX(0);opacity:1;} }
        @keyframes slideRightOut { from{transform:translateX(0);opacity:1;} to{transform:translateX(40px);opacity:0;} }
        @keyframes slideRightIn { from{transform:translateX(-40px);opacity:0;} to{transform:translateX(0);opacity:1;} }

        /* Our Tile Selection Section */
        .tile-selection-section { background: #28180f; color:#fff; padding:80px 5vw; text-align:center; }
        .tile-selection-section .explore-collection-text { font-size:.95rem; font-weight:600; color: var(--secondary-color); text-transform:uppercase; letter-spacing:1px; margin-bottom:8px; }
        .tile-selection-section h2 { font-size:2.4rem; font-weight:900; margin:0 0 10px; }
        .tile-selection-section .description-text { font-size:1rem; color:#dcdcdc; max-width:620px; margin:10px auto 34px; line-height:1.6; }
        .tile-selection-grid { display:flex; justify-content:center; align-items:stretch; gap:26px; flex-wrap:wrap; }
        .tile-selection-item { background:#3b2415; border-radius:16px; box-shadow:0 8px 24px rgba(0,0,0,.24); padding:18px; width:210px; display:flex; flex-direction:column; align-items:center; transition:.28s; cursor:pointer; opacity:0; transform: translateY(16px); }
        .tile-selection-item.animate { animation: fadeInUp .5s forwards; }
        .tile-selection-item:hover { transform: translateY(-6px); box-shadow:0 12px 30px rgba(0,0,0,.3); }
        .tile-selection-img-wrap { width:100%; height:150px; display:flex; align-items:center; justify-content:center; background:#4e3220; border-radius:12px; overflow:hidden; margin-bottom:14px; }
        .tile-selection-img-wrap img { width:100%; height:100%; object-fit:cover; }
        .tile-selection-item h3 { font-size:1.05rem; font-weight:800; color:#fff; margin:2px 0 8px; }
        .tile-selection-item p { font-size:.92rem; color:#d6d6d6; text-align:center; line-height:1.45; }
        .tile-selection-item .add-to-cart-button { background:var(--secondary-color); color:#fff; border:none; border-radius:22px; padding:9px 16px; font-size:.9rem; font-weight:800; cursor:pointer; transition: .25s; display:flex; align-items:center; gap:8px; margin-top:12px; width:100%; justify-content:center; }
        .tile-selection-item .add-to-cart-button:hover { background:#e09462; }

        /* Tile Categories + Grid */
        .tile-categories-section { background: #fff; color:var(--text-dark); padding:80px 5vw; }
        .tile-categories-container { display:flex; gap:36px; max-width:1400px; margin:0 auto; align-items:flex-start; }
        .categories-sidebar { width:260px; background:#fff; padding:22px; border-radius:14px; box-shadow:0 4px 14px rgba(0,0,0,.06); }
        .categories-sidebar h3 { font-size:1.06rem; font-weight:800; margin:0 0 12px; color:var(--secondary-color); }
        .categories-sidebar ul { list-style:none; padding:0; margin:0 0 18px; }
        .categories-sidebar li { margin-bottom:12px; display:flex; align-items:center; }
        .categories-sidebar label { font-size:.98rem; font-weight:600; color:#444; cursor:pointer; margin-left:10px; }
        .categories-sidebar input[type="checkbox"], .categories-sidebar input[type="radio"] { appearance:none; width:18px; height:18px; border:2px solid #ccc; border-radius:4px; cursor:pointer; position:relative; top:2px; outline:none; transition:.2s; }
        .categories-sidebar input[type="checkbox"]:checked, .categories-sidebar input[type="radio"]:checked { background:var(--secondary-color); border-color:var(--secondary-color); }
        .categories-sidebar input[type="radio"] { border-radius:50%; }
        .apply-filters-button { background:var(--secondary-color); color:#fff; border:none; border-radius:28px; padding:11px 20px; font-size:.98rem; font-weight:800; cursor:pointer; width:100%; transition:.25s; box-shadow:0 4px 12px rgba(207,135,86,.28); }
        .apply-filters-button:hover { background:#e09462; }

        .tile-grid-area { flex:1; }
        .tiles-header { text-align:left; margin: 0 0 18px 0; }
        .tiles-header .small-text { font-size:.95rem; font-weight:600; color:var(--secondary-color); text-transform:uppercase; letter-spacing:1px; display:block; margin-bottom:6px; }
        .tiles-header .big-text { font-size:2.1rem; font-weight:900; margin:0; color:var(--text-dark); }
        .tiles-header .description { font-size:1rem; color:var(--text-light); margin-top:8px; }

        .tile-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 22px; }
        .tile-item { background:#fff; border-radius:18px; box-shadow:0 8px 20px rgba(0,0,0,.06); padding:18px; text-align:center; display:flex; flex-direction:column; align-items:center; border:1px solid #f2f2f2; opacity:0; transform: translateY(16px); }
        .tile-item.animate { animation: fadeInUp .5s forwards; }
        .tile-item:hover { box-shadow: 0 12px 34px rgba(207,135,86,.16), 0 4px 12px rgba(0,0,0,.06); transform: translateY(-4px); }
        .tile-item img { width:100%; height:180px; object-fit:cover; border-radius:12px; margin-bottom:12px; transition: transform .35s; }
        .tile-item:hover img { transform: scale(1.04); }
        .tile-item .item-details { width:100%; margin-bottom:12px; }
        .tile-item .item-title { font-size:1.02rem; font-weight:800; color:#333; margin-bottom:6px; letter-spacing:.3px; }
        .tile-item .item-price { font-size:1.1rem; font-weight:900; color:var(--secondary-color); }
        .tile-item .add-to-cart-button { background: var(--secondary-color); color:#fff; border:none; border-radius:26px; padding:10px 18px; font-size:.92rem; font-weight:800; cursor:pointer; transition:.25s; box-shadow:0 2px 6px rgba(207,135,86,.18); display:flex; align-items:center; gap:8px; justify-content:center; width:100%; }
        .tile-item .add-to-cart-button:hover { background:#e09462; transform: translateY(-2px); box-shadow:0 6px 14px rgba(207,135,86,.28); }

        /* Responsive */
        @media (max-width: 1200px) { .featured-items { gap:18px; } .featured-item { width:200px; } .featured-img-wrap { width:140px; height:140px; } .categories-sidebar { width:220px; } }
        @media (max-width: 992px) {
            .landing-hero-section { flex-direction:column; justify-content:center; padding-top:100px; padding-bottom:50px; }
            .landing-hero-content { flex-direction:column; align-items:center; gap:1.2rem; }
            .center-hero-img { max-height:45vh; transform: rotate(25deg); margin-top:4rem; }
            .landing-hero-text-overlay { text-align:center; }
            .landing-hero-text-overlay .big-text { font-size:2.4rem; }
            .featured-items-container { max-width:720px; }
            .tile-categories-container { flex-direction:column; }
            .categories-sidebar { width:100%; max-width:520px; }
        }
        @media (max-width: 768px) { .featured-items-container { max-width: 520px; } .featured-item { width:180px; } .featured-arrow { width:42px; height:42px; font-size:1.05rem; } }
        @media (max-width: 576px) {
            .landing-hero-section { padding: 100px 2vw 20px; }
            .center-hero-img { max-height:35vh; transform: rotate(15deg); margin-top:1rem; }
            .landing-hero-text-overlay .small-text { font-size: .95rem; }
            .landing-hero-text-overlay .big-text { font-size: 1.9rem; }
            .featured-items-container { max-width: 320px; }
            .featured-item { width:220px; }
            .tile-grid { grid-template-columns: 1fr; }
        }
    </style>
    <script>
        // Prevent showing cached page on back
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || (window.performance && window.performance.navigation && window.performance.navigation.type === 2)) {
                location.reload();
            }
        });
    </script>
</head>

<body>
    <section class="landing-hero-section">
        <div class="landing-hero-content">
            <img src="../images/user/landingpagetile1.png" alt="Landing Tile" class="center-hero-img">
            <div class="landing-hero-text-overlay">
                <div class="small-text">EYE PLEASING. FEEL BETTER.</div>
                <div class="big-text">CHOOSE YOUR<br>TILES NOW.</div>
                <div class="description">Discover our premium collection of tiles that combine elegance, durability, and style to transform any space into a masterpiece.</div>
                <a href="#tiles-grid" class="primary-btn">Shop Now</a>
            </div>
        </div>
    </section>

    <section class="featured-section">
        <div class="section-header">
            <span class="small-text">Premium Selection</span>
            <h2 class="big-text">Featured Items</h2>
            <div class="description">Explore our handpicked selection of premium tiles that combine quality craftsmanship with exceptional design for your home or business.</div>
        </div>
        <div class="featured-carousel">
            <button class="featured-arrow prev" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
            <div class="featured-items-container">
                <div class="featured-items"></div>
            </div>
            <button class="featured-arrow next" aria-label="Next"><i class="fas fa-chevron-right"></i></button>
        </div>
        <div class="featured-pagination"></div>
    </section>

    <section class="tile-selection-section">
        <div class="explore-collection-text">Explore Our Collection</div>
        <h2>Our Tile Selection</h2>
        <p class="description-text">From classic ceramics to luxurious natural stone, find the perfect tiles to match your style and needs.</p>
        <div class="tile-selection-grid">
            <div class="tile-selection-item"><div class="tile-selection-img-wrap"><img src="../images/user/tile1.jpg" alt="Ceramic Tiles"></div><h3>Ceramic Tiles</h3><p>Durable and versatile ceramic tiles for any space</p><button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button></div>
            <div class="tile-selection-item"><div class="tile-selection-img-wrap"><img src="../images/user/tile2.jpg" alt="Porcelain Tiles"></div><h3>Porcelain Tiles</h3><p>Premium quality porcelain for high-end finishes</p><button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button></div>
            <div class="tile-selection-item"><div class="tile-selection-img-wrap"><img src="../images/user/tile3.jpg" alt="Mosaic Tiles"></div><h3>Mosaic Tiles</h3><p>Artistic designs for unique decorative accents</p><button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button></div>
            <div class="tile-selection-item"><div class="tile-selection-img-wrap"><img src="../images/user/tile4.jpg" alt="Natural Stone"></div><h3>Natural Stone</h3><p>Elegant natural stone for luxurious spaces</p><button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button></div>
            <div class="tile-selection-item"><div class="tile-selection-img-wrap"><img src="../images/user/tile5.jpg" alt="Premium Tiles"></div><h3>Premium Tiles</h3><p>High-end premium tiles for luxury spaces</p><button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button></div>
        </div>
    </section>

    <section class="tile-categories-section" id="tiles-grid">
        <div class="tile-categories-container">
            <div class="categories-sidebar">
                <h3>Categories</h3>
                <ul>
                    <li><input type="checkbox" id="cat-ceramic"><label for="cat-ceramic">Ceramic Tile</label></li>
                    <li><input type="checkbox" id="cat-porcelain"><label for="cat-porcelain">Porcelain Tile</label></li>
                    <li><input type="checkbox" id="cat-mosaic"><label for="cat-mosaic">Mosaic Tile</label></li>
                    <li><input type="checkbox" id="cat-stone"><label for="cat-stone">Natural Stone</label></li>
                    <li><input type="checkbox" id="cat-outdoor"><label for="cat-outdoor">Outdoor Tiles</label></li>
                </ul>
                <h3>Price Range</h3>
                <ul>
                    <li><input type="radio" name="price" id="p1"><label for="p1">Under ₱500</label></li>
                    <li><input type="radio" name="price" id="p2"><label for="p2">₱500 - ₱1000</label></li>
                    <li><input type="radio" name="price" id="p3"><label for="p3">₱1000 - ₱2000</label></li>
                    <li><input type="radio" name="price" id="p4"><label for="p4">Over ₱2000</label></li>
                </ul>
                <button class="apply-filters-button">Apply Filters</button>
            </div>

            <div class="tile-grid-area">
                <div class="tiles-header">
                    <span class="small-text">Shop Our Collection</span>
                    <h3 class="big-text">Premium Tiles</h3>
                    <div class="description">Browse our extensive collection of premium tiles for every room in your home or business.</div>
                </div>
                <div class="tile-grid">
                    <div class="tile-item"><img src="../images/user/tile1.jpg" alt="Premium Ceramic Tile"><div class="item-details"><div class="item-title">Premium Ceramic Tile</div><div class="item-price">₱1,250</div></div><button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button></div>
                    <div class="tile-item"><img src="../images/user/tile2.jpg" alt="Porcelain Tile"><div class="item-details"><div class="item-title">Porcelain Tile</div><div class="item-price">₱950</div></div><button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button></div>
                    <div class="tile-item"><img src="../images/user/tile3.jpg" alt="Mosaic Tile"><div class="item-details"><div class="item-title">Mosaic Tile</div><div class="item-price">₱1,750</div></div><button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button></div>
                    <div class="tile-item"><img src="../images/user/tile4.jpg" alt="Natural Stone Tile"><div class="item-details"><div class="item-title">Natural Stone Tile</div><div class="item-price">₱850</div></div><button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button></div>
                    <div class="tile-item"><img src="../images/user/tile5.jpg" alt="Classic Tile"><div class="item-details"><div class="item-title">Classic Tile</div><div class="item-price">₱2,100</div></div><button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button></div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Featured carousel data
        const featuredItems = [
            { img: '../images/user/tile1.jpg', title: 'Premium Ceramic Tile', price: '₱1,250' },
            { img: '../images/user/tile2.jpg', title: 'Porcelain Tile', price: '₱950' },
            { img: '../images/user/tile3.jpg', title: 'Mosaic Tile', price: '₱1,750' },
            { img: '../images/user/tile4.jpg', title: 'Natural Stone Tile', price: '₱850' },
            { img: '../images/user/tile5.jpg', title: 'Classic Tile', price: '₱2,100' },
            { img: '../images/user/tile2.jpg', title: 'Hexagon Matte Tile', price: '₱1,350' }
        ];

        const itemsPerPage = () => {
            if (window.innerWidth <= 576) return 1;
            if (window.innerWidth <= 768) return 2;
            if (window.innerWidth <= 992) return 3;
            return 4;
        };

        let currentPage = 0; let animating = false;

        function renderFeatured(direction = 0) {
            const container = document.querySelector('.featured-items');
            const pagination = document.querySelector('.featured-pagination');
            if (!container || !pagination) return;
            const perPage = itemsPerPage();
            const pageCount = Math.ceil(featuredItems.length / perPage);
            currentPage = (currentPage + pageCount) % pageCount; // clamp

            const start = currentPage * perPage; const end = start + perPage;
            const itemsToRender = featuredItems.slice(start, end);

            const doUpdate = () => {
                container.innerHTML = '';
                itemsToRender.forEach((item) => {
                    const card = document.createElement('div');
                    card.className = 'featured-item';
                    card.innerHTML = `
                        <div class="featured-img-wrap"><img src="${item.img}" alt="${item.title}" loading="lazy"></div>
                        <div class="item-title">${item.title}</div>
                        <div class="item-price">${item.price}</div>
                        <button class="add-to-cart"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                    `;
                    container.appendChild(card);
                });
                renderDots(pageCount);
            };

            if (direction !== 0 && !animating) {
                animating = true;
                container.classList.add(direction > 0 ? 'slide-left-out' : 'slide-right-out');
                setTimeout(() => {
                    container.classList.remove('slide-left-out', 'slide-right-out');
                    doUpdate();
                    container.classList.add(direction > 0 ? 'slide-left-in' : 'slide-right-in');
                    setTimeout(() => { container.classList.remove('slide-left-in', 'slide-right-in'); animating = false; }, 320);
                }, 320);
            } else {
                doUpdate();
            }
        }

        function renderDots(pageCount) {
            const pagination = document.querySelector('.featured-pagination');
            if (!pagination) return;
            pagination.innerHTML = '';
            for (let i = 0; i < pageCount; i++) {
                const dot = document.createElement('span');
                dot.className = 'featured-dot' + (i === currentPage ? ' active' : '');
                dot.title = `Page ${i + 1}`;
                dot.onclick = () => {
                    if (animating || i === currentPage) return;
                    const direction = i > currentPage ? 1 : -1;
                    currentPage = i;
                    renderFeatured(direction);
                };
                pagination.appendChild(dot);
            }
        }

        function nextPage() { if (animating) return; const perPage = itemsPerPage(); const pageCount = Math.ceil(featuredItems.length / perPage); currentPage = (currentPage + 1) % pageCount; renderFeatured(1); }
        function prevPage() { if (animating) return; const perPage = itemsPerPage(); const pageCount = Math.ceil(featuredItems.length / perPage); currentPage = (currentPage - 1 + pageCount) % pageCount; renderFeatured(-1); }

        window.addEventListener('resize', () => { const newPer = itemsPerPage(); const currentRendered = document.querySelectorAll('.featured-item').length; if (newPer !== currentRendered) { currentPage = 0; renderFeatured(); } });

        document.addEventListener('DOMContentLoaded', () => {
            const nextBtn = document.querySelector('.featured-arrow.next');
            const prevBtn = document.querySelector('.featured-arrow.prev');
            if (nextBtn) nextBtn.addEventListener('click', nextPage);
            if (prevBtn) prevBtn.addEventListener('click', prevPage);
            renderFeatured();

            // Intersection animations
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('animate'); observer.unobserve(e.target); } });
            }, { threshold: 0.15 });
            document.querySelectorAll('.tile-selection-item, .tile-item').forEach(el => observer.observe(el));
        });
    </script>
</body>

</html>
