<?php
include '../../connection/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = trim($_POST['company_name'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $logo_blob = null;

    // Handle logo upload as BLOB
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $logo_blob = file_get_contents($_FILES['logo']['tmp_name']);
    }

    if ($company_name && $contact_number && $logo_blob) {
        $stmt = $db_connection->prepare('INSERT INTO suppliers (supplier_name, supplier_logo, contact_number) VALUES (?, ?, ?)');
        $stmt->bindParam(1, $company_name);
        $stmt->bindParam(2, $logo_blob, PDO::PARAM_LOB);
        $stmt->bindParam(3, $contact_number);
        $stmt->execute();
        header('Location: ../admin_suppliers.php?success=1');
        exit();
    } else {
        header('Location: ../admin_suppliers.php?error=1');
        exit();
    }
}
