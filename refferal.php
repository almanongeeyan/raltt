<?php
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refferal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="refferal.css">
</head>

<body>
    <div class="content-placeholder">
        <h1 class="main-heading">
            REFER A FRIEND,<span class="highlight">GET REWARDS!</span>
        </h1>
        <p class="sub-text">Get a discount when a customer <span class="highlight">enters your referral code.</span> Enjoy the <span class="highlight"> Points </span> you earn!.</p>
    </div>

    <div class="cards-container">
  <div class="card">
    <img src="images/step1.png" alt="Card 1 Image">
  </div>
  <div class="card">
    <img src="images/step2.png" alt="Card 2 Image">
  </div>
  <div class="card">
    <img src="images/step3.png" alt="Card 3 Image">
  </div>
  <div class="card">
    <img src="images/step4.png" alt="Card 4 Image">
  </div>
</div>

<div class="footer-text-block">
  <h2 class="footer-heading">
    Join <span class="highlight">RALTT</span> Today and Start Referring!
  </h2>
</div>

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
        document.querySelectorAll('.header .navbar .dropdown > .dropbtn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                if (window.innerWidth <= 991) {
                    const dropdown = btn.parentElement;
                    dropdown.classList.toggle('open');
                    e.preventDefault();
                }
            });
        });
    </script>

</body>

</html>