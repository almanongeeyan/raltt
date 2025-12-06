<?php
// Start output buffering immediately to capture any stray whitespace
ob_start();
session_start();

// Adjust the path if necessary
require_once '../connection/connection.php'; 
$conn = $db_connection;

// --- 1. CSRF Token Generation ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// --- 2. AJAX Request Handler (Real-time Update) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
    // clear any previous output (whitespace/HTML)
    ob_clean(); 
    header('Content-Type: application/json');
    
    // Suppress warnings for the JSON response to prevent syntax errors
    error_reporting(0); 

    // Verify CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token. Refresh page.']);
        exit;
    }

    $user_id = intval($_POST['user_id']);
    $new_status = ($_POST['new_status'] === 'active') ? 'active' : 'inactive';
    $request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : null;

    try {
        $conn->beginTransaction();

        // 1. Update User Status
        $stmt = $conn->prepare("UPDATE users SET account_status = ? WHERE id = ? AND user_role = 'CUSTOMER'");
        $stmt->execute([$new_status, $user_id]);
        $user_updated = $stmt->rowCount() > 0;

        // 2. If activating via a Request, approve the request
        $request_updated = false;
        if ($request_id && $new_status === 'active') {
            $stmt2 = $conn->prepare("UPDATE account_reactivation_requests SET status = 'APPROVED', resolution_date = NOW(), admin_user_id = ? WHERE request_id = ?");
            $admin_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $stmt2->execute([$admin_id, $request_id]);
            $request_updated = $stmt2->rowCount() > 0;
        }

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => "User #$user_id is now " . ucfirst($new_status),
            'new_status' => $new_status,
            'request_id' => $request_id,
            'request_status' => ($request_id && $new_status === 'active') ? 'APPROVED' : null
        ]);

    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// Flush buffer for HTML page load
ob_end_flush(); 

// --- 3. Data Fetching ---

// Helper function for violations
function user_has_violations($conn, $user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM buyer_warnings WHERE user_id = ? AND is_active = 1");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn() > 0;
}

$customer_accounts = [];
try {
    $sql = "SELECT id, full_name, email, phone_number, user_role, account_status, created_at FROM users WHERE user_role = 'CUSTOMER' ORDER BY created_at DESC";
    $stmt = $conn->query($sql);
    if ($stmt) $customer_accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { /* Log error */ }

$activation_requests = [];
try {
    $sql_req = "SELECT arr.request_id, arr.user_id, arr.contact_type, arr.contact_value, arr.reason_provided, arr.submission_date, arr.status,
                        u.full_name, u.email, u.phone_number, u.account_status,
                        GROUP_CONCAT(DISTINCT bw.warning_type SEPARATOR ', ') AS warning_types
                FROM account_reactivation_requests arr
                LEFT JOIN users u ON arr.user_id = u.id
                LEFT JOIN buyer_warnings bw ON bw.user_id = arr.user_id AND bw.is_active = 1
                GROUP BY arr.request_id
                ORDER BY arr.submission_date DESC";
    $stmt_req = $conn->query($sql_req);
    if ($stmt_req) $activation_requests = $stmt_req->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { /* Log error */ }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers | Admin</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': { '400': '#f2946f', '500': '#ed6631', '600': '#d55c2c', '700': '#b24d25' },
                        'accent': { '50': '#f6f6f6', '900': '#3d3d3d' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'], heading: ['Montserrat', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        .violation-indicator {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #fff0f0;
            color: #ed6631;
            font-size: 12px;
            border-radius: 4px;
            padding: 2px 6px;
            margin-left: 6px;
            cursor: pointer;
        }
        .violation-indicator:hover { background: #ffe0e0; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d1d1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #b0b0b0; }
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .fade-out { animation: fadeOut 0.5s forwards; }
        @keyframes fadeOut { from { opacity: 1; max-height: 100px; } to { opacity: 0; max-height: 0; padding: 0; border: 0; } }
    </style>
</head>
<body class="font-sans text-gray-700 bg-gray-50">

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content p-6 ml-0 md:ml-[250px] transition-all duration-300">
        
        <div class="bg-white rounded-2xl p-6 mb-6 flex flex-col md:flex-row justify-between items-center shadow-sm">
            <div>
                <h1 class="text-2xl font-bold text-accent-900 font-heading">Customer Accounts</h1>
                <p class="text-sm text-gray-500">Manage access and view order history.</p>
            </div>
            <div class="flex bg-gray-100 p-1 rounded-lg mt-4 md:mt-0">
                <button id="btn-all" class="px-4 py-2 text-sm font-medium rounded-md bg-white shadow text-primary-600 transition-all" onclick="switchTab('all')">All Accounts</button>
                <?php
                    $pendingOrRejectedCount = 0;
                    foreach ($activation_requests as $req) {
                        $status = strtoupper(trim($req['status']));
                        if (($status === 'PENDING' || $status === 'REJECTED') && $req['account_status'] !== 'active') {
                            $pendingOrRejectedCount++;
                        }
                    }
                ?>
                <button id="btn-req" class="px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:bg-white transition-all" onclick="switchTab('req')">
                    Requests 
                    <span id="req-badge-count" class="bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full ml-1 <?php echo $pendingOrRejectedCount > 0 ? '' : 'hidden'; ?>">
                        <?php echo $pendingOrRejectedCount; ?>
                    </span>
                </button>
            </div>
        </div>

        <div id="toast-container" class="fixed top-6 right-6 z-[100] flex flex-col gap-3 pointer-events-none"></div>

        <div id="section-all" class="bg-white rounded-2xl p-6 shadow-sm fade-in">
            <div class="flex flex-col md:flex-row justify-between items-center mb-5 gap-4">
                <h2 class="text-lg font-semibold text-accent-900">Customer List</h2>
                <div class="relative w-full md:w-64">
                    <input type="text" id="searchInput" onkeyup="filterTable()" class="w-full pl-9 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="Search customers...">
                    <i class="fa fa-search absolute left-3 top-2.5 text-gray-400"></i>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User Info</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="table-body-all" class="bg-white divide-y divide-gray-200">
                        <?php foreach ($customer_accounts as $c): 
                            $isActive = ($c['account_status'] === 'active');
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors" id="row-<?php echo $c['id']; ?>" data-search="<?php echo strtolower($c['full_name'] . ' ' . $c['email']); ?>">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">#<?php echo $c['id']; ?></td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900" style="position:relative;">
                                    <?php echo htmlspecialchars($c['full_name']); ?>
                                    <?php if (user_has_violations($conn, $c['id'])): ?>
                                        <span class="violation-indicator" onclick="showViolationsModal(<?php echo $c['id']; ?>, '<?php echo htmlspecialchars(addslashes($c['full_name'])); ?>')">
                                            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Has Violation
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-xs text-gray-500">Joined: <?php echo date('M d, Y', strtotime($c['created_at'])); ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div><i class="fa fa-envelope w-4 text-gray-400"></i> <?php echo htmlspecialchars($c['email']); ?></div>
                                <div class="mt-1"><i class="fa fa-phone w-4 text-gray-400"></i> <?php echo htmlspecialchars($c['phone_number']); ?></div>
                            </td>
                            <td class="px-6 py-4 status-cell">
                                <span id="badge-<?php echo $c['id']; ?>" class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo $isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo ucfirst($c['account_status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium flex items-center gap-2">
                                <button onclick="openCart(<?php echo $c['id']; ?>)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded shadow-sm text-xs flex items-center gap-1 transition-colors">
                                    <i class="fa fa-shopping-cart"></i> Cart
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($customer_accounts)): ?>
                            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No customers found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div id="no-results" class="hidden px-6 py-8 text-center text-gray-500">No matches found.</div>
            </div>
        </div>

        <div id="section-req" class="bg-white rounded-2xl p-6 shadow-sm fade-in hidden">
            <h2 class="text-lg font-semibold text-accent-900 mb-4">Account Reactivation Requests</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No.</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User Info</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Violations</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody id="req-table-body" class="divide-y divide-gray-200">
                        <?php 
                        $visibleReqNum = 1;
                        foreach ($activation_requests as $req): 
                            $status = strtoupper(trim($req['status']));
                            if (!(($status === 'PENDING' || $status === 'REJECTED') && $req['account_status'] !== 'active')) continue;
                        ?>
                        <tr class="hover:bg-gray-50" id="req-row-<?php echo $req['request_id']; ?>">
                            <td class="px-4 py-4 text-sm font-medium text-gray-500 number-cell">#<?php echo $visibleReqNum++; ?></td>
                            <td class="px-4 py-4 text-sm">
                                <div class="font-semibold text-gray-900"><?php echo isset($req['full_name']) ? htmlspecialchars($req['full_name']) : 'N/A'; ?></div>
                                <div class="text-xs text-gray-500">ID: <?php echo $req['user_id']; ?></div>
                            </td>
                            <td class="px-4 py-4">
                                <button onclick="showViolationsModal(<?php echo (int)$req['user_id']; ?>, '<?php echo isset($req['full_name']) ? htmlspecialchars(addslashes($req['full_name'])) : 'N/A'; ?>')"
                                        class="inline-flex items-center px-2.5 py-1.5 rounded border border-red-200 bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100 transition-colors">
                                    <i class="fa fa-triangle-exclamation mr-1.5"></i> View Strikes
                                </button>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700 max-w-xs truncate" title="<?php echo htmlspecialchars($req['reason_provided']); ?>">
                                <?php echo htmlspecialchars(mb_strimwidth($req['reason_provided'], 0, 60, '...')); ?>
                            </td>
                            <td class="px-4 py-4 text-xs text-gray-500">
                                <?php echo date('M d, Y H:i', strtotime($req['submission_date'])); ?>
                            </td>
                            <td class="px-4 py-4 text-xs">
                                <?php
                                    $statusColor = $status === 'APPROVED' ? 'bg-green-100 text-green-800' : ($status === 'REJECTED' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800');
                                ?>
                                <span class="px-2 py-1 rounded-full font-semibold <?php echo $statusColor; ?>"><?php echo ucfirst(strtolower($status)); ?></span>
                            </td>
                            <td class="px-4 py-4 text-xs">
                                <?php if ($req['user_id']): ?>
                                    <?php if ($req['account_status'] === 'active'): ?>
                                        <button onclick="confirmToggle(<?php echo $req['user_id']; ?>, 'active', <?php echo $req['request_id']; ?>)"
                                                class="px-3 py-1.5 rounded shadow-sm text-xs flex items-center gap-1 transition-colors text-white bg-red-500 hover:bg-red-600">
                                            <i class="fa fa-user-slash"></i> Deactivate
                                        </button>
                                    <?php else: ?>
                                        <button onclick="confirmToggle(<?php echo $req['user_id']; ?>, 'inactive', <?php echo $req['request_id']; ?>)"
                                                class="px-3 py-1.5 rounded shadow-sm text-xs flex items-center gap-1 transition-colors text-white bg-green-500 hover:bg-green-600">
                                            <i class="fa fa-user-check"></i> Approve
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if($visibleReqNum === 1): ?>
                            <tr id="no-req-row"><td colspan="7" class="px-6 py-8 text-center text-gray-500">No reactivation requests found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div id="confirmModal" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 transform scale-95 transition-transform duration-300" id="confirmModalContent">
            <div class="text-center">
                <div id="modalIcon" class="mb-4 flex justify-center"></div>
                <h3 class="text-lg font-bold text-gray-800" id="modalTitle">Confirm Action</h3>
                <p class="text-sm text-gray-600 mt-2" id="modalDesc">Are you sure you want to proceed?</p>
            </div>
            <div class="mt-6 flex gap-3">
                <button onclick="closeConfirmModal()" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                <button id="btnConfirmAction" class="flex-1 px-4 py-2 text-sm font-medium text-white rounded-lg shadow">Confirm</button>
            </div>
        </div>
    </div>

    <div id="simpleViolationsModal" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 relative">
            <button onclick="closeSimpleViolationsModal()" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 text-2xl">&times;</button>
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-accent-900">
                <i class="fa fa-exclamation-triangle text-primary-500"></i> User Violations
            </h3>
            <div id="simpleViolationsContent" class="max-h-[60vh] overflow-y-auto custom-scrollbar min-h-[40px]"></div>
        </div>
    </div>

    <div id="cartModal" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6 relative">
            <button onclick="document.getElementById('cartModal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 text-2xl">&times;</button>
            <h3 class="text-xl font-bold mb-4 flex items-center gap-2 text-accent-900"><i class="fa fa-shopping-cart text-primary-500"></i> Customer Cart</h3>
            <div id="cartContent" class="max-h-[60vh] overflow-y-auto custom-scrollbar min-h-[100px]"></div>
        </div>
    </div>

    <script>
        // --- TABS & SEARCH ---
        function switchTab(tab) {
            const sectionAll = document.getElementById('section-all');
            const sectionReq = document.getElementById('section-req');
            const btnAll = document.getElementById('btn-all');
            const btnReq = document.getElementById('btn-req');

            if (tab === 'all') {
                sectionAll.classList.remove('hidden');
                sectionReq.classList.add('hidden');
                btnAll.className = "px-4 py-2 text-sm font-medium rounded-md bg-white shadow text-primary-600 transition-all";
                btnReq.className = "px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:bg-white transition-all";
            } else {
                sectionAll.classList.add('hidden');
                sectionReq.classList.remove('hidden');
                btnReq.className = "px-4 py-2 text-sm font-medium rounded-md bg-white shadow text-primary-600 transition-all";
                btnAll.className = "px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:bg-white transition-all";
            }
        }

        function filterTable() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#table-body-all tr');
            let visibleCount = 0;
            rows.forEach(row => {
                if (row.getAttribute('data-search').includes(input)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            const noResults = document.getElementById('no-results');
            noResults.classList.toggle('hidden', visibleCount > 0 || rows.length === 0);
        }

        // --- STATUS TOGGLE LOGIC ---
        let currentAction = {};

        function confirmToggle(userId, currentStatus, requestId = null) {
            const modal = document.getElementById('confirmModal');
            const content = document.getElementById('confirmModalContent');
            const title = document.getElementById('modalTitle');
            const desc = document.getElementById('modalDesc');
            const icon = document.getElementById('modalIcon');
            const btn = document.getElementById('btnConfirmAction');

            currentAction = { userId, currentStatus, requestId };

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);

            if (currentStatus === 'active') {
                title.innerText = 'Deactivate Account?';
                desc.innerText = 'This user will be logged out and lose access immediately.';
                icon.innerHTML = '<div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center"><i class="fas fa-user-slash text-3xl text-red-500"></i></div>';
                btn.innerText = 'Yes, Deactivate';
                btn.className = "flex-1 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 shadow transition-colors";
            } else {
                title.innerText = 'Activate Account?';
                desc.innerText = 'This user will regain access to their account.';
                icon.innerHTML = '<div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center"><i class="fas fa-user-check text-3xl text-green-500"></i></div>';
                btn.innerText = 'Yes, Activate';
                btn.className = "flex-1 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 shadow transition-colors";
            }

            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
            newBtn.addEventListener('click', () => executeToggle(newBtn));
        }

        function closeConfirmModal() {
            const modal = document.getElementById('confirmModal');
            const content = document.getElementById('confirmModalContent');
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        function executeToggle(btnElement) {
            const { userId, currentStatus, requestId } = currentAction;
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            
            btnElement.innerText = 'Processing...';
            btnElement.disabled = true;

            const formData = new FormData();
            formData.append('action', 'toggle_status');
            formData.append('user_id', userId);
            formData.append('new_status', newStatus);
            formData.append('csrf_token', '<?php echo $csrf_token; ?>');
            if (requestId) formData.append('request_id', requestId);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // Check if the response is OK (200)
                if (!response.ok) {
                    throw new Error('Server Error');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateUIRow(userId, data.new_status);
                    
                    if (data.request_status === 'APPROVED' && requestId) {
                        removeRequestRow(requestId);
                    }

                    showToast(data.message, 'success');
                    closeConfirmModal();
                } else {
                    showToast(data.message, 'error');
                    btnElement.innerText = 'Try Again';
                    btnElement.disabled = false;
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                showToast('Server connection failed.', 'error');
                closeConfirmModal();
            });
        }

        function updateUIRow(id, status) {
            const badge = document.getElementById(`badge-${id}`);
            const btn = document.getElementById(`btn-toggle-${id}`);
            
            if (badge) {
                if (status === 'active') {
                    badge.className = "px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800";
                    badge.innerText = "Active";
                } else {
                    badge.className = "px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800";
                    badge.innerText = "Inactive";
                }
            }

            if (btn) {
                if (status === 'active') {
                    btn.className = "bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded shadow-sm text-xs flex items-center gap-1 transition-colors";
                    btn.innerHTML = '<i class="fa fa-user-slash"></i> Deactivate';
                    btn.setAttribute('onclick', `confirmToggle(${id}, 'active')`);
                } else {
                    btn.className = "bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded shadow-sm text-xs flex items-center gap-1 transition-colors";
                    btn.innerHTML = '<i class="fa fa-user-check"></i> Activate';
                    btn.setAttribute('onclick', `confirmToggle(${id}, 'inactive')`);
                }
            }
        }

        function removeRequestRow(requestId) {
            const row = document.getElementById(`req-row-${requestId}`);
            if (row) {
                row.classList.add('fade-out');
                setTimeout(() => {
                    row.remove();
                    renumberRequests();
                    updateRequestCounterBadge();
                }, 500);
            }
        }

        function renumberRequests() {
            const tbody = document.getElementById('req-table-body');
            const rows = tbody.querySelectorAll('tr:not(#no-req-row)');
            
            if (rows.length === 0) {
                if(!document.getElementById('no-req-row')) {
                     const noRow = document.createElement('tr');
                     noRow.id = 'no-req-row';
                     noRow.innerHTML = '<td colspan="7" class="px-6 py-8 text-center text-gray-500">No reactivation requests found.</td>';
                     tbody.appendChild(noRow);
                }
            } else {
                let num = 1;
                rows.forEach(row => {
                    const numCell = row.querySelector('.number-cell');
                    if (numCell) {
                        numCell.innerText = `#${num}`;
                        num++;
                    }
                });
            }
        }

        function updateRequestCounterBadge() {
            const tbody = document.getElementById('req-table-body');
            const rows = tbody.querySelectorAll('tr:not(#no-req-row)');
            const count = rows.length;
            
            const badge = document.getElementById('req-badge-count');
            if (badge) {
                badge.innerText = count;
                if (count > 0) {
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        }

        // --- VIOLATION MODAL (SIMPLE VERSION) ---
        function showViolationsModal(userId, fullName) {
            const modal = document.getElementById('simpleViolationsModal');
            const content = document.getElementById('simpleViolationsContent');
            content.innerHTML = '<div class="p-8 text-center text-gray-500"><i class="fa fa-circle-notch fa-spin text-2xl mb-2"></i><p>Loading violations...</p></div>';
            modal.classList.remove('hidden');
            
            fetch(`../connection/get_buyer_warning_types.php?user_id=${userId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success' && data.violations.length > 0) {
                        let html = `<div class='mb-2 text-sm text-gray-700'><b>${fullName}</b></div>`;
                        html += `<ul class='space-y-2'>`;
                        data.violations.forEach(v => {
                            html += `<li class='border-l-4 border-primary-500 pl-3 py-2 bg-orange-50 rounded text-sm'>
                                <div class='font-bold text-primary-700'>${v.warning_type.replace(/_/g, ' ')}</div>
                                <div class='text-xs text-gray-600'>${v.warning_notes || ''}</div>
                                <div class='text-[10px] text-gray-400'>${v.warning_date ? new Date(v.warning_date).toLocaleDateString() : ''}</div>
                            </li>`;
                        });
                        html += `</ul>`;
                        content.innerHTML = html;
                    } else {
                        content.innerHTML = `<div class='py-8 text-center text-gray-500'><i class='fa fa-shield-check text-2xl mb-2 text-green-400'></i><div class='font-semibold'>No violations found.</div></div>`;
                    }
                })
                .catch(err => {
                    console.error(err);
                    content.innerHTML = '<div class="p-4 text-center text-red-500">Failed to load data.</div>';
                });
        }

        function closeSimpleViolationsModal() {
            document.getElementById('simpleViolationsModal').classList.add('hidden');
        }

        // --- CART MODAL ---
        function openCart(userId) {
            const modal = document.getElementById('cartModal');
            const content = document.getElementById('cartContent');
            modal.classList.remove('hidden');
            content.innerHTML = '<div class="p-8 text-center text-gray-500"><i class="fa fa-circle-notch fa-spin text-2xl mb-2"></i><p>Loading items...</p></div>';
            
            fetch(`../connection/get_user_cart.php?user_id=${userId}`)
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success' && data.items.length > 0) {
                        let html = `<table class="w-full text-left border-collapse">
                                        <tr class="border-b text-gray-500 text-sm bg-gray-50">
                                            <th class="p-3 font-medium">Product</th>
                                            <th class="p-3 font-medium">Price</th>
                                            <th class="p-3 font-medium">Qty</th>
                                        </tr>`;
                        data.items.forEach(item => {
                             const img = item.product_image || 'https://via.placeholder.com/40';
                             html += `<tr class="border-b hover:bg-gray-50 transition-colors">
                                        <td class="p-3 flex items-center gap-3">
                                            <img src="${img}" class="w-10 h-10 rounded object-cover border bg-white">
                                            <span class="text-sm font-medium text-gray-800">${item.product_name}</span>
                                        </td>
                                        <td class="p-3 text-primary-600 font-bold text-sm">₱${parseFloat(item.product_price).toLocaleString()}</td>
                                        <td class="p-3 text-gray-600 text-sm">${item.quantity}</td>
                                      </tr>`;
                        });
                        html += `</table>`;
                        content.innerHTML = html;
                    } else {
                        content.innerHTML = '<div class="p-10 text-center text-gray-400"><i class="fa fa-basket-shopping text-4xl mb-3 opacity-20"></i><p>Cart is empty.</p></div>';
                    }
                })
                .catch(err => {
                    content.innerHTML = '<div class="p-8 text-center text-red-500">Failed to load cart.</div>';
                });
        }

        // --- TOAST ---
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
    </script>
</body>
</html>