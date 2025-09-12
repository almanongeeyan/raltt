
<?php
include '../includes/sidebar.php';
include '../connection/connection.php';
// Fetch suppliers
$suppliers = $db_connection->query('SELECT * FROM suppliers ORDER BY supplier_id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppliers Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar-form {
            position: fixed;
            right: 0;
            top: 0;
            height: 100vh;
            width: 350px;
            background: #fff;
            box-shadow: -2px 0 10px rgba(0,0,0,0.08);
            z-index: 1050;
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            transform: translateX(100%);
            transition: transform 0.3s cubic-bezier(.4,0,.2,1);
        }
        .sidebar-form.open {
            transform: translateX(0);
        }
        .sidebar-form input[type="file"] {
            border: none;
        }
        .sidebar-form .close-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #888;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="main-content-wrapper" style="margin-left:250px;">
        <header class="bg-white shadow-sm p-6 sticky top-0 z-10">
            <div class="container mx-auto max-w-4xl">
                <h1 class="text-4xl font-extrabold text-gray-800 flex items-center gap-3 mb-2">
                    <i class="fas fa-truck-field text-blue-600 text-4xl"></i>
                    <span>Suppliers</span>
                </h1>
                <button id="addSupplierBtn" class="mt-3 bg-gradient-to-r from-blue-600 to-blue-400 hover:from-blue-700 hover:to-blue-500 text-white font-semibold px-6 py-2 rounded-xl shadow-lg flex items-center gap-2 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <i class="fas fa-plus"></i> Add New Supplier
                </button>
            </div>
        </header>
        <main class="container mx-auto py-10 px-4 max-w-4xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <?php foreach ($suppliers as $supplier): ?>
                <div class="bg-white rounded-2xl shadow-lg p-7 flex items-center gap-6 border border-gray-100 hover:shadow-2xl transition-all duration-200 group relative">
                    <div class="w-24 h-24 flex items-center justify-center bg-gradient-to-br from-blue-50 to-gray-100 rounded-full overflow-hidden border-2 border-blue-100 group-hover:scale-105 transition-transform">
                        <?php if ($supplier['supplier_logo']): ?>
                            <img src="../<?= htmlspecialchars($supplier['supplier_logo']) ?>" alt="Logo" class="object-contain w-full h-full" />
                        <?php else: ?>
                            <i class="fas fa-image text-4xl text-gray-300"></i>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1">
                        <div class="font-extrabold text-xl text-gray-800 mb-1 group-hover:text-blue-700 transition-colors"><?= htmlspecialchars($supplier['supplier_name']) ?></div>
                        <div class="text-gray-500 text-base mt-1 flex items-center"><i class="fas fa-phone mr-2 text-blue-400"></i><?= htmlspecialchars($supplier['contact_number']) ?></div>
                    </div>
                    <div class="flex flex-col gap-2 ml-4">
                        <button class="edit-btn bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded shadow text-sm font-semibold flex items-center gap-1" 
                            data-id="<?= $supplier['supplier_id'] ?>" 
                            data-name="<?= htmlspecialchars($supplier['supplier_name'], ENT_QUOTES) ?>" 
                            data-contact="<?= htmlspecialchars($supplier['contact_number'], ENT_QUOTES) ?>" 
                            data-logo="<?= htmlspecialchars($supplier['supplier_logo'], ENT_QUOTES) ?>">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form action="processes/delete_supplier.php" method="GET" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                            <input type="hidden" name="id" value="<?= $supplier['supplier_id'] ?>">
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded shadow text-sm font-semibold flex items-center gap-1"><i class="fas fa-trash"></i> Delete</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </main>
        <div id="sidebarForm" class="sidebar-form">
            <button class="close-btn" type="button" title="Close">&times;</button>
            <form action="processes/process_add_supplier.php" method="POST" enctype="multipart/form-data" class="flex flex-col gap-5">
                <div class="mb-2">
                    <label class="block text-gray-700 font-semibold mb-2">Company Logo</label>
                    <input type="file" name="logo" accept="image/*" required class="w-full border border-blue-200 rounded-lg p-2 bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300" />
                </div>
                <div class="mb-2">
                    <label class="block text-gray-700 font-semibold mb-2">Company Name</label>
                    <input type="text" name="company_name" required class="w-full border border-blue-200 rounded-lg p-2 bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300" placeholder="Enter company name" />
                </div>
                <div class="mb-2">
                    <label class="block text-gray-700 font-semibold mb-2">Contact Number</label>
                    <input type="text" name="contact_number" required class="w-full border border-blue-200 rounded-lg p-2 bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300" placeholder="Enter contact number" />
                </div>
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-400 text-white font-bold py-2 rounded-xl hover:from-blue-700 hover:to-blue-500 transition-all shadow-lg">Add Supplier</button>
            </form>
        </div>

        <!-- Edit Supplier Modal -->
        <div id="editSupplierModal" class="sidebar-form" style="z-index: 1100; display: none;">
            <button class="close-btn" type="button" title="Close">&times;</button>
            <form id="editSupplierForm" action="processes/edit_supplier.php" method="POST" enctype="multipart/form-data" class="flex flex-col gap-5">
                <input type="hidden" name="supplier_id" id="editSupplierId">
                <input type="hidden" name="current_logo" id="editCurrentLogo">
                <div class="mb-2">
                    <label class="block text-gray-700 font-semibold mb-2">Company Logo</label>
                    <input type="file" name="logo" accept="image/*" class="w-full border border-blue-200 rounded-lg p-2 bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300" />
                    <div id="editLogoPreview" class="mt-2"></div>
                </div>
                <div class="mb-2">
                    <label class="block text-gray-700 font-semibold mb-2">Company Name</label>
                    <input type="text" name="company_name" id="editCompanyName" required class="w-full border border-blue-200 rounded-lg p-2 bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300" placeholder="Enter company name" />
                </div>
                <div class="mb-2">
                    <label class="block text-gray-700 font-semibold mb-2">Contact Number</label>
                    <input type="text" name="contact_number" id="editContactNumber" required class="w-full border border-blue-200 rounded-lg p-2 bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300" placeholder="Enter contact number" />
                </div>
                <button type="submit" class="bg-gradient-to-r from-yellow-500 to-yellow-400 text-white font-bold py-2 rounded-xl hover:from-yellow-600 hover:to-yellow-500 transition-all shadow-lg">Update Supplier</button>
            </form>
        </div>
    </div>
    <script>
        // Add Supplier Sidebar
        const sidebar = document.getElementById('sidebarForm');
        const addBtn = document.getElementById('addSupplierBtn');
        const closeBtn = sidebar.querySelector('.close-btn');
        addBtn.addEventListener('click', () => sidebar.classList.add('open'));
        closeBtn.addEventListener('click', () => sidebar.classList.remove('open'));
        document.addEventListener('mousedown', (e) => {
            if (sidebar.classList.contains('open') && !sidebar.contains(e.target) && !addBtn.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });

        // Edit Supplier Modal
        const editModal = document.getElementById('editSupplierModal');
        const editForm = document.getElementById('editSupplierForm');
        const editBtns = document.querySelectorAll('.edit-btn');
        const editCloseBtn = editModal.querySelector('.close-btn');
        editBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('editSupplierId').value = this.dataset.id;
                document.getElementById('editCompanyName').value = this.dataset.name;
                document.getElementById('editContactNumber').value = this.dataset.contact;
                document.getElementById('editCurrentLogo').value = this.dataset.logo;
                // Show logo preview
                const preview = document.getElementById('editLogoPreview');
                if (this.dataset.logo) {
                    preview.innerHTML = `<img src="../${this.dataset.logo}" alt="Logo" class="w-16 h-16 object-contain rounded-full border">`;
                } else {
                    preview.innerHTML = '<i class="fas fa-image text-3xl text-gray-300"></i>';
                }
                editModal.style.display = 'flex';
                setTimeout(() => editModal.classList.add('open'), 10);
            });
        });
        editCloseBtn.addEventListener('click', () => {
            editModal.classList.remove('open');
            setTimeout(() => editModal.style.display = 'none', 300);
        });
        document.addEventListener('mousedown', (e) => {
            if (editModal.classList.contains('open') && !editModal.contains(e.target) && !e.target.classList.contains('edit-btn')) {
                editModal.classList.remove('open');
                setTimeout(() => editModal.style.display = 'none', 300);
            }
        });
    </script>
</body>
</html>
