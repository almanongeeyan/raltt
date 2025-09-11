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
</head>
<style>
  /* Changes in refferal */


body {
  background: #f8f5f2;
  min-height: 100vh;
  margin: 0;
  padding: 0;
}

/* Gradient section for referral header */
.gradient-section {
  width: 100vw;
  position: relative;
  left: 50%;
  right: 50%;
  margin-left: -50vw;
  margin-right: -50vw;
  padding: 80px 20px 60px;
  text-align: center;
  background: linear-gradient(to bottom, #ffece2 0%, #f8f5f2 100%);
  box-sizing: border-box;
  will-change: transform;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1;
}

.content-placeholder {
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: center;
  min-height: 60vh;
  padding: 1rem;
  text-align: center;
  position: relative;
  top: 100px;
  box-sizing: border-box;
  z-index: 20;
}

.main-heading {
    font-family: 'Inter', sans-serif;
    font-weight: 700;
    font-size: clamp(1.8rem, 5vw, 3rem);
    margin: 0;
    color: #222;
}

.highlight {
    color: #ff5722;
    font-weight: 900;
}

.sub-text {
  font-family: 'Inter', sans-serif;
  font-weight: 400;
  font-size: clamp(0.9rem, 1.8vw, 1.2rem);
  margin-top: 0.5rem;
  color: #555;
  margin-bottom: 1.5rem;
}

.cards-container {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  grid-template-rows: repeat(2, auto);
  gap: 10px;
  max-width: 700px;
  margin: 30px auto 60px;
  padding: 0 1rem;
  margin-bottom: 20px;
  position: relative;
  z-index: 5;
}

.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgb(0 0 0 / 0.1);
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    aspect-ratio: 1 / 1;
    transition: transform 0.3s ease;
    cursor: pointer;
}

.card:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgb(0 0 0 / 0.15);
}

.card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

@media (max-width: 400px) {
    .cards-container {
        grid-template-columns: 1fr;
        grid-template-rows: repeat(4, auto);
        max-width: 320px;
    }
}

.footer-text-block {
  max-width: 900px;
  margin: 100px auto 60px;
  padding: 0 1rem;
  text-align: center;
}

.footer-heading {
    font-family: 'Inter', sans-serif;
    font-weight: 700;
    font-size: clamp(2rem, 4vw, 3rem);
    color: #222;
    margin: 0;
}

.footer-text-block .highlight {
    color: #ff5722;
    font-weight: 900;
}
  </style>
<body>
  <section class="gradient-section">
    <div class="content-placeholder">
      <h1 class="main-heading">
        REFER A FRIEND,<span class="highlight">GET REWARDS!</span>
      </h1>
      <p class="sub-text">Get a discount when a customer <span class="highlight">enters your referral code.</span> Enjoy
        the <span class="highlight"> Points </span> you earn!.</p>
    </div>
  </section>

  <div class="cards-container" style="margin-top:-150px;">
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

 

</body>

</html>
<?php
include 'includes/footer.php';
?>