<?php
include '../includes/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Requests</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
        }
        .main-content-wrapper {
            flex: 1;
            padding-left: 0;
            transition: padding-left 0.3s ease;
        }
        @media (min-width: 768px) {
            .main-content-wrapper {
                padding-left: 250px;
            }
        }
        .tab-button {
            position: relative;
            font-weight: 500;
            flex: 1;
            text-align: center;
        }

        .tab-button::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #94481b;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .tab-button.active::after {
            transform: scaleX(1);
        }
        
        .tab-button.active {
            color: #94481b;
        }

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
        .form-select {
            color: #333;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content-wrapper">
        <main class="min-h-screen">
            <div class="max-w-7xl mx-auto py-8 px-4">
                <div class="mb-8 flex items-center justify-between">
                    <div class="flex items-center">
                        <a href="admin_cancelRequests.php" class="flex items-center no-underline hover:no-underline">
                            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-undo-alt mr-3 text-[#94481b]"></i>Cancel Requests
                            </h1>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 relative">
                    <div class="flex flex-col mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center mb-4">
                            <i class="fas fa-list-alt mr-2 text-[#94481b]"></i>Requests
                        </h2>
                        
                        <!-- Tabs -->
                        <div class="flex flex-wrap gap-4 mb-4 border-b border-gray-200">
                            <button class="tab-button text-primary py-2 active" data-tab="pending">
                                Pending
                            </button>
                            <button class="tab-button text-primary py-2" data-tab="resolved">
                                Resolved
                            </button>
                        </div>

                        <!-- Filter Dropdown -->
                        <div class="flex gap-4">
                            <select id="date-filter" class="w-1/2 p-3 text-textdark rounded-lg border border-gray-300 bg-white form-select">
                                <option value="all">All Time</option>
                                <option value="7-days">Last 7 Days</option>
                                <option value="30-days">Last 30 Days</option>
                            </select>
                            <select id="branch-filter" class="w-1/2 p-3 text-textdark rounded-lg border border-gray-300 bg-white form-select">
                                <option value="all">All Branches</option>
                                <option value="Brixton Branch">Brixton Branch</option>
                                <option value="Samaria Branch">Samaria Branch</option>
                                <option value="Vanguard Branch">Vanguard Branch</option>
                                <option value="Deparo Branch">Deparo Branch</option>
                                <option value="Kiko Branch">Kiko Branch</option>
                            </select>
                        </div>

                    </div>
                    
                    <!-- Tab Content -->
                    <div id="tab-content" class="min-h-[400px] relative">
                        <!-- Content will be rendered here by JavaScript -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Receipt Modal -->
    <div id="receiptModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-40 hidden">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-8 relative overflow-y-auto max-h-[90vh]">
            <button id="closeReceiptModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl focus:outline-none"><i class="fas fa-times"></i></button>
            <div id="receiptContent"></div>
        </div>
    </div>

<script>
    // Dummy data for requests
    const requestsData = {
        pending: [
            { id: '12345', orderId: 'ORD-12345', date: 'Oct 25, 2023', reason: 'Customer changed mind.', customer: 'John Doe', product: 'Floral Blue Tiles', qty: 10, total: 1200, status: 'Pending', branch: 'Brixton Branch', mobile: '0912-345-6789', address: '123 Main St, City', email: 'john.doe@email.com' },
            { id: '67890', orderId: 'ORD-67890', date: 'Oct 26, 2023', reason: 'Incorrect size ordered.', customer: 'Jane Smith', product: 'Black Diamond Tiles', qty: 5, total: 600, status: 'Pending', branch: 'Samaria Branch', mobile: '0998-765-4321', address: '456 Oak Ave, City', email: 'jane.smith@email.com' },
        ],
        resolved: [
            { id: '11223', orderId: 'ORD-11223', date: 'Oct 20, 2023', reason: 'Customer moved to a new address.', customer: 'Bob White', product: 'Classical Black Tiles', qty: 8, total: 900, status: 'Resolved', branch: 'Vanguard Branch', mobile: '0933-444-5555', address: '321 Elm St, City', email: 'bob.white@email.com' },
            { id: '44556', orderId: 'ORD-44556', date: 'Oct 18, 2023', reason: 'Incorrect color ordered.', customer: 'Charlie Black', product: 'Leafy Rose Tiles', qty: 7, total: 800, status: 'Resolved', branch: 'Deparo Branch', mobile: '0922-333-4444', address: '654 Maple Ave, City', email: 'charlie.black@email.com' },
        ]
    };

    const tabsContainer = document.querySelector('.flex.flex-wrap.gap-4');
    const tabContent = document.getElementById('tab-content');
    const receiptModal = document.getElementById('receiptModal');
    const closeReceiptModalBtn = document.getElementById('closeReceiptModal');
    const receiptContent = document.getElementById('receiptContent');

    const dateFilter = document.getElementById('date-filter');
    const branchFilter = document.getElementById('branch-filter');
    const pendingBtn = document.querySelector('[data-tab="pending"]');
    const resolvedBtn = document.querySelector('[data-tab="resolved"]');

    let currentFilter = { status: 'pending', date: 'all', branch: 'all' };

    closeReceiptModalBtn.addEventListener('click', () => {
        receiptModal.classList.add('hidden');
    });

    function showReceiptModal(request) {
        const grandTotal = request.total;
        
        let title, buttonsHtml, orderDetailsHtml;

        if (request.status === 'Pending') {
            title = request.orderId;
            orderDetailsHtml = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="border-b border-dashed border-[#94481b]/20 pb-4 mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 tracking-wide">Customer Details:</h3>
                        <div class="text-gray-700 font-medium">${request.customer}</div>
                        <div class="text-gray-700">${request.address}</div>
                        <div class="text-gray-700">Mobile: <span class="font-medium">${request.mobile}</span></div>
                        <div class="text-gray-700">Email: <span class="font-medium">${request.email}</span></div>
                    </div>
                    <div class="border-b border-dashed border-[#94481b]/20 pb-4 mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 tracking-wide">Request Details:</h3>
                        <div class="flex items-center mb-1">
                            <span class="text-gray-700 mr-2">Order #:</span>
                            <span class="font-medium">${request.orderId}</span>
                        </div>
                        <div class="flex items-center mb-1">
                            <span class="text-gray-700 mr-2">Status:</span>
                            <span class="font-medium">${request.status}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-gray-700 mr-2">Reason:</span>
                            <span class="font-medium text-lg text-blue-600">${request.reason}</span>
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
                                <th class="pb-2 pt-2 px-3 font-semibold text-gray-700 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-[#94481b]/10">
                                <td class="py-3 px-3 text-gray-700">${request.product}</td>
                                <td class="py-3 px-3 text-gray-700 text-center">${request.qty}</td>
                                <td class="py-3 px-3 text-gray-700 text-right font-medium">₱${request.total.toLocaleString()}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-6 border-t border-dashed border-[#94481b]/20 pt-6">
                    <div class="w-full md:w-1/2">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 tracking-wide">Notes:</h3>
                        <div class="text-sm text-gray-600">
                           Review the request details and customer information before taking action. Contact the customer for further verification if needed.
                        </div>
                    </div>
                    <div class="w-full md:w-1/2 pl-0 md:pl-4 mt-4 md:mt-0">
                        <div class="flex justify-between items-center py-2">
                            <span class="font-semibold text-gray-800">Order Total:</span>
                            <span class="font-bold text-blue-600">₱${grandTotal.toLocaleString()}</span>
                        </div>
                    </div>
                </div>
            `;
            buttonsHtml = `
                <button type="button" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 font-medium close-receipt-btn">Close</button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow approve-request-btn">
                    <i class="fas fa-check-circle mr-2"></i> Approve Request
                </button>
            `;
        } else {
            // Resolved ticket details
            title = request.orderId;
            orderDetailsHtml = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="border-b border-dashed border-[#94481b]/20 pb-4 mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 tracking-wide">Customer Details:</h3>
                        <div class="text-gray-700 font-medium">${request.customer}</div>
                        <div class="text-gray-700">${request.address}</div>
                        <div class="text-gray-700">Mobile: <span class="font-medium">${request.mobile}</span></div>
                        <div class="text-gray-700">Email: <span class="font-medium">${request.email}</span></div>
                    </div>
                    <div class="border-b border-dashed border-[#94481b]/20 pb-4 mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 tracking-wide">Request Details:</h3>
                        <div class="flex items-center mb-1">
                            <span class="text-gray-700 mr-2">Order #:</span>
                            <span class="font-medium">${request.orderId}</span>
                        </div>
                        <div class="flex items-center mb-1">
                            <span class="text-gray-700 mr-2">Status:</span>
                            <span class="font-medium">${request.status}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-gray-700 mr-2">Reason:</span>
                            <span class="font-medium text-lg text-blue-600">${request.reason}</span>
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
                                <th class="pb-2 pt-2 px-3 font-semibold text-gray-700 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-[#94481b]/10">
                                <td class="py-3 px-3 text-gray-700">${request.product}</td>
                                <td class="py-3 px-3 text-gray-700 text-center">${request.qty}</td>
                                <td class="py-3 px-3 text-gray-700 text-right font-medium">₱${request.total.toLocaleString()}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-6 border-t border-dashed border-[#94481b]/20 pt-6">
                    <div class="w-full md:w-1/2">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 tracking-wide">Notes:</h3>
                        <div class="text-sm text-gray-600">
                           This ticket has been resolved. The cancellation request was approved and the order has been cancelled.
                        </div>
                    </div>
                    <div class="w-full md:w-1/2 pl-0 md:pl-4 mt-4 md:mt-0">
                        <div class="flex justify-between items-center py-2">
                            <span class="font-semibold text-gray-800">Order Total:</span>
                            <span class="font-bold text-blue-600">₱${grandTotal.toLocaleString()}</span>
                        </div>
                    </div>
                </div>
            `;
            buttonsHtml = `
                <button type="button" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 font-medium close-receipt-btn">Close</button>
                <button type="button" disabled class="px-5 py-2 rounded-lg bg-gray-600 text-white font-semibold shadow cursor-not-allowed">
                    <i class="fas fa-check-circle mr-2"></i> Resolved
                </button>
            `;
        }
        
        const content = `
            <div class="watermark">REQUEST</div>
            <div class="receipt-header text-white p-8 relative border-b-2 border-[#94481b]/30 shadow-lg" style="background: linear-gradient(to bottom, #ffece2 0%, #f8f5f2 100%); border-radius: 16px 16px 0 0;">
                <div class="flex flex-col items-start mb-6">
                    <div class="text-3xl font-bold mb-1 text-gray-900 tracking-wide" style="color: #94481b; letter-spacing: 2px;">${title}</div>
                    <div class="text-lg font-semibold text-gray-800 mb-1 border-b border-[#94481b]/20 pb-1 pr-8" style="color: #333;">${request.branch}</div>
                </div>
                <div class="absolute top-8 right-8 text-right">
                    <div class="text-3xl font-bold mb-1" style="color: #94481b;">REQUEST</div>
                    <div class="text-gray-700">Request ID #${request.id}</div>
                    <div class="text-gray-700">Date: ${request.date}</div>
                </div>
            </div>
            <div class="receipt-body p-8 bg-white border border-[#94481b]/20 rounded-b-2xl shadow-inner">
                ${orderDetailsHtml}
                <div class="flex justify-end gap-2 mt-6 pt-4 border-t-2 border-[#94481b]/30">
                    ${buttonsHtml}
                </div>
            </div>
        `;
        receiptContent.innerHTML = content;
        receiptModal.classList.remove('hidden');

        // Re-attach event listeners to the dynamically created buttons
        document.querySelector('.close-receipt-btn').addEventListener('click', () => receiptModal.classList.add('hidden'));
    }

    tabsContainer.addEventListener('click', (e) => {
        const button = e.target.closest('.tab-button');
        if (button) {
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            renderRequests(button.dataset.tab);
        }
    });

    function renderRequests(tab) {
        const requests = requestsData[tab] || [];
        tabContent.innerHTML = '';

        if (requests.length === 0) {
            tabContent.innerHTML = '<div class="text-center text-gray-500 py-12">No requests found.</div>';
            return;
        }

        let tableHtml = `
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left border border-gray-200 rounded-lg overflow-hidden table-fixed">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-4 py-2 border-b border-gray-200 text-blue-900 font-semibold w-1/4">Request ID</th>
                            <th class="px-4 py-2 border-b border-gray-200 text-blue-900 font-semibold w-1/4">Order #</th>
                            <th class="px-4 py-2 border-b border-gray-200 text-blue-900 font-semibold w-1/4">Date</th>
                            <th class="px-4 py-2 border-b border-gray-200 text-blue-900 font-semibold w-1/4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
        `;

        requests.forEach(request => {
            const actionButton = tab === 'pending' ?
                `<button class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700 text-xs font-semibold transition w-[150px] process-request-btn" data-request='${JSON.stringify(request)}'>Process</button>` :
                `<button class="px-3 py-1 rounded bg-gray-600 text-white hover:bg-gray-700 text-xs font-semibold transition w-[150px] process-request-btn" data-request='${JSON.stringify(request)}'>View</button>`;

            tableHtml += `
                <tr class="hover:bg-blue-50">
                    <td class="px-4 py-2 border-b border-gray-200 font-mono w-1/4">#${request.id}</td>
                    <td class="px-4 py-2 border-b border-gray-200 w-1/4">${request.orderId}</td>
                    <td class="px-4 py-2 border-b border-gray-200 w-1/4">${request.date}</td>
                    <td class="px-4 py-2 border-b border-gray-200 w-1/4">${actionButton}</td>
                </tr>
            `;
        });

        tableHtml += `
                    </tbody>
                </table>
            </div>
        `;

        tabContent.innerHTML = tableHtml;

        // Add event listeners for the new buttons
        document.querySelectorAll('.process-request-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const request = JSON.parse(this.getAttribute('data-request'));
                showReceiptModal(request);
            });
        });
    }

    // Initial render
    renderRequests('pending');
</script>

</body>
</html>
