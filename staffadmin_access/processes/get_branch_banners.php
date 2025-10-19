<?php
// get_branch_banners.php
// Returns the banners for the current branch, ordered by display_order
session_start();
require_once '../../connection/connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['branch_id'])) {
    echo json_encode(['success' => false, 'message' => 'Branch not set in session.']);
    exit;
}
$branch_id = $_SESSION['branch_id'];

try {
    $stmt = $conn->prepare("SELECT display_order, banner_image FROM branch_banners WHERE branch_id = :branch_id AND is_active = 1 ORDER BY display_order ASC");
    $stmt->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $stmt->execute();
    $banners = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $banners[$row['display_order']] = 'data:image/jpeg;base64,' . base64_encode($row['banner_image']);
    }
    echo json_encode(['success' => true, 'banners' => $banners]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
