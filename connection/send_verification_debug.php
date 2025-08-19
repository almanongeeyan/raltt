<?php
// Debug version of send_verification.php
// This will show detailed information about what's happening

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

// For debugging, let's create a detailed response
$debugInfo = [
    'phone_number' => $phoneNumber,
    'twilio_sid' => $sid,
    'verify_service_sid' => $verify_service_sid,
    'timestamp' => date('Y-m-d H:i:s'),
    'steps' => []
];

try {
    $debugInfo['steps'][] = 'Starting verification process';
    
    // Instantiate a new Twilio client
    $twilio = new Client($sid, $token);
    $debugInfo['steps'][] = 'Twilio client created successfully';
    
    // Test account connection first
    try {
        $account = $twilio->api->accounts($sid)->fetch();
        $debugInfo['steps'][] = 'Account connection successful - Status: ' . $account->status;
    } catch (Exception $e) {
        $debugInfo['steps'][] = 'Account connection failed: ' . $e->getMessage();
        throw new Exception('Account authentication failed: ' . $e->getMessage());
    }
    
    // Test verify service
    try {
        $service = $twilio->verify->v2->services($verify_service_sid)->fetch();
        // Some SDK versions may not expose 'status'; avoid accessing it to prevent fatal errors
        $serviceName = isset($service->friendlyName) ? $service->friendlyName : 'Unknown';
        $debugInfo['steps'][] = 'Verify service found - Name: ' . $serviceName;
    } catch (Exception $e) {
        $debugInfo['steps'][] = 'Verify service not found: ' . $e->getMessage();
        throw new Exception('Verify service not found: ' . $e->getMessage());
    }
    
    // Create and send the verification code via SMS
    $debugInfo['steps'][] = 'Attempting to create verification';
    
    $verification = $twilio->verify->v2->services($verify_service_sid)
                                   ->verifications
                                   ->create($phoneNumber, "sms");
    
    $debugInfo['steps'][] = 'Verification created successfully';
    $debugInfo['verification_status'] = $verification->status;
    $debugInfo['verification_sid'] = $verification->sid;
    
    // Check if the request to Twilio was successful
    if ($verification->status === 'pending') {
        $debugInfo['steps'][] = 'Verification status is pending - SMS sent successfully';
        
        echo json_encode([
            'status' => 'success', 
            'message' => 'Verification code sent successfully to ' . substr($phoneNumber, 0, 7) . '****' . substr($phoneNumber, -3),
            'phone' => $phoneNumber,
            'debug' => $debugInfo
        ]);
    } else {
        throw new Exception('Unexpected verification status: ' . $verification->status);
    }

} catch (TwilioException $e) {
    $debugInfo['steps'][] = 'Twilio API Error: ' . $e->getMessage();
    $debugInfo['error_code'] = $e->getCode();
    $debugInfo['error_type'] = 'TwilioException';
    
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
    echo json_encode([
        'status' => 'error', 
        'message' => $errorMessage,
        'debug' => $debugInfo
    ]);
    
} catch (Exception $e) {
    $debugInfo['steps'][] = 'General Error: ' . $e->getMessage();
    $debugInfo['error_code'] = $e->getCode();
    $debugInfo['error_type'] = 'Exception';
    $debugInfo['error_file'] = $e->getFile();
    $debugInfo['error_line'] = $e->getLine();
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'An unexpected error occurred. Please try again later.',
        'debug' => $debugInfo
    ]);
}
?>
