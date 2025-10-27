<?php 
include '../includes/sidebar.php'; 
require_once '../connection/connection.php';

// Get branch_id from session (set by sidebar.php)
$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 1;

// Key Metrics Queries
// Daily Revenue and Customer Count (for Revenue Performance Table)
$daily_performance = [];
$stmt = $db_connection->prepare('
    SELECT DATE(o.order_date) AS day, 
           SUM(o.total_amount) AS revenue, 
           COUNT(DISTINCT o.user_id) AS customers
    FROM orders o
    WHERE o.branch_id = :branch_id
    GROUP BY day
    ORDER BY day DESC
    LIMIT 7
');
$stmt->execute(['branch_id' => $branch_id]);
while ($row = $stmt->fetch()) {
    $daily_performance[] = $row;
}

// Total Revenue
$stmt = $db_connection->prepare('SELECT SUM(o.total_amount) AS revenue FROM orders o WHERE o.branch_id = :branch_id');
$stmt->execute(['branch_id' => $branch_id]);
$total_revenue = $stmt->fetchColumn() ?: 0;

// Orders Count
$stmt = $db_connection->prepare('SELECT COUNT(*) FROM orders WHERE branch_id = :branch_id');
$stmt->execute(['branch_id' => $branch_id]);
$orders_count = $stmt->fetchColumn() ?: 0;

// Customers Count
$stmt = $db_connection->prepare('SELECT COUNT(DISTINCT o.user_id) FROM orders o WHERE o.branch_id = :branch_id');
$stmt->execute(['branch_id' => $branch_id]);
$customers_count = $stmt->fetchColumn() ?: 0;

// Products Sold
$stmt = $db_connection->prepare('SELECT SUM(oi.quantity) FROM order_items oi INNER JOIN orders o ON oi.order_id = o.order_id WHERE o.branch_id = :branch_id');
$stmt->execute(['branch_id' => $branch_id]);
$products_sold = $stmt->fetchColumn() ?: 0;

// Sales by Branch (for chart)
$branch_sales = [];
$stmt = $db_connection->query('SELECT b.branch_name, SUM(o.total_amount) AS sales FROM branches b LEFT JOIN orders o ON b.branch_id = o.branch_id GROUP BY b.branch_id');
while ($row = $stmt->fetch()) {
    $branch_sales[] = $row;
}

// Top Selling Categories (for chart)
$category_sales = [];
$stmt = $db_connection->prepare('SELECT tc.classification_name, SUM(oi.quantity) AS sold FROM order_items oi INNER JOIN products p ON oi.product_id = p.product_id INNER JOIN product_classifications pc ON p.product_id = pc.product_id INNER JOIN tile_classifications tc ON pc.classification_id = tc.classification_id INNER JOIN orders o ON oi.order_id = o.order_id WHERE o.branch_id = :branch_id GROUP BY tc.classification_id ORDER BY sold DESC LIMIT 6');
$stmt->execute(['branch_id' => $branch_id]);
while ($row = $stmt->fetch()) {
    $category_sales[] = $row;
}

// Popular Tile Designs (table)
$design_sales = [];
$stmt = $db_connection->prepare('SELECT td.design_name, SUM(oi.quantity) AS sold, SUM(oi.quantity * p.product_price) AS revenue FROM order_items oi INNER JOIN products p ON oi.product_id = p.product_id INNER JOIN product_designs pd ON p.product_id = pd.product_id INNER JOIN tile_designs td ON pd.design_id = td.design_id INNER JOIN orders o ON oi.order_id = o.order_id WHERE o.branch_id = :branch_id GROUP BY td.design_id ORDER BY sold DESC LIMIT 5');
$stmt->execute(['branch_id' => $branch_id]);
while ($row = $stmt->fetch()) {
    $design_sales[] = $row;
}

// Recent Orders (table)
$recent_orders = [];
$stmt = $db_connection->prepare('SELECT o.order_reference, u.full_name, o.total_amount, o.order_status FROM orders o INNER JOIN users u ON o.user_id = u.id WHERE o.branch_id = :branch_id ORDER BY o.order_id DESC LIMIT 5');
$stmt->execute(['branch_id' => $branch_id]);
while ($row = $stmt->fetch()) {
    $recent_orders[] = $row;
}

// Popular Tile Sizes (chart)
$size_sales = [];
$stmt = $db_connection->prepare('SELECT ts.size_name, SUM(oi.quantity) AS sold FROM order_items oi INNER JOIN products p ON oi.product_id = p.product_id INNER JOIN product_sizes ps ON p.product_id = ps.product_id INNER JOIN tile_sizes ts ON ps.size_id = ts.size_id INNER JOIN orders o ON oi.order_id = o.order_id WHERE o.branch_id = :branch_id GROUP BY ts.size_id ORDER BY sold DESC');
$stmt->execute(['branch_id' => $branch_id]);
while ($row = $stmt->fetch()) {
    $size_sales[] = $row;
}

// Tile Applications (chart)
$application_sales = [];
$stmt = $db_connection->prepare('SELECT bfc.best_for_name, SUM(oi.quantity) AS sold FROM order_items oi INNER JOIN products p ON oi.product_id = p.product_id INNER JOIN product_best_for pbf ON p.product_id = pbf.product_id INNER JOIN best_for_categories bfc ON pbf.best_for_id = bfc.best_for_id INNER JOIN orders o ON oi.order_id = o.order_id WHERE o.branch_id = :branch_id GROUP BY bfc.best_for_id ORDER BY sold DESC');
$stmt->execute(['branch_id' => $branch_id]);
while ($row = $stmt->fetch()) {
    $application_sales[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Analytics Dashboard - Rich Anne Tiles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': {
                            '50': '#fef8f6',
                            '100': '#fdf0ec',
                            '200': '#fbd9cc',
                            '300': '#f8c2ad',
                            '400': '#f2946f',
                            '500': '#ed6631',
                            '600': '#d55c2c',
                            '700': '#b24d25',
                            '800': '#8e3d1d',
                            '900': '#743218'
                        },
                        'accent': {
                            '50': '#f6f6f6',
                            '100': '#e7e7e7',
                            '200': '#d1d1d1',
                            '300': '#b0b0b0',
                            '400': '#888888',
                            '500': '#6d6d6d',
                            '600': '#5d5d5d',
                            '700': '#4f4f4f',
                            '800': '#454545',
                            '900': '#3d3d3d'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Montserrat', 'sans-serif'],
                    },
                    boxShadow: {
                        'soft': '0 2px 15px rgba(0, 0, 0, 0.05)',
                        'medium': '0 5px 25px rgba(0, 0, 0, 0.08)',
                        'large': '0 10px 40px rgba(0, 0, 0, 0.12)',
                        'xl': '0 15px 50px rgba(0, 0, 0, 0.15)'
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'fade-in': 'fadeIn 0.8s ease-out',
                        'slide-up': 'slideUp 0.8s ease-out',
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
        }

        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left var(--transition-speed);
        }

        html.sidebar-collapsed .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .dashboard-tab {
            transition: all 0.3s ease;
        }

        .dashboard-tab.active {
            background: linear-gradient(135deg, #ed6631 0%, #d55c2c 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(237, 102, 49, 0.3);
        }

        .metric-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: linear-gradient(135deg, #ffffff 0%, #fdf0ec 100%);
            border: 1px solid rgba(237, 102, 49, 0.1);
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .metric-icon {
            background: linear-gradient(135deg, #ed6631 0%, #d55c2c 100%);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .floating-element {
            animation: float 8s ease-in-out infinite;
        }

        .dashboard-card {
            background: white;
            border: 1px solid rgba(237, 102, 49, 0.1);
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-completed {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-processing {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-shipped {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .trend-up {
            color: #16a34a;
        }

        .trend-down {
            color: #dc2626;
        }

        .table-row-hover:hover {
            background-color: #fdf0ec;
        }
    </style>
</head>
<body class="font-sans text-accent-700">
    <div class="main-content p-6">
        <!-- Header -->
        <div class="dashboard-card rounded-2xl p-6 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="mb-4 md:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold text-accent-900 mb-2 font-heading">Analytics Dashboard</h1>
                <p class="text-accent-600">Monitor your tile business performance in real-time</p>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full md:w-auto">
                <div class="flex items-center gap-3">
                    <span class="text-sm text-accent-600 font-medium">View:</span>
                    <select class="px-4 py-2 border border-accent-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                        <option>Today</option>
                        <option selected>This Week</option>
                        <option>This Month</option>
                        <option>This Quarter</option>
                        <option>This Year</option>
                    </select>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-accent-600 font-medium">Compare:</span>
                    <select class="px-4 py-2 border border-accent-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                        <option>Previous Period</option>
                        <option selected>Last Week</option>
                        <option>Last Month</option>
                        <option>Last Year</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Dashboard Tabs -->
        <div class="dashboard-card rounded-2xl p-2 mb-6 flex overflow-x-auto">
            <button id="tab-overview" class="dashboard-tab active px-6 py-3 rounded-xl font-medium text-sm mx-1">Overview Dashboard</button>
            <button id="tab-login-trail" class="dashboard-tab px-6 py-3 rounded-xl font-medium text-sm mx-1">Customer Activity Trail</button>
        </div>

        <!-- Overview Dashboard -->
        <div id="overview-dashboard" class="dashboard-content">
            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
                <!-- Revenue Card -->
                <div class="metric-card rounded-2xl p-6 floating-element">
                    <div class="flex justify-between items-start mb-4">
                        <div class="text-sm font-medium text-accent-500">TOTAL REVENUE</div>
                        <div class="w-12 h-12 rounded-xl metric-icon flex items-center justify-center shadow-md">
                            <i class="fas fa-dollar-sign text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-accent-900 mb-2">₱<?= number_format($total_revenue, 2) ?></div>
                    <div class="text-sm trend-up flex items-center">
                        <i class="fas fa-arrow-up mr-1"></i> 12.5% vs last week
                    </div>
                </div>

                <!-- Orders Card -->
                <div class="metric-card rounded-2xl p-6 floating-element" style="animation-delay: 0.5s">
                    <div class="flex justify-between items-start mb-4">
                        <div class="text-sm font-medium text-accent-500">ORDERS</div>
                        <div class="w-12 h-12 rounded-xl metric-icon flex items-center justify-center shadow-md">
                            <i class="fas fa-shopping-cart text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-accent-900 mb-2"><?= $orders_count ?></div>
                    <div class="text-sm trend-up flex items-center">
                        <i class="fas fa-arrow-up mr-1"></i> 8.3% vs last week
                    </div>
                </div>

                <!-- Customers Card -->
                <div class="metric-card rounded-2xl p-6 floating-element" style="animation-delay: 1s">
                    <div class="flex justify-between items-start mb-4">
                        <div class="text-sm font-medium text-accent-500">CUSTOMERS</div>
                        <div class="w-12 h-12 rounded-xl metric-icon flex items-center justify-center shadow-md">
                            <i class="fas fa-users text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-accent-900 mb-2"><?= $customers_count ?></div>
                    <div class="text-sm trend-up flex items-center">
                        <i class="fas fa-arrow-up mr-1"></i> 5.2% vs last week
                    </div>
                </div>

                <!-- Products Card -->
                <div class="metric-card rounded-2xl p-6 floating-element" style="animation-delay: 1.5s">
                    <div class="flex justify-between items-start mb-4">
                        <div class="text-sm font-medium text-accent-500">PRODUCTS SOLD</div>
                        <div class="w-12 h-12 rounded-xl metric-icon flex items-center justify-center shadow-md">
                            <i class="fas fa-box text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-accent-900 mb-2"><?= $products_sold ?></div>
                    <div class="text-sm trend-up flex items-center">
                        <i class="fas fa-arrow-up mr-1"></i> 15.7% vs last week
                    </div>
                </div>
            </div>

            <!-- Sales Chart -->
            <div class="dashboard-card rounded-2xl p-6 mb-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-5">
                    <h2 class="text-xl font-semibold text-accent-900 mb-2 md:mb-0 font-heading">Revenue Performance</h2>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-accent-600">Last 7 days</span>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
                <!-- Revenue Performance Table -->
                <div class="overflow-x-auto mt-6">
                    <table class="w-full text-sm text-left text-accent-600">
                        <thead class="text-xs text-accent-700 uppercase bg-accent-50">
                            <tr>
                                <th class="px-4 py-3 font-medium">Date</th>
                                <th class="px-4 py-3 font-medium">Revenue</th>
                                <th class="px-4 py-3 font-medium">Customers</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($daily_performance as $row) { ?>
                            <tr class="border-b border-accent-100 table-row-hover">
                                <td class="px-4 py-3 font-medium text-primary-600"><?= htmlspecialchars($row['day']) ?></td>
                                <td class="px-4 py-3 font-semibold">₱<?= number_format($row['revenue'], 2) ?></td>
                                <td class="px-4 py-3"><?= (int)$row['customers'] ?> customers</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Branch Performance -->
                <div class="dashboard-card rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-accent-900 mb-5 font-heading">Sales by Branch</h2>
                    <div class="chart-container">
                        <canvas id="branchChart"></canvas>
                    </div>
                </div>

                <!-- Top Selling Categories -->
                <div class="dashboard-card rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-accent-900 mb-5 font-heading">Top Selling Categories</h2>
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Tile Designs Performance -->
                <div class="dashboard-card rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-accent-900 mb-5 font-heading">Popular Tile Designs</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-accent-600">
                            <thead class="text-xs text-accent-700 uppercase bg-accent-50">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Design</th>
                                    <th class="px-4 py-3 font-medium">Units Sold</th>
                                    <th class="px-4 py-3 font-medium">Revenue</th>
                                    <th class="px-4 py-3 font-medium">Rank</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($design_sales as $index => $row) { ?>
                                <tr class="border-b border-accent-100 table-row-hover">
                                    <td class="px-4 py-3 font-medium"><?= htmlspecialchars($row['design_name']) ?></td>
                                    <td class="px-4 py-3"><?= (int)$row['sold'] ?> units</td>
                                    <td class="px-4 py-3 font-semibold">₱<?= number_format($row['revenue'], 2) ?></td>
                                    <td class="px-4 py-3">
                                        <?php if ($index == 0) { ?>
                                            <span class="status-badge bg-yellow-100 text-yellow-800">#1</span>
                                        <?php } elseif ($index == 1) { ?>
                                            <span class="status-badge bg-gray-100 text-gray-800">#2</span>
                                        <?php } elseif ($index == 2) { ?>
                                            <span class="status-badge bg-orange-100 text-orange-800">#3</span>
                                        <?php } else { ?>
                                            <span class="status-badge bg-accent-100 text-accent-800">#<?= $index + 1 ?></span>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="dashboard-card rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-accent-900 mb-5 font-heading">Recent Orders</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-accent-600">
                            <thead class="text-xs text-accent-700 uppercase bg-accent-50">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Order ID</th>
                                    <th class="px-4 py-3 font-medium">Customer</th>
                                    <th class="px-4 py-3 font-medium">Amount</th>
                                    <th class="px-4 py-3 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $row) { ?>
                                <tr class="border-b border-accent-100 table-row-hover">
                                    <td class="px-4 py-3 font-medium text-primary-600">#<?= htmlspecialchars($row['order_reference']) ?></td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($row['full_name']) ?></td>
                                    <td class="px-4 py-3 font-semibold">₱<?= number_format($row['total_amount'], 2) ?></td>
                                    <td class="px-4 py-3">
                                        <?php 
                                        $status = strtolower($row['order_status']);
                                        $color = 'bg-accent-100 text-accent-800';
                                        if ($status === 'completed') $color = 'status-completed';
                                        elseif ($status === 'processing') $color = 'status-processing';
                                        elseif ($status === 'shipped') $color = 'status-shipped';
                                        elseif ($status === 'cancelled') $color = 'status-cancelled';
                                        ?>
                                        <span class="status-badge <?= $color ?>"><?= htmlspecialchars($row['order_status']) ?></span>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tile Sizes & Best For -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Tile Sizes -->
                <div class="dashboard-card rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-accent-900 mb-5 font-heading">Popular Tile Sizes</h2>
                    <div class="chart-container">
                        <canvas id="sizeChart"></canvas>
                    </div>
                </div>

                <!-- Best For Applications -->
                <div class="dashboard-card rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-accent-900 mb-5 font-heading">Tile Applications</h2>
                    <div class="chart-container">
                        <canvas id="applicationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Branch Trail Modal -->
        <div id="customerTrailModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-40 flex items-center justify-center p-4">
            <div class="dashboard-card w-full max-w-4xl p-8 relative max-h-[90vh] overflow-y-auto">
                <button id="closeTrailModal" class="absolute top-4 right-4 text-accent-400 hover:text-accent-700 text-2xl">&times;</button>
                <h2 class="text-2xl font-bold mb-6 text-primary-600 font-heading">Customer Activity Trail</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-accent-600">
                        <thead class="text-xs text-accent-700 uppercase bg-accent-50">
                            <tr>
                                <th class="px-4 py-3 font-medium">Customer</th>
                                <th class="px-4 py-3 font-medium">Branch</th>
                                <th class="px-4 py-3 font-medium">Event</th>
                                <th class="px-4 py-3 font-medium">Date/Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $trail_stmt = $db_connection->query('SELECT t.*, u.full_name, b.branch_name FROM customer_branch_trail t INNER JOIN users u ON t.user_id = u.id INNER JOIN branches b ON t.branch_id = b.branch_id ORDER BY t.created_at DESC LIMIT 50');
                            while ($trail = $trail_stmt->fetch()) {
                            ?>
                            <tr class="border-b border-accent-100 table-row-hover">
                                <td class="px-4 py-3 font-medium text-primary-600"><?php echo htmlspecialchars($trail['full_name']); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($trail['branch_name']); ?></td>
                                <td class="px-4 py-3">
                                    <span class="status-badge <?php echo $trail['event_type'] === 'login_location' ? 'status-completed' : 'status-processing'; ?>">
                                        <?php echo $trail['event_type'] === 'login_location' ? 'Login Location' : 'Branch Change'; ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3"><?php echo date('M d, Y h:i A', strtotime($trail['created_at'])); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching for Customer Trail
            document.getElementById('tab-login-trail').addEventListener('click', function() {
                document.getElementById('customerTrailModal').classList.remove('hidden');
            });
            document.getElementById('closeTrailModal').addEventListener('click', function() {
                document.getElementById('customerTrailModal').classList.add('hidden');
            });

            // Revenue Chart (dynamic from PHP)
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const revenueLabels = <?php echo json_encode(array_reverse(array_map(function($row){ return $row['day']; }, $daily_performance))); ?>;
            const revenueData = <?php echo json_encode(array_reverse(array_map(function($row){ return (float)$row['revenue']; }, $daily_performance))); ?>;
            const revenueChart = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: revenueLabels,
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: revenueData,
                        borderColor: '#ed6631',
                        backgroundColor: 'rgba(237, 102, 49, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return '₱' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₱' + (value / 1000).toFixed(0) + 'K';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Branch Performance Chart
            const branchCtx = document.getElementById('branchChart').getContext('2d');
            const branchChart = new Chart(branchCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_column($branch_sales, 'branch_name')) ?>,
                    datasets: [{
                        label: 'Sales (₱)',
                        data: <?= json_encode(array_map(function($b){return (float)$b['sales'];}, $branch_sales)) ?>,
                        backgroundColor: [
                            'rgba(237, 102, 49, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(139, 92, 246, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(239, 68, 68, 0.7)'
                        ],
                        borderColor: [
                            'rgb(237, 102, 49)',
                            'rgb(16, 185, 129)',
                            'rgb(139, 92, 246)',
                            'rgb(245, 158, 11)',
                            'rgb(239, 68, 68)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₱' + (value / 1000).toFixed(0) + 'K';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Category Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            const categoryChart = new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode(array_column($category_sales, 'classification_name')) ?>,
                    datasets: [{
                        data: <?= json_encode(array_map(function($c){return (int)$c['sold'];}, $category_sales)) ?>,
                        backgroundColor: [
                            'rgba(237, 102, 49, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(139, 92, 246, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(239, 68, 68, 0.7)',
                            'rgba(99, 102, 241, 0.7)'
                        ],
                        borderColor: [
                            'rgb(237, 102, 49)',
                            'rgb(16, 185, 129)',
                            'rgb(139, 92, 246)',
                            'rgb(245, 158, 11)',
                            'rgb(239, 68, 68)',
                            'rgb(99, 102, 241)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });

            // Size Chart
            const sizeCtx = document.getElementById('sizeChart').getContext('2d');
            const sizeChart = new Chart(sizeCtx, {
                type: 'pie',
                data: {
                    labels: <?= json_encode(array_column($size_sales, 'size_name')) ?>,
                    datasets: [{
                        data: <?= json_encode(array_map(function($s){return (int)$s['sold'];}, $size_sales)) ?>,
                        backgroundColor: [
                            'rgba(237, 102, 49, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(139, 92, 246, 0.7)'
                        ],
                        borderColor: [
                            'rgb(237, 102, 49)',
                            'rgb(16, 185, 129)',
                            'rgb(245, 158, 11)',
                            'rgb(139, 92, 246)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });

            // Application Chart
            const applicationCtx = document.getElementById('applicationChart').getContext('2d');
            const applicationChart = new Chart(applicationCtx, {
                type: 'polarArea',
                data: {
                    labels: <?= json_encode(array_column($application_sales, 'best_for_name')) ?>,
                    datasets: [{
                        data: <?= json_encode(array_map(function($a){return (int)$a['sold'];}, $application_sales)) ?>,
                        backgroundColor: [
                            'rgba(237, 102, 49, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(139, 92, 246, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(239, 68, 68, 0.7)'
                        ],
                        borderColor: [
                            'rgb(237, 102, 49)',
                            'rgb(16, 185, 129)',
                            'rgb(139, 92, 246)',
                            'rgb(245, 158, 11)',
                            'rgb(239, 68, 68)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>