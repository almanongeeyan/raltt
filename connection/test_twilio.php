<?php
// Simple test file to verify Twilio integration (moved to connection/)

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
	echo "<p style='color: red;'>❌ vendor/autoload.php not found</p>";
	exit;
}

require_once __DIR__ . '/../vendor/autoload.php';

use Twilio\Rest\Client;

// Your Twilio credentials
$sid = "ACb859b1d0b98e99dd9948bcc0901b71e4";
$token = "29f2a1c745f31079c1289a8d912c127e";
$verify_service_sid = "VA6d14e3699a4add30e49a090f0457c18e";

echo "<h2>Twilio Integration Test</h2>";

try {
	// Test 1: Check if Twilio client can be instantiated
	echo "<h3>Test 1: Twilio Client Instantiation</h3>";
	$twilio = new Client($sid, $token);
	echo "✅ Twilio client created successfully<br>";
	
	// Test 2: Check if Verify service exists
	echo "<h3>Test 2: Verify Service Check</h3>";
	try {
		$service = $twilio->verify->v2->services($verify_service_sid)->fetch();
		echo "✅ Verify service found: " . htmlspecialchars($service->friendlyName ?? 'N/A') . "<br>";
		echo "Service SID: " . $service->sid . "<br>";
		if (isset($service->status)) {
			echo "Service Status: " . htmlspecialchars($service->status) . "<br>";
		}
	} catch (Exception $e) {
		echo "❌ Verify service error: " . $e->getMessage() . "<br>";
	}
	
	// Test 3: Check account information
	echo "<h3>Test 3: Account Information</h3>";
	try {
		$account = $twilio->api->accounts($sid)->fetch();
		echo "✅ Account found: " . $account->friendlyName . "<br>";
		echo "Account Status: " . $account->status . "<br>";
		echo "Account Type: " . $account->type . "<br>";
	} catch (Exception $e) {
		echo "❌ Account error: " . $e->getMessage() . "<br>";
	}
	
	echo "<h3>Test Summary</h3>";
	echo "If you see all ✅ marks above, your Twilio integration is working correctly!<br>";
	echo "You can now use the phone verification system in your registration form.<br>";
	
} catch (Exception $e) {
	echo "<h3>❌ Critical Error</h3>";
	echo "Failed to initialize Twilio: " . $e->getMessage() . "<br>";
	echo "Please check your credentials and ensure the Twilio SDK is properly installed.<br>";
}

echo "<hr>";
echo "<p><strong>Note:</strong> This is a test file. Remove it from production for security reasons.</p>";
echo "<p><a href='../register.php'>Go to Registration Form</a></p>";
?>


