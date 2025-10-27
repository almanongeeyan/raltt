<?php
// get_user_tickets.php
include '../connection/connection.php';
header('Content-Type: application/json');

session_start();
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
if (!$user_id) {
    echo json_encode(['success' => false, 'error' => 'Missing user_id']);
    exit;
}


$stmt = $conn->prepare("SELECT ticket_id, order_reference, issue_type, issue_description, ticket_status, created_at, awaiting_customer_at FROM customer_tickets WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'tickets' => $tickets]);
