<?php
// check_verification.php

// Load Composer's autoloader
require __DIR__ . '/../vendor/autoload.php';

use Twilio\Rest\Client;

// The credentials below are hardcoded for simplicity.
// For production, it's highly recommended to use environment variables.
$sid = 'AC4ce3b22a6b0813ddabc8af53330f2b63';
$token = '6ac768c348b35d8b86f276612c2fca8f';
$verify_service_sid = 'VA332fb460c09cf1680db23118718cad64';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['phone_number']) || !isset($_POST['verification_code'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}

$phoneNumber = trim($_POST['phone_number']);
$verificationCode = trim($_POST['verification_code']);

// This logic is crucial to convert a domestic number (e.g., 0917...) 
// to the international E.164 format (+63917...) that Twilio requires.
if (substr($phoneNumber, 0, 1) === '0' && strlen($phoneNumber) === 11) {
    $phoneNumber = '+63' . substr($phoneNumber, 1);
} elseif (strlen($phoneNumber) === 10 && substr($phoneNumber, 0, 1) === '9') {
    $phoneNumber = '+63' . $phoneNumber;
}

// Ensure the number is now in the correct E.164 format before proceeding.
if (!preg_match('/^\+[1-9]\d{1,14}$/', $phoneNumber)) {
    echo json_encode(['success' => false, 'message' => 'Invalid phone number format. Please use a number like 09171234567.']);
    exit();
}

try {
    $twilio = new Client($sid, $token);
    $verification_check = $twilio->verify->v2->services($verify_service_sid)
                                     ->verificationChecks
                                     ->create([
                                         "to" => $phoneNumber,
                                         "code" => $verificationCode
                                     ]);

    if ($verification_check->status === 'approved') {
        echo json_encode(['success' => true, 'message' => 'Verification successful!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid verification code.']);
    }
} catch (Exception $e) {
    error_log("Twilio verification check failed: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred during verification.']);
}