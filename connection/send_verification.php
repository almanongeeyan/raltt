<?php
// send_verification.php

require __DIR__ . '/../vendor/autoload.php';

use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

header('Content-Type: application/json');

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['phone_number'])) {
    echo json_encode(['success' => false, 'message' => 'Phone number is required.']);
    exit();
}

// Twilio credentials - should ideally be in environment variables
$sid = 'AC4ce3b22a6b0813ddabc8af53330f2b63';
$token = '6ac768c348b35d8b86f276612c2fca8f';
$verify_service_sid = 'VA332fb460c09cf1680db23118718cad64';

$phoneNumber = trim($_POST['phone_number']);

// Normalize the phone number
try {
    // Remove all non-digit characters
    $digits = preg_replace('/[^0-9]/', '', $phoneNumber);
    
    // Handle different number formats
    if (strlen($digits) === 11 && $digits[0] === '0') {
        // Philippine format: 09171234567 → +639171234567
        $phoneNumber = '+63' . substr($digits, 1);
    } elseif (strlen($digits) === 10 && $digits[0] === '9') {
        // Philippine format: 9171234567 → +639171234567
        $phoneNumber = '+63' . $digits;
    } elseif (strlen($digits) === 12 && substr($digits, 0, 2) === '63') {
        // Philippine format: 639171234567 → +639171234567
        $phoneNumber = '+' . $digits;
    } elseif (!empty($digits)) {
        // For other numbers, just prepend + if not present
        if (strpos($phoneNumber, '+') !== 0) {
            $phoneNumber = '+' . $digits;
        }
    }

    // Final validation
    if (!preg_match('/^\+[1-9]\d{1,14}$/', $phoneNumber)) {
        throw new Exception('Invalid phone number format');
    }

    // Initialize Twilio client
    $twilio = new Client($sid, $token);
    
    // Send verification code
    $verification = $twilio->verify->v2->services($verify_service_sid)
                                     ->verifications
                                     ->create($phoneNumber, "sms");

    echo json_encode([
        'success' => true,
        'sid' => $verification->sid,
        'message' => 'Verification code sent successfully'
    ]);

} catch (TwilioException $e) {
    // More detailed Twilio-specific error messages
    $message = 'Failed to send verification code.';
    
    if (strpos($e->getMessage(), 'not found') !== false) {
        $message = 'Invalid verification service SID.';
    } elseif (strpos($e->getMessage(), 'authenticate') !== false) {
        $message = 'Invalid Twilio credentials.';
    } elseif (strpos($e->getMessage(), 'not a valid phone number') !== false) {
        $message = 'Invalid phone number format.';
    }
    
    error_log("Twilio Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $message]);

} catch (Exception $e) {
    error_log("General Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}