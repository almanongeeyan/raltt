<?php
// Returns an array of branch product counts: [branch_id => count]
require_once '../connection/connection.php';

$sql = "SELECT pb.branch_id, COUNT(DISTINCT pb.product_id) as product_count
        FROM product_branches pb
        JOIN products p ON pb.product_id = p.product_id
        WHERE p.is_archived = 0
        GROUP BY pb.branch_id";
$stmt = $db_connection->prepare($sql);
$stmt->execute();
$counts = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $counts[$row['branch_id']] = (int)$row['product_count'];
}
echo json_encode($counts);
