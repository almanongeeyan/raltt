<?php
session_start();
// Prevent back navigation after login
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
// If already logged in, redirect to landing page
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    header('Location: logged_user/landing_page.php');
    exit();
}
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rich Anne Lea Tiles Trading - Tile Visualizer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>
    <style>
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .home-section::after {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
            background: radial-gradient(circle at 50% 40%, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.5) 40%, rgba(0,0,0,0.3) 70%, rgba(0,0,0,0.15) 90%, rgba(0,0,0,0.05) 100%);
        }
        
        [data-scroll] {
            opacity: 0;
            transition: opacity 0.6s ease, transform 0.6s ease;
            transform: translateY(20px);
        }

        [data-scroll="in"] {
            opacity: 1;
            transform: translateY(0);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease;
        }
        /* Button hover effects */
        .btn, .tab-btn, .animated-btn {
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        .btn::before, .tab-btn::before, .animated-btn::before {
            content: "";
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%) scale(0);
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(244,124,46,0.15) 0%, rgba(148,72,27,0.10) 100%);
            transition: transform 0.4s cubic-bezier(0.4,0,0.2,1), opacity 0.4s cubic-bezier(0.4,0,0.2,1);
            opacity: 0.7;
            z-index: 0;
        }
        .btn:hover::before, .tab-btn:hover::before, .animated-btn:hover::before {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
        .btn:hover, .tab-btn:hover, .animated-btn:hover {
            box-shadow: 0 8px 32px 0 rgba(244,124,46,0.15), 0 1.5px 8px 0 rgba(148,72,27,0.10);
            filter: brightness(1.05) saturate(1.1);
            transition: box-shadow 0.3s, filter 0.3s;
        }
        .btn:active, .tab-btn:active, .animated-btn:active {
            filter: brightness(0.97) saturate(0.95);
        }
        .tab-btn.active {
            box-shadow: 0 8px 32px 0 rgba(244,124,46,0.18), 0 1.5px 8px 0 rgba(148,72,27,0.13);
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <!-- Hero Section -->
    <section class="home-section relative w-full h-screen max-h-[800px] overflow-hidden flex items-center justify-center" data-scroll>
        <img src="images/homepage.jpg" alt="Beautiful tile designs" class="absolute w-full h-full object-cover z-10">
        <div class="container mx-auto px-5">
            <div class="hero-content relative z-20 text-white text-center px-5 w-full">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 leading-tight drop-shadow-lg">
                    Bring Your Visions to Life: <span class="block">Design Stunning Spaces</span> with Our 3D Tile Visualizer
                </h1>
                <p class="text-xl md:text-2xl mb-8 drop-shadow">Rich Anne Lea Tiles Trading</p>
                <a href="user_login_form.php" class="btn bg-[#94481b] text-white border-none py-3 px-8 text-lg font-semibold rounded-full cursor-pointer transition-all duration-300 shadow-lg hover:bg-[#b35923] hover:-translate-y-1 hover:shadow-xl">
                    Start Visualizing Now
                </a>
            </div>
        </div>
    </section>

    <div class="my-16"></div>

    <!-- Video Text Section -->
    <section class="video-text-section py-16 w-full" data-scroll>
        <div class="container mx-auto px-5">
            <div class="video-text-container flex flex-wrap items-center justify-between gap-10">
                <div class="video-text flex-1 min-w-[300px] text-center">
                    <h1 class="text-3xl md:text-4xl lg:text-5xl leading-tight mb-4">
                        <span class="text-[#F47C2E] font-bold">Craft Your Visions</span> 
                        <br><span class="font-bold">with our</span>
                        <br><span class="font-bold">Tile Visualizer Tool</span>
                    </h1>
                    <p class="text-base text-gray-600 max-w-[500px] mx-auto">
                        Enhancing your imagination with the use of tile visualizer in creating limitless designs all you want.
                    </p>
                </div>
                <div class="video-container flex-1 min-w-[300px] max-w-[600px] mx-auto">
                    <video src="images/video.mp4" autoplay loop muted playsinline class="w-full rounded-xl shadow-xl"></video>
                </div>
            </div>
        </div>
    </section>

    <div class="my-4"></div>

    <!-- Meet RALTT Section -->
    <section class="meet-raltt-section py-16 w-full text-center" data-scroll>
        <div class="container mx-auto px-5">
            <h3 class="text-[#F47C2E] font-semibold tracking-wide mb-4"><b>MEET RALTT</b></h3>
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 leading-tight">
                Tile Visualizer and E-Commerce in One Website
            </h1>
            <p class="text-gray-600 text-lg max-w-[700px] mx-auto">
                Browse while using our tile visualizer tool and check out items you want and deliver at the same day.
            </p>
        </div>
    </section>

    <!-- Tabbed Section -->
    <section class="tabbed-section py-16 w-full" data-scroll>
        <div class="container mx-auto px-5">
            <div class="tab-buttons flex justify-center gap-5 mb-8 flex-wrap">
                <button class="tab-btn text-lg font-semibold py-3 px-6 rounded-full bg-white text-[#94481b] border-2 border-[#94481b] cursor-pointer transition-all duration-300 active:bg-[#94481b] active:text-white active:-translate-y-1 active:shadow-lg" data-tab="visualizer-tab" id="tab-btn-visualizer">
                    3D Visualizer
                </button>
                <button class="tab-btn text-lg font-semibold py-3 px-6 rounded-full bg-white text-[#94481b] border-2 border-[#94481b] cursor-pointer transition-all duration-300" data-tab="ecommerce-tab" id="tab-btn-ecommerce">
                    Tile E-Commerce
                </button>
            </div>
            
            <div class="tab-content active flex items-center gap-10 mt-8 flex-wrap w-full fade-in" id="visualizer-tab">
                <img src="images/2dtilehomepage.png" alt="3D Visualizer" class="flex-1 min-w-[300px] max-w-full rounded-2xl">
                <div class="tab-text flex-1 min-w-[300px] text-left">
                    <h2 class="text-3xl md:text-4xl font-bold mb-2 leading-tight">3D Tile Visualizer</h2>
                    <div class="subtitle text-gray-500 text-sm mb-4">Expand imagination with 3D</div>
                    <div class="description text-gray-700 text-lg mb-6">
                        Enhance your shopping experience with our 3D Tile Visualizer! Simply upload an image to see how
                        tiles, marble, or wood flooring fit your space. Visualize with confidence and make informed
                        decisions effortlessly!
                    </div>
                    <a href="user_login_form.php" class="animated-btn inline-block py-3 px-8 bg-[#94481b] text-white border-none rounded-full text-lg font-semibold no-underline transition-all duration-300 shadow-lg">
                        Launch visualizer
                    </a>
                </div>
            </div>
            
            <div class="tab-content hidden items-center gap-10 mt-8 flex-wrap w-full" id="ecommerce-tab">
                <div class="flex gap-5 flex-wrap justify-center flex-1 min-w-[300px]">
                    <img src="images/tilehp1.PNG" alt="Tile Shop 1" class="flex-1 min-w-[200px] max-w-full rounded-2xl">
                    <img src="images/tilehp2.PNG" alt="Tile Shop 2" class="flex-1 min-w-[200px] max-w-full rounded-2xl">
                </div>
                <div class="tab-text flex-1 min-w-[300px] text-left">
                    <h2 class="text-3xl md:text-4xl font-bold mb-2 leading-tight">Choose over 1000+ tile designs</h2>
                    <div class="subtitle text-gray-500 text-sm mb-4">Add to cart and checkout your tile choice</div>
                    <div class="description text-gray-700 text-lg mb-6">
                        Choose from over 1000+ tile designs at RALTT! Discover a variety of styles in tiles, marble, and
                        wood flooring to perfectly match your space.
                    </div>
                    <a href="userloginform.php" class="animated-btn inline-block py-3 px-8 bg-[#94481b] text-white border-none rounded-full text-lg font-semibold no-underline transition-all duration-300 shadow-lg">
                        Buy now
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Trusted Section -->
    <section class="trusted-section w-full py-20 text-center bg-[#faf9f7]" data-scroll>
        <div class="container mx-auto px-5">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 leading-tight">
                Trusted by over <span class="text-[#F47C2E]">50+ distributors</span>
            </h1>
            <div class="subtitle text-gray-500 text-xl mb-8">Low prices vs. other tile trade retailers</div>
            <div class="distributor-grid grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-5 w-full max-w-[1200px] mx-auto p-8 bg-white rounded-2xl shadow-lg">
                <div class="distributor-item bg-gray-100 rounded-2xl p-5 flex items-center justify-center transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-white aspect-square">
                    <img src="images/distributors/logo1.jpg" alt="Distributor 1" class="max-w-full max-h-full object-contain grayscale-[20%] hover:grayscale-0 transition-all duration-300">
                </div>
                <div class="distributor-item bg-gray-100 rounded-2xl p-5 flex items-center justify-center transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-white aspect-square">
                    <img src="images/distributors/logo2.jpg" alt="Distributor 2" class="max-w-full max-h-full object-contain grayscale-[20%] hover:grayscale-0 transition-all duration-300">
                </div>
                <div class="distributor-item bg-gray-100 rounded-2xl p-5 flex items-center justify-center transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-white aspect-square">
                    <img src="images/distributors/logo3.jpg" alt="Distributor 3" class="max-w-full max-h-full object-contain grayscale-[20%] hover:grayscale-0 transition-all duration-300">
                </div>
                <div class="distributor-item bg-gray-100 rounded-2xl p-5 flex items-center justify-center transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-white aspect-square">
                    <img src="images/distributors/logo4.jpg" alt="Distributor 4" class="max-w-full max-h-full object-contain grayscale-[20%] hover:grayscale-0 transition-all duration-300">
                </div>
                <div class="distributor-item bg-gray-100 rounded-2xl p-5 flex items-center justify-center transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-white aspect-square">
                    <img src="images/distributors/logo5.jpg" alt="Distributor 5" class="max-w-full max-h-full object-contain grayscale-[20%] hover:grayscale-0 transition-all duration-300">
                </div>
                <div class="distributor-item bg-gray-100 rounded-2xl p-5 flex items-center justify-center transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-white aspect-square">
                    <img src="images/distributors/logo6.jpg" alt="Distributor 6" class="max-w-full max-h-full object-contain grayscale-[20%] hover:grayscale-0 transition-all duration-300">
                </div>
                <div class="distributor-item bg-gray-100 rounded-2xl p-5 flex items-center justify-center transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-white aspect-square">
                    <img src="images/distributors/logo7.jpg" alt="Distributor 7" class="max-w-full max-h-full object-contain grayscale-[20%] hover:grayscale-0 transition-all duration-300">
                </div>
                <div class="distributor-item bg-gray-100 rounded-2xl p-5 flex items-center justify-center transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-white aspect-square">
                    <img src="images/distributors/logo8.jpg" alt="Distributor 8" class="max-w-full max-h-full object-contain grayscale-[20%] hover:grayscale-0 transition-all duration-300">
                </div>
                <div class="distributor-item bg-gray-100 rounded-2xl p-5 flex items-center justify-center transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-white aspect-square">
                    <img src="images/distributors/logo9.jpg" alt="Distributor 9" class="max-w-full max-h-full object-contain grayscale-[20%] hover:grayscale-0 transition-all duration-300">
                </div>
                <div class="distributor-item bg-gray-100 rounded-2xl p-5 flex items-center justify-center transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-white aspect-square">
                    <img src="images/distributors/logo10.jpg" alt="Distributor 10" class="max-w-full max-h-full object-contain grayscale-[20%] hover:grayscale-0 transition-all duration-300">
                </div>
                <div class="distributor-item bg-gray-100 rounded-2xl p-5 flex items-center justify-center transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-white aspect-square">
                    <img src="images/distributors/logo11.jpg" alt="Distributor 11" class="max-w-full max-h-full object-contain grayscale-[20%] hover:grayscale-0 transition-all duration-300">
                </div>
            </div>
        </div>
    </section>

    <!-- App Download Section -->
    <section class="app-section w-full py-20 text-center" data-scroll>
        <div class="container mx-auto px-5">
            <div class="app-container max-w-[1100px] mx-auto p-16 bg-[#faf9f7] rounded-2xl">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 leading-tight">Checkout wherever you are!</h1>
                <div class="subtitle text-gray-400 text-lg mb-8">Download our app now</div>
                <div class="app-content flex items-center justify-center gap-10 flex-wrap">
                    <div class="app-image flex-1 min-w-[300px] max-w-[400px]">
                        <img src="images/phonehome.jpg" alt="Phone Mockup" class="w-full rounded-2xl shadow-xl">
                    </div>
                    <div class="app-details flex-1 min-w-[300px] text-center">
                        <div class="app-highlight text-[#94481b] text-xl font-semibold mb-8 leading-relaxed">
                            Let's make your ideas come<br>
                            into real-life visuals within<br>
                            your hands.
                        </div>
                        <a href="#" class="btn bg-[#94481b] text-white text-lg font-semibold border-none rounded-full py-3 px-8 my-2 shadow-lg w-60 max-w-full transition-all duration-300 hover:bg-[#b35923] hover:-translate-y-1 hover:shadow-xl flex items-center justify-center gap-2 no-underline">
                            <i class="fas fa-download text-lg"></i> Download the App
                        </a>
                        <div class="btn-divider text-gray-500 text-base my-3 relative flex items-center justify-center">
                            <span class="before:content-[''] before:flex-1 before:border-b before:border-gray-300 before:mx-2 after:content-[''] after:flex-1 after:border-b after:border-gray-300 after:mx-2">or</span>
                        </div>
                        <a href="#" class="btn bg-[#4285F4] text-white text-lg font-semibold border-none rounded-full py-3 px-8 my-2 shadow-lg w-60 max-w-full transition-all duration-300 hover:bg-[#3367D6] hover:-translate-y-1 hover:shadow-xl flex items-center justify-center gap-2 no-underline">
                            <i class="fab fa-google-play text-lg"></i> Get on Google Play
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Tab functionality

        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');

            // Helper to set active button based on visible tab
            function setActiveTabButton() {
                tabButtons.forEach(btn => {
                    btn.classList.remove('active', 'bg-[#94481b]', 'text-white', '-translate-y-1', 'shadow-lg');
                    btn.classList.add('bg-white', 'text-[#94481b]');
                });
                tabContents.forEach(content => {
                    if (content.classList.contains('active') && !content.classList.contains('hidden')) {
                        const tabId = content.id;
                        const btn = document.querySelector('.tab-btn[data-tab="' + tabId + '"]');
                        if (btn) {
                            btn.classList.add('active', 'bg-[#94481b]', 'text-white', '-translate-y-1', 'shadow-lg');
                            btn.classList.remove('bg-white', 'text-[#94481b]');
                        }
                    }
                });
            }

            // Initial state: ensure correct button is active
            setActiveTabButton();

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons and content
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'bg-[#94481b]', 'text-white', '-translate-y-1', 'shadow-lg');
                        btn.classList.add('bg-white', 'text-[#94481b]');
                    });
                    tabContents.forEach(content => {
                        content.classList.remove('active', 'flex');
                        content.classList.add('hidden');
                    });

                    // Add active class to clicked button
                    this.classList.add('active', 'bg-[#94481b]', 'text-white', '-translate-y-1', 'shadow-lg');
                    this.classList.remove('bg-white', 'text-[#94481b]');

                    // Show corresponding content
                    const tabId = this.getAttribute('data-tab');
                    const tabContent = document.getElementById(tabId);
                    tabContent.classList.remove('hidden');
                    tabContent.classList.add('active', 'flex', 'fade-in');

                    // Ensure only the correct button is active
                    setActiveTabButton();
                });
            });

            // Scroll animation functionality
            const scrollElements = document.querySelectorAll('[data-scroll]');
            
            const elementInView = (el, dividend = 1) => {
                const elementTop = el.getBoundingClientRect().top;
                return (
                    elementTop <= (window.innerHeight || document.documentElement.clientHeight) / dividend
                );
            };
            
            const elementOutofView = (el) => {
                const elementTop = el.getBoundingClientRect().top;
                return (
                    elementTop > (window.innerHeight || document.documentElement.clientHeight)
                );
            };
            
            const displayScrollElement = (element) => {
                element.setAttribute('data-scroll', 'in');
            };
            
            const hideScrollElement = (element) => {
                element.setAttribute('data-scroll', 'out');
            };
            
            const handleScrollAnimation = () => {
                scrollElements.forEach((el) => {
                    if (elementInView(el, 1.25)) {
                        displayScrollElement(el);
                    } else if (elementOutofView(el)) {
                        hideScrollElement(el);
                    }
                });
            };
            
            // Initialize scroll animation
            window.addEventListener('load', () => {
                handleScrollAnimation();
                scrollElements.forEach(el => el.offsetHeight);
            });
            
            let ticking = false;
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        handleScrollAnimation();
                        ticking = false;
                    });
                    ticking = true;
                }
            });

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php
include 'includes/footer.php';
?>