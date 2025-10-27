<?php
// logged_user/processes/get_best_for_categories.php
// Returns all best_for_categories as JSON
header('Content-Type: application/json');
require_once '../../connection/connection.php';
try {
    $stmt = $conn->prepare('SELECT best_for_id, best_for_name FROM best_for_categories ORDER BY best_for_name ASC');
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($categories);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
}
