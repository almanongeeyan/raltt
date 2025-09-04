<?php
// add_product_category.php
require_once '../../connection/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    if (!$product_id || !$category_id) {
        echo json_encode(['status' => 'error', 'message' => 'Product and category required']);
        exit;
    }
    try {
        $stmt = $db_connection->prepare('INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)');
        $stmt->execute([$product_id, $category_id]);
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
