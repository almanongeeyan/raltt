<?php
session_start();
require_once '../connection/connection.php';

header('Content-Type: application/json');

try {
    // --- 1. GET SESSION & FILTER PARAMETERS ---
    $session_branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
    $filter_search = $_GET['search'] ?? '';
    $filter_remarks = $_GET['remarks'] ?? '';
    $filter_date = $_GET['dateFilter'] ?? '';
    $filter_start = $_GET['dateStart'] ?? '';
    $filter_end = $_GET['dateEnd'] ?? '';

    // --- 2. BUILD DYNAMIC SQL QUERY ---
    $params = [];
    $sql = "SELECT 
                    p.product_name,
                    p.product_image,
                    COALESCE(pb.stock_count, 0) AS last_restock_quantity,
                    COALESCE(SUM(oi.quantity), 0) AS total_sold,
                    MAX(o.order_date) AS last_update,
                    (CASE 
                        WHEN (COALESCE(pb.stock_count, 0) + COALESCE(SUM(oi.quantity), 0)) = 0 THEN 0
                        ELSE (COALESCE(SUM(oi.quantity), 0) / (COALESCE(pb.stock_count, 0) + COALESCE(SUM(oi.quantity), 0))) * 100
                    END) AS percent_taken,
                    (CASE
                        WHEN COALESCE(pb.stock_count, 0) = 0 AND COALESCE(SUM(oi.quantity), 0) = 0 THEN 'No Stock'
                        WHEN COALESCE(pb.stock_count, 0) = 0 THEN 'No Stock'
                        WHEN (COALESCE(SUM(oi.quantity), 0) / (COALESCE(pb.stock_count, 0) + COALESCE(SUM(oi.quantity), 0))) * 100 >= 95 THEN 'Low Stock'
                        ELSE 'Sufficient'
                    END) AS remarks
                FROM products p
                LEFT JOIN product_branches pb ON p.product_id = pb.product_id AND pb.branch_id = ?
                LEFT JOIN order_items oi ON p.product_id = oi.product_id
                LEFT JOIN orders o ON oi.order_id = o.order_id AND o.order_status = 'completed' AND o.branch_id = ?
                WHERE p.is_archived = 0";

    // Ensure branch is always set for joins
    $branch_param = $session_branch_id ? $session_branch_id : 1; // Fallback to branch 1 if not set
    $params[] = $branch_param;
    $params[] = $branch_param;

    // Search filter
    if (!empty($filter_search)) {
        $sql .= " AND p.product_name LIKE ?";
        $params[] = "%$filter_search%";
    }

    // Date filter
    if (!empty($filter_date)) {
        switch ($filter_date) {
            case 'today':
                $sql .= " AND DATE(o.order_date) = CURDATE()";
                break;
            case 'week':
                $sql .= " AND YEARWEEK(o.order_date, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'month':
                $sql .= " AND MONTH(o.order_date) = MONTH(CURDATE()) AND YEAR(o.order_date) = YEAR(CURDATE())";
                break;
            case 'custom':
                if (!empty($filter_start) && !empty($filter_end)) {
                    $sql .= " AND DATE(o.order_date) BETWEEN ? AND ?";
                    $params[] = $filter_start;
                    $params[] = $filter_end;
                }
                break;
        }
    }

    $sql .= " GROUP BY p.product_id, p.product_name, p.product_image, pb.stock_count";

    // Remarks filter (HAVING clause must be after GROUP BY)
    if (!empty($filter_remarks)) {
        $sql .= " HAVING remarks = ?";
        $params[] = $filter_remarks;
    }

    $sql .= " ORDER BY p.product_name ASC";

    $stmt = $db_connection->prepare($sql);
    $stmt->execute($params);

    $inventoryData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Convert product_image blob to base64 data URI for each row
    foreach ($inventoryData as &$row) {
        if (!empty($row['product_image'])) {
            $row['product_image'] = 'data:image/jpeg;base64,' . base64_encode($row['product_image']);
        } else {
            $row['product_image'] = '../images/user/tile1.jpg';
        }
    }
    unset($row);

    echo json_encode(['success' => true, 'inventoryData' => $inventoryData]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => "Database Error: " . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => "An unexpected error occurred."]);
}
?>