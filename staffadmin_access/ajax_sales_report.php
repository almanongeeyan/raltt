<?php
session_start();
require_once '../connection/connection.php';

header('Content-Type: application/json');

$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
$filter_date = $_GET['dateFilter'] ?? '';
$filter_start = $_GET['dateStart'] ?? '';
$filter_end = $_GET['dateEnd'] ?? '';
$filter_search = $_GET['search'] ?? '';

$totalSales = 0;
$totalUnits = 0;
$totalCompletedOrders = 0;
$averageOrderValue = 0;
$salesData = [];
$error_message = '';
$productSalesSum = 0;

$dateWhereClause = '';
$dateParams = [];
$searchWhereClause = '';
$searchParams = [];

switch ($filter_date) {
    case 'today':
        $dateWhereClause = " AND DATE(o.order_date) = CURDATE()";
        break;
    case 'week':
        $dateWhereClause = " AND YEARWEEK(o.order_date, 0) = YEARWEEK(NOW(), 0)";
        break;
    case 'month':
        $dateWhereClause = " AND YEAR(o.order_date) = YEAR(NOW()) AND MONTH(o.order_date) = MONTH(NOW())";
        break;
    case 'custom':
        if (!empty($filter_start) && !empty($filter_end)) {
            $dateWhereClause = " AND DATE(o.order_date) BETWEEN ? AND ?";
            $dateParams[] = $filter_start;
            $dateParams[] = $filter_end;
        }
        break;
}

if (!empty($filter_search)) {
    $searchWhereClause = " AND p.product_name LIKE ?";
    $searchParams[] = "%$filter_search%";
}

try {
    $ordersSql = "SELECT o.order_id, o.total_amount 
                  FROM orders o 
                  WHERE o.order_status = 'completed'";
    $ordersParams = [];
    if ($branch_id) {
        $ordersSql .= " AND o.branch_id = ?";
        $ordersParams[] = $branch_id;
    }
    $ordersSql .= $dateWhereClause;
    $ordersParams = array_merge($ordersParams, $dateParams);
    $ordersStmt = $db_connection->prepare($ordersSql);
    $ordersStmt->execute($ordersParams);
    $completedOrders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
    $totalCompletedOrders = count($completedOrders);
    foreach ($completedOrders as $order) {
        $totalSales += (float)$order['total_amount'];
    }
    $orderProductTotals = [];
    foreach ($completedOrders as $order) {
        $orderId = $order['order_id'];
        $oiSql = "SELECT SUM(quantity * unit_price) AS product_total FROM order_items WHERE order_id = ?";
        $oiStmt = $db_connection->prepare($oiSql);
        $oiStmt->execute([$orderId]);
        $productTotal = (float)$oiStmt->fetchColumn();
        $orderProductTotals[] = $productTotal;
    }
    $productSalesSum = array_sum($orderProductTotals);
    $averageOrderValue = $totalCompletedOrders > 0 ? ($productSalesSum / $totalCompletedOrders) : 0;

    $sql = "SELECT 
                p.product_name, 
                SUM(oi.quantity) as total_units, 
                SUM(oi.quantity * oi.unit_price) as total_revenue, 
                MAX(o.order_date) as last_sale_date
            FROM order_items oi
            JOIN products p ON oi.product_id = p.product_id
            JOIN orders o ON oi.order_id = o.order_id
            WHERE o.order_status = 'completed'";
    $params = [];
    if ($branch_id) {
        $sql .= " AND o.branch_id = ?";
        $params[] = $branch_id;
    }
    $sql .= $dateWhereClause;
    $params = array_merge($params, $dateParams);
    $sql .= $searchWhereClause;
    $params = array_merge($params, $searchParams);
    $sql .= " GROUP BY p.product_id, p.product_name
              ORDER BY total_revenue DESC";
    $stmt = $db_connection->prepare($sql);
    $stmt->execute($params);
    $salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($filter_search)) {
        $totalUnits = 0;
        foreach ($salesData as $row) {
            $totalUnits += (int)$row['total_units'];
        }
    } else {
        $unitSql = "SELECT SUM(oi.quantity) 
                    FROM order_items oi
                    JOIN orders o ON oi.order_id = o.order_id
                    WHERE o.order_status = 'completed'";
        $unitParams = [];
        if ($branch_id) {
            $unitSql .= " AND o.branch_id = ?";
            $unitParams[] = $branch_id;
        }
        $unitSql .= $dateWhereClause;
        $unitParams = array_merge($unitParams, $dateParams);
        $unitStmt = $db_connection->prepare($unitSql);
        $unitStmt->execute($unitParams);
        $totalUnits = (int)$unitStmt->fetchColumn();
    }

    echo json_encode([
        'success' => true,
        'totalSales' => number_format($totalSales, 2),
        'totalUnits' => $totalUnits,
        'averageOrderValue' => number_format($averageOrderValue, 2),
        'salesData' => $salesData
    ]);
    exit;

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Unable to load sales report. Please try again later.'
    ]);
    exit;
}
