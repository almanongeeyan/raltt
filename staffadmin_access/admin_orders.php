<?php
session_start();
include '../includes/sidebar.php';

// Database connection
require_once '../connection/connection.php';

// Get branch_id from session
$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;

// Initialize variables
$orders = [];
$orderStats = [
    'total' => 0,
    'pending' => 0,
    'paid' => 0,
    'processing' => 0,
    'ready_for_pickup' => 0,
    'completed' => 0,
    'cancelled' => 0,
    'otw' => 0,
    'to_receive' => 0
];
$error_message = '';

try {
    // Build query based on branch access
    $query = "SELECT DISTINCT o.*, b.branch_name, u.full_name as customer_name, 
            u.phone_number as customer_phone, u.email as customer_email, u.full_address as customer_full_address
        FROM orders o 
        JOIN branches b ON o.branch_id = b.branch_id 
        JOIN users u ON o.user_id = u.id 
        WHERE 1=1";
    
    $params = [];
    
    if ($branch_id) {
        $query .= " AND o.branch_id = ?";
        $params[] = $branch_id;
    }
    
    $query .= " ORDER BY o.order_date DESC";
    
    $stmt = $db_connection->prepare($query);
    $stmt->execute($params);
    $rawOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Filter orders based on date and status
    $today = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d');
    
    $userRole = isset($_SESSION['user_role']) ? strtoupper($_SESSION['user_role']) : '';
    foreach ($rawOrders as $order) {
        $orderDate = (new DateTime($order['order_date'], new DateTimeZone('Asia/Manila')))->format('Y-m-d');
        $orderStatus = $order['order_status'];
        $isPickUpOrder = strtolower($order['payment_method']) === 'pick_up';
        // If user is DRIVER, skip pick up orders
        if ($userRole === 'DRIVER' && $isPickUpOrder) {
            continue;
        }
        // Always show orders from today
        if ($orderDate === $today) {
            $orders[] = $order;
            continue;
        }
        // For previous days, only show if status is NOT completed or cancelled
        if ($orderDate < $today) {
            if (!in_array($orderStatus, ['completed', 'cancelled'])) {
                $orders[] = $order;
            }
            continue;
        }
        // Future dates (if any) - show all
        $orders[] = $order;
    }

    // Get order items for each order
    foreach ($orders as $index => $order) {
        $itemsQuery = "SELECT oi.*, p.product_name, p.product_image 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.product_id 
                       WHERE oi.order_id = ?";
        $itemsStmt = $db_connection->prepare($itemsQuery);
        $itemsStmt->execute([$order['order_id']]);
        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Process product images
        foreach ($items as $itemIndex => $item) {
            if (!empty($item['product_image'])) {
                $items[$itemIndex]['product_image'] = base64_encode($item['product_image']);
            }
        }
        
        $orders[$index]['items'] = $items;

        // Count stats
        $orderStats['total']++;
        if (isset($orderStats[$order['order_status']])) {
            $orderStats[$order['order_status']]++;
        }
    }

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $error_message = "Unable to load orders. Please try again later.";
}

// Helper functions
function formatOrderStatus($status) {
    $statusMap = [
        'pending' => 'Pending',
        'paid' => 'Paid',
        'processing' => 'Processing',
        'ready_for_pickup' => 'Ready for Pick-Up',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'otw' => 'Out for Delivery',
        'to_receive' => 'To Receive'
    ];
    return $statusMap[$status] ?? ucfirst(str_replace('_', ' ', $status));
}

function formatPaymentMethod($method) {
    $methodMap = [
        'gcash' => 'GCash',
        'cod' => 'Cash on Delivery',
        'pick_up' => 'Pick Up'
    ];
    return $methodMap[$method] ?? $method;
}

function getStatusFlow($paymentMethod, $currentStatus) {
    $paymentMethod = strtolower($paymentMethod);
    $flow = [];
    
    if ($paymentMethod === 'pick_up') {
        $flow = ['pending', 'processing', 'ready_for_pickup', 'completed'];
    } elseif ($paymentMethod === 'gcash') {
        $flow = ['paid', 'processing', 'otw', 'completed'];
    } elseif ($paymentMethod === 'cod') {
        $flow = ['pending', 'processing', 'otw', 'completed'];
    }

    if (!in_array($currentStatus, $flow)) {
        $flow[] = $currentStatus;
    }
    
    return $flow;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            display: flex;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fef8f4 0%, #f9f5f2 100%);
        }
        .main-content-wrapper { flex: 1; padding-left: 0; transition: padding-left 0.3s ease; }
        @media (min-width: 768px) { .main-content-wrapper { padding-left: 250px; } }
        .fade-in { animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        .fade-out { animation: fadeOut 0.5s ease-in-out forwards; }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; height: 0; padding: 0; margin: 0; border: 0; } }

        .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; justify-content: center; min-width: 100px; }
        .status-pending { background: #fef3c7; color: #92400e; border: 1px solid #fedf89; }
        .status-paid { background: #dbeafe; color: #1e40af; border: 1px solid #b2ddff; }
        .status-processing { background: #f0f9ff; color: #0d75bc; border: 1px solid #b9e6fe; }
        .status-ready_for_pickup { background: #fffbeb; color: #d97706; border: 1px solid #fed7aa; }
        .status-completed { background: #dcfce7; color: #166534; border: 1px solid #abefc6; }
        .status-cancelled { background: #fee2e2; color: #991b1b; border: 1px solid #fecdc9; }
        .status-otw { background: #f3e8ff; color: #7c3aed; border: 1px solid #d8b4fe; }
        .status-to_receive { background: #fef3c7; color: #92400e; border: 1px solid #fedf89; }

        .payment-badge { padding: 4px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 500; }
        .payment-gcash { background: #e0f2fe; color: #0369a1; }
        .payment-cod { background: #fef3c7; color: #92400e; }
        .payment-pick_up { background: #f3e8ff; color: #7c3aed; }

        .order-card { background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(125, 49, 10, 0.08); transition: all 0.3s ease; border: 1px solid #f0e6df; border-left: 4px solid transparent; overflow: hidden; }
        .order-card:hover { box-shadow: 0 4px 20px rgba(125, 49, 10, 0.12); transform: translateY(-2px); }
        .order-card.pending { border-left-color: #92400e; }
        .order-card.paid { border-left-color: #1e40af; }
        .order-card.processing { border-left-color: #0d75bc; }
        .order-card.ready_for_pickup { border-left-color: #d97706; }
        .order-card.completed { border-left-color: #166534; }
        .order-card.cancelled { border-left-color: #991b1b; }
        .order-card.otw { border-left-color: #7c3aed; }
        .order-card.to_receive { border-left-color: #92400e; }

        .stats-card { transition: all 0.3s ease; border-radius: 12px; overflow: hidden; }
        .stats-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08); }
        .filter-btn.active { background-color: #7d310a; color: white; }
        
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            position: fixed;
            inset: 0;
            z-index: 2000 !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            animation: modalSlideIn 0.3s ease-out;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(125, 49, 10, 0.15);
            z-index: 2100 !important;
            margin: auto;
        }
        @keyframes modalSlideIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }

        .loading-spinner { display: inline-block; width: 16px; height: 16px; border: 2px solid rgba(0, 0, 0, 0.1); border-radius: 50%; border-top-color: #333; animation: spin 1s ease-in-out infinite; }
        .status-badge .loading-spinner { border: 2px solid currentColor; border-right-color: transparent !important; border-top-color: currentColor !important; opacity: 0.5; }
        .status-update-btn .loading-spinner { border: 2px solid currentColor; border-right-color: transparent !important; border-top-color: currentColor !important; opacity: 0.5; margin-right: 4px; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .action-btn { padding: 8px 16px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; transition: all 0.2s ease; border: none; cursor: pointer; display: flex; align-items: center; gap: 6px; }
        .btn-view { background: #3b82f6; color: white; }
        .btn-view:hover { background: #2563eb; transform: translateY(-1px); }
        .btn-print { background: #10b981; color: white; }
        .btn-print:hover { background: #059669; transform: translateY(-1px); }

        .order-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .status-update-btn { padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 500; border: none; cursor: pointer; transition: all 0.2s ease; background: #f8fafc; color: #374151; border: 1px solid #e5e7eb; display: inline-flex; align-items: center; justify-content: center; }
        
        .status-update-btn:not(:disabled):hover { background: #7d310a; color: white; border-color: #7d310a; transform: translateY(-1px); }
        .status-update-btn.active { background: #7d310a; color: white; border-color: #7d310a; }
        .status-update-btn:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
        .status-update-btn.active:disabled { opacity: 1; }
        .status-update-btn:not(.active):disabled { opacity: 0.4; }
        .status-update-btn:disabled:hover { background: #f8fafc; color: #374151; border-color: #e5e7eb; }
        .status-update-btn.active:disabled:hover { background: #7d310a; color: white; border-color: #7d310a; }
        
        .status-update-btn.is-loading { background: #e5e7eb; color: #374151; opacity: 0.7; cursor: wait; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen" data-user-role="<?php echo isset($_SESSION['user_role']) ? htmlspecialchars($_SESSION['user_role']) : ''; ?>">
    <div class="main-content-wrapper">
        <main class="min-h-screen">
            <div class="max-w-7xl mx-auto py-8 px-4">
                <div class="mb-8 fade-in">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-clipboard-list mr-3 text-blue-600"></i>Orders Dashboard
                    </h1>
                    <p class="text-gray-600 mt-2">Manage and track orders across all branches</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="stats-card bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center">
                            <div class="rounded-full bg-blue-100 p-3 mr-4">
                                <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Total Orders</p>
                                <p class="text-2xl font-bold text-gray-800" id="total-orders"><?php echo $orderStats['total']; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stats-card bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center">
                            <div class="rounded-full bg-amber-100 p-3 mr-4">
                                <i class="fas fa-clock text-amber-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Processing</p>
                                <p class="text-2xl font-bold text-gray-800" id="processing-orders"><?php echo $orderStats['processing']; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stats-card bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center">
                            <div class="rounded-full bg-orange-100 p-3 mr-4">
                                <i class="fas fa-box text-orange-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Ready for Pickup</p>
                                <p class="text-2xl font-bold text-gray-800" id="pickup-orders"><?php echo $orderStats['ready_for_pickup']; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stats-card bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center">
                            <div class="rounded-full bg-purple-100 p-3 mr-4">
                                <i class="fas fa-store text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Your Branch</p>
                                <p class="text-lg font-bold text-gray-800">
                                    <?php 
                                    if ($branch_id) {
                                        $this_branch_name = 'Unknown Branch';
                                        if (!empty($orders)) {
                                            $first_order_with_name = array_filter($orders, function($o) { return !empty($o['branch_name']); });
                                            if (!empty($first_order_with_name)) {
                                                $this_branch_name = htmlspecialchars(reset($first_order_with_name)['branch_name']);
                                            }
                                        }
                                        echo $this_branch_name;
                                    } else {
                                        echo 'All Branches';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6 fade-in">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex flex-wrap gap-2">
                            <button class="filter-btn px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition active" data-status="all">
                                All Orders
                            </button>
                            <button class="filter-btn px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition" data-status="pending">
                                Pending
                            </button>
                            <button class="filter-btn px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition" data-status="paid">
                                Paid
                            </button>
                            <button class="filter-btn px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition" data-status="processing">
                                Processing
                            </button>
                            <button class="filter-btn px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition" data-status="ready_for_pickup">
                                Ready for Pickup
                            </button>
                            <button class="filter-btn px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition" data-status="completed">
                                Completed
                            </button>
                            <button class="filter-btn px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition" data-status="cancelled">
                                Cancelled
                            </button>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <input type="text" id="search-orders" placeholder="Search orders..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 w-full md:w-64">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4" id="orders-container">
                    <?php if (!empty($error_message)): ?>
                        <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
                            <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-3"></i>
                            <h3 class="text-lg font-semibold text-red-800 mb-2">Error Loading Orders</h3>
                            <p class="text-red-600"><?php echo $error_message; ?></p>
                        </div>
                    <?php elseif (empty($orders)): ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center" id="no-orders-message-initial">
                            <i class="fas fa-box-open text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">No Orders Found</h3>
                            <p class="text-gray-500">There are no orders for your branch at the moment.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <?php
                            $statusClass = 'status-' . $order['order_status'];
                            $orderCardClass = 'order-card ' . $order['order_status'];
                            $paymentClass = 'payment-' . strtolower($order['payment_method']);
                            $statusFlow = getStatusFlow($order['payment_method'], $order['order_status']);
                            $currentStatusIndex = array_search($order['order_status'], $statusFlow);
                            if ($currentStatusIndex === false) {
                                $currentStatusIndex = count($statusFlow) - 1;
                            }
                            // Check if already reported by this driver for this order
                            $alreadyReported = false;
                            if (isset($_SESSION['user_role']) && strtoupper($_SESSION['user_role']) === 'DRIVER' && isset($_SESSION['user_id'])) {
                                $reportCheckStmt = $db_connection->prepare("SELECT warning_id FROM buyer_warnings WHERE user_id = ? AND order_id = ? AND staff_user_id = ? LIMIT 1");
                                $reportCheckStmt->execute([$order['user_id'], $order['order_id'], $_SESSION['user_id']]);
                                $alreadyReported = $reportCheckStmt->fetch() ? true : false;
                            }
                            ?>
                            <div class="<?php echo $orderCardClass; ?> p-6 fade-in" 
                                 id="order-<?php echo $order['order_id']; ?>" 
                                 data-status="<?php echo $order['order_status']; ?>" 
                                 data-order-id="<?php echo $order['order_id']; ?>">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-4 mb-3">
                                            <h3 class="text-lg font-semibold text-gray-800">
                                                Order #<?php echo htmlspecialchars($order['order_reference']); ?>
                                            </h3>
                                            <span class="<?php echo $statusClass; ?> status-badge" id="status-<?php echo $order['order_id']; ?>">
                                                <?php echo formatOrderStatus($order['order_status']); ?>
                                            </span>
                                            <span class="<?php echo $paymentClass; ?> payment-badge">
                                                <?php echo formatPaymentMethod($order['payment_method']); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-500">Customer:</span>
                                                <p class="font-medium"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Date:</span>
                                                <p class="font-medium"><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Branch:</span>
                                                <p class="font-medium"><?php echo htmlspecialchars($order['branch_name']); ?></p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Total:</span>
                                                <p class="font-medium text-green-600">₱<?php echo number_format($order['total_amount'], 2); ?></p>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Order Items:</h4>
                                            <div class="space-y-2">
                                                <?php foreach ($order['items'] as $item): ?>
                                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                                        <div class="flex items-center">
                                                            <?php if (!empty($item['product_image'])): ?>
                                                                <img src="data:image/jpeg;base64,<?php echo $item['product_image']; ?>" 
                                                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                                                     class="w-10 h-10 rounded-lg object-cover mr-3">
                                                            <?php else: ?>
                                                                <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                                                                    <i class="fas fa-box text-gray-400"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div>
                                                                <p class="font-medium text-sm"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                                                <p class="text-xs text-gray-500">Qty: <?php echo $item['quantity']; ?> × ₱<?php echo number_format($item['unit_price'], 2); ?></p>
                                                            </div>
                                                        </div>
                                                        <p class="font-semibold text-sm">₱<?php echo number_format($item['quantity'] * $item['unit_price'], 2); ?></p>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Quick Status Update:</h4>
                                            <div class="flex flex-wrap gap-2" id="quick-status-<?php echo $order['order_id']; ?>">
                                                <?php
                                                $userRole = isset($_SESSION['user_role']) ? strtoupper($_SESSION['user_role']) : '';
                                                foreach ($statusFlow as $index => $nextStatus):
                                                    $isCurrent = ($index === $currentStatusIndex);
                                                    $isNext = ($index === $currentStatusIndex + 1);
                                                    $isPast = ($index < $currentStatusIndex);
                                                    $isDisabled = !$isNext;
                                                    $isActive = ($isCurrent || $isPast);

                                                    // COD: cashier->processing, encoder->otw, driver->completed
                                                    // GCASH: cashier->processing, encoder->otw, driver->completed
                                                    // Pick Up: cashier->processing, encoder->ready_for_pickup, cashier->completed
                                                    $canPress = false;
                                                    $method = strtolower($order['payment_method']);
                                                    if ($method === 'cod') {
                                                        if ($nextStatus === 'processing' && $userRole === 'CASHIER') $canPress = true;
                                                        if ($nextStatus === 'otw' && $userRole === 'ENCODER') $canPress = true;
                                                        if ($nextStatus === 'completed' && $userRole === 'DRIVER') $canPress = true;
                                                    } elseif ($method === 'gcash') {
                                                        if ($nextStatus === 'processing' && $userRole === 'CASHIER') $canPress = true;
                                                        if ($nextStatus === 'otw' && $userRole === 'ENCODER') $canPress = true;
                                                        if ($nextStatus === 'completed' && $userRole === 'DRIVER') $canPress = true;
                                                    } elseif ($method === 'pick_up') {
                                                        if ($nextStatus === 'processing' && $userRole === 'CASHIER') $canPress = true;
                                                        if ($nextStatus === 'ready_for_pickup' && $userRole === 'ENCODER') $canPress = true;
                                                        if ($nextStatus === 'completed' && $userRole === 'CASHIER') $canPress = true;
                                                    }
                                                    // Always allow past/active for visual, but only allow click if canPress
                                                    $finalDisabled = $isDisabled || (!$canPress && $isNext);
                                                ?>
                                                    <button class="status-update-btn <?php echo $isActive ? 'active' : ''; ?>"
                                                            data-order-id="<?php echo $order['order_id']; ?>"
                                                            data-status="<?php echo $nextStatus; ?>"
                                                            <?php echo $finalDisabled ? 'disabled' : ''; ?>>
                                                        <?php echo formatOrderStatus($nextStatus); ?>
                                                        <?php if ($isCurrent): ?>
                                                            <i class="fas fa-check ml-1"></i>
                                                        <?php endif; ?>
                                                    </button>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="lg:text-right">
                                        <div class="order-actions justify-end">
                                            <button class="action-btn btn-view" onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <?php if (isset($_SESSION['user_role']) && strtoupper($_SESSION['user_role']) === 'CASHIER'): ?>
                                            <button class="action-btn btn-print" onclick="printOrder(<?php echo $order['order_id']; ?>)">
                                                <i class="fas fa-print"></i> Print
                                            </button>
                                            <?php endif; ?>
                                            <?php if (isset($_SESSION['user_role']) && strtoupper($_SESSION['user_role']) === 'DRIVER'): ?>
                                            <?php
                                            $canShowDriverActions = false;
                                            // Find if the 'completed' button is enabled for this order
                                            foreach ($statusFlow as $index => $nextStatus) {
                                                $isCurrent = ($index === $currentStatusIndex);
                                                $isNext = ($index === $currentStatusIndex + 1);
                                                $isPast = ($index < $currentStatusIndex);
                                                $isDisabled = !$isNext;
                                                $method = strtolower($order['payment_method']);
                                                $canPress = false;
                                                if ($method === 'cod' || $method === 'gcash') {
                                                    if ($nextStatus === 'completed' && $userRole === 'DRIVER') $canPress = true;
                                                } elseif ($method === 'pick_up') {
                                                    if ($nextStatus === 'completed' && $userRole === 'CASHIER') $canPress = true;
                                                }
                                                $finalDisabled = $isDisabled || (!$canPress && $isNext);
                                                if ($nextStatus === 'completed' && !$finalDisabled) {
                                                    $canShowDriverActions = true;
                                                    break;
                                                }
                                            }
                                            ?>
                                            <button class="action-btn btn-view" style="<?php echo !$canShowDriverActions ? 'background:#d1d5db;color:#6b7280;cursor:not-allowed;' : 'background:#10b981;color:white;'; ?>" onclick="<?php echo !$canShowDriverActions ? 'return false;' : 'openRouteModalForOrder(' . $order['order_id'] . ')'; ?>" <?php echo !$canShowDriverActions ? 'disabled' : ''; ?>>
                                                <i class="fas fa-route"></i> Route
                                            </button>
                                            <?php
                                            // Check if already reported by this staff for this order and user
                                            $alreadyReported = false;
                                            try {
                                                $checkStmt = $db_connection->prepare("SELECT warning_id FROM buyer_warnings WHERE user_id = ? AND order_id = ? AND staff_user_id = ? LIMIT 1");
                                                $checkStmt->execute([$order['user_id'], $order['order_id'], $_SESSION['user_id']]);
                                                if ($checkStmt->fetch()) {
                                                    $alreadyReported = true;
                                                }
                                            } catch (Exception $e) {}
                                            ?>
                                            <button class="action-btn btn-view" style="<?php echo ($alreadyReported || !$canShowDriverActions) ? 'background:#d1d5db;color:#6b7280;cursor:not-allowed;' : 'background:#ef4444;color:white;'; ?>" onclick="<?php echo ($alreadyReported || !$canShowDriverActions) ? 'return false;' : 'openReportModal(' . $order['order_id'] . ', ' . $order['user_id'] . ')'; ?>" <?php echo ($alreadyReported || !$canShowDriverActions) ? 'disabled' : ''; ?>>
                                                <i class="fas fa-flag"></i> <?php echo $alreadyReported ? 'Reported' : 'Report User'; ?>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="mt-2 text-sm text-gray-500">
                                            Last updated: <span id="updated-<?php echo $order['order_id']; ?>"><?php echo date('M j, g:i A', strtotime($order['updated_at'] ?? $order['order_date'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <div id="orderDetailsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay hidden" style="display:none;">
        <div class="modal-content w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Order Details</h3>
                <button id="closeOrderModal" class="text-gray-400 hover:text-gray-600 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="orderDetailsContent" class="p-6">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>

        <!-- Report Modal -->
        <div id="reportModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay hidden" style="display:none;">
            <div class="modal-content w-full max-w-md">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-800">Report Buyer</h3>
                    <button id="closeReportModal" class="text-gray-400 hover:text-gray-600 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="reportForm" class="p-6 space-y-4">
                    <input type="hidden" id="report_order_id" name="order_id">
                    <input type="hidden" id="report_user_id" name="user_id">
                    <div>
                        <label for="warning_type" class="block text-sm font-medium text-gray-700 mb-2">Warning Type</label>
                        <select id="warning_type" name="warning_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-400 focus:border-red-400" required>
                            <option value="">Loading...</option>
                        </select>
                    </div>
                    <div>
                        <label for="warning_notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                        <textarea id="warning_notes" name="warning_notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-400 focus:border-red-400"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" id="cancelReportBtn" class="action-btn" style="background:#e5e7eb;color:#374151;">Cancel</button>
                        <button type="submit" class="action-btn" style="background:#ef4444;color:white;">Submit Report</button>
                    </div>
                </form>
            </div>
        </div>

    <!-- Leaflet CSS/JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="/raltt/js/leaflet-route-modal.js"></script>
    <script>
    // Global variables
    let currentOrders = <?php echo json_encode($orders, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    let currentFilter = 'all';
    let currentSearch = '';
    let pollingInterval = null;
    // Example: Set this to your branch/driver location (should be dynamic in real app)
    let driverLocation = { lat: 14.5995, lng: 120.9842 }; // Default: Manila
        // Route modal for drivers (uses device geolocation)
        function openRouteModalForOrder(orderId) {
            const order = currentOrders.find(o => parseInt(o.order_id) === parseInt(orderId));
            if (!order) return;
            const destAddress = order.customer_full_address || '';
            if (!destAddress) {
                showRouteModal(0, 0, 0, 0, 'No destination address found for this order.');
                return;
            }
            // Show modal immediately in loading state
            showRouteModal(0, 0, 0, 0, destAddress + ' (Loading...)');

            // Helper: always show the address string, even on error
            function showRouteError(msg) {
                showRouteModal(14.5995, 120.9842, 14.6091, 121.0223, destAddress + '<br><span style="color:#b91c1c">' + msg + '</span>');
            }

            // Helper to generate multiple simplified address versions
            function getAddressVariants(address) {
                let variants = [address];
                // Remove postal code
                let v1 = address.replace(/,?\s*\d{4,5},?/g, '').trim();
                if (v1 !== address) variants.push(v1);
                // Remove zone
                let v2 = v1.replace(/,?\s*Zone\s*\d+/gi, '').trim();
                if (v2 !== v1) variants.push(v2);
                // Remove district
                let v3 = v2.replace(/,?\s*District\s*\d+/gi, '').trim();
                if (v3 !== v2) variants.push(v3);
                // Remove Northern Manila District
                let v4 = v3.replace(/,?\s*Northern Manila District,?/gi, '').trim();
                if (v4 !== v3) variants.push(v4);
                // Remove Metro Manila
                let v5 = v4.replace(/,?\s*Metro Manila,?/gi, '').trim();
                if (v5 !== v4) variants.push(v5);
                // Remove Philippines
                let v6 = v5.replace(/,?\s*Philippines,?/gi, '').trim();
                if (v6 !== v5) variants.push(v6);
                // Remove all after city
                let v7 = v6.replace(/,?\s*Caloocan.*$/i, ', Caloocan').trim();
                if (v7 !== v6) variants.push(v7);
                // Remove all after road
                let v8 = v7.replace(/,?\s*Road.*$/i, ' Road').trim();
                if (v8 !== v7) variants.push(v8);
                return variants.filter((v, i, arr) => v && arr.indexOf(v) === i);
            }

            function tryGeocodeVariants(variants, idx) {
                if (idx >= variants.length) {
                    showRouteError('Map marker is approximate. Address not found by geocoding.');
                    return;
                }
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(variants[idx])}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const destLat = parseFloat(data[0].lat);
                            const destLng = parseFloat(data[0].lon);
                            if (navigator.geolocation) {
                                navigator.geolocation.getCurrentPosition(function(position) {
                                    const driverLat = position.coords.latitude;
                                    const driverLng = position.coords.longitude;
                                    showRouteModal(driverLat, driverLng, destLat, destLng, destAddress);
                                }, function(error) {
                                    showRouteError('Could not get your current location. Please allow location access.');
                                }, {timeout: 10000});
                            } else {
                                showRouteError('Geolocation is not supported by your browser.');
                            }
                        } else {
                            tryGeocodeVariants(variants, idx + 1);
                        }
                    })
                    .catch(() => {
                        showRouteError('Error finding destination location for this address.');
                    });
            }

            // Try all address variants for geocoding
            tryGeocodeVariants(getAddressVariants(destAddress), 0);
        }

        // DOM elements
        const ordersContainer = document.getElementById('orders-container');
        const orderDetailsModal = document.getElementById('orderDetailsModal');
        const orderDetailsContent = document.getElementById('orderDetailsContent');

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
        console.log('Orders loaded:', currentOrders.length);
        console.log('Orders data:', currentOrders);
        setupEventListeners();
        updateStats();
        setupReportModalEvents();
        startOrdersPolling();
        });

        // Set up event listeners
        function setupEventListeners() {
            // Filter buttons
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = this.dataset.status;
                    filterOrders();
                });
            });

            // Search input
            document.getElementById('search-orders').addEventListener('input', function() {
                currentSearch = this.value.toLowerCase();
                filterOrders();
            });

            // Close modal
            document.getElementById('closeOrderModal').addEventListener('click', closeOrderModal);

            // Close modal when clicking outside
            orderDetailsModal.addEventListener('click', function(e) {
                if (e.target === orderDetailsModal) closeOrderModal();
            });

            // Status update buttons event delegation
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.status-update-btn');
                if (btn && !btn.disabled) {
                    const orderId = btn.getAttribute('data-order-id');
                    const newStatus = btn.getAttribute('data-status');
                    if (orderId && newStatus) {
                        updateOrderStatus(orderId, newStatus, btn);
                    }
                }
            });

            // ESC key to close modal
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeOrderModal();
                }
            });

                // Report modal close/cancel
                document.getElementById('closeReportModal').addEventListener('click', closeReportModal);
                document.getElementById('cancelReportBtn').addEventListener('click', closeReportModal);

                // Submit report
                document.getElementById('reportForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    submitReportForm();
                });

                // Close modal when clicking outside
                document.getElementById('reportModal').addEventListener('click', function(e) {
                    if (e.target === this) closeReportModal();
                });
        }
        // Setup report modal events (for page load)
        function setupReportModalEvents() {
            // Preload warning types
            fetch('../connection/get_buyer_warning_types.php')
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('warning_type');
                    select.innerHTML = '';
                    if (data.success && Array.isArray(data.types)) {
                        select.innerHTML = '<option value="">Select warning type</option>' +
                            data.types.map(type => `<option value="${type}">${formatWarningType(type)}</option>`).join('');
                    } else {
                        select.innerHTML = '<option value="">Unable to load types</option>';
                    }
                });
        }

        // Open report modal
        function openReportModal(orderId, userId) {
            document.getElementById('report_order_id').value = orderId;
            document.getElementById('report_user_id').value = userId;
            document.getElementById('warning_type').value = '';
            document.getElementById('warning_notes').value = '';
            document.getElementById('reportModal').classList.remove('hidden');
            document.getElementById('reportModal').style.display = 'flex';
        }

        // Close report modal
        function closeReportModal() {
            document.getElementById('reportModal').classList.add('hidden');
            document.getElementById('reportModal').style.display = 'none';
        }

        // Format warning type for display
        function formatWarningType(type) {
            const map = {
                'NO_SHOW_PICKUP': 'Missed Pickup (Buyer did not arrive)',
                'BOGUS_BUYER_COD': 'Suspicious Cash on Delivery Activity',
                'FAKE_ACCOUNT': 'Potential Fake Account',
                'EXCESSIVE_CANCELLATION': 'Frequent Order Cancellations'
            };
            return map[type] || type;
        }

        // Submit report form
        function submitReportForm() {
            const orderId = document.getElementById('report_order_id').value;
            const userId = document.getElementById('report_user_id').value;
            const warningType = document.getElementById('warning_type').value;
            const warningNotes = document.getElementById('warning_notes').value;
            if (!orderId || !userId || !warningType) {
                Swal.fire({
                    icon: 'error',
                    title: 'Incomplete Report',
                    text: 'Please choose a warning type to continue.',
                    position: 'top-end',
                    toast: true,
                    timer: 3000,
                    showConfirmButton: false
                });
                return;
            }
            const formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('user_id', userId);
            formData.append('warning_type', warningType);
            formData.append('warning_notes', warningNotes);
            fetch('../connection/report_buyer_warning.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    closeReportModal();
                    // Real-time UI update: disable button and set order as cancelled
                    const orderId = document.getElementById('report_order_id').value;
                    // Disable the report button and set gray color
                    const reportBtn = document.querySelector('button[onclick*="openReportModal(' + orderId + '"]');
                    if (reportBtn) {
                        reportBtn.disabled = true;
                        reportBtn.style.background = '#d1d5db';
                        reportBtn.style.color = '#6b7280';
                        reportBtn.textContent = '';
                        reportBtn.innerHTML = '<i class="fas fa-flag"></i> Reported';
                        reportBtn.style.cursor = 'not-allowed';
                    }
                    // Set order card as cancelled
                    const orderCard = document.getElementById('order-' + orderId);
                    if (orderCard) {
                        orderCard.classList.remove('pending','paid','processing','ready_for_pickup','completed','otw','to_receive');
                        orderCard.classList.add('cancelled');
                        orderCard.dataset.status = 'cancelled';
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Report Submitted',
                        text: 'Thank you for submitting the report. The buyer warning has been recorded and the order has been cancelled.',
                        position: 'top-end',
                        toast: true,
                        timer: 2500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        text: data.error || 'Unable to submit your report at this time. Please try again later.',
                        position: 'top-end',
                        toast: true,
                        timer: 4000,
                        showConfirmButton: false
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Network Issue',
                    text: 'A network error occurred. Please check your connection and try again.',
                    position: 'top-end',
                    toast: true,
                    timer: 4000,
                    showConfirmButton: false
                });
            });
        }

        // Filter orders based on current filter and search
        function filterOrders() {
            let hasVisibleOrders = false;
            // Remove any existing "no results" message
            const noResultsMsg = document.getElementById('no-orders-message-dynamic');
            if (noResultsMsg) noResultsMsg.remove();
            // Hide the initial "no orders" message if it exists
            const initialNoOrdersMsg = document.getElementById('no-orders-message-initial');
            if (initialNoOrdersMsg) initialNoOrdersMsg.style.display = 'none';
            document.querySelectorAll('.order-card').forEach(card => {
                const status = card.dataset.status;
                const orderId = parseInt(card.dataset.orderId);
                // Find the order in currentOrders
                const order = currentOrders.find(o => parseInt(o.order_id) === orderId);
                if (!order) {
                    card.style.display = 'none';
                    return;
                }
                // Check status filter
                const statusMatch = (currentFilter === 'all') || (status === currentFilter);
                // Check search filter
                const searchMatch = (currentSearch === '') ||
                    (order.order_reference && order.order_reference.toLowerCase().includes(currentSearch)) ||
                    (order.customer_name && order.customer_name.toLowerCase().includes(currentSearch)) ||
                    (order.customer_email && order.customer_email.toLowerCase().includes(currentSearch)) ||
                    (order.items && Array.isArray(order.items) && order.items.some(item => 
                        item.product_name && item.product_name.toLowerCase().includes(currentSearch)
                    ));
                if (statusMatch && searchMatch) {
                    card.style.display = 'block';
                    hasVisibleOrders = true;
                } else {
                    card.style.display = 'none';
                }
            });
            if (!hasVisibleOrders && currentOrders.length > 0) {
                ordersContainer.innerHTML += `
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center" id="no-orders-message-dynamic">
                        <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">No Orders Found</h3>
                        <p class="text-gray-500">No orders match your current filters.</p>
                    </div>
                `;
            } else if (currentOrders.length === 0 && initialNoOrdersMsg) {
                initialNoOrdersMsg.style.display = 'block';
            }
        }

        // Real-time polling for new orders
        function startOrdersPolling() {
            if (pollingInterval) clearInterval(pollingInterval);
            pollingInterval = setInterval(fetchLatestOrders, 5000); // every 5 seconds
        }

        function fetchLatestOrders() {
            fetch('fetch_admin_orders.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success && Array.isArray(data.orders)) {
                        // Only update if there are changes
                        if (JSON.stringify(currentOrders) !== JSON.stringify(data.orders)) {
                            currentOrders = data.orders;
                            renderOrdersList();
                            updateStats();
                            filterOrders();
                        }
                    }
                })
                .catch(err => {
                    console.error('Polling error:', err);
                });
        }

        // Render orders list in the DOM
        function renderOrdersList() {
            let html = '';
            if (!currentOrders.length) {
                html = `<div class=\"bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center\" id=\"no-orders-message-initial\">\n` +
                    `<i class=\"fas fa-box-open text-4xl text-gray-300 mb-4\"></i>\n` +
                    `<h3 class=\"text-lg font-semibold text-gray-700 mb-2\">No Orders Found</h3>\n` +
                    `<p class=\"text-gray-500\">There are no orders for your branch at the moment.</p>\n` +
                `</div>`;
            } else {
                currentOrders.forEach(order => {
                    const statusClass = 'status-' + order.order_status;
                    const orderCardClass = 'order-card ' + order.order_status;
                    const paymentClass = 'payment-' + (order.payment_method || '').toLowerCase();
                    const itemsHTML = (order.items || []).map(item => `
                        <div class=\"flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0\">\n` +
                            `<div class=\"flex items-center\">\n` +
                                (item.product_image ? `<img src=\"data:image/jpeg;base64,${item.product_image}\" alt=\"${item.product_name}\" class=\"w-10 h-10 rounded-lg object-cover mr-3\">` : `<div class=\"w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center mr-3\"><i class=\"fas fa-box text-gray-400\"></i></div>`) +
                                `<div>\n` +
                                    `<p class=\"font-medium text-sm\">${item.product_name}</p>\n` +
                                    `<p class=\"text-xs text-gray-500\">Qty: ${item.quantity} × ₱${parseFloat(item.unit_price).toFixed(2)}</p>\n` +
                                `</div>\n` +
                            `</div>\n` +
                            `<p class=\"font-semibold text-sm\">₱${(parseFloat(item.unit_price) * parseInt(item.quantity)).toFixed(2)}</p>\n` +
                        `</div>`
                    ).join('');
                    html += `<div class=\"${orderCardClass} p-6 fade-in\" id=\"order-${order.order_id}\" data-status=\"${order.order_status}\" data-order-id=\"${order.order_id}\">\n` +
                        `<div class=\"flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4\">\n` +
                            `<div class=\"flex-1\">\n` +
                                `<div class=\"flex flex-wrap items-center gap-4 mb-3\">\n` +
                                    `<h3 class=\"text-lg font-semibold text-gray-800\">Order #${order.order_reference}</h3>\n` +
                                    `<span class=\"${statusClass} status-badge\" id=\"status-${order.order_id}\">${formatOrderStatusText(order.order_status)}</span>\n` +
                                    `<span class=\"${paymentClass} payment-badge\">${formatPaymentMethodText(order.payment_method)}</span>\n` +
                                `</div>\n` +
                                `<div class=\"grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm\">\n` +
                                    `<div><span class=\"text-gray-500\">Customer:</span><p class=\"font-medium\">${order.customer_name}</p></div>\n` +
                                    `<div><span class=\"text-gray-500\">Date:</span><p class=\"font-medium\">${new Date(order.order_date).toLocaleString()}</p></div>\n` +
                                    `<div><span class=\"text-gray-500\">Branch:</span><p class=\"font-medium\">${order.branch_name}</p></div>\n` +
                                    `<div><span class=\"text-gray-500\">Total:</span><p class=\"font-medium text-green-600\">₱${parseFloat(order.total_amount).toFixed(2)}</p></div>\n` +
                                `</div>\n` +
                                `<div class=\"mt-4\">\n` +
                                    `<h4 class=\"text-sm font-semibold text-gray-700 mb-2\">Order Items:</h4>\n` +
                                    `<div class=\"space-y-2\">${itemsHTML}</div>\n` +
                                `</div>\n` +
                                `<div class=\"mt-4\">\n` +
                                    `<h4 class=\"text-sm font-semibold text-gray-700 mb-2\">Quick Status Update:</h4>\n` +
                                    `<div class=\"flex flex-wrap gap-2\" id=\"quick-status-${order.order_id}\">${renderQuickStatusButtons(order)}</div>\n` +
                                `</div>\n` +
                            `</div>\n` +
                            `<div class=\"lg:text-right\">\n` +
                                `<div class=\"order-actions justify-end\">\n` +
                                    `<button class=\"action-btn btn-view\" onclick=\"viewOrderDetails(${order.order_id})\"><i class=\"fas fa-eye\"></i> View</button>\n` +
                                    `<button class=\"action-btn btn-print\" onclick=\"printOrder(${order.order_id})\"><i class=\"fas fa-print\"></i> Print</button>\n` +
                                `</div>\n` +
                                `<div class=\"mt-2 text-sm text-gray-500\">Last updated: <span id=\"updated-${order.order_id}\">${new Date(order.updated_at || order.order_date).toLocaleString()}</span></div>\n` +
                            `</div>\n` +
                        `</div>\n` +
                    `</div>`;
                });
            }
            ordersContainer.innerHTML = html;
        }

        // Render quick status buttons for each order
        function renderQuickStatusButtons(order) {
            const statusFlow = getStatusFlow(order.payment_method, order.order_status);
            const currentStatusIndex = statusFlow.indexOf(order.order_status);
            const userRole = (document.body.getAttribute('data-user-role') || '').toUpperCase();
            let buttonsHTML = '';
            statusFlow.forEach((status, index) => {
                const isCurrent = (index === currentStatusIndex);
                const isNext = (index === currentStatusIndex + 1);
                const isPast = (index < currentStatusIndex);
                let canPress = false;
                const method = (order.payment_method || '').toLowerCase();
                if (method === 'cod') {
                    if (status === 'processing' && userRole === 'CASHIER') canPress = true;
                    if (status === 'otw' && userRole === 'ENCODER') canPress = true;
                    if (status === 'completed' && userRole === 'DRIVER') canPress = true;
                } else if (method === 'gcash') {
                    if (status === 'processing' && userRole === 'CASHIER') canPress = true;
                    if (status === 'otw' && userRole === 'ENCODER') canPress = true;
                    if (status === 'completed' && userRole === 'DRIVER') canPress = true;
                } else if (method === 'pick_up') {
                    if (status === 'processing' && userRole === 'CASHIER') canPress = true;
                    if (status === 'ready_for_pickup' && userRole === 'ENCODER') canPress = true;
                    if (status === 'completed' && userRole === 'CASHIER') canPress = true;
                }
                const isDisabled = !isNext || (!canPress && isNext);
                const isActive = (isCurrent || isPast);
                buttonsHTML += `
                    <button class=\"status-update-btn ${isActive ? 'active' : ''}\" data-order-id=\"${order.order_id}\" data-status=\"${status}\" ${isDisabled ? 'disabled' : ''}>
                        ${formatOrderStatusText(status)}
                        ${isCurrent ? '<i class=\"fas fa-check ml-1\"></i>' : ''}
                    </button>
                `;
            });
            return buttonsHTML;
        }

        // Quick status update
        function updateOrderStatus(orderId, newStatus, clickedButton) {
            const originalButtonText = clickedButton.innerHTML;
            clickedButton.innerHTML = '<div class="loading-spinner"></div>';
            clickedButton.classList.add('is-loading');
            clickedButton.disabled = true;

            const quickStatusContainer = document.getElementById(`quick-status-${orderId}`);
            
            // Disable all buttons in this group
            quickStatusContainer.querySelectorAll('.status-update-btn').forEach(btn => btn.disabled = true);

            const formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('new_status', newStatus);

            fetch('../connection/update_order_status.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network error: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update the order in currentOrders
                    const orderIndex = currentOrders.findIndex(o => parseInt(o.order_id) === parseInt(orderId));
                    if (orderIndex !== -1) {
                        currentOrders[orderIndex].order_status = newStatus;
                        currentOrders[orderIndex].updated_at = new Date().toISOString();
                    }
                    
                    // Update UI
                    updateOrderUI(orderId, newStatus);
                    updateStats();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Status Updated!',
                        text: `Order status changed to ${formatOrderStatusText(newStatus)}`,
                        timer: 2000,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true
                    });
                    
                } else {
                    throw new Error(data.message || 'Failed to update status');
                }
            })
            .catch(error => {
                console.error('Update Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: error.message,
                    position: 'top-end',
                    toast: true,
                    timer: 4000,
                    showConfirmButton: false
                });
                
                // Re-enable buttons on failure
                const order = currentOrders.find(o => parseInt(o.order_id) === parseInt(orderId));
                if (order) {
                    updateQuickStatusButtons(orderId, order.order_status, order.payment_method);
                }
            });
        }
        
        // Update order UI after status change
        function updateOrderUI(orderId, newStatus) {
            // Update status badge
            const statusBadge = document.getElementById(`status-${orderId}`);
            statusBadge.className = `status-${newStatus} status-badge`;
            statusBadge.textContent = formatOrderStatusText(newStatus);
            
            // Update order card
            const orderCard = document.getElementById(`order-${orderId}`);
            orderCard.className = orderCard.className.replace(/\b(pending|paid|processing|ready_for_pickup|completed|cancelled|otw|to_receive)\b/g, '');
            orderCard.classList.add(newStatus);
            orderCard.dataset.status = newStatus;
            
            // Update timestamp
            const updatedElement = document.getElementById(`updated-${orderId}`);
            updatedElement.textContent = new Date().toLocaleString('en-US', { 
                month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true 
            });
            
            // Update quick status buttons
            const order = currentOrders.find(o => parseInt(o.order_id) === parseInt(orderId));
            if (order) {
                updateQuickStatusButtons(orderId, newStatus, order.payment_method);
            }
            
            // Apply filter if needed
            if (currentFilter !== 'all' && newStatus !== currentFilter) {
                setTimeout(() => {
                    orderCard.classList.add('fade-out');
                    setTimeout(() => {
                        orderCard.style.display = 'none';
                        filterOrders();
                    }, 500);
                }, 1000);
            }
        }
        
        // Update statistics
        function updateStats() {
            const stats = {
                total: currentOrders.length,
                pending: currentOrders.filter(o => o.order_status === 'pending').length,
                paid: currentOrders.filter(o => o.order_status === 'paid').length,
                processing: currentOrders.filter(o => o.order_status === 'processing').length,
                ready_for_pickup: currentOrders.filter(o => o.order_status === 'ready_for_pickup').length,
                completed: currentOrders.filter(o => o.order_status === 'completed').length,
                cancelled: currentOrders.filter(o => o.order_status === 'cancelled').length,
                otw: currentOrders.filter(o => o.order_status === 'otw').length,
                to_receive: currentOrders.filter(o => o.order_status === 'to_receive').length
            };

            document.getElementById('total-orders').textContent = stats.total;
            document.getElementById('processing-orders').textContent = stats.processing;
            document.getElementById('pickup-orders').textContent = stats.ready_for_pickup;
        }

        // Update quick status buttons
        function updateQuickStatusButtons(orderId, newStatus, paymentMethod) {
            const quickStatusContainer = document.getElementById(`quick-status-${orderId}`);
            const statusFlow = getStatusFlow(paymentMethod, newStatus);
            const currentStatusIndex = statusFlow.indexOf(newStatus);

            // Get user role from a data attribute set server-side
            const userRole = (document.body.getAttribute('data-user-role') || '').toUpperCase();

            let buttonsHTML = '';
            statusFlow.forEach((status, index) => {
                const isCurrent = (index === currentStatusIndex);
                const isNext = (index === currentStatusIndex + 1);
                const isPast = (index < currentStatusIndex);

                // COD/GCASH: cashier->processing, encoder->otw, driver->completed
                // Pick Up: cashier->processing, encoder->ready_for_pickup, cashier->completed
                let canPress = false;
                const method = (paymentMethod || '').toLowerCase();
                if (method === 'cod') {
                    if (status === 'processing' && userRole === 'CASHIER') canPress = true;
                    if (status === 'otw' && userRole === 'ENCODER') canPress = true;
                    if (status === 'completed' && userRole === 'DRIVER') canPress = true;
                } else if (method === 'gcash') {
                    if (status === 'processing' && userRole === 'CASHIER') canPress = true;
                    if (status === 'otw' && userRole === 'ENCODER') canPress = true;
                    if (status === 'completed' && userRole === 'CASHIER') canPress = true;
                } else if (method === 'pick_up') {
                    if (status === 'processing' && userRole === 'CASHIER') canPress = true;
                    if (status === 'ready_for_pickup' && userRole === 'ENCODER') canPress = true;
                    if (status === 'completed' && userRole === 'CASHIER') canPress = true;
                }
                const isDisabled = !isNext || (!canPress && isNext);
                const isActive = (isCurrent || isPast);

                buttonsHTML += `
                    <button class="status-update-btn ${isActive ? 'active' : ''}"
                            data-order-id="${orderId}"
                            data-status="${status}"
                            ${isDisabled ? 'disabled' : ''}>
                        ${formatOrderStatusText(status)}
                        ${isCurrent ? '<i class=\"fas fa-check ml-1\"></i>' : ''}
                    </button>
                `;
            });
            quickStatusContainer.innerHTML = buttonsHTML;
        }

        // Get status flow
        function getStatusFlow(paymentMethod, currentStatus) {
            const method = (paymentMethod || '').toLowerCase();
            let flow = [];
            
            if (method === 'pick_up') {
                flow = ['pending', 'processing', 'ready_for_pickup', 'completed'];
            } else if (method === 'gcash') {
                flow = ['paid', 'processing', 'otw', 'completed'];
            } else if (method === 'cod') {
                flow = ['pending', 'processing', 'otw', 'completed'];
            }
            
            if (!flow.includes(currentStatus)) {
                flow.push(currentStatus);
            }
            
            return flow;
        }

        // View order details
        function viewOrderDetails(orderId) {
            const order = currentOrders.find(o => parseInt(o.order_id) === parseInt(orderId));
            if (!order) return;

            const itemsHTML = (order.items || []).map(item => `
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <div class="flex items-center">
                        ${item.product_image ? 
                            `<img src="data:image/jpeg;base64,${item.product_image}" 
                                 alt="${item.product_name}" 
                                 class="w-16 h-16 rounded-lg object-cover mr-4">` :
                            `<div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-box text-gray-400 text-xl"></i>
                            </div>`
                        }
                        <div>
                            <h4 class="font-semibold text-gray-800">${item.product_name}</h4>
                            <p class="text-sm text-gray-600">Quantity: ${item.quantity}</p>
                            <p class="text-sm text-gray-600">Unit Price: ₱${parseFloat(item.unit_price).toFixed(2)}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-800">₱${(parseFloat(item.unit_price) * parseInt(item.quantity)).toFixed(2)}</p>
                    </div>
                </div>
            `).join('');

            const statusClass = `status-${order.order_status}`;
            const paymentClass = `payment-${(order.payment_method || '').toLowerCase()}`;

            orderDetailsContent.innerHTML = `
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-3">Order Information</h4>
                            <div class="space-y-2 text-sm">
                                <p><span class="text-gray-600">Order Reference:</span> <span class="font-medium">${order.order_reference}</span></p>
                                <p><span class="text-gray-600">Order Date:</span> <span class="font-medium">${new Date(order.order_date).toLocaleString()}</span></p>
                                <p><span class="text-gray-600">Status:</span> <span class="${statusClass} status-badge">${formatOrderStatusText(order.order_status)}</span></p>
                                <p><span class="text-gray-600">Payment Method:</span> <span class="${paymentClass} payment-badge">${formatPaymentMethodText(order.payment_method)}</span></p>
                                <p><span class="text-gray-600">Referral Coins Used:</span> <span class="font-medium">${order.coins_redeemed ? order.coins_redeemed : 0}</span></p>
                                <p><span class="text-gray-600">Shipping Fee:</span> <span class="font-medium">₱${order.shipping_fee ? parseFloat(order.shipping_fee).toFixed(2) : '0.00'}</span></p>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-3">Customer Information</h4>
                            <div class="space-y-2 text-sm">
                                <p><span class="text-gray-600">Name:</span> <span class="font-medium">${order.customer_name}</span></p>
                                <p><span class="text-gray-600">Email:</span> <span class="font-medium">${order.customer_email}</span></p>
                                <p><span class="text-gray-600">Phone:</span> <span class="font-medium">${order.customer_phone}</span></p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">Order Items</h4>
                        <div class="space-y-2">
                            ${itemsHTML}
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex flex-col gap-2">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-800">Subtotal:</span>
                                <span class="text-lg font-bold text-gray-700">₱${order.original_subtotal ? parseFloat(order.original_subtotal).toFixed(2) : '0.00'}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-800">Referral Coins Used:</span>
                                <span class="text-lg font-bold text-yellow-600">${order.coins_redeemed ? order.coins_redeemed : 0}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-800">Shipping Fee:</span>
                                <span class="text-lg font-bold text-blue-600">₱${order.shipping_fee ? parseFloat(order.shipping_fee).toFixed(2) : '0.00'}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-800">Total Amount:</span>
                                <span class="text-xl font-bold text-green-600">₱${parseFloat(order.total_amount).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            orderDetailsModal.classList.remove('hidden');
            orderDetailsModal.style.display = 'flex';
        }

        // Print order
        function printOrder(orderId) {
            const order = currentOrders.find(o => parseInt(o.order_id) === parseInt(orderId));
            if (!order) return;
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Order #${order.order_reference}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
                        .header h1 { margin: 0; color: #7d310a; }
                        .order-info { margin-bottom: 20px; }
                        .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                        .items-table th, .items-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                        .items-table th { background-color: #f5f5f5; }
                        .total { text-align: right; font-size: 18px; font-weight: bold; margin-top: 20px; }
                        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #666; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Rich Anne Lea Tiles Trading</h1>
                        <h2>Order Invoice</h2>
                        <p>Order #${order.order_reference}</p>
                    </div>
                    
                    <div class="order-info">
                        <p><strong>Customer:</strong> ${order.customer_name}</p>
                        <p><strong>Date:</strong> ${new Date(order.order_date).toLocaleString()}</p>
                        <p><strong>Status:</strong> ${formatOrderStatusText(order.order_status)}</p>
                        <p><strong>Payment Method:</strong> ${formatPaymentMethodText(order.payment_method)}</p>
                    </div>
                    
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${(order.items || []).map(item => `
                                <tr>
                                    <td>${item.product_name}</td>
                                    <td>${item.quantity}</td>
                                    <td>₱${parseFloat(item.unit_price).toFixed(2)}</td>
                                    <td>₱${(parseFloat(item.unit_price) * parseInt(item.quantity)).toFixed(2)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                    
                    <div class="total">
                        Total Amount: ₱${parseFloat(order.total_amount).toFixed(2)}
                    </div>
                    
                    <div class="footer">
                        <p>Thank you for your business!</p>
                        <p>Generated on ${new Date().toLocaleString()}</p>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }

        // Close modal
        function closeOrderModal() {
            orderDetailsModal.classList.add('hidden');
            orderDetailsModal.style.display = 'none';
        }

        // Helper functions for formatting
        function formatOrderStatusText(status) {
            const statusMap = {
                'pending': 'Pending', 'paid': 'Paid', 'processing': 'Processing',
                'ready_for_pickup': 'Ready for Pick-Up', 'completed': 'Completed',
                'cancelled': 'Cancelled', 'otw': 'Out for Delivery', 'to_receive': 'To Receive'
            };
            return statusMap[status] || status;
        }

        function formatPaymentMethodText(method) {
            const methodMap = {
                'gcash': 'GCash', 'cod': 'Cash on Delivery', 'pick_up': 'Pick Up'
            };
            return methodMap[method] || method;
        }
    </script>
</body>
</html>