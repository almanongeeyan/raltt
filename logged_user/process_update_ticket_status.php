<?php
// process_update_ticket_status.php for customer side
include '../connection/connection.php';
header('Content-Type: application/json');

$ticket_id = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

if (!$ticket_id || !$status) {
    echo json_encode(['success' => false, 'error' => 'Missing ticket_id or status']);
    exit;
}

try {
    if ($status === 'Resolved') {
        $stmt = $conn->prepare("UPDATE customer_tickets SET ticket_status = ?, updated_at = NOW() WHERE ticket_id = ?");
        $stmt->execute([$status, $ticket_id]);
    } else {
        $stmt = $conn->prepare("UPDATE customer_tickets SET ticket_status = ?, updated_at = NOW() WHERE ticket_id = ?");
        $stmt->execute([$status, $ticket_id]);
    }
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Ticket status updated.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'No rows updated. Ticket may not exist or status is unchanged.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
