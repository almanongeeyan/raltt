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


// Admin login (by email/password/code)
if (isset($_GET['admin']) && $_GET['admin'] == 1) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $code = $_POST['code'] ?? '';
    if (empty($email) || empty($password) || empty($code)) {
        $response['message'] = 'Email, password, and code are required';
        http_response_code(400);
        echo json_encode($response);
        exit();
    }
    try {
        // Find user (admin/staff) by email
        $stmt = $db_connection->prepare("SELECT * FROM users WHERE LOWER(email) = LOWER(?) AND user_role IN ('ADMIN','ENCODER','CASHIER','DRIVER')");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            $response['message'] = 'Email not found or not an admin/staff.';
            http_response_code(401);
            echo json_encode($response);
            exit();
        }
        // Check password
        if (!password_verify($password, $user['password_hash'])) {
            $response['message'] = 'Incorrect password.';
            http_response_code(401);
            echo json_encode($response);
            exit();
        }
        // Check account status
        if (strtolower($user['account_status']) !== 'active') {
            $response['message'] = 'Your account is inactive. Please contact support.';
            http_response_code(403);
            echo json_encode($response);
            exit();
        }
        // Check code in email_verifications (not expired, not used)
        $stmt = $db_connection->prepare("SELECT * FROM email_verifications WHERE email = ? AND verification_code = ? AND is_used = 0 AND expires_at > UTC_TIMESTAMP() ORDER BY verification_id DESC LIMIT 1");
        $stmt->execute([$email, $code]);
        $codeRow = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$codeRow) {
            $response['message'] = 'Invalid or expired code.';
            http_response_code(401);
            echo json_encode($response);
            exit();
        }
        // Mark code as used
        $db_connection->prepare('UPDATE email_verifications SET is_used = 1 WHERE verification_id = ?')->execute([$codeRow['verification_id']]);
        // Set session for admin
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $user['id'];
    $_SESSION['admin_email'] = $user['email'];
    $_SESSION['admin_name'] = $user['full_name'];
    $_SESSION['user_role'] = strtoupper($user['user_role']);
    if (isset($user['branch_id'])) {
        $_SESSION['branch_id'] = $user['branch_id'];
    }
        $response['status'] = 'success';
        $response['message'] = 'Admin login successful.';
        // Redirect based on user_role
        if (strtoupper($user['user_role']) === 'ADMIN') {
            $response['redirect'] = '/raltt/staffadmin_access/admin_analytics.php';
        } else if (in_array(strtoupper($user['user_role']), ['ENCODER', 'CASHIER', 'DRIVER'])) {
            $response['redirect'] = '/raltt/staffadmin_access/admin_orders.php';
        } else {
            $response['redirect'] = '/raltt/logged_user/landing_page.php';
        }
        http_response_code(200);
        echo json_encode($response);
        exit();
    } catch (PDOException $e) {
        error_log("[".date('Y-m-d H:i:s')."] Admin login error: " . $e->getMessage() . PHP_EOL, 3, "database_errors.log");
        $response['message'] = 'Database error during admin login';
        http_response_code(500);
        echo json_encode($response);
        exit();
    }
}

// Default: manual user login (phone/password)
if (!(isset($_GET['admin']) && $_GET['admin'] == 1)) {
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

    // Check account status (case-insensitive)
    if (strtolower($user['account_status']) !== 'active') {
        $response['message'] = 'Your account is inactive. Please contact support to reactivate your account.';
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
    $_SESSION['user_role'] = strtoupper($user['user_role']);
    if (isset($user['branch_id'])) {
        $_SESSION['branch_id'] = $user['branch_id'];
    }
    // You can add more session variables as needed

    // Update last login time
    $updateStmt = $db_connection->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $updateStmt->execute([$user['id']]);

    // Success response
    $response['status'] = 'success';
    $response['message'] = 'Login successful';
    // Redirect based on role
    if (strtoupper($user['user_role']) === 'ADMIN') {
        $response['redirect'] = 'staffadmin_access/admin_analytics.php';
    } elseif (strtoupper($user['user_role']) === 'CASHIER') {
        $response['redirect'] = 'staffadmin_access/admin_salesreports.php';
    } elseif (strtoupper($user['user_role']) === 'ENCODER') {
        $response['redirect'] = 'staffadmin_access/admin_orders.php';
    } elseif (strtoupper($user['user_role']) === 'DRIVER') {
        $response['redirect'] = 'staffadmin_access/admin_orders.php';
    } else {
        $response['redirect'] = 'logged_user/landing_page.php';
    }
    http_response_code(200);

} catch (PDOException $e) {
    error_log("[".date('Y-m-d H:i:s')."] Login error: " . $e->getMessage() . PHP_EOL, 3, "database_errors.log");
    $response['message'] = 'Database error during login';
    http_response_code(500);
}

// Return JSON response
echo json_encode($response);
exit();
}