<?php
// logged_user/processes/add_to_cart.php
// Adds a product to the cart for the current user and branch
session_start();
require_once '../../connection/connection.php'; // Ensure connection.php is included

// Get POST data
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$branch_id = isset($_POST['branch_id']) ? intval($_POST['branch_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

if ($user_id <= 0 || $product_id <= 0 || $branch_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Missing required data.']);
    exit;
}


// Check if product already exists in cart for this user
$checkSql = "SELECT cart_item_id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($checkSql);
$stmt->execute([$user_id, $product_id]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    // Update quantity
    $newQty = $existing['quantity'] + $quantity;
    $updateSql = "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $success = $updateStmt->execute([$newQty, $existing['cart_item_id']]);
    echo json_encode(['success' => $success, 'updated' => true, 'new_quantity' => $newQty]);
} else {
    // Insert new cart item
    $insertSql = "INSERT INTO cart_items (user_id, product_id, quantity, created_at) VALUES (?, ?, ?, NOW())";
    $insertStmt = $conn->prepare($insertSql);
    $success = $insertStmt->execute([$user_id, $product_id, $quantity]);
    echo json_encode(['success' => $success, 'updated' => false, 'new_quantity' => $quantity]);
}
