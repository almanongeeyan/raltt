<?php
// logged_user/processes/get_premium_tiles.php
// Returns all products for the selected branch as JSON


session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['branch_id'])) {
    echo json_encode([]);
    exit;
}
require_once '../../connection/connection.php';
$branch_id = (int)$_SESSION['branch_id'];
try {
    $stmt = $conn->prepare('
        SELECT p.product_id, p.product_name, p.product_price, p.product_description, 
               p.product_image, p.is_popular, p.is_best_seller, p.is_archived
        FROM products p
        JOIN product_branches pb ON p.product_id = pb.product_id
        WHERE pb.branch_id = ? AND p.is_archived = 0 AND p.product_type = "tile"
        GROUP BY p.product_id
        ORDER BY p.product_name ASC
    ');
    $stmt->execute([$branch_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($products as &$row) {
        $product_id = $row['product_id'];
        // Get designs
        $dstmt = $conn->prepare('SELECT td.design_name FROM product_designs pd JOIN tile_designs td ON pd.design_id = td.design_id WHERE pd.product_id = ?');
        $dstmt->execute([$product_id]);
        $designs = [];
        while ($d = $dstmt->fetch(PDO::FETCH_ASSOC)) $designs[] = $d['design_name'];
        $row['designs'] = $designs;
        // Convert image blob to base64
        if (!empty($row['product_image'])) {
            $row['product_image'] = 'data:image/jpeg;base64,' . base64_encode($row['product_image']);
        } else {
            $row['product_image'] = null;
        }
    }
    echo json_encode($products);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
}
