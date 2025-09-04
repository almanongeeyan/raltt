<?php
// Twilio Credential Update Tool (moved to connection/)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$new_sid = trim($_POST['new_sid'] ?? '');
	$new_token = trim($_POST['new_token'] ?? '');
	$new_verify_service = trim($_POST['new_verify_service'] ?? '');
	
	if (!empty($new_sid) && !empty($new_token) && !empty($new_verify_service)) {
		// Update the verification files
		$files_to_update = [
			'connection/send_verification.php',
			'connection/send_verification_debug.php',
			'connection/check_verification.php',
			'connection/test_twilio_simple.php',
			'connection/check_twilio_credentials.php'
		];
		
		$update_count = 0;
		foreach ($files_to_update as $file) {
			if (file_exists(__DIR__ . '/../' . basename($file))) {
				$content = file_get_contents(__DIR__ . '/../' . basename($file));
			} elseif (file_exists($file)) {
				$content = file_get_contents($file);
			} else {
				continue;
			}
			
			// Replace known credentials with new ones (handles current and previous values)
			$known_sids = [
				'AC4ce3b22a6b0813ddabc8af53330f2b63',
				'ACb859b1d0b98e99dd9948bcc0901b71e4'
			];
			$known_tokens = [
				'6ac768c348b35d8b86f276612c2fca8f',
				'd8ab9cabe63989d12608d9f062ba588e',
				'29f2a1c745f31079c1289a8d912c127e'
			];
			$known_verify_services = [
				'VA332fb460c09cf1680db23118718cad64',
				'VA6d14e3699a4add30e49a090f0457c18e'
			];

			foreach ($known_sids as $old) {
				$content = str_replace($old, $new_sid, $content);
			}
			foreach ($known_tokens as $old) {
				$content = str_replace($old, $new_token, $content);
			}
			foreach ($known_verify_services as $old) {
				$content = str_replace($old, $new_verify_service, $content);
			}
			
			if (file_exists($file)) {
				if (file_put_contents($file, $content)) {
					$update_count++;
				}
			}
		}
		
		$message = "✅ Successfully updated $update_count files with new credentials!";
		$message_type = "success";
	} else {
		$message = "❌ Please fill in all fields.";
		$message_type = "error";
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Update Twilio Credentials</title>
	<style>
		body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
		.form-group { margin-bottom: 20px; }
		label { display: block; margin-bottom: 5px; font-weight: bold; }
		input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: monospace; }
		button { background: #007bff; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; }
		button:hover { background: #0056b3; }
		.message { padding: 15px; border-radius: 4px; margin-bottom: 20px; }
		.success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
		.error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
		.info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
		.credential-box { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 4px; margin: 20px 0; }
	</style>
</head>
<body>
	<h1>🔑 Update Twilio Credentials</h1>
	
	<?php if (isset($message)): ?>
		<div class="message <?php echo $message_type; ?>">
			<?php echo $message; ?>
		</div>
	<?php endif; ?>
	
	<div class="info message">
		<h3>📋 Instructions</h3>
		<p><strong>Step 1:</strong> Go to <a href="https://console.twilio.com/" target="_blank">Twilio Console</a></p>
		<p><strong>Step 2:</strong> Navigate to <strong>Account → API Keys & Tokens</strong></p>
		<p><strong>Step 3:</strong> Click <strong>"Regenerate"</strong> next to your Auth Token</p>
		<p><strong>Step 4:</strong> Copy the new credentials below</p>
		<p><strong>Step 5:</strong> Click "Update Credentials" to update all files</p>
	</div>
	
	<div class="credential-box">
		<h3>🔍 Current Credentials (from your files)</h3>
		<p><strong>Account SID:</strong> <code>ACb859b1d0b98e99dd9948bcc0901b71e4</code></p>
		<p><strong>Auth Token:</strong> <code>29f2a1c745f31079c1289a8d912c127e</code></p>
		<p><strong>Verify Service SID:</strong> <code>VA6d14e3699a4add30e49a090f0457c18e</code></p>
	</div>
	
	<form method="POST">
		<div class="form-group">
			<label for="new_sid">New Account SID:</label>
			<input type="text" id="new_sid" name="new_sid" placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" required>
		</div>
		
		<div class="form-group">
			<label for="new_token">New Auth Token:</label>
			<input type="text" id="new_token" name="new_token" placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" required>
		</div>
		
		<div class="form-group">
			<label for="new_verify_service">New Verify Service SID:</label>
			<input type="text" id="new_verify_service" name="new_verify_service" placeholder="VAxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" required>
		</div>
		
		<button type="submit">Update Credentials</button>
	</form>
	
	<hr>
	<p><a href="check_twilio_credentials.php">Check Credentials</a> | 
	   <a href="test_twilio_simple.php">Test Connection</a> | 
	   <a href="../register.php">Back to Registration</a></p>
</body>
</html>


