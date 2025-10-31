<?php
require_once '../config/payment_config.php';

function processGCashPayment($amount, $order_reference) {
    $secret_key = PAYMONGO_SECRET_KEY;
    
    // In test mode, use lower amounts for testing
    if (PAYMENT_TEST_MODE && $amount > 10000) {
        $amount = 100; // Force small amount for testing
    }
    
    $data = [
        'data' => [
            'attributes' => [
                'amount' => intval($amount * 100), // Convert to centavos
                'description' => 'Order #' . $order_reference,
                'statement_descriptor' => 'RALTT TEST STORE',
                'currency' => 'PHP',
                'source' => [
                    'type' => 'gcash'
                ]
            ]
        ]
    ];
    
    $ch = curl_init('https://api.paymongo.com/v1/links');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($secret_key . ':')
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200) {
        $result = json_decode($response, true);
        
        return [
            'success' => true,
            'reference' => $result['data']['id'],
            'checkout_url' => $result['data']['attributes']['checkout_url'],
            'qr_code' => $result['data']['attributes']['qr_code']['image_url'],
            'expiry' => date('Y-m-d H:i:s', strtotime('+15 minutes'))
        ];
        
    } else {
        // For testing, simulate success even if API fails
        if (PAYMENT_TEST_MODE) {
            return [
                'success' => true,
                'reference' => 'test_gcash_ref_' . uniqid(),
                'checkout_url' => 'https://paymongo.com/test/gcash',
                'qr_code' => 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=TEST_GCASH_PAYMENT_' . $order_reference,
                'expiry' => date('Y-m-d H:i:s', strtotime('+15 minutes'))
            ];
        }
        
        error_log("PayMongo API Error: " . $response);
        return [
            'success' => false,
            'message' => 'Payment gateway error: HTTP ' . $http_code
        ];
    }
}
?>