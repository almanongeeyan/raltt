<?php
// update_password.php
session_start();
require_once '../connection/connection.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'error' => 'Not logged in.']);
    exit;
}

// Get POST data
$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (strlen($new) < 8) {
    echo json_encode(['success' => false, 'error' => 'New password must be at least 8 characters.']);
    exit;
}
if ($new !== $confirm) {
    echo json_encode(['success' => false, 'error' => 'Passwords do not match.']);
    exit;
}

try {
    $stmt = $db_connection->prepare('SELECT password_hash FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$user_id]);
    $hash = $stmt->fetchColumn();
    if (!$hash || !password_verify($current, $hash)) {
        echo json_encode(['success' => false, 'error' => 'Current password is incorrect.']);
        exit;
    }
    $new_hash = password_hash($new, PASSWORD_DEFAULT);
    $stmt = $db_connection->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
    $stmt->execute([$new_hash, $user_id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log('Password update error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error.']);
}
