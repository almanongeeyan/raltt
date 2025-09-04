<?php
// Simple error log viewer to help debug issues
echo "<h2>Error Log Checker</h2>";

// Check PHP error log
echo "<h3>PHP Error Log</h3>";
$phpErrorLog = ini_get('error_log');
if ($phpErrorLog && file_exists($phpErrorLog)) {
    echo "<p>PHP Error Log: $phpErrorLog</p>";
    $errors = file_get_contents($phpErrorLog);
    if ($errors) {
        echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>";
        echo htmlspecialchars($errors);
        echo "</pre>";
    } else {
        echo "<p style='color: green;'>✅ No PHP errors found</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ PHP error log not found or not configured</p>";
}

// Check database errors log
echo "<h3>Database Errors Log</h3>";
$dbErrorLog = 'connection/database_errors.log';
if (file_exists($dbErrorLog)) {
    echo "<p>Database Error Log: $dbErrorLog</p>";
    $errors = file_get_contents($dbErrorLog);
    if ($errors) {
        echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>";
        echo htmlspecialchars($errors);
        echo "</pre>";
    } else {
        echo "<p style='color: green;'>✅ No database errors found</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ Database error log not found</p>";
}

// Check if we can write to error log
echo "<h3>Error Logging Test</h3>";
$testMessage = "Test error log entry at " . date('Y-m-d H:i:s');
if (error_log($testMessage)) {
    echo "<p style='color: green;'>✅ Error logging is working</p>";
} else {
    echo "<p style='color: red;'>❌ Error logging is not working</p>";
}

echo "<hr>";
echo "<p><a href='connection/test_twilio_simple.php'>Test Twilio Connection</a> | ";
echo "<a href='register.php'>Back to Registration</a></p>";
?>
