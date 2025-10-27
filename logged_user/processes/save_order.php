<?php
// save_order.php
require_once '../../connection/connection.php';
session_start();

$response = ['success' => false, 'message' => 'Unknown error'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $selected_cart_items = isset($_POST['selected_cart_items']) ? json_decode($_POST['selected_cart_items'], true) : [];
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
    $applied_coins = isset($_POST['applied_coins']) ? intval($_POST['applied_coins']) : 0;
    
    if ($user_id > 0 && !empty($selected_cart_items) && in_array($payment_method, ['cod', 'self-pickup'])) {
        try {
            // Calculate total and get cart items
            $in = str_repeat('?,', count($selected_cart_items) - 1) . '?';
            $stmt = $conn->prepare(
                "SELECT ci.cart_item_id, ci.product_id, ci.quantity, p.product_price FROM cart_items ci JOIN products p ON ci.product_id = p.product_id WHERE ci.user_id = ? AND ci.cart_item_id IN ($in)"
            );
            $stmt->execute(array_merge([$user_id], $selected_cart_items));
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item['product_price'] * $item['quantity'];
            }
            $shipping = ($subtotal >= 1000) ? 0 : ($subtotal > 0 ? 40 : 0);
            // Calculate how many coins will actually be used (cannot exceed 20, user's coins, or subtotal)
            $max_coins_applicable = ($subtotal > 600) ? min(20, $applied_coins, $subtotal) : 0;
            $referral_discount = $max_coins_applicable;
            $total = max(0, $subtotal + $shipping - $referral_discount);

            // Generate order reference
            $order_reference = 'RAL-' . strtoupper(bin2hex(random_bytes(4)));
            // Get branch_id from session (set by headeruser.php)
            $branch_id = isset($_SESSION['branch_id']) ? intval($_SESSION['branch_id']) : 1;
            $stmt = $conn->prepare("INSERT INTO orders (order_reference, user_id, branch_id, total_amount, original_subtotal, coins_redeemed, payment_method, order_status, shipping_fee, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $order_reference,
                $user_id,
                $branch_id,
                $total,
                $subtotal,
                $max_coins_applicable,
                ($payment_method === 'self-pickup' ? 'pick_up' : $payment_method),
                'pending',
                $shipping
            ]);
            $order_id = $conn->lastInsertId();

            // Insert order items (unit_price column)
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            foreach ($cartItems as $item) {
                $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['product_price']]);
            }

            // Deduct only the coins actually used from the user's referral_coins
            if ($max_coins_applicable > 0) {
                $stmt = $conn->prepare("UPDATE users SET referral_coins = GREATEST(referral_coins - ?, 0) WHERE id = ?");
                $stmt->execute([$max_coins_applicable, $user_id]);
            }

            // Remove items from cart
            $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND cart_item_id IN ($in)");
            $stmt->execute(array_merge([$user_id], $selected_cart_items));

            $response = ['success' => true, 'order_id' => $order_id];
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
        }
    } else {
        $response = ['success' => false, 'message' => 'Invalid request.'];
    }
} else {
    $response = ['success' => false, 'message' => 'Invalid method.'];
}

header('Content-Type: application/json');
echo json_encode($response);
