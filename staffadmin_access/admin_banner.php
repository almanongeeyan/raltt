<?php
session_start();
include '../includes/sidebar.php';
// Use branch_id (int) from session and map to branch name for display and logic
$branch_names = [
    1 => 'Deparo Branch',
    2 => 'Vanguard Branch',
    3 => 'Brixton Branch',
    4 => 'Samaria Branch',
    5 => 'Kiko Branch',
];
$user_branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
$user_branch_name = isset($branch_names[$user_branch_id]) ? $branch_names[$user_branch_id] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Branches Banner Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { display: flex; min-height: 100vh; }
        .main-content-wrapper { flex: 1; padding-left: 0; transition: padding-left 0.3s ease; }
        @media (min-width: 768px) { .main-content-wrapper { padding-left: 250px; } }
        .branch-card { transition: all 0.3s ease; }
        .branch-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1); }
        .banner-count { background-color: #eff8ff; color: #1570ef; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="main-content-wrapper">
        <main class="min-h-screen">
            <div class="max-w-7xl mx-auto py-8 px-4">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-boxes-stacked mr-3 text-blue-600"></i>Branches Banner Dashboard
                    </h1>
                    <p class="text-gray-600 mt-2">View and manage banners for each branch</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
                    <?php
                    $branches = [
                        3 => 'Brixton Branch',
                        4 => 'Samaria Branch',
                        2 => 'Vanguard Branch',
                        1 => 'Deparo Branch',
                        5 => 'Kiko Branch',
                    ];
                    foreach ($branches as $branch_id => $branch_name):
                        $isUserBranch = ($user_branch_name === $branch_name);
                    ?>
                    <div class="branch-card bg-white rounded-2xl shadow-lg p-6 flex flex-col justify-between items-center border border-blue-100 relative mx-auto max-w-xs">
                        <div class="rounded-full bg-blue-100 p-4 mb-3 flex items-center justify-center">
                            <i class="fas fa-store text-2xl text-blue-600"></i>
                        </div>
                        <div class="font-bold text-lg text-gray-900 mb-2 text-center tracking-wide flex-1 flex items-center justify-center"><?= $branch_name ?></div>
                        <div class="text-sm text-gray-600 mb-4 flex-1 flex items-center justify-center banner-count" id="branch-count-<?= $branch_id ?>">...</div>
                        <button class="view-banners-btn <?= $isUserBranch ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-300 text-gray-400 cursor-not-allowed opacity-60' ?> font-medium px-5 py-2.5 rounded-lg shadow-sm transition flex items-center mt-2"
                            data-branch="<?= $branch_name ?>" data-branchid="<?= $branch_id ?>" <?= $isUserBranch ? '' : 'disabled' ?> >
                            <i class="fas fa-eye mr-2"></i> View Banners
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div id="bannersModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-40 hidden">
                    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-8 relative">
                        <button id="closeBannersModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl focus:outline-none"><i class="fas fa-times"></i></button>
                        <h2 id="modalBranchTitle" class="text-2xl font-bold text-gray-800 mb-6 flex items-center"><i class="fas fa-store mr-2 text-blue-600"></i>Banners for <span class="ml-2" id="branchName"></span></h2>
                        <div id="bannersTableContainer"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const userBranch = <?php echo json_encode($user_branch_name); ?>;
const viewBtns = document.querySelectorAll('.view-banners-btn');
viewBtns.forEach(btn => {
    btn.addEventListener('click', function(e) {
        const branch = this.getAttribute('data-branch');
        const branchid = this.getAttribute('data-branchid');
        if (branch !== userBranch) {
            Swal.fire({
                icon: 'error',
                title: 'Access Denied',
                text: 'You can only view banners for your assigned branch (' + userBranch + ').',
                confirmButtonColor: '#8A421D',
            });
            e.preventDefault();
            return;
        }
        // Redirect to admin_banner.php with branch id
        window.location.href = 'admin_banner.php?branch_id=' + branchid;
    });
});

// Fetch live banner counts for each branch
fetch('processes/get_branch_banner_counts.php')
    .then(res => res.json())
    .then(counts => {
        Object.entries(counts).forEach(([branchId, count]) => {
            const el = document.getElementById('branch-count-' + branchId);
            if (el) el.textContent = count + ' Banners';
        });
    });
</script>
</body>
</html>