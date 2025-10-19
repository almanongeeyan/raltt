<?php include '../includes/sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Analytics Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2c3e50',
                        secondary: '#e67e22',
                        accent: '#3498db',
                        success: '#27ae60',
                        warning: '#f39c12',
                        danger: '#e74c3c',
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
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="main-content p-5">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm p-5 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center">
            <h1 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Dashboard</h1>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <span class="text-sm text-gray-600">View:</span>
                <select class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                    <option>Today</option>
                    <option selected>This Week</option>
                    <option>This Month</option>
                    <option>This Quarter</option>
                    <option>This Year</option>
                </select>
                <span class="text-sm text-gray-600">Compare to:</span>
                <select class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                    <option>Previous Period</option>
                    <option selected>Last Week</option>
                    <option>Last Month</option>
                    <option>Last Year</option>
                </select>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
            <!-- Revenue Card -->
            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex justify-between items-start mb-4">
                    <div class="text-sm font-medium text-gray-500">TOTAL REVENUE</div>
                    <div class="w-10 h-10 rounded-lg bg-success flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-white"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-800 mb-2">₱1,248,750</div>
                <div class="text-sm text-success flex items-center">
                    <i class="fas fa-arrow-up mr-1"></i> 12.5% from last week
                </div>
            </div>

            <!-- Orders Card -->
            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex justify-between items-start mb-4">
                    <div class="text-sm font-medium text-gray-500">ORDERS</div>
                    <div class="w-10 h-10 rounded-lg bg-accent flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-white"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-800 mb-2">328</div>
                <div class="text-sm text-success flex items-center">
                    <i class="fas fa-arrow-up mr-1"></i> 8.2% from last week
                </div>
            </div>

            <!-- Customers Card -->
            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex justify-between items-start mb-4">
                    <div class="text-sm font-medium text-gray-500">CUSTOMERS</div>
                    <div class="w-10 h-10 rounded-lg bg-warning flex items-center justify-center">
                        <i class="fas fa-users text-white"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-800 mb-2">1,584</div>
                <div class="text-sm text-success flex items-center">
                    <i class="fas fa-arrow-up mr-1"></i> 5.7% from last week
                </div>
            </div>

            <!-- Products Card -->
            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex justify-between items-start mb-4">
                    <div class="text-sm font-medium text-gray-500">PRODUCTS SOLD</div>
                    <div class="w-10 h-10 rounded-lg bg-danger flex items-center justify-center">
                        <i class="fas fa-box text-white"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-800 mb-2">2,845</div>
                <div class="text-sm text-success flex items-center">
                    <i class="fas fa-arrow-up mr-1"></i> 10.3% from last week
                </div>
            </div>
        </div>

        <!-- Sales Chart -->
        <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-5">
                <h2 class="text-lg font-semibold text-gray-800 mb-2 md:mb-0">Revenue Performance</h2>
                <div class="flex gap-2">
                    <button class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-accent">Revenue</button>
                    <button class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-accent">Orders</button>
                    <button class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-accent">Customers</button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Branch Performance -->
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="text-lg font-semibold text-gray-800 mb-5">Sales by Branch</h2>
                <div class="chart-container">
                    <canvas id="branchChart"></canvas>
                </div>
            </div>

            <!-- Top Selling Categories -->
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="text-lg font-semibold text-gray-800 mb-5">Top Selling Categories</h2>
                <div class="chart-container">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Tile Designs Performance -->
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="text-lg font-semibold text-gray-800 mb-5">Popular Tile Designs</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th class="px-4 py-3">Design</th>
                                <th class="px-4 py-3">Sales</th>
                                <th class="px-4 py-3">Revenue</th>
                                <th class="px-4 py-3">Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">Minimalist</td>
                                <td class="px-4 py-3">542 units</td>
                                <td class="px-4 py-3">₱324,500</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">+18%</span>
                                </td>
                            </tr>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">Modern</td>
                                <td class="px-4 py-3">487 units</td>
                                <td class="px-4 py-3">₱298,750</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">+12%</span>
                                </td>
                            </tr>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">Rustic</td>
                                <td class="px-4 py-3">385 units</td>
                                <td class="px-4 py-3">₱231,000</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">+8%</span>
                                </td>
                            </tr>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">Floral</td>
                                <td class="px-4 py-3">321 units</td>
                                <td class="px-4 py-3">₱205,440</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">+3%</span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">Geometric</td>
                                <td class="px-4 py-3">298 units</td>
                                <td class="px-4 py-3">₱178,800</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">-2%</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="text-lg font-semibold text-gray-800 mb-5">Recent Orders</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th class="px-4 py-3">Order ID</th>
                                <th class="px-4 py-3">Customer</th>
                                <th class="px-4 py-3">Amount</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-accent">#RALTT-7829</td>
                                <td class="px-4 py-3">Maria Santos</td>
                                <td class="px-4 py-3">₱24,500</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Completed</span>
                                </td>
                            </tr>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-accent">#RALTT-7828</td>
                                <td class="px-4 py-3">Juan Dela Cruz</td>
                                <td class="px-4 py-3">₱18,750</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Processing</span>
                                </td>
                            </tr>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-accent">#RALTT-7827</td>
                                <td class="px-4 py-3">Antonio Reyes</td>
                                <td class="px-4 py-3">₱31,200</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Shipped</span>
                                </td>
                            </tr>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-accent">#RALTT-7826</td>
                                <td class="px-4 py-3">Sofia Garcia</td>
                                <td class="px-4 py-3">₱15,840</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Completed</span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-accent">#RALTT-7825</td>
                                <td class="px-4 py-3">James Tan</td>
                                <td class="px-4 py-3">₱27,900</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Processing</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tile Sizes & Best For -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Tile Sizes -->
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="text-lg font-semibold text-gray-800 mb-5">Popular Tile Sizes</h2>
                <div class="chart-container">
                    <canvas id="sizeChart"></canvas>
                </div>
            </div>

            <!-- Best For Applications -->
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="text-lg font-semibold text-gray-800 mb-5">Tile Applications</h2>
                <div class="chart-container">
                    <canvas id="applicationChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const revenueChart = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: [125000, 152000, 185000, 176000, 210000, 245000, 255750],
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        fill: true,
                        tension: 0.3
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
                    labels: ['Deparo', 'Vanguard', 'Brixton', 'Samaria', 'Phase 1'],
                    datasets: [{
                        label: 'Sales (₱)',
                        data: [325000, 285000, 275000, 215000, 148750],
                        backgroundColor: [
                            'rgba(52, 152, 219, 0.7)',
                            'rgba(46, 204, 113, 0.7)',
                            'rgba(155, 89, 182, 0.7)',
                            'rgba(241, 196, 15, 0.7)',
                            'rgba(230, 126, 34, 0.7)'
                        ],
                        borderColor: [
                            'rgb(52, 152, 219)',
                            'rgb(46, 204, 113)',
                            'rgb(155, 89, 182)',
                            'rgb(241, 196, 15)',
                            'rgb(230, 126, 34)'
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
                    labels: ['Ceramic', 'Porcelain', 'Granite', 'Marble', 'Cement', 'Glass'],
                    datasets: [{
                        data: [30, 25, 15, 12, 10, 8],
                        backgroundColor: [
                            'rgba(52, 152, 219, 0.7)',
                            'rgba(46, 204, 113, 0.7)',
                            'rgba(155, 89, 182, 0.7)',
                            'rgba(241, 196, 15, 0.7)',
                            'rgba(230, 126, 34, 0.7)',
                            'rgba(231, 76, 60, 0.7)'
                        ],
                        borderColor: [
                            'rgb(52, 152, 219)',
                            'rgb(46, 204, 113)',
                            'rgb(155, 89, 182)',
                            'rgb(241, 196, 15)',
                            'rgb(230, 126, 34)',
                            'rgb(231, 76, 60)'
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
                    labels: ['60x60 cm', '30x60 cm', '40x40 cm', '30x30 cm'],
                    datasets: [{
                        data: [45, 25, 20, 10],
                        backgroundColor: [
                            'rgba(52, 152, 219, 0.7)',
                            'rgba(46, 204, 113, 0.7)',
                            'rgba(241, 196, 15, 0.7)',
                            'rgba(230, 126, 34, 0.7)'
                        ],
                        borderColor: [
                            'rgb(52, 152, 219)',
                            'rgb(46, 204, 113)',
                            'rgb(241, 196, 15)',
                            'rgb(230, 126, 34)'
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
                    labels: ['Floor', 'Wall', 'Bathroom', 'Kitchen', 'Outdoor'],
                    datasets: [{
                        data: [35, 25, 20, 15, 5],
                        backgroundColor: [
                            'rgba(52, 152, 219, 0.7)',
                            'rgba(46, 204, 113, 0.7)',
                            'rgba(155, 89, 182, 0.7)',
                            'rgba(241, 196, 15, 0.7)',
                            'rgba(230, 126, 34, 0.7)'
                        ],
                        borderColor: [
                            'rgb(52, 152, 219)',
                            'rgb(46, 204, 113)',
                            'rgb(155, 89, 182)',
                            'rgb(241, 196, 15)',
                            'rgb(230, 126, 34)'
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