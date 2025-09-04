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
$token = "89f0376667216d156594c2453b957191";
$verify_service_sid = "VA6d14e3699a4add30e49a090f0457c18e";

// Check if a phone number was submitted
if (!isset($_POST['phone']) || empty($_POST['phone'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Phone number is required.']);
    exit();
}

$phoneNumber = trim($_POST['phone']);

// Enhanced phone number validation for Philippine format
if (!preg_match('/^\+639[0-9]{9}$/', $phoneNumber)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid phone number format. Must be in format +639xxxxxxxxx']);
    exit();
}

// Rate limiting - check if too many requests from same IP
session_start();
$ip = $_SERVER['REMOTE_ADDR'];
$rateLimitKey = "verification_attempts_" . $ip;
$maxAttempts = 5;
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
    // Log the attempt
    error_log("Attempting to send verification to: " . $phoneNumber . " from IP: " . $ip);
    
    // Instantiate a new Twilio client
    $twilio = new Client($sid, $token);
    error_log("Twilio client created successfully");
    
    // Create and send the verification code via SMS
    error_log("Calling Twilio API with service SID: " . $verify_service_sid);
    
    $verification = $twilio->verify->v2->services($verify_service_sid)
                                       ->verifications
                                       ->create($phoneNumber, "sms");
    
    error_log("Twilio API call completed. Status: " . $verification->status);
    
    // Increment rate limit counter
    $_SESSION[$rateLimitKey]['count']++;
    
    // Check if the request to Twilio was successful
    if ($verification->status === 'pending') {
        // Log successful verification attempt
        error_log("Verification code sent successfully to: " . $phoneNumber . " from IP: " . $ip);
        
        echo json_encode([
            'status' => 'success', 
            'message' => 'Verification code sent successfully to ' . substr($phoneNumber, 0, 7) . '****' . substr($phoneNumber, -3),
            'phone' => $phoneNumber
        ]);
    } else {
        throw new Exception('Unexpected verification status: ' . $verification->status);
    }

} catch (TwilioException $e) {
    // Log Twilio-specific errors with full details
    error_log("Twilio API Error for phone " . $phoneNumber . ": " . $e->getMessage() . " from IP: " . $ip);
    error_log("Twilio Error Code: " . $e->getCode());
    error_log("Twilio Error Details: " . print_r($e, true));
    
    $errorMessage = 'Failed to send verification code. ';
    
    // Provide user-friendly error messages for common Twilio errors
    if ($e->getCode() === 21608 || stripos($e->getMessage(), 'unverified') !== false) {
        $errorMessage = 'Twilio trial account limitation: you can only send SMS to verified numbers. Verify the phone number in your Twilio Console or upgrade your account.';
    } elseif (stripos($e->getMessage(), 'not a valid phone number') !== false) {
        $errorMessage .= 'Invalid phone number format.';
    } elseif (stripos($e->getMessage(), 'authentication') !== false || stripos($e->getMessage(), '401') !== false) {
        $errorMessage .= 'Authentication failed. Please check Twilio credentials.';
    } elseif (stripos($e->getMessage(), 'quota') !== false || $e->getCode() === 20429) {
        $errorMessage .= 'Service quota exceeded. Please try again later.';
    } elseif (stripos($e->getMessage(), 'Service not found') !== false || stripos($e->getMessage(), '20404') !== false) {
        $errorMessage .= 'Verification service not found. Please contact support.';
    } else {
        $errorMessage .= 'Please try again later.';
    }
    
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $errorMessage]);
    
} catch (Exception $e) {
    // Log general errors with full details
    error_log("General Error in send_verification.php: " . $e->getMessage() . " for phone " . $phoneNumber . " from IP: " . $ip);
    error_log("Error Code: " . $e->getCode());
    error_log("Error File: " . $e->getFile() . " Line: " . $e->getLine());
    error_log("Error Trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred. Please try again later.']);
}
?>