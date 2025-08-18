<?php
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rich Anne Lea Tiles - Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        /* General page and section styling */
        body {
            overflow-x: hidden;
            background-color: #f8f5f2; /* Light background for the whole page */
        }

        .homepage-message {
            width: 100vw;
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            padding: 80px 20px 60px;
            text-align: center;
            background: linear-gradient(to bottom, #ffece2 0%, #f8f5f2 100%);
            box-sizing: border-box;
        }

        .homepage-message-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .homepage-message h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 18px;
            color: #333;
        }

        .homepage-message .easy {
            color: #F47C2E;
            display: inline-block;
        }

        .homepage-message p {
            font-size: 1.1rem;
            font-weight: 400;
            max-width: 700px;
            margin: 0 auto;
            color: #222;
            line-height: 1.6;
        }

        /* Tile Type Section Styles */
        .tile-type-section {
            width: 100vw;
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            margin-top: 0;
            margin-bottom: 60px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            padding: 50px 0;
            box-sizing: border-box;
            z-index: 1;
        }

        .tile-type-toggle {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .tile-type-toggle button {
            font-size: 1.1rem;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 8px;
            background: #f5f5f5;
            color: #555;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .tile-type-toggle button::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: #94481b;
            transition: width 0.3s ease;
        }

        .tile-type-toggle button:hover {
            background: #eee;
            color: #333;
        }

        .tile-type-toggle button:hover::after {
            width: 100%;
        }

        .tile-type-toggle button.active {
            background: #94481b;
            color: white;
            box-shadow: 0 4px 12px rgba(148, 72, 27, 0.2);
        }

        .tile-type-toggle button.active::after {
            width: 100%;
            background: #7a3a16;
        }

        .tile-type-content {
            width: 100%;
        }

        .tile-type-flex {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 40px;
        }

        .tile-type-img {
            width: 300px;
            height: 350px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 6px 8px 16px rgba(0, 0, 0, 0.1);
            background: #fff;
        }

        .tile-type-desc {
            max-width: 500px;
            text-align: left;
        }

        .tile-type-desc h2 {
            font-size: 2rem;
            font-weight: 500;
            margin-bottom: 8px;
            color: #444;
            letter-spacing: 1px;
        }

        .tile-type-desc hr {
            border: none;
            border-top: 1px solid #eee;
            margin: 15px 0 20px;
        }

        .tile-type-desc p {
            font-size: 1.08rem;
            color: #555;
            line-height: 1.6;
        }

        /* --- New Section: Wide Range Tile Selection --- */
        .tile-selection-section {
            padding: 50px 20px;
            text-align: center;
            background-color: #f8f5f2;
        }

        .tile-selection-section h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 40px;
        }

        .tile-selection-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .tile-selection-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .tile-selection-item img {
            width: 100%;
            max-width: 250px;
            height: 250px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .tile-selection-item img:hover {
            transform: scale(1.05);
        }

        .tile-selection-item p {
            font-size: 1.1rem;
            font-weight: 600;
            color: #F47C2E;
            margin-top: 15px;
        }

        /* --- New Section: Patterns and Designs --- */
        .patterns-and-designs-section {
            background-color: #f8f5f2;
            padding: 50px 20px;
            text-align: center;
        }

        .patterns-and-designs-section h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 40px;
        }

        .pattern-category {
            border: 1px solid #e0e0e0;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .pattern-category h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #555;
            margin-bottom: 20px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .pattern-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            justify-items: center;
        }

        .pattern-tile {
            width: 100%;
            padding-top: 100%; /* Creates a perfect square for the tile */
            position: relative;
            background-size: cover;
            background-position: center;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .pattern-tile::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.1);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 8px;
        }

        .pattern-tile:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        
        .pattern-tile:hover::after {
            opacity: 1;
        }



        /* Responsive Styles */
        @media (max-width: 1024px) {
            .tile-type-section {
                max-width: 900px;
                padding: 30px;
            }
        }

        @media (max-width: 900px) {
            .tile-type-flex {
                flex-direction: column;
                align-items: center;
                gap: 25px;
            }

            .tile-type-img {
                width: 100%;
                max-width: 400px;
                height: auto;
            }

            .tile-type-desc {
                max-width: 100%;
                text-align: center;
            }
        }

        @media (max-width: 768px) {
            .homepage-message {
                padding: 60px 20px;
            }

            .homepage-message h1 {
                font-size: 2rem;
            }

            .homepage-message p {
                font-size: 1rem;
            }

            .tile-selection-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .tile-selection-item {
                margin-bottom: 20px;
            }
        }

        @media (max-width: 600px) {
            .tile-type-section {
                width: 100vw;
                left: 50%;
                right: 50%;
                margin-left: -50vw;
                margin-right: -50vw;
                padding: 20px 0;
                margin-top: 0;
                margin-bottom: 40px;
                border-radius: 0;
                box-sizing: border-box;
                overflow-x: hidden;
            }

            .tile-type-toggle {
                gap: 10px;
            }

            .tile-type-toggle button {
                padding: 10px 20px;
                font-size: 1rem;
            }

            .tile-selection-section h2 {
                font-size: 1.5rem;
                margin-bottom: 20px;
            }

            .patterns-and-designs-section h2 {
                font-size: 1.5rem;
                margin-bottom: 20px;
            }

            /* Fix for the patterns and designs section */
            .pattern-grid {
                grid-template-columns: repeat(2, 1fr); /* Two columns on mobile */
            }
            
            .pattern-category h3 {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 480px) {
            .homepage-message {
                padding: 40px 15px;
            }

            .homepage-message h1 {
                font-size: 1.8rem;
            }

            .tile-type-section {
                padding: 10px 0;
            }
            
            .tile-selection-grid {
                grid-template-columns: 1fr;
            }

            /* Mobile view: 4 tiles in a single line */
            .pattern-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 10px;
                justify-content: space-between;
                overflow-x: auto;
                white-space: nowrap;
            }

            .pattern-tile {
                width: 100px; /* Fixed width for single-line scrolling */
                padding-top: 100px;
                flex-shrink: 0;
            }
            
            .pattern-category {
                padding: 20px 10px;
            }
            
            .pattern-category h3 {
                font-size: 1rem;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="homepage-message">
        <div class="homepage-message-container">
            <h1>Shopping made <span class="easy">easy!</span></h1>
            <p>
                Discover an easy shopping experience with Rich Anne Lea Tiles Trading! Explore a vast selection of textures,
                colors, and styles that bring your design vision to life. With our high-quality tiles, you can effortlessly
                blend creativity and functionality, transforming any space into a masterpiece. Whether you're looking for
                modern elegance or timeless charm, we help you find the perfect match for your aesthetic. Shop with us
                today and turn your ideas into reality!
            </p>
        </div>
    </div>
    
    <div class="tile-type-section">
        <div class="tile-type-toggle">
            <button id="glossyBtn" class="active">Glossy</button>
            <button id="matteBtn">Matte</button>
        </div>
        <div id="glossyContent" class="tile-type-content">
            <div class="tile-type-flex">
                <img src="images/glossy.PNG" alt="Glossy Tile" class="tile-type-img">
                <div class="tile-type-desc">
                    <h2>Glossy</h2>
                    <hr>
                    <p>Rich Anne Tiles Trading offers a premium selection of glossy tiles that combine elegance and durability. Perfect for modern homes and businesses, these tiles feature a smooth, reflective finish that enhances any space with style and sophistication. Upgrade your floors and walls with Rich Anne Tiles Trading today!</p>
                </div>
            </div>
        </div>
        <div id="matteContent" class="tile-type-content" style="display:none;">
            <div class="tile-type-flex">
                <img src="images/matte.PNG" alt="Matte Tile" class="tile-type-img">
                <div class="tile-type-desc">
                    <h2>Matte</h2>
                    <hr>
                    <p>Rich Anne Tiles Trading presents a high-quality selection of matte tiles, perfect for creating a stylish and modern space. With their soft, non-reflective finish, these tiles offer a sleek look while providing excellent slip resistance. Elevate your space with Rich Anne Tiles Trading today!</p>
                </div>
            </div>
        </div>
    </div>

    <div class="tile-selection-section">
        <h2>Our visualizer supports a wide range of tile selection</h2>
        <div class="tile-selection-grid">
            <div class="tile-selection-item">
                <img src="images/indoor.PNG" alt="Indoor Tiles">
                <p>Indoor</p>
            </div>
            <div class="tile-selection-item">
                <img src="images/outdoor.PNG" alt="Outdoor Tiles">
                <p>Outdoor</p>
            </div>
            <div class="tile-selection-item">
                <img src="images/industrial.PNG" alt="Industrial Tiles">
                <p>Industrial</p>
            </div>
            <div class="tile-selection-item">
                <img src="images/pool.PNG" alt="Pool Tiles">
                <p>Pool</p>
            </div>
            <div class="tile-selection-item">
                <img src="images/countertops.PNG" alt="Countertop Tiles">
                <p>Countertops</p>
            </div>
        </div>
    </div>
    
    <div class="patterns-and-designs-section">
        <h2>Patterns and Designs</h2>
        <div class="pattern-category">
            <h3>FLORAL</h3>
            <div class="pattern-grid">
                <div class="pattern-tile" style="background-image: url('images/p&d/floral1.PNG');"></div>
                <div class="pattern-tile" style="background-image: url('images/p&d/floral2.PNG');"></div>
                <div class="pattern-tile" style="background-image: url('images/p&d/floral3.PNG');"></div>
                <div class="pattern-tile" style="background-image: url('images/p&d/floral4.PNG');"></div>
            </div>
        </div>
        <div class="pattern-category">
            <h3>MINIMALIST</h3>
            <div class="pattern-grid">
                <div class="pattern-tile" style="background-image: url('images/p&d/minimalist1.PNG');"></div>
                <div class="pattern-tile" style="background-image: url('images/p&d/minimalist2.PNG');"></div>
                <div class="pattern-tile" style="background-image: url('images/p&d/minimalist3.PNG');"></div>
                <div class="pattern-tile" style="background-image: url('images/p&d/minimalist4.PNG');"></div>
            </div>
        </div>
        <div class="pattern-category">
            <h3>BLACK AND WHITE</h3>
            <div class="pattern-grid">
                <div class="pattern-tile" style="background-image: url('images/p&d/b&w1.PNG');"></div>
                <div class="pattern-tile" style="background-image: url('images/p&d/b&w2.PNG');"></div>
                <div class="pattern-tile" style="background-image: url('images/p&d/b&w3.PNG');"></div>
                <div class="pattern-tile" style="background-image: url('images/p&d/b&w4.PNG');"></div>
            </div>
        </div>

    </div>
    
    <script>
        const glossyBtn = document.getElementById('glossyBtn');
        const matteBtn = document.getElementById('matteBtn');
        const glossyContent = document.getElementById('glossyContent');
        const matteContent = document.getElementById('matteContent');

        glossyBtn.addEventListener('click', function() {
            glossyBtn.classList.add('active');
            matteBtn.classList.remove('active');
            glossyContent.style.display = 'block';
            matteContent.style.display = 'none';
        });

        matteBtn.addEventListener('click', function() {
            matteBtn.classList.add('active');
            glossyBtn.classList.remove('active');
            matteContent.style.display = 'block';
            glossyContent.style.display = 'none';
        });
    </script>
    <?php
    include 'includes/footer.php';
    ?>
</body>

</html>