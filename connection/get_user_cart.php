<?php
require_once '../connection/connection.php';
$conn = $db_connection;

header('Content-Type: application/json');

if (!isset($_GET['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing user_id']);
    exit;
}

$user_id = intval($_GET['user_id']);

$sql = "SELECT ci.cart_item_id, ci.quantity, p.product_id, p.product_name, p.product_price, p.product_image FROM cart_items ci JOIN products p ON ci.product_id = p.product_id WHERE ci.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

foreach ($cart_items as &$item) {
    if (!empty($item['product_image'])) {
        $item['product_image'] = 'data:image/jpeg;base64,' . base64_encode($item['product_image']);
    } else {
        $item['product_image'] = null;
    }
}

if ($cart_items) {
    echo json_encode(['status' => 'success', 'items' => $cart_items]);
} else {
    echo json_encode(['status' => 'empty', 'items' => []]);
}
