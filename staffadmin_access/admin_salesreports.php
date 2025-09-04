<?php include '../includes/sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sales Report Dashboard</title>
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
                <!-- Page Header and Description -->
                <div class="mb-4">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-line mr-3 text-blue-600"></i>Sales Report Dashboard
                    </h1>
                </div>
                
                <!-- Sales Summary Section (Larger Metrics, Transaction Style) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500 flex items-center">
                        <div class="rounded-full bg-yellow-100 p-5 mr-5">
                            <i class="fas fa-coins text-yellow-500 text-3xl"></i>
                        </div>
                        <div>
                            <div class="text-gray-600 text-base font-semibold">Total Sales</div>
                            <div class="font-bold text-3xl text-green-700">₱79,500.00</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500 flex items-center">
                        <div class="rounded-full bg-blue-100 p-5 mr-5">
                            <i class="fas fa-cubes text-blue-500 text-3xl"></i>
                        </div>
                        <div>
                            <div class="text-gray-600 text-base font-semibold">Total Units Sold</div>
                            <div class="font-bold text-3xl text-blue-700">159</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500 flex items-center">
                        <div class="rounded-full bg-purple-100 p-5 mr-5">
                            <i class="fas fa-store text-purple-500 text-3xl"></i>
                        </div>
                        <div>
                            <div class="text-gray-600 text-base font-semibold">Branches Covered</div>
                            <div class="font-bold text-3xl text-purple-700">5</div>
                        </div>
                    </div>
                </div>
                <!-- Filter Section -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div id="filterText" class="mb-4 text-blue-700 font-semibold text-lg"></div>
                    <form id="filterForm" class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex flex-1 gap-2 flex-wrap">
                            <div class="relative flex-1 min-w-[180px]">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" placeholder="Search product name..." class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" />
                            </div>
                            <select id="branchSelect" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none min-w-[150px]">
                                <option value="">All Branches</option>
                                <option value="Brixton">Brixton</option>
                                <option value="Vanguard">Vanguard</option>
                                <option value="Kiko">Kiko</option>
                                <option value="Deparo">Deparo</option>
                                <option value="Samaria">Samaria</option>
                            </select>
                        </div>
                        <div class="flex gap-2 items-center flex-wrap">
                            <select id="dateFilter" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none min-w-[130px]">
                                <option value="">All Time</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="custom">Custom Range</option>
                            </select>
                            <input type="date" id="dateStart" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" />
                            <span class="mx-1 text-gray-400">-</span>
                            <input type="date" id="dateEnd" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" />
                        </div>
                    </form>
                </div>
                <!-- Sales Table -->
                <div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Units Sold</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sales</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <!-- Example Row 1 -->
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-3 font-semibold text-gray-700">Floral Blue Tiles</td>
                                <td class="px-4 py-3 text-gray-700">Brixton</td>
                                <td class="px-4 py-3 text-blue-700 font-bold">60</td>
                                <td class="px-4 py-3 text-green-700 font-bold">₱30,000.00</td>
                                <td class="px-4 py-3 text-gray-500">2025-08-20</td>
                            </tr>
                            <!-- Example Row 2 -->
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-3 font-semibold text-gray-700">Black Diamond Tiles</td>
                                <td class="px-4 py-3 text-gray-700">Vanguard</td>
                                <td class="px-4 py-3 text-blue-700 font-bold">49</td>
                                <td class="px-4 py-3 text-green-700 font-bold">₱24,500.00</td>
                                <td class="px-4 py-3 text-gray-500">2025-08-18</td>
                            </tr>
                            <!-- Example Row 3 -->
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-3 font-semibold text-gray-700">Classic White Tiles</td>
                                <td class="px-4 py-3 text-gray-700">Kiko</td>
                                <td class="px-4 py-3 text-blue-700 font-bold">20</td>
                                <td class="px-4 py-3 text-green-700 font-bold">₱10,000.00</td>
                                <td class="px-4 py-3 text-gray-500">2025-08-15</td>
                            </tr>
                            <!-- Example Row 4 -->
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-3 font-semibold text-gray-700">Matte Grey Tiles</td>
                                <td class="px-4 py-3 text-gray-700">Deparo</td>
                                <td class="px-4 py-3 text-blue-700 font-bold">30</td>
                                <td class="px-4 py-3 text-green-700 font-bold">₱15,000.00</td>
                                <td class="px-4 py-3 text-gray-500">2025-08-10</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-6 text-xs text-gray-500">Sales data is for demonstration. Filter and search to view specific results.</div>
                <script>
                // --- Date Filter Logic ---
                const filterText = document.getElementById('filterText');
                const dateFilter = document.getElementById('dateFilter');
                const dateStart = document.getElementById('dateStart');
                const dateEnd = document.getElementById('dateEnd');
                // Set max date to today for both date pickers
                const today = new Date().toISOString().split('T')[0];
                dateStart.max = today;
                dateEnd.max = today;
                // Disable future dates
                dateStart.addEventListener('input', function() {
                    if (dateStart.value > today) dateStart.value = today;
                    dateEnd.min = dateStart.value;
                });
                dateEnd.addEventListener('input', function() {
                    if (dateEnd.value > today) dateEnd.value = today;
                    dateStart.max = dateEnd.value || today;
                });
                // Hide date pickers unless custom is selected
                function updateDateInputs() {
                    if (dateFilter.value === 'custom') {
                        dateStart.style.display = '';
                        dateEnd.style.display = '';
                    } else {
                        dateStart.style.display = 'none';
                        dateEnd.style.display = 'none';
                    }
                }
                dateFilter.addEventListener('change', function() {
                    updateDateInputs();
                    updateFilterText();
                });
                // Initial state
                updateDateInputs();
                // Update filter text
                function updateFilterText() {
                    let text = '';
                    if (dateFilter.value === 'today') text = 'Showing sales for today';
                    else if (dateFilter.value === 'week') text = 'Showing sales for this week';
                    else if (dateFilter.value === 'month') text = 'Showing sales for this month';
                    else if (dateFilter.value === 'custom' && dateStart.value && dateEnd.value) {
                        text = `Showing sales from ${dateStart.value} to ${dateEnd.value}`;
                    } else if (dateFilter.value === 'custom') {
                        text = 'Select a date range to filter sales';
                    } else {
                        text = 'Showing all sales';
                    }
                    filterText.textContent = text;
                }
                dateStart.addEventListener('input', updateFilterText);
                dateEnd.addEventListener('input', updateFilterText);
                // On page load
                updateFilterText();
                </script>
            </div>
        </main>
    </div>
</body>
</html>
