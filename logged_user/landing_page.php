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
        :root {
            --primary: #7D310A;
            --secondary: #CF8756;
            --dark: #270F03;
            --light: #F7F7F7;
            --text: #222;
            --muted: #666;
            --gap: 16px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            color: var(--text);
            background: var(--light);
        }

        /* Hero */
        .landing-hero-section {
            position: relative;
            width: 100vw;
            min-height: 68vh;
            background: linear-gradient(rgba(0,0,0,.7), rgba(0,0,0,.7)), url('../images/user/landingpagebackground.PNG') center/cover no-repeat;
            display: grid;
            place-items: center;
            padding: 80px 5vw 40px;
        }
        .landing-hero-content { display: grid; grid-template-columns: 1.1fr .9fr; gap: 4vw; align-items: center; max-width: 1200px; width: 100%; z-index: 1; }
        .center-hero-img { width: 100%; max-width: 560px; height: auto; transform: rotate(26deg); filter: drop-shadow(0 8px 30px rgba(0,0,0,.5)); justify-self: center; }
        .landing-hero-text-overlay { color: #fff; }
        .landing-hero-text-overlay .small-text { font-weight: 600; letter-spacing: .08em; opacity: .95; }
        .landing-hero-text-overlay .big-text { margin: 10px 0 0; font-size: clamp(2rem, 4.5vw, 3.2rem); font-weight: 900; color: var(--secondary); line-height: 1.1; text-shadow: 0 2px 6px rgba(0,0,0,.4); }

        /* Featured */
        .featured-section { background: #fff; padding: 64px 0 40px; }
        .section-lead { text-align: center; font-size: .9rem; color: var(--secondary); font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .section-title { text-align: center; margin: 6px auto 10px; font-size: clamp(1.6rem, 2.6vw, 2.4rem); color: var(--primary); font-weight: 900; }
        .section-desc { text-align: center; margin: 0 auto 26px; max-width: 640px; color: var(--muted); }

        .featured-carousel { display: grid; grid-template-columns: 48px 1fr 48px; align-items: center; gap: 12px; width: 92vw; max-width: 1200px; margin: 0 auto; }
        .featured-arrow { width: 48px; height: 48px; border-radius: 50%; border: 1px solid #ddd; background: #fff; display: grid; place-items: center; color: var(--primary); cursor: pointer; transition: .2s ease; }
        .featured-arrow:hover { background: var(--primary); color: #fff; transform: scale(1.05); }
        .featured-arrow:disabled { opacity: .5; cursor: not-allowed; }

        .featured-items { display: flex; gap: var(--gap); width: 100%; overflow: hidden; padding: 6px 0; --perPage: 4; }
        .featured-item { width: calc((100% - (var(--gap) * (var(--perPage) - 1))) / var(--perPage)); background: #fff; border-radius: 16px; box-shadow: 0 6px 20px rgba(0,0,0,.08); padding: 16px; display: flex; flex-direction: column; align-items: center; border: 1px solid #f0f0f0; transition: box-shadow .25s ease, transform .25s ease; }
        .featured-item:hover { transform: translateY(-6px); box-shadow: 0 12px 32px rgba(0,0,0,.12); }
        .featured-img-wrap { width: 140px; height: 140px; background: #fafafa; border-radius: 12px; display: grid; place-items: center; overflow: hidden; margin-bottom: 12px; }
        .featured-img-wrap img { width: 90%; height: 90%; object-fit: contain; display: block; }
        .item-title { font-weight: 800; color: #333; font-size: .98rem; text-align: center; margin: 6px 0 4px; }
        .item-price { color: var(--secondary); font-weight: 800; margin-bottom: 10px; }
        .add-to-cart { background: var(--primary); color: #fff; border: 2px solid var(--primary); border-radius: 30px; padding: 8px 14px; font-weight: 700; cursor: pointer; transition: .2s ease; display: inline-flex; gap: 8px; align-items: center; }
        .add-to-cart:hover { background: var(--secondary); border-color: var(--secondary); }

        .featured-pagination { display: flex; justify-content: center; gap: 10px; margin-top: 16px; }
        .featured-dot { width: 9px; height: 9px; border-radius: 50%; background: #ddd; border: 2px solid #fff; transition: .2s ease; cursor: pointer; }
        .featured-dot.active { background: var(--primary); transform: scale(1.1); box-shadow: 0 0 0 3px rgba(125,49,10,.08); }

        /* Slide keyframes */
        @keyframes slideLeftOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(-24px); opacity: .98; } }
        @keyframes slideLeftIn { from { transform: translateX(24px); opacity: .98; } to { transform: translateX(0); opacity: 1; } }
        @keyframes slideRightOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(24px); opacity: .98; } }
        @keyframes slideRightIn { from { transform: translateX(-24px); opacity: .98; } to { transform: translateX(0); opacity: 1; } }
        .slide-left-out { animation: slideLeftOut .35s ease both; }
        .slide-left-in { animation: slideLeftIn .35s ease both; }
        .slide-right-out { animation: slideRightOut .35s ease both; }
        .slide-right-in { animation: slideRightIn .35s ease both; }

        /* Explore/Tile Selection */
        .tile-selection-section { background: var(--dark); color: #fff; padding: 64px 5vw; text-align: center; }
        .tile-selection-section .explore-collection-text { color: var(--secondary); letter-spacing: .08em; font-weight: 700; text-transform: uppercase; }
        .tile-selection-section h2 { margin: 8px 0 10px; font-weight: 900; font-size: clamp(1.8rem, 3vw, 2.2rem); }
        .tile-selection-section .description-text { max-width: 640px; margin: 0 auto 30px; color: #ccc; }
        .tile-selection-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; max-width: 1200px; margin: 0 auto; }
        .tile-selection-item { background: #352110; border-radius: 14px; padding: 16px; box-shadow: 0 10px 28px rgba(0,0,0,.25); transition: transform .25s ease, box-shadow .25s ease; display: flex; flex-direction: column; }
        .tile-selection-item:hover { transform: translateY(-6px); box-shadow: 0 16px 36px rgba(0,0,0,.35); }
        .tile-selection-img-wrap { height: 160px; border-radius: 10px; overflow: hidden; background: #4e3220; margin-bottom: 12px; }
        .tile-selection-img-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .tile-selection-item h3 { margin: 4px 0 6px; font-weight: 800; }
        .tile-selection-item p { margin: 0 0 12px; color: #ddd; }
        .tile-selection-item .add-to-cart-button { background: var(--secondary); color: #fff; border: none; border-radius: 28px; padding: 10px 14px; font-weight: 800; cursor: pointer; transition: .2s ease; }
        .tile-selection-item .add-to-cart-button:hover { background: #e09e69; }

        /* Product Grid (Premium Tiles) */
        .tile-categories-section { background: #fff; color: var(--text); padding: 60px 5vw 80px; }
        .tile-categories-container { display: grid; grid-template-columns: 280px 1fr; gap: 24px; max-width: 1200px; width: 92vw; margin: 0 auto; align-items: start; }
        .categories-sidebar { background: #fff; border-radius: 14px; padding: 18px; box-shadow: 0 8px 24px rgba(0,0,0,.06); }
        .categories-sidebar h3 { margin: 0 0 10px; color: var(--primary); font-weight: 900; }
        .categories-sidebar ul { list-style: none; padding: 0; margin: 0 0 16px; display: grid; gap: 10px; }
        .categories-sidebar li { display: flex; align-items: center; gap: 10px; }
        .categories-sidebar input { width: 18px; height: 18px; appearance: none; border: 2px solid #ccc; border-radius: 4px; position: relative; cursor: pointer; }
        .categories-sidebar input[type=radio] { border-radius: 50%; }
        .categories-sidebar input:checked { background: var(--secondary); border-color: var(--secondary); }
        .categories-sidebar input[type=checkbox]:checked::before { content: '\2713'; position: absolute; color: #fff; font-size: 12px; top: 50%; left: 50%; transform: translate(-50%,-56%); }
        .apply-filters-button { width: 100%; border: none; border-radius: 10px; background: var(--primary); color: #fff; padding: 10px 12px; font-weight: 800; cursor: pointer; }
        .apply-filters-button:hover { background: var(--secondary); }

        .product-panel { margin-bottom: 12px; }
        .product-panel h2 { margin: 0 0 6px; font-weight: 900; color: var(--primary); font-size: clamp(1.4rem, 2.2vw, 2rem); }
        .product-panel p { margin: 0 0 16px; color: var(--muted); }
        .tile-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; }
        .tile-item { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,.08); transition: transform .2s ease, box-shadow .2s ease; display: flex; flex-direction: column; }
        .tile-item:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.12); }
        .tile-item img { height: 200px; object-fit: cover; width: 100%; display: block; }
        .tile-item .item-details { padding: 12px; text-align: center; }
        .item-title { margin: 0 0 6px; font-weight: 800; }
        .price { color: var(--secondary); font-weight: 800; }
        .add-to-cart-button { margin: 8px 12px 14px; padding: 8px 12px; border-radius: 28px; border: 2px solid var(--primary); color: var(--primary); background: #fff; font-weight: 800; cursor: pointer; }
        .add-to-cart-button:hover { background: var(--primary); color: #fff; }

        /* Reveal on scroll */
        .reveal-up { opacity: 0; transform: translateY(12px); }
        .reveal-up.in { opacity: 1; transform: translateY(0); transition: opacity .5s ease, transform .5s ease; }

        /* Responsive */
        @media (max-width: 1100px) {
            .landing-hero-content { grid-template-columns: 1fr; text-align: center; }
            .center-hero-img { max-width: 70%; transform: rotate(18deg); justify-self: center; }
            .tile-categories-container { grid-template-columns: 1fr; }
        }
        @media (max-width: 900px) {
            .featured-items { --gap: 14px; }
            .featured-items { --perPage: 3; }
        }
        @media (max-width: 600px) {
            .featured-items { --gap: 12px; }
            .featured-img-wrap { width: 110px; height: 110px; }
        }
    </style>
</head>
<body>
    <section class="landing-hero-section">
        <div class="landing-hero-content">
            <div class="landing-hero-text-overlay reveal-up">
                <div class="small-text">STYLE IN YOUR EVERY STEP.</div>
                <div class="big-text">CHOOSE YOUR<br/>TILES NOW.</div>
            </div>
            <img src="../images/user/landingpagetile1.png" alt="Landing Tile" class="center-hero-img reveal-up" />
        </div>
    </section>

    <section class="featured-section">
        <div class="section-lead reveal-up">Premium Selection</div>
        <h2 class="section-title reveal-up">Featured Items</h2>
        <p class="section-desc reveal-up">Explore our handpicked selection of premium tiles that combine quality craftsmanship with exceptional design for your home or business.</p>

        <div class="featured-carousel">
            <button class="featured-arrow prev" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
            <div class="featured-items"></div>
            <button class="featured-arrow next" aria-label="Next"><i class="fas fa-chevron-right"></i></button>
        </div>
        <div class="featured-pagination"></div>
    </section>

    <section class="tile-selection-section">
        <div class="explore-collection-text reveal-up">Explore Our Collection</div>
        <h2 class="reveal-up">Our Tile Selection</h2>
        <p class="description-text reveal-up">From classic ceramics to luxurious natural stone, find the perfect tiles to match your style and needs.</p>
        <div class="tile-selection-grid">
            <div class="tile-selection-item reveal-up">
                <div class="tile-selection-img-wrap"><img src="../images/user/tile1.jpg" alt="Ceramic Tiles" /></div>
                <h3>Ceramic Tiles</h3>
                <p>Durable and versatile ceramic tiles for any space</p>
                <button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
            </div>
            <div class="tile-selection-item reveal-up">
                <div class="tile-selection-img-wrap"><img src="../images/user/tile2.jpg" alt="Porcelain Tiles" /></div>
                <h3>Porcelain Tiles</h3>
                <p>Premium quality porcelain for high-end finishes</p>
                <button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
            </div>
            <div class="tile-selection-item reveal-up">
                <div class="tile-selection-img-wrap"><img src="../images/user/tile3.jpg" alt="Mosaic Tiles" /></div>
                <h3>Mosaic Tiles</h3>
                <p>Artistic designs for unique decorative accents</p>
                <button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
            </div>
            <div class="tile-selection-item reveal-up">
                <div class="tile-selection-img-wrap"><img src="../images/user/tile4.jpg" alt="Natural Stone" /></div>
                <h3>Natural Stone</h3>
                <p>Elegant natural stone for luxurious spaces</p>
                <button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
            </div>
            <div class="tile-selection-item reveal-up">
                <div class="tile-selection-img-wrap"><img src="../images/user/tile5.jpg" alt="Premium Tiles" /></div>
                <h3>Premium Tiles</h3>
                <p>High-end premium tiles for luxury spaces</p>
                <button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
            </div>
        </div>
    </section>

    <section class="tile-categories-section">
        <div class="tile-categories-container">
            <div class="categories-sidebar reveal-up">
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
                    <li><input type="radio" name="price-range" id="p1"><label for="p1">Under ₱500</label></li>
                    <li><input type="radio" name="price-range" id="p2"><label for="p2">₱500 - ₱1000</label></li>
                    <li><input type="radio" name="price-range" id="p3"><label for="p3">₱1000 - ₱2000</label></li>
                    <li><input type="radio" name="price-range" id="p4"><label for="p4">Over ₱2000</label></li>
                </ul>
                <button class="apply-filters-button">Apply Filters</button>
            </div>
            <div>
                <div class="product-panel reveal-up">
                    <h2>Premium Tiles</h2>
                    <p>Browse our extensive collection of premium tiles for every room in your home or business.</p>
                </div>
                <div class="tile-grid">
                    <div class="tile-item reveal-up">
                        <img src="../images/user/tile1.jpg" alt="Premium Ceramic Tile" />
                        <div class="item-details"><div class="item-title">Premium Ceramic Tile</div><div class="price">₱1,250</div></div>
                        <button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                    </div>
                    <div class="tile-item reveal-up">
                        <img src="../images/user/tile2.jpg" alt="Porcelain Tile" />
                        <div class="item-details"><div class="item-title">Porcelain Tile</div><div class="price">₱950</div></div>
                        <button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                    </div>
                    <div class="tile-item reveal-up">
                        <img src="../images/user/tile3.jpg" alt="Mosaic Tile" />
                        <div class="item-details"><div class="item-title">Mosaic Tile</div><div class="price">₱1,750</div></div>
                        <button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                    </div>
                    <div class="tile-item reveal-up">
                        <img src="../images/user/tile4.jpg" alt="Natural Stone Tile" />
                        <div class="item-details"><div class="item-title">Natural Stone Tile</div><div class="price">₱850</div></div>
                        <button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                    </div>
                    <div class="tile-item reveal-up">
                        <img src="../images/user/tile5.jpg" alt="Classic Tile" />
                        <div class="item-details"><div class="item-title">Classic Tile</div><div class="price">₱2,100</div></div>
                        <button class="add-to-cart-button"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                    </div>
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
            { img: '../images/user/tile5.jpg', title: 'Classic Tile', price: '₱2,100' }
        ];

        const container = document.querySelector('.featured-items');
        const pagination = document.querySelector('.featured-pagination');
        const prevBtn = document.querySelector('.featured-arrow.prev');
        const nextBtn = document.querySelector('.featured-arrow.next');

        const itemsPerPage = () => {
            const w = window.innerWidth;
            if (w <= 900) return 3;
            if (w <= 1200) return 3;
            return 4;
        };

        let currentPage = 0; let animating = false;

        function renderFeatured(direction = 0) {
            if (!container) return;
            const perPage = itemsPerPage();
            container.style.setProperty('--perPage', perPage);
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
                    updateFeaturedItems(start, end);
                    container.classList.add(direction > 0 ? 'slide-left-in' : 'slide-right-in');
                    setTimeout(() => {
                        container.classList.remove('slide-left-in', 'slide-right-in');
                        animating = false;
                    }, 350);
                }, 350);
            } else {
                updateFeaturedItems(start, end);
            }
            renderDots(pageCount);
        }

        function updateFeaturedItems(start, end) {
            container.innerHTML = '';
            const slice = featuredItems.slice(start, end);
            while (slice.length < itemsPerPage()) slice.push({ isEmpty: true });

            slice.forEach((item) => {
                const el = document.createElement('div');
                el.className = 'featured-item';
                if (item.isEmpty) {
                    el.style.visibility = 'hidden';
                } else {
                    el.innerHTML = `
                        <div class="featured-img-wrap"><img src="${item.img}" alt="${item.title}" loading="lazy" /></div>
                        <div class="item-title">${item.title}</div>
                        <div class="item-price">${item.price}</div>
                        <button class="add-to-cart"><i class="fa fa-shopping-cart"></i> Add to Cart</button>
                    `;
                }
                container.appendChild(el);
            });
        }

        function renderDots(pageCount) {
            pagination.innerHTML = '';
            for (let i = 0; i < pageCount; i++) {
                const d = document.createElement('span');
                d.className = 'featured-dot' + (i === currentPage ? ' active' : '');
                d.title = `Page ${i + 1}`;
                d.addEventListener('click', () => {
                    if (animating || i === currentPage) return;
                    const dir = i > currentPage ? 1 : -1;
                    currentPage = i; renderFeatured(dir);
                });
                pagination.appendChild(d);
            }
        }

        function nextFeatured() { if (!animating) { currentPage++; renderFeatured(1); } }
        function prevFeatured() { if (!animating) { currentPage--; renderFeatured(-1); } }

        nextBtn.addEventListener('click', nextFeatured);
        prevBtn.addEventListener('click', prevFeatured);
        window.addEventListener('resize', () => { currentPage = 0; renderFeatured(); });

        // Reveal animations on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); observer.unobserve(e.target); } });
        }, { threshold: .12 });
        document.querySelectorAll('.reveal-up').forEach(el => observer.observe(el));
        document.querySelectorAll('.tile-selection-item, .tile-item').forEach(el => observer.observe(el));

        // Initial render
        renderFeatured();
    </script>
</body>
</html>