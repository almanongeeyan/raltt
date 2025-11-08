<?php
// paymongo_gcash.php
require_once '../../connection/connection.php';
session_start();

$response = ['success' => false, 'message' => 'Unknown error'];

// PayMongo test keys
$paymongo_pk = 'pk_test_Ss4YHM1o9smoyJqfcdrJcray';
$paymongo_sk = 'sk_test_Aiy79mHPkayJLgS3aLF5RJ2x';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $selected_cart_items = isset($_POST['selected_cart_items']) ? json_decode($_POST['selected_cart_items'], true) : [];
    $applied_coins = isset($_POST['applied_coins']) ? intval($_POST['applied_coins']) : 0;
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $user_fullname = isset($_POST['user_fullname']) ? $_POST['user_fullname'] : '';
    $user_email = isset($_POST['user_email']) ? $_POST['user_email'] : '';
    $user_phone = isset($_POST['user_phone']) ? $_POST['user_phone'] : '';

    if ($user_id > 0 && !empty($selected_cart_items) && $amount > 0) {
        try {
            // Create GCash source via PayMongo API
            $ch = curl_init('https://api.paymongo.com/v1/sources');
            $payload = [
                'data' => [
                    'attributes' => [
                        'amount' => intval($amount * 100), // PayMongo expects centavos
                        'redirect' => [
                            'success' => 'https://www.paymongo.com/success',
                            'failed' => 'https://www.paymongo.com/failed'
                        ],
                        'type' => 'gcash',
                        'currency' => 'PHP',
                    ]
                ]
            ];
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Basic ' . base64_encode($paymongo_sk . ':'),
                'Content-Type: application/json'
            ]);
            $result = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $data = json_decode($result, true);
            if (($httpcode === 201 || $httpcode === 200) && isset($data['data']['attributes']['redirect']['checkout_url'])) {
                $checkout_url = $data['data']['attributes']['redirect']['checkout_url'];
                // Save order as pending, payment_method = gcash
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
                $max_coins_applicable = ($subtotal > 600) ? min(20, $applied_coins, $subtotal) : 0;
                $referral_discount = $max_coins_applicable;
                $total = max(0, $subtotal + $shipping - $referral_discount);
                $order_reference = 'RAL-' . strtoupper(bin2hex(random_bytes(4)));
                $branch_id = isset($_SESSION['branch_id']) ? intval($_SESSION['branch_id']) : 1;
                $stmt = $conn->prepare("INSERT INTO orders (order_reference, user_id, branch_id, total_amount, original_subtotal, coins_redeemed, payment_method, order_status, shipping_fee, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([
                    $order_reference,
                    $user_id,
                    $branch_id,
                    $total,
                    $subtotal,
                    $max_coins_applicable,
                    'gcash',
                    'paid',
                    $shipping
                ]);
                $order_id = $conn->lastInsertId();
                $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
                foreach ($cartItems as $item) {
                    $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['product_price']]);
                }
                if ($max_coins_applicable > 0) {
                    $stmt = $conn->prepare("UPDATE users SET referral_coins = GREATEST(referral_coins - ?, 0) WHERE id = ?");
                    $stmt->execute([$max_coins_applicable, $user_id]);
                }
                $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND cart_item_id IN ($in)");
                $stmt->execute(array_merge([$user_id], $selected_cart_items));
                $response = ['success' => true, 'checkout_url' => $checkout_url, 'order_id' => $order_id];
            } else {
                $response = ['success' => false, 'message' => 'Failed to create GCash source', 'details' => $data];
            }
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
