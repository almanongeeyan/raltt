<?php
// This file will handle editing a supplier
include '../../connection/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = intval($_POST['supplier_id'] ?? 0);
    $company_name = trim($_POST['company_name'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $logo_blob = null;

    // Handle logo upload as BLOB if new file is provided
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $logo_blob = file_get_contents($_FILES['logo']['tmp_name']);
    }

    if ($supplier_id && $company_name && $contact_number) {
        if ($logo_blob !== null) {
            $stmt = $db_connection->prepare('UPDATE suppliers SET supplier_name = ?, supplier_logo = ?, contact_number = ? WHERE supplier_id = ?');
            $stmt->bindParam(1, $company_name);
            $stmt->bindParam(2, $logo_blob, PDO::PARAM_LOB);
            $stmt->bindParam(3, $contact_number);
            $stmt->bindParam(4, $supplier_id);
            $stmt->execute();
        } else {
            $stmt = $db_connection->prepare('UPDATE suppliers SET supplier_name = ?, contact_number = ? WHERE supplier_id = ?');
            $stmt->execute([$company_name, $contact_number, $supplier_id]);
        }
        header('Location: ../admin_suppliers.php?updated=1');
        exit();
    } else {
        header('Location: ../admin_suppliers.php?error=1');
        exit();
    }
}
