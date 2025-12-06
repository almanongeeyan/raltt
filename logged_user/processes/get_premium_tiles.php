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
    // Get all products for the branch
    $stmt = $conn->prepare('
        SELECT p.product_id, p.product_name, p.product_price, p.product_description, 
               p.product_image, p.is_best_seller, p.is_archived, p.product_type
        FROM products p
        JOIN product_branches pb ON p.product_id = pb.product_id
        WHERE pb.branch_id = ? AND p.is_archived = 0 AND (p.product_type = "tile" OR p.product_type = "other")
        GROUP BY p.product_id
        ORDER BY p.product_name ASC
    ');
    $stmt->execute([$branch_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Find the product_id with the highest total quantity in cart_items for this branch
    $cartStmt = $conn->prepare('
        SELECT ci.product_id, SUM(ci.quantity) as total_qty
        FROM cart_items ci
        JOIN product_branches pb ON ci.product_id = pb.product_id
        WHERE pb.branch_id = ?
        GROUP BY ci.product_id
        ORDER BY total_qty DESC
        LIMIT 1
    ');
    $cartStmt->execute([$branch_id]);
    $mostAdded = $cartStmt->fetch(PDO::FETCH_ASSOC);
    $mostAddedId = $mostAdded ? $mostAdded['product_id'] : null;

    foreach ($products as &$row) {
        $product_id = $row['product_id'];
        // Set is_popular: 1 if this is the most added to cart, else 0
        $row['is_popular'] = ($mostAddedId && $product_id == $mostAddedId) ? 1 : 0;

        // Get stock count from product_branches
        $stockStmt = $conn->prepare('SELECT stock_count FROM product_branches WHERE product_id = ? AND branch_id = ? LIMIT 1');
        $stockStmt->execute([$product_id, $branch_id]);
        $stockRow = $stockStmt->fetch(PDO::FETCH_ASSOC);
        $row['stock_count'] = $stockRow ? (int)$stockRow['stock_count'] : 0;

        // Get designs
        $dstmt = $conn->prepare('SELECT td.design_name FROM product_designs pd JOIN tile_designs td ON pd.design_id = td.design_id WHERE pd.product_id = ?');
        $dstmt->execute([$product_id]);
        $designs = [];
        while ($d = $dstmt->fetch(PDO::FETCH_ASSOC)) $designs[] = $d['design_name'];
        $row['designs'] = $designs;
        // Get best_for_ids
        $bfstmt = $conn->prepare('SELECT best_for_id FROM product_best_for WHERE product_id = ?');
        $bfstmt->execute([$product_id]);
        $best_for_ids = [];
        while ($bf = $bfstmt->fetch(PDO::FETCH_ASSOC)) $best_for_ids[] = $bf['best_for_id'];
        $row['best_for_ids'] = $best_for_ids;
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
