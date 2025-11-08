<?php
session_start();
require_once '../connection/connection.php';

// -----------------------------------------------------------------
// --- AJAX LOGIC (ALL-IN-ONE FILE) ---
// -----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $response = [
        'success' => false, 
        'salesData' => [], 
        'totalSales' => 0, 
        'totalUnits' => 0, 
        'averageOrderValue' => 0,
        'totalOrders' => 0
    ];
    
    try {
        $branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
        
        // Get filters from POST
        $dateFilter = $_POST['dateFilter'] ?? '';
        $dateStart = $_POST['dateStart'] ?? '';
        $dateEnd = $_POST['dateEnd'] ?? '';
        $search = $_POST['search'] ?? '';
        $sortOrder = $_POST['sortOrder'] ?? 'desc'; // 'asc' or 'desc'

        // Validate date inputs
        if ($dateFilter === 'custom') {
            if (empty($dateStart) || empty($dateEnd)) {
                throw new Exception("Custom date range requires both start and end dates.");
            }
            
            // Validate date format and logical order
            $startTimestamp = strtotime($dateStart);
            $endTimestamp = strtotime($dateEnd);
            
            if (!$startTimestamp || !$endTimestamp) {
                throw new Exception("Invalid date format provided.");
            }
            
            if ($startTimestamp > $endTimestamp) {
                throw new Exception("Start date cannot be after end date.");
            }
            
            // Ensure dates are not in the future
            $today = strtotime(date('Y-m-d'));
            if ($startTimestamp > $today || $endTimestamp > $today) {
                throw new Exception("Date range cannot include future dates.");
            }
        }

        // --- Build WHERE clauses ---
        $params = [];
        $orderWhere = "o.order_status = 'completed'";
        
        if ($branch_id) {
            $orderWhere .= " AND o.branch_id = ?";
            $params[] = $branch_id;
        }
        
        $dateWhere = "";
        if ($dateFilter === 'today') {
            $dateWhere = " AND DATE(o.order_date) = CURDATE()";
        } elseif ($dateFilter === 'week') {
            $dateWhere = " AND YEARWEEK(o.order_date, 1) = YEARWEEK(CURDATE(), 1)";
        } elseif ($dateFilter === 'month') {
            $dateWhere = " AND MONTH(o.order_date) = MONTH(CURDATE()) AND YEAR(o.order_date) = YEAR(CURDATE())";
        } elseif ($dateFilter === 'custom' && !empty($dateStart) && !empty($dateEnd)) {
            $dateWhere = " AND DATE(o.order_date) BETWEEN ? AND ?";
            $params[] = $dateStart;
            $params[] = $dateEnd;
        }
        
        $orderWhere .= $dateWhere;
        
        $searchWhere = "";
        if (!empty($search)) {
            $searchWhere = " AND (p.product_name LIKE ? OR p.product_id LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        // --- Query 1: Summary Metrics ---
        $summarySql = "
            SELECT 
                COALESCE(SUM(o.total_amount), 0) AS totalSales,
                COALESCE(COUNT(DISTINCT o.order_id), 0) AS totalOrders
            FROM orders o
            WHERE $orderWhere
        ";
        
        $summaryStmt = $db_connection->prepare($summarySql);
        $summaryStmt->execute($params);
        $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);
        
        $response['totalSales'] = (float)$summary['totalSales'];
        $response['totalOrders'] = (int)$summary['totalOrders'];

        // --- Query 2: Detailed Product Sales List ---
        $salesSql = "
            SELECT 
                p.product_id,
                p.product_name,
                SUM(oi.quantity) AS total_units,
                SUM(oi.quantity * oi.unit_price) AS total_revenue,
                MAX(o.order_date) AS last_sale_date,
                GROUP_CONCAT(DISTINCT u.full_name ORDER BY u.full_name SEPARATOR ', ') AS customer_names,
                COUNT(DISTINCT o.user_id) AS unique_customers,
                b.branch_name AS top_branch_name
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.order_id
            JOIN products p ON oi.product_id = p.product_id
            JOIN users u ON o.user_id = u.id
            LEFT JOIN branches b ON o.branch_id = b.branch_id
            WHERE $orderWhere $searchWhere
            GROUP BY p.product_id, p.product_name
            ORDER BY total_revenue " . ($sortOrder === 'asc' ? 'ASC' : 'DESC') . "
        ";
        
        $salesStmt = $db_connection->prepare($salesSql);
        $salesStmt->execute($params);
        $salesData = $salesStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response['salesData'] = $salesData;
        $response['totalUnits'] = array_sum(array_column($salesData, 'total_units'));
        $response['averageOrderValue'] = ($response['totalOrders'] > 0) ? 
            ($response['totalSales'] / $response['totalOrders']) : 0;
        $response['success'] = true;
        
    } catch (PDOException $e) {
        $response['error'] = "Database error: " . $e->getMessage();
        error_log($response['error']);
    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
        error_log($response['error']);
    }
    
    echo json_encode($response);
    exit;
}

// -----------------------------------------------------------------
// --- HTML PAGE LOGIC (GET Request) ---
// -----------------------------------------------------------------

// This part only runs if it's not an AJAX POST request
include '../includes/sidebar.php';

// Get branch name for the header
$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
$branch_name = 'All Branches';
if ($branch_id) {
    try {
        $branchSql = "SELECT branch_name FROM branches WHERE branch_id = ?";
        $branchStmt = $db_connection->prepare($branchSql);
        $branchStmt->execute([$branch_id]);
        $branch_name = $branchStmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Branch Error: " . $e->getMessage());
    }
}

// Get filter values from URL to set initial state of form fields
$filter_date = $_GET['dateFilter'] ?? '';
$filter_start = $_GET['dateStart'] ?? '';
$filter_end = $_GET['dateEnd'] ?? '';
$filter_search = $_GET['search'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sales Report - <?php echo htmlspecialchars($branch_name); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 80px;
            --transition-speed: 0.3s;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: #f8fafc;
        }
        
        .main-content-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: margin-left var(--transition-speed);
            padding: 2rem;
            width: calc(100% - var(--sidebar-width));
        }

        html.sidebar-collapsed .main-content-wrapper {
            margin-left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }

        @media (max-width: 768px) {
            .main-content-wrapper {
                margin-left: 0;
                padding: 1rem;
                width: 100%;
            }
        }

        /* Card hover effect */
        .summary-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .summary-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        /* Sticky table header */
        .table-header-sticky th {
            position: sticky;
            top: 0;
            background-color: #f9fafb;
            z-index: 10;
        }

        /* Modal styles */
        .modal-overlay {
            z-index: 2000;
        }
        .modal-content {
            z-index: 2100;
            margin: auto;
        }

        /* Print styles */
        @media print {
            body > * { display: none !important; }
            #reportModal, #modalPrintArea { 
                display: block !important; 
                position: relative !important;
                inset: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
                background: white !important;
                border: none !important;
                box-shadow: none !important;
                overflow: visible !important;
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
                max-height: none !important;
            }
            
            .modal-header, .modal-footer, .no-print { 
                display: none !important; 
            }
            
            #modalPrintArea {
                max-width: 100% !important; 
                margin: 0 !important; 
                padding: 0.5in !important;
                box-shadow: none !important; 
                border: none !important;
            }
            
            table { 
                width: 100% !important; 
                border-collapse: collapse !important; 
                font-size: 10pt !important; 
            }
            
            th, td { 
                border: 1px solid #333 !important; 
                padding: 6px !important; 
                color: #000 !important; 
                background: white !important;
            }
            
            h1, h2, h3, p, div, span { 
                color: #000 !important; 
            }
            
            .print-header { 
                display: block !important; 
                text-align: center !important; 
                margin-bottom: 1rem !important;
                border-bottom: 2px solid #333 !important;
                padding-bottom: 0.5rem !important;
            }
            
            .print-header h1 { 
                font-size: 20pt !important; 
                font-weight: bold !important; 
                margin-bottom: 0.5rem !important;
            }
            
            .print-header p { 
                font-size: 11pt !important; 
                color: #333 !important; 
                margin: 0.25rem 0 !important;
            }
            
            .print-summary { 
                margin-bottom: 1rem !important; 
                display: flex !important; 
                justify-content: space-between !important;
                flex-wrap: wrap !important;
            }
            
            .print-summary > div { 
                border: 1px solid #333 !important; 
                padding: 0.75rem !important; 
                border-radius: 4px !important; 
                text-align: center !important;
                flex: 1 !important;
                margin: 0 0.25rem 0.5rem !important;
                min-width: 30% !important;
            }
            
            .print-summary-label { 
                font-size: 10pt !important; 
                color: #333 !important; 
                margin-bottom: 0.25rem !important;
                font-weight: bold !important;
            }
            
            .print-summary-value { 
                font-size: 14pt !important; 
                font-weight: bold !important; 
                color: #000 !important; 
            }
            
            .no-print-in-modal { 
                display: none !important; 
            }
            
            .customer-names-print {
                max-width: 150px !important;
                word-break: break-word !important;
            }
            
            /* Ensure tables don't break across pages */
            table { page-break-inside: auto !important; }
            tr { page-break-inside: avoid !important; page-break-after: auto !important; }
            thead { display: table-header-group !important; }
            tfoot { display: table-footer-group !important; }
        }
        
        /* Loading animation */
        .loading-spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        /* Error message styling */
        .error-message {
            background-color: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        
        /* Success message styling */
        .success-message {
            background-color: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    
    <div class="main-content-wrapper">
        <main>
            <div class="max-w-7xl mx-auto">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-line mr-3 text-blue-600"></i>Sales Report
                    </h1>
                    <p class="text-lg text-gray-600 mt-2">
                        Viewing completed sales data for <span class="font-bold text-blue-700"><?php echo htmlspecialchars($branch_name); ?></span>
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 border-l-4 border-yellow-500 flex items-center summary-card">
                        <div class="rounded-full bg-yellow-50 p-5 mr-5">
                            <i class="fas fa-coins text-yellow-500 text-3xl"></i>
                        </div>
                        <div>
                            <div class="text-gray-500 text-sm font-bold uppercase tracking-wider">Total Sales</div>
                            <div class="font-extrabold text-3xl text-gray-900 mt-1" id="summaryTotalSales">₱0.00</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 border-l-4 border-blue-500 flex items-center summary-card">
                        <div class="rounded-full bg-blue-50 p-5 mr-5">
                            <i class="fas fa-cubes text-blue-500 text-3xl"></i>
                        </div>
                        <div>
                            <div class="text-gray-500 text-sm font-bold uppercase tracking-wider">Total Units Sold</div>
                            <div class="font-extrabold text-3xl text-gray-900 mt-1" id="summaryTotalUnits">0</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 border-l-4 border-purple-500 flex items-center summary-card">
                        <div class="rounded-full bg-purple-50 p-5 mr-5">
                            <i class="fas fa-file-invoice-dollar text-purple-500 text-3xl"></i>
                        </div>
                        <div>
                            <div class="text-gray-500 text-sm font-bold uppercase tracking-wider">Avg. Order Value</div>
                            <div class="font-extrabold text-3xl text-gray-900 mt-1" id="summaryAOV">₱0.00</div>
                        </div>
                    </div>
                </div>

                <form id="filterForm" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8 no-print" autocomplete="off">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-12 gap-6">
                        <div class="xl:col-span-4">
                            <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-2">Search Product</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" name="search" id="searchInput" placeholder="By product name or ID..." 
                                       class="pl-10 w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block transition-colors" 
                                       value="<?php echo htmlspecialchars($filter_search); ?>" />
                            </div>
                        </div>

                        <div class="xl:col-span-3">
                            <label for="dateFilter" class="block text-sm font-medium text-gray-700 mb-2">Timeframe</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="far fa-calendar-alt text-gray-400"></i>
                                </div>
                                <select id="dateFilter" name="dateFilter" class="pl-10 w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block transition-colors appearance-none">
                                    <option value="" <?php echo ($filter_date == '') ? 'selected' : ''; ?>>All Time</option>
                                    <option value="today" <?php echo ($filter_date == 'today') ? 'selected' : ''; ?>>Today</option>
                                    <option value="week" <?php echo ($filter_date == 'week') ? 'selected' : ''; ?>>This Week</option>
                                    <option value="month" <?php echo ($filter_date == 'month') ? 'selected' : ''; ?>>This Month</option>
                                    <option value="custom" <?php echo ($filter_date == 'custom') ? 'selected' : ''; ?>>Custom Range</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <div class="xl:col-span-3">
                            <label for="sortOrder" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-sort-amount-down text-gray-400"></i>
                                </div>
                                <select id="sortOrder" name="sortOrder" class="pl-10 w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block transition-colors appearance-none">
                                    <option value="desc">Highest Sales First</option>
                                    <option value="asc">Lowest Sales First</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <div class="xl:col-span-2 flex items-end">
                            <button type="button" id="generateReportBtn" class="w-full px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-lg shadow hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-300 active:scale-95 flex items-center justify-center">
                                <i class="fas fa-sync-alt mr-2"></i> Refresh
                            </button>
                        </div>
                    </div>

                    <div id="customDateRow" class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6 pt-6 border-t border-gray-100 hidden opacity-0 transform -translate-y-4 transition-all duration-300 ease-in-out">
                         <div>
                            <label for="dateStart" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" id="dateStart" name="dateStart" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block transition-colors" value="<?php echo htmlspecialchars($filter_start); ?>" />
                        </div>
                        <div>
                            <label for="dateEnd" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" id="dateEnd" name="dateEnd" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block transition-colors" value="<?php echo htmlspecialchars($filter_end); ?>" />
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div id="filterText" class="text-blue-800 bg-blue-50 px-3 py-1.5 rounded-full text-sm font-medium flex items-center">
                             <i class="fas fa-info-circle mr-2"></i>
                             <span>Showing all sales</span>
                        </div>
                        <div id="errorMessage" class="hidden w-full sm:w-auto"></div>
                    </div>
                </form>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden print-table">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                         <h2 class="text-lg font-semibold text-gray-800">Product Sales Details</h2>
                         <div class="text-sm text-gray-500">
                             <i class="fas fa-table mr-1"></i> Sales Data
                         </div>
                    </div>
                    <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="table-header-sticky bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Units Sold</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total Sales</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Last Sale Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Customers</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider"># of Customers</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Top Branch</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white" id="salesTableBody">
                                <tr id="loading-row">
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                        <i class="fas fa-spinner fa-spin text-4xl text-blue-200 mb-4"></i>
                                        <p class="text-gray-500 font-medium">Loading sales data...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 text-right">
                        <button id="openReportModalBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                             <i class="fas fa-expand mr-2"></i> View Full Report
                        </button>
                    </div>
                </div>
                
            </div>
        </main>
    </div>

    <div id="reportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 modal-overlay hidden">
        <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full max-h-[90vh] flex flex-col modal-content">
            <div class="modal-header flex justify-between items-center p-5 border-b">
                <h2 class="text-2xl font-bold text-gray-800">Sales Report</h2>
                <button id="modalCloseBtn1" class="text-gray-400 hover:text-gray-600 text-3xl focus:outline-none">&times;</button>
            </div>
            
            <div id="modalPrintArea" class="p-8 overflow-y-auto">
                <div class="print-header hidden">
                    <h1>Sales Report</h1>
                    <p>Branch: <span id="modalBranchName"><?php echo htmlspecialchars($branch_name); ?></span></p>
                    <p>Report for: <span id="modalFilterTimeframe"></span></p>
                    <p>Generated on: <span id="modalGeneratedDate"></span></p>
                </div>
                
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Report Summary</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8 print-summary">
                    <div class="bg-gray-50 p-4 rounded-lg border">
                        <div class="text-gray-500 font-semibold print-summary-label">Total Sales</div>
                        <div class="font-bold text-2xl text-green-700 print-summary-value" id="modalTotalSales"></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border">
                        <div class="text-gray-500 font-semibold print-summary-label">Total Units Sold</div>
                        <div class="font-bold text-2xl text-blue-700 print-summary-value" id="modalTotalUnits"></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border">
                        <div class="text-gray-500 font-semibold print-summary-label">Avg. Order Value</div>
                        <div class="font-bold text-2xl text-purple-700 print-summary-value" id="modalAOV"></div>
                    </div>
                </div>
                
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Detailed Product Sales</h3>
                <div class="overflow-x-auto rounded-lg border max-h-[40vh]">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 table-header-sticky">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Units Sold</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sales</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Sale Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customers</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"># of Customers</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Top Branch</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="modalSalesTableBody">
                        </tbody>
                    </table>
                </div>
                <p class="text-sm text-gray-500 mt-4 no-print-in-modal">
                    * Total Sales in summary card includes shipping fees.
                    * Total Sales in the table represents product revenue only.
                </p>

            </div>
            
            <div class="modal-footer flex justify-end gap-3 p-5 border-t bg-gray-50 rounded-b-xl">
                <button id="modalCloseBtn2" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg shadow-sm hover:bg-gray-50 transition focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">Close</button>
                <button id="modalExportExcelBtn" class="px-5 py-2.5 bg-green-600 text-white rounded-lg shadow-sm hover:bg-green-700 transition focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <i class="fas fa-file-excel mr-2"></i>Export to Excel
                </button>
                <button id="modalPrintBtn" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-print mr-2"></i>Print Report
                </button>
            </div>
        </div>
    </div>

    <div id="customerNamesModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 modal-overlay hidden z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full max-h-[80vh] flex flex-col modal-content">
            <div class="modal-header flex justify-between items-center p-5 border-b">
                <h2 class="text-xl font-bold text-gray-800">Customer Names</h2>
                <button id="modalCloseBtnCustomer" class="text-gray-400 hover:text-gray-600 text-3xl focus:outline-none">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto">
                <ul id="customerNamesList" class="list-disc pl-6 text-gray-700"></ul>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const filterText = document.getElementById('filterText').querySelector('span');
            const dateFilter = document.getElementById('dateFilter');
            const dateStart = document.getElementById('dateStart');
            const dateEnd = document.getElementById('dateEnd');
            const customDateRow = document.getElementById('customDateRow');
            const searchInput = document.getElementById('searchInput');
            const salesTableBody = document.getElementById('salesTableBody');
            const summaryTotalSales = document.getElementById('summaryTotalSales');
            const summaryTotalUnits = document.getElementById('summaryTotalUnits');
            const summaryAOV = document.getElementById('summaryAOV');
            const errorMessage = document.getElementById('errorMessage');
            
            // --- Date Filter and Validation Logic ---
            function setupDateInputs() {
                const today = new Date().toISOString().split('T')[0];
                
                // Prevent selecting future dates
                dateStart.max = today;
                dateEnd.max = today;

                dateFilter.addEventListener('change', () => {
                    updateDateInputs();
                    fetchSalesReport();
                });
                
                dateStart.addEventListener('change', () => {
                    dateEnd.min = dateStart.value;
                    if (dateEnd.value && dateStart.value > dateEnd.value) {
                        dateEnd.value = dateStart.value;
                    }
                    if (dateFilter.value === 'custom') fetchSalesReport();
                });

                dateEnd.addEventListener('change', () => {
                    dateStart.max = dateEnd.value || today;
                    if (dateStart.value && dateStart.value > dateEnd.value) {
                        dateStart.value = dateEnd.value;
                    }
                    if (dateFilter.value === 'custom') fetchSalesReport();
                });

                updateDateInputs();
            }
            
            function updateDateInputs() {
                if (dateFilter.value === 'custom') {
                    customDateRow.classList.remove('hidden');
                    // Trigger reflow to ensure transition works
                    void customDateRow.offsetWidth; 
                    customDateRow.classList.remove('opacity-0', '-translate-y-4');
                    
                    if (!dateStart.value) {
                        const today = new Date();
                        const oneWeekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                        dateStart.value = oneWeekAgo.toISOString().split('T')[0];
                    }
                    if (!dateEnd.value) {
                        dateEnd.value = new Date().toISOString().split('T')[0];
                    }
                } else {
                    customDateRow.classList.add('opacity-0', '-translate-y-4');
                    // Wait for transition to finish before hiding
                    setTimeout(() => {
                         if (dateFilter.value !== 'custom') {
                             customDateRow.classList.add('hidden');
                         }
                    }, 300);
                }
            }
            
            setupDateInputs();

            // Filter Text Update
            function updateFilterText(filterVal, startVal, endVal, searchVal) {
                let text = '';
                if (filterVal === 'today') text = 'Showing sales for today';
                else if (filterVal === 'week') text = 'Showing sales for this week';
                else if (filterVal === 'month') text = 'Showing sales for this month';
                else if (filterVal === 'custom' && startVal && endVal) {
                    text = `Showing sales from ${startVal} to ${endVal}`;
                } else {
                    text = 'Showing all sales';
                }
                if (searchVal) {
                    text += ` | Searching for "${searchVal}"`;
                }
                filterText.textContent = text;
                document.getElementById('modalFilterTimeframe').textContent = text;
            }

            // AJAX Filtering
            let currentRequest = null;
            let searchDebounce = null;
            let currentSalesData = null;
            
            function showError(message) {
                errorMessage.innerHTML = `<div class="error-message">${message}</div>`;
                errorMessage.classList.remove('hidden');
                setTimeout(() => { errorMessage.classList.add('hidden'); }, 5000);
            }
            
            function showSuccess(message) {
                errorMessage.innerHTML = `<div class="success-message">${message}</div>`;
                errorMessage.classList.remove('hidden');
                setTimeout(() => { errorMessage.classList.add('hidden'); }, 3000);
            }
            
            function fetchSalesReport() {
                if (currentRequest) currentRequest.abort();
                
                // Validate custom date range
                if (dateFilter.value === 'custom') {
                    if (!dateStart.value || !dateEnd.value) {
                        showError('Please select both start and end dates.');
                        return;
                    }
                    if (dateStart.value > dateEnd.value) {
                        showError('Start date cannot be after end date.');
                        return;
                    }
                }
                
                const formData = new FormData();
                formData.append('dateFilter', dateFilter.value);
                formData.append('dateStart', dateStart.value);
                formData.append('dateEnd', dateEnd.value);
                formData.append('search', searchInput.value);
                const sortOrder = document.getElementById('sortOrder').value;
                formData.append('sortOrder', sortOrder);

                salesTableBody.innerHTML = `
                    <tr id="loading-row">
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            <i class="fas fa-spinner fa-spin text-4xl text-blue-200 mb-4 loading-spinner"></i>
                            <p class="text-gray-500 font-medium">Loading sales data...</p>
                        </td>
                    </tr>
                `;
                
                // Animate summary values to show loading state
                [summaryTotalSales, summaryTotalUnits, summaryAOV].forEach(el => el.classList.add('opacity-50'));

                currentRequest = new AbortController();
                
                fetch('<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>', { 
                    method: 'POST', body: formData, signal: currentRequest.signal 
                })
                .then(res => res.ok ? res.json() : Promise.reject(`HTTP error! status: ${res.status}`))
                .then(data => {
                    currentRequest = null;
                    [summaryTotalSales, summaryTotalUnits, summaryAOV].forEach(el => el.classList.remove('opacity-50'));
                    
                    if (!data.success) {
                        showError(data.error || 'Error loading data.');
                        salesTableBody.innerHTML = `<tr><td colspan="7" class="px-6 py-10 text-center text-red-500"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p>${data.error || 'Error loading data.'}</p></td></tr>`;
                        return;
                    }
                    
                    currentSalesData = data;
                    summaryTotalSales.textContent = '₱' + parseFloat(data.totalSales).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                    summaryTotalUnits.textContent = data.totalUnits.toLocaleString();
                    summaryAOV.textContent = '₱' + parseFloat(data.averageOrderValue).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                    
                    if (!data.salesData || data.salesData.length === 0) {
                        salesTableBody.innerHTML = `
                            <tr id="no-orders-row">
                                <td colspan="7" class="px-6 py-16 text-center text-gray-400">
                                    <i class="fas fa-search text-5xl mb-4 opacity-20"></i>
                                    <p class="text-lg font-medium">No sales found</p>
                                    <p class="text-sm">Try adjusting your filters</p>
                                </td>
                            </tr>
                        `;
                    } else {
                        salesTableBody.innerHTML = data.salesData.map((row, idx) => {
                            const names = (row.customer_names || '').split(', ').filter(Boolean);
                            let customerSummary = '';
                            if (names.length === 0) {
                                customerSummary = '<span class="text-gray-400 italic">None</span>';
                            } else if (names.length <= 2) {
                                customerSummary = names.map(escapeHTML).join(', ');
                            } else {
                                customerSummary = `${names.length} customers: ${names.slice(0,2).map(escapeHTML).join(', ')}, <span class=\"text-blue-600 font-medium\">View All</span>`;
                            }
                            // Make the cell clickable if there are customers
                            const customerCell = names.length === 0
                                ? `<td class=\"px-6 py-4 text-sm text-gray-400 italic\">None</td>`
                                : `<td class=\"px-6 py-4 text-sm text-gray-600 cursor-pointer hover:bg-blue-100/60 rounded-lg transition view-all-customers-cell\" data-names=\"${encodeURIComponent(row.customer_names)}\">${customerSummary}</td>`;
                            return `
                            <tr class=\"hover:bg-blue-50/50 transition sales-row\">
                                <td class=\"px-6 py-4 text-sm font-medium text-gray-900\">${escapeHTML(row.product_name)}</td>
                                <td class=\"px-6 py-4 text-sm text-right text-gray-600 font-semibold\">${parseInt(row.total_units).toLocaleString()}</td>
                                <td class=\"px-6 py-4 text-sm text-right text-green-700 font-bold\">₱${parseFloat(row.total_revenue).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                                <td class=\"px-6 py-4 text-sm text-gray-500\">${row.last_sale_date ? (new Date(row.last_sale_date)).toLocaleDateString('en-US', {year:'numeric', month:'short', day:'numeric'}) : '-'}</td>
                                ${customerCell}
                                <td class=\"px-6 py-4 text-sm text-right text-gray-500\">${parseInt(row.unique_customers).toLocaleString()}</td>
                                <td class=\"px-6 py-4 text-sm text-gray-600\">${escapeHTML(row.top_branch_name || '-')}</td>
                            </tr>`;
                        }).join('');
                    }
                    updateFilterText(dateFilter.value, dateStart.value, dateEnd.value, searchInput.value);
                })
                .catch(err => { if (err.name !== 'AbortError') { console.error(err); showError('Connection error. Please try again.'); } });
            }
            
            function escapeHTML(str) {
                return (str || '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
            }

            // Event listeners
            searchInput.addEventListener('input', function() {
                clearTimeout(searchDebounce);
                searchDebounce = setTimeout(fetchSalesReport, 400);
            });
            document.getElementById('sortOrder').addEventListener('change', fetchSalesReport);
            document.getElementById('generateReportBtn').addEventListener('click', () => {
                 // Add a small rotation animation on click
                 const icon = this.querySelector('.fa-sync-alt');
                 if(icon) icon.classList.add('fa-spin');
                 fetchSalesReport();
                 setTimeout(() => { if(icon) icon.classList.remove('fa-spin'); }, 1000);
            });
            document.getElementById('openReportModalBtn').addEventListener('click', showReportModal);

            // Modal Logic
            const reportModal = document.getElementById('reportModal');
            const hideReportModal = () => reportModal.classList.add('hidden');
            
            function showReportModal() {
                if (!currentSalesData) { showError('Please wait for data to load.'); return; }
                
                document.getElementById('modalTotalSales').textContent = summaryTotalSales.textContent;
                document.getElementById('modalTotalUnits').textContent = summaryTotalUnits.textContent;
                document.getElementById('modalAOV').textContent = summaryAOV.textContent;
                document.getElementById('modalGeneratedDate').textContent = new Date().toLocaleString();
                
                const modalBody = document.getElementById('modalSalesTableBody');
                if (!currentSalesData.salesData || currentSalesData.salesData.length === 0) {
                    modalBody.innerHTML = '<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">No data available for this report.</td></tr>';
                } else {
                    modalBody.innerHTML = currentSalesData.salesData.map(row => {
                        const names = (row.customer_names || '').split(', ').filter(Boolean);
                        return `<tr>
                            <td class="px-4 py-3 font-medium text-gray-800">${escapeHTML(row.product_name)}</td>
                            <td class="px-4 py-3 text-right">${parseInt(row.total_units).toLocaleString()}</td>
                            <td class="px-4 py-3 text-right font-semibold">₱${parseFloat(row.total_revenue).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                            <td class="px-4 py-3 text-gray-600">${row.last_sale_date ? (new Date(row.last_sale_date)).toLocaleDateString() : '-'}</td>
                            <td class="px-4 py-3 text-sm customer-names-print">${names.length ? names.map(escapeHTML).join('<br>') : '-'}</td>
                            <td class="px-4 py-3 text-right">${parseInt(row.unique_customers).toLocaleString()}</td>
                            <td class="px-4 py-3 text-gray-600">${escapeHTML(row.top_branch_name || '-')}</td>
                        </tr>`;
                    }).join('');
                }
                reportModal.classList.remove('hidden');
            }
            
            document.getElementById('modalCloseBtn1').addEventListener('click', hideReportModal);
            document.getElementById('modalCloseBtn2').addEventListener('click', hideReportModal);
            document.getElementById('modalPrintBtn').addEventListener('click', () => {
                document.querySelectorAll('.print-header').forEach(el => el.classList.remove('hidden'));
                window.print();
                setTimeout(() => document.querySelectorAll('.print-header').forEach(el => el.classList.add('hidden')), 500);
            });

             document.getElementById('modalExportExcelBtn').addEventListener('click', () => {
                if (!currentSalesData) { showError('No data to export.'); return; }
                const wb = XLSX.utils.book_new();
                
                // Summary Sheet
                const ws_summary = XLSX.utils.aoa_to_sheet([
                    ["Sales Report Summary"], [],
                    ["Branch:", "<?php echo htmlspecialchars($branch_name); ?>"],
                    ["Timeframe:", document.getElementById('modalFilterTimeframe').textContent],
                    ["Generated:", new Date().toLocaleString()], [],
                    ["Metric", "Value"],
                    ["Total Sales", currentSalesData.totalSales],
                    ["Total Units", currentSalesData.totalUnits],
                    ["Avg Order Value", currentSalesData.averageOrderValue]
                ]);
                XLSX.utils.book_append_sheet(wb, ws_summary, 'Summary');

                // Data Sheet
                if (currentSalesData.salesData?.length > 0) {
                    const ws_data = XLSX.utils.json_to_sheet(currentSalesData.salesData.map(row => ({
                        "Product ID": row.product_id, "Product Name": row.product_name,
                        "Units Sold": parseInt(row.total_units), "Total Sales": parseFloat(row.total_revenue),
                        "Last Sale": row.last_sale_date, "Customers": row.customer_names,
                        "Customer Count": parseInt(row.unique_customers), "Branch": row.top_branch_name
                    })));
                    XLSX.utils.book_append_sheet(wb, ws_data, 'Sales Data');
                }
                XLSX.writeFile(wb, `SalesReport_${new Date().toISOString().slice(0,10)}.xlsx`);
                showSuccess('Excel file downloaded.');
            });

            // Customer Modal
            document.addEventListener('click', e => {
                // If click is on the new customer cell or its child, open modal
                let cell = e.target;
                // Traverse up to the cell if a child is clicked
                while (cell && !cell.classList?.contains('view-all-customers-cell') && cell !== document.body) {
                    cell = cell.parentElement;
                }
                if (cell && cell.classList?.contains('view-all-customers-cell')) {
                    e.preventDefault();
                    const list = document.getElementById('customerNamesList');
                    list.innerHTML = decodeURIComponent(cell.getAttribute('data-names')).split(', ').filter(Boolean)
                        .map(n => `<li class="py-1.5 border-b border-gray-100 last:border-0">${escapeHTML(n)}</li>`).join('');
                    document.getElementById('customerNamesModal').classList.remove('hidden');
                }
            });
            document.getElementById('modalCloseBtnCustomer').onclick = () => document.getElementById('customerNamesModal').classList.add('hidden');
            window.onclick = e => {
                if (e.target === reportModal) hideReportModal();
                if (e.target === document.getElementById('customerNamesModal')) document.getElementById('customerNamesModal').classList.add('hidden');
            };

            // Initial load
            fetchSalesReport();
        });
    </script>
</body>
</html>