<?php
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About RALTT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link href="about_us.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

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
                <img src="images/tile1.png" alt="Tiles Stack">
            </div>
        </div>
        <div class="about-raltt-bottom">
            <div class="about-raltt-bottom-img">
                <img src="images/tile2.png" alt="Tile Samples">
            </div>
            <div class="about-raltt-bottom-desc">
                <p><br>
                    Want something new and refreshing experience on tile visualizer? RALTT is
                    here to maximize your tile design imagination while shopping on our tile e-commerce shop. 
                    <br><br><button><a href="user_login_form.php" class="checkout-btn">Check out now!</a></button>
                    
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
                            <img src="images/SAMARIA.png" alt="Samaria Branch" />
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

    <script>
        const branches = [
            { name: "SAMARIA", img: "images/SAMARIA.png", address: "St. Vincent Ferrer Ave. Samaria Corner, Tala, Caloocan City", hours: "8AM – 6PM", contact: "0999 999 9999" },
            { name: "KIKO", img: "images/KIKO.png", address: "Kiko, Camarin Road, Caloocan City", hours: "9AM – 7PM", contact: "0999 999 9999" },
            { name: "DEPARO", img: "imagesDEPARO.png", address: "189 Deparo Road, Caloocan City", hours: "10AM – 8PM", contact: "0999 999 9999" },
            { name: "VANGUARD", img: "images/VANGUARD.png", address: "Phase 6, Vanguard, Camarin, North Caloocan", hours: "8AM – 5PM", contact: "0999 999 9999" },
            { name: "BRIXTON", img: "images/BRIXTON.png", address: "Coaster St. Brixtonville Subdivision, Caloocan City", hours: "7AM – 9PM", contact: "0999 999 9999" }
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
<?php
include 'includes/header.php';
?>