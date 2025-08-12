<?php
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Website Privacy Policy</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
            width: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .policy-container {
            text-align: center;
            background: linear-gradient(180deg, #FFE7D7 0%, #FFF 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 150px 20px 60px 20px;
            width: 100%;
        }

        /* Desktop view */
        .policy-container h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 18px;
            color: #000;
        }

        .policy-container p {
            max-width: 900px;
            margin: 0 auto;
            font-size: 1.08rem;
            color: #4B3B2B;
            font-weight: 400;
            line-height: 1.6;
            padding: 0 10px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
            width: 100%;
        }

        .section {
            display: flex;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #ccc;
            flex-wrap: wrap;
        }

        .section img {
            width: 100px;
            height: 100px;
            margin-right: 60px;
            flex-shrink: 0;
        }

        .section-content {
            flex: 1;
            min-width: 0;
        }

        .section-content h2 {
            margin-bottom: 10px;
            font-size: 1.4rem;
        }

        .section-content ul {
            margin-top: 5px;
            padding-left: 20px;
        }

        .section-content p,
        .section-content ul li {
            font-size: 1rem;
        }

        /* ===== Mobile View Styling ===== */
        @media (max-width: 100px) {
            .policy-container h1 {
                font-size: 3rem;        /* Smaller for mobile */
                font-weight: 700;       
                line-height: 1.6;       /* Similar spacing to "Visualize" */
                letter-spacing: -0.5px; /* Slightly tighter text */
                margin-bottom: 18px;
                font-family: 'Inter', sans-serif; /* Keep same family but apply mobile-specific weight/size */
            }

            .section {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .section img {
                margin-bottom: 10px;
                margin-right: 0;
            }

            .section-content ul {
                padding-left: 0;
                list-style-position: inside;
            }
        }
    </style>
</head>

<body>

    <div class="policy-container">
        <h1>Website Privacy Policy</h1>
        <p>
            Rich Anne Lea Tiles Trading values your privacy and is committed to protecting your personal information. 
            This Privacy Policy outlines how we collect, use, and safeguard the data of our website visitors.
        </p>
    </div>

    <div class="container">

        <div class="section">
            <img src="https://img.icons8.com/ios-filled/50/000000/data-configuration.png" alt="Data Icon">
            <div class="section-content">
                <h2>Data Collection and Use</h2>
                <p>We may collect the following information from you through our Contact Form:</p>
                <ul>
                    <li>Name</li>
                    <li>Email Address</li>
                    <li>Home Address</li>
                    <li>Phone Number (if provided)</li>
                    <li>Message or Inquiry Details</li>
                </ul>
            </div>
        </div>

        <div class="section">
            <img src="https://img.icons8.com/ios-filled/50/000000/combo-chart--v1.png" alt="Analytics Icon">
            <div class="section-content">
                <h2>Website Analytics</h2>
                <p>We use tools like Google Analytics to collect non-identifiable data:</p>
                <ul>
                    <li>IP Address</li>
                    <li>Pages visited and time spent</li>
                    <li>Search queries used</li>
                    <li>Date and time of access</li>
                </ul>
            </div>
        </div>

        <div class="section">
            <img src="https://img.icons8.com/ios-filled/50/000000/cookie.png" alt="Cookies Icon">
            <div class="section-content">
                <h2>Cookies</h2>
                <p>We may use cookies to enhance user experience. You can manage or disable cookies in your browser settings.</p>
            </div>
        </div>
    </div>

</body>
</html>
<?php
include 'includes/footer.php';
?>