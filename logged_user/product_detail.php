<?php
session_start();
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Additional security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\' https: data: \'unsafe-inline\' \'unsafe-eval\'; img-src \'self\' https: data:; style-src \'self\' https: \'unsafe-inline\'; script-src \'self\' https: \'unsafe-inline\' \'unsafe-eval\';');

if (!isset($_SESSION['logged_in'])) {
    header('Location: ../connection/tresspass.php');
    exit();
}

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product details
require_once '../connection/connection.php';
$product = null;
$related_products = [];
$reviews = [];
$average_rating = 0;
$rating_distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
$total_reviews = 0;
$user_can_review = false;
$has_purchased = false;

if ($product_id > 0) {
    // Get selected branch id
    $branch_id = isset($_SESSION['branch_id']) ? intval($_SESSION['branch_id']) : 1;
    // Fetch main product for branch
    $stmt = $conn->prepare('
     SELECT p.*, 
         GROUP_CONCAT(DISTINCT td.design_name) as designs,
         GROUP_CONCAT(DISTINCT ts.size_name) as sizes,
         GROUP_CONCAT(DISTINCT tf.finish_name) as finishes,
         GROUP_CONCAT(DISTINCT tc.classification_name) as classifications,
         GROUP_CONCAT(DISTINCT tbf.best_for_name) as best_for,
         pb.stock_count as stock_count
        FROM products p
        LEFT JOIN product_branches pb ON p.product_id = pb.product_id
        LEFT JOIN product_designs pd ON p.product_id = pd.product_id
        LEFT JOIN tile_designs td ON pd.design_id = td.design_id
        LEFT JOIN product_sizes ps ON p.product_id = ps.product_id
        LEFT JOIN tile_sizes ts ON ps.size_id = ts.size_id
        LEFT JOIN product_finishes pf ON p.product_id = pf.product_id
        LEFT JOIN tile_finishes tf ON pf.finish_id = tf.finish_id
        LEFT JOIN product_classifications pc ON p.product_id = pc.product_id
        LEFT JOIN tile_classifications tc ON pc.classification_id = tc.classification_id
        LEFT JOIN product_best_for pbf ON p.product_id = pbf.product_id
        LEFT JOIN best_for_categories tbf ON pbf.best_for_id = tbf.best_for_id
        WHERE p.product_id = ? AND p.is_archived = 0 AND pb.branch_id = ?
        GROUP BY p.product_id
    ');
    $stmt->execute([$product_id, $branch_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fetch all possible values for each attribute
    $all_designs = $conn->query('SELECT design_name FROM tile_designs ORDER BY design_name')->fetchAll(PDO::FETCH_COLUMN);
    $all_sizes = $conn->query('SELECT size_name FROM tile_sizes ORDER BY size_name')->fetchAll(PDO::FETCH_COLUMN);
    $all_finishes = $conn->query('SELECT finish_name FROM tile_finishes ORDER BY finish_name')->fetchAll(PDO::FETCH_COLUMN);
    $all_classifications = $conn->query('SELECT classification_name FROM tile_classifications ORDER BY classification_name')->fetchAll(PDO::FETCH_COLUMN);
    $all_best_for = $conn->query('SELECT best_for_name FROM best_for_categories ORDER BY best_for_name')->fetchAll(PDO::FETCH_COLUMN);

    if ($product) {
        // Convert image blob to base64
        if (!empty($product['product_image'])) {
            $product['product_image'] = 'data:image/jpeg;base64,' . base64_encode($product['product_image']);
        } else {
            $product['product_image'] = '../images/user/tile1.jpg';
        }
        // Convert comma-separated values to arrays
    $product['designs'] = $product['designs'] ? explode(',', $product['designs']) : [];
        $product['sizes'] = $product['sizes'] ? explode(',', $product['sizes']) : [];
        $product['finishes'] = $product['finishes'] ? explode(',', $product['finishes']) : [];
        $product['classifications'] = $product['classifications'] ? explode(',', $product['classifications']) : [];
        $product['best_for'] = $product['best_for'] ? explode(',', $product['best_for']) : [];
    // Ensure stock_count is available and numeric
    $product['stock_count'] = isset($product['stock_count']) ? intval($product['stock_count']) : 0;
        
        // Fetch product reviews
        $review_stmt = $conn->prepare('
            SELECT pr.*, u.full_name 
            FROM product_reviews pr 
            LEFT JOIN users u ON pr.user_id = u.id 
            WHERE pr.product_id = ?
            ORDER BY pr.created_at DESC
        ');
        $review_stmt->execute([$product_id]);
        $reviews = $review_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate average rating and distribution
        if (!empty($reviews)) {
            $total_rating = 0;
            $total_reviews = count($reviews);
            
            foreach ($reviews as $review) {
                $rating = intval($review['rating']);
                $total_rating += $rating;
                if (isset($rating_distribution[$rating])) {
                    $rating_distribution[$rating]++;
                }
            }
            
            $average_rating = $total_reviews > 0 ? round($total_rating / $total_reviews, 1) : 0;
        }
        
        // Check if current user can review this product
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            
            // Check if user has purchased this product
            $purchase_stmt = $conn->prepare('
                SELECT COUNT(*) FROM orders o 
                JOIN order_items oi ON o.order_id = oi.order_id 
                WHERE o.user_id = ? AND oi.product_id = ? AND o.order_status = "completed"
            ');
            $purchase_stmt->execute([$user_id, $product_id]);
            $has_purchased = $purchase_stmt->fetchColumn() > 0;
            
            // Check if user already reviewed this product
            $reviewed_stmt = $conn->prepare('
                SELECT COUNT(*) FROM product_reviews 
                WHERE user_id = ? AND product_id = ?
            ');
            $reviewed_stmt->execute([$user_id, $product_id]);
            $already_reviewed = $reviewed_stmt->fetchColumn() == 0;
            
            $user_can_review = $has_purchased && $already_reviewed;
        }
        
        // Fetch related products
        $related_stmt = $conn->prepare('
            SELECT p.*, 
                   GROUP_CONCAT(DISTINCT td.design_name) as designs
            FROM products p
            LEFT JOIN product_designs pd ON p.product_id = pd.product_id
            LEFT JOIN tile_designs td ON pd.design_id = td.design_id
            WHERE p.product_id != ? AND p.is_archived = 0 
            AND (p.is_popular = 1 OR p.is_best_seller = 1)
            GROUP BY p.product_id
            ORDER BY p.is_best_seller DESC, p.is_popular DESC, p.created_at DESC
            LIMIT 8
        ');
        $related_stmt->execute([$product_id]);
        $related_products = $related_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Process related product images
        foreach ($related_products as &$related_product) {
            if (!empty($related_product['product_image'])) {
                $related_product['product_image'] = 'data:image/jpeg;base64,' . base64_encode($related_product['product_image']);
            } else {
                $related_product['product_image'] = '../images/user/tile1.jpg';
            }
        }
    }
}

include '../includes/headeruser.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product ? htmlspecialchars($product['product_name']) : 'Product Not Found'; ?> - Rich Anne Lea Tiles Trading</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/three@0.153.0/build/three.min.js"></script>
    
    <style>
        /* Product-specific styles only */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .glass-effect-product {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .gradient-border-product {
            background: linear-gradient(135deg, #7d310a, #cf8756, #e8a56a);
            padding: 1px;
            border-radius: 16px;
        }
        
        .product-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.12);
        }
        
        .attribute-chip {
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .attribute-chip.active-design {
            background: linear-gradient(90deg, #ffe5d0 0%, #ffd6b3 100%);
            color: #7d310a;
            border: 2px solid #cf8756;
        }
        .attribute-chip.inactive-design {
            background: #f8f5f2;
            color: #bbb;
            border: 1px solid #eee;
        }
        .attribute-chip.active-size {
            background: linear-gradient(90deg, #e8f7fa 0%, #c3eaff 100%);
            color: #0a7d7d;
            border: 2px solid #3bb3b3;
        }
        .attribute-chip.inactive-size {
            background: #f8f5f2;
            color: #bbb;
            border: 1px solid #eee;
        }
        .attribute-chip.active-finish {
            background: linear-gradient(90deg, #f7e8fa 0%, #eac3ff 100%);
            color: #7d0a7d;
            border: 2px solid #b33bb3;
        }
        .attribute-chip.inactive-finish {
            background: #f8f5f2;
            color: #bbb;
            border: 1px solid #eee;
        }
        .attribute-chip.active-classification {
            background: linear-gradient(90deg, #e8fae8 0%, #c3ffc3 100%);
            color: #0a7d31;
            border: 2px solid #3bb33b;
        }
        .attribute-chip.inactive-classification {
            background: #f8f5f2;
            color: #bbb;
            border: 1px solid #eee;
        }
        .attribute-chip.active-bestfor {
            background: linear-gradient(90deg, #fffbe8 0%, #fff3c3 100%);
            color: #b38b00;
            border: 2px solid #ffe066;
        }
        .attribute-chip.inactive-bestfor {
            background: #f8f5f2;
            color: #bbb;
            border: 1px solid #eee;
        }
        .attribute-chip .chip-icon {
            margin-right: 4px;
            opacity: 0.7;
        }
        .attribute-chip[title]:hover::after {
            content: attr(title);
            position: absolute;
            left: 50%;
            top: 110%;
            transform: translateX(-50%);
            background: #333;
            color: #fff;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            white-space: nowrap;
            z-index: 10;
            opacity: 0.95;
        }
        
        .modal-overlay-product {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
        /* Modal box and stock badge styles */
        .modal-box {
            transform: translateY(6px) scale(0.98);
            opacity: 0;
            transition: transform 220ms ease, opacity 220ms ease;
            background: linear-gradient(145deg, #ffffff, #f9f9f9);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .modal-box.show {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
        .modal-box input[type="number"] {
            appearance: textfield;
            -moz-appearance: textfield;
            -webkit-appearance: textfield;
        }
        .modal-box input[type="number"]::-webkit-outer-spin-button,
        .modal-box input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .quantity-btn {
            transition: all 0.2s ease;
            padding: 4px;
            border-radius: 50%;
        }
        .quantity-btn:not(:disabled):hover {
            transform: scale(1.15);
            background: rgba(125,49,10,0.1);
        }
        .quantity-btn:disabled {
            cursor: not-allowed;
        }
        .stock-badge {
            font-size: 0.85rem;
            padding: 0.45rem 0.65rem;
            border-radius: 10px;
            background: linear-gradient(145deg, #fff7ed, #fff1e6);
            border: 1px solid rgba(125,49,10,0.1);
            color: #7d310a;
            box-shadow: 0 4px 12px rgba(125,49,10,0.08);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }
        .stock-badge.out {
            background: linear-gradient(145deg, #fff5f5, #fff1f1);
            color: #b91c1c;
            border-color: rgba(185,28,28,0.15);
            box-shadow: 0 4px 12px rgba(185,28,28,0.08);
        }
        .stock-badge i {
            font-size: 0.9em;
            margin-top: -1px;
        }
        
        /* Product-specific color variables */
        .text-primary-product { color: #7d310a; }
        .text-secondary-product { color: #cf8756; }
        .bg-primary-product { background-color: #7d310a; }
        .bg-secondary-product { background-color: #cf8756; }
        .border-primary-product { border-color: #7d310a; }
        .border-secondary-product { border-color: #cf8756; }
        
        .hover\:bg-primary-product:hover { background-color: #7d310a; }
        .hover\:text-primary-product:hover { color: #7d310a; }
        
        /* Review Section Styles */
        .review-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #f0e6df;
            transition: all 0.3s ease;
        }
        
        .review-card:hover {
            box-shadow: 0 4px 20px rgba(125, 49, 10, 0.08);
            transform: translateY(-2px);
        }
        
        .rating-stars {
            display: flex;
            gap: 2px;
        }
        
        .rating-stars .star {
            color: #d1d5db;
            font-size: 14px;
        }
        
        .rating-stars .star.filled {
            color: #fbbf24;
        }
        
        .rating-bar {
            background: #f3f4f6;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .rating-fill {
            background: linear-gradient(90deg, #fbbf24, #f59e0b);
            height: 8px;
            border-radius: 10px;
            transition: width 0.5s ease;
        }
        
        /* Heatmap animation styles */
        .heatmap-update {
            animation: heatmapPulse 1s ease-in-out;
        }
        
        @keyframes heatmapPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        @media (max-width: 768px) {
            .mobile-padding {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .mobile-flex-col {
                flex-direction: column;
            }
        }
    </style>
</head>
<body class="font-inter text-gray-900" style="min-height:100vh;background:#fff;">
    <?php if (!isset($_SESSION['logged_in'])): ?>
        <script>window.location.href='../connection/tresspass.php';</script>
    <?php exit(); endif; ?>
    
    <!-- Product Detail Section -->
    <section class="py-8 md:py-12 mobile-padding">
        <div class="max-w-7xl mx-auto">
            <!-- Elegant Breadcrumb -->
            <nav class="mb-6 md:mb-8">
                <ol class="flex items-center space-x-2 text-sm text-gray-500">
                    <li><a href="landing_page.php" class="hover:text-gold transition-colors duration-200 flex items-center gap-1">
                        <i class="fas fa-home text-xs"></i> Home
                    </a></li>
                    <li><i class="fas fa-chevron-right text-xs opacity-50"></i></li>
                    <li><a href="#premium-tiles" class="hover:text-gold transition-colors duration-200">Products</a></li>
                    <li><i class="fas fa-chevron-right text-xs opacity-50"></i></li>
                    <li class="text-gold font-medium flex items-center gap-1">
                        <i class="fas fa-cube text-xs"></i>
                        <?php echo $product ? htmlspecialchars($product['product_name']) : 'Product Not Found'; ?>
                    </li>
                </ol>
            </nav>
            
            <?php if ($product): ?>
            <!-- Main Product Container -->
            <div class="gradient-border-product animate-fade-in-up">
                <div class="glass-effect-product rounded-[15px] overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                        <!-- Product Image Section -->
                        <div class="space-y-4">
                            <!-- Main Image -->
                            <div class="relative bg-gradient-to-br from-primary/5 to-secondary/10 rounded-xl p-4 flex items-center justify-center h-72 md:h-80 overflow-hidden">
                                <img src="<?php echo $product['product_image']; ?>" 
                                     alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                     class="max-h-full max-w-full object-contain relative z-10">
                                <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-transparent opacity-20"></div>
                            </div>
                            
                            <!-- 3D View -->
                            <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-lg mb-4">
                                <h3 class="text-base font-semibold text-primary-product mb-3 flex items-center">
                                    <i class="fas fa-cube mr-2"></i> 3D Interactive View
                                </h3>
                                <div id="product3DView" class="w-full h-48 bg-gray-50 rounded-lg flex items-center justify-center border border-gray-200">
                                    <!-- 3D viewer will be rendered here -->
                                </div>
                                <p class="text-xs text-gray-500 mt-2 text-center">Drag to rotate • Scroll to zoom</p>
                            </div>

                            <!-- Heatmap Stats Panel -->
                            <?php
                            // Get total sold (completed orders)
                            $sold_stmt = $conn->prepare('SELECT SUM(oi.quantity) FROM order_items oi JOIN orders o ON oi.order_id = o.order_id WHERE oi.product_id = ? AND o.order_status = "completed"');
                            $sold_stmt->execute([$product_id]);
                            $total_sold = $sold_stmt->fetchColumn();
                            $total_sold = $total_sold ? $total_sold : 0;

                            // Get total completed orders
                            $completed_stmt = $conn->prepare('SELECT COUNT(DISTINCT o.order_id) FROM orders o JOIN order_items oi ON oi.order_id = o.order_id WHERE oi.product_id = ? AND o.order_status = "completed"');
                            $completed_stmt->execute([$product_id]);
                            $completed_orders = $completed_stmt->fetchColumn();
                            $completed_orders = $completed_orders ? $completed_orders : 0;

                            // Get total times added to cart
                            $cart_stmt = $conn->prepare('SELECT SUM(quantity) FROM cart_items WHERE product_id = ?');
                            $cart_stmt->execute([$product_id]);
                            $cart_adds = $cart_stmt->fetchColumn();
                            $cart_adds = $cart_adds ? $cart_adds : 0;
                            ?>
                            <div class="bg-gradient-to-br from-yellow-100 via-orange-100 to-pink-100 rounded-xl p-4 border border-yellow-200 shadow-lg mb-4 animate-fade-in-up">
                                <h3 class="text-base font-semibold text-orange-700 mb-3 flex items-center">
                                    <i class="fas fa-fire mr-2"></i> Product Popularity Heatmap
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                                    <div class="bg-white rounded-lg p-4 shadow flex flex-col items-center justify-center border border-orange-200">
                                        <span class="text-3xl font-bold text-orange-600 mb-1"><i class="fas fa-shopping-bag"></i> <span id="total-sold"><?php echo $total_sold; ?></span></span>
                                        <span class="text-xs text-gray-600">Total Sold</span>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 shadow flex flex-col items-center justify-center border border-green-200">
                                        <span class="text-3xl font-bold text-green-600 mb-1"><i class="fas fa-check-circle"></i> <span id="completed-orders"><?php echo $completed_orders; ?></span></span>
                                        <span class="text-xs text-gray-600">Orders Completed</span>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 shadow flex flex-col items-center justify-center border border-pink-200">
                                        <span class="text-3xl font-bold text-pink-600 mb-1"><i class="fas fa-cart-plus"></i> <span id="cart-adds"><?php echo $cart_adds; ?></span></span>
                                        <span class="text-xs text-gray-600">Times Added to Cart</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Product Details -->
                        <div class="space-y-4">
                            <!-- Header with Badges -->
                            <div class="relative">
                                <h1 class="text-xl md:text-2xl font-bold text-primary-product mb-2 leading-tight">
                                    <?php echo htmlspecialchars($product['product_name']); ?>
                                </h1>
                                <div class="text-xl md:text-2xl font-bold text-secondary-product mb-3">
                                    ₱<?php echo number_format($product['product_price'], 2); ?>
                                </div>
                                
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <?php if ($product['is_popular'] == 1): ?>
                                        <span class="inline-flex items-center gap-1 bg-primary-product text-white px-2 py-1 rounded-lg text-xs font-medium">
                                            <i class="fas fa-fire text-xs"></i> Popular
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($product['is_best_seller'] == 1): ?>
                                        <span class="inline-flex items-center gap-1 bg-secondary-product text-white px-2 py-1 rounded-lg text-xs font-medium">
                                            <i class="fas fa-trophy text-xs"></i> Bestseller
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Product Description -->
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <h3 class="text-sm font-semibold text-primary-product mb-2 flex items-center">
                                    <i class="fas fa-align-left mr-2"></i> Description
                                </h3>
                                <p class="text-gray-700 text-sm leading-relaxed"><?php echo htmlspecialchars($product['product_description'] ?: 'No description available.'); ?></p>
                            </div>
                            
                            <!-- Product Attributes -->
                            <div class="space-y-3">
                                <!-- Tile Design -->
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <h4 class="text-sm font-medium text-primary-product mb-2 flex items-center">
                                        <i class="fas fa-palette mr-2"></i> Tile Design
                                    </h4>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($all_designs as $design): ?>
                                            <?php $isActive = in_array($design, $product['designs']); ?>
                                            <span class="attribute-chip <?php echo $isActive ? 'active-design' : 'inactive-design'; ?> px-3 py-1 rounded-lg text-xs font-semibold border" title="<?php echo $isActive ? 'Selected design' : 'Not selected'; ?>">
                                                <i class="fas fa-palette chip-icon"></i>
                                                <?php echo htmlspecialchars($design); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <!-- Tile Size -->
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <h4 class="text-sm font-medium text-primary-product mb-2 flex items-center">
                                        <i class="fas fa-ruler-combined mr-2"></i> Tile Size
                                    </h4>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($all_sizes as $size): ?>
                                            <?php $isActive = in_array($size, $product['sizes']); ?>
                                            <span class="attribute-chip <?php echo $isActive ? 'active-size' : 'inactive-size'; ?> px-3 py-1 rounded-lg text-xs font-semibold border" title="<?php echo $isActive ? 'Selected size' : 'Not selected'; ?>">
                                                <i class="fas fa-ruler-combined chip-icon"></i>
                                                <?php echo htmlspecialchars($size); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <!-- Tile Finishes -->
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <h4 class="text-sm font-medium text-primary-product mb-2 flex items-center">
                                        <i class="fas fa-wand-magic-sparkles mr-2"></i> Tile Finishes
                                    </h4>
                                    <div class="flex flex-wrap gap-2">
                                        <?php
                                            $finishIcons = [
                                                'Glossy' => 'fa-gem',
                                                'Matte' => 'fa-grip-lines',
                                                'Rough' => 'fa-mountain',
                                                'Textured' => 'fa-layer-group',
                                                'Polished' => 'fa-wand-magic-sparkles',
                                                'Satin' => 'fa-droplet',
                                                'Stone' => 'fa-cube',
                                                'Wood' => 'fa-tree',
                                                'Metallic' => 'fa-coins',
                                                'Other' => 'fa-star',
                                            ];
                                        ?>
                                        <?php foreach ($all_finishes as $finish): ?>
                                            <?php $isActive = in_array($finish, $product['finishes']); ?>
                                            <?php $iconClass = isset($finishIcons[$finish]) ? $finishIcons[$finish] : $finishIcons['Other']; ?>
                                            <span class="attribute-chip <?php echo $isActive ? 'active-finish' : 'inactive-finish'; ?> px-3 py-1 rounded-lg text-xs font-semibold border flex items-center" title="<?php echo $isActive ? 'Selected finish' : 'Not selected'; ?>">
                                                <i class="fas <?php echo $iconClass; ?> chip-icon"></i>
                                                <?php echo htmlspecialchars($finish); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <!-- Tile Classification -->
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <h4 class="text-sm font-medium text-primary-product mb-2 flex items-center">
                                        <i class="fas fa-tags mr-2"></i> Tile Classification
                                    </h4>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($all_classifications as $classification): ?>
                                            <?php $isActive = in_array($classification, $product['classifications']); ?>
                                            <span class="attribute-chip <?php echo $isActive ? 'active-classification' : 'inactive-classification'; ?> px-3 py-1 rounded-lg text-xs font-semibold border" title="<?php echo $isActive ? 'Selected classification' : 'Not selected'; ?>">
                                                <i class="fas fa-tags chip-icon"></i>
                                                <?php echo htmlspecialchars($classification); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <!-- Best For -->
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <h4 class="text-sm font-medium text-primary-product mb-2 flex items-center">
                                        <i class="fas fa-star mr-2"></i> Best For
                                    </h4>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($all_best_for as $best): ?>
                                            <?php $isActive = in_array($best, $product['best_for']); ?>
                                            <span class="attribute-chip <?php echo $isActive ? 'active-bestfor' : 'inactive-bestfor'; ?> px-3 py-1 rounded-lg text-xs font-semibold border" title="<?php echo $isActive ? 'Recommended' : 'Not recommended'; ?>">
                                                <i class="fas fa-star chip-icon"></i>
                                                <?php echo htmlspecialchars($best); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row gap-3 pt-3">
                                <button type="button" id="openQtyModal" class="flex-1 py-3 bg-primary-product text-white rounded-lg font-semibold text-sm transition-all duration-200 hover:bg-primary/90 shadow-lg flex items-center justify-center gap-2">
                                    <i class="fa fa-shopping-cart"></i> 
                                    Add to Cart
                                </button>
                                <button onclick="history.back()" class="flex-1 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold text-sm transition-all duration-200 hover:bg-gray-300 shadow-lg flex items-center justify-center gap-2">
                                    <i class="fa fa-arrow-left"></i> 
                                    Back to Products
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Reviews Section -->
            <section class="mt-12 md:mt-16">
                <div class="gradient-border-product animate-fade-in-up">
                    <div class="glass-effect-product rounded-[15px] overflow-hidden">
                        <div class="p-6 md:p-8">
                            <!-- Reviews Header -->
                            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
                                <div class="flex-1">
                                    <h2 class="text-xl md:text-2xl font-semibold text-primary-product mb-4 flex items-center gap-3">
                                        <i class="fas fa-star text-secondary-product text-2xl"></i>
                                        Customer Reviews
                                        <?php if ($total_reviews > 0): ?>
                                            <span class="bg-primary-product text-white px-3 py-1 rounded-full text-sm font-medium">
                                                <?php echo $total_reviews; ?>
                                            </span>
                                        <?php endif; ?>
                                    </h2>
                                    
                                    <!-- Average Rating -->
                                    <div class="flex items-center gap-6 flex-wrap">
                                        <div class="flex items-center gap-4">
                                            <div class="text-4xl font-bold text-primary-product"><?php echo $average_rating; ?></div>
                                            <div class="flex flex-col gap-1">
                                                <div class="rating-stars">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <span class="star <?php echo $i <= floor($average_rating) ? 'filled' : ''; ?>">
                                                            <i class="fas fa-star"></i>
                                                        </span>
                                                    <?php endfor; ?>
                                                </div>
                                                <span class="text-sm text-gray-600">Based on <?php echo $total_reviews; ?> review<?php echo $total_reviews != 1 ? 's' : ''; ?></span>
                                            </div>
                                        </div>
                                        
                                        <!-- Write Review Button - Redirects to myProfile.php -->
                                        <?php if ($user_can_review): ?>
                                        <a href="myProfile.php?section=to-review&product_id=<?php echo $product_id; ?>" class="bg-primary-product text-white px-6 py-3 rounded-lg font-semibold text-sm transition-all duration-200 hover:bg-primary/90 shadow-lg flex items-center gap-2">
                                            <i class="fas fa-edit"></i> Write Review
                                        </a>
                                        <?php elseif (isset($_SESSION['user_id'])): ?>
                                        <div class="text-sm text-gray-500 flex items-center gap-2 bg-gray-100 px-4 py-2 rounded-lg">
                                            <i class="fas fa-info-circle text-primary-product"></i>
                                            <?php echo $has_purchased ? 'You have already reviewed this product' : 'Purchase this product to leave a review'; ?>
                                        </div>
                                        <?php else: ?>
                                        <a href="../connection/login.php" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold text-sm transition-all duration-200 hover:bg-gray-300 shadow-lg flex items-center gap-2">
                                            <i class="fas fa-sign-in-alt"></i> Login to Review
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                                <!-- Rating Distribution -->
                                <div class="lg:col-span-1">
                                    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                                        <h3 class="text-lg font-semibold text-primary-product mb-4 flex items-center gap-2">
                                            <i class="fas fa-chart-bar"></i> Rating Breakdown
                                        </h3>
                                        <div class="space-y-3">
                                            <?php for ($stars = 5; $stars >= 1; $stars--): 
                                                $percentage = $total_reviews > 0 ? ($rating_distribution[$stars] / $total_reviews) * 100 : 0;
                                            ?>
                                                <div class="flex items-center gap-3">
                                                    <div class="flex items-center gap-2 w-16">
                                                        <span class="text-sm font-medium text-gray-700"><?php echo $stars; ?></span>
                                                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                                                    </div>
                                                    <div class="flex-1 rating-bar">
                                                        <div class="rating-fill" style="width: <?php echo $percentage; ?>%"></div>
                                                    </div>
                                                    <span class="text-sm text-gray-500 w-12 text-right"><?php echo $rating_distribution[$stars]; ?></span>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Review Summary Stats -->
                                    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm mt-4">
                                        <h3 class="text-lg font-semibold text-primary-product mb-4 flex items-center gap-2">
                                            <i class="fas fa-chart-pie"></i> Review Summary
                                        </h3>
                                        <div class="space-y-3 text-sm">
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                                <span class="text-gray-600">Total Reviews</span>
                                                <span class="font-semibold text-primary-product"><?php echo $total_reviews; ?></span>
                                            </div>
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                                <span class="text-gray-600">Average Rating</span>
                                                <span class="font-semibold text-primary-product"><?php echo $average_rating; ?>/5</span>
                                            </div>
                                            <div class="flex justify-between items-center py-2">
                                                <span class="text-gray-600">5 Star Reviews</span>
                                                <span class="font-semibold text-primary-product"><?php echo $rating_distribution[5]; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reviews List -->
                                <div class="lg:col-span-3">
                                    <?php if (!empty($reviews)): ?>
                                        <div class="space-y-6">
                                            <?php foreach ($reviews as $review): ?>
                                                <div class="review-card p-6 animate-fade-in-up">
                                                    <div class="flex items-start justify-between mb-4">
                                                        <div class="flex items-center gap-4">
                                                            <div class="w-12 h-12 bg-gradient-to-br from-primary-product to-secondary-product rounded-full flex items-center justify-center text-black font-semibold text-lg shadow-lg">
                                                                <?php echo strtoupper(substr($review['full_name'] ?: 'User', 0, 1)); ?>
                                                            </div>
                                                            <div>
                                                                <h4 class="font-semibold text-gray-800 text-lg"><?php echo htmlspecialchars($review['full_name'] ?: 'Anonymous User'); ?></h4>
                                                                <div class="flex items-center gap-3 mt-1">
                                                                    <div class="rating-stars">
                                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                            <span class="star <?php echo $i <= $review['rating'] ? 'filled' : ''; ?>">
                                                                                <i class="fas fa-star"></i>
                                                                            </span>
                                                                        <?php endfor; ?>
                                                                    </div>
                                                                    <span class="text-sm text-gray-500"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="space-y-4">
                                                        <?php if (!empty($review['title'])): ?>
                                                            <h5 class="font-semibold text-gray-800 text-xl"><?php echo htmlspecialchars($review['title']); ?></h5>
                                                        <?php endif; ?>
                                                        
                                                        <p class="text-gray-700 leading-relaxed text-lg"><?php echo htmlspecialchars($review['review_text']); ?></p>
                                                        
                                                        <?php if (!empty($review['feedback'])): ?>
                                                            <div class="flex flex-wrap gap-2 mt-4">
                                                                <?php 
                                                                $feedbacks = explode(',', $review['feedback']);
                                                                foreach ($feedbacks as $feedback): 
                                                                    $feedback = trim($feedback);
                                                                    if (!empty($feedback)):
                                                                ?>
                                                                    <span class="bg-gradient-to-r from-primary-product/10 to-secondary-product/10 text-primary-product px-4 py-2 rounded-full text-sm font-medium border border-primary-product/20">
                                                                        <i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($feedback); ?>
                                                                    </span>
                                                                <?php 
                                                                    endif;
                                                                endforeach; 
                                                                ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <?php if (isset($review['is_verified_purchase']) && $review['is_verified_purchase']): ?>
                                                        <div class="flex items-center gap-2 mt-6 pt-4 border-t border-gray-100">
                                                            <i class="fas fa-check-circle text-green-500 text-lg"></i>
                                                            <span class="text-sm text-green-600 font-semibold">Verified Purchase</span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="bg-white rounded-xl p-12 text-center border border-gray-200 shadow-sm">
                                            <div class="w-20 h-20 bg-gradient-to-br from-primary-product/10 to-secondary-product/10 rounded-full flex items-center justify-center mx-auto mb-6">
                                                <i class="fas fa-star text-3xl text-primary-product"></i>
                                            </div>
                                            <h3 class="text-2xl font-semibold text-gray-700 mb-3">No Reviews Yet</h3>
                                            <p class="text-gray-500 text-lg mb-6 max-w-md mx-auto">Be the first to share your thoughts about this amazing product!</p>
                                            <?php if ($user_can_review): ?>
                                                
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quantity Modal -->
            <div id="qtyModal" class="fixed inset-0 z-50 flex items-center justify-center modal-overlay-product hidden">
                <div id="qtyModalBox" class="modal-box bg-white rounded-xl shadow-lg p-6 w-full max-w-sm mx-4 relative border border-gray-200">
                    <button type="button" id="closeQtyModal" class="absolute top-4 right-4 text-gray-400 hover:text-primary-product text-lg focus:outline-none">
                        <i class="fa fa-times"></i>
                    </button>
                    <h2 class="text-lg font-semibold text-primary-product mb-2 flex items-center gap-2">
                        <i class="fa fa-shopping-cart"></i> Add to Cart
                    </h2>
                    <p class="text-sm text-gray-500 mb-4">Select quantity to add to your cart. We'll reserve items in your cart until checkout.</p>
                    <form action="processes/add_to_cart.php" method="POST" class="space-y-4" id="addToCartForm">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <input type="hidden" name="branch_id" value="<?php echo isset($_SESSION['branch_id']) ? intval($_SESSION['branch_id']) : 1; ?>">
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                            <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-3 relative">
                        <div class="flex-1 relative">
                            <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-2">
                                <button type="button" class="quantity-btn decrease text-gray-400 hover:text-primary-product focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed" <?php echo $product['stock_count'] <= 0 ? 'disabled' : ''; ?>>
                                    <i class="fas fa-minus-circle"></i>
                                </button>
                                <button type="button" class="quantity-btn increase text-gray-400 hover:text-primary-product focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed" <?php echo $product['stock_count'] <= 0 ? 'disabled' : ''; ?>>
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                            </div>
                            <input type="number" name="quantity" id="quantity" min="1" value="<?php echo $product['stock_count'] > 0 ? 1 : 0; ?>" required 
                                max="<?php echo $product['stock_count']; ?>" <?php echo $product['stock_count'] <= 0 ? 'disabled' : ''; ?>
                                class="w-full pl-3 pr-20 py-2 rounded-lg border border-gray-300 focus:border-primary-product focus:ring-2 focus:ring-primary/20 text-sm font-medium text-primary-product bg-white transition-all duration-200">
                        </div>
                        <div id="quantityLeft" class="stock-badge flex-shrink-0 <?php echo $product['stock_count'] <= 0 ? 'out' : ''; ?>" aria-live="polite">
                            <i class="fas <?php echo $product['stock_count'] > 0 ? 'fa-box text-primary-product' : 'fa-box-open text-red-600'; ?> mr-1"></i>
                            <?php echo $product['stock_count'] > 0 ? 'Qty left: ' . $product['stock_count'] : 'Out of stock'; ?>
                        </div>
                    </div>
                    <div id="qtyError" class="text-sm text-red-600 hidden flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Requested quantity exceeds available stock.</span>
                    </div>
                </div>
            </div>
                        <button type="submit" id="submitAddToCart" class="w-full py-3 bg-primary-product text-white rounded-lg font-semibold text-sm transition-all duration-200 hover:bg-primary/90 shadow-lg flex items-center justify-center gap-2">
                            <i class="fa fa-cart-plus"></i> Add to Cart
                        </button>
                    </form>
                </div>
            </div>

            <!-- You May Also Like Section -->
            <?php if (!empty($related_products)): ?>
            <section class="mt-12 md:mt-16">
                <div class="text-center mb-6 md:mb-8">
                    <h2 class="text-xl md:text-2xl font-semibold text-primary-product mb-2">
                        You May Also Like
                    </h2>
                    <p class="text-gray-500 text-sm max-w-2xl mx-auto">
                        Discover more premium tiles that complement your style
                    </p>
                    <div class="w-16 h-0.5 bg-primary/20 mx-auto mt-3 rounded-full"></div>
                </div>

                <!-- Related Products Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                    <?php foreach ($related_products as $related): ?>
                    <div class="product-card bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                        <div class="relative overflow-hidden">
                            <img src="<?php echo $related['product_image']; ?>" 
                                 alt="<?php echo htmlspecialchars($related['product_name']); ?>" 
                                 class="w-full h-40 object-cover transition-transform duration-300 hover:scale-105">
                            
                            <!-- Badges -->
                            <div class="absolute top-2 left-2 flex flex-col gap-1">
                                <?php if ($related['is_best_seller'] == 1): ?>
                                    <span class="bg-secondary-product text-white px-2 py-1 rounded text-xs font-medium">
                                        <i class="fas fa-trophy text-xs mr-1"></i> Bestseller
                                    </span>
                                <?php endif; ?>
                                <?php if ($related['is_popular'] == 1): ?>
                                    <span class="bg-primary-product text-white px-2 py-1 rounded text-xs font-medium">
                                        <i class="fas fa-fire text-xs mr-1"></i> Popular
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Overlay on hover -->
                            <div class="absolute inset-0 bg-gradient-to-t from-primary/80 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-3">
                                <a href="product_detail.php?id=<?php echo $related['product_id']; ?>" 
                                   class="bg-white text-primary-product px-4 py-1.5 rounded-lg font-medium text-xs transition-all hover:bg-primary-product hover:text-white">
                                    View Details
                                </a>
                            </div>
                        </div>
                        
                        <div class="p-3">
                            <h3 class="font-medium text-gray-700 mb-1 line-clamp-1 text-sm"><?php echo htmlspecialchars($related['product_name']); ?></h3>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-base font-semibold text-secondary-product">₱<?php echo number_format($related['product_price'], 2); ?></span>
                            </div>
                            
                            <?php if (!empty($related['designs'])): ?>
                                <div class="flex items-center gap-1 text-gray-500 text-xs mb-2">
                                    <i class="fas fa-palette text-primary-product"></i>
                                    <span class="line-clamp-1"><?php echo htmlspecialchars(explode(',', $related['designs'])[0]); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <button class="w-full bg-primary-product text-white py-2 rounded-lg font-medium text-xs transition-all duration-200 hover:bg-primary/90 shadow-lg flex items-center justify-center gap-1">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <?php else: ?>
            <!-- Product Not Found -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center max-w-md mx-auto">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-2xl text-primary-product"></i>
                </div>
                <h2 class="text-lg font-semibold text-primary-product mb-2">Product Not Found</h2>
                <p class="text-gray-500 text-sm mb-4">The product you're looking for doesn't exist or has been removed.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="landing_page.php" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-product text-white rounded-lg font-medium text-sm transition-all duration-200 hover:bg-primary/90">
                        <i class="fa fa-home"></i> Back to Home
                    </a>
                    <a href="landing_page.php#premium-tiles" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium text-sm transition-all duration-200 hover:bg-gray-300">
                        <i class="fa fa-cube"></i> Browse Products
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <script>
        // Toast notification function (only called when needed)
        function showToast(message) {
            let toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 z-[100] bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg font-semibold text-lg flex items-center gap-3 animate-fade-in-up';
            toast.innerHTML = '<i class="fas fa-check-circle text-xl"></i> ' + message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 600);
            }, 3000);
        }

        // Function to update heatmap data
        function updateHeatmapData() {
            // Make an AJAX request to get updated heatmap data
            fetch('processes/get_heatmap_data.php?product_id=<?php echo $product_id; ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the cart adds count with animation
                        const cartAddsElement = document.getElementById('cart-adds');
                        if (cartAddsElement) {
                            cartAddsElement.textContent = data.cart_adds;
                            cartAddsElement.parentElement.parentElement.classList.add('heatmap-update');
                            setTimeout(() => {
                                cartAddsElement.parentElement.parentElement.classList.remove('heatmap-update');
                            }, 1000);
                        }
                        
                        // Update other heatmap data if needed
                        const totalSoldElement = document.getElementById('total-sold');
                        const completedOrdersElement = document.getElementById('completed-orders');
                        
                        if (totalSoldElement && data.total_sold !== undefined) {
                            totalSoldElement.textContent = data.total_sold;
                        }
                        
                        if (completedOrdersElement && data.completed_orders !== undefined) {
                            completedOrdersElement.textContent = data.completed_orders;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error updating heatmap data:', error);
                });
        }

        // Quantity Modal logic & AJAX add to cart
        document.addEventListener('DOMContentLoaded', function() {
            var qtyModal = document.getElementById('qtyModal');
            var qtyModalBox = document.getElementById('qtyModalBox');
            var openBtn = document.getElementById('openQtyModal');
            var closeBtn = document.getElementById('closeQtyModal');
            var addToCartForm = document.getElementById('addToCartForm');
            var quantityInput = document.getElementById('quantity');
            var quantityLeftEl = document.getElementById('quantityLeft');
            var qtyError = document.getElementById('qtyError');
            var submitBtn = document.getElementById('submitAddToCart');

            function openModal() {
                if (!qtyModal) return;
                qtyModal.classList.remove('hidden');
                // show animated box slightly after overlay
                setTimeout(function() {
                    qtyModalBox && qtyModalBox.classList.add('show');
                }, 10);
                // reset quantity
                if (quantityInput) {
                    quantityInput.value = <?php echo $product['stock_count'] > 0 ? 1 : 0; ?>;
                    var max = parseInt(quantityInput.getAttribute('max') || '0', 10);
                    quantityInput.setAttribute('max', max);
                    quantityLeftEl && (quantityLeftEl.textContent = <?php echo $product['stock_count']; ?> > 0 ? 'Qty left: ' + <?php echo $product['stock_count']; ?> : 'Out of stock');
                    // update submit state
                    if (<?php echo $product['stock_count']; ?> <= 0) {
                        submitBtn && (submitBtn.disabled = true);
                        qtyError && (qtyError.classList.remove('hidden'), qtyError.textContent = 'This product is currently out of stock.');
                    } else {
                        submitBtn && (submitBtn.disabled = false);
                        qtyError && qtyError.classList.add('hidden');
                    }
                }
            }

            function closeModal() {
                if (!qtyModal) return;
                qtyModalBox && qtyModalBox.classList.remove('show');
                // wait for animation
                setTimeout(function() {
                    qtyModal.classList.add('hidden');
                }, 220);
            }

            if (openBtn && qtyModal) {
                openBtn.addEventListener('click', openModal);
            }

            if (closeBtn && qtyModal) {
                closeBtn.addEventListener('click', closeModal);
            }

            // Close modal on outside click
            qtyModal && qtyModal.addEventListener('click', function(e) {
                if (e.target === qtyModal) {
                    closeModal();
                }
            });

            // Validate quantity input against available stock live
            if (quantityInput) {
                quantityInput.addEventListener('input', function() {
                    var val = parseInt(quantityInput.value || '0', 10);
                    var max = parseInt(quantityInput.getAttribute('max') || '0', 10);
                    if (isNaN(val) || val < 1) {
                        quantityInput.value = 1;
                        val = 1;
                    }
                    if (val > max) {
                        qtyError && (qtyError.classList.remove('hidden'), qtyError.textContent = 'Requested quantity exceeds available stock.');
                        submitBtn && (submitBtn.disabled = true);
                    } else {
                        qtyError && qtyError.classList.add('hidden');
                        submitBtn && (submitBtn.disabled = false);
                    }
                });
            }

            // Handle quantity buttons
            const decreaseBtn = document.querySelector('.quantity-btn.decrease');
            const increaseBtn = document.querySelector('.quantity-btn.increase');

            if (decreaseBtn && increaseBtn && quantityInput) {
                decreaseBtn.addEventListener('click', () => {
                    const currentValue = parseInt(quantityInput.value || '0', 10);
                    if (currentValue > 1) {
                        quantityInput.value = currentValue - 1;
                        quantityInput.dispatchEvent(new Event('input'));
                    }
                });

                increaseBtn.addEventListener('click', () => {
                    const currentValue = parseInt(quantityInput.value || '0', 10);
                    const max = parseInt(quantityInput.getAttribute('max') || '0', 10);
                    if (currentValue < max) {
                        quantityInput.value = currentValue + 1;
                        quantityInput.dispatchEvent(new Event('input'));
                    }
                });

                // Update button states on quantity change
                function updateButtonStates() {
                    const val = parseInt(quantityInput.value || '0', 10);
                    const max = parseInt(quantityInput.getAttribute('max') || '0', 10);
                    
                    decreaseBtn.disabled = val <= 1;
                    increaseBtn.disabled = val >= max;
                    
                    decreaseBtn.style.opacity = val <= 1 ? '0.5' : '1';
                    increaseBtn.style.opacity = val >= max ? '0.5' : '1';

                    submitBtn.disabled = val > max || val < 1;
                    submitBtn.style.opacity = (val > max || val < 1) ? '0.5' : '1';
                    
                    if (val > max) {
                        quantityInput.classList.add('border-red-500');
                        qtyError.classList.remove('hidden');
                    } else {
                        quantityInput.classList.remove('border-red-500');
                        qtyError.classList.add('hidden');
                    }
                }

                quantityInput.addEventListener('input', updateButtonStates);
                updateButtonStates(); // Initial state
            }

            // AJAX submit for add to cart
            if (addToCartForm) {
                addToCartForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    // final validation before submit
                    var qty = parseInt(quantityInput ? quantityInput.value || '0' : '0', 10);
                    var max = parseInt(quantityInput ? quantityInput.getAttribute('max') || '0' : '0', 10);
                    if (qty > max) {
                        qtyError && (qtyError.classList.remove('hidden'), qtyError.textContent = 'Requested quantity exceeds available stock.');
                        return;
                    }

                    var formData = new FormData(addToCartForm);
                    fetch('processes/add_to_cart.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            closeModal();
                            showToast('Product Successfully Added to your Cart');
                            // Update the heatmap data after successful add to cart
                            updateHeatmapData();
                        } else {
                            showToast(data.error || 'Failed to add to cart');
                        }
                    })
                    .catch(() => {
                        showToast('Failed to add to cart');
                    });
                });
            }
        });

        // Enhanced 3D viewer using Three.js
        function init3DViewer() {
            const container = document.getElementById('product3DView');
            if (!container) return;
            
            const width = container.clientWidth;
            const height = container.clientHeight;
            
            // Remove previous canvas if any
            while (container.firstChild) container.removeChild(container.firstChild);
            
            // Create renderer
            const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
            renderer.setClearColor(0xfdfaf7, 1);
            renderer.setSize(width, height);
            renderer.shadowMap.enabled = true;
            container.appendChild(renderer.domElement);
            
            // Create scene
            const scene = new THREE.Scene();
            
            // Add shadow plane
            const shadowGeo = new THREE.PlaneGeometry(2, 0.6);
            const shadowMat = new THREE.ShadowMaterial({ opacity: 0.2 });
            const shadow = new THREE.Mesh(shadowGeo, shadowMat);
            shadow.position.y = -0.3;
            shadow.rotation.x = -Math.PI / 2;
            shadow.receiveShadow = true;
            scene.add(shadow);
            
            // Create camera
            const camera = new THREE.PerspectiveCamera(30, width / height, 0.1, 1000);
            camera.position.set(0, 0.1, 2);
            
            // Enhanced lighting
            const ambient = new THREE.AmbientLight(0xffffff, 1);
            scene.add(ambient);
            
            const dirLight = new THREE.DirectionalLight(0xffffff, 1);
            dirLight.position.set(2, 3, 4);
            dirLight.castShadow = true;
            scene.add(dirLight);
            
            // Create cube geometry for tile
            const geometry = new THREE.BoxGeometry(0.8, 0.8, 0.05);
            
            // Load product image as texture
            const loader = new THREE.TextureLoader();
            const productImage = '<?php echo $product ? $product["product_image"] : ""; ?>';
            
            if (productImage && productImage.startsWith('data:image')) {
                loader.load(productImage, function(texture) {
                    // Create enhanced materials
                    const materials = [
                        new THREE.MeshStandardMaterial({ color: 0xf9f5f2, roughness: 0.3 }),
                        new THREE.MeshStandardMaterial({ color: 0xf9f5f2, roughness: 0.3 }),
                        new THREE.MeshStandardMaterial({ color: 0xf9f5f2, roughness: 0.3 }),
                        new THREE.MeshStandardMaterial({ color: 0xf9f5f2, roughness: 0.3 }),
                        new THREE.MeshStandardMaterial({ map: texture, roughness: 0.2 }),
                        new THREE.MeshStandardMaterial({ color: 0xf9f5f2, roughness: 0.3 })
                    ];
                    
                    const cube = new THREE.Mesh(geometry, materials);
                    cube.castShadow = true;
                    cube.position.y = 0.1;
                    scene.add(cube);
                    
                    // Mouse/touch controls
                    let isDragging = false;
                    let prevX = 0;
                    let prevY = 0;
                    let rotationY = Math.PI / 8;
                    let rotationX = -Math.PI / 16;
                    let autoRotate = true;
                    
                    function handleInteractionStart(x, y) {
                        isDragging = true;
                        prevX = x;
                        prevY = y;
                        autoRotate = false;
                    }
                    
                    function handleInteractionMove(x, y) {
                        if (isDragging) {
                            const dx = x - prevX;
                            const dy = y - prevY;
                            rotationY += dx * 0.01;
                            rotationX += dy * 0.01;
                            rotationX = Math.max(-Math.PI / 2.5, Math.min(Math.PI / 2.5, rotationX));
                            prevX = x;
                            prevY = y;
                        }
                    }
                    
                    function handleInteractionEnd() {
                        isDragging = false;
                        setTimeout(() => autoRotate = true, 2000);
                    }
                    
                    // Mouse events
                    container.addEventListener('mousedown', (e) => handleInteractionStart(e.clientX, e.clientY));
                    window.addEventListener('mouseup', handleInteractionEnd);
                    window.addEventListener('mousemove', (e) => handleInteractionMove(e.clientX, e.clientY));
                    
                    // Touch events
                    container.addEventListener('touchstart', (e) => {
                        if (e.touches.length === 1) {
                            handleInteractionStart(e.touches[0].clientX, e.touches[0].clientY);
                        }
                    });
                    window.addEventListener('touchend', handleInteractionEnd);
                    window.addEventListener('touchmove', (e) => {
                        if (isDragging && e.touches.length === 1) {
                            handleInteractionMove(e.touches[0].clientX, e.touches[0].clientY);
                        }
                    });
                    
                    // Animation loop
                    function animate() {
                        requestAnimationFrame(animate);
                        if (autoRotate && !isDragging) {
                            rotationY += 0.002;
                        }
                        cube.rotation.y = rotationY;
                        cube.rotation.x = rotationX;
                        renderer.render(scene, camera);
                    }
                    animate();
                    
                    // Handle window resize
                    function handleResize() {
                        const newWidth = container.clientWidth;
                        const newHeight = container.clientHeight;
                        camera.aspect = newWidth / newHeight;
                        camera.updateProjectionMatrix();
                        renderer.setSize(newWidth, newHeight);
                    }
                    window.addEventListener('resize', handleResize);
                    
                }, undefined, function() {
                    // Fallback if texture loading fails
                    container.innerHTML = `
                        <div class="text-center text-gray-500">
                            <i class="fas fa-exclamation-triangle text-lg mb-1"></i>
                            <p class="text-xs">3D view not available</p>
                        </div>
                    `;
                });
            } else {
                // No product image available
                container.innerHTML = `
                    <div class="text-center text-gray-500">
                        <i class="fas fa-image text-lg mb-1"></i>
                        <p class="text-xs">No product image available</p>
                    </div>
                `;
            }
        }
        
        // Initialize 3D viewer when page loads
        document.addEventListener('DOMContentLoaded', function() {
            init3DViewer();
        });
    </script>
</body>
</html>