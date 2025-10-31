<?php
require_once '../../connection/connection.php';
require_once '../config/payment_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

if ($order_id === 0 || $user_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

// Check if order belongs to user
$stmt = $conn->prepare("SELECT gcash_reference, gcash_status FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

// If already marked as paid in database
if ($order['gcash_status'] === 'paid') {
    echo json_encode(['success' => true]);
    exit();
}

// For testing, simulate random payment confirmation
if (PAYMENT_TEST_MODE) {
    // 30% chance of payment being confirmed
    if (rand(1, 100) <= 30) {
        $stmt = $conn->prepare("UPDATE orders SET gcash_status = 'paid', payment_status = 'paid' WHERE order_id = ?");
        $stmt->execute([$order_id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'status' => 'pending']);
    }
    exit();
}

// Live mode code would go here
echo json_encode(['success' => false, 'status' => 'pending']);
?>