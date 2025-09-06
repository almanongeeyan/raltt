<?php
// generate_description.php
// Usage: POST with JSON: {"name":..., "price":..., "design":..., "stock":..., "categories": [...] }
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Validate required fields
if (empty($data['name']) || empty($data['price']) || !isset($data['designs']) || !is_array($data['designs']) || count($data['designs']) === 0 || empty($data['classification']) || empty($data['finish']) || empty($data['size'])) {
    echo json_encode(['error' => 'Missing or invalid input fields', 'details' => $data]);
    exit;
}

$name = escapeshellarg($data['name']);
$price = escapeshellarg($data['price']);
$designs = $data['designs'];
$designs_json = json_encode(array_values($designs), JSON_UNESCAPED_UNICODE);
$designs_b64 = base64_encode($designs_json);
$classification = escapeshellarg($data['classification']);
$finish = escapeshellarg($data['finish']);
$size = escapeshellarg($data['size']);

$python = 'C:\\Users\\pc\\AppData\\Local\\Programs\\Python\\Python313\\python.exe'; // Full path to python.exe
$script = __DIR__ . '/generate_tile_description.py';

$cmd = "$python $script --name $name --price $price --designs $designs_b64 --classification $classification --finish $finish --size $size";
// For debugging: log the command
file_put_contents(__DIR__ . '/generate_desc_debug.log', date('Y-m-d H:i:s') . "\nCMD: $cmd\nINPUT: " . json_encode($data) . "\n\n", FILE_APPEND);

// Capture both stdout and stderr
$output = null;
$return_var = 0;
exec($cmd . ' 2>&1', $output, $return_var);
$output_str = is_array($output) ? implode("\n", $output) : $output;
if ($return_var !== 0) {
    echo json_encode(['error' => 'Python script error', 'details' => $output_str]);
    exit;
}
if (trim($output_str) === '') {
    echo json_encode(['error' => 'No output from Python script']);
    exit;
}
echo json_encode(['description' => trim($output_str)]);
