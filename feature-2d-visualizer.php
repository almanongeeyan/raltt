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
            max-width: 900px;
            margin: 0 auto;
            font-size: 1.08rem;
            color: #4B3B2B;
            font-weight: 400;
            line-height: 1.6;
        }
    </style>
</head>

<body>

    <section class="visualize-section">
        <div class="visualize-title">
            <span class="highlight">Visualize</span> Your Imagination
        </div>
        <div class="visualize-desc">
            You can navigate various textures, colors, styles, and a lot more and cherish everything in "Almost real life" virtualizations.<br>
            So, you can resolve choices that synchronize seamlessly with your visionary aesthetic. If you want to effortlessly weave the tapestry of imagination and actuality and allow your space to eloquently echo your distinctive panache, it's your time to contact us today!
        </div>
    </section>


</body>
</html>
<?php
include 'includes/footer.php';
?>