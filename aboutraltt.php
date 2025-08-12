<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About RALTT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link href="aboutraltt.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

</head>

<body>

    <header class="header">
        <a href="index.php" class="logo">
            <!-- Replaced placeholder with header-home.png -->
            <img src="images/header.png" alt="RALTT Logo">
            <!-- Removed the text as the image now contains the branding -->
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

    <section class="about-raltt-section">
        <div class="about-raltt-top">
            <div class="about-raltt-title">
                <span class="highlight">RALTT:</span> Bring Your<br>
                Visions to Life:<br>
                Design Stunning Spaces,<br>
                with our <span class="highlight2">2D Tile Visualizer</span>
            </div>
            <div class="about-raltt-tiles-img">
                <img src="images/tile1.png" alt="Tiles Stack">
            </div>
        </div>
        <div class="about-raltt-bottom">
            <div class="about-raltt-bottom-img">
                <img src="images/tile2.png" alt="Tile Samples">
            </div>
            <div class="about-raltt-bottom-desc">
                <p>
                    We will bring something new and refreshing to your usage of visualizer and tile e-commerce. RALTT is
                    here to maximize your tile design imagination while shopping on our tile e-commerce shop. <span
                        class="highlight">Check out now!</span>
                </p>
            </div>
        </div>
    </section>

    <div class="branch-columns">
        <section class="branches-section">

            <div class="branches-top">
                <hr class="branches-top-line" />
                <div class="branches-title">Our Branches</div>
            </div>

            <div class="branches-columns">
                <div class="branches-container" alt="1">
                    <div class="branches-left">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3875.679906514091!2d121.03429821483269!3d14.676041889810735!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b7b12e5f0c5d%3A0x9717f4be57b2345d!2sCaloocan%20City!5e0!3m2!1sen!2sph!4v1691791091927!5m2!1sen!2sph"
                            width="500" height="500" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>

                <div class="branches-container" alt="2">
                    <div class="branches-right">
                        <div class="branch-header">
                            <i class="fa-solid fa-angle-left" id="branch-left"></i>
                            <span id="branch-name">SAMARIA</span>
                            <i class="fa-solid fa-angle-right" id="branch-right"></i>
                        </div>


                        <div class="branch-card" id="branch-card">
                            <img src="SAMARIA.png" alt="Samaria Branch" />
                            <div class="branch-info-block">
                                <div class="info-title">Address</div>
                                <hr class="branches-bottom-line" />
                                <div class="info-desc">
                                    St. Vincent Ferrer Ave. Samaria Corner, Tala, Caloocan City
                                </div>
                            </div>
                            <div class="branch-info-block">
                                <div class="info-title">Operating Hours</div>
                                <hr class="branches-bottom-line" />
                                <div class="info-desc">8AM – 6PM</div>
                            </div>
                            <div class="branch-info-block">
                                <div class="info-title">Contact Details</div>
                                <hr class="branches-bottom-line" />
                                <div class="info-desc">0999 999 9999</div>
                            </div>
                        </div>

                    </div>
                </div>
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
        <!-- Footer bottom area, matching the layout in the screenshot, improved for spacing and animation -->
        <div class="footer-lower">
            <!-- Logo only (no text/credit, as it's in the image) -->
            <div class="footer-logo">
                <img src="images/logocopyright.png" alt="Rich Anne Lea Tiles Trading Logo">
            </div>
            <!-- Contact Us -->
            <div class="footer-contact">
                <span>Contact Us</span>
                <div>
                    <span class="icon">
                        <!-- Modern phone icon SVG -->
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
                        <!-- Modern location icon SVG -->
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path
                                d="M10 2.5a6 6 0 0 0-6 6c0 4.2 5.2 8.6 5.4 8.8.3.2.7.2 1 0 .2-.2 5.4-4.6 5.4-8.8a6 6 0 0 0-6-6zm0 8.2a2.2 2.2 0 1 1 0-4.4 2.2 2.2 0 0 1 0 4.4z"
                                fill="currentColor" />
                        </svg>
                    </span>
                    <span>Phase 6, Camarin, North Caloocan</span>
                </div>
            </div>
            <!-- About Us -->
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

        // Optional: Close dropdown if clicking outside (for desktop)
        window.addEventListener('click', function (event) {
            if (!event.target.matches('.dropbtn')) {
                const dropdowns = document.getElementsByClassName("dropdown-content");
                for (let i = 0; i < dropdowns.length; i++) {
                    const openDropdown = dropdowns[i];
                    if (openDropdown.style.display === 'block') { // Check if it's currently displayed
                        openDropdown.style.display = 'none';
                        openDropdown.style.opacity = '0';
                        openDropdown.style.transform = 'translateY(10px)';
                    }
                }
            }
        });

        // For mobile, make dropdown toggle on click of dropbtn
        document.querySelectorAll('.dropdown .dropbtn').forEach(button => {
            button.addEventListener('click', function (event) {
                if (window.innerWidth <= 991) { // Apply only for mobile
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
                    event.stopPropagation(); // Prevent immediate closing from window click listener
                }
            });
        });
    </script>

    <script>
        const branches = [
            { name: "SAMARIA", img: "SAMARIA.png", address: "St. Vincent Ferrer Ave. Samaria Corner, Tala, Caloocan City", hours: "8AM – 6PM", contact: "0999 999 9999" },
            { name: "KIKO", img: "KIKO.png", address: "Kiko, Camarin Road, Caloocan City", hours: "9AM – 7PM", contact: "0999 999 9999" },
            { name: "DEPARO", img: "DEPARO.png", address: "189 Deparo Road, Caloocan City", hours: "10AM – 8PM", contact: "0999 999 9999" },
            { name: "VANGUARD", img: "VANGUARD.png", address: "Phase 6, Vanguard, Camarin, North Caloocan", hours: "8AM – 5PM", contact: "0999 999 9999" },
            { name: "BRIXTON", img: "BRIXTON.png", address: "Coaster St. Brixtonville Subdivision, Caloocan City", hours: "7AM – 9PM", contact: "0999 999 9999" }
        ];

        let currentIndex = 0;
        const branchNameElem = document.getElementById('branch-name');
        const branchCardElem = document.getElementById('branch-card');

        function updateBranchContent(index) {
            const branch = branches[index];
            branchNameElem.textContent = branch.name;

            branchCardElem.innerHTML = `
    <img src="${branch.img}" alt="${branch.name} Branch" />
    <div class="branch-info-block">
      <div class="info-title">Address</div>
      <hr class="branches-bottom-line" />
      <div class="info-desc">${branch.address}</div>
    </div>
    <div class="branch-info-block">
      <div class="info-title">Operating Hours</div>
      <hr class="branches-bottom-line" />
      <div class="info-desc">${branch.hours}</div>
    </div>
    <div class="branch-info-block">
      <div class="info-title">Contact Details</div>
      <hr class="branches-bottom-line" />
      <div class="info-desc">${branch.contact}</div>
    </div>
  `;
        }

        function slideTransition(newIndex, direction) {
            const slideOutClass = direction === "left" ? "slide-out-left" : "slide-out-right";
            const slideInClass = direction === "left" ? "slide-in-right" : "slide-in-left";

            branchCardElem.classList.add(slideOutClass);
            branchCardElem.addEventListener('transitionend', function handler() {
                branchCardElem.classList.remove(slideOutClass);

                updateBranchContent(newIndex);
                branchCardElem.classList.add(slideInClass);
                void branchCardElem.offsetWidth;

                branchCardElem.addEventListener('transitionend', function handler2() {
                    branchCardElem.classList.remove(slideInClass);
                    branchCardElem.removeEventListener('transitionend', handler2);
                }, { once: true });

                branchCardElem.removeEventListener('transitionend', handler);
            }, { once: true });
        }

        document.getElementById('branch-left').addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + branches.length) % branches.length;
            slideTransition(currentIndex, 'left');
        });

        document.getElementById('branch-right').addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % branches.length;
            slideTransition(currentIndex, 'right');
        });
        updateBranchContent(currentIndex);

    </script>

</body>

</html>