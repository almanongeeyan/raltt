<?php
// 3D Visualizer Page for RALTT
include '../includes/headeruser.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Visualizer - Rich Anne Lea Tiles Trading</title>
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
        
        /* Environment Button Styles */
        .env-btn {
            border-width: 2px;
            border-color: #e5e7eb;
            background: #fff;
            color: #b0b0b0;
            transition: all 0.2s;
            font-weight: 600;
            box-shadow: none;
        }
        .env-btn:hover {
            border-color: #cf8756;
            color: #7d310a;
            background: #f9f5f2;
            box-shadow: 0 2px 8px rgba(207,135,86,0.10);
            z-index: 1;
        }
        .env-btn.active, .env-btn:focus {
            background: #7d310a;
            color: #fff;
            border-color: #cf8756;
            box-shadow: 0 4px 16px rgba(125,49,10,0.13);
        }
        .env-btn.active:hover, .env-btn:focus:hover {
            background: #5a2307;
            color: #fff;
            border-color: #cf8756;
        }
        
        /* Filter Styles */
        .filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s;
        }
        .filter-chip:hover {
            border-color: #cf8756;
            color: #7d310a;
        }
        .filter-chip.active {
            background: #7d310a;
            border-color: #7d310a;
            color: white;
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
        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #7d310a, #cf8756);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        .product-card:hover::before {
            transform: scaleX(1);
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
        
        /* AI Result Image */
        .ai-result-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 1rem;
        }
    </style>
</head>
<body class="text-gray-800 min-h-screen pt-24 pb-12 px-4 md:px-8">
    <div class="header-spacer"></div>

    <div class="max-w-[1400px] mx-auto">
        <div class="hero-immersive animate-fade-in text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-black text-primary mb-3 tracking-tight drop-shadow-lg">
                RALTT Tile Studio
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Visualize our premium tiles in realistic 3D patterns or AI-generated spaces.<br>
                <span class="text-secondary font-bold">Experience your dream floor before you buy.</span>
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white rounded-3xl shadow-soft_xl overflow-hidden border border-gray-100 animate-fade-in-up">
                    <div class="p-6">
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-primary flex items-center gap-2">
                                <i class="fas fa-wand-magic-sparkles text-secondary"></i>
                                AI Scene Generator
                            </h3>
                            <p class="text-sm text-gray-500">Dream up your perfect room with AI.</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-xs font-medium text-gray-500 mb-2">SELECT ENVIRONMENT</p>
                            <div class="grid grid-cols-6 gap-2">
                                <button class="env-btn active group h-16 p-2 rounded-xl border-2 border-secondary bg-primary text-white flex flex-col items-center justify-center gap-1 transition-all" data-env="living-room">
                                    <i class="fas fa-couch text-lg"></i>
                                    <span class="text-xs font-bold uppercase">Living</span>
                                </button>
                                <button class="env-btn group h-16 p-2 rounded-xl border-2 border-gray-100 hover:border-secondary text-gray-400 hover:text-primary flex flex-col items-center justify-center gap-1 transition-all" data-env="bathroom">
                                    <i class="fas fa-bath text-lg"></i>
                                    <span class="text-xs font-bold uppercase">Bath</span>
                                </button>
                                <button class="env-btn group h-16 p-2 rounded-xl border-2 border-gray-100 hover:border-secondary text-gray-400 hover:text-primary flex flex-col items-center justify-center gap-1 transition-all" data-env="kitchen">
                                    <i class="fas fa-utensils text-lg"></i>
                                    <span class="text-xs font-bold uppercase">Kitchen</span>
                                </button>
                                <button class="env-btn group h-16 p-2 rounded-xl border-2 border-gray-100 hover:border-secondary text-gray-400 hover:text-primary flex flex-col items-center justify-center gap-1 transition-all" data-env="bedroom">
                                    <i class="fas fa-bed text-lg"></i>
                                    <span class="text-xs font-bold uppercase">Bed</span>
                                </button>
                                <button class="env-btn group h-16 p-2 rounded-xl border-2 border-gray-100 hover:border-secondary text-gray-400 hover:text-primary flex flex-col items-center justify-center gap-1 transition-all" data-env="patio">
                                    <i class="fas fa-sun text-lg"></i>
                                    <span class="text-xs font-bold uppercase">Patio</span>
                                </button>
                                <button class="env-btn group h-16 p-2 rounded-xl border-2 border-gray-100 hover:border-secondary text-gray-400 hover:text-primary flex flex-col items-center justify-center gap-1 transition-all" data-env="entryway">
                                    <i class="fas fa-door-open text-lg"></i>
                                    <span class="text-xs font-bold uppercase">Entry</span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="relative rounded-2xl overflow-hidden bg-gray-100 border border-gray-200 h-[400px] flex items-center justify-center">
                            <div id="ai-placeholder" class="text-center p-8 text-gray-400">
                                <i class="fas fa-image text-5xl mb-4 opacity-20"></i>
                                <p class="font-medium text-sm">Select tile & environment, then generate.</p>
                            </div>
                            <div id="ai-loading" class="hidden absolute inset-0 bg-white/80 backdrop-blur z-20 flex flex-col items-center justify-center">
                                <div class="w-12 h-12 border-4 border-orange-100 border-t-primary rounded-full animate-spin mb-4"></div>
                                <p class="text-primary font-bold animate-pulse">Dreaming up your space...</p>
                            </div>
                            <img id="ai-result-image" src="" alt="AI Result" class="hidden ai-result-image">
                            <div id="ai-reference-badge" class="hidden absolute top-4 left-4 bg-white/90 backdrop-blur-md p-1.5 pr-3 rounded-lg shadow-sm z-30 flex items-center gap-2">
                                <img src="" id="ref-tile-img" class="w-8 h-8 rounded object-cover border border-gray-200">
                                <div>
                                    <p class="text-[10px] font-bold text-primary uppercase">Reference</p>
                                    <p class="text-[10px] text-gray-600 leading-tight truncate max-w-[100px]" id="ref-tile-name">-</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 mt-4">
                            <button id="btn-generate-ai" class="py-4 bg-primary hover:bg-primaryDark text-white rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2" disabled>
                                <i class="fas fa-wand-magic-sparkles"></i> Generate with AI
                            </button>
                            <button id="btn-buy-now" class="py-4 bg-secondary hover:bg-primary text-white rounded-xl font-bold text-lg shadow-md hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2" disabled>
                                <i class="fas fa-shopping-cart"></i> Buy Now
                            </button>
                        </div>
                        
                        <p class="text-center text-xs text-gray-400 mt-3">Powered by Stability AI. Colors and Patterns can be inaccurate.</p>
                    </div>
                </div>
                
                <div class="bg-white rounded-3xl shadow-soft_xl overflow-hidden border border-gray-100 relative animate-fade-in-up">
                    <div class="p-4 px-6 border-b border-gray-50 bg-white z-10 relative">
                        <div class="flex flex-wrap justify-between items-center gap-3">
                            <div>
                                <h2 class="text-xl font-bold text-primary flex items-center gap-2">
                                    <i class="fas fa-cube text-secondary"></i> 3D Pattern Inspector
                                </h2>
                                <p class="text-xs text-gray-500 mt-0.5" id="selected-tile-name">Select a tile to begin</p>
                            </div>
                            <a id="btn-view-details" href="#" class="hidden items-center gap-1 px-3 py-1.5 bg-offwhite text-primary text-xs font-bold rounded-full hover:bg-primary hover:text-white transition-colors">
                                Details <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    
                        <div class="mt-4">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Select Style</span>
                            <div class="flex gap-2 overflow-x-auto custom-scrollbar pb-2 pt-1.5">
                                <button class="control-btn style-btn" data-style="none" disabled>Empty</button>
                                <button class="control-btn style-btn" data-style="american" disabled><i class="fas fa-tv mr-1"></i> American</button>
                                <button class="control-btn style-btn" data-style="japanese" disabled><i class="fas fa-torii-gate mr-1"></i> Japanese</button>
                                <button class="control-btn style-btn" data-style="chinese" disabled><i class="fas fa-vihara mr-1"></i> Chinese</button>
                                <button class="control-btn style-btn" data-style="zen" disabled><i class="fas fa-spa mr-1"></i> Zen</button>
                                <button class="control-btn style-btn" data-style="southern" disabled><i class="fas fa-sun mr-1"></i> Southern</button>
                                <button class="control-btn style-btn" data-style="european" disabled><i class="fas fa-building-columns mr-1"></i> European</button>
                                <button class="control-btn style-btn" data-style="german" disabled><i class="fas fa-house-chimney-window mr-1"></i> German</button>
                            </div>
                        </div>

                        <div class="mt-3">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Select Room Type</span>
                            <div class="flex gap-2 overflow-x-auto custom-scrollbar pb-2 pt-1.5">
                                <button class="control-btn room-btn active" data-room="indoor" disabled><i class="fas fa-couch mr-1"></i> Indoor</button>
                                <button class="control-btn room-btn" data-room="bathroom" disabled><i class="fas fa-bath mr-1"></i> Bathroom</button>
                                <button class="control-btn room-btn" data-room="kitchen" disabled><i class="fas fa-kitchen-set mr-1"></i> Kitchen</button>
                                <button class="control-btn room-btn" data-room="corridor" disabled><i class="fas fa-person-walking-arrow-right mr-1"></i> Corridor</button>
                                <button class="control-btn room-btn" data-room="outdoor" disabled><i class="fas fa-sun mr-1"></i> Outdoor</button>
                                <button class="control-btn room-btn" data-room="pool" disabled><i class="fas fa-water-ladder mr-1"></i> Pool</button>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div id="tiling-controls" class="absolute top-4 right-4 flex flex-col gap-2 z-10 opacity-50 pointer-events-none transition-opacity">
                            <div class="flex gap-1 p-1 bg-white/60 backdrop-blur-md rounded-lg shadow-sm">
                                <span class="text-[10px] font-bold text-primary uppercase px-2 flex items-center">Grid:</span>
                                <button class="control-btn tiling-btn active" data-size="1">1x1</button>
                                <button class="control-btn tiling-btn" data-size="2">2x2</button>
                                <button class="control-btn tiling-btn" data-size="3">3x3</button>
                                <button class="control-btn tiling-btn" data-size="4">4x4</button>
                            </div>
                        </div>
                        <div id="visualizer-canvas-container" class="w-full h-[500px] relative">
                            <div id="canvas-overlay" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 z-20 pointer-events-none transition-opacity duration-500">
                                <div class="bg-white/80 backdrop-blur px-6 py-4 rounded-2xl shadow-sm flex flex-col items-center">
                                    <i class="fas fa-hand-pointer text-3xl mb-3 text-secondary animate-bounce"></i>
                                    <p class="font-semibold text-sm">Select a tile from the list</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="py-3 bg-gray-50 text-center text-[11px] font-bold uppercase tracking-wider text-gray-400 flex justify-center gap-8 border-t border-gray-100">
                        <span><i class="fas fa-mouse mr-1"></i> Drag to Rotate</span>
                        <span><i class="fas fa-search-plus mr-1"></i> Scroll to Zoom</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 h-full">
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
                        <div id="product-grid" class="grid grid-cols-2 gap-3">
                            <div class="col-span-2 space-y-3">
                                <div class="flex gap-3 h-32">
                                    <div class="w-1/2 bg-gray-100 rounded-xl shimmer"></div>
                                    <div class="w-1/2 bg-gray-100 rounded-xl shimmer"></div>
                                </div>
                                <div class="flex gap-3 h-32">
                                    <div class="w-1/2 bg-gray-100 rounded-xl shimmer"></div>
                                    <div class="w-1/2 bg-gray-100 rounded-xl shimmer"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>

    <input type="hidden" id="api-key" value="sk-ccijXOi5O4SbEwyVHAakyvR7BOZHs8Hz0TeMaBc5tv7qbLYs">

    <script async src="https://unpkg.com/es-module-shims@1.8.0/dist/es-module-shims.js"></script>
    <script type="importmap">
    {
        "imports": {
            "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
            "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
        }
    }
    </script>

    <script type="module">
        import * as THREE from 'three';
        import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
        import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';

        // =========================================
        // STATE MANAGEMENT
        // =========================================
        const state = {
            currentTile: null,
            currentTexture: null,
            currentEnv: 'living-room',
            currentGridSize: 1,
            currentStyle: 'none',
            currentRoom: 'indoor',
            products: [],
            isAiGenerating: false,
            activeFilters: new Set()
        };

        // =========================================
        // 3D ENGINE SETUP
        // =========================================
        let scene, camera, renderer, controls, styleGroup, floorObject;
        const textureLoader = new THREE.TextureLoader();

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

            styleGroup = new THREE.Group();
            scene.add(styleGroup);

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

            // Enhanced Lighting
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.4);
            scene.add(ambientLight);
            const mainLight = new THREE.DirectionalLight(0xfff0dd, 2);
            mainLight.position.set(5, 10, 5);
            mainLight.castShadow = true;
            mainLight.shadow.mapSize.set(2048, 2048);
            mainLight.shadow.camera.near = 0.5;
            mainLight.shadow.camera.far = 30;
            mainLight.shadow.bias = -0.0005;
            scene.add(mainLight);
            const fillLight = new THREE.DirectionalLight(0xddeeff, 0.5);
            fillLight.position.set(-5, 3, -5);
            scene.add(fillLight);

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

        // =========================================
        // PROCEDURAL ASSET LIBRARY
        // =========================================
        const materials = {
            woodDark: new THREE.MeshStandardMaterial({ color: 0x3d2314, roughness: 0.7 }),
            woodLight: new THREE.MeshStandardMaterial({ color: 0xd4bc92, roughness: 0.6 }),
            woodRed: new THREE.MeshStandardMaterial({ color: 0x8a1a1a, roughness: 0.6 }),
            woodBlack: new THREE.MeshStandardMaterial({ color: 0x1a0f0a, roughness: 0.7 }),
            fabric: new THREE.MeshStandardMaterial({ color: 0xcccccc, roughness: 0.9 }),
            fabricRed: new THREE.MeshStandardMaterial({ color: 0x8a1a1a, roughness: 0.9 }),
            fabricGold: new THREE.MeshStandardMaterial({ color: 0xd4af37, roughness: 0.8 }),
            metalBlack: new THREE.MeshStandardMaterial({ color: 0x222222, metalness: 0.8, roughness: 0.2 }),
            metalChrome: new THREE.MeshStandardMaterial({ color: 0xcccccc, metalness: 1.0, roughness: 0.1 }),
            metalGold: new THREE.MeshStandardMaterial({ color: 0xd4af37, metalness: 0.9, roughness: 0.3 }),
            ceramicWhite: new THREE.MeshPhysicalMaterial({ color: 0xffffff, roughness: 0.1, metalness: 0.1, reflectivity: 0.9 }),
            ceramicBlack: new THREE.MeshPhysicalMaterial({ color: 0x111111, roughness: 0.1, metalness: 0.1, reflectivity: 0.9 }),
            plantGreen: new THREE.MeshStandardMaterial({ color: 0x2a4f2a, roughness: 0.8 }),
            plantBamboo: new THREE.MeshStandardMaterial({ color: 0x4a7c59, roughness: 0.7 }),
            humanAbstract: new THREE.MeshStandardMaterial({ color: 0xffffff, roughness: 0.5, metalness: 0.1, transparent: true, opacity: 0.8 }),
            stoneMarble: new THREE.MeshStandardMaterial({ color: 0xfdfdfd, roughness: 0.1, metalness: 0.2 }),
            stoneDark: new THREE.MeshStandardMaterial({ color: 0x333333, roughness: 0.3, metalness: 0.1 }),
            water: new THREE.MeshPhysicalMaterial({ color: 0x55aaff, transmission: 1, roughness: 0.1, metalness: 0.2, transparent: true, opacity: 0.7 }),
            leatherBlack: new THREE.MeshStandardMaterial({ color: 0x111111, roughness: 0.4 }),
            wallBeige: new THREE.MeshStandardMaterial({ color: 0xf5f5dc, roughness: 0.8 }),
            wallWhite: new THREE.MeshStandardMaterial({ color: 0xf8f8f8, roughness: 0.7 }),
            wallPaper: new THREE.MeshStandardMaterial({ color: 0xf8f0e0, roughness: 0.8 }),
            glass: new THREE.MeshPhysicalMaterial({ 
                color: 0xffffff, 
                transmission: 0.9, 
                roughness: 0.05, 
                metalness: 0,
                transparent: true,
                opacity: 0.7
            }),
            paper: new THREE.MeshStandardMaterial({ color: 0xf5f5dc, roughness: 0.9 })
        };

        function enableShadows(mesh) {
            mesh.castShadow = true;
            mesh.receiveShadow = true;
            return mesh;
        }

        // Floor/Pool Creators
        function createFloor(texture) {
            const floorMat = new THREE.MeshPhysicalMaterial({ 
                map: texture, 
                roughness: 0.3, 
                metalness: 0.1, 
                envMapIntensity: 1 
            });
            const floor = enableShadows(new THREE.Mesh(
                new THREE.BoxGeometry(4, 0.05, 4),
                floorMat
            ));
            floor.position.y = -0.025;
            return floor;
        }

        function createPoolBasin(texture) {
            const poolGroup = new THREE.Group();
            const tileMat = new THREE.MeshPhysicalMaterial({ 
                map: texture, 
                roughness: 0.2, 
                metalness: 0.0, 
                envMapIntensity: 0.5 
            });
            const W = 3.8, D = 3.8, H = 1.5;
            
            // Floor
            const floor = enableShadows(new THREE.Mesh(new THREE.PlaneGeometry(W, D), tileMat));
            floor.rotation.x = -Math.PI / 2;
            floor.position.y = -H / 2;
            poolGroup.add(floor);
            
            // Walls
            const walls = [
                { position: [0, 0, -D/2], rotation: [0, 0, 0] },
                { position: [0, 0, D/2], rotation: [0, Math.PI, 0] },
                { position: [-W/2, 0, 0], rotation: [0, Math.PI/2, 0] },
                { position: [W/2, 0, 0], rotation: [0, -Math.PI/2, 0] }
            ];
            
            walls.forEach(wall => {
                const wallMesh = enableShadows(new THREE.Mesh(new THREE.PlaneGeometry(W, H), tileMat));
                wallMesh.position.set(...wall.position);
                wallMesh.rotation.y = wall.rotation[1];
                poolGroup.add(wallMesh);
            });
            
            // Water
            const water = enableShadows(new THREE.Mesh(new THREE.PlaneGeometry(W-0.01, D-0.01), materials.water));
            water.rotation.x = -Math.PI/2;
            water.position.y = (H/2) - 0.2;
            poolGroup.add(water);

            // Pool ladder
            const ladder = new THREE.Group();
            [-0.2, 0.2].forEach(x => {
                const rail = new THREE.Mesh(new THREE.CylinderGeometry(0.03, 0.03, H + 0.5, 8), materials.metalChrome);
                rail.position.set(x, 0.25, 0);
                ladder.add(rail);
            });
            
            for(let y = 0; y < 4; y++) {
                const step = new THREE.Mesh(new THREE.CylinderGeometry(0.02, 0.02, 0.44, 8), materials.metalChrome);
                step.rotation.z = Math.PI / 2;
                step.position.y = -H/2 + 0.3 + y * 0.3;
                ladder.add(step);
            }
            
            ladder.position.set(W/2 - 0.5, H/2 - 0.25, 0);
            poolGroup.add(ladder);

            poolGroup.position.y = -0.05;
            controls.target.set(0, -0.5, 0);
            camera.position.set(0, 2, 3.5);
            return poolGroup;
        }

        // Furniture and Objects
        function createAbstractPerson(x, z, pose = 'standing') {
            const group = new THREE.Group();
            const height = 1.75;
            
            const bodyGeo = pose === 'standing' 
                ? new THREE.CylinderGeometry(0.2, 0.15, height * 0.6, 16) 
                : new THREE.CylinderGeometry(0.25, 0.25, height * 0.4, 16);
                
            const body = enableShadows(new THREE.Mesh(bodyGeo, materials.humanAbstract));
            body.position.y = pose === 'standing' ? height * 0.4 : height * 0.2 + 0.2;
            group.add(body);
            
            const head = enableShadows(new THREE.Mesh(new THREE.SphereGeometry(0.15, 16, 16), materials.humanAbstract));
            head.position.y = pose === 'standing' ? height - 0.15 : height * 0.6 + 0.2;
            group.add(head);
            
            if (pose === 'sitting') {
                const leg = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.2, 0.4, 0.4), materials.humanAbstract));
                leg.position.set(0, 0.2, 0.2);
                group.add(leg);
            }
            
            group.position.set(x, 0, z);
            if(pose === 'sitting') group.rotation.y = -Math.PI / 2;
            return group;
        }

        function createModernTV(x, y, z, rotationY = 0) {
            const group = new THREE.Group();
            
            const screen = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(1.8, 1.0, 0.04), materials.metalBlack));
            screen.position.y = 1.1;
            group.add(screen);
            
            const glow = new THREE.Mesh(
                new THREE.PlaneGeometry(1.75, 0.95), 
                new THREE.MeshStandardMaterial({ color: 0x223355, emissive: 0x112244 })
            );
            glow.position.set(0, 1.1, 0.025);
            group.add(glow);
            
            const stand = new THREE.Mesh(new THREE.BoxGeometry(0.6, 0.6, 0.05), materials.metalBlack);
            stand.position.set(0, 0.8, -0.05);
            group.add(stand);
            
            const base = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(1.2, 0.05, 0.4), materials.metalBlack));
            base.position.y = 0.5;
            group.add(base);
            
            const cabinet = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(2.4, 0.5, 0.5), materials.woodDark));
            cabinet.position.y = 0.25;
            group.add(cabinet);
            
            group.position.set(x, y, z);
            group.rotation.y = rotationY;
            return group;
        }

        function createFloorLamp(x, z) {
            const group = new THREE.Group();
            
            const base = enableShadows(new THREE.Mesh(new THREE.CylinderGeometry(0.15, 0.15, 0.05, 16), materials.metalBlack));
            group.add(base);
            
            const pole = enableShadows(new THREE.Mesh(new THREE.CylinderGeometry(0.02, 0.02, 1.6, 8), materials.metalBlack));
            pole.position.y = 0.8;
            group.add(pole);
            
            const shade = enableShadows(new THREE.Mesh(
                new THREE.CylinderGeometry(0.25, 0.35, 0.4, 32, 1, true), 
                new THREE.MeshStandardMaterial({ 
                    color: 0xfffee0, 
                    side: THREE.DoubleSide, 
                    transparent: true, 
                    opacity: 0.9 
                })
            ));
            shade.position.y = 1.6;
            group.add(shade);
            
            const bulb = new THREE.PointLight(0xffaa55, 0.5, 3);
            bulb.position.set(0, 1.6, 0);
            group.add(bulb);
            
            group.position.set(x, 0, z);
            return group;
        }

        function createPottedPlant(x, y, z, type = 'round') {
            const group = new THREE.Group();
            
            const pot = enableShadows(new THREE.Mesh(new THREE.CylinderGeometry(0.25, 0.2, 0.4, 16), materials.ceramicWhite));
            pot.position.y = 0.2;
            group.add(pot);
            
            const soil = new THREE.Mesh(
                new THREE.CircleGeometry(0.23, 16), 
                new THREE.MeshStandardMaterial({ color: 0x221100 })
            );
            soil.rotation.x = -Math.PI/2; 
            soil.position.y = 0.38;
            group.add(soil);
            
            if (type === 'round') {
                const foliage = enableShadows(new THREE.Mesh(new THREE.DodecahedronGeometry(0.35, 1), materials.plantGreen));
                foliage.position.y = 0.6; 
                group.add(foliage);
            } else if (type === 'tall') {
                for(let i = 0; i < 5; i++) {
                    const leaf = enableShadows(new THREE.Mesh(
                        new THREE.ConeGeometry(0.05, 0.8 + Math.random() * 0.3, 8), 
                        materials.plantGreen
                    ));
                    leaf.position.set(
                        Math.random() * 0.1 - 0.05, 
                        0.4, 
                        Math.random() * 0.1 - 0.05
                    );
                    leaf.rotation.set(Math.random() * 0.2, Math.random() * Math.PI, Math.random() * 0.2);
                    group.add(leaf);
                }
            } else if (type === 'bamboo') {
                for(let i = 0; i < 7; i++) {
                    const bamboo = enableShadows(new THREE.Mesh(
                        new THREE.CylinderGeometry(0.03, 0.03, 1.2, 8), 
                        materials.plantBamboo
                    ));
                    bamboo.position.set(
                        Math.random() * 0.2 - 0.1, 
                        0.6, 
                        Math.random() * 0.2 - 0.1
                    );
                    group.add(bamboo);
                }
            }
            
            group.position.set(x, y, z);
            return group;
        }

        function createModernTable(x, y, z) {
            const group = new THREE.Group();
            const top = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.8, 0.05, 0.8), materials.stoneMarble));
            top.position.y = 0.4;
            group.add(top);
            
            const base = enableShadows(new THREE.Mesh(new THREE.CylinderGeometry(0.3, 0.3, 0.4, 16), materials.metalChrome));
            base.position.y = 0.2;
            group.add(base);

            group.position.set(x, y, z);
            return group;
        }

        function createFloorCushion(x, y, z, color = 'default') {
            const material = color === 'red' ? materials.fabricRed : materials.fabric;
            const cushion = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.5, 0.1, 0.5), material));
            cushion.position.set(x, y + 0.05, z);
            return cushion;
        }

        function createDoorway(x, y, z, rotationY = 0) {
            const group = new THREE.Group();
            
            // Frame
            const frameTop = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(1.2, 0.1, 0.1), materials.woodDark));
            frameTop.position.y = 2.15;
            group.add(frameTop);

            const frameLeft = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.1, 2.2, 0.1), materials.woodDark));
            frameLeft.position.set(-0.55, 1.1, 0);
            group.add(frameLeft);

            const frameRight = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.1, 2.2, 0.1), materials.woodDark));
            frameRight.position.set(0.55, 1.1, 0);
            group.add(frameRight);

            // Door
            const door = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(1.0, 2.1, 0.05), materials.woodLight));
            door.position.set(0, 1.05, 0);
            group.add(door);
            
            group.position.set(x, y, z);
            group.rotation.y = rotationY;
            return group;
        }

        function createWindow(x, y, z, rotationY = 0) {
            const group = new THREE.Group();
            
            // Frame
            const frameTop = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(1.5, 0.1, 0.1), materials.woodDark));
            frameTop.position.y = 1.15;
            group.add(frameTop);

            const frameBottom = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(1.5, 0.1, 0.1), materials.woodDark));
            frameBottom.position.y = 0.15;
            group.add(frameBottom);

            const frameLeft = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.1, 1.0, 0.1), materials.woodDark));
            frameLeft.position.set(-0.7, 0.65, 0);
            group.add(frameLeft);

            const frameRight = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.1, 1.0, 0.1), materials.woodDark));
            frameRight.position.set(0.7, 0.65, 0);
            group.add(frameRight);

            // Glass
            const glass = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(1.4, 0.9, 0.02), materials.glass));
            glass.position.set(0, 0.65, 0);
            group.add(glass);
            
            group.position.set(x, y, z);
            group.rotation.y = rotationY;
            return group;
        }

        function createWall(x, y, z, width, height, rotationY = 0) {
            const wall = enableShadows(new THREE.Mesh(
                new THREE.BoxGeometry(width, height, 0.1), 
                materials.wallBeige
            ));
            wall.position.set(x, y + height/2, z);
            wall.rotation.y = rotationY;
            return wall;
        }

        function createShojiScreen(x, y, z, rotationY = 0) {
            const group = new THREE.Group();
            
            // Frame
            const frameTop = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(1.5, 0.1, 0.05), materials.woodBlack));
            frameTop.position.y = 1.55;
            group.add(frameTop);

            const frameBottom = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(1.5, 0.1, 0.05), materials.woodBlack));
            frameBottom.position.y = 0.05;
            group.add(frameBottom);

            const frameLeft = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.1, 1.4, 0.05), materials.woodBlack));
            frameLeft.position.set(-0.7, 0.8, 0);
            group.add(frameLeft);

            const frameRight = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.1, 1.4, 0.05), materials.woodBlack));
            frameRight.position.set(0.7, 0.8, 0);
            group.add(frameRight);

            // Paper panels
            const paper = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(1.4, 1.4, 0.01), materials.paper));
            paper.position.set(0, 0.8, 0);
            group.add(paper);
            
            // Grid pattern
            for(let i = 0; i < 3; i++) {
                const horizontal = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(1.4, 0.02, 0.02), materials.woodBlack));
                horizontal.position.set(0, 0.4 + i * 0.5, 0.005);
                group.add(horizontal);
            }
            
            for(let i = 0; i < 3; i++) {
                const vertical = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.02, 1.4, 0.02), materials.woodBlack));
                vertical.position.set(-0.35 + i * 0.35, 0.8, 0.005);
                group.add(vertical);
            }
            
            group.position.set(x, y, z);
            group.rotation.y = rotationY;
            return group;
        }

        function createStoneLantern(x, y, z) {
            const group = new THREE.Group();
            
            // Base
            const base = enableShadows(new THREE.Mesh(new THREE.CylinderGeometry(0.4, 0.5, 0.2, 16), materials.stoneDark));
            base.position.y = 0.1;
            group.add(base);
            
            // Stem
            const stem = enableShadows(new THREE.Mesh(new THREE.CylinderGeometry(0.2, 0.25, 0.8, 16), materials.stoneDark));
            stem.position.y = 0.6;
            group.add(stem);
            
            // Light chamber
            const chamber = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.5, 0.4, 0.5), materials.stoneDark));
            chamber.position.y = 1.1;
            group.add(chamber);
            
            // Roof
            const roof = enableShadows(new THREE.Mesh(new THREE.CylinderGeometry(0.6, 0.3, 0.2, 16), materials.stoneDark));
            roof.position.y = 1.4;
            group.add(roof);
            
            // Light
            const light = new THREE.PointLight(0xffaa55, 0.8, 2);
            light.position.set(0, 1.1, 0);
            group.add(light);
            
            group.position.set(x, y, z);
            return group;
        }

        function createJapaneseLowTable() {
            const group = new THREE.Group();
            const tableTop = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(1.2, 0.08, 0.8), materials.woodBlack));
            tableTop.position.y = 0.3; 
            group.add(tableTop);
            
            // Add legs
            [-0.4, 0.4].forEach(x => {
                [-0.25, 0.25].forEach(z => {
                    const leg = enableShadows(new THREE.Mesh(new THREE.CylinderGeometry(0.05, 0.05, 0.25, 8), materials.woodBlack));
                    leg.position.set(x, 0.15, z); 
                    group.add(leg);
                });
            });
            
            return group;
        }

        function createChineseCabinet() {
            const group = new THREE.Group();
            const cabinet = enableShadows(new THREE.Mesh(
                new THREE.BoxGeometry(1.2, 1.8, 0.45), 
                materials.woodRed
            ));
            cabinet.position.set(0, 0.9, 0); 
            group.add(cabinet);
            
            // Add decorative elements - gold accents
            const top = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(1.3, 0.1, 0.5), materials.woodRed));
            top.position.y = 1.85;
            group.add(top);
            
            // Gold handles
            const handleLeft = enableShadows(new THREE.Mesh(new THREE.CylinderGeometry(0.02, 0.02, 0.15, 8), materials.metalGold));
            handleLeft.position.set(-0.4, 1.0, 0.23);
            handleLeft.rotation.z = Math.PI / 2;
            group.add(handleLeft);
            
            const handleRight = enableShadows(new THREE.Mesh(new THREE.CylinderGeometry(0.02, 0.02, 0.15, 8), materials.metalGold));
            handleRight.position.set(0.4, 1.0, 0.23);
            handleRight.rotation.z = Math.PI / 2;
            group.add(handleRight);
            
            // Decorative panels
            const panel = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.8, 0.6, 0.01), materials.woodDark));
            panel.position.set(0, 1.2, 0.22);
            group.add(panel);
            
            return group;
        }

        function createChineseVase(x, y, z) {
            const group = new THREE.Group();
            
            // Vase body
            const vase = enableShadows(new THREE.Mesh(
                new THREE.CylinderGeometry(0.15, 0.1, 0.4, 16, 1, false, 0, Math.PI * 2),
                materials.ceramicWhite
            ));
            vase.position.y = 0.2;
            group.add(vase);
            
            // Gold rim
            const rim = enableShadows(new THREE.Mesh(
                new THREE.TorusGeometry(0.15, 0.02, 16, 32),
                materials.metalGold
            ));
            rim.position.y = 0.4;
            rim.rotation.x = Math.PI / 2;
            group.add(rim);
            
            // Flowers
            for(let i = 0; i < 5; i++) {
                const stem = enableShadows(new THREE.Mesh(
                    new THREE.CylinderGeometry(0.005, 0.005, 0.3, 8),
                    materials.plantGreen
                ));
                stem.position.set(
                    Math.random() * 0.1 - 0.05,
                    0.5,
                    Math.random() * 0.1 - 0.05
                );
                group.add(stem);
                
                const flower = enableShadows(new THREE.Mesh(
                    new THREE.SphereGeometry(0.03, 8, 8),
                    materials.fabricRed
                ));
                flower.position.set(
                    stem.position.x,
                    0.65,
                    stem.position.z
                );
                group.add(flower);
            }
            
            group.position.set(x, y, z);
            return group;
        }

        function createZenGarden(x, y, z, width = 1.5, depth = 1.0) {
            const group = new THREE.Group();
            
            // Sand base
            const sand = enableShadows(new THREE.Mesh(
                new THREE.BoxGeometry(width, 0.05, depth),
                new THREE.MeshStandardMaterial({ color: 0xf5f5dc, roughness: 0.9 })
            ));
            sand.position.y = 0.025;
            group.add(sand);
            
            // Rocks
            const rockPositions = [
                [0.3, 0, 0.2],
                [-0.4, 0, -0.3],
                [0.5, 0, -0.4]
            ];
            
            rockPositions.forEach(pos => {
                const rock = enableShadows(new THREE.Mesh(
                    new THREE.DodecahedronGeometry(0.1 + Math.random() * 0.05, 0),
                    materials.stoneDark
                ));
                rock.position.set(pos[0], 0.1, pos[1]);
                group.add(rock);
            });
            
            // Raked pattern
            for(let i = 0; i < 8; i++) {
                const rakeLine = enableShadows(new THREE.Mesh(
                    new THREE.BoxGeometry(width - 0.2, 0.01, 0.01),
                    new THREE.MeshStandardMaterial({ color: 0xddddbb })
                ));
                rakeLine.position.set(0, 0.03, -0.4 + i * 0.1);
                group.add(rakeLine);
            }
            
            group.position.set(x, y, z);
            return group;
        }

        // Style-specific furniture
        function createAmericanCouch() {
            const couch = new THREE.Group();
            
            const base = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(2.2, 0.4, 0.8), materials.fabric));
            base.position.y = 0.2; 
            couch.add(base);
            
            const back = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(2.2, 0.5, 0.2), materials.fabric));
            back.position.set(0, 0.65, -0.3); 
            couch.add(back);
            
            const armLeft = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.3, 0.6, 0.8), materials.fabric));
            armLeft.position.set(-1.1, 0.3, 0); 
            couch.add(armLeft);
            
            const armRight = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.3, 0.6, 0.8), materials.fabric));
            armRight.position.set(1.1, 0.3, 0); 
            couch.add(armRight);
            
            couch.position.set(0, 0, -1.2);
            return couch;
        }

        function createFireplace() {
            const group = new THREE.Group();
            
            const base = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(1.8, 0.2, 0.5), materials.stoneMarble));
            base.position.y = 0.1;
            group.add(base);
            
            const leftColumn = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.3, 1.2, 0.4), materials.stoneMarble));
            leftColumn.position.set(-0.6, 0.6, 0);
            group.add(leftColumn);
            
            const rightColumn = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.3, 1.2, 0.4), materials.stoneMarble));
            rightColumn.position.set(0.6, 0.6, 0);
            group.add(rightColumn);
            
            const top = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(1.8, 0.2, 0.5), materials.stoneMarble));
            top.position.y = 1.3;
            group.add(top);
            
            const back = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(1.0, 1.0, 0.1), materials.metalBlack));
            back.position.set(0, 0.7, -0.1);
            group.add(back);
            
            group.position.set(0, 0, 1.5);
            group.rotation.y = Math.PI;
            return group;
        }

        // Classic European Armchair
        function createEuropeanArmchair() {
            const group = new THREE.Group();
            // Seat
            const seat = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.7, 0.18, 0.7), materials.fabricGold));
            seat.position.y = 0.18;
            group.add(seat);
            // Backrest
            const back = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.7, 0.5, 0.12), materials.fabricGold));
            back.position.set(0, 0.43, -0.29);
            group.add(back);
            // Arms
            const armL = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.12, 0.3, 0.7), materials.woodDark));
            armL.position.set(-0.29, 0.33, 0);
            group.add(armL);
            const armR = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.12, 0.3, 0.7), materials.woodDark));
            armR.position.set(0.29, 0.33, 0);
            group.add(armR);
            // Legs
            [-0.25, 0.25].forEach(x => {
                [-0.25, 0.25].forEach(z => {
                    const leg = enableShadows(new THREE.Mesh(new THREE.CylinderGeometry(0.04, 0.04, 0.18, 8), materials.woodDark));
                    leg.position.set(x, 0.09, z);
                    group.add(leg);
                });
            });
            group.position.set(-1.0, 0, -0.5);
            return group;
        }

        // European Round Marble Table
        function createEuropeanTable() {
            const group = new THREE.Group();
            const top = enableShadows(new THREE.Mesh(new THREE.CylinderGeometry(0.5, 0.5, 0.06, 32), materials.stoneMarble));
            top.position.y = 0.36;
            group.add(top);
            const base = enableShadows(new THREE.Mesh(new THREE.CylinderGeometry(0.18, 0.18, 0.36, 16), materials.metalGold));
            base.position.y = 0.18;
            group.add(base);
            group.position.set(0, 0, 0.7);
            return group;
        }

        // European Chandelier
        function createEuropeanChandelier() {
            const group = new THREE.Group();
            const body = enableShadows(new THREE.Mesh(new THREE.SphereGeometry(0.12, 16, 16), materials.metalGold));
            body.position.y = 2.2;
            group.add(body);
            for(let i=0; i<5; i++) {
                const arm = enableShadows(new THREE.Mesh(new THREE.CylinderGeometry(0.02, 0.02, 0.35, 8), materials.metalGold));
                arm.position.set(Math.sin(i*2*Math.PI/5)*0.25, 2.1, Math.cos(i*2*Math.PI/5)*0.25);
                arm.rotation.z = Math.PI/2;
                group.add(arm);
                const bulb = new THREE.PointLight(0xffeebb, 0.3, 2);
                bulb.position.set(Math.sin(i*2*Math.PI/5)*0.25, 2.1, Math.cos(i*2*Math.PI/5)*0.25);
                group.add(bulb);
            }
            return group;
        }

        // German Minimalist Lounge Chair
        function createGermanLoungeChair() {
            const group = new THREE.Group();
            // Seat
            const seat = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.7, 0.12, 0.5), materials.leatherBlack));
            seat.position.y = 0.12;
            group.add(seat);
            // Backrest
            const back = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.5, 0.3, 0.08), materials.leatherBlack));
            back.position.set(0, 0.32, -0.21);
            group.add(back);
            // Legs
            [-0.25, 0.25].forEach(x => {
                const leg = enableShadows(new THREE.Mesh(new THREE.CylinderGeometry(0.03, 0.03, 0.18, 8), materials.metalChrome));
                leg.position.set(x, 0.09, 0.18);
                group.add(leg);
            });
            // Armrests
            const armL = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.08, 0.12, 0.5), materials.metalChrome));
            armL.position.set(-0.29, 0.18, 0);
            group.add(armL);
            const armR = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.08, 0.12, 0.5), materials.metalChrome));
            armR.position.set(0.29, 0.18, 0);
            group.add(armR);
            group.position.set(1.0, 0, -0.5);
            return group;
        }

        // German Bookshelf
        function createGermanBookshelf() {
            const group = new THREE.Group();
            const shelf = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.12, 1.0, 0.7), materials.woodLight));
            shelf.position.set(0, 0.5, 0);
            group.add(shelf);
            for(let i=0; i<5; i++) {
                const board = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.12, 0.04, 0.7), materials.woodDark));
                board.position.set(0, 0.12 + i*0.2, 0);
                group.add(board);
            }
            group.position.set(-1.2, 0, 1.0);
            return group;
        }

        function createBathtub() {
            const group = new THREE.Group();
            
            const tub = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(1.6, 0.7, 0.8), materials.ceramicWhite));
            tub.position.y = 0.35;
            group.add(tub);
            
            const faucet = enableShadows(new THREE.Mesh(new THREE.CylinderGeometry(0.02, 0.02, 0.2, 8), materials.metalChrome));
            faucet.position.set(-0.7, 0.7, 0); 
            faucet.rotation.z = 0.2;
            group.add(faucet);
            
            group.position.set(0.8, 0, -1.2);
            return group;
        }

        function createKitchenCounter() {
            const group = new THREE.Group();
            
            const counter = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(2.5, 0.9, 0.7), materials.woodDark));
            counter.position.y = 0.45;
            group.add(counter);
            
            const top = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(2.5, 0.05, 0.7), materials.stoneMarble));
            top.position.y = 0.925;
            group.add(top);
            
            const sink = enableShadows(new THREE.Mesh(new THREE.BoxGeometry(0.5, 0.02, 0.4), materials.metalBlack));
            sink.position.set(-0.7, 0.95, 0);
            group.add(sink);
            
            group.position.set(0, 0, -1.5);
            return group;
        }

        // Stone pathwalk with pebbles for outdoor
        function createStonePathwalkPebbles(x = 0, y = 0, z = 0, count = 18, radius = 0.18, spreadX = 2.5, spreadZ = 1.5) {
            const group = new THREE.Group();
            for (let i = 0; i < count; i++) {
                const pebbleRadius = radius * (0.7 + Math.random() * 0.6);
                const geo = new THREE.DodecahedronGeometry(pebbleRadius, 0);
                const mat = new THREE.MeshStandardMaterial({
                    color: [0xbbb7a1, 0x8d8a7b, 0xcfcfc4, 0x9e9e9e][Math.floor(Math.random() * 4)],
                    roughness: 0.8 + Math.random() * 0.15,
                    metalness: 0.05 + Math.random() * 0.1
                });
                const mesh = enableShadows(new THREE.Mesh(geo, mat));
                // Arrange pebbles in a winding path
                const t = i / (count - 1);
                const px = x + (Math.sin(t * Math.PI * 1.2) * spreadX * 0.4) + (Math.random() - 0.5) * 0.18;
                const pz = z + (t - 0.5) * spreadZ + (Math.random() - 0.5) * 0.18;
                mesh.position.set(px, y + pebbleRadius * 0.5, pz);
                mesh.rotation.y = Math.random() * Math.PI * 2;
                group.add(mesh);
            }
            return group;
        }

        function createOutdoorLounge() {
            const group = new THREE.Group();
            // Add stone pathwalk pebbles instead of table
            const pathwalk = createStonePathwalkPebbles(0, 0, 0, 18, 0.18, 2.5, 1.5);
            group.add(pathwalk);
            // Add a potted plant for decor
            group.add(createPottedPlant(1.4, 0, -0.5, 'tall'));
            group.position.set(0, 0, -1.0);
            return group;
        }

        // =========================================
        // SCENE MANAGEMENT
        // =========================================
        function update3DScene() {
            // Clear old objects
            styleGroup.clear();
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
            
            // Reset camera defaults
            controls.target.set(0, 0.8, 0);
            camera.position.set(0, 2.5, 4.5);
            
            if (!state.currentTexture) return;

            // Apply tiling
            state.currentTexture.repeat.set(state.currentGridSize, state.currentGridSize);
            
            // Create floor or pool
            if (state.currentRoom === 'pool') {
                floorObject = createPoolBasin(state.currentTexture);
                document.getElementById('tiling-controls').classList.add('opacity-50', 'pointer-events-none');
            } else {
                floorObject = createFloor(state.currentTexture);
                document.getElementById('tiling-controls').classList.remove('opacity-50', 'pointer-events-none');
            }
            scene.add(floorObject);

            // Add basic room structure
            if (state.currentRoom !== 'outdoor' && state.currentRoom !== 'pool' && state.currentStyle === 'none') {
                // Add walls
                styleGroup.add(createWall(0, 0, -2, 4, 2.5, 0)); // Back wall
                styleGroup.add(createWall(-2, 0, 0, 4, 2.5, Math.PI/2)); // Left wall
                styleGroup.add(createWall(2, 0, 0, 4, 2.5, -Math.PI/2)); // Right wall
                
                // Add windows
                styleGroup.add(createWindow(0, 0, -1.99, 0));
                styleGroup.add(createWindow(-1.99, 0, 0, Math.PI/2));
                
                // Add doorway
                styleGroup.add(createDoorway(1.99, 0, 0, -Math.PI/2));
            }

            // Add room-specific assets
            switch(state.currentRoom) {
                case 'bathroom':
                    styleGroup.add(createBathtub());
                    styleGroup.add(createPottedPlant(-1.2, 0, 0, 'round'));
                    break;
                case 'kitchen':
                    styleGroup.add(createKitchenCounter());
                    styleGroup.add(createPottedPlant(1.5, 0, -1.0, 'round'));
                    break;
                case 'outdoor':
                    styleGroup.add(createOutdoorLounge());
                    styleGroup.add(createPottedPlant(-1.5, 0, 1.0, 'tall'));
                    break;
                case 'corridor':
                    styleGroup.add(createAbstractPerson(-1.5, 0.5, 'standing'));
                    styleGroup.add(createPottedPlant(1.0, 0, 0, 'tall'));
                    break;
                case 'indoor':
                    // Style-specific assets will be added below
                    break;
            }

            // Add style-specific assets for indoor rooms
            if (state.currentRoom === 'indoor') {
                switch(state.currentStyle) {
                    case 'chinese':
                        // Main Chinese cabinet
                        // Place cabinet at the back (z = -1.3)
                        let cabinet = createChineseCabinet();
                        if (cabinet) cabinet.position.z = -1.3;
                        styleGroup.add(cabinet);
                        // Place vases at the back corners (z = -1.3, x = -1.0 and 1.0)
                        styleGroup.add(createChineseVase(-1.0, 0, -1.3));
                        styleGroup.add(createChineseVase(1.0, 0, -1.3));
                        break;
                    case 'american':
                        styleGroup.add(createAmericanCouch());
                        styleGroup.add(createModernTV(0, 0, 1.5, Math.PI));
                        styleGroup.add(createFloorLamp(1.8, -1.5));
                        styleGroup.add(createPottedPlant(-1.5, 0, -1.0, 'tall'));
                        break;
                    case 'european':
                        styleGroup.add(createFireplace());
                        styleGroup.add(createEuropeanArmchair());
                        styleGroup.add(createEuropeanTable());
                        styleGroup.add(createEuropeanChandelier());
                        styleGroup.add(createPottedPlant(1.2, 0, -1.0, 'round'));
                        break;
                    case 'german':
                        styleGroup.add(createGermanLoungeChair());
                        styleGroup.add(createModernTable(0, 0, 0.8));
                        styleGroup.add(createFloorLamp(1.2, -1.0));
                        styleGroup.add(createPottedPlant(-1.2, 0, 1.0, 'tall'));
                        break;
                    case 'japanese':
                        styleGroup.add(createJapaneseLowTable());
                        styleGroup.add(createFloorCushion(0.5, 0, 0.7, 'red'));
                        styleGroup.add(createFloorCushion(-0.5, 0, 0.7, 'red'));
                        styleGroup.add(createPottedPlant(1.2, 0, 1.0, 'bamboo'));
                        styleGroup.add(createStoneLantern(-1.2, 0, 1.0));
                        break;
                    case 'zen':
                        styleGroup.add(createShojiScreen(-1.5, 0, 0, Math.PI/2));
                        styleGroup.add(createShojiScreen(1.5, 0, 0, -Math.PI/2));
                        styleGroup.add(createJapaneseLowTable());
                        styleGroup.add(createFloorCushion(0.4, 0, 0.8));
                        styleGroup.add(createFloorCushion(-0.4, 0, 0.8));
                        styleGroup.add(createZenGarden(0, 0, -1.0, 1.2, 0.8));
                        styleGroup.add(createPottedPlant(1.2, 0, 1.0, 'bamboo'));
                        break;
                    case 'southern':
                        // Southern style: show a table, not pebbles
                        styleGroup.add(createModernTable(0, 0, 0));
                        styleGroup.add(createPottedPlant(1.2, 0, 0.8, 'tall'));
                        styleGroup.add(createPottedPlant(-1.2, 0, 0.8, 'round'));
                        break;
                    case 'none':
                    default:
                        // Empty room - just walls and floor
                        break;
                }
            }
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
            });
        }

        // =========================================
        // UI EVENT HANDLERS
        // =========================================
        function setupEventListeners() {
            // Tiling controls
            document.querySelectorAll('.tiling-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    document.querySelectorAll('.tiling-btn').forEach(b => b.classList.remove('active'));
                    e.currentTarget.classList.add('active');
                    state.currentGridSize = parseInt(e.currentTarget.dataset.size);
                    if (state.currentTexture) {
                        state.currentTexture.repeat.set(state.currentGridSize, state.currentGridSize);
                    }
                });
            });

            // Style and Room controls: FIXED - now properly updates UI state
            // Only one button can be active between style and room
            document.querySelectorAll('.style-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    if (e.currentTarget.disabled) return;
                    // Deactivate all style and room buttons
                    document.querySelectorAll('.style-btn').forEach(b => b.classList.remove('active'));
                    document.querySelectorAll('.room-btn').forEach(b => b.classList.remove('active'));
                    // Activate the clicked style button
                    e.currentTarget.classList.add('active');
                    state.currentStyle = e.currentTarget.dataset.style;
                    state.currentRoom = 'indoor'; // Always switch to indoor for style
                    update3DScene();
                });
            });

            document.querySelectorAll('.room-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    if (e.currentTarget.disabled) return;
                    // Deactivate all style and room buttons
                    document.querySelectorAll('.room-btn').forEach(b => b.classList.remove('active'));
                    document.querySelectorAll('.style-btn').forEach(b => b.classList.remove('active'));
                    // Activate the clicked room button and the 'Empty' style
                    e.currentTarget.classList.add('active');
                    document.querySelector('.style-btn[data-style="none"]').classList.add('active');
                    state.currentRoom = e.currentTarget.dataset.room;
                    state.currentStyle = 'none'; // Force no style when room is selected
                    update3DScene();
                });
            });

            // Environment controls
            document.querySelectorAll('.env-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.env-btn').forEach(b => b.classList.remove('active', 'bg-primary', 'text-white', 'border-secondary'));
                    btn.classList.add('active', 'bg-primary', 'text-white', 'border-secondary');
                    state.currentEnv = btn.dataset.env;
                });
            });

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

            // AI Generation
            document.getElementById('btn-generate-ai').addEventListener('click', generateAIImage);
            
            // Buy Now
            document.getElementById('btn-buy-now').addEventListener('click', () => {
                if (state.currentTile && state.currentTile.product_id) {
                    window.location.href = `product_detail.php?id=${state.currentTile.product_id}`;
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
                    <div class="col-span-2 text-center text-gray-400 py-8">
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
            document.getElementById('ref-tile-img').src = product.product_image;
            document.getElementById('ref-tile-name').textContent = product.product_name;

            // Enable controls (do not reset style or room)
            document.querySelectorAll('.style-btn, .room-btn').forEach(btn => {
                btn.disabled = false;
            });

            // When a tile is selected, deactivate all style and room buttons
            document.querySelectorAll('.style-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.room-btn').forEach(b => b.classList.remove('active'));
            // Re-activate the button matching the current state
            var styleBtn = document.querySelector('.style-btn[data-style="' + state.currentStyle + '"]');
            if (styleBtn) styleBtn.classList.add('active');
            var roomBtn = document.querySelector('.room-btn[data-room="' + state.currentRoom + '"]');
            if (roomBtn) roomBtn.classList.add('active');

            // Load texture and update scene
            loadTileTexture(product.product_image);

            // Enable action buttons
            document.getElementById('btn-buy-now').disabled = false;
            document.getElementById('btn-generate-ai').disabled = false;

            // Show details button
            const detailsBtn = document.getElementById('btn-view-details');
            detailsBtn.href = `product_detail.php?id=${product.product_id}`;
            detailsBtn.classList.remove('hidden');
        }

        // =========================================
        // AI IMAGE GENERATION
        // =========================================
        async function generateAIImage() {
            if (!state.currentTile || state.isAiGenerating) return;
            
            state.isAiGenerating = true;
            document.getElementById('ai-loading').classList.remove('hidden');
            
            try {
                const tileName = state.currentTile.product_name;
                const environment = state.currentEnv;
                const prompt = `A beautiful ${environment} with ${tileName} tiles on the floor, interior design, photorealistic, high quality, detailed, professional photography, natural lighting`;
                const imageUrl = await callAIGenerationAPI(prompt);
                
                if (imageUrl) {
                    const aiResultImage = document.getElementById('ai-result-image');
                    aiResultImage.src = imageUrl;
                    aiResultImage.classList.remove('hidden');
                    document.getElementById('ai-placeholder').classList.add('hidden');
                    document.getElementById('ai-reference-badge').classList.remove('hidden');
                } else {
                    throw new Error('Failed to generate image');
                }
            } catch (error) {
                console.error('AI generation error:', error);
                alert('Failed to generate AI image. Please try again later.');
            } finally {
                state.isAiGenerating = false;
                document.getElementById('ai-loading').classList.add('hidden');
            }
        }

        async function callAIGenerationAPI(prompt) {
            const apiKey = document.getElementById('api-key').value;
            try {
                const response = await fetch('https://api.stability.ai/v1/generation/stable-diffusion-xl-1024-v1-0/text-to-image', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${apiKey}`,
                    },
                    body: JSON.stringify({
                        text_prompts: [{ text: prompt, weight: 1 }],
                        cfg_scale: 7, 
                        height: 1024, 
                        width: 1024, 
                        steps: 30, 
                        samples: 1,
                    })
                });
                
                if (!response.ok) {
                    throw new Error(`API request failed: ${response.status} ${response.statusText}`);
                }
                
                const responseData = await response.json();
                if (responseData.artifacts && responseData.artifacts.length > 0) {
                    const imageData = responseData.artifacts[0].base64;
                    return `data:image/png;base64,${imageData}`;
                } else {
                    throw new Error('No image data in response');
                }
            } catch (error) {
                console.error('Error generating AI image:', error);
                return getFallbackImage();
            }
        }

        function getFallbackImage() {
            const canvas = document.createElement('canvas');
            canvas.width = 1024; 
            canvas.height = 1024;
            const ctx = canvas.getContext('2d');
            
            const gradient = ctx.createLinearGradient(0, 0, 1024, 1024);
            gradient.addColorStop(0, '#f8ede3');
            gradient.addColorStop(1, '#f7c59f');
            ctx.fillStyle = gradient;
            ctx.fillRect(0, 0, 1024, 1024);
            
            ctx.fillStyle = '#7d310a';
            ctx.font = 'bold 48px Inter, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('AI Preview Unavailable', 512, 512);
            ctx.font = '24px Inter, sans-serif';
            ctx.fillText('Please try again later', 512, 580);
            
            return canvas.toDataURL('image/png');
        }

        // =========================================
        // INITIALIZATION
        // =========================================
        function initializeApp() {
            init3D();
            setupEventListeners();
            fetchTileCategories();
            fetchProducts();
        }

        // Start the application
        initializeApp();
    </script>
</body>
</html>