<?php
// Enforce session and cache control to prevent back navigation after logout
session_start();
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Additional security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\' https: data: \'unsafe-inline\' \'unsafe-eval\'; img-src \'self\' https: data:; style-src \'self\' https: \'unsafe-inline\'; script-src \'self\' https: \'unsafe-inline\' \'unsafe-eval\';');

if (!isset($_SESSION['logged_in'])) {
    header('Location: ../connection/tresspass.php');
    exit();
}
include '../includes/headeruser.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rich Anne Lea Tiles Trading - Landing Page</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap"
      rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>
    <style>
      /* General Styles */
      :root {
        --primary-color: #7d310a;
        --secondary-color: #cf8756;
        --accent-color: #e8a56a;
        --dark-color: #270f03;
        --light-color: #f9f5f2;
        --text-dark: #333;
        --text-light: #777;
      }

      body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', sans-serif;
        overflow-x: hidden;
        color: var(--text-dark);
        background-color: var(--light-color);
      }

      /* Hero Section */
      .landing-hero-section {
        position: relative;
        width: 100vw;
        min-height: 100vh;
        background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
          url('../images/user/landingpagebackground.PNG') center center/cover
            no-repeat;
        display: flex;
        align-items: center;
        justify-content: space-between;
        overflow: hidden;
        padding: 65px 5vw 0 5vw;
        box-sizing: border-box;
      }

      /* Hero Content Container */
      .landing-hero-content {
        position: relative;
        z-index: 2;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 3vw;
      }

      .center-hero-img {
        max-width: 100%;
        max-height: 80vh;
        width: 700px;
        height: auto;
        transform: rotate(40deg);
        filter: drop-shadow(0 4px 15px rgba(0, 0, 0, 0.5));
        animation: float 6s ease-in-out infinite;
      }

      .center-hero-img:not([src]), 
      .center-hero-img[src=""],
      .center-hero-img[src*="unsplash"] {
        display: none;
      }

      .center-hero-img:not([src])::after,
      .center-hero-img[src=""]::after {
        content: 'Image Loading...';
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #999;
        font-size: 0.9rem;
      }

      @keyframes float {
        0%,
        100% {
          transform: rotate(40deg) translateY(0);
        }
        50% {
          transform: rotate(40deg) translateY(-20px);
        }
      }

      /* Text Beside Image */
      .landing-hero-text-overlay {
        flex: 1;
        text-align: left;
        padding-left: 0;
        pointer-events: auto;
      }

      .landing-hero-text-overlay .small-text {
        font-size: 1.2rem;
        font-weight: 600;
        letter-spacing: 1px;
        color: #fff;
        text-shadow: 1px 1px 2px #000;
      }

      .landing-hero-text-overlay .big-text {
        font-size: 3rem;
        font-weight: 900;
        color: var(--secondary-color);
        text-shadow: 2px 2px 8px #000;
        line-height: 1.1;
        margin: 10px 0;
      }

      .landing-hero-text-overlay .description {
        font-size: 1.1rem;
        color: #fff;
        margin-top: 20px;
        max-width: 500px;
        line-height: 1.6;
      }

      /* Featured Items Carousel Styles */
      .featured-section {
        background: var(--light-color);
        color: var(--text-dark);
        padding: 80px 0;
        text-align: center;
      }

      .section-header {
        margin-bottom: 30px;
      }

      .section-header .small-text {
        font-size: 1.1rem;
        font-weight: 500;
        letter-spacing: 1px;
        color: var(--text-light);
        display: block;
        margin-bottom: 10px;
  }

      .section-header .big-text {
        font-size: 2.5rem;
        font-weight: 900;
        margin: 0;
        line-height: 1.1;
        color: var(--primary-color);
      }

      .section-header .description {
        font-size: 1.1rem;
        color: var(--text-light);
        max-width: 700px;
        margin: 20px auto 0;
        line-height: 1.6;
      }

      /* White outline for Our Tile Selection text */
      .tile-categories-section .section-header .big-text {
        color: #fff;
        text-shadow: 
          -1px -1px 0 #000,
          1px -1px 0 #000,
          -1px 1px 0 #000,
          1px 1px 0 #000;
      }

      .featured-carousel {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 32px;
        position: relative;
        max-width: 1800px;
        width: 95vw;
        margin: 40px auto 0;
      }

      .featured-arrow {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
        color: var(--primary-color);
        cursor: pointer;
        transition: all 0.2s;
        z-index: 2;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        outline: none;
      }

      .featured-arrow:hover {
        color: #fff;
        background: var(--primary-color);
        transform: scale(1.1);
      }

      .featured-items-container {
        width: 100%;
        overflow: hidden;
        position: relative;
      }

      .featured-items {
        display: flex;
        gap: 32px;
        width: 100%;
        max-width: 1600px;
        transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        padding: 20px 0;
        min-height: 420px;
        transform: translateX(0);
      }

      .featured-item {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        padding: 30px 20px;
        width: 220px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: box-shadow 0.3s,
          transform 0.3s cubic-bezier(0.4, 1.4, 0.6, 1.1);
        border: 1px solid #f0f0f0;
        position: relative;
        z-index: 1;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.5s forwards;
      }

      @keyframes fadeInUp {
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      .featured-item:hover {
        box-shadow: 0 12px 40px rgba(207, 135, 86, 0.15),
          0 4px 15px rgba(0, 0, 0, 0.08);
        transform: translateY(-10px) scale(1.02);
        border-color: var(--secondary-color);
      }

      .featured-img-wrap {
        width: 160px;
        height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fafafa;
        border-radius: 15px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        overflow: hidden;
      }

      .featured-img-wrap img {
        width: 90%;
        height: 90%;
        object-fit: contain;
        border-radius: 15px;
        transition: transform 0.3s ease;
        background-color: #f5f5f5;
      }

      .featured-img-wrap img:not([src]), 
      .featured-img-wrap img[src=""],
      .featured-img-wrap img[src*="unsplash"] {
        display: none;
      }

      .featured-img-wrap img:not([src])::after,
      .featured-img-wrap img[src=""]::after {
        content: 'Image Loading...';
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #999;
        font-size: 0.9rem;
      }

      .featured-item:hover .featured-img-wrap img {
        transform: scale(1.05);
      }

      .featured-item .item-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: #333;
        letter-spacing: 0.5px;
      }

      .featured-item .item-price {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--secondary-color);
        margin-bottom: 20px;
      }

      .featured-item .add-to-cart {
        background: var(--primary-color);
        border: 2px solid var(--primary-color);
        color: white;
        border-radius: 30px;
        padding: 10px 24px;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 6px rgba(207, 135, 86, 0.1);
        outline: none;
        display: flex;
        align-items: center;
        gap: 8px;
      }

      .featured-item .add-to-cart:hover {
        background: var(--secondary-color);
        border-color: var(--secondary-color);
        color: #fff;
        box-shadow: 0 4px 12px rgba(207, 135, 86, 0.3);
        transform: translateY(-2px);
      }

      .featured-pagination {
        margin-top: 35px;
        display: flex;
        justify-content: center;
        gap: 12px;
      }

      .featured-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #e0e0e0;
        display: inline-block;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid #fff;
      }

      .featured-dot.active {
        background: var(--primary-color);
        box-shadow: 0 2px 8px rgba(125, 49, 10, 0.3);
        border-color: var(--primary-color);
        transform: scale(1.15);
      }

      /* Tile Categories Section */
      .tile-categories-section {
        background: var(--dark-color);
        color: #fff;
        padding: 80px 0;
        text-align: center;
        position: relative;
        overflow: hidden;
      }

      .tile-categories-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, rgba(125, 49, 10, 0.05) 25%, transparent 25%), 
                    linear-gradient(-45deg, rgba(125, 49, 10, 0.05) 25%, transparent 25%), 
                    linear-gradient(45deg, transparent 75%, rgba(125, 49, 10, 0.05) 75%), 
                    linear-gradient(-45deg, transparent 75%, rgba(125, 49, 10, 0.05) 75%);
        background-size: 20px 20px;
        background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        opacity: 0.3;
        z-index: 0; /* Lower z-index so it does not cover interactive elements */
      }

      .tile-categories-grid {
        display: flex;
        flex-wrap: nowrap;
        justify-content: center;
        gap: 20px;
        padding: 20px 0;
        max-width: 1400px;
        margin: 0 auto;
        overflow-x: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
      }

      .tile-categories-grid::-webkit-scrollbar {
        display: none;
      }

      /* Tile Categories Navigation Buttons - HIDDEN */
      .tile-categories-nav {
        display: none;
      }

      .tile-category {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 1.4, 0.6, 1.1);
        position: relative;
        opacity: 0;
        transform: translateY(20px);
        min-width: 200px;
        width: 200px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        cursor: pointer;
        border: 1px solid #f0f0f0;
      }

      .tile-category.animate {
        animation: fadeInUp 0.5s forwards;
      }

      .tile-category:nth-child(1) {
        animation-delay: 0.1s;
      }
      .tile-category:nth-child(2) {
        animation-delay: 0.2s;
      }
      .tile-category:nth-child(3) {
        animation-delay: 0.3s;
      }
      .tile-category:nth-child(4) {
        animation-delay: 0.4s;
      }
      .tile-category:nth-child(5) {
        animation-delay: 0.5s;
      }

      .tile-category:hover {
        box-shadow: 0 12px 40px rgba(207, 135, 86, 0.15),
          0 4px 15px rgba(0, 0, 0, 0.08);
        transform: translateY(-10px) scale(1.02);
        border-color: var(--secondary-color);
      }

      .tile-category-img {
        height: 150px;
        overflow: hidden;
        position: relative;
        flex-shrink: 0;
      }

      .tile-category-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
        background-color: #f5f5f5;
      }

      .tile-category-img img:not([src]), 
      .tile-category-img img[src=""],
      .tile-category-img img[src*="unsplash"] {
        display: none;
      }

      .tile-category-img img:not([src])::after,
      .tile-category-img img[src=""]::after {
        content: 'Image Loading...';
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #999;
        font-size: 0.9rem;
      }

      .tile-category:hover .tile-category-img img {
        transform: scale(1.05);
      }

      .tile-category-content {
        padding: 15px;
        text-align: center;
        background: #fff;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
      }

      .tile-category-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 8px;
      }

      .tile-category-desc {
        font-size: 0.8rem;
        color: #666;
        margin-bottom: 15px;
        flex: 1;
      }

      .explore-btn {
        display: inline-block;
        padding: 8px 16px;
        background: var(--primary-color);
        color: white;
        border-radius: 30px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
        font-size: 0.8rem;
      }

      .explore-btn:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
      }

      .explore-btn.add-to-cart-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 16px;
        background: var(--primary-color);
        color: white;
        border-radius: 30px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 1.4, 0.6, 1.1);
        font-size: 0.8rem;
        box-shadow: 0 4px 12px rgba(207, 135, 86, 0.2);
        width: 100%;
        max-width: 160px;
        margin: 0 auto;
        position: relative;
        overflow: hidden;
        align-self: center;
      }

      .explore-btn.add-to-cart-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
      }

      .explore-btn.add-to-cart-btn:hover::before {
        left: 100%;
      }

      .explore-btn.add-to-cart-btn:hover {
        background: var(--secondary-color);
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(207, 135, 86, 0.4);
      }

      .explore-btn.add-to-cart-btn:active {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(207, 135, 86, 0.3);
      }

      .explore-btn.add-to-cart-btn i {
        font-size: 0.75rem;
        transition: transform 0.3s ease;
      }

      .explore-btn.add-to-cart-btn:hover i {
        transform: scale(1.1);
      }

      /* New Section Styles */
      .shop-collection-section {
        background-color: var(--light-color);
        padding: 60px 5vw;
        color: var(--text-dark);
      }

      .shop-collection-container {
        display: flex;
        gap: 30px;
        max-width: 1500px;
        margin: 0 auto;
        flex-wrap: wrap;
      }

      .filter-sidebar {
        flex-basis: 280px;
        flex-shrink: 0;
        background: #fff;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        height: fit-content;
        position: relative;
        z-index: 10;
      }

      .filter-sidebar h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 25px;
      }

      .filter-group {
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
        margin-bottom: 20px;
      }

      .filter-group:last-child {
        border-bottom: none;
      }

      .filter-group h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 15px;
      }

      .filter-checkbox-group,
      .filter-radio-group {
        display: flex;
        flex-direction: column;
        gap: 12px;
      }

      .filter-checkbox,
      .filter-radio {
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        color: var(--text-light);
        cursor: pointer;
        transition: color 0.2s;
      }

      .filter-checkbox:hover,
      .filter-radio:hover {
        color: var(--primary-color);
      }

      .filter-checkbox input,
      .filter-radio input {
        margin-right: 10px;
        -webkit-appearance: none;
        appearance: none;
        width: 18px;
        height: 18px;
        border: 2px solid #ccc;
        border-radius: 4px;
        background-color: #f9f9f9;
        transition: all 0.2s;
        cursor: pointer;
      }

      .filter-radio input {
        border-radius: 50%;
      }

      .filter-checkbox input:checked,
      .filter-radio input:checked {
        border-color: var(--primary-color);
        background-color: var(--primary-color);
      }

      .filter-checkbox input:checked::before {
        content: '\2713';
        display: block;
        color: #fff;
        font-size: 12px;
        line-height: 14px;
        text-align: center;
      }

      .filter-radio input:checked::before {
        content: '';
        display: block;
        width: 8px;
        height: 8px;
        background: #fff;
        border-radius: 50%;
        margin: 3px;
      }

      .filter-apply-btn {
        width: 100%;
        padding: 12px;
        background-color: var(--primary-color);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 20px;
      }

      .filter-apply-btn:hover {
        background-color: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      }

      .product-display-main {
        flex: 1;
        min-width: 0;
      }

      .product-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
      }

      .product-header h2 {
        font-size: 2.5rem;
        font-weight: 900;
        margin: 0;
        color: var(--primary-color);
      }

      .product-header p {
        font-size: 1rem;
        color: var(--text-light);
        margin: 0;
      }

      .sort-by-container {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.9rem;
        color: var(--text-light);
      }

      .sort-by-container select {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 0.9rem;
        color: var(--text-dark);
        background-color: #fff;
      }

      .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
      }

      .product-card {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s, box-shadow 0.3s;
        position: relative;
      }

      .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
      }

      .product-card-img-wrap {
        height: 250px;
        overflow: hidden;
        position: relative;
      }

      .product-card-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
        background-color: #f5f5f5;
      }

      .product-card-img-wrap img:not([src]), 
      .product-card-img-wrap img[src=""],
      .product-card-img-wrap img[src*="unsplash"] {
        display: none;
      }

      .product-card-img-wrap img:not([src])::after,
      .product-card-img-wrap img[src=""]::after {
        content: 'Image Loading...';
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #999;
        font-size: 0.9rem;
      }

      .product-card:hover .product-card-img-wrap img {
        transform: scale(1.05);
      }

      .product-card-info {
        padding: 20px;
        text-align: center;
      }

      .product-card-info h3 {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0 0 5px;
        color: #333;
      }

      .product-card-info .price {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--secondary-color);
        margin-bottom: 15px;
      }

      .product-card-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
      }

      .product-card-btn {
        padding: 10px 20px;
        border: 2px solid var(--primary-color);
        border-radius: 30px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: transparent;
        color: var(--primary-color);
      }

      .product-card-btn:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(125, 49, 10, 0.3);
      }

      .add-to-cart-btn {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
      }

      .add-to-cart-btn:hover {
        background: var(--secondary-color);
        border-color: var(--secondary-color);
        color: white;
      }

      /* Labels for new products and sales */
      .product-card .label {
        position: absolute;
        top: 15px;
        left: 15px;
        background: var(--primary-color);
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
      }

      .product-card .label.bestseller {
        background-color: var(--secondary-color);
      }

      .product-card .label.sale {
        background-color: #d9534f;
      }

      /* Media Queries */
      @media (max-width: 1200px) {
        .featured-items {
          max-width: 900px;
          gap: 20px;
        }
        .featured-carousel {
          max-width: 1100px;
          gap: 25px;
        }
        .featured-item {
          width: 200px;
          padding: 25px 15px;
        }
        .featured-img-wrap {
          width: 140px;
          height: 140px;
        }
        .tile-category {
          min-width: 180px;
          width: 180px;
        }
        .tile-category-img {
          height: 130px;
        }
        .product-grid {
          grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        }
      }

      @media (max-width: 900px) {
        .landing-hero-section {
          flex-direction: column;
          justify-content: center;
          padding-top: 100px;
          padding-bottom: 50px;
        }
        .landing-hero-content {
          flex-direction: column;
          align-items: center;
          gap: 1.5rem;
          padding: 0 2vw;
        }
        .center-hero-img {
          max-height: 45vh;
          transform: rotate(25deg);
          margin-top: 5rem;
        }
        .landing-hero-text-overlay {
          text-align: center;
          padding: 0;
        }
        .landing-hero-text-overlay .big-text {
          font-size: 2.5rem;
        }
        .section-header .big-text {
          font-size: 2rem;
          margin-bottom: 40px;
        }
        .featured-items {
          max-width: 600px;
          gap: 16px;
        }
        .featured-carousel {
          max-width: 700px;
          gap: 20px;
        }
        .featured-item {
          width: 180px;
          padding: 20px 15px;
        }
        .featured-img-wrap {
          width: 120px;
          height: 120px;
        }
        .tile-category {
          min-width: 160px;
          width: 160px;
        }
        .tile-category-img {
          height: 120px;
        }
        .tile-category-title {
          font-size: 1rem;
        }
        .tile-category-desc {
          font-size: 0.75rem;
        }
        .shop-collection-container {
          flex-direction: column;
          gap: 30px;
        }
        .filter-sidebar {
          width: 100%;
          flex-basis: auto;
          order: 1;
          margin-bottom: 30px;
          padding: 25px;
          position: relative;
          top: 0;
          right: 0;
        }
        .product-display-main {
          order: 2;
          width: 100%;
        }
        .product-header {
          flex-direction: row;
          align-items: center;
          justify-content: space-between;
          gap: 15px;
          margin-bottom: 30px;
        }
        .product-header h2 {
          font-size: 2rem;
          margin: 0;
        }
        .product-header p {
          display: none;
        }
        .product-grid {
          grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        }
        .explore-btn.add-to-cart-btn {
          width: 100%;
          justify-content: center;
          padding: 12px 20px;
          font-size: 0.9rem;
          margin-top: 10px;
        }
      }

      @media (max-width: 700px) {
        .featured-items {
          max-width: 400px;
          gap: 12px;
        }
        .featured-carousel {
          max-width: 420px;
          gap: 15px;
        }
        .featured-item {
          width: 160px;
          padding: 18px 12px;
        }
        .featured-arrow {
          width: 40px;
          height: 40px;
          font-size: 1.2rem;
        }
        .featured-img-wrap {
          width: 100px;
          height: 100px;
        }
        .tile-category {
          min-width: 140px;
          width: 140px;
        }
        .tile-category-img {
          height: 100px;
        }
        .tile-category-content {
          padding: 12px;
        }
        .tile-category-title {
          font-size: 0.9rem;
        }
        .tile-category-desc {
          font-size: 0.7rem;
        }
        .explore-btn.add-to-cart-btn {
          width: 100%;
          justify-content: center;
          padding: 10px 16px;
          font-size: 0.85rem;
          margin-top: 8px;
        }
        .product-grid {
          grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
          gap: 15px;
        }
        .filter-sidebar {
          padding: 20px;
          margin-bottom: 20px;
        }
        .filter-sidebar h3 {
          font-size: 1.1rem;
          margin-bottom: 20px;
        }
        .filter-group {
          padding-bottom: 15px;
          margin-bottom: 15px;
        }
        .shop-collection-section {
          padding: 40px 3vw;
        }
        .product-header {
          flex-direction: column;
          align-items: flex-start;
          gap: 10px;
        }
        .product-header h2 {
          font-size: 1.8rem;
        }
        .product-header p {
          display: none;
        }
        .product-grid {
          grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        }
        .explore-btn.add-to-cart-btn {
          width: 100%;
          justify-content: center;
          padding: 12px 20px;
          font-size: 0.9rem;
          margin-top: 10px;
        }
      }

      @media (max-width: 500px) {
        .landing-hero-section {
          padding: 100px 2vw 20px;
          min-height: auto;
        }
        .landing-hero-content {
          padding: 0;
        }
        .center-hero-img {
          max-height: 35vh;
          transform: rotate(15deg);
          margin-top: 1rem;
        }
        .landing-hero-text-overlay .small-text {
          font-size: 1rem;
        }
        .landing-hero-text-overlay .big-text {
          font-size: 1.8rem;
        }
        .featured-section {
          padding: 40px 0;
        }
        .featured-dot {
          width: 8px;
          height: 8px;
        }
        .section-header .big-text {
          font-size: 1.8rem;
        }
        .tile-category {
          min-width: 110px;
          width: 110px;
        }
        .tile-category-img {
          height: 80px;
        }
        .tile-category-content {
          padding: 8px;
        }
        .tile-category-title {
          font-size: 0.75rem;
        }
        .tile-category-desc {
          font-size: 0.6rem;
        }
        .explore-btn.add-to-cart-btn {
          width: 100%;
          justify-content: center;
          padding: 6px 10px;
          font-size: 0.65rem;
          margin-top: 6px;
        }
        .product-grid {
          grid-template-columns: 1fr;
          gap: 20px;
        }
        .product-card-buttons {
          flex-direction: column;
          gap: 8px;
        }
        .product-card-btn {
          width: 100%;
          justify-content: center;
        }
        .featured-carousel {
          gap: 10px;
        }
        .featured-arrow {
          width: 35px;
          height: 35px;
          font-size: 1rem;
        }
        .filter-sidebar {
          padding: 18px;
          margin-bottom: 15px;
        }
        .filter-sidebar h3 {
          font-size: 1rem;
          margin-bottom: 15px;
        }
        .filter-group {
          padding-bottom: 12px;
          margin-bottom: 12px;
        }
        .product-header {
          flex-direction: column;
          align-items: flex-start;
          gap: 8px;
        }
        .product-header h2 {
          font-size: 1.6rem;
        }
      }

      @media (max-width: 400px) {
        .featured-items {
          max-width: 280px;
          gap: 6px;
        }
        .featured-item {
          width: 85px;
          padding: 10px 6px;
          min-height: 200px;
        }
        .featured-img-wrap {
          width: 60px;
          height: 60px;
          margin-bottom: 12px;
        }
        .featured-item .item-title {
          font-size: 0.7rem;
        }
        .featured-item .item-price {
          font-size: 0.8rem;
        }
        .featured-item .add-to-cart {
          padding: 5px 10px;
          font-size: 0.7rem;
        }
        .tile-category {
          min-width: 100px;
          width: 100px;
        }
        .tile-category-img {
          height: 70px;
        }
        .tile-category-content {
          padding: 6px;
        }
        .tile-category-title {
          font-size: 0.7rem;
        }
        .tile-category-desc {
          font-size: 0.55rem;
        }
        .explore-btn.add-to-cart-btn {
          padding: 5px 8px;
          font-size: 0.6rem;
        }
        .shop-collection-section {
          padding: 30px 2vw;
        }
      }
    </style>
  </head>

  <body>
    <?php
    // Extra check in body to force redirect if session is not valid
    if (!isset($_SESSION['logged_in'])) {
        echo "<script>window.location.href='../connection/tresspass.php';</script>";
        exit();
    }
    ?>
    <script>
      // Detect browser back navigation and force check for session
      window.addEventListener('pageshow', function (event) {
        if (
          event.persisted ||
          (window.performance && window.performance.navigation.type === 2)
        ) {
          fetch(window.location.href, { cache: 'reload', credentials: 'same-origin' }).catch(
            () => {
              window.location.href = '../connection/tresspass.php';
            }
          );
        }
      });
    </script>
    <section class="landing-hero-section">
      <div class="landing-hero-content">
        <img
          src="../images/user/landingpagetile1.png"
          alt="Landing Tile"
          class="center-hero-img"
        />
        <div class="landing-hero-text-overlay">
          <div class="small-text">STYLE IN YOUR EVERY STEP.</div>
          <div class="big-text">CHOOSE YOUR<br />TILES NOW.</div>
          <div class="description">
            Discover our premium collection of tiles that combine elegance,
            durability, and style to transform any space into a masterpiece.
          </div>
        </div>
      </div>
    </section>

    <section class="featured-section">
      <div class="section-header">
        <span class="small-text">Premium Selection</span>
        <h2 class="big-text">Featured Items</h2>
        <div class="description">
          Explore our handpicked selection of premium tiles that combine quality
          craftsmanship with exceptional design for your home or business.
        </div>
      </div>
      <div class="featured-carousel">
        <button class="featured-arrow prev" aria-label="Previous">
          <i class="fas fa-chevron-left"></i>
        </button>
        <div class="featured-items-container">
          <div class="featured-items"></div>
        </div>
        <button class="featured-arrow next" aria-label="Next">
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
      <div class="featured-pagination"></div>
    </section>

    <section class="tile-categories-section">
      <div class="tile-categories-container">
        <div class="section-header">
          <span class="small-text">Explore Our Collection</span>
          <h2 class="big-text">Our Tile Selection</h2>
          <div class="description">
            From classic ceramics to luxurious natural stone, find the perfect
            tiles to match your style and needs.
          </div>
        </div>

        <div class="tile-categories-grid">
          <div class="tile-category">
            <div class="tile-category-img">
              <img src="../images/user/tile1.jpg" alt="Ceramic Tiles" />
            </div>
            <div class="tile-category-content">
              <h3 class="tile-category-title">Ceramic Tiles</h3>
              <p class="tile-category-desc">
                Durable and versatile ceramic tiles for any space
              </p>
              <button class="explore-btn add-to-cart-btn">
                <i class="fa fa-search"></i> Explore Now
              </button>
            </div>
          </div>

          <div class="tile-category">
            <div class="tile-category-img">
              <img src="../images/user/tile2.jpg" alt="Porcelain Tiles" />
            </div>
            <div class="tile-category-content">
              <h3 class="tile-category-title">Porcelain Tiles</h3>
              <p class="tile-category-desc">
                Premium quality porcelain for high-end finishes
              </p>
              <button class="explore-btn add-to-cart-btn">
                <i class="fa fa-search"></i> Explore Now
              </button>
            </div>
          </div>

          <div class="tile-category">
            <div class="tile-category-img">
              <img src="../images/user/tile3.jpg" alt="Mosaic Tiles" />
            </div>
            <div class="tile-category-content">
              <h3 class="tile-category-title">Mosaic Tiles</h3>
              <p class="tile-category-desc">
                Artistic designs for unique decorative accents
              </p>
              <button class="explore-btn add-to-cart-btn">
                <i class="fa fa-search"></i> Explore Now
              </button>
            </div>
          </div>

          <div class="tile-category">
            <div class="tile-category-img">
              <img src="../images/user/tile4.jpg" alt="Natural Stone Tiles" />
            </div>
            <div class="tile-category-content">
              <h3 class="tile-category-title">Natural Stone</h3>
              <p class="tile-category-desc">
                Elegant natural stone for luxurious spaces
              </p>
              <button class="explore-btn add-to-cart-btn">
                <i class="fa fa-search"></i> Explore Now
              </button>
            </div>
          </div>

          <div class="tile-category">
            <div class="tile-category-img">
              <img src="../images/user/tile5.jpg" alt="Premium Tiles" />
            </div>
            <div class="tile-category-content">
              <h3 class="tile-category-title">Premium Tiles</h3>
              <p class="tile-category-desc">
                High-end premium tiles for luxury spaces
              </p>
              <button class="explore-btn add-to-cart-btn">
                <i class="fa fa-search"></i> Explore Now
              </button>
            </div>
          </div>
        </div>

        <!-- Navigation buttons removed as requested -->
      </div>
    </section>

    <section class="shop-collection-section">
      <div class="shop-collection-container">
        <div class="filter-sidebar">
          <h3 class="sidebar-title">Categories</h3>
          <div class="filter-group">
            <div class="filter-checkbox-group">
              <label class="filter-checkbox">
                <input type="checkbox" name="category" />
                Ceramic Tiles
              </label>
              <label class="filter-checkbox">
                <input type="checkbox" name="category" />
                Porcelain Tiles
              </label>
              <label class="filter-checkbox">
                <input type="checkbox" name="category" />
                Mosaic Tiles
              </label>
              <label class="filter-checkbox">
                <input type="checkbox" name="category" />
                Natural Stone
              </label>
              <label class="filter-checkbox">
                <input type="checkbox" name="category" />
                Outdoor Tiles
              </label>
            </div>
          </div>

          <h3 class="sidebar-title">Price Range</h3>
          <div class="filter-group">
            <div class="filter-radio-group">
              <label class="filter-radio">
                <input type="radio" name="price-range" />
                Under ₱500
              </label>
              <label class="filter-radio">
                <input type="radio" name="price-range" />
                ₱500 - ₱1000
              </label>
              <label class="filter-radio">
                <input type="radio" name="price-range" />
                ₱1000 - ₱2000
              </label>
              <label class="filter-radio">
                <input type="radio" name="price-range" />
                Over ₱2000
              </label>
            </div>
          </div>

          <button class="filter-apply-btn">Apply Filters</button>
        </div>

        <div class="product-display-main">
          <div class="product-header">
            <div>
              <h2 class="big-text">Premium Tiles</h2>
              <p>
                Browse our extensive collection of premium tiles for every room
                in your home or business.
              </p>
            </div>
            
          </div>

          <div class="product-grid">
            <div class="product-card">
              <span class="label bestseller">Bestseller</span>
              <div class="product-card-img-wrap">
                <img
                  src="../images/user/tile1.jpg"
                  alt="Premium Ceramic Tile"
                />
              </div>
              <div class="product-card-info">
                <h3>Premium Ceramic Tile</h3>
                <div class="price">₱1,250</div>
                <div class="product-card-buttons">
                  <button class="product-card-btn add-to-cart-btn">
                    <i class="fa fa-shopping-cart"></i> Add to Cart
                  </button>
                </div>
              </div>
            </div>

            <div class="product-card">
              <div class="product-card-img-wrap">
                <img
                  src="../images/user/tile2.jpg"
                  alt="Porcelain Tile"
                />
              </div>
              <div class="product-card-info">
                <h3>Porcelain Tile</h3>
                <div class="price">₱950</div>
                <div class="product-card-buttons">
                  <button class="product-card-btn add-to-cart-btn">
                    <i class="fa fa-shopping-cart"></i> Add to Cart
                  </button>
                </div>
              </div>
            </div>

            <div class="product-card">
              <span class="label">New</span>
              <div class="product-card-img-wrap">
                <img
                  src="../images/user/tile3.jpg"
                  alt="Mosaic Tile"
                />
              </div>
              <div class="product-card-info">
                <h3>Mosaic Tile</h3>
                <div class="price">₱1,750</div>
                <div class="product-card-buttons">
                  <button class="product-card-btn add-to-cart-btn">
                    <i class="fa fa-shopping-cart"></i> Add to Cart
                  </button>
                </div>
              </div>
            </div>

            <div class="product-card">
              <div class="product-card-img-wrap">
                <img
                  src="../images/user/tile4.jpg"
                  alt="Natural Stone Tile"
                />
              </div>
              <div class="product-card-info">
                <h3>Natural Stone Tile</h3>
                <div class="price">₱850</div>
                <div class="product-card-buttons">
                  <button class="product-card-btn add-to-cart-btn">
                    <i class="fa fa-shopping-cart"></i> Add to Cart
                  </button>
                </div>
              </div>
            </div>

            <div class="product-card">
              <span class="label sale">Sale</span>
              <div class="product-card-img-wrap">
                <img
                  src="../images/user/tile5.jpg"
                  alt="Classic Tile"
                />
              </div>
              <div class="product-card-info">
                <h3>Classic Tile</h3>
                <div class="price">₱2,100</div>
                <div class="product-card-buttons">
                  <button class="product-card-btn add-to-cart-btn">
                    <i class="fa fa-shopping-cart"></i> Add to Cart
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <script>
      const featuredItems = [
        {
          img: '../images/user/tile1.jpg',
          title: 'Premium Ceramic Tile',
          price: '₱1,250',
        },
        {
          img: '../images/user/tile2.jpg',
          title: 'Porcelain Tile',
          price: '₱950',
        },
        {
          img: '../images/user/tile3.jpg',
          title: 'Mosaic Tile',
          price: '₱1,750',
        },
        {
          img: '../images/user/tile4.jpg',
          title: 'Natural Stone Tile',
          price: '₱850',
        },
        {
          img: '../images/user/tile5.jpg',
          title: 'Classic Tile',
          price: '₱2,100',
        },
      ];

      const itemsPerPage = () => {
        if (window.innerWidth <= 600) return 3; // Show 3 items on mobile
        if (window.innerWidth <= 900) return 2;
        if (window.innerWidth <= 1200) return 3;
        return 5;
      };

      let currentPage = 0;
      let animating = false;

      function renderFeaturedItems(direction = 0) {
        const container = document.querySelector('.featured-items');
        if (!container) return;
        const perPage = itemsPerPage();
        const pageCount = Math.ceil(featuredItems.length / perPage);

        if (currentPage < 0) currentPage = pageCount - 1;
        if (currentPage >= pageCount) currentPage = 0;

        const start = currentPage * perPage;
        const end = start + perPage;

        if (direction !== 0 && !animating) {
          animating = true;
          
          // Calculate the exact translation needed
          const itemWidth = 220 + 32; // item width + gap
          const translateAmount = direction > 0 ? -itemWidth : itemWidth;
          
          container.style.transition = 'transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
          container.style.transform = `translateX(${translateAmount}px)`;

          setTimeout(() => {
            container.style.transition = 'none';
            updateFeaturedItems(container, start, end);
            container.style.transform = 'translateX(0)';

            setTimeout(() => {
              container.style.transition = 'transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
              animating = false;
            }, 50);
          }, 600);
        } else {
          updateFeaturedItems(container, start, end);
        }
        renderPagination();
      }

      function updateFeaturedItems(container, start, end) {
        // Clear container safely
        while (container.firstChild) {
          container.removeChild(container.firstChild);
        }
        
        const itemsToRender = featuredItems.slice(start, end);

        // Handle cases where the last page has fewer items
        while (itemsToRender.length < itemsPerPage()) {
          itemsToRender.push({
            img: '',
            title: '',
            price: '',
            isEmpty: true,
          });
        }

        itemsToRender.forEach((item, index) => {
          const div = document.createElement('div');
          div.className = 'featured-item';
          div.style.animationDelay = `${index * 0.1}s`;

          if (item.isEmpty) {
            div.classList.add('empty');
            div.style.visibility = 'hidden';
          } else {
            // Create elements safely instead of using innerHTML
            const imgWrap = document.createElement('div');
            imgWrap.className = 'featured-img-wrap';
            
            const img = document.createElement('img');
            img.src = item.img || '';
            img.alt = item.title || '';
            img.loading = 'lazy';
            
            const titleDiv = document.createElement('div');
            titleDiv.className = 'item-title';
            titleDiv.textContent = item.title || '';
            
            const priceDiv = document.createElement('div');
            priceDiv.className = 'item-price';
            priceDiv.textContent = item.price || '';
            
            const button = document.createElement('button');
            button.className = 'add-to-cart';
            button.innerHTML = '<i class="fa fa-shopping-cart"></i> Add to Cart';
            
            imgWrap.appendChild(img);
            div.appendChild(imgWrap);
            div.appendChild(titleDiv);
            div.appendChild(priceDiv);
            div.appendChild(button);
          }
          container.appendChild(div);
        });
      }

      function renderPagination() {
        const perPage = itemsPerPage();
        const pageCount = Math.ceil(featuredItems.length / perPage);
        const pagination = document.querySelector('.featured-pagination');
        if (!pagination) return;
        
        // Clear pagination safely
        while (pagination.firstChild) {
          pagination.removeChild(pagination.firstChild);
        }
        
        for (let i = 0; i < pageCount; i++) {
          const dot = document.createElement('span');
          dot.className = 'featured-dot' + (i === currentPage ? ' active' : '');
          dot.title = `Show items ${i * perPage + 1} - ${Math.min(
            (i + 1) * perPage,
            featuredItems.length
          )}`;
          
          // Use addEventListener instead of onclick for better security
          dot.addEventListener('click', () => {
            if (animating || i === currentPage) return;
            const direction = i > currentPage ? 1 : -1;
            currentPage = i;
            renderFeaturedItems(direction);
          });
          
          pagination.appendChild(dot);
        }
      }

      function nextFeatured() {
        if (animating) return;
        const perPage = itemsPerPage();
        const pageCount = Math.ceil(featuredItems.length / perPage);
        currentPage = (currentPage + 1) % pageCount;
        renderFeaturedItems(1);
      }

      function prevFeatured() {
        if (animating) return;
        const perPage = itemsPerPage();
        const pageCount = Math.ceil(featuredItems.length / perPage);
        currentPage = (currentPage - 1 + pageCount) % pageCount;
        renderFeaturedItems(-1);
      }

      window.addEventListener('resize', () => {
        const newPerPage = itemsPerPage();
        const oldPerPage = document.querySelectorAll('.featured-item:not(.empty)')
          .length;
        if (newPerPage !== oldPerPage) {
          currentPage = 0;
          renderFeaturedItems();
        }
      });

      document.addEventListener('DOMContentLoaded', () => {
        const nextBtn = document.querySelector('.featured-arrow.next');
        const prevBtn = document.querySelector('.featured-arrow.prev');
        
        // Use addEventListener instead of onclick for better security
        if (nextBtn) {
          nextBtn.addEventListener('click', nextFeatured);
        }
        if (prevBtn) {
          prevBtn.addEventListener('click', prevFeatured);
        }

        // Handle image loading errors (only for featured/product images, not tile-category images)
        const images = document.querySelectorAll('.featured-img-wrap img, .product-card-img-wrap img');
        images.forEach(img => {
          img.addEventListener('error', function() {
            this.style.display = 'none';
            const parent = this.parentElement;
            if (parent) {
              parent.style.background = 'linear-gradient(45deg, #f0f0f0 25%, transparent 25%), linear-gradient(-45deg, #f0f0f0 25%, transparent 25%)';
              parent.style.backgroundSize = '20px 20px';
              parent.style.display = 'flex';
              parent.style.alignItems = 'center';
              parent.style.justifyContent = 'center';
              const placeholder = document.createElement('span');
              placeholder.style.color = '#999';
              placeholder.style.fontSize = '0.9rem';
              placeholder.textContent = 'Image not available';
              while (parent.firstChild) {
                parent.removeChild(parent.firstChild);
              }
              parent.appendChild(placeholder);
            }
          });
        });

        // Animate tile categories when they come into view
        const tileCategories = document.querySelectorAll('.tile-category');
        // Always add click event listeners immediately
        tileCategories.forEach((category) => {
          // Add click functionality to tile categories
          category.addEventListener('click', function() {
            const title = this.querySelector('.tile-category-title').textContent;
            const description = this.querySelector('.tile-category-desc').textContent;
            Swal.fire({
              title: title,
              text: description,
              icon: 'info',
              confirmButtonText: 'Explore',
              confirmButtonColor: '#7d310a',
              showCancelButton: true,
              cancelButtonText: 'Close'
            });
          });
          // Add hover effect for better UX
          category.style.cursor = 'pointer';
        });

        // Animate with IntersectionObserver (optional, for fade-in effect only)
        if ('IntersectionObserver' in window) {
          const observer = new IntersectionObserver(
            (entries) => {
              entries.forEach((entry) => {
                if (entry.isIntersecting) {
                  entry.target.classList.add('animate');
                  observer.unobserve(entry.target);
                }
              });
            },
            { threshold: 0.1 }
          );
          tileCategories.forEach((category) => {
            observer.observe(category);
          });
        } else {
          // Fallback: add animate class immediately
          tileCategories.forEach((category) => category.classList.add('animate'));
        }

        renderFeaturedItems();
      });
    </script>
  </body>
</html>

