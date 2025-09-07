<?php
// get_edit_product_info.php
require_once '../../connection/connection.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

try {
    $product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
    if ($product_id <= 0) throw new Exception('Invalid product ID.');

    // Fetch product main info
    $stmt = $db_connection->prepare('SELECT * FROM products WHERE product_id = ? LIMIT 1');
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) throw new Exception('Product not found.');

    // Fetch stock for current branch
    $branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
    $stock_count = 0;
    if ($branch_id) {
        $stmt = $db_connection->prepare('SELECT stock_count FROM product_branches WHERE product_id = ? AND branch_id = ? LIMIT 1');
        $stmt->execute([$product_id, $branch_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) $stock_count = (int)$row['stock_count'];
    }
    $product['stock_count'] = $stock_count;

    // Relationships (for tile products)
    $product['tile_designs'] = [];
    $product['tile_classification'] = null;
    $product['tile_finish'] = null;
    $product['tile_size'] = null;
    $product['best_for'] = [];

    if ($product['product_type'] === 'tile') {
        // Designs (multi)
        $stmt = $db_connection->prepare('SELECT td.design_name FROM product_designs pd JOIN tile_designs td ON pd.design_id = td.design_id WHERE pd.product_id = ?');
        $stmt->execute([$product_id]);
        $product['tile_designs'] = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'design_name');
        // Classification (single)
        $stmt = $db_connection->prepare('SELECT tc.classification_name FROM product_classifications pc JOIN tile_classifications tc ON pc.classification_id = tc.classification_id WHERE pc.product_id = ? LIMIT 1');
        $stmt->execute([$product_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) $product['tile_classification'] = $row['classification_name'];
        // Finish (single)
        $stmt = $db_connection->prepare('SELECT tf.finish_name FROM product_finishes pf JOIN tile_finishes tf ON pf.finish_id = tf.finish_id WHERE pf.product_id = ? LIMIT 1');
        $stmt->execute([$product_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) $product['tile_finish'] = $row['finish_name'];
        // Size (single)
        $stmt = $db_connection->prepare('SELECT ts.size_name FROM product_sizes ps JOIN tile_sizes ts ON ps.size_id = ts.size_id WHERE ps.product_id = ? LIMIT 1');
        $stmt->execute([$product_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) $product['tile_size'] = $row['size_name'];
        // Best For (multi)
        $stmt = $db_connection->prepare('SELECT bf.best_for_name FROM product_best_for pb JOIN best_for_categories bf ON pb.best_for_id = bf.best_for_id WHERE pb.product_id = ?');
        $stmt->execute([$product_id]);
        $product['best_for'] = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'best_for_name');
    }

    // Dropdown options
    $dropdowns = [];
    $dropdowns['tile_designs'] = array_column($db_connection->query('SELECT design_name FROM tile_designs ORDER BY design_name')->fetchAll(PDO::FETCH_ASSOC), 'design_name');
    $dropdowns['tile_classifications'] = array_column($db_connection->query('SELECT classification_name FROM tile_classifications ORDER BY classification_name')->fetchAll(PDO::FETCH_ASSOC), 'classification_name');
    $dropdowns['tile_finishes'] = array_column($db_connection->query('SELECT finish_name FROM tile_finishes ORDER BY finish_name')->fetchAll(PDO::FETCH_ASSOC), 'finish_name');
    $dropdowns['tile_sizes'] = array_column($db_connection->query('SELECT size_name FROM tile_sizes ORDER BY size_name')->fetchAll(PDO::FETCH_ASSOC), 'size_name');
    $dropdowns['best_for'] = array_column($db_connection->query('SELECT best_for_name FROM best_for_categories ORDER BY best_for_name')->fetchAll(PDO::FETCH_ASSOC), 'best_for_name');

    echo json_encode([
        'product' => $product,
        'dropdowns' => $dropdowns
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
