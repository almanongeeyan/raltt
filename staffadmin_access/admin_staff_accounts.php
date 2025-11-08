<?php
// START SESSION AT THE VERY TOP
session_start();

include '../includes/sidebar.php';
require_once '../connection/connection.php';

$conn = $db_connection;

// --- Main Page Logic: Fetch Staff Accounts ---
$staff_accounts = [];
$branches = [];

try {
    // Fetch branches for dropdown
    $stmt_branches = $conn->query("SELECT branch_id, branch_name FROM branches ORDER BY branch_name ASC");
    if ($stmt_branches) {
        $branches = $stmt_branches->fetchAll();
    }

    // Filter staff by branch for non-admins
    $where = "user_role IN ('ADMIN','ENCODER','CASHIER','DRIVER')";
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] !== 'ADMIN' && isset($_SESSION['branch_id'])) {
        $branch_id = (int)$_SESSION['branch_id'];
        $where .= " AND branch_id = $branch_id";
    }
    // Exclude current admin from the list
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN' && isset($_SESSION['admin_id'])) {
        $admin_id = (int)$_SESSION['admin_id'];
        $where .= " AND u.id != $admin_id";
    }
    
    $sql = "SELECT u.id, u.full_name, u.email, u.phone_number, u.user_role, u.account_status, u.created_at, u.branch_id, b.branch_name FROM users u LEFT JOIN branches b ON u.branch_id = b.branch_id WHERE $where ORDER BY u.created_at DESC";
    $stmt = $conn->query($sql);
    if ($stmt) {
        $staff_accounts = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $staff_accounts = [];
    $branches = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff Accounts - Rich Anne Tiles</title>
    
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
            color: #4f4f4f;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left var(--transition-speed);
        }
        html.sidebar-collapsed .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }
        .drawer {
            transition: transform 0.35s cubic-bezier(.4,0,.2,1), box-shadow 0.3s;
            transform: translateX(100%);
            box-shadow: 0 8px 32px 0 rgba(60, 60, 60, 0.18);
        }
        .drawer.is-open {
            transform: translateX(0);
            box-shadow: 0 8px 32px 0 rgba(60, 60, 60, 0.22);
        }
        .drawer-overlay {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }
        .drawer-overlay:not(.hidden) {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-overlay {
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }
        .modal-content {
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body class="font-sans">

    <div class="main-content p-6">

        <div class="dashboard-card bg-white rounded-2xl p-6 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center shadow-medium">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl font-bold text-accent-900 font-heading">
                    Manage Staff Accounts
                </h1>
                <p class="text-accent-600">Add, edit, or deactivate staff members.</p>
            </div>
            <button id="openDrawerBtn" class="flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-white bg-primary-500 rounded-lg shadow-soft hover:bg-primary-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all">
                <i class="fas fa-plus-circle text-xs"></i>
                Add Staff Account
            </button>
        </div>

        <div class="dashboard-card bg-white rounded-2xl p-6 shadow-medium">
            <h2 class="text-xl font-semibold text-accent-900 font-heading mb-5">All Staff Accounts</h2>
            <!-- Filter Bar -->
            <div class="flex flex-wrap gap-4 mb-6 items-end bg-gray-50 p-4 rounded-xl border border-gray-200 shadow-sm">
                <div class="flex flex-col">
                    <label for="filterName" class="block text-xs font-semibold text-gray-700 mb-1">Search Name</label>
                    <input id="filterName" type="text" placeholder="Search by name..." class="block w-48 rounded-lg border border-gray-300 px-3 py-2 focus:border-primary-500 focus:ring-primary-500 text-sm bg-white">
                </div>
                <div class="flex flex-col">
                    <label for="filterStatus" class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                    <select id="filterStatus" class="block w-36 rounded-lg border border-gray-300 px-3 py-2 focus:border-primary-500 focus:ring-primary-500 text-sm bg-white">
                        <option value="">All</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="filterRole" class="block text-xs font-semibold text-gray-700 mb-1">Role</label>
                    <select id="filterRole" class="block w-36 rounded-lg border border-gray-300 px-3 py-2 focus:border-primary-500 focus:ring-primary-500 text-sm bg-white">
                        <option value="">All</option>
                        <option value="ADMIN">Admin</option>
                        <option value="ENCODER">Encoder</option>
                        <option value="CASHIER">Cashier</option>
                        <option value="DRIVER">Driver</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="filterBranch" class="block text-xs font-semibold text-gray-700 mb-1">Branch</label>
                    <select id="filterBranch" class="block w-36 rounded-lg border border-gray-300 px-3 py-2 focus:border-primary-500 focus:ring-primary-500 text-sm bg-white">
                        <option value="">All</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?php echo htmlspecialchars($branch['branch_id']); ?>"><?php echo htmlspecialchars(ucwords(strtolower($branch['branch_name']))); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="staff-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (count($staff_accounts) === 0): ?>
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                    No staff accounts found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($staff_accounts as $staff): ?>
                                <tr class="hover:bg-primary-50 transition-colors" data-row-id="<?php echo htmlspecialchars($staff['id']); ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800"><?php echo htmlspecialchars($staff['id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium"><?php echo htmlspecialchars($staff['full_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($staff['email']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($staff['phone_number']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars(ucwords(strtolower($staff['user_role']))); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700" data-branch-id="<?php echo htmlspecialchars($staff['branch_id'] ?? ''); ?>"><?php echo htmlspecialchars(ucwords(strtolower($staff['branch_name'] ?? ''))); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm status-cell">
                                        <?php if ($staff['account_status'] == 'active'): ?>
                                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Inactive
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars(date('M d, Y', strtotime($staff['created_at']))); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium action-cell">
                                        <button class="text-primary-600 hover:text-primary-800 transition-colors mr-3 action-btn edit" 
                                                data-id="<?php echo htmlspecialchars($staff['id']); ?>"
                                                data-full_name="<?php echo htmlspecialchars($staff['full_name']); ?>"
                                                data-email="<?php echo htmlspecialchars($staff['email']); ?>"
                                                data-phone="<?php echo htmlspecialchars($staff['phone_number']); ?>">
                                            Edit
                                        </button>
                                        <?php if ($staff['account_status'] == 'active'): ?>
                                            <button class="text-red-600 hover:text-red-800 transition-colors action-btn status-toggle" 
                                                    data-id="<?php echo htmlspecialchars($staff['id']); ?>" 
                                                    data-status="active">
                                                Deactivate
                                            </button>
                                        <?php else: ?>
                                            <button class="text-green-600 hover:text-green-800 transition-colors action-btn status-toggle" 
                                                    data-id="<?php echo htmlspecialchars($staff['id']); ?>" 
                                                    data-status="inactive">
                                                Activate
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal (SINGLE INSTANCE - placed outside the table loop) -->
    <div id="editStaffModal" class="fixed inset-0 z-[100] bg-gray-900 bg-opacity-60 flex items-center justify-center hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 relative">
            <button id="closeEditModalBtn" class="absolute top-4 right-4 text-accent-400 hover:text-primary-500 text-2xl"><i class="fas fa-times"></i></button>
            <h2 class="text-2xl font-bold mb-6 text-accent-900">Edit Staff Account</h2>
            <form id="editStaffForm" class="space-y-6">
                <input type="hidden" id="edit_user_id" name="user_id">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
                        <i class="fas fa-user"></i> Full Name
                        <button type="button" class="ml-2 text-accent-400 hover:text-primary-500 edit-field-btn" data-field="edit_full_name">
                            <i class="fas fa-edit"></i>
                        </button>
                    </label>
                    <input type="text" id="edit_full_name" name="full_name" class="block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 text-base transition-all" disabled>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
                        <i class="fas fa-envelope"></i> Email Address
                        <button type="button" class="ml-2 text-accent-400 hover:text-primary-500 edit-field-btn" data-field="edit_email">
                            <i class="fas fa-edit"></i>
                        </button>
                    </label>
                    <input type="email" id="edit_email" name="email" class="block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 text-base transition-all" disabled>
                    <span id="edit_email_error" class="text-red-600 text-xs mt-1 hidden">Invalid email address.</span>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
                        <i class="fas fa-phone"></i> Phone Number
                        <button type="button" class="ml-2 text-accent-400 hover:text-primary-500 edit-field-btn" data-field="edit_phone">
                            <i class="fas fa-edit"></i>
                        </button>
                    </label>
                    <input type="tel" id="edit_phone" name="phone" maxlength="13" pattern="\+639[0-9]{9}" class="block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 text-base transition-all" disabled>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
                        <i class="fas fa-lock"></i> Password
                        <button type="button" class="ml-2 text-accent-400 hover:text-primary-500 edit-field-btn" data-field="edit_password">
                            <i class="fas fa-edit"></i>
                        </button>
                    </label>
                    <div class="relative">
                        <input type="password" id="edit_password" name="password" class="block w-full rounded-lg border border-gray-300 pr-10 px-4 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 text-base transition-all" disabled>
                        <button type="button" tabindex="-1" class="absolute right-3 top-1/2 -translate-y-1/2 text-accent-400 hover:text-primary-500 focus:outline-none" onclick="togglePasswordVisibility('edit_password', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
                        <i class="fas fa-user-tag"></i> Role
                        <button type="button" class="ml-2 text-accent-400 hover:text-primary-500 edit-field-btn" data-field="edit_role">
                            <i class="fas fa-edit"></i>
                        </button>
                    </label>
                    <select id="edit_role" name="role" class="block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 text-base transition-all" disabled>
                        <option value="ENCODER">Encoder</option>
                        <option value="CASHIER">Cashier</option>
                        <option value="DRIVER">Driver</option>
                    </select>
                </div>
                <button type="submit" id="saveEditBtn" class="w-full py-3 rounded-lg bg-primary-500 text-white font-bold text-lg shadow-md hover:bg-primary-600 transition-all disabled:opacity-50" disabled>
                    Save Changes
                </button>
            </form>
        </div>
    </div>

    <!-- Redesigned Drawer for Add Staff Account -->
    <div id="drawer" class="drawer fixed inset-y-0 right-0 z-50 w-full max-w-md bg-white shadow-2xl rounded-l-2xl border-l border-gray-200 transition-transform duration-300">
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-primary-50 to-white rounded-tl-2xl">
                <span class="text-2xl font-bold font-heading text-accent-900" id="drawer-title">Add Staff Account</span>
                <button id="closeDrawerBtn" class="text-accent-400 hover:text-primary-500 transition-colors rounded-full p-2 focus:outline-none focus:ring-2 focus:ring-primary-300">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <form id="addStaffForm" class="flex-1 px-8 py-6 space-y-5 overflow-y-auto bg-white" autocomplete="off" novalidate>
                <div>
                    <label for="drawer_full_name" class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2"><span class="text-accent-400"><i class="fas fa-user"></i></span>Full Name</label>
                    <input type="text" id="drawer_full_name" name="full_name" required class="block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 text-base transition-all">
                </div>
                <div>
                    <label for="drawer_email" class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2"><span class="text-accent-400"><i class="fas fa-envelope"></i></span>Gmail Address</label>
                    <div class="flex gap-2 items-center">
                        <input type="email" id="drawer_email" name="email" required placeholder="example@gmail.com" class="block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 text-base transition-all">
                        <button type="button" id="sendCodeBtn" class="px-4 py-2 rounded-lg bg-primary-500 text-white font-semibold text-sm hover:bg-primary-600 transition-all">Send Code</button>
                        <span id="emailValidatedBadge" class="hidden ml-2 px-3 py-1 rounded-lg bg-green-100 text-green-700 text-xs font-semibold flex items-center gap-1"><i class="fas fa-check-circle"></i> Validated</span>
                    </div>
                </div>

                <!-- Email Verification Modal -->
                <div id="emailCodeModal" class="modal-overlay fixed inset-0 z-[70] p-4 bg-gray-900 bg-opacity-60 flex items-center justify-center hidden">
                    <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-xs">
                        <div class="p-6 text-center">
                            <div class="mb-4"><i class="fas fa-envelope-open-text text-4xl text-primary-500"></i></div>
                            <h3 class="text-lg font-bold font-heading text-accent-900">Enter Verification Code</h3>
                            <p class="text-sm text-accent-600 mt-2">A 6-digit code was sent to your email.</p>
                            <input id="emailCodeInput" type="text" maxlength="6" pattern="\d{6}" class="mt-4 block w-full rounded-lg border border-gray-300 px-4 py-2 text-center text-lg tracking-widest focus:border-primary-500 focus:ring-2 focus:ring-primary-100" placeholder="------">
                            <div id="emailCodeError" class="text-red-600 text-xs mt-2 hidden"></div>
                        </div>
                        <div class="flex gap-3 p-4 bg-gray-50 rounded-b-2xl">
                            <button id="emailCodeCancelBtn" class="flex-1 px-4 py-2 text-sm font-medium text-accent-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition-all">Cancel</button>
                            <button id="emailCodeVerifyBtn" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-primary-500 border border-transparent rounded-lg shadow-sm hover:bg-primary-600 transition-all">Verify</button>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="drawer_phone" class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2"><span class="text-accent-400"><i class="fas fa-phone"></i></span>Phone Number</label>
                    <input type="tel" id="drawer_phone" name="phone" maxlength="13" required placeholder="+639XXXXXXXXX" pattern="\+639[0-9]{9}" class="block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 text-base transition-all">
                </div>
                <div class="flex gap-4">
                    <div class="flex-1">
                        <label for="drawer_password" class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2"><span class="text-accent-400"><i class="fas fa-lock"></i></span>Password</label>
                        <div class="relative">
                            <input type="password" id="drawer_password" name="password" required class="block w-full rounded-lg border border-gray-300 pr-10 px-4 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 text-base transition-all">
                            <button type="button" tabindex="-1" class="absolute right-3 top-1/2 -translate-y-1/2 text-accent-400 hover:text-primary-500 focus:outline-none" onclick="togglePasswordVisibility('drawer_password', this)"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="flex-1">
                        <label for="drawer_confirm_password" class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2"><span class="text-accent-400"><i class="fas fa-lock"></i></span>Confirm Password</label>
                        <div class="relative">
                            <input type="password" id="drawer_confirm_password" name="confirm_password" required class="block w-full rounded-lg border border-gray-300 pr-10 px-4 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 text-base transition-all">
                            <button type="button" tabindex="-1" class="absolute right-3 top-1/2 -translate-y-1/2 text-accent-400 hover:text-primary-500 focus:outline-none" onclick="togglePasswordVisibility('drawer_confirm_password', this)"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="drawer_user_role" class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2"><span class="text-accent-400"><i class="fas fa-user-tag"></i></span>User Role</label>
                    <select id="drawer_user_role" name="user_role" required class="block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 text-base transition-all">
                        <option value="">-- Select Role --</option>
                        <option value="ADMIN">Admin</option>
                        <option value="ENCODER">Encoder</option>
                        <option value="CASHIER">Cashier</option>
                        <option value="DRIVER">Driver</option>
                    </select>
                </div>
                <div id="branchDropdownContainer" style="display:none;">
                    <label for="drawer_branch_id" class="block text-sm font-semibold text-gray-700 mb-1">Assign Branch</label>
                    <select id="drawer_branch_id" name="branch_id" class="block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 text-base transition-all">
                        <option value="">-- Select Branch --</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?php echo htmlspecialchars($branch['branch_id']); ?>"><?php echo htmlspecialchars($branch['branch_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
            <div class="px-8 py-5 border-t border-gray-100 bg-gradient-to-r from-primary-50 to-white rounded-bl-2xl">
                <button type="submit" form="addStaffForm" id="submitAddStaff" class="w-full flex items-center justify-center gap-2 px-6 py-3 text-base font-bold text-white bg-primary-500 rounded-lg shadow-soft hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-300 transition-all">
                    <i class="fas fa-user-plus mr-2"></i> Add Staff
                </button>
            </div>
        </div>
    </div>
    <div id="drawer-overlay" class="drawer-overlay fixed inset-0 z-40 bg-gray-900 bg-opacity-60 transition-opacity duration-300 hidden" aria-hidden="true"></div>

    <div id="confirmModal" class="modal-overlay fixed inset-0 z-[60] p-4 bg-gray-900 bg-opacity-60 flex items-center justify-center hidden">
        <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-sm">
            <div class="p-6 text-center">
                <div id="modalIconContainer" class="mb-4"></div>
                <h3 class="text-lg font-bold font-heading text-accent-900" id="modalMsg">Are you sure?</h3>
                <p class="text-sm text-accent-600 mt-2" id="modalSubMsg">This action will update the staff's account status.</p>
            </div>
            <div class="flex gap-3 p-4 bg-gray-50 rounded-b-2xl">
                <button id="modalCancelBtn" class="flex-1 px-4 py-2 text-sm font-medium text-accent-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition-all">
                    Cancel
                </button>
                <button id="modalConfirmBtn" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-primary-500 border border-transparent rounded-lg shadow-sm hover:bg-primary-600 transition-all">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <div id="toast-container" class="fixed top-6 right-6 z-[70] w-full max-w-xs space-y-3"></div>

    <script>
        // Global functions
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toast-container');
            const toastId = 'toast-' + Date.now();
            let iconHtml, classes;

            if (type === 'success') {
                iconHtml = '<i class="fas fa-check-circle"></i>';
                classes = 'bg-green-100 border-green-300 text-green-800';
            } else if (type === 'warning') {
                iconHtml = '<i class="fas fa-exclamation-triangle"></i>';
                classes = 'bg-yellow-100 border-yellow-300 text-yellow-800';
            } else { // error
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
            
            // For fade-in animation
            setTimeout(() => {
                const toast = document.getElementById(toastId);
                if(toast) {
                    toast.classList.remove('opacity-0', 'translate-x-10');
                }
            }, 10);

            // Auto-dismiss
            setTimeout(() => {
                const toast = document.getElementById(toastId);
                if (toast) {
                    toast.classList.add('opacity-0');
                    setTimeout(() => toast.remove(), 300);
                }
            }, 3500);
        }

        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text.replace(/[&<>"']/g, function(m) {
                return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m];
            });
        }

        function formatDate(dateStr) {
            if (!dateStr) return 'N/A';
            const d = new Date(dateStr);
            return d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
        }

        // Main JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // --- Real-time filter logic ---
            const filterName = document.getElementById('filterName');
            const filterStatus = document.getElementById('filterStatus');
            const filterRole = document.getElementById('filterRole');
            const filterBranch = document.getElementById('filterBranch');
            const staffTable = document.getElementById('staff-table');

            function filterTable() {
                const nameVal = (filterName.value || '').toLowerCase();
                const statusVal = filterStatus.value;
                const roleVal = filterRole.value;
                const branchVal = filterBranch.value;
                const rows = staffTable.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const name = row.children[1]?.textContent.toLowerCase() || '';
                    const role = row.children[4]?.textContent.trim().toUpperCase() || '';
                    const branchId = row.children[5]?.getAttribute('data-branch-id') || '';
                    
                    let status = '';
                    const statusCell = row.children[6];
                    if (statusCell) {
                        const badge = statusCell.querySelector('span');
                        if (badge) status = badge.textContent.trim().toLowerCase();
                    }
                    
                    let show = true;
                    if (nameVal && !name.includes(nameVal)) show = false;
                    if (statusVal && status !== statusVal) show = false;
                    if (roleVal && role !== roleVal) show = false;
                    if (branchVal && branchId !== branchVal) show = false;
                    
                    row.style.display = show ? '' : 'none';
                });
            }

            [filterName, filterStatus, filterRole, filterBranch].forEach(el => {
                if (el) el.addEventListener('input', filterTable);
            });
            [filterStatus, filterRole, filterBranch].forEach(el => {
                if (el) el.addEventListener('change', filterTable);
            });

            // --- Branch dropdown show/hide logic ---
            const userRoleSelect = document.getElementById('drawer_user_role');
            const branchDropdown = document.getElementById('branchDropdownContainer');
            
            if (userRoleSelect) {
                userRoleSelect.addEventListener('change', function() {
                    if (this.value === 'ADMIN') {
                        branchDropdown.style.display = 'block';
                    } else {
                        branchDropdown.style.display = 'none';
                    }
                });
            }

            // --- DRAWER SYSTEM ---
            const openDrawerBtn = document.getElementById('openDrawerBtn');
            const closeDrawerBtn = document.getElementById('closeDrawerBtn');
            const drawer = document.getElementById('drawer');
            const drawerOverlay = document.getElementById('drawer-overlay');

            function openDrawer() {
                drawer.classList.add('is-open');
                drawerOverlay.classList.remove('hidden');
            }

            function closeDrawer() {
                drawer.classList.remove('is-open');
                drawerOverlay.classList.add('hidden');
            }

            if (openDrawerBtn) openDrawerBtn.addEventListener('click', openDrawer);
            if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeDrawer);
            if (drawerOverlay) drawerOverlay.addEventListener('click', closeDrawer);

            // --- Add Staff Form Submission ---
            const addStaffForm = document.getElementById('addStaffForm');
            let emailVerified = false;
            let verifiedEmail = '';

            if (addStaffForm) {
                addStaffForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const email = document.getElementById('drawer_email').value.trim();
                    
                    if (!emailVerified || email !== verifiedEmail) {
                        showToast('Please verify the email first.', 'warning');
                        return;
                    }

                    const formData = new FormData(addStaffForm);
                    formData.set('action', 'add');
                    
                    const userRole = formData.get('user_role');
                    if (userRole === 'ADMIN') {
                        const branchId = document.getElementById('drawer_branch_id').value;
                        if (!branchId) {
                            showToast('Please select a branch for Admin.', 'warning');
                            return;
                        }
                        formData.set('branch_id', branchId);
                    } else {
                        formData.delete('branch_id');
                    }

                    fetch('staff_api.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showToast(data.message, 'success');
                            if (data.staff) addStaffRow(data.staff);
                            closeDrawer();
                            addStaffForm.reset();
                            document.getElementById('branchDropdownContainer').style.display = 'none';
                            emailVerified = false;
                            verifiedEmail = '';
                            
                            // Reset email field and badge/button
                            const drawerEmail = document.getElementById('drawer_email');
                            drawerEmail.readOnly = false;
                            document.getElementById('sendCodeBtn').style.display = '';
                            document.getElementById('emailValidatedBadge').classList.add('hidden');
                        } else {
                            showToast(data.message, 'error');
                        }
                    })
                    .catch(() => showToast('Network error. Please try again.', 'error'));
                });
            }

            // --- Email Verification Logic ---
            const sendCodeBtn = document.getElementById('sendCodeBtn');
            const drawerEmail = document.getElementById('drawer_email');
            const emailCodeModal = document.getElementById('emailCodeModal');
            const emailCodeInput = document.getElementById('emailCodeInput');
            const emailCodeError = document.getElementById('emailCodeError');
            const emailCodeCancelBtn = document.getElementById('emailCodeCancelBtn');
            const emailCodeVerifyBtn = document.getElementById('emailCodeVerifyBtn');

            if (sendCodeBtn) {
                sendCodeBtn.addEventListener('click', function() {
                    const email = drawerEmail.value.trim();
                    if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
                        showToast('Enter a valid email address.', 'warning');
                        return;
                    }

                    sendCodeBtn.disabled = true;
                    sendCodeBtn.textContent = 'Sending...';
                    
                    fetch('send_verification.php', {
                        method: 'POST',
                        body: new URLSearchParams({ email })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Verification code sent to email.', 'success');
                            emailCodeModal.classList.remove('hidden');
                            emailCodeModal.classList.add('active');
                            emailCodeInput.value = '';
                            emailCodeError.classList.add('hidden');
                        } else {
                            showToast(data.message, 'error');
                        }
                    })
                    .finally(() => {
                        sendCodeBtn.disabled = false;
                        sendCodeBtn.textContent = 'Send Code';
                    });
                });
            }

            if (emailCodeCancelBtn) {
                emailCodeCancelBtn.addEventListener('click', function() {
                    emailCodeModal.classList.add('hidden');
                    emailCodeModal.classList.remove('active');
                });
            }

            if (emailCodeVerifyBtn) {
                emailCodeVerifyBtn.addEventListener('click', function() {
                    const email = drawerEmail.value.trim();
                    const code = emailCodeInput.value.trim();
                    
                    if (!/^\d{6}$/.test(code)) {
                        emailCodeError.textContent = 'Enter the 6-digit code.';
                        emailCodeError.classList.remove('hidden');
                        return;
                    }

                    emailCodeVerifyBtn.disabled = true;
                    emailCodeVerifyBtn.textContent = 'Verifying...';
                    
                    fetch('verify_code.php', {
                        method: 'POST',
                        body: new URLSearchParams({ email, code })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Email verified!', 'success');
                            emailVerified = true;
                            verifiedEmail = email;
                            emailCodeModal.classList.add('hidden');
                            emailCodeModal.classList.remove('active');
                            
                            // Make email readonly and show validated badge
                            drawerEmail.readOnly = true;
                            document.getElementById('sendCodeBtn').style.display = 'none';
                            document.getElementById('emailValidatedBadge').classList.remove('hidden');
                        } else {
                            emailCodeError.textContent = data.message || 'Invalid code.';
                            emailCodeError.classList.remove('hidden');
                        }
                    })
                    .catch(() => {
                        emailCodeError.textContent = 'Network error. Please try again.';
                        emailCodeError.classList.remove('hidden');
                    })
                    .finally(() => {
                        emailCodeVerifyBtn.disabled = false;
                        emailCodeVerifyBtn.textContent = 'Verify';
                    });
                });
            }

            // --- EDIT STAFF MODAL LOGIC ---
            const editModal = document.getElementById('editStaffModal');
            const closeEditModalBtn = document.getElementById('closeEditModalBtn');
            const editStaffForm = document.getElementById('editStaffForm');
            const saveEditBtn = document.getElementById('saveEditBtn');
            let originalEditData = {};

            // Event delegation for edit buttons
            document.querySelector('#staff-table').addEventListener('click', function(e) {
                const btn = e.target.closest('.edit');
                if (!btn) return;
                
                editModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                
                // Fill modal fields
                document.getElementById('edit_user_id').value = btn.dataset.id;
                document.getElementById('edit_full_name').value = btn.dataset.full_name;
                document.getElementById('edit_email').value = btn.dataset.email;
                document.getElementById('edit_phone').value = btn.dataset.phone;
                document.getElementById('edit_password').value = '';
                
                // Set role value
                const row = btn.closest('tr');
                if (row) {
                    const roleCell = row.querySelector('td:nth-child(5)');
                    if (roleCell) {
                        let roleVal = roleCell.textContent.trim().toUpperCase();
                        document.getElementById('edit_role').value = roleVal;
                    }
                }
                // Disable all fields initially
                ['edit_full_name','edit_email','edit_phone','edit_password','edit_role'].forEach(f => {
                    document.getElementById(f).disabled = true;
                });
                saveEditBtn.disabled = true;
                originalEditData = {
                    full_name: btn.dataset.full_name,
                    email: btn.dataset.email,
                    phone: btn.dataset.phone,
                    password: '',
                    role: document.getElementById('edit_role').value
                };
            });

            // Close edit modal
            closeEditModalBtn.addEventListener('click', function() {
                editModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            });

            // Enable field editing when edit button is clicked
            document.querySelectorAll('.edit-field-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const field = this.dataset.field;
                    document.getElementById(field).disabled = false;
                    document.getElementById(field).focus();
                });
            });

            // Enable Save button if any field changes
            ['edit_full_name','edit_email','edit_phone','edit_password','edit_role'].forEach(f => {
                const field = document.getElementById(f);
                if (field) {
                    field.addEventListener('input', function() {
                        let changed = false;
                        let emailValid = true;
                        
                        // Validate email if changed
                        if (document.getElementById('edit_email').value !== originalEditData.email) {
                            emailValid = /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(document.getElementById('edit_email').value);
                            document.getElementById('edit_email_error').style.display = emailValid ? 'none' : 'block';
                        } else {
                            document.getElementById('edit_email_error').style.display = 'none';
                        }
                        
                        // Check if any field has changed
                        ['full_name','email','phone','role'].forEach(k => {
                            if (document.getElementById('edit_' + k).value !== originalEditData[k]) {
                                changed = true;
                            }
                        });
                        
                        // Password is always considered a change if not empty
                        if (document.getElementById('edit_password').value.length > 0) {
                            changed = true;
                        }
                        
                        saveEditBtn.disabled = !(changed && emailValid);
                    });
                }
            });

            // Submit edit form
            editStaffForm.addEventListener('submit', function(e) {
                e.preventDefault();
                saveEditBtn.disabled = true;
                
                const formData = new FormData(editStaffForm);
                
                fetch('processes/update_staff_account.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.text())
                .then(resp => {
                    const userId = document.getElementById('edit_user_id').value;
                    const row = document.querySelector('tr[data-row-id="' + userId + '"]');
                    
                    if (resp === 'OK') {
                        showToast('Staff account updated successfully.', 'success');
                        
                        if (row) {
                            // Update the row with new data
                            row.querySelector('td:nth-child(2)').textContent = document.getElementById('edit_full_name').value;
                            row.querySelector('td:nth-child(3)').textContent = document.getElementById('edit_email').value;
                            row.querySelector('td:nth-child(4)').textContent = document.getElementById('edit_phone').value;
                        }
                        
                        editModal.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                    } else {
                        showToast('Failed to update staff account.', 'error');
                        editModal.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                        saveEditBtn.disabled = false;
                    }
                })
                .catch(() => {
                    showToast('Error updating staff account.', 'error');
                    editModal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                    saveEditBtn.disabled = false;
                });
            });

            // --- MODAL SYSTEM for Deactivate/Activate ---
            const confirmModal = document.getElementById('confirmModal');
            const modalMsg = document.getElementById('modalMsg');
            const modalSubMsg = document.getElementById('modalSubMsg');
            const modalConfirmBtn = document.getElementById('modalConfirmBtn');
            const modalCancelBtn = document.getElementById('modalCancelBtn');
            const modalIconContainer = document.getElementById('modalIconContainer');
            
            let currentModalContext = {
                action: null,
                id: null,
                button: null
            };

            // Event delegation for status toggle buttons
            document.querySelector('#staff-table').addEventListener('click', function(e) {
                const btn = e.target.closest('.status-toggle');
                if (!btn) return;
                
                const id = btn.dataset.id;
                const currentStatus = btn.dataset.status;
                const action = (currentStatus === 'active') ? 'deactivate' : 'activate';
                
                currentModalContext = { action, id, button: btn };
                
                // Configure and show modal
                if (action === 'deactivate') {
                    modalMsg.textContent = 'Deactivate Account?';
                    modalSubMsg.textContent = 'This will mark the staff account as Inactive.';
                    modalConfirmBtn.textContent = 'Deactivate';
                    modalConfirmBtn.className = modalConfirmBtn.className.replace(/bg-primary-500|bg-green-600/g, 'bg-red-600').replace(/hover:bg-primary-600|hover:bg-green-700/g, 'hover:bg-red-700');
                    modalIconContainer.innerHTML = '<i class="fas fa-user-slash text-5xl text-red-500"></i>';
                } else {
                    modalMsg.textContent = 'Activate Account?';
                    modalSubMsg.textContent = 'This will mark the staff account as Active.';
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
                // Reset confirm button to default theme color
                modalConfirmBtn.className = modalConfirmBtn.className.replace(/bg-red-600|bg-green-600/g, 'bg-primary-500').replace(/hover:bg-red-700|hover:bg-green-700/g, 'hover:bg-primary-600');
            }

            modalCancelBtn.addEventListener('click', closeModal);
            
            // Close modal on overlay click
            confirmModal.addEventListener('click', function(e) {
                if (e.target === confirmModal) {
                    closeModal();
                }
            });
            
            // Handle Confirm
            modalConfirmBtn.addEventListener('click', function() {
                const { action, id, button } = currentModalContext;
                if (!action || !id || !button) return;
                
                const formData = new FormData();
                formData.append('action', action);
                formData.append('id', id);
                
                fetch('staff_api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        updateRowStatus(button, action);
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(() => showToast('Network error.', 'error'))
                .finally(() => closeModal());
            });

            // --- Helper functions ---
            function updateRowStatus(button, action) {
                const row = button.closest('tr');
                if (!row) return;

                const statusCell = row.querySelector('.status-cell');
                
                if (action === 'deactivate') {
                    // Update status badge
                    statusCell.innerHTML = `
                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            Inactive
                        </span>`;
                    // Update button
                    button.textContent = 'Activate';
                    button.dataset.status = 'inactive';
                    button.className = button.className.replace(/text-red-600|hover:text-red-800/g, 'text-green-600 hover:text-green-800');
                } else {
                    // Update status badge
                    statusCell.innerHTML = `
                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Active
                        </span>`;
                    // Update button
                    button.textContent = 'Deactivate';
                    button.dataset.status = 'active';
                    button.className = button.className.replace(/text-green-600|hover:text-green-800/g, 'text-red-600 hover:text-red-800');
                }
            }

            function addStaffRow(staff) {
                const tbody = document.querySelector('#staff-table tbody');
                // Remove "No staff accounts found" row if present
                const emptyRow = tbody.querySelector('tr td[colspan="9"]');
                if (emptyRow) emptyRow.parentElement.remove();
                
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-primary-50 transition-colors';
                tr.setAttribute('data-row-id', staff.id);
                tr.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">${staff.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">${escapeHtml(staff.full_name)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${escapeHtml(staff.email)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${escapeHtml(staff.phone_number)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${escapeHtml((staff.user_role||'').toLowerCase().replace(/\b\w/g, c => c.toUpperCase()))}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700" data-branch-id="${staff.branch_id || ''}">${escapeHtml((staff.branch_name||'').toLowerCase().replace(/\b\w/g, c => c.toUpperCase()))}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm status-cell">
                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${formatDate(staff.created_at)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium action-cell">
                        <button class="text-primary-600 hover:text-primary-800 transition-colors mr-3 action-btn edit" 
                                data-id="${staff.id}"
                                data-full_name="${escapeHtml(staff.full_name)}"
                                data-email="${escapeHtml(staff.email)}"
                                data-phone="${escapeHtml(staff.phone_number)}">
                            Edit
                        </button>
                        <button class="text-red-600 hover:text-red-800 transition-colors action-btn status-toggle" 
                                data-id="${staff.id}" 
                                data-status="active">
                            Deactivate
                        </button>
                    </td>
                `;
                tbody.prepend(tr); // Add new row to the top
            }
        });
    </script>
</body>
</html>