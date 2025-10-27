<?php
session_start();
include '../includes/sidebar.php';
require_once '../connection/connection.php';

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
    <title>Admin Sales Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        body { display: flex; min-height: 100vh; }
        .main-content-wrapper { flex: 1; padding-left: 0; transition: padding-left 0.3s ease; }
        @media (min-width: 768px) { .main-content-wrapper { padding-left: 250px; } }

        /* Print styles */
        @media print {
            body > * {
                display: none !important;
            }
            #reportModal, #modalPrintArea {
                display: block !important;
            }
            #reportModal {
                position: relative;
                inset: 0;
                padding: 0;
                margin: 0;
                background: none;
                border: none;
                box-shadow: none;
                overflow: visible;
                width: 100%;
                max-width: 100%;
            }
            .modal-header, .modal-footer {
                display: none !important;
            }
            #modalPrintArea {
                max-width: 100%;
                margin: 0;
                padding: 1in;
                box-shadow: none;
                border: none;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 10pt;
            }
            th, td {
                border: 1px solid #999;
                padding: 8px;
                color: #000;
            }
            h1, h2, p, div {
                color: #000 !important;
            }
            .print-header {
                display: block !important; /* Ensure this is shown */
                text-align: center;
                margin-bottom: 2rem;
            }
            .print-header h1 {
                font-size: 24pt;
                font-weight: bold;
            }
            .print-header p {
                font-size: 12pt;
                color: #333 !important;
            }
            .print-summary {
                margin-bottom: 1.5rem;
                display: flex;
                justify-content: space-around;
            }
            .print-summary > div {
                border: 1px solid #ccc;
                padding: 1rem;
                border-radius: 8px;
                text-align: center;
            }
            .print-summary-label {
                font-size: 11pt;
                color: #333 !important;
                margin-bottom: 0.5rem;
            }
            .print-summary-value {
                font-size: 16pt;
                font-weight: bold;
                color: #000 !important;
            }
            .no-print-in-modal {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    
    <div class="main-content-wrapper">
        <main class="min-h-screen">
            <div class="max-w-7xl mx-auto py-8 px-4">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-line mr-3 text-blue-600"></i>Sales Report
                    </h1>
                    <p class="text-lg text-gray-600 mt-2">
                        Viewing completed sales data for <span class="font-bold text-blue-700"><?php echo htmlspecialchars($branch_name); ?></span>
                    </p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-yellow-500 flex items-center">
                        <div class="rounded-full bg-yellow-100 p-5 mr-5">
                            <i class="fas fa-coins text-yellow-500 text-3xl"></i>
                        </div>
                        <div>
                            <div class="text-gray-600 text-base font-semibold">Total Sales</div>
                            <div class="font-bold text-3xl text-green-700" id="summaryTotalSales">₱0.00</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500 flex items-center">
                        <div class="rounded-full bg-blue-100 p-5 mr-5">
                            <i class="fas fa-cubes text-blue-500 text-3xl"></i>
                        </div>
                        <div>
                            <div class="text-gray-600 text-base font-semibold">Total Units Sold</div>
                            <div class="font-bold text-3xl text-blue-700" id="summaryTotalUnits">0</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-purple-500 flex items-center">
                        <div class="rounded-full bg-purple-100 p-5 mr-5">
                            <i class="fas fa-file-invoice-dollar text-purple-500 text-3xl"></i>
                        </div>
                        <div>
                            <div class="text-gray-600 text-base font-semibold">Avg. Order Value</div>
                            <div class="font-bold text-3xl text-purple-700" id="summaryAOV">₱0.00</div>
                        </div>
                    </div>
                </div>

                <form id="filterForm" class="bg-white rounded-xl shadow-sm p-6 mb-8 no-print" autocomplete="off">
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
                            <button type="button" id="generateReportBtn" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700 transition">
                                <i class="fas fa-file-alt mr-2"></i>Generate Report
                            </button>
                        </div>
                    </div>
                    <div id="filterText" class="mt-4 text-blue-700 font-semibold text-lg"></div>
                </form>

                <div class="bg-white rounded-xl shadow p-6 overflow-x-auto print-table">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
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
        </main>
    </div>

    <div id="reportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[2000] hidden" style="z-index:2000;">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] flex flex-col" style="z-index:2100; margin:auto;">
            <div class="modal-header flex justify-between items-center p-5 border-b">
                <h2 class="text-2xl font-bold text-gray-800">Sales Report</h2>
                <button id="modalCloseBtn1" class="text-gray-400 hover:text-gray-600 text-3xl">&times;</button>
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
                <div class="overflow-x-auto rounded-lg border">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
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
                    * Total Sales in summary card includes shipping fees (if any).
                    * Total Sales in the table represents product revenue only.
                </p>

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
        const today = new Date().toISOString().split('T')[0];

        // Date Filter Logic
        function setupDateInputs() {
            dateStart.max = today;
            dateEnd.max = today;
            dateFilter.addEventListener('change', updateDateInputs);
            updateDateInputs(); // Set initial state
        }
        
        function updateDateInputs() {
            if (dateFilter.value === 'custom') {
                dateStart.style.display = '';
                dateSeparator.style.display = '';
                dateEnd.style.display = '';
            } else {
                dateStart.style.display = 'none';
                dateSeparator.style.display = 'none';
                dateEnd.style.display = 'none';
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
        
        function fetchSalesReport() {
            // Cancel previous request if still pending
            if (currentRequest) {
                currentRequest.abort();
            }
            
            const params = new URLSearchParams();
            params.append('dateFilter', dateFilter.value);
            params.append('dateStart', dateStart.value);
            params.append('dateEnd', dateEnd.value);
            params.append('search', searchInput.value);

            // Show loading state
            salesTableBody.innerHTML = `<tr><td colspan="4" class="px-4 py-6 text-center text-gray-500"><i class="fas fa-spinner fa-spin text-xl"></i> Loading...</td></tr>`;

            // Create new request
            currentRequest = new AbortController();
            const signal = currentRequest.signal;

            fetch('ajax_sales_report.php?' + params.toString(), { signal })
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
                    
                    // Store current sales data for Excel export
                    currentSalesData = data;
                    
                    // Update summary cards
                    summaryTotalSales.textContent = '₱' + parseFloat(data.totalSales).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                    summaryTotalUnits.textContent = data.totalUnits;
                    summaryAOV.textContent = '₱' + parseFloat(data.averageOrderValue).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});

                    // Update table
                    if (!data.salesData || data.salesData.length === 0) {
                        salesTableBody.innerHTML = `<tr id="no-orders-row"><td colspan="4" class="px-4 py-6 text-center text-gray-500"><i class="fas fa-search text-3xl text-gray-300 mb-2"></i><p>No sales data found for the selected filters.</p></td></tr>`;
                    } else {
                        salesTableBody.innerHTML = data.salesData.map(row => `
                            <tr class="hover:bg-blue-50 transition sales-row">
                                <td class="px-4 py-3 font-semibold text-gray-800">${escapeHTML(row.product_name)}</td>
                                <td class="px-4 py-3 text-blue-700 font-bold text-right">${row.total_units}</td>
                                <td class="px-4 py-3 text-green-700 font-bold text-right">₱${parseFloat(row.total_revenue).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                                <td class="px-4 py-3 text-gray-500">${row.last_sale_date ? (new Date(row.last_sale_date)).toLocaleDateString('en-US', {year:'numeric', month:'short', day:'numeric'}) : ''}</td>
                            </tr>
                        `).join('');
                    }
                    updateFilterText(dateFilter.value, dateStart.value, dateEnd.value, searchInput.value);
                })
                .catch(err => {
                    if (err.name === 'AbortError') {
                        // Request was cancelled, do nothing
                        return;
                    }
                    salesTableBody.innerHTML = `<tr><td colspan="4" class="px-4 py-6 text-center text-red-600">An error occurred. Please try again.</td></tr>`;
                    summaryTotalSales.textContent = '₱0.00';
                    summaryTotalUnits.textContent = '0';
                    summaryAOV.textContent = '₱0.00';
                    currentRequest = null;
                });
        }
        
        function escapeHTML(str) {
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
        dateFilter.addEventListener('change', fetchSalesReport);
        dateStart.addEventListener('change', fetchSalesReport);
        dateEnd.addEventListener('change', fetchSalesReport);
        
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

        const exportToExcel = () => {
            if (!currentSalesData) {
                alert('No sales data available to export.');
                return;
            }

            // Create workbook
            const wb = XLSX.utils.book_new();
            
            // Add summary sheet
            const summaryData = [
                ['Sales Report Summary'],
                [''],
                ['Branch:', '<?php echo htmlspecialchars($branch_name); ?>'],
                ['Report Timeframe:', document.getElementById('modalFilterTimeframe').textContent],
                ['Generated:', new Date().toLocaleString()],
                [''],
                ['SUMMARY'],
                ['Total Sales:', currentSalesData.totalSales],
                ['Total Units Sold:', currentSalesData.totalUnits],
                ['Average Order Value:', currentSalesData.averageOrderValue],
                [''],
                ['DETAILED PRODUCT SALES'],
                ['Product', 'Units Sold', 'Total Sales', 'Last Sale Date']
            ];
            
            // Add product sales data
            if (currentSalesData.salesData && currentSalesData.salesData.length > 0) {
                currentSalesData.salesData.forEach(row => {
                    summaryData.push([
                        row.product_name,
                        row.total_units,
                        row.total_revenue,
                        row.last_sale_date ? new Date(row.last_sale_date).toLocaleDateString() : ''
                    ]);
                });
            }
            
            const summarySheet = XLSX.utils.aoa_to_sheet(summaryData);
            XLSX.utils.book_append_sheet(wb, summarySheet, 'Sales Summary');
            
            // Add detailed data sheet
            if (currentSalesData.salesData && currentSalesData.salesData.length > 0) {
                const detailedData = [
                    ['Product', 'Units Sold', 'Total Sales', 'Last Sale Date']
                ];
                
                currentSalesData.salesData.forEach(row => {
                    detailedData.push([
                        row.product_name,
                        row.total_units,
                        row.total_revenue,
                        row.last_sale_date ? new Date(row.last_sale_date).toLocaleDateString() : ''
                    ]);
                });
                
                const detailedSheet = XLSX.utils.aoa_to_sheet(detailedData);
                XLSX.utils.book_append_sheet(wb, detailedSheet, 'Product Details');
            }
            
            // Generate filename with timestamp
            const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
            const filename = `Sales_Report_${timestamp}.xlsx`;
            
            // Export the workbook
            XLSX.writeFile(wb, filename);
        };
        
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