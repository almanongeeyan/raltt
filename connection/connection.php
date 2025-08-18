<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "raltt_db";

// Additional configuration options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // Uses native prepared statements
    PDO::ATTR_PERSISTENT         => false, // Better for most web apps
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci" // Proper charset
];

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8mb4", 
        $username, 
        $password,
        $options
    );
    
    // Assign the PDO object to a variable that other scripts can use
    $db_connection = $conn;

} catch(PDOException $e) {
    // Log the full error with timestamp
    error_log("[".date('Y-m-d H:i:s')."] Database connection failed: " . $e->getMessage() . PHP_EOL, 3, "database_errors.log");

    // Send a JSON response
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Service unavailable. Please try again later.'
    ]);
    exit();
}
?>