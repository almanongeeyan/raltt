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

$fullName = isset($_POST['fullName']) ? trim($_POST['fullName']) : '';
$contactNumber = isset($_POST['contactNumber']) ? trim($_POST['contactNumber']) : '';
$houseAddress = isset($_POST['houseAddress']) ? trim($_POST['houseAddress']) : '';
$pinLocation = isset($_POST['pinLocation']) ? trim($_POST['pinLocation']) : '';

if ($fullName === '' || $contactNumber === '' || $houseAddress === '' || $pinLocation === '') {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

try {
    $stmt = $conn->prepare('UPDATE users SET full_name = :full_name, phone_number = :phone_number, house_address = :house_address, full_address = :full_address WHERE id = :id');
    $result = $stmt->execute([
        ':full_name' => $fullName,
        ':phone_number' => $contactNumber,
        ':house_address' => $houseAddress,
        ':full_address' => $pinLocation,
        ':id' => $user_id
    ]);
    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
