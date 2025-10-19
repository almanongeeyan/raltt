<?php
// get_order_items.php
include '../connection/connection.php';
header('Content-Type: application/json');

$ref = isset($_GET['ref']) ? strtoupper(trim($_GET['ref'])) : '';
if (!preg_match('/^[A-Z0-9]{8}$/', $ref)) {
    echo json_encode(['success' => false, 'error' => 'Invalid reference number.']);
    exit;
}

// Query order by reference number
$orderQuery = $conn->prepare("SELECT order_id, order_reference FROM orders WHERE order_reference = ? LIMIT 1");
$orderQuery->execute(['RAL-' . $ref]);
$order = $orderQuery->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo json_encode(['success' => false, 'error' => 'Order not found.']);
    exit;
}
$orderId = $order['order_id'];

// Query items for this order, join with products for name and image
$itemQuery = $conn->prepare("SELECT oi.order_item_id AS id, p.product_name AS name, p.product_image AS image, oi.unit_price AS price, oi.quantity AS quantity, oi.order_id FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?");
$itemQuery->execute([$orderId]);
$items = $itemQuery->fetchAll(PDO::FETCH_ASSOC);

// Convert image to base64 if not null
foreach ($items as &$item) {
    if (!empty($item['image'])) {
        $item['image'] = 'data:image/jpeg;base64,' . base64_encode($item['image']);
    } else {
        $item['image'] = '';
    }
}
unset($item);

echo json_encode(['success' => true, 'items' => $items, 'order_id' => $orderId]);
