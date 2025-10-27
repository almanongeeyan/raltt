<?php
session_start();
include '../includes/sidebar.php';
require_once '../connection/connection.php';

// --- 1. GET SESSION & FILTER PARAMETERS ---
$session_branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;

// Get filter values from URL (or set defaults)
$filter_search = $_GET['search'] ?? '';
$filter_remarks = $_GET['remarks'] ?? '';
$filter_date = $_GET['dateFilter'] ?? '';
$filter_start = $_GET['dateStart'] ?? '';
$filter_end = $_GET['dateEnd'] ?? '';

// --- 2. INITIALIZE VARIABLES ---
$inventoryData = [];
$error_message = '';
$branch_name_display = isset($_SESSION['branch_name']) ? $_SESSION['branch_name'] : 'Branch';

// --- 3. BUILD DYNAMIC SQL QUERY ---
$params = [];

// --- Inventory Report Query using sales data ---
$sql = "SELECT 
    p.product_name,
    100 AS last_restock_quantity,
    SUM(oi.quantity) AS total_sold,
    MAX(o.order_date) AS last_update,
    (SUM(oi.quantity) / 100) * 100 AS percent_taken,
    (CASE
        WHEN SUM(oi.quantity) >= 95 THEN 'Low Stock'
        WHEN SUM(oi.quantity) = 0 THEN 'No Stock'
        ELSE 'Sufficient'
    END) AS remarks
FROM order_items oi
JOIN orders o ON oi.order_id = o.order_id
JOIN products p ON oi.product_id = p.product_id
WHERE o.order_status = 'completed'";

// Branch filter
if ($session_branch_id) {
    $sql .= " AND o.branch_id = ?";
    $params[] = $session_branch_id;
}

// Search filter
if (!empty($filter_search)) {
    $sql .= " AND p.product_name LIKE ?";
    $params[] = "%$filter_search%";
}

// Date filter
switch ($filter_date) {
    case 'today':
        $sql .= " AND DATE(o.order_date) = CURDATE()";
        break;
    case 'week':
        $sql .= " AND YEARWEEK(o.order_date, 0) = YEARWEEK(NOW(), 0)";
        break;
    case 'month':
        $sql .= " AND YEAR(o.order_date) = YEAR(NOW()) AND MONTH(o.order_date) = MONTH(NOW())";
        break;
    case 'custom':
        if (!empty($filter_start) && !empty($filter_end)) {
            $sql .= " AND DATE(o.order_date) BETWEEN ? AND ?";
            $params[] = $filter_start;
            $params[] = $filter_end;
        }
        break;
}

$sql .= " GROUP BY p.product_id";

// Remarks filter (HAVING clause after GROUP BY)
if (!empty($filter_remarks)) {
    $sql .= " HAVING remarks = ?";
    $params[] = $filter_remarks;
}

$sql .= " ORDER BY p.product_name";

try {
    $stmt = $db_connection->prepare($sql);
    $stmt->execute($params);
    $inventoryData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $error_message = "Unable to load inventory report. Please try again later.";
}

// --- Helper function for Remarks Badge ---
function getRemarksBadge($remarks) {
    switch ($remarks) {
        case 'Overstock':
            return '<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">Overstock</span>';
        case 'Sufficient':
            return '<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Sufficient</span>';
        case 'Low Stock':
            return '<span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">Low Stock</span>';
        case 'No Stock':
            return '<span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-semibold">No Stock</span>';
        default:
            return '<span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold">' . htmlspecialchars($remarks) . '</span>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Inventory Reports Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        /* Sidebar integration */
        body { display: flex; min-height: 100vh; }
        .main-content-wrapper { flex: 1; padding-left: 0; transition: padding-left 0.3s ease; }
        @media (min-width: 768px) { .main-content-wrapper { padding-left: 250px; } }

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
                max-width: 100%; margin: 0; padding: 1in 0.5in 0.5in 0.5in;
                box-shadow: none; border: none;
                background: #fff !important;
            }
            .print-header {
                text-align: left;
                margin-bottom: 2rem;
                border-bottom: 2px solid #222;
                padding-bottom: 1rem;
            }
            .print-header-title {
                font-size: 2rem;
                font-weight: bold;
                color: #222;
                margin-bottom: 0.25rem;
            }
            .print-header-details {
                font-size: 1rem;
                color: #444;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 11pt;
                margin-top: 1rem;
            }
            th, td {
                border: 1px solid #222;
                padding: 10px 8px;
                color: #222;
                background: #fff !important;
            }
            th {
                background: #f0f0f0 !important;
                font-weight: bold;
                font-size: 12pt;
            }
            tr {
                page-break-inside: avoid;
            }
            h1, h2, p, div { color: #222 !important; }
            .badge-print-text { display: inline !important; }
            .badge-print-hide { display: none !important; }
        }
        .badge-print-text { display: none; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="main-content-wrapper">
        <main class="min-h-screen">
            <div class="max-w-7xl mx-auto py-8 px-4">
                
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-boxes-stacked mr-3 text-blue-600"></i>Inventory Reports Dashboard
                    </h1>
                    <p class="text-lg text-gray-600 mt-2">
                        Viewing inventory for <span class="font-bold text-blue-700"><?php echo htmlspecialchars($branch_name_display); ?></span>
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 mb-8 no-print">
                    <form id="filterForm" autocomplete="off">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex flex-1 gap-2 flex-wrap">
                                <div class="relative flex-1 min-w-[180px]">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                    <input type="text" name="search" id="searchInput" placeholder="Search product name..." class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" value="<?php echo htmlspecialchars($filter_search); ?>" />
                                </div>
                                
                                <select name="remarks" id="remarksSelect" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none min-w-[150px]">
                                    <option value="">All Remarks</option>
                                    <option value="Overstock" <?php echo ($filter_remarks == 'Overstock') ? 'selected' : ''; ?>>Overstock</option>
                                    <option value="Sufficient" <?php echo ($filter_remarks == 'Sufficient') ? 'selected' : ''; ?>>Sufficient</option>
                                    <option value="Low Stock" <?php echo ($filter_remarks == 'Low Stock') ? 'selected' : ''; ?>>Low Stock</option>
                                    <option value="No Stock" <?php echo ($filter_remarks == 'No Stock') ? 'selected' : ''; ?>>No Stock</option>
                                </select>
                            </div>
                            <div class="flex gap-2 items-center flex-wrap">
                                <select name="dateFilter" id="dateFilter" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none min-w-[130px]">
                                    <option value="" <?php echo ($filter_date == '') ? 'selected' : ''; ?>>All Time</option>
                                    <option value="today" <?php echo ($filter_date == 'today') ? 'selected' : ''; ?>>Today</option>
                                    <option value="week" <?php echo ($filter_date == 'week') ? 'selected' : ''; ?>>This Week</option>
                                    <option value="month" <?php echo ($filter_date == 'month') ? 'selected' : ''; ?>>This Month</option>
                                    <option value="custom" <?php echo ($filter_date == 'custom') ? 'selected' : ''; ?>>Custom Range</option>
                                </select>
                                <input type="date" name="dateStart" id="dateStart" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" value="<?php echo htmlspecialchars($filter_start); ?>" />
                                <span id="dateSeparator" class="mx-1 text-gray-400">-</span>
                                <input type="date" name="dateEnd" id="dateEnd" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" value="<?php echo htmlspecialchars($filter_end); ?>" />
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center justify-between mt-4">
                             <div id="filterText" class="text-blue-700 font-semibold text-lg mb-2 md:mb-0"></div>
                             <div class="flex gap-2">
                                <button type="button" id="generateReportBtn" class="w-full md:w-auto px-5 py-2.5 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700 transition">
                                    <i class="fas fa-file-alt mr-2"></i>Generate Report
                                </button>
                             </div>
                        </div>
                    </form>
                </div>
                
                <div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
                    <?php if (!empty($error_message)): ?>
                        <div class="text-center p-6 bg-red-50 text-red-700 rounded-lg"><?php echo $error_message; ?></div>
                    <?php else: ?>
                        <table class="min-w-full divide-y divide-gray-200" id="inventoryTable">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Last Restock Qty</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sold</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">% Taken</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Update</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100" id="inventoryTableBody">
                                <?php if (empty($inventoryData)): ?>
                                    <tr id="no-orders-row">
                                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                            <i class="fas fa-box-open text-3xl text-gray-300 mb-2"></i>
                                            <p>No inventory data found for the selected filters.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($inventoryData as $row): ?>
                                        <tr class="hover:bg-blue-50 transition">
                                            <td class="px-4 py-3 font-semibold text-gray-700"><?php echo htmlspecialchars($row['product_name']); ?></td>
                                            <td class="px-4 py-3 text-gray-700 text-right"><?php echo (int)$row['last_restock_quantity']; ?></td>
                                            <td class="px-4 py-3 text-blue-700 font-bold text-right"><?php echo (int)$row['total_sold']; ?></td>
                                            <td class="px-4 py-3 <?php echo $row['percent_taken'] >= 95 ? 'text-red-700' : 'text-green-700'; ?> font-semibold text-right"><?php echo number_format($row['percent_taken'], 0); ?>%</td>
                                            <td class="px-4 py-3 text-gray-700"><?php echo htmlspecialchars(date('M j, Y', strtotime($row['last_update']))); ?></td>
                                            <td class="px-4 py-3">
                                                <span class="badge-print-hide"><?php echo getRemarksBadge($row['remarks']); ?></span>
                                                <span class="badge-print-text"><?php echo htmlspecialchars($row['remarks']); ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                <div class="mt-6 text-xs text-gray-500 no-print">
                    <div class="mb-1">Remarks: <span class="font-semibold">Low Stock</span> (>= 95% taken), <span class="font-semibold">No Stock</span> (0 units), <span class="font-semibold">Sufficient</span> (all others)</div>
                </div>
            </div>
        </main>
    </div>

    <div id="reportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[2000] hidden" style="z-index:2000;">
        <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full max-h-[90vh] flex flex-col" style="z-index:2100; margin:auto;">
            <div class="modal-header flex justify-between items-center p-5 border-b">
                <h2 class="text-2xl font-bold text-gray-800">Inventory Report</h2>
                <button id="modalCloseBtn1" class="text-gray-400 hover:text-gray-600 text-3xl">&times;</button>
            </div>
            
            <div id="modalPrintArea" class="p-8 overflow-y-auto">
                <div class="print-header hidden">
                    <h1 class="print-header-title">Inventory Report</h1>
                    <p class="print-header-details">Branch: <span id="modalBranchName"><?php echo htmlspecialchars($branch_name_display); ?></span></p>
                    <p class="print-header-details">Report for: <span id="modalFilterTimeframe"></span></p>
                </div>
                
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Detailed Inventory Status</h3>
                <div class="overflow-x-auto rounded-lg border">
                    <table class="min-w-full divide-y divide-gray-200" id="modalTable">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            
            <div class="modal-footer flex justify-end gap-3 p-5 border-t bg-gray-50 rounded-b-xl">
                <button id="modalCloseBtn2" class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg shadow-sm hover:bg-gray-300 transition">Close</button>
                <button id="modalExportExcelBtn" class="px-5 py-2.5 bg-green-600 text-white rounded-lg shadow-sm hover:bg-green-700 transition">
                    <i class="fas fa-file-excel mr-2"></i>Export to Excel
                </button>
                <button id="modalPrintBtn" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700 transition">
                    <i class="fas fa-print mr-2"></i>Print Report
                </button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- DOM Elements ---
        const filterForm = document.getElementById('filterForm');
        const filterText = document.getElementById('filterText');
        const dateFilter = document.getElementById('dateFilter');
        const dateStart = document.getElementById('dateStart');
        const dateEnd = document.getElementById('dateEnd');
        const dateSeparator = document.getElementById('dateSeparator');
        const searchInput = document.getElementById('searchInput');
        const remarksSelect = document.getElementById('remarksSelect');
        const inventoryTableBody = document.getElementById('inventoryTableBody');
        
        const today = new Date().toISOString().split('T')[0];
        let currentRequest = null;
        let debounceTimer;
        let currentInventoryData = <?php echo json_encode($inventoryData); ?>;

        // --- Debounce Utility ---
        function debounce(func, delay) {
            return function(...args) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    func.apply(this, args);
                }, delay);
            };
        }

        // --- Date Filter Logic ---
        function setupDateInputs() {
            dateStart.max = today;
            dateEnd.max = today;

            // Set min/max constraints
            dateStart.addEventListener('input', function() {
                if (dateStart.value > today) dateStart.value = today;
                dateEnd.min = dateStart.value;
            });
            dateEnd.addEventListener('input', function() {
                if (dateEnd.value > today) dateEnd.value = today;
                dateStart.max = dateEnd.value || today;
            });

            updateDateInputs();
            updateFilterText();
        }

        function updateDateInputs() {
            const isCustom = dateFilter.value === 'custom';
            dateStart.style.display = isCustom ? '' : 'none';
            dateSeparator.style.display = isCustom ? '' : 'none';
            dateEnd.style.display = isCustom ? '' : 'none';
            
            dateStart.required = isCustom;
            dateEnd.required = isCustom;
        }

        // --- Filter Text Update ---
        function updateFilterText() {
            let text = '';
            const filterVal = dateFilter.value;
            const startVal = dateStart.value;
            const endVal = dateEnd.value;
            const searchVal = searchInput.value;
            const remarksVal = remarksSelect.value;

            if (filterVal === 'today') text = 'Updates for today';
            else if (filterVal === 'week') text = 'Updates for this week';
            else if (filterVal === 'month') text = 'Updates for this month';
            else if (filterVal === 'custom' && startVal && endVal) {
                text = `Updates from ${startVal} to ${endVal}`;
            } else if (filterVal === 'custom') {
                text = 'Updates for custom range (please select dates)';
            } else {
                text = 'All inventory updates';
            }

            if (remarksVal) {
                text += ` | Status: "${remarksVal}"`;
            }
            if (searchVal) {
                text += ` | Searching for "${searchVal}"`;
            }
            
            filterText.textContent = `Showing: ${text}`;
            document.getElementById('modalFilterTimeframe').textContent = text;
        }

        // --- AJAX Filtering ---
        function fetchInventoryReport() {
            if (dateFilter.value === 'custom' && (!dateStart.value || !dateEnd.value)) {
                inventoryTableBody.innerHTML = `<tr><td colspan="6" class="px-4 py-6 text-center text-blue-600">Please select a start and end date for the custom range.</td></tr>`;
                return;
            }

            if (currentRequest) {
                currentRequest.abort();
            }
            
            const params = new URLSearchParams();
            params.append('dateFilter', dateFilter.value);
            params.append('dateStart', dateStart.value);
            params.append('dateEnd', dateEnd.value);
            params.append('search', searchInput.value);
            params.append('remarks', remarksSelect.value);

            inventoryTableBody.innerHTML = `<tr><td colspan="6" class="px-4 py-6 text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Loading...</td></tr>`;

            currentRequest = new AbortController();
            const signal = currentRequest.signal;

            fetch('ajax_inventory_report.php?' + params.toString(), { signal })
                .then(res => res.json())
                .then(data => {
                    currentRequest = null;
                    
                    if (!data.success) {
                        inventoryTableBody.innerHTML = `<tr><td colspan="6" class="px-4 py-6 text-center text-red-600">${data.error || 'Error loading data.'}</td></tr>`;
                        currentInventoryData = [];
                        return;
                    }
                    
                    currentInventoryData = data.inventoryData || [];
                    
                    if (currentInventoryData.length === 0) {
                        inventoryTableBody.innerHTML = `<tr id="no-orders-row"><td colspan="6" class="px-4 py-6 text-center text-gray-500"><i class="fas fa-box-open text-3xl text-gray-300 mb-2"></i><p>No inventory data found for the selected filters.</p></td></tr>`;
                    } else {
                        inventoryTableBody.innerHTML = currentInventoryData.map(row => `
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-3 font-semibold text-gray-700">${escapeHTML(row.product_name)}</td>
                                <td class="px-4 py-3 text-gray-700 text-right">${parseInt(row.last_restock_quantity)}</td>
                                <td class="px-4 py-3 text-blue-700 font-bold text-right">${parseInt(row.total_sold)}</td>
                                <td class="px-4 py-3 ${row.percent_taken >= 95 ? 'text-red-700' : 'text-green-700'} font-semibold text-right">${parseFloat(row.percent_taken).toFixed(0)}%</td>
                                <td class="px-4 py-3 text-gray-700">${row.last_update ? (new Date(row.last_update)).toLocaleDateString(undefined, {year:'numeric', month:'short', day:'numeric'}) : ''}</td>
                                <td class="px-4 py-3">
                                    <span class="badge-print-hide">${getRemarksBadge(row.remarks)}</span>
                                    <span class="badge-print-text">${row.remarks}</span>
                                </td>
                            </tr>
                        `).join('');
                    }
                })
                .catch(err => {
                    if (err.name === 'AbortError') return;
                    inventoryTableBody.innerHTML = `<tr><td colspan="6" class="px-4 py-6 text-center text-red-600">An error occurred while fetching data.</td></tr>`;
                    currentRequest = null;
                    currentInventoryData = [];
                });
        }

        function escapeHTML(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function getRemarksBadge(remarks) {
            switch (remarks) {
                case 'Overstock':
                    return '<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">Overstock</span>';
                case 'Sufficient':
                    return '<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Sufficient</span>';
                case 'Low Stock':
                    return '<span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">Low Stock</span>';
                case 'No Stock':
                    return '<span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-semibold">No Stock</span>';
                default:
                    return '<span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold">' + remarks + '</span>';
            }
        }

        // **SMOOTH FILTERING:** Create a single debounced function
        const debouncedFetch = debounce(fetchInventoryReport, 400);

        // This function runs on EVERY filter change
        function handleFilterChange() {
            updateFilterText();
            debouncedFetch();
        }

        // --- Event listeners for filters ---
        dateFilter.addEventListener('change', function() {
            updateDateInputs();
            handleFilterChange();
        });
        
        dateStart.addEventListener('change', handleFilterChange);
        dateEnd.addEventListener('change', handleFilterChange);
        searchInput.addEventListener('input', handleFilterChange);
        remarksSelect.addEventListener('change', handleFilterChange);
        
        // Initial page setup
        setupDateInputs();

        // --- Modal Logic ---
        const generateReportBtn = document.getElementById('generateReportBtn');
        const reportModal = document.getElementById('reportModal');
        const modalCloseBtn1 = document.getElementById('modalCloseBtn1');
        const modalCloseBtn2 = document.getElementById('modalCloseBtn2');
        const modalPrintBtn = document.getElementById('modalPrintBtn');
        const modalExportExcelBtn = document.getElementById('modalExportExcelBtn');
        
        const mainTable = document.getElementById('inventoryTable');
        const modalTable = document.getElementById('modalTable');

        const showReportModal = () => {
            if (mainTable && modalTable) {
                const mainThead = mainTable.querySelector('thead').innerHTML;
                const mainTbody = mainTable.querySelector('tbody').innerHTML;
                
                modalTable.querySelector('thead').innerHTML = mainThead;
                modalTable.querySelector('tbody').innerHTML = mainTbody;
            }
            
            reportModal.classList.remove('hidden');
        };

        const hideReportModal = () => {
            reportModal.classList.add('hidden');
        };

        const printModal = () => {
            document.querySelectorAll('.print-header').forEach(el => el.style.display = 'block');
            window.print();
            document.querySelectorAll('.print-header').forEach(el => el.style.display = 'none');
        };

        const exportToExcel = () => {
            if (!currentInventoryData || currentInventoryData.length === 0) {
                alert('No inventory data available to export.');
                return;
            }

            // Create workbook
            const wb = XLSX.utils.book_new();
            
            // Calculate statistics
            const lowStockCount = currentInventoryData.filter(item => item.remarks === 'Low Stock').length;
            const noStockCount = currentInventoryData.filter(item => item.remarks === 'No Stock').length;
            const sufficientCount = currentInventoryData.filter(item => item.remarks === 'Sufficient').length;
            const overstockCount = currentInventoryData.filter(item => item.remarks === 'Overstock').length;

            // Add summary sheet
            const summaryData = [
                ['Inventory Report Summary'],
                [''],
                ['Branch:', '<?php echo htmlspecialchars($branch_name_display); ?>'],
                ['Report Timeframe:', document.getElementById('modalFilterTimeframe').textContent],
                ['Generated:', new Date().toLocaleString()],
                [''],
                ['INVENTORY STATUS SUMMARY'],
                ['Total Products:', currentInventoryData.length],
                ['Low Stock Items:', lowStockCount],
                ['No Stock Items:', noStockCount],
                ['Sufficient Stock Items:', sufficientCount],
                ['Overstock Items:', overstockCount],
                [''],
                ['DETAILED INVENTORY DATA'],
                ['Product', 'Last Restock Qty', 'Total Sold', '% Taken', 'Last Update', 'Remarks']
            ];
            
            // Add inventory data to summary sheet
            currentInventoryData.forEach(row => {
                summaryData.push([
                    row.product_name,
                    parseInt(row.last_restock_quantity),
                    parseInt(row.total_sold),
                    parseFloat(row.percent_taken).toFixed(0) + '%',
                    row.last_update ? new Date(row.last_update).toLocaleDateString() : '',
                    row.remarks
                ]);
            });
            
            const summarySheet = XLSX.utils.aoa_to_sheet(summaryData);
            XLSX.utils.book_append_sheet(wb, summarySheet, 'Inventory Summary');
            
            // Add detailed data sheet
            const detailedData = [
                ['Product', 'Last Restock Qty', 'Total Sold', '% Taken', 'Last Update', 'Remarks']
            ];
            
            currentInventoryData.forEach(row => {
                detailedData.push([
                    row.product_name,
                    parseInt(row.last_restock_quantity),
                    parseInt(row.total_sold),
                    parseFloat(row.percent_taken).toFixed(0) + '%',
                    row.last_update ? new Date(row.last_update).toLocaleDateString() : '',
                    row.remarks
                ]);
            });
            
            const detailedSheet = XLSX.utils.aoa_to_sheet(detailedData);
            XLSX.utils.book_append_sheet(wb, detailedSheet, 'Inventory Details');
            
            // Generate filename with timestamp
            const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
            const filename = `Inventory_Report_${timestamp}.xlsx`;
            
            // Export the workbook
            XLSX.writeFile(wb, filename);
        };

        // --- Event Listeners ---
        generateReportBtn.addEventListener('click', showReportModal);
        modalCloseBtn1.addEventListener('click', hideReportModal);
        modalCloseBtn2.addEventListener('click', hideReportModal);
        modalPrintBtn.addEventListener('click', printModal);
        modalExportExcelBtn.addEventListener('click', exportToExcel);
    });
    </script>
</body>
</html>