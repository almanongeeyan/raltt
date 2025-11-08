<?php
session_start();
require_once '../connection/connection.php';
$conn = $db_connection;

// --- CSRF Token Generation ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// --- POST Request Handler (Activate/Deactivate) ---
// This logic now runs *before* any HTML is sent
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_account_id'], $_POST['new_status'], $_POST['csrf_token'])) {
    
    // 1. Validate CSRF token
    if (!hash_equals($csrf_token, $_POST['csrf_token'])) {
        // CSRF attack or stale token
        $_SESSION['flash_message'] = "Invalid request. Please try again.";
        $_SESSION['flash_type'] = 'error';
    } else {
        // 2. Process the request
        try {
            $toggle_id = intval($_POST['toggle_account_id']);
            // Whitelist the new status to be safe
            $new_status = ($_POST['new_status'] === 'active') ? 'active' : 'inactive'; 

            $update = $conn->prepare("UPDATE users SET account_status = ? WHERE id = ? AND user_role = 'CUSTOMER'");
            $update->execute([$new_status, $toggle_id]);

            if ($update->rowCount() > 0) {
                $_SESSION['flash_message'] = "Account status updated successfully for user ID #$toggle_id.";
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = "Could not find user ID #$toggle_id or status was already set.";
                $_SESSION['flash_type'] = 'error';
            }
        } catch (PDOException $e) {
            $_SESSION['flash_message'] = "Database error: " . $e->getMessage();
            $_SESSION['flash_type'] = 'error';
        }
    }

    // 3. PRG Pattern: Redirect to self to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// --- Flash Message Retrieval (for displaying after redirect) ---
$flash_message = null;
if (isset($_SESSION['flash_message'])) {
    $flash_message = $_SESSION['flash_message'];
    $flash_type = $_SESSION['flash_type'] ?? 'success';
    // Clear the message so it doesn't show again
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

// --- Data Fetching (GET Request) ---
$customer_accounts = [];
try {
    $sql = "SELECT id, full_name, email, phone_number, user_role, account_status, created_at FROM users WHERE user_role = 'CUSTOMER' ORDER BY created_at DESC";
    $stmt = $conn->query($sql);
    if ($stmt) {
        $customer_accounts = $stmt->fetchAll(PDO::FETCH_ASSOC); // Use FETCH_ASSOC
    }
} catch (PDOException $e) {
    // Set a flash message if data fetching fails
    $flash_message = "Error fetching customer data: " . $e->getMessage();
    $flash_type = 'error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customer Accounts - Rich Anne Tiles</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': {
                            '50': '#fef8f6', '100': '#fdf0ec', '200': '#fbd9cc', '300': '#f8c2ad',
                            '400': '#f2946f', '500': '#ed6631', '600': '#d55c2c', '700': '#b24d25',
                            '800': '#8e3d1d', '900': '#743218'
                        },
                        'accent': {
                            '50': '#f6f6f6', '100': '#e7e7e7', '200': '#d1d1d1', '300': '#b0b0b0',
                            '400': '#888888', '500': '#6d6d6d', '600': '#5d5d5d', '700': '#4f4f4f',
                            '800': '#454545', '900': '#3d3d3d'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Montserrat', 'sans-serif'],
                    },
                    boxShadow: {
                        'soft': '0 2px 15px rgba(0, 0, 0, 0.05)',
                        'medium': '0 5px 25px rgba(0, 0, 0, 0.08)',
                    }
                }
            }
        }
    </script>
    
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 80px;
            --transition-speed: 0.3s;
        }
        body {
            transition: padding-left var(--transition-speed);
            background: linear-gradient(135deg, #fef8f6 0%, #fdf0ec 100%);
            font-family: 'Inter', sans-serif;
            color: #4f4f4f; /* accent-700 */
        }
        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left var(--transition-speed);
        }
        html.sidebar-collapsed .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }
        /* Style for the 'no results' row */
        #no-results-row.hidden {
            display: none;
        }
    </style>
</head>
<body class="font-sans">

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content p-6">

        <div class="dashboard-card bg-white rounded-2xl p-6 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center shadow-medium">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl font-bold text-accent-900 font-heading">
                    Manage Customer Accounts
                </h1>
                <p class="text-accent-600">View and manage all registered customers.</p>
            </div>
        </div>


        <!-- Toast Container -->
        <div id="toast-container" class="fixed top-6 right-6 z-[70] w-full max-w-xs space-y-3"></div>

        <div class="dashboard-card bg-white rounded-2xl p-6 shadow-medium">
            
            <div class="flex flex-col md:flex-row justify-between items-center mb-5 gap-3">
                <h2 class="text-xl font-semibold text-accent-900 font-heading mb-3 md:mb-0">
                    All Customer Accounts
                </h2>
                <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto items-center">
                    <div class="relative w-full md:w-72">
                        <input type="text" id="searchInput" class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300" placeholder="Search by name, email, or phone...">
                        <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <select id="statusFilter" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-white">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="customer-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (count($customer_accounts) === 0): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fa fa-users text-3xl text-gray-400 mb-2"></i>
                                    <p>No customer accounts found.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($customer_accounts as $customer): ?>
                                <tr class="hover:bg-primary-50 transition-colors" data-searchable-row
                                    data-name="<?php echo htmlspecialchars(strtolower($customer['full_name'])); ?>"
                                    data-email="<?php echo htmlspecialchars(strtolower($customer['email'])); ?>"
                                    data-phone="<?php echo htmlspecialchars(strtolower($customer['phone_number'])); ?>">
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800"><?php echo htmlspecialchars($customer['id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium"><?php echo htmlspecialchars($customer['full_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($customer['email']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($customer['phone_number']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm status-cell">
                                        <?php if ($customer['account_status'] == 'active'): ?>
                                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Inactive
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars(date('M d, Y', strtotime($customer['created_at']))); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium action-cell">
                                        <div class="flex items-center gap-2">
                                            <button 
                                                class="view-cart-btn bg-primary-500 hover:bg-primary-600 text-white px-3 py-1.5 rounded-md shadow-soft transition-colors flex items-center gap-1.5 text-xs font-medium" 
                                                data-user-id="<?php echo htmlspecialchars($customer['id']); ?>"
                                                title="View Cart"
                                            >
                                                <i class="fa fa-shopping-cart w-3"></i> View Cart
                                            </button>
                                            <?php if ($customer['account_status'] == 'active'): ?>
                                                <button class="status-toggle-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md shadow-soft transition-colors flex items-center gap-1.5 text-xs font-medium" 
                                                    data-id="<?php echo htmlspecialchars($customer['id']); ?>" data-status="active" title="Deactivate">
                                                    <i class="fa fa-user-slash w-3"></i> Deactivate
                                                </button>
                                            <?php else: ?>
                                                <button class="status-toggle-btn bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md shadow-soft transition-colors flex items-center gap-1.5 text-xs font-medium" 
                                                    data-id="<?php echo htmlspecialchars($customer['id']); ?>" data-status="inactive" title="Activate">
                                                    <i class="fa fa-user-check w-3"></i> Activate
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
    <!-- Modal for Activate/Deactivate Confirmation -->
    <div id="confirmModal" class="modal-overlay fixed inset-0 z-[60] p-4 bg-gray-900 bg-opacity-60 flex items-center justify-center hidden">
        <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-sm">
            <div class="p-6 text-center">
                <div id="modalIconContainer" class="mb-4"></div>
                <h3 class="text-lg font-bold font-heading text-accent-900" id="modalMsg">Are you sure?</h3>
                <p class="text-sm text-accent-600 mt-2" id="modalSubMsg">This action will update the customer's account status.</p>
            </div>
            <div class="flex gap-3 p-4 bg-gray-50 rounded-b-2xl">
                <button id="modalCancelBtn" class="flex-1 px-4 py-2 text-sm font-medium text-accent-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition-all">Cancel</button>
                <button id="modalConfirmBtn" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-primary-500 border border-transparent rounded-lg shadow-sm hover:bg-primary-600 transition-all">Confirm</button>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- TOAST SYSTEM ---
        const toastContainer = document.getElementById('toast-container');
        function showToast(message, type = 'success') {
            const toastId = 'toast-' + Date.now();
            let iconHtml, classes;
            if (type === 'success') {
                iconHtml = '<i class="fas fa-check-circle"></i>';
                classes = 'bg-green-100 border-green-300 text-green-800';
            } else if (type === 'warning') {
                iconHtml = '<i class="fas fa-exclamation-triangle"></i>';
                classes = 'bg-yellow-100 border-yellow-300 text-yellow-800';
            } else {
                iconHtml = '<i class="fas fa-times-circle"></i>';
                classes = 'bg-red-100 border-red-300 text-red-800';
            }
            const toastHtml = `
                <div id="${toastId}" class="flex items-center gap-3 w-full p-4 rounded-lg shadow-lg border ${classes} transition-all duration-300 opacity-0 translate-x-10" role="alert">
                    <div class="text-lg">${iconHtml}</div>
                    <div class="text-sm font-semibold">${message}</div>
                    <button class="ml-auto -mr-1 text-lg opacity-70 hover:opacity-100" onclick="document.getElementById('${toastId}').remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            setTimeout(() => {
                const toast = document.getElementById(toastId);
                if(toast) {
                    toast.classList.remove('opacity-0', 'translate-x-10');
                }
            }, 10);
            setTimeout(() => {
                const toast = document.getElementById(toastId);
                if (toast) {
                    toast.classList.add('opacity-0');
                    setTimeout(() => toast.remove(), 300);
                }
            }, 3500);
        }

        // --- MODAL SYSTEM for Deactivate/Activate ---
        const confirmModal = document.getElementById('confirmModal');
        const modalMsg = document.getElementById('modalMsg');
        const modalSubMsg = document.getElementById('modalSubMsg');
        const modalConfirmBtn = document.getElementById('modalConfirmBtn');
        const modalCancelBtn = document.getElementById('modalCancelBtn');
        const modalIconContainer = document.getElementById('modalIconContainer');
        let currentModalContext = { action: null, id: null, button: null };

        // Event delegation for status toggle buttons
        document.querySelector('#customer-table').addEventListener('click', function(e) {
            const btn = e.target.closest('.status-toggle-btn');
            if (!btn) return;
            const id = btn.dataset.id;
            const currentStatus = btn.dataset.status;
            const action = (currentStatus === 'active') ? 'deactivate' : 'activate';
            currentModalContext = { action, id, button: btn };
            // Configure and show modal
            if (action === 'deactivate') {
                modalMsg.textContent = 'Deactivate Account?';
                modalSubMsg.textContent = 'This will mark the customer account as Inactive.';
                modalConfirmBtn.textContent = 'Deactivate';
                modalConfirmBtn.className = modalConfirmBtn.className.replace(/bg-primary-500|bg-green-600/g, 'bg-red-600').replace(/hover:bg-primary-600|hover:bg-green-700/g, 'hover:bg-red-700');
                modalIconContainer.innerHTML = '<i class="fas fa-user-slash text-5xl text-red-500"></i>';
            } else {
                modalMsg.textContent = 'Activate Account?';
                modalSubMsg.textContent = 'This will mark the customer account as Active.';
                modalConfirmBtn.textContent = 'Activate';
                modalConfirmBtn.className = modalConfirmBtn.className.replace(/bg-primary-500|bg-red-600/g, 'bg-green-600').replace(/hover:bg-primary-600|hover:bg-red-700/g, 'hover:bg-green-700');
                modalIconContainer.innerHTML = '<i class="fas fa-user-check text-5xl text-green-500"></i>';
            }
            confirmModal.classList.remove('hidden');
            confirmModal.classList.add('active');
        });

        function closeModal() {
            confirmModal.classList.add('hidden');
            confirmModal.classList.remove('active');
            modalConfirmBtn.className = modalConfirmBtn.className.replace(/bg-red-600|bg-green-600/g, 'bg-primary-500').replace(/hover:bg-red-700|hover:bg-green-700/g, 'hover:bg-primary-600');
        }
        modalCancelBtn.addEventListener('click', closeModal);
        confirmModal.addEventListener('click', function(e) {
            if (e.target === confirmModal) closeModal();
        });

        // Handle Confirm
        // Remove any previous event listeners to prevent multiple toasts
        modalConfirmBtn.replaceWith(modalConfirmBtn.cloneNode(true));
        const newModalConfirmBtn = document.getElementById('modalConfirmBtn');
        newModalConfirmBtn.addEventListener('click', function() {
            const { action, id, button } = currentModalContext;
            if (!action || !id || !button) return;
            const newStatus = (action === 'activate') ? 'active' : 'inactive';
            const csrfToken = <?php echo json_encode($csrf_token); ?>;
            const formData = new FormData();
            formData.append('toggle_account_id', id);
            formData.append('new_status', newStatus);
            formData.append('csrf_token', csrfToken);
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(() => {
                updateRowStatus(button, action);
                showToast('Account status updated successfully.', 'success');
            })
            .catch(() => showToast('Network error. Please try again.', 'error'))
            .finally(() => closeModal());
        });

        // --- Helper functions ---
        function updateRowStatus(button, action) {
            const row = button.closest('tr');
            if (!row) return;
            const statusCell = row.querySelector('.status-cell');
            if (action === 'deactivate') {
                statusCell.innerHTML = `<span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>`;
                button.textContent = 'Activate';
                button.dataset.status = 'inactive';
                button.className = button.className.replace(/bg-red-500|hover:bg-red-600/g, 'bg-green-500 hover:bg-green-600');
                button.innerHTML = '<i class="fa fa-user-check w-3"></i> Activate';
            } else {
                statusCell.innerHTML = `<span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>`;
                button.textContent = 'Deactivate';
                button.dataset.status = 'active';
                button.className = button.className.replace(/bg-green-500|hover:bg-green-600/g, 'bg-red-500 hover:bg-red-600');
                button.innerHTML = '<i class="fa fa-user-slash w-3"></i> Deactivate';
            }
        }
    });
    </script>
                                </tr>
                            <?php endforeach; ?>
                            
                            <tr id="no-results-row" class="hidden">
                                 <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fa fa-search text-3xl text-gray-400 mb-2"></i>
                                    <p>No accounts match your search.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> <div id="cartModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-40 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6 relative">
            <button id="closeCartModal" class="absolute top-3 right-4 text-gray-400 hover:text-red-500 text-3xl font-bold leading-none">&times;</button>
            <h3 class="text-2xl font-bold mb-4 text-primary-700 flex items-center gap-2"><i class="fa fa-shopping-cart"></i> Customer Cart</h3>
            <div id="cartModalContent" class="max-h-[60vh] overflow-y-auto">
                <div class="text-center text-gray-500">Loading...</div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- Flash Message Auto-hide ---
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.transition = 'opacity 0.5s ease';
                flashMessage.style.opacity = '0';
                setTimeout(() => flashMessage.remove(), 500);
            }, 5000); // Hide after 5 seconds
        }

        // --- Live Search & Status Filter ---
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const tableRows = document.querySelectorAll('[data-searchable-row]');
        const noResultsRow = document.getElementById('no-results-row');

        function filterRows() {
            const searchTerm = (searchInput.value || '').toLowerCase().trim();
            const statusVal = statusFilter.value;
            let visibleRows = 0;
            tableRows.forEach(row => {
                const name = row.getAttribute('data-name');
                const email = row.getAttribute('data-email');
                const phone = row.getAttribute('data-phone');
                // Find status from badge
                let status = '';
                const statusCell = row.querySelector('.status-cell');
                if (statusCell) {
                    const badge = statusCell.querySelector('span');
                    if (badge) status = badge.textContent.trim().toLowerCase();
                }
                let show = true;
                if (searchTerm && !(name.includes(searchTerm) || email.includes(searchTerm) || phone.includes(searchTerm))) show = false;
                if (statusVal && status !== statusVal) show = false;
                row.style.display = show ? '' : 'none';
                if (show) visibleRows++;
            });
            // Toggle the 'no results' row
            if (noResultsRow) {
                if (visibleRows === 0 && (searchInput.value.length > 0 || statusFilter.value.length > 0)) {
                    noResultsRow.classList.remove('hidden');
                } else {
                    noResultsRow.classList.add('hidden');
                }
            }
        }
        if (searchInput) searchInput.addEventListener('keyup', filterRows);
        if (statusFilter) statusFilter.addEventListener('change', filterRows);

        // --- View Cart Modal Logic ---
        const cartModal = document.getElementById('cartModal');
        const closeCartModal = document.getElementById('closeCartModal');
        const cartModalContent = document.getElementById('cartModalContent');

        document.querySelectorAll('.view-cart-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                cartModalContent.innerHTML = '<div class="text-center text-gray-500 p-8"><i class="fa fa-spinner fa-spin text-2xl"></i><p>Loading...</p></div>';
                cartModal.classList.remove('hidden');
                
                // Fetch cart data
                fetch(`../connection/get_user_cart.php?user_id=${userId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success' && data.items.length > 0) {
                            let html = `<div class="overflow-x-auto"><table class='min-w-full divide-y divide-gray-200'>
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class='px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase'>Image</th>
                                        <th class='px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase'>Product</th>
                                        <th class='px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase'>Price</th>
                                        <th class='px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase'>Qty</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                            
                            data.items.forEach(item => {
                                const placeholder = 'https://via.placeholder.com/60x60?text=No+Image';
                                const imgSrc = (item.product_image && item.product_image.trim() !== '') ? item.product_image : placeholder;
                                const price = parseFloat(item.product_price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                                html += `<tr class='hover:bg-primary-50'>` +
                                    `<td class='px-4 py-2'><img src='${imgSrc}' alt='${item.product_name}' class='w-16 h-16 object-cover rounded shadow' /></td>` +
                                    `<td class='px-4 py-2 font-semibold text-accent-900'>${item.product_name}</td>` +
                                    `<td class='px-4 py-2 text-primary-700'>₱${price}</td>` +
                                    `<td class='px-4 py-2'>${item.quantity}</td>` +
                                `</tr>`;
                            });
                            html += '</tbody></table></div>';
                            cartModalContent.innerHTML = html;
                        } else {
                            cartModalContent.innerHTML = '<div class="text-center text-gray-500 p-8"><i class="fa fa-shopping-cart text-2xl mb-2"></i><p>This customer\'s cart is empty.</p></div>';
                        }
                    })
                    .catch((error) => {
                        console.error('Error fetching cart:', error);
                        cartModalContent.innerHTML = '<div class="text-center text-red-500 p-8"><i class="fa fa-exclamation-triangle text-2xl mb-2"></i><p>Failed to load cart. Please try again.</p></div>';
                    });
            });
        });

        // Close modal listeners
        closeCartModal.addEventListener('click', () => cartModal.classList.add('hidden'));
        window.addEventListener('click', (e) => {
            if (e.target === cartModal) cartModal.classList.add('hidden');
        });
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !cartModal.classList.contains('hidden')) {
                cartModal.classList.add('hidden');
            }
        });

    });
    </script>

</body>
</html>