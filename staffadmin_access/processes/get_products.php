<?php
// get_products.php
require_once __DIR__ . '/../../connection/connection.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
$show_archived = isset($_GET['show_archived']) && $_GET['show_archived'] == '1';

// Base query to get products
$sql = "SELECT p.*, pb.stock_count
        FROM products p
        INNER JOIN product_branches pb ON p.product_id = pb.product_id AND pb.branch_id = :branch_id";
        
if (!$show_archived) {
    $sql .= " WHERE p.is_archived = 0";
}

$stmt = $db_connection->prepare($sql);
$stmt->bindValue(':branch_id', $branch_id, PDO::PARAM_INT);
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all product IDs for fetching relationships
$product_ids = array_column($products, 'product_id');
$designMap = $classMap = $finishMap = $sizeMap = $bestForMap = [];

if (!empty($product_ids)) {
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    
    // Fetch tile designs
    $stmt = $db_connection->prepare("SELECT pd.product_id, td.design_name 
                                    FROM product_designs pd 
                                    JOIN tile_designs td ON pd.design_id = td.design_id 
                                    WHERE pd.product_id IN ($placeholders)");
    $stmt->execute($product_ids);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $designMap[$row['product_id']][] = $row['design_name'];
    }

    // Fetch tile classifications (table: product_classifications)
    $stmt = $db_connection->prepare("SELECT pc.product_id, tc.classification_name 
                                    FROM product_classifications pc 
                                    JOIN tile_classifications tc ON pc.classification_id = tc.classification_id 
                                    WHERE pc.product_id IN ($placeholders)");
    $stmt->execute($product_ids);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $classMap[$row['product_id']][] = $row['classification_name'];
    }

    // Fetch tile finishes (table: product_finishes)
    $stmt = $db_connection->prepare("SELECT pf.product_id, tf.finish_name 
                                    FROM product_finishes pf 
                                    JOIN tile_finishes tf ON pf.finish_id = tf.finish_id 
                                    WHERE pf.product_id IN ($placeholders)");
    $stmt->execute($product_ids);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $finishMap[$row['product_id']][] = $row['finish_name'];
    }

    // Fetch tile sizes (table: product_sizes)
    $stmt = $db_connection->prepare("SELECT ps.product_id, ts.size_name 
                                    FROM product_sizes ps 
                                    JOIN tile_sizes ts ON ps.size_id = ts.size_id 
                                    WHERE ps.product_id IN ($placeholders)");
    $stmt->execute($product_ids);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $sizeMap[$row['product_id']][] = $row['size_name'];
    }

    // Fetch best for categories (table: product_best_for)
    $stmt = $db_connection->prepare("SELECT pb.product_id, bf.best_for_name 
                                    FROM product_best_for pb 
                                    JOIN best_for_categories bf ON pb.best_for_id = bf.best_for_id 
                                    WHERE pb.product_id IN ($placeholders)");
    $stmt->execute($product_ids);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $bestForMap[$row['product_id']][] = $row['best_for_name'];
    }
}

// Process products for frontend display
foreach ($products as &$product) {
    // Convert product_image (BLOB) to base64
    if (!empty($product['product_image'])) {
        $product['product_image'] = 'data:image/jpeg;base64,' . base64_encode($product['product_image']);
    } else {
        $product['product_image'] = null;
    }

    // Ensure stock_count is set
    if (!isset($product['stock_count']) || $product['stock_count'] === null) {
        $product['stock_count'] = 0;
    }

    // Add relationship data only for tile products
    $product_id = $product['product_id'];
    if (isset($product['product_type']) && $product['product_type'] === 'tile') {
        $product['tile_designs'] = $designMap[$product_id] ?? [];
        $product['tile_classifications'] = $classMap[$product_id] ?? [];
        $product['tile_finishes'] = $finishMap[$product_id] ?? [];
        $product['tile_sizes'] = $sizeMap[$product_id] ?? [];
        $product['best_for'] = $bestForMap[$product_id] ?? [];
    } else {
        $product['tile_designs'] = null;
        $product['tile_classifications'] = null;
        $product['tile_finishes'] = null;
        $product['tile_sizes'] = null;
        $product['best_for'] = null;
    }
}
unset($product);

header('Content-Type: application/json');
echo json_encode($products);
?>