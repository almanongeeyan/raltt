<?php
// get_products.php
require_once '../../connection/connection.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
// Debug: show current branch_id
if (isset($_GET['debug'])) {
        header('Content-Type: application/json');
        echo json_encode(['branch_id' => $branch_id]);
        exit;
}


$show_archived = isset($_GET['show_archived']) && $_GET['show_archived'] == '1';
$sql = "SELECT p.*, pb.stock_count
                FROM products p
                JOIN product_branches pb ON p.product_id = pb.product_id
                WHERE pb.branch_id = :branch_id";
if (!$show_archived) {
        $sql .= " AND p.is_archived = 0";
}
$stmt = $db_connection->prepare($sql);
$stmt->bindValue(':branch_id', $branch_id, PDO::PARAM_INT);
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all categories for all products in one query
$ids = array_column($products, 'product_id');
$catMap = [];
if ($ids) {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $catStmt = $db_connection->prepare("SELECT pc.product_id, tc.category_name FROM product_categories pc JOIN tile_categories tc ON pc.category_id = tc.category_id WHERE pc.product_id IN ($in)");
        $catStmt->execute($ids);
        foreach ($catStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $catMap[$row['product_id']][] = $row['category_name'];
        }
}

// Convert product_image (BLOB) to base64 for frontend display and add categories
foreach ($products as &$product) {
        if (!empty($product['product_image'])) {
                $product['product_image'] = 'data:image/jpeg;base64,' . base64_encode($product['product_image']);
        } else {
                $product['product_image'] = null;
        }
        $product['tile_categories'] = $catMap[$product['product_id']] ?? [];
}
unset($product);

header('Content-Type: application/json');
echo json_encode($products);
