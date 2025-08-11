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
    <!-- <link rel="stylesheet" href="style.css"> -->
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
            line-height: 1.6;
            overflow-x: hidden;
        }

        .visualize-section {
            width: 100%;
            min-height: 380px;
            background: linear-gradient(180deg, #FFE7D7 0%, #FFF 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 70px 20px 60px 20px;
            text-align: center;
        }

        .visualize-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .visualize-title .highlight {
            color: #F47C2E;
        }

        .visualize-desc {
            max-width: 1000px;
            margin: 0 auto;
            font-size: 1.08rem;
            color: #4B3B2B;
            font-weight: 400;
            line-height: 1.6;
        }
        
        .tile-visualizer-section {
            width: 100%;
            height: 500px;
            display: flex;
            justify-content: center;
            background: #ffffffff;
        }
        .tile-visualizer-container {
            display: flex;
            align-items: center;
            gap: 38px;
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
            background: #ffffffff;
        }
        .tile-visualizer-image img {
            width: 400px;
            height: 350px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 18px 0 rgba(0,0,0,0.07);
            background: #eee;
        }
        .tile-visualizer-content {
            flex: 1;
            text-align: left;
        }
        .tile-visualizer-content h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #222;
        }
        .tile-visualizer-content p {
            font-size: 1.05rem;
            color: #444;
            margin-bottom: 10px;
        }
        .tile-visualizer-bold {
            font-weight: 600;
            color: #222;
            margin-bottom: 16px;
        }
        .visualizer-btn {
            background: #F47C2E;
            color: #fff;
            font-weight: 700;
            font-size: 1.08rem;
            border: none;
            border-radius: 8px;
            padding: 10px 28px;
            cursor: pointer;
            transition: background 0.2s;
            box-shadow: 0 2px 8px 0 rgba(244,124,46,0.08);
            text-decoration: none; 
        }
        .visualizer-btn:hover {
            background: #d9651c;
        }

        .visualizer-cards-section {
            width: 100%;
            margin-top: 160px;
            margin-bottom: 100px;
            display: flex;
            justify-content: center;
        }
        .visualizer-cards-container {
            display: flex;
            gap: 38px;
            justify-content: center;
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .visualizer-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 18px 0 rgba(0,0,0,0.07);
            padding: 38px 30px 32px 30px;
            min-width: 220px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: box-shadow 0.2s;
        }
        .visualizer-card:hover {
            box-shadow: 0 8px 28px 0 rgba(244,124,46,0.13);
        }
        .card-icon {
            font-size: 2.5rem;
            color: #F47C2E;
            margin-bottom: 18px;
        }
        .card-title {
            font-size: 1.18rem;
            font-weight: 600;
            color: #8B4A1B;
            text-align: center;
        }

        /* Responsive Styles */
        @media (max-width: 900px) {
            .tile-visualizer-container,
            .visualizer-cards-container {
                flex-direction: column;
                align-items: center;
                gap: 32px;
            }
            .tile-visualizer-content {
                text-align: center;
            }
        }
        @media (max-width: 600px) {
            .tile-visualizer-image img {
                width: 100%;
                height: auto;
            }
            .visualizer-card {
                min-width: 0;
                width: 100%;
                padding: 28px 10px 24px 10px;
            }
            .visualizer-cards-container {
                gap: 18px;
            }
        }

    </style>
</head>

<body>

    <section class="visualize-section">
        <div class="visualize-title">
            <span class="highlight">Visualize</span> Your Imagination
        </div>
        <div class="visualize-desc">
            You can navigate various textures, colors, styles, and a lot more and cherish everything in "Almost real life" virtualizations. <br>
            If you want to effortlessly weave the tapestry of imagination and actuality and allow your space to eloquently echo your distinctive panache, it's your time to <b> contact us today! </b>
        </div>
    </section>

    <!-- 2D Tile Visualizer Section -->
    <section class="tile-visualizer-section">
        <div class="tile-visualizer-container">
            <div class="tile-visualizer-image">
                <!-- Mock image, replace src with your real image later -->
                <img src="images/2d.PNG" alt="2D Tile Visualizer" />
            </div>
            <div class="tile-visualizer-content">
                <h2>2D Tile Visualizer</h2> 
                <p>
                    You will definitely enjoy visualizing tiles of your choice and selecting pre-designed rooms to witness their chosen products in their home environment.
                </p>
                <p class="tile-visualizer-bold">
                    <br>Let's enhance your shopping journey with our 2D Tile Visualizer!
                </p>
                <!-- <button class="visualizer-btn">Try Our Visualizer!</button> -->
                <br><button></button><a href="register.php" class="visualizer-btn">Try Our Visualizer!</a></button>
            </div>
        </div>
    </section>

    <!-- 3 Cards Section -->
    <section class="visualizer-cards-section">
        <div class="visualizer-cards-container">
            <div class="visualizer-card">
                <div class="card-icon">
                    <i class="fa-solid fa-diamond"></i>
                </div>
                <div class="card-title">Pick your desired tiles</div>
            </div>
            <div class="visualizer-card">
                <div class="card-icon">
                    <i class="fa-solid fa-th-large"></i>
                </div>
                <div class="card-title">Align them all you want</div>
            </div>
            <div class="visualizer-card">
                <div class="card-icon">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <div class="card-title">Play with your imaginations</div>
            </div>
        </div>
    </section>

</body>
</html>
<?php
include 'includes/footer.php';
?>