<?php
include '../includes/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* This is a helper style to ensure main content shifts correctly when sidebar is open */
        body {
            display: flex;
            min-height: 100vh;
        }

        .main-content-wrapper {
            flex: 1;
            padding-left: 0; /* Default for mobile/collapsed */
            transition: padding-left 0.3s ease;
        }

        @media (min-width: 768px) {
            .main-content-wrapper {
                padding-left: 250px; /* Adjust for sidebar width on desktop */
            }
        }

        .branch-card {
            transition: all 0.3s ease;
        }
        .branch-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .status-completed {
            background-color: #ecfdf3;
            color: #039855;
        }
        .status-pending {
            background-color: #fffaeb;
            color: #dc6803;
        }
        .status-shipped {
            background-color: #eff8ff;
            color: #1570ef;
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
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="main-content-wrapper">
        <main class="min-h-screen">
            <div class="max-w-7xl mx-auto py-8 px-4">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-clipboard-list mr-3 text-blue-600"></i>Orders Dashboard
                    </h1>
                    <p class="text-gray-600 mt-2">Manage and track orders across all branches</p>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
                    <?php
                    $branches = [
                        ['name' => 'Brixton Branch', 'icon' => 'fa-store', 'orders' => 15],
                        ['name' => 'Samaria Branch', 'icon' => 'fa-store', 'orders' => 9],
                        ['name' => 'Vanguard Branch', 'icon' => 'fa-store', 'orders' => 7],
                        ['name' => 'Deparo Branch', 'icon' => 'fa-store', 'orders' => 6],
                        ['name' => 'Kiko Branch', 'icon' => 'fa-store', 'orders' => 5],
                    ];
                    foreach ($branches as $i => $branch): ?>
                    <div class="branch-card bg-white rounded-2xl shadow-lg p-6 flex flex-col justify-between items-center border border-blue-100 relative mx-auto max-w-xs">
                        <div class="rounded-full bg-blue-100 p-4 mb-3 flex items-center justify-center">
                            <i class="fas <?= $branch['icon'] ?> text-2xl text-blue-600"></i>
                        </div>
                        <div class="font-bold text-lg text-gray-900 mb-2 text-center tracking-wide flex-1 flex items-center justify-center"><?= $branch['name'] ?></div>
                        <div class="text-sm text-gray-600 mb-4 flex-1 flex items-center justify-center"><?= $branch['orders'] ?> Orders</div>
                        <?php
                        // Get user's branch from session (use branch_id and map to branch name)
                        if (!isset($user_branch_name)) {
                            $branch_names = [
                                1 => 'Deparo Branch',
                                2 => 'Vanguard Branch',
                                3 => 'Brixton Branch',
                                4 => 'Samaria Branch',
                                5 => 'Kiko Branch',
                            ];
                            $user_branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
                            $user_branch_name = isset($branch_names[$user_branch_id]) ? $branch_names[$user_branch_id] : '';
                        }
                        $isUserBranch = ($user_branch_name === $branch['name']);
                        ?>
                        <button class="view-orders-btn <?= $isUserBranch ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-300 text-gray-400 cursor-not-allowed opacity-60' ?> font-medium px-5 py-2.5 rounded-lg shadow-sm transition flex items-center mt-2" data-branch="<?= $branch['name'] ?>" <?= $isUserBranch ? '' : 'disabled' ?>>
                            <i class="fas fa-eye mr-2"></i> View Orders
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div id="ordersModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-40 hidden">
                    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-8 relative">
                        <button id="closeOrdersModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl focus:outline-none"><i class="fas fa-times"></i></button>
                        <h2 id="modalBranchTitle" class="text-2xl font-bold text-gray-800 mb-6 flex items-center"><i class="fas fa-store mr-2 text-blue-600"></i>Orders for <span class="ml-2" id="branchName"></span></h2>
                        <div id="ordersTableContainer">
                            </div>
                    </div>
                </div>

                <div id="receiptModal" class="fixed inset-0 z-[70] flex items-center justify-end pr-8 md:pr-24 bg-black bg-opacity-60 backdrop-blur-sm hidden">
                    <div class="bg-white rounded-2xl shadow-2xl border-4 border-white w-full max-w-2xl md:max-w-3xl lg:max-w-4xl p-0 relative max-h-[90vh] overflow-y-auto mr-0 md:mr-8" style="box-shadow: 0 8px 40px 8px rgba(148,72,27,0.10), 0 2px 8px 0 rgba(0,0,0,0.08);">
                        <div id="receiptContent" class="relative overflow-hidden"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>

<script>
// Dummy orders data
const ordersData = {
    'Brixton Branch': [
        { id: 1001, customer: 'John Doe', address: '123 Main St, City', payment: 'Gcash', product: 'Floral Blue Tiles', qty: 10, total: 1200, status: 'Completed', date: '2023-10-15' },
        { id: 1002, customer: 'Jane Smith', address: '456 Oak Ave, City', payment: 'Cash on Delivery', product: 'Black Diamond Tiles', qty: 5, total: 600, status: 'Pending', date: '2023-10-18' },
    ],
    'Samaria Branch': [
        { id: 2001, customer: 'Alice Brown', address: '789 Pine Rd, City', payment: 'Paymongo', product: 'Classical Black Tiles', qty: 8, total: 900, status: 'Completed', date: '2023-10-12' },
    ],
    'Vanguard Branch': [
        { id: 3001, customer: 'Bob White', address: '321 Elm St, City', payment: 'Gcash', product: 'Floral Beige Green Tiles', qty: 12, total: 1500, status: 'Shipped', date: '2023-10-20' },
    ],
    'Deparo Branch': [
        { id: 4001, customer: 'Charlie Black', address: '654 Maple Ave, City', payment: 'Cash on Delivery', product: 'Leafy Rose Tiles', qty: 7, total: 800, status: 'Pending', date: '2023-10-22' },
    ],
    'Kiko Branch': [
        { id: 5001, customer: 'Daisy Green', address: '987 Cedar Rd, City', payment: 'Paymongo', product: 'Marble White Tiles', qty: 15, total: 1800, status: 'Completed', date: '2023-10-17' },
    ],
};


const userBranch = <?php echo json_encode($user_branch_name ?? ''); ?>;
const viewBtns = document.querySelectorAll('.view-orders-btn');
const ordersModal = document.getElementById('ordersModal');
const closeOrdersModalBtn = document.getElementById('closeOrdersModal');
const branchNameSpan = document.getElementById('branchName');
const ordersTableContainer = document.getElementById('ordersTableContainer');

const receiptModal = document.getElementById('receiptModal');
const closeReceiptModalBtn = document.getElementById('closeReceiptModal');
const receiptContent = document.getElementById('receiptContent');

viewBtns.forEach(btn => {
    btn.addEventListener('click', function(e) {
        const branch = this.getAttribute('data-branch');
        if (branch !== userBranch) {
            Swal.fire({
                icon: 'error',
                title: 'Access Denied',
                text: 'You can only view orders for your assigned branch (' + userBranch + ').',
                confirmButtonColor: '#8A421D',
            });
            e.preventDefault();
            return;
        }
        branchNameSpan.textContent = branch;
        ordersModal.classList.remove('hidden');
        renderOrdersTable(branch);
    });
});

closeOrdersModalBtn.addEventListener('click', () => {
    ordersModal.classList.add('hidden');
});

closeReceiptModalBtn.addEventListener('click', () => {
    receiptModal.classList.add('hidden');
});

function renderOrdersTable(branch) {
    // Only show non-completed orders
    const orders = (ordersData[branch] || []).filter(order => order.status !== 'Completed');
    if (orders.length === 0) {
        ordersTableContainer.innerHTML = '<div class="text-gray-500 text-center py-8">No pending or active orders for this branch.</div>';
        return;
    }
    let table = `<div class="overflow-x-auto"><table class="min-w-full text-sm text-left border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-blue-50">
            <tr>
                <th class="px-4 py-2 border-b border-gray-200 text-blue-900 font-semibold">Order ID</th>
                <th class="px-4 py-2 border-b border-gray-200 text-blue-900 font-semibold">Customer</th>
                <th class="px-4 py-2 border-b border-gray-200 text-blue-900 font-semibold">Product</th>
                <th class="px-4 py-2 border-b border-gray-200 text-blue-900 font-semibold">Qty</th>
                <th class="px-4 py-2 border-b border-gray-200 text-blue-900 font-semibold">Total</th>
                <th class="px-4 py-2 border-b border-gray-200 text-blue-900 font-semibold">Status</th>
                <th class="px-4 py-2 border-b border-gray-200 text-blue-900 font-semibold">Action</th>
            </tr>
        </thead>
        <tbody class="bg-white">`;
    orders.forEach((order, idx) => {
        let statusClass = 'status-pending';
        if (order.status === 'Shipped') statusClass = 'status-shipped';
        // Only allow process for non-completed
        let actionBtn = '';
        if (["Pending", "Shipped", "On the Way"].includes(order.status)) {
            actionBtn = `<button class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700 text-xs font-semibold transition process-order-btn" data-order='${JSON.stringify(order)}'>Process</button>`;
        }
        table += `<tr class="hover:bg-blue-50">
            <td class="px-4 py-2 border-b border-gray-200 font-mono">#${order.id}</td>
            <td class="px-4 py-2 border-b border-gray-200">${order.customer}</td>
            <td class="px-4 py-2 border-b border-gray-200">${order.product}</td>
            <td class="px-4 py-2 border-b border-gray-200 text-center">${order.qty}</td>
            <td class="px-4 py-2 border-b border-gray-200 font-medium">₱${order.total.toLocaleString()}</td>
            <td class="px-4 py-2 border-b border-gray-200"><span class="${statusClass} px-2 py-1 rounded-full text-xs">${order.status}</span></td>
            <td class="px-4 py-2 border-b border-gray-200 text-center">${actionBtn}</td>
        </tr>`;
    });
    table += '</tbody></table></div>';
    ordersTableContainer.innerHTML = table;

    // Add event listeners for process buttons
    document.querySelectorAll('.process-order-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const order = JSON.parse(this.getAttribute('data-order'));
            ordersModal.classList.add('hidden');
            showReceiptModal(order);
        });
    });
}

function showReceiptModal(order) {
    // Demo: add mobile, email, and referralCoins to order if not present
    order.mobile = order.mobile || '09XX-XXX-XXXX';
    order.email = order.email || 'customer@email.com';
    order.referralCoins = order.referralCoins !== undefined ? order.referralCoins : 0;
    const unitPrice = (order.total / order.qty).toFixed(2);
    const shippingFee = order.total > 1000 ? 0 : 150;
    // Referral coins can be up to 10 pesos
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
        'Kiko Branch': {
            address: 'Kiko, Camarin Road, Caloocan City',
        },
    };
    // Find branch name from order (fallback to empty if not found)
    let branchName = '';
    let branchAddress = '';
    for (const branch in ordersData) {
        if (ordersData[branch].some(o => o.id === order.id)) {
            branchName = branch;
            branchAddress = branchInfo[branch]?.address || '';
            break;
        }
    }
    const content = `
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
                        <span class="font-medium">${order.status}</span>
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
            <form class="space-y-4 pt-4 border-t-2 border-[#94481b]/30 mt-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Update Order Status</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none">
                        <option value="Pending" ${order.status === 'Pending' ? 'selected' : ''}>Pending</option>
                        <option value="Shipped" ${order.status === 'Shipped' ? 'selected' : ''}>Shipped</option>
                        <option value="On the Way" ${order.status === 'On the Way' ? 'selected' : ''}>On the Way</option>
                        <option value="Cancelled" ${order.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" id="closeReceiptModalBtn" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 font-medium">Close</button>
                    <button type="button" class="px-5 py-2 rounded-lg bg-gray-600 text-white font-semibold hover:bg-gray-700 shadow">
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    `;
    receiptContent.innerHTML = content;
    receiptModal.classList.remove('hidden');
    
    // Add event listeners for the new buttons within the modal content
    document.getElementById('closeReceiptModalBtn').addEventListener('click', () => receiptModal.classList.add('hidden'));
    
    // Prevent form submit (demo only)
    document.querySelector('#receiptContent form').addEventListener('submit', e => { 
        e.preventDefault(); 
        receiptModal.classList.add('hidden');
        alert('Order status updated successfully!');
    });
}

// Optional: close modals on ESC key
window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!ordersModal.classList.contains('hidden')) {
            ordersModal.classList.add('hidden');
        }
        if (!receiptModal.classList.contains('hidden')) {
            receiptModal.classList.add('hidden');
        }
    }
});
</script>
</body>
</html>