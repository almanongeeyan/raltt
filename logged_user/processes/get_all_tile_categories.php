<?php
// logged_user/processes/get_all_tile_categories.php
// Returns all tile categories as JSON

header('Content-Type: application/json');
require_once '../../connection/connection.php';

try {
    $stmt = $conn->prepare('SELECT category_id, category_name FROM tile_categories ORDER BY category_name ASC');
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($categories);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
}
