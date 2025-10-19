<?php
// reverse_geocode.php
header('Content-Type: application/json');
if (!isset($_GET['lat']) || !isset($_GET['lng'])) {
    echo json_encode(['error' => 'Missing coordinates']);
    exit;
}
$lat = $_GET['lat'];
$lng = $_GET['lng'];
$url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&zoom=18&addressdetails=1";
$opts = [
    "http" => [
        "header" => "User-Agent: raltt-app/1.0\r\n"
    ]
];
$context = stream_context_create($opts);
$response = file_get_contents($url, false, $context);
if ($response === FALSE) {
    echo json_encode(['error' => 'Failed to fetch address']);
    exit;
}
echo $response;
