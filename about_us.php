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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

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
        
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            padding: 1rem;
        }
        
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .modal-content {
            background: white;
            border-radius: 1.5rem;
            width: 100%;
            max-width: 1000px;
            max-height: 90vh;
            overflow-y: auto;
            transform: scale(0.9);
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .modal-overlay.active .modal-content {
            transform: scale(1);
            opacity: 1;
        }
        
        .branch-card-transition {
            transition: all 0.4s ease-in-out;
        }
        
        .slide-out-left {
            opacity: 0;
            transform: translateX(-50px);
        }
        
        .slide-out-right {
            opacity: 0;
            transform: translateX(50px);
        }
        
        .slide-in-left,
        .slide-in-right {
            opacity: 1;
            transform: translateX(0);
        }
        
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
            background-color: #ed6631;
        }
        
        .map-container {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .stats-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .floating-element {
            animation: float 8s ease-in-out infinite;
        }
        
        .branch-selector {
            display: flex;
            overflow-x: auto;
            gap: 0.75rem;
            padding-bottom: 1rem;
            scrollbar-width: thin;
        }
        
        .branch-selector::-webkit-scrollbar {
            height: 6px;
        }
        
        .branch-selector::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .branch-selector::-webkit-scrollbar-thumb {
            background: #ed6631;
            border-radius: 10px;
        }
        
        .branch-button {
            flex-shrink: 0;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .branch-button.active {
            background: linear-gradient(135deg, #ed6631 0%, #d55c2c 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(237, 102, 49, 0.3);
        }
        
        .branch-button:not(.active) {
            background: #f5f5f5;
            color: #6d6d6d;
        }
        
        .branch-button:not(.active):hover {
            background: #eaeaea;
        }
        
        .visualizer-showcase {
            background: linear-gradient(135deg, rgba(237, 102, 49, 0.1) 0%, rgba(213, 92, 44, 0.05) 100%);
            border-radius: 1.5rem;
            position: relative;
            overflow: hidden;
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
    <section class="min-h-screen pt-24 pb-12 px-6 lg:px-12 gradient-bg flex items-center relative overflow-hidden section-spacing">
        <!-- Background decorative elements -->
        <div class="decorative-circle w-64 h-64 top-0 right-0 -translate-y-32 translate-x-32"></div>
        <div class="decorative-circle w-96 h-96 bottom-0 left-0 -translate-x-64 translate-y-64"></div>
        <div class="decorative-circle w-48 h-48 top-1/4 left-1/4"></div>
        
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center relative z-10">
            <div class="order-2 lg:order-1 text-center lg:text-left animate-fade-in">
                <h1 class="font-heading text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                    <span class="text-gradient">RALTT</span> 
                    <span class="block mt-2 text-accent-900">Transform Your Spaces</span>
                </h1>
                <p class="text-lg md:text-xl text-accent-600 mb-10 leading-relaxed max-w-2xl">
                    Design stunning spaces with our powerful <strong class="text-primary-600 font-semibold">3D Tile Visualizer</strong>.
                    We bring your tile design imagination to life while offering a seamless shopping experience.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-12">

                    <button id="find-store-btn" class="btn-secondary px-8 py-4 text-lg font-semibold rounded-xl inline-flex items-center justify-center">
                        <i class="fa-solid fa-location-dot mr-3"></i>
                        Find a Store
                    </button>
                </div>
                
                <!-- Enhanced Stats Section -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 max-w-lg">
                    <div class="text-center stats-card p-5 rounded-xl shadow-soft card-hover floating-element">
                        <div class="text-2xl md:text-3xl font-bold text-primary-600 mb-1">5+</div>
                        <div class="text-accent-600 text-sm font-medium">Branches</div>
                    </div>

                </div>
            </div>

            <div class="order-1 lg:order-2 flex justify-center lg:justify-end animate-slide-up">
                <div class="relative">
                    <div class="visualizer-showcase p-6 bg-white/30 backdrop-blur-sm rounded-2xl shadow-large border border-white/50">
                        <div class="h-64 w-64 md:h-80 md:w-80 bg-gradient-to-br from-primary-200 to-primary-100 flex items-center justify-center rounded-2xl overflow-hidden relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary-100 to-transparent"></div>
                            <div class="relative w-full h-full flex items-center justify-center">
                                <i class="fa-solid fa-gem text-primary-300 text-6xl md:text-8xl opacity-30 absolute top-4 left-4"></i>
                                <i class="fa-solid fa-cube text-primary-300 text-5xl md:text-7xl opacity-30 absolute bottom-6 right-6"></i>
                                <i class="fa-solid fa-ruler-combined text-primary-500 text-7xl md:text-9xl z-10"></i>
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

    <!-- Enhanced Branches Section -->
    <section class="py-16 px-6 lg:px-12 bg-white section-spacing">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16 animate-fade-in">
                <h2 class="font-heading text-3xl md:text-4xl font-bold text-accent-900 mb-4">
                    Explore Our Branches
                </h2>
                <p class="text-lg text-accent-600 max-w-2xl mx-auto">Find the RALTT location nearest to you. Each branch offers our full range of tiles and design services.</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-10">
                <div class="lg:w-7/12 animate-slide-up">
                    <div class="map-container h-80 lg:h-[500px]">
                        <iframe id="branch-map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15433.080516641774!2d121.03362143009946!3d14.75704764848384!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397bd586a117565%3A0x2832561ce14a3174!2sTala%2C%20Caloocan%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1625000000000!5m2!1sen!2sph" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full h-full"></iframe>
                    </div>
                </div>

                <div class="lg:w-5/12 flex flex-col items-center animate-slide-up">
                    <div class="flex items-center justify-between w-full max-w-sm p-4 mb-8 bg-white rounded-xl shadow-soft border border-accent-100">
                        <button id="branch-left" aria-label="Previous Branch" class="p-3 bg-accent-50 hover:bg-primary-500 rounded-full transition-all duration-300 hover:text-white hover:scale-110">
                            <i class="fa-solid fa-angle-left text-xl"></i>
                        </button>
                        <span id="branch-name" class="text-xl font-bold text-accent-900 tracking-wider">Samaria</span>
                        <button id="branch-right" aria-label="Next Branch" class="p-3 bg-accent-50 hover:bg-primary-500 rounded-full transition-all duration-300 hover:text-white hover:scale-110">
                            <i class="fa-solid fa-angle-right text-xl"></i>
                        </button>
                    </div>

                    <div class="branch-card-transition bg-white rounded-2xl p-8 shadow-medium border border-accent-100 w-full max-w-lg card-hover" id="branch-card">
                        <!-- Branch content will be loaded here via JavaScript -->
                    </div>
                    
                    <div class="mt-8 text-center">
                        <button id="contact-support" class="inline-flex items-center text-primary-600 font-semibold hover:text-primary-700 transition duration-200 group">
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Store Locator Modal -->
    <div id="store-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="flex justify-between items-center p-6 border-b border-accent-200 sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-accent-900">Find a Store Near You</h3>
                <button id="close-modal" class="text-accent-500 hover:text-primary-600 text-xl transition-colors">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-6">
                    <p class="text-accent-600 mb-4">Select a branch to view its location and details:</p>
                    <div class="branch-selector">
                        <!-- Branch buttons will be dynamically added here -->
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="map-container h-80 lg:h-96">
                        <iframe id="modal-map" src="" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full h-full"></iframe>
                    </div>
                    <div id="modal-branch-details" class="bg-accent-50 rounded-xl p-6">
                        <!-- Branch details will be displayed here -->
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button id="get-directions" class="btn-primary px-6 py-3 text-white rounded-lg font-semibold inline-flex items-center">
                        <i class="fa-solid fa-directions mr-2"></i>
                        Get Directions
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const branches = [
            {
                name: "Samaria",
                address: "St. Vincent Ferrer Ave. Samaria Corner, Tala, Caloocan City",
                hours: "8:00 AM – 6:00 PM",
                contact: "0999 999 9999",
                map: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15433.080516641774!2d121.03362143009946!3d14.75704764848384!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397bd586a117565%3A0x2832561ce14a3174!2sTala%2C%20Caloocan%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1625000000000!5m2!1sen!2sph"
            },
            {
                name: "Phase 1",
                address: "Phase 1, Camarin Road, Caloocan City",
                hours: "9:00 AM – 7:00 PM",
                contact: "0999 999 9999",
                map: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3858.627702581635!2d121.01168531478546!3d14.607425189785834!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397bd30f87a8987%3A0x89d25141b714777d!2sPhase%201%2C%20Camarin%20Rd%2C%20Caloocan%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1625000000000!5m2!1sen!2sph"
            },
            {
                name: "Deparo",
                address: "189 Deparo Road, Caloocan City",
                hours: "10:00 AM – 8:00 PM",
                contact: "0999 999 9999",
                map: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3858.2961451796805!2d121.017676499333!3d14.75233823334116!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b1c722c4d1b9%3A0xc107b82c47609263!2sRich%20Anne%20Tiles!5e0!3m2!1sen!2sph!4v1756129529669!5m2!1sen!2sph"
            },
            {
                name: "Vanguard",
                address: "Phase 6, Vanguard, Camarin, North Caloocan",
                hours: "8:00 AM – 5:00 PM",
                contact: "0999 999 9999",
                map: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3858.297019964697!2d121.06286101292358!3d14.759202001446935!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b919d7d11f69%3A0x288d3d951a8a2522!2sVanguard!5e0!3m2!1sen!2sph!4v1756129529669!5m2!1sen!2sph"
            },
            {
                name: "Brixton",
                address: "Coaster St. Brixtonville Subdivision, Caloocan City",
                hours: "7:00 AM – 9:00 PM",
                contact: "0999 999 9999",
                map: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3859.083321523455!2d120.97931341478523!3d14.583120689801826!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c8d9c22e4c2f%3A0xf6f7f6f7f6f7f6f7!2sBrixtonville%20Subdivision%2C%20Caloocan%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1625000000000!5m2!1sen!2sph"
            }
        ];

        let currentIndex = 0;
        const branchNameElem = document.getElementById('branch-name');
        const branchCardElem = document.getElementById('branch-card');
        const branchMapElem = document.getElementById('branch-map');
        const modal = document.getElementById('store-modal');
        const modalMap = document.getElementById('modal-map');
        const modalBranchDetails = document.getElementById('modal-branch-details');
        const branchButtonsContainer = document.querySelector('.branch-selector');
        const getDirectionsBtn = document.getElementById('get-directions');

        // Render branch selection buttons in modal
        function renderBranchButtons() {
            branchButtonsContainer.innerHTML = '';
            branches.forEach((branch, index) => {
                const button = document.createElement('button');
                button.className = `branch-button ${index === currentIndex ? 'active' : ''}`;
                button.textContent = branch.name;
                button.addEventListener('click', () => {
                    selectBranchInModal(index);
                });
                branchButtonsContainer.appendChild(button);
            });
        }

        // Select branch in modal
        function selectBranchInModal(index) {
            currentIndex = index;
            const branch = branches[index];
            
            // Update modal map
            modalMap.src = branch.map;
            
            // Update branch details
            modalBranchDetails.innerHTML = `
                <h4 class="font-bold text-xl text-accent-900 mb-4">${branch.name}</h4>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <i class="fa-solid fa-location-dot text-primary-600 mt-1 mr-3"></i>
                        <div>
                            <p class="font-medium text-accent-800">Address</p>
                            <p class="text-accent-600">${branch.address}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fa-solid fa-clock text-primary-600 mt-1 mr-3"></i>
                        <div>
                            <p class="font-medium text-accent-800">Operating Hours</p>
                            <p class="text-accent-600">${branch.hours}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fa-solid fa-phone text-primary-600 mt-1 mr-3"></i>
                        <div>
                            <p class="font-medium text-accent-800">Contact</p>
                            <p class="text-accent-600">${branch.contact}</p>
                        </div>
                    </div>
                </div>
            `;
            
            // Update buttons styling
            const buttons = branchButtonsContainer.querySelectorAll('.branch-button');
            buttons.forEach((btn, i) => {
                if (i === index) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
            
            // Update get directions button
            getDirectionsBtn.onclick = () => {
                window.open(`https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(branch.address)}`, '_blank');
            };
        }

        function renderBranchCard(branch) {
            return `
                <div class="branch-info-block mb-6">
                    <div class="text-primary-600 text-lg font-bold mb-3 flex items-center">
                        <i class="fa-solid fa-location-dot mr-3"></i> 
                        <span>Address</span>
                    </div>
                    <p class="text-accent-600 custom-bullet">${branch.address}</p>
                </div>
                <hr class="border-accent-200 my-6" />
                <div class="branch-info-block mb-6">
                    <div class="text-primary-600 text-lg font-bold mb-3 flex items-center">
                        <i class="fa-solid fa-clock mr-3"></i> 
                        <span>Operating Hours</span>
                    </div>
                    <p class="text-accent-600 custom-bullet">9:00AM to 6:00PM Daily</p>
                </div>
                <hr class="border-accent-200 my-6" />
                <div class="branch-info-block">
                    <div class="text-primary-600 text-lg font-bold mb-3 flex items-center">
                        <i class="fa-solid fa-phone mr-3"></i> 
                        <span>Contact Details</span>
                    </div>
                    <p class="text-accent-600 custom-bullet">09817547870</p>
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

        // Modal functionality
        document.getElementById('find-store-btn').addEventListener('click', () => {
            renderBranchButtons();
            selectBranchInModal(currentIndex);
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        document.getElementById('close-modal').addEventListener('click', () => {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });

        // Contact support button
        document.getElementById('contact-support').addEventListener('click', () => {
            alert('Our support team will contact you shortly. Thank you for reaching out to RALTT!');
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
        document.querySelectorAll('.animate-fade-in, .animate-slide-up').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>

</html>
<?php
include 'includes/footer.php';
?>