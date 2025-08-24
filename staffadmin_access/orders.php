<?php include '../includes/sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        <i class="fas fa-clipboard-list mr-3 text-blue-600"></i>Orders Dashboard
                    </h1>
                </div>

                <!-- Branches Overview -->
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
                    <?php
                    $branches = [
                        ['name' => 'Brixton Branch', 'icon' => 'fa-store', 'color' => 'blue'],
                        ['name' => 'Samaria Branch', 'icon' => 'fa-store', 'color' => 'green'],
                        ['name' => 'Vanguard Branch', 'icon' => 'fa-store', 'color' => 'amber'],
                        ['name' => 'Deparo Branch', 'icon' => 'fa-store', 'color' => 'purple'],
                        ['name' => 'Kiko Branch', 'icon' => 'fa-store', 'color' => 'red'],
                    ];
                    foreach ($branches as $i => $branch): ?>
                    <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center border border-<?= $branch['color'] ?>-100 relative hover:shadow-2xl">
                        <div class="rounded-full bg-<?= $branch['color'] ?>-100 p-4 mb-3">
                            <i class="fas <?= $branch['icon'] ?> text-2xl text-<?= $branch['color'] ?>-600"></i>
                        </div>
                        <div class="font-bold text-lg text-gray-900 mb-2 text-center tracking-wide"><?= $branch['name'] ?></div>
                        <button class="view-orders-btn bg-<?= $branch['color'] ?>-600 hover:bg-<?= $branch['color'] ?>-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition flex items-center mt-2" data-branch="<?= $branch['name'] ?>">
                            <i class="fas fa-eye mr-2"></i> View Orders
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Orders Modal -->
                <div id="ordersModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-8 relative">
                        <button id="closeOrdersModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl focus:outline-none"><i class="fas fa-times"></i></button>
                        <h2 id="modalBranchTitle" class="text-2xl font-bold text-gray-800 mb-6 flex items-center"><i class="fas fa-store mr-2 text-blue-600"></i>Orders for <span class="ml-2" id="branchName"></span></h2>
                        <div id="ordersTableContainer">
                            <!-- Orders table will be injected here -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
<script>
// Dummy orders data for each branch
const ordersData = {
    'Brixton Branch': [
        { id: 1, customer: 'John Doe', product: 'Tile A', qty: 10, total: 1200, status: 'Completed' },
        { id: 2, customer: 'Jane Smith', product: 'Tile B', qty: 5, total: 600, status: 'Pending' },
    ],
    'Samaria Branch': [
        { id: 3, customer: 'Alice Brown', product: 'Tile C', qty: 8, total: 900, status: 'Completed' },
    ],
    'Vanguard Branch': [
        { id: 4, customer: 'Bob White', product: 'Tile D', qty: 12, total: 1500, status: 'Shipped' },
    ],
    'Deparo Branch': [
        { id: 5, customer: 'Charlie Black', product: 'Tile E', qty: 7, total: 800, status: 'Pending' },
    ],
    'Kiko Branch': [
        { id: 6, customer: 'Daisy Green', product: 'Tile F', qty: 15, total: 1800, status: 'Completed' },
    ],
};

const viewBtns = document.querySelectorAll('.view-orders-btn');
const modal = document.getElementById('ordersModal');
const closeModalBtn = document.getElementById('closeOrdersModal');
const branchNameSpan = document.getElementById('branchName');
const modalBranchTitle = document.getElementById('modalBranchTitle');
const ordersTableContainer = document.getElementById('ordersTableContainer');

viewBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        const branch = this.getAttribute('data-branch');
        branchNameSpan.textContent = branch;
        modal.classList.remove('hidden');
        renderOrdersTable(branch);
    });
});

closeModalBtn.addEventListener('click', () => {
    modal.classList.add('hidden');
});

function renderOrdersTable(branch) {
    const orders = ordersData[branch] || [];
    if (orders.length === 0) {
        ordersTableContainer.innerHTML = '<div class="text-gray-500 text-center py-8">No orders for this branch.</div>';
        return;
    }
    let table = `<div class="overflow-x-auto"><table class="min-w-full text-sm text-left border">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border">Order ID</th>
                <th class="px-4 py-2 border">Customer</th>
                <th class="px-4 py-2 border">Product</th>
                <th class="px-4 py-2 border">Quantity</th>
                <th class="px-4 py-2 border">Total</th>
                <th class="px-4 py-2 border">Status</th>
            </tr>
        </thead>
        <tbody>`;
    orders.forEach(order => {
        table += `<tr>
            <td class="px-4 py-2 border">${order.id}</td>
            <td class="px-4 py-2 border">${order.customer}</td>
            <td class="px-4 py-2 border">${order.product}</td>
            <td class="px-4 py-2 border">${order.qty}</td>
            <td class="px-4 py-2 border">₱${order.total}</td>
            <td class="px-4 py-2 border">${order.status}</td>
        </tr>`;
    });
    table += '</tbody></table></div>';
    ordersTableContainer.innerHTML = table;
}
// Optional: close modal on ESC key
window.addEventListener('keydown', function(e) {
    if (!modal.classList.contains('hidden') && e.key === 'Escape') {
        modal.classList.add('hidden');
    }
});
</script>
</body>
</html>
