<?php
session_start();
require_once '../../connection/connection.php';
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$cart_item_id = isset($_POST['cart_item_id']) ? intval($_POST['cart_item_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

if ($user_id > 0 && $cart_item_id > 0 && $quantity > 0) {
    $stmt = $conn->prepare('UPDATE cart_items SET quantity = ? WHERE cart_item_id = ? AND user_id = ?');
    $success = $stmt->execute([$quantity, $cart_item_id, $user_id]);
    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
}
