<?php
// save_ticket.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../connection/connection.php';
header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required = ['user_id', 'order_id', 'order_reference', 'issue_type', 'damage_time', 'issue_description'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'error' => "Missing field: $field"]);
        exit;
    }
}

try {
    $photo = !empty($data['photo']) ? base64_decode($data['photo']) : null;
    $stmt = $conn->prepare("INSERT INTO customer_tickets (user_id, order_id, order_reference, issue_type, damage_time, issue_description, photo, ticket_status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'Open', NOW(), NOW())");
    $stmt->execute([
        $data['user_id'],
        $data['order_id'],
        $data['order_reference'],
        $data['issue_type'],
        $data['damage_time'],
        $data['issue_description'],
        $photo
    ]);
    echo json_encode(['success' => true, 'message' => 'Ticket submitted successfully.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
