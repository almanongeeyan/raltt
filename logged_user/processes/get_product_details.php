<?php
// logged_user/processes/get_product_details.php
// Returns product details (description, SKU, image) as JSON by SKU or ID

header('Content-Type: application/json');
require_once '../../connection/connection.php';

$sku = isset($_GET['sku']) ? $_GET['sku'] : null;
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$sku && !$id) {
    echo json_encode(['error' => 'Missing SKU or ID']);
    exit;
}

$query = "SELECT id, sku, name, description, image_path FROM products WHERE ";
if ($sku) {
    $query .= "sku = :sku";
    $params = [':sku' => $sku];
} else {
    $query .= "id = :id";
    $params = [':id' => $id];
}

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        echo json_encode([
            'id' => $row['id'],
            'sku' => $row['sku'],
            'name' => $row['name'],
            'description' => $row['description'],
            'image_path' => $row['image_path']
        ]);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
}
