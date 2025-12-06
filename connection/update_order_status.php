
<?php
session_start();
require_once '../connection/connection.php';

// Set header to return JSON
header('Content-Type: application/json');

$response = [
    'success' => false,
    'error' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle status update
    $order_id = $_POST['order_id'] ?? null;
    $new_status = $_POST['new_status'] ?? null;

    if ($order_id && $new_status) {
        try {
            $db_connection->beginTransaction();
            $stmt = $db_connection->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
            $result = $stmt->execute([$new_status, $order_id]);
            if ($result) {

                // Send notification to user
                $orderInfoStmt = $db_connection->prepare("SELECT user_id, order_reference FROM orders WHERE order_id = ?");
                $orderInfoStmt->execute([$order_id]);
                $orderInfo = $orderInfoStmt->fetch(PDO::FETCH_ASSOC);
                if ($orderInfo) {
                    $notifMsg = "Your order #" . htmlspecialchars($orderInfo['order_reference']) . " status has been updated to " . htmlspecialchars(ucwords(str_replace('_', ' ', $new_status))) . ".";
                    $notifStmt = $db_connection->prepare("INSERT INTO user_notifications (user_id, notification_type, notification_message, related_order_id) VALUES (?, 'ORDER_STATUS', ?, ?)");
                    $notifStmt->execute([
                        $orderInfo['user_id'],
                        $notifMsg,
                        $order_id
                    ]);
                }

                $db_connection->commit();
                $response['success'] = true;
            } else {
                $db_connection->rollBack();
                $response['error'] = 'Failed to update order status.';
            }
        } catch (PDOException $e) {
            if ($db_connection->inTransaction()) $db_connection->rollBack();
            error_log("Order Status Update Error: " . $e->getMessage());
            $response['error'] = 'A database error occurred.';
        }
    } else {
        $response['error'] = 'Missing order_id or new_status.';
    }
    echo json_encode($response);
    exit;
}

// ...existing code for GET inventory queries...
// (Retain your inventory query logic here if needed)

?>