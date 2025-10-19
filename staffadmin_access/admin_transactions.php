<?php include '../includes/sidebar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Completed Orders</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border-radius: 12px 12px 0 0;
        }
        .receipt-body {
            border-left: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            border-radius: 0 0 12px 12px;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex">
        <!-- Sidebar is included and styled by its own file -->
        <div class="hidden md:block" style="width:250px;"></div>
        <main class="flex-1 min-h-screen md:ml-0" style="margin-left:0;">
            <div class="max-w-7xl mx-auto py-8 px-4">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-clipboard-check mr-3 text-blue-600"></i>Transaction Dashboard
                    </h1>
                </div>
                <!-- Dashboard Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500 flex items-center">
                        <div class="rounded-full bg-blue-100 p-3 mr-4">
                            <i class="fas fa-clipboard-list text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Total Orders</p>
                            <h3 class="font-bold text-2xl">42</h3>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500 flex items-center">
                        <div class="rounded-full bg-green-100 p-3 mr-4">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Completed</p>
                            <h3 class="font-bold text-2xl">24</h3>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500 flex items-center">
                        <div class="rounded-full bg-red-100 p-3 mr-4">
                            <i class="fas fa-times-circle text-red-600"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Cancelled</p>
                            <h3 class="font-bold text-2xl">5</h3>
                        </div>
                    </div>
                </div>
                <!-- Filter Section -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div id="filterText" class="mb-4 text-blue-700 font-semibold text-lg"></div>
                    <form id="filterForm" class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex flex-1 gap-2">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" placeholder="Search customer name..." class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" />
                            </div>
                            <select id="statusSelect" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none">
                                <option value="">All Status</option>
                                <option>Completed</option>
                                <option>Cancelled</option>
                            </select>
                            <!-- Branch Dropdown -->
                            <select id="branchSelect" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none">
                                <option value="">All Branches</option>
                                <option value="Brixton">Brixton</option>
                                <option value="Vanguard">Vanguard</option>
                                <option value="Phase 1">Phase 1</option>
                                <option value="Deparo">Deparo</option>
                                <option value="Samaria">Samaria</option>
                            </select>
                        </div>
                        <div class="flex gap-2 items-center">
                            <select id="dateFilter" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none">
                                <option value="">All Time</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="year">This Year</option>
                                <option value="custom">Custom Range</option>
                            </select>
                            <input type="date" id="dateStart" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" />
                            <span class="mx-1 text-gray-400">-</span>
                            <input type="date" id="dateEnd" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" />
                        </div>
                    </form>
                </div>
                <!-- Orders Table -->
                <div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <!-- Example Row 1 -->
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-3 font-semibold text-gray-700">#1001</td>
                                <td class="px-4 py-3 text-gray-500">2025-08-20</td>
                                <td class="px-4 py-3 text-gray-700">Juan Dela Cruz</td>
                                <td class="px-4 py-3 text-blue-600 font-bold">₱5,000.00</td>
                                <td class="px-4 py-3"><span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Completed</span></td>
                                <td class="px-4 py-3"><button class="view-invoice-btn bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition" data-order='{"id":1001,"date":"2025-08-20","customer":"Juan Dela Cruz","address":"123 Main St, City","mobile":"0917-123-4567","email":"juan@example.com","payment":"Gcash","product":"Floral Blue Tiles","qty":10,"total":5000,"status":"Completed","referralCoins":5}'>View</button></td>
                            </tr>
                            <!-- Example Row 2 -->
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-3 font-semibold text-gray-700">#1002</td>
                                <td class="px-4 py-3 text-gray-500">2025-08-18</td>
                                <td class="px-4 py-3 text-gray-700">Maria Santos</td>
                                <td class="px-4 py-3 text-blue-600 font-bold">₱2,500.00</td>
                                <td class="px-4 py-3"><span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Completed</span></td>
                                <td class="px-4 py-3"><button class="view-invoice-btn bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition" data-order='{"id":1002,"date":"2025-08-18","customer":"Maria Santos","address":"456 Oak Ave, City","mobile":"0918-987-6543","email":"maria@example.com","payment":"Paymongo","product":"Black Diamond Tiles","qty":5,"total":2500,"status":"Completed","referralCoins":0}'>View</button></td>
                            </tr>
                            <!-- Example Row 3 -->
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-3 font-semibold text-gray-700">#1003</td>
                                <td class="px-4 py-3 text-gray-500">2025-08-15</td>
                                <td class="px-4 py-3 text-gray-700">Pedro Reyes</td>
                                <td class="px-4 py-3 text-blue-600 font-bold">₱4,000.00</td>
                                <td class="px-4 py-3"><span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">Cancelled</span></td>
                                <td class="px-4 py-3"><button class="view-invoice-btn bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg shadow-sm transition" data-order='{"id":1003,"date":"2025-08-15","customer":"Pedro Reyes","address":"789 Pine Rd, City","mobile":"0919-555-1234","email":"pedro@example.com","payment":"Cash on Delivery","product":"Classical Black Tiles","qty":8,"total":4000,"status":"Cancelled","referralCoins":8}'>View</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- Pagination -->
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
                <!-- Invoice Modal -->
                <div id="invoiceModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                    <div class="bg-white rounded-2xl shadow-2xl border-4 border-white w-full max-w-2xl md:max-w-3xl lg:max-w-4xl p-0 relative max-h-[90vh] overflow-y-auto ml-[250px]" style="box-shadow: 0 8px 40px 8px rgba(148,72,27,0.10), 0 2px 8px 0 rgba(0,0,0,0.08);">
                        <div id="invoiceContent" class="relative overflow-hidden"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
    // --- Date Filter Logic ---
    const filterText = document.getElementById('filterText');
    const dateFilter = document.getElementById('dateFilter');
    const dateStart = document.getElementById('dateStart');
    const dateEnd = document.getElementById('dateEnd');
    // Set max date to today for both date pickers
    const today = new Date().toISOString().split('T')[0];
    dateStart.max = today;
    dateEnd.max = today;
    // Disable future dates
    dateStart.addEventListener('input', function() {
        if (dateStart.value > today) dateStart.value = today;
        dateEnd.min = dateStart.value;
    });
    dateEnd.addEventListener('input', function() {
        if (dateEnd.value > today) dateEnd.value = today;
        dateStart.max = dateEnd.value || today;
    });
    // Hide date pickers unless custom is selected
    function updateDateInputs() {
        if (dateFilter.value === 'custom') {
            dateStart.style.display = '';
            dateEnd.style.display = '';
        } else {
            dateStart.style.display = 'none';
            dateEnd.style.display = 'none';
        }
    }
    dateFilter.addEventListener('change', function() {
        updateDateInputs();
        updateFilterText();
    });
    // Initial state
    updateDateInputs();
    // Update filter text
    function updateFilterText() {
        let text = '';
        if (dateFilter.value === 'week') text = 'Showing orders from this week';
        else if (dateFilter.value === 'month') text = 'Showing orders from this month';
        else if (dateFilter.value === 'year') text = 'Showing orders from this year';
        else if (dateFilter.value === 'custom' && dateStart.value && dateEnd.value) {
            text = `Showing orders from ${dateStart.value} to ${dateEnd.value}`;
        } else if (dateFilter.value === 'custom') {
            text = 'Select a date range to filter orders';
        } else {
            text = 'Showing all orders';
        }
        filterText.textContent = text;
    }
    dateStart.addEventListener('input', updateFilterText);
    dateEnd.addEventListener('input', updateFilterText);
    // On page load
    updateFilterText();

    // --- Invoice Modal Logic ---
    const invoiceModal = document.getElementById('invoiceModal');
    const invoiceContent = document.getElementById('invoiceContent');
    
    document.querySelectorAll('.view-invoice-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const order = JSON.parse(this.getAttribute('data-order'));
            showInvoice(order);
        });
    });
    
    function showInvoice(order) {
        const unitPrice = (order.total / order.qty).toFixed(2);
        const shippingFee = order.total > 1000 ? 0 : 150;
        const referralCoinsUsed = Math.min(order.referralCoins, 10);
        const grandTotal = order.total + shippingFee - referralCoinsUsed;
        
        // Branch info for header
        const branchInfo = {
            'Brixton Branch': {
                address: 'Coaster St. Brixtonville Subdivision, Caloocan City',
            },
            'Samaria Branch': {
                address: 'St. Vincent Ferrer Ave. Samaria Corner, Tala, Caloocan City',
            },
            'Vanguard Branch': {
                address: 'Phase 6, Vanguard, Camarin, North Caloocan',
            },
            'Deparo Branch': {
                address: '189 Deparo Road, Caloocan City',
            },
            'Phase 1 Branch': {
                address: 'Phase 1, Camarin Road, Caloocan City',
            },
        };
        
        // For demo purposes, assign a branch
        const branchName = 'Brixton Branch';
        const branchAddress = branchInfo[branchName].address;
        
        invoiceContent.innerHTML = `
            <div class="watermark">INVOICE</div>
            <div class="receipt-header text-white p-8 relative border-b-2 border-[#94481b]/30 shadow-lg" style="background: linear-gradient(to bottom, #ffece2 0%, #f8f5f2 100%); border-radius: 16px 16px 0 0;">
                <div class="flex flex-col items-start mb-6">
                    <div class="text-3xl font-bold mb-1 text-gray-900 tracking-wide" style="color: #94481b; letter-spacing: 2px;">Rich Anne Lea Tiles Trading</div>
                    <div class="text-lg font-semibold text-gray-800 mb-1 border-b border-[#94481b]/20 pb-1 pr-8" style="color: #333;">${branchName}</div>
                    <div class="text-base text-gray-700 mb-1" style="color: #555;">${branchAddress}</div>
                </div>
                <div class="absolute top-8 right-8 text-right">
                    <div class="text-3xl font-bold mb-1" style="color: #94481b;">INVOICE</div>
                    <div class="text-gray-700">Order #${order.id}</div>
                    <div class="text-gray-700">Date: ${order.date}</div>
                </div>
            </div>
            <div class="receipt-body p-8 bg-white border border-[#94481b]/20 rounded-b-2xl shadow-inner">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="border-b border-dashed border-[#94481b]/20 pb-4 mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 tracking-wide">Bill To:</h3>
                        <div class="text-gray-700 font-medium">${order.customer}</div>
                        <div class="text-gray-700">${order.address}</div>
                        <div class="text-gray-700">Mobile: <span class="font-medium">${order.mobile}</span></div>
                        <div class="text-gray-700">Email: <span class="font-medium">${order.email}</span></div>
                    </div>
                    <div class="border-b border-dashed border-[#94481b]/20 pb-4 mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 tracking-wide">Payment Details:</h3>
                        <div class="flex items-center mb-1">
                            <span class="text-gray-700 mr-2">Method:</span>
                            <span class="font-medium">${order.payment}</span>
                        </div>
                        <div class="flex items-center mb-1">
                            <span class="text-gray-700 mr-2">Status:</span>
                            <span class="font-medium ${order.status === 'Completed' ? 'text-green-600' : 'text-red-600'}">${order.status}</span>
                        </div>
                        <div class="flex items-center mb-1">
                            <span class="text-gray-700 mr-2">Referral Coins Used:</span>
                            <span class="font-medium text-green-700">₱${referralCoinsUsed.toLocaleString()}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-gray-700 mr-2">Order Total:</span>
                            <span class="font-medium text-lg text-blue-600">₱${order.total.toLocaleString()}</span>
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
                            <tr class="border-b border-[#94481b]/10">
                                <td class="py-3 px-3 text-gray-700">${order.product}</td>
                                <td class="py-3 px-3 text-gray-700 text-center">${order.qty}</td>
                                <td class="py-3 px-3 text-gray-700 text-right">₱${unitPrice}</td>
                                <td class="py-3 px-3 text-gray-700 text-right font-medium">₱${order.total.toLocaleString()}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-6 border-t border-dashed border-[#94481b]/20 pt-6">
                    <div class="w-full md:w-1/2">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 tracking-wide">Payment Instructions:</h3>
                        <div class="text-sm text-gray-600">
                            ${order.payment === 'Gcash' ? 
                              'Please send payment to GCash #0917-123-4567. Include order number in description.' : 
                             order.payment === 'Paymongo' ? 
                              'Payment link has been sent to your email. Click to complete transaction.' :
                             'Payment will be collected upon delivery. Please have exact amount ready.'}
                        </div>
                    </div>
                    <div class="w-full md:w-1/2 pl-0 md:pl-4 mt-4 md:mt-0">
                        <div class="flex justify-between items-center py-2 border-b border-dashed border-[#94481b]/10">
                            <span class="text-gray-700">Subtotal:</span>
                            <span class="text-gray-700">₱${order.total.toLocaleString()}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-dashed border-[#94481b]/10">
                            <span class="text-gray-700">Shipping:</span>
                            <span class="text-gray-700">₱${shippingFee.toLocaleString()}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-dashed border-[#94481b]/10">
                            <span class="text-gray-700">Referral Coins Used:</span>
                            <span class="text-green-700">₱${referralCoinsUsed.toLocaleString()}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="font-semibold text-gray-800">Total:</span>
                            <span class="font-bold text-blue-600">₱${grandTotal.toLocaleString()}</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-4 pt-4 border-t-2 border-[#94481b]/30 mt-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Order Status</label>
                        <div class="px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-100 text-gray-700">
                            ${order.status}
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" id="closeInvoiceModal" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 font-medium">Close</button>
                        <button type="button" class="px-5 py-2 rounded-lg bg-gray-600 text-white font-semibold hover:bg-gray-700 shadow">
                            <i class="fas fa-print mr-2"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        `;
        invoiceModal.classList.remove('hidden');
        
        // Add event listener for the close button
        document.getElementById('closeInvoiceModal').addEventListener('click', () => {
            invoiceModal.classList.add('hidden');
        });
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