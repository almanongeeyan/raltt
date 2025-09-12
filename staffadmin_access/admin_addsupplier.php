<?php include '../includes/sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Suppliers Dashboard</title>
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 80px;
            --transition-speed: 0.3s;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            transition: padding-left var(--transition-speed);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: margin-left var(--transition-speed);
        }

        /* Adjust main content when sidebar is collapsed */
        html.sidebar-collapsed .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }

        .dashboard-header {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .dashboard-header h1 {
            font-size: 24px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .dashboard-content {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .dashboard-content p {
            color: #666;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <div class="flex">
        <div class="hidden md:block" style="width:250px;"></div>
        <main class="flex-1 min-h-screen md:ml-0" style="margin-left:0;">
            <div class="max-w-7xl mx-auto py-8 px-4">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-truck-field mr-3 text-blue-600"></i>
                        Suppliers Dashboard
                    </h1>
                </div>
                <!-- Search and Add Section -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input id="searchSupplierInput" type="text" placeholder="Search suppliers by name or contact..." class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" />
                        </div>
                        <div class="flex gap-2">
                            <button id="openAddSupplierSidebar" type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition flex items-center">
                                <i class="fas fa-plus-circle mr-2"></i> Add Supplier
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Supplier Grid -->
                <div id="supplierGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8"></div>
                <!-- Pagination (optional, for large supplier lists) -->
                <div class="flex justify-center mt-10">
                    <nav id="paginationNav" class="inline-flex rounded-md shadow-sm"></nav>
                </div>
            </div>
        </main>
    </div>
    <div id="addSupplierSidebar" class="fixed top-0 right-0 h-full w-full max-w-lg bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col" style="max-width: 540px;">
        <div class="flex justify-between items-center p-4 border-b">
            <h2 class="text-xl font-bold text-blue-700 flex items-center gap-2"><i class="fas fa-plus-circle"></i> Add Supplier</h2>
            <button id="closeAddSupplierSidebar" class="text-gray-400 hover:text-blue-600 text-2xl font-bold transition">&times;</button>
        </div>
        <div class="flex-1 overflow-y-auto p-6">
            <form id="addSupplierForm" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Company Logo</label>
                    <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="logo" type="file" accept="image/*" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Company Name</label>
                    <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="company_name" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Contact Number</label>
                    <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="contact_number" required>
                </div>
                <div class="flex gap-3 justify-end mt-6">
                    <button type="button" id="cancelAddSupplierSidebar" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 font-medium transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow transition">Add Supplier</button>
                </div>
            </form>
        </div>
    </div>
    <div id="editSupplierModalContainer"></div>
    <div id="loadingOverlay" class="loading-overlay">
        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);">
            <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <div class="mt-4 text-blue-700 font-semibold text-center">Loading...</div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
    // Sidebar open/close logic
    const addSidebar = document.getElementById('addSupplierSidebar');
    document.getElementById('openAddSupplierSidebar').onclick = () => {
        addSidebar.classList.remove('translate-x-full');
        addSidebar.classList.add('translate-x-0');
        addSidebar.style.display = 'flex';
        document.body.classList.add('overflow-hidden');
    };
    document.getElementById('closeAddSupplierSidebar').onclick = closeSidebar;
    document.getElementById('cancelAddSupplierSidebar').onclick = closeSidebar;
    function closeSidebar() {
        addSidebar.classList.add('translate-x-full');
        addSidebar.classList.remove('translate-x-0');
        setTimeout(() => { addSidebar.style.display = 'none'; document.body.classList.remove('overflow-hidden'); }, 300);
    }

    // Pagination variables
    let allSuppliers = [];
    let currentPage = 1;
    const suppliersPerPage = 16;

    // Fetch and display suppliers
    function fetchSuppliers() {
        document.getElementById('supplierGrid').innerHTML = '<div class="col-span-4 text-center py-12 text-blue-400">Loading suppliers...</div>';
        fetch('processes/get_suppliers.php')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    allSuppliers = res.data;
                    renderSuppliers();
                } else throw new Error(res.message);
            })
            .catch(() => {
                document.getElementById('supplierGrid').innerHTML = '<div class="col-span-4 text-center py-12 text-red-400">Failed to load suppliers.</div>';
            });
    }

    function renderSuppliers() {
        const search = document.getElementById('searchSupplierInput').value.trim().toLowerCase();
        let filtered = allSuppliers.filter(s =>
            s.supplier_name.toLowerCase().includes(search) ||
            s.contact_number.toLowerCase().includes(search)
        );
        // Pagination
        const totalPages = Math.ceil(filtered.length / suppliersPerPage);
        if (currentPage > totalPages) currentPage = 1;
        const startIdx = (currentPage - 1) * suppliersPerPage;
        const endIdx = startIdx + suppliersPerPage;
        const pageSuppliers = filtered.slice(startIdx, endIdx);
        const grid = document.getElementById('supplierGrid');
        if (!pageSuppliers.length) {
            grid.innerHTML = '<div class="col-span-4 text-center py-12 text-gray-400">No suppliers found.</div>';
            document.getElementById('paginationNav').innerHTML = '';
            return;
        }
        grid.innerHTML = pageSuppliers.map(s => `
            <div class="product-card bg-gradient-to-br from-blue-50 to-white border-blue-100 rounded-2xl shadow-lg p-6 flex flex-col items-center relative hover:shadow-2xl">
                <div class="absolute top-3 right-3 z-20 flex gap-2">
                    <button class="edit-btn bg-blue-100 hover:bg-blue-200 text-blue-700 font-bold px-3 py-2 rounded-lg shadow transition flex items-center" title="Edit" 
                        data-id="${s.supplier_id}" 
                        data-name="${s.supplier_name.replace(/'/g, "&#39;")}" 
                        data-contact="${s.contact_number.replace(/'/g, "&#39;")}" 
                        data-logo="${s.supplier_logo ? s.supplier_logo.replace(/'/g, "&#39;") : ''}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="delete-btn bg-yellow-100 hover:bg-yellow-200 text-yellow-700 font-bold px-3 py-2 rounded-lg shadow transition flex items-center" title="Delete" data-id="${s.supplier_id}"><i class="fas fa-trash"></i></button>
                </div>
                <div class="w-24 h-24 flex items-center justify-center bg-gradient-to-br from-blue-50 to-gray-100 rounded-full overflow-hidden border-2 border-blue-100 group-hover:scale-105 transition-transform mb-4">
                    ${s.supplier_logo ? `<img src="${s.supplier_logo}" alt="Logo" class="object-contain w-full h-full" />` : `<i class='fas fa-image text-4xl text-gray-300'></i>`}
                </div>
                <div class="font-bold text-xl text-gray-900 mb-1 text-center tracking-wide">${s.supplier_name}</div>
                <div class="text-blue-500 text-base mt-1 flex items-center justify-center"><i class="fas fa-phone mr-2 text-blue-400"></i>${s.contact_number}</div>
            </div>
        `).join('');
        // Pagination controls
        renderPaginationControls(totalPages);
        // Add event listeners for edit/delete
        grid.querySelectorAll('.delete-btn').forEach(btn => {
            btn.onclick = function() {
                const id = this.dataset.id;
                Swal.fire({
                    title: 'Delete Supplier?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                }).then(result => {
                    if (result.isConfirmed) deleteSupplier(id);
                });
            };
        });
        grid.querySelectorAll('.edit-btn').forEach(btn => {
            btn.onclick = function() {
                showEditModal(this.dataset);
            };
        });
    }

    function renderPaginationControls(totalPages) {
        const pagination = document.getElementById('paginationNav');
        if (!pagination) return;
        if (totalPages <= 1) { pagination.innerHTML = ''; return; }
        let html = '';
        html += `<a href="#" class="py-2 px-3 ml-0 leading-tight text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700" data-page="prev"><i class="fas fa-chevron-left"></i></a>`;
        for (let i = 1; i <= totalPages; i++) {
            html += `<a href="#" class="py-2 px-3 leading-tight border border-gray-300 ${i === currentPage ? 'bg-blue-500 text-white font-bold' : 'bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700'}" data-page="${i}">${i}</a>`;
        }
        html += `<a href="#" class="py-2 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700" data-page="next"><i class="fas fa-chevron-right"></i></a>`;
        pagination.innerHTML = html;
        pagination.querySelectorAll('a[data-page]').forEach(a => {
            a.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.getAttribute('data-page');
                if (page === 'prev') {
                    if (currentPage > 1) currentPage--;
                } else if (page === 'next') {
                    if (currentPage < totalPages) currentPage++;
                } else {
                    currentPage = parseInt(page);
                }
                renderSuppliers();
                document.getElementById('supplierGrid').scrollIntoView({behavior: 'smooth'});
            });
        });
    }

    // Add supplier
    document.getElementById('addSupplierForm').onsubmit = function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        document.getElementById('loadingOverlay').style.display = 'block';
        fetch('processes/process_add_supplier.php', {
            method: 'POST',
            body: formData
        })
        .then(res => {
            document.getElementById('loadingOverlay').style.display = 'none';
            if (res.redirected || res.ok) {
                Swal.fire('Success', 'Supplier added!', 'success');
                form.reset();
                closeSidebar();
                fetchSuppliers();
            } else throw new Error('Failed');
        })
        .catch(() => {
            document.getElementById('loadingOverlay').style.display = 'none';
            Swal.fire('Error', 'Failed to add supplier.', 'error');
        });
    };

    // Delete supplier
    function deleteSupplier(id) {
        document.getElementById('loadingOverlay').style.display = 'block';
        fetch('processes/delete_supplier.php?id=' + encodeURIComponent(id))
            .then(() => {
                document.getElementById('loadingOverlay').style.display = 'none';
                Swal.fire('Deleted!', 'Supplier deleted.', 'success');
                fetchSuppliers();
            })
            .catch(() => {
                document.getElementById('loadingOverlay').style.display = 'none';
                Swal.fire('Error', 'Failed to delete supplier.', 'error');
            });
    }

    // Edit supplier modal
    function showEditModal(data) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md relative border border-blue-200">
                <button class="absolute top-3 right-3 text-gray-400 hover:text-blue-600 text-2xl font-bold transition close-edit-modal">&times;</button>
                <h2 class="text-2xl font-extrabold mb-6 text-blue-700 flex items-center gap-2"><i class='fas fa-pen-to-square'></i> Edit Supplier</h2>
                <form id="editSupplierForm" enctype="multipart/form-data">
                    <input type="hidden" name="supplier_id" value="${data.id}">
                    <input type="hidden" name="current_logo" value="${data.logo}">
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Company Logo</label>
                        <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="logo" type="file" accept="image/*">
                        <div class="mt-2">${data.logo ? `<img src="${data.logo}" alt="Logo" class="w-16 h-16 object-contain rounded-full border">` : '<i class="fas fa-image text-3xl text-gray-300"></i>'}</div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Company Name</label>
                        <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="company_name" value="${data.name}" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Contact Number</label>
                        <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="contact_number" value="${data.contact}" required>
                    </div>
                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" class="close-edit-modal px-5 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 font-medium transition">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow transition">Save Changes</button>
                    </div>
                </form>
            </div>
        `;
        document.body.appendChild(modal);
        modal.querySelectorAll('.close-edit-modal').forEach(btn => btn.onclick = () => modal.remove());
        modal.querySelector('#editSupplierForm').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            document.getElementById('loadingOverlay').style.display = 'block';
            fetch('processes/edit_supplier.php', {
                method: 'POST',
                body: formData
            })
            .then(res => {
                document.getElementById('loadingOverlay').style.display = 'none';
                if (res.redirected || res.ok) {
                    Swal.fire('Updated!', 'Supplier updated.', 'success');
                    modal.remove();
                    fetchSuppliers();
                } else throw new Error('Failed');
            })
            .catch(() => {
                document.getElementById('loadingOverlay').style.display = 'none';
                Swal.fire('Error', 'Failed to update supplier.', 'error');
            });
        };
    }

    // Search filter
    document.getElementById('searchSupplierInput').oninput = function() {
        currentPage = 1;
        renderSuppliers();
    };

    // Initial load
    fetchSuppliers();
    </script>
</body>
</html>