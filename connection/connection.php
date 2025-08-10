<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "raltt_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Assign the PDO object to a variable that other scripts can use
$db_connection = $conn;
?>