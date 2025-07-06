<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rich Anne Lea Tiles Trading</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>


    <style>

    </style>
</head>

<body>
    <header class="header">
        <nav class="navbar">
            <a href="#feature">Feature</a>
            <a href="#products">Products</a>
            <a href="#aboutus">About Us</a>
        </nav>
        <a href="login.php" class="login-btn"> <i class="fas fa-user"></i> Login</a>
        <div class="fas fa-bars" id="menu"></div>
    </header>

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
</body>

</html>