<?php
// 3D Simulator Page for RALTT
include '../includes/headeruser.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Simulator - Rich Anne Lea Tiles Trading</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7d310a',
                        primaryDark: '#5a2307',
                        secondary: '#cf8756',
                        accent: '#e8a56a',
                        light: '#f8fafc',
                        offwhite: '#f9f5f2',
                        dark: '#270f03',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    boxShadow: {
                        'soft_xl': '0 20px 40px rgba(0,0,0,0.08)',
                        'soft_lg': '0 10px 25px rgba(0,0,0,0.05)',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'fade-in-up': 'fadeInUp 0.6s ease-out',
                        'slide-in-right': 'slideInRight 0.5s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideInRight: {
                            '0%': { opacity: '0', transform: 'translateX(20px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #f8ede3 0%, #f7c59f 100%) fixed;
            min-height: 100vh;
        }
        .hero-immersive {
            background: linear-gradient(120deg, rgba(255,255,255,0.95) 60%, rgba(255, 200, 120, 0.12) 100%), url('../images/visualizer/immersive-bg.jpg') center/cover no-repeat;
            border-radius: 2.5rem;
            box-shadow: 0 8px 32px rgba(125, 49, 10, 0.10);
            padding: 3rem 2rem 2rem 2rem;
            margin-bottom: 2.5rem;
            position: relative;
            overflow: hidden;
        }
        .header-spacer { height: 90px; }
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cf8756; border-radius: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #7d310a; }

        /* 3D Container */
        #visualizer-canvas-container {
            background: radial-gradient(circle at center, #ffffff 0%, #e2e2e2 100%);
            cursor: grab;
            border-radius: 1.5rem;
            overflow: hidden;
        }
        #visualizer-canvas-container:active { cursor: grabbing; }

        /* Controls */
        .control-btn {
            padding: 6px 14px; 
            font-size: 12px; 
            font-weight: 600; 
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9); 
            color: #555; 
            transition: all 0.2s;
            border: 1px solid rgba(0,0,0,0.1); 
            white-space: nowrap;
        }
        .control-btn:hover { 
            background: white; 
            color: #7d310a; 
            border-color: #7d310a; 
        }
        .control-btn.active {
            background: #7d310a; 
            color: white; 
            border-color: #7d310a;
            box-shadow: 0 2px 6px rgba(125, 49, 10, 0.2);
        }
        .control-btn:disabled {
            background: #f3f4f6;
            color: #9ca3af;
            border-color: #e5e7eb;
            cursor: not-allowed;
            opacity: 0.7;
        }
        .control-btn:disabled:hover {
            background: #f3f4f6;
            color: #9ca3af;
            border-color: #e5e7eb;
        }
        
        /* Object Library Styles */
        .object-category {
            border-bottom: 1px solid #e5e7eb;
        }
        .object-category:last-child {
            border-bottom: none;
        }
        .object-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            color: #374151;
        }
        .object-item:hover {
            background-color: #f9f5f2;
            color: #7d310a;
        }
        .object-item.active {
            background-color: #7d310a;
            color: white;
        }
        .object-item.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #f9fafb;
        }
        .object-item.disabled:hover {
            background-color: #f9fafb;
            color: #374151;
        }
        .object-item-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            border-radius: 6px;
            color: #7d310a;
        }
        .object-item.active .object-item-icon {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .object-item.disabled .object-item-icon {
            background: #f3f4f6;
            color: #9ca3af;
        }
        
        /* Toolbar */
        .toolbar-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
        }
        .toolbar-btn:hover {
            background: #f9f5f2;
            color: #7d310a;
        }
        .toolbar-btn.active {
            background: #7d310a;
            color: white;
        }
        .toolbar-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .toolbar-btn.disabled:hover {
            background: transparent;
            color: #6b7280;
        }
        
        /* Loading Animation */
        @keyframes shimmer {
            0% { background-position: -468px 0; }
            100% { background-position: 468px 0; }
        }
        .shimmer {
            animation: shimmer 1.5s infinite linear;
            background: linear-gradient(to right, #f6f7f8 8%, #edeef1 18%, #f6f7f8 33%);
            background-size: 800px 104px;
        }
        
        /* Product Card Enhancements */
        .product-card {
            transition: all 0.3s ease;
            border-radius: 1rem;
            overflow: hidden;
            position: relative;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        /* Light Toggle */
        .light-toggle {
            position: relative;
            width: 80px;
            height: 32px;
            background: #f3f4f6;
            border-radius: 15px;
            cursor: pointer;
            border: 2px solid #e5e7eb;
            outline: none;
            transition: background 0.3s, border-color 0.3s;
        }
        .light-toggle .toggle-slider {
            position: absolute;
            top: 4px;
            left: 10px;
            width: 24px;
            height: 24px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.12);
            transition: left 0.3s, background 0.3s;
            z-index: 2;
        }
        .light-toggle.active {
            background: #23253a;
            border-color: #1a237e;
        }
        .light-toggle.active .toggle-slider {
            left: 46px;
            background: #fbbf24;
        }
        .light-toggle .toggle-icon {
            z-index: 3;
            pointer-events: none;
            opacity: 1;
            transition: opacity 0.3s;
        }
        .light-toggle .sun-icon { opacity: 1; }
        .light-toggle .moon-icon { opacity: 1; }
        .light-toggle.active .sun-icon { opacity: 0; }
        .light-toggle:not(.active) .moon-icon { opacity: 0; }
        .light-toggle .fa-sun {
            color: #fbbf24;
            filter: drop-shadow(0 1px 2px #fff8);
        }
        .light-toggle .fa-moon {
            color: #b3c2f7;
            filter: drop-shadow(0 1px 2px #23253a88);
        }
        
        /* Selection Outline */
        .selected-outline {
            outline: 2px solid #7d310a;
            outline-offset: 2px;
        }
        
        /* Filter Chips */
        .filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #f3f4f6;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .filter-chip:hover {
            background: #e5e7eb;
        }
        .filter-chip.active {
            background: #7d310a;
            color: white;
        }
        
        /* Object Highlight */
        .object-highlight {
            outline: 2px solid #7d310a;
            outline-offset: 2px;
        }
    </style>
</head>
<body class="text-gray-800 min-h-screen pt-24 pb-12 px-4 md:px-8">
    <div class="header-spacer"></div>

    <div class="max-w-[1600px] mx-auto">
        <div class="hero-immersive animate-fade-in text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-black text-primary mb-3 tracking-tight drop-shadow-lg">
                RALTT 3D Tile Simulator
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Design your perfect space with interactive objects, doors, and appliances.<br>
                <span class="text-secondary font-bold">Select a tile first, then drag objects to customize your space.</span>
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left Panel - Object Library -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl shadow-soft_xl overflow-hidden border border-gray-100 animate-fade-in-up">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-primary flex items-center gap-2">
                            <i class="fas fa-cube text-secondary"></i>
                            Object Library
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">Select a tile first to enable</p>
                    </div>
                    
                    <div class="p-4 space-y-4 max-h-[500px] overflow-y-auto custom-scrollbar">
                        <!-- Doors Category -->
                        <div class="object-category pb-4">
                            <h4 class="text-sm font-bold text-primary mb-2 flex items-center gap-2">
                                <span class="object-item-icon" style="background:linear-gradient(135deg,#fbeee6,#f7c59f);color:#7d310a;"><i class="fas fa-door-open"></i></span> Doors
                            </h4>
                            <div class="space-y-2">
                                <div class="object-item disabled" data-type="door" data-model="wooden">
                                    <div class="object-item-icon" style="background:linear-gradient(135deg,#fbeee6,#f7c59f);color:#7d310a;"><i class="fas fa-door-closed"></i></div>
                                    <span class="text-sm">Wooden Door</span>
                                </div>
                                <div class="object-item disabled" data-type="door" data-model="glass">
                                    <div class="object-item-icon" style="background:linear-gradient(135deg,#e0f7fa,#b2ebf2);color:#00796b;"><i class="fas fa-door-closed"></i></div>
                                    <span class="text-sm">Glass Door</span>
                                </div>
                                <div class="object-item disabled" data-type="door" data-model="sliding">
                                    <div class="object-item-icon" style="background:linear-gradient(135deg,#f3e5f5,#ce93d8);color:#6a1b9a;"><i class="fas fa-door-closed"></i></div>
                                    <span class="text-sm">Sliding Door</span>
                                </div>
                            </div>
                        </div>

                        <!-- Appliances Category -->
                        <div class="object-category pb-4">
                            <h4 class="text-sm font-bold text-primary mb-2 flex items-center gap-2">
                                <span class="object-item-icon" style="background:linear-gradient(135deg,#e0f7fa,#b2ebf2);color:#00796b;"><i class="fas fa-blender"></i></span> Appliances
                            </h4>
                            <div class="space-y-2">
                                <div class="object-item disabled" data-type="appliance" data-model="refrigerator">
                                    <div class="object-item-icon" style="background:linear-gradient(135deg,#e3f2fd,#90caf9);color:#1565c0;"><i class="fas fa-snowflake"></i></div>
                                    <span class="text-sm">Refrigerator</span>
                                </div>
                                <div class="object-item disabled" data-type="appliance" data-model="oven">
                                    <div class="object-item-icon" style="background:linear-gradient(135deg,#fff3e0,#ffb74d);color:#e65100;"><i class="fas fa-fire"></i></div>
                                    <span class="text-sm">Oven</span>
                                </div>
                                <div class="object-item disabled" data-type="appliance" data-model="sink">
                                    <div class="object-item-icon" style="background:linear-gradient(135deg,#e0f2f1,#80cbc4);color:#00695c;"><i class="fas fa-faucet"></i></div>
                                    <span class="text-sm">Kitchen Sink</span>
                                </div>
                                <div class="object-item disabled" data-type="appliance" data-model="dishwasher">
                                    <div class="object-item-icon" style="background:linear-gradient(135deg,#f3e5f5,#ce93d8);color:#6a1b9a;"><i class="fas fa-broom"></i></div>
                                    <span class="text-sm">Dishwasher</span>
                                </div>
                            </div>
                        </div>

                        <!-- Furniture Category -->
                        <div class="object-category pb-4">
                            <h4 class="text-sm font-bold text-primary mb-2 flex items-center gap-2">
                                <span class="object-item-icon" style="background:linear-gradient(135deg,#f3e5f5,#ce93d8);color:#6a1b9a;"><i class="fas fa-couch"></i></span> Furniture
                            </h4>
                            <div class="space-y-2">
                                <div class="object-item disabled" data-type="furniture" data-model="sofa">
                                    <div class="object-item-icon" style="background:linear-gradient(135deg,#fbeee6,#f7c59f);color:#7d310a;"><i class="fas fa-couch"></i></div>
                                    <span class="text-sm">Sofa</span>
                                </div>
                                <div class="object-item disabled" data-type="furniture" data-model="table">
                                    <div class="object-item-icon" style="background:linear-gradient(135deg,#fff3e0,#ffb74d);color:#e65100;"><i class="fas fa-table"></i></div>
                                    <span class="text-sm">Coffee Table</span>
                                </div>
                                <div class="object-item disabled" data-type="furniture" data-model="chair">
                                    <div class="object-item-icon" style="background:linear-gradient(135deg,#e0f2f1,#80cbc4);color:#00695c;"><i class="fas fa-chair"></i></div>
                                    <span class="text-sm">Chair</span>
                                </div>
                                <div class="object-item disabled" data-type="furniture" data-model="cabinet">
                                    <div class="object-item-icon" style="background:linear-gradient(135deg,#e3f2fd,#90caf9);color:#1565c0;"><i class="fas fa-archive"></i></div>
                                    <span class="text-sm">Cabinet</span>
                                </div>
                            </div>
                        </div>

                        <!-- Decorations Category -->
                        <div class="object-category pb-4">
                            <h4 class="text-sm font-bold text-primary mb-2 flex items-center gap-2">
                                <span class="object-item-icon" style="background:linear-gradient(135deg,#fffde7,#fff9c4);color:#fbc02d;"><i class="fas fa-palette"></i></span> Decorations
                            </h4>
                            <div class="space-y-2">
                                <div class="object-item disabled" data-type="decoration" data-model="plant">
                                    <div class="object-item-icon" style="background:linear-gradient(135deg,#e8f5e9,#a5d6a7);color:#388e3c;"><i class="fas fa-leaf"></i></div>
                                    <span class="text-sm">Plant</span>
                                </div>
                                <div class="object-item disabled" data-type="decoration" data-model="lamp">
                                    <div class="object-item-icon" style="background:linear-gradient(135deg,#fffde7,#fff9c4);color:#fbc02d;"><i class="fas fa-lightbulb"></i></div>
                                    <span class="text-sm">Table Lamp</span>
                                </div>
                                <div class="object-item disabled" data-type="decoration" data-model="rug">
                                    <div class="object-item-icon" style="background:linear-gradient(135deg,#fbeee6,#f7c59f);color:#7d310a;"><i class="fas fa-square"></i></div>
                                    <span class="text-sm">Area Rug</span>
                                </div>
                                <div class="object-item disabled" data-type="decoration" data-model="painting">
                                    <div class="object-item-icon" style="background:linear-gradient(135deg,#e3f2fd,#90caf9);color:#1565c0;"><i class="fas fa-image"></i></div>
                                    <span class="text-sm">Wall Art</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tools Panel -->
                <div class="bg-white rounded-3xl shadow-soft_xl overflow-hidden border border-gray-100 animate-fade-in-up">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-primary flex items-center gap-2">
                            <i class="fas fa-lightbulb text-secondary"></i>
                            Lighting
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Lighting</span>
                            <button id="light-toggle" class="light-toggle flex items-center justify-between relative focus:outline-none transition-all duration-300" type="button" aria-pressed="true" title="Switch to night mode">
                                <span class="sr-only">Toggle lighting</span>
                                <span class="toggle-icon sun-icon absolute left-[10px] top-1/2 -translate-y-1/2 text-xl transition-all duration-300"><i class="fas fa-sun"></i></span>
                                <span class="toggle-icon moon-icon absolute right-[10px] top-1/2 -translate-y-1/2 text-xl transition-all duration-300"><i class="fas fa-moon"></i></span>
                                <span class="toggle-slider transition-all duration-300"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main 3D Canvas -->
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white rounded-3xl shadow-soft_xl overflow-hidden border border-gray-100 relative animate-fade-in-up">
                    <div class="p-4 px-6 border-b border-gray-50 bg-white z-10 relative">
                        <div class="flex flex-wrap justify-between items-center gap-3">
                            <div>
                                <h2 class="text-xl font-bold text-primary flex items-center gap-2">
                                    <i class="fas fa-cube text-secondary"></i> 3D Tile Simulator
                                </h2>
                                <p class="text-xs text-gray-500 mt-0.5" id="selected-tile-name">Select a tile to begin</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <button id="btn-reset-scene" class="control-btn" disabled>
                                    <i class="fas fa-redo mr-1"></i> Reset
                                </button>
                                <a id="btn-view-details" href="#" class="hidden items-center gap-1 px-3 py-1.5 bg-offwhite text-primary text-xs font-bold rounded-full hover:bg-primary hover:text-white transition-colors">
                                    Details <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    
                    </div>

                    <div class="relative">
                        <div id="tiling-controls" class="absolute top\.5 right-3 flex flex-col gap-2 z-10 opacity-50 pointer-events-none transition-opacity">
                            <div class="flex gap-1 p-1 bg-white/60 backdrop-blur-md rounded-lg shadow-sm">
                                <span class="text-[10px] font-bold text-primary uppercase px-2 flex items-center">Grid:</span>
                                <button class="control-btn tiling-btn active" data-size="1">1x1</button>
                                <button class="control-btn tiling-btn" data-size="2">2x2</button>
                                <button class="control-btn tiling-btn" data-size="3">3x3</button>
                                <button class="control-btn tiling-btn" data-size="4">4x4</button>
                            </div>
                        </div>
                        <div id="visualizer-canvas-container" class="w-full h-[600px] relative">
                            <div id="canvas-overlay" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 z-20 pointer-events-none transition-opacity duration-500">
                                <div class="bg-white/80 backdrop-blur px-6 py-4 rounded-2xl shadow-sm flex flex-col items-center">
                                    <i class="fas fa-hand-pointer text-3xl mb-3 text-secondary animate-bounce"></i>
                                    <p class="font-semibold text-sm">Select a tile from the list</p>
                                    <p class="text-xs mt-1">Then drag objects from the library</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="py-3 bg-gray-50 text-center text-[11px] font-bold uppercase tracking-wider text-gray-400 flex justify-center gap-8 border-t border-gray-100">
                        <span><i class="fas fa-mouse mr-1"></i> Drag to Rotate</span>
                        <span><i class="fas fa-search-plus mr-1"></i> Scroll to Zoom</span>
                        <span><i class="fas fa-arrows-alt mr-1"></i> Drag Objects to Move</span>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Products -->
            <div class="lg:col-span-3 h-full">
                <div class="bg-white rounded-3xl shadow-soft_xl border border-gray-100 overflow-hidden sticky top-24 flex flex-col max-h-[calc(100vh-8rem)] animate-slide-in-right">
                    <div class="p-4 border-b border-gray-100 bg-white z-10">
                        <div class="relative mb-4">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="product-search" placeholder="Search collection..." class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent outline-none transition-all text-sm font-medium text-primary">
                        </div>
                        
                        <div class="flex flex-wrap gap-2 mb-2">
                            <span class="text-xs font-medium text-gray-500 w-full">FILTER BY DESIGN</span>
                            <div id="filter-chips" class="flex flex-wrap gap-2">
                                </div>
                        </div>
                        
                        <div class="flex items-center justify-between mt-4">
                            <span class="text-xs font-medium text-gray-500" id="product-count">0 products</span>
                            <button id="clear-filters" class="text-xs text-primary hover:text-primaryDark font-medium flex items-center gap-1">
                                <i class="fas fa-times"></i> Clear filters
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto p-4 pt-0">
                        <div id="product-grid" class="grid grid-cols-1 gap-3">
                            <div class="space-y-3">
                                <div class="h-32 bg-gray-100 rounded-xl shimmer"></div>
                                <div class="h-32 bg-gray-100 rounded-xl shimmer"></div>
                                <div class="h-32 bg-gray-100 rounded-xl shimmer"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>

    <script async src="https://unpkg.com/es-module-shims@1.8.0/dist/es-module-shims.js"></script>
    <script type="importmap">
    {
        "imports": {
            "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
            "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
        }
    }
    </script>

    <style>
        /* Overlay arrows for object controls */
        #object-arrows {
            position: absolute;
            z-index: 30;
            display: flex;
            gap: 5px;
            display: none;
            pointer-events: none;
        }
        .object-arrow-btn {
            pointer-events: auto;
            background: rgba(255,255,255,0.95);
            border: 1px solid #cf8756;
            color: #7d310a;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 2px 8px rgba(125,49,10,0.08);
            margin: 0 4px;
            transition: background 0.2s, color 0.2s;
        }
        .object-arrow-btn:hover {
            background: #7d310a;
            color: #fff;
        }
    </style>
    <script type="module">
        import * as THREE from 'three';
        import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
        import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';
        import { DragControls } from 'three/addons/controls/DragControls.js';

        // =========================================
        // STATE MANAGEMENT
        // =========================================
        const state = {
            currentTile: null,
            currentTexture: null,
            currentGridSize: 1,
            products: [],
            activeFilters: new Set(),
            isDaytime: true,
            activeTool: 'select',
            selectedObject: null,
            placedObjects: [],
            tileSelected: false,
            isDragging: false,
            platformBounds: {
                minX: -1.8,
                maxX: 1.8,
                minZ: -1.8,
                maxZ: 1.8
            }
        };

        // =========================================
        // 3D ENGINE SETUP
        // =========================================
        let scene, camera, renderer, controls, dragControls, floorObject;
        let objectArrowsEl = null;
        const textureLoader = new THREE.TextureLoader();
        let ambientLight, directionalLight, pointLights = [];

        function init3D() {
            const container = document.getElementById('visualizer-canvas-container');
            renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
            renderer.setSize(container.clientWidth, container.clientHeight);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            renderer.toneMapping = THREE.ACESFilmicToneMapping;
            renderer.toneMappingExposure = 1.2;
            renderer.shadowMap.enabled = true;
            renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            container.appendChild(renderer.domElement);

            scene = new THREE.Scene();
            const pmremGenerator = new THREE.PMREMGenerator(renderer);
            scene.environment = pmremGenerator.fromScene(new RoomEnvironment(renderer)).texture;

            camera = new THREE.PerspectiveCamera(45, container.clientWidth / container.clientHeight, 0.1, 100);
            camera.position.set(0, 2.5, 4.5);

            controls = new OrbitControls(camera, renderer.domElement);
            controls.target.set(0, 0.8, 0);
            controls.enableDamping = true;
            controls.dampingFactor = 0.05;
            controls.maxPolarAngle = Math.PI / 2.05;
            controls.minDistance = 2;
            controls.maxDistance = 10;
            controls.enablePan = false;

            // Lighting setup
            setupLighting();

            // Initialize basic drag controls
            dragControls = new DragControls([], camera, renderer.domElement);
            setupDragControls();

            // Add platform boundaries
            createPlatformBoundaries();

            window.addEventListener('resize', () => {
                camera.aspect = container.clientWidth / container.clientHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(container.clientWidth, container.clientHeight);
            });
            
            renderer.setAnimationLoop(() => {
                controls.update();
                renderer.render(scene, camera);
            });
        }

        // FIXED: Proper drag controls setup
        function setupDragControls() {
            dragControls.dispose();
            dragControls = new DragControls(state.placedObjects, camera, renderer.domElement);
            
            dragControls.addEventListener('dragstart', function(event) {
                controls.enabled = false;
                let dragMesh = event.object;
                let obj = dragMesh.userData.parentGroup || dragMesh;
                state.selectedObject = obj;
                state.isDragging = true;
                updateObjectSelection();
                
                // Store initial position and rotation
                dragMesh.userData.startPosition = obj.position.clone();
                dragMesh.userData.startEuler = new THREE.Euler().copy(obj.rotation);
            });
            
            dragControls.addEventListener('drag', function(event) {
                let dragMesh = event.object;
                let obj = dragMesh.userData.parentGroup || dragMesh;
                
                // Get the movement in world space
                const movementX = dragMesh.position.x - dragMesh.userData.startPosition.x;
                const movementZ = dragMesh.position.z - dragMesh.userData.startPosition.z;
                
                // Create a movement vector
                const movement = new THREE.Vector3(movementX, 0, movementZ);
                
                // Apply rotation to the movement vector (rotate according to object's Y rotation)
                const rotationMatrix = new THREE.Matrix4().makeRotationY(obj.rotation.y);
                movement.applyMatrix4(rotationMatrix);
                
                // Apply the rotated movement to the object
                obj.position.x = dragMesh.userData.startPosition.x + movement.x;
                obj.position.z = dragMesh.userData.startPosition.z + movement.z;
                
                // Ensure object sits on platform
                positionObjectOnPlatform(obj);
                
                // Update drag mesh position to match
                dragMesh.position.copy(obj.position);
            });
            
            dragControls.addEventListener('dragend', function(event) {
                controls.enabled = true;
                state.isDragging = false;
                let dragMesh = event.object;
                let obj = dragMesh.userData.parentGroup || dragMesh;
                // Final constraint check
                constrainObjectToPlatform(obj);
                // Sync drag mesh position/rotation with parent group
                syncDragMeshWithObject(obj);
                // Clear temp data
                delete dragMesh.userData.startPosition;
                delete dragMesh.userData.startEuler;
            });
        }

        // Overlay UI for object arrows
        function createObjectArrowsUI() {
            if (document.getElementById('object-arrows')) return;
            const container = document.getElementById('visualizer-canvas-container');
            const arrows = document.createElement('div');
            arrows.id = 'object-arrows';
            arrows.innerHTML = `
                <button class="object-arrow-btn" id="arrow-rotate" title="Rotate"><i class="fas fa-undo"></i></button>
                <button class="object-arrow-btn" id="arrow-delete" title="Delete"><i class="fas fa-trash"></i></button>
            `;
            container.appendChild(arrows);
            objectArrowsEl = arrows;
            
            // Event listeners
            arrows.querySelector('#arrow-rotate').addEventListener('click', (e) => {
                e.stopPropagation();
                rotateSelectedObject();
            });
            arrows.querySelector('#arrow-delete').addEventListener('click', (e) => {
                e.stopPropagation();
                deleteSelectedObject();
                hideObjectArrows();
            });
        }

        function showObjectArrows(screenX, screenY) {
            if (!objectArrowsEl) return;
            objectArrowsEl.style.display = 'flex';
            objectArrowsEl.style.left = `${screenX - 50}px`;
            objectArrowsEl.style.top = `${screenY - 50}px`;
        }
        function hideObjectArrows() {
            if (objectArrowsEl) objectArrowsEl.style.display = 'none';
        }

        function setupLighting() {
            // Clear existing lights
            if (ambientLight) scene.remove(ambientLight);
            if (directionalLight) scene.remove(directionalLight);
            pointLights.forEach(light => scene.remove(light));
            pointLights = [];

            // Change scene background and tone down platform color
            if (state.isDaytime) {
                scene.background = new THREE.Color(0xf8ede3);
                ambientLight = new THREE.AmbientLight(0xffffff, 0.9);
                directionalLight = new THREE.DirectionalLight(0xfff8f0, 1.8);
                directionalLight.position.set(8, 12, 6);
                
                // Add fill light
                const fillLight = new THREE.DirectionalLight(0xfff0e0, 0.4);
                fillLight.position.set(-5, 5, -5);
                scene.add(fillLight);
                pointLights.push(fillLight);
            } else {
                scene.background = new THREE.Color(0x181c2a);
                ambientLight = new THREE.AmbientLight(0x223366, 0.2);
                directionalLight = new THREE.DirectionalLight(0xffaa66, 0.3);
                directionalLight.position.set(3, 8, 3);
                
                // Enhanced point lights for night scene
                const pointLight1 = new THREE.PointLight(0xffaa55, 0.8, 8);
                pointLight1.position.set(1.5, 3, 1.5);
                scene.add(pointLight1);
                pointLights.push(pointLight1);
                
                const pointLight2 = new THREE.PointLight(0xaaccff, 0.5, 6);
                pointLight2.position.set(-1.5, 2.5, -1.5);
                scene.add(pointLight2);
                pointLights.push(pointLight2);
                
                const pointLight3 = new THREE.PointLight(0xffdd99, 0.3, 5);
                pointLight3.position.set(0, 2, 0);
                scene.add(pointLight3);
                pointLights.push(pointLight3);
            }

            directionalLight.castShadow = true;
            directionalLight.shadow.mapSize.set(2048, 2048);
            directionalLight.shadow.camera.near = 0.5;
            directionalLight.shadow.camera.far = 30;
            directionalLight.shadow.bias = -0.0005;

            scene.add(ambientLight);
            scene.add(directionalLight);
        }

        function toggleLighting() {
            state.isDaytime = !state.isDaytime;
            setupLighting();
            updatePlatformAndObjectColors();
            // Update UI
            const lightToggle = document.getElementById('light-toggle');
            if (state.isDaytime) {
                lightToggle.classList.remove('active');
                lightToggle.setAttribute('aria-pressed', 'true');
                lightToggle.title = 'Switch to night mode';
            } else {
                lightToggle.classList.add('active');
                lightToggle.setAttribute('aria-pressed', 'false');
                lightToggle.title = 'Switch to day mode';
            }
        }

        function updatePlatformAndObjectColors() {
            // Update platform (floor) color
            if (floorObject) {
                floorObject.traverse(child => {
                    if (child.isMesh && child.material) {
                        if (state.isDaytime) {
                            if (child.material.color) child.material.color.set(0xffffff);
                        } else {
                            if (child.material.color) child.material.color.set(0x23253a);
                        }
                    }
                });
            }

            // Night mode color effect parameters (fine-tuned)
            // Use a richer blue-purple tint for moonlight effect
            const nightTint = { r: 0x33/255, g: 0x3a/255, b: 0x6d/255 }; // #333a6d
            const nightBlend = 0.48; // more tint for a stronger effect
            const nightDarken = 0.78; // slightly less darkening
            const nightDesaturate = 0.45; // less desaturation, keep some color vibrancy

            // Helper: blend color with tint
            function blendColor(orig, tint, blend) {
                return {
                    r: orig.r * (1-blend) + tint.r * blend,
                    g: orig.g * (1-blend) + tint.g * blend,
                    b: orig.b * (1-blend) + tint.b * blend
                };
            }

            // Helper: desaturate color
            function desaturateColor(rgb, amount) {
                // Convert to grayscale
                const gray = rgb.r * 0.3 + rgb.g * 0.59 + rgb.b * 0.11;
                return {
                    r: rgb.r * (1-amount) + gray * amount,
                    g: rgb.g * (1-amount) + gray * amount,
                    b: rgb.b * (1-amount) + gray * amount
                };
            }

            // Update all placed objects
            state.placedObjects.forEach(dragMesh => {
                const parentGroup = dragMesh.userData.parentGroup;
                if (parentGroup) {
                    parentGroup.traverse(child => {
                        if (child.isMesh && child.material && child.material.color) {
                            if (!child.material.userData) child.material.userData = {};
                            if (!child.material.userData.originalColor) {
                                child.material.userData.originalColor = child.material.color.clone();
                            }
                            let orig = child.material.userData.originalColor;
                            if (!state.isDaytime) {
                                // Night mode: blend with blue, desaturate, darken
                                let rgb = { r: orig.r, g: orig.g, b: orig.b };
                                rgb = blendColor(rgb, nightTint, nightBlend);
                                rgb = desaturateColor(rgb, nightDesaturate);
                                rgb.r *= nightDarken;
                                rgb.g *= nightDarken;
                                rgb.b *= nightDarken;
                                child.material.color.setRGB(rgb.r, rgb.g, rgb.b);
                            } else {
                                child.material.color.copy(orig);
                            }
                        }
                    });
                }
            });
        }

        function createPlatformBoundaries() {
            // Platform boundaries for collision detection
            state.platformBounds = {
                minX: -1.8,
                maxX: 1.8,
                minZ: -1.8,
                maxZ: 1.8
            };
        }

        function positionObjectOnPlatform(object) {
            const bbox = new THREE.Box3().setFromObject(object);
            const objectBaseY = bbox.min.y;
            const platformTopY = 0.025;
            const yOffset = platformTopY - objectBaseY;
            object.position.y += yOffset;
        }

        function constrainObjectToPlatform(object) {
            // Ensure object stays within platform boundaries
            object.position.x = THREE.MathUtils.clamp(
                object.position.x, 
                state.platformBounds.minX, 
                state.platformBounds.maxX
            );
            object.position.z = THREE.MathUtils.clamp(
                object.position.z, 
                state.platformBounds.minZ, 
                state.platformBounds.maxZ
            );

            // Ensure object sits on platform
            positionObjectOnPlatform(object);
        }

        // =========================================
        // OBJECT CREATION FUNCTIONS
        // =========================================
        const materials = {
            woodDark: new THREE.MeshStandardMaterial({ color: 0x5d4037, roughness: 0.7, metalness: 0.1 }),
            woodLight: new THREE.MeshStandardMaterial({ color: 0xd7ccc8, roughness: 0.6, metalness: 0.1 }),
            woodMedium: new THREE.MeshStandardMaterial({ color: 0x8d6e63, roughness: 0.6, metalness: 0.1 }),
            metal: new THREE.MeshStandardMaterial({ color: 0xb0bec5, metalness: 0.9, roughness: 0.2 }),
            metalDark: new THREE.MeshStandardMaterial({ color: 0x546e7a, metalness: 0.8, roughness: 0.3 }),
            glass: new THREE.MeshPhysicalMaterial({ 
                color: 0xffffff, 
                transmission: 0.9, 
                roughness: 0.05, 
                metalness: 0,
                transparent: true,
                opacity: 0.8
            }),
            fabric: new THREE.MeshStandardMaterial({ color: 0x795548, roughness: 0.9 }),
            fabricLight: new THREE.MeshStandardMaterial({ color: 0xa1887f, roughness: 0.8 }),
            plastic: new THREE.MeshStandardMaterial({ color: 0xf5f5f5, roughness: 0.4 }),
            plasticDark: new THREE.MeshStandardMaterial({ color: 0x424242, roughness: 0.5 }),
            ceramic: new THREE.MeshPhysicalMaterial({ color: 0xffffff, roughness: 0.1, metalness: 0.1, reflectivity: 0.9 }),
            plant: new THREE.MeshStandardMaterial({ color: 0x2e7d32, roughness: 0.8 }),
            plantLight: new THREE.MeshStandardMaterial({ color: 0x4caf50, roughness: 0.7 }),
            white: new THREE.MeshStandardMaterial({ color: 0xffffff, roughness: 0.7 }),
            black: new THREE.MeshStandardMaterial({ color: 0x212121, roughness: 0.6 }),
            stainless: new THREE.MeshStandardMaterial({ color: 0xe0e0e0, metalness: 0.8, roughness: 0.2 }),
            bronze: new THREE.MeshStandardMaterial({ color: 0xcd7f32, metalness: 0.7, roughness: 0.3 })
        };

        function enableShadows(mesh) {
            mesh.castShadow = true;
            mesh.receiveShadow = true;
            return mesh;
        }

        // Object creation functions (createDoor, createAppliance, createFurniture, createDecoration)
        // ... [Keep all the object creation functions exactly as they were - they work fine]
        function createDoor(type) {
            const group = new THREE.Group();
            let doorMaterial, frameMaterial;
            
            if (type === 'glass') {
                doorMaterial = materials.glass;
                frameMaterial = materials.metalDark;
                // Door frame
                const frame = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(1.2, 2.2, 0.1),
                    frameMaterial
                ));
                group.add(frame);
                // Glass door
                const door = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(1.0, 2.0, 0.05),
                    doorMaterial
                ));
                door.position.z = 0.025;
                group.add(door);
                // Handle
                const handle = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.02, 0.02, 0.1, 8),
                    materials.bronze
                ));
                handle.position.set(0.4, 0, 0.05);
                handle.rotation.z = Math.PI / 2;
                group.add(handle);
            } else if (type === 'sliding') {
                doorMaterial = materials.metal;
                frameMaterial = materials.metalDark;
                // Sliding door frame
                const frame = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(1.3, 2.25, 0.12),
                    frameMaterial
                ));
                group.add(frame);
                // Rail
                const rail = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(1.3, 0.08, 0.12),
                    materials.metalDark
                ));
                rail.position.y = 1.15;
                group.add(rail);
                // Two sliding panels
                const panel1 = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.6, 2.0, 0.05),
                    doorMaterial
                ));
                panel1.position.set(-0.35, 0, 0.03);
                group.add(panel1);
                const panel2 = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.6, 2.0, 0.05),
                    doorMaterial
                ));
                panel2.position.set(0.35, 0.1, 0.06);
                group.add(panel2);
                // Handles
                const handle1 = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.02, 0.02, 0.12, 8),
                    materials.bronze
                ));
                handle1.position.set(-0.55, 0, 0.08);
                handle1.rotation.z = Math.PI / 2;
                group.add(handle1);
                const handle2 = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.02, 0.02, 0.12, 8),
                    materials.bronze
                ));
                handle2.position.set(0.55, 0, 0.08);
                handle2.rotation.z = Math.PI / 2;
                group.add(handle2);
            } else { // wooden
                doorMaterial = materials.woodDark;
                frameMaterial = materials.woodMedium;
                // Door frame
                const frame = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(1.2, 2.2, 0.1),
                    frameMaterial
                ));
                group.add(frame);
                // Door
                const door = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(1.0, 2.0, 0.05),
                    doorMaterial
                ));
                door.position.z = 0.025;
                group.add(door);
                // Handle
                const handle = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.02, 0.02, 0.1, 8),
                    materials.bronze
                ));
                handle.position.set(0.4, 0, 0.05);
                handle.rotation.z = Math.PI / 2;
                group.add(handle);
            }
            
            group.userData = { type: 'door', model: type, isGroup: true };
            return group;
        }

        function createAppliance(type) {
            const group = new THREE.Group();
            
            if (type === 'refrigerator') {
                const body = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.8, 1.8, 0.6),
                    materials.white
                ));
                group.add(body);
                
                const handle = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.05, 0.4, 0.1),
                    materials.metalDark
                ));
                handle.position.set(0.4, 0, 0.3);
                group.add(handle);

                // Add details
                const detail = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.6, 0.02, 0.02),
                    materials.metal
                ));
                detail.position.set(0, 0.5, 0.31);
                group.add(detail);
            } 
            else if (type === 'oven') {
                const body = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.8, 0.9, 0.6),
                    materials.stainless
                ));
                group.add(body);
                
                const door = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.7, 0.6, 0.05),
                    materials.glass
                ));
                door.position.set(0, -0.1, 0.3);
                group.add(door);
                
                const controls = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.3, 0.1, 0.05),
                    materials.metalDark
                ));
                controls.position.set(0, 0.4, 0.3);
                group.add(controls);

                // Burners
                const burner1 = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.08, 0.08, 0.02, 16),
                    materials.metalDark
                ));
                burner1.position.set(-0.2, 0.46, 0.1);
                group.add(burner1);

                const burner2 = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.08, 0.08, 0.02, 16),
                    materials.metalDark
                ));
                burner2.position.set(0.2, 0.46, 0.1);
                group.add(burner2);
            }
            else if (type === 'sink') {
                const counter = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(1.0, 0.9, 0.6),
                    materials.ceramic
                ));
                group.add(counter);
                
                const basin = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.3, 0.3, 0.2, 32),
                    materials.ceramic
                ));
                basin.position.set(0, 0.5, 0);
                group.add(basin);
                
                const faucet = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.02, 0.02, 0.2, 8),
                    materials.metal
                ));
                faucet.position.set(0.2, 0.6, 0);
                group.add(faucet);

                const handles = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.1, 0.05, 0.05),
                    materials.metal
                ));
                handles.position.set(0.1, 0.6, 0);
                group.add(handles);
            }
            else if (type === 'dishwasher') {
                const body = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.8, 0.9, 0.6),
                    materials.stainless
                ));
                group.add(body);
                
                const door = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.7, 0.8, 0.05),
                    materials.metal
                ));
                door.position.set(0, 0, 0.3);
                group.add(door);
                
                const handle = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.3, 0.02, 0.05),
                    materials.metalDark
                ));
                handle.position.set(0, 0, 0.325);
                group.add(handle);

                // Control panel
                const panel = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.2, 0.1, 0.02),
                    materials.plasticDark
                ));
                panel.position.set(0.2, 0.3, 0.31);
                group.add(panel);
            }
            
            group.userData = { type: 'appliance', model: type, isGroup: true };
            return group;
        }

        function createFurniture(type) {
            const group = new THREE.Group();
            
            if (type === 'sofa') {
                const seat = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(1.8, 0.4, 0.8),
                    materials.fabric
                ));
                seat.position.y = 0.2;
                group.add(seat);
                
                const back = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(1.8, 0.6, 0.1),
                    materials.fabric
                ));
                back.position.set(0, 0.5, -0.35);
                group.add(back);
                
                const armLeft = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.2, 0.6, 0.8),
                    materials.fabricLight
                ));
                armLeft.position.set(-0.8, 0.3, 0);
                group.add(armLeft);
                
                const armRight = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.2, 0.6, 0.8),
                    materials.fabricLight
                ));
                armRight.position.set(0.8, 0.3, 0);
                group.add(armRight);

                // Cushions
                const cushion1 = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.5, 0.1, 0.7),
                    materials.fabricLight
                ));
                cushion1.position.set(-0.4, 0.45, 0.1);
                group.add(cushion1);

                const cushion2 = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.5, 0.1, 0.7),
                    materials.fabricLight
                ));
                cushion2.position.set(0.4, 0.45, 0.1);
                group.add(cushion2);
            }
            else if (type === 'table') {
                const top = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(1.0, 0.05, 0.6),
                    materials.woodLight
                ));
                top.position.y = 0.4;
                group.add(top);
                
                const leg1 = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.05, 0.05, 0.4, 8),
                    materials.woodDark
                ));
                leg1.position.set(-0.4, 0.2, -0.2);
                group.add(leg1);
                
                const leg2 = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.05, 0.05, 0.4, 8),
                    materials.woodDark
                ));
                leg2.position.set(0.4, 0.2, -0.2);
                group.add(leg2);
                
                const leg3 = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.05, 0.05, 0.4, 8),
                    materials.woodDark
                ));
                leg3.position.set(-0.4, 0.2, 0.2);
                group.add(leg3);
                
                const leg4 = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.05, 0.05, 0.4, 8),
                    materials.woodDark
                ));
                leg4.position.set(0.4, 0.2, 0.2);
                group.add(leg4);
            }
            else if (type === 'chair') {
                const seat = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.5, 0.05, 0.5),
                    materials.woodLight
                ));
                seat.position.y = 0.4;
                group.add(seat);
                
                const back = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.5, 0.5, 0.05),
                    materials.woodLight
                ));
                back.position.set(0, 0.65, -0.225);
                group.add(back);
                
                const leg1 = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.03, 0.03, 0.4, 8),
                    materials.woodDark
                ));
                leg1.position.set(-0.2, 0.2, -0.2);
                group.add(leg1);
                
                const leg2 = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.03, 0.03, 0.4, 8),
                    materials.woodDark
                ));
                leg2.position.set(0.2, 0.2, -0.2);
                group.add(leg2);
                
                const leg3 = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.03, 0.03, 0.4, 8),
                    materials.woodDark
                ));
                leg3.position.set(-0.2, 0.2, 0.2);
                group.add(leg3);
                
                const leg4 = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.03, 0.03, 0.4, 8),
                    materials.woodDark
                ));
                leg4.position.set(0.2, 0.2, 0.2);
                group.add(leg4);

                // Cushion
                const cushion = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.45, 0.05, 0.45),
                    materials.fabric
                ));
                cushion.position.y = 0.425;
                group.add(cushion);
            }
            else if (type === 'cabinet') {
                const body = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(1.0, 1.2, 0.5),
                    materials.woodDark
                ));
                group.add(body);
                
                const door = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.45, 1.1, 0.05),
                    materials.woodMedium
                ));
                door.position.set(-0.225, 0, 0.225);
                group.add(door);
                
                const handle = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.02, 0.02, 0.1, 8),
                    materials.bronze
                ));
                handle.position.set(-0.4, 0, 0.25);
                handle.rotation.z = Math.PI / 2;
                group.add(handle);

                // Shelves
                const shelf1 = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.9, 0.02, 0.4),
                    materials.woodMedium
                ));
                shelf1.position.set(0, 0.3, 0.1);
                group.add(shelf1);

                const shelf2 = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.9, 0.02, 0.4),
                    materials.woodMedium
                ));
                shelf2.position.set(0, -0.3, 0.1);
                group.add(shelf2);
            }
            
            group.userData = { type: 'furniture', model: type, isGroup: true };
            return group;
        }

        function createDecoration(type) {
            const group = new THREE.Group();
            
            if (type === 'plant') {
                const pot = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.2, 0.15, 0.3, 16),
                    materials.ceramic
                ));
                pot.position.y = 0.15;
                group.add(pot);
                
                const stem = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.03, 0.03, 0.4, 8),
                    materials.plant
                ));
                stem.position.y = 0.45;
                group.add(stem);
                
                const foliage1 = enableShadows(new THREE.Mesh(
                    new THREE.SphereGeometry(0.15, 8, 8),
                    materials.plantLight
                ));
                foliage1.position.set(0.1, 0.65, 0.1);
                group.add(foliage1);

                const foliage2 = enableShadows(new THREE.Mesh(
                    new THREE.SphereGeometry(0.12, 8, 8),
                    materials.plantLight
                ));
                foliage2.position.set(-0.08, 0.7, -0.05);
                group.add(foliage2);

                const foliage3 = enableShadows(new THREE.Mesh(
                    new THREE.SphereGeometry(0.1, 8, 8),
                    materials.plantLight
                ));
                foliage3.position.set(0.05, 0.75, -0.08);
                group.add(foliage3);
            }
            else if (type === 'lamp') {
                const base = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.1, 0.1, 0.05, 16),
                    materials.bronze
                ));
                group.add(base);
                
                const stem = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.02, 0.02, 0.4, 8),
                    materials.bronze
                ));
                stem.position.y = 0.2;
                group.add(stem);
                
                const shade = enableShadows(new THREE.Mesh(
                    new THREE.ConeGeometry(0.15, 0.3, 16),
                    materials.fabricLight
                ));
                shade.position.y = 0.45;
                group.add(shade);
                
                // Add light
                const light = new THREE.PointLight(0xffaa55, state.isDaytime ? 0.3 : 0.8, 3);
                light.position.set(0, 0.45, 0);
                group.add(light);
            }
            else if (type === 'rug') {
                const rug = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(1.5, 0.02, 1.0),
                    materials.fabric
                ));
                rug.position.y = 0.01;
                group.add(rug);

                // Pattern
                const pattern = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(1.3, 0.03, 0.8),
                    materials.fabricLight
                ));
                pattern.position.y = 0.015;
                group.add(pattern);
            }
            else if (type === 'painting') {
                const frame = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.8, 0.6, 0.05),
                    materials.woodDark
                ));
                group.add(frame);
                
                const canvas = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.7, 0.5, 0.01),
                    materials.fabricLight
                ));
                canvas.position.z = 0.02;
                group.add(canvas);

                // Simple painting design
                const art1 = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.2, 0.1, 0.02),
                    materials.plant
                ));
                art1.position.set(-0.1, 0.1, 0.025);
                group.add(art1);

                const art2 = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(0.15, 0.08, 0.02),
                    materials.bronze
                ));
                art2.position.set(0.15, -0.1, 0.025);
                group.add(art2);
            }
            
            group.userData = { type: 'decoration', model: type, isGroup: true };
            return group;
        }

        function createObject(type, model) {
            let object;
            switch(type) {
                case 'door':
                    object = createDoor(model);
                    break;
                case 'appliance':
                    object = createAppliance(model);
                    break;
                case 'furniture':
                    object = createFurniture(model);
                    break;
                case 'decoration':
                    object = createDecoration(model);
                    break;
                default:
                    return null;
            }

            // Ensure the object is properly set up as a group
            if (!object.userData.isGroup) {
                const wrapperGroup = new THREE.Group();
                wrapperGroup.add(object);
                wrapperGroup.userData = {
                    type: type,
                    model: model,
                    isGroup: true,
                    originalObject: object
                };
                object = wrapperGroup;
            }

            // Calculate the object's bounding box to position it correctly on the platform (exclude drag mesh)
            // Temporarily store children, add drag mesh after positioning
            const bbox = new THREE.Box3().setFromObject(object);
            const size = bbox.getSize(new THREE.Vector3());
            const platformTopY = 0.025;
            const objectBaseY = bbox.min.y;

            // Position the object so it sits on the platform
            const yOffset = platformTopY - objectBaseY;
            object.position.set(
                (Math.random() - 0.5) * 2,
                yOffset,
                (Math.random() - 0.5) * 2
            );

            // Now add the drag mesh (after positioning)
            const dragMesh = new THREE.Mesh(
                new THREE.BoxGeometry(size.x || 1, size.y || 1, size.z || 1),
                new THREE.MeshBasicMaterial({ visible: false })
            );
            dragMesh.position.copy(object.position);
            dragMesh.userData.parentGroup = object;
            object.add(dragMesh);

            // Final Y alignment and bounds check (after drag mesh is added)
            constrainObjectToPlatform(object);

            // Add to scene and update drag controls
            scene.add(object);
            state.placedObjects.push(dragMesh);
            setupDragControls();

            // Apply dark mode effect if needed
            updatePlatformAndObjectColors();
            return object;
        }

        function updateObjectSelection() {
            // Remove selection from all objects
            state.placedObjects.forEach(dragMesh => {
                const parentGroup = dragMesh.userData.parentGroup;
                if (parentGroup) {
                    parentGroup.traverse(child => {
                        if (child.isMesh && child.material && child.material.emissive) {
                            child.material.emissive = new THREE.Color(0x000000);
                        }
                    });
                }
            });

            // Add selection highlight to selected object
            if (state.selectedObject) {
                state.selectedObject.traverse(child => {
                    if (child.isMesh && child.material && child.material.emissive) {
                        child.material.emissive = new THREE.Color(0x333333);
                    }
                });
                // Show arrows overlay at object's screen position
                setTimeout(() => {
                    const pos = state.selectedObject.position.clone();
                    const vector = pos.project(camera);
                    const container = renderer.domElement.getBoundingClientRect();
                    const screenX = ((vector.x + 1) / 2) * container.width + container.left;
                    const screenY = ((-vector.y + 1) / 2) * container.height + container.top;
                    showObjectArrows(screenX, screenY);
                }, 30);
            } else {
                hideObjectArrows();
            }
        }

        function deleteSelectedObject() {
            if (state.selectedObject) {
                // Remove the drag mesh from placed objects
                state.placedObjects = state.placedObjects.filter(dragMesh => 
                    dragMesh.userData.parentGroup !== state.selectedObject
                );
                // Remove the object from the scene
                scene.remove(state.selectedObject);
                state.selectedObject = null;
                setupDragControls();
                hideObjectArrows();
            }
        }

        function rotateSelectedObject() {
            if (state.selectedObject) {
                state.selectedObject.rotation.y += Math.PI / 4;
                // Sync drag mesh position/rotation with parent group
                syncDragMeshWithObject(state.selectedObject);
                updateObjectSelection();
            }
        }

        // Utility: Sync drag mesh with parent group
        function syncDragMeshWithObject(object) {
            // Find the drag mesh (invisible box) child
            object.traverse(child => {
                if (child.isMesh && child.userData && child.userData.parentGroup === object) {
                    child.position.copy(object.position);
                    child.rotation.copy(object.rotation);
                }
            });
        }

        function resetScene() {
            // Remove all objects from scene
            state.placedObjects.forEach(dragMesh => {
                if (dragMesh.userData && dragMesh.userData.parentGroup) {
                    scene.remove(dragMesh.userData.parentGroup);
                }
            });
            state.placedObjects = [];
            state.selectedObject = null;
            setupDragControls();
            hideObjectArrows();
            // Reset camera
            controls.reset();
        }

        // =========================================
        // SCENE MANAGEMENT
        // =========================================
        function createFloor(texture) {
            // Lighting-based color for platform
            let color = state.isDaytime ? 0xffffff : 0x23253a;
            let roughness = state.isDaytime ? 0.3 : 0.55;
            let metalness = state.isDaytime ? 0.1 : 0.04;
            let envMapIntensity = state.isDaytime ? 1 : 0.4;
            
            const floorMat = new THREE.MeshPhysicalMaterial({ 
                map: texture, 
                roughness: roughness, 
                metalness: metalness, 
                envMapIntensity: envMapIntensity,
                color: color
            });
            
            // Main tile platform
            const floor = enableShadows(new THREE.Mesh(
                new THREE.BoxGeometry(4, 0.05, 4),
                floorMat
            ));
            floor.position.y = 0.025;
            
            // Border/bevel
            const borderMat = new THREE.MeshPhysicalMaterial({
                color: 0x888888,
                roughness: 0.6,
                metalness: 0.2
            });
            const border = enableShadows(new THREE.Mesh(
                new THREE.BoxGeometry(4.1, 0.03, 4.1),
                borderMat
            ));
            border.position.y = 0.01;
            
            const platformGroup = new THREE.Group();
            platformGroup.add(border);
            platformGroup.add(floor);
            return platformGroup;
        }

        function update3DScene() {
            // Clear old floor
            if (floorObject) {
                scene.remove(floorObject);
                floorObject.traverse(child => {
                    if (child.isMesh) {
                        child.geometry.dispose();
                        if (child.material.isMaterial) {
                            child.material.dispose();
                        } else if (Array.isArray(child.material)) {
                            child.material.forEach(m => m.dispose());
                        }
                    }
                });
            }
            
            if (!state.currentTexture) return;

            // Apply tiling
            state.currentTexture.repeat.set(state.currentGridSize, state.currentGridSize);
            state.currentTexture.wrapS = THREE.RepeatWrapping;
            state.currentTexture.wrapT = THREE.RepeatWrapping;
            
            // Create floor
            floorObject = createFloor(state.currentTexture);
            scene.add(floorObject);
            // Ensure platform color matches current mode
            updatePlatformAndObjectColors();
        }

        // =========================================
        // TILE TEXTURE LOADING
        // =========================================
        function loadTileTexture(imageUrl) {
            textureLoader.load(imageUrl, (texture) => {
                texture.colorSpace = THREE.SRGBColorSpace;
                texture.wrapS = THREE.RepeatWrapping;
                texture.wrapT = THREE.RepeatWrapping;
                state.currentTexture = texture;

                update3DScene();
                document.getElementById('canvas-overlay').classList.add('opacity-0');
                
                // Enable object library and tools
                enableObjectLibrary();
            });
        }

        // =========================================
        // UI MANAGEMENT
        // =========================================
        function enableObjectLibrary() {
            state.tileSelected = true;
            
            // Enable all object items
            document.querySelectorAll('.object-item').forEach(item => {
                item.classList.remove('disabled');
            });
            
            // Enable reset button
            document.getElementById('btn-reset-scene').disabled = false;
            
            // Enable tiling controls
            document.getElementById('tiling-controls').classList.remove('opacity-50', 'pointer-events-none');
        }

        function disableObjectLibrary() {
            state.tileSelected = false;
            
            // Disable all object items
            document.querySelectorAll('.object-item').forEach(item => {
                item.classList.add('disabled');
            });
            
            // Disable reset button
            document.getElementById('btn-reset-scene').disabled = true;
            
            // Disable tiling controls
            document.getElementById('tiling-controls').classList.add('opacity-50', 'pointer-events-none');
        }

        // =========================================
        // UI EVENT HANDLERS
        // =========================================
        function setupEventListeners() {
            createObjectArrowsUI();
            
            // Tiling controls
            document.querySelectorAll('.tiling-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    if (!state.tileSelected) return;
                    
                    document.querySelectorAll('.tiling-btn').forEach(b => b.classList.remove('active'));
                    e.currentTarget.classList.add('active');
                    state.currentGridSize = parseInt(e.currentTarget.dataset.size);
                    if (state.currentTexture) {
                        state.currentTexture.repeat.set(state.currentGridSize, state.currentGridSize);
                    }
                });
            });

            // Object selection
            document.querySelectorAll('.object-item').forEach(item => {
                item.addEventListener('click', (e) => {
                    if (e.currentTarget.classList.contains('disabled')) {
                        alert('Please select a tile first to add objects');
                        return;
                    }
                    
                    const type = e.currentTarget.dataset.type;
                    const model = e.currentTarget.dataset.model;
                    
                    if (state.currentTexture) {
                        createObject(type, model);
                    }
                });
            });

            // Light toggle
            const lightToggle = document.getElementById('light-toggle');
            lightToggle.addEventListener('click', toggleLighting);
            // Keyboard accessibility
            lightToggle.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggleLighting();
                }
            });
            // Set initial state
            if (state.isDaytime) {
                lightToggle.classList.remove('active');
                lightToggle.setAttribute('aria-pressed', 'true');
                lightToggle.title = 'Switch to night mode';
            } else {
                lightToggle.classList.add('active');
                lightToggle.setAttribute('aria-pressed', 'false');
                lightToggle.title = 'Switch to day mode';
            }

            // Reset scene
            document.getElementById('btn-reset-scene').addEventListener('click', resetScene);

            // Search and filter
            document.getElementById('product-search').addEventListener('input', filterAndRenderProducts);
            
            document.getElementById('clear-filters').addEventListener('click', () => {
                document.querySelectorAll('.filter-chip').forEach(chip => {
                    chip.classList.remove('active');
                });
                document.querySelector('.filter-chip[data-filter="all"]').classList.add('active');
                state.activeFilters.clear();
                document.getElementById('product-search').value = '';
                filterAndRenderProducts();
            });

            // Object selection via click
            renderer.domElement.addEventListener('click', (event) => {
                if (state.isDragging) return;
                
                const mouse = new THREE.Vector2();
                const rect = renderer.domElement.getBoundingClientRect();
                mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
                mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
                
                const raycaster = new THREE.Raycaster();
                raycaster.setFromCamera(mouse, camera);
                
                // Intersect with all placed objects (drag meshes)
                const intersects = raycaster.intersectObjects(state.placedObjects, true);
                
                if (intersects.length > 0) {
                    let dragMesh = intersects[0].object;
                    let obj = dragMesh.userData.parentGroup || dragMesh;
                    state.selectedObject = obj;
                    updateObjectSelection();
                } else {
                    state.selectedObject = null;
                    updateObjectSelection();
                }
            });
        }

        // =========================================
        // PRODUCT MANAGEMENT
        // =========================================
        async function fetchTileCategories() {
            try {
                const res = await fetch('../logged_user/processes/get_all_tile_categories.php');
                const categories = await res.json();
                const filterChips = document.getElementById('filter-chips');
                
                const allChip = document.createElement('div');
                allChip.className = 'filter-chip active';
                allChip.innerHTML = '<i class="fas fa-layer-group"></i> All Designs';
                allChip.dataset.filter = 'all';
                allChip.addEventListener('click', toggleFilter);
                filterChips.appendChild(allChip);
                
                categories.forEach(cat => {
                    const chip = document.createElement('div');
                    chip.className = 'filter-chip';
                    chip.innerHTML = `<i class="fas fa-shapes"></i> ${cat.design_name}`;
                    chip.dataset.filter = cat.design_name;
                    chip.addEventListener('click', toggleFilter);
                    filterChips.appendChild(chip);
                });
            } catch (e) { 
                console.error('Failed to fetch categories:', e);
            }
        }

        function toggleFilter(e) {
            const filter = e.currentTarget.dataset.filter;
            if (filter === 'all') {
                document.querySelectorAll('.filter-chip').forEach(chip => {
                    chip.classList.remove('active');
                });
                e.currentTarget.classList.add('active');
                state.activeFilters.clear();
            } else {
                document.querySelector('.filter-chip[data-filter="all"]').classList.remove('active');
                e.currentTarget.classList.toggle('active');
                if (e.currentTarget.classList.contains('active')) {
                    state.activeFilters.add(filter);
                } else {
                    state.activeFilters.delete(filter);
                }
                if (state.activeFilters.size === 0) {
                    document.querySelector('.filter-chip[data-filter="all"]').classList.add('active');
                }
            }
            filterAndRenderProducts();
        }

        async function fetchProducts() {
            try {
                const res = await fetch('../logged_user/processes/get_premium_tiles.php');
                state.products = await res.json();
                filterAndRenderProducts();
            } catch (e) {
                document.getElementById('product-grid').innerHTML = '<p class="col-span-2 text-center p-4 text-red-400">Connection failed.</p>';
            }
        }

        function filterAndRenderProducts() {
            const search = document.getElementById('product-search').value.trim().toLowerCase();
            let filtered = state.products;
            
            if (search) {
                filtered = filtered.filter(p => p.product_name.toLowerCase().includes(search));
            }
            
            if (state.activeFilters.size > 0) {
                filtered = filtered.filter(p => 
                    Array.isArray(p.designs) && 
                    p.designs.some(design => state.activeFilters.has(design))
                );
            }
            
            renderProducts(filtered);
        }

        function renderProducts(list) {
            const grid = document.getElementById('product-grid');
            const productCount = document.getElementById('product-count');
            
            productCount.textContent = `${list.length} product${list.length !== 1 ? 's' : ''}`;
            
            grid.innerHTML = '';
            if (!list.length) {
                grid.innerHTML = `
                    <div class="text-center text-gray-400 py-8">
                        <i class="fas fa-search text-3xl mb-3 opacity-30"></i>
                        <p class="font-medium">No tiles found matching your criteria</p>
                        <p class="text-sm mt-1">Try adjusting your search or filters</p>
                    </div>
                `;
                return;
            }
            
            list.forEach(product => {
                const card = document.createElement('div');
                card.className = 'product-card bg-white rounded-2xl shadow-soft_lg border border-gray-100 overflow-hidden cursor-pointer';
                card.innerHTML = `
                    <div class="relative overflow-hidden">
                        <img src="${product.product_image}" class="w-full h-32 object-cover transition-transform duration-300 hover:scale-105">
                        <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-lg text-xs font-bold text-primary">
                            ₱${parseFloat(product.product_price).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}
                        </div>
                    </div>
                    <div class="p-3">
                        <h4 class="font-bold text-sm truncate text-primary">${product.product_name}</h4>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-gray-500">${Array.isArray(product.designs) ? product.designs[0] : 'Premium Tile'}</span>
                            <button class="text-xs text-secondary hover:text-primary font-medium">
                                Select <i class="fas fa-chevron-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                `;
                card.onclick = () => selectProduct(product);
                grid.appendChild(card);
            });
        }

        function selectProduct(product) {
            state.currentTile = product;
            document.getElementById('selected-tile-name').innerHTML = `<span class="font-semibold text-primary">${product.product_name}</span>`;

            // Load texture and update scene
            loadTileTexture(product.product_image);

            // Show details button
            const detailsBtn = document.getElementById('btn-view-details');
            detailsBtn.href = `product_detail.php?id=${product.product_id}`;
            detailsBtn.classList.remove('hidden');
        }

        // =========================================
        // INITIALIZATION
        // =========================================
        function initializeApp() {
            init3D();
            setupEventListeners();
            fetchTileCategories();
            fetchProducts();
            // Initially disable object library
            disableObjectLibrary();
        }

        // Start the application
        initializeApp();
    </script>
</body>
</html>