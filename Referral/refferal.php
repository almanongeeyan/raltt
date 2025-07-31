<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Design</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="refferal.css">
</head>

<body>

    <header class="header">
        <a href="index.php" class="logo">
            <img src="images/header.png" alt="RALTT Logo">
        </a>

        <nav class="navbar">
            <a href="#whats-new">What's New</a>
            <div class="dropdown">
                <button class="dropbtn">Features <i class="fas fa-caret-down"></i></button>
                <div class="dropdown-content">
                    <a href="2d_tile_visualizer.php"><i class="fas fa-cube"></i> 2D Tile Visualizer</a>
                    <a href="referral_code.php"><i class="fas fa-users"></i> Referral Code</a>
                </div>
            </div>
            <a href="product.php">Products</a>
            <a href="#aboutus">About Us</a>
        </nav>

        <a href="login.php" class="login-btn"> <i class="fas fa-user"></i> Login</a>
        <div class="fas fa-bars" id="menu"></div>
    </header>

    <div id="page1" class="scroll-section">
        <div class="centered-container">
            <h1 class="main-heading" id="1"> REFER TO A <span class="highlight-word"> FRIEND!</span></h1>
            <p class="sub-heading">Get a discount when a <span class="highlight-word">FRIEND</span> of yours enters your
                referral code.<br>
                For every order that your <span class="highlight-word">FRIEND</span> made, you get a reward point.
                Enjoy!
            </p>
        </div>
    </div>

    <div id="page2" class="scroll-section">
        <div class="cards-container">
            <div class="card">
                <h3 class="card-title">Invite Your Friend</h3>
                <img src="steponeicon.png" alt="Invite" class="card-image" />
            </div>
            <div class="card">
                <h3 class="card-title">Earn Points</h3>
                <img src="step2icon.png" alt="Earn Points" class="card-image" />
            </div>
            <div class="card">
                <h3 class="card-title"> Next Purchase Discount</h3>
                <img src="step3icon.png" alt="Discounts" class="card-image" />
            </div>
        </div>
    </div>

    <div id="page2" class="scroll-section">
        <div class="form-wrapper">
            <form class="large-form">
                <div class="inner-form left-form">
                    <label for="referral-code">Enter Your <span class="highlight-word">FRIEND'S</span> Referral
                        Code</label>
                    <br>
                    <input type="text" id="referral-code" placeholder="e.g., ABC123" required>
                    <button type="submit">Submit</button>
                </div>

                <div class="inner-form right-form">
                    <p class="big-text">Share your referral code to a <span class="highlight-word">FRIEND</span> and
                        earn rewards!</p>
                    <p class="big-text"
                        style="background: white; width:325px; height: 45px; display: flex; justify-content: center; align-items: center; margin: 0;">
                        REF-7X29QZ
                    </p>
                    <br>
                    <button type="button" class="share-button">Share</button>
                </div>
            </form>
        </div>
    </div>

    <div class="content-placeholder"></div>

    <footer class="footer">
        <div class="footer-line"></div>
        <div class="footer-content">
            <div class="footer-left">
                <div class="footer-square">
                    <div class="footer-square-content">
                        <div class="footer-square-column">
                            <p>Features</p>
                            <a href="2d_tile_visualizer.php">2D Tile Visualizer</a>
                            <a href="referral_code.php">Referral Code</a>
                        </div>
                        <div class="footer-square-column">
                            <p>Meet RALTT</p>
                            <a href="about_us.php">About Us</a>
                            <a href="tile_e_commerce.php">Tile E-Commerce</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-right">
                <span class="footer-checkout">Check Out Now,</span>
                <span class="footer-delivery">Same-day delivery.</span>
            </div>
        </div>
        <div class="footer-bottom-line"></div>
        <div class="footer-lower">
            <div class="footer-logo">
                <img src="images/logocopyright.png" alt="Rich Anne Lea Tiles Trading Logo">
            </div>
            <div class="footer-contact">
                <span>Contact Us</span>
                <div>
                    <span class="icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path
                                d="M16.7 13.6l-2.2-1c-.5-.2-1.1-.1-1.4.3l-.7.9c-2.1-1-3.8-2.7-4.8-4.8l.9-.7c.4-.4.5-.9.3-1.4l-1-2.2c-.3-.6-1-.9-1.6-.8l-1.7.3c-.6.1-1 .6-1 1.2C2.5 13.1 6.9 17.5 12.1 17.5c.6 0 1.1-.4 1.2-1l.3-1.7c.1-.6-.2-1.3-.9-1.6z"
                                fill="currentColor" />
                        </svg>
                    </span>
                    <span>+639817547870</span>
                </div>
                <div>
                    <span class="icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path
                                d="M10 2.5a6 6 0 0 0-6 6c0 4.2 5.2 8.6 5.4 8.8.3.2.7.2 1 0 .2-.2 5.4-4.6 5.4-8.8a6 6 0 0 0-6-6zm0 8.2a2.2 2.2 0 1 1 0-4.4 2.2 2.2 0 0 1 0 4.4z"
                                fill="currentColor" />
                        </svg>
                    </span>
                    <span>Phase 6, Camarin, North Caloocan</span>
                </div>
            </div>
            <div class="footer-about">
                <span>About us</span>
                <a href="#">Privacy</a>
                <a href="#">Other branches</a>
            </div>
        </div>
    </footer>

    <script>
        const menu = document.querySelector('#menu');
        const navbar = document.querySelector('.header .navbar');

        menu.addEventListener('click', () => {
            navbar.classList.toggle('active');
        });

        window.addEventListener('scroll', () => {
            navbar.classList.remove('active');
        });

        window.addEventListener('click', function (event) {
            if (!event.target.matches('.dropbtn')) {
                const dropdowns = document.getElementsByClassName("dropdown-content");
                for (let i = 0; i < dropdowns.length; i++) {
                    const openDropdown = dropdowns[i];
                    if (openDropdown.style.display === 'block') {
                        openDropdown.style.display = 'none';
                        openDropdown.style.opacity = '0';
                        openDropdown.style.transform = 'translateY(10px)';
                    }
                }
            }
        });

        document.querySelectorAll('.dropdown .dropbtn').forEach(button => {
            button.addEventListener('click', function (event) {
                if (window.innerWidth <= 991) {
                    const dropdownContent = this.nextElementSibling;
                    if (dropdownContent.style.display === 'block') {
                        dropdownContent.style.display = 'none';
                        dropdownContent.style.opacity = '0';
                        dropdownContent.style.transform = 'translateY(10px)';
                    } else {
                        dropdownContent.style.display = 'block';
                        dropdownContent.style.opacity = '1';
                        dropdownContent.style.transform = 'translateY(0)';
                    }
                    event.stopPropagation();
                }
            });
        });
    </script>

    <script>
        const scrollSteps = [0, 100, 200, 375];
        let currentStep = 0;
        let isScrolling = false;

        function scrollToVH(vh) {
            const px = (vh / 100) * window.innerHeight;
            window.scrollTo({ top: px, behavior: 'smooth' });
        }

        function handleWheel(e) {
            e.preventDefault();
            if (isScrolling) return;
            isScrolling = true;

            const direction = e.deltaY > 0 ? 1 : -1;
            currentStep = Math.min(Math.max(currentStep + direction, 0), scrollSteps.length - 1);
            scrollToVH(scrollSteps[currentStep]);

            setTimeout(() => {
                isScrolling = false;
            }, 900);
        }

        window.addEventListener('wheel', handleWheel, { passive: false });
    </script>

</body>

</html>