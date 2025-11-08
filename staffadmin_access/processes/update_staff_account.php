<?php
session_start();
require_once '../../connection/connection.php';

$conn = $db_connection;

// Validate POST data
if (!isset($_POST['user_id'])) {
	echo 'Missing user ID';
	exit;
}

// Sanitize and validate input
$user_id = (int)$_POST['user_id'];
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$role = isset($_POST['role']) ? strtoupper(trim($_POST['role'])) : '';

$allowed_roles = ['DRIVER', 'CASHIER', 'ENCODER'];
if (!in_array($role, $allowed_roles)) {
	echo 'Invalid role';
	exit;
}

// Check if user exists
$stmt_check = $conn->prepare('SELECT id FROM users WHERE id = ?');
$stmt_check->execute([$user_id]);
if ($stmt_check->rowCount() === 0) {
	echo 'User not found';
	exit;
}


// Email must not be empty
if (empty($email)) {
	echo 'Email cannot be empty';
	exit;
}
// Check for email uniqueness (ignore current user)
$stmt_email = $conn->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
$stmt_email->execute([$email, $user_id]);
if ($stmt_email->rowCount() > 0) {
	echo 'Email already in use';
	exit;
}

$update_fields = [
	'full_name' => $full_name,
	'email' => $email,
	'phone_number' => $phone,
	'user_role' => $role
];

// Only update password if provided
if (!empty($password)) {
	$update_fields['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
}

$set_clause = [];
$params = [];
foreach ($update_fields as $col => $val) {
	$set_clause[] = "$col = ?";
	$params[] = $val;
}
$params[] = $user_id;

$sql = "UPDATE users SET ".implode(', ', $set_clause).", updated_at = NOW() WHERE id = ?";
try {
	$stmt = $conn->prepare($sql);
	if ($stmt && $stmt->execute($params)) {
		echo 'OK';
	} else {
		$errorInfo = $stmt ? $stmt->errorInfo() : $conn->errorInfo();
		echo 'Failed: ' . ($errorInfo[2] ?? 'Unknown error');
	}
} catch (PDOException $e) {
	echo 'Failed: ' . $e->getMessage();
}
?>
