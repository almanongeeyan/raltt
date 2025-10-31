<?php
session_start();
include '../includes/sidebar.php';

// Database connection
require_once '../connection/connection.php';

// Get branch_id from session
$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;

// Get orders from database

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
    'to_receive' => 0 // Kept for stats, but removed from gcash flow
];

try {
    // Build query based on branch access
    // Add is_hidden support and deduplicate orders by order_reference
    if ($branch_id) {
        $query = "SELECT o.*, b.branch_name, u.full_name as customer_name, u.phone_number as customer_phone, u.email as customer_email
                  FROM orders o 
                  JOIN branches b ON o.branch_id = b.branch_id 
                  JOIN users u ON o.user_id = u.id 
                  WHERE o.branch_id = ?
                  ORDER BY o.order_date DESC";
        $stmt = $db_connection->prepare($query);
        $stmt->execute([$branch_id]);
    } else {
        $query = "SELECT o.*, b.branch_name, u.full_name as customer_name, u.phone_number as customer_phone, u.email as customer_email
                  FROM orders o 
                  JOIN branches b ON o.branch_id = b.branch_id 
                  JOIN users u ON o.user_id = u.id 
                  ORDER BY o.order_date DESC";
        $stmt = $db_connection->prepare($query);
        $stmt->execute();
    }

    $rawOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Use rawOrders directly, no deduplication
    $orders = $rawOrders;

    // Only show orders for today or future (not hidden after 11:59PM)
    $today = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d');
    $orders = array_filter($orders, function($order) use ($today) {
        $orderDate = (new DateTime($order['order_date'], new DateTimeZone('Asia/Manila')))->format('Y-m-d');
        // Only show orders for today or future
        return $orderDate >= $today;
    });

    // Get order items for each order
    foreach ($orders as &$order) {
        $itemsQuery = "SELECT oi.*, p.product_name, p.product_image 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.product_id 
                       WHERE oi.order_id = ?";
        $itemsStmt = $db_connection->prepare($itemsQuery);
        $itemsStmt->execute([$order['order_id']]);

        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($items as &$item) {
            if (isset($item['product_image']) && !empty($item['product_image'])) {
                $item['product_image'] = base64_encode($item['product_image']);
            }
        }
        $order['items'] = $items;

        // Only count stats for today or future
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

// **FIXED LOGIC as per user request**
function getStatusFlow($paymentMethod, $currentStatus) {
    $paymentMethod = strtolower($paymentMethod);
    $flow = [];
    
    // Define base flows based on user request
    if ($paymentMethod === 'pick_up') {
        $flow = ['pending', 'processing', 'ready_for_pickup', 'completed'];
    } elseif ($paymentMethod === 'gcash') {
        // **FIX #3: 'to_receive' REMOVED from gcash flow**
        $flow = ['paid', 'processing', 'otw', 'completed'];
    } elseif ($paymentMethod === 'cod') {
        $flow = ['pending', 'processing', 'otw', 'completed'];
    } 
    // 'cancelled' button and default flow removed

    // Ensure the current status is always in the list, even if it's not in the 'default' flow
    // (e.g., if it was manually set to 'cancelled')
    if (!in_array($currentStatus, $flow)) {
         $flow[] = $currentStatus;
    }
    
    return $flow; // Return the flow in the specified order
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
        
        /* **FIX #1: Modal styles (already correct for centering and overlay)** */
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
        
        /* **FIX #4: Styles for sequential buttons** */
        .status-update-btn:not(:disabled):hover { background: #7d310a; color: white; border-color: #7d310a; transform: translateY(-1px); }
        .status-update-btn.active { background: #7d310a; color: white; border-color: #7d310a; }
        .status-update-btn:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
        .status-update-btn.active:disabled { opacity: 1; } /* Current and past statuses are fully opaque */
        .status-update-btn:not(.active):disabled { opacity: 0.4; } /* Future statuses are dimmed */
        .status-update-btn:disabled:hover { background: #f8fafc; color: #374151; border-color: #e5e7eb; }
        .status-update-btn.active:disabled:hover { background: #7d310a; color: white; border-color: #7d310a; }
        
        .status-update-btn.is-loading { background: #e5e7eb; color: #374151; opacity: 0.7; cursor: wait; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
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
                                            // Find the first order to display the branch name
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
                        <?php
                        // No need to filter here, already filtered above for today/future
                        ?>
                        
                        <?php foreach ($orders as $order): ?>
                            <?php
                            $statusClass = 'status-' . $order['order_status'];
                            $orderCardClass = 'order-card ' . $order['order_status'];
                            $paymentClass = 'payment-' . strtolower($order['payment_method']);
                            $statusFlow = getStatusFlow($order['payment_method'], $order['order_status']);
                            // **FIX #4: Get index for sequential logic**
                            $currentStatusIndex = array_search($order['order_status'], $statusFlow);
                            // Handle cases where status (like 'cancelled') might not be in the flow
                            if ($currentStatusIndex === false) {
                                $currentStatusIndex = count($statusFlow) - 1; // Treat it as the last step
                            }
                            ?>
                            <div class="<?php echo $orderCardClass; ?> p-6 fade-in" id="order-<?php echo $order['order_id']; ?>" data-status="<?php echo $order['order_status']; ?>">
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
                                                <?php // **FIX #4: Sequential Button Logic** ?>
                                                <?php foreach ($statusFlow as $index => $nextStatus): 
                                                    $isCurrent = ($index === $currentStatusIndex);
                                                    $isNext = ($index === $currentStatusIndex + 1);
                                                    $isPast = ($index < $currentStatusIndex);
                                                    
                                                    $isDisabled = !$isNext; // Only the 'next' button is enabled
                                                    $isActive = ($isCurrent || $isPast); // 'Past' and 'Current' are active
                                                ?>
                                                    <button class="status-update-btn <?php echo $isActive ? 'active' : ''; ?>" 
                                                            data-order-id="<?php echo $order['order_id']; ?>"
                                                            data-status="<?php echo $nextStatus; ?>"
                                                            <?php echo $isDisabled ? 'disabled' : ''; ?>>
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
                                            
                                            <button class="action-btn btn-print" onclick="printOrder(<?php echo $order['order_id']; ?>)">
                                                <i class="fas fa-print"></i> Print
                                            </button>
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
                </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentOrders = <?php echo json_encode($orders); ?>;
        let currentFilter = 'all';
        let currentSearch = '';

        // DOM elements
        const ordersContainer = document.getElementById('orders-container');
        const orderDetailsModal = document.getElementById('orderDetailsModal');
        const orderDetailsContent = document.getElementById('orderDetailsContent');

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            if(currentOrders.length > 0) {
                console.log('Orders loaded into JavaScript:', currentOrders);
            }
            setupEventListeners();
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
                        console.log('Button clicked. Order ID:', orderId, 'New Status:', newStatus);
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
                const orderId = card.id.replace('order-', '');
                const order = currentOrders.find(o => o.order_id == orderId);
                
                if (!order) {
                    card.style.display = 'none';
                    return;
                }

                // Check status filter
                const statusMatch = (currentFilter === 'all') || (status === currentFilter);

                // Check search filter
                const searchMatch = (currentSearch === '') ||
                    order.order_reference.toLowerCase().includes(currentSearch) ||
                    order.customer_name.toLowerCase().includes(currentSearch) ||
                    order.customer_email.toLowerCase().includes(currentSearch) ||
                    (order.items && order.items.some(item => 
                        item.product_name.toLowerCase().includes(currentSearch)
                    ));

                if (statusMatch && searchMatch) {
                    card.style.display = 'block';
                    card.classList.remove('fade-out'); // Ensure it's not faded
                    hasVisibleOrders = true;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show 'No orders found' message if no orders are visible
            if (!hasVisibleOrders && currentOrders.length > 0) {
                ordersContainer.innerHTML += `
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center" id="no-orders-message-dynamic">
                        <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">No Orders Found</h3>
                        <p class="text-gray-500">No orders match your current filters.</p>
                    </div>
                `;
            } else if (currentOrders.length === 0 && initialNoOrdersMsg) {
                // If all orders are gone, show the initial message again
                 initialNoOrdersMsg.style.display = 'block';
            }
        }

        // Quick status update
        function updateOrderStatus(orderId, newStatus, clickedButton) {
            console.log('Attempting to update order:', orderId, 'to status:', newStatus);
            
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
                console.log('Fetch response received:', response);
                if (!response.ok) {
                    throw new Error('Network error: ' + response.status + ' ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data from server:', data);
                if (data.success) {
                    // 1. Update the main status badge
                    const statusBadge = document.getElementById(`status-${orderId}`);
                    statusBadge.className = `status-${newStatus} status-badge`;
                    statusBadge.textContent = formatOrderStatusText(newStatus);
                    
                    // 2. Update the order card class and data-status
                    const orderCard = document.getElementById(`order-${orderId}`);
                    // Remove all old status classes
                    orderCard.className = orderCard.className.replace(/\b(pending|paid|processing|ready_for_pickup|completed|cancelled|otw|to_receive)\b/g, '');
                    orderCard.classList.add(newStatus);
                    orderCard.dataset.status = newStatus;
                    
                    // 3. Update timestamp
                    const updatedElement = document.getElementById(`updated-${orderId}`);
                    updatedElement.textContent = new Date().toLocaleString('en-US', { 
                        month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true 
                    });
                    
                    // 4. Update the master 'currentOrders' JS object
                    const orderIndex = currentOrders.findIndex(o => o.order_id == orderId);
                    if (orderIndex !== -1) {
                        currentOrders[orderIndex].order_status = newStatus;
                        currentOrders[orderIndex].updated_at = new Date().toISOString();
                    }
                    
                    // 5. Re-render the quick status buttons with new sequential logic
                    updateQuickStatusButtons(orderId, newStatus, currentOrders[orderIndex].payment_method);
                    
                    // 6. Update stats cards
                    updateStats();
                    
                    // 7. Show success toast
                    Swal.fire({
                        icon: 'success',
                        title: 'Status Updated!',
                        text: `Order status changed to ${formatOrderStatusText(newStatus)}`,
                        timer: 2000,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true
                    });
                    
                    // 8. If filter is on and card no longer matches, fade it out
                    if (currentFilter !== 'all' && newStatus !== currentFilter) {
                        setTimeout(() => {
                            orderCard.classList.add('fade-out');
                            // After fade-out, set to display:none
                            setTimeout(() => {
                                orderCard.style.display = 'none';
                                filterOrders(); // Re-run filter to check for "no results" msg
                            }, 500); 
                        }, 1000); // Wait 1 sec before fading
                    }
                    
                } else {
                    // Server reported an error
                    throw new Error(data.message || 'Failed to update status. Unknown server error.');
                }
            })
            .catch(error => {
                // Catches network errors or thrown errors
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
                
                // Re-enable buttons and revert text on failure
                clickedButton.innerHTML = originalButtonText;
                clickedButton.classList.remove('is-loading');
                // Find the order's current status from the master list
                const order = currentOrders.find(o => o.order_id == orderId);
                if (order) {
                    updateQuickStatusButtons(orderId, order.order_status, order.payment_method);
                }
            });
        }
        
        // Update statistics based on the global currentOrders array
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

        // **FIX #4: Update quick status buttons with sequential logic**
        function updateQuickStatusButtons(orderId, newStatus, paymentMethod) {
            const quickStatusContainer = document.getElementById(`quick-status-${orderId}`);
            const statusFlow = getStatusFlow(paymentMethod, newStatus);
            const currentStatusIndex = statusFlow.indexOf(newStatus);
            
            let buttonsHTML = '';
            statusFlow.forEach((status, index) => {
                const isCurrent = (index === currentStatusIndex);
                const isNext = (index === currentStatusIndex + 1);
                const isPast = (index < currentStatusIndex);
                
                const isDisabled = !isNext; // Only the 'next' button is enabled
                const isActive = (isCurrent || isPast); // 'Past' and 'Current' are active
                
                buttonsHTML += `
                    <button class="status-update-btn ${isActive ? 'active' : ''}" 
                            data-order-id="${orderId}"
                            data-status="${status}"
                            ${isDisabled ? 'disabled' : ''}>
                        ${formatOrderStatusText(status)}
                        ${isCurrent ? '<i class="fas fa-check ml-1"></i>' : ''}
                    </button>
                `;
            });
            
            quickStatusContainer.innerHTML = buttonsHTML;
        }

        // **FIXED LOGIC as per user request**
        // JS version of getStatusFlow to match PHP logic
        function getStatusFlow(paymentMethod, currentStatus) {
            const method = (paymentMethod || '').toLowerCase();
            let flow = [];
            
            // Define base flows based on user request
            if (method === 'pick_up') {
                flow = ['pending', 'processing', 'ready_for_pickup', 'completed'];
            } else if (method === 'gcash') {
                // **FIX #3: 'to_receive' REMOVED from gcash flow**
                flow = ['paid', 'processing', 'otw', 'completed'];
            } else if (method === 'cod') {
                flow = ['pending', 'processing', 'otw', 'completed'];
            }
            
            // Ensure the current status is always in the list
            if (!flow.includes(currentStatus)) {
                flow.push(currentStatus);
            }
            
            return flow; // Return the flow in the specified order
        }


        // View order details with correct image handling
        function viewOrderDetails(orderId) {
            const order = currentOrders.find(o => o.order_id == orderId);
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
            const order = currentOrders.find(o => o.order_id == orderId);
            if (!order) return;
            
            // Using your project name
            const companyName = "Rich Anne Lea Tiles Trading";

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
                        <h1>${companyName}</h1>
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