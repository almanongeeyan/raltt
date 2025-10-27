<?php include '../includes/sidebar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Tickets History | Support Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        success: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                        },
                        warning: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                        },
                        danger: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                        },
                        gray: {
                            50: '#f9fafb',
                            100: '#f3f4f6',
                            200: '#e5e7eb',
                            300: '#d1d5db',
                            400: '#9ca3af',
                            500: '#6b7280',
                            600: '#4b5563',
                            700: '#374151',
                            800: '#1f2937',
                            900: '#111827',
                        }
                    },
                    fontFamily: {
                        inter: ['Inter', 'sans-serif'],
                    },
                    boxShadow: {
                        'card': '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)',
                        'card-hover': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
        }

        .status-resolved {
            background-color: #dcfce7;
            color: #15803d;
        }

        .status-closed {
            background-color: #f3f4f6;
            color: #4b5563;
        }

        .ticket-card {
            transition: all 0.2s ease-in-out;
            border-left: 4px solid transparent;
            cursor: pointer;
        }

        .ticket-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .ticket-card.resolved {
            border-left-color: #22c55e;
        }

        .ticket-card.closed {
            border-left-color: #6b7280;
        }

        .filter-tab {
            position: relative;
            transition: all 0.2s ease;
        }

        .filter-tab.active {
            color: #2563eb;
        }

        .filter-tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #2563eb;
        }

        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            position: fixed;
            inset: 0;
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-content {
            max-height: 90vh;
            overflow-y: auto;
            width: 100%;
            max-width: 800px;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
        }

        .empty-state-icon {
            font-size: 3rem;
            color: #9ca3af;
            margin-bottom: 1rem;
        }

        .history-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
        }

        .history-resolved {
            background-color: #dcfce7;
            color: #15803d;
        }

        .history-closed {
            background-color: #f3f4f6;
            color: #4b5563;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">

    <div class="lg:ml-64 flex flex-col min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <button onclick="history.back()" class="flex items-center text-gray-500 hover:text-gray-700 mr-4">
                            <i class="fas fa-arrow-left mr-2"></i>
                        </button>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Customer Tickets History</h1>
                            <p class="text-sm text-gray-500 mt-1">View resolved and closed customer support tickets</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="search-tickets" placeholder="Search tickets..." 
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-64">
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow-card p-4 border-l-4 border-primary-500">
                        <div class="flex items-center">
                            <div class="rounded-full bg-primary-100 p-3 mr-4">
                                <i class="fas fa-history text-primary-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total History</p>
                                <p class="text-2xl font-bold text-gray-900" id="total-tickets">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-card p-4 border-l-4 border-success-500">
                        <div class="flex items-center">
                            <div class="rounded-full bg-success-100 p-3 mr-4">
                                <i class="fas fa-check-circle text-success-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Resolved</p>
                                <p class="text-2xl font-bold text-gray-900" id="resolved-tickets">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-card p-4 border-l-4 border-gray-500">
                        <div class="flex items-center">
                            <div class="rounded-full bg-gray-100 p-3 mr-4">
                                <i class="fas fa-archive text-gray-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Closed</p>
                                <p class="text-2xl font-bold text-gray-900" id="closed-tickets">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-card p-4 border-l-4 border-warning-500">
                        <div class="flex items-center">
                            <div class="rounded-full bg-warning-100 p-3 mr-4">
                                <i class="fas fa-clock text-warning-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Avg. Resolution Time</p>
                                <p class="text-2xl font-bold text-gray-900" id="avg-resolution">0 days</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters and Controls -->
                <div class="bg-white rounded-xl shadow-card mb-6 overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex space-x-1 bg-gray-100 rounded-lg p-1 overflow-x-auto">
                                <button id="all-tab" class="filter-tab py-2 px-4 rounded-md font-medium active">All History</button>
                                <button id="resolved-tab" class="filter-tab py-2 px-4 rounded-md font-medium text-gray-500">Resolved</button>
                                <button id="closed-tab" class="filter-tab py-2 px-4 rounded-md font-medium text-gray-500">Closed</button>
                                <button id="last-week-tab" class="filter-tab py-2 px-4 rounded-md font-medium text-gray-500">Last Week</button>
                                <button id="last-month-tab" class="filter-tab py-2 px-4 rounded-md font-medium text-gray-500">Last Month</button>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <select id="issue-filter" class="text-sm border border-gray-300 rounded-lg py-2 px-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">All Issues</option>
                                    <option value="Cracked Tile">Cracked Tile</option>
                                    <option value="Shattered Tile">Shattered Tile</option>
                                    <option value="Defective Tile">Defective Tile</option>
                                    <option value="Wrong Item Delivered">Wrong Item Delivered</option>
                                    <option value="Payment Issues">Payment Issues</option>
                                    <option value="Other">Other</option>
                                </select>
                                <button id="clear-filters" class="text-sm text-gray-600 hover:text-gray-800 py-2 px-3 border border-gray-300 rounded-lg">
                                    Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tickets List -->
                    <div id="tickets-container">
                        <div class="flex flex-col items-center justify-center py-12">
                            <div class="loading-spinner text-4xl text-primary-500 mb-4">
                                <i class="fas fa-circle-notch"></i>
                            </div>
                            <p class="text-gray-500">Loading ticket history...</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Ticket Details Modal -->
    <div id="ticket-modal" class="fixed inset-0 z-50 hidden">
        <div class="modal-overlay">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl z-10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-100 text-primary-600">
                                <i class="fas fa-history"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Ticket #<span id="modal-ticket-id"></span> (History)</h2>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span id="modal-ticket-status" class="status-badge text-sm"></span>
                                    <span class="text-xs text-gray-500">Resolved on <span id="modal-resolution-date"></span></span>
                                </div>
                            </div>
                        </div>
                        <button id="close-modal" class="text-gray-400 hover:text-gray-600 transition-colors duration-200 p-2 rounded-full hover:bg-gray-100">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Customer Information -->
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                            <h3 class="text-lg font-semibold text-primary-700 mb-4 flex items-center">
                                <i class="fas fa-user-circle text-primary-500 mr-2"></i>Customer Information
                            </h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="block text-xs text-gray-500 uppercase tracking-wide">Name</span>
                                    <span id="modal-customer-name" class="block text-gray-900 font-medium mt-1"></span>
                                </div>
                                <div>
                                    <span class="block text-xs text-gray-500 uppercase tracking-wide">Email</span>
                                    <span id="modal-customer-email" class="block text-gray-900 font-medium mt-1"></span>
                                </div>
                                <div>
                                    <span class="block text-xs text-gray-500 uppercase tracking-wide">Phone</span>
                                    <span id="modal-customer-phone" class="block text-gray-900 font-medium mt-1"></span>
                                </div>
                                <div>
                                    <span class="block text-xs text-gray-500 uppercase tracking-wide">Address</span>
                                    <span id="modal-customer-address" class="block text-gray-900 font-medium mt-1"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Details -->
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                            <h3 class="text-lg font-semibold text-primary-700 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-primary-500 mr-2"></i>Ticket Details
                            </h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="block text-xs text-gray-500 uppercase tracking-wide">Order Reference</span>
                                    <span id="modal-order-id" class="block text-gray-900 font-medium mt-1"></span>
                                </div>
                                <div>
                                    <span class="block text-xs text-gray-500 uppercase tracking-wide">Issue Type</span>
                                    <span id="modal-issue-type" class="block text-gray-900 font-medium mt-1"></span>
                                </div>
                                <div>
                                    <span class="block text-xs text-gray-500 uppercase tracking-wide">Description</span>
                                    <p id="modal-issue-description" class="block text-gray-900 mt-2 bg-white p-3 rounded-lg border border-gray-200"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resolution Details -->
                    <div id="resolution-section" class="bg-success-50 rounded-xl p-5 border border-success-200 mb-6">
                        <h3 class="text-lg font-semibold text-success-700 mb-4 flex items-center">
                            <i class="fas fa-check-circle text-success-500 mr-2"></i>Resolution Details
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-clock mr-2"></i>
                                <span>Resolution time: <span id="modal-resolution-time" class="font-medium"></span></span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-calendar mr-2"></i>
                                <span>Ticket created: <span id="modal-created-date" class="font-medium"></span></span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-calendar-check mr-2"></i>
                                <span>Ticket resolved: <span id="modal-resolved-date" class="font-medium"></span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Ticket Timeline -->
                    <div class="bg-primary-50 rounded-xl p-5 border border-primary-200">
                        <h3 class="text-lg font-semibold text-primary-700 mb-4 flex items-center">
                            <i class="fas fa-stream text-primary-500 mr-2"></i>Ticket Timeline
                        </h3>
                        <div id="ticket-timeline" class="space-y-4">
                            <!-- Timeline items will be populated here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let ticketsData = [];
        let currentFilter = { status: 'all', issue: '', date: '', search: '' };
        const branchId = <?php echo isset($_SESSION['branch_id']) ? intval($_SESSION['branch_id']) : 'null'; ?>;

        // DOM elements
        const ticketsContainer = document.getElementById('tickets-container');
        const allTab = document.getElementById('all-tab');
        const resolvedTab = document.getElementById('resolved-tab');
        const closedTab = document.getElementById('closed-tab');
        const lastWeekTab = document.getElementById('last-week-tab');
        const lastMonthTab = document.getElementById('last-month-tab');
        const issueFilter = document.getElementById('issue-filter');
        const searchInput = document.getElementById('search-tickets');
        const clearFiltersBtn = document.getElementById('clear-filters');
        const ticketModal = document.getElementById('ticket-modal');

        // Stats elements
        const totalTicketsEl = document.getElementById('total-tickets');
        const resolvedTicketsEl = document.getElementById('resolved-tickets');
        const closedTicketsEl = document.getElementById('closed-tickets');
        const avgResolutionEl = document.getElementById('avg-resolution');

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            loadTickets();
            setupEventListeners();
        });

        // Set up event listeners
        function setupEventListeners() {
            allTab.addEventListener('click', () => setActiveFilter('status', 'all'));
            resolvedTab.addEventListener('click', () => setActiveFilter('status', 'Resolved'));
            closedTab.addEventListener('click', () => setActiveFilter('status', 'Closed'));
            lastWeekTab.addEventListener('click', () => setActiveFilter('date', 'last-week'));
            lastMonthTab.addEventListener('click', () => setActiveFilter('date', 'last-month'));
            
            issueFilter.addEventListener('change', (e) => setActiveFilter('issue', e.target.value));
            searchInput.addEventListener('input', (e) => setActiveFilter('search', e.target.value));
            clearFiltersBtn.addEventListener('click', clearFilters);
            
            // Close modal when clicking outside
            document.querySelector('.modal-overlay').addEventListener('click', (e) => {
                if (e.target === document.querySelector('.modal-overlay')) {
                    ticketModal.classList.add('hidden');
                }
            });
            
            // Close modal when clicking close button
            document.getElementById('close-modal').addEventListener('click', () => {
                ticketModal.classList.add('hidden');
            });
        }

        // Set active filter and update UI
        function setActiveFilter(type, value) {
            currentFilter[type] = value;
            
            // Update active tab UI
            if (type === 'status' || type === 'date') {
                [allTab, resolvedTab, closedTab, lastWeekTab, lastMonthTab].forEach(tab => {
                    tab.classList.remove('active', 'text-primary-700');
                    tab.classList.add('text-gray-500');
                });
                
                if (value === 'all') {
                    allTab.classList.add('active', 'text-primary-700');
                    allTab.classList.remove('text-gray-500');
                } else if (value === 'Resolved') {
                    resolvedTab.classList.add('active', 'text-primary-700');
                    resolvedTab.classList.remove('text-gray-500');
                } else if (value === 'Closed') {
                    closedTab.classList.add('active', 'text-primary-700');
                    closedTab.classList.remove('text-gray-500');
                } else if (value === 'last-week') {
                    lastWeekTab.classList.add('active', 'text-primary-700');
                    lastWeekTab.classList.remove('text-gray-500');
                } else if (value === 'last-month') {
                    lastMonthTab.classList.add('active', 'text-primary-700');
                    lastMonthTab.classList.remove('text-gray-500');
                }
            }
            
            filterAndRenderTickets();
            updateStats();
        }

        // Clear all filters
        function clearFilters() {
            currentFilter = { status: 'all', issue: '', date: '', search: '' };
            issueFilter.value = '';
            searchInput.value = '';
            setActiveFilter('status', 'all');
        }

        // Load tickets from backend
        async function loadTickets() {
            try {
                if (branchId === null) {
                    showErrorState('Branch ID not set in session.');
                    return;
                }

                // Show loading state
                ticketsContainer.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-12">
                        <div class="loading-spinner text-4xl text-primary-500 mb-4">
                            <i class="fas fa-circle-notch"></i>
                        </div>
                        <p class="text-gray-500">Loading ticket history...</p>
                    </div>
                `;

                const response = await fetch('get_ticket_history.php?branch_id=' + branchId);
                const data = await response.json();
                
                if (!data.success || !Array.isArray(data.tickets)) {
                    showErrorState(data.error || 'No ticket history found.');
                    return;
                }
                
                ticketsData = data.tickets;
                filterAndRenderTickets();
                updateStats();
                
            } catch (err) {
                showErrorState('Unable to load ticket history. Please try again.');
                console.error('Error loading tickets:', err);
            }
        }

        // Filter tickets based on current filters and render them
        function filterAndRenderTickets() {
            let filteredTickets = ticketsData.filter(ticket => {
                // Status filter
                let statusMatch = true;
                if (currentFilter.status === 'all') {
                    statusMatch = true;
                } else {
                    statusMatch = ticket.status === currentFilter.status;
                }
                
                // Date filter
                let dateMatch = true;
                if (currentFilter.date === 'last-week') {
                    const oneWeekAgo = new Date();
                    oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
                    const ticketDate = new Date(ticket.resolvedDate);
                    dateMatch = ticketDate >= oneWeekAgo;
                } else if (currentFilter.date === 'last-month') {
                    const oneMonthAgo = new Date();
                    oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1);
                    const ticketDate = new Date(ticket.resolvedDate);
                    dateMatch = ticketDate >= oneMonthAgo;
                }
                
                // Issue type filter
                const issueMatch = currentFilter.issue ? 
                    ticket.issue === currentFilter.issue : true;
                    
                // Search filter
                const searchMatch = currentFilter.search ? 
                    Object.values(ticket).some(value => 
                        String(value).toLowerCase().includes(currentFilter.search.toLowerCase())
                    ) : true;
                    
                return statusMatch && dateMatch && issueMatch && searchMatch;
            });
            
            renderTickets(filteredTickets);
        }

        // Render tickets to the DOM
        function renderTickets(tickets) {
            if (tickets.length === 0) {
                ticketsContainer.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">No ticket history found</h3>
                        <p class="text-gray-500 mb-4">Try adjusting your filters or search terms</p>
                        <button id="clear-filters-empty" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition">
                            Clear Filters
                        </button>
                    </div>
                `;
                
                document.getElementById('clear-filters-empty').addEventListener('click', clearFilters);
                return;
            }
            
            let ticketsHTML = '';
            
            tickets.forEach(ticket => {
                const statusClass = `status-${ticket.status.toLowerCase()}`;
                const cardClass = `ticket-card ${ticket.status.toLowerCase()} bg-white p-5 mb-3 rounded-lg shadow-card border border-gray-200`;
                const historyBadgeClass = `history-badge history-${ticket.status.toLowerCase()}`;
                
                ticketsHTML += `
                    <div class="${cardClass}" data-ticket-id="${ticket.id}">
                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <h3 class="font-semibold text-gray-900">${ticket.issue}</h3>
                                    <span class="${historyBadgeClass}">${ticket.status}</span>
                                    <span class="text-xs text-gray-500 bg-gray-100 py-1 px-2 rounded">Resolved in ${ticket.resolutionTime}</span>
                                </div>
                                <p class="text-gray-600 text-sm mb-3 line-clamp-2">${ticket.description}</p>
                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <i class="fas fa-user mr-1"></i>
                                        <span>${ticket.customerName || 'Unknown Customer'}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-shopping-bag mr-1"></i>
                                        <span>Order #${ticket.orderId}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar mr-1"></i>
                                        <span>Resolved: ${ticket.resolvedDate}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 md:mt-0 flex items-center gap-2">
                                <button class="view-details-btn bg-primary-500 hover:bg-primary-600 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition" data-ticket-id="${ticket.id}">
                                    View History
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            ticketsContainer.innerHTML = ticketsHTML;
            
            // Add event listeners to the newly created buttons
            document.querySelectorAll('.view-details-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const ticketId = this.dataset.ticketId;
                    const ticket = ticketsData.find(t => t.id == ticketId);
                    if (ticket) {
                        showTicketDetails(ticket);
                    }
                });
            });
            
            document.querySelectorAll('.ticket-card').forEach(card => {
                card.addEventListener('click', function() {
                    const ticketId = this.dataset.ticketId;
                    const ticket = ticketsData.find(t => t.id == ticketId);
                    if (ticket) {
                        showTicketDetails(ticket);
                    }
                });
            });
        }

        // Show ticket details in modal
        function showTicketDetails(ticket) {
            // Set modal content
            document.getElementById('modal-ticket-id').textContent = ticket.id;
            document.getElementById('modal-resolution-date').textContent = ticket.resolvedDate;
            document.getElementById('modal-customer-name').textContent = ticket.customerName || 'Not provided';
            document.getElementById('modal-customer-email').textContent = ticket.customerEmail || 'Not provided';
            document.getElementById('modal-customer-phone').textContent = ticket.customerPhone || 'Not provided';
            document.getElementById('modal-customer-address').textContent = ticket.customerAddress || 'Not provided';
            document.getElementById('modal-order-id').textContent = ticket.orderId;
            document.getElementById('modal-issue-type').textContent = ticket.issue;
            document.getElementById('modal-issue-description').textContent = ticket.description;
            document.getElementById('modal-resolution-time').textContent = ticket.resolutionTime;
            document.getElementById('modal-created-date').textContent = ticket.date;
            document.getElementById('modal-resolved-date').textContent = ticket.resolvedDate;
            
            // Set status badge
            const statusBadge = document.getElementById('modal-ticket-status');
            const statusClass = `status-${ticket.status.toLowerCase()}`;
            statusBadge.className = `status-badge ${statusClass} text-sm`;
            statusBadge.textContent = ticket.status;
            
            // Render timeline
            const timelineContainer = document.getElementById('ticket-timeline');
            let timelineHTML = '';
            
            if (ticket.timeline && ticket.timeline.length > 0) {
                ticket.timeline.forEach((event, index) => {
                    const isLast = index === ticket.timeline.length - 1;
                    timelineHTML += `
                        <div class="flex">
                            <div class="flex flex-col items-center mr-4">
                                <div class="rounded-full bg-primary-500 w-3 h-3"></div>
                                ${!isLast ? '<div class="w-0.5 h-full bg-gray-300 mt-1"></div>' : ''}
                            </div>
                            <div class="flex-1 pb-4 ${isLast ? '' : 'border-b border-gray-200'}">
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-900">${event.action}</span>
                                    <span class="text-sm text-gray-500">${event.date}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">${event.description}</p>
                            </div>
                        </div>
                    `;
                });
            } else {
                timelineHTML = '<p class="text-gray-500 text-center py-4">No timeline data available</p>';
            }
            
            timelineContainer.innerHTML = timelineHTML;
            
            // Show modal
            ticketModal.classList.remove('hidden');
        }

        // Update statistics
        function updateStats() {
            const filteredTickets = ticketsData.filter(ticket => {
                let statusMatch = true;
                if (currentFilter.status === 'all') {
                    statusMatch = true;
                } else {
                    statusMatch = ticket.status === currentFilter.status;
                }
                
                let dateMatch = true;
                if (currentFilter.date === 'last-week') {
                    const oneWeekAgo = new Date();
                    oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
                    const ticketDate = new Date(ticket.resolvedDate);
                    dateMatch = ticketDate >= oneWeekAgo;
                } else if (currentFilter.date === 'last-month') {
                    const oneMonthAgo = new Date();
                    oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1);
                    const ticketDate = new Date(ticket.resolvedDate);
                    dateMatch = ticketDate >= oneMonthAgo;
                }
                
                const issueMatch = currentFilter.issue ? 
                    ticket.issue === currentFilter.issue : true;
                    
                const searchMatch = currentFilter.search ? 
                    Object.values(ticket).some(value => 
                        String(value).toLowerCase().includes(currentFilter.search.toLowerCase())
                    ) : true;
                    
                return statusMatch && dateMatch && issueMatch && searchMatch;
            });

            const total = filteredTickets.length;
            const resolved = filteredTickets.filter(t => 
                t.status.toLowerCase() === 'resolved').length;
            const closed = filteredTickets.filter(t => 
                t.status.toLowerCase() === 'closed').length;
            
            // Calculate average resolution time
            let totalDays = 0;
            let count = 0;
            
            filteredTickets.forEach(ticket => {
                if (ticket.resolutionTime) {
                    // Extract days from "X days" string
                    const daysMatch = ticket.resolutionTime.match(/(\d+)\s*days?/);
                    if (daysMatch) {
                        const days = parseInt(daysMatch[1]);
                        if (!isNaN(days)) {
                            totalDays += days;
                            count++;
                        }
                    }
                }
            });
            
            const avgDays = count > 0 ? Math.round(totalDays / count) : 0;
            
            totalTicketsEl.textContent = total;
            resolvedTicketsEl.textContent = resolved;
            closedTicketsEl.textContent = closed;
            avgResolutionEl.textContent = `${avgDays} day${avgDays !== 1 ? 's' : ''}`;
        }

        // Show error state
        function showErrorState(message) {
            ticketsContainer.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon text-danger-500">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Unable to load ticket history</h3>
                    <p class="text-gray-500 mb-4">${message}</p>
                    <button id="retry-loading" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition">
                        Try Again
                    </button>
                </div>
            `;
            
            document.getElementById('retry-loading').addEventListener('click', loadTickets);
        }
    </script>
</body>
</html>