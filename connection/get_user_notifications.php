<?php
// get_user_notifications.php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['notifications' => []]);
    exit;
}
require_once __DIR__ . '/connection.php';
if (!isset($db_connection) && isset($conn)) {
    $db_connection = $conn;
}
try {
    $stmt = $db_connection->prepare("SELECT notification_type, notification_message, is_read, created_at FROM user_notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$_SESSION['user_id']]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['notifications' => $notifications]);
} catch (Exception $e) {
    echo json_encode(['notifications' => []]);
}
