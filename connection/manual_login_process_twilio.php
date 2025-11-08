<?php
// Manual login process with Twilio verification
session_start();
header('Content-Type: application/json');

require_once 'connection.php';

$response = [
    'status' => 'error',
    'message' => 'Unknown error',
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    http_response_code(405);
    echo json_encode($response);
    exit();
}

$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$code = trim($_POST['code'] ?? '');

if (empty($phone) || empty($password)) {
    $response['message'] = 'Phone and password are required.';
    http_response_code(400);
    echo json_encode($response);
    exit();
}

if (empty($code)) {
    $response['message'] = 'Verification code is required.';
    http_response_code(400);
    echo json_encode($response);
    exit();
}

// 1. Check verification code with Twilio
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
$verifyUrl = rtrim($baseUrl, '/\\') . '/check_verification.php';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $verifyUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['phone' => $phone, 'code' => $code]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$verifyResult = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);
$verifyData = json_decode($verifyResult, true);
if (!$verifyData || $verifyData['status'] !== 'success') {
    $response['message'] = ($verifyData['message'] ?? 'Phone verification failed.') . ($curlError ? ' [CURL: ' . $curlError . ']' : '');
    $response['debug'] = [
        'verify_url' => $verifyUrl,
        'verify_result' => $verifyResult,
        'curl_error' => $curlError
    ];
    http_response_code(401);
    echo json_encode($response);
    exit();
}

// 2. Check user credentials
$stmt = $db_connection->prepare('SELECT * FROM users WHERE phone_number = ? LIMIT 1');
$stmt->execute([$phone]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user || !password_verify($password, $user['password_hash'])) {
    $response['message'] = 'Wrong phone number or password.';
    http_response_code(401);
    echo json_encode($response);
    exit();
}

// 3. Set session and respond
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_role'] = $user['user_role'];
$response['status'] = 'success';
$response['message'] = 'Login successful!';
$response['redirect'] = '../raltt/logged_user/landing_page.php';
echo json_encode($response);
exit();
