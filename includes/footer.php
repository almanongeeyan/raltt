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
        /* Styling for the footer */
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ensure footer stays at the bottom */
        }

        /* Basic styling for content above footer to push it down if needed */
        .content-placeholder {
            flex-grow: 1; /* Allows content to take available space */
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
            margin-top: 2rem; /* Added margin-top to separate from main content */
        }

        .footer-line {
            width: 90%;
            height: 1px;
            background-color: #fff;
            margin-bottom: 2rem;
        }

        .footer-content {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 90%;
            max-width: 1200px;
            margin-bottom: 0;
            flex-wrap: wrap; /* Allow content to wrap on smaller screens */
        }

        .footer-left {
            display: flex;
            align-items: stretch;
            width: auto;
            margin-right: 0.5rem;
            flex-grow: 1; /* Allow it to grow */
            justify-content: center; /* Center content within left section */
        }

        .footer-square {
            width: 90%; /* Adjusted for responsiveness */
            max-width: 500px; /* Max width for the square */
            height: auto;
            border: 1px solid white;
            margin-right: 0;
            display: flex;
            flex-direction: row;
            justify-content: space-around;
            align-items: flex-start;
            color: #fff;
            padding: 1rem;
            box-sizing: border-box;
            line-height: 1.5;
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .footer-square-content {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            width: 100%;
            height: 100%;
            justify-content: space-around;
            margin-right: 0.5rem;
            flex-wrap: wrap; /* Allow columns to wrap if needed */
        }

        .footer-square-column {
            display: flex;
            flex-direction: column;
            width: 45%; /* Adjusted width for two columns */
            padding-right: 1rem; /* Reduced padding */
            padding-left: 0;
            box-sizing: border-box;
            margin-bottom: 0;
            color: #fff;
            text-align: left;
        }

        .footer-square-column p:nth-child(1) {
            font-weight: bold;
            margin-bottom: 0.5rem; /* Space below heading */
        }

        .footer-square-column a {
            color: #fff;
            text-decoration: none;
            margin-bottom: 0.25rem;
            transition: color 0.3s ease, transform 0.3s ease; /* Smooth transition for hover */
            display: inline-block; /* Needed for transform */
        }

        .footer-square-column a:hover {
            color: #EF7232; /* Orange color on hover */
            transform: translateY(-3px); /* Slight upward movement */
        }

        .footer-text {
            font-size: 1rem;
            margin: 0;
            display: none; /* This was already hidden, keeping it hidden */
        }

        .footer-right {
            text-align: left;
            display: flex;
            flex-direction: column;
            align-items: flex-start; /* Align text to the left within this column */
            line-height: 1.2;
            width: auto;
            margin-right: 3rem;
            flex-grow: 1; /* Allow it to grow */
            justify-content: center; /* Center content vertically */
        }

        .footer-checkout {
            color: #EF7232;
            font-weight: bold;
            font-size: 2.8rem;
            margin-bottom: 0.5rem;
            text-align: left;
        }

        .footer-delivery {
            color: #FFFFFF;
            font-weight: bold;
            font-size: 2.5rem;
            margin-bottom: 0;
            text-align: left;
        }

        .footer-bottom-line {
            position: absolute;
            bottom: 0;
            left: 5%;
            width: 90%;
            height: 1px;
            background-color: #fff;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        /* Media queries for responsiveness */
        @media (max-width: 768px) {
            .footer-content {
                flex-direction: column;
                text-align: center;
            }

            .footer-left {
                margin-bottom: 1rem;
                justify-content: center;
                width: 100%;
                margin-right: 0; /* Remove right margin on small screens */
            }

            .footer-right {
                text-align: center; /* Center text on small screens */
                width: 100%;
                align-items: center; /* Center items on small screens */
                margin-right: 0; /* Remove right margin on small screens */
            }

            .footer-square {
                width: 90%; /* Make square take more width */
                max-width: 300px; /* Limit max width */
                flex-direction: column; /* Stack columns vertically */
                align-items: center; /* Center content when stacked */
                padding: 1.5rem 1rem; /* Adjust padding */
            }

            .footer-square-content {
                flex-direction: column;
                align-items: center;
            }

            .footer-square-column {
                width: 100%; /* Full width for columns when stacked */
                padding-right: 0;
                text-align: center; /* Center text in columns */
                margin-bottom: 1rem; /* Add space between stacked columns */
            }

            .footer-square-column p:nth-child(1) {
                margin-bottom: 0.25rem; /* Adjust spacing */
            }

            .footer-checkout {
                font-size: 2.2rem; /* Adjust font size for smaller screens */
            }

            .footer-delivery {
                font-size: 2rem; /* Adjust font size for smaller screens */
            }
        }

        @media (max-width: 480px) {
            .footer-checkout {
                font-size: 1.8rem;
            }

            .footer-delivery {
                font-size: 1.6rem;
            }
        }
    </style>
</head>

<body>
    <!-- Placeholder for main content to push footer down -->
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
    </footer>
</body>

</html>
