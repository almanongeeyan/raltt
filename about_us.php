<?php
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About RALTT - Premium Tile Solutions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <script>
        // Tailwind configuration for custom colors and fonts
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#F47C2E', // Primary orange
                        'primary-light': '#F9A562', // Lighter orange
                        'primary-dark': '#D45A0F', // Darker orange
                        'accent': '#2D3748', // Dark gray for text
                        'light-bg': '#F7FAFC', // Light background
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Montserrat', 'sans-serif'],
                    },
                    boxShadow: {
                        'soft': '0 4px 20px rgba(0, 0, 0, 0.05)',
                        'medium': '0 8px 30px rgba(0, 0, 0, 0.08)',
                        'large': '0 15px 40px rgba(0, 0, 0, 0.12)',
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'fade-in': 'fadeIn 0.8s ease-out',
                        'slide-in-left': 'slideInLeft 0.8s ease-out',
                        'slide-in-right': 'slideInRight 0.8s ease-out',
                    }
                }
            }
        }
    </script>

    <style>
        /* Custom animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        /* Custom transitions for the branch card */
        .branch-card-transition {
            transition: all 0.4s ease-in-out;
            will-change: transform, opacity;
        }

        .slide-out-left {
            opacity: 0;
            transform: translateX(-100px);
        }

        .slide-out-right {
            opacity: 0;
            transform: translateX(100px);
        }

        .slide-in-left,
        .slide-in-right {
            opacity: 1;
            transform: translateX(0);
        }
        
        /* Custom bullet points for lists */
        .custom-bullet {
            position: relative;
            padding-left: 1.5rem;
        }
        
        .custom-bullet:before {
            content: "";
            position: absolute;
            left: 0;
            top: 0.5rem;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #F47C2E;
        }
        
        /* Gradient backgrounds */
        .hero-gradient {
            background: linear-gradient(135deg, #FFFFFF 0%, #FEF6EE 100%);
        }
        
        .section-light {
            background-color: #F7FAFC;
        }
        
        .section-accent {
            background: linear-gradient(135deg, #F47C2E 0%, #F9A562 100%);
        }
        
        /* Button styles */
        .btn-primary {
            background-color: #F47C2E;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #D45A0F;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(244, 124, 46, 0.3);
        }
        
        .btn-secondary {
            background-color: white;
            color: #F47C2E;
            border: 2px solid #F47C2E;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background-color: #F47C2E;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(244, 124, 46, 0.2);
        }
        
        /* Card hover effects */
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        /* Custom decorative elements */
        .decorative-circle {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(244,124,46,0.1) 0%, rgba(244,124,46,0.05) 70%, transparent 100%);
            z-index: 0;
        }
        
        /* Improved section spacing */
        .section-spacing {
            padding-top: 6rem;
            padding-bottom: 6rem;
        }
        
        /* Enhanced typography */
        .text-gradient {
            background: linear-gradient(135deg, #F47C2E 0%, #F9A562 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Improved image styling */
        .image-frame {
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        /* Enhanced map styling */
        .map-container {
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 4px solid #F47C2E;
        }
    </style>
</head>

<body class="font-sans text-accent bg-white overflow-x-hidden">
    <!-- Hero Section -->
    <section class="min-h-screen pt-24 pb-12 px-6 lg:px-12 hero-gradient flex items-center relative overflow-hidden section-spacing">
        <!-- Background decorative elements -->
        <div class="decorative-circle w-64 h-64 top-0 right-0 -translate-y-32 translate-x-32"></div>
        <div class="decorative-circle w-96 h-96 bottom-0 left-0 -translate-x-64 translate-y-64"></div>
        <div class="decorative-circle w-48 h-48 top-1/4 left-1/4"></div>
        
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center relative z-10">
            <div class="order-2 lg:order-1 text-center lg:text-left animate-fade-in">
                <h1 class="font-heading text-5xl md:text-6xl lg:text-7xl font-extrabold leading-tight mb-6">
                    <span class="text-gradient">RALTT</span> 
                    <span class="block mt-2 text-accent">Transform Your Spaces</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-600 mb-10 leading-relaxed max-w-2xl">
                    Design stunning spaces with our powerful <strong class="text-primary font-semibold">3D Tile Visualizer</strong>.
                    We bring your tile design imagination to life while offering a seamless shopping experience.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-12">
                    <a href="#" class="btn-primary px-10 py-4 text-lg font-bold text-white rounded-xl shadow-large inline-flex items-center justify-center">
                        Try Our Visualizer
                        <i class="fa-solid fa-arrow-right ml-3 transition-transform group-hover:translate-x-1"></i>
                    </a>
                    <a href="#" class="btn-secondary px-8 py-4 text-lg font-semibold rounded-xl inline-flex items-center justify-center">
                        <i class="fa-solid fa-location-dot mr-3"></i>
                        Find a Store
                    </a>
                </div>
                
                <!-- Stats Section -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-8 max-w-md">
                    <div class="text-center bg-white p-6 rounded-2xl shadow-soft card-hover">
                        <div class="text-3xl md:text-4xl font-bold text-primary mb-2">5+</div>
                        <div class="text-gray-600 font-medium">Branches</div>
                    </div>
                    <div class="text-center bg-white p-6 rounded-2xl shadow-soft card-hover">
                        <div class="text-3xl md:text-4xl font-bold text-primary mb-2">1000+</div>
                        <div class="text-gray-600 font-medium">Tile Designs</div>
                    </div>
                    <div class="text-center bg-white p-6 rounded-2xl shadow-soft card-hover col-span-2 md:col-span-1">
                        <div class="text-3xl md:text-4xl font-bold text-primary mb-2">10+</div>
                        <div class="text-gray-600 font-medium">Years Experience</div>
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2 flex justify-center lg:justify-end animate-slide-in-right">
                <div class="relative">
                    <div class="p-8 bg-white rounded-3xl shadow-large border border-gray-100 animate-float">
                        <div class="h-64 w-64 md:h-96 md:w-96 bg-gradient-to-br from-primary/20 to-primary-light/30 flex items-center justify-center rounded-2xl overflow-hidden relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-transparent"></div>
                            <div class="relative w-full h-full flex items-center justify-center">
                                <i class="fa-solid fa-gem text-white text-7xl md:text-9xl opacity-20 absolute top-4 left-4"></i>
                                <i class="fa-solid fa-cube text-white text-6xl md:text-8xl opacity-25 absolute bottom-6 right-6"></i>
                                <i class="fa-solid fa-ruler-combined text-primary text-8xl md:text-10xl z-10 drop-shadow-lg"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Decorative elements -->
                    <div class="absolute -top-4 -right-4 w-20 h-20 bg-primary/10 rounded-full"></div>
                    <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-primary/10 rounded-full"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Values Section -->
    <section class="py-16 px-6 lg:px-12 section-light section-spacing">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16 animate-fade-in">
                <h2 class="font-heading text-4xl md:text-5xl font-extrabold text-accent mb-4">
                    Our Mission & Values
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">We're committed to transforming spaces with quality tiles and innovative visualization technology.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-3xl shadow-soft border border-gray-100 card-hover animate-slide-in-left">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary/20 to-primary-light/30 rounded-full flex items-center justify-center mb-6 shadow-soft">
                        <i class="fa-solid fa-lightbulb text-primary text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-accent mb-4">Innovation</h3>
                    <p class="text-gray-600 leading-relaxed">We continuously develop new tools like our 3D visualizer to help customers imagine their perfect spaces before making decisions.</p>
                </div>
                
                <div class="bg-white p-8 rounded-3xl shadow-soft border border-gray-100 card-hover animate-fade-in">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary/20 to-primary-light/30 rounded-full flex items-center justify-center mb-6 shadow-soft">
                        <i class="fa-solid fa-award text-primary text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-accent mb-4">Quality</h3>
                    <p class="text-gray-600 leading-relaxed">We source only the finest materials to ensure our tiles stand the test of time in both beauty and durability for your spaces.</p>
                </div>
                
                <div class="bg-white p-8 rounded-3xl shadow-soft border border-gray-100 card-hover animate-slide-in-right">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary/20 to-primary-light/30 rounded-full flex items-center justify-center mb-6 shadow-soft">
                        <i class="fa-solid fa-users text-primary text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-accent mb-4">Customer Focus</h3>
                    <p class="text-gray-600 leading-relaxed">Our team is dedicated to helping you find the perfect solution for your space, with personalized service and expert guidance.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Branches Section -->
    <section class="py-16 px-6 lg:px-12 bg-white section-spacing">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12 animate-fade-in">
                <h2 class="font-heading text-4xl md:text-5xl font-extrabold text-accent mb-4">
                    Explore Our Branches
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Find the RALTT location nearest to you. Each branch offers our full range of tiles and design services.</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-10">
                <div class="lg:w-7/12 animate-slide-in-left">
                    <div class="map-container h-80 lg:h-[500px]">
                        <iframe id="branch-map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15433.080516641774!2d121.03362143009946!3d14.75704764848384!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397bd586a117565%3A0x2832561ce14a3174!2sTala%2C%20Caloocan%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1625000000000!5m2!1sen!2sph" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full h-full"></iframe>
                    </div>
                </div>

                <div class="lg:w-5/12 flex flex-col items-center animate-slide-in-right">
                    <div class="flex items-center justify-between w-full max-w-sm p-4 mb-8 bg-white rounded-2xl shadow-soft border border-gray-200">
                        <button id="branch-left" aria-label="Previous Branch" class="p-3 bg-gray-100 hover:bg-primary rounded-full transition-all duration-300 hover:text-white hover:scale-110">
                            <i class="fa-solid fa-angle-left text-xl"></i>
                        </button>
                        <span id="branch-name" class="text-2xl font-bold text-accent tracking-wider">SAMARIA</span>
                        <button id="branch-right" aria-label="Next Branch" class="p-3 bg-gray-100 hover:bg-primary rounded-full transition-all duration-300 hover:text-white hover:scale-110">
                            <i class="fa-solid fa-angle-right text-xl"></i>
                        </button>
                    </div>

                    <div class="branch-card-transition bg-white rounded-2xl p-8 shadow-medium border border-gray-200 w-full max-w-lg card-hover" id="branch-card">
                        <!-- Branch content will be loaded here via JavaScript -->
                    </div>
                    
                    <div class="mt-8 text-center">
                        <p class="text-gray-600 mb-4">Need help finding the right branch?</p>
                        <a href="#" class="inline-flex items-center text-primary font-semibold hover:text-primary-dark transition duration-200 group">
                            <i class="fa-solid fa-headset mr-2 group-hover:scale-110 transition-transform"></i>
                            Contact Our Support Team
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-16 px-6 lg:px-12 section-light section-spacing">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16 animate-fade-in">
                <h2 class="font-heading text-4xl md:text-5xl font-extrabold text-accent mb-4">
                    Our Services
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">From design to installation, we offer comprehensive tile solutions for your space.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Enhanced Service Card: Tile Selection -->
                <div class="bg-white p-8 rounded-3xl shadow-soft border-2 border-primary/20 text-center card-hover animate-slide-in-left">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary/20 to-primary-light/30 rounded-full flex items-center justify-center mb-6 mx-auto shadow-soft">
                        <i class="fa-solid fa-cube text-primary text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-extrabold text-accent mb-3 tracking-wide">Tile Selection</h3>
                    <p class="text-gray-600 text-lg leading-relaxed">Wide variety of premium tiles for every style and budget.</p>
                </div>

                <!-- Enhanced Service Card: 3D Visualization -->
                <div class="bg-white p-8 rounded-3xl shadow-soft border-2 border-primary/20 text-center card-hover animate-fade-in">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary/20 to-primary-light/30 rounded-full flex items-center justify-center mb-6 mx-auto shadow-soft">
                        <i class="fa-solid fa-vr-cardboard text-primary text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-extrabold text-accent mb-3 tracking-wide">3D Visualization</h3>
                    <p class="text-gray-600 text-lg leading-relaxed">See how tiles will look in your space before purchasing.</p>
                </div>

                <!-- Enhanced Service Card: Design Consultation -->
                <div class="bg-white p-8 rounded-3xl shadow-soft border-2 border-primary/20 text-center card-hover animate-fade-in">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary/20 to-primary-light/30 rounded-full flex items-center justify-center mb-6 mx-auto shadow-soft">
                        <i class="fa-solid fa-ruler-combined text-primary text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-extrabold text-accent mb-3 tracking-wide">Design Consultation</h3>
                    <p class="text-gray-600 text-lg leading-relaxed">Expert advice to help you create the perfect space.</p>
                </div>

                <!-- Enhanced Service Card: Delivery & Installation -->
                <div class="bg-white p-8 rounded-3xl shadow-soft border-2 border-primary/20 text-center card-hover animate-slide-in-right">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary/20 to-primary-light/30 rounded-full flex items-center justify-center mb-6 mx-auto shadow-soft">
                        <i class="fa-solid fa-truck text-primary text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-extrabold text-accent mb-3 tracking-wide">Delivery & Installation</h3>
                    <p class="text-gray-600 text-lg leading-relaxed">Complete service from selection to installation.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 px-6 lg:px-12 section-accent text-white section-spacing">
        <div class="max-w-5xl mx-auto text-center animate-fade-in">
            <h2 class="font-heading text-4xl md:text-5xl font-extrabold mb-6">Ready to Transform Your Space?</h2>
            <p class="text-xl mb-10 max-w-2xl mx-auto">Visit one of our branches or try our 3D visualizer to see how our tiles can bring your vision to life.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#" class="px-10 py-4 text-lg font-bold bg-white text-primary rounded-xl shadow-large hover:bg-gray-100 transition duration-300 inline-flex items-center justify-center group">
                    <i class="fa-solid fa-cube mr-3 group-hover:scale-110 transition-transform"></i>
                    Try Our Visualizer
                </a>
                <a href="#" class="px-10 py-4 text-lg font-bold bg-transparent border-2 border-white text-white rounded-xl hover:bg-white hover:text-primary transition duration-300 inline-flex items-center justify-center group">
                    <i class="fa-solid fa-store mr-3 group-hover:scale-110 transition-transform"></i>
                    Find a Store
                </a>
            </div>
        </div>
    </section>

    <script>
        const branches = [
            {
                name: "SAMARIA",
                address: "St. Vincent Ferrer Ave. Samaria Corner, Tala, Caloocan City",
                hours: "8AM – 6PM",
                contact: "0999 999 9999",
                map: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15433.080516641774!2d121.03362143009946!3d14.75704764848384!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397bd586a117565%3A0x2832561ce14a3174!2sTala%2C%20Caloocan%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1625000000000!5m2!1sen!2sph"
            },
            {
                name: "Phase 1",
                address: "Phase 1, Camarin Road, Caloocan City",
                hours: "9AM – 7PM",
                contact: "0999 999 9999",
                map: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3858.627702581635!2d121.01168531478546!3d14.607425189785834!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397bd30f87a8987%3A0x89d25141b714777d!2sPhase%201%2C%20Camarin%20Rd%2C%20Caloocan%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1625000000000!5m2!1sen!2sph"
            },
            {
                name: "DEPARO",
                address: "189 Deparo Road, Caloocan City",
                hours: "10AM – 8PM",
                contact: "0999 999 9999",
                map: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3858.2961451796805!2d121.017676499333!3d14.75233823334116!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b1c722c4d1b9%3A0xc107b82c47609263!2sRich%20Anne%20Tiles!5e0!3m2!1sen!2sph!4v1756129529669!5m2!1sen!2sph"
            },
            {
                name: "VANGUARD",
                address: "Phase 6, Vanguard, Camarin, North Caloocan",
                hours: "8AM – 5PM",
                contact: "0999 999 9999",
                map: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3858.297019964697!2d121.06286101292358!3d14.759202001446935!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b919d7d11f69%3A0x288d3d951a8a2522!2sVanguard!5e0!3m2!1sen!2sph!4v1756129529669!5m2!1sen!2sph"
            },
            {
                name: "BRIXTON",
                address: "Coaster St. Brixtonville Subdivision, Caloocan City",
                hours: "7AM – 9PM",
                contact: "0999 999 9999",
                map: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3859.083321523455!2d120.97931341478523!3d14.583120689801826!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c8d9c22e4c2f%3A0xf6f7f6f7f6f7f6f7!2sBrixtonville%20Subdivision%2C%20Caloocan%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1625000000000!5m2!1sen!2sph"
            }
        ];

        let currentIndex = 0;
        const branchNameElem = document.getElementById('branch-name');
        const branchCardElem = document.getElementById('branch-card');
        const branchMapElem = document.getElementById('branch-map');

        function renderBranchCard(branch) {
            return `
                <div class="branch-info-block mb-6">
                    <div class="text-primary text-lg font-bold mb-3 flex items-center">
                        <i class="fa-solid fa-location-dot mr-3"></i> 
                        <span>Address</span>
                    </div>
                    <p class="text-gray-600 custom-bullet">${branch.address}</p>
                </div>
                <hr class="border-gray-200 my-6" />
                <div class="branch-info-block mb-6">
                    <div class="text-primary text-lg font-bold mb-3 flex items-center">
                        <i class="fa-solid fa-clock mr-3"></i> 
                        <span>Operating Hours</span>
                    </div>
                    <p class="text-gray-600 custom-bullet">${branch.hours}</p>
                </div>
                <hr class="border-gray-200 my-6" />
                <div class="branch-info-block">
                    <div class="text-primary text-lg font-bold mb-3 flex items-center">
                        <i class="fa-solid fa-phone mr-3"></i> 
                        <span>Contact Details</span>
                    </div>
                    <p class="text-gray-600 custom-bullet">${branch.contact}</p>
                </div>
            `;
        }

        function updateBranchContent(index) {
            const branch = branches[index];
            branchNameElem.textContent = branch.name;
            branchMapElem.src = branch.map;
            branchCardElem.innerHTML = renderBranchCard(branch);
        }

        // Slide transition logic
        function slideTransition(newIndex, direction) {
            const slideOutClass = direction === "left" ? "slide-out-left" : "slide-out-right";
            const slideInClass = direction === "left" ? "slide-in-right" : "slide-in-left";

            // 1. Slide out
            branchCardElem.classList.add(slideOutClass);

            function onSlideOut() {
                branchCardElem.classList.remove(slideOutClass);
                updateBranchContent(newIndex);
                void branchCardElem.offsetWidth;
                branchCardElem.classList.add(slideInClass);
                branchCardElem.removeEventListener('transitionend', onSlideOut);
            }
            branchCardElem.addEventListener('transitionend', onSlideOut);

            function onSlideIn() {
                branchCardElem.classList.remove(slideInClass);
                branchCardElem.removeEventListener('transitionend', onSlideIn);
            }
            branchCardElem.addEventListener('transitionend', onSlideIn);
        }

        document.getElementById('branch-left').addEventListener('click', () => {
            const newIndex = (currentIndex - 1 + branches.length) % branches.length;
            currentIndex = newIndex;
            slideTransition(newIndex, 'left');
        });

        document.getElementById('branch-right').addEventListener('click', () => {
            const newIndex = (currentIndex + 1) % branches.length;
            currentIndex = newIndex;
            slideTransition(newIndex, 'right');
        });

        // Initialize with the first branch
        updateBranchContent(currentIndex);
        branchCardElem.classList.add('slide-in-right');
        branchCardElem.addEventListener('transitionend', function handler() {
            branchCardElem.classList.remove('slide-in-right');
            branchCardElem.removeEventListener('transitionend', handler);
        }, { once: true });

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

        // Observe all animated elements
        document.querySelectorAll('.animate-fade-in, .animate-slide-in-left, .animate-slide-in-right').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>

</html>
<?php
include 'includes/footer.php';
?>