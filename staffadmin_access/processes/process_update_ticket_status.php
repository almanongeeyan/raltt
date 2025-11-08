<?php
// process_update_ticket_status.php
include '../../connection/connection.php';
header('Content-Type: application/json');

$ticket_id = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

if (!$ticket_id || !$status) {
    echo json_encode(['success' => false, 'error' => 'Missing ticket_id or status']);
    exit;
}

try {
    if ($status === 'Awaiting Customer') {
        $stmt = $conn->prepare("UPDATE customer_tickets SET ticket_status = ?, updated_at = NOW(), awaiting_customer_at = NOW() WHERE ticket_id = ?");
        $stmt->execute([$status, $ticket_id]);
    } else if ($status === 'Resolved') {
        $stmt = $conn->prepare("UPDATE customer_tickets SET ticket_status = ?, updated_at = NOW() WHERE ticket_id = ?");
        $stmt->execute([$status, $ticket_id]);
    } else {
        $stmt = $conn->prepare("UPDATE customer_tickets SET ticket_status = ?, updated_at = NOW(), awaiting_customer_at = NULL WHERE ticket_id = ?");
        $stmt->execute([$status, $ticket_id]);
    }
    if ($stmt->rowCount() > 0) {
        // Fetch user_id and order_id for notification
        $infoStmt = $conn->prepare("SELECT user_id, order_id FROM customer_tickets WHERE ticket_id = ? LIMIT 1");
        $infoStmt->execute([$ticket_id]);
        $ticketInfo = $infoStmt->fetch(PDO::FETCH_ASSOC);
        if ($ticketInfo) {
            $user_id = $ticketInfo['user_id'];
            $order_id = $ticketInfo['order_id'];
            $notifMsg = 'Your support ticket status has been updated to: ' . $status;
            $notifStmt = $conn->prepare("INSERT INTO user_notifications (user_id, notification_type, notification_message, related_order_id, related_ticket_id, is_read, created_at) VALUES (?, 'TICKET_UPDATE', ?, ?, ?, 0, NOW())");
            $notifStmt->execute([$user_id, $notifMsg, $order_id, $ticket_id]);
        }
        echo json_encode(['success' => true, 'message' => 'Ticket status updated.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'No rows updated. Ticket may not exist or status is unchanged.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
