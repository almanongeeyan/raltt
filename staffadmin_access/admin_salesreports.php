<?php
session_start();
require_once '../connection/connection.php';

// -----------------------------------------------------------------
// --- AJAX LOGIC (ALL-IN-ONE FILE) ---
// -----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $response = ['success' => false, 'salesData' => [], 'totalSales' => 0, 'totalUnits' => 0, 'averageOrderValue' => 0];
    
    try {
        $branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;

        // Get filters from POST
        $dateFilter = $_POST['dateFilter'] ?? '';
        $dateStart = $_POST['dateStart'] ?? '';
        $dateEnd = $_POST['dateEnd'] ?? '';
        $search = $_POST['search'] ?? '';

        // --- Build WHERE clauses ---
        $params = [];
        $orderWhere = "o.order_status = 'completed'";
        
        // Branch filtering
        if ($branch_id) {
            $orderWhere .= " AND o.branch_id = ?";
            $params[] = $branch_id;
        }

        // Date filtering
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

        // Search filtering
        $searchWhere = "";
        if (!empty($search)) {
            $searchWhere = " AND p.product_name LIKE ?";
            $params[] = '%' . $search . '%';
        }

        // --- Query 1: Summary Metrics ---
        // We run a separate, simpler query for the main summary cards
        $summarySql = "
            SELECT 
                COALESCE(SUM(o.total_amount), 0) AS totalSales,
                COALESCE(COUNT(DISTINCT o.order_id), 0) AS totalOrders
            FROM orders o
            WHERE $orderWhere
        ";
        // Re-bind params for summary (it has fewer params than the main query)
        $summaryParams = [];
        if ($branch_id) $summaryParams[] = $branch_id;
        if ($dateFilter === 'custom' && !empty($dateStart) && !empty($dateEnd)) {
            $summaryParams[] = $dateStart;
            $summaryParams[] = $dateEnd;
        }
        
        $summaryStmt = $db_connection->prepare($summarySql);
        $summaryStmt->execute($summaryParams);
        $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

        $response['totalSales'] = (float)$summary['totalSales'];
        $totalOrders = (int)$summary['totalOrders'];


        // --- Query 2: Detailed Product Sales List ---
        $salesSql = "
            SELECT 
                p.product_name,
                SUM(oi.quantity) AS total_units,
                SUM(oi.quantity * oi.unit_price) AS total_revenue,
                MAX(o.order_date) AS last_sale_date
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.order_id
            JOIN products p ON oi.product_id = p.product_id
            WHERE $orderWhere $searchWhere
            GROUP BY p.product_id, p.product_name
            ORDER BY total_revenue DESC
        ";

        $salesStmt = $db_connection->prepare($salesSql);
        $salesStmt->execute($params);
        $response['salesData'] = $salesStmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate Total Units from the detailed query
        $totalUnits = 0;
        foreach ($response['salesData'] as $row) {
            $totalUnits += (int)$row['total_units'];
        }
        $response['totalUnits'] = $totalUnits;

        // Calculate AOV
        $response['averageOrderValue'] = ($totalOrders > 0) ? ($response['totalSales'] / $totalOrders) : 0;
        
        $response['success'] = true;

    } catch (PDOException $e) {
        $response['error'] = "Database error: " . $e->getMessage();
        error_log($response['error']);
    } catch (Exception $e) {
        $response['error'] = "General error: " . $e->getMessage();
        error_log($response['error']);
    }

    echo json_encode($response);
    exit; // Stop script execution
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
            background-color: #f8fafc; /* Tailwind gray-50 */
        }
        
        /* Responsive layout that works with sidebar.js */
        .main-content-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width); /* Default for open sidebar */
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
            background-color: #f9fafb; /* gray-50 */
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
            #reportModal, #modalPrintArea { display: block !important; }
            #reportModal {
                position: relative; inset: 0; padding: 0; margin: 0;
                background: none; border: none; box-shadow: none;
                overflow: visible; width: 100%; max-width: 100%;
            }
            .modal-header, .modal-footer { display: none !important; }
            #modalPrintArea {
                max-width: 100%; margin: 0; padding: 1in;
                box-shadow: none; border: none;
            }
            table { width: 100%; border-collapse: collapse; font-size: 10pt; }
            th, td { border: 1px solid #999; padding: 8px; color: #000; }
            h1, h2, h3, p, div { color: #000 !important; }
            .print-header { display: block !important; text-align: center; margin-bottom: 2rem; }
            .print-header h1 { font-size: 24pt; font-weight: bold; }
            .print-header p { font-size: 12pt; color: #333 !important; }
            .print-summary { margin-bottom: 1.5rem; display: flex; justify-content: space-around; }
            .print-summary > div { border: 1px solid #ccc; padding: 1rem; border-radius: 8px; text-align: center; }
            .print-summary-label { font-size: 11pt; color: #333 !important; margin-bottom: 0.5rem; }
            .print-summary-value { font-size: 16pt; font-weight: bold; color: #000 !important; }
            .no-print-in-modal { display: none !important; }
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
                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500 flex items-center summary-card">
                        <div class="rounded-full bg-yellow-100 p-5 mr-5">
                            <i class="fas fa-coins text-yellow-500 text-3xl"></i>
                        </div>
                        <div>
                            <div class="text-gray-600 text-base font-semibold">Total Sales</div>
                            <div class="font-bold text-3xl text-gray-800" id="summaryTotalSales">₱0.00</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 flex items-center summary-card">
                        <div class="rounded-full bg-blue-100 p-5 mr-5">
                            <i class="fas fa-cubes text-blue-500 text-3xl"></i>
                        </div>
                        <div>
                            <div class="text-gray-600 text-base font-semibold">Total Units Sold</div>
                            <div class="font-bold text-3xl text-gray-800" id="summaryTotalUnits">0</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 flex items-center summary-card">
                        <div class="rounded-full bg-purple-100 p-5 mr-5">
                            <i class="fas fa-file-invoice-dollar text-purple-500 text-3xl"></i>
                        </div>
                        <div>
                            <div class="text-gray-600 text-base font-semibold">Avg. Order Value</div>
                            <div class="font-bold text-3xl text-gray-800" id="summaryAOV">₱0.00</div>
                        </div>
                    </div>
                </div>

                <form id="filterForm" class="bg-white rounded-xl shadow-md p-6 mb-8 no-print" autocomplete="off">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="relative flex-1 min-w-[250px]">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" name="search" id="searchInput" placeholder="Search product name..." class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" value="<?php echo htmlspecialchars($filter_search); ?>" />
                        </div>
                        <div class="flex gap-2 items-center flex-wrap">
                            <select id="dateFilter" name="dateFilter" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none min-w-[130px]">
                                <option value="" <?php echo ($filter_date == '') ? 'selected' : ''; ?>>All Time</option>
                                <option value="today" <?php echo ($filter_date == 'today') ? 'selected' : ''; ?>>Today</option>
                                <option value="week" <?php echo ($filter_date == 'week') ? 'selected' : ''; ?>>This Week</option>
                                <option value="month" <?php echo ($filter_date == 'month') ? 'selected' : ''; ?>>This Month</option>
                                <option value="custom" <?php echo ($filter_date == 'custom') ? 'selected' : ''; ?>>Custom Range</option>
                            </select>
                            <input type="date" id="dateStart" name="dateStart" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" value="<?php echo htmlspecialchars($filter_start); ?>" />
                            <span id="dateSeparator" class="mx-1 text-gray-400">-</span>
                            <input type="date" id="dateEnd" name="dateEnd" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" value="<?php echo htmlspecialchars($filter_end); ?>" />
                            <button type="button" id="generateReportBtn" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas fa-file-alt mr-2"></i>Generate Report
                            </button>
                        </div>
                    </div>
                    <div id="filterText" class="mt-4 text-blue-700 font-semibold text-lg"></div>
                </form>

                <div class="bg-white rounded-xl shadow-md p-6 overflow-x-auto print-table">
                    <div class="max-h-[600px] overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="table-header-sticky">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Units Sold</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sales</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Sale Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100" id="salesTableBody">
                                <tr id="loading-row">
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                        <i class="fas fa-spinner fa-spin text-3xl text-gray-300 mb-2"></i>
                                        <p>Loading sales data...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </main>
    </div>

    <div id="reportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 modal-overlay hidden">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] flex flex-col modal-content">
            <div class="modal-header flex justify-between items-center p-5 border-b">
                <h2 class="text-2xl font-bold text-gray-800">Sales Report</h2>
                <button id="modalCloseBtn1" class="text-gray-400 hover:text-gray-600 text-3xl focus:outline-none">&times;</button>
            </div>
            
            <div id="modalPrintArea" class="p-8 overflow-y-auto">
                <div class="print-header hidden">
                    <h1>Sales Report</h1>
                    <p>Branch: <span id="modalBranchName"><?php echo htmlspecialchars($branch_name); ?></span></p>
                    <p>Report for: <span id="modalFilterTimeframe"></span></p>
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
                <button id="modalCloseBtn2" class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg shadow-sm hover:bg-gray-300 transition focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">Close</button>
                <button id="modalExportExcelBtn" class="px-5 py-2.5 bg-green-600 text-white rounded-lg shadow-sm hover:bg-green-700 transition focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <i class="fas fa-file-excel mr-2"></i>Export to Excel
                </button>
                <button id="modalPrintBtn" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-print mr-2"></i>Print Report
                </button>
            </div>
        </div>
    </div>

    <script>
        // This is a placeholder for your sidebar.js
        // It assumes your sidebar toggle adds/removes a 'sidebar-collapsed' class to the <html> tag
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('sidebar-toggle'); // Assuming your button has this ID
            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    document.documentElement.classList.toggle('sidebar-collapsed');
                });
            }
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // DOM Elements
        const filterText = document.getElementById('filterText');
        const dateFilter = document.getElementById('dateFilter');
        const dateStart = document.getElementById('dateStart');
        const dateEnd = document.getElementById('dateEnd');
        const dateSeparator = document.getElementById('dateSeparator');
        const searchInput = document.getElementById('searchInput');
        const salesTableBody = document.getElementById('salesTableBody');
        const summaryTotalSales = document.getElementById('summaryTotalSales');
        const summaryTotalUnits = document.getElementById('summaryTotalUnits');
        const summaryAOV = document.getElementById('summaryAOV');
        
        // --- *** NEW: Date Filter and Validation Logic *** ---
        function setupDateInputs() {
            const today = new Date().toISOString().split('T')[0];
            
            // Prevent selecting future dates
            dateStart.max = today;
            dateEnd.max = today;

            dateFilter.addEventListener('change', () => {
                updateDateInputs();
                fetchSalesReport(); // Fetch when the dropdown changes
            });
            
            dateStart.addEventListener('change', () => {
                // Set the minimum selectable date for the end date picker
                dateEnd.min = dateStart.value;
                
                // Auto-correct if end is before start
                if (dateEnd.value && dateStart.value > dateEnd.value) {
                    dateEnd.value = dateStart.value;
                }
                fetchSalesReport();
            });

            dateEnd.addEventListener('change', () => {
                // Set the maximum selectable date for the start date picker
                dateStart.max = dateEnd.value || today; // Use end date, or fallback to today
                
                // Auto-correct if start is after end
                if (dateStart.value && dateStart.value > dateEnd.value) {
                    dateStart.value = dateEnd.value;
                }
                fetchSalesReport();
            });

            updateDateInputs(); // Set initial state
        }
        
        function updateDateInputs() {
            if (dateFilter.value === 'custom') {
                dateStart.style.display = 'inline-block';
                dateSeparator.style.display = 'inline-block';
                dateEnd.style.display = 'inline-block';
            } else {
                dateStart.style.display = 'none';
                dateSeparator.style.display = 'none';
                dateEnd.style.display = 'none';
            }
        }
        
        setupDateInputs();
        // --- *** END OF DATE FIX *** ---

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
        let currentSalesData = null; // Store the full data object
        
        function fetchSalesReport() {
            if (currentRequest) {
                currentRequest.abort();
            }
            
            // Use FormData to send data as POST
            const formData = new FormData();
            formData.append('dateFilter', dateFilter.value);
            formData.append('dateStart', dateStart.value);
            formData.append('dateEnd', dateEnd.value);
            formData.append('search', searchInput.value);

            salesTableBody.innerHTML = `<tr><td colspan="4" class="px-4 py-6 text-center text-gray-500"><i class="fas fa-spinner fa-spin text-xl"></i> Loading...</td></tr>`;
            // Also reset summary cards on load
            summaryTotalSales.textContent = '...';
            summaryTotalUnits.textContent = '...';
            summaryAOV.textContent = '...';

            currentRequest = new AbortController();
            const signal = currentRequest.signal;

            // Fetch from the current file (admin_salesreports.php) using POST
            fetch('<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>', { 
                method: 'POST',
                body: formData,
                signal: signal 
            })
            .then(res => res.json())
            .then(data => {
                currentRequest = null;
                
                if (!data.success) {
                    salesTableBody.innerHTML = `<tr><td colspan="4" class="px-4 py-6 text-center text-red-600">${data.error || 'Error loading data.'}</td></tr>`;
                    summaryTotalSales.textContent = '₱0.00';
                    summaryTotalUnits.textContent = '0';
                    summaryAOV.textContent = '₱0.00';
                    return;
                }
                
                currentSalesData = data; // Store the entire response
                
                // *** THIS IS WHERE THE METRICS ARE UPDATED ***
                summaryTotalSales.textContent = '₱' + parseFloat(data.totalSales).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                summaryTotalUnits.textContent = data.totalUnits;
                summaryAOV.textContent = '₱' + parseFloat(data.averageOrderValue).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                // *** END OF METRICS UPDATE ***

                if (!data.salesData || data.salesData.length === 0) {
                    salesTableBody.innerHTML = `<tr id="no-orders-row"><td colspan="4" class="px-4 py-6 text-center text-gray-500"><i class="fas fa-box-open text-3xl text-gray-300 mb-2"></i><p>No sales data found for the selected filters.</p></td></tr>`;
                } else {
                    salesTableBody.innerHTML = data.salesData.map(row => `
                        <tr class="hover:bg-blue-50 transition sales-row">
                            <td class="px-4 py-3 font-semibold text-gray-800">${escapeHTML(row.product_name)}</td>
                            <td class="px-4 py-3 text-blue-700 font-bold text-right">${row.total_units}</td>
                            <td class="px-4 py-3 text-green-700 font-bold text-right">₱${parseFloat(row.total_revenue).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                            <td class="px-4 py-3 text-gray-500">${row.last_sale_date ? (new Date(row.last_sale_date)).toLocaleDateString('en-US', {year:'numeric', month:'short', day:'numeric'}) : 'N/A'}</td>
                        </tr>
                    `).join('');
                }
                updateFilterText(dateFilter.value, dateStart.value, dateEnd.value, searchInput.value);
            })
            .catch(err => {
                if (err.name === 'AbortError') {
                    return; // Request was cancelled, do nothing
                }
                console.error('Fetch Error:', err); // Log error for debugging
                salesTableBody.innerHTML = `<tr><td colspan="4" class="px-4 py-6 text-center text-red-600">An error occurred while fetching data. Please check the console.</td></tr>`;
                summaryTotalSales.textContent = '₱0.00';
                summaryTotalUnits.textContent = '0';
                summaryAOV.textContent = '₱0.00';
                currentRequest = null;
            });
        }
        
        function escapeHTML(str) {
            if (typeof str !== 'string') return '';
            return str.replace(/[&<>"']/g, function(m) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                }[m];
            });
        }

        // Event listeners for filters
        // The date filter/picker listeners already call fetchSalesReport
        searchInput.addEventListener('input', function() {
            clearTimeout(searchDebounce);
            searchDebounce = setTimeout(fetchSalesReport, 400); // 400ms debounce
        });

        // Modal Logic
        const generateReportBtn = document.getElementById('generateReportBtn');
        const reportModal = document.getElementById('reportModal');
        const modalCloseBtn1 = document.getElementById('modalCloseBtn1');
        const modalCloseBtn2 = document.getElementById('modalCloseBtn2');
        const modalPrintBtn = document.getElementById('modalPrintBtn');
        const modalExportExcelBtn = document.getElementById('modalExportExcelBtn');
        
        const showReportModal = () => {
            if (!currentSalesData) {
                alert('Please wait for the data to load before generating a report.');
                return;
            }
            document.getElementById('modalTotalSales').textContent = summaryTotalSales.textContent;
            document.getElementById('modalTotalUnits').textContent = summaryTotalUnits.textContent;
            document.getElementById('modalAOV').textContent = summaryAOV.textContent;
            document.getElementById('modalSalesTableBody').innerHTML = salesTableBody.innerHTML;
            reportModal.classList.remove('hidden');
        };
        
        const hideReportModal = () => {
            reportModal.classList.add('hidden');
        };
        
        const printModal = () => {
            window.print();
        };

        // --- *** ENHANCED EXCEL EXPORT FUNCTION *** ---
        const exportToExcel = () => {
            if (!currentSalesData) {
                alert('No sales data available to export.');
                return;
            }

            const wb = XLSX.utils.book_new();

            // --- 2. Create Summary Sheet ---
            const summary_data = [
                ["Sales Report Summary"],
                [],
                ["Branch:", "<?php echo htmlspecialchars($branch_name); ?>"],
                ["Report Timeframe:", document.getElementById('modalFilterTimeframe').textContent],
                ["Generated On:", new Date().toLocaleString()],
                [],
                ["Metric", "Value"],
                ["Total Sales", { v: currentSalesData.totalSales, t: 'n', z: '₱#,##0.00' }],
                ["Total Units Sold", { v: currentSalesData.totalUnits, t: 'n' }],
                ["Average Order Value", { v: currentSalesData.averageOrderValue, t: 'n', z: '₱#,##0.00' }]
            ];
            
            const ws_summary = XLSX.utils.aoa_to_sheet(summary_data);

            ws_summary['!cols'] = [{ wch: 25 }, { wch: 25 }];
            ws_summary['A1'].s = { font: { bold: true, sz: 16 }, alignment: { horizontal: "center" } };
            ws_summary['A7'].s = { font: { bold: true }, fill: { fgColor: { rgb: "DDEEFF" } }, border: { bottom: { style: "thin" } } };
            ws_summary['B7'].s = { font: { bold: true }, fill: { fgColor: { rgb: "DDEEFF" } }, border: { bottom: { style: "thin" } } };
            ws_summary['A3'].s = { font: { bold: true } };
            ws_summary['A4'].s = { font: { bold: true } };
            ws_summary['A5'].s = { font: { bold: true } };
            ws_summary['A8'].s = { font: { bold: true } };
            ws_summary['A9'].s = { font: { bold: true } };
            ws_summary['A10'].s = { font: { bold: true } };
            ws_summary['!merges'] = [{ s: { r: 0, c: 0 }, e: { r: 0, c: 1 } }];
            
            XLSX.utils.book_append_sheet(wb, ws_summary, 'Summary');

            // --- 3. Create Product Details Sheet ---
            if (currentSalesData.salesData && currentSalesData.salesData.length > 0) {
                const product_data = currentSalesData.salesData.map(row => ({
                    "Product": row.product_name,
                    "Units Sold": { v: parseInt(row.total_units), t: 'n', z: '0' },
                    "Total Sales": { v: parseFloat(row.total_revenue), t: 'n', z: '₱#,##0.00' },
                    "Last Sale Date": row.last_sale_date ? new Date(row.last_sale_date) : null
                }));

                const ws_details = XLSX.utils.json_to_sheet(product_data, {
                    header: ["Product", "Units Sold", "Total Sales", "Last Sale Date"],
                    cellDates: true 
                });

                ws_details['!cols'] = [{ wch: 60 }, { wch: 15 }, { wch: 20 }, { wch: 20 }];
                ws_details['!autofilter'] = { ref: "A1:D1" };
                
                ['A1', 'B1', 'C1', 'D1'].forEach(cell => {
                    if (ws_details[cell]) {
                        ws_details[cell].s = {
                            font: { bold: true, color: { rgb: "FFFFFF" } },
                            fill: { fgColor: { rgb: "4F81BD" } },
                            alignment: { horizontal: "center" }
                        };
                    }
                });
                
                product_data.forEach((row, index) => {
                    const cellRef = 'D' + (index + 2); 
                    if (ws_details[cellRef] && ws_details[cellRef].v) {
                        ws_details[cellRef].t = 'd';
                        ws_details[cellRef].z = 'yyyy-mm-dd';
                    }
                });

                XLSX.utils.book_append_sheet(wb, ws_details, 'Product Details');
            }

            // --- 4. (Optional) Add Raw Data Sheet ---
            const ws_raw = XLSX.utils.json_to_sheet(currentSalesData.salesData);
            XLSX.utils.book_append_sheet(wb, ws_raw, 'Raw Data');
            if(wb.Sheets['Raw Data']) {
                wb.Sheets['Raw Data'].Hidden = 1;
            }

            // --- 5. Generate and Download File ---
            const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
            const filename = `SalesReport_<?php echo preg_replace("/[^a-zA-Z0-9]/", "_", $branch_name); ?>_${timestamp}.xlsx`;
            
            XLSX.writeFile(wb, filename);
        };
        // --- End of Excel function ---
        
        if (generateReportBtn) generateReportBtn.addEventListener('click', showReportModal);
        if (modalCloseBtn1) modalCloseBtn1.addEventListener('click', hideReportModal);
        if (modalCloseBtn2) modalCloseBtn2.addEventListener('click', hideReportModal);
        if (modalPrintBtn) modalPrintBtn.addEventListener('click', printModal);
        if (modalExportExcelBtn) modalExportExcelBtn.addEventListener('click', exportToExcel);

        // Initial data load
        fetchSalesReport();
    });
    </script>
</body>
</html>