<?php
// Referral code processing for logged-in users
session_start();
header('Content-Type: application/json');

require_once '../../connection/connection.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
	exit();
}

if (!isset($_SESSION['user_id'])) {
	echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
	exit();
}

$user_id = $_SESSION['user_id'];
$referral_code = strtoupper(trim($_POST['referral_code'] ?? ''));


if (empty($referral_code) || !preg_match('/^[A-Z0-9]{6}$/', $referral_code)) {
	echo json_encode(['success' => false, 'message' => 'Invalid referral code format.']);
	exit();
}

try {
	// Get logged-in user's info
	$stmt = $db_connection->prepare('SELECT referral_code, has_used_referral_code FROM users WHERE id = ? LIMIT 1');
	$stmt->execute([$user_id]);
	$user = $stmt->fetch(PDO::FETCH_ASSOC);
	if (!$user) {
		echo json_encode(['success' => false, 'message' => 'User not found.']);
		exit();
	}

	if ($user['has_used_referral_code'] === 'TRUE') {
		echo json_encode(['success' => false, 'message' => 'You have already used a referral code.']);
		exit();
	}
	// Prevent users with blank/empty referral_code from using the system
	if (empty($user['referral_code']) || strlen($user['referral_code']) !== 6) {
		echo json_encode(['success' => false, 'message' => 'Your account does not have a valid referral code. Please contact support.']);
		exit();
	}
	if ($user['referral_code'] === $referral_code) {
		echo json_encode(['success' => false, 'message' => 'You cannot use your own referral code.']);
		exit();
	}


	// Check if referral code exists and get owner (with name)
	$stmt = $db_connection->prepare('SELECT id, referral_coins, full_name AS owner_name FROM users WHERE referral_code = ? LIMIT 1');
	$stmt->execute([$referral_code]);
	$owner = $stmt->fetch(PDO::FETCH_ASSOC);

	if (!$owner || empty($owner['id'])) {
		echo json_encode(['success' => false, 'message' => 'Referral code not found or invalid.']);
		exit();
	}
	if ($owner['id'] == $user_id) {
		echo json_encode(['success' => false, 'message' => 'You cannot use your own referral code.']);
		exit();
	}

	// Begin transaction
	$db_connection->beginTransaction();

	// 1. Set has_used_referral_code = 'TRUE' and add 5 coins to user
	$stmt = $db_connection->prepare('UPDATE users SET has_used_referral_code = "TRUE", referral_coins = referral_coins + 5 WHERE id = ?');
	$stmt->execute([$user_id]);

	// 2. Add 10 coins to owner
	$stmt = $db_connection->prepare('UPDATE users SET referral_coins = referral_coins + 10 WHERE id = ?');
	$stmt->execute([$owner['id']]);

	$db_connection->commit();

	echo json_encode([
		'success' => true,
		'message' => 'Referral code applied! You received 5 referral coins. <br><span class="font-semibold text-primary">'.$owner['owner_name'].'</span> received 10 coins as the code owner.',
		'owner_name' => $owner['owner_name']
	]);
	exit();

} catch (Exception $e) {
	if ($db_connection->inTransaction()) {
		$db_connection->rollBack();
	}
	error_log("[".date('Y-m-d H:i:s')."] Referral code error: " . $e->getMessage() . PHP_EOL, 3, __DIR__ . '/../../connection/database_errors.log');
	echo json_encode(['success' => false, 'message' => 'A server error occurred. Please try again.']);
	exit();
}
