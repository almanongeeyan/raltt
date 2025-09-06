<?php
// logged_user/processes/get_all_tile_categories.php
// Returns all tile categories as JSON


header('Content-Type: application/json');
require_once '../../connection/connection.php';
try {
    $stmt = $conn->prepare('SELECT design_id, design_name FROM tile_designs ORDER BY design_name ASC');
    $stmt->execute();
    $designs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($designs);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
}
