<?php include '../includes/sidebar.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Tickets | Support Center</title>
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

        .status-pending {
            background-color: #fef3c7;
            color: #b45309;
        }

        .status-open {
            background-color: #fef3c7;
            color: #b45309;
        }

        .status-in-progress {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-exchange-approved {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-awaiting-customer {
            background-color: #fef3c7;
            color: #b45309;
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
        }

        .ticket-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .ticket-card.pending {
            border-left-color: #f59e0b;
        }

        .ticket-card.open {
            border-left-color: #f59e0b;
        }

        .ticket-card.in-progress {
            border-left-color: #3b82f6;
        }

        .ticket-card.exchange-approved {
            border-left-color: #3b82f6;
        }

        .ticket-card.awaiting-customer {
            border-left-color: #f59e0b;
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

        .status-update-btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            border: 1px solid #e5e7eb;
            background: white;
        }

        .status-update-btn:hover:not(:disabled) {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .status-update-btn.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .status-update-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
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
                            <h1 class="text-2xl font-bold text-gray-900">Customer Support Tickets</h1>
                            <p class="text-sm text-gray-500 mt-1">Manage and resolve customer issues efficiently</p>
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
                                <i class="fas fa-ticket-alt text-primary-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Tickets</p>
                                <p class="text-2xl font-bold text-gray-900" id="total-tickets">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-card p-4 border-l-4 border-warning-500">
                        <div class="flex items-center">
                            <div class="rounded-full bg-warning-100 p-3 mr-4">
                                <i class="fas fa-clock text-warning-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Pending</p>
                                <p class="text-2xl font-bold text-gray-900" id="pending-tickets">0</p>
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
                </div>

                <!-- Filters and Controls -->
                <div class="bg-white rounded-xl shadow-card mb-6 overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex space-x-1 bg-gray-100 rounded-lg p-1 overflow-x-auto">
                                <button id="all-tab" class="filter-tab py-2 px-4 rounded-md font-medium active">All Tickets</button>
                                <button id="open-tab" class="filter-tab py-2 px-4 rounded-md font-medium text-gray-500">Open</button>
                                <button id="inprogress-tab" class="filter-tab py-2 px-4 rounded-md font-medium text-gray-500">In Progress</button>
                                <button id="exchange-tab" class="filter-tab py-2 px-4 rounded-md font-medium text-gray-500">Exchange Approved</button>
                                <button id="awaiting-tab" class="filter-tab py-2 px-4 rounded-md font-medium text-gray-500">Awaiting Customer</button>
                                <button id="resolved-tab" class="filter-tab py-2 px-4 rounded-md font-medium text-gray-500">Resolved</button>
                                <button id="closed-tab" class="filter-tab py-2 px-4 rounded-md font-medium text-gray-500">Closed</button>
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
                            <p class="text-gray-500">Loading tickets...</p>
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
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Ticket #<span id="modal-ticket-id"></span></h2>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span id="modal-ticket-status" class="status-badge text-sm"></span>
                                    <span class="text-xs text-gray-500">Created on <span id="modal-ticket-date"></span></span>
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

                    <!-- Resolution Details (if available) -->
                    <div id="resolution-section" class="bg-success-50 rounded-xl p-5 border border-success-200 mb-6 hidden">
                        <h3 class="text-lg font-semibold text-success-700 mb-4 flex items-center">
                            <i class="fas fa-check-circle text-success-500 mr-2"></i>Resolution Details
                        </h3>
                        <p id="modal-resolution" class="text-gray-900"></p>
                    </div>

                    <!-- Quick Status Update -->
                    <div class="bg-primary-50 rounded-xl p-5 border border-primary-200">
                        <h3 class="text-lg font-semibold text-primary-700 mb-4 flex items-center">
                            <i class="fas fa-sync-alt text-primary-500 mr-2"></i>Update Status
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">Update the status of this ticket to track its progress.</p>
                        <div id="quick-status-ticket-modal" class="flex flex-wrap gap-2"></div>
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
        const openTab = document.getElementById('open-tab');
        const inProgressTab = document.getElementById('inprogress-tab');
        const exchangeTab = document.getElementById('exchange-tab');
        const awaitingTab = document.getElementById('awaiting-tab');
        const resolvedTab = document.getElementById('resolved-tab');
        const closedTab = document.getElementById('closed-tab');
        const issueFilter = document.getElementById('issue-filter');
        const searchInput = document.getElementById('search-tickets');
        const clearFiltersBtn = document.getElementById('clear-filters');
        const ticketModal = document.getElementById('ticket-modal');

        // Stats elements
        const totalTicketsEl = document.getElementById('total-tickets');
        const pendingTicketsEl = document.getElementById('pending-tickets');
        const resolvedTicketsEl = document.getElementById('resolved-tickets');
        const closedTicketsEl = document.getElementById('closed-tickets');

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            loadTickets();
            setupEventListeners();
        });

        // Set up event listeners
        function setupEventListeners() {
            allTab.addEventListener('click', () => setActiveFilter('status', 'all'));
            openTab.addEventListener('click', () => setActiveFilter('status', 'Open'));
            inProgressTab.addEventListener('click', () => setActiveFilter('status', 'In Progress'));
            exchangeTab.addEventListener('click', () => setActiveFilter('status', 'Exchange Approved'));
            awaitingTab.addEventListener('click', () => setActiveFilter('status', 'Awaiting Customer'));
            resolvedTab.addEventListener('click', () => setActiveFilter('status', 'Resolved'));
            closedTab.addEventListener('click', () => setActiveFilter('status', 'Closed'));
            
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
            if (type === 'status') {
                [allTab, openTab, inProgressTab, exchangeTab, awaitingTab, resolvedTab, closedTab].forEach(tab => {
                    tab.classList.remove('active', 'text-primary-700');
                    tab.classList.add('text-gray-500');
                });
                
                if (value === 'all') {
                    allTab.classList.add('active', 'text-primary-700');
                    allTab.classList.remove('text-gray-500');
                } else if (value === 'Open') {
                    openTab.classList.add('active', 'text-primary-700');
                    openTab.classList.remove('text-gray-500');
                } else if (value === 'In Progress') {
                    inProgressTab.classList.add('active', 'text-primary-700');
                    inProgressTab.classList.remove('text-gray-500');
                } else if (value === 'Exchange Approved') {
                    exchangeTab.classList.add('active', 'text-primary-700');
                    exchangeTab.classList.remove('text-gray-500');
                } else if (value === 'Awaiting Customer') {
                    awaitingTab.classList.add('active', 'text-primary-700');
                    awaitingTab.classList.remove('text-gray-500');
                } else if (value === 'Resolved') {
                    resolvedTab.classList.add('active', 'text-primary-700');
                    resolvedTab.classList.remove('text-gray-500');
                } else if (value === 'Closed') {
                    closedTab.classList.add('active', 'text-primary-700');
                    closedTab.classList.remove('text-gray-500');
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
                
                const response = await fetch('get_all_tickets.php?branch_id=' + branchId);
                const result = await response.json();
                
                if (result.success && result.tickets) {
                    ticketsData = result.tickets.map(ticket => ({
                        id: ticket.ticket_id,
                        orderId: ticket.order_reference,
                        date: new Date(ticket.created_at).toLocaleDateString(),
                        status: ticket.ticket_status,
                        issue: ticket.issue_type,
                        description: ticket.issue_description,
                        resolution: ticket.resolution || '',
                        product: ticket.issue_type,
                        price: '', // Not available
                        branch: ticket.branch_id || '',
                        qty: '', // Not available
                        customerName: ticket.customer_name || '',
                        customerEmail: ticket.customer_email || '',
                        customerPhone: ticket.customer_phone || '',
                        customerAddress: (ticket.house_address ? ticket.house_address + ', ' : '') + (ticket.full_address ? ticket.full_address : ''),
                        awaiting_customer_at: ticket.awaiting_customer_at || null,
                    }));
                    
                    filterAndRenderTickets();
                    updateStats();
                } else {
                    showErrorState('No tickets found.');
                }
            } catch (err) {
                showErrorState('Unable to load tickets. Please try again.');
                console.error('Error loading tickets:', err);
            }
        }

        // Filter tickets based on current filters and render them
        function filterAndRenderTickets() {
            // Remove closed tickets after 11:59PM each day (frontend only)
            const now = new Date();
            const isAfterMidnight = now.getHours() === 23 && now.getMinutes() === 59;
            let filteredTickets = ticketsData.filter(ticket => {
                // Remove closed tickets after 11:59PM
                if (ticket.status === 'Closed' && isAfterMidnight) {
                    return false;
                }
                // Status filter
                let statusMatch = true;
                if (currentFilter.status === 'all') {
                    statusMatch = true;
                } else {
                    statusMatch = ticket.status === currentFilter.status;
                }
                // Issue type filter
                const issueMatch = currentFilter.issue ? 
                    ticket.issue === currentFilter.issue : true;
                // Search filter
                const searchMatch = currentFilter.search ? 
                    Object.values(ticket).some(value => 
                        String(value).toLowerCase().includes(currentFilter.search.toLowerCase())
                    ) : true;
                return statusMatch && issueMatch && searchMatch;
            });
            renderTickets(filteredTickets);
        }

        // Render tickets to the DOM
        function renderTickets(tickets) {
            if (tickets.length === 0) {
                ticketsContainer.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">No tickets found</h3>
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
                const statusClass = `status-${ticket.status.toLowerCase().replace(/ /g, '-')}`;
                const cardClass = `ticket-card ${ticket.status.toLowerCase().replace(/ /g, '-')} bg-white p-5 mb-3 rounded-lg shadow-card border border-gray-200`;
                ticketsHTML += `
                    <div class="${cardClass}" data-ticket-id="${ticket.id}">
                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <h3 class="font-semibold text-gray-900">${ticket.issue}</h3>
                                    <span class="status-badge ${statusClass}">${ticket.status}</span>
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
                                        <span>${ticket.date}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 md:mt-0 flex items-center gap-2">
                                <button class="view-details-btn bg-primary-500 hover:bg-primary-600 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition" data-ticket-id="${ticket.id}">
                                    Details
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
            document.getElementById('modal-ticket-date').textContent = ticket.date;
            document.getElementById('modal-customer-name').textContent = ticket.customerName || 'Not provided';
            document.getElementById('modal-customer-email').textContent = ticket.customerEmail || 'Not provided';
            document.getElementById('modal-customer-phone').textContent = ticket.customerPhone || 'Not provided';
            document.getElementById('modal-customer-address').textContent = ticket.customerAddress || 'Not provided';
            document.getElementById('modal-order-id').textContent = ticket.orderId;
            document.getElementById('modal-issue-type').textContent = ticket.issue;
            document.getElementById('modal-issue-description').textContent = ticket.description;
            
            // Set status badge
            const statusBadge = document.getElementById('modal-ticket-status');
            const statusClass = `status-${ticket.status.toLowerCase().replace(/ /g, '-')}`;
            statusBadge.className = `status-badge ${statusClass} text-sm`;
            statusBadge.textContent = ticket.status;
            
            // Show resolution if available
            const resolutionSection = document.getElementById('resolution-section');
            const resolutionText = document.getElementById('modal-resolution');
            if (ticket.resolution && ticket.resolution.trim() !== '') {
                resolutionText.textContent = ticket.resolution;
                resolutionSection.classList.remove('hidden');
            } else {
                resolutionSection.classList.add('hidden');
            }
            
            // Show modal
            ticketModal.classList.remove('hidden');
            
            // Render quick status buttons
            renderQuickStatusButtonsModal(ticket);
        }

        // Render quick status buttons for modal
        function renderQuickStatusButtonsModal(ticket) {
            // Modal status flow
            const statusFlow = ['Open', 'In Progress', 'Exchange Approved', 'Awaiting Customer', 'Resolved', 'Closed'];
            const currentStatusIndex = statusFlow.indexOf(ticket.status);
            let buttonsHTML = '';
            // For Resolved, only enable after 1 day since status set
            let resolvedEnabled = true;
            if (statusFlow[currentStatusIndex + 1] === 'Resolved') {
                // Only use awaiting_customer_at for cooldown
                let awaitingCustomerAt = ticket.awaiting_customer_at;
                if (awaitingCustomerAt) {
                    const awaitingDate = new Date(awaitingCustomerAt);
                    const now = new Date();
                    const diffMs = now - awaitingDate;
                    const diffDays = diffMs / (1000 * 60 * 60 * 24);
                    resolvedEnabled = diffDays >= 1;
                } else {
                    // If not set, Resolved should be disabled
                    resolvedEnabled = false;
                }
            }
            statusFlow.forEach((status, idx) => {
                const isCurrent = idx === currentStatusIndex;
                const isNext = idx === currentStatusIndex + 1;
                let disabled = true;
                let extra = '';
                if (isCurrent) {
                    disabled = true;
                } else if (isNext) {
                    if (status === 'Resolved') {
                        disabled = !resolvedEnabled;
                        if (!resolvedEnabled) {
                            let awaitingCustomerAt = ticket.awaiting_customer_at;
                            if (awaitingCustomerAt) {
                                const awaitingDate = new Date(awaitingCustomerAt);
                                const now = new Date();
                                const diffMs = now - awaitingDate;
                                const msLeft = (1000 * 60 * 60 * 24) - diffMs;
                                if (msLeft > 0) {
                                    const hours = Math.floor(msLeft / (1000 * 60 * 60));
                                    const minutes = Math.floor((msLeft % (1000 * 60 * 60)) / (1000 * 60));
                                    const seconds = Math.floor((msLeft % (1000 * 60)) / 1000);
                                    extra = `<span class='text-xs text-gray-400 ml-2'>(Available in ${hours}h ${minutes}m ${seconds}s)</span>`;
                                }
                            }
                        }
                    } else {
                        disabled = false;
                    }
                }
                buttonsHTML += `
                    <button class="status-update-btn ${isCurrent ? 'active' : ''} ${disabled ? 'opacity-50 cursor-not-allowed' : ''}"
                        data-ticket-id="${ticket.id}"
                        data-status="${status}"
                        ${disabled ? 'disabled' : ''}>
                        ${status}
                        ${isCurrent ? '<i class=\"fas fa-check ml-1\"></i>' : ''}
                        ${extra}
                    </button>
                `;
            });
            document.getElementById('quick-status-ticket-modal').innerHTML = buttonsHTML;
            // Add event listeners to status update buttons
            document.querySelectorAll('#quick-status-ticket-modal .status-update-btn:not([disabled])').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const ticketId = this.dataset.ticketId;
                    const status = this.dataset.status;
                    this.classList.add('is-loading');
                    this.innerHTML = '<span class="loading-spinner mr-2"><i class="fas fa-circle-notch fa-spin"></i></span>' + status;
                    updateTicketStatusAjax(ticketId, status, this);
                });
            });
            // If Resolved is next and not enabled, update countdown every second
            if (statusFlow[currentStatusIndex + 1] === 'Resolved' && !resolvedEnabled) {
                let awaitingCustomerAt = ticket.awaiting_customer_at;
                if (awaitingCustomerAt) {
                    const updateCountdown = () => {
                        const awaitingDate = new Date(awaitingCustomerAt);
                        const now = new Date();
                        const diffMs = now - awaitingDate;
                        const msLeft = (1000 * 60 * 60 * 24) - diffMs;
                        if (msLeft > 0) {
                            const hours = Math.floor(msLeft / (1000 * 60 * 60));
                            const minutes = Math.floor((msLeft % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((msLeft % (1000 * 60)) / 1000);
                            const btn = document.querySelector('#quick-status-ticket-modal .status-update-btn[data-status="Resolved"]');
                            if (btn) {
                                btn.innerHTML = `Resolved <span class='text-xs text-gray-400 ml-2'>(Available in ${hours}h ${minutes}m ${seconds}s)</span>`;
                            }
                            setTimeout(updateCountdown, 1000);
                        } else {
                            // Enable the button
                            const btn = document.querySelector('#quick-status-ticket-modal .status-update-btn[data-status="Resolved"]');
                            if (btn) {
                                btn.disabled = false;
                                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                                btn.innerHTML = 'Resolved';
                                btn.addEventListener('click', function(e) {
                                    e.stopPropagation();
                                    const ticketId = this.dataset.ticketId;
                                    const status = this.dataset.status;
                                    this.classList.add('is-loading');
                                    this.innerHTML = '<span class="loading-spinner mr-2"><i class="fas fa-circle-notch fa-spin"></i></span>' + status;
                                    updateTicketStatusAjax(ticketId, status, this);
                                });
                            }
                        }
                    };
                    updateCountdown();
                }
            }
        }

        // AJAX update for ticket status with animation and real-time update
        function updateTicketStatusAjax(ticketId, status, btn) {
            fetch('processes/process_update_ticket_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `ticket_id=${encodeURIComponent(ticketId)}&status=${encodeURIComponent(status)}`
            })
            .then(res => res.json())
            .then(async result => {
                if (result.success) {
                    // Animate success
                    btn.classList.remove('is-loading');
                    btn.classList.add('bg-success-500', 'text-white');
                    btn.innerHTML = '<i class="fas fa-check mr-1"></i>Updated!';
                    
                    setTimeout(() => {
                        ticketModal.classList.add('hidden');
                        loadTickets(); // Reload tickets real-time
                    }, 700);
                } else {
                    btn.classList.remove('is-loading');
                    btn.classList.add('bg-danger-500', 'text-white');
                    btn.innerHTML = '<i class="fas fa-times mr-1"></i>Error';
                    
                    setTimeout(() => {
                        btn.classList.remove('bg-danger-500', 'text-white');
                        btn.innerHTML = status;
                    }, 1200);
                    
                    alert('Failed to update status: ' + (result.error || 'Unknown error'));
                }
            })
            .catch(error => {
                btn.classList.remove('is-loading');
                btn.classList.add('bg-danger-500', 'text-white');
                btn.innerHTML = '<i class="fas fa-times mr-1"></i>Error';
                
                setTimeout(() => {
                    btn.classList.remove('bg-danger-500', 'text-white');
                    btn.innerHTML = status;
                }, 1200);
                
                alert('An error occurred while updating the ticket status.');
            });
        }

        // Update statistics
        function updateStats() {
            const total = ticketsData.length;
            const pending = ticketsData.filter(t => 
                ['pending', 'open', 'awaiting customer'].includes(t.status.toLowerCase())).length;
            const resolved = ticketsData.filter(t => 
                t.status.toLowerCase() === 'resolved').length;
            const closed = ticketsData.filter(t => 
                t.status.toLowerCase() === 'closed').length;
            
            totalTicketsEl.textContent = total;
            pendingTicketsEl.textContent = pending;
            resolvedTicketsEl.textContent = resolved;
            closedTicketsEl.textContent = closed;
        }

        // Show error state
        function showErrorState(message) {
            ticketsContainer.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon text-danger-500">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Unable to load tickets</h3>
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