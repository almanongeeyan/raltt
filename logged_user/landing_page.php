<?php
include '../includes/headeruser.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rich Anne Lea Tiles Trading - Landing Page </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <style>
        .landing-hero-section {
            position: relative;
            width: 100vw;
            min-height: 100vh;
            background: url('../images/user/landingpagebackground.PNG') center center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .landing-hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9); /* 90% black overlay */
            z-index: 1;
        }
        .landing-hero-content {
            position: relative;
            z-index: 2;
            color: #fff;
            text-align: center;
        }
    </style>
</head>

<body>
    <section class="landing-hero-section">
        <div class="landing-hero-content">
            <!-- Add your hero content here, e.g. heading, subtitle, button -->
            <h1>Welcome to Rich Anne Lea Tiles Trading</h1>
            <p>Discover premium tiles for every space</p>
        </div>
    </section>
</body>
</html>