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
$phone = trim($_POST['verified_phone'] ?? '');
// $email removed
$branch_id = null; // Always null, register.php does not provide branch_id

// Address fields
$region = trim($_POST['region'] ?? '');
$province = trim($_POST['province'] ?? '');
$city = trim($_POST['city'] ?? '');
$barangay = trim($_POST['barangay'] ?? '');
$detailed_address = trim($_POST['detailed_address'] ?? '');
$address = trim($_POST['address'] ?? '');
$password = $_POST['password'] ?? '';

// Compose house_address from all address fields
$house_address = $detailed_address;
if ($barangay) $house_address .= ', ' . $barangay;
if ($city) $house_address .= ', ' . $city;
if ($province) $house_address .= ', ' . $province;
if ($region) $house_address .= ', ' . $region;

// Hash the password
$passwordHash = password_hash($password, PASSWORD_BCRYPT);
$referral_code = trim($_POST['referral_code'] ?? '');

// Get user_role from POST, default to CUSTOMER
$user_role = strtoupper(trim($_POST['user_role'] ?? 'CUSTOMER'));
if (!in_array($user_role, ['CUSTOMER','ADMIN','ENCODER','CASHIER','DRIVER'])) {
    $user_role = 'CUSTOMER';
}

// Only set email_verified for ADMIN, CASHIER, DRIVER, ENCODER
$set_email_verified = in_array($user_role, ['ADMIN','CASHIER','DRIVER','ENCODER']);


// Validate required fields
$validationErrors = [];

if (empty($fullname)) {
    $validationErrors['fullname'] = 'Full name is required';
}
if (empty($phone)) {
    $validationErrors['phone'] = 'Phone number is required';
} elseif (!preg_match('/^\+639[0-9]{9}$/', $phone)) {
    $validationErrors['phone'] = 'Invalid phone number format';
}
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // email validation removed
}
if (empty($region)) {
    $validationErrors['region'] = 'Region is required';
}
if (empty($province)) {
    $validationErrors['province'] = 'Province is required';
}
if (empty($city)) {
    $validationErrors['city'] = 'City is required';
}
if (empty($barangay)) {
    $validationErrors['barangay'] = 'Barangay is required';
}
if (empty($detailed_address)) {
    $validationErrors['detailed_address'] = 'House/Street address is required';
}
if (empty($address)) {
    $validationErrors['address'] = 'Location address is required';
}
if (empty($password)) {
    $validationErrors['password'] = 'Password is required';
} elseif (strlen($password) < 8) {
    $validationErrors['password'] = 'Password must be at least 8 characters';
}

// Check if phone already exists
if (empty($validationErrors['phone'])) {
    try {
        $checkPhone = $db_connection->prepare("SELECT id FROM users WHERE phone_number = ?");
        $checkPhone->execute([$phone]);
        if ($checkPhone->rowCount() > 0) {
            $validationErrors['phone'] = 'This phone number is already registered';
        }
    } catch (PDOException $e) {
        error_log("Phone check error: " . $e->getMessage());
        $response['message'] = 'Database error during phone verification';
        http_response_code(500);
        echo json_encode($response);
        exit();
    }
}

// Return validation errors if any
if (!empty($validationErrors)) {
    $response['errors'] = $validationErrors;
    // Combine all error messages into one string for easier display
    $errorList = array_map(function($k, $v) { return ucfirst($k) . ': ' . $v; }, array_keys($validationErrors), $validationErrors);
    $response['message'] = "Please fix the following errors:\n" . implode("\n", $errorList);
    http_response_code(400);
    echo json_encode($response);
    exit();
}


// Insert into users table
try {
    if ($set_email_verified) {
        $stmt = $db_connection->prepare("
            INSERT INTO users (
                google_id,
                password_hash,
                user_role,
                branch_id,
                full_name,
                email,
                email_verified,
                phone_number,
                house_address,
                full_address,
                referral_code,
                referral_coins,
                has_used_referral_code,
                account_status,
                region,
                province,
                city,
                barangay,
                detailed_address
            ) VALUES (
                :google_id,
                :password_hash,
                :user_role,
                :branch_id,
                :full_name,
                :email,
                :email_verified,
                :phone_number,
                :house_address,
                :full_address,
                :referral_code,
                :referral_coins,
                :has_used_referral_code,
                :account_status,
                :region,
                :province,
                :city,
                :barangay,
                :detailed_address
            )
        ");
        $result = $stmt->execute([
            ':google_id' => null,
            ':password_hash' => $passwordHash,
            ':user_role' => $user_role,
            ':branch_id' => $branch_id,
            ':full_name' => $fullname,
            // email removed
            ':email' => null,
            ':email_verified' => 0,
            ':phone_number' => $phone,
            ':house_address' => $house_address,
            ':full_address' => $address,
            ':referral_code' => $referral_code !== '' ? $referral_code : null,
            ':referral_coins' => 0,
            ':has_used_referral_code' => 0,
            ':account_status' => 'active',
            ':region' => $region,
            ':province' => $province,
            ':city' => $city,
            ':barangay' => $barangay,
            ':detailed_address' => $detailed_address
        ]);
    } else {
        $stmt = $db_connection->prepare("
            INSERT INTO users (
                google_id,
                password_hash,
                user_role,
                branch_id,
                full_name,
                email,
                phone_number,
                house_address,
                full_address,
                referral_code,
                referral_coins,
                has_used_referral_code,
                account_status,
                region,
                province,
                city,
                barangay,
                detailed_address
            ) VALUES (
                :google_id,
                :password_hash,
                :user_role,
                :branch_id,
                :full_name,
                :email,
                :phone_number,
                :house_address,
                :full_address,
                :referral_code,
                :referral_coins,
                :has_used_referral_code,
                :account_status,
                :region,
                :province,
                :city,
                :barangay,
                :detailed_address
            )
        ");
        $result = $stmt->execute([
            ':google_id' => null,
            ':password_hash' => $passwordHash,
            ':user_role' => $user_role,
            ':branch_id' => $branch_id,
            ':full_name' => $fullname,
            // email removed
            ':email' => null,
            ':phone_number' => $phone,
            ':house_address' => $house_address,
            ':full_address' => $address,
            ':referral_code' => $referral_code !== '' ? $referral_code : null,
            ':referral_coins' => 0,
            ':has_used_referral_code' => 0,
            ':account_status' => 'active',
            ':region' => $region,
            ':province' => $province,
            ':city' => $city,
            ':barangay' => $barangay,
            ':detailed_address' => $detailed_address
        ]);
    }

    if ($result) {
        $response['status'] = 'success';
        $response['message'] = 'Registration successful! You can now login.';
        $response['user_id'] = $db_connection->lastInsertId();
        http_response_code(201);
    } else {
        throw new PDOException("Failed to execute insert statement");
    }
} catch (PDOException $e) {
    error_log("Registration error: " . $e->getMessage());
    $response['message'] = 'Database error during registration. Please try again.';
    $response['debug'] = [
        'error' => $e->getMessage(),
        'sql' => isset($stmt) && method_exists($stmt, 'queryString') ? $stmt->queryString : null,
        'params' => isset($result) ? $result : null
    ];
    http_response_code(500);
}

echo json_encode($response);
exit();