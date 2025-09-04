<?php
// add_tile_category.php
require_once '../../connection/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = trim($_POST['category_name'] ?? '');
    if ($category_name === '') {
        echo json_encode(['status' => 'error', 'message' => 'Category name required']);
        exit;
    }
    try {
        $stmt = $db_connection->prepare('INSERT INTO tile_categories (category_name) VALUES (?)');
        $stmt->execute([$category_name]);
        $category_id = $db_connection->lastInsertId();
        echo json_encode(['status' => 'success', 'category_id' => $category_id, 'category_name' => $category_name]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
