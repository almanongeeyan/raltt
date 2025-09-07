<?php
// connection/geocode_proxy.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (!isset($_GET['lat']) || !isset($_GET['lon'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing latitude or longitude.']);
    exit();
}

$lat = urlencode($_GET['lat']);
$lon = urlencode($_GET['lon']);

$url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lon}&zoom=18&addressdetails=1";

$opts = [
    'http' => [
        'header' => [
            'User-Agent: raltt-app/1.0 (contact@raltt.com)',
            'Accept-Language: en'
        ]
    ]
];
$context = stream_context_create($opts);

$result = @file_get_contents($url, false, $context);

if ($result === FALSE) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch address from geocoding service.']);
    exit();
}

// Pass through the JSON from Nominatim
header('Content-Type: application/json');
echo $result;
