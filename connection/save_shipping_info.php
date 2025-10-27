<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/connection.php';
$conn = $db_connection;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];


// Accept both camelCase and snake_case keys for compatibility
$fields = [
    'full_name' => isset($_POST['full_name']) ? trim($_POST['full_name']) : (isset($_POST['fullName']) ? trim($_POST['fullName']) : null),
    'phone_number' => isset($_POST['phone_number']) ? trim($_POST['phone_number']) : (isset($_POST['contactNumber']) ? trim($_POST['contactNumber']) : null),
    'house_address' => isset($_POST['house_address']) ? trim($_POST['house_address']) : (isset($_POST['houseAddress']) ? trim($_POST['houseAddress']) : null),
    'full_address' => isset($_POST['full_address']) ? trim($_POST['full_address']) : (isset($_POST['pinLocation']) ? trim($_POST['pinLocation']) : null),
];

// Remove fields that are null or empty (not sent or blank)
$updateFields = array_filter($fields, function($v) { return $v !== null && $v !== ''; });

if (empty($updateFields)) {
    echo json_encode(['status' => 'error', 'message' => 'No changes to update.']);
    exit;
}

try {
    $setParts = [];
    $params = [];
    foreach ($updateFields as $key => $value) {
        $setParts[] = "$key = :$key";
        $params[":$key"] = $value;
    }
    $params[':id'] = $user_id;
    $sql = 'UPDATE users SET ' . implode(', ', $setParts) . ' WHERE id = :id';
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute($params);
    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
