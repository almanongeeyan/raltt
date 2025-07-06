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
        margin-bottom: 0;
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
    }

    .footer-left {
        display: flex;
        align-items: stretch;
        width: auto;
        margin-right: 0.5rem;
    }

    .footer-square {
        width: 90%;
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
    }

    .footer-square-column {
        display: flex;
        flex-direction: column;
        width: 40%;
        padding-right: 2rem;
        padding-left: 0;
        box-sizing: border-box;
        margin-bottom: 0;
        color: #fff;
        text-align: left;
    }

    .footer-square-column p:nth-child(1) {
        font-weight: bold;
    }

    .footer-square-column p {
        white-space: nowrap;
        margin-bottom: 0.25rem;
    }


    .footer-text {
        font-size: 1rem;
        margin: 0;
        display: none;
    }

    .footer-right {
        text-align: left;
        display: flex;
        flex-direction: column;
        align-items: center;
        line-height: 1.2;
        width: auto;
        margin-right: 3rem;
        /* Increased right margin */
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

    @media (max-width: 768px) {
        .footer-content {
            flex-direction: column;
            text-align: center;
        }

        .footer-left {
            margin-bottom: 1rem;
            justify-content: center;
            width: 100%;
        }

        .footer-right {
            text-align: center;
            width: 100%;
        }

        .footer-square {
            width: 80%;
            max-width: 200px;
        }
    }
    </style>
</head>

<body>
    <footer class="footer">
        <div class="footer-line"></div>
        <div class="footer-content">
            <div class="footer-left">
                <div class="footer-square">
                    <div class="footer-square-content">
                        <div class="footer-square-column">
                            <p>Features</p>
                            <p>2D Tile Visualizer</p>
                            <p>Referral Code</p>
                        </div>
                        <div class="footer-square-column">
                            <p>Meet RALTT</p>
                            <p>About Us</p>
                            <p>Tile E-Commerce</p>
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