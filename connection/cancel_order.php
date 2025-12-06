
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
        // Find the order by reference and user, and check status
        $stmt = $db_connection->prepare("SELECT order_id, order_status FROM orders WHERE order_reference = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$orderRef, $userId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($order) {
            $orderId = $order['order_id'];
            $orderStatus = strtolower($order['order_status']);
            // Get branch_id from orders table
            $stmtBranch = $db_connection->prepare("SELECT branch_id FROM orders WHERE order_id = ? LIMIT 1");
            $stmtBranch->execute([$orderId]);
            $branchRow = $stmtBranch->fetch(PDO::FETCH_ASSOC);
            $branchId = $branchRow ? $branchRow['branch_id'] : null;
            // Prevent cancellation if order is processing
            if ($orderStatus === 'processing') {
                echo json_encode(['success' => false, 'error' => 'Order is processing and cannot be cancelled.']);
                exit;
            }
            // Update order status, cancellation reason, and cancelled_by_user
            $update = $db_connection->prepare("UPDATE orders SET order_status = 'cancelled', cancellation_reason = ?, cancelled_by_user = 1 WHERE order_id = ?");
            $success = $update->execute([$cancelReason, $orderId]);
            if ($success) {
                // Restore stock for all items in the cancelled order using branch_id from orders
                $stmtItems = $db_connection->prepare("SELECT product_id, quantity, unit_price FROM order_items WHERE order_id = ?");
                $stmtItems->execute([$orderId]);
                $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
                foreach ($items as $item) {
                    $productId = $item['product_id'];
                    $qty = $item['quantity'];
                    if ($branchId !== null) {
                        // Try to update stock
                        $stmtUpdateStock = $db_connection->prepare("UPDATE product_branches SET stock_count = stock_count + ? WHERE product_id = ? AND branch_id = ?");
                        $stmtUpdateStock->execute([$qty, $productId, $branchId]);
                        // If no row was updated, insert a new row
                        if ($stmtUpdateStock->rowCount() === 0) {
                            $stmtInsertStock = $db_connection->prepare("INSERT INTO product_branches (product_id, branch_id, stock_count) VALUES (?, ?, ?)");
                            $stmtInsertStock->execute([$productId, $branchId, $qty]);
                        }
                    }
                }
                // Move cancelled order to cancelled table
                $stmtOrder = $db_connection->prepare("SELECT * FROM orders WHERE order_id = ? LIMIT 1");
                $stmtOrder->execute([$orderId]);
                $orderData = $stmtOrder->fetch(PDO::FETCH_ASSOC);
                if ($orderData) {
                    $columns = array_keys($orderData);
                    $columnsList = implode(',', $columns);
                    $placeholders = implode(',', array_fill(0, count($columns), '?'));
                    $values = array_values($orderData);
                    $insertCancelled = $db_connection->prepare("INSERT INTO cancelled ($columnsList) VALUES ($placeholders)");
                    $insertCancelled->execute($values);
                }
                echo json_encode(['success' => true]);
                exit;
            } else {
                // If update succeeded but echo did not run, still return success
                echo json_encode(['success' => true]);
                exit;
            }
        }
    }
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}
?>
