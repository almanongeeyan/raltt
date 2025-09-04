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


// Branch staff login (by username/password, not phone)
if (isset($_POST['username']) && isset($_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    try {
        $stmt = $db_connection->prepare("SELECT * FROM branch_staff WHERE username = ?");
        $stmt->execute([$username]);
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$staff) {
            $response['message'] = 'Username not found';
            http_response_code(401);
            echo json_encode($response);
            exit();
        }
        // Accept plain password for legacy data, or password_verify for hashed
        $password_valid = password_verify($password, $staff['password_hash']) || $password === $staff['password_hash'];
        if (!$password_valid) {
            $response['message'] = 'Incorrect password';
            http_response_code(401);
            echo json_encode($response);
            exit();
        }
        // Set session for branch staff
        $_SESSION['branch_staff_logged_in'] = true;
        $_SESSION['branch_staff_id'] = $staff['staff_id'];
        $_SESSION['branch_id'] = $staff['branch_id'];
        $_SESSION['branch_staff_username'] = $staff['username'];
        $response['status'] = 'success';
        $response['message'] = 'Logged in as branch staff of branch ID: ' . $staff['branch_id'];
        $response['branch_id'] = $staff['branch_id'];
    $response['redirect'] = '/raltt/staffadmin_access/admin_analytics.php';
        http_response_code(200);
        echo json_encode($response);
        exit();
    } catch (PDOException $e) {
        error_log("[".date('Y-m-d H:i:s')."] Branch staff login error: " . $e->getMessage() . PHP_EOL, 3, "database_errors.log");
        $response['message'] = 'Database error during branch staff login';
        http_response_code(500);
        echo json_encode($response);
        exit();
    }
}

// Default: manual user login (phone/password)
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
if (!preg_match('/^\\+639[0-9]{9}$/', $phone)) {
    $response['message'] = 'Invalid phone number format';
    http_response_code(400);
    echo json_encode($response);
    exit();
}

try {
    // Check if user exists in users table
    $stmt = $db_connection->prepare("SELECT * FROM users WHERE phone_number = ? AND password_hash IS NOT NULL");
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
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['phone_number'] = $user['phone_number'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['account_type'] = 'manual';
    // You can add more session variables as needed

    // Update last login time
    $updateStmt = $db_connection->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $updateStmt->execute([$user['id']]);

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