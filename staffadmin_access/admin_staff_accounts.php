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
    <title>Manage Staff | Rich Anne Tiles</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': { '400': '#f2946f', '500': '#ed6631', '600': '#d55c2c', '700': '#b24d25' },
                        'accent': { '50': '#f6f6f6', '100': '#e7e7e7', '200': '#d1d1d1', '300': '#b0b0b0', '900': '#3d3d3d' }
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
            --transition-speed: 0.3s;
        }
        body {
            background: linear-gradient(135deg, #fef8f6 0%, #fdf0ec 100%);
            color: #4f4f4f;
            font-family: 'Inter', sans-serif;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left var(--transition-speed);
        }
        @media (max-width: 768px) {
            .main-content { margin-left: 0; }
        }
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d1d1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #b0b0b0; }

        /* Drawer Animation */
        .drawer {
            transition: transform 0.3s ease-in-out;
            transform: translateX(100%);
        }
        .drawer.is-open { transform: translateX(0); }
    </style>
</head>
<body class="font-sans text-gray-700">

    <div class="main-content p-6">

        <div class="bg-white rounded-2xl p-6 mb-6 flex flex-col md:flex-row justify-between items-center shadow-sm">
            <div class="mb-4 md:mb-0">
                <h1 class="text-2xl font-bold text-accent-900 font-heading">Manage Staff Accounts</h1>
                <p class="text-sm text-gray-500 mt-1">Control access, assign roles, and manage employees.</p>
            </div>
            <button id="openDrawerBtn" class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-primary-500 rounded-lg shadow hover:bg-primary-600 transition-all">
                <i class="fas fa-plus-circle"></i>
                Add New Staff
            </button>
        </div>

        <div id="toast-container" class="fixed top-6 right-6 z-[100] flex flex-col gap-3 pointer-events-none"></div>

        <div class="bg-white rounded-2xl p-6 shadow-sm">
            
            <div class="flex flex-col lg:flex-row gap-4 mb-6 items-end lg:items-center justify-between">
                <h2 class="text-lg font-semibold text-accent-900 whitespace-nowrap">Staff Directory</h2>
                
                <div class="flex flex-wrap gap-3 w-full lg:w-auto">
                    <div class="relative flex-grow lg:flex-grow-0">
                        <input id="filterName" type="text" placeholder="Search name..." class="w-full lg:w-48 pl-9 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:outline-none">
                        <i class="fa fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                    </div>
                    
                    <select id="filterRole" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white text-gray-600">
                        <option value="">All Roles</option>
                        <option value="ADMIN">Admin</option>
                        <option value="ENCODER">Encoder</option>
                        <option value="CASHIER">Cashier</option>
                        <option value="DRIVER">Driver</option>
                    </select>

                    <select id="filterStatus" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white text-gray-600">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>

                    <select id="filterBranch" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white text-gray-600">
                        <option value="">All Branches</option>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-sm">
                        <?php if (empty($staff_accounts)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fa fa-users text-3xl text-gray-300 mb-3"></i>
                                    <p>No staff accounts found.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($staff_accounts as $staff): ?>
                                <tr class="hover:bg-gray-50 transition-colors group" data-row-id="<?php echo htmlspecialchars($staff['id']); ?>">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">#<?php echo htmlspecialchars($staff['id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-800">
                                        <?php echo htmlspecialchars($staff['full_name']); ?>
                                        <div class="text-xs text-gray-400 font-normal mt-0.5">Joined: <?php echo date('M d, Y', strtotime($staff['created_at'])); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                        <div class="flex items-center gap-2"><i class="fa fa-envelope text-xs text-gray-400 w-4"></i> <?php echo htmlspecialchars($staff['email']); ?></div>
                                        <div class="flex items-center gap-2 mt-1"><i class="fa fa-phone text-xs text-gray-400 w-4"></i> <?php echo htmlspecialchars($staff['phone_number']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            <?php echo htmlspecialchars(ucwords(strtolower($staff['user_role']))); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600" data-branch-id="<?php echo htmlspecialchars($staff['branch_id'] ?? ''); ?>">
                                        <?php echo htmlspecialchars(ucwords(strtolower($staff['branch_name'] ?? '-'))); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap status-cell">
                                        <?php if ($staff['account_status'] == 'active'): ?>
                                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                        <?php else: ?>
                                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium action-cell">
                                        <div class="flex items-center gap-2">
                                            <button class="action-btn edit bg-gray-100 hover:bg-primary-50 text-gray-600 hover:text-primary-600 p-1.5 rounded transition-colors" 
                                                    title="Edit"
                                                    data-id="<?php echo htmlspecialchars($staff['id']); ?>"
                                                    data-full_name="<?php echo htmlspecialchars($staff['full_name']); ?>"
                                                    data-email="<?php echo htmlspecialchars($staff['email']); ?>"
                                                    data-phone="<?php echo htmlspecialchars($staff['phone_number']); ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <?php if ($staff['account_status'] == 'active'): ?>
                                                <button class="action-btn status-toggle bg-red-50 hover:bg-red-100 text-red-600 p-1.5 rounded transition-colors" 
                                                        title="Deactivate"
                                                        data-id="<?php echo htmlspecialchars($staff['id']); ?>" 
                                                        data-status="active">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="action-btn status-toggle bg-green-50 hover:bg-green-100 text-green-600 p-1.5 rounded transition-colors" 
                                                        title="Activate"
                                                        data-id="<?php echo htmlspecialchars($staff['id']); ?>" 
                                                        data-status="inactive">
                                                    <i class="fas fa-user-check"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div> <div id="drawer-overlay" class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity duration-300 hidden"></div>
    <div id="drawer" class="drawer fixed inset-y-0 right-0 z-50 w-full max-w-md bg-white shadow-2xl border-l border-gray-100 flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-bold text-accent-900">Add New Staff</h2>
            <button id="closeDrawerBtn" class="text-gray-400 hover:text-red-500 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="addStaffForm" class="flex-1 overflow-y-auto p-6 space-y-5 custom-scrollbar" autocomplete="off">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Full Name</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-3 top-2.5 text-gray-400"></i>
                    <input type="text" name="full_name" required class="block w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm" placeholder="John Doe">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Address</label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <i class="fas fa-envelope absolute left-3 top-2.5 text-gray-400"></i>
                        <input type="email" id="drawer_email" name="email" required class="block w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm" placeholder="email@example.com">
                    </div>
                    <button type="button" id="sendCodeBtn" class="px-3 py-2 bg-primary-500 text-white rounded-lg text-xs font-bold hover:bg-primary-600 transition-colors whitespace-nowrap">Verify</button>
                </div>
                <div id="emailValidatedBadge" class="hidden mt-2 text-green-600 text-xs font-bold flex items-center gap-1"><i class="fas fa-check-circle"></i> Verified</div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Phone Number</label>
                <div class="relative">
                    <i class="fas fa-phone absolute left-3 top-2.5 text-gray-400"></i>
                    <input type="tel" name="phone" required class="block w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm" placeholder="+639..." maxlength="13">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-2.5 text-gray-400"></i>
                        <input type="password" id="drawer_password" name="password" required class="block w-full pl-10 pr-8 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                        <button type="button" class="absolute right-2 top-2 text-gray-400 hover:text-gray-600" onclick="togglePasswordVisibility('drawer_password', this)"><i class="fas fa-eye text-xs"></i></button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Confirm</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-2.5 text-gray-400"></i>
                        <input type="password" id="drawer_confirm_password" name="confirm_password" required class="block w-full pl-10 pr-8 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                        <button type="button" class="absolute right-2 top-2 text-gray-400 hover:text-gray-600" onclick="togglePasswordVisibility('drawer_confirm_password', this)"><i class="fas fa-eye text-xs"></i></button>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Role</label>
                <select id="drawer_user_role" name="user_role" required class="block w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm bg-white">
                    <option value="">-- Select Role --</option>
                    <option value="ADMIN">Admin</option>
                    <option value="ENCODER">Encoder</option>
                    <option value="CASHIER">Cashier</option>
                    <option value="DRIVER">Driver</option>
                </select>
            </div>

            <div id="branchDropdownContainer" class="hidden">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Assign Branch</label>
                <select id="drawer_branch_id" name="branch_id" class="block w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm bg-white">
                    <option value="">-- Select Branch --</option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?php echo htmlspecialchars($branch['branch_id']); ?>"><?php echo htmlspecialchars($branch['branch_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
        
        <div class="p-6 border-t border-gray-100 bg-gray-50">
            <button type="submit" form="addStaffForm" class="w-full py-2.5 bg-primary-500 text-white font-bold rounded-lg shadow-md hover:bg-primary-600 transition-all">
                Create Account
            </button>
        </div>
    </div>

    <div id="emailCodeModal" class="fixed inset-0 z-[60] hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 text-center transform scale-95 transition-transform">
            <div class="mx-auto w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mb-4 text-primary-600">
                <i class="fas fa-envelope-open-text text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Verify Email</h3>
            <p class="text-sm text-gray-500 mt-1 mb-4">Enter the 6-digit code sent to your email.</p>
            <input id="emailCodeInput" type="text" maxlength="6" class="w-full text-center text-2xl tracking-widest border border-gray-300 rounded-lg py-2 focus:ring-2 focus:ring-primary-500 outline-none mb-2" placeholder="000000">
            <p id="emailCodeError" class="text-red-500 text-xs hidden mb-2"></p>
            <div class="flex gap-2">
                <button id="emailCodeCancelBtn" class="flex-1 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium">Cancel</button>
                <button id="emailCodeVerifyBtn" class="flex-1 py-2 text-white bg-primary-500 hover:bg-primary-600 rounded-lg text-sm font-medium">Verify</button>
            </div>
        </div>
    </div>

    <div id="editStaffModal" class="fixed inset-0 z-[60] hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 relative transform scale-95 transition-transform">
            <button id="closeEditModalBtn" class="absolute top-4 right-4 text-gray-400 hover:text-red-500"><i class="fas fa-times"></i></button>
            <h2 class="text-xl font-bold mb-4 text-gray-800">Edit Staff Details</h2>
            
            <form id="editStaffForm" class="space-y-4">
                <input type="hidden" id="edit_user_id" name="user_id">
                
                <?php 
                $fields = [
                    ['id' => 'edit_full_name', 'label' => 'Full Name', 'icon' => 'user', 'type' => 'text'],
                    ['id' => 'edit_email', 'label' => 'Email Address', 'icon' => 'envelope', 'type' => 'email'],
                    ['id' => 'edit_phone', 'label' => 'Phone Number', 'icon' => 'phone', 'type' => 'tel']
                ];
                foreach($fields as $f): 
                ?>
                <div>
                    <label class="flex justify-between text-xs font-bold text-gray-700 uppercase mb-1">
                        <?php echo $f['label']; ?>
                        <button type="button" class="text-primary-500 hover:text-primary-700 edit-field-btn text-xs" data-field="<?php echo $f['id']; ?>"><i class="fas fa-pen"></i> Edit</button>
                    </label>
                    <div class="relative">
                        <i class="fas fa-<?php echo $f['icon']; ?> absolute left-3 top-2.5 text-gray-400"></i>
                        <input type="<?php echo $f['type']; ?>" id="<?php echo $f['id']; ?>" name="<?php echo str_replace('edit_', '', $f['id']); ?>" disabled class="block w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 bg-gray-50 disabled:text-gray-500 text-sm focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                    </div>
                    <?php if($f['id'] == 'edit_email') echo '<p id="edit_email_error" class="text-red-500 text-xs mt-1 hidden">Invalid format.</p>'; ?>
                </div>
                <?php endforeach; ?>

                <div>
                    <label class="flex justify-between text-xs font-bold text-gray-700 uppercase mb-1">
                        Password
                        <button type="button" class="text-primary-500 hover:text-primary-700 edit-field-btn text-xs" data-field="edit_password"><i class="fas fa-pen"></i> Edit</button>
                    </label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-2.5 text-gray-400"></i>
                        <input type="password" id="edit_password" name="password" disabled placeholder="Unchanged" class="block w-full pl-10 pr-8 py-2 rounded-lg border border-gray-300 bg-gray-50 text-sm focus:bg-white focus:ring-2 focus:ring-primary-500">
                        <button type="button" class="absolute right-2 top-2 text-gray-400" onclick="togglePasswordVisibility('edit_password', this)"><i class="fas fa-eye text-xs"></i></button>
                    </div>
                </div>

                <div>
                    <label class="flex justify-between text-xs font-bold text-gray-700 uppercase mb-1">
                        Role
                        <button type="button" class="text-primary-500 hover:text-primary-700 edit-field-btn text-xs" data-field="edit_role"><i class="fas fa-pen"></i> Edit</button>
                    </label>
                    <select id="edit_role" name="role" disabled class="block w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-sm focus:bg-white focus:ring-2 focus:ring-primary-500">
                        <option value="ENCODER">Encoder</option>
                        <option value="CASHIER">Cashier</option>
                        <option value="DRIVER">Driver</option>
                        <option value="ADMIN">Admin</option>
                    </select>
                </div>

                <button type="submit" id="saveEditBtn" class="w-full py-2.5 bg-primary-500 text-white font-bold rounded-lg shadow hover:bg-primary-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all" disabled>Save Changes</button>
            </form>
        </div>
    </div>

    <div id="confirmModal" class="fixed inset-0 z-[60] hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 text-center transform scale-95 transition-transform">
            <div id="modalIconContainer" class="flex justify-center mb-4"></div>
            <h3 class="text-lg font-bold text-gray-800" id="modalMsg">Confirm?</h3>
            <p class="text-sm text-gray-500 mt-2 mb-6" id="modalSubMsg">Are you sure?</p>
            <div class="flex gap-3">
                <button id="modalCancelBtn" class="flex-1 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium text-sm">Cancel</button>
                <button id="modalConfirmBtn" class="flex-1 py-2 text-white bg-primary-500 hover:bg-primary-600 rounded-lg font-medium text-sm shadow">Confirm</button>
            </div>
        </div>
    </div>

    <script>
        // --- GLOBAL UTILS ---
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const div = document.createElement('div');
            
            const icon = type === 'success' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-triangle"></i>';
            const colors = type === 'success' ? 'bg-green-100 border-green-300 text-green-800' : 'bg-red-100 border-red-300 text-red-800';
            
            div.className = `pointer-events-auto flex items-center gap-3 w-72 p-4 rounded-lg shadow-lg border ${colors} transform translate-x-full transition-all duration-300`;
            div.innerHTML = `<div class="text-lg">${icon}</div><div class="text-sm font-semibold">${message}</div>`;
            
            container.appendChild(div);
            requestAnimationFrame(() => div.classList.remove('translate-x-full'));
            setTimeout(() => {
                div.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => div.remove(), 300);
            }, 3000);
        }

        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // --- MAIN LOGIC ---
        document.addEventListener('DOMContentLoaded', function() {
            
            // 1. Filtering Logic
            const filterInputs = [
                document.getElementById('filterName'),
                document.getElementById('filterStatus'),
                document.getElementById('filterRole'),
                document.getElementById('filterBranch')
            ];

            function filterTable() {
                const nameVal = (filterInputs[0].value || '').toLowerCase();
                const statusVal = filterInputs[1].value;
                const roleVal = filterInputs[2].value;
                const branchVal = filterInputs[3].value;

                document.querySelectorAll('#staff-table tbody tr').forEach(row => {
                    // Get text content safely
                    const name = row.children[1].textContent.toLowerCase();
                    const role = row.children[3].textContent.trim().toUpperCase();
                    const branchId = row.children[4].getAttribute('data-branch-id') || '';
                    const statusBadge = row.querySelector('.status-cell span');
                    const status = statusBadge ? statusBadge.textContent.trim().toLowerCase() : '';

                    let show = true;
                    if (nameVal && !name.includes(nameVal)) show = false;
                    if (statusVal && status !== statusVal) show = false;
                    if (roleVal && role !== roleVal) show = false;
                    if (branchVal && branchId !== branchVal) show = false;

                    row.style.display = show ? '' : 'none';
                });
            }
            filterInputs.forEach(el => el && el.addEventListener('input', filterTable));
            filterInputs.forEach(el => el && el.addEventListener('change', filterTable));

            // 2. Drawer Logic
            const drawer = document.getElementById('drawer');
            const overlay = document.getElementById('drawer-overlay');
            const branchDropdown = document.getElementById('branchDropdownContainer');
            const roleSelect = document.getElementById('drawer_user_role');

            document.getElementById('openDrawerBtn').addEventListener('click', () => {
                drawer.classList.add('is-open');
                overlay.classList.remove('hidden');
            });

            function closeDrawer() {
                drawer.classList.remove('is-open');
                overlay.classList.add('hidden');
            }
            
            document.getElementById('closeDrawerBtn').addEventListener('click', closeDrawer);
            overlay.addEventListener('click', closeDrawer);

            if(roleSelect) {
                roleSelect.addEventListener('change', function() {
                    if(this.value === 'ADMIN') branchDropdown.classList.remove('hidden');
                    else branchDropdown.classList.add('hidden');
                });
            }

            // 3. Email Verification Logic
            const sendCodeBtn = document.getElementById('sendCodeBtn');
            const emailModal = document.getElementById('emailCodeModal');
            let emailVerified = false;
            let verifiedEmail = '';

            sendCodeBtn.addEventListener('click', () => {
                const email = document.getElementById('drawer_email').value.trim();
                if (!/^\S+@\S+\.\S+$/.test(email)) return showToast('Invalid email address', 'error');

                sendCodeBtn.innerText = 'Sending...';
                sendCodeBtn.disabled = true;

                fetch('send_verification.php', {
                    method: 'POST',
                    body: new URLSearchParams({ email })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        emailModal.classList.remove('hidden');
                        document.getElementById('emailCodeInput').value = '';
                        showToast('Code sent!', 'success');
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .finally(() => {
                    sendCodeBtn.innerText = 'Verify';
                    sendCodeBtn.disabled = false;
                });
            });

            document.getElementById('emailCodeCancelBtn').addEventListener('click', () => emailModal.classList.add('hidden'));
            
            document.getElementById('emailCodeVerifyBtn').addEventListener('click', () => {
                const code = document.getElementById('emailCodeInput').value;
                const email = document.getElementById('drawer_email').value.trim();
                const errorMsg = document.getElementById('emailCodeError');

                fetch('verify_code.php', {
                    method: 'POST',
                    body: new URLSearchParams({ email, code })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        emailVerified = true;
                        verifiedEmail = email;
                        emailModal.classList.add('hidden');
                        document.getElementById('drawer_email').readOnly = true;
                        sendCodeBtn.classList.add('hidden');
                        document.getElementById('emailValidatedBadge').classList.remove('hidden');
                        showToast('Email Verified!', 'success');
                    } else {
                        errorMsg.innerText = data.message || 'Invalid Code';
                        errorMsg.classList.remove('hidden');
                    }
                });
            });

            // 4. Add Staff Submit
            document.getElementById('addStaffForm').addEventListener('submit', (e) => { // Listener on FORM not button
                // Prevent default happens automatically if listener is on 'submit'
            });

            document.querySelector('#drawer button[type="submit"]').addEventListener('click', (e) => {
                e.preventDefault();
                const form = document.getElementById('addStaffForm');
                const email = document.getElementById('drawer_email').value.trim();

                if (!emailVerified || email !== verifiedEmail) return showToast('Please verify email first', 'warning');
                if (!form.reportValidity()) return;

                const formData = new FormData(form);
                formData.append('action', 'add');

                fetch('staff_api.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        showToast('Staff added successfully', 'success');
                        setTimeout(() => window.location.reload(), 1000); // Simple reload to refresh table
                    } else {
                        showToast(data.message, 'error');
                    }
                });
            });

            // 5. Edit Staff Logic
            const editModal = document.getElementById('editStaffModal');
            const saveEditBtn = document.getElementById('saveEditBtn');
            let originalData = {};

            document.querySelector('#staff-table').addEventListener('click', (e) => {
                const btn = e.target.closest('.edit');
                if(!btn) return;

                document.getElementById('edit_user_id').value = btn.dataset.id;
                document.getElementById('edit_full_name').value = btn.dataset.full_name;
                document.getElementById('edit_email').value = btn.dataset.email;
                document.getElementById('edit_phone').value = btn.dataset.phone;
                
                // Get role from table row text
                const role = btn.closest('tr').querySelector('td:nth-child(4)').innerText.trim().toUpperCase();
                document.getElementById('edit_role').value = role;
                
                // Reset state
                editModal.classList.remove('hidden');
                document.querySelectorAll('#editStaffForm input, #editStaffForm select').forEach(i => {
                    if(i.type !== 'hidden') i.disabled = true;
                });
                saveEditBtn.disabled = true;

                originalData = {
                    full_name: btn.dataset.full_name,
                    email: btn.dataset.email,
                    phone: btn.dataset.phone,
                    role: role,
                    password: ''
                };
            });

            document.getElementById('closeEditModalBtn').addEventListener('click', () => editModal.classList.add('hidden'));

            // Enable fields
            document.querySelectorAll('.edit-field-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const input = document.getElementById(this.dataset.field);
                    input.disabled = false;
                    input.focus();
                    input.classList.remove('bg-gray-50');
                });
            });

            // Check for changes
            document.getElementById('editStaffForm').addEventListener('input', () => {
                let hasChange = false;
                ['full_name', 'email', 'phone', 'role'].forEach(k => {
                   if(document.getElementById(`edit_${k}`).value !== originalData[k]) hasChange = true;
                });
                if(document.getElementById('edit_password').value.length > 0) hasChange = true;
                
                saveEditBtn.disabled = !hasChange;
            });

            saveEditBtn.addEventListener('click', (e) => {
                e.preventDefault();
                saveEditBtn.innerText = 'Saving...';
                const formData = new FormData(document.getElementById('editStaffForm'));
                
                fetch('processes/update_staff_account.php', { method: 'POST', body: formData })
                .then(res => res.text())
                .then(resp => {
                    if(resp.trim() === 'OK') {
                        showToast('Updated successfully', 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showToast('Update failed', 'error');
                        saveEditBtn.innerText = 'Save Changes';
                    }
                });
            });

            // 6. Activate/Deactivate
            const confirmModal = document.getElementById('confirmModal');
            let actionCtx = null;

            document.querySelector('#staff-table').addEventListener('click', (e) => {
                const btn = e.target.closest('.status-toggle');
                if(!btn) return;

                const id = btn.dataset.id;
                const currentStatus = btn.dataset.status;
                const action = currentStatus === 'active' ? 'deactivate' : 'activate';
                actionCtx = { id, action, btn };

                const iconContainer = document.getElementById('modalIconContainer');
                const modalMsg = document.getElementById('modalMsg');
                const confirmBtn = document.getElementById('modalConfirmBtn');

                if(action === 'deactivate') {
                    modalMsg.innerText = "Deactivate Account?";
                    iconContainer.innerHTML = '<div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-500 text-2xl"><i class="fas fa-user-slash"></i></div>';
                    confirmBtn.className = "flex-1 py-2 text-white bg-red-500 hover:bg-red-600 rounded-lg font-medium text-sm shadow";
                } else {
                    modalMsg.innerText = "Activate Account?";
                    iconContainer.innerHTML = '<div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-500 text-2xl"><i class="fas fa-user-check"></i></div>';
                    confirmBtn.className = "flex-1 py-2 text-white bg-green-500 hover:bg-green-600 rounded-lg font-medium text-sm shadow";
                }

                confirmModal.classList.remove('hidden');
            });

            document.getElementById('modalCancelBtn').addEventListener('click', () => confirmModal.classList.add('hidden'));
            
            document.getElementById('modalConfirmBtn').addEventListener('click', () => {
                if(!actionCtx) return;
                const formData = new FormData();
                formData.append('action', actionCtx.action);
                formData.append('id', actionCtx.id);

                fetch('staff_api.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        showToast('Status updated', 'success');
                        // DOM update logic
                        const row = actionCtx.btn.closest('tr');
                        const statusCell = row.querySelector('.status-cell');
                        const btn = actionCtx.btn;

                        if(actionCtx.action === 'deactivate') {
                            statusCell.innerHTML = '<span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>';
                            btn.dataset.status = 'inactive';
                            btn.innerHTML = '<i class="fas fa-user-check"></i>';
                            btn.className = 'action-btn status-toggle bg-green-50 hover:bg-green-100 text-green-600 p-1.5 rounded transition-colors';
                            btn.title = 'Activate';
                        } else {
                            statusCell.innerHTML = '<span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>';
                            btn.dataset.status = 'active';
                            btn.innerHTML = '<i class="fas fa-user-slash"></i>';
                            btn.className = 'action-btn status-toggle bg-red-50 hover:bg-red-100 text-red-600 p-1.5 rounded transition-colors';
                            btn.title = 'Deactivate';
                        }
                        confirmModal.classList.add('hidden');
                    } else {
                        showToast('Error updating status', 'error');
                    }
                });
            });

        });
    </script>
</body>
</html>