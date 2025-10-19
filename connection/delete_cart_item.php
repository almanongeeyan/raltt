<?php
// delete_cart_item.php
require_once 'connection.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$cart_item_id = isset($data['cart_item_id']) ? intval($data['cart_item_id']) : 0;
$response = ['success' => false];

if ($cart_item_id > 0) {
    $stmt = $conn->prepare('DELETE FROM cart_items WHERE cart_item_id = ?');
    if ($stmt->execute([$cart_item_id])) {
        $response['success'] = true;
    }
}
echo json_encode($response);