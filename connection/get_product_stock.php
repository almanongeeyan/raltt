<?php
// connection/get_product_stock.php
require_once 'connection.php';
header('Content-Type: application/json');
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$branch_id = isset($_GET['branch_id']) ? intval($_GET['branch_id']) : 0;
if ($product_id <= 0 || $branch_id <= 0) {
    echo json_encode(['stock_count' => 0, 'error' => 'Invalid product or branch.']);
    exit;
}
$stmt = $conn->prepare('SELECT stock_count FROM product_branches WHERE product_id = ? AND branch_id = ? LIMIT 1');
$stmt->execute([$product_id, $branch_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row && isset($row['stock_count'])) {
    echo json_encode(['stock_count' => intval($row['stock_count'])]);
} else {
    echo json_encode(['stock_count' => 0]);
}
