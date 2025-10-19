<?php
// reset_password.php
require_once '../connection/connection.php';
header('Content-Type: application/json');

$phone = $_POST['phone'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$verification_code = $_POST['verification_code'] ?? '';

if (strlen($new_password) < 8) {
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters.']);
    exit;
}

// Check verification code (simulate, replace with actual check if needed)
$stmt = $db_connection->prepare('SELECT id FROM users WHERE phone_number = ? LIMIT 1');
$stmt->execute([$phone]);
$user_id = $stmt->fetchColumn();
if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'User not found.']);
    exit;
}
// You should check the verification code here, but for now we assume it's valid

$new_hash = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $db_connection->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
$stmt->execute([$new_hash, $user_id]);

echo json_encode(['status' => 'success']);
