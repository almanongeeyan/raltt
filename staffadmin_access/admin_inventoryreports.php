<?php
session_start();
include '../includes/sidebar.php';

// Get branch name for display
$branch_name_display = isset($_SESSION['branch_name']) ? $_SESSION['branch_name'] : 'Branch';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Inventory Reports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 80px;
            --transition-speed: 0.3s;
        }
        body { display: flex; min-height: 100vh; background-color: #f8fafc; }
        .main-content-wrapper {
            flex: 1; margin-left: var(--sidebar-width); transition: margin-left var(--transition-speed);
            padding: 2rem; width: calc(100% - var(--sidebar-width));
        }
        html.sidebar-collapsed .main-content-wrapper {
            margin-left: var(--sidebar-collapsed-width); width: calc(100% - var(--sidebar-collapsed-width));
        }
        @media (max-width: 768px) {
            .main-content-wrapper { margin-left: 0; padding: 1rem; width: 100%; }
        }
        .summary-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .summary-card:hover { transform: translateY(-4px); box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); }
        .table-header-sticky th { position: sticky; top: 0; background-color: #f9fafb; z-index: 10; }
        
        /* Print & Modal Styles */
        @media print {
            body > * { display: none !important; }
            #reportModal, #modalPrintArea { display: block !important; position: relative !important; inset: 0 !important; width: 100% !important; height: auto !important; background: white !important; }
            .modal-header, .modal-footer, .no-print, .no-print-in-modal { display: none !important; }
            #modalPrintArea { padding: 0.5in !important; margin: 0 !important; border: none !important; box-shadow: none !important; }
            .print-header { display: block !important; text-align: center !important; margin-bottom: 2rem !important; border-bottom: 2px solid #333 !important; padding-bottom: 1rem !important; }
            table { width: 100% !important; border-collapse: collapse !important; font-size: 10pt !important; }
            th, td { border: 1px solid #333 !important; padding: 6px !important; color: #000 !important; }
            .badge-print-hide { display: none !important; }
            .badge-print-text { display: inline !important; }
        }
        .badge-print-text { display: none; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    
    <div class="main-content-wrapper">
        <main>
            <div class="max-w-7xl mx-auto">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-boxes-stacked mr-3 text-blue-600"></i>Inventory Report
                    </h1>
                    <p class="text-lg text-gray-600 mt-2">
                        Viewing inventory status for <span class="font-bold text-blue-700"><?php echo htmlspecialchars($branch_name_display); ?></span>
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center summary-card border-l-4 border-blue-500">
                        <div class="rounded-full bg-blue-50 p-4 mr-4"><i class="fas fa-cube text-blue-500 text-2xl"></i></div>
                        <div>
                            <div class="text-sm font-bold text-gray-500 uppercase tracking-wider">Total Items</div>
                            <div class="text-3xl font-extrabold text-gray-900 mt-1" id="metricTotal">0</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center summary-card border-l-4 border-green-500">
                        <div class="rounded-full bg-green-50 p-4 mr-4"><i class="fas fa-check-circle text-green-500 text-2xl"></i></div>
                        <div>
                            <div class="text-sm font-bold text-gray-500 uppercase tracking-wider">Sufficient</div>
                            <div class="text-3xl font-extrabold text-gray-900 mt-1" id="metricSufficient">0</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center summary-card border-l-4 border-yellow-500">
                        <div class="rounded-full bg-yellow-50 p-4 mr-4"><i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i></div>
                        <div>
                            <div class="text-sm font-bold text-gray-500 uppercase tracking-wider">Low Stock</div>
                            <div class="text-3xl font-extrabold text-gray-900 mt-1" id="metricLow">0</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center summary-card border-l-4 border-red-500">
                        <div class="rounded-full bg-red-50 p-4 mr-4"><i class="fas fa-times-circle text-red-500 text-2xl"></i></div>
                        <div>
                            <div class="text-sm font-bold text-gray-500 uppercase tracking-wider">No Stock</div>
                            <div class="text-3xl font-extrabold text-gray-900 mt-1" id="metricNo">0</div>
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
                                <input type="text" id="searchInput" placeholder="Product name..." 
                                       class="pl-10 w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block transition-colors" />
                            </div>
                        </div>

                        <div class="xl:col-span-3">
                            <label for="remarksSelect" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-filter text-gray-400"></i>
                                </div>
                                <select id="remarksSelect" class="pl-10 w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block transition-colors appearance-none">
                                    <option value="">All Statuses</option>
                                    <option value="Sufficient">Sufficient Stock</option>
                                    <option value="Low Stock">Low Stock</option>
                                    <option value="No Stock">No Stock</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <div class="xl:col-span-3">
                            <label for="dateFilter" class="block text-sm font-medium text-gray-700 mb-2">Timeframe</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="far fa-calendar-alt text-gray-400"></i>
                                </div>
                                <select id="dateFilter" class="pl-10 w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block transition-colors appearance-none">
                                    <option value="">All Time</option>
                                    <option value="today">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                    <option value="custom">Custom Range</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <div class="xl:col-span-2 flex items-end">
                            <button type="button" id="generateReportBtn" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow transition-colors duration-200 focus:ring-4 focus:ring-blue-300 flex items-center justify-center">
                                <i class="fas fa-sync-alt mr-2"></i> Refresh Data
                            </button>
                        </div>
                    </div>

                    <div id="customDateRow" class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6 pt-6 border-t border-gray-100 hidden">
                         <div>
                            <label for="dateStart" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" id="dateStart" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block transition-colors" />
                        </div>
                        <div>
                            <label for="dateEnd" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" id="dateEnd" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block transition-colors" />
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div id="filterText" class="text-blue-800 bg-blue-50 px-3 py-1.5 rounded-full text-sm font-medium flex items-center">
                             <i class="fas fa-info-circle mr-2"></i><span>Showing all inventory</span>
                        </div>
                    </div>
                </form>
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden print-table">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                         <h2 class="text-lg font-semibold text-gray-800">Inventory Details</h2>
                         <button id="openReportModalBtn" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                             <i class="fas fa-expand mr-2"></i> Full Report
                        </button>
                    </div>
                    <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="inventoryTable">
                            <thead class="table-header-sticky bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Photo</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Stock Qty</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total Sold</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">% Taken</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Last Sale</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white" id="inventoryTableBody">
                                <tr id="loading-row">
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                        <i class="fas fa-spinner fa-spin text-4xl text-blue-200 mb-4"></i>
                                        <p class="font-medium">Loading inventory data...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 text-xs text-gray-500">
                        <span class="font-semibold">Note:</span> "Low Stock" is ≥ 95% taken. "No Stock" is 0 quantity.
                    </div>
                </div>

            </div>
        </main>
    </div>

    <div id="reportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[2000] hidden">
        <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full max-h-[90vh] flex flex-col modal-content">
            <div class="modal-header flex justify-between items-center p-5 border-b">
                <h2 class="text-2xl font-bold text-gray-800">Full Inventory Report</h2>
                <button id="modalCloseBtn1" class="text-gray-400 hover:text-gray-600 text-3xl focus:outline-none">&times;</button>
            </div>
            
            <div id="modalPrintArea" class="p-8 overflow-y-auto">
                <div class="print-header hidden">
                    <h1 class="text-2xl font-bold mb-2">Inventory Report</h1>
                    <p>Branch: <strong><?php echo htmlspecialchars($branch_name_display); ?></strong></p>
                    <p>Report for: <span id="modalFilterTimeframe"></span></p>
                    <p>Generated on: <?php echo date('F j, Y, g:i a'); ?></p>
                </div>
                
                <div class="mb-6 no-print-in-modal grid grid-cols-4 gap-4 text-center">
                    <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                        <div class="text-xs text-blue-600 font-bold uppercase">Total Items</div>
                        <div class="text-xl font-bold text-blue-800" id="modalMetricTotal">0</div>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                        <div class="text-xs text-green-600 font-bold uppercase">Sufficient</div>
                        <div class="text-xl font-bold text-green-800" id="modalMetricSufficient">0</div>
                    </div>
                    <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-100">
                        <div class="text-xs text-yellow-600 font-bold uppercase">Low Stock</div>
                        <div class="text-xl font-bold text-yellow-800" id="modalMetricLow">0</div>
                    </div>
                    <div class="p-3 bg-red-50 rounded-lg border border-red-100">
                        <div class="text-xs text-red-600 font-bold uppercase">No Stock</div>
                        <div class="text-xl font-bold text-red-800" id="modalMetricNo">0</div>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-700 mb-4">Detailed Inventory List</h3>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200" id="modalTable">
                        <thead class="bg-gray-50">
                             <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Product</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Stock Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Total Sold</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">% Taken</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Last Sale</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white" id="modalTableBody"></tbody>
                    </table>
                </div>
            </div>
            
            <div class="modal-footer flex justify-end gap-3 p-5 border-t bg-gray-50 rounded-b-xl">
                <button id="modalCloseBtn2" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg shadow-sm hover:bg-gray-50 transition font-medium">Close</button>
                <button id="modalExportExcelBtn" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-sm transition font-medium flex items-center">
                    <i class="fas fa-file-excel mr-2"></i>Export to Excel
                </button>
                <button id="modalPrintBtn" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition font-medium flex items-center">
                    <i class="fas fa-print mr-2"></i>Print Report
                </button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // DOM Elements
        const elements = {
            filterText: document.getElementById('filterText').querySelector('span'),
            dateFilter: document.getElementById('dateFilter'),
            dateStart: document.getElementById('dateStart'),
            dateEnd: document.getElementById('dateEnd'),
            customDateRow: document.getElementById('customDateRow'),
            searchInput: document.getElementById('searchInput'),
            remarksSelect: document.getElementById('remarksSelect'),
            tableBody: document.getElementById('inventoryTableBody'),
            modalTableBody: document.getElementById('modalTableBody'),
            reportModal: document.getElementById('reportModal'),
            metrics: {
                total: document.getElementById('metricTotal'),
                sufficient: document.getElementById('metricSufficient'),
                low: document.getElementById('metricLow'),
                no: document.getElementById('metricNo')
            },
            modalMetrics: {
                total: document.getElementById('modalMetricTotal'),
                sufficient: document.getElementById('modalMetricSufficient'),
                low: document.getElementById('modalMetricLow'),
                no: document.getElementById('modalMetricNo')
            }
        };

        let currentData = [];
        let abortController = null;
        const todayStr = new Date().toISOString().split('T')[0];

        // Initialize Date Inputs
        elements.dateStart.max = todayStr;
        elements.dateEnd.max = todayStr;

        function updateDateInputs() {
            const isCustom = elements.dateFilter.value === 'custom';
            elements.customDateRow.classList.toggle('hidden', !isCustom);
            if (isCustom && !elements.dateStart.value) {
                const lastWeek = new Date();
                lastWeek.setDate(lastWeek.getDate() - 7);
                elements.dateStart.value = lastWeek.toISOString().split('T')[0];
                elements.dateEnd.value = todayStr;
            }
        }

        function updateFilterText() {
            let text = elements.dateFilter.options[elements.dateFilter.selectedIndex].text;
            if (elements.dateFilter.value === 'custom' && elements.dateStart.value && elements.dateEnd.value) {
                text = `${elements.dateStart.value} to ${elements.dateEnd.value}`;
            }
            if (elements.remarksSelect.value) text += ` | ${elements.remarksSelect.options[elements.remarksSelect.selectedIndex].text}`;
            if (elements.searchInput.value) text += ` | Search: "${elements.searchInput.value}"`;
            
            elements.filterText.textContent = `Showing: ${text}`;
            document.getElementById('modalFilterTimeframe').textContent = text;
        }

        function getBadgeHtml(status) {
            const classes = {
                'Sufficient': 'bg-green-100 text-green-800',
                'Low Stock': 'bg-yellow-100 text-yellow-800',
                'No Stock': 'bg-red-100 text-red-800'
            };
            return `<span class="px-2.5 py-0.5 rounded-full text-xs font-medium ${classes[status] || 'bg-gray-100 text-gray-800'} badge-print-hide">${status}</span><span class="badge-print-text">${status}</span>`;
        }

        function updateMetrics(data) {
            const counts = { total: data.length, sufficient: 0, low: 0, no: 0 };
            data.forEach(item => {
                if (item.remarks === 'Sufficient') counts.sufficient++;
                else if (item.remarks === 'Low Stock') counts.low++;
                else if (item.remarks === 'No Stock') counts.no++;
            });

            Object.keys(counts).forEach(key => {
                elements.metrics[key].textContent = counts[key];
                elements.modalMetrics[key].textContent = counts[key];
            });
        }

        function getProductImgSrc(row) {
            if (!row.product_image || row.product_image === 'null') return '../images/user/tile1.jpg';
            if (row.product_image.startsWith('data:image')) return row.product_image;
            if (row.product_image.endsWith('.jpg') || row.product_image.endsWith('.png')) return '../images/visualizer/' + row.product_image;
            return '../images/user/tile1.jpg';
        }
        function renderTable(data, isModal = false) {
            const tbody = isModal ? elements.modalTableBody : elements.tableBody;
            if (!data.length) {
                tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-10 text-center text-gray-500"><i class="fas fa-box-open text-3xl mb-2 opacity-50"></i><p>No inventory matches your filters.</p></td></tr>`;
                return;
            }
            tbody.innerHTML = data.map(row => `
                <tr class="hover:bg-blue-50/50 transition">
                    <td class="px-6 py-3"><img src="${getProductImgSrc(row)}" alt="${row.product_name}" class="w-14 h-14 object-cover rounded shadow border border-gray-200 bg-gray-50"></td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${row.product_name}</td>
                    <td class="px-6 py-4 text-sm text-right text-gray-600">${parseInt(row.last_restock_quantity).toLocaleString()}</td>
                    <td class="px-6 py-4 text-sm text-right font-semibold text-blue-700">${parseInt(row.total_sold).toLocaleString()}</td>
                    <td class="px-6 py-4 text-sm text-right font-bold ${parseFloat(row.percent_taken) >= 95 ? 'text-red-600' : 'text-green-600'}">${parseFloat(row.percent_taken).toFixed(0)}%</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${row.last_update ? new Date(row.last_update).toLocaleDateString() : '-'}</td>
                    <td class="px-6 py-4 text-sm">${getBadgeHtml(row.remarks)}</td>
                </tr>
            `).join('');
        }

        function fetchData() {
            if (abortController) abortController.abort();
            abortController = new AbortController();

            const params = new URLSearchParams({
                dateFilter: elements.dateFilter.value,
                dateStart: elements.dateStart.value,
                dateEnd: elements.dateEnd.value,
                search: elements.searchInput.value,
                remarks: elements.remarksSelect.value
            });

            elements.tableBody.innerHTML = `<tr><td colspan="6" class="px-6 py-10 text-center text-gray-500"><i class="fas fa-spinner fa-spin text-3xl mb-3 text-blue-300"></i><p>Updating inventory data...</p></td></tr>`;
            [elements.metrics.total, elements.metrics.sufficient, elements.metrics.low, elements.metrics.no].forEach(el => el.classList.add('opacity-50'));

            fetch('ajax_inventory_report.php?' + params.toString(), { signal: abortController.signal })
                .then(response => response.json())
                .then(data => {
                    [elements.metrics.total, elements.metrics.sufficient, elements.metrics.low, elements.metrics.no].forEach(el => el.classList.remove('opacity-50'));
                    if (data.success) {
                        currentData = data.inventoryData;
                        renderTable(currentData);
                        updateMetrics(currentData);
                        updateFilterText();
                    } else {
                        elements.tableBody.innerHTML = `<tr><td colspan="6" class="px-6 py-10 text-center text-red-500"><i class="fas fa-exclamation-circle text-2xl mb-2"></i><p>${data.error}</p></td></tr>`;
                    }
                })
                .catch(err => { if (err.name !== 'AbortError') console.error('Fetch error:', err); });
        }

        // Event Listeners
        let debounceTimer;
        const debouncedFetch = () => { clearTimeout(debounceTimer); debounceTimer = setTimeout(fetchData, 300); };

        elements.dateFilter.addEventListener('change', () => { updateDateInputs(); fetchData(); });
        elements.searchInput.addEventListener('input', debouncedFetch);
        elements.remarksSelect.addEventListener('change', fetchData);
        elements.dateStart.addEventListener('change', fetchData);
        elements.dateEnd.addEventListener('change', fetchData);
        document.getElementById('generateReportBtn').addEventListener('click', () => {
            const icon = document.querySelector('#generateReportBtn .fa-sync-alt');
            icon.classList.add('fa-spin');
            fetchData();
            setTimeout(() => icon.classList.remove('fa-spin'), 1000);
        });

        // Modal & Export Handlers
        document.getElementById('openReportModalBtn').onclick = () => {
            renderTable(currentData, true);
            elements.reportModal.classList.remove('hidden');
        };
        const hideModal = () => elements.reportModal.classList.add('hidden');
        document.getElementById('modalCloseBtn1').onclick = hideModal;
        document.getElementById('modalCloseBtn2').onclick = hideModal;
        elements.reportModal.onclick = (e) => { if (e.target === elements.reportModal) hideModal(); };

        document.getElementById('modalPrintBtn').onclick = () => {
            document.querySelectorAll('.print-header').forEach(el => el.classList.remove('hidden'));
            window.print();
            document.querySelectorAll('.print-header').forEach(el => el.classList.add('hidden'));
        };

        document.getElementById('modalExportExcelBtn').onclick = () => {
            if (!currentData.length) return alert('No data to export.');
            const wb = XLSX.utils.book_new();
            
            // Summary Sheet
            const summaryWS = XLSX.utils.aoa_to_sheet([
                ['INVENTORY REPORT SUMMARY'], [],
                ['Branch:', '<?php echo htmlspecialchars($branch_name_display); ?>'],
                ['Generated:', new Date().toLocaleString()],
                ['Timeframe:', document.getElementById('modalFilterTimeframe').textContent], [],
                ['METRICS'],
                ['Total Items', elements.metrics.total.textContent],
                ['Sufficient Stock', elements.metrics.sufficient.textContent],
                ['Low Stock', elements.metrics.low.textContent],
                ['No Stock', elements.metrics.no.textContent]
            ]);
            XLSX.utils.book_append_sheet(wb, summaryWS, 'Summary');

            // Data Sheet
            // Only export visible (not archived) products, and make photo a clickable link
            const exportRows = currentData.map(row => ({
                "Photo": row.product_image.startsWith('data:image')
                    ? 'Image in system (see app)'
                    : row.product_image,
                "Product Name": row.product_name,
                "Stock Quantity": parseInt(row.last_restock_quantity),
                "Total Sold": parseInt(row.total_sold),
                "% Taken": parseFloat(row.percent_taken).toFixed(2) + '%',
                "Last Sale": row.last_update ? new Date(row.last_update).toLocaleDateString() : '-',
                "Status": row.remarks
            }));
            const ws = XLSX.utils.json_to_sheet(exportRows);
            // Style header row for clarity
            const header = [
                "Photo", "Product Name", "Stock Quantity", "Total Sold", "% Taken", "Last Sale", "Status"
            ];
            XLSX.utils.sheet_add_aoa(ws, [header], {origin: 'A1'});
            // Set column widths for better appearance
            ws['!cols'] = [
                {wch: 20}, // Photo
                {wch: 30}, // Product Name
                {wch: 15}, // Stock Quantity
                {wch: 12}, // Total Sold
                {wch: 10}, // % Taken
                {wch: 15}, // Last Sale
                {wch: 12}  // Status
            ];
            XLSX.utils.book_append_sheet(wb, ws, 'Inventory Data');
            XLSX.writeFile(wb, `InventoryReport_${new Date().toISOString().slice(0,10)}.xlsx`);
        };

        // Initial Load
        updateDateInputs();
        fetchData();
    });
    </script>
</body>
</html>