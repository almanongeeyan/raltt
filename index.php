<?php
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal Bite Clinic</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
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

</body>

<script>
const menu = document.querySelector('#menu');
const navbar = document.querySelector('.header .navbar');

menu.addEventListener('click', () => {
    navbar.classList.toggle('active');
});

window.addEventListener('scroll', () => {
    navbar.classList.remove('active');
});
</script>

</html>
<?php
include 'includes/footer.php';
?>