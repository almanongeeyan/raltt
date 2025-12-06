<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/connection.php';
$conn = $db_connection;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];


// Accept both camelCase and snake_case keys for compatibility
$fields = [
    'full_name' => isset($_POST['full_name']) ? trim($_POST['full_name']) : (isset($_POST['fullName']) ? trim($_POST['fullName']) : null),
    'phone_number' => isset($_POST['phone_number']) ? trim($_POST['phone_number']) : (isset($_POST['contactNumber']) ? trim($_POST['contactNumber']) : null),
    'house_address' => isset($_POST['house_address']) ? trim($_POST['house_address']) : (isset($_POST['houseAddress']) ? trim($_POST['houseAddress']) : null),
    'full_address' => isset($_POST['full_address']) ? trim($_POST['full_address']) : (isset($_POST['pinLocation']) ? trim($_POST['pinLocation']) : null),
    // Detailed address components (optional)
    'region' => isset($_POST['region']) ? trim($_POST['region']) : null,
    'province' => isset($_POST['province']) ? trim($_POST['province']) : null,
    'city' => isset($_POST['city']) ? trim($_POST['city']) : null,
    'barangay' => isset($_POST['barangay']) ? trim($_POST['barangay']) : null,
    'street' => isset($_POST['streetSelect']) ? trim($_POST['streetSelect']) : (isset($_POST['street']) ? trim($_POST['street']) : null),
    'zone' => isset($_POST['zoneSelect']) ? trim($_POST['zoneSelect']) : (isset($_POST['zone']) ? trim($_POST['zone']) : null),
    'purok' => isset($_POST['purokSelect']) ? trim($_POST['purokSelect']) : null,
    'detailed_address' => isset($_POST['detailedAddress']) ? trim($_POST['detailedAddress']) : null,
];

// Remove fields that are null or empty (not sent or blank)
$updateFields = array_filter($fields, function($v) { return $v !== null && $v !== ''; });

if (empty($updateFields)) {
    // Log that no changes were sent (helpful for debugging client behavior)
    @file_put_contents(__DIR__ . '/save_shipping_info.log', date('[Y-m-d H:i:s]') . " No changes to update. POST=" . json_encode($_POST) . PHP_EOL, FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => 'No changes to update.']);
    exit;
}

try {
    // Ensure any new address columns exist in the users table. If a provided detailed field
    // maps to a column that doesn't exist, add the column so we can persist structured data.
    $possibleColumns = ['region','province','city','barangay','street','zone','purok','detailed_address'];
    foreach ($possibleColumns as $col) {
        if (isset($updateFields[$col])) {
            // Check if column exists using a direct query (avoid parameterization issues with SHOW ... LIKE)
            $colSafe = str_replace("'", "''", $col);
            $colCheckStmt = $conn->query("SHOW COLUMNS FROM `users` LIKE '" . $colSafe . "'");
            $colCheck = $colCheckStmt ? $colCheckStmt->fetchAll(PDO::FETCH_ASSOC) : [];
            if (count($colCheck) === 0) {
                // Add the column with reasonable varchar length
                $sqlAlter = "ALTER TABLE `users` ADD COLUMN `" . $col . "` varchar(255) DEFAULT NULL";
                try {
                    $conn->exec($sqlAlter);
                } catch (PDOException $e) {
                    // If altering fails, log the error but continue — we'll still attempt to save other fields
                    @file_put_contents(__DIR__ . '/save_shipping_info.log', date('[Y-m-d H:i:s]') . " ALTER FAILED: $sqlAlter -- " . $e->getMessage() . " POST=" . json_encode($_POST) . PHP_EOL, FILE_APPEND);
                }
            }
        }
    }
    $setParts = [];
    $params = [];
    foreach ($updateFields as $key => $value) {
        // Backtick column names to avoid reserved-word conflicts
        $setParts[] = "`$key` = :$key";
        $params[":$key"] = $value;
    }
    $params[':id'] = $user_id;
    $sql = 'UPDATE users SET ' . implode(', ', $setParts) . ' WHERE id = :id';
    // Log SQL and params for debugging in case of errors
    @file_put_contents(__DIR__ . '/save_shipping_info.log', date('[Y-m-d H:i:s]') . " PREPARE: $sql -- PARAMS=" . json_encode($params) . " POST=" . json_encode($_POST) . PHP_EOL, FILE_APPEND);
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute($params);
    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        // Log failure with error info
        $err = $stmt->errorInfo();
        @file_put_contents(__DIR__ . '/save_shipping_info.log', date('[Y-m-d H:i:s]') . " EXECUTE FAILED: " . json_encode($err) . " SQL=" . $sql . " PARAMS=" . json_encode($params) . " POST=" . json_encode($_POST) . PHP_EOL, FILE_APPEND);
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
} catch (PDOException $e) {
    // Log exception details
    @file_put_contents(__DIR__ . '/save_shipping_info.log', date('[Y-m-d H:i:s]') . " EXCEPTION: " . $e->getMessage() . " TRACE=" . $e->getTraceAsString() . " POST=" . json_encode($_POST) . PHP_EOL, FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
