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


$query = "SELECT product_id, sku, product_name, product_description, product_image FROM products WHERE ";
if ($sku) {
    $query .= "sku = :sku";
    $params = [':sku' => $sku];
} else {
    $query .= "product_id = :id";
    $params = [':id' => $id];
}

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $product_id = $row['product_id'];
        // Get designs
        $designs = [];
        $dstmt = $conn->prepare('SELECT td.design_name FROM product_designs pd JOIN tile_designs td ON pd.design_id = td.design_id WHERE pd.product_id = ?');
        $dstmt->execute([$product_id]);
        while ($d = $dstmt->fetch(PDO::FETCH_ASSOC)) $designs[] = $d['design_name'];
        // Get sizes
        $sizes = [];
        $sstmt = $conn->prepare('SELECT ts.size_name FROM product_sizes ps JOIN tile_sizes ts ON ps.size_id = ts.size_id WHERE ps.product_id = ?');
        $sstmt->execute([$product_id]);
        while ($s = $sstmt->fetch(PDO::FETCH_ASSOC)) $sizes[] = $s['size_name'];
        // Get finishes
        $finishes = [];
        $fstmt = $conn->prepare('SELECT tf.finish_name FROM product_finishes pf JOIN tile_finishes tf ON pf.finish_id = tf.finish_id WHERE pf.product_id = ?');
        $fstmt->execute([$product_id]);
        while ($f = $fstmt->fetch(PDO::FETCH_ASSOC)) $finishes[] = $f['finish_name'];
        // Get classifications
        $classifications = [];
        $cstmt = $conn->prepare('SELECT tc.classification_name FROM product_classifications pc JOIN tile_classifications tc ON pc.classification_id = tc.classification_id WHERE pc.product_id = ?');
        $cstmt->execute([$product_id]);
        while ($c = $cstmt->fetch(PDO::FETCH_ASSOC)) $classifications[] = $c['classification_name'];
        // Get best for
        $best_for = [];
        $bstmt = $conn->prepare('SELECT bfc.best_for_name FROM product_best_for pbf JOIN best_for_categories bfc ON pbf.best_for_id = bfc.best_for_id WHERE pbf.product_id = ?');
        $bstmt->execute([$product_id]);
        while ($b = $bstmt->fetch(PDO::FETCH_ASSOC)) $best_for[] = $b['best_for_name'];
        // Convert image blob to base64
        $image = null;
        if (!empty($row['product_image'])) {
            $image = 'data:image/jpeg;base64,' . base64_encode($row['product_image']);
        }
        echo json_encode([
            'id' => $row['product_id'],
            'sku' => $row['sku'],
            'name' => $row['product_name'],
            'description' => $row['product_description'],
            'image_path' => $image,
            'designs' => $designs,
            'sizes' => $sizes,
            'finishes' => $finishes,
            'classifications' => $classifications,
            'best_for' => $best_for
        ]);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
}
