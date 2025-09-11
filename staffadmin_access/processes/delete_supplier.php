<?php
// This file will handle deleting a supplier by ID
include '../../connection/connection.php';

if (isset($_GET['id'])) {
    $supplier_id = intval($_GET['id']);
    // Optionally, delete logo file from server here
    $stmt = $db_connection->prepare('DELETE FROM suppliers WHERE supplier_id = ?');
    $stmt->execute([$supplier_id]);
}
header('Location: ../admin_suppliers.php');
exit();
