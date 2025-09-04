<?php
// store_product.php
require_once '../../connection/connection.php';


function storeProduct($data, $file = null) {
    global $db_connection;
    if (session_status() === PHP_SESSION_NONE) session_start();
    $isTile = ($data['product_type'] === 'tile');

    $sql = "INSERT INTO products (
        product_type, product_name, product_price, product_description, product_image, product_spec, tile_type, tile_design, sku, is_popular, is_hot, is_best_seller, is_archived
    ) VALUES (
        :product_type, :product_name, :product_price, :product_description, :product_image, :product_spec, :tile_type, :tile_design, :sku, :is_popular, :is_hot, :is_best_seller, :is_archived
    )";

    // Handle image upload (optional)
    $imageData = null;
    if ($file && isset($file['tmp_name']) && is_uploaded_file($file['tmp_name'])) {
        $imageData = file_get_contents($file['tmp_name']);
    }

    $stmt = $db_connection->prepare($sql);
    $stmt->bindValue(':product_type', $data['product_type']);
    $stmt->bindValue(':product_name', $data['product_name']);
    $stmt->bindValue(':product_price', $data['product_price']);
    $stmt->bindValue(':product_description', $data['product_description']);
    $stmt->bindValue(':product_image', $imageData, PDO::PARAM_LOB);
    $stmt->bindValue(':product_spec', $isTile ? null : ($data['product_spec'] ?? null));
    $stmt->bindValue(':tile_type', $isTile ? ($data['tile_type'] ?? null) : null);
    $stmt->bindValue(':tile_design', $isTile ? ($data['tile_design'] ?? null) : null);
    $stmt->bindValue(':sku', $data['sku'] ?? null);
    $stmt->bindValue(':is_popular', $data['is_popular'] ?? 0, PDO::PARAM_INT);
    $stmt->bindValue(':is_hot', $data['is_hot'] ?? 0, PDO::PARAM_INT);
    $stmt->bindValue(':is_best_seller', $data['is_best_seller'] ?? 0, PDO::PARAM_INT);
    $stmt->bindValue(':is_archived', isset($data['is_archived']) ? (int)$data['is_archived'] : 0, PDO::PARAM_INT);
    $stmt->execute();
    $productId = $db_connection->lastInsertId();

    // Insert into product_branches for stock using session branch_id
    $branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
    $stock_count = isset($data['stock_count']) ? (int)$data['stock_count'] : 0;
    if ($branch_id && $productId) {
        $sql2 = "INSERT INTO product_branches (product_id, branch_id, stock_count) VALUES (:product_id, :branch_id, :stock_count)";
        $stmt2 = $db_connection->prepare($sql2);
        $stmt2->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt2->bindValue(':branch_id', $branch_id, PDO::PARAM_INT);
        $stmt2->bindValue(':stock_count', $stock_count, PDO::PARAM_INT);
        $stmt2->execute();
    }
    return $productId;
}

// If this file is accessed via POST, handle the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    try {
        $data = $_POST;
        $file = isset($_FILES['product_image']) ? $_FILES['product_image'] : null;
        $productId = storeProduct($data, $file);
        echo json_encode(['status' => 'success', 'product_id' => $productId]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit();
}
