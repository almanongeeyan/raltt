<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rich Anne Lea Tiles - Premium Tile Collection</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': {
                            '50': '#fef8f6',
                            '100': '#fdf0ec',
                            '200': '#fbd9cc',
                            '300': '#f8c2ad',
                            '400': '#f2946f',
                            '500': '#ed6631',
                            '600': '#d55c2c',
                            '700': '#b24d25',
                            '800': '#8e3d1d',
                            '900': '#743218'
                        },
                        'accent': {
                            '50': '#f6f6f6',
                            '100': '#e7e7e7',
                            '200': '#d1d1d1',
                            '300': '#b0b0b0',
                            '400': '#888888',
                            '500': '#6d6d6d',
                            '600': '#5d5d5d',
                            '700': '#4f4f4f',
                            '800': '#454545',
                            '900': '#3d3d3d'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Montserrat', 'sans-serif'],
                    },
                    boxShadow: {
                        'soft': '0 2px 15px rgba(0, 0, 0, 0.05)',
                        'medium': '0 5px 25px rgba(0, 0, 0, 0.08)',
                        'large': '0 10px 40px rgba(0, 0, 0, 0.12)',
                        'xl': '0 15px 50px rgba(0, 0, 0, 0.15)'
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'fade-in': 'fadeIn 0.8s ease-out',
                        'slide-up': 'slideUp 0.8s ease-out',
                        'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
                    }
                }
            }
        }
    </script>
    <style>
        html {
            scroll-behavior: smooth;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes pulseSoft {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #fef8f6 0%, #fdf0ec 50%, #fbd9cc 100%);
        }
        
        .section-spacing {
            padding-top: 5rem;
            padding-bottom: 5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #ed6631 0%, #d55c2c 100%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(237, 102, 49, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(237, 102, 49, 0.4);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #ed6631 0%, #d55c2c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .pattern-tile {
            transition: all 0.3s ease;
        }
        
        .pattern-tile:hover {
            transform: scale(1.05);
        }
        
        .tile-selection-item img {
            transition: all 0.3s ease;
        }
        
        .tile-selection-item:hover img {
            transform: scale(1.05);
        }
        
        .decorative-circle {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(237, 102, 49, 0.1) 0%, rgba(237, 102, 49, 0.05) 70%, transparent 100%);
            z-index: 0;
        }
        
        .stats-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .floating-element {
            animation: float 8s ease-in-out infinite;
        }
        
        .tab-button {
            transition: all 0.3s ease;
        }
        
        .tab-button.active {
            background: linear-gradient(135deg, #ed6631 0%, #d55c2c 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(237, 102, 49, 0.3);
        }
        
        .tab-button:not(.active) {
            background: #f5f5f5;
            color: #6d6d6d;
        }
        
        .tab-button:not(.active):hover {
            background: #eaeaea;
        }
    </style>
</head>
<body class="font-sans text-accent-700 bg-white overflow-x-hidden">
    <?php include 'includes/header.php'; ?>
    
    <!-- Hero Section -->
    <section class="min-h-screen pt-24 pb-12 px-6 lg:px-12 gradient-bg flex items-center relative overflow-hidden section-spacing">
        <!-- Background decorative elements -->
        <div class="decorative-circle w-64 h-64 top-0 right-0 -translate-y-32 translate-x-32"></div>
        <div class="decorative-circle w-96 h-96 bottom-0 left-0 -translate-x-64 translate-y-64"></div>
        <div class="decorative-circle w-48 h-48 top-1/4 left-1/4"></div>
        
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center relative z-10">
            <div class="order-2 lg:order-1 text-center lg:text-left animate-fade-in">
                <h1 class="font-heading text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                    <span class="text-gradient">Premium Tile</span> 
                    <span class="block mt-2 text-accent-900">Collection</span>
                </h1>
                <p class="text-lg md:text-xl text-accent-600 mb-10 leading-relaxed max-w-2xl">
                    Discover an exceptional shopping experience with Rich Anne Lea Tiles Trading! 
                    Explore our vast selection of premium textures, colors, and styles that bring 
                    your design vision to life with elegance and durability.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-12">
                    <a href="#tile-types" class="btn-primary px-8 py-4 text-lg font-semibold text-white rounded-xl inline-flex items-center justify-center group">
                        Explore Collections
                        <i class="fa-solid fa-arrow-down ml-3 transition-transform group-hover:translate-y-1"></i>
                    </a>
                    <a href="user_login_form.php" class="bg-white text-primary-600 px-8 py-4 text-lg font-semibold rounded-xl inline-flex items-center justify-center border-2 border-primary-600 hover:bg-primary-600 hover:text-white transition-colors">
                        <i class="fa-solid fa-cube mr-3"></i>
                        Try Visualizer
                    </a>
                </div>
                
                <!-- Enhanced Stats Section -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 max-w-lg">
                    <div class="text-center stats-card p-5 rounded-xl shadow-soft card-hover floating-element">
                        <div class="text-2xl md:text-3xl font-bold text-primary-600 mb-1">1000+</div>
                        <div class="text-accent-600 text-sm font-medium">Tile Designs</div>
                    </div>
                    <div class="text-center stats-card p-5 rounded-xl shadow-soft card-hover floating-element" style="animation-delay: 1s;">
                        <div class="text-2xl md:text-3xl font-bold text-primary-600 mb-1">50+</div>
                        <div class="text-accent-600 text-sm font-medium">Patterns</div>
                    </div>
                    <div class="text-center stats-card p-5 rounded-xl shadow-soft card-hover floating-element col-span-2 md:col-span-1" style="animation-delay: 2s;">
                        <div class="text-2xl md:text-3xl font-bold text-primary-600 mb-1">Premium</div>
                        <div class="text-accent-600 text-sm font-medium">Quality</div>
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2 flex justify-center lg:justify-end animate-slide-up">
                <div class="relative">
                    <div class="bg-white/30 backdrop-blur-sm rounded-2xl shadow-large border border-white/50 p-6">
                        <div class="h-64 w-64 md:h-80 md:w-80 bg-gradient-to-br from-primary-200 to-primary-100 flex items-center justify-center rounded-2xl overflow-hidden relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary-100 to-transparent"></div>
                            <div class="relative w-full h-full flex items-center justify-center">
                                <i class="fa-solid fa-gem text-primary-300 text-6xl md:text-8xl opacity-30 absolute top-4 left-4"></i>
                                <i class="fa-solid fa-border-all text-primary-500 text-7xl md:text-9xl z-10"></i>
                                <i class="fa-solid fa-cube text-primary-300 text-5xl md:text-7xl opacity-30 absolute bottom-6 right-6"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Decorative elements -->
                    <div class="absolute -top-4 -right-4 w-16 h-16 bg-primary-200 rounded-full"></div>
                    <div class="absolute -bottom-4 -left-4 w-12 h-12 bg-primary-200 rounded-full"></div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Tile Types Section -->
    <section id="tile-types" class="py-16 bg-white section-spacing">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="text-center mb-16 animate-fade-in">
                <h2 class="font-heading text-3xl md:text-4xl font-bold text-accent-900 mb-4">
                    Explore Our Tile Finishes
                </h2>
                <p class="text-lg text-accent-600 max-w-2xl mx-auto">Choose between our premium glossy and matte finishes to match your design vision.</p>
            </div>
            
            <div class="flex justify-center mb-12">
                <div class="inline-flex rounded-xl border border-accent-200 p-1 bg-accent-50">
                    <button id="glossyBtn" class="tab-button px-8 py-4 rounded-lg font-semibold transition-all duration-300 active">Glossy Finish</button>
                    <button id="matteBtn" class="tab-button px-8 py-4 rounded-lg font-semibold transition-all duration-300">Matte Finish</button>
                </div>
            </div>
            
            <div id="glossyContent" class="animate-fade-in">
                <div class="flex flex-col lg:flex-row items-center gap-12 max-w-6xl mx-auto">
                    <div class="lg:w-1/2">
                        <div class="rounded-2xl overflow-hidden shadow-xl">
                            <img src="images/glossy.PNG" alt="Glossy Tile" class="w-full h-80 object-cover">
                        </div>
                    </div>
                    <div class="lg:w-1/2 text-center lg:text-left">
                        <h2 class="text-3xl font-semibold mb-4 text-accent-900">Glossy Tiles</h2>
                        <div class="h-1 w-16 bg-primary-600 mb-6"></div>
                        <p class="text-accent-600 text-lg leading-relaxed mb-6">
                            Our premium glossy tiles combine elegance with exceptional durability. Featuring a smooth, 
                            reflective finish that enhances natural light, these tiles are perfect for creating bright, 
                            spacious environments in modern homes and commercial spaces.
                        </p>
                        <div class="flex flex-wrap gap-2 mb-6">
                            <span class="bg-primary-100 text-primary-700 px-3 py-1 rounded-full text-sm font-medium">High Shine</span>
                            <span class="bg-primary-100 text-primary-700 px-3 py-1 rounded-full text-sm font-medium">Easy Clean</span>
                            <span class="bg-primary-100 text-primary-700 px-3 py-1 rounded-full text-sm font-medium">Light Enhancing</span>
                        </div>
                        <a href="user_login_form.php" class="inline-flex items-center text-primary-600 font-semibold hover:text-primary-700 transition-colors group">
                            Visualize Glossy Tiles
                            <i class="fa-solid fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div id="matteContent" class="animate-fade-in hidden">
                <div class="flex flex-col lg:flex-row items-center gap-12 max-w-6xl mx-auto">
                    <div class="lg:w-1/2">
                        <div class="rounded-2xl overflow-hidden shadow-xl">
                            <img src="images/matte.PNG" alt="Matte Tile" class="w-full h-80 object-cover">
                        </div>
                    </div>
                    <div class="lg:w-1/2 text-center lg:text-left">
                        <h2 class="text-3xl font-semibold mb-4 text-accent-900">Matte Tiles</h2>
                        <div class="h-1 w-16 bg-primary-600 mb-6"></div>
                        <p class="text-accent-600 text-lg leading-relaxed mb-6">
                            Discover the sophisticated appeal of our matte tile collection. With their soft, 
                            non-reflective finish, these tiles offer excellent slip resistance while maintaining 
                            a contemporary, elegant appearance perfect for any space.
                        </p>
                        <div class="flex flex-wrap gap-2 mb-6">
                            <span class="bg-primary-100 text-primary-700 px-3 py-1 rounded-full text-sm font-medium">Slip Resistant</span>
                            <span class="bg-primary-100 text-primary-700 px-3 py-1 rounded-full text-sm font-medium">Modern Finish</span>
                            <span class="bg-primary-100 text-primary-700 px-3 py-1 rounded-full text-sm font-medium">Low Maintenance</span>
                        </div>
                        <a href="user_login_form.php" class="inline-flex items-center text-primary-600 font-semibold hover:text-primary-700 transition-colors group">
                            Visualize Matte Tiles
                            <i class="fa-solid fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Tile Selection Section -->
    <section class="py-16 bg-accent-50 section-spacing">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="text-center mb-16 animate-fade-in">
                <h2 class="font-heading text-3xl md:text-4xl font-bold text-accent-900 mb-4">
                    Comprehensive Tile Categories
                </h2>
                <p class="text-lg text-accent-600 max-w-2xl mx-auto">Our visualizer supports a wide range of tile applications for every space.</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-8 max-w-6xl mx-auto">
                <div class="tile-selection-item text-center animate-slide-up card-hover">
                    <div class="mb-4 overflow-hidden rounded-2xl shadow-medium">
                        <img src="images/indoor.PNG" alt="Indoor Tiles" class="w-full h-64 object-cover">
                    </div>
                    <p class="text-lg font-semibold text-primary-600">Indoor</p>
                    <p class="text-accent-500 text-sm mt-1">Living spaces & interiors</p>
                </div>
                
                <div class="tile-selection-item text-center animate-slide-up card-hover" style="animation-delay: 0.1s">
                    <div class="mb-4 overflow-hidden rounded-2xl shadow-medium">
                        <img src="images/outdoor.PNG" alt="Outdoor Tiles" class="w-full h-64 object-cover">
                    </div>
                    <p class="text-lg font-semibold text-primary-600">Outdoor</p>
                    <p class="text-accent-500 text-sm mt-1">Patios & exterior areas</p>
                </div>
                
                <div class="tile-selection-item text-center animate-slup-up card-hover" style="animation-delay: 0.2s">
                    <div class="mb-4 overflow-hidden rounded-2xl shadow-medium">
                        <img src="images/industrial.PNG" alt="Industrial Tiles" class="w-full h-64 object-cover">
                    </div>
                    <p class="text-lg font-semibold text-primary-600">Industrial</p>
                    <p class="text-accent-500 text-sm mt-1">Commercial & heavy use</p>
                </div>
                
                <div class="tile-selection-item text-center animate-slide-up card-hover" style="animation-delay: 0.3s">
                    <div class="mb-4 overflow-hidden rounded-2xl shadow-medium">
                        <img src="images/pool.PNG" alt="Pool Tiles" class="w-full h-64 object-cover">
                    </div>
                    <p class="text-lg font-semibold text-primary-600">Pool</p>
                    <p class="text-accent-500 text-sm mt-1">Aquatic environments</p>
                </div>
                
                <div class="tile-selection-item text-center animate-slide-up card-hover" style="animation-delay: 0.4s">
                    <div class="mb-4 overflow-hidden rounded-2xl shadow-medium">
                        <img src="images/countertops.PNG" alt="Countertop Tiles" class="w-full h-64 object-cover">
                    </div>
                    <p class="text-lg font-semibold text-primary-600">Countertops</p>
                    <p class="text-accent-500 text-sm mt-1">Kitchen & bathroom surfaces</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Patterns and Designs Section -->
    <section class="py-16 bg-white section-spacing">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="text-center mb-16 animate-fade-in">
                <h2 class="font-heading text-3xl md:text-4xl font-bold text-accent-900 mb-4">
                    Patterns and Designs
                </h2>
                <p class="text-lg text-accent-600 max-w-2xl mx-auto">Explore our diverse collection of patterns to create unique and captivating spaces.</p>
            </div>
            
            <div class="space-y-16 max-w-6xl mx-auto">
                <!-- Floral Patterns -->
                <div class="pattern-category animate-slide-up">
                    <h3 class="text-2xl font-semibold mb-8 text-center text-accent-900 border-b border-accent-200 pb-4">Floral Patterns</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="pattern-tile aspect-square rounded-xl shadow-medium overflow-hidden" style="background-image: url('images/p&d/floral1.PNG'); background-size: cover; background-position: center;"></div>
                        <div class="pattern-tile aspect-square rounded-xl shadow-medium overflow-hidden" style="background-image: url('images/p&d/floral2.PNG'); background-size: cover; background-position: center;"></div>
                        <div class="pattern-tile aspect-square rounded-xl shadow-medium overflow-hidden" style="background-image: url('images/p&d/floral3.PNG'); background-size: cover; background-position: center;"></div>
                        <div class="pattern-tile aspect-square rounded-xl shadow-medium overflow-hidden" style="background-image: url('images/p&d/floral4.PNG'); background-size: cover; background-position: center;"></div>
                    </div>
                </div>
                
                <!-- Minimalist Patterns -->
                <div class="pattern-category animate-slide-up" style="animation-delay: 0.1s">
                    <h3 class="text-2xl font-semibold mb-8 text-center text-accent-900 border-b border-accent-200 pb-4">Minimalist Patterns</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="pattern-tile aspect-square rounded-xl shadow-medium overflow-hidden" style="background-image: url('images/p&d/minimalist1.PNG'); background-size: cover; background-position: center;"></div>
                        <div class="pattern-tile aspect-square rounded-xl shadow-medium overflow-hidden" style="background-image: url('images/p&d/minimalist2.PNG'); background-size: cover; background-position: center;"></div>
                        <div class="pattern-tile aspect-square rounded-xl shadow-medium overflow-hidden" style="background-image: url('images/p&d/minimalist3.PNG'); background-size: cover; background-position: center;"></div>
                        <div class="pattern-tile aspect-square rounded-xl shadow-medium overflow-hidden" style="background-image: url('images/p&d/minimalist4.PNG'); background-size: cover; background-position: center;"></div>
                    </div>
                </div>
                
                <!-- Black and White Patterns -->
                <div class="pattern-category animate-slide-up" style="animation-delay: 0.2s">
                    <h3 class="text-2xl font-semibold mb-8 text-center text-accent-900 border-b border-accent-200 pb-4">Black and White Patterns</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="pattern-tile aspect-square rounded-xl shadow-medium overflow-hidden" style="background-image: url('images/p&d/b&w1.PNG'); background-size: cover; background-position: center;"></div>
                        <div class="pattern-tile aspect-square rounded-xl shadow-medium overflow-hidden" style="background-image: url('images/p&d/b&w2.PNG'); background-size: cover; background-position: center;"></div>
                        <div class="pattern-tile aspect-square rounded-xl shadow-medium overflow-hidden" style="background-image: url('images/p&d/b&w3.PNG'); background-size: cover; background-position: center;"></div>
                        <div class="pattern-tile aspect-square rounded-xl shadow-medium overflow-hidden" style="background-image: url('images/p&d/b&w4.PNG'); background-size: cover; background-position: center;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <script>
        // Tab functionality
        const glossyBtn = document.getElementById('glossyBtn');
        const matteBtn = document.getElementById('matteBtn');
        const glossyContent = document.getElementById('glossyContent');
        const matteContent = document.getElementById('matteContent');

        glossyBtn.addEventListener('click', function() {
            glossyBtn.classList.add('active');
            matteBtn.classList.remove('active');
            glossyContent.classList.remove('hidden');
            matteContent.classList.add('hidden');
        });

        matteBtn.addEventListener('click', function() {
            matteBtn.classList.add('active');
            glossyBtn.classList.remove('active');
            matteContent.classList.remove('hidden');
            glossyContent.classList.add('hidden');
        });

        // Scroll animation functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Intersection Observer for scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                    }
                });
            }, observerOptions);

            // Observe all animated elements
            document.querySelectorAll('.animate-fade-in, .animate-slide-up').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>