<?php
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal Bite Clinic</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .home-section,
        .video-text-section {
            flex-grow: 1;
            /* Allow these sections to take up available space */
        }
    </style>
</head>

<body>

    <section class="home-section">
        <img src="images/homepage.jpg" alt="Homepage Image">
        <div class="text-container">
            <h1>Bring Your Visions to Life: <span style="font-weight: bold;">Design Stunning Spaces</span> with Our 2D
                Tile Visualizer</h1>
            <p>Rich Anne Lea Tiles Trading</p>
            <button>Start Visualizing Now</button>
        </div>
    </section>
    <br>
    <br>
    <section class="video-text-section">
        <div class="left-text">
            <h1><span style="font-weight: bold; color: #946750;">Craft Your Visions</span> <br> <span
                    style="color: black;">with our <br>Tile</span> <span style="color: black;">Visualizer Tool</span>
            </h1>
            <p style="font-size: medium; color: black;">Enhancing your imagination with the use of tile visualizer in
                creating limitless designs all you want.</p>
        </div>
        <div class="right-video">
            <video src="images/video.mp4" autoplay loop muted></video>
        </div>
    </section>

    <section class="meet-raltt-section" style="text-align:center; margin-top: 40px;">
        <h3 style="color: #94481b; font-weight: 600; letter-spacing: 1px;">MEET RALTT</h3>
        <h1 style="font-size: 2rem; font-weight: bold; margin: 10px 0;">
            Tile Visualizer and E-Commerce in One Website
        </h1>
        <p style="color: #444; max-width: 500px; margin: 0 auto 30px;">
            Browse while using our tile visualizer tool and check out items you want and deliver at the same day.
        </p>
    </section>
    <section class="tabbed-section" style="margin: 60px auto 0; max-width: 1100px;">
        <div class="tab-buttons" style="display: flex; justify-content: center; gap: 20px; margin-bottom: 30px;">
            <button class="tab-btn animated-btn active" data-tab="visualizer-tab">2D Visualizer</button>
            <button class="tab-btn animated-btn secondary" data-tab="ecommerce-tab">Tile E-Commerce</button>
        </div>
        <div class="tab-content" id="visualizer-tab"
            style="display: flex; align-items: center; gap: 40px; margin-top: 40px;">
            <img src="images/2dtilehomepage.png" alt="2D Visualizer"
                style="width: 350px; border-radius: 18px; box-shadow: 0 4px 24px rgba(0,0,0,0.07);">
            <div>
                <h2 style="font-size: 2.2rem; font-weight: bold; margin-bottom: 8px;">2D Tile Visualizer</h2>
                <div style="color: #888; font-size: 0.98rem; margin-bottom: 10px;">Expand imagination with 2D</div>
                <div style="color: #555; font-size: 1.1rem; max-width: 420px; margin-bottom: 28px;">
                    Enhance your shopping experience with our 2D Tile Visualizer! Simply upload an image to see how
                    tiles, marble, or wood flooring fit your space. Visualize with confidence and make informed
                    decisions effortlessly!
                </div>
                <a href="visualizer.php" class="animated-btn" style="background: #e6844a; border: none;">Launch
                    visualizer</a>
            </div>
        </div>
        <div class="tab-content" id="ecommerce-tab"
            style="display: none; align-items: center; gap: 40px; margin-top: 40px;">
            <div style="display: flex; gap: 20px;">
                <img src="images/tiles-shop1.jpg" alt="Tile Shop 1"
                    style="width: 220px; border-radius: 18px; box-shadow: 0 4px 24px rgba(0,0,0,0.07);">
                <img src="images/tiles-shop2.jpg" alt="Tile Shop 2"
                    style="width: 220px; border-radius: 18px; box-shadow: 0 4px 24px rgba(0,0,0,0.07);">
            </div>
            <div>
                <h2 style="font-size: 2.2rem; font-weight: bold; margin-bottom: 8px;">Choose over<br>1000+ tile designs
                </h2>
                <div style="color: #888; font-size: 0.98rem; margin-bottom: 10px;">Add to cart and checkout your tile
                    choice</div>
                <div style="color: #555; font-size: 1.1rem; max-width: 420px; margin-bottom: 28px;">
                    Choose from over 1000+ tile designs at RALTT! Discover a variety of styles in tiles, marble, and
                    wood flooring to perfectly match your space.
                </div>
                <a href="shop.php" class="animated-btn" style="background: #e6844a; border: none;">Buy now</a>
            </div>
        </div>
    </section>
    <style>
        .animated-btn {
            display: inline-block;
            padding: 14px 32px;
            background: #94481b;
            color: #fff;
            border: none;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(148, 72, 27, 0.15);
            transition:
                transform 0.18s cubic-bezier(.4, 0, .2, 1),
                box-shadow 0.18s cubic-bezier(.4, 0, .2, 1),
                background 0.18s,
                color 0.18s,
                border 0.18s;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .animated-btn:hover,
        .animated-btn:focus,
        .tab-btn.active {
            transform: translateY(-3px) scale(1.04);
            box-shadow: 0 8px 24px rgba(148, 72, 27, 0.22);
            background: #94481b;
            color: #fff;
            border: 2px solid #94481b;
        }

        .animated-btn.secondary {
            background: #fff;
            color: #94481b;
            border: 2px solid #94481b;
        }

        .animated-btn.secondary:hover,
        .animated-btn.secondary:focus,
        .tab-btn.secondary.active {
            background: #94481b;
            color: #fff;
            border: 2px solid #94481b;
        }

        .tab-buttons {
            background: transparent;
            border-radius: 12px;
            margin-bottom: 0;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
            box-shadow: none;
        }

        .tab-btn {
            font-size: 1.2rem;
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 30px;
            margin: 0;
            outline: none;
            border: 2px solid transparent;
            background: #fff;
            color: #94481b;
            transition:
                background 0.18s,
                color 0.18s,
                box-shadow 0.18s,
                border 0.18s,
                transform 0.18s;
        }

        .tab-btn.active,
        .tab-btn:focus {
            background: #94481b;
            color: #fff;
            border: 2px solid #94481b;
            box-shadow: 0 8px 24px rgba(148, 72, 27, 0.22);
            transform: translateY(-3px) scale(1.04);
            z-index: 1;
        }

        .tab-btn.secondary {
            background: #fff;
            color: #94481b;
            border: 2px solid #94481b;
        }

        .tab-btn.secondary.active,
        .tab-btn.secondary:focus {
            background: #94481b;
            color: #fff;
            border: 2px solid #94481b;
        }

        .tab-content {
            animation: fadeIn 0.4s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .trusted-section {
            background: #faf9f7;
            border-radius: 18px;
            box-shadow: 0 4px 32px rgba(148, 72, 27, 0.06);
            padding: 36px 0 48px 0;
            margin-top: 60px;
        }

        .trusted-section h1 {
            margin-bottom: 0.2em;
            letter-spacing: -1px;
        }

        .trusted-section>div {
            margin-bottom: 0.5em;
        }

        .distributor-carousel-outer {
            overflow: hidden;
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 2px 16px rgba(148, 72, 27, 0.07);
            padding: 18px 0 10px 0;
        }

        .distributor-carousel {
            display: flex;
            gap: 24px;
            transition: transform 0.8s cubic-bezier(.7, 0, .3, 1);
            will-change: transform;
        }

        .distributor-carousel img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            border-radius: 12px;
            background: #f7f7f7;
            box-shadow: 0 2px 8px rgba(148, 72, 27, 0.07);
            padding: 8px;
            transition:
                box-shadow 0.3s,
                transform 0.5s cubic-bezier(.7, 0, .3, 1),
                background 0.3s,
                filter 0.3s,
                opacity 0.4s;
            opacity: 0.7;
            filter: blur(1px) grayscale(30%);
        }

        .distributor-carousel img.center-logo {
            width: 110px;
            height: 110px;
            z-index: 2;
            transform: scale(1.35) rotate(-2deg);
            box-shadow: 0 8px 32px 0 rgba(148, 72, 27, 0.18), 0 0 0 6px #fff8f3;
            background: #fff8f3;
            opacity: 1;
            filter: none;
            border: 2.5px solid #94481b22;
            animation: pop-center 0.7s cubic-bezier(.7, 0, .3, 1);
        }

        @keyframes pop-center {
            0% {
                transform: scale(1) rotate(0deg);
            }

            60% {
                transform: scale(1.45) rotate(-4deg);
            }

            100% {
                transform: scale(1.35) rotate(-2deg);
            }
        }

        .distributor-carousel img.side-logo {
            width: 85px;
            height: 85px;
            z-index: 1;
            opacity: 0.92;
            filter: blur(0.5px) grayscale(10%);
            transform: scale(1.08);
            background: #f7f7f7;
            box-shadow: 0 4px 16px rgba(148, 72, 27, 0.10);
        }

        .distributor-carousel img.far-logo {
            width: 70px;
            height: 70px;
            opacity: 0.7;
            filter: blur(1.5px) grayscale(40%);
            transform: scale(0.95);
        }

        .distributor-carousel img.fade-logo {
            opacity: 0.3;
            filter: blur(2.5px) grayscale(80%);
            transform: scale(0.8);
            pointer-events: none;
        }

        .trusted-section {
            margin-bottom: 80px;
        }

        .app-section {
            margin-bottom: 80px;
            background: #faf9f7;
            border-radius: 18px;
            box-shadow: 0 4px 32px rgba(148, 72, 27, 0.06);
            padding: 48px 0 48px 0;
        }

        .app-flex {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 60px;
            flex-wrap: wrap;
        }

        .app-phone {
            flex: 0 0 270px;
            display: flex;
            justify-content: center;
        }

        .app-info {
            flex: 0 0 340px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
        }

        .app-download-btn {
            display: inline-flex;
            align-items: center;
            background: #e0dfde;
            color: #444;
            font-size: 1.08rem;
            font-weight: 500;
            border-radius: 8px;
            padding: 12px 28px;
            margin-bottom: 8px;
            text-decoration: none;
            transition: background 0.18s, color 0.18s, box-shadow 0.18s;
            box-shadow: 0 2px 8px rgba(148, 72, 27, 0.07);
        }

        .app-download-btn i {
            margin-right: 10px;
            font-size: 1.2em;
        }

        .app-download-btn:hover,
        .app-download-btn:focus {
            background: #94481b;
            color: #fff;
            box-shadow: 0 6px 24px rgba(148, 72, 27, 0.15);
        }

        .google-play-btn img {
            height: 48px;
            transition: transform 0.18s;
        }

        .google-play-btn:hover img {
            transform: scale(1.07) rotate(-2deg);
        }

        @media (max-width: 900px) {
            .app-flex {
                flex-direction: column;
                gap: 30px;
            }

            .app-info {
                align-items: center;
                text-align: center;
            }
        }
    </style>

    <!-- Trusted Distributors Section -->
    <section class="trusted-section" style="margin: 80px auto 0; max-width: 1200px; text-align: center;">
        <h1 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.3em;">
            Trusted by over <span style="color: #94481b;">50+ distributors</span>
        </h1>
        <div style="color: #888; font-size: 1.15rem; margin-bottom: 32px;">
            Low prices vs. other tile trade retailers
        </div>
        <div class="distributor-carousel-outer">
            <div class="distributor-carousel" id="distributor-carousel">
                <!-- Example logos, replace src with your actual distributor logo images -->
                <img src="images/distributors/logo1.jpg" alt="Distributor 1">
                <img src="images/distributors/logo2.jpg" alt="Distributor 2">
                <img src="images/distributors/logo3.jpg" alt="Distributor 3">
                <img src="images/distributors/logo4.jpg" alt="Distributor 4">
                <img src="images/distributors/logo5.jpg" alt="Distributor 5">
                <img src="images/distributors/logo6.jpg" alt="Distributor 6">
                <img src="images/distributors/logo7.jpg" alt="Distributor 7">
                <img src="images/distributors/logo8.jpg" alt="Distributor 8">
                <img src="images/distributors/logo9.jpg" alt="Distributor 9">
                <img src="images/distributors/logo10.jpg" alt="Distributor 10">
                <img src="images/distributors/logo11.jpg" alt="Distributor 11">
                <!-- Add more as needed -->
            </div>
        </div>
    </section>

    <!-- App Download Section -->
    <section class="app-section" style="margin: 80px auto 0; max-width: 1100px;">
        <h1 style="text-align:center; font-size:2.4rem; font-weight: bold; margin-bottom: 0.2em;">
            Checkout wherever you are!
        </h1>
        <div style="text-align:center; color:#aaa; font-size:1.1rem; margin-bottom: 36px;">
            Download our app now
        </div>
        <div class="app-flex">
            <div class="app-phone">
                <img src="images/phonehome.jpg" alt="Phone Mockup" style="width: 260px; max-width: 90%;">
            </div>
            <div class="app-info">
                <div style="font-size: 1.25rem; color: #94481b; font-weight: 600; margin-bottom: 30px; line-height: 1.4;">
                    Let’s make your ideas come<br>
                    into real-life visuals within<br>
                    your hands.
                </div>
                <a href="#" class="app-download-btn">
                    <i class="fa fa-download"></i> Download the app
                </a>
                <div style="margin: 18px 0 10px 0; color: #888; font-size: 1.1rem;">OR</div>
                <a href="#" class="google-play-btn">
                    <img src="images/google-play-badge.png" alt="Get it on Google Play" style="height: 48px;">
                </a>
            </div>
        </div>
    </section>

    <!-- Distributor Carousel Script -->
    <script src="js/distributor-carousel.js"></script>
</body>

<script src="js/home.js"></script>

</html>
<?php
include 'includes/footer.php';
?>