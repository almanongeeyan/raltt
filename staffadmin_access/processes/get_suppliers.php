<?php
include '../../connection/connection.php';
header('Content-Type: application/json');

try {
    $suppliers = $db_connection->query('SELECT * FROM suppliers ORDER BY supplier_id DESC')->fetchAll(PDO::FETCH_ASSOC);
    // Convert logo blob to base64 data URL
    foreach ($suppliers as &$supplier) {
        if (!empty($supplier['supplier_logo'])) {
            $imgData = $supplier['supplier_logo'];
            $base64 = base64_encode($imgData);
            // Try to detect mime type (default to png)
            $mime = 'image/png';
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $detected = finfo_buffer($finfo, $imgData);
                if ($detected) $mime = $detected;
                finfo_close($finfo);
            }
            $supplier['supplier_logo'] = 'data:' . $mime . ';base64,' . $base64;
        } else {
            $supplier['supplier_logo'] = null;
        }
    }
    echo json_encode(['status' => 'success', 'data' => $suppliers]);
} catch(Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
