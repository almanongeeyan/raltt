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
    <title>Rich Anne Lea Tiles Trading - Premium Tiles Collection</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.all.min.js"></script>
    <style>
        /* CSS Variables */
        :root {
            --primary-color: #7d310a;
            --primary-dark: #5a2307;
            --secondary-color: #cf8756;
            --accent-color: #e8a56a;
            --dark-color: #270f03;
            --light-color: #f9f5f2;
            --text-dark: #333;
            --text-light: #777;
            --white: #ffffff;
            --gray-light: #f5f5f5;
            --shadow-light: rgba(0, 0, 0, 0.08);
            --shadow-medium: rgba(0, 0, 0, 0.15);
            --shadow-heavy: rgba(207, 135, 86, 0.3);
            --border-radius: 20px;
            --transition: all 0.3s cubic-bezier(0.4, 1.4, 0.6, 1.1);
            --container-width: 1400px;
        }

        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            color: var(--text-dark);
            background-color: var(--light-color);
            line-height: 1.6;
        }

        /* Utility Classes */
        .container {
            width: 100%;
            max-width: var(--container-width);
            margin: 0 auto;
            padding: 0 20px;
        }

        .section-padding {
            padding: 100px 0;
        }

        .text-center {
            text-align: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            outline: none;
        }

        .btn-primary {
            background: var(--primary-color);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px var(--shadow-heavy);
        }

        .btn-secondary {
            background: var(--secondary-color);
            color: var(--white);
        }

        .btn-secondary:hover {
            background: var(--accent-color);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px var(--shadow-heavy);
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
            position: relative;
        }

        .section-subtitle {
            font-size: 1.1rem;
            font-weight: 500;
            letter-spacing: 1.5px;
            color: var(--secondary-color);
            text-transform: uppercase;
            margin-bottom: 1rem;
            display: block;
        }

        .section-title {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .section-description {
            font-size: 1.1rem;
            color: var(--text-light);
            max-width: 700px;
            margin: 0 auto;
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.4) 100%),
                        url('../images/user/landingpagebackground.PNG') center center/cover no-repeat;
            display: flex;
            align-items: center;
            padding: 100px 0 60px;
            overflow: hidden;
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5rem;
            align-items: center;
            max-width: var(--container-width);
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            z-index: 2;
        }

        .hero-text {
            color: var(--white);
        }

        .hero-subtitle {
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            color: var(--secondary-color);
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
        }

        .hero-title span {
            color: var(--secondary-color);
        }

        .hero-description {
            font-size: 1.2rem;
            max-width: 500px;
            line-height: 1.7;
            margin-bottom: 2.5rem;
            opacity: 0.9;
        }

        .hero-cta {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .hero-image {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .hero-img {
            max-width: 100%;
            height: auto;
            max-height: 70vh;
            transform: rotate(35deg);
            filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.6));
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: rotate(35deg) translateY(0) rotate(0deg); }
            50% { transform: rotate(35deg) translateY(-20px) rotate(5deg); }
        }

        .hero-shape {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            opacity: 0.1;
            filter: blur(50px);
            z-index: -1;
            animation: pulse 8s ease-in-out infinite;
        }

        .hero-shape-1 {
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
        }

        .hero-shape-2 {
            bottom: -150px;
            left: -150px;
            width: 600px;
            height: 600px;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.1; transform: scale(1); }
            50% { opacity: 0.15; transform: scale(1.1); }
        }

        /* Featured Section */
        .featured-section {
            background: var(--light-color);
            padding: var(--section-padding);
            position: relative;
        }

        .featured-carousel {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            overflow: hidden;
            padding: 0 20px;
        }

        .swiper {
            width: 100%;
            padding: 30px 10px;
        }

        .swiper-slide {
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 10px 30px var(--shadow-light);
            transition: var(--transition);
            border: 1px solid rgba(0, 0, 0, 0.05);
            height: auto;
        }

        .swiper-slide:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px var(--shadow-heavy);
        }

        .carousel-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .swiper-slide:hover .carousel-img {
            transform: scale(1.05);
        }

        .carousel-content {
            padding: 1.5rem;
        }

        .carousel-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.75rem;
        }

        .carousel-price {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .carousel-btn {
            width: 100%;
            padding: 12px 24px;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .carousel-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px var(--shadow-heavy);
        }

        .swiper-button-next, 
        .swiper-button-prev {
            color: var(--primary-color);
            background: var(--white);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            box-shadow: 0 5px 15px var(--shadow-medium);
            transition: var(--transition);
        }

        .swiper-button-next:after, 
        .swiper-button-prev:after {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .swiper-button-next:hover, 
        .swiper-button-prev:hover {
            background: var(--primary-color);
            color: var(--white);
            transform: scale(1.1);
        }

        .swiper-pagination-bullet {
            width: 12px;
            height: 12px;
            background: var(--gray-light);
            opacity: 1;
        }

        .swiper-pagination-bullet-active {
            background: var(--primary-color);
        }

        /* Tile Categories Section */
        .tile-categories-section {
            background: linear-gradient(to bottom, var(--dark-color), #1a0a02);
            color: var(--white);
            padding: var(--section-padding);
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
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%237d310a' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.3;
            z-index: 1;
        }

        .tile-categories-section .section-title {
            color: var(--white);
            position: relative;
            z-index: 2;
        }

        .tile-categories-section .section-subtitle {
            color: var(--secondary-color);
        }

        .tile-categories-grid {
            display: flex;
            justify-content: center;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            flex-wrap: wrap;
        }

        .tile-category {
            background: linear-gradient(145deg, #2c1407, #1f0d04);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            transition: var(--transition);
            cursor: pointer;
            border: 1px solid rgba(125, 49, 10, 0.3);
            position: relative;
            width: 220px;
            opacity: 0;
            transform: translateY(30px);
        }

        .tile-category.animate {
            animation: fadeInUp 0.6s forwards;
        }

        .tile-category:nth-child(1) { animation-delay: 0.1s; }
        .tile-category:nth-child(2) { animation-delay: 0.2s; }
        .tile-category:nth-child(3) { animation-delay: 0.3s; }
        .tile-category:nth-child(4) { animation-delay: 0.4s; }
        .tile-category:nth-child(5) { animation-delay: 0.5s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tile-category:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 15px 40px rgba(207, 135, 86, 0.2);
            border-color: var(--secondary-color);
        }

        .tile-category-img {
            height: 160px;
            overflow: hidden;
            position: relative;
        }

        .tile-category-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s ease;
        }

        .tile-category:hover .tile-category-img img {
            transform: scale(1.15);
        }

        .tile-category-content {
            padding: 1.5rem;
            text-align: center;
            position: relative;
        }

        .tile-category-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 0.75rem;
        }

        .tile-category-desc {
            font-size: 0.9rem;
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .tile-category-btn {
            width: 100%;
            padding: 10px 20px;
            background: linear-gradient(to right, var(--primary-color), var(--primary-dark));
            color: var(--white);
            border: none;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .tile-category-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.7s;
        }

        .tile-category-btn:hover::before {
            left: 100%;
        }

        .tile-category-btn:hover {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .tile-category-btn i {
            transition: transform 0.3s ease;
        }

        .tile-category-btn:hover i {
            transform: translateX(3px);
        }

        /* Shop Collection Section */
        .shop-collection-section {
            background: var(--light-color);
            padding: var(--section-padding);
        }

        .shop-container {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 3rem;
            max-width: var(--container-width);
            margin: 0 auto;
        }

        .filter-sidebar {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: 0 10px 30px var(--shadow-light);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .filter-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--gray-light);
        }

        .filter-group {
            border-bottom: 1px solid var(--gray-light);
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-group:last-child {
            border-bottom: none;
        }

        .filter-group h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
        }

        .filter-options {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .filter-option {
            display: flex;
            align-items: center;
            font-size: 0.95rem;
            color: var(--text-light);
            cursor: pointer;
            transition: color 0.2s;
        }

        .filter-option:hover {
            color: var(--primary-color);
        }

        .filter-option input {
            margin-right: 0.75rem;
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary-color);
        }

        .filter-apply {
            width: 100%;
            padding: 14px;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 1rem;
        }

        .filter-apply:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px var(--shadow-heavy);
        }

        .product-main h2 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .product-main > p {
            font-size: 1.1rem;
            color: var(--text-light);
            margin-bottom: 2rem;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .product-card {
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 10px 30px var(--shadow-light);
            transition: var(--transition);
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px var(--shadow-medium);
        }

        .product-label {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--primary-color);
            color: var(--white);
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            z-index: 2;
        }

        .product-label.bestseller {
            background: var(--secondary-color);
        }

        .product-label.sale {
            background: #d9534f;
        }

        .product-label.new {
            background: #5cb85c;
        }

        .product-img-container {
            overflow: hidden;
            position: relative;
            height: 250px;
        }

        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-img {
            transform: scale(1.08);
        }

        .product-info {
            padding: 1.5rem;
            text-align: center;
        }

        .product-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.75rem;
        }

        .product-price {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .product-btn {
            width: 100%;
            padding: 12px 24px;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .product-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px var(--shadow-heavy);
        }

        /* Footer */
        .main-footer {
            background: var(--dark-color);
            color: var(--white);
            padding: 60px 0 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            max-width: var(--container-width);
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-column h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--secondary-color);
        }

        .footer-column p {
            margin-bottom: 1.5rem;
            line-height: 1.7;
            opacity: 0.8;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: var(--white);
            text-decoration: none;
            transition: color 0.3s;
            opacity: 0.8;
        }

        .footer-links a:hover {
            color: var(--secondary-color);
            opacity: 1;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
            transition: var(--transition);
        }

        .social-links a:hover {
            background: var(--secondary-color);
            transform: translateY(-3px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            margin-top: 50px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            opacity: 0.7;
        }

        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-color);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: var(--transition);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
        }

        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            background: var(--secondary-color);
            transform: translateY(-5px);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .hero-content {
                gap: 3rem;
            }
            
            .shop-container {
                grid-template-columns: 270px 1fr;
                gap: 2.5rem;
            }
        }

        @media (max-width: 992px) {
            .section-padding {
                padding: 80px 0;
            }
            
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 3rem;
            }
            
            .hero-title {
                font-size: 3.2rem;
            }
            
            .hero-img {
                max-height: 50vh;
                transform: rotate(25deg);
            }
            
            .hero-cta {
                justify-content: center;
            }
            
            .shop-container {
                grid-template-columns: 1fr;
                gap: 3rem;
            }
            
            .filter-sidebar {
                position: static;
                order: -1;
            }
            
            .tile-categories-grid {
                gap: 1.5rem;
            }
            
            .tile-category {
                width: 200px;
            }
        }

        @media (max-width: 768px) {
            .section-padding {
                padding: 70px 0;
            }
            
            .section-title {
                font-size: 2.2rem;
            }
            
            .hero-section {
                min-height: 80vh;
                padding: 80px 0 40px;
            }
            
            .hero-title {
                font-size: 2.8rem;
            }
            
            .hero-description {
                font-size: 1.1rem;
            }
            
            .hero-cta {
                flex-direction: column;
                align-items: center;
            }
            
            .tile-category {
                width: 100%;
                max-width: 280px;
            }
            
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 1.5rem;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 2.5rem;
            }
            
            .social-links {
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding: 0 15px;
            }
            
            .section-padding {
                padding: 60px 0;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .hero-title {
                font-size: 2.2rem;
            }
            
            .hero-img {
                max-height: 40vh;
                transform: rotate(15deg);
            }
            
            .tile-category-img {
                height: 140px;
            }
            
            .product-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-sidebar {
                padding: 1.5rem;
            }
            
            .back-to-top {
                bottom: 20px;
                right: 20px;
                width: 45px;
                height: 45px;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 1.9rem;
            }
            
            .hero-description {
                font-size: 1rem;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
            
            .tile-category-content {
                padding: 1.2rem;
            }
            
            .product-img-container {
                height: 220px;
            }
        }
    </style>
</head>
<body>
    <?php
    if (!isset($_SESSION['logged_in'])) {
        echo "<script>window.location.href='../connection/tresspass.php';</script>";
        exit();
    }
    ?>
    
    <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                fetch(window.location.href, { cache: 'reload', credentials: 'same-origin' }).catch(() => {
                    window.location.href = '../connection/tresspass.php';
                });
            }
        });
    </script>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-shape hero-shape-1"></div>
        <div class="hero-shape hero-shape-2"></div>
        
        <div class="hero-content">
            <div class="hero-text">
                <div class="hero-subtitle">STYLE IN YOUR EVERY STEP</div>
                <h1 class="hero-title">CHOOSE YOUR <span>PERFECT TILES</span></h1>
                <p class="hero-description">
                    Discover our premium collection of tiles that combine elegance,
                    durability, and style to transform any space into a masterpiece.
                    Experience the difference quality makes.
                </p>
                <div class="hero-cta">
                    <a href="#shop" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> Shop Now
                    </a>
                    <a href="#categories" class="btn btn-secondary">
                        <i class="fas fa-th-large"></i> Browse Categories
                    </a>
                </div>
            </div>
            <div class="hero-image">
                <img src="../images/user/landingpagetile1.png" alt="Premium Tile Selection" class="hero-img" />
            </div>
        </div>
    </section>

    <!-- Featured Section -->
    <section class="featured-section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Premium Selection</span>
                <h2 class="section-title">Featured Products</h2>
                <p class="section-description">
                    Explore our handpicked selection of premium tiles that combine quality
                    craftsmanship with exceptional design for your home or business.
                </p>
            </div>
            
            <div class="featured-carousel">
                <div class="swiper featuredSwiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img src="../images/user/tile1.jpg" alt="Premium Ceramic Tile" class="carousel-img" />
                            <div class="carousel-content">
                                <h3 class="carousel-title">Premium Ceramic Tile</h3>
                                <div class="carousel-price">₱1,250</div>
                                <button class="carousel-btn">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <img src="../images/user/tile2.jpg" alt="Porcelain Tile" class="carousel-img" />
                            <div class="carousel-content">
                                <h3 class="carousel-title">Porcelain Tile</h3>
                                <div class="carousel-price">₱950</div>
                                <button class="carousel-btn">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <img src="../images/user/tile3.jpg" alt="Mosaic Tile" class="carousel-img" />
                            <div class="carousel-content">
                                <h3 class="carousel-title">Mosaic Tile</h3>
                                <div class="carousel-price">₱1,750</div>
                                <button class="carousel-btn">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <img src="../images/user/tile4.jpg" alt="Natural Stone Tile" class="carousel-img" />
                            <div class="carousel-content">
                                <h3 class="carousel-title">Natural Stone Tile</h3>
                                <div class="carousel-price">₱850</div>
                                <button class="carousel-btn">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <img src="../images/user/tile5.jpg" alt="Classic Tile" class="carousel-img" />
                            <div class="carousel-content">
                                <h3 class="carousel-title">Classic Tile</h3>
                                <div class="carousel-price">₱2,100</div>
                                <button class="carousel-btn">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>

    <!-- Tile Categories Section -->
    <section class="tile-categories-section" id="categories">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Explore Our Collection</span>
                <h2 class="section-title">Tile Categories</h2>
                <p class="section-description">
                    From classic ceramics to luxurious natural stone, find the perfect
                    tiles to match your style and needs.
                </p>
            </div>
            
            <div class="tile-categories-grid">
                <div class="tile-category" data-category="ceramic">
                    <div class="tile-category-img">
                        <img src="../images/user/tile1.jpg" alt="Ceramic Tiles" />
                    </div>
                    <div class="tile-category-content">
                        <h3 class="tile-category-title">Ceramic Tiles</h3>
                        <p class="tile-category-desc">
                            Durable and versatile ceramic tiles for any space
                        </p>
                        <button class="tile-category-btn">
                            <i class="fas fa-shopping-cart"></i> Shop Now
                        </button>
                    </div>
                </div>

                <div class="tile-category" data-category="porcelain">
                    <div class="tile-category-img">
                        <img src="../images/user/tile2.jpg" alt="Porcelain Tiles" />
                    </div>
                    <div class="tile-category-content">
                        <h3 class="tile-category-title">Porcelain Tiles</h3>
                        <p class="tile-category-desc">
                            Premium quality porcelain for high-end finishes
                        </p>
                        <button class="tile-category-btn">
                            <i class="fas fa-shopping-cart"></i> Shop Now
                        </button>
                    </div>
                </div>

                <div class="tile-category" data-category="mosaic">
                    <div class="tile-category-img">
                        <img src="../images/user/tile3.jpg" alt="Mosaic Tiles" />
                    </div>
                    <div class="tile-category-content">
                        <h3 class="tile-category-title">Mosaic Tiles</h3>
                        <p class="tile-category-desc">
                            Artistic designs for unique decorative accents
                        </p>
                        <button class="tile-category-btn">
                            <i class="fas fa-shopping-cart"></i> Shop Now
                        </button>
                    </div>
                </div>

                <div class="tile-category" data-category="natural-stone">
                    <div class="tile-category-img">
                        <img src="../images/user/tile4.jpg" alt="Natural Stone Tiles" />
                    </div>
                    <div class="tile-category-content">
                        <h3 class="tile-category-title">Natural Stone</h3>
                        <p class="tile-category-desc">
                            Elegant natural stone for luxurious spaces
                        </p>
                        <button class="tile-category-btn">
                            <i class="fas fa-shopping-cart"></i> Shop Now
                        </button>
                    </div>
                </div>

                <div class="tile-category" data-category="premium">
                    <div class="tile-category-img">
                        <img src="../images/user/tile5.jpg" alt="Premium Tiles" />
                    </div>
                    <div class="tile-category-content">
                        <h3 class="tile-category-title">Premium Tiles</h3>
                        <p class="tile-category-desc">
                            High-end premium tiles for luxury spaces
                        </p>
                        <button class="tile-category-btn">
                            <i class="fas fa-shopping-cart"></i> Shop Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Shop Collection Section -->
    <section class="shop-collection-section" id="shop">
        <div class="container">
            <div class="shop-container">
                <div class="filter-sidebar">
                    <h3 class="filter-title">Filter Products</h3>
                    
                    <div class="filter-group">
                        <h4>Categories</h4>
                        <div class="filter-options">
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="ceramic" />
                                Ceramic Tiles
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="porcelain" />
                                Porcelain Tiles
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="mosaic" />
                                Mosaic Tiles
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="natural-stone" />
                                Natural Stone
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="category" value="outdoor" />
                                Outdoor Tiles
                            </label>
                        </div>
                    </div>

                    <div class="filter-group">
                        <h4>Price Range</h4>
                        <div class="filter-options">
                            <label class="filter-option">
                                <input type="radio" name="price-range" value="under-500" />
                                Under ₱500
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="price-range" value="500-1000" />
                                ₱500 - ₱1000
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="price-range" value="1000-2000" />
                                ₱1000 - ₱2000
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="price-range" value="over-2000" />
                                Over ₱2000
                            </label>
                        </div>
                    </div>

                    <div class="filter-group">
                        <h4>Tile Size</h4>
                        <div class="filter-options">
                            <label class="filter-option">
                                <input type="checkbox" name="size" value="small" />
                                Small (Under 30cm)
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size" value="medium" />
                                Medium (30-60cm)
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="size" value="large" />
                                Large (Over 60cm)
                            </label>
                        </div>
                    </div>

                    <button class="filter-apply">Apply Filters</button>
                    <button class="filter-apply" style="background: #6c757d; margin-top: 10px;">Reset Filters</button>
                </div>

                <div class="product-main">
                    <h2>Premium Tile Collection</h2>
                    <p>Browse our extensive collection of premium tiles for every room in your home or business.</p>
                    
                    <div class="product-grid">
                        <div class="product-card">
                            <span class="product-label bestseller">Bestseller</span>
                            <div class="product-img-container">
                                <img src="../images/user/tile1.jpg" alt="Premium Ceramic Tile" class="product-img" />
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Premium Ceramic Tile</h3>
                                <div class="product-price">₱1,250</div>
                                <button class="product-btn">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>

                        <div class="product-card">
                            <div class="product-img-container">
                                <img src="../images/user/tile2.jpg" alt="Porcelain Tile" class="product-img" />
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Porcelain Tile</h3>
                                <div class="product-price">₱950</div>
                                <button class="product-btn">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>

                        <div class="product-card">
                            <span class="product-label new">New</span>
                            <div class="product-img-container">
                                <img src="../images/user/tile3.jpg" alt="Mosaic Tile" class="product-img" />
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Mosaic Tile</h3>
                                <div class="product-price">₱1,750</div>
                                <button class="product-btn">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>

                        <div class="product-card">
                            <div class="product-img-container">
                                <img src="../images/user/tile4.jpg" alt="Natural Stone Tile" class="product-img" />
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Natural Stone Tile</h3>
                                <div class="product-price">₱850</div>
                                <button class="product-btn">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>

                        <div class="product-card">
                            <span class="product-label sale">Sale</span>
                            <div class="product-img-container">
                                <img src="../images/user/tile5.jpg" alt="Classic Tile" class="product-img" />
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Classic Tile</h3>
                                <div class="product-price">₱2,100</div>
                                <button class="product-btn">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>

                        <div class="product-card">
                            <div class="product-img-container">
                                <img src="../images/user/tile1.jpg" alt="Wood Effect Tile" class="product-img" />
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Wood Effect Tile</h3>
                                <div class="product-price">₱1,450</div>
                                <button class="product-btn">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-column">
                <h3>Rich Anne Lea Tiles Trading</h3>
                <p>Providing high-quality tiles for homes and businesses since 2010. We offer a wide range of premium tiles to suit every style and budget.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>
            
            <div class="footer-column">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Products</a></li>
                    <li><a href="#">Categories</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3>Categories</h3>
                <ul class="footer-links">
                    <li><a href="#">Ceramic Tiles</a></li>
                    <li><a href="#">Porcelain Tiles</a></li>
                    <li><a href="#">Mosaic Tiles</a></li>
                    <li><a href="#">Natural Stone</a></li>
                    <li><a href="#">Outdoor Tiles</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3>Contact Info</h3>
                <ul class="footer-links">
                    <li><i class="fas fa-map-marker-alt"></i> 123 Tile Street, Manila, Philippines</li>
                    <li><i class="fas fa-phone"></i> +63 912 345 6789</li>
                    <li><i class="fas fa-envelope"></i> info@richanneleatiles.com</li>
                    <li><i class="fas fa-clock"></i> Mon-Sat: 8:00 AM - 6:00 PM</li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2023 Rich Anne Lea Tiles Trading. All Rights Reserved.</p>
        </div>
    </footer>

    <a href="#" class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </a>

    <script>
        // Initialize Swiper for featured carousel
        var swiper = new Swiper(".featuredSwiper", {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                },
                992: {
                    slidesPerView: 3,
                },
                1200: {
                    slidesPerView: 4,
                },
            },
        });

        // Animate tile categories on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const tileCategories = document.querySelectorAll('.tile-category');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.2
            });
            
            tileCategories.forEach(category => {
                observer.observe(category);
            });
        });

        // Back to top button
        const backToTopButton = document.getElementById('backToTop');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('visible');
            } else {
                backToTopButton.classList.remove('visible');
            }
        });
        
        backToTopButton.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Add to cart functionality
        const addToCartButtons = document.querySelectorAll('.carousel-btn, .tile-category-btn, .product-btn');
        
        addToCartButtons.forEach(button => {
            button.addEventListener('click', () => {
                Swal.fire({
                    title: 'Added to Cart!',
                    text: 'Product has been added to your shopping cart.',
                    icon: 'success',
                    confirmButtonColor: '#7d310a',
                    confirmButtonText: 'Continue Shopping'
                });
            });
        });

        // Filter functionality
        const filterApply = document.querySelector('.filter-apply');
        
        filterApply.addEventListener('click', () => {
            Swal.fire({
                title: 'Filters Applied!',
                text: 'Your filters have been successfully applied.',
                icon: 'success',
                confirmButtonColor: '#7d310a',
                confirmButtonText: 'OK'
            });
        });
    </script>
</body>
</html>