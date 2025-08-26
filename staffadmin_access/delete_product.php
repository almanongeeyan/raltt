<?php
// delete_product.php
require_once '../connection/connection.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

// Accept both JSON and form POST
if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
} else {
    $data = $_POST;
}

$product_id = isset($data['product_id']) ? (int)$data['product_id'] : 0;
if (!$product_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing product_id.']);
    exit();
}

try {
    $unarchive = isset($data['unarchive']) ? (int)$data['unarchive'] : 0;
    if ($unarchive === 1) {
        // Unarchive: set is_archived = 0
        $stmt = $db_connection->prepare("UPDATE products SET is_archived = 0 WHERE product_id = :product_id");
    } else {
        // Archive: set is_archived = 1
        $stmt = $db_connection->prepare("UPDATE products SET is_archived = 1 WHERE product_id = :product_id");
    }
    $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
