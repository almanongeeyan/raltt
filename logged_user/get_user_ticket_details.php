<?php
// get_user_ticket_details.php
include '../connection/connection.php';
header('Content-Type: application/json');

session_start();
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$ticket_id = isset($_GET['ticket_id']) ? intval($_GET['ticket_id']) : 0;
if (!$user_id || !$ticket_id) {
    echo json_encode(['success' => false, 'error' => 'Missing user_id or ticket_id']);
    exit;
}


$stmt = $conn->prepare("SELECT ticket_id, order_reference, issue_type, damage_time, issue_description, ticket_status, created_at, order_id FROM customer_tickets WHERE user_id = ? AND ticket_id = ? LIMIT 1");
$stmt->execute([$user_id, $ticket_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

// Get selected tile items for this ticket (from order_items and products)
$items = [];
if ($ticket && isset($ticket['order_id']) && $ticket['order_id']) {
    $itemStmt = $conn->prepare("SELECT p.product_name, oi.quantity, oi.unit_price FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?");
    $itemStmt->execute([$ticket['order_id']]);
    $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
}
$ticket['items'] = $items;

echo json_encode(['success' => true, 'ticket' => $ticket]);
