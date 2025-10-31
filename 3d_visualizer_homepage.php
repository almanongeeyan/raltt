<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Tile Visualizer - Rich Anne Tiles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
                        'rotate': 'rotate 20s linear infinite',
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
        
        .rotating-element {
            animation: rotate 20s linear infinite;
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
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
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
        
        .visualizer-preview {
            background: linear-gradient(135deg, rgba(254, 248, 246, 0.8) 0%, rgba(253, 240, 236, 0.6) 100%);
            border: 2px solid rgba(237, 102, 49, 0.3);
        }
        
        .tile-mockup {
            background: linear-gradient(45deg, #f0f0f0 25%, transparent 25%), 
                        linear-gradient(-45deg, #f0f0f0 25%, transparent 25%), 
                        linear-gradient(45deg, transparent 75%, #f0f0f0 75%), 
                        linear-gradient(-45deg, transparent 75%, #f0f0f0 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        }
    </style>
</head>
<body class="font-sans text-accent-700 bg-white overflow-x-hidden">
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="min-h-screen pt-16 pb-12 px-6 lg:px-12 gradient-bg flex items-center relative overflow-hidden section-spacing">
        <!-- Background decorative elements -->
        <div class="decorative-circle w-64 h-64 top-0 right-0 -translate-y-32 translate-x-32"></div>
        <div class="decorative-circle w-96 h-96 bottom-0 left-0 -translate-x-64 translate-y-64"></div>
        <div class="decorative-circle w-48 h-48 top-1/4 left-1/4"></div>
        
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center relative z-10">
            <div class="order-2 lg:order-1 text-center lg:text-left animate-fade-in">
                <div class="inline-flex items-center gap-3 bg-white/80 backdrop-blur-sm px-6 py-3 rounded-full mb-6 border border-primary-200">
                    <i class="fa-solid fa-rotate text-primary-600 text-lg"></i>
                    <span class="text-sm font-medium text-accent-600">3D Interactive Experience</span>
                </div>
                
                <h1 class="font-heading text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                    <span class="text-gradient">3D Tile Visualizer</span> 
                    <span class="block mt-2 text-accent-900">Examine Every Detail in 3D</span>
                </h1>
                
                <p class="text-lg md:text-xl text-accent-600 mb-10 leading-relaxed max-w-2xl">
                    Rotate, zoom, and explore our premium tile collection from every angle. 
                    Our 3D visualizer lets you examine textures, finishes, and patterns 
                    in stunning detail before making your selection.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-12">
                    <a href="user_login_form.php" class="btn-primary px-8 py-4 text-lg font-semibold text-white rounded-xl inline-flex items-center justify-center group">
                        <i class="fa-solid fa-rotate mr-3"></i>
                        Try Our Visualizer
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
                        <div class="text-2xl md:text-3xl font-bold text-primary-600 mb-1">500+</div>
                        <div class="text-accent-600 text-sm font-medium">Tile Designs</div>
                    </div>
                    <div class="text-center stats-card p-5 rounded-xl shadow-soft card-hover floating-element" style="animation-delay: 1s;">
                        <div class="text-2xl md:text-3xl font-bold text-primary-600 mb-1">360°</div>
                        <div class="text-accent-600 text-sm font-medium">Full Rotation</div>
                    </div>
                    <div class="text-center stats-card p-5 rounded-xl shadow-soft card-hover floating-element col-span-2 md:col-span-1" style="animation-delay: 2s;">
                        <div class="text-2xl md:text-3xl font-bold text-primary-600 mb-1">HD</div>
                        <div class="text-accent-600 text-sm font-medium">Textures</div>
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2 flex justify-center lg:justify-end animate-slide-up">
                <div class="relative">
                    <div class="visualizer-preview rounded-3xl p-6">
                        <div class="w-full max-w-md aspect-square bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl overflow-hidden relative border-2 border-primary-300">
                            <!-- 3D Visualizer Mockup -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-48 h-48 bg-white rounded-xl shadow-lg overflow-hidden rotating-element">
                                    <div class="tile-mockup w-full h-full flex items-center justify-center">
                                        <div class="text-center">
                                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                                <i class="fa-solid fa-rotate text-primary-600"></i>
                                            </div>
                                            <p class="text-xs text-accent-600 font-medium">Drag to Rotate</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Control Elements -->
                            <div class="absolute bottom-4 left-4 flex gap-2">
                                <div class="w-8 h-8 bg-white rounded-lg shadow-md flex items-center justify-center">
                                    <i class="fa-solid fa-rotate text-primary-600 text-sm"></i>
                                </div>
                                <div class="w-8 h-8 bg-white rounded-lg shadow-md flex items-center justify-center">
                                    <i class="fa-solid fa-magnifying-glass-plus text-primary-600 text-sm"></i>
                                </div>
                                <div class="w-8 h-8 bg-white rounded-lg shadow-md flex items-center justify-center">
                                    <i class="fa-solid fa-arrows-left-right text-primary-600 text-sm"></i>
                                </div>
                            </div>
                            
                            <!-- Rotation Indicator -->
                            <div class="absolute top-4 right-4 bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-medium text-accent-700 flex items-center gap-1">
                                <i class="fa-solid fa-rotate text-primary-600"></i>
                                <span>3D View</span>
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
                    How Our 3D Visualizer Works
                </h2>
                <p class="text-lg text-accent-600 max-w-2xl mx-auto">Examine tiles from every angle in three simple steps.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <!-- Step 1 -->
                <div class="bg-white rounded-2xl p-8 text-center card-hover border border-accent-100 shadow-soft animate-slide-up">
                    <div class="feature-icon w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <div class="text-primary-600 text-2xl font-bold">1</div>
                    </div>
                    <h3 class="text-xl font-semibold text-accent-900 mb-4">Select a Tile</h3>
                    <p class="text-accent-600 leading-relaxed">
                        Browse our collection and choose any tile to examine in our 3D visualizer.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white rounded-2xl p-8 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.1s">
                    <div class="feature-icon w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <div class="text-primary-600 text-2xl font-bold">2</div>
                    </div>
                    <h3 class="text-xl font-semibold text-accent-900 mb-4">Rotate & Explore</h3>
                    <p class="text-accent-600 leading-relaxed">
                        Drag to rotate the tile 3D and examine it from every possible angle.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-2xl p-8 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.2s">
                    <div class="feature-icon w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <div class="text-primary-600 text-2xl font-bold">3</div>
                    </div>
                    <h3 class="text-xl font-semibold text-accent-900 mb-4">Zoom for Detail</h3>
                    <p class="text-accent-600 leading-relaxed">
                        Zoom in to examine textures, finishes, and intricate details up close.
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
                    Visualizer Features
                </h2>
                <p class="text-lg text-accent-600 max-w-2xl mx-auto">Examine tiles like never before with our advanced 3D viewer.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
                <!-- Feature 1 -->
                <div class="bg-white rounded-2xl p-6 text-center card-hover border border-accent-100 shadow-soft animate-slide-up">
                    <div class="feature-icon w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-rotate text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-accent-900 mb-3">3D Rotation</h3>
                    <p class="text-accent-600 text-sm leading-relaxed">
                        Examine tiles from every angle with smooth, intuitive rotation controls
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-2xl p-6 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.1s">
                    <div class="feature-icon w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-magnifying-glass text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-accent-900 mb-3">Zoom In Detail</h3>
                    <p class="text-accent-600 text-sm leading-relaxed">
                        Get up close with high-resolution textures and intricate pattern details
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-2xl p-6 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.2s">
                    <div class="feature-icon w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-sun text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-accent-900 mb-3">Lighting Effects</h3>
                    <p class="text-accent-600 text-sm leading-relaxed">
                        See how light interacts with different finishes and textures
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white rounded-2xl p-6 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.3s">
                    <div class="feature-icon w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-gauge-high text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-accent-900 mb-3">Real-time Rendering</h3>
                    <p class="text-accent-600 text-sm leading-relaxed">
                        Instant response with no loading delays as you rotate and zoom
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white rounded-2xl p-6 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.4s">
                    <div class="feature-icon w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-mobile-screen-button text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-accent-900 mb-3">Mobile Friendly</h3>
                    <p class="text-accent-600 text-sm leading-relaxed">
                        Touch-friendly controls for seamless use on smartphones and tablets
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white rounded-2xl p-6 text-center card-hover border border-accent-100 shadow-soft animate-slide-up" style="animation-delay: 0.5s">
                    <div class="feature-icon w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-layer-group text-2xl text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-accent-900 mb-3">Multiple Finishes</h3>
                    <p class="text-accent-600 text-sm leading-relaxed">
                        Compare how the same tile looks with different finish options
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
                        Why Use Our 3D Visualizer?
                    </h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-eye text-primary-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-accent-900 mb-2">Examine Details</h3>
                                <p class="text-accent-600">See textures, edges, and surface details that photos can't capture</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-rotate text-primary-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-accent-900 mb-2">Full Perspective</h3>
                                <p class="text-accent-600">Understand how tiles look from all angles, not just the front</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-lightbulb text-primary-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-accent-900 mb-2">Light Interaction</h3>
                                <p class="text-accent-600">See how different finishes react to light from various directions</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-cube text-primary-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-accent-900 mb-2">Better Decisions</h3>
                                <p class="text-accent-600">Make confident choices with complete visual information</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-center animate-slide-up">
                    <div class="bg-white rounded-3xl p-8 max-w-md w-full border border-accent-100 shadow-medium">
                        <div class="text-center mb-6">
                            <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-rotate text-3xl text-primary-600"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-accent-900 mb-2">Ready to Explore?</h3>
                            <p class="text-accent-600">Start examining our tiles in 3D detail</p>
                        </div>
                        
                        <div class="space-y-4">
                            <a href="user_login_form.php" class="btn-primary w-full py-4 rounded-xl font-semibold text-lg flex items-center justify-center gap-3">
                                <i class="fa-solid fa-play"></i>
                                Launch Visualizer
                            </a>
                            
                            <a href="products_view.php" class="btn-secondary w-full py-4 rounded-xl font-semibold text-lg flex items-center justify-center gap-3">
                                <i class="fa-solid fa-grid"></i>
                                Browse Tile Collection
                            </a>
                        </div>
                        
                        <div class="mt-6 p-4 bg-primary-50 rounded-xl border border-primary-200">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-mobile-screen-button text-primary-600"></i>
                                <p class="text-primary-700 text-sm">
                                    <strong>Works on all devices</strong> - Desktop, tablet, and mobile
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

    <?php include 'includes/footer.php'; ?>
</body>
</html>