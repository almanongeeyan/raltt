<?php
// This endpoint saves a buyer warning report
require_once '../connection/connection.php';
header('Content-Type: application/json');

session_start();
$userRole = isset($_SESSION['user_role']) ? strtoupper($_SESSION['user_role']) : '';

// staff_user_id is the same as user_id in session (sidebar.php)

$staffUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
// If not set, try to fetch from DB using email and user_role
if ((!$staffUserId || $staffUserId <= 0) && isset($_SESSION['user_email']) && $userRole === 'DRIVER') {
    try {
        $stmt = $db_connection->prepare("SELECT id FROM users WHERE email = ? AND user_role = 'DRIVER' LIMIT 1");
        $stmt->execute([$_SESSION['user_email']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && isset($row['id'])) {
            $staffUserId = (int)$row['id'];
        }
    } catch (Exception $e) {}
}


if ($userRole !== 'DRIVER') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
if (!$staffUserId || $staffUserId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Staff user ID not set in session. Please re-login.']);
    exit;
}

$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : null;
$warningType = isset($_POST['warning_type']) ? $_POST['warning_type'] : '';
$warningNotes = isset($_POST['warning_notes']) ? $_POST['warning_notes'] : '';

if (!$userId || !$orderId || !$warningType) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

try {
    // Check if already reported by this staff for this order and user
    $checkStmt = $db_connection->prepare("SELECT warning_id FROM buyer_warnings WHERE user_id = ? AND order_id = ? AND staff_user_id = ? LIMIT 1");
    $checkStmt->execute([$userId, $orderId, $staffUserId]);
    if ($checkStmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'You have already reported this user for this order.']);
        exit;
    }
    // Insert new report
    $stmt = $db_connection->prepare("INSERT INTO buyer_warnings (user_id, order_id, warning_type, staff_user_id, warning_notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $orderId, $warningType, $staffUserId, $warningNotes]);
    // Flag order as cancelled
    $cancelOrderStmt = $db_connection->prepare("UPDATE orders SET order_status = 'cancelled' WHERE order_id = ?");
    $cancelOrderStmt->execute([$orderId]);

    // Restore stock for all items in the cancelled order
    $orderInfoStmt = $db_connection->prepare("SELECT branch_id FROM orders WHERE order_id = ? LIMIT 1");
    $orderInfoStmt->execute([$orderId]);
    $orderInfo = $orderInfoStmt->fetch(PDO::FETCH_ASSOC);
    $branchId = $orderInfo ? (int)$orderInfo['branch_id'] : null;
    if ($branchId) {
        $itemsStmt = $db_connection->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
        $itemsStmt->execute([$orderId]);
        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($items as $item) {
            $productId = (int)$item['product_id'];
            $qty = (int)$item['quantity'];
            $updateStockStmt = $db_connection->prepare("UPDATE product_branches SET stock_count = stock_count + ? WHERE product_id = ? AND branch_id = ?");
            $updateStockStmt->execute([$qty, $productId, $branchId]);
        }
    }

    // Send notification to user
    $notifMsg = 'You have been reported by a driver for order #' . $orderId . '. Reason: ' . $warningType . '.';
    $notifStmt = $db_connection->prepare("INSERT INTO user_notifications (user_id, notification_type, notification_message, related_order_id) VALUES (?, 'BUYER_WARNING', ?, ?)");
    $notifStmt->execute([$userId, $notifMsg, $orderId]);

    // Check total active reports for this user
    $countStmt = $db_connection->prepare("SELECT COUNT(*) as total_reports FROM buyer_warnings WHERE user_id = ? AND is_active = 1");
    $countStmt->execute([$userId]);
    $reportCount = $countStmt->fetchColumn();
    if ($reportCount >= 3) {
        // Deactivate user account
        $deactivateStmt = $db_connection->prepare("UPDATE users SET account_status = 'inactive' WHERE id = ?");
        $deactivateStmt->execute([$userId]);
        // Send notification about deactivation
        $deactMsg = 'Your account has been deactivated due to multiple reports.';
        $deactNotifStmt = $db_connection->prepare("INSERT INTO user_notifications (user_id, notification_type, notification_message) VALUES (?, 'ACCOUNT_STATUS', ?)");
        $deactNotifStmt->execute([$userId, $deactMsg]);
    }
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
