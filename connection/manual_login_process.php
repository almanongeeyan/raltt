<?php
session_start();
// Prevent back navigation after login
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
// If already logged in, redirect to landing page
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    header('Location: logged_user/landing_page.php');
    exit();
}
require_once 'connection.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Initialize response array
$response = [
    'status' => 'error',
    'message' => 'Unknown error occurred'
];

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    http_response_code(405);
    echo json_encode($response);
    exit();
}

// Get and sanitize input data
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';

// Validate required fields
if (empty($phone) || empty($password)) {
    $response['message'] = 'Phone number and password are required';
    http_response_code(400);
    echo json_encode($response);
    exit();
}

// Validate phone number format
if (!preg_match('/^\+639[0-9]{9}$/', $phone)) {
    $response['message'] = 'Invalid phone number format';
    http_response_code(400);
    echo json_encode($response);
    exit();
}

try {
    // Check if user exists
    $stmt = $db_connection->prepare("SELECT * FROM manual_accounts WHERE phone_number = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $response['message'] = 'Phone number not registered';
        http_response_code(401);
        echo json_encode($response);
        exit();
    }

    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        $response['message'] = 'Incorrect password';
        http_response_code(401);
        echo json_encode($response);
        exit();
    }

    // Check account status
    if ($user['account_status'] !== 'active') {
        $response['message'] = 'Your account is not active. Please contact support.';
        http_response_code(403);
        echo json_encode($response);
        exit();
    }

    // Login successful - set session variables
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['phone_number'] = $user['phone_number'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['account_type'] = 'manual';

    // Update last login time
    $updateStmt = $db_connection->prepare("UPDATE manual_accounts SET last_login = NOW() WHERE user_id = ?");
    $updateStmt->execute([$user['user_id']]);

    // Success response
    $response['status'] = 'success';
    $response['message'] = 'Login successful';
    $response['redirect'] = 'logged_user/landing_page.php';
    http_response_code(200);

} catch (PDOException $e) {
    error_log("[".date('Y-m-d H:i:s')."] Login error: " . $e->getMessage() . PHP_EOL, 3, "database_errors.log");
    $response['message'] = 'Database error during login';
    http_response_code(500);
}

// Return JSON response
echo json_encode($response);
exit();