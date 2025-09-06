<?php
// save_recommendations.php
// Receives: POST 'categories' (array of category keys in order)
// Requires: user session (user_id)

require_once '../../connection/connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$categories = isset($_POST['categories']) ? json_decode($_POST['categories'], true) : [];

if (!is_array($categories) || count($categories) !== 3) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid categories']);
    exit;
}


// Map JS keys to DB design_id (from tile_designs)
$keyToId = [
    'minimalist' => 1,
    'floral' => 2,
    'black_white' => 3,
    'modern' => 4,
    'rustic' => 5,
    'geometric' => 6
];

try {
    $pdo = $conn; // from connection.php
    $pdo->beginTransaction();
    // Remove previous design preferences for this user
    $stmt = $pdo->prepare('DELETE FROM user_design_preferences WHERE user_id = ?');
    $stmt->execute([$user_id]);
    // Insert new design preferences
    $stmt = $pdo->prepare('INSERT INTO user_design_preferences (user_id, design_id, rank) VALUES (?, ?, ?)');
    foreach ($categories as $i => $key) {
        if (!isset($keyToId[$key])) continue;
        $stmt->execute([$user_id, $keyToId[$key], $i+1]);
    }
    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'DB error', 'details' => $e->getMessage()]);
}
