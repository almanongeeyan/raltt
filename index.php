<?php
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rich Anne Lea Tiles Trading - Tile Visualizer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Base Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            color: #333;
            line-height: 1.6 ;
            overflow-x: hidden;
        }

        /* Container for all sections */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Hero Section */
        .home-section {
            position: relative;
            width: 100%;
            height: 100vh;
            max-height: 800px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .home-section img {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
            text-align: center;
            padding: 0 20px;
            width: 100%;
        }

        .hero-content::before {
            content: "";
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 120vw;         /* Increased from 90vw */
            max-width: 1000px;    /* Increased from 700px */
            height: 120vw;        /* Increased from 90vw */
            max-height: 1000px;   /* Increased from 700px */
            background: radial-gradient(circle, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 60%, rgba(0,0,0,0) 100%);
            z-index: -1;
            pointer-events: none;
            border-radius: 50%;
            filter: blur(2px);
        }

        .hero-content h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .hero-content p {
            font-size: clamp(1rem, 2vw, 1.5rem);
            margin-bottom: 2rem;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }

        .hero-content button {
            background: #94481b;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .hero-content button:hover {
            background: #b35923;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        /* Video Text Section */
        .video-text-section {
            padding: 60px 0;
            width: 100%;
        }

        .video-text-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
        }

        .video-text {
            flex: 1;
            min-width: 300px;
            text-align: center;
        }

        .video-text h1 {
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .video-text p {
            font-size: 1rem;
            color: #666;
            max-width: 500px;
            margin: 0 auto;
        }

        .video-container {
            flex: 1;
            min-width: 300px;
            max-width: 600px;
            margin: 0 auto;
        }

        .video-container video {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        /* Meet RALTT Section */
        .meet-raltt-section {
            padding: 60px 0;
            width: 100%;
            text-align: center;
        }

        .meet-raltt-section h3 {
            color: #94481b;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }

        .meet-raltt-section h1 {
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .meet-raltt-section p {
            color: #666;
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
        }

        /* Tabbed Section */
        .tabbed-section {
            padding: 60px 0;
            width: 100%;
        }

        .tab-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .tab-btn {
            font-size: 1.1rem;
            font-weight: 600;
            padding: 12px 25px;
            border-radius: 30px;
            background: #fff;
            color: #94481b;
            border: 2px solid #94481b;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            background: #94481b;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(148, 72, 27, 0.3);
        }

        .tab-content {
            display: none;
            align-items: center;
            gap: 40px;
            margin-top: 30px;
            flex-wrap: wrap;
            width: 100%;
        }

        .tab-content.active {
            display: flex;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .tab-content img {
            flex: 1;
            min-width: 300px;
            max-width: 100%;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .tab-text {
            flex: 1;
            min-width: 300px;
            text-align: left;
        }

        .tab-content h2 {
            font-size: clamp(1.8rem, 4vw, 2.2rem);
            font-weight: 700;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .tab-content .subtitle {
            color: #888;
            font-size: 0.98rem;
            margin-bottom: 1rem;
        }

        .tab-content .description {
            color: #555;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .animated-btn {
            display: inline-block;
            padding: 12px 30px;
            background: #94481b; /* Same as login button */
            color: white;
            border: none;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            letter-spacing: 0.01em;
            white-space: nowrap;
        }

        .animated-btn:hover {
            background: #b35923;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        /* Trusted Section */
        .trusted-section {
            width: 100%;
            padding: 80px 0;
            text-align: center;
            background: #faf9f7;
        }

        .trusted-section h1 {
            font-size: clamp(2rem, 5vw, 2.7rem);
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .trusted-section .subtitle {
            color: #888;
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .distributor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 20px;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.05);
        }

        .distributor-item {
            background: #f8f8f8;
            border-radius: 15px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            aspect-ratio: 1/1;
        }

        .distributor-item img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            filter: grayscale(20%);
            transition: all 0.3s ease;
        }

        .distributor-item:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            background: white;
        }

        .distributor-item:hover img {
            filter: grayscale(0%);
        }

        /* App Section */
        .app-section {
            width: 100%;
            padding: 80px 0;
            text-align: center;
        }

        .app-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 60px 20px;
            background: #faf9f7;
            border-radius: 20px;
        }

        .app-section h1 {
            font-size: clamp(1.8rem, 4vw, 2.4rem);
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .app-section .subtitle {
            color: #aaa;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .app-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }

        .app-image {
            flex: 1;
            min-width: 300px;
            max-width: 400px;
        }

        .app-image img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .app-details {
            flex: 1;
            min-width: 300px;
            text-align: center;
        }

        .app-highlight {
            font-size: 1.25rem;
            color: #94481b;
            font-weight: 600;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        /* Unified Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #94481b;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 30px;
            padding: 12px 30px;
            margin: 8px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            width: 240px;
            min-width: 180px;
            max-width: 100%;
            transition: all 0.3s ease;
            gap: 8px;
            cursor: pointer;
            text-decoration: none;
            letter-spacing: 0.01em;
            white-space: nowrap;
        }

        .btn i {
            font-size: 1.15em;
        }

        .btn:hover {
            background: #b35923;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        .btn-download {
            background: #94481b;
        }

        .btn-playstore {
            background: #4285F4;
        }

        .btn-playstore:hover {
            background: #3367D6;
        }

        .btn-divider {
            color: #888;
            font-size: 1.05rem;
            margin: 10px 0;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-divider::before,
        .btn-divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #ddd;
            margin: 0 10px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .home-section {
                height: 80vh;
            }
            
            .hero-content {
                padding: 0 15px;
            }
            
            .video-text-container {
                flex-direction: column;
                gap: 30px;
            }
            
            .video-text, .video-container {
                width: 100%;
                padding: 0 15px;
            }
            
            .tab-buttons {
                gap: 10px;
            }
            
            .tab-btn {
                padding: 10px 20px;
                font-size: 1rem;
            }
            
            .tab-content {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .tab-text {
                text-align: center;
                padding: 0 15px;
            }
            
            .distributor-grid {
                grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
                gap: 15px;
                padding: 20px;
            }
            
            .distributor-item {
                padding: 15px;
            }
            
            .app-content {
                flex-direction: column;
            }
            
            .app-details {
                text-align: center;
            }

            .btn {
                width: 100%;
                max-width: 280px;
            }
        }

        @media (max-width: 480px) {
            .home-section {
                height: 70vh;
            }
            
            .hero-content h1 {
                font-size: 1.8rem;
            }
            
            .hero-content p {
                font-size: 1rem;
            }
            
            .tab-buttons {
                flex-direction: column;
                width: 100%;
            }
            
            .tab-btn {
                width: 100%;
            }
            
            .distributor-grid {
                grid-template-columns: repeat(auto-fit, minmax(70px, 1fr));
                gap: 10px;
                padding: 15px;
            }
            
            .app-container {
                padding: 40px 15px;
            }
            
            .app-highlight {
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Hero Section -->
    <section class="home-section">
        <img src="images/homepage.jpg" alt="Beautiful tile designs">
        <div class="container">
            <div class="hero-content">
                <h1>Bring Your Visions to Life: <span>Design Stunning Spaces</span> with Our 2D Tile Visualizer</h1>
                <p>Rich Anne Lea Tiles Trading</p>
                <button class="btn">Start Visualizing Now</button>
            </div>
        </div>
    </section>

    <!-- Video Text Section -->
    <section class="video-text-section">
        <div class="container">
            <div class="video-text-container">
                <div class="video-text">
                    <h1><span style="color: #946750;">Craft Your Visions</span> <br>with our<br>Tile Visualizer Tool</h1>
                    <p>Enhancing your imagination with the use of tile visualizer in creating limitless designs all you want.</p>
                </div>
                <div class="video-container">
                    <video src="images/video.mp4" autoplay loop muted></video>
                </div>
            </div>
        </div>
    </section>

    <!-- Meet RALTT Section -->
    <section class="meet-raltt-section">
        <div class="container">
            <h3>MEET RALTT</h3>
            <h1>Tile Visualizer and E-Commerce in One Website</h1>
            <p>Browse while using our tile visualizer tool and check out items you want and deliver at the same day.</p>
        </div>
    </section>

    <!-- Tabbed Section -->
    <section class="tabbed-section">
        <div class="container">
            <div class="tab-buttons">
                <button class="tab-btn active" data-tab="visualizer-tab">2D Visualizer</button>
                <button class="tab-btn" data-tab="ecommerce-tab">Tile E-Commerce</button>
            </div>
            
            <div class="tab-content active" id="visualizer-tab">
                <img src="images/2dtilehomepage.png" alt="2D Visualizer">
                <div class="tab-text">
                    <h2>2D Tile Visualizer</h2>
                    <div class="subtitle">Expand imagination with 2D</div>
                    <div class="description">
                        Enhance your shopping experience with our 2D Tile Visualizer! Simply upload an image to see how
                        tiles, marble, or wood flooring fit your space. Visualize with confidence and make informed
                        decisions effortlessly!
                    </div>
                    <a href="visualizer.php" class="animated-btn">Launch visualizer</a>
                </div>
            </div>
            
            <div class="tab-content" id="ecommerce-tab">
                <div style="display: flex; gap: 20px; flex-wrap: wrap; justify-content: center;">
                    <img src="images/tilehp1.PNG" alt="Tile Shop 1" style="flex: 1; min-width: 200px; max-width: 100%;">
                    <img src="images/tilehp2.PNG" alt="Tile Shop 2" style="flex: 1; min-width: 200px; max-width: 100%;">
                </div>
                <div class="tab-text">
                    <h2>Choose over 1000+ tile designs</h2>
                    <div class="subtitle">Add to cart and checkout your tile choice</div>
                    <div class="description">
                        Choose from over 1000+ tile designs at RALTT! Discover a variety of styles in tiles, marble, and
                        wood flooring to perfectly match your space.
                    </div>
                    <a href="shop.php" class="animated-btn">Buy now</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Trusted Distributors Section -->
    <section class="trusted-section">
        <div class="container">
            <h1>Trusted by over <span style="color: #94481b;">50+ distributors</span></h1>
            <div class="subtitle">Low prices vs. other tile trade retailers</div>
            <div class="distributor-grid">
                <div class="distributor-item"><img src="images/distributors/logo1.jpg" alt="Distributor 1"></div>
                <div class="distributor-item"><img src="images/distributors/logo2.jpg" alt="Distributor 2"></div>
                <div class="distributor-item"><img src="images/distributors/logo3.jpg" alt="Distributor 3"></div>
                <div class="distributor-item"><img src="images/distributors/logo4.jpg" alt="Distributor 4"></div>
                <div class="distributor-item"><img src="images/distributors/logo5.jpg" alt="Distributor 5"></div>
                <div class="distributor-item"><img src="images/distributors/logo6.jpg" alt="Distributor 6"></div>
                <div class="distributor-item"><img src="images/distributors/logo7.jpg" alt="Distributor 7"></div>
                <div class="distributor-item"><img src="images/distributors/logo8.jpg" alt="Distributor 8"></div>
                <div class="distributor-item"><img src="images/distributors/logo9.jpg" alt="Distributor 9"></div>
                <div class="distributor-item"><img src="images/distributors/logo10.jpg" alt="Distributor 10"></div>
                <div class="distributor-item"><img src="images/distributors/logo11.jpg" alt="Distributor 11"></div>
            </div>
        </div>
    </section>

    <!-- App Download Section -->
    <section class="app-section">
        <div class="container">
            <div class="app-container">
                <h1>Checkout wherever you are!</h1>
                <div class="subtitle">Download our app now</div>
                <div class="app-content">
                    <div class="app-image">
                        <img src="images/phonehome.jpg" alt="Phone Mockup">
                    </div>
                    <div class="app-details">
                        <div class="app-highlight">
                            Let's make your ideas come<br>
                            into real-life visuals within<br>
                            your hands.
                        </div>
                        <a href="#" class="btn btn-download">
                            <i class="fas fa-download"></i> Download the App
                        </a>
                        <div class="btn-divider">or</div>
                        <a href="#" class="btn btn-playstore">
                            <i class="fab fa-google-play"></i> Get on Google Play
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-btn');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons and content
                    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Show corresponding content
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>
<?php
include 'includes/footer.php';
?>