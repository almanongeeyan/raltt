<?php
// mark_notifications_read.php
// Marks all notifications as read for the logged-in user
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Not logged in');
}
require_once __DIR__ . '/connection.php';
if (!isset($db_connection) && isset($conn)) {
    $db_connection = $conn;
}
try {
    $stmt = $db_connection->prepare("UPDATE user_notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$_SESSION['user_id']]);
    echo 'OK';
} catch (Exception $e) {
    http_response_code(500);
    echo 'Error';
}
