<?php
// debug_paymongo.php
// Quick debug script for PayMongo GCash source creation
$paymongo_sk = 'sk_test_Aiy79mHPkayJLgS3aLF5RJ2x';
$amount = 10000; // 100 PHP
$ch = curl_init('https://api.paymongo.com/v1/sources');
$payload = [
    'data' => [
        'attributes' => [
            'amount' => $amount,
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
echo "HTTP code: $httpcode\n";
echo $result;
