<?php
session_start();
require_once '../connection/connection.php';

header('Content-Type: application/json');

$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
$userRole = isset($_SESSION['user_role']) ? strtoupper($_SESSION['user_role']) : '';

$orders = [];
$error_message = '';

try {
    $query = "SELECT DISTINCT o.*, b.branch_name, u.full_name as customer_name, 
            u.phone_number as customer_phone, u.email as customer_email, u.full_address as customer_full_address
        FROM orders o 
        JOIN branches b ON o.branch_id = b.branch_id 
        JOIN users u ON o.user_id = u.id 
        WHERE 1=1";
    $params = [];
    if ($branch_id) {
        $query .= " AND o.branch_id = ?";
        $params[] = $branch_id;
    }
    $query .= " ORDER BY o.order_date DESC";
    $stmt = $db_connection->prepare($query);
    $stmt->execute($params);
    $rawOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $today = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d');
    foreach ($rawOrders as $order) {
        $orderDate = (new DateTime($order['order_date'], new DateTimeZone('Asia/Manila')))->format('Y-m-d');
        $orderStatus = $order['order_status'];
        $isPickUpOrder = strtolower($order['payment_method']) === 'pick_up';
        if ($userRole === 'DRIVER' && $isPickUpOrder) continue;
        if ($orderDate === $today) {
            $orders[] = $order;
            continue;
        }
        if ($orderDate < $today) {
            if (!in_array($orderStatus, ['completed', 'cancelled'])) {
                $orders[] = $order;
            }
            continue;
        }
        $orders[] = $order;
    }
    foreach ($orders as $index => $order) {
        $itemsQuery = "SELECT oi.*, p.product_name, p.product_image 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.product_id 
                       WHERE oi.order_id = ?";
        $itemsStmt = $db_connection->prepare($itemsQuery);
        $itemsStmt->execute([$order['order_id']]);
        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($items as $itemIndex => $item) {
            if (!empty($item['product_image'])) {
                $items[$itemIndex]['product_image'] = base64_encode($item['product_image']);
            }
        }
        $orders[$index]['items'] = $items;
    }
    echo json_encode(['success' => true, 'orders' => $orders]);
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Unable to load orders.']);
}
