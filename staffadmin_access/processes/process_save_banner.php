<?php
// process_save_banner.php
// Handles saving a cropped banner image to the branch_banners table
session_start();
require_once '../../connection/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['branch_id'])) {
    echo json_encode(['success' => false, 'message' => 'Branch not set in session.']);
    exit;
}
$branch_id = $_SESSION['branch_id'];

if (!isset($_POST['image']) || !isset($_POST['order'])) {
    echo json_encode(['success' => false, 'message' => 'Missing image or order.']);
    exit;
}

$imageData = $_POST['image'];
$order = intval($_POST['order']);

try {
    // Remove any existing banner for this branch/order
    $del = $conn->prepare("DELETE FROM branch_banners WHERE branch_id = :branch_id AND display_order = :display_order");
    $del->execute([':branch_id' => $branch_id, ':display_order' => $order]);

    // If image is empty, treat as delete only
    if (empty($imageData)) {
        echo json_encode(['success' => true, 'message' => 'Banner deleted.']);
        exit;
    }

    // Extract base64 data
    if (preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $imageData, $type)) {
        $imageData = substr($imageData, strpos($imageData, ',') + 1);
        $imageData = base64_decode($imageData);
        if ($imageData === false) {
            echo json_encode(['success' => false, 'message' => 'Base64 decode failed.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid image data.']);
        exit;
    }

    // Insert new banner
    $ins = $conn->prepare("INSERT INTO branch_banners (branch_id, banner_image, display_order, is_active) VALUES (:branch_id, :banner_image, :display_order, 1)");
    $ins->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    $ins->bindParam(':banner_image', $imageData, PDO::PARAM_LOB);
    $ins->bindParam(':display_order', $order, PDO::PARAM_INT);
    $success = $ins->execute();

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Banner saved.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save banner.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
