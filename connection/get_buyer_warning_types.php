
<?php
require_once '../connection/connection.php';
header('Content-Type: application/json');

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing user_id']);
    exit;
}

try {
    $stmt = $db_connection->prepare("SELECT warning_type, warning_notes, warning_date FROM buyer_warnings WHERE user_id = ? ORDER BY warning_date DESC");
    $stmt->execute([$user_id]);
    $violations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode([
        'status' => 'success',
        'violations' => $violations
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
