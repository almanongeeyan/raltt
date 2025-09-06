<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['branch_id'])) {
    echo json_encode([]);
    exit;
}

require_once '../../connection/connection.php';

$user_id = $_SESSION['user_id'];
$branch_id = (int)$_SESSION['branch_id'];

// Get top 3 recommended designs for user (ordered by rank)
$designStmt = $conn->prepare('SELECT design_id FROM user_design_preferences WHERE user_id = ? ORDER BY rank ASC');
$designStmt->execute([$user_id]);
$userDesigns = $designStmt->fetchAll(PDO::FETCH_COLUMN);
$recommendedProducts = [];
$debug = [
    'user_id' => $user_id,
    'branch_id' => $branch_id,
    'designs' => $userDesigns,
    'products_found' => 0,
    'products' => [],
];
if ($userDesigns) {
    $designWeights = [0=>4, 1=>2, 2=>1];
    $usedProductIds = [];
    foreach ($userDesigns as $i => $designId) {
        $limit = $designWeights[$i] ?? 1;
        $prodStmt = $conn->prepare('SELECT p.product_id, p.product_name, p.product_price, p.product_description, p.product_image, td.design_name FROM products p JOIN product_designs pd ON p.product_id = pd.product_id JOIN tile_designs td ON pd.design_id = td.design_id JOIN product_branches pb ON p.product_id = pb.product_id WHERE pd.design_id = ? AND pb.branch_id = ? AND p.is_archived = 0 LIMIT ?');
        $prodStmt->bindValue(1, $designId, PDO::PARAM_INT);
        $prodStmt->bindValue(2, $branch_id, PDO::PARAM_INT);
        $prodStmt->bindValue(3, $limit, PDO::PARAM_INT);
        $prodStmt->execute();
        foreach ($prodStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (in_array($row['product_id'], $usedProductIds)) continue;
            $usedProductIds[] = $row['product_id'];
            if (!empty($row['product_image'])) {
                $row['product_image'] = 'data:image/jpeg;base64,' . base64_encode($row['product_image']);
            } else {
                $row['product_image'] = null;
            }
            $recommendedProducts[] = $row;
            $debug['products'][] = $row['product_id'];
        }
    }
    $debug['products_found'] = count($recommendedProducts);
}
if (isset($_GET['debug'])) {
    echo json_encode(['debug'=>$debug,'products'=>$recommendedProducts]);
} else {
    echo json_encode($recommendedProducts);
}
