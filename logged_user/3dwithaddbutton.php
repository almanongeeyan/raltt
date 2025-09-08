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
        
        .pagination-btn {
            transition: all 0.2s ease;
        }
        
        .pagination-btn:hover {
            background-color: #cf8756;
            color: white;
        }
        
        #visualizer-3d {
            background: radial-gradient(circle at 60% 40%, #fff 60%, #f7f3ef 100%);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(207,135,86,0.2), 0 1.5px 0 #fff;
            overflow: hidden;
            /* Hide scrollbars for all browsers */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none;  /* IE and Edge */
        }

        #visualizer-3d::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }
        
        .bathroom-container {
            position: relative;
            width: 100%;
            height: 340px;
            overflow: hidden;
            border-radius: 16px;
        }
        
        #visualizer-container {
            position: relative;
            width: 100%;
            height: 100%;
        }
        
        #background-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            pointer-events: none; /* Allows mouse events to pass through to the canvas */
        }
        
        #webgl-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
    </style>
</head>
<body class="bg-light min-h-screen pt-24">
    <div class="container mx-auto px-4 py-8">
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-black text-primary mb-4">3D Tile Visualizer</h1>
            <p class="text-lg max-w-3xl mx-auto" style="color:#111;font-weight:500;">Experience our premium tiles in immersive 3D. Select any tile to visualize it in your space before making a purchase decision.</p>
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
                    <h2 class="text-xl font-bold text-primary mb-4">Above View Tile Visualization</h2>
                    <p class="text-textlight text-sm mb-4">See how your selected tile looks from above, with furniture based on its best use.</p>
                    <div class="bathroom-container" style="height:320px;">
                        <canvas id="aboveview-canvas" style="width:100%;height:100%;display:block;"></canvas>
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <div class="text-textlight text-xs">Furniture will appear if the tile is best for tables.</div>
                        <div class="flex items-center">
                            <span class="text-textlight text-xs mr-2">Tile Scale:</span>
                            <input type="range" id="tile-scale-above" min="0.5" max="3" step="0.1" value="1" class="w-24">
                        </div>
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
    <script src="https://cdn.jsdelivr.net/npm/three@0.153.0/examples/js/controls/OrbitControls.js"></script>
    <script>
    // Global variables for 3D visualizer
    let scene3d, camera3d, renderer3d, cube;
    let isDragging = false;
    let previousMousePosition = { x: 0, y: 0 };
    
    // Above view visualization
    let aboveViewCanvas, aboveViewCtx, aboveTileImg = null, aboveFurnitureImg = null;
    let tileScaleAbove = 1;
    let tileIsBestForTable = false;

    // Data handling
    let allProducts = [];
    let currentPage = 1;
    const productsPerPage = 8;
    
    // Initialize 3D scene for single tile preview
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
        directionalLight.shadow.mapSize.width = 2048;
        directionalLight.shadow.mapSize.height = 2048;
        directionalLight.shadow.blurSamples = 16;
        scene3d.add(directionalLight);
        
        const shadowGeometry = new THREE.PlaneGeometry(2.8, 0.9);
        const shadowMaterial = new THREE.ShadowMaterial({ opacity: 0.28 });
        const shadow = new THREE.Mesh(shadowGeometry, shadowMaterial);
        shadow.position.y = -0.45;
        shadow.receiveShadow = true;
        scene3d.add(shadow);
        
        container.addEventListener('mousedown', onMouseDown);
        container.addEventListener('touchstart', onTouchStart);
        window.addEventListener('mouseup', onMouseUp);
        window.addEventListener('touchend', onTouchEnd);
        window.addEventListener('mousemove', onMouseMove);
        window.addEventListener('touchmove', onTouchMove);
        container.addEventListener('wheel', onMouseWheel);
        
        animate3d();
    }
    
    // Animation loop for 3D preview
    function animate3d() {
        requestAnimationFrame(animate3d);
        renderer3d.render(scene3d, camera3d);
    }
    
    // Mouse/Touch event handlers for 3D controls
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

    // Initialize above view canvas
    function initAboveView() {
        aboveViewCanvas = document.getElementById('aboveview-canvas');
        aboveViewCtx = aboveViewCanvas.getContext('2d');
        resizeAboveView();
        window.addEventListener('resize', resizeAboveView);
        document.getElementById('tile-scale-above').addEventListener('input', function(e) {
            tileScaleAbove = parseFloat(e.target.value);
            drawAboveView();
        });
        drawAboveView();
    }

    function resizeAboveView() {
        if (!aboveViewCanvas) return;
        const parent = aboveViewCanvas.parentElement;
        aboveViewCanvas.width = parent.clientWidth;
        aboveViewCanvas.height = parent.clientHeight;
        drawAboveView();
    }

    function drawAboveView() {
        if (!aboveViewCtx) return;
        const w = aboveViewCanvas.width;
        const h = aboveViewCanvas.height;
        aboveViewCtx.clearRect(0, 0, w, h);
        // Draw tiled background
        if (aboveTileImg) {
            const tileW = 80 * tileScaleAbove;
            const tileH = 80 * tileScaleAbove;
            for (let y = 0; y < h; y += tileH) {
                for (let x = 0; x < w; x += tileW) {
                    aboveViewCtx.drawImage(aboveTileImg, x, y, tileW, tileH);
                }
            }
        } else {
            // fallback color
            aboveViewCtx.fillStyle = '#eee';
            aboveViewCtx.fillRect(0, 0, w, h);
        }
        // Draw furniture if best for table
        if (tileIsBestForTable && aboveFurnitureImg) {
            const fw = w * 0.4, fh = h * 0.25;
            aboveViewCtx.drawImage(aboveFurnitureImg, (w-fw)/2, (h-fh)/2, fw, fh);
        }
    }
    
    // Render 3D model and update above view
    function render3D(imageUrl, isBestForTable) {
        const container = document.getElementById('visualizer-3d');
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

        // Update above view
        tileIsBestForTable = !!isBestForTable;
        aboveTileImg = null;
        if (imageUrl) {
            const img = new window.Image();
            img.crossOrigin = 'anonymous';
            img.onload = function() {
                aboveTileImg = img;
                drawAboveView();
            };
            img.onerror = function() {
                aboveTileImg = null;
                drawAboveView();
            };
            img.src = imageUrl;
        } else {
            drawAboveView();
        }
    }

    // Fetch products
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
    
    // Render products in grid
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
                    <img src="${product.product_image || '../images/user/tile1.jpg'}" alt="${product.product_name}" class="w-full h-48 object-cover bg-light" />
                </div>
                <div class="p-4 flex-1 flex flex-col">
                    <h3 class="font-bold text-primary text-base mb-1">${product.product_name}</h3>
                    <div class="text-secondary font-extrabold text-lg mb-3">₱${parseInt(product.product_price).toLocaleString()}</div>
                    <div class="mt-auto">
                        <button onclick="render3D('${product.product_image || '../images/user/tile1.jpg'}', ${product.best_for_table ? 'true' : 'false'})" 
                                class="w-full bg-primary text-white rounded-lg py-2 px-4 font-bold hover:bg-secondary transition-colors mb-2 view-3d-btn">
                            <i class="fas fa-cube mr-2"></i>View in 3D
                        </button>
                        <button class="w-full bg-[#2B3241] text-[#EF7232] rounded-lg py-2 px-4 font-bold hover:bg-[#EF7232] hover:text-[#2B3241] transition-colors add-to-cart-btn">
                            <i class="fas fa-shopping-cart mr-2"></i>Add to Cart
                        </button>
                    </div>
                </div>
            `;
            grid.appendChild(card);
        });
    }
    
    // Render pagination controls
    function renderPagination() {
        const pageCount = Math.ceil(allProducts.length / productsPerPage);
        const paginationContainer = document.getElementById('pagination');
        paginationContainer.innerHTML = '';
        if (pageCount <= 1) return;
        
        if (currentPage > 1) {
            const prevBtn = document.createElement('button');
            prevBtn.className = 'pagination-btn bg-white border border-secondary text-secondary rounded-md px-3 py-2 font-semibold';
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
            pageBtn.className = `pagination-btn border rounded-md px-4 py-2 font-semibold ${i === currentPage ? 'bg-secondary text-white border-secondary' : 'bg-white border-secondary text-secondary'}`;
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
            nextBtn.className = 'pagination-btn bg-white border border-secondary text-secondary rounded-md px-3 py-2 font-semibold';
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
    
    // Preload furniture image
    function preloadFurnitureImg() {
        aboveFurnitureImg = new window.Image();
        aboveFurnitureImg.src = '../images/visualizer/table_topview.png'; // You must provide this image
        aboveFurnitureImg.onload = drawAboveView;
    }

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        init3DScene();
        initAboveView();
        preloadFurnitureImg();
        fetchProducts();
        // Prevent page scroll when mouse is over 3D preview
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