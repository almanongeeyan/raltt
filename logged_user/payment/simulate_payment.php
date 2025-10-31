<?php
require_once '../../connection/connection.php';
require_once '../config/payment_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only allow in test mode
if (!PAYMENT_TEST_MODE) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Simulation only allowed in test mode']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$order_id = $input['order_id'] ?? 0;
$result = $input['result'] ?? 'success';
$user_id = $_SESSION['user_id'] ?? 0;

if ($order_id === 0 || $user_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

// Verify order belongs to user
$stmt = $conn->prepare("SELECT order_id FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

// Update payment status based on simulation
$status_map = [
    'success' => 'paid',
    'failed' => 'failed', 
    'expired' => 'expired'
];

$new_status = $status_map[$result] ?? 'paid';

$stmt = $conn->prepare("UPDATE orders SET gcash_status = ? WHERE order_id = ?");
$stmt->execute([$new_status, $order_id]);

echo json_encode(['success' => true, 'new_status' => $new_status]);
?>