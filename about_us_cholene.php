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
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            /* background: linear-gradient(180deg, #FFE7D7 0%, #FFF 100%); */
            min-height: 100vh;
            color: #222;
        }
        .about-raltt-section {
            background: linear-gradient(180deg, #FFE7D7 0%, #FFF 100%);
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding-top: 200px;
            box-sizing: border-box;
        }
        .about-raltt-top {
            width: 100%;
            height: 380px;
            max-width: 1200px;
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            justify-content: space-between;
            gap: 40px;
            margin-bottom: 60px;
            padding: 0 30px;
            
        }
        .about-raltt-title {
            flex: 2;
            font-size: 2.7rem;
            font-weight: 700;
            line-height: 1.13;
            color: #222;
            margin-bottom: 0;
        }
        .about-raltt-title .highlight {
            color: #F47C2E;
        }
        .about-raltt-title .highlight2 {
            color: #F47C2E;
        }
        .about-raltt-tiles-img {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: flex-end;
            min-width: 220px;
        }
        .about-raltt-tiles-img img {
            width: 500px;
            height: auto;
            object-fit: contain;
            border-radius: 10px;
            box-shadow: 0 4px 18px 0 rgba(0,0,0,0.07);
            background: #fff;
        }
        .about-raltt-bottom {
            width: 100%;
            max-width: 1100px;
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            justify-content: space-between;
            gap: 40px;
            padding: 0 30px;
            background-color: transparent;
        }
        .about-raltt-bottom-img {
            flex: 1.2;
            display: flex;
            align-items: flex-end;
            justify-content: flex-start;
        }
        .about-raltt-bottom-img img {
            width: 340px;
            height: auto;
            object-fit: contain;
            border-radius: 6px;
            background: #fff;
            box-shadow: 0 4px 18px 0 rgba(0,0,0,0.07);
        }
        .about-raltt-bottom-desc {
            flex: 2;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .about-raltt-bottom-desc p {
            font-size: 1.25rem;
            color: #757575;
            font-weight: 400;
            line-height: 1.4;
            text-align: center;
            margin: 0;
        }
        .about-raltt-bottom-desc .highlight {
            color: #F47C2E;
            font-weight: 700;
        }
        @media (max-width: 1000px) {
            .about-raltt-top, .about-raltt-bottom {
                flex-direction: column;
                align-items: center;
                gap: 30px;
                padding: 0 10px;
            }
            .about-raltt-title {
                font-size: 2rem;
                text-align: center;
            }
            .about-raltt-bottom-desc p {
                font-size: 1.08rem;
            }
            .about-raltt-bottom-img img {
                width: 220px;
            }
            .about-raltt-tiles-img img {
                width: 160px;
            }
        }
        @media (max-width: 600px) {
            .about-raltt-section {
                padding-top: 30px;
            }
            .about-raltt-title {
                font-size: 1.3rem;
            }
            .about-raltt-bottom-desc p {
                font-size: 1rem;
            }
            .about-raltt-bottom-img img {
                width: 120px;
            }
            .about-raltt-tiles-img img {
                width: 90px;
            }
        }
    </style>
</head>
<body>
    <section class="about-raltt-section">
        <div class="about-raltt-top">
            <div class="about-raltt-title">
                <span class="highlight">RALTT:</span> Bring Your<br>
                Visions to Life:<br>
                Design Stunning Spaces,<br>
                with our <span class="highlight2">2D Tile Visualizer</span>
            </div>
            <div class="about-raltt-tiles-img">
                <!-- Replace with your real image -->
                <img src="images/tiles_stack.png" alt="Tiles Stack">
            </div>
        </div>
        <div class="about-raltt-bottom">
            <div class="about-raltt-bottom-img">
                <!-- Replace with your real image -->
                <img src="images/tiles_sample.png" alt="Tile Samples">
            </div>
            <div class="about-raltt-bottom-desc">
                <p>
                    We will bring something new and refreshing to your usage of visualizer and tile e-commerce. RALTT is here to maximize your tile design imagination while shopping on our tile e-commerce shop. <span class="highlight">Check out now!</span>
                </p>
            </div>
        </div>
    </section>
</body>
</html>
<?php
include 'includes/footer.php';
?>