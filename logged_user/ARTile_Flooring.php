<?php
include '../includes/headeruser.php';
require_once '../connection/connection.php';
// --- TILE FETCH LOGIC ---
$stmt = $conn->prepare("SELECT product_id, product_name, product_price, product_image FROM products WHERE product_type = 'tile' AND is_archived = 0 ORDER BY product_id DESC");
$stmt->execute();
$tiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($tiles as &$tile) {
    if (!empty($tile['product_image'])) {
        $tile['tile_image'] = 'data:image/jpeg;base64,' . base64_encode($tile['product_image']);
    } else {
        $tile['tile_image'] = '../images/user/tile1.jpg';
    }
}
unset($tile);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AR Tile Flooring Overlay | RALTT</title>
    <link rel="icon" type="image/png" href="../images/userlogo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@3.18.0/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/body-pix@2.0.5/dist/body-pix.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0B1120 0%, #1E293B 100%);
            color: #F1F5F9;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .glass-effect {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .gradient-text {
            background: linear-gradient(135deg, #FBBF24 0%, #F59E0B 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .tile-thumb.selected {
            border: 2px solid #FBBF24;
            box-shadow: 0 0 0 3px #FBBF2440;
        }
        .tile-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 10;
        }
        .tile-pattern {
            position: absolute;
            width: 100%;
            height: 100%;
            background-repeat: repeat;
            opacity: 0.9;
            transform-origin: center;
            transition: all 0.3s ease;
        }
        .overlay-controls {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 20;
            display: flex;
            gap: 1rem;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            padding: 12px 20px;
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .overlay-btn {
            background: #FBBF24;
            color: #222;
            border-radius: 9999px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            box-shadow: 0 2px 8px #FBBF2430;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
        }
        .overlay-btn:hover {
            background: #F59E0B;
            transform: scale(1.05);
        }
        .pattern-controls {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            justify-content: center;
        }
        .pattern-btn {
            background: rgba(30, 41, 59, 0.7);
            color: #F1F5F9;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .pattern-btn.active {
            background: #FBBF24;
            color: #222;
        }
        .pattern-btn:hover {
            background: rgba(251, 191, 36, 0.2);
        }
        .loading-indicator {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 25;
            background: rgba(15, 23, 42, 0.9);
            padding: 20px 30px;
            border-radius: 12px;
            text-align: center;
            display: none;
        }
        .spinner {
            border: 3px solid rgba(251, 191, 36, 0.3);
            border-radius: 50%;
            border-top: 3px solid #FBBF24;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .corner-marker {
            position: absolute;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #FBBF24;
            border: 3px solid #222;
            cursor: move;
            z-index: 15;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 10px rgba(251, 191, 36, 0.7);
            transform: translate(-50%, -50%);
            transition: all 0.2s ease;
        }
        .corner-marker:hover {
            transform: translate(-50%, -50%) scale(1.2);
        }
        .corner-marker::after {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #222;
        }
        .corner-area {
            position: absolute;
            border: 2px dashed rgba(251, 191, 36, 0.7);
            background: rgba(251, 191, 36, 0.1);
            z-index: 12;
            pointer-events: none;
        }
        .corner-connector {
            position: absolute;
            background: rgba(251, 191, 36, 0.5);
            z-index: 11;
            pointer-events: none;
            height: 2px;
            transform-origin: 0 0;
        }
        .mode-indicator {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 15;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .mode-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #EF4444;
        }
        .mode-dot.active {
            background: #10B981;
        }
        .corner-controls {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 15;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            padding: 12px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .corner-btn {
            background: rgba(30, 41, 59, 0.7);
            color: #F1F5F9;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
            margin: 4px;
        }
        .corner-btn:hover {
            background: rgba(251, 191, 36, 0.2);
        }
        .corner-btn.active {
            background: #FBBF24;
            color: #222;
        }
        .tile-preview {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .tile-preview.active {
            opacity: 1;
        }
        .corner-instructions {
            position: absolute;
            bottom: 80px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 15;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            padding: 12px 20px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            font-size: 14px;
        }
        .corner-counter {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background: #FBBF24;
            color: #222;
            border-radius: 50%;
            font-weight: bold;
            margin: 0 4px;
        }
        .corner-number {
            position: absolute;
            top: -28px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 20px;
            background: #FBBF24;
            color: #222;
            border-radius: 50%;
            font-size: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .perspective-controls {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            justify-content: center;
        }
        .perspective-btn {
            background: rgba(30, 41, 59, 0.7);
            color: #F1F5F9;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .perspective-btn.active {
            background: #FBBF24;
            color: #222;
        }
        .perspective-btn:hover {
            background: rgba(251, 191, 36, 0.2);
        }
        .canvas-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 5;
        }
        #canvas-output {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        /* FIXED CAMERA CONTAINER STYLES */
        .camera-container {
            position: relative;
            width: 100%;
            height: 400px; /* Fixed height instead of padding-bottom */
            border-radius: 1rem;
            overflow: hidden;
            background: linear-gradient(to bottom right, #1f2937, #111827);
            border: 2px solid #374151;
        }
        .camera-inner {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .start-camera-btn {
            background: #FBBF24;
            color: #222;
            border: none;
            padding: 12px 24px;
            border-radius: 9999px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 16px;
            z-index: 30; /* Higher z-index to ensure it's clickable */
            position: relative;
        }
        .start-camera-btn:hover {
            background: #F59E0B;
            transform: scale(1.05);
        }
        .start-camera-btn:active {
            transform: scale(0.95);
        }
        /* Ensure video fills container */
        #video-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        /* Fix for camera instructions overlay */
        #camera-instructions {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 25;
        }
    </style>
</head>
<body>
    <div class="h-16"></div>
    <main class="container mx-auto px-4 py-8">
        <section class="text-center mb-10">
            <div class="inline-flex items-center gap-3 glass-effect px-6 py-3 rounded-full mb-6">
                <i class="fa-solid fa-cube text-amber-400 text-lg"></i>
                <span class="text-sm font-medium text-gray-300">AR Flooring Overlay</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                <span class="text-white">Try Tiles</span>
                <span class="gradient-text">On Your Floor</span>
            </h1>
            <p class="text-lg text-gray-300 max-w-2xl mx-auto">Select four corners of your floor area, then choose a tile to overlay with realistic perspective.</p>
        </section>

        <!-- Camera & Overlay Section -->
        <section class="glass-effect rounded-3xl p-6 md:p-8 mb-16 max-w-4xl mx-auto">
            <div class="camera-container" id="camera-container">
                <div class="camera-inner">
                    <video id="video-preview" class="w-full h-full object-cover" autoplay playsinline muted></video>
                    <canvas id="canvas-output" class="canvas-container"></canvas>
                    
                    <!-- Corner selection elements -->
                    <div id="corner-area" class="corner-area"></div>
                    <div id="corner-connectors"></div>
                    <div id="corner-markers"></div>
                    
                    <!-- Tile overlay -->
                    <div class="tile-preview" id="tile-preview"></div>
                    
                    <!-- Mode indicator -->
                    <div class="mode-indicator" id="mode-indicator">
                        <div class="mode-dot" id="mode-dot"></div>
                        <span id="mode-text">Selecting corners</span>
                    </div>
                    
                    <!-- Corner controls -->
                    <div class="corner-controls" id="corner-controls">
                        <div class="text-white text-sm font-medium mb-2">Corner Selection</div>
                        <button class="corner-btn active" id="btn-add-corners">Add Corners</button>
                        <button class="corner-btn" id="btn-adjust-corners">Adjust Corners</button>
                        <button class="corner-btn" id="btn-reset-corners">Reset</button>
                    </div>
                    
                    <!-- Corner instructions -->
                    <div class="corner-instructions" id="corner-instructions">
                        <span id="instructions-text">Click on the video to place corner <span class="corner-counter">1</span> of 4</span>
                    </div>
                    
                    <!-- Overlay controls (shown after tile selection) -->
                    <div class="overlay-controls" id="overlay-controls" style="display:none;">
                        <button class="overlay-btn" id="btn-rotate" title="Rotate"><i class="fa-solid fa-rotate-right"></i></button>
                        <button class="overlay-btn" id="btn-scale-up" title="Zoom In"><i class="fa-solid fa-magnifying-glass-plus"></i></button>
                        <button class="overlay-btn" id="btn-scale-down" title="Zoom Out"><i class="fa-solid fa-magnifying-glass-minus"></i></button>
                        <button class="overlay-btn" id="btn-reset" title="Reset"><i class="fa-solid fa-arrows-rotate"></i></button>
                    </div>
                    
                    <!-- Instructions -->
                    <div id="camera-instructions" class="flex items-center justify-center transition-opacity duration-300">
                        <div class="text-center p-8 max-w-sm">
                            <div class="w-20 h-20 bg-amber-400/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-camera text-3xl text-amber-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Point Camera at Your Floor</h3>
                            <p class="text-gray-300 text-sm leading-relaxed">Select four corners of the floor area you want to tile. Once selected, choose a tile to overlay it with realistic perspective.</p>
                            <button class="start-camera-btn mt-4" id="start-camera-btn">
                                Start Camera
                            </button>
                        </div>
                    </div>
                    
                    <div class="loading-indicator" id="loading-indicator">
                        <div class="spinner"></div>
                        <p class="text-white text-sm" id="loading-text">Starting camera...</p>
                    </div>
                </div>
            </div>
            
            <div class="pattern-controls mt-4" id="pattern-controls" style="display:none;">
                <button class="pattern-btn active" data-pattern="straight">Straight</button>
                <button class="pattern-btn" data-pattern="diagonal">Diagonal</button>
                <button class="pattern-btn" data-pattern="herringbone">Herringbone</button>
            </div>
            
            <div class="perspective-controls mt-4" id="perspective-controls" style="display:none;">
                <button class="perspective-btn active" data-perspective="realistic">Realistic View</button>
                <button class="perspective-btn" data-perspective="top-down">Top-Down View</button>
            </div>
        </section>

        <!-- Tile Selection Gallery -->
        <section class="mb-20">
            <div class="text-center mb-8">
                <h2 class="text-2xl md:text-3xl font-bold mb-2">Choose a <span class="gradient-text">Tile</span></h2>
                <p class="text-gray-400 text-base max-w-xl mx-auto">Tap a tile to overlay it on your selected floor area. Try different styles instantly!</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-6">
                <?php if (empty($tiles)): ?>
                    <div class="col-span-full text-center py-12">
                        <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-tile text-2xl text-gray-500"></i>
                        </div>
                        <p class="text-gray-400 text-lg">No tiles available in the database.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($tiles as $tile): ?>
                    <div class="tile-thumb glass-effect rounded-xl p-2 text-center cursor-pointer border border-transparent hover:border-amber-400/40 transition-all duration-200" data-img="<?php echo $tile['tile_image']; ?>">
                        <img src="<?php echo $tile['tile_image']; ?>" alt="<?php echo htmlspecialchars($tile['product_name']); ?>" class="w-full h-20 object-cover rounded-lg mb-2">
                        <h4 class="font-semibold text-xs text-white truncate mb-1"><?php echo htmlspecialchars($tile['product_name']); ?></h4>
                        <p class="text-amber-400 font-bold text-xs">₱<?php echo number_format($tile['product_price'], 2); ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
        // Camera setup
        const video = document.getElementById('video-preview');
        const canvas = document.getElementById('canvas-output');
        const ctx = canvas.getContext('2d');
        const cameraContainer = document.getElementById('camera-container');
        const cameraInner = document.querySelector('.camera-inner');
        const cornerArea = document.getElementById('corner-area');
        const cornerConnectors = document.getElementById('corner-connectors');
        const cornerMarkers = document.getElementById('corner-markers');
        const tilePreview = document.getElementById('tile-preview');
        const controls = document.getElementById('overlay-controls');
        const patternControls = document.getElementById('pattern-controls');
        const perspectiveControls = document.getElementById('perspective-controls');
        const instructions = document.getElementById('camera-instructions');
        const cornerInstructions = document.getElementById('corner-instructions');
        const instructionsText = document.getElementById('instructions-text');
        const loadingIndicator = document.getElementById('loading-indicator');
        const loadingText = document.getElementById('loading-text');
        const modeIndicator = document.getElementById('mode-indicator');
        const modeDot = document.getElementById('mode-dot');
        const modeText = document.getElementById('mode-text');
        const cornerControls = document.getElementById('corner-controls');
        const startCameraBtn = document.getElementById('start-camera-btn');
        
        // Corner selection buttons
        const btnAddCorners = document.getElementById('btn-add-corners');
        const btnAdjustCorners = document.getElementById('btn-adjust-corners');
        const btnResetCorners = document.getElementById('btn-reset-corners');
        
        let stream = null;
        let currentTileImage = null;
        let currentPattern = 'straight';
        let currentPerspective = 'realistic';
        let scale = 1;
        let rotation = 0;
        let corners = [];
        let isAddingCorners = true;
        let isAdjustingCorners = false;
        let selectedCorner = null;
        let dragOffset = { x: 0, y: 0 };
        let animationFrameId = null;
        let perspectiveTransform = null;

        // Debug: Check if elements are properly loaded
        console.log('Start Camera Button:', startCameraBtn);
        console.log('Camera Container:', cameraContainer);

        // Start camera when button is clicked
        startCameraBtn.addEventListener('click', initializeCamera);

        // Initialize camera
        async function initializeCamera() {
            console.log('Initialize camera called');
            try {
                loadingText.textContent = "Starting camera...";
                loadingIndicator.style.display = 'block';
                
                // Stop any existing stream
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                
                // Try different camera constraints for better compatibility
                const constraints = {
                    video: {
                        facingMode: 'environment',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                };
                
                stream = await navigator.mediaDevices.getUserMedia(constraints);
                
                video.srcObject = stream;
                
                // Wait for video to be ready
                video.addEventListener('loadeddata', function() {
                    console.log('Video data loaded');
                    // Set canvas dimensions to match video
                    const videoWidth = video.videoWidth || 640;
                    const videoHeight = video.videoHeight || 480;
                    
                    canvas.width = videoWidth;
                    canvas.height = videoHeight;
                    
                    loadingIndicator.style.display = 'none';
                    instructions.style.display = 'none';
                    setupCornerSelection();
                    
                    // Start drawing video to canvas
                    drawVideoToCanvas();
                });
                
                video.addEventListener('error', function(e) {
                    console.error('Video error:', e);
                    loadingIndicator.style.display = 'none';
                    showCameraError('Error loading video stream. Please try again.');
                });
                
            } catch (e) {
                console.error('Camera initialization error:', e);
                loadingIndicator.style.display = 'none';
                handleCameraError(e);
            }
        }

        // Draw video to canvas
        function drawVideoToCanvas() {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            }
            requestAnimationFrame(drawVideoToCanvas);
        }

        // Handle camera errors
        function handleCameraError(e) {
            let errorMessage = 'Error initializing camera. ';
            
            if (e.name === 'NotAllowedError') {
                errorMessage += 'Please check camera permissions and allow access to your camera.';
            } else if (e.name === 'NotFoundError') {
                errorMessage += 'No camera found on your device.';
            } else if (e.name === 'NotSupportedError') {
                errorMessage += 'Your browser does not support camera access.';
            } else if (e.name === 'OverconstrainedError') {
                errorMessage += 'Camera constraints could not be satisfied. Trying with default settings...';
                // Try again with simpler constraints
                trySimplerConstraints();
                return;
            } else {
                errorMessage += 'Please check camera permissions and try again.';
            }
            
            showCameraError(errorMessage);
        }

        // Try simpler camera constraints
        async function trySimplerConstraints() {
            try {
                loadingText.textContent = "Trying alternative camera settings...";
                loadingIndicator.style.display = 'block';
                
                const simplerConstraints = {
                    video: true // Let the browser choose the best available
                };
                
                stream = await navigator.mediaDevices.getUserMedia(simplerConstraints);
                video.srcObject = stream;
                
                video.addEventListener('loadeddata', function() {
                    console.log('Video loaded with simpler constraints');
                    const videoWidth = video.videoWidth || 640;
                    const videoHeight = video.videoHeight || 480;
                    
                    canvas.width = videoWidth;
                    canvas.height = videoHeight;
                    
                    loadingIndicator.style.display = 'none';
                    instructions.style.display = 'none';
                    setupCornerSelection();
                    drawVideoToCanvas();
                });
                
            } catch (simpleError) {
                console.error('Simple constraints also failed:', simpleError);
                loadingIndicator.style.display = 'none';
                showCameraError('Could not access camera with any settings. Please check permissions.');
            }
        }

        // Show camera error
        function showCameraError(message) {
            instructions.innerHTML = `
                <div class="text-center p-4">
                    <div class="w-16 h-16 bg-red-400/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-camera-slash text-2xl text-red-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Camera Error</h3>
                    <p class="text-red-300 text-sm mb-4">${message}</p>
                    <button class="start-camera-btn" onclick="initializeCamera()">
                        Try Again
                    </button>
                </div>
            `;
            instructions.style.display = 'flex';
        }

        // Setup corner selection
        function setupCornerSelection() {
            console.log('Setting up corner selection');
            
            // Add click event to video for corner placement
            cameraInner.addEventListener('click', handleContainerClick);
            
            // Set up corner control buttons
            btnAddCorners.addEventListener('click', () => {
                isAddingCorners = true;
                isAdjustingCorners = false;
                updateModeIndicator();
                updateCornerButtons();
                updateInstructions();
            });
            
            btnAdjustCorners.addEventListener('click', () => {
                if (corners.length === 4) {
                    isAddingCorners = false;
                    isAdjustingCorners = true;
                    updateModeIndicator();
                    updateCornerButtons();
                    updateInstructions();
                } else {
                    alert("Please select all four corners first.");
                }
            });
            
            btnResetCorners.addEventListener('click', resetCorners);
            
            // Start animation loop
            animationFrameId = requestAnimationFrame(updateCornerVisuals);
            
            // Show corner instructions
            cornerInstructions.style.display = 'block';
            updateInstructions();
        }

        // Handle container click for corner placement
        function handleContainerClick(e) {
            if (!isAddingCorners || corners.length >= 4) return;
            
            const rect = cameraInner.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            
            corners.push({ x, y });
            createCornerMarker(x, y, corners.length);
            
            updateInstructions();
            
            if (corners.length === 4) {
                // All corners selected, switch to adjust mode
                isAddingCorners = false;
                isAdjustingCorners = true;
                updateModeIndicator();
                updateCornerButtons();
                updateInstructions();
                
                // Calculate perspective transform
                calculatePerspectiveTransform();
            }
            
            updateCornerVisuals();
        }

        // Calculate perspective transform for realistic tile projection
        function calculatePerspectiveTransform() {
            if (corners.length !== 4) return;
            
            // Get the actual pixel positions of corners
            const rect = cameraInner.getBoundingClientRect();
            const sourcePoints = corners.map(corner => [
                (corner.x / 100) * rect.width,
                (corner.y / 100) * rect.height
            ]);
            
            // Define destination points (a rectangle)
            const width = rect.width;
            const height = rect.height;
            const destPoints = [
                [0, 0],           // Top-left
                [width, 0],       // Top-right
                [width, height],  // Bottom-right
                [0, height]       // Bottom-left
            ];
            
            // Calculate perspective transform matrix
            perspectiveTransform = getPerspectiveTransform(sourcePoints, destPoints);
        }

        // Get perspective transform matrix
        function getPerspectiveTransform(src, dst) {
            // Create matrix for solving the perspective transform
            const A = [];
            const B = [];
            
            for (let i = 0; i < 4; i++) {
                const [x, y] = src[i];
                const [u, v] = dst[i];
                
                A.push([x, y, 1, 0, 0, 0, -u*x, -u*y]);
                A.push([0, 0, 0, x, y, 1, -v*x, -v*y]);
                
                B.push(u);
                B.push(v);
            }
            
            // Solve the system of equations
            const h = solveLinearSystem(A, B);
            
            // Create 3x3 perspective transform matrix
            return [
                [h[0], h[1], h[2]],
                [h[3], h[4], h[5]],
                [h[6], h[7], 1]
            ];
        }

        // Solve linear system Ax = B
        function solveLinearSystem(A, B) {
            // Using Gaussian elimination for simplicity
            const n = A.length;
            
            for (let i = 0; i < n; i++) {
                // Find pivot
                let maxRow = i;
                for (let j = i + 1; j < n; j++) {
                    if (Math.abs(A[j][i]) > Math.abs(A[maxRow][i])) {
                        maxRow = j;
                    }
                }
                
                // Swap rows
                [A[i], A[maxRow]] = [A[maxRow], A[i]];
                [B[i], B[maxRow]] = [B[maxRow], B[i]];
                
                // Eliminate column
                for (let j = i + 1; j < n; j++) {
                    const factor = A[j][i] / A[i][i];
                    for (let k = i; k < n; k++) {
                        A[j][k] -= factor * A[i][k];
                    }
                    B[j] -= factor * B[i];
                }
            }
            
            // Back substitution
            const x = new Array(n).fill(0);
            for (let i = n - 1; i >= 0; i--) {
                let sum = 0;
                for (let j = i + 1; j < n; j++) {
                    sum += A[i][j] * x[j];
                }
                x[i] = (B[i] - sum) / A[i][i];
            }
            
            return x;
        }

        // Update instructions text
        function updateInstructions() {
            if (isAddingCorners) {
                if (corners.length < 4) {
                    instructionsText.innerHTML = `Click on the video to place corner <span class="corner-counter">${corners.length + 1}</span> of 4`;
                }
            } else if (isAdjustingCorners) {
                instructionsText.innerHTML = "Drag the corner markers to adjust the floor area";
            }
        }

        // Create a corner marker
        function createCornerMarker(x, y, index) {
            const marker = document.createElement('div');
            marker.className = 'corner-marker';
            marker.style.left = `${x}%`;
            marker.style.top = `${y}%`;
            marker.dataset.index = index - 1;
            
            // Add number indicator
            const number = document.createElement('div');
            number.className = 'corner-number';
            number.textContent = index;
            marker.appendChild(number);
            
            // Add drag functionality
            marker.addEventListener('mousedown', startDrag);
            marker.addEventListener('touchstart', startDrag, { passive: false });
            
            cornerMarkers.appendChild(marker);
            return marker;
        }

        // Start dragging a corner
        function startDrag(e) {
            if (!isAdjustingCorners) return;
            
            e.preventDefault();
            e.stopPropagation();
            
            const marker = e.target.closest('.corner-marker');
            if (!marker) return;
            
            selectedCorner = parseInt(marker.dataset.index);
            
            const rect = cameraInner.getBoundingClientRect();
            const markerRect = marker.getBoundingClientRect();
            
            if (e.type === 'mousedown') {
                dragOffset.x = e.clientX - (markerRect.left + markerRect.width / 2);
                dragOffset.y = e.clientY - (markerRect.top + markerRect.height / 2);
                
                document.addEventListener('mousemove', drag);
                document.addEventListener('mouseup', stopDrag);
            } else if (e.type === 'touchstart') {
                const touch = e.touches[0];
                dragOffset.x = touch.clientX - (markerRect.left + markerRect.width / 2);
                dragOffset.y = touch.clientY - (markerRect.top + markerRect.height / 2);
                
                document.addEventListener('touchmove', drag, { passive: false });
                document.addEventListener('touchend', stopDrag);
            }
        }

        // Drag corner
        function drag(e) {
            if (selectedCorner === null) return;
            
            e.preventDefault();
            
            const rect = cameraInner.getBoundingClientRect();
            let clientX, clientY;
            
            if (e.type === 'mousemove') {
                clientX = e.clientX;
                clientY = e.clientY;
            } else if (e.type === 'touchmove') {
                clientX = e.touches[0].clientX;
                clientY = e.touches[0].clientY;
            }
            
            const x = ((clientX - dragOffset.x - rect.left) / rect.width) * 100;
            const y = ((clientY - dragOffset.y - rect.top) / rect.height) * 100;
            
            // Update corner position
            corners[selectedCorner].x = Math.max(0, Math.min(100, x));
            corners[selectedCorner].y = Math.max(0, Math.min(100, y));
            
            // Recalculate perspective transform
            calculatePerspectiveTransform();
            
            updateCornerVisuals();
        }

        // Stop dragging
        function stopDrag() {
            selectedCorner = null;
            document.removeEventListener('mousemove', drag);
            document.removeEventListener('mouseup', stopDrag);
            document.removeEventListener('touchmove', drag);
            document.removeEventListener('touchend', stopDrag);
        }

        // Update corner visuals
        function updateCornerVisuals() {
            // Update corner markers
            const markers = cornerMarkers.querySelectorAll('.corner-marker');
            markers.forEach((marker, index) => {
                if (corners[index]) {
                    marker.style.left = `${corners[index].x}%`;
                    marker.style.top = `${corners[index].y}%`;
                }
            });
            
            // Update corner area and connectors
            updateCornerArea();
            updateCornerConnectors();
            
            // Update tile preview if a tile is selected
            if (currentTileImage && corners.length === 4) {
                applyTilePattern(currentPattern);
            }
        }

        // Update corner area polygon
        function updateCornerArea() {
            if (corners.length < 3) {
                cornerArea.style.clipPath = '';
                return;
            }
            
            // Create polygon points - connect all corners in order
            const points = corners.map(corner => `${corner.x}% ${corner.y}%`).join(', ');
            cornerArea.style.clipPath = `polygon(${points})`;
        }

        // Update corner connectors
        function updateCornerConnectors() {
            // Clear existing connectors
            cornerConnectors.innerHTML = '';
            
            if (corners.length < 2) return;
            
            // Create connectors between consecutive corners
            for (let i = 0; i < corners.length; i++) {
                const nextIndex = (i + 1) % corners.length;
                if (corners[nextIndex]) {
                    createConnector(corners[i], corners[nextIndex]);
                }
            }
        }

        // Create a connector between two corners
        function createConnector(corner1, corner2) {
            const connector = document.createElement('div');
            connector.className = 'corner-connector';
            
            const rect = cameraInner.getBoundingClientRect();
            
            // Calculate actual pixel positions
            const x1 = (corner1.x / 100) * rect.width;
            const y1 = (corner1.y / 100) * rect.height;
            const x2 = (corner2.x / 100) * rect.width;
            const y2 = (corner2.y / 100) * rect.height;
            
            // Calculate distance and angle
            const dx = x2 - x1;
            const dy = y2 - y1;
            const length = Math.sqrt(dx * dx + dy * dy);
            const angle = Math.atan2(dy, dx) * 180 / Math.PI;
            
            // Set connector position and style
            connector.style.left = `${x1}px`;
            connector.style.top = `${y1}px`;
            connector.style.width = `${length}px`;
            connector.style.transform = `rotate(${angle}deg)`;
            
            cornerConnectors.appendChild(connector);
        }

        // Reset corners
        function resetCorners() {
            corners = [];
            cornerMarkers.innerHTML = '';
            cornerArea.style.clipPath = '';
            cornerConnectors.innerHTML = '';
            tilePreview.classList.remove('active');
            tilePreview.innerHTML = '';
            controls.style.display = 'none';
            patternControls.style.display = 'none';
            perspectiveControls.style.display = 'none';
            
            isAddingCorners = true;
            isAdjustingCorners = false;
            updateModeIndicator();
            updateCornerButtons();
            updateInstructions();
        }

        // Update mode indicator
        function updateModeIndicator() {
            if (isAddingCorners) {
                modeDot.className = 'mode-dot';
                modeText.textContent = 'Selecting corners';
            } else if (isAdjustingCorners) {
                modeDot.className = 'mode-dot active';
                modeText.textContent = 'Adjusting corners';
            }
        }

        // Update corner buttons
        function updateCornerButtons() {
            btnAddCorners.classList.toggle('active', isAddingCorners);
            btnAdjustCorners.classList.toggle('active', isAdjustingCorners);
        }

        // Tile selection logic
        document.querySelectorAll('.tile-thumb').forEach(el => {
            el.addEventListener('click', function() {
                if (corners.length !== 4) {
                    alert("Please select all four corners of your floor area first.");
                    return;
                }
                
                document.querySelectorAll('.tile-thumb').forEach(t => t.classList.remove('selected'));
                this.classList.add('selected');
                
                // Show loading indicator
                loadingIndicator.style.display = 'block';
                loadingText.textContent = "Applying tile pattern...";
                
                // Get the tile image
                const tileImageUrl = this.getAttribute('data-img');
                currentTileImage = tileImageUrl;
                
                // Apply tile pattern after a short delay to show loading
                setTimeout(() => {
                    applyTilePattern(currentPattern);
                    controls.style.display = 'flex';
                    patternControls.style.display = 'flex';
                    perspectiveControls.style.display = 'flex';
                    loadingIndicator.style.display = 'none';
                    cornerInstructions.style.display = 'none';
                }, 500);
            });
        });

        // Apply tile pattern to the overlay
        function applyTilePattern(pattern) {
            currentPattern = pattern;
            
            if (!currentTileImage || corners.length !== 4) return;
            
            // Clear existing pattern
            tilePreview.innerHTML = '';
            
            // Create pattern container
            const patternContainer = document.createElement('div');
            patternContainer.className = 'tile-pattern';
            
            // Set background image
            patternContainer.style.backgroundImage = `url('${currentTileImage}')`;
            
            // Apply pattern-specific styles
            switch(pattern) {
                case 'straight':
                    patternContainer.style.backgroundSize = '25% auto';
                    break;
                case 'diagonal':
                    patternContainer.style.backgroundSize = '35% auto';
                    break;
                case 'herringbone':
                    patternContainer.style.backgroundSize = '40% auto';
                    break;
            }
            
            // Apply perspective transformation
            if (currentPerspective === 'realistic' && perspectiveTransform) {
                applyRealisticPerspective(patternContainer);
            } else {
                // Top-down view - simple clipping
                const points = corners.map(corner => `${corner.x}% ${corner.y}%`).join(', ');
                patternContainer.style.clipPath = `polygon(${points})`;
                patternContainer.style.transform = `scale(${scale}) rotate(${rotation}deg)`;
            }
            
            tilePreview.appendChild(patternContainer);
            tilePreview.classList.add('active');
            
            // Update pattern buttons
            document.querySelectorAll('.pattern-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.getAttribute('data-pattern') === pattern) {
                    btn.classList.add('active');
                }
            });
        }

        // Apply realistic perspective to tile pattern
        function applyRealisticPerspective(patternContainer) {
            // For now, use simple clipping until we implement proper perspective
            const points = corners.map(corner => `${corner.x}% ${corner.y}%`).join(', ');
            patternContainer.style.clipPath = `polygon(${points})`;
            patternContainer.style.transform = `scale(${scale}) rotate(${rotation}deg)`;
        }

        // Pattern selection
        document.querySelectorAll('.pattern-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const pattern = this.getAttribute('data-pattern');
                applyTilePattern(pattern);
            });
        });

        // Perspective selection
        document.querySelectorAll('.perspective-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const perspective = this.getAttribute('data-perspective');
                currentPerspective = perspective;
                
                // Update perspective buttons
                document.querySelectorAll('.perspective-btn').forEach(b => {
                    b.classList.remove('active');
                });
                this.classList.add('active');
                
                applyTilePattern(currentPattern);
            });
        });

        // Controls
        document.getElementById('btn-rotate').addEventListener('click', () => {
            rotation += 15;
            applyTilePattern(currentPattern);
        });
        
        document.getElementById('btn-scale-up').addEventListener('click', () => {
            scale = Math.min(scale + 0.1, 2.5);
            applyTilePattern(currentPattern);
        });
        
        document.getElementById('btn-scale-down').addEventListener('click', () => {
            scale = Math.max(scale - 0.1, 0.4);
            applyTilePattern(currentPattern);
        });
        
        document.getElementById('btn-reset').addEventListener('click', () => {
            scale = 1;
            rotation = 0;
            applyTilePattern(currentPattern);
        });

        // Clean up on page unload
        window.addEventListener('beforeunload', () => {
            if (animationFrameId) {
                cancelAnimationFrame(animationFrameId);
            }
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        });

        // Debug info
        console.log('AR Tile Overlay initialized successfully');
    </script>
</body>
</html>