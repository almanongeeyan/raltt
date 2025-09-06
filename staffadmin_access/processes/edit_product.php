<?php
// edit_product.php
// This script updates product details in the database.

require_once '../../connection/connection.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        $data = $_POST;
        $product_id = isset($data['product_id']) ? (int)$data['product_id'] : 0;
        
        if ($product_id <= 0) {
            throw new Exception('Invalid product ID.');
        }

        // Fetch product type and details
        $stmt = $db_connection->prepare('SELECT product_type FROM products WHERE product_id = ? LIMIT 1');
        $stmt->execute([$product_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            throw new Exception('Product not found.');
        }
        
        $isTile = ($row['product_type'] === 'tile');

        // Update main product fields
        $sql = "UPDATE products SET 
                product_name = :product_name, 
                product_price = :product_price, 
                product_description = :product_description, 
                sku = :sku, 
                is_popular = :is_popular, 
                is_best_seller = :is_best_seller, 
                is_archived = :is_archived, 
                updated_at = NOW() 
                WHERE product_id = :product_id";
                
        $stmt = $db_connection->prepare($sql);
        $stmt->bindValue(':product_name', $data['product_name'] ?? '');
        $stmt->bindValue(':product_price', $data['product_price'] ?? 0);
        $stmt->bindValue(':product_description', $data['product_description'] ?? '');
        $stmt->bindValue(':sku', $data['sku'] ?? null);
        $stmt->bindValue(':is_popular', isset($data['is_popular']) ? (int)$data['is_popular'] : 0, PDO::PARAM_INT);
        $stmt->bindValue(':is_best_seller', isset($data['is_best_seller']) ? (int)$data['is_best_seller'] : 0, PDO::PARAM_INT);
        $stmt->bindValue(':is_archived', isset($data['is_archived']) ? (int)$data['is_archived'] : 0, PDO::PARAM_INT);
        $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();

        // Update stock in product_branches
        $branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
        if ($branch_id) {
            $stock_count = isset($data['stock_count']) ? (int)$data['stock_count'] : 0;
            
            // Check if record exists
            $checkStmt = $db_connection->prepare('SELECT COUNT(*) FROM product_branches WHERE product_id = ? AND branch_id = ?');
            $checkStmt->execute([$product_id, $branch_id]);
            $exists = $checkStmt->fetchColumn();
            
            if ($exists) {
                $stmt = $db_connection->prepare('UPDATE product_branches SET stock_count = :stock_count WHERE product_id = :product_id AND branch_id = :branch_id');
            } else {
                $stmt = $db_connection->prepare('INSERT INTO product_branches (product_id, branch_id, stock_count) VALUES (:product_id, :branch_id, :stock_count)');
            }
            
            $stmt->bindValue(':stock_count', $stock_count, PDO::PARAM_INT);
            $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->bindValue(':branch_id', $branch_id, PDO::PARAM_INT);
            $stmt->execute();
        }

        // Handle product type specific updates
        if ($isTile) {
            // Update tile-specific attributes
            updateTileAttributes($db_connection, $product_id, $data);
        } else {
            // Update other product attributes
            updateOtherProductAttributes($db_connection, $product_id, $data);
        }

        echo json_encode(['status' => 'success', 'message' => 'Product updated successfully.']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit();
}

/**
 * Update tile-specific attributes
 */
function updateTileAttributes($db_connection, $product_id, $data) {
    // Tile Designs (multi)
    $db_connection->prepare('DELETE FROM product_designs WHERE product_id = ?')->execute([$product_id]);
    if (!empty($data['tile_design']) && is_array($data['tile_design'])) {
        $stmtDesign = $db_connection->prepare('INSERT INTO product_designs (product_id, design_id) VALUES (?, ?)');
        foreach ($data['tile_design'] as $designName) {
            $q = $db_connection->prepare('SELECT design_id FROM tile_designs WHERE design_name = ? LIMIT 1');
            $q->execute([$designName]);
            $row = $q->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $stmtDesign->execute([$product_id, $row['design_id']]);
            }
        }
    }
    
    // Classification (single)
    $db_connection->prepare('DELETE FROM product_classifications WHERE product_id = ?')->execute([$product_id]);
    if (!empty($data['tile_classification'])) {
        $stmtClass = $db_connection->prepare('INSERT INTO product_classifications (product_id, classification_id) VALUES (?, ?)');
        $q = $db_connection->prepare('SELECT classification_id FROM tile_classifications WHERE classification_name = ? LIMIT 1');
        $q->execute([$data['tile_classification']]);
        $row = $q->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $stmtClass->execute([$product_id, $row['classification_id']]);
        }
    }
    
    // Finish (single)
    $db_connection->prepare('DELETE FROM product_finishes WHERE product_id = ?')->execute([$product_id]);
    if (!empty($data['tile_finish'])) {
        $stmtFinish = $db_connection->prepare('INSERT INTO product_finishes (product_id, finish_id) VALUES (?, ?)');
        $q = $db_connection->prepare('SELECT finish_id FROM tile_finishes WHERE finish_name = ? LIMIT 1');
        $q->execute([$data['tile_finish']]);
        $row = $q->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $stmtFinish->execute([$product_id, $row['finish_id']]);
        }
    }
    
    // Size (single)
    $db_connection->prepare('DELETE FROM product_sizes WHERE product_id = ?')->execute([$product_id]);
    if (!empty($data['tile_size'])) {
        $stmtSize = $db_connection->prepare('INSERT INTO product_sizes (product_id, size_id) VALUES (?, ?)');
        $q = $db_connection->prepare('SELECT size_id FROM tile_sizes WHERE size_name = ? LIMIT 1');
        $q->execute([$data['tile_size']]);
        $row = $q->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $stmtSize->execute([$product_id, $row['size_id']]);
        }
    }
    
    // Best For (multi)
    $db_connection->prepare('DELETE FROM product_best_for WHERE product_id = ?')->execute([$product_id]);
    if (!empty($data['best_for']) && is_array($data['best_for'])) {
        $stmtBestFor = $db_connection->prepare('INSERT INTO product_best_for (product_id, best_for_id) VALUES (?, ?)');
        foreach ($data['best_for'] as $bestForName) {
            $q = $db_connection->prepare('SELECT best_for_id FROM best_for_categories WHERE best_for_name = ? LIMIT 1');
            $q->execute([$bestForName]);
            $row = $q->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $stmtBestFor->execute([$product_id, $row['best_for_id']]);
            }
        }
    }
}

/**
 * Update other product attributes
 */
function updateOtherProductAttributes($db_connection, $product_id, $data) {
    // Update product specification if provided
    if (!empty($data['product_spec'])) {
        $stmt = $db_connection->prepare('UPDATE products SET product_spec = :product_spec WHERE product_id = :product_id');
        $stmt->bindValue(':product_spec', $data['product_spec']);
        $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    // Clear any existing tile-specific relationships for other products
    $db_connection->prepare('DELETE FROM product_designs WHERE product_id = ?')->execute([$product_id]);
    $db_connection->prepare('DELETE FROM product_classifications WHERE product_id = ?')->execute([$product_id]);
    $db_connection->prepare('DELETE FROM product_finishes WHERE product_id = ?')->execute([$product_id]);
    $db_connection->prepare('DELETE FROM product_sizes WHERE product_id = ?')->execute([$product_id]);
    $db_connection->prepare('DELETE FROM product_best_for WHERE product_id = ?')->execute([$product_id]);
}