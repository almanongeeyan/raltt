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

// Map JS keys to DB category_id
$keyToId = [
    'black_white' => 4,
    'floral' => 2,
    'indoor' => 3,
    'minimalist' => 1,
    'modern' => 5,
    'pool' => 6
];

try {
    $pdo = $conn; // from connection.php
    $pdo->beginTransaction();
    // Remove previous recommendations for this user
    $stmt = $pdo->prepare('DELETE FROM user_recommendations WHERE user_id = ?');
    $stmt->execute([$user_id]);
    // Insert new recommendations
    $stmt = $pdo->prepare('INSERT INTO user_recommendations (user_id, category_id, rank) VALUES (?, ?, ?)');
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
