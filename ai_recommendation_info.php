<?php
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Tile Recommendation - Rich Anne Tiles</title>
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
        
        .style-card {
            transition: all 0.3s ease;
            aspect-ratio: 1 / 1;
        }
        
        .style-card:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(237, 102, 49, 0.2);
        }
        
        .selection-badge {
            background: linear-gradient(135deg, #FFD700 0%, #FFEF8A 100%);
            border: 2px solid white;
        }
        
        .ai-preview {
            background: linear-gradient(135deg, rgba(254, 248, 246, 0.8) 0%, rgba(253, 240, 236, 0.6) 100%);
            border: 2px solid rgba(237, 102, 49, 0.3);
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
                    <i class="fa-solid fa-robot text-primary-600 text-lg"></i>
                    <span class="text-sm font-medium text-accent-600">Smart Personalization</span>
                </div>
                
                <h1 class="font-heading text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                    <span class="text-gradient">AI Tile Recommendation</span> 
                    <span class="block mt-2 text-accent-900">Personalized Just For You</span>
                </h1>
                
                <p class="text-lg md:text-xl text-accent-600 mb-10 leading-relaxed max-w-2xl">
                    Our intelligent AI learns your unique style preferences to recommend the perfect tiles. 
                    Simply select your top 3 favorite styles and let our algorithm do the magic.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-12">
                    <a href="user_login_form.php"  class="btn-primary px-8 py-4 text-lg font-semibold text-white rounded-xl inline-flex items-center justify-center group">
                        <i class="fa-solid fa-star mr-3"></i>
                        Get Personalized Recommendations
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
                        <div class="text-2xl md:text-3xl font-bold text-primary-600 mb-1">95%</div>
                        <div class="text-accent-600 text-sm font-medium">Accuracy Rate</div>
                    </div>
                    <div class="text-center stats-card p-5 rounded-xl shadow-soft card-hover floating-element" style="animation-delay: 1s;">
                        <div class="text-2xl md:text-3xl font-bold text-primary-600 mb-1">6</div>
                        <div class="text-accent-600 text-sm font-medium">Style Categories</div>
                    </div>
                    <div class="text-center stats-card p-5 rounded-xl shadow-soft card-hover floating-element col-span-2 md:col-span-1" style="animation-delay: 2s;">
                        <div class="text-2xl md:text-3xl font-bold text-primary-600 mb-1">Instant</div>
                        <div class="text-accent-600 text-sm font-medium">Results</div>
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2 flex justify-center lg:justify-end animate-slide-up">
                <div class="relative">
                    <div class="ai-preview rounded-3xl p-6">
                        <div class="w-full max-w-md aspect-[4/3] bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl overflow-hidden relative border-2 border-primary-300 p-4">
                            <!-- Mock AI Recommendation Interface -->
                            <div class="grid grid-cols-3 gap-3 h-full">
                                <!-- Style Cards Preview -->
                                <div class="style-card bg-white rounded-lg shadow-md border border-primary-200 flex items-center justify-center">
                                    <i class="fa-solid fa-border-all text-primary-600 text-xl"></i>
                                </div>
                                <div class="style-card bg-white rounded-lg shadow-md border border-primary-200 flex items-center justify-center">
                                    <i class="fa-solid fa-seedling text-primary-600 text-xl"></i>
                                </div>
                                <div class="style-card bg-white rounded-lg shadow-md border border-primary-200 flex items-center justify-center">
                                    <i class="fa-solid fa-palette text-primary-600 text-xl"></i>
                                </div>
                                <div class="style-card bg-white rounded-lg shadow-md border border-primary-200 flex items-center justify-center">
                                    <i class="fa-solid fa-cube text-primary-600 text-xl"></i>
                                </div>
                                <div class="style-card bg-white rounded-lg shadow-md border border-primary-200 flex items-center justify-center relative">
                                    <i class="fa-solid fa-mountain text-primary-600 text-xl"></i>
                                    <div class="selection-badge absolute -top-2 -right-2 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">1st</div>
                                </div>
                                <div class="style-card bg-white rounded-lg shadow-md border border-primary-200 flex items-center justify-center">
                                    <i class="fa-solid fa-shapes text-primary-600 text-xl"></i>
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="absolute bottom-4 left-4 right-4">
                                <div class="bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-primary-600 h-2.5 rounded-full transition-all duration-500" style="width: 66%"></div>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-sm text-accent-600">2 / 3 selected</span>
                                    <span class="text-sm font-bold text-primary-600">Personalizing...</span>
                                </div>
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
                    How AI Recommendation Works
                </h2>
                <p class="text-lg text-accent-600 max-w-2xl mx-auto">Our intelligent system learns your preferences in three simple steps to deliver perfect tile recommendations.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <!-- Step 1 -->
                <div class="bg-white rounded-2xl p-8 text-center card-hover border border-accent-100 shadow-soft animate-slide-up">
                    <div class="feature-icon w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <div class="text-primary-600 text-2xl font-bold">1</div>
                    </div>
                    <h3 class="text-xl font-semibold text-accent-900 mb-4">Select Your Top 3 Styles</h3>
                    <p class="text-accent-600 leading-relaxed">
                        Choose from 6 distinct tile style categories. Pick your top 3 favorites in order of preference.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white rounded-2xl p-8 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.1s">
                    <div class="feature-icon w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <div class="text-primary-600 text-2xl font-bold">2</div>
                    </div>
                    <h3 class="text-xl font-semibold text-accent-900 mb-4">AI Analysis</h3>
                    <p class="text-accent-600 leading-relaxed">
                        Our algorithm analyzes your preferences against thousands of tile designs to understand your unique style.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-2xl p-8 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.2s">
                    <div class="feature-icon w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <div class="text-primary-600 text-2xl font-bold">3</div>
                    </div>
                    <h3 class="text-xl font-semibold text-accent-900 mb-4">Get Personalized Results</h3>
                    <p class="text-accent-600 leading-relaxed">
                        Receive curated tile recommendations that perfectly match your selected style preferences.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Style Categories Section -->
    <section class="py-16 bg-accent-50 section-spacing">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="text-center mb-16 animate-fade-in">
                <h2 class="font-heading text-3xl md:text-4xl font-bold text-accent-900 mb-4">
                    Available Style Categories
                </h2>
                <p class="text-lg text-accent-600 max-w-2xl mx-auto">Choose from our carefully curated tile style categories to help our AI understand your preferences.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <!-- Minimalist -->
                <div class="bg-white rounded-2xl p-6 text-center card-hover border border-accent-100 shadow-soft animate-slide-up">
                    <div class="feature-icon w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-border-all text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-accent-900 mb-3">Minimalist</h3>
                    <p class="text-accent-600 text-sm leading-relaxed">
                        Clean lines, simple patterns, and understated elegance
                    </p>
                </div>

                <!-- Floral -->
                <div class="bg-white rounded-2xl p-6 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.1s">
                    <div class="feature-icon w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-seedling text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-accent-900 mb-3">Floral</h3>
                    <p class="text-accent-600 text-sm leading-relaxed">
                        Nature-inspired patterns with botanical and flower motifs
                    </p>
                </div>

                <!-- Black & White -->
                <div class="bg-white rounded-2xl p-6 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.2s">
                    <div class="feature-icon w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-palette text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-accent-900 mb-3">Black & White</h3>
                    <p class="text-accent-600 text-sm leading-relaxed">
                        Classic monochrome patterns with timeless appeal
                    </p>
                </div>

                <!-- Modern -->
                <div class="bg-white rounded-2xl p-6 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.3s">
                    <div class="feature-icon w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-cube text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-accent-900 mb-3">Modern</h3>
                    <p class="text-accent-600 text-sm leading-relaxed">
                        Contemporary designs with sleek and innovative patterns
                    </p>
                </div>

                <!-- Rustic -->
                <div class="bg-white rounded-2xl p-6 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.4s">
                    <div class="feature-icon w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-mountain text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-accent-900 mb-3">Rustic</h3>
                    <p class="text-accent-600 text-sm leading-relaxed">
                        Natural textures and earthy tones with vintage charm
                    </p>
                </div>

                <!-- Geometric -->
                <div class="bg-white rounded-2xl p-6 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.5s">
                    <div class="feature-icon w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-shapes text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-accent-900 mb-3">Geometric</h3>
                    <p class="text-accent-600 text-sm leading-relaxed">
                        Bold shapes, angles, and mathematical precision in design
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
                        Why Use AI Recommendations?
                    </h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-bolt text-primary-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-accent-900 mb-2">Save Time</h3>
                                <p class="text-accent-600">No more endless browsing - get personalized results instantly</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-check-circle text-primary-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-accent-900 mb-2">Perfect Matches</h3>
                                <p class="text-accent-600">95% accuracy in recommending tiles that match your style preferences</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-lightbulb text-primary-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-accent-900 mb-2">Discover New Styles</h3>
                                <p class="text-accent-600">Find tiles you might have missed with traditional browsing</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-chart-line text-primary-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-accent-900 mb-2">Smart Learning</h3>
                                <p class="text-accent-600">The more you use it, the better it understands your preferences</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-center animate-slide-up">
                    <div class="bg-white rounded-3xl p-8 max-w-md w-full border border-accent-100 shadow-medium">
                        <div class="text-center mb-6">
                            <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-robot text-3xl text-primary-600"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-accent-900 mb-2">Ready to Discover?</h3>
                            <p class="text-accent-600">Get personalized tile recommendations in minutes</p>
                        </div>
                        
                        <div class="space-y-4">
                            
                            <a href="user_login_form.php" class="btn-secondary w-full py-4 rounded-xl font-semibold text-lg flex items-center justify-center gap-3">
                                <i class="fa-solid fa-grid"></i>
                                Login Now
                            </a>
                        </div>
                        
                        <div class="mt-6 p-4 bg-primary-50 rounded-xl border border-primary-200">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-clock text-primary-600"></i>
                                <p class="text-primary-700 text-sm">
                                    <strong>Takes only 30 seconds</strong> to select your top 3 styles
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