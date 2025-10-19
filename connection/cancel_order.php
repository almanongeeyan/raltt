
<?php
session_start();
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderRef = $_POST['order_reference'] ?? '';
    $cancelReason = $_POST['cancel_reason'] ?? '';
    $userId = $_SESSION['user_id'] ?? null;

    if ($orderRef && $cancelReason && $userId) {
        // Check cancel count for user
        $stmtCancel = $db_connection->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND order_status = 'cancelled' AND cancelled_by_user = 1");
        $stmtCancel->execute([$userId]);
        $cancelCount = $stmtCancel->fetchColumn();
        if ($cancelCount >= 3) {
            echo json_encode(['success' => false, 'error' => 'You have reached the maximum of 3 cancellations.']);
            exit;
        }
        // Find the order by reference and user
        $stmt = $db_connection->prepare("SELECT order_id FROM orders WHERE order_reference = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$orderRef, $userId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($order) {
            $orderId = $order['order_id'];
            // Update order status, cancellation reason, and cancelled_by_user
            $update = $db_connection->prepare("UPDATE orders SET order_status = 'cancelled', cancellation_reason = ?, cancelled_by_user = 1 WHERE order_id = ?");
            $success = $update->execute([$cancelReason, $orderId]);
            if ($success) {
                echo json_encode(['success' => true]);
                exit;
            }
        }
    }
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}
?>
