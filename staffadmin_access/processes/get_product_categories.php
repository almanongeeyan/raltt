<?php
// get_product_categories.php
require_once '../../connection/connection.php';

$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
if (!$product_id) {
    echo json_encode([]);
    exit;
}
$stmt = $db_connection->prepare('SELECT category_id FROM product_categories WHERE product_id = ?');
$stmt->execute([$product_id]);
$rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo json_encode($rows);
