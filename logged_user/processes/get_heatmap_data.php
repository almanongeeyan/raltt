<?php
// processes/get_heatmap_data.php
session_start();
require_once '../../connection/connection.php';

// Get product ID from request
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
    exit;
}

// Get updated heatmap data
$response = ['success' => true];

// Get total sold (completed orders)
$sold_stmt = $conn->prepare('SELECT SUM(oi.quantity) FROM order_items oi JOIN orders o ON oi.order_id = o.order_id WHERE oi.product_id = ? AND o.order_status = "completed"');
$sold_stmt->execute([$product_id]);
$total_sold = $sold_stmt->fetchColumn();
$response['total_sold'] = $total_sold ? $total_sold : 0;

// Get total completed orders
$completed_stmt = $conn->prepare('SELECT COUNT(DISTINCT o.order_id) FROM orders o JOIN order_items oi ON oi.order_id = o.order_id WHERE oi.product_id = ? AND o.order_status = "completed"');
$completed_stmt->execute([$product_id]);
$completed_orders = $completed_stmt->fetchColumn();
$response['completed_orders'] = $completed_orders ? $completed_orders : 0;

// Get total times added to cart
$cart_stmt = $conn->prepare('SELECT SUM(quantity) FROM cart_items WHERE product_id = ?');
$cart_stmt->execute([$product_id]);
$cart_adds = $cart_stmt->fetchColumn();
$response['cart_adds'] = $cart_adds ? $cart_adds : 0;

header('Content-Type: application/json');
echo json_encode($response);
?>