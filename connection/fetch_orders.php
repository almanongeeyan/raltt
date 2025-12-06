<?php
session_start();
require_once 'connection.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$orderTabs = [
    'all-orders' => '',
    'pending' => "order_status = 'pending'",
    'to-ship' => "order_status IN ('processing','paid')",
    'to-receive' => "order_status IN ('ready_for_pickup','otw')",
    'completed' => "order_status = 'completed'",
    'cancelled' => "order_status = 'cancelled'"
];

$html = '';
foreach ($orderTabs as $tab => $where) {
    $ordersList = [];
    $query = "SELECT o.*, oi.order_item_id, oi.product_id, oi.quantity, oi.unit_price, p.product_name, p.product_image, b.branch_name
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN products p ON oi.product_id = p.product_id
        JOIN branches b ON o.branch_id = b.branch_id
        WHERE o.user_id = ?";
    if ($where) {
        $query .= " AND $where";
    }
    $query .= " ORDER BY o.order_date DESC, oi.order_item_id ASC";
    $stmt = $db_connection->prepare($query);
    $stmt->execute([$user_id]);
    $ordersList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group items by order
    $groupedOrders = [];
    foreach ($ordersList as $row) {
        $oid = $row['order_id'];
        if (!isset($groupedOrders[$oid])) {
            $groupedOrders[$oid] = [
                'order_reference' => $row['order_reference'],
                'order_date' => $row['order_date'],
                'order_status' => $row['order_status'],
                'total_amount' => $row['total_amount'],
                'branch_name' => $row['branch_name'],
                'items' => []
            ];
        }
        $groupedOrders[$oid]['items'][] = [
            'product_name' => $row['product_name'],
            'product_image' => $row['product_image'],
            'quantity' => $row['quantity'],
            'unit_price' => $row['unit_price']
        ];
    }
    $flexClass = (!empty($groupedOrders) ? 'flex-col gap-4' : 'justify-center items-center py-12');
    $html .= '<div class="orders-tab-content flex ' . $flexClass . '" id="' . $tab . '" style="' . ($tab === 'all-orders' ? 'display:flex' : 'display:none') . '">';
    if (!empty($groupedOrders)) {
        foreach ($groupedOrders as $order) {
            $html .= '<a href="order_confirmation.php?order_reference=' . urlencode($order['order_reference']) . '" class="orders-drawer p-4 mb-4 rounded-lg border border-gray-200 shadow-sm animate-fade-in" style="display:block;text-decoration:none;">';
            $html .= '<div class="flex flex-wrap justify-between items-center mb-3">';
            $html .= '<span class="order-ref font-semibold text-xs text-primary">Order Ref: ' . htmlspecialchars($order['order_reference']) . '</span>';
            $html .= '<span class="date text-xs text-textlight">' . date('F j, Y', strtotime($order['order_date'])) . '</span>';
            $html .= '</div>';
            foreach ($order['items'] as $item) {
                $productTotal = $item['unit_price'] * $item['quantity'];
                $imgSrc = !empty($item['product_image']) ? "data:image/jpeg;base64," . base64_encode($item['product_image']) : "https://placehold.co/64x64/f9f5f2/7d310a?text=IMG";
                $html .= '<div class="grid grid-cols-1 md:grid-cols-8 md:gap-4 items-center py-2">';
                $html .= '<div class="product-info flex items-center col-span-4 mb-3 md:mb-0">';
                $html .= '<img src="' . $imgSrc . '" alt="Product" class="w-12 h-12 rounded-lg object-cover mr-4">';
                $html .= '<div class="product-text flex flex-col">';
                $html .= '<span class="title font-semibold text-textdark text-sm">' . htmlspecialchars($item['product_name']) . '</span>';
                $html .= '</div></div>';
                $html .= '<div class="qty text-textdark font-medium text-center col-span-1 mb-2 md:mb-0 text-sm">' . $item['quantity'] . '</div>';
                $html .= '<div class="price text-primary font-semibold text-center col-span-2 mb-2 md:mb-0 text-sm">₱' . number_format($productTotal, 2) . '</div>';
                $html .= '<div class="status text-center col-span-1 mb-2 md:mb-0">';
                $statusText = ($order['order_status'] === 'otw') ? 'Out for Delivery' : ucfirst($order['order_status']);
                $html .= '<span class="status-badge ' . ($order['order_status'] === 'cancelled' ? 'status-cancelled' : 'status-pending') . '">' . $statusText . '</span>';
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</a>';
        }
    } else {
        $html .= '<div class="text-center">';
        $html .= '<i class="fa-solid fa-box-open text-4xl text-textlight mb-4"></i>';
        $html .= '<p class="text-textlight text-lg">No orders found</p>';
        $html .= '<p class="text-textlight text-sm mt-2">Your ' . str_replace('-', ' ', $tab) . ' will appear here</p>';
        $html .= '</div>';
    }
    $html .= '</div>';
}
echo json_encode(['success' => true, 'html' => $html]);
