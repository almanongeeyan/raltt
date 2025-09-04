<?php
// Include database connection
require_once 'connection.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Initialize response array
$response = [
    'status' => 'error',
    'message' => 'Unknown error occurred',
    'errors' => []
];

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    http_response_code(405);
    echo json_encode($response);
    exit();
}

// Get and sanitize input data
$fullname = trim($_POST['fullname'] ?? '');
$phone = trim($_POST['verified_phone'] ?? ''); // Using the verified phone from hidden input

$house_address = trim($_POST['house_address'] ?? '');
$address = trim($_POST['address'] ?? '');
$password = $_POST['password'] ?? '';
$referral_code = trim($_POST['referral_code'] ?? ''); // Optional, if provided by user


// Validate required fields
$requiredFields = [
    'fullname' => $fullname,
    'verified_phone' => $phone,
    'house_address' => $house_address,
    'address' => $address,
    'password' => $password
];

foreach ($requiredFields as $field => $value) {
    if (empty($value)) {
        $response['errors'][$field] = 'This field is required';
    }
}

// Additional validations
if (!empty($phone) && !preg_match('/^\+639[0-9]{9}$/', $phone)) {
    $response['errors']['phone'] = 'Invalid phone number format';
}

if (strlen($password) < 8) {
    $response['errors']['password'] = 'Password must be at least 8 characters';
}

// Check if phone or email already exists in users table
try {
    $checkPhone = $db_connection->prepare("SELECT id FROM users WHERE phone_number = ?");
    $checkPhone->execute([$phone]);
    if ($checkPhone->rowCount() > 0) {
        $response['errors']['phone'] = 'This phone number is already registered';
    }
    // Optionally check for duplicate email if you collect it
    // $checkEmail = $db_connection->prepare("SELECT id FROM users WHERE email = ?");
    // $checkEmail->execute([$email]);
    // if ($checkEmail->rowCount() > 0) {
    //     $response['errors']['email'] = 'This email is already registered';
    // }
} catch (PDOException $e) {
    error_log("[".date('Y-m-d H:i:s')."] Phone check error: " . $e->getMessage() . PHP_EOL, 3, "database_errors.log");
    $response['message'] = 'Database error during phone verification';
    http_response_code(500);
    echo json_encode($response);
    exit();
}

// If there are validation errors, return them
if (!empty($response['errors'])) {
    $response['message'] = 'Validation failed';
    http_response_code(400);
    echo json_encode($response);
    exit();
}

// Hash the password
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

// Insert into users table
try {
    $stmt = $db_connection->prepare("
        INSERT INTO users (
            google_id,
            password_hash,
            full_name,
            email,
            phone_number,
            house_address,
            full_address,
            referral_code,
            referral_coins,
            has_used_referral_code,
            account_status
        ) VALUES (
            :google_id,
            :password_hash,
            :full_name,
            :email,
            :phone_number,
            :house_address,
            :full_address,
            :referral_code,
            :referral_coins,
            :has_used_referral_code,
            :account_status
        )
    ");

    $stmt->execute([
        ':google_id' => null,
        ':password_hash' => $passwordHash,
        ':full_name' => $fullname,
        ':email' => '', // No email collected in manual registration
        ':phone_number' => $phone,
        ':house_address' => $house_address,
        ':full_address' => $address,
        ':referral_code' => $referral_code,
        ':referral_coins' => 0,
        ':has_used_referral_code' => 0,
        ':account_status' => 'active'
    ]);

    // Success response
    $response['status'] = 'success';
    $response['message'] = 'Registration successful!';
    $response['user_id'] = $db_connection->lastInsertId();
    http_response_code(201);

} catch (PDOException $e) {
    error_log("[".date('Y-m-d H:i:s')."] Registration error: " . $e->getMessage() . PHP_EOL, 3, "database_errors.log");
    $response['message'] = 'Database error during registration';
    http_response_code(500);
}

// Return JSON response
echo json_encode($response);
exit();