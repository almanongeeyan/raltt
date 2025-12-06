<?php
// request_activation.php: Handles account activation requests with reason
// Expects POST: contact, reason

require_once 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$contact = trim($_POST['contact'] ?? '');
$reason = trim($_POST['reason'] ?? '');

if (!$contact || !$reason) {
    echo json_encode(['status' => 'error', 'message' => 'Contact and reason are required.']);
    exit;
}

// Validate contact
$isPhone = preg_match('/^\+639[0-9]{9}$/', $contact);
$isEmail = filter_var($contact, FILTER_VALIDATE_EMAIL);
if (!$isPhone && !$isEmail) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid phone or email.']);
    exit;
}


// Find user by contact (inactive only)
$user = null;
if ($isPhone) {
    $stmt = $conn->prepare('SELECT id FROM users WHERE phone_number = ? AND account_status = "inactive" AND user_role = "CUSTOMER"');
    $stmt->execute([$contact]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $contactType = 'PHONE';
} else if ($isEmail) {
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? AND account_status = "inactive" AND user_role = "CUSTOMER"');
    $stmt->execute([$contact]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $contactType = 'EMAIL';
}


if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'No inactive account found for this contact.']);
    exit;
}

// Check if a request already exists for this user/contact
$stmt = $conn->prepare('SELECT request_id FROM account_reactivation_requests WHERE user_id = ? AND contact_type = ? AND contact_value = ?');
$stmt->execute([$user['id'], $contactType, $contact]);
if ($stmt->fetch(PDO::FETCH_ASSOC)) {
    echo json_encode(['status' => 'error', 'message' => 'You already requested for activation for this account.']);
    exit;
}

try {
    $stmt = $conn->prepare('INSERT INTO account_reactivation_requests (user_id, contact_type, contact_value, reason_provided, submission_date) VALUES (?, ?, ?, ?, NOW())');
    $stmt->execute([$user['id'], $contactType, $contact, $reason]);
    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error.']);
}
