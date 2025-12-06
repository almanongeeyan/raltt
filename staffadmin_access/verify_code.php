<?php

header('Content-Type: application/json');
require_once '../connection/connection.php';

// Get POST data
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$code = isset($_POST['code']) ? trim($_POST['code']) : '';

if (!$email || !$code) {
    echo json_encode(['success' => false, 'message' => 'Missing email or code.']);
    exit;
}

// Validate code from database
try {
    $stmt = $db_connection->prepare("SELECT * FROM email_verifications WHERE email = ? AND verification_code = ? AND is_used = 0 AND expires_at > UTC_TIMESTAMP() ORDER BY verification_id DESC LIMIT 1");
    $stmt->execute([$email, $code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired code.']);
        exit;
    }
    // Mark code as used
    $db_connection->prepare('UPDATE email_verifications SET is_used = 1 WHERE verification_id = ?')->execute([$row['verification_id']]);
    echo json_encode(['success' => true, 'message' => 'Verification successful.']);
} catch (Exception $e) {
    error_log('Validation Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error during code validation.']);
}
