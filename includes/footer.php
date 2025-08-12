<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Design</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            /* Remove the background here! */
            background: #f7f7fa;
            /* Light background for the main site */
        }

        .content-placeholder {
            flex-grow: 1;
        }

        .footer {
            background-color: #2B3241;
            color: #fff;
            padding-top: 2rem;
            padding-bottom: 2rem;
            text-align: center;
            font-family: 'Inter', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            margin-top: 2rem;
            width: 100%;
        }

        .footer-line {
            width: 92%;
            height: 1px;
            background-color: #fff;
            margin-bottom: 2rem;
            margin-left: auto;
            margin-right: auto;
        }

        .footer-content {
            display: flex;
            flex-direction: row;
            align-items: stretch;
            /* Align top and bottom */
            justify-content: center;
            width: 92%;
            max-width: 1200px;
            margin-bottom: 0;
            flex-wrap: wrap;
            text-align: left;
            gap: 2.5rem;
        }

        .footer-left {
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            width: auto;
            margin: 0;
            flex-grow: 0;
        }

        .footer-square {
            width: 100%;
            max-width: 600px;
            border: 1px solid white;
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            align-items: flex-start;
            color: #fff;
            padding: 1.5rem 2.5rem;
            box-sizing: border-box;
            line-height: 1.5;
            font-size: 1rem;
            margin-bottom: 0;
            background: rgba(43,50,65,0.98);
            box-shadow: 0 2px 12px 0 rgba(0,0,0,0.08);
        }

        .footer-square-content {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            width: 100%;
            height: 100%;
            justify-content: flex-start;
            flex-wrap: wrap;
            gap: 2.5rem;
        }

        .footer-square-column {
            display: flex;
            flex-direction: column;
            min-width: 180px;
            padding: 0 1.5rem 0 0;
            box-sizing: border-box;
            margin-bottom: 0;
            color: #fff;
            text-align: left;
        }

        .footer-square-column p {
            font-weight: bold;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }

        .footer-square-column a {
            color: #fff;
            text-decoration: none;
            margin-bottom: 0.25rem;
            transition: color 0.3s cubic-bezier(.4,0,.2,1);
            display: inline-block;
            border-radius: 4px;
            padding: 2px 6px;
        }

        .footer-square-column a:hover {
            color: #EF7232;
            background: rgba(239,114,50,0.08);
        }

        .footer-right {
            text-align: left;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            line-height: 1.2;
            margin: 0;
            min-width: 340px;
        }

        .footer-checkout {
            color: #EF7232;
            font-weight: bold;
            font-size: 3.2rem;
            margin-bottom: 0.2rem;
            text-align: left;
            letter-spacing: 0.5px;
            line-height: 1.1;
        }

        .footer-delivery {
            color: #FFFFFF;
            font-weight: bold;
            font-size: 3.2rem;
            margin-bottom: 0;
            text-align: left;
            letter-spacing: 0.5px;
            line-height: 1.1;
            /* Removed animation/hover */
        }

        .footer-bottom-line {
            width: 92%;
            height: 1px;
            background-color: #fff;
            margin-top: 2rem;
            margin-bottom: 2rem;
            margin-left: auto;
            margin-right: auto;
            position: static;
        }

        .footer-lower {
            width: 92%;
            margin: 0 auto;
            background: #2B3241;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 2.5rem 0 0 0;
            flex-wrap: wrap;
            box-sizing: border-box;
            gap: 2rem;
        }

        .footer-logo {
            flex: 1 1 320px;
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            min-width: 180px;
        }
        .footer-logo img {
            height: 110px; /* Increased size */
            max-width: 400px;
            width: auto;
            transition: transform 0.3s cubic-bezier(.4,0,.2,1), box-shadow 0.3s cubic-bezier(.4,0,.2,1);
            border-radius: 8px;
        }
        .footer-logo img:hover {
            transform: scale(1.09) rotate(-2deg);
            box-shadow: 0 8px 32px 0 rgba(239,114,50,0.18);
            background: rgba(239,114,50,0.07);
        }

        .footer-contact, .footer-about {
            flex: 1 1 220px;
            min-width: 180px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-top: 0.5rem;
        }
        .footer-contact span, .footer-about span {
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 0.7rem;
            letter-spacing: 0.5px;
        }
        .footer-contact div, .footer-about a {
            font-size: 1rem;
        }
        .footer-contact div {
            display: flex;
            align-items: center;
            margin-bottom: 0.3rem;
        }
        .footer-contact span.emoji {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        .footer-about a {
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
            margin-bottom: 0.3rem;
            transition: color 0.3s cubic-bezier(.4,0,.2,1), transform 0.3s cubic-bezier(.4,0,.2,1), background 0.3s cubic-bezier(.4,0,.2,1);
            border-radius: 4px;
            padding: 2px 6px;
        }
        .footer-about a:hover {
            color: #EF7232;
            background: rgba(239,114,50,0.08);
            transform: translateY(-2px) scale(1.06);
        }

        .footer-contact .icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.5em;
            height: 1.5em;
            margin-right: 0.5em;
            color: #EF7232;
            font-size: 1.3em;
            background: none; /* No background */
            border-radius: 0;
            box-shadow: none;
            transition: none;
        }
        .footer-contact div:hover .icon {
            background: none;
            color: #EF7232;
            transform: none;
        }

        @media (max-width: 1100px) {
            .footer-content {
                flex-direction: column;
                align-items: center;
                gap: 1.5rem;
                text-align: center;
            }
            .footer-right, .footer-left {
                margin-right: 0;
                align-items: center;
                text-align: center;
                justify-content: center;
            }
            .footer-square {
                margin-bottom: 1.5rem;
                justify-content: center;
                padding: 1.2rem 0.5rem;
            }
            .footer-square-content {
                justify-content: center;
                gap: 1.5rem;
            }
            .footer-square-column {
                padding: 0 0.5rem;
                min-width: 140px;
            }
            .footer-right {
                min-width: unset;
            }
            .footer-checkout, .footer-delivery {
                font-size: 2rem;
                text-align: center;
            }
        }
        @media (max-width: 768px) {
            .footer-content {
                flex-direction: column;
                align-items: center;
                gap: 1.2rem;
            }
            .footer-square {
                max-width: 320px;
                width: 100%;
                padding: 1.2rem 0.5rem;
            }
            .footer-square-content {
                flex-direction: column;
                gap: 0.5rem;
            }
            .footer-square-column {
                min-width: unset;
                padding: 0;
            }
            .footer-right {
                min-width: unset;
            }
            .footer-checkout, .footer-delivery {
                font-size: 1.5rem;
                text-align: center;
            }
        }
        @media (max-width: 480px) {
            .footer-checkout {
                font-size: 1.2rem;
            }
            .footer-delivery {
                font-size: 1.2rem;
            }
            .footer-square {
                font-size: 0.95rem;
            }
            .footer-logo img {
                height: 40px;
                max-width: 120px;
            }
            .footer-lower {
                padding-top: 1.2rem;
            }
        }
    </style>
</head>

<body>
    <div class="content-placeholder"></div>

    <footer class="footer">
        <div class="footer-line"></div>
        <div class="footer-content">
            <div class="footer-left">
                <div class="footer-square">
                    <div class="footer-square-content">
                        <div class="footer-square-column">
                            <p>Features</p>
                            <a href="2d_visualizer_homepage.php">2D Tile Visualizer</a>
                            <a href="referral_code.php">Referral Code</a>
                        </div>
                        <div class="footer-square-column">
                            <p>Meet RALTT</p>
                            <a href="about_us_cholene.php">About Us</a>
                            <a href="product_view_homepage.php">Tile E-Commerce</a>
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
                            <path d="M16.7 13.6l-2.2-1c-.5-.2-1.1-.1-1.4.3l-.7.9c-2.1-1-3.8-2.7-4.8-4.8l.9-.7c.4-.4.5-.9.3-1.4l-1-2.2c-.3-.6-1-.9-1.6-.8l-1.7.3c-.6.1-1 .6-1 1.2C2.5 13.1 6.9 17.5 12.1 17.5c.6 0 1.1-.4 1.2-1l.3-1.7c.1-.6-.2-1.3-.9-1.6z" fill="currentColor"/>
                        </svg>
                    </span>
                    <span>+639817547870</span>
                </div>
                <div>
                    <span class="icon">
                        <!-- Modern location icon SVG -->
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M10 2.5a6 6 0 0 0-6 6c0 4.2 5.2 8.6 5.4 8.8.3.2.7.2 1 0 .2-.2 5.4-4.6 5.4-8.8a6 6 0 0 0-6-6zm0 8.2a2.2 2.2 0 1 1 0-4.4 2.2 2.2 0 0 1 0 4.4z" fill="currentColor"/>
                        </svg>
                    </span>
                    <span>Phase 6, Camarin, North Caloocan</span>
                </div>
            </div>
            <!-- About Us -->
            <div class="footer-about">
                <span>About us</span>
                <a href="#">Privacy</a>
                <a href="about_us_cholene.php">Other branches</a>
            </div>
        </div>
    </footer>
</body>

</html>
