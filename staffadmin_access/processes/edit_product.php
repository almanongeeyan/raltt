<?php
// edit_product.php
require_once '../../connection/connection.php';
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
    // Update products table
    $sql = "UPDATE products SET product_name = :product_name, product_description = :product_description, product_price = :product_price WHERE product_id = :product_id";
    $stmt = $db_connection->prepare($sql);
    $stmt->bindValue(':product_name', $data['product_name']);
    $stmt->bindValue(':product_description', $data['product_description']);
    $stmt->bindValue(':product_price', $data['product_price']);
    $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();

    // Update stock in product_branches for this user's branch
    $branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
    if ($branch_id && isset($data['stock_count'])) {
        $sql2 = "UPDATE product_branches SET stock_count = :stock_count WHERE product_id = :product_id AND branch_id = :branch_id";
        $stmt2 = $db_connection->prepare($sql2);
        $stmt2->bindValue(':stock_count', $data['stock_count'], PDO::PARAM_INT);
        $stmt2->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $stmt2->bindValue(':branch_id', $branch_id, PDO::PARAM_INT);
        $stmt2->execute();
    }

    // Update product_categories for tile products
    if (isset($data['category_ids']) && is_array($data['category_ids'])) {
        // Remove old
        $del = $db_connection->prepare('DELETE FROM product_categories WHERE product_id = ?');
        $del->execute([$product_id]);
        // Insert new
        $ins = $db_connection->prepare('INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)');
        foreach ($data['category_ids'] as $catId) {
            $ins->execute([$product_id, $catId]);
        }
    }

    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
