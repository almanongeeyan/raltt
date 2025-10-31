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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7d310a',
                        secondary: '#cf8756',
                        accent: '#e8a56a',
                        dark: '#270f03',
                        light: '#f9f5f2',
                        textdark: '#333',
                        textlight: '#777',
                    },
                    fontFamily: {
                        inter: ['Inter', 'sans-serif'],
                    },
                    boxShadow: {
                        'product': '0 8px 24px rgba(0,0,0,0.08)',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #ffece2 0%, #f8f5f2 60%, #e8a56a 100%);
        }
        
        .product-card {
            transition: all 0.3s ease;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.15);
        }
        
        #visualizer-3d {
            background: radial-gradient(circle at 60% 40%, #fff 60%, #f7f3ef 100%);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(207,135,86,0.2), 0 1.5px 0 #fff;
            overflow: hidden;
        }

        .environment-buttons {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 20px;
        }
        
        .env-btn {
            background: white;
            border: 2px solid #e8a56a;
            border-radius: 12px;
            padding: 12px 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            color: #7d310a;
        }
        
        .env-btn:hover, .env-btn.active {
            background: #7d310a;
            border-color: #7d310a;
            color: white;
            transform: translateY(-2px);
        }
        
        .ai-generated-display {
            background: white;
            border-radius: 16px;
            height: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            border: 2px solid #e8a56a;
        }
        
        .ai-generated-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }
        
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
            flex-direction: column;
            border-radius: 16px;
        }
        
        .stability-badge {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .floating-containers-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 1fr);
            gap: 8px;
            margin: 0 auto;
            width: 100%;
            max-width: 500px;
            height: 450px;
        }
        
        .floating-container {
            background: #f9f5f2;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px dashed #cf8756;
        }

        .floating-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }
        
        .floating-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .floating-container.empty:before {
            content: '+';
            font-size: 24px;
            color: #cf8756;
            opacity: 0.7;
        }

        .drag-instructions {
            text-align: center;
            margin-top: 16px;
            color: #7d310a;
            font-weight: 500;
            background: #fff8f0;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #e8a56a;
        }

        .container-highlight {
            border: 2px solid #7d310a;
            background-color: rgba(125, 49, 10, 0.1);
        }
        
        .tile-dragging {
            opacity: 0.8;
            transform: scale(1.05);
            z-index: 100;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            margin-top: 10px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #8b5cf6, #7c3aed);
            border-radius: 3px;
            transition: width 0.3s ease;
            width: 0%;
        }

        .tile-preview {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid #e8a56a;
            margin: 0 auto 10px;
        }

        .prompt-preview {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            margin-top: 12px;
            font-size: 12px;
            color: #475569;
        }

        .api-key-section {
            display: none;
        }

        .tile-reference-display {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(255,255,255,0.9);
            padding: 8px;
            border-radius: 8px;
            border: 2px solid #e8a56a;
            z-index: 5;
        }

        .tile-reference-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-light min-h-screen pt-24">
    <div class="container mx-auto px-4 py-8">
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-black text-primary mb-4">3D Tile Visualizer</h1>
            <p class="text-lg max-w-3xl mx-auto" style="color:#111;font-weight:500;">Experience our premium tiles in immersive 3D with AI-powered visualization using your selected tile image.</p>
        </div>
        
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="lg:w-1/2 w-full">
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-primary mb-4">3D Preview</h2>
                    <div id="visualizer-3d" class="w-full h-80 md:h-96 flex items-center justify-center">
                        <div class="text-center text-textlight">
                            <i class="fas fa-cube text-4xl mb-3 text-secondary"></i>
                            <p>Select a product to view in 3D</p>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-textlight text-center">
                        <p>Drag to rotate • Scroll to zoom</p>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-primary">AI Environment Visualization</h2>
                        <span class="stability-badge">USES YOUR TILE IMAGE</span>
                    </div>
                    <p class="text-textlight text-sm mb-4">Select an environment to generate AI-powered visualization using your actual tile image as reference.</p>
                    
                    <!-- Hidden API Key Section -->
                    <div class="api-key-section">
                        <input type="hidden" id="api-key" value="sk-88Ufan8hPs5BlgAfLsUsHMML7DFvpV8VY77004U6UIL4CLLK">
                    </div>

                    <!-- Current Tile Preview -->
                    <div id="current-tile-section" class="hidden mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center gap-4">
                            <img id="current-tile-preview" src="" alt="Selected tile" class="tile-preview">
                            <div>
                                <h4 class="font-semibold text-primary text-sm" id="current-tile-name"></h4>
                                <p class="text-xs text-textlight">Ready for AI visualization - Your tile image will be used as reference</p>
                            </div>
                        </div>
                    </div>

                    <!-- Environment Buttons -->
                    <div class="mt-6">
                        <h3 class="font-semibold text-primary mb-3">Select Environment:</h3>
                        <div class="environment-buttons">
                            <div class="env-btn" data-environment="living-room">
                                <i class="fas fa-home mr-2"></i>Living Room
                            </div>
                            <div class="env-btn" data-environment="patio">
                                <i class="fas fa-tree mr-2"></i>Outdoor Patio
                            </div>
                            <div class="env-btn" data-environment="bathroom">
                                <i class="fas fa-bath mr-2"></i>Bathroom
                            </div>
                            <div class="env-btn" data-environment="kitchen">
                                <i class="fas fa-utensils mr-2"></i>Kitchen
                            </div>
                            <div class="env-btn" data-environment="bedroom">
                                <i class="fas fa-bed mr-2"></i>Bedroom
                            </div>
                            <div class="env-btn" data-environment="entryway">
                                <i class="fas fa-door-open mr-2"></i>Entryway
                            </div>
                        </div>
                    </div>

                    <!-- Prompt Preview -->
                    <div id="prompt-preview" class="prompt-preview hidden">
                        <strong>AI Prompt:</strong> <span id="prompt-text"></span>
                    </div>
                    
                    <!-- AI Display -->
                    <div class="ai-generated-display mt-6" id="ai-display">
                        <div class="text-center text-textlight p-6">
                            <i class="fas fa-robot text-4xl mb-3 text-secondary"></i>
                            <p class="font-semibold mb-2">AI Visualization Ready</p>
                            <p class="text-sm">Select a tile and environment to generate AI visualization</p>
                            <p class="text-xs text-green-600 mt-2">Your actual tile image will be used as reference</p>
                        </div>
                    </div>
                    
                    <!-- Controls -->
                    <div class="flex items-center justify-between mt-4">
                        <div class="text-textlight text-xs">
                            <i class="fas fa-bolt text-purple-500 mr-1"></i>
                            Uses Stability AI with image reference
                        </div>
                        <button id="regenerate-btn" class="bg-primary text-white text-sm px-4 py-2 rounded-lg font-semibold hover:bg-secondary transition-colors hidden">
                            <i class="fas fa-sync-alt mr-2"></i>Regenerate
                        </button>
                    </div>
                </div>

                <!-- Floating Containers Section -->
                <div class="bg-white rounded-2xl shadow-lg p-6 mt-6">
                    <h2 class="text-xl font-bold text-primary mb-4">Tile Arrangement</h2>
                    <p class="text-textlight text-sm mb-4">Drag tiles from the products section to any container below to create your custom arrangement.</p>
                    
                    <div class="floating-containers-grid" id="floating-containers">
                        <!-- Containers will be generated by JavaScript -->
                    </div>
                    
                    <p class="drag-instructions">
                        <i class="fas fa-hand-point-up mr-2"></i>
                        Drag tiles from the products section to place them in containers
                    </p>
                    <div class="flex justify-center mt-4">
                        <button id="export-arrangement-btn" class="bg-primary text-white font-bold px-6 py-2 rounded-lg shadow hover:bg-secondary transition-colors">
                            <i class="fas fa-download mr-2"></i>Export as Image
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="lg:w-1/2 w-full">
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                        <h2 class="text-xl font-bold text-primary mb-4 sm:mb-0">Available Tiles</h2>
                        <div class="flex items-center">
                            <span class="text-sm text-textlight mr-2">Filter by:</span>
                            <select class="bg-light border border-gray-200 rounded-lg px-3 py-2 text-sm">
                                <option>All Tiles</option>
                                <option>Popular</option>
                                <option>Best Sellers</option>
                                <option>New Arrivals</option>
                            </select>
                        </div>
                    </div>
                    
                    <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6"></div>
                    
                    <div id="pagination" class="flex justify-center gap-2 mt-8 pt-6 border-t border-gray-100"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/three@0.153.0/build/three.min.js"></script>
    <script>
    // Global variables
    let scene3d, camera3d, renderer3d, cube;
    let isDragging = false;
    let previousMousePosition = { x: 0, y: 0 };
    let currentEnvironment = null;
    let currentTileImage = null;
    let currentTileName = null;
    let allProducts = [];
    let currentPage = 1;
    const productsPerPage = 8;
    let draggedTile = null;
    let containers = [];

    // Stability AI Configuration
    const STABILITY_API_KEY = 'sk-88Ufan8hPs5BlgAfLsUsHMML7DFvpV8VY77004U6UIL4CLLK';

    // Initialize 3D Scene
    function init3DScene() {
        const container = document.getElementById('visualizer-3d');
        
        scene3d = new THREE.Scene();
        camera3d = new THREE.PerspectiveCamera(30, container.clientWidth / container.clientHeight, 0.1, 1000);
        camera3d.position.set(0, 0.12, 2.7);
        
        renderer3d = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        renderer3d.setClearColor(0xf7f3ef, 1);
        renderer3d.setSize(container.clientWidth, container.clientHeight);
        renderer3d.shadowMap.enabled = true;
        
        container.innerHTML = '';
        container.appendChild(renderer3d.domElement);
        
        const ambientLight = new THREE.AmbientLight(0xffffff, 1.08);
        scene3d.add(ambientLight);
        
        const directionalLight = new THREE.DirectionalLight(0xffffff, 0.85);
        directionalLight.position.set(2, 3, 4);
        directionalLight.castShadow = true;
        scene3d.add(directionalLight);
        
        const shadowGeometry = new THREE.PlaneGeometry(2.8, 0.9);
        const shadowMaterial = new THREE.ShadowMaterial({ opacity: 0.28 });
        const shadow = new THREE.Mesh(shadowGeometry, shadowMaterial);
        shadow.position.y = -0.45;
        shadow.receiveShadow = true;
        scene3d.add(shadow);
        
        // Event listeners for 3D controls
        container.addEventListener('mousedown', onMouseDown);
        container.addEventListener('touchstart', onTouchStart);
        window.addEventListener('mouseup', onMouseUp);
        window.addEventListener('touchend', onTouchEnd);
        window.addEventListener('mousemove', onMouseMove);
        window.addEventListener('touchmove', onTouchMove);
        container.addEventListener('wheel', onMouseWheel);
        
        animate3d();
    }

    function animate3d() {
        requestAnimationFrame(animate3d);
        renderer3d.render(scene3d, camera3d);
    }

    // Mouse/Touch event handlers
    function onMouseDown(event) {
        isDragging = true;
        previousMousePosition = { x: event.clientX, y: event.clientY };
    }
    
    function onTouchStart(event) {
        if (event.touches.length === 1) {
            isDragging = true;
            previousMousePosition = { x: event.touches[0].clientX, y: event.touches[0].clientY };
        }
    }
    
    function onMouseUp() { isDragging = false; }
    function onTouchEnd() { isDragging = false; }
    
    function onMouseMove(event) {
        if (isDragging && cube) {
            const deltaX = event.clientX - previousMousePosition.x;
            const deltaY = event.clientY - previousMousePosition.y;
            cube.rotation.y += deltaX * 0.01;
            cube.rotation.x += deltaY * 0.01;
            previousMousePosition = { x: event.clientX, y: event.clientY };
        }
    }
    
    function onTouchMove(event) {
        if (isDragging && event.touches.length === 1 && cube) {
            const deltaX = event.touches[0].clientX - previousMousePosition.x;
            const deltaY = event.touches[0].clientY - previousMousePosition.y;
            cube.rotation.y += deltaX * 0.01;
            cube.rotation.x += deltaY * 0.01;
            previousMousePosition = { x: event.touches[0].clientX, y: event.touches[0].clientY };
            event.preventDefault();
        }
    }
    
    function onMouseWheel(event) {
        if (camera3d) {
            camera3d.position.z += event.deltaY * 0.01;
            camera3d.position.z = Math.max(1.5, Math.min(5, camera3d.position.z));
        }
    }

    // Initialize Environment Buttons
    function initEnvironmentButtons() {
        const envButtons = document.querySelectorAll('.env-btn');
        envButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                envButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentEnvironment = this.dataset.environment;
                
                if (currentTileImage && currentTileName) {
                    generateAIVisualization(currentTileImage, currentTileName, currentEnvironment);
                } else {
                    showAIMessage('Please select a tile first', 'exclamation-triangle');
                }
            });
        });
        
        document.getElementById('regenerate-btn').addEventListener('click', function() {
            if (currentTileImage && currentTileName && currentEnvironment) {
                generateAIVisualization(currentTileImage, currentTileName, currentEnvironment);
            }
        });
    }

    // Generate AI Visualization with Stability AI using enhanced prompts
    async function generateAIVisualization(tileImage, tileName, environment) {
        const aiDisplay = document.getElementById('ai-display');
        const regenerateBtn = document.getElementById('regenerate-btn');
        
        // Show loading state
        showLoadingState(aiDisplay, environment);
        regenerateBtn.classList.remove('hidden');

        try {
            // Get tile description from image analysis
            const tileDescription = await analyzeTileImage(tileImage, tileName);
            const prompt = createEnhancedPromptWithDescription(tileName, environment, tileDescription);
            
            // Show prompt preview
            document.getElementById('prompt-preview').classList.remove('hidden');
            document.getElementById('prompt-text').textContent = prompt.substring(0, 150) + '...';
            
            const result = await callStabilityAI(STABILITY_API_KEY, prompt);
            
            if (result.success) {
                // Display the generated image with tile reference
                aiDisplay.innerHTML = `
                    <div class="tile-reference-display">
                        <img src="${tileImage}" alt="Reference tile" class="tile-reference-image">
                        <div class="text-xs text-center mt-1 text-primary font-semibold">Your Tile: ${tileName}</div>
                    </div>
                    <img src="${result.image}" alt="AI Generated ${environment} visualization with ${tileName}" class="ai-generated-image">
                    <div class="absolute bottom-3 right-3 bg-black bg-opacity-70 text-white text-xs px-3 py-1 rounded-full">
                        <i class="fas fa-robot mr-1"></i>Stability AI
                    </div>
                    <div class="absolute top-3 right-3 bg-green-500 text-white text-xs px-2 py-1 rounded">
                        Using Your Tile Pattern
                    </div>
                `;
                
                // Hide prompt preview after successful generation
                document.getElementById('prompt-preview').classList.add('hidden');
            } else {
                throw new Error(result.error);
            }
            
        } catch (error) {
            console.error('AI Generation Error:', error);
            showAIMessage('Failed to generate image: ' + error.message, 'exclamation-triangle');
            document.getElementById('prompt-preview').classList.add('hidden');
        }
    }

    // Analyze tile image and create description
    function analyzeTileImage(tileImage, tileName) {
        return new Promise((resolve) => {
            const img = new Image();
            img.onload = function() {
                // Create a simple analysis based on the image
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.drawImage(img, 0, 0);
                
                // Get image data for basic analysis
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const data = imageData.data;
                
                // Simple color analysis
                let r = 0, g = 0, b = 0;
                for (let i = 0; i < data.length; i += 4) {
                    r += data[i];
                    g += data[i + 1];
                    b += data[i + 2];
                }
                const pixelCount = data.length / 4;
                const avgR = Math.round(r / pixelCount);
                const avgG = Math.round(g / pixelCount);
                const avgB = Math.round(b / pixelCount);
                
                // Determine color family
                let colorFamily = 'neutral';
                if (avgR > avgG + 30 && avgR > avgB + 30) colorFamily = 'warm';
                else if (avgG > avgR + 30 && avgG > avgB + 30) colorFamily = 'green';
                else if (avgB > avgR + 30 && avgB > avgG + 30) colorFamily = 'cool';
                
                // Determine pattern type based on name and colors
                let patternType = 'geometric pattern';
                if (tileName.toLowerCase().includes('marble')) patternType = 'marble veining';
                if (tileName.toLowerCase().includes('wood')) patternType = 'wood grain';
                if (tileName.toLowerCase().includes('stone')) patternType = 'natural stone texture';
                if (tileName.toLowerCase().includes('mosaic')) patternType = 'mosaic pattern';
                if (tileName.toLowerCase().includes('hexagon')) patternType = 'hexagonal pattern';
                if (tileName.toLowerCase().includes('subway')) patternType = 'rectangular subway tile pattern';
                
                const description = `${colorFamily} colored ${patternType} with precise geometric layout, consistent grout lines, ${tileName.toLowerCase()}`;
                resolve(description);
            };
            img.src = tileImage;
        });
    }

    // Create enhanced prompt with tile description
    function createEnhancedPromptWithDescription(tileName, environment, tileDescription) {
        const environmentContext = {
            'living-room': 'modern living room interior, focus on floor showing exact tile pattern, contemporary furniture partially visible, natural lighting from large windows and focus more on the flooring',
            'patio': 'outdoor patio scene with tile flooring, beautiful garden background, sunny day lighting, outdoor furniture partially visible and focus more on the flooring',
            'bathroom': 'elegant modern bathroom interior with tile flooring, minimalist design, excellent lighting, spa-like atmosphere and focus more on the flooring',
            'kitchen': 'contemporary kitchen interior with tile flooring, modern appliances partially visible, clean countertops, natural lighting and focus more on the flooring',
            'bedroom': 'modern bedroom interior with tile flooring, contemporary bed and furniture partially visible, soft natural lighting and focus more on the flooring',
            'entryway': 'elegant entryway or foyer with tile flooring, architectural elements partially visible, welcoming atmosphere and focus more on the flooring'
        };
        
        return `Professional architectural photography of a ${environmentContext[environment]}. The floor features ${tileDescription}. The tile pattern must be clearly visible and accurately represented, covering 60-70% of the image. Photorealistic style, 4k resolution, professional interior design photography, accurate perspective, realistic lighting and shadows.`;
    }

    // Call Stability AI API
    async function callStabilityAI(apiKey, prompt) {
        try {
            console.log('Starting Stability AI image generation...');
            
            const response = await fetch('ai-proxy-stability.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    apiKey: apiKey,
                    prompt: prompt
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: Proxy request failed`);
            }

            const data = await response.json();
            
            if (data.success) {
                return {
                    success: true,
                    image: data.image
                };
            } else {
                throw new Error(data.error || 'Unknown error from Stability AI');
            }
            
        } catch (error) {
            console.error('Stability AI API Error:', error);
            return {
                success: false,
                error: error.message
            };
        }
    }

    // Helper functions
    function showLoadingState(container, environment) {
        container.innerHTML = `
            <div class="loading-overlay">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin text-4xl text-purple-500 mb-3"></i>
                    <p class="text-textlight font-semibold text-lg">Generating ${environment} Visualization</p>
                    <p class="text-textlight text-sm mt-2">Using your tile image as reference with Stability AI...</p>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progress-fill"></div>
                    </div>
                    <p class="text-xs text-textlight mt-3">Processing your tile image for accurate results</p>
                </div>
            </div>
        `;
        
        // Animate progress bar
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += 2;
            const progressFill = document.getElementById('progress-fill');
            if (progressFill) {
                progressFill.style.width = Math.min(progress, 90) + '%';
            }
            if (progress >= 100) {
                clearInterval(progressInterval);
            }
        }, 300);
    }

    function showAIMessage(message, icon = 'info-circle') {
        document.getElementById('ai-display').innerHTML = `
            <div class="text-center text-textlight">
                <i class="fas fa-${icon} text-3xl mb-3 text-secondary"></i>
                <p class="font-semibold">${message}</p>
            </div>
        `;
    }

    // Render 3D Model
    function render3D(imageUrl, productName, isBestForTable) {
        const container = document.getElementById('visualizer-3d');
        
        currentTileImage = imageUrl;
        currentTileName = productName;
        
        // Show current tile preview
        document.getElementById('current-tile-section').classList.remove('hidden');
        document.getElementById('current-tile-preview').src = imageUrl;
        document.getElementById('current-tile-name').textContent = productName;
        
        if (cube) {
            scene3d.remove(cube);
        }
        
        const geometry = new THREE.BoxGeometry(1.05, 1.05, 0.07);
        const textureLoaderCube = new THREE.TextureLoader();
        textureLoaderCube.load(imageUrl, function(texture) {
            texture.anisotropy = renderer3d.capabilities.getMaxAnisotropy();
            const materials = [
                new THREE.MeshStandardMaterial({ color: 0xf9f5f2, roughness: 0.32, metalness: 0.13 }),
                new THREE.MeshStandardMaterial({ color: 0xf9f5f2, roughness: 0.32, metalness: 0.13 }),
                new THREE.MeshStandardMaterial({ color: 0xf9f5f2, roughness: 0.32, metalness: 0.13 }),
                new THREE.MeshStandardMaterial({ color: 0xf9f5f2, roughness: 0.32, metalness: 0.13 }),
                new THREE.MeshStandardMaterial({ map: texture, roughness: 0.16, metalness: 0.18 }),
                new THREE.MeshStandardMaterial({ color: 0xf9f5f2, roughness: 0.32, metalness: 0.13 })
            ];
            cube = new THREE.Mesh(geometry, materials);
            cube.castShadow = true;
            cube.receiveShadow = false;
            cube.position.y = 0.18;
            scene3d.add(cube);
        }, undefined, function(error) {
            console.error('Error loading texture:', error);
            container.innerHTML = `<div class="text-center text-textlight p-8"><i class="fas fa-exclamation-triangle text-3xl mb-3"></i><p>Failed to load 3D preview</p></div>`;
        });

        showAIMessage(`Tile selected: <strong>${productName}</strong><br>Now select an environment to generate AI visualization using your tile image`, 'cube');
        document.getElementById('regenerate-btn').classList.add('hidden');
        document.querySelectorAll('.env-btn').forEach(btn => btn.classList.remove('active'));
        currentEnvironment = null;
        document.getElementById('prompt-preview').classList.add('hidden');
    }

    // Initialize Floating Containers
    function initFloatingContainers() {
        const containersGrid = document.getElementById('floating-containers');
        containersGrid.innerHTML = '';
        containers = [];
        
        for (let i = 0; i < 9; i++) {
            const container = document.createElement('div');
            container.className = 'floating-container empty';
            container.dataset.index = i;
            containers.push({element: container, tile: null});
            
            container.addEventListener('dragover', handleDragOver);
            container.addEventListener('dragenter', handleDragEnter);
            container.addEventListener('dragleave', handleDragLeave);
            container.addEventListener('drop', handleDrop);
            
            containersGrid.appendChild(container);
        }
    }

    // Drag and Drop Handlers
    function handleDragStart(e) {
        draggedTile = {
            productId: e.target.dataset.productId,
            imageUrl: e.target.src,
            productName: e.target.dataset.productName
        };
        e.target.classList.add('tile-dragging');
    }
    
    function handleDragEnd(e) {
        e.target.classList.remove('tile-dragging');
        draggedTile = null;
    }
    
    function handleDragOver(e) {
        e.preventDefault();
    }
    
    function handleDragEnter(e) {
        e.preventDefault();
        e.target.classList.add('container-highlight');
    }
    
    function handleDragLeave(e) {
        e.target.classList.remove('container-highlight');
    }
    
    function handleDrop(e) {
        e.preventDefault();
        e.target.classList.remove('container-highlight');
        
        if (!draggedTile) return;
        
        const containerIndex = parseInt(e.target.dataset.index);
        const container = containers[containerIndex];
        
        container.tile = draggedTile;
        container.element.classList.remove('empty');
        container.element.innerHTML = `<img src="${draggedTile.imageUrl}" alt="${draggedTile.productName}" draggable="false">`;
        
        const removeBtn = document.createElement('button');
        removeBtn.className = 'absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 transition-opacity duration-200';
        removeBtn.innerHTML = '×';
        removeBtn.onclick = function(event) {
            event.stopPropagation();
            resetContainer(containerIndex);
        };
        container.element.appendChild(removeBtn);
        
        container.element.addEventListener('mouseenter', () => {
            removeBtn.classList.remove('opacity-0');
            removeBtn.classList.add('opacity-100');
        });
        
        container.element.addEventListener('mouseleave', () => {
            removeBtn.classList.remove('opacity-100');
            removeBtn.classList.add('opacity-0');
        });
        
        draggedTile = null;
    }
    
    function resetContainer(index) {
        const container = containers[index];
        container.tile = null;
        container.element.classList.add('empty');
        container.element.innerHTML = '';
    }

    // Fetch Products
    function fetchProducts() {
        document.getElementById('products-grid').innerHTML = `
            <div class="col-span-2 flex justify-center items-center py-12">
                <div class="animate-pulse text-center">
                    <i class="fas fa-spinner fa-spin text-4xl text-secondary mb-3"></i>
                    <p class="text-textlight">Loading products...</p>
                </div>
            </div>
        `;
        
        fetch('../logged_user/processes/get_premium_tiles.php')
            .then(response => response.json())
            .then(products => {
                allProducts = products;
                renderProducts();
                renderPagination();
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                document.getElementById('products-grid').innerHTML = `
                    <div class="col-span-2 text-center py-12">
                        <i class="fas fa-exclamation-triangle text-3xl text-red-500 mb-3"></i>
                        <p class="text-textlight">Failed to load products. Please try again later.</p>
                        <button onclick="fetchProducts()" class="mt-4 bg-primary text-white rounded-lg px-4 py-2 font-semibold hover:bg-secondary transition-colors">Retry</button>
                    </div>
                `;
            });
    }

    // Render Products
    function renderProducts() {
        const grid = document.getElementById('products-grid');
        grid.innerHTML = '';
        
        if (allProducts.length === 0) {
            grid.innerHTML = `<div class="col-span-2 text-center py-12"><i class="fas fa-box-open text-3xl text-textlight mb-3"></i><p class="text-textlight">No products available at the moment.</p></div>`;
            return;
        }
        
        const start = (currentPage - 1) * productsPerPage;
        const end = start + productsPerPage;
        const pageProducts = allProducts.slice(start, end);
        
        pageProducts.forEach(product => {
            const card = document.createElement('div');
            card.className = 'product-card bg-white rounded-2xl overflow-hidden border border-gray-100 flex flex-col';
            let badge = '';
            if (product.is_best_seller == 1) {
                badge = '<span class="absolute top-3 left-3 bg-secondary text-white px-2 py-1 rounded text-xs font-bold uppercase z-10">Bestseller</span>';
            } else if (product.is_popular == 1) {
                badge = '<span class="absolute top-3 left-3 bg-primary text-white px-2 py-1 rounded text-xs font-bold uppercase z-10">Popular</span>';
            }
            card.innerHTML = `
                <div class="relative">
                    ${badge}
                    <img src="${product.product_image || '../images/user/tile1.jpg'}" 
                         alt="${product.product_name}" 
                         class="w-full h-48 object-cover bg-light product-image"
                         draggable="true"
                         data-product-id="${product.id}"
                         data-product-name="${product.product_name}" />
                </div>
                <div class="p-4 flex-1 flex flex-col">
                    <h3 class="font-bold text-primary text-base mb-1">${product.product_name}</h3>
                    <div class="text-secondary font-extrabold text-lg mb-3">₱${parseInt(product.product_price).toLocaleString()}</div>
                    <div class="mt-auto">
                        <button onclick="render3D('${product.product_image || '../images/user/tile1.jpg'}', '${product.product_name}', ${product.best_for_table ? 'true' : 'false'})" 
                                class="w-full bg-primary text-white rounded-lg py-2 px-4 font-bold hover:bg-secondary transition-colors mb-2 view-3d-btn">
                            <i class="fas fa-cube mr-2"></i>View in 3D
                        </button>
                    </div>
                </div>
            `;
            grid.appendChild(card);
        });
        
        // Add drag event listeners
        setTimeout(() => {
            document.querySelectorAll('.product-image').forEach(img => {
                img.addEventListener('dragstart', handleDragStart);
                img.addEventListener('dragend', handleDragEnd);
            });
        }, 100);
    }

    // Render Pagination
    function renderPagination() {
        const pageCount = Math.ceil(allProducts.length / productsPerPage);
        const paginationContainer = document.getElementById('pagination');
        paginationContainer.innerHTML = '';
        
        if (pageCount <= 1) return;
        
        if (currentPage > 1) {
            const prevBtn = document.createElement('button');
            prevBtn.className = 'bg-white border border-secondary text-secondary rounded-md px-3 py-2 font-semibold hover:bg-secondary hover:text-white transition-colors';
            prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
            prevBtn.addEventListener('click', () => {
                currentPage--;
                renderProducts();
                renderPagination();
                window.scrollTo({ top: document.getElementById('products-grid').offsetTop - 100, behavior: 'smooth' });
            });
            paginationContainer.appendChild(prevBtn);
        }
        
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(pageCount, startPage + maxVisiblePages - 1);
        
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className = `border rounded-md px-4 py-2 font-semibold ${i === currentPage ? 'bg-secondary text-white border-secondary' : 'bg-white border-secondary text-secondary hover:bg-secondary hover:text-white'} transition-colors`;
            pageBtn.textContent = i;
            pageBtn.addEventListener('click', () => {
                currentPage = i;
                renderProducts();
                renderPagination();
                window.scrollTo({ top: document.getElementById('products-grid').offsetTop - 100, behavior: 'smooth' });
            });
            paginationContainer.appendChild(pageBtn);
        }
        
        if (currentPage < pageCount) {
            const nextBtn = document.createElement('button');
            nextBtn.className = 'bg-white border border-secondary text-secondary rounded-md px-3 py-2 font-semibold hover:bg-secondary hover:text-white transition-colors';
            nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
            nextBtn.addEventListener('click', () => {
                currentPage++;
                renderProducts();
                renderPagination();
                window.scrollTo({ top: document.getElementById('products-grid').offsetTop - 100, behavior: 'smooth' });
            });
            paginationContainer.appendChild(nextBtn);
        }
    }

    // Export Arrangement as Image
    function exportTileArrangementAsImage() {
        const grid = document.getElementById('floating-containers');
        const cols = 3;
        const rows = 3;
        const cellW = grid.offsetWidth / cols;
        const cellH = grid.offsetHeight / rows;
        const canvas = document.createElement('canvas');
        canvas.width = grid.offsetWidth;
        canvas.height = grid.offsetHeight;
        const ctx = canvas.getContext('2d');

        ctx.fillStyle = '#f9f5f2';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        for (let i = 0; i < rows * cols; i++) {
            const container = grid.children[i];
            if (!container) continue;
            const img = container.querySelector('img');
            if (img && img.src) {
                drawImageCover(ctx, img, (i % cols) * cellW, Math.floor(i / cols) * cellH, cellW, cellH);
            }
        }

        const watermark = 'Rich Anne Lea Tiles Trading';
        ctx.save();
        ctx.translate(canvas.width / 2, canvas.height / 2);
        ctx.rotate(-Math.atan(canvas.height / canvas.width));
        ctx.font = 'bold 38px Inter, Arial, sans-serif';
        ctx.globalAlpha = 0.22;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.shadowColor = 'rgba(125,49,10,0.25)';
        ctx.shadowBlur = 8;
        ctx.lineWidth = 2;
        ctx.strokeStyle = 'rgba(255,255,255,0.7)';
        ctx.strokeText(watermark, 0, 0);
        ctx.shadowBlur = 0;
        ctx.fillStyle = '#7d310a';
        ctx.fillText(watermark, 0, 0);
        ctx.restore();

        const link = document.createElement('a');
        link.download = 'tile-arrangement.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    }

    function drawImageCover(ctx, img, x, y, w, h) {
        const imgRatio = img.naturalWidth / img.naturalHeight;
        const cellRatio = w / h;
        let drawW, drawH, offsetX, offsetY;
        if (imgRatio > cellRatio) {
            drawH = h;
            drawW = h * imgRatio;
            offsetX = x - (drawW - w) / 2;
            offsetY = y;
        } else {
            drawW = w;
            drawH = w / imgRatio;
            offsetX = x;
            offsetY = y - (drawH - h) / 2;
        }
        ctx.drawImage(img, offsetX, offsetY, drawW, drawH);
    }

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
        init3DScene();
        initEnvironmentButtons();
        initFloatingContainers();
        fetchProducts();
        
        document.getElementById('export-arrangement-btn').addEventListener('click', exportTileArrangementAsImage);
        
        const visualizer3d = document.getElementById('visualizer-3d');
        if (visualizer3d) {
            visualizer3d.addEventListener('wheel', function(e) {
                e.preventDefault();
            }, { passive: false });
        }
    });
    </script>
</body>
</html>