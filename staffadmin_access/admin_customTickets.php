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
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7d310a',
                        secondary: '#cf8756',
                        accent: '#e8a56a',
                        dark: '#270f03',
                        light: '#f9f5f2',
                        textdark: '#333',
                        textlight: '#777',
                    },
                    fontFamily: {
                        inter: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        /* This is a helper style to ensure main content shifts correctly when sidebar is open */
        body {
            display: flex;
            min-height: 100vh;
        }

        .main-content-wrapper {
            flex: 1;
            padding-left: 0;
            /* Default for mobile/collapsed */
            transition: padding-left 0.3s ease;
        }

        @media (min-width: 768px) {
            .main-content-wrapper {
                padding-left: 250px;
                /* Adjust for sidebar width on desktop */
            }
        }

        .page-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .form-box {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            max-height: 90vh;
            /* Set a maximum height for scrolling */
            overflow-y: auto;
            /* Enable vertical scrolling */
        }

        /* New class for the sticky header */
        .sticky-header {
            position: sticky;
            top: 0;
            background-color: white;
            z-index: 10;
            border-bottom: 1px solid #e8d9cf;
            margin-bottom: 1.5rem;
            /* Equivalent to mb-6 */
            padding-bottom: 1rem;
            /* Equivalent to pb-4 */
        }


        .ticket-header {
            background: linear-gradient(90deg, #f9f5f2 0%, #f0e6df 100%);
            border-bottom: 1px solid #e8d9cf;
        }

        .ticket-item {
            border: 1px solid #e8d9cf;
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .ticket-item:hover {
            border-color: #cf8756;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        .status-processing {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .status-resolved {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .status-closed {
            background-color: #e5e7eb;
            color: #4b5563;
        }
        
        .order-card {
            border: 1px solid #e8d9cf;
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .order-card:hover, .order-card.selected {
            border-color: #7d310a;
            box-shadow: 0 4px 12px rgba(125, 49, 10, 0.1);
        }
        
        .order-card.selected {
            background-color: #f9f5f2;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 700px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .filter-button {
            position: relative;
            transition: all 0.3s ease;
        }

        .filter-button::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 100%;
            height: 2px;
            background-color: #7d310a;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .filter-button.active::after {
            transform: scaleX(1);
        }

        .view-details-btn {
            position: relative;
        }

        .view-details-btn::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: #7d310a;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .view-details-btn:hover::after {
            transform: scaleX(1);
        }
        
        .tab-button {
            position: relative;
            font-weight: 500;
            flex: 1; /* This will make buttons occupy equal space */
            text-align: center;
        }

        .tab-button::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #7d310a;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .tab-button.active::after {
            transform: scaleX(1);
        }

        @media (max-width: 768px) {
            .ticket-container {
                flex-direction: column;
            }
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="main-content-wrapper">
        <main class="min-h-screen">
            <div class="max-w-7xl mx-auto py-8 px-4">
                <div class="mb-8">
                    <a href="javascript:history.back()" class="flex items-center text-gray-800 hover:text-primary transition">
                        <h1 class="text-3xl font-black flex items-center">
                        <i class="fas fa-undo-alt mr-3 text-[#94481b]"></i>Back
                        </h1>
                    </a>
                </div>

                <div class="page-container">
                    <div class="ticket-container flex flex-col lg:flex-row gap-6">
                        <!-- Ticket History List -->
                        <div class="form-box flex-grow p-6">
                            <div class="ticket-header pb-4 border-b border-gray-200 mb-6">
                                <div class="flex items-center text-primary font-black text-xl mb-4">
                                    <i class="fa-solid fa-list-ul mr-3"></i>
                                    <span>Ticket History</span>
                                </div>
                                <div class="flex gap-4">
                                    <button id="pending-btn" class="tab-button text-primary py-2 active">Pending</button>
                                    <button id="resolved-btn" class="tab-button text-primary py-2">Resolved</button>
                                </div>
                                
                            </div>
                            <!-- Filter Dropdown -->
                            <div class="mb-6">
                                <select id="issue-filter" class="w-full p-3 text-textdark rounded-lg border border-gray-300 bg-white">
                                    <option value="">All Issues</option>
                                    <option value="Cracked Tiles">Cracked Tiles</option>
                                    <option value="Shattered Tiles">Shattered Tiles</option>
                                    <option value="Defective Tiles">Defective Tiles</option>
                                    <option value="Wrong Item Delivered">Wrong Item Delivered</option>
                                    <option value="Other Issue">Other Issue</option>
                                </select>
                            </div>

                            <div id="ticket-list" class="space-y-4">
                                <!-- Tickets will be dynamically inserted here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details Modal -->
                <div id="detailsModal" class="modal">
                    <div class="modal-content">
                        <div id="modal-content-inner">
                            <!-- Content will be dynamically loaded here -->
                        </div>
                    </div>
                </div>

                <script>
                    const ticketsData = [
                        { id: 'TKT-789456', orderId: 'ORD-123456', date: 'Nov 16, 2023', status: 'Pending', issue: 'Cracked Tiles', description: 'Several tiles arrived with cracks along the edges. The package appeared to have been mishandled during transit.', resolution: 'Exchange for Same Item', product: 'Arte Ceramiche Matte Floor Tile', price: 200, branch: 'Deparo Branch', qty: 2, customerName: 'Juan Dela Cruz', customerEmail: 'juan.dcruz@email.com', customerPhone: '0917-123-4567' },
                        { id: 'TKT-789457', orderId: 'ORD-123457', date: 'Nov 5, 2023', status: 'Resolved', issue: 'Shattered Tiles', description: 'Two tiles were completely shattered upon opening the box. The sound was very distinct when the package was dropped by the delivery personnel.', resolution: 'Replacement Sent', product: 'Porcelain Wood-Look Tile', price: 150, branch: 'Brixton Branch', qty: 1, customerName: 'Maria Santos', customerEmail: 'maria.santos@email.com', customerPhone: '0922-345-6789' },
                        { id: 'TKT-789455', orderId: 'ORD-123455', date: 'Oct 28, 2023', status: 'Closed', issue: 'Color Mismatch', description: 'Tiles received don\'t match the sample color from the showroom. They are a much darker shade of beige.', resolution: 'Refund Issued', product: 'Marble Effect Wall Tile', price: 700, branch: 'Vanguard Branch', qty: 1, customerName: 'Pedro Reyes', customerEmail: 'pedro.reyes@email.com', customerPhone: '0933-456-7890' },
                    ];

                    const ticketList = document.getElementById('ticket-list');
                    const pendingBtn = document.getElementById('pending-btn');
                    const resolvedBtn = document.getElementById('resolved-btn');
                    const issueFilter = document.getElementById('issue-filter');
                    const detailsModal = document.getElementById('detailsModal');
                    const modalContentInner = document.getElementById('modal-content-inner');

                    let currentFilter = { status: 'Pending', issue: '' };

                    function filterAndRenderTickets() {
                        let filteredTickets = ticketsData.filter(ticket => {
                            const statusMatch = currentFilter.status ? ticket.status.toLowerCase() === currentFilter.status.toLowerCase() : true;
                            const issueMatch = currentFilter.issue ? ticket.issue === currentFilter.issue : true;
                            return statusMatch && issueMatch;
                        });

                        ticketList.innerHTML = '';
                        if (filteredTickets.length === 0) {
                            ticketList.innerHTML = '<div class="text-center text-textlight py-12">No tickets match your filters.</div>';
                            return;
                        }

                        filteredTickets.forEach(ticket => {
                            const statusClass = `status-${ticket.status.toLowerCase()}`;
                            const initials = ticket.product.split(' ').map(n => n[0]).join('');
                            const ticketHtml = `
                                <div class="order-card p-4 flex items-center justify-between" data-ticket-id="${ticket.id}">
                                    <div class="flex items-center">
                                        <div class="w-16 h-16 rounded-lg bg-light text-primary font-bold flex items-center justify-center text-xl mr-4">
                                            ${initials}
                                        </div>
                                        <div class="flex-grow">
                                            <p class="font-bold text-textdark">${ticket.product}</p>
                                            <p class="text-sm text-textlight">Order #${ticket.orderId} • ${ticket.date}</p>
                                            <p class="text-sm text-textlight">${ticket.branch} • Qty: ${ticket.qty}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-primary font-black mb-2">₱${ticket.price.toLocaleString()}</p>
                                        <span class="${statusClass} text-xs font-medium py-1 px-2 rounded-full">${ticket.status}</span>
                                        <button class="text-primary text-sm font-medium mt-2 view-details-btn block w-full text-right">View Details</button>
                                    </div>
                                </div>
                            `;
                            ticketList.innerHTML += ticketHtml;
                        });
                        
                        document.querySelectorAll('.view-details-btn').forEach(button => {
                            button.addEventListener('click', function(e) {
                                e.stopPropagation(); // Prevents the parent div from being clicked
                                const ticketId = this.closest('.order-card').dataset.ticketId;
                                const ticket = ticketsData.find(t => t.id === ticketId);
                                if (ticket) {
                                    renderTicketDetails(ticket);
                                }
                            });
                        });

                        document.querySelectorAll('.order-card').forEach(card => {
                            card.addEventListener('click', function() {
                                document.querySelectorAll('.order-card').forEach(c => c.classList.remove('selected'));
                                this.classList.add('selected');
                            });
                        });
                    }
                    
                    function renderTicketDetails(ticket) {
                        const statusClass = `status-${ticket.status.toLowerCase()}`;
                        const isPending = ticket.status === 'Pending';

                        let modalHtml = '';

                        if (isPending) {
                            modalHtml = `
                                <div class="p-8">
                                    <div class="flex justify-between items-center mb-6 pb-4 border-b border-textlight/20">
                                        <h2 class="text-2xl font-black text-primary">Ticket Details</h2>
                                        <button class="text-2xl text-textlight hover:text-textdark close-modal-btn">&times;</button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                        <div>
                                            <label class="block text-textdark font-medium mb-1">Ticket ID</label>
                                            <div class="w-full p-3 text-textdark rounded-lg border border-gray-300 bg-white">${ticket.id}</div>
                                        </div>
                                        <div>
                                            <label class="block text-textdark font-medium mb-1">Status</label>
                                            <div class="w-full p-3 rounded-lg border border-gray-300 bg-white">
                                                <span class="${statusClass} text-sm font-medium py-1 px-2 rounded-full">${ticket.status}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-textdark font-medium mb-1">Order ID</label>
                                            <div class="w-full p-3 text-textdark rounded-lg border border-gray-300 bg-white">#${ticket.orderId}</div>
                                        </div>
                                        <div>
                                            <label class="block text-textdark font-medium mb-1">Date</label>
                                            <div class="w-full p-3 text-textdark rounded-lg border border-gray-300 bg-white">${ticket.date}</div>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-textdark font-medium mb-1">Customer Name</label>
                                            <div class="w-full p-3 text-textdark rounded-lg border border-gray-300 bg-white">${ticket.customerName}</div>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-textdark font-medium mb-1">Issue Type</label>
                                            <input type="text" class="w-full p-3 text-textdark rounded-lg border border-gray-300 bg-white" value="${ticket.issue}" disabled>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-textdark font-medium mb-1">Description</label>
                                            <textarea class="w-full p-3 text-textdark rounded-lg border border-gray-300 bg-white" rows="3" disabled>${ticket.description}</textarea>
                                        </div>
                                    </div>

                                    <div class="mb-6">
                                        <label class="block text-textdark font-medium mb-2">Proposed Solution</label>
                                        <select id="solution-select" class="w-full p-3 text-textdark rounded-lg border border-gray-300 bg-white">
                                            <option value="">Select a solution</option>
                                            <option value="Exchange for Same Item">Exchange for Same Item</option>
                                            <option value="Refund Issued">Refund Issued</option>
                                            <option value="Store Credit">Store Credit</option>
                                            <option value="Replacement Sent">Replacement Sent</option>
                                        </select>
                                    </div>
                                    <div class="flex justify-end gap-4 mt-6">
                                        <button class="py-2 px-6 rounded-lg font-medium text-textdark border border-textlight/20 hover:bg-gray-100 transition close-modal-btn">Close</button>
                                        <button id="send-btn" class="py-2 px-6 rounded-lg font-bold text-lg transition bg-primary hover:bg-secondary text-white">Send</button>
                                    </div>
                                </div>
                            `;
                        } else {
                            modalHtml = `
                                <div class="p-8">
                                    <div class="flex justify-between items-center mb-6 pb-4 border-b border-textlight/20">
                                        <h2 class="text-2xl font-black text-primary">Ticket Details</h2>
                                        <button class="text-2xl text-textlight hover:text-textdark close-modal-btn">&times;</button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                        <div>
                                            <label class="block text-textdark font-medium mb-1">Ticket ID</label>
                                            <div class="w-full p-3 text-textdark rounded-lg border border-gray-300 bg-white">${ticket.id}</div>
                                        </div>
                                        <div>
                                            <label class="block text-textdark font-medium mb-1">Status</label>
                                            <div class="w-full p-3 rounded-lg border border-gray-300 bg-white">
                                                <span class="${statusClass} text-sm font-medium py-1 px-2 rounded-full">${ticket.status}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-textdark font-medium mb-1">Order ID</label>
                                            <div class="w-full p-3 text-textdark rounded-lg border border-gray-300 bg-white">#${ticket.orderId}</div>
                                        </div>
                                        <div>
                                            <label class="block text-textdark font-medium mb-1">Date</label>
                                            <div class="w-full p-3 text-textdark rounded-lg border border-gray-300 bg-white">${ticket.date}</div>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-textdark font-medium mb-1">Customer Name</label>
                                            <div class="w-full p-3 text-textdark rounded-lg border border-gray-300 bg-white">${ticket.customerName}</div>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-textdark font-medium mb-1">Issue Type</label>
                                            <input type="text" class="w-full p-3 text-textdark rounded-lg border border-gray-300 bg-white" value="${ticket.issue}" disabled>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-textdark font-medium mb-1">Description</label>
                                            <textarea class="w-full p-3 text-textdark rounded-lg border border-gray-300 bg-white" rows="3" disabled>${ticket.description}</textarea>
                                        </div>
                                    </div>

                                    <div class="mb-6">
                                        <label class="block text-textdark font-medium mb-2">Final Solution</label>
                                        <div class="w-full p-3 text-sm text-textdark rounded-lg border border-gray-300 bg-white">${ticket.resolution}</div>
                                    </div>
                                    <div class="flex justify-end gap-4 mt-6">
                                        <button class="py-2 px-6 rounded-lg font-medium text-textdark border border-textlight/20 hover:bg-gray-100 transition close-modal-btn">Close</button>
                                        <button class="py-2 px-6 rounded-lg font-bold text-lg transition bg-gray-400 text-gray-700 cursor-not-allowed" disabled>Resolved</button>
                                    </div>
                                </div>
                            `;
                        }

                        modalContentInner.innerHTML = modalHtml;
                        detailsModal.style.display = 'flex';
                        
                        document.querySelectorAll('.close-modal-btn').forEach(btn => {
                            btn.addEventListener('click', () => {
                                detailsModal.style.display = 'none';
                            });
                        });

                        if (isPending) {
                            const sendBtn = document.getElementById('send-btn');
                            sendBtn.addEventListener('click', () => {
                                const solution = document.getElementById('solution-select').value;
                                if (solution) {
                                    console.log(`Sending solution for ticket ${ticket.id}: ${solution}`);
                                    detailsModal.style.display = 'none';
                                } else {
                                    console.log('Please select a solution.');
                                }
                            });
                        }
                    }
                    
                    pendingBtn.addEventListener('click', () => {
                        currentFilter.status = 'Pending';
                        pendingBtn.classList.add('active');
                        resolvedBtn.classList.remove('active');
                        filterAndRenderTickets();
                    });

                    resolvedBtn.addEventListener('click', () => {
                        currentFilter.status = 'Resolved';
                        resolvedBtn.classList.add('active');
                        pendingBtn.classList.remove('active');
                        filterAndRenderTickets();
                    });

                    issueFilter.addEventListener('change', (e) => {
                        currentFilter.issue = e.target.value;
                        filterAndRenderTickets();
                    });
                    
                    // Initial render
                    filterAndRenderTickets();
                </script>
            </div>
        </main>
    </div>
</body>
</html>