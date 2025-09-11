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
                        <a href="<?= $isUserBranch ? 'cancelOrders.php' : '#' ?>" 
                           class="view-orders-btn text-center <?= $isUserBranch ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-300 text-gray-400 cursor-not-allowed opacity-60' ?> 
                           font-medium px-5 py-2.5 rounded-lg shadow-sm transition flex items-center justify-center w-full"
                           data-branch="<?= $branch['name'] ?>" 
                           <?= $isUserBranch ? '' : 'disabled' ?>>
                            <i class="fas fa-eye mr-2"></i> View Orders
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
