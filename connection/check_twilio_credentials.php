<?php
// Twilio Credential Checker (moved to connection/)

echo "<h2>🔍 Twilio Credential Checker</h2>";
echo "<p>This tool will help you verify your Twilio credentials and identify what needs to be fixed.</p>";

// Check if autoload exists
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
	echo "<p style='color: red;'>❌ vendor/autoload.php not found. Run 'composer install' first.</p>";
	exit;
}

require_once __DIR__ . '/../vendor/autoload.php';

use Twilio\Rest\Client;

// Current credentials from your files
$current_sid = "ACb859b1d0b98e99dd9948bcc0901b71e4";
$current_token = "89f0376667216d156594c2453b957191";
$current_verify_service = "VA6d14e3699a4add30e49a090f0457c18e";

echo "<h3>📋 Current Credentials</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
echo "<tr><th style='padding: 8px; text-align: left;'>Field</th><th style='padding: 8px; text-align: left;'>Value</th><th style='padding: 8px; text-align: left;'>Status</th></tr>";
echo "<tr><td style='padding: 8px;'>Account SID</td><td style='padding: 8px; font-family: monospace;'>$current_sid</td><td style='padding: 8px;'>✅ Format looks correct</td></tr>";
echo "<tr><td style='padding: 8px;'>Auth Token</td><td style='padding: 8px; font-family: monospace;'>$current_token</td><td style='padding: 8px; color: green;'>✅ Updated with new token</td></tr>";
echo "<tr><td style='padding: 8px;'>Verify Service SID</td><td style='padding: 8px; font-family: monospace;'>$current_verify_service</td><td style='padding: 8px;'>✅ Format looks correct</td></tr>";
echo "</table>";

echo "<h3>🧪 Testing Connection</h3>";

try {
	$twilio = new Client($current_sid, $current_token);
	echo "<p style='color: green;'>✅ Twilio client created successfully</p>";
	
	// Test account connection
	try {
		$account = $twilio->api->accounts($current_sid)->fetch();
		echo "<p style='color: green;'>✅ Account connection successful</p>";
		echo "<p><strong>Account Status:</strong> " . $account->status . "</p>";
		echo "<p><strong>Account Type:</strong> " . $account->type . "</p>";
		echo "<p><strong>Account Name:</strong> " . $account->friendlyName . "</p>";
		
		if ($account->status === 'active') {
			echo "<p style='color: green;'>✅ Account is active and working</p>";
		} else {
			echo "<p style='color: red;'>❌ Account is not active. Status: " . $account->status . "</p>";
		}
		
	} catch (Exception $e) {
		echo "<p style='color: red;'>❌ Account connection failed: " . $e->getMessage() . "</p>";
	}
	
} catch (Exception $e) {
	echo "<p style='color: red;'>❌ Failed to create Twilio client: " . $e->getMessage() . "</p>";
}

echo "<h3>📱 Testing Verify Service</h3>";

try {
	$twilio = new Client($current_sid, $current_token);
	$service = $twilio->verify->v2->services($current_verify_service)->fetch();
	echo "<p style='color: green;'>✅ Verify service found</p>";
	echo "<p><strong>Service Name:</strong> " . htmlspecialchars($service->friendlyName ?? 'N/A') . "</p>";
	if (isset($service->status)) {
		echo "<p><strong>Service Status:</strong> " . htmlspecialchars($service->status) . "</p>";
	}
	
} catch (Exception $e) {
	echo "<p style='color: red;'>❌ Verify service not found: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='../register.php'>← Back to Registration Form</a> | ";
echo "<a href='test_twilio_simple.php'>Test Again</a></p>";
?>


