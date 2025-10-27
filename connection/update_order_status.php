
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
            $stmt = $db_connection->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
            $result = $stmt->execute([$new_status, $order_id]);
            if ($result) {
                $response['success'] = true;
            } else {
                $response['error'] = 'Failed to update order status.';
            }
        } catch (PDOException $e) {
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