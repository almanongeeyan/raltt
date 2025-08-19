<?php
// Enable CORS for cross-origin requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit();
}

// Check if Twilio PHP SDK is available
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Twilio SDK not found. Please run composer install']);
    exit();
}

require __DIR__ . '/../vendor/autoload.php';

use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

// Your Twilio credentials
$sid = "ACb859b1d0b98e99dd9948bcc0901b71e4";
$token = "29f2a1c745f31079c1289a8d912c127e";
$verify_service_sid = "VA6d14e3699a4add30e49a090f0457c18e";

// Check if required parameters are submitted
if (!isset($_POST['phone']) || empty($_POST['phone']) || !isset($_POST['code']) || empty($_POST['code'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Phone number and verification code are required.']);
    exit();
}

$phoneNumber = trim($_POST['phone']);
$verificationCode = trim($_POST['code']);

// Validate phone number format
if (!preg_match('/^\+639[0-9]{9}$/', $phoneNumber)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid phone number format.']);
    exit();
}

// Validate verification code format (6 digits)
if (!preg_match('/^[0-9]{6}$/', $verificationCode)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Verification code must be 6 digits.']);
    exit();
}

// Rate limiting for verification attempts
session_start();
$ip = $_SERVER['REMOTE_ADDR'];
$rateLimitKey = "verification_check_attempts_" . $ip;
$maxAttempts = 10;
$timeWindow = 300; // 5 minutes

if (!isset($_SESSION[$rateLimitKey])) {
    $_SESSION[$rateLimitKey] = ['count' => 0, 'first_attempt' => time()];
}

// Reset counter if time window has passed
if (time() - $_SESSION[$rateLimitKey]['first_attempt'] > $timeWindow) {
    $_SESSION[$rateLimitKey] = ['count' => 0, 'first_attempt' => time()];
}

// Check if rate limit exceeded
if ($_SESSION[$rateLimitKey]['count'] >= $maxAttempts) {
    http_response_code(429);
    echo json_encode(['status' => 'error', 'message' => 'Too many verification attempts. Please wait 5 minutes before trying again.']);
    exit();
}

try {
    // Instantiate a new Twilio client
    $twilio = new Client($sid, $token);
    
    // Check the verification code
    $verificationCheck = $twilio->verify->v2->services($verify_service_sid)
                                           ->verificationChecks
                                           ->create([
                                               'to' => $phoneNumber,
                                               'code' => $verificationCode
                                           ]);
    
    // Increment rate limit counter
    $_SESSION[$rateLimitKey]['count']++;
    
    // Check the verification status
    if ($verificationCheck->status === 'approved') {
        // Log successful verification
        error_log("Phone number verified successfully: " . $phoneNumber . " from IP: " . $ip);
        
        echo json_encode([
            'status' => 'success', 
            'message' => 'Phone number verified successfully!',
            'phone' => $phoneNumber
        ]);
    } elseif ($verificationCheck->status === 'pending') {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Verification code is incorrect. Please try again.'
        ]);
    } elseif ($verificationCheck->status === 'expired') {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Verification code has expired. Please request a new one.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Verification failed. Please try again.'
        ]);
    }

} catch (TwilioException $e) {
    // Log Twilio-specific errors
    error_log("Twilio API Error during verification check for phone " . $phoneNumber . ": " . $e->getMessage() . " from IP: " . $ip);
    
    $errorMessage = 'Verification failed. ';
    
    // Provide user-friendly error messages for common Twilio errors
    if ($e->getCode() === 21608 || stripos($e->getMessage(), 'unverified') !== false) {
        $errorMessage = 'Twilio trial account limitation: you can only send SMS to verified numbers. Verify the phone number in your Twilio Console or upgrade your account.';
    } elseif (stripos($e->getMessage(), 'not a valid phone number') !== false) {
        $errorMessage .= 'Invalid phone number format.';
    } elseif (stripos($e->getMessage(), 'authentication') !== false || stripos($e->getMessage(), '401') !== false) {
        $errorMessage .= 'Authentication failed. Please check Twilio credentials.';
    } elseif (stripos($e->getMessage(), 'quota') !== false || $e->getCode() === 20429) {
        $errorMessage .= 'Service quota exceeded. Please try again later.';
    } else {
        $errorMessage .= 'Please try again later.';
    }
    
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $errorMessage]);
    
} catch (Exception $e) {
    // Log general errors
    error_log("General Error in check_verification.php: " . $e->getMessage() . " for phone " . $phoneNumber . " from IP: " . $ip);
    
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred. Please try again later.']);
}
?>