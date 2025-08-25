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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        /* Fix overlay by header and add gradient to first section */
        .about-raltt-section {
            margin-top: 80px; /* header height */
            background: linear-gradient(135deg, #fff 60%, #fbeee6 100%);
            position: relative;
            z-index: 1;
        }
        :root {
            --primary-color: #f0590e;
            --dark-bg: #2B3241;
            --card-bg: #3a4051;
            --text-color: #333;
            --white-color: white;
            --font-inter: 'Inter', sans-serif;
        }

        body {
            font-family: var(--font-inter);
            margin: 0;
            padding: 0;
            background-color: var(--white-color);
        }

        /* About RALTT Section */
        .about-raltt-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: auto auto;
            gap: 20px;
            align-items: center;
            padding: 2rem 5% 3rem 5%; /* Add extra bottom padding */
            background-color: transparent;
        }

        .grid-top-left {
            font-size: 40px;
            font-weight: bold;
            color: var(--text-color);
            text-align: left;
        }

        .highlight {
            color: var(--primary-color);
        }

        .grid-top-right img,
        .grid-bottom-left img {
            width: 100%;
            max-width: 500px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .grid-bottom-right {
            font-size: 20px;
            color: var(--text-color);
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }

        .grid-bottom-right p {
            margin: 0;
        }

        .checkout-btn {
            text-decoration: none;
            color: var(--white-color);
            background-color: var(--primary-color);
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
        }

        /* Branches Section */
        .branches-section {
            background-color: var(--dark-bg);
            color: var(--white-color);
            padding: 50px 5% 30px;
            position: relative;
        }

        .branches-top {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding-bottom: 20px;
            position: relative;
        }

        .branches-top::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: var(--white-color);
        }

        .branches-title {
            font-size: 60px;
            font-weight: 600;
            margin: 0;
        }

        .branches-columns {
            display: flex;
            flex-wrap: nowrap;
            gap: 50px;
            justify-content: center;
            padding-top: 20px;
            border-top: 2px solid var(--white-color);
        }

        .branches-left,
        .branches-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .branches-left {
            max-width: 600px;
            padding-top: 40px;
        }

        .branches-left iframe {
            width: 100%;
            height: 450px;
            border: 0;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .branches-right {
            gap: 20px;
            max-width: 450px;
        }

        .branch-header {
            display: flex;
            width: 100%;
            max-width: 300px;
            align-items: center;
            justify-content: space-between;
            border: 2px solid var(--white-color);
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 700;
            font-size: 24px;
            color: var(--white-color);
            user-select: none;
        }

        .branch-header i {
            cursor: pointer;
            color: var(--white-color);
            font-size: 20px;
            padding: 5px;
        }

        .branch-card {
            background-color: var(--card-bg);
            border-radius: 5px;
            padding: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            color: var(--white-color);
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 400px;
            transition: opacity 0.4s ease-in-out, transform 0.4s ease-in-out;
        }

        .branch-card img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .branch-info-block {
            margin-bottom: 10px;
        }

        .info-title {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .info-desc {
            font-size: 12px;
            line-height: 1.4;
        }

        .branches-bottom-line {
            width: 100%;
            height: 1px;
            background-color: var(--white-color);
            border: none;
            margin-bottom: 5px;
        }

        .branch-card.slide-out-left {
            opacity: 0;
            transform: translateX(-100%);
        }

        .branch-card.slide-out-right {
            opacity: 0;
            transform: translateX(100%);
        }

        .branch-card.slide-in-left,
        .branch-card.slide-in-right {
            opacity: 1;
            transform: translateX(0);
        }

        /* Responsive Design */
        @media (max-width: 991px) {
            .about-raltt-section {
                margin-top: 70px;
            }
            .about-raltt-grid {
                grid-template-columns: 1fr;
                grid-template-rows: auto auto auto auto;
                gap: 20px;
                padding: 2rem 3% 2.5rem 3%;
            }

            .grid-top-left,
            .grid-bottom-right {
                text-align: center;
                align-items: center;
                justify-content: center;
            }

            .grid-bottom-right p {
                text-align: center;
            }

            .grid-top-right,
            .grid-bottom-left {
                justify-self: center;
            }

            .branches-columns {
                flex-direction: column;
                gap: 20px;
                border-top: none;
            }

            .branches-left,
            .branches-right {
                max-width: 100%;
            }

            .branches-top::before {
                display: none;
            }

            .branches-columns::before {
                content: '';
                position: absolute;
                top: 0;
                left: 5%;
                right: 5%;
                height: 2px;
                background-color: var(--white-color);
            }
        }

        @media (max-width: 768px) {
            .about-raltt-section {
                margin-top: 60px;
            }
            .about-raltt-grid {
                padding: 1.5rem 2% 2rem 2%;
                gap: 15px;
            }
            .grid-top-left {
                font-size: 28px;
            }
            .grid-bottom-right {
                font-size: 16px;
            }
            .branches-title {
                font-size: 36px;
            }
            .branches-left iframe {
                height: 220px;
            }
        }

        @media (max-width: 480px) {
            .about-raltt-section {
                margin-top: 55px;
            }
            .about-raltt-grid {
                padding: 1rem 1% 1.5rem 1%;
                gap: 10px;
            }
            .grid-top-left {
                font-size: 20px;
            }
            .grid-bottom-right {
                font-size: 14px;
            }
            .branches-title {
                font-size: 24px;
            }
            .branches-left iframe {
                height: 260px;
                min-height: 200px;
                max-width: 100vw;
            }
            .branch-header {
                font-size: 16px;
            }
            .info-title {
                font-size: 11px;
            }
            .info-desc {
                font-size: 9px;
            }
        }
    </style>
</head>

<body>
    <section class="about-raltt-section">
        <div class="about-raltt-grid">
            <div class="grid-top-left">
                <span class="highlight">RALTT:</span> Bring Your<br>
                Visions to Life:<br>
                Design Stunning Spaces,<br>
                with our <span class="highlight">2D Tile Visualizer</span>
            </div>

            <div class="grid-top-right">
                <img src="images/tile1.png" alt="Tiles Stack">
            </div>

            <div class="grid-bottom-left">
                <img src="images/tile2.png" alt="Tile Samples">
            </div>

            <div class="grid-bottom-right">
                <p>
                    We will bring something new and refreshing to your usage
                    <br> of visualizer and tile e-commerce. RALTT is here
                    <br>to maximize your tile design imagination while
                    <br>shopping on our tile e-commerce shop. <span class="highlight">Check out now!</span>
                </p>
            </div>
        </div>
    </section>

    <section class="branches-section">
        <div class="branches-top">
            <div class="branches-title">Our Branches</div>
        </div>

        <div class="branches-columns">
            <div class="branches-left">
                <iframe id="branch-map"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15433.080516641774!2d121.03362143009946!3d14.75704764848384!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397bd586a117565%3A0x2832561ce14a3174!2sTala%2C%20Caloocan%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1625000000000!5m2!1sen!2sph"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>

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
    </section>

    <script>
        const branches = [
            {
                name: "SAMARIA",
                img: "images/SAMARIA.png",
                address: "St. Vincent Ferrer Ave. Samaria Corner, Tala, Caloocan City",
                hours: "8AM – 6PM",
                contact: "0999 999 9999",
                map: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15433.080516641774!2d121.03362143009946!3d14.75704764848384!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397bd586a117565%3A0x2832561ce14a3174!2sTala%2C%20Caloocan%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1625000000000!5m2!1sen!2sph"
            },
            {
                name: "KIKO",
                img: "images/KIKO.png",
                address: "Kiko, Camarin Road, Caloocan City",
                hours: "9AM – 7PM",
                contact: "0999 999 9999",
                map: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3858.627702581635!2d121.01168531478546!3d14.607425189785834!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397bd30f87a8987%3A0x89d25141b714777d!2sKiko%20Rd%2C%20Caloocan%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1625000000000!5m2!1sen!2sph"
            },
            {
                name: "DEPARO",
                img: "images/DEPARO.png",
                address: "189 Deparo Road, Caloocan City",
                hours: "10AM – 8PM",
                contact: "0999 999 9999",
                map: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3858.2961451796805!2d121.017676499333!3d14.75233823334116!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b1c722c4d1b9%3A0xc107b82c47609263!2sRich%20Anne%20Tiles!5e0!3m2!1sen!2sph!4v1756129529669!5m2!1sen!2sph"
            },
            {
                name: "VANGUARD",
                img: "images/VANGUARD.png",
                address: "Phase 6, Vanguard, Camarin, North Caloocan",
                hours: "8AM – 5PM",
                contact: "0999 999 9999",
                map: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3858.297019964697!2d121.06286101292358!3d14.759202001446935!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b919d7d11f69%3A0x288d3d951a8a2522!2sVanguard!5e0!3m2!1sen!2sph!4v1756129529669!5m2!1sen!2sph"
            },
            {
                name: "BRIXTON",
                img: "images/BRIXTON.png",
                address: "Coaster St. Brixtonville Subdivision, Caloocan City",
                hours: "7AM – 9PM",
                contact: "0999 999 9999",
                map: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3859.083321523455!2d120.97931341478523!3d14.583120689801826!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c8d9c22e4c2f%3A0xf6f7f6f7f6f7f6f7!2sBrixtonville%20Subdivision%2C%20Caloocan%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1625000000000!5m2!1sen!2sph"
            }
        ];

        let currentIndex = 0;
        const branchNameElem = document.getElementById('branch-name');
        const branchCardElem = document.getElementById('branch-card');
        const branchMapElem = document.getElementById('branch-map');

        function updateBranchContent(index) {
            const branch = branches[index];
            branchNameElem.textContent = branch.name;
            branchMapElem.src = branch.map;

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
include 'includes/footer.php';
?>