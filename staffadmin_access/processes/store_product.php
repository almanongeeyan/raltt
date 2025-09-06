<?php
// store_product.php
require_once '../../connection/connection.php';


function storeProduct($data, $file = null) {
    global $db_connection;
    if (session_status() === PHP_SESSION_NONE) session_start();
    $isTile = ($data['product_type'] === 'tile');

    $sql = "INSERT INTO products (
        product_type, product_name, product_price, product_description, product_image, product_spec, sku, is_popular, is_best_seller, is_archived
    ) VALUES (
        :product_type, :product_name, :product_price, :product_description, :product_image, :product_spec, :sku, :is_popular, :is_best_seller, :is_archived
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
    $stmt->bindValue(':sku', $data['sku'] ?? null);
    $stmt->bindValue(':is_popular', $data['is_popular'] ?? 0, PDO::PARAM_INT);
    $stmt->bindValue(':is_best_seller', $data['is_best_seller'] ?? 0, PDO::PARAM_INT);
    $stmt->bindValue(':is_archived', isset($data['is_archived']) ? (int)$data['is_archived'] : 0, PDO::PARAM_INT);
    $stmt->execute();
    $productId = $db_connection->lastInsertId();

    // Insert into product_branches for stock using session branch_id
    $branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
    $stock_count = isset($data['stock_count']) ? (int)$data['stock_count'] : 0;
    // Debug log branch_id and productId
    file_put_contents(__DIR__ . '/store_product_debug.log', date('Y-m-d H:i:s') . " | branch_id: $branch_id | product_id: $productId | stock_count: $stock_count\n", FILE_APPEND);
    if ($branch_id && $productId) {
        $sql2 = "INSERT INTO product_branches (product_id, branch_id, stock_count) VALUES (:product_id, :branch_id, :stock_count)";
        $stmt2 = $db_connection->prepare($sql2);
        $stmt2->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt2->bindValue(':branch_id', $branch_id, PDO::PARAM_INT);
        $stmt2->bindValue(':stock_count', $stock_count, PDO::PARAM_INT);
        $stmt2->execute();
    }
    // Insert into new many-to-many tables for tiles
    if ($isTile && $productId) {
        // Tile Designs (multi)
        if (!empty($data['tile_design']) && is_array($data['tile_design'])) {
            $stmtDesign = $db_connection->prepare('INSERT INTO product_designs (product_id, design_id) VALUES (?, ?)');
            foreach ($data['tile_design'] as $designName) {
                // Get design_id from tile_designs table
                $designId = null;
                $q = $db_connection->prepare('SELECT design_id FROM tile_designs WHERE design_name = ? LIMIT 1');
                $q->execute([$designName]);
                $row = $q->fetch(PDO::FETCH_ASSOC);
                if ($row) $designId = $row['design_id'];
                if ($designId) $stmtDesign->execute([$productId, $designId]);
            }
        }
        // Classification (single)
        if (!empty($data['tile_classification'])) {
            $stmtClass = $db_connection->prepare('INSERT INTO product_classifications (product_id, classification_id) VALUES (?, ?)');
            // Get classification_id from tile_classifications table
            $q = $db_connection->prepare('SELECT classification_id FROM tile_classifications WHERE classification_name = ? LIMIT 1');
            $q->execute([$data['tile_classification']]);
            $row = $q->fetch(PDO::FETCH_ASSOC);
            if ($row) $stmtClass->execute([$productId, $row['classification_id']]);
        }
        // Finish (single)
        if (!empty($data['tile_finish'])) {
            $stmtFinish = $db_connection->prepare('INSERT INTO product_finishes (product_id, finish_id) VALUES (?, ?)');
            $q = $db_connection->prepare('SELECT finish_id FROM tile_finishes WHERE finish_name = ? LIMIT 1');
            $q->execute([$data['tile_finish']]);
            $row = $q->fetch(PDO::FETCH_ASSOC);
            if ($row) $stmtFinish->execute([$productId, $row['finish_id']]);
        }
        // Size (single)
        if (!empty($data['tile_size'])) {
            $stmtSize = $db_connection->prepare('INSERT INTO product_sizes (product_id, size_id) VALUES (?, ?)');
            $q = $db_connection->prepare('SELECT size_id FROM tile_sizes WHERE size_name = ? LIMIT 1');
            $q->execute([$data['tile_size']]);
            $row = $q->fetch(PDO::FETCH_ASSOC);
            if ($row) $stmtSize->execute([$productId, $row['size_id']]);
        }
        // Best For (multi)
        if (!empty($data['best_for']) && is_array($data['best_for'])) {
            $stmtBestFor = $db_connection->prepare('INSERT INTO product_best_for (product_id, best_for_id) VALUES (?, ?)');
            foreach ($data['best_for'] as $bestForName) {
                $q = $db_connection->prepare('SELECT best_for_id FROM best_for_categories WHERE best_for_name = ? LIMIT 1');
                $q->execute([$bestForName]);
                $row = $q->fetch(PDO::FETCH_ASSOC);
                if ($row) $stmtBestFor->execute([$productId, $row['best_for_id']]);
            }
        }
    }
    return $productId;
}

// If this file is accessed via POST, handle the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    try {
        $data = $_POST;
        // If category_ids is not an array (e.g. only one selected), make it an array
        if (isset($data['category_ids']) && !is_array($data['category_ids'])) {
            $data['category_ids'] = [$data['category_ids']];
        }
        $file = isset($_FILES['product_image']) ? $_FILES['product_image'] : null;
        $productId = storeProduct($data, $file);
        echo json_encode(['status' => 'success', 'product_id' => $productId]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit();
}
