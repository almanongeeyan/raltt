<?php
session_start();
// Prevent back navigation after logout
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
// Redirect to login if not logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ../index.php');
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

        /* Hero Content Container (side by side image and text) */
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
            /* Added padding to fix hover animation clipping */
            padding: 20px 0; 
            min-height: 420px;
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
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #e0e0e0;
            display: inline-block;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 2px solid #fff;
        }

        .featured-dot.active {
            background: #CF8756;
            box-shadow: 0 2px 8px rgba(207, 135, 86, 0.3);
            border-color: #CF8756;
            transform: scale(1.15);
        }

        .slide-left-in {
            animation: slideLeftIn 0.35s cubic-bezier(.4, 1.4, .6, 1);
        }

        .slide-left-out {
            animation: slideLeftOut 0.35s cubic-bezier(.4, 1.4, .6, 1);
        }

        .slide-right-in {
            animation: slideRightIn 0.35s cubic-bezier(.4, 1.4, .6, 1);
        }

        .slide-right-out {
            animation: slideRightOut 0.35s cubic-bezier(.4, 1.4, .6, 1);
        }

        @keyframes slideLeftIn {
            from {
                opacity: 0;
                transform: translateX(80px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideLeftOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(-80px);
            }
        }

        @keyframes slideRightIn {
            from {
                opacity: 0;
                transform: translateX(-80px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideRightOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(80px);
            }
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
        }
    </style>
</head>

<body>
    <section class="landing-hero-section">
        <div class="landing-hero-content">
            <img src="../images/user/landingpagetile1.png" alt="Landing Tile" class="center-hero-img">
            <div class="landing-hero-text-overlay">
                <div class="small-text">STYLE--- IN YOUR EVERY STEP.</div>
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

    <script>
        const featuredItems = [{
            img: '../images/user/tile1.jpg',
            title: 'Long-Length 2.0',
            price: 'P22.00',
        }, {
            img: '../images/user/tile1.jpg',
            title: 'Speed 500 Ignite',
            price: 'P120.00',
        }, {
            img: '../images/user/tile2.jpg',
            title: 'Jordan Hyper Grip Ot',
            price: 'P50.00',
        }, {
            img: '../images/user/tile3.jpg',
            title: 'Swimming Cap Slin',
            price: 'P22.00',
        }, {
            img: '../images/user/tile4.jpg',
            title: 'Soccer Ball Club America',
            price: 'P30.00',
        }, {
            img: '../images/user/tile5.jpg',
            title: 'Hyperadapt Shield Lite Half-Zip',
            price: 'P110.00',
        }, ];

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
                    div.innerHTML = '';
                } else {
                    div.innerHTML = `
                        <div class="featured-img-wrap">
                            <img src="${item.img}" alt="${item.title}">
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
            // Check if the number of items per page has changed
            const newPerPage = itemsPerPage();
            const oldPerPage = document.querySelectorAll('.featured-item').length;
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