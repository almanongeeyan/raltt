<?php
// Simple Twilio connection test
// This will help identify if the issue is with the SDK or the API call

echo "<h2>Twilio Connection Test</h2>";

// Check if autoload exists
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
	echo "<p style='color: red;'>❌ vendor/autoload.php not found. Run 'composer install' first.</p>";
	exit;
}

require_once __DIR__ . '/../vendor/autoload.php';

use Twilio\Rest\Client;

// Your credentials
$sid = "ACb859b1d0b98e99dd9948bcc0901b71e4";
$token = "89f0376667216d156594c2453b957191";
$verify_service_sid = "VA6d14e3699a4add30e49a090f0457c18e";

echo "<h3>Step 1: Testing Twilio Client Creation</h3>";

try {
	$twilio = new Client($sid, $token);
	echo "<p style='color: green;'>✅ Twilio client created successfully</p>";
} catch (Exception $e) {
	echo "<p style='color: red;'>❌ Failed to create Twilio client: " . $e->getMessage() . "</p>";
	exit;
}

echo "<h3>Step 2: Testing Account Connection</h3>";

try {
	$account = $twilio->api->accounts($sid)->fetch();
	echo "<p style='color: green;'>✅ Account connection successful</p>";
	echo "<p>Account Status: " . $account->status . "</p>";
	echo "<p>Account Type: " . $account->type . "</p>";
} catch (Exception $e) {
	echo "<p style='color: red;'>❌ Account connection failed: " . $e->getMessage() . "</p>";
	echo "<p>This suggests an authentication issue with your credentials.</p>";
}

echo "<h3>Step 3: Testing Verify Service</h3>";

try {
	$service = $twilio->verify->v2->services($verify_service_sid)->fetch();
	echo "<p style='color: green;'>✅ Verify service found</p>";
	echo "<p>Service Name: " . htmlspecialchars($service->friendlyName ?? 'N/A') . "</p>";
	// Some SDK versions may not expose 'status' on verify Service fetch
	if (isset($service->status)) {
		echo "<p>Service Status: " . htmlspecialchars($service->status) . "</p>";
	}
} catch (Exception $e) {
	echo "<p style='color: red;'>❌ Verify service not found: " . $e->getMessage() . "</p>";
	echo "<p>This suggests the Verify Service SID is incorrect or the service doesn't exist.</p>";
}

echo "<h3>Step 4: Testing Phone Number Validation</h3>";

$testPhone = "+639171234567"; // Test Philippine number

try {
	$verification = $twilio->verify->v2->services($verify_service_sid)
						   ->verifications
						   ->create($testPhone, "sms");
	
	echo "<p style='color: green;'>✅ Test verification created successfully</p>";
	echo "<p>Verification Status: " . $verification->status . "</p>";
	echo "<p>Verification SID: " . $verification->sid . "</p>";
	
	// Note: We can't cancel verifications in Twilio, but this test shows the API is working
	echo "<p style='color: blue;'>ℹ️ Test verification created successfully - this means the API is working!</p>";
	
} catch (Exception $e) {
	echo "<p style='color: red;'>❌ Test verification failed: " . $e->getMessage() . "</p>";
	echo "<p>Error Code: " . $e->getCode() . "</p>";
}

echo "<hr>";
echo "<h3>Summary</h3>";
echo "<p>If you see any ❌ marks above, that's likely the cause of your verification failure.</p>";
echo "<p><a href='../register.php'>← Back to Registration Form</a></p>";
?>


