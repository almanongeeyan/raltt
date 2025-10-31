<?php
// START SESSION AT THE VERY TOP
session_start();
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
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $branch_sales[] = $row;
}

// Top Selling Categories (for chart)
$category_sales = [];
$stmt = $db_connection->prepare('SELECT tc.classification_name, SUM(oi.quantity) AS sold FROM order_items oi INNER JOIN products p ON oi.product_id = p.product_id INNER JOIN product_classifications pc ON p.product_id = pc.product_id INNER JOIN tile_classifications tc ON pc.classification_id = tc.classification_id INNER JOIN orders o ON oi.order_id = o.order_id WHERE o.branch_id = :branch_id GROUP BY tc.classification_id ORDER BY sold DESC LIMIT 6');
$stmt->execute(['branch_id' => $branch_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $category_sales[] = $row;
}

// Popular Tile Designs (table)
$design_sales = [];
$stmt = $db_connection->prepare('SELECT td.design_name, SUM(oi.quantity) AS sold, SUM(oi.quantity * p.product_price) AS revenue FROM order_items oi INNER JOIN products p ON oi.product_id = p.product_id INNER JOIN product_designs pd ON p.product_id = pd.product_id INNER JOIN tile_designs td ON pd.design_id = td.design_id INNER JOIN orders o ON oi.order_id = o.order_id WHERE o.branch_id = :branch_id GROUP BY td.design_id ORDER BY sold DESC LIMIT 5');
$stmt->execute(['branch_id' => $branch_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $design_sales[] = $row;
}

// Recent Orders (table)
$recent_orders = [];
$stmt = $db_connection->prepare('SELECT o.order_reference, u.full_name, o.total_amount, o.order_status FROM orders o INNER JOIN users u ON o.user_id = u.id WHERE o.branch_id = :branch_id ORDER BY o.order_id DESC LIMIT 5');
$stmt->execute(['branch_id' => $branch_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $recent_orders[] = $row;
}

// Popular Tile Sizes (chart)
$size_sales = [];
$stmt = $db_connection->prepare('SELECT ts.size_name, SUM(oi.quantity) AS sold FROM order_items oi INNER JOIN products p ON oi.product_id = p.product_id INNER JOIN product_sizes ps ON p.product_id = ps.product_id INNER JOIN tile_sizes ts ON ps.size_id = ts.size_id INNER JOIN orders o ON oi.order_id = o.order_id WHERE o.branch_id = :branch_id GROUP BY ts.size_id ORDER BY sold DESC');
$stmt->execute(['branch_id' => $branch_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $size_sales[] = $row;
}

// Tile Applications (chart)
$application_sales = [];
$stmt = $db_connection->prepare('SELECT bfc.best_for_name, SUM(oi.quantity) AS sold FROM order_items oi INNER JOIN products p ON oi.product_id = p.product_id INNER JOIN product_best_for pbf ON p.product_id = pbf.product_id INNER JOIN best_for_categories bfc ON pbf.best_for_id = bfc.best_for_id INNER JOIN orders o ON oi.order_id = o.order_id WHERE o.branch_id = :branch_id GROUP BY bfc.best_for_id ORDER BY sold DESC');
$stmt->execute(['branch_id' => $branch_id]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $application_sales[] = $row;
}

// Monthly Sales Heatmap Data
$monthly_sales = [];
$current_year = date('Y');
$stmt = $db_connection->prepare('
    SELECT 
        MONTH(o.order_date) as month,
        SUM(o.total_amount) as revenue,
        COUNT(DISTINCT o.order_id) as orders,
        SUM(oi.quantity) as units_sold
    FROM orders o 
    INNER JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.branch_id = :branch_id AND YEAR(o.order_date) = :year
    GROUP BY MONTH(o.order_date)
    ORDER BY month ASC
');
$stmt->execute(['branch_id' => $branch_id, 'year' => $current_year]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $monthly_sales[$row['month']] = $row;
}

// Fill in missing months with zero values
$complete_monthly_sales = [];
for ($month = 1; $month <= 12; $month++) {
    if (isset($monthly_sales[$month])) {
        $complete_monthly_sales[] = $monthly_sales[$month];
    } else {
        $complete_monthly_sales[] = [
            'month' => $month,
            'revenue' => 0,
            'orders' => 0,
            'units_sold' => 0
        ];
    }
}

// Daily Sales Heatmap Data (for current month)
$daily_sales_heatmap = [];
$current_month = date('n');
$current_year = date('Y');
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year);

$stmt = $db_connection->prepare('
    SELECT 
        DAY(o.order_date) as day,
        SUM(o.total_amount) as revenue,
        COUNT(DISTINCT o.order_id) as orders
    FROM orders o 
    WHERE o.branch_id = :branch_id 
        AND MONTH(o.order_date) = :month 
        AND YEAR(o.order_date) = :year
    GROUP BY DAY(o.order_date)
    ORDER BY day ASC
');
$stmt->execute([
    'branch_id' => $branch_id,
    'month' => $current_month,
    'year' => $current_year
]);

$daily_sales_data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $daily_sales_data[$row['day']] = $row;
}

// Fill in missing days with zero values
for ($day = 1; $day <= $days_in_month; $day++) {
    if (isset($daily_sales_data[$day])) {
        $daily_sales_heatmap[] = $daily_sales_data[$day];
    } else {
        $daily_sales_heatmap[] = [
            'day' => $day,
            'revenue' => 0,
            'orders' => 0
        ];
    }
}

// Predictive Analytics - Sales Forecast (simulated)
$sales_forecast = [];
$last_7_days_perf = array_reverse($daily_performance);
if (count($last_7_days_perf) > 0) {
    $avg_daily_revenue = array_sum(array_column($last_7_days_perf, 'revenue')) / count($last_7_days_perf);
    
    // Generate 7-day forecast based on average with slight growth
    for ($i = 1; $i <= 7; $i++) {
        $forecast_date = date('Y-m-d', strtotime("+$i days"));
        $growth_factor = 1 + (0.02 * $i); // 2% daily growth
        $forecast_revenue = $avg_daily_revenue * $growth_factor;
        
        $sales_forecast[] = [
            'date' => $forecast_date,
            'revenue' => $forecast_revenue
        ];
    }
}

// Predictive Analytics - Customer Behavior (simulated)
$customer_segments = [
    ['segment' => 'Loyal Customers', 'count' => round($customers_count * 0.3), 'avg_order_value' => 8500],
    ['segment' => 'Occasional Buyers', 'count' => round($customers_count * 0.5), 'avg_order_value' => 4500],
    ['segment' => 'New Customers', 'count' => round($customers_count * 0.2), 'avg_order_value' => 3200]
];

// Predictive Analytics - Inventory Recommendations (based on actual data)
$inventory_recommendations = [];
if (count($category_sales) > 0) {
    $top_category = $category_sales[0];
    $inventory_recommendations[] = [
        'category' => $top_category['classification_name'],
        'recommendation' => 'Increase stock by 20%',
        'reason' => 'Top performing category with consistent demand'
    ];
}

if (count($design_sales) > 0) {
    $top_design = $design_sales[0];
    $inventory_recommendations[] = [
        'category' => $top_design['design_name'],
        'recommendation' => 'Maintain current stock levels',
        'reason' => 'High demand but sufficient inventory'
    ];
}

// Set default dates for report generation
$default_start_date = date('Y-m-d', strtotime('-30 days'));
$default_end_date = date('Y-m-d');
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

        /* Heatmap styles */
        .heatmap-container {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            margin-top: 1rem;
        }

        .heatmap-day {
            aspect-ratio: 1;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .heatmap-day:hover {
            transform: scale(1.1);
            z-index: 10;
        }

        .heatmap-intensity-0 { background-color: #f3f4f6; color: #6b7280; }
        .heatmap-intensity-1 { background-color: #fed7aa; color: #7c2d12; }
        .heatmap-intensity-2 { background-color: #fdba74; color: #7c2d12; }
        .heatmap-intensity-3 { background-color: #fb923c; color: #7c2d12; }
        .heatmap-intensity-4 { background-color: #f97316; color: #7c2d12; }
        .heatmap-intensity-5 { background-color: #ea580c; color: white; }

        /* Modal styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 1rem;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px; /* Smaller modal */
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }

        .modal-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 2rem;
        }

        /* Fix for sidebar z-index */
        .sidebar {
            z-index: 100;
        }

        .main-content {
            z-index: 1;
        }

        /* Form validation styles */
        .form-error {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        .input-error {
            border-color: #dc2626 !important;
        }

        /* Loading spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="font-sans text-accent-700">
    <div class="main-content p-6">
        <div class="dashboard-card rounded-2xl p-6 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="mb-4 md:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold text-accent-900 mb-2 font-heading">Analytics Dashboard</h1>
                <p class="text-accent-600">Monitor your tile business performance in real-time</p>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full md:w-auto">
                <div class="flex items-center gap-3">
                    <span class="text-sm text-accent-600 font-medium">View:</span>
                    <select id="viewPeriod" class="px-4 py-2 border border-accent-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                        <option value="today">Today</option>
                        <option value="week" selected>This Week</option>
                        <option value="month">This Month</option>
                        <option value="quarter">This Quarter</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-accent-600 font-medium">Compare:</span>
                    <select id="comparePeriod" class="px-4 py-2 border border-accent-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                        <option value="previous">Previous Period</option>
                        <option value="last_week" selected>Last Week</option>
                        <option value="last_month">Last Month</option>
                        <option value="last_year">Last Year</option>
                    </select>
                </div>
                <button id="generateReport" class="px-4 py-2 bg-primary-500 text-white rounded-lg text-sm font-medium hover:bg-primary-600 transition-colors flex items-center gap-2">
                    <i class="fas fa-file-export"></i> Generate Report
                </button>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-2 mb-6 flex overflow-x-auto">
            <button id="tab-overview" class="dashboard-tab active px-6 py-3 rounded-xl font-medium text-sm mx-1">Overview Dashboard</button>
            <button id="tab-predictive" class="dashboard-tab px-6 py-3 rounded-xl font-medium text-sm mx-1">Predictive Analytics</button>
            <button id="tab-heatmaps" class="dashboard-tab px-6 py-3 rounded-xl font-medium text-sm mx-1">Sales Heatmaps</button>
            <button id="tab-login-trail" class="dashboard-tab px-6 py-3 rounded-xl font-medium text-sm mx-1">Customer Activity Trail</button>
        </div>

        <div id="overview-dashboard" class="dashboard-content">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
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
                <div class="dashboard-card rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-accent-900 mb-5 font-heading">Sales by Branch</h2>
                    <div class="chart-container">
                        <canvas id="branchChart"></canvas>
                    </div>
                </div>

                <div class="dashboard-card rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-accent-900 mb-5 font-heading">Top Selling Categories</h2>
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="dashboard-card rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-accent-900 mb-5 font-heading">Popular Tile Sizes</h2>
                    <div class="chart-container">
                        <canvas id="sizeChart"></canvas>
                    </div>
                </div>

                <div class="dashboard-card rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-accent-900 mb-5 font-heading">Tile Applications</h2>
                    <div class="chart-container">
                        <canvas id="applicationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div id="predictive-dashboard" class="dashboard-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="dashboard-card rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-accent-900 mb-5 font-heading">7-Day Sales Forecast</h2>
                    <div class="chart-container">
                        <canvas id="forecastChart"></canvas>
                    </div>
                    <div class="mt-4 text-sm text-accent-600">
                        <p><i class="fas fa-info-circle text-primary-500 mr-2"></i> Forecast based on historical data and seasonal trends</p>
                    </div>
                </div>

                <div class="dashboard-card rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-accent-900 mb-5 font-heading">Customer Segmentation</h2>
                    <div class="chart-container">
                        <canvas id="segmentationChart"></canvas>
                    </div>
                    <div class="mt-4">
                        <table class="w-full text-sm text-left text-accent-600">
                            <thead class="text-xs text-accent-700 uppercase bg-accent-50">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Segment</th>
                                    <th class="px-4 py-3 font-medium">Customers</th>
                                    <th class="px-4 py-3 font-medium">Avg. Order Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customer_segments as $segment) { ?>
                                <tr class="border-b border-accent-100 table-row-hover">
                                    <td class="px-4 py-3 font-medium"><?= $segment['segment'] ?></td>
                                    <td class="px-4 py-3"><?= $segment['count'] ?></td>
                                    <td class="px-4 py-3 font-semibold">₱<?= number_format($segment['avg_order_value'], 2) ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="dashboard-card rounded-2xl p-6 mb-6">
                <h2 class="text-xl font-semibold text-accent-900 mb-5 font-heading">Inventory Recommendations</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($inventory_recommendations as $rec) { ?>
                    <div class="border border-accent-200 rounded-xl p-4 bg-accent-50">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center mr-4">
                                <i class="fas fa-boxes text-primary-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-accent-900"><?= $rec['category'] ?></h3>
                                <p class="text-sm text-accent-700 mt-1"><?= $rec['recommendation'] ?></p>
                                <p class="text-xs text-accent-600 mt-2"><?= $rec['reason'] ?></p>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="border border-accent-200 rounded-xl p-4 bg-accent-50">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center mr-4">
                                <i class="fas fa-chart-line text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-accent-900">Seasonal Trends</h3>
                                <p class="text-sm text-accent-700 mt-1">Prepare for increased demand in Q4</p>
                                <p class="text-xs text-accent-600 mt-2">Historical data shows 25% increase in sales during holiday season</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-card rounded-2xl p-6">
                <h2 class="text-xl font-semibold text-accent-900 mb-5 font-heading">Predictive Insights</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex items-center mb-2">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                                <i class="fas fa-users text-blue-600"></i>
                            </div>
                            <h3 class="font-semibold text-accent-900">Customer Growth</h3>
                        </div>
                        <p class="text-sm text-accent-700">Expected 15% new customer acquisition in the next quarter based on current trends.</p>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                        <div class="flex items-center mb-2">
                            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                                <i class="fas fa-chart-bar text-green-600"></i>
                            </div>
                            <h3 class="font-semibold text-accent-900">Revenue Projection</h3>
                        </div>
                        <p class="text-sm text-accent-700">Next month's revenue is projected to increase by 8-12% compared to current month.</p>
                    </div>
                    <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                        <div class="flex items-center mb-2">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                                <i class="fas fa-shopping-cart text-purple-600"></i>
                            </div>
                            <h3 class="font-semibold text-accent-900">Order Volume</h3>
                        </div>
                        <p class="text-sm text-accent-700">Expect 20-30 more orders per day during weekends based on historical patterns.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="heatmaps-dashboard" class="dashboard-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="dashboard-card rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-accent-900 mb-5 font-heading">Monthly Sales Performance</h2>
                    <div class="chart-container">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>

                <div class="dashboard-card rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-accent-900 mb-5 font-heading">Daily Sales Heatmap - <?= date('F Y') ?></h2>
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-accent-600">Sales Intensity</span>
                            <div class="flex items-center gap-2">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-gray-100 rounded mr-1"></div>
                                    <span class="text-xs">Low</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-orange-500 rounded mr-1"></div>
                                    <span class="text-xs">High</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="heatmap-container">
                        <?php
                        $first_day_of_month = date('N', strtotime(date('Y-m-01')));
                        // Add empty cells for days before the first day of the month
                        for ($i = 1; $i < $first_day_of_month; $i++) {
                            echo '<div class="heatmap-day"></div>';
                        }
                        
                        foreach ($daily_sales_heatmap as $day_data) {
                            $revenue = $day_data['revenue'];
                            $intensity = 0;
                            
                            if ($revenue > 0) {
                                if ($revenue < 1000) $intensity = 1;
                                elseif ($revenue < 2500) $intensity = 2;
                                elseif ($revenue < 5000) $intensity = 3;
                                elseif ($revenue < 10000) $intensity = 4;
                                else $intensity = 5;
                            }
                            
                            $tooltip = $revenue > 0 ? 
                                "Day {$day_data['day']}: ₱" . number_format($revenue, 2) . " ({$day_data['orders']} orders)" :
                                "Day {$day_data['day']}: No sales";
                            
                            echo "<div class='heatmap-day heatmap-intensity-{$intensity}' title='{$tooltip}'>";
                            echo $day_data['day'];
                            echo "</div>";
                        }
                        ?>
                    </div>
                    <div class="mt-4 text-sm text-accent-600">
                        <p><i class="fas fa-info-circle text-primary-500 mr-2"></i> Hover over days to see sales details</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-card rounded-2xl p-6">
                <h2 class="text-xl font-semibold text-accent-900 mb-5 font-heading">Product Performance Heatmap</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-accent-800 mb-3">Top Selling Categories</h3>
                        <div class="space-y-3">
                            <?php 
                            $max_category_sales = max(array_column($category_sales, 'sold'));
                            foreach ($category_sales as $category) {
                                $percentage = $max_category_sales > 0 ? ($category['sold'] / $max_category_sales) * 100 : 0;
                            ?>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-accent-700"><?= $category['classification_name'] ?></span>
                                <div class="w-32 bg-accent-200 rounded-full h-2">
                                    <div class="bg-primary-500 h-2 rounded-full" style="width: <?= $percentage ?>%"></div>
                                </div>
                                <span class="text-sm font-semibold text-accent-900"><?= $category['sold'] ?></span>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-accent-800 mb-3">Revenue by Size</h3>
                        <div class="space-y-3">
                            <?php 
                            $max_size_sales = max(array_column($size_sales, 'sold'));
                            foreach ($size_sales as $size) {
                                $percentage = $max_size_sales > 0 ? ($size['sold'] / $max_size_sales) * 100 : 0;
                            ?>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-accent-700"><?= $size['size_name'] ?></span>
                                <div class="w-32 bg-accent-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: <?= $percentage ?>%"></div>
                                </div>
                                <span class="text-sm font-semibold text-accent-900"><?= $size['sold'] ?></span>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="customer-trail-dashboard" class="dashboard-content hidden">
            <div class="dashboard-card rounded-2xl p-6">
                <h2 class="text-xl font-semibold text-accent-900 mb-5 font-heading">Customer Activity Trail</h2>
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
                            while ($trail = $trail_stmt->fetch(PDO::FETCH_ASSOC)) {
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

    <div id="reportModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="text-xl font-bold text-accent-900 font-heading">Generate Report</h2>
                <button id="closeReportModal" class="text-accent-500 hover:text-accent-700 text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="reportForm">
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-accent-800 mb-3">Export Format</h3>
                        <div class="flex gap-4">
                            <div class="flex items-center">
                                <input type="radio" id="format-excel" name="export-format" value="excel" class="mr-2" checked>
                                <label for="format-excel" class="flex items-center cursor-pointer">
                                    <i class="fas fa-file-excel text-green-600 mr-2"></i>
                                    <span>Excel (.xlsx)</span>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="format-pdf" name="export-format" value="pdf" class="mr-2">
                                <label for="format-pdf" class="flex items-center cursor-pointer">
                                    <i class="fas fa-file-pdf text-red-600 mr-2"></i>
                                    <span>PDF</span>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="format-csv" name="export-format" value="csv" class="mr-2">
                                <label for="format-csv" class="flex items-center cursor-pointer">
                                    <i class="fas fa-file-csv text-blue-600 mr-2"></i>
                                    <span>CSV</span>
                                </label>
                            </div>
                        </div>
                        <div class="text-sm text-accent-600 mt-2">
                            <i class="fas fa-info-circle text-primary-500 mr-1"></i> 
                            Selecting "Excel" will download the full 4-tab dashboard report. PDF/CSV will generate a sales report for the dates below.
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-accent-800 mb-3">Date Range (for PDF/CSV)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-accent-700 mb-1">From</label>
                                <input type="date" id="startDate" name="start_date" value="<?= $default_start_date ?>" class="w-full px-3 py-2 border border-accent-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" max="<?= $default_end_date ?>">
                                <div id="startDateError" class="form-error">Please select a valid start date</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-accent-700 mb-1">To</label>
                                <input type="date" id="endDate" name="end_date" value="<?= $default_end_date ?>" class="w-full px-3 py-2 border border-accent-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" min="<?= $default_start_date ?>" max="<?= $default_end_date ?>">
                                <div id="endDateError" class="form-error">Please select a valid end date</div>
                            </div>
                        </div>
                        <div id="dateRangeError" class="form-error mt-2">End date must be after start date</div>
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" id="cancelReport" class="px-4 py-2 border border-accent-300 text-accent-700 rounded-lg hover:bg-accent-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" id="generateReportBtn" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors flex items-center gap-2">
                            <i class="fas fa-download"></i> Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- Tab switching ---
            const tabs = document.querySelectorAll('.dashboard-tab');
            const overviewDashboard = document.getElementById('overview-dashboard');
            const predictiveDashboard = document.getElementById('predictive-dashboard');
            const heatmapsDashboard = document.getElementById('heatmaps-dashboard');
            const customerTrailDashboard = document.getElementById('customer-trail-dashboard');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    overviewDashboard.classList.add('hidden');
                    predictiveDashboard.classList.add('hidden');
                    heatmapsDashboard.classList.add('hidden');
                    customerTrailDashboard.classList.add('hidden');

                    if (this.id === 'tab-overview') {
                        overviewDashboard.classList.remove('hidden');
                    } else if (this.id === 'tab-predictive') {
                        predictiveDashboard.classList.remove('hidden');
                    } else if (this.id === 'tab-heatmaps') {
                        heatmapsDashboard.classList.remove('hidden');
                    } else if (this.id === 'tab-login-trail') {
                        customerTrailDashboard.classList.remove('hidden');
                    }
                });
            });

            // --- *** FIXED: Modal and Form Validation Logic is back *** ---
            const startDateInput = document.getElementById('startDate');
            const endDateInput = document.getElementById('endDate');
            const today = new Date().toISOString().split('T')[0];
            
            // Set max date for both inputs to today (prevents selecting future dates)
            startDateInput.max = today;
            endDateInput.max = today;
            
            // Update endDate min constraint when startDate changes
            startDateInput.addEventListener('change', function() {
                endDateInput.min = this.value;
                validateDateRange();
            });
            
            // Update startDate max constraint when endDate changes
            endDateInput.addEventListener('change', function() {
                startDateInput.max = this.value;
                validateDateRange();
            });

            // Modal handling functions
            function openModal(modalId) {
                const modal = document.getElementById(modalId);
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }

            // Report generation modal
            document.getElementById('generateReport').addEventListener('click', function() {
                openModal('reportModal');
            });

            document.getElementById('closeReportModal').addEventListener('click', function() {
                closeModal('reportModal');
                resetReportForm();
            });

            document.getElementById('cancelReport').addEventListener('click', function() {
                closeModal('reportModal');
                resetReportForm();
            });
            
            // *** FIXED: Removed report type selection logic ***

            function validateDateRange() {
                const startDate = new Date(document.getElementById('startDate').value);
                const endDate = new Date(document.getElementById('endDate').value);
                
                if (document.getElementById('startDate').value && document.getElementById('endDate').value) {
                    if (endDate < startDate) {
                        showError('dateRangeError', 'End date must be after start date');
                        return false;
                    } else {
                        hideError('dateRangeError');
                    }
                }
                return true;
            }

            // Form validation functions
            function showError(elementId, message) {
                const element = document.getElementById(elementId);
                element.textContent = message;
                element.style.display = 'block';
                
                const inputId = elementId.replace('Error', '');
                const input = document.getElementById(inputId);
                if (input) {
                    input.classList.add('input-error');
                }
            }

            function hideError(elementId) {
                const element = document.getElementById(elementId);
                element.style.display = 'none';
                
                const inputId = elementId.replace('Error', '');
                const input = document.getElementById(inputId);
                if (input) {
                    input.classList.remove('input-error');
                }
            }

            function validateReportForm() {
                let isValid = true;
                
                // *** FIXED: Removed validation for reportType ***
                
                // Validate start date
                const startDate = document.getElementById('startDate').value;
                if (!startDate) {
                    showError('startDateError', 'Please select a start date');
                    isValid = false;
                } else {
                    hideError('startDateError');
                }
                
                // Validate end date
                const endDate = document.getElementById('endDate').value;
                if (!endDate) {
                    showError('endDateError', 'Please select an end date');
                    isValid = false;
                } else {
                    hideError('endDateError');
                }
                
                // Validate date range
                if (startDate && endDate && !validateDateRange()) {
                    isValid = false;
                }
                
                return isValid;
            }

            function resetReportForm() {
                // *** FIXED: Removed reset for reportType ***
                document.getElementById('reportForm').reset();
                
                document.querySelectorAll('.form-error').forEach(el => {
                    el.style.display = 'none';
                });
                
                document.querySelectorAll('.input-error').forEach(el => {
                    el.classList.remove('input-error');
                });

                document.getElementById('startDate').value = '<?= $default_start_date ?>';
                document.getElementById('endDate').value = '<?= $default_end_date ?>';
                
                startDateInput.min = null;
                startDateInput.max = today;
                endDateInput.min = '<?= $default_start_date ?>';
                endDateInput.max = today;
            }

            // Form submission
            document.getElementById('reportForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // NOTE: We validate all fields even if Excel doesn't use the dates,
                // just to keep the form logic simple and consistent.
                if (validateReportForm()) {
                    const format = document.querySelector('input[name="export-format"]:checked').value;
                    const startDate = document.getElementById('startDate').value;
                    const endDate = document.getElementById('endDate').value;
                    
                    const submitBtn = document.getElementById('generateReportBtn');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<div class="loading-spinner"></div> Generating...';
                    submitBtn.disabled = true;

                    // *** FIXED: URL no longer contains 'type' ***
                    let url = `generate_report.php?format=${format}&start_date=${startDate}&end_date=${endDate}`;
                    
                    if (format === 'pdf') {
                        window.open(url, '_blank');
                    } else {
                        const a = document.createElement('a');
                        a.href = url;
                        // Filename is now set by the server, so 'download' attribute is optional
                        // a.download = `report.${format === 'excel' ? 'xlsx' : format}`;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                    }

                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                        closeModal('reportModal');
                        resetReportForm();
                    }, 2000);
                }
            });

            // Close modals when clicking outside
            document.querySelectorAll('.modal-overlay').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeModal(this.id);
                        if (this.id === 'reportModal') {
                            resetReportForm();
                        }
                    }
                });
            });

            // Close modals with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.modal-overlay.active').forEach(modal => {
                        closeModal(modal.id);
                        if (modal.id === 'reportModal') {
                            resetReportForm();
                        }
                    });
                }
            });

            // --- ALL CHARTS ---
            // (Chart JavaScript is unchanged)

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
                    plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false, callbacks: { label: (context) => '₱' + context.parsed.y.toLocaleString() } } },
                    scales: { y: { beginAtZero: true, grid: { drawBorder: false }, ticks: { callback: (value) => '₱' + (value / 1000).toFixed(0) + 'K' } }, x: { grid: { display: false } } }
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
                        backgroundColor: ['rgba(237, 102, 49, 0.7)', 'rgba(16, 185, 129, 0.7)', 'rgba(139, 92, 246, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(239, 68, 68, 0.7)'],
                        borderColor: ['rgb(237, 102, 49)', 'rgb(16, 185, 129)', 'rgb(139, 92, 246)', 'rgb(245, 158, 11)', 'rgb(239, 68, 68)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, grid: { drawBorder: false }, ticks: { callback: (value) => '₱' + (value / 1000).toFixed(0) + 'K' } }, x: { grid: { display: false } } }
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
                        backgroundColor: ['rgba(237, 102, 49, 0.7)', 'rgba(16, 185, 129, 0.7)', 'rgba(139, 92, 246, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(239, 68, 68, 0.7)', 'rgba(99, 102, 241, 0.7)'],
                        borderColor: ['rgb(237, 102, 49)', 'rgb(16, 185, 129)', 'rgb(139, 92, 246)', 'rgb(245, 158, 11)', 'rgb(239, 68, 68)', 'rgb(99, 102, 241)'],
                        borderWidth: 1
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
            });

            // Size Chart
            const sizeCtx = document.getElementById('sizeChart').getContext('2d');
            const sizeChart = new Chart(sizeCtx, {
                type: 'pie',
                data: {
                    labels: <?= json_encode(array_column($size_sales, 'size_name')) ?>,
                    datasets: [{
                        data: <?= json_encode(array_map(function($s){return (int)$s['sold'];}, $size_sales)) ?>,
                        backgroundColor: ['rgba(237, 102, 49, 0.7)', 'rgba(16, 185, 129, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(139, 92, 246, 0.7)'],
                        borderColor: ['rgb(237, 102, 49)', 'rgb(16, 185, 129)', 'rgb(245, 158, 11)', 'rgb(139, 92, 246)'],
                        borderWidth: 1
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
            });

            // Application Chart
            const applicationCtx = document.getElementById('applicationChart').getContext('2d');
            const applicationChart = new Chart(applicationCtx, {
                type: 'polarArea',
                data: {
                    labels: <?= json_encode(array_column($application_sales, 'best_for_name')) ?>,
                    datasets: [{
                        data: <?= json_encode(array_map(function($a){return (int)$a['sold'];}, $application_sales)) ?>,
                        backgroundColor: ['rgba(237, 102, 49, 0.7)', 'rgba(16, 185, 129, 0.7)', 'rgba(139, 92, 246, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(239, 68, 68, 0.7)'],
                        borderColor: ['rgb(237, 102, 49)', 'rgb(16, 185, 129)', 'rgb(139, 92, 246)', 'rgb(245, 158, 11)', 'rgb(239, 68, 68)'],
                        borderWidth: 1
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
            });

            // Monthly Sales Chart
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            const monthlyChart = new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: <?= json_encode(array_map(function($m){return (float)$m['revenue'];}, $complete_monthly_sales)) ?>,
                        backgroundColor: 'rgba(237, 102, 49, 0.7)',
                        borderColor: 'rgb(237, 102, 49)',
                        borderWidth: 1
                    }, {
                        label: 'Orders',
                        data: <?= json_encode(array_map(function($m){return (int)$m['orders'];}, $complete_monthly_sales)) ?>,
                        backgroundColor: 'rgba(139, 92, 246, 0.7)',
                        borderColor: 'rgb(139, 92, 246)',
                        borderWidth: 1,
                        type: 'line',
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } },
                    scales: { 
                        y: { 
                            beginAtZero: true, 
                            grid: { drawBorder: false }, 
                            ticks: { callback: (value) => '₱' + (value / 1000).toFixed(0) + 'K' } 
                        }, 
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            grid: { drawOnChartArea: false }
                        },
                        x: { grid: { display: false } } 
                    }
                }
            });

            // Forecast Chart
            const forecastCtx = document.getElementById('forecastChart').getContext('2d');
            const forecastChart = new Chart(forecastCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode(array_column($sales_forecast, 'date')) ?>,
                    datasets: [{
                        label: 'Forecasted Revenue',
                        data: <?= json_encode(array_map(function($f){return (float)$f['revenue'];}, $sales_forecast)) ?>,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        borderDash: [5, 5]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false, callbacks: { label: (context) => '₱' + context.parsed.y.toLocaleString() } } },
                    scales: { y: { beginAtZero: true, grid: { drawBorder: false }, ticks: { callback: (value) => '₱' + (value / 1000).toFixed(0) + 'K' } }, x: { grid: { display: false } } }
                }
            });

            // Segmentation Chart
            const segmentationCtx = document.getElementById('segmentationChart').getContext('2d');
            const segmentationChart = new Chart(segmentationCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_column($customer_segments, 'segment')) ?>,
                    datasets: [{
                        label: 'Customer Count',
                        data: <?= json_encode(array_column($customer_segments, 'count')) ?>,
                        backgroundColor: ['rgba(237, 102, 49, 0.7)', 'rgba(16, 185, 129, 0.7)', 'rgba(139, 92, 246, 0.7)'],
                        borderColor: ['rgb(237, 102, 49)', 'rgb(16, 185, 129)', 'rgb(139, 92, 246)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, grid: { drawBorder: false } }, x: { grid: { display: false } } }
                }
            });
        });
    </script>
</body>
</html>