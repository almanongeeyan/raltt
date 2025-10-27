<?php
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AR Tile Scanner - Rich Anne Tiles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700;800&display=swap');
        
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
        
        .btn-secondary {
            background: white;
            color: #ed6631;
            border: 2px solid #ed6631;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #ed6631;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(237, 102, 49, 0.2);
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
        
        .feature-icon {
            background: linear-gradient(135deg, rgba(237, 102, 49, 0.1) 0%, rgba(213, 92, 44, 0.05) 100%);
            border: 1px solid rgba(237, 102, 49, 0.2);
        }
        
        .scanning-animation {
            animation: pulse-scan 2s infinite;
        }
        
        @keyframes pulse-scan {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(237, 102, 49, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(237, 102, 49, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(237, 102, 49, 0);
            }
        }
        
        .floating-element {
            animation: float 6s ease-in-out infinite;
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
        
        .ar-preview {
            background: linear-gradient(135deg, rgba(254, 248, 246, 0.8) 0%, rgba(253, 240, 236, 0.6) 100%);
            border: 2px solid rgba(237, 102, 49, 0.3);
        }
        
        .stats-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .decorative-circle {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(237, 102, 49, 0.1) 0%, rgba(237, 102, 49, 0.05) 70%, transparent 100%);
            z-index: 0;
        }
    </style>
</head>
<body class="font-sans text-accent-700 bg-white overflow-x-hidden">

    <!-- Hero Section -->
    <section class="min-h-screen pt-16 pb-12 px-6 lg:px-12 gradient-bg flex items-center relative overflow-hidden section-spacing">
        <!-- Background decorative elements -->
        <div class="decorative-circle w-64 h-64 top-0 right-0 -translate-y-32 translate-x-32"></div>
        <div class="decorative-circle w-96 h-96 bottom-0 left-0 -translate-x-64 translate-y-64"></div>
        <div class="decorative-circle w-48 h-48 top-1/4 left-1/4"></div>
        
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center relative z-10">
            <div class="order-2 lg:order-1 text-center lg:text-left animate-fade-in">
                <div class="inline-flex items-center gap-3 bg-white/80 backdrop-blur-sm px-6 py-3 rounded-full mb-6 border border-primary-200">
                    <i class="fa-solid fa-camera text-primary-600 text-lg"></i>
                    <span class="text-sm font-medium text-accent-600">AI-Powered Augmented Reality</span>
                </div>
                
                <h1 class="font-heading text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                    <span class="text-gradient">AR Tile Scanner</span> 
                    <span class="block mt-2 text-accent-900">Smart Recognition Technology</span>
                </h1>
                
                <p class="text-lg md:text-xl text-accent-600 mb-10 leading-relaxed max-w-2xl">
                    Point your camera at any tile and let our advanced AI instantly identify it from our database. 
                    Get real-time information, pricing, and availability in seconds.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-12">
                    <a href="user_login_form.php" class="btn-primary px-8 py-4 text-lg font-semibold text-white rounded-xl inline-flex items-center justify-center group">
                        <i class="fa-solid fa-camera mr-3"></i>
                        Launch AR Scanner
                        <i class="fa-solid fa-arrow-right ml-3 transition-transform group-hover:translate-x-1"></i>
                    </a>
                    <a href="#how-it-works" class="btn-secondary px-8 py-4 text-lg font-semibold rounded-xl inline-flex items-center justify-center">
                        <i class="fa-solid fa-circle-info mr-3"></i>
                        How It Works
                    </a>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 max-w-lg">
                    <div class="text-center stats-card p-5 rounded-xl shadow-soft card-hover floating-element">
                        <div class="text-2xl md:text-3xl font-bold text-primary-600 mb-1">1000+</div>
                        <div class="text-accent-600 text-sm font-medium">Tiles in Database</div>
                    </div>
                    <div class="text-center stats-card p-5 rounded-xl shadow-soft card-hover floating-element" style="animation-delay: 1s;">
                        <div class="text-2xl md:text-3xl font-bold text-primary-600 mb-1">70%</div>
                        <div class="text-accent-600 text-sm font-medium">Accuracy Rate</div>
                    </div>
                    <div class="text-center stats-card p-5 rounded-xl shadow-soft card-hover floating-element col-span-2 md:col-span-1" style="animation-delay: 2s;">
                        <div class="text-2xl md:text-3xl font-bold text-primary-600 mb-1">Instant</div>
                        <div class="text-accent-600 text-sm font-medium">Recognition</div>
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2 flex justify-center lg:justify-end animate-slide-up">
                <div class="relative">
                    <div class="ar-preview rounded-3xl p-6 scanning-animation">
                        <div class="w-full max-w-md aspect-[9/16] bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl overflow-hidden relative border-2 border-primary-300">
                            <!-- Mock AR Scanner Interface -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center p-8">
                                    <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fa-solid fa-camera-search text-3xl text-primary-600"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-accent-900 mb-2">Point at Tile</h3>
                                    <p class="text-accent-600 text-sm">AI is analyzing pattern...</p>
                                </div>
                            </div>
                            
                            <!-- Detection Frame -->
                            <div class="absolute inset-8 border-2 border-primary-400 rounded-lg pointer-events-none">
                                <div class="absolute -top-1 -left-1 w-4 h-4 border-t-2 border-l-2 border-primary-500"></div>
                                <div class="absolute -top-1 -right-1 w-4 h-4 border-t-2 border-r-2 border-primary-500"></div>
                                <div class="absolute -bottom-1 -left-1 w-4 h-4 border-b-2 border-l-2 border-primary-500"></div>
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 border-b-2 border-r-2 border-primary-500"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Elements -->
                    <div class="absolute -top-4 -right-4 w-8 h-8 bg-primary-200 rounded-full"></div>
                    <div class="absolute -bottom-4 -left-4 w-6 h-6 bg-primary-200 rounded-full"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-16 bg-white section-spacing">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="text-center mb-16 animate-fade-in">
                <h2 class="font-heading text-3xl md:text-4xl font-bold text-accent-900 mb-4">
                    How It Works
                </h2>
                <p class="text-lg text-accent-600 max-w-2xl mx-auto">Our advanced AI technology makes tile identification simple and accurate in just three easy steps.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <!-- Step 1 -->
                <div class="bg-white rounded-2xl p-8 text-center card-hover border border-accent-100 shadow-soft animate-slide-up">
                    <div class="feature-icon w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <div class="text-primary-600 text-2xl font-bold">1</div>
                    </div>
                    <h3 class="text-xl font-semibold text-accent-900 mb-4">Point Your Camera</h3>
                    <p class="text-accent-600 leading-relaxed">
                        Open the AR scanner and point your smartphone camera at any tile surface. The AI will automatically detect tile patterns.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white rounded-2xl p-8 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.1s">
                    <div class="feature-icon w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <div class="text-primary-600 text-2xl font-bold">2</div>
                    </div>
                    <h3 class="text-xl font-semibold text-accent-900 mb-4">AI Analysis</h3>
                    <p class="text-accent-600 leading-relaxed">
                        Our neural network analyzes the tile pattern, color, and texture in real-time to identify matches from our database.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-2xl p-8 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.2s">
                    <div class="feature-icon w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <div class="text-primary-600 text-2xl font-bold">3</div>
                    </div>
                    <h3 class="text-xl font-semibold text-accent-900 mb-4">Get Results</h3>
                    <p class="text-accent-600 leading-relaxed">
                        Instantly receive detailed information including product name, specifications, pricing, and availability.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-accent-50 section-spacing">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="text-center mb-16 animate-fade-in">
                <h2 class="font-heading text-3xl md:text-4xl font-bold text-accent-900 mb-4">
                    Advanced Features
                </h2>
                <p class="text-lg text-accent-600 max-w-2xl mx-auto">Powered by cutting-edge technology for the best tile recognition experience.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
                <!-- Feature 1 -->
                <div class="bg-white rounded-2xl p-6 text-center card-hover border border-accent-100 shadow-soft animate-slide-up">
                    <div class="feature-icon w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-brain text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-accent-900 mb-3">AI-Powered Recognition</h3>
                    <p class="text-accent-600 text-sm leading-relaxed">
                        Advanced machine learning algorithms trained on thousands of tile patterns
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-2xl p-6 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.1s">
                    <div class="feature-icon w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-bolt text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-accent-900 mb-3">Real-Time Processing</h3>
                    <p class="text-accent-600 text-sm leading-relaxed">
                        Instant analysis and identification without delays or waiting
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-2xl p-6 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.2s">
                    <div class="feature-icon w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-database text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-accent-900 mb-3">Extensive Database</h3>
                    <p class="text-accent-600 text-sm leading-relaxed">
                        Access to our complete catalog of 1000+ tile designs and patterns
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white rounded-2xl p-6 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.3s">
                    <div class="feature-icon w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-mobile-screen text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-accent-900 mb-3">Mobile Optimized</h3>
                    <p class="text-accent-600 text-sm leading-relaxed">
                        Works seamlessly on all smartphones with camera capabilities
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="py-16 bg-white section-spacing">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="animate-fade-in">
                    <h2 class="font-heading text-3xl md:text-4xl font-bold text-accent-900 mb-6">
                        Why Use Our AR Scanner?
                    </h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-clock text-primary-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-accent-900 mb-2">Save Time</h3>
                                <p class="text-accent-600">Instantly identify tiles without manual searching through catalogs</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-check-circle text-primary-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-accent-900 mb-2">Accurate Results</h3>
                                <p class="text-accent-600">70% accuracy rate in tile pattern recognition and matching</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-shopping-cart text-primary-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-accent-900 mb-2">Instant Pricing</h3>
                                <p class="text-accent-600">Get real-time pricing and availability information immediately</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-lightbulb text-primary-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-accent-900 mb-2">Smart Suggestions</h3>
                                <p class="text-accent-600">Receive recommendations for similar tiles and complementary designs</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-center animate-slide-up">
                    <div class="bg-white rounded-3xl p-8 max-w-md w-full border border-accent-100 shadow-medium">
                        <div class="text-center mb-6">
                            <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-star text-3xl text-primary-600"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-accent-900 mb-2">Ready to Scan?</h3>
                            <p class="text-accent-600">Start identifying tiles with our advanced AR scanner</p>
                        </div>
                        
                        <div class="space-y-4">
                            <a href="user_login_form.php" class="btn-primary w-full py-4 rounded-xl font-semibold text-lg flex items-center justify-center gap-3">
                                <i class="fa-solid fa-camera"></i>
                                Launch AR Scanner
                            </a>
                        </div>
                        
                        <div class="mt-6 p-4 bg-primary-50 rounded-xl border border-primary-200">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-info-circle text-primary-600"></i>
                                <p class="text-primary-700 text-sm">
                                    <strong>Tip:</strong> Ensure good lighting and a clear view of the tile for best results
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll animations
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

        // Observe animated elements
        document.querySelectorAll('.animate-fade-in, .animate-slide-up').forEach(el => {
            observer.observe(el);
        });

        // Initialize floating elements animation
        document.querySelectorAll('.floating-element').forEach(el => {
            el.style.animationPlayState = 'running';
        });
    </script>
</body>
</html>
<?php
include 'includes/footer.php';
?>