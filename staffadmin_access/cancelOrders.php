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

        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 100;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="main-content-wrapper">
        <main class="min-h-screen">
            <div class="max-w-7xl mx-auto py-8 px-4">
                <div class="mb-8 flex items-center justify-between">
                    <div class="flex items-center">
                        <a href="admin_cancelRequests.php" class="flex items-center no-underline hover:no-underline">
                            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-undo-alt mr-3 text-[#94481b]"></i>Back
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
                            <select id="date-filter"
                                class="w-1/2 p-3 text-textdark rounded-lg border border-gray-300 bg-white form-select">
                                <option value="all">All Time</option>
                                <option value="7-days">Last 7 Days</option>
                                <option value="30-days">Last 30 Days</option>
                            </select>
                            <select id="branch-filter"
                                class="w-1/2 p-3 text-textdark rounded-lg border border-gray-300 bg-white form-select">
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
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl relative overflow-hidden max-h-[90vh]">
            <button id="closeReceiptModal"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl focus:outline-none"><i
                    class="fas fa-times"></i></button>
            <div id="receiptContent" class="p-8">
            </div>
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

            const unitPrice = request.total / request.qty;

            // This data needs to be dynamic to match the order
            const branchInfo = {
                'Brixton Branch': { address: 'Coaster St. Brixtonville Subdivision, Caloocan City' },
                'Samaria Branch': { address: 'St. Vincent Ferrer Ave. Samaria Corner, Tala, Caloocan City' },
                'Vanguard Branch': { address: 'Phase 6, Vanguard, Camarin, North Caloocan' },
                'Deparo Branch': { address: '189 Deparo Road, Caloocan City' },
                'Kiko Branch': { address: 'Kiko, Camarin Road, Caloocan City' },
            };
            const branchAddress = branchInfo[request.branch] ? branchInfo[request.branch].address : 'N/A';
            const shippingFee = request.total > 1000 ? 0 : 150;
            const referralCoinsUsed = 0; // Assuming 0 for now as per image

            let statusColor = '';
            if (request.status === 'Pending') {
                statusColor = '#dc6803';
            } else if (request.status === 'Resolved') {
                statusColor = '#039855';
            } else if (request.status === 'Shipped') {
                statusColor = '#1570ef';
            }

            // HTML content based on the provided image
            const content = `
            <div class="bg-white rounded-xl relative max-h-[90vh] overflow-y-auto">
                <div class="p-8">
                    <div class="receipt-header flex justify-between items-start mb-6 pb-2 border-b border-gray-300">
                        <div class="flex flex-col">
                            <div class="text-2xl font-bold text-[#94481b]">Rich Anne Lea Tiles Trading</div>
                            <div class="text-base text-gray-700 font-semibold">${request.branch}</div>
                            <div class="text-sm text-gray-500">${branchAddress}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold text-[#94481b]">INVOICE</div>
                            <div class="text-sm text-gray-700">Order #${request.orderId}</div>
                            <div class="text-sm text-gray-700">Date: ${request.date}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-8 mb-6">
                        <div class="bill-to">
                            <h3 class="text-lg font-bold text-gray-800 mb-2">Bill To:</h3>
                            <div class="text-gray-700 font-medium">${request.customer}</div>
                            <div class="text-gray-700 text-sm">${request.address}</div>
                            <div class="text-gray-700 text-sm">Mobile: ${request.mobile}</div>
                            <div class="text-gray-700 text-sm">Email: ${request.email}</div>
                        </div>
                        <div class="payment-details">
                            <h3 class="text-lg font-bold text-gray-800 mb-2">Payment Details:</h3>
                            <div class="flex items-center mb-1">
                                <span class="text-gray-700 text-sm mr-2">Method:</span>
                                <span class="font-medium text-gray-800 text-sm">Gcash</span>
                            </div>
                            <div class="flex items-center mb-1">
                                <span class="text-gray-700 text-sm mr-2">Status:</span>
                                <span class="font-medium text-sm" style="color:${statusColor}">${request.status}</span>
                            </div>
                            <div class="flex items-center mb-1">
                                <span class="text-gray-700 text-sm mr-2">Referral Coins Used:</span>
                                <span class="font-medium text-sm">₱${referralCoinsUsed}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-gray-700 text-sm mr-2">Order Total:</span>
                                <span class="font-bold text-lg text-blue-600">₱${grandTotal.toLocaleString()}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="cancelation-reason mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Cancelation Reason</h3>
                        <div class="p-4 bg-gray-100 rounded-lg text-gray-700">
                            ${request.reason}
                        </div>
                    </div>


                    <div class="order-items mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Order Items</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="border-b border-gray-300">
                                    <tr class="text-gray-600">
                                        <th class="py-2 pr-4 font-semibold">Product</th>
                                        <th class="py-2 px-4 text-center font-semibold">Quantity</th>
                                        <th class="py-2 px-4 text-right font-semibold">Unit Price</th>
                                        <th class="py-2 pl-4 text-right font-semibold">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-gray-200">
                                        <td class="py-3 pr-4">${request.product}</td>
                                        <td class="py-3 px-4 text-center">${request.qty}</td>
                                        <td class="py-3 px-4 text-right">₱${unitPrice.toFixed(2)}</td>
                                        <td class="py-3 pl-4 text-right">₱${request.total.toLocaleString()}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="totals-section flex justify-end mb-6">
                        <div class="w-full md:w-1/2">
                            <div class="flex justify-between py-1 border-b border-dashed border-gray-300">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium text-gray-800">₱${request.total.toLocaleString()}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-dashed border-gray-300">
                                <span class="text-gray-600">Shipping:</span>
                                <span class="font-medium text-gray-800">₱${shippingFee}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-dashed border-gray-300">
                                <span class="text-gray-600">Referral Coins Used:</span>
                                <span class="font-medium text-green-600">₱${referralCoinsUsed}</span>
                            </div>
                            <div class="flex justify-between py-1 font-bold text-lg">
                                <span class="text-gray-800">Total:</span>
                                <span class="text-[#94481b]">₱${grandTotal.toLocaleString()}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 mt-6">
                        <button class="bg-red-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-red-700 reject-request-btn flex-1">
                            <i class="fas fa-times mr-2"></i> Reject Request
                        </button>
                        <button class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 approve-request-btn flex-1">
                            <i class="fas fa-check mr-2"></i> Approve Request
                        </button>
                    </div>

                </div>
            </div>`;

            receiptContent.innerHTML = content;
            receiptModal.classList.remove('hidden');
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
                btn.addEventListener('click', function () {
                    const request = JSON.parse(this.getAttribute('data-request'));
                    showReceiptModal(request);
                });
            });
        }
        
        function closeModal() {
            receiptModal.classList.add('hidden');
        }

        // Initial render
        renderRequests('pending');
    </script>

</body>

</html>
