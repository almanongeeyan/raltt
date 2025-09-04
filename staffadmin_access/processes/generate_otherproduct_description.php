<?php
// generate_otherproduct_description.php
// Usage: POST with JSON: {"name":..., "price":..., "spec":... }
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Validate required fields

if (empty($data['name']) || empty($data['price']) || empty($data['spec'])) {
    echo json_encode([
        'error' => 'Missing or invalid input fields',
        'details' => $data,
        'debug' => [
            'received' => $data,
            'raw_input' => file_get_contents('php://input'),
            'expected_fields' => ['name', 'price', 'spec']
        ]
    ]);
    exit;
}

$name = escapeshellarg($data['name']);
$price = escapeshellarg($data['price']);
$spec = escapeshellarg($data['spec']);

$python = 'C:\\Users\\pc\\AppData\\Local\\Programs\\Python\\Python313\\python.exe'; // Full path to python.exe
$script = __DIR__ . '/generate_otherproduct_description.py';

$cmd = "$python $script --name $name --price $price --spec $spec";
// For debugging: log the command
file_put_contents(__DIR__ . '/generate_otherproduct_desc_debug.log', date('Y-m-d H:i:s') . "\nCMD: $cmd\nINPUT: " . json_encode($data) . "\n\n", FILE_APPEND);

// Capture both stdout and stderr

$output = null;
$return_var = 0;
exec($cmd . ' 2>&1', $output, $return_var);
$output_str = is_array($output) ? implode("\n", $output) : $output;

$debug_response = [
    'output' => $output_str,
    'return_var' => $return_var,
    'cmd' => $cmd,
    'input' => $data
];

if ($return_var !== 0) {
    echo json_encode(['error' => 'Python script error', 'details' => $output_str, 'debug' => $debug_response]);
    exit;
}
if (trim($output_str) === '') {
    echo json_encode(['error' => 'No output from Python script', 'debug' => $debug_response]);
    exit;
}
echo json_encode(['description' => trim($output_str), 'debug' => $debug_response]);
