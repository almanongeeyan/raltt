<?php
session_start();
include '../includes/sidebar.php';
require_once '../connection/connection.php';

// Get branch_id from session
$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;

// Initialize variables
$orders = [];
$orderStats = [
    'total' => 0,
    'completed' => 0,
    'cancelled' => 0,
];
$error_message = '';
$branch_name_map = [];

try {
    // **FIX #1: Removed `u.address as customer_address` which was likely causing the error.**
    $query_sql = "SELECT o.*, 
                         b.branch_name,
                         u.full_name as customer_name,
                         u.phone_number as customer_phone,
                         u.email as customer_email
                  FROM orders o 
                  JOIN branches b ON o.branch_id = b.branch_id 
                  JOIN users u ON o.user_id = u.id 
                  WHERE (o.order_status = 'completed' OR o.order_status = 'cancelled')";
    
    $params = [];

    // Build query based on branch access
    if ($branch_id) {
        // Staff can only see orders from their branch
        $query_sql .= " AND o.branch_id = ?";
        $params[] = $branch_id;
    } else {
        // Admin can see all branches. Fetch all branch names for the filter.
        $branchStmt = $db_connection->query("SELECT branch_name FROM branches ORDER BY branch_name");
        $branch_name_map = $branchStmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    $query_sql .= " ORDER BY o.order_date DESC";
    
    $stmt = $db_connection->prepare($query_sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get order items for each order and update stats
    foreach ($orders as &$order) {
        $itemsQuery = "SELECT oi.*, p.product_name 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.product_id 
                       WHERE oi.order_id = ?";
        $itemsStmt = $db_connection->prepare($itemsQuery);
        $itemsStmt->execute([$order['order_id']]);
        
        $order['items'] = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Count statuses
        $orderStats['total']++;
        if (isset($orderStats[$order['order_status']])) {
            $orderStats[$order['order_status']]++;
        }

        // Collect branch names for staff view
        if ($branch_id && !in_array($order['branch_name'], $branch_name_map)) {
            $branch_name_map[] = $order['branch_name'];
        }
    }
    
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    // This is the error message you are seeing.
    $error_message = "Unable to load transactions. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Completed Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Styles for your branded invoice */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            opacity: 0.03;
            font-size: 8rem;
            font-weight: bold;
            pointer-events: none;
            z-index: 0;
        }
        .receipt-header {
            /* Styles from your code, preserved for your branding */
            background: linear-gradient(to bottom, #ffece2 0%, #f8f5f2 100%);
            border-radius: 16px 16px 0 0;
            border-bottom: 2px solid #94481b/30;
            box-shadow: 0 4px 12px rgba(148,72,27,0.05);
        }
        .receipt-body {
            border: 1px solid #94481b/20;
            border-top: none;
            border-radius: 0 0 16px 16px;
            box-shadow: 0 4px 12px rgba(148,72,27,0.02) inset;
        }
        /* Custom styles for sidebar integration */
        body { display: flex; min-height: 100vh; }
        .main-content-wrapper { flex: 1; padding-left: 0; transition: padding-left 0.3s ease; }
        @media (min-width: 768px) { .main-content-wrapper { padding-left: 250px; } }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    
    <div class="main-content-wrapper">
        <main class="min-h-screen">
            <div class="max-w-7xl mx-auto py-8 px-4">
                
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-clipboard-check mr-3 text-blue-600"></i>Transaction Dashboard
                    </h1>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500 flex items-center">
                        <div class="rounded-full bg-blue-100 p-3 mr-4">
                            <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Total Transactions</p>
                            <h3 class="font-bold text-2xl"><?php echo $orderStats['total']; ?></h3>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500 flex items-center">
                        <div class="rounded-full bg-green-100 p-3 mr-4">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Completed</p>
                            <h3 class="font-bold text-2xl"><?php echo $orderStats['completed']; ?></h3>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-red-500 flex items-center">
                        <div class="rounded-full bg-red-100 p-3 mr-4">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Cancelled</p>
                            <h3 class="font-bold text-2xl"><?php echo $orderStats['cancelled']; ?></h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div id="filterText" class="mb-4 text-blue-700 font-semibold text-lg"></div>
                    <form id="filterForm" class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex flex-1 gap-2 flex-wrap">
                            <div class="relative flex-1 min-w-[200px]">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="searchInput" placeholder="Search customer name..." class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" />
                            </div>
                            <select id="statusSelect" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none">
                                <option value="">All Status</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            <select id="branchSelect" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none">
                                <option value="">All Branches</option>
                                <?php foreach ($branch_name_map as $branch): ?>
                                    <option value="<?php echo htmlspecialchars($branch); ?>"><?php echo htmlspecialchars($branch); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex gap-2 items-center flex-wrap">
                            <select id="dateFilter" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none">
                                <option value="">All Time</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="year">This Year</option>
                                <option value="custom">Custom Range</option>
                            </select>
                            <input type="date" id="dateStart" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" />
                            <span class="mx-1 text-gray-400 hidden" id="dateSeparator">-</span>
                            <input type="date" id="dateEnd" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" />
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
                    <?php if (!empty($error_message)): ?>
                        <div class="text-center p-6 bg-red-50 text-red-700 rounded-lg"><?php echo $error_message; ?></div>
                    <?php else: ?>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Ref</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100" id="transactionsTableBody">
                                <?php if (empty($orders)): ?>
                                    <tr id="no-orders-row">
                                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                            <i class="fas fa-box-open text-3xl text-gray-300 mb-2"></i>
                                            <p>No transactions found.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr class="hover:bg-blue-50 transition transaction-row" 
                                            data-customer="<?php echo htmlspecialchars(strtolower($order['customer_name'])); ?>"
                                            data-status="<?php echo htmlspecialchars($order['order_status']); ?>"
                                            data-branch="<?php echo htmlspecialchars($order['branch_name']); ?>"
                                            data-date="<?php echo htmlspecialchars(date('Y-m-d', strtotime($order['order_date']))); ?>">
                                            
                                            <td class="px-4 py-3 font-semibold text-gray-700">#<?php echo htmlspecialchars($order['order_reference']); ?></td>
                                            <td class="px-4 py-3 text-gray-500"><?php echo htmlspecialchars(date('Y-m-d', strtotime($order['order_date']))); ?></td>
                                            <td class="px-4 py-3 text-gray-700"><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                            <td class="px-4 py-3 text-gray-600"><?php echo htmlspecialchars($order['branch_name']); ?></td>
                                            <td class="px-4 py-3 text-blue-600 font-bold">₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td class="px-4 py-3">
                                                <?php if ($order['order_status'] == 'completed'): ?>
                                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Completed</span>
                                                <?php else: ?>
                                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">Cancelled</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-3">
                                                <button class="view-invoice-btn bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition"
                                                        data-order='<?php echo htmlspecialchars(json_encode($order), ENT_QUOTES, 'UTF-8'); ?>'>
                                                    View
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <tr id="no-results-row" class="hidden">
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                        <i class="fas fa-search text-3xl text-gray-300 mb-2"></i>
                                        <p>No transactions match your filters.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    
                    <div class="flex justify-center mt-8">
                        <nav class="inline-flex rounded-md shadow-sm">
                            <a href="#" class="py-2 px-3 ml-0 leading-tight text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                            <a href="#" class="py-2 px-4 leading-tight text-blue-600 bg-blue-50 border border-gray-300 hover:bg-blue-100 hover:text-blue-700">1</a>
                            <a href="#" class="py-2 px-4 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">2</a>
                            <a href="#" class="py-2 px-4 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">3</a>
                            <a href="#" class="py-2 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </nav>
                    </div>
                </div>
                
                <div id="invoiceModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden p-4">
                    <div class="bg-white rounded-2xl shadow-2xl border-4 border-white w-full max-w-2xl md:max-w-3xl lg:max-w-4xl p-0 relative max-h-[90vh] overflow-y-auto" style="box-shadow: 0 8px 40px 8px rgba(148,72,27,0.10), 0 2px 8px 0 rgba(0,0,0,0.08);">
                        <div id="invoiceContent" class="relative overflow-hidden">
                            </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
    // --- Branch Info Map (for invoice header) ---
    // This provides addresses for the branch names fetched from the DB
    const branchInfoMap = {
        'Brixton Branch': { address: 'Coaster St. Brixtonville Subdivision, Caloocan City' },
        'Samaria Branch': { address: 'St. Vincent Ferrer Ave. Samaria Corner, Tala, Caloocan City' },
        'Vanguard Branch': { address: 'Phase 6, Vanguard, Camarin, North Caloocan' },
        'Deparo Branch': { address: '189 Deparo Road, Caloocan City' },
        'Phase 1 Branch': { address: 'Phase 1, Camarin Road, Caloocan City' }
    };

    // --- DOM Elements for Filters ---
    const filterText = document.getElementById('filterText');
    const dateFilter = document.getElementById('dateFilter');
    const dateStart = document.getElementById('dateStart');
    const dateEnd = document.getElementById('dateEnd');
    const dateSeparator = document.getElementById('dateSeparator');
    
    const searchInput = document.getElementById('searchInput');
    const statusSelect = document.getElementById('statusSelect');
    const branchSelect = document.getElementById('branchSelect');
    const tableBody = document.getElementById('transactionsTableBody');
    const allRows = document.querySelectorAll('.transaction-row');
    const noResultsRow = document.getElementById('no-results-row');

    // --- Date Filter Logic ---
    const today = new Date().toISOString().split('T')[0];
    dateStart.max = today;
    dateEnd.max = today;

    dateStart.addEventListener('input', function() {
        if (dateStart.value > today) dateStart.value = today;
        dateEnd.min = dateStart.value;
        updateFilterText();
        applyFilters();
    });
    dateEnd.addEventListener('input', function() {
        if (dateEnd.value > today) dateEnd.value = today;
        dateStart.max = dateEnd.value || today;
        updateFilterText();
        applyFilters();
    });

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
    dateFilter.addEventListener('change', function() {
        updateDateInputs();
        updateFilterText();
        applyFilters();
    });
    
    updateDateInputs(); // Initial state

    function updateFilterText() {
        let text = '';
        if (dateFilter.value === 'week') text = 'Showing transactions from this week';
        else if (dateFilter.value === 'month') text = 'Showing transactions from this month';
        else if (dateFilter.value === 'year') text = 'Showing transactions from this year';
        else if (dateFilter.value === 'custom' && dateStart.value && dateEnd.value) {
            text = `Showing transactions from ${dateStart.value} to ${dateEnd.value}`;
        } else if (dateFilter.value === 'custom') {
            text = 'Select a date range to filter transactions';
        } else {
            text = 'Showing all transactions';
        }
        filterText.textContent = text;
    }
    updateFilterText(); // On page load

    // --- Main Client-Side Filter Logic ---
    function applyFilters() {
        const searchVal = searchInput.value.toLowerCase();
        const statusVal = statusSelect.value;
        const branchVal = branchSelect.value;
        const dateFilterVal = dateFilter.value;
        const startVal = dateStart.value;
        const endVal = dateEnd.value;

        let hasVisibleRows = false;
        
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        
        let dateRangeStart, dateRangeEnd;
        
        if (dateFilterVal === 'week') {
            const firstDayOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
            dateRangeStart = firstDayOfWeek.toISOString().split('T')[0];
            dateRangeEnd = new Date(firstDayOfWeek.setDate(firstDayOfWeek.getDate() + 6)).toISOString().split('T')[0];
        } else if (dateFilterVal === 'month') {
            dateRangeStart = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            dateRangeEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
        } else if (dateFilterVal === 'year') {
            dateRangeStart = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
            dateRangeEnd = new Date(today.getFullYear(), 11, 31).toISOString().split('T')[0];
        } else if (dateFilterVal === 'custom' && startVal && endVal) {
            dateRangeStart = startVal;
            dateRangeEnd = endVal;
        }

        allRows.forEach(row => {
            const customer = row.dataset.customer;
            const status = row.dataset.status;
            const branch = row.dataset.branch;
            const date = row.dataset.date;

            let isVisible = true;

            // Search filter
            if (searchVal && !customer.includes(searchVal)) {
                isVisible = false;
            }
            // Status filter
            if (isVisible && statusVal && status !== statusVal) {
                isVisible = false;
            }
            // Branch filter
            if (isVisible && branchVal && branch !== branchVal) {
                isVisible = false;
            }
            // Date filter
            if (isVisible && dateRangeStart && dateRangeEnd) {
                if (date < dateRangeStart || date > dateRangeEnd) {
                    isVisible = false;
                }
            }

            row.style.display = isVisible ? '' : 'none';
            if (isVisible) {
                hasVisibleRows = true;
            }
        });

        // Show/hide the "no results" message
        noResultsRow.style.display = hasVisibleRows ? 'none' : '';
    }

    // Add event listeners to filters
    searchInput.addEventListener('input', applyFilters);
    statusSelect.addEventListener('change', applyFilters);
    branchSelect.addEventListener('change', applyFilters);


    // --- Invoice Modal Logic ---
    const invoiceModal = document.getElementById('invoiceModal');
    const invoiceContent = document.getElementById('invoiceContent');
    
    document.querySelectorAll('.view-invoice-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const order = JSON.parse(this.getAttribute('data-order'));
            showInvoice(order);
        });
    });
    
    // Close modal on outside click
    invoiceModal.addEventListener('click', function(e) {
        if (e.target === invoiceModal) {
            invoiceModal.classList.add('hidden');
        }
    });

    function showInvoice(order) {
        // --- Calculate Totals ---
        const subtotal = parseFloat(order.total_amount);
        // Use database fields if they exist, otherwise default to 0
        const shippingFee = parseFloat(order.shipping_fee || 0);
        const discount = parseFloat(order.discount_amount || 0); 
        const grandTotal = subtotal + shippingFee - discount;
        
        // --- Get Branch Info ---
        const branchName = order.branch_name || 'Rich Anne Lea Tiles Trading';
        const branchAddress = branchInfoMap[branchName] ? branchInfoMap[branchName].address : 'Main Office Address, Caloocan City';
        
        // --- Build Items Table ---
        let itemsHTML = '';
        if (order.items && order.items.length > 0) {
            order.items.forEach(item => {
                const itemTotal = (parseFloat(item.quantity) * parseFloat(item.unit_price)).toFixed(2);
                itemsHTML += `
                    <tr class="border-b border-[#94481b]/10">
                        <td class="py-3 px-3 text-gray-700">${item.product_name}</td>
                        <td class="py-3 px-3 text-gray-700 text-center">${item.quantity}</td>
                        <td class="py-3 px-3 text-gray-700 text-right">₱${parseFloat(item.unit_price).toFixed(2)}</td>
                        <td class="py-3 px-3 text-gray-700 text-right font-medium">₱${itemTotal}</td>
                    </tr>
                `;
            });
        } else {
            itemsHTML = `
                <tr class="border-b border-[#94481b]/10">
                    <td class="py-3 px-3 text-gray-700">Summary of Order</td>
                    <td class="py-3 px-3 text-gray-700 text-center">1</td>
                    <td class="py-3 px-3 text-gray-700 text-right">₱${subtotal.toFixed(2)}</td>
                    <td class="py-3 px-3 text-gray-700 text-right font-medium">₱${subtotal.toFixed(2)}</td>
                </tr>
            `;
        }
        
        // --- Get Payment Instructions ---
        let paymentInstructions = 'Payment has been processed.';
        if (order.payment_method === 'gcash') {
            paymentInstructions = 'Payment was made via GCash.';
        } else if (order.payment_method === 'paymongo') {
            paymentInstructions = 'Payment was processed via Paymongo.';
        } else if (order.payment_method === 'cod') {
            paymentInstructions = 'Payment was collected upon delivery.';
        } else if (order.payment_method === 'pick_up') {
            paymentInstructions = 'Payment was made upon pick-up at the branch.';
        }
        
        // --- Render Modal HTML ---
        invoiceContent.innerHTML = `
            <div class="watermark">INVOICE</div>
            <div class="receipt-header text-white p-8 relative">
                <div class="flex flex-col items-start mb-6">
                    <div class="text-3xl font-bold mb-1 text-gray-900 tracking-wide" style="color: #94481b; letter-spacing: 2px;">Rich Anne Lea Tiles Trading</div>
                    <div class="text-lg font-semibold text-gray-800 mb-1 border-b border-[#94481b]/20 pb-1 pr-8" style="color: #333;">${branchName}</div>
                    <div class="text-base text-gray-700 mb-1" style="color: #555;">${branchAddress}</div>
                </div>
                <div class="absolute top-8 right-8 text-right">
                    <div class="text-3xl font-bold mb-1" style="color: #94481b;">INVOICE</div>
                    <div class="text-gray-700">Order #${order.order_reference}</div>
                    <div class="text-gray-700">Date: ${new Date(order.order_date).toLocaleDateString()}</div>
                </div>
            </div>
            <div class="receipt-body p-8 bg-white">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="border-b border-dashed border-[#94481b]/20 pb-4 mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 tracking-wide">Bill To:</h3>
                        <div class="text-gray-700 font-medium">${order.customer_name}</div>
                        <div class="text-gray-700">${order.address || 'N/A'}</div>
                        <div class="text-gray-700">Mobile: <span class="font-medium">${order.customer_phone || 'N/A'}</span></div>
                        <div class="text-gray-700">Email: <span class="font-medium">${order.customer_email || 'N/A'}</span></div>
                    </div>
                    <div class="border-b border-dashed border-[#94481b]/20 pb-4 mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 tracking-wide">Payment Details:</h3>
                        <div class="flex items-center mb-1">
                            <span class="text-gray-700 mr-2">Method:</span>
                            <span class="font-medium">${order.payment_method.toUpperCase()}</span>
                        </div>
                        <div class="flex items-center mb-1">
                            <span class="text-gray-700 mr-2">Status:</span>
                            <span class="font-medium ${order.order_status === 'completed' ? 'text-green-600' : 'text-red-600'}">${order.order_status}</span>
                        </div>
                        <div class="flex items-center mb-1">
                            <span class="text-gray-700 mr-2">Discount (Coins):</span>
                            <span class="font-medium text-green-700">- ₱${discount.toFixed(2)}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-gray-700 mr-2">Order Total:</span>
                            <span class="font-medium text-lg text-blue-600">₱${grandTotal.toFixed(2)}</span>
                        </div>
                    </div>
                </div>
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b-2 border-[#94481b]/30 pb-2 tracking-wide">Order Items</h3>
                    <table class="w-full text-left border border-[#94481b]/20 rounded-lg overflow-hidden">
                        <thead>
                            <tr class="border-b-2 border-[#94481b]/20 bg-[#ffece2]">
                                <th class="pb-2 pt-2 px-3 font-semibold text-gray-700">Product</th>
                                <th class="pb-2 pt-2 px-3 font-semibold text-gray-700 text-center">Quantity</th>
                                <th class="pb-2 pt-2 px-3 font-semibold text-gray-700 text-right">Unit Price</th>
                                <th class="pb-2 pt-2 px-3 font-semibold text-gray-700 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHTML}
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-6 border-t border-dashed border-[#94481b]/20 pt-6">
                    <div class="w-full md:w-1/2">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 tracking-wide">Notes:</h3>
                        <div class="text-sm text-gray-600">
                            ${paymentInstructions}
                        </div>
                    </div>
                    <div class="w-full md:w-1/2 pl-0 md:pl-4 mt-4 md:mt-0">
                        <div class="flex justify-between items-center py-2 border-b border-dashed border-[#94481b]/10">
                            <span class="text-gray-700">Subtotal:</span>
                            <span class="text-gray-700">₱${subtotal.toFixed(2)}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-dashed border-[#94481b]/10">
                            <span class="text-gray-700">Shipping:</span>
                            <span class="text-gray-700">₱${shippingFee.toFixed(2)}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-dashed border-[#94481b]/10">
                            <span class="text-gray-700">Discount (Coins):</span>
                            <span class="text-green-700">- ₱${discount.toFixed(2)}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="font-semibold text-gray-800 text-lg">Total Paid:</span>
                            <span class="font-bold text-blue-600 text-xl">₱${grandTotal.toFixed(2)}</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-4 pt-4 border-t-2 border-[#94481b]/30 mt-4">
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" id="closeInvoiceModal" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 font-medium">Close</button>
                        <button type="button" onclick="printInvoice()" class="px-5 py-2 rounded-lg bg-gray-600 text-white font-semibold hover:bg-gray-700 shadow">
                            <i class="fas fa-print mr-2"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        `;
        invoiceModal.classList.remove('hidden');
        
        // Add event listener for the new close button
        document.getElementById('closeInvoiceModal').addEventListener('click', () => {
            invoiceModal.classList.add('hidden');
        });
    }
    
    function printInvoice() {
        const printContent = document.getElementById('invoiceContent').innerHTML;
        const printWindow = window.open('', '_blank', 'height=800,width=800');
        printWindow.document.write('<html><head><title>Print Invoice</title>');
        // Import Tailwind for printing
        printWindow.document.write('<script src="https://cdn.tailwindcss.com"><\/script>');
        printWindow.document.write('<style>');
        // Add your invoice styles here for printing
        printWindow.document.write(`
            .watermark { opacity: 0.05 !important; }
            .receipt-header { background: #fefaf8 !important; -webkit-print-color-adjust: exact; }
            .bg-\\[\\#ffece2\\] { background-color: #ffece2 !important; -webkit-print-color-adjust: exact; }
            @page { size: auto; margin: 0.5in; }
            body { margin: 0; }
        `);
        printWindow.document.write('<\/style></head><body>');
        printWindow.document.write(printContent);
        printWindow.document.write('<\/body><\/html>');
        printWindow.document.close();
        
        // Use a timeout to ensure styles are loaded
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 1000);
    }

    // Optional: close modal on ESC
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !invoiceModal.classList.contains('hidden')) {
            invoiceModal.classList.add('hidden');
        }
    });
    </script>
</body>
</html>