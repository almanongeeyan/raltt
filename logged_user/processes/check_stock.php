<?php
require_once '../../connection/connection.php';
session_start();

header('Content-Type: application/json');

if (!isset($_POST['product_id'])) {
    echo json_encode(['success' => false, 'error' => 'Product ID is required']);
    exit;
}

$product_id = intval($_POST['product_id']);
$branch_id = isset($_SESSION['branch_id']) ? intval($_SESSION['branch_id']) : 1;

try {
    $stmt = $conn->prepare('SELECT stock_count FROM product_branches WHERE product_id = ? AND branch_id = ?');
    $stmt->execute([$product_id, $branch_id]);
    $stock = $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'stock_count' => $stock
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to check stock availability'
    ]);
}
?>