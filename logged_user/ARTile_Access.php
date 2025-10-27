<?php
include '../includes/headeruser.php';
require_once '../connection/connection.php';

// --- PAGINATION LOGIC ---
$items_per_page = 12;

$total_items_stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE product_type = 'tile' AND is_archived = 0");
$total_items_stmt->execute();
$total_items = $total_items_stmt->fetchColumn();

$total_pages = $total_items > 0 ? ceil($total_items / $items_per_page) : 0;

$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}
if ($current_page < 1) {
    $current_page = 1;
}

$offset = ($current_page - 1) * $items_per_page;

// --- DATABASE QUERY ---
$stmt = $conn->prepare("SELECT product_id, product_name, product_price, product_image FROM products WHERE product_type = 'tile' AND is_archived = 0 ORDER BY product_id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
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
    <title>Smart AR Tile Scanner | RALTT</title>
    <link rel="icon" type="image/png" href="../images/userlogo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.10.0/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet@2.1.0"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        :root {
            --primary-dark: #0B1120;
            --primary-blue: #1E293B;
            --accent-gold: #FBBF24;
            --accent-gold-dark: #D97706;
            --success-green: #10B981;
            --warning-orange: #F59E0B;
            --error-red: #EF4444;
        }
        
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #0B1120 0%, #1E293B 100%);
            color: #F1F5F9;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        .glass-effect { 
            background: rgba(30, 41, 59, 0.7); 
            backdrop-filter: blur(20px); 
            -webkit-backdrop-filter: blur(20px); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }
        
        .glass-effect-light { 
            background: rgba(255, 255, 255, 0.05); 
            backdrop-filter: blur(15px); 
            -webkit-backdrop-filter: blur(15px); 
            border: 1px solid rgba(255, 255, 255, 0.08); 
        }
        
        .gradient-text {
            background: linear-gradient(135deg, var(--accent-gold) 0%, #F59E0B 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .confidence-bar {
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #EF4444, #F59E0B, #10B981);
            border-radius: 3px;
            margin-top: 8px;
            overflow: hidden;
        }
        
        .confidence-fill {
            height: 100%;
            background: var(--success-green);
            transition: width 0.3s ease;
            border-radius: 3px;
        }
        
        .scanning-active {
            animation: pulse-scan 2s infinite;
            box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.7);
        }
        
        @keyframes pulse-scan {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(251, 191, 36, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(251, 191, 36, 0);
            }
        }
        
        .tile-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .tile-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(251, 191, 36, 0.1), transparent);
            transition: left 0.5s ease;
        }
        
        .tile-card:hover::before {
            left: 100%;
        }
        
        .tile-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent-gold);
        }
        
        .modal { 
            display: none; 
            position: fixed; 
            top: 0;
            left: 0;
            width: 100%; 
            height: 100%; 
            z-index: 1000; 
            align-items: center; 
            justify-content: center; 
            background: rgba(11, 17, 32, 0.95); 
            backdrop-filter: blur(12px); 
            animation: fadeIn 0.3s ease; 
            padding: 1rem;
        }
        
        .modal.show { 
            display: flex; 
        }
        
        .modal-content { 
            animation: slideUp 0.4s ease; 
            max-height: 90vh;
            overflow-y: auto;
        }
        
        @keyframes fadeIn { 
            from { opacity: 0; } 
            to { opacity: 1; } 
        }
        
        @keyframes slideUp { 
            from { transform: translateY(30px); opacity: 0; } 
            to { transform: translateY(0px); opacity: 1; } 
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-gold) 0%, var(--accent-gold-dark) 100%);
            transition: all 0.3s ease;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(251, 191, 36, 0.3);
        }
        
        .detection-canvas {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 2;
        }
        
        .status-pulse {
            animation: status-pulse 2s infinite;
        }
        
        @keyframes status-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .scrollbar-thin {
            scrollbar-width: thin;
            scrollbar-color: rgba(251, 191, 36, 0.3) transparent;
        }
        
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: rgba(251, 191, 36, 0.3);
            border-radius: 3px;
        }
    </style>
</head>
<body class="antialiased">
    <!-- Header Spacer -->
    <div class="h-16"></div>

    <main class="container mx-auto px-4 py-8">
        <!-- Hero Section -->
        <section class="text-center mb-12">
            <div class="inline-flex items-center gap-3 glass-effect px-6 py-3 rounded-full mb-6">
                <i class="fa-solid fa-robot text-amber-400 text-lg"></i>
                <span class="text-sm font-medium text-gray-300">AI-Powered Tile Recognition</span>
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
                <span class="text-white">Smart Tile</span>
                <span class="gradient-text">Scanner</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
                Advanced AI automatically detects tiles in real-time. Simply point your camera and let the system identify matching tiles instantly.
            </p>
        </section>

        <!-- Scanner Section -->
        <section class="glass-effect rounded-3xl p-6 md:p-8 mb-16 max-w-6xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-8 items-start">
                <!-- Camera Section -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-green-400 rounded-full status-pulse" id="status-indicator"></div>
                        <h2 class="text-2xl font-bold text-white">Live AI Detection</h2>
                    </div>
                    
                    <div id="camera-container" class="relative w-full aspect-video rounded-2xl overflow-hidden bg-gradient-to-br from-gray-900 to-gray-800 border-2 border-gray-700">
                        <video id="video-preview" class="w-full h-full object-cover" autoplay playsinline muted></video>
                        <canvas id="detection-canvas" class="detection-canvas w-full h-full"></canvas>
                        
                        <!-- Instructions Overlay -->
                        <div id="scan-instructions" class="absolute inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity duration-300">
                            <div class="text-center p-8 max-w-sm">
                                <div class="w-20 h-20 bg-amber-400/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-camera-search text-3xl text-amber-400"></i>
                                </div>
                                <h3 class="text-xl font-bold text-white mb-3">Point Camera at Tile</h3>
                                <p class="text-gray-300 text-sm leading-relaxed">
                                    AI will automatically detect tile patterns and enable the capture button when ready
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Tips -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="glass-effect-light rounded-xl p-4 text-center">
                            <i class="fa-solid fa-sun text-amber-400 text-lg mb-2"></i>
                            <p class="text-xs text-gray-300">Good Lighting</p>
                        </div>
                        <div class="glass-effect-light rounded-xl p-4 text-center">
                            <i class="fa-solid fa-hand-holding text-amber-400 text-lg mb-2"></i>
                            <p class="text-xs text-gray-300">Steady Camera</p>
                        </div>
                    </div>
                </div>

                <!-- Detection Panel -->
                <div class="space-y-6">
                    <!-- Confidence Meter -->
                    <div class="glass-effect rounded-2xl p-6">
                        <h3 class="text-lg font-bold text-white mb-4">Detection Confidence</h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-300">Tile Recognition</span>
                                    <span class="text-sm font-bold text-green-400" id="confidence-value">0%</span>
                                </div>
                                <div class="confidence-bar">
                                    <div class="confidence-fill" id="confidence-fill" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Cards -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="glass-effect rounded-xl p-4 text-center">
                            <i class="fa-solid fa-microchip text-amber-400 text-lg mb-2"></i>
                            <p class="text-xs text-gray-300 mb-1">AI Status</p>
                            <p class="text-white font-bold text-sm" id="detection-status">Loading</p>
                        </div>
                        <div class="glass-effect rounded-xl p-4 text-center">
                            <i class="fa-solid fa-crosshairs text-amber-400 text-lg mb-2"></i>
                            <p class="text-xs text-gray-300 mb-1">Pattern Match</p>
                            <p class="text-white font-bold text-sm" id="pattern-status">Waiting</p>
                        </div>
                    </div>

                    <!-- Capture Button -->
                    <button id="capture-btn" disabled class="w-full bg-gray-600 text-gray-400 rounded-2xl p-4 text-lg font-bold transition-all duration-300 cursor-not-allowed flex items-center justify-center gap-3">
                        <i class="fa-solid fa-camera"></i>
                        <span>Waiting for Tile Detection</span>
                    </button>

                    <!-- Detection Log -->
                    <div class="glass-effect rounded-2xl p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-bold text-white">Detection Log</h4>
                            <span class="text-xs text-gray-400" id="log-count">0 entries</span>
                        </div>
                        <div id="detection-log" class="text-xs text-gray-400 space-y-2 max-h-32 overflow-y-auto scrollbar-thin">
                            <div class="text-amber-400">System initializing...</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tile Gallery Section -->
        <section class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">
                    Available <span class="gradient-text">Tile Collection</span>
                </h2>
                <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                    Browse our complete catalog of recognizable tiles. Scan any of these patterns for instant identification.
                </p>
            </div>

            <!-- Tile Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-6 mb-12">
                <?php if (empty($tiles)): ?>
                    <div class="col-span-full text-center py-12">
                        <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-tile text-2xl text-gray-500"></i>
                        </div>
                        <p class="text-gray-400 text-lg">No tiles available in the database.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($tiles as $tile): ?>
                    <div class="tile-card glass-effect rounded-xl p-3 text-center group cursor-pointer border border-transparent hover:border-amber-400/30">
                        <div class="relative overflow-hidden rounded-lg mb-3">
                            <img 
                                src="<?php echo $tile['tile_image']; ?>" 
                                alt="<?php echo htmlspecialchars($tile['product_name']); ?>" 
                                class="w-full h-24 object-cover rounded-lg transform group-hover:scale-105 transition-transform duration-300"
                                loading="lazy"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                        <h4 class="font-semibold text-xs text-white truncate mb-1"><?php echo htmlspecialchars($tile['product_name']); ?></h4>
                        <p class="text-amber-400 font-bold text-xs">₱<?php echo number_format($tile['product_price'], 2); ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="flex justify-center items-center space-x-2">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?>" class="glass-effect-light rounded-xl px-4 py-3 hover:border-amber-400 transition-all duration-300 flex items-center gap-2 text-sm">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                        <span>Previous</span>
                    </a>
                <?php else: ?>
                    <span class="glass-effect-light rounded-xl px-4 py-3 text-gray-500 cursor-not-allowed flex items-center gap-2 text-sm">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                        <span>Previous</span>
                    </span>
                <?php endif; ?>

                <div class="flex items-center space-x-2 mx-4">
                    <?php 
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);
                    
                    for ($i = $start_page; $i <= $end_page; $i++): 
                    ?>
                        <a href="?page=<?php echo $i; ?>" class="glass-effect-light rounded-lg w-10 h-10 flex items-center justify-center transition-all duration-300 text-sm <?php echo ($i == $current_page) ? 'bg-amber-400 text-gray-900 font-bold shadow-lg' : 'hover:border-amber-400 hover:scale-105'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
                
                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo $current_page + 1; ?>" class="glass-effect-light rounded-xl px-4 py-3 hover:border-amber-400 transition-all duration-300 flex items-center gap-2 text-sm">
                        <span>Next</span>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </a>
                <?php else: ?>
                    <span class="glass-effect-light rounded-xl px-4 py-3 text-gray-500 cursor-not-allowed flex items-center gap-2 text-sm">
                        <span>Next</span>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- Success Modal -->
    <div id="success-modal" class="modal">
        <div class="modal-content glass-effect rounded-3xl max-w-md w-full mx-4 border border-green-500/20">
            <div class="p-8 text-center">
                <div id="success-content"></div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="error-modal" class="modal">
        <div class="modal-content glass-effect rounded-3xl max-w-sm w-full mx-4 border border-red-500/20">
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-triangle-exclamation text-2xl text-red-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3">Scan Failed</h3>
                <p id="error-message" class="text-gray-300 mb-6 leading-relaxed"></p>
                <button onclick="closeModal('error-modal')" class="btn-primary w-full py-3 rounded-xl font-semibold">
                    Try Again
                </button>
            </div>
        </div>
    </div>

    <script>
    class SmartTileScanner {
        constructor() {
            this.video = document.getElementById('video-preview');
            this.canvas = document.getElementById('detection-canvas');
            this.ctx = this.canvas.getContext('2d');
            this.captureBtn = document.getElementById('capture-btn');
            this.detectionLog = document.getElementById('detection-log');
            this.confidenceValue = document.getElementById('confidence-value');
            this.confidenceFill = document.getElementById('confidence-fill');
            this.detectionStatus = document.getElementById('detection-status');
            this.patternStatus = document.getElementById('pattern-status');
            this.statusIndicator = document.getElementById('status-indicator');
            this.scanInstructions = document.getElementById('scan-instructions');
            this.logCount = document.getElementById('log-count');
            
            this.model = null;
            this.stream = null;
            this.isDetecting = false;
            this.detectionInterval = null;
            this.currentConfidence = 0;
            this.tileDetected = false;
            this.logEntries = 0;
            
            this.TILE_KEYWORDS = [
                'tile', 'floor', 'wall', 'ceramic', 'porcelain', 'stone', 'marble', 
                'granite', 'mosaic', 'pattern', 'checker', 'grid', 'square', 'rectangle',
                'brick', 'paving', 'flooring'
            ];
            
            this.init();
        }

        async init() {
            this.log('Starting Smart Tile Scanner...', 'info');
            await this.loadModel();
            await this.startCamera();
            this.startDetection();
        }

        async loadModel() {
            try {
                this.log('Loading AI detection model...', 'info');
                this.updateDetectionStatus('Loading AI...');
                
                this.model = await mobilenet.load({ 
                    version: 2, 
                    alpha: 0.5 
                });
                
                this.log('AI model loaded successfully', 'success');
                this.updateDetectionStatus('Ready');
                this.updatePatternStatus('Waiting');
                
            } catch (error) {
                this.log('Error loading AI model: ' + error.message, 'error');
                this.updateDetectionStatus('Error');
            }
        }

        async startCamera() {
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { 
                        facingMode: 'environment',
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    }
                });
                
                this.video.srcObject = this.stream;
                this.log('Camera initialized successfully', 'success');
                
                this.video.addEventListener('loadedmetadata', () => {
                    this.canvas.width = this.video.videoWidth;
                    this.canvas.height = this.video.videoHeight;
                });
                
            } catch (error) {
                this.log('Camera access denied: ' + error.message, 'error');
                this.showError('Camera access is required for tile scanning. Please allow camera permissions.');
            }
        }

        startDetection() {
            this.detectionInterval = setInterval(() => {
                if (this.model && this.video.readyState === this.video.HAVE_ENOUGH_DATA) {
                    this.detectTile();
                }
            }, 1500);
        }

        async detectTile() {
            if (this.isDetecting) return;
            
            this.isDetecting = true;
            
            try {
                this.ctx.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);
                const predictions = await this.model.classify(this.canvas);
                const tileConfidence = this.analyzePredictions(predictions);
                
                this.updateConfidence(tileConfidence);
                
                if (tileConfidence >= 65) {
                    this.enableCapture();
                } else {
                    this.disableCapture();
                }
                
            } catch (error) {
                console.error('Detection error:', error);
            }
            
            this.isDetecting = false;
        }

        analyzePredictions(predictions) {
            let maxConfidence = 0;
            
            predictions.forEach(prediction => {
                const className = prediction.className.toLowerCase();
                const confidence = prediction.probability * 100;
                
                const isTileLike = this.TILE_KEYWORDS.some(keyword => 
                    className.includes(keyword)
                );
                
                if (isTileLike && confidence > maxConfidence) {
                    maxConfidence = confidence;
                    this.log(`Detected: ${prediction.className} (${confidence.toFixed(1)}%)`, 'detection');
                }
            });
            
            const patternConfidence = this.detectTilePatterns();
            return Math.max(maxConfidence, patternConfidence);
        }

        detectTilePatterns() {
            try {
                const imageData = this.ctx.getImageData(0, 0, this.canvas.width, this.canvas.height);
                const gridConfidence = this.analyzeGridPattern(imageData);
                
                if (gridConfidence > 40) {
                    this.updatePatternStatus('Pattern Found');
                    this.log(`Grid pattern detected: ${gridConfidence.toFixed(1)}%`, 'pattern');
                } else {
                    this.updatePatternStatus('Searching');
                }
                
                return gridConfidence;
            } catch (error) {
                return 0;
            }
        }

        analyzeGridPattern(imageData) {
            let edgeCount = 0;
            const data = imageData.data;
            const width = imageData.width;
            const height = imageData.height;
            const sampleSize = 15;
            
            for (let y = sampleSize; y < height - sampleSize; y += sampleSize) {
                for (let x = sampleSize; x < width - sampleSize; x += sampleSize) {
                    if (this.isEdgePoint(data, x, y, width)) {
                        edgeCount++;
                    }
                }
            }
            
            const totalSamples = Math.floor((width - 2*sampleSize) / sampleSize) * Math.floor((height - 2*sampleSize) / sampleSize);
            const edgeRatio = (edgeCount / totalSamples) * 100;
            
            return Math.min(edgeRatio * 1.5, 100);
        }

        isEdgePoint(data, x, y, width) {
            const currentIdx = (y * width + x) * 4;
            const currentBrightness = (data[currentIdx] + data[currentIdx + 1] + data[currentIdx + 2]) / 3;
            
            const offsets = [[-2, 0], [2, 0], [0, -2], [0, 2]];
            
            for (const [dx, dy] of offsets) {
                const newX = x + dx;
                const newY = y + dy;
                
                if (newX >= 0 && newX < width && newY >= 0 && newY < width) {
                    const newIdx = (newY * width + newX) * 4;
                    const newBrightness = (data[newIdx] + data[newIdx + 1] + data[newIdx + 2]) / 3;
                    
                    if (Math.abs(currentBrightness - newBrightness) > 25) {
                        return true;
                    }
                }
            }
            
            return false;
        }

        updateConfidence(confidence) {
            this.currentConfidence = confidence;
            this.confidenceValue.textContent = `${confidence.toFixed(1)}%`;
            this.confidenceFill.style.width = `${confidence}%`;
            
            if (confidence >= 65) {
                this.confidenceFill.style.background = '#10B981';
                this.confidenceValue.className = 'text-sm font-bold text-green-400';
            } else if (confidence >= 40) {
                this.confidenceFill.style.background = '#F59E0B';
                this.confidenceValue.className = 'text-sm font-bold text-yellow-400';
            } else {
                this.confidenceFill.style.background = '#EF4444';
                this.confidenceValue.className = 'text-sm font-bold text-red-400';
            }
        }

        enableCapture() {
            if (!this.tileDetected) {
                this.tileDetected = true;
                this.captureBtn.disabled = false;
                this.captureBtn.className = 'w-full btn-primary rounded-2xl p-4 text-lg font-bold transition-all duration-300 flex items-center justify-center gap-3 scanning-active cursor-pointer';
                this.captureBtn.innerHTML = '<i class="fa-solid fa-camera"></i><span>Capture Tile</span>';
                this.scanInstructions.style.opacity = '0';
                
                setTimeout(() => {
                    this.scanInstructions.style.display = 'none';
                }, 300);
                
                this.log('Tile detected! Capture enabled', 'success');
                this.captureBtn.onclick = () => this.captureImage();
            }
        }

        disableCapture() {
            if (this.tileDetected) {
                this.tileDetected = false;
                this.captureBtn.disabled = true;
                this.captureBtn.className = 'w-full bg-gray-600 text-gray-400 rounded-2xl p-4 text-lg font-bold transition-all duration-300 cursor-not-allowed flex items-center justify-center gap-3';
                this.captureBtn.innerHTML = '<i class="fa-solid fa-camera"></i><span>Waiting for Tile Detection</span>';
                this.scanInstructions.style.display = 'flex';
                
                setTimeout(() => {
                    this.scanInstructions.style.opacity = '1';
                }, 50);
            }
        }

        async captureImage() {
            if (!this.tileDetected) return;
            
            this.log('Capturing image for matching...', 'info');
            this.captureBtn.disabled = true;
            this.captureBtn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i><span>Matching Tile...</span>';
            
            try {
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = this.video.videoWidth;
                canvas.height = this.video.videoHeight;
                context.drawImage(this.video, 0, 0, canvas.width, canvas.height);
                
                const blob = await new Promise(resolve => {
                    canvas.toBlob(resolve, 'image/jpeg', 0.9);
                });

                const formData = new FormData();
                formData.append('image', blob, 'tile_scan.jpg');
                
                const response = await fetch('../connection/ar_match_fast.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                this.handleScanResult(result);
                
            } catch (error) {
                this.showError('Capture failed: ' + error.message);
                this.log('Capture error: ' + error.message, 'error');
            } finally {
                this.enableCapture();
            }
        }

        handleScanResult(result) {
            if (result.success && result.products && result.products.length > 0) {
                this.showSuccess(result.products);
                this.log(`Tile matched: ${result.products[0].tile_name}`, 'success');
            } else {
                this.showError(result.message || 'No matching tile found in database.');
                this.log('No match found for captured tile', 'warning');
            }
        }

        updateDetectionStatus(status) {
            this.detectionStatus.textContent = status;
        }

        updatePatternStatus(status) {
            this.patternStatus.textContent = status;
        }

        log(message, type = 'info') {
            this.logEntries++;
            const timestamp = new Date().toLocaleTimeString();
            const typeIcon = {
                'info': 'fa-info-circle text-blue-400',
                'success': 'fa-check-circle text-green-400',
                'error': 'fa-exclamation-circle text-red-400',
                'warning': 'fa-exclamation-triangle text-yellow-400',
                'detection': 'fa-search text-amber-400',
                'pattern': 'fa-th-large text-purple-400'
            }[type] || 'fa-info-circle text-gray-400';

            const logEntry = `
                <div class="flex items-start gap-2 py-1">
                    <i class="fa-solid ${typeIcon} text-xs mt-0.5 flex-shrink-0"></i>
                    <span class="flex-1">${message}</span>
                    <span class="text-gray-500 text-xs flex-shrink-0">${timestamp}</span>
                </div>
            `;
            
            this.detectionLog.insertAdjacentHTML('afterbegin', logEntry);
            this.logCount.textContent = `${this.logEntries} entries`;
            
            // Keep only last 8 entries
            const entries = this.detectionLog.querySelectorAll('div');
            if (entries.length > 8) {
                entries[entries.length - 1].remove();
            }
        }

        showSuccess(products) {
            const topProduct = products[0];
            const modal = document.getElementById('success-modal');
            const content = document.getElementById('success-content');
            
            const confidenceColor = topProduct.confidence >= 80 ? 'text-green-400' : 
                                  topProduct.confidence >= 60 ? 'text-yellow-400' : 'text-orange-400';
            
            content.innerHTML = `
                <div class="w-20 h-20 bg-green-400/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-check text-3xl text-green-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3">Tile Matched!</h3>
                <div class="space-y-2 mb-4">
                    <p class="text-white text-lg font-semibold">${topProduct.tile_name}</p>
                    <p class="text-gray-300 text-sm">${topProduct.tile_description || 'No description available'}</p>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-gray-400">Confidence</span>
                    <span class="${confidenceColor} font-bold">${topProduct.confidence}%</span>
                </div>
                <div class="bg-amber-400/10 rounded-xl p-4 mb-6">
                    <p class="text-gray-400 text-sm mb-1">Price</p>
                    <p class="text-amber-400 text-2xl font-bold">₱${parseFloat(topProduct.tile_price).toLocaleString()}</p>
                </div>
                <button onclick="closeModal('success-modal')" class="btn-primary w-full py-3 rounded-xl font-semibold text-lg">
                    Continue Scanning
                </button>
            `;
            
            modal.classList.add('show');
        }

        showError(message) {
            const modal = document.getElementById('error-modal');
            const errorMessage = document.getElementById('error-message');
            
            errorMessage.textContent = message;
            modal.classList.add('show');
        }
    }

    // Global functions
    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('show');
    }

    // Initialize scanner
    document.addEventListener('DOMContentLoaded', () => {
        new SmartTileScanner();
    });

    // Handle page visibility changes
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            console.log('Page is hidden');
        } else {
            console.log('Page is visible');
        }
    });
    </script>
</body>
</html>