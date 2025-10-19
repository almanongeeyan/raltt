<?php
require_once '../../connection/connection.php';

function getCartItems($conn, $user_id) {
    $stmt = $conn->prepare('SELECT ci.cart_item_id, ci.product_id, ci.quantity, p.product_name, p.product_price, p.product_image, p.product_description FROM cart_items ci JOIN products p ON ci.product_id = p.product_id WHERE ci.user_id = ?');
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCartSummary($cartItems) {
    $selectedCount = count($cartItems);
    $subtotal = 0;
    foreach ($cartItems as $item) {
        $subtotal += $item['product_price'] * $item['quantity'];
    }
    $shipping = $subtotal > 0 ? 40 : 0;
    $tax = round($subtotal * 0.06); // 6% tax
    $total = $subtotal + $shipping + $tax;
    return [
        'selectedCount' => $selectedCount,
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'tax' => $tax,
        'total' => $total
    ];
}
