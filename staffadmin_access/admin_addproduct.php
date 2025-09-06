<?php
include '../includes/sidebar.php';
// Get branch name from session (set in sidebar.php)
$branch_name = isset($_SESSION['branch_name']) ? $_SESSION['branch_name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Products Dashboard</title>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .product-card {
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .inventory-low {
            background-color: #fef3f2;
            color: #d92d20;
        }
        .inventory-medium {
            background-color: #fffaeb;
            color: #dc6803;
        }
        .inventory-high {
            background-color: #ecfdf3;
            color: #039855;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.2);
            display: none;
            z-index: 9999;
        }
        .multi-select-container {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            padding: 0.5rem;
        }
        .multi-select-item {
            display: flex;
            align-items: center;
            padding: 0.25rem 0;
        }
        
        /* Form selector button styles */
        .form-selector-btn {
            transition: all 0.2s ease;
            border: 2px solid #e2e8f0;
        }
        .form-selector-btn.active {
            border-color: #3b82f6;
            background-color: #eff6ff;
            color: #3b82f6;
            font-weight: 600;
        }
        .form-selector-btn:hover:not(.active) {
            border-color: #93c5fd;
            background-color: #f8fafc;
        }
        
        /* Edit modal sizing */
        .edit-modal-container {
            max-width: 95vw;
            width: 800px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .edit-modal-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        @media (min-width: 768px) {
            .edit-modal-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        /* Sidebar styling */
        #addProductSidebar {
            z-index: 100;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex">
        <!-- Sidebar is included and styled by its own file -->
        <div class="hidden md:block" style="width:250px;"></div>
        <main class="flex-1 min-h-screen md:ml-0" style="margin-left:0;">
            <div class="max-w-7xl mx-auto py-8 px-4">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-boxes mr-3 text-blue-600"></i>
                        <?php if ($branch_name) { ?>
                            <?= htmlspecialchars($branch_name) ?> Product Dashboard
                        <?php } else { ?>
                            Product Dashboard
                        <?php } ?>
                    </h1>
                </div>

                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                        <div class="flex items-center">
                            <div class="rounded-full bg-blue-100 p-3 mr-4">
                                <i class="fas fa-box text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Total Products</p>
                                <h3 class="font-bold text-2xl" id="totalProductsMetric">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-gray-500">
                        <div class="flex items-center">
                            <div class="rounded-full bg-gray-100 p-3 mr-4">
                                <i class="fas fa-archive text-gray-600"></i>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Total Archived</p>
                                <h3 class="font-bold text-2xl" id="totalArchivedMetric">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                        <div class="flex items-center">
                            <div class="rounded-full bg-green-100 p-3 mr-4">
                                <i class="fas fa-warehouse text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Total Inventory</p>
                                <h3 class="font-bold text-2xl" id="totalInventoryMetric">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-amber-500 cursor-pointer" id="lowStockMetricCard">
                        <div class="flex items-center">
                            <div class="rounded-full bg-amber-100 p-3 mr-4">
                                <i class="fas fa-exclamation-triangle text-amber-600"></i>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Low to No Stocks</p>
                                <h3 class="font-bold text-2xl" id="lowStockMetric">0</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter Section -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex flex-1 gap-2">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input id="searchInput" type="text" placeholder="Enter product name, design or type ..." class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" />
                            </div>
                            <select id="categoryDropdown" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none">
                                <option value="all">All Categories</option>
                                <option value="tile">Tile</option>
                                <option value="other">Others</option>
                            </select>
                            <select id="tileTypeDropdown" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none hidden">
                                <option value="">All Tile Designs</option>
                            </select>
                            <select id="otherSpecDropdown" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none hidden">
                                <option value="">All Product Specs</option>
                            </select>
                            <select id="stockStatusDropdown" class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none">
                                <option value="all">Stock Status</option>
                                <option value="in">In Stock</option>
                                <option value="low">Low Stock</option>
                                <option value="none">No Stock</option>
                                <option value="over">Overstock</option>
                            </select>
                            <div class="flex items-center ml-4">
                                <input type="checkbox" id="showArchivedCheckbox" class="form-checkbox h-5 w-5 text-blue-600 transition duration-150 ease-in-out border-gray-300 focus:ring-blue-400">
                                <label for="showArchivedCheckbox" class="ml-2 text-gray-700 select-none cursor-pointer text-sm font-medium">Show archived</label>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button id="openAddProductSidebar" type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition flex items-center">
                                <i class="fas fa-plus-circle mr-2"></i> Add Product
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Add Product Sidebar Drawer -->
                <div id="addProductSidebar" class="fixed top-0 right-0 h-full w-full max-w-lg bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col" style="max-width: 540px;">
                    <div class="flex justify-between items-center p-4 border-b">
                        <h2 class="text-xl font-bold text-blue-700 flex items-center gap-2"><i class="fas fa-plus-circle"></i> Add Product</h2>
                        <button id="cancelAddProductSidebar" class="text-gray-400 hover:text-blue-600 text-3xl font-bold transition">&times;</button>
                    </div>
                    <div class="flex-1 overflow-y-auto p-6">
                        <div class="flex gap-2 mb-6">
                            <button id="btnForTiles" type="button" class="form-selector-btn active px-4 py-2 rounded-lg font-semibold focus:outline-none">Tile Product</button>
                            <button id="btnForOthers" type="button" class="form-selector-btn px-4 py-2 rounded-lg font-semibold focus:outline-none">Other Product</button>
                        </div>
                        <form id="formForTiles" enctype="multipart/form-data">
                            <input type="hidden" name="product_type" value="tile">
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Tile Name</label>
                                <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="product_name" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2 flex items-center gap-2">Tile Description
                                    <button type="button" id="generateDescriptionBtn" class="ml-2 px-3 py-1 rounded bg-blue-500 text-white text-xs font-semibold disabled:opacity-50" disabled>Generate</button>
                                    <span id="generateSpinner" class="ml-2 hidden"><i class="fas fa-spinner fa-spin"></i></span>
                                </label>
                                <textarea id="tileDescription" class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="product_description" rows="3" required disabled></textarea>
                                <div id="generateFailNote" class="text-xs text-red-500 mt-1 hidden">Failed to generate description.</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Tile Classification</label>
                                <select name="tile_classification" class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" required>
                                    <option value="">Select classification</option>
                                    <option value="ceramic">Ceramic</option>
                                    <option value="porcelain">Porcelain</option>
                                    <option value="granite">Granite</option>
                                    <option value="cement">Cement</option>
                                    <option value="glass tiles">Glass Tiles</option>
                                    <option value="marble">Marble</option>
                                    <option value="stone">Stone</option>
                                    <option value="slate">Slate</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Best For</label>
                                <div class="multi-select-container">
                                    <div class="multi-select-item"><input type="checkbox" name="best_for[]" value="indoor"><label class="ml-2">Indoor</label></div>
                                    <div class="multi-select-item"><input type="checkbox" name="best_for[]" value="outdoor"><label class="ml-2">Outdoor</label></div>
                                    <div class="multi-select-item"><input type="checkbox" name="best_for[]" value="swimming pool"><label class="ml-2">Swimming Pool</label></div>
                                    <div class="multi-select-item"><input type="checkbox" name="best_for[]" value="bathroom"><label class="ml-2">Bathroom</label></div>
                                    <div class="multi-select-item"><input type="checkbox" name="best_for[]" value="kitchen countertop"><label class="ml-2">Kitchen Countertop</label></div>
                                    <div class="multi-select-item"><input type="checkbox" name="best_for[]" value="wall"><label class="ml-2">Wall</label></div>
                                    <div class="multi-select-item"><input type="checkbox" name="best_for[]" value="corridor"><label class="ml-2">Corridor</label></div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Tile Design</label>
                                <div class="multi-select-container">
                                    <div class="multi-select-item"><input type="checkbox" name="tile_design[]" value="minimalist"><label class="ml-2">Minimalist</label></div>
                                    <div class="multi-select-item"><input type="checkbox" name="tile_design[]" value="floral"><label class="ml-2">Floral</label></div>
                                    <div class="multi-select-item"><input type="checkbox" name="tile_design[]" value="black and white"><label class="ml-2">Black and White</label></div>
                                    <div class="multi-select-item"><input type="checkbox" name="tile_design[]" value="modern"><label class="ml-2">Modern</label></div>
                                    <div class="multi-select-item"><input type="checkbox" name="tile_design[]" value="rustic"><label class="ml-2">Rustic</label></div>
                                    <div class="multi-select-item"><input type="checkbox" name="tile_design[]" value="geometric"><label class="ml-2">Geometric</label></div>
                                    <div class="multi-select-item"><input type="checkbox" name="tile_design[]" value="lines"><label class="ml-2">Lines</label></div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Tile Finish</label>
                                <select name="tile_finish" class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" required>
                                    <option value="">Select finish</option>
                                    <option value="glossy">Glossy</option>
                                    <option value="matte">Matte</option>
                                    <option value="textured">Textured</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Tile Size</label>
                                <select name="tile_size" class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" required>
                                    <option value="">Select size</option>
                                    <option value="60x60">60x60</option>
                                    <option value="30x60">30x60</option>
                                    <option value="40x40">40x40</option>
                                    <option value="20x20">20x20</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Price</label>
                                <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="product_price" type="number" step="0.01" min="0" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Stock</label>
                                <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="stock_count" type="number" min="0" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Image</label>
                                <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="product_image" type="file" accept="image/*" required>
                            </div>
                            <div class="flex gap-3 justify-end mt-6">
                                <button type="button" id="cancelAddProductSidebar2" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 font-medium transition">Cancel</button>
                                <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow transition">Add Tile Product</button>
                            </div>
                        </form>
                        <form id="formForOthers" class="hidden" enctype="multipart/form-data">
                            <input type="hidden" name="product_type" value="other">
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Product Name</label>
                                <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="product_name" id="otherProductName" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2 flex items-center gap-2">Description
                                    <button type="button" id="generateOtherDescriptionBtn" class="ml-2 px-3 py-1 rounded bg-blue-500 text-white text-xs font-semibold disabled:opacity-50" disabled>Generate</button>
                                    <span id="generateOtherSpinner" class="ml-2 hidden"><i class="fas fa-spinner fa-spin"></i></span>
                                </label>
                                <textarea class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="product_description" id="otherProductDescription" rows="3" required disabled></textarea>
                                <div id="generateOtherFailNote" class="text-xs text-red-500 mt-1 hidden">Failed to generate description.</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Product Specification</label>
                                <select name="product_spec" id="otherProductSpec" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                                    <option value="">Select specification</option>
                                    <option value="PVC Doors">PVC Doors</option>
                                    <option value="Sinks">Sinks</option>
                                    <option value="Tile Vinyl">Tile Vinyl</option>
                                    <option value="Bowls">Bowls</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Price</label>
                                    <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="product_price" id="otherProductPrice" type="number" step="0.01" min="0" required>
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Stock</label>
                                    <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="stock_count" type="number" min="0" required>
                                </div>
                            </div>
                            <div class="mb-4 mt-4">
                                <label class="block text-gray-700 font-semibold mb-2">Image</label>
                                <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="product_image" type="file" accept="image/*" required>
                            </div>
                            <div class="flex gap-3 justify-end mt-6">
                                <button type="button" id="cancelAddProductSidebar2" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 font-medium transition">Cancel</button>
                                <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow transition">Add Product</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Product Grid (Dynamic) -->
                <div id="productGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8"></div>

                <!-- Pagination -->
                <div class="flex justify-center mt-10">
                    <nav class="inline-flex rounded-md shadow-sm">
                        <a href="#" class="py-2 px-3 ml-0 leading-tight text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </nav>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);">
            <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <div class="mt-4 text-blue-700 font-semibold text-center">Loading...</div>
        </div>
    </div>
    
    <!-- Edit Modals -->
    <div id="editModalContainer"></div>

    <script>
    // Global variables
    let allProducts = [];
    let editModal = null;
    
    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeAddProductSidebar();
        initializeFormToggle();
        initializeDynamicFields();
        initializeDescriptionGenerators();
        initializeFormSubmissions();
        loadProducts();
        initializeFilters();
        initializeLowStockMetric();
    });
    
    // Initialize add product sidebar
    function initializeAddProductSidebar() {
        const sidebar = document.getElementById('addProductSidebar');
        const openBtn = document.getElementById('openAddProductSidebar');
        const cancelBtn = document.getElementById('cancelAddProductSidebar');
        const cancelBtn2 = document.getElementById('cancelAddProductSidebar2');
        
        if (openBtn) openBtn.addEventListener('click', function() {
            sidebar.classList.remove('translate-x-full');
            sidebar.classList.add('translate-x-0');
            sidebar.style.display = 'flex';
            document.body.classList.add('overflow-hidden');
        });
        if (cancelBtn) cancelBtn.addEventListener('click', closeSidebar);
        if (cancelBtn2) cancelBtn2.addEventListener('click', closeSidebar);
        function closeSidebar() {
            sidebar.classList.add('translate-x-full');
            sidebar.classList.remove('translate-x-0');
            setTimeout(() => { sidebar.style.display = 'none'; }, 300);
            document.body.classList.remove('overflow-hidden');
        }
    }
    
    // Initialize form toggle between tiles and others
    function initializeFormToggle() {
        const btnForTiles = document.getElementById('btnForTiles');
        const btnForOthers = document.getElementById('btnForOthers');
        const formForTiles = document.getElementById('formForTiles');
        const formForOthers = document.getElementById('formForOthers');
        
        // Add active class styling
        function setActiveButton(activeBtn, inactiveBtn) {
            activeBtn.classList.add('active', 'border-blue-600', 'text-blue-600');
            activeBtn.classList.remove('border-gray-400', 'text-gray-700');
            
            inactiveBtn.classList.remove('active', 'border-blue-600', 'text-blue-600');
            inactiveBtn.classList.add('border-gray-400', 'text-gray-700');
        }
        
        if (btnForTiles && btnForOthers) {
            // Add the form-selector-btn class for styling
            btnForTiles.classList.add('form-selector-btn');
            btnForOthers.classList.add('form-selector-btn');
            
            // Set initial active state
            setActiveButton(btnForTiles, btnForOthers);
            
            btnForTiles.addEventListener('click', function() {
                setActiveButton(btnForTiles, btnForOthers);
                formForTiles.classList.remove('hidden');
                formForOthers.classList.add('hidden');
            });
            
            btnForOthers.addEventListener('click', function() {
                setActiveButton(btnForOthers, btnForTiles);
                formForOthers.classList.remove('hidden');
                formForTiles.classList.add('hidden');
            });
        }
    }
    
    // Initialize dynamic fields for tile designs and best for
    function initializeDynamicFields() {
        // Tile design fields
        const tileDesignFields = document.getElementById('tileDesignFields');
        if (tileDesignFields) {
            tileDesignFields.addEventListener('click', function(e) {
                if (e.target.closest('.add-tile-design-btn')) {
                    const rows = tileDesignFields.querySelectorAll('.tile-design-row');
                    if (rows.length >= 8) return;
                    
                    const row = e.target.closest('.tile-design-row');
                    const newRow = row.cloneNode(true);
                    newRow.querySelector('select').value = '';
                    const btn = newRow.querySelector('.add-tile-design-btn');
                    btn.innerHTML = '<i class="fas fa-minus"></i>';
                    btn.classList.remove('bg-blue-500', 'add-tile-design-btn');
                    btn.classList.add('bg-red-500', 'remove-tile-design-btn');
                    btn.title = 'Remove';
                    
                    tileDesignFields.appendChild(newRow);
                    updateTileDesignOptions();
                    
                    if (rows.length >= 7) {
                        const plusBtns = tileDesignFields.querySelectorAll('.add-tile-design-btn');
                        plusBtns.forEach(btn => btn.style.display = 'none');
                    }
                } else if (e.target.closest('.remove-tile-design-btn')) {
                    const row = e.target.closest('.tile-design-row');
                    row.remove();
                    updateTileDesignOptions();
                    
                    const rows = tileDesignFields.querySelectorAll('.tile-design-row');
                    if (rows.length < 8) {
                        const plusBtns = tileDesignFields.querySelectorAll('.add-tile-design-btn');
                        plusBtns.forEach(btn => btn.style.display = '');
                    }
                }
            });
            
            tileDesignFields.addEventListener('change', function(e) {
                if (e.target.matches('select')) updateTileDesignOptions();
            });
            
            function updateTileDesignOptions() {
                const selects = tileDesignFields.querySelectorAll('select');
                const selected = Array.from(selects).map(s => s.value).filter(v => v);
                
                selects.forEach(select => {
                    const current = select.value;
                    Array.from(select.options).forEach(opt => {
                        if (!opt.value) return;
                        if (selected.includes(opt.value) && opt.value !== current) {
                            opt.style.display = 'none';
                        } else {
                            opt.style.display = '';
                        }
                    });
                });
            }
            
            updateTileDesignOptions();
        }
        
        // Best for fields
        const bestForFields = document.getElementById('bestForFields');
        if (bestForFields) {
            bestForFields.addEventListener('click', function(e) {
                if (e.target.closest('.add-bestfor-btn')) {
                    const rows = bestForFields.querySelectorAll('.best-for-row');
                    if (rows.length >= 8) return;
                    
                    const row = e.target.closest('.best-for-row');
                    const newRow = row.cloneNode(true);
                    newRow.querySelector('select').value = '';
                    const btn = newRow.querySelector('.add-bestfor-btn');
                    btn.innerHTML = '<i class="fas fa-minus"></i>';
                    btn.classList.remove('bg-blue-500', 'add-bestfor-btn');
                    btn.classList.add('bg-red-500', 'remove-bestfor-btn');
                    btn.title = 'Remove';
                    
                    bestForFields.appendChild(newRow);
                    updateBestForOptions();
                    
                    if (rows.length >= 7) {
                        const plusBtns = bestForFields.querySelectorAll('.add-bestfor-btn');
                        plusBtns.forEach(btn => btn.style.display = 'none');
                    }
                } else if (e.target.closest('.remove-bestfor-btn')) {
                    const row = e.target.closest('.best-for-row');
                    row.remove();
                    updateBestForOptions();
                    
                    const rows = bestForFields.querySelectorAll('.best-for-row');
                    if (rows.length < 8) {
                        const plusBtns = bestForFields.querySelectorAll('.add-bestfor-btn');
                        plusBtns.forEach(btn => btn.style.display = '');
                    }
                }
            });
            
            bestForFields.addEventListener('change', function(e) {
                if (e.target.matches('select')) updateBestForOptions();
            });
            
            function updateBestForOptions() {
                const selects = bestForFields.querySelectorAll('select');
                const selected = Array.from(selects).map(s => s.value).filter(v => v);
                
                selects.forEach(select => {
                    const current = select.value;
                    Array.from(select.options).forEach(opt => {
                        if (!opt.value) return;
                        if (selected.includes(opt.value) && opt.value !== current) {
                            opt.style.display = 'none';
                        } else {
                            opt.style.display = '';
                        }
                    });
                });
            }
            
            updateBestForOptions();
        }
    }
    
    // Initialize description generators
    function initializeDescriptionGenerators() {
        // Tile description generator
        const tileDescBtn = document.getElementById('generateDescriptionBtn');
        const tileDescTextarea = document.getElementById('tileDescription');
        const tileForm = document.getElementById('formForTiles');
        const nameInput = tileForm.querySelector('input[name="product_name"]');
        const priceInput = tileForm.querySelector('input[name="product_price"]');
        const classSelect = tileForm.querySelector('select[name="tile_classification"]');
        const finishSelect = tileForm.querySelector('select[name="tile_finish"]');
        const sizeSelect = tileForm.querySelector('select[name="tile_size"]');
        const designCheckboxes = tileForm.querySelectorAll('input[name="tile_design[]"]');

        function checkTileFieldsFilled() {
            const name = nameInput.value.trim();
            const price = priceInput.value.trim();
            const classification = classSelect.value.trim();
            const finish = finishSelect.value.trim();
            const size = sizeSelect.value.trim();
            let designChecked = false;
            designCheckboxes.forEach(cb => { if (cb.checked) designChecked = true; });
            return name && price && classification && finish && size && designChecked;
        }

        function updateTileDescState() {
            const filled = checkTileFieldsFilled();
            tileDescBtn.disabled = !filled;
            tileDescTextarea.disabled = !filled;
        }

        [nameInput, priceInput, classSelect, finishSelect, sizeSelect].forEach(el => {
            el.addEventListener('input', updateTileDescState);
            el.addEventListener('change', updateTileDescState);
        });
        designCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateTileDescState);
        });
        updateTileDescState();

        if (tileDescBtn) {
            tileDescBtn.addEventListener('click', function() {
                if (!checkTileFieldsFilled()) {
                    Swal.fire('Error', 'Please fill all required fields first', 'error');
                    return;
                }
                const name = nameInput.value.trim();
                const price = priceInput.value.trim();
                const classification = classSelect.value.trim();
                const finish = finishSelect.value.trim();
                const size = sizeSelect.value.trim();
                const designs = Array.from(designCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
                // Call your Python backend here (AJAX to generate_tile_description.py)
                generateDescription('tile', { name, price, designs, classification, finish, size });
            });
        }
        
        // Other product description generator
        const otherDescBtn = document.getElementById('generateOtherDescriptionBtn');
        const otherDescTextarea = document.getElementById('otherProductDescription');
        const otherForm = document.getElementById('formForOthers');
        const otherNameInput = document.getElementById('otherProductName');
        const otherPriceInput = document.getElementById('otherProductPrice');
        const otherSpecSelect = document.getElementById('otherProductSpec');

        function checkOtherFieldsFilled() {
            return otherNameInput.value.trim() && otherPriceInput.value.trim() && otherSpecSelect.value.trim();
        }

        function updateOtherDescState() {
            const filled = checkOtherFieldsFilled();
            otherDescBtn.disabled = !filled;
            otherDescTextarea.disabled = !filled;
        }

        [otherNameInput, otherPriceInput, otherSpecSelect].forEach(el => {
            el.addEventListener('input', updateOtherDescState);
            el.addEventListener('change', updateOtherDescState);
        });
        updateOtherDescState();

        if (otherDescBtn) {
            otherDescBtn.addEventListener('click', function() {
                if (!checkOtherFieldsFilled()) {
                    Swal.fire('Error', 'Please fill all required fields first', 'error');
                    return;
                }
                const name = otherNameInput.value.trim();
                const price = otherPriceInput.value.trim();
                const spec = otherSpecSelect.value.trim();
                generateDescription('other', { name, price, spec });
            });
        }
        
        function generateDescription(type, data) {
            const spinner = type === 'tile' ? 
                document.getElementById('generateSpinner') : 
                document.getElementById('generateOtherSpinner');
            const textarea = type === 'tile' ? 
                document.getElementById('tileDescription') : 
                document.getElementById('otherProductDescription');
            const failNote = type === 'tile' ? 
                document.getElementById('generateFailNote') : 
                document.getElementById('generateOtherFailNote');
            const endpoint = type === 'tile' ? 
                'processes/generate_description.php' : 
                'processes/generate_otherproduct_description.php';
            spinner.classList.remove('hidden');
            textarea.value = '';
            showLoadingOverlay();
            fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(data => {
                if (data.description) {
                    textarea.value = data.description;
                    failNote.classList.add('hidden');
                } else {
                    textarea.value = 'Failed to generate description.';
                    failNote.classList.remove('hidden');
                }
            })
            .catch(() => {
                textarea.value = 'Error generating description.';
                failNote.classList.remove('hidden');
            })
            .finally(() => {
                spinner.classList.add('hidden');
                hideLoadingOverlay();
            });
        }
    }
    
    // Initialize form submissions
    function initializeFormSubmissions() {
        // Handle tile form submission
        const tileForm = document.getElementById('formForTiles');
        if (tileForm) {
            tileForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitProductForm(this, 'tile');
            });
        }
        
        // Handle other form submission
        const otherForm = document.getElementById('formForOthers');
        if (otherForm) {
            otherForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitProductForm(this, 'other');
            });
        }
        
        function submitProductForm(form, type) {
            const formData = new FormData(form);
            formData.set('product_type', type);
            showLoadingOverlay();
            fetch('processes/store_product.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Product Added!',
                        text: 'The product was successfully added.',
                        timer: 1800,
                        showConfirmButton: false
                    });
                    form.reset();
                    // Wait for sidebar close animation before loading products
                    closeSidebarWithCallback(loadProducts);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to add product.'
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to add product.'
                });
            })
            .finally(() => {
                hideLoadingOverlay();
            });
        }

        // Helper: close sidebar and run callback after animation
        function closeSidebarWithCallback(cb) {
            const sidebar = document.getElementById('addProductSidebar');
            sidebar.classList.add('translate-x-full');
            sidebar.classList.remove('translate-x-0');
            setTimeout(() => {
                sidebar.style.display = 'none';
                document.body.classList.remove('overflow-hidden');
                if (typeof cb === 'function') cb();
            }, 300); // match animation duration
        }
    }
    
    // Load products from server
    function loadProducts() {
        const showArchived = document.getElementById('showArchivedCheckbox')?.checked ? 1 : 0;
        
        showLoadingOverlay();
        
        fetch('processes/get_products.php?show_archived=' + showArchived)
            .then(res => res.json())
            .then(products => {
                allProducts = products;
                populateFilterDropdowns(products);
                filterProducts();
            })
            .catch(error => {
                console.error('Error loading products:', error);
                document.getElementById('productGrid').innerHTML = 
                    '<div class="col-span-4 text-center text-red-400 py-10">Failed to load products.</div>';
            })
            .finally(() => {
                hideLoadingOverlay();
            });
    }
    
    // Populate filter dropdowns
    function populateFilterDropdowns(products) {
        // Tile designs
        const tileTypeDropdown = document.getElementById('tileTypeDropdown');
        if (tileTypeDropdown) {
            const allTileDesigns = new Set();
            products.forEach(p => {
                if (p.product_type === 'tile' && Array.isArray(p.tile_designs)) {
                    p.tile_designs.forEach(des => allTileDesigns.add(des));
                }
            });
            tileTypeDropdown.innerHTML = '<option value="">All Tile Designs</option>' +
                Array.from(allTileDesigns).map(des => `<option value="${des}">${des}</option>`).join('');
        }
        
        // Other product specs
        const otherSpecDropdown = document.getElementById('otherSpecDropdown');
        if (otherSpecDropdown) {
            const allSpecs = new Set();
            products.forEach(p => {
                if (p.product_type === 'other' && p.product_spec) {
                    allSpecs.add(p.product_spec);
                }
            });
            otherSpecDropdown.innerHTML = '<option value="">All Product Specs</option>' +
                Array.from(allSpecs).map(spec => `<option value="${spec}">${spec}</option>`).join('');
        }
    }
    
    // Filter products based on criteria
    function filterProducts() {
        const search = document.getElementById('searchInput').value.trim().toLowerCase();
        const category = document.getElementById('categoryDropdown').value;
        const tileType = document.getElementById('tileTypeDropdown').value;
        const otherSpec = document.getElementById('otherSpecDropdown').value;
        const stockStatus = document.getElementById('stockStatusDropdown').value;
        const showArchived = document.getElementById('showArchivedCheckbox').checked;
        
        let filtered = allProducts.filter(product => {
            // Filter by archived status FIRST
            if (!showArchived && product.is_archived && product.is_archived != '0') return false;
            
            // Then apply other filters
            // Filter by search term
            if (search) {
                const searchableText = [
                    product.product_name,
                    product.product_description,
                    product.product_spec,
                    ...(product.tile_designs || []),
                    ...(product.tile_classifications || []),
                    ...(product.tile_finishes || []),
                    ...(product.tile_sizes || []),
                    ...(product.best_for || [])
                ].filter(Boolean).join(' ').toLowerCase();
                
                if (!searchableText.includes(search)) return false;
            }
            
            // Filter by category
            if (category !== 'all' && product.product_type !== category) return false;
            
            // Filter by tile type
            if (category === 'tile' && tileType) {
                if (!Array.isArray(product.tile_designs) || !product.tile_designs.includes(tileType)) return false;
            }
            
            // Filter by other spec
            if (category === 'other' && otherSpec && product.product_spec !== otherSpec) return false;
            
            // Filter by stock status
            const stock = parseInt(product.stock_count || 0);
            if (stockStatus === 'in' && !(stock >= 100 && stock < 300)) return false;
            if (stockStatus === 'over' && stock < 300) return false;
            if (stockStatus === 'low' && !(stock < 50 && stock >= 10)) return false;
            if (stockStatus === 'none' && stock >= 10) return false;
            
            return true;
        });
        
        // Update metrics
        updateMetrics(filtered);
        
        // Render products
        renderProducts(filtered);
    }
    
    // Update metrics based on filtered products
    function updateMetrics(products) {
        document.getElementById('totalProductsMetric').textContent = 
            products.filter(p => !p.is_archived || p.is_archived == '0').length;
        
        document.getElementById('totalInventoryMetric').textContent = 
            products.filter(p => !p.is_archived || p.is_archived == '0')
                .reduce((sum, p) => sum + (parseInt(p.stock_count || 0)), 0);
        
        document.getElementById('lowStockMetric').textContent = 
            products.filter(p => {
                const stock = parseInt(p.stock_count || 0);
                return (!p.is_archived || p.is_archived == '0') && stock < 50;
            }).length;
        
        document.getElementById('totalArchivedMetric').textContent = 
            products.filter(p => p.is_archived && p.is_archived != '0').length;
    }
    
    // Render products to the grid
    function renderProducts(products) {
        const grid = document.getElementById('productGrid');
        
        if (!products.length) {
            grid.innerHTML = '<div class="col-span-4 text-center text-gray-400 py-10">No products found.</div>';
            return;
        }
        
        grid.innerHTML = products.map(product => renderProductCard(product)).join('');
        
        // Add event listeners to action buttons
        addProductActionListeners();
    }
    
    // Render a single product card
    function renderProductCard(product) {
        const stock = parseInt(product.stock_count || 0);
        const status = getStockStatus(stock);
        const isArchived = product.is_archived && product.is_archived != '0';
        
        let imgSrc = '../images/user/tile1.jpg';
        if (product.product_image && product.product_image.startsWith('data:image')) {
            imgSrc = product.product_image;
        }
        
        // Generate subtitle based on product type
        let subtitle = '';
        if (product.product_type === 'tile') {
            const parts = [];
            if (Array.isArray(product.tile_designs) && product.tile_designs.length) {
                parts.push(product.tile_designs.map(des => 
                    `<span class='bg-green-100 text-green-700 rounded px-2 py-0.5 text-xs font-semibold mb-1'>${des}</span>`
                ).join(''));
            }
            if (Array.isArray(product.tile_classifications) && product.tile_classifications.length) {
                parts.push(product.tile_classifications.map(cls => 
                    `<span class='bg-yellow-100 text-yellow-700 rounded px-2 py-0.5 text-xs font-semibold mb-1'>${cls}</span>`
                ).join(''));
            }
            if (Array.isArray(product.tile_finishes) && product.tile_finishes.length) {
                parts.push(product.tile_finishes.map(fin => 
                    `<span class='bg-pink-100 text-pink-700 rounded px-2 py-0.5 text-xs font-semibold mb-1'>${fin}</span>`
                ).join(''));
            }
            if (Array.isArray(product.tile_sizes) && product.tile_sizes.length) {
                parts.push(product.tile_sizes.map(sz => 
                    `<span class='bg-purple-100 text-purple-700 rounded px-2 py-0.5 text-xs font-semibold mb-1'>${sz}</span>`
                ).join(''));
            }
            if (Array.isArray(product.best_for) && product.best_for.length) {
                parts.push(product.best_for.map(bf => 
                    `<span class='bg-orange-100 text-orange-700 rounded px-2 py-0.5 text-xs font-semibold mb-1'>${bf}</span>`
                ).join(''));
            }
            subtitle = `<div class='flex flex-wrap justify-center gap-1'>${parts.join('')}</div>`;
        } else if (product.product_type === 'other' && product.product_spec) {
            subtitle = `<div class='flex flex-wrap justify-center gap-1'><span class='bg-blue-100 text-blue-700 rounded px-2 py-0.5 text-xs font-semibold mb-1'>${product.product_spec}</span></div>`;
        }
        
        return `
        <div class="product-card ${isArchived ? 'opacity-60 grayscale pointer-events-auto border-2 border-gray-400 bg-gray-100' : 'bg-gradient-to-br from-blue-50 to-white border-blue-100'} rounded-2xl shadow-lg p-6 flex flex-col items-center relative hover:shadow-2xl">
            <div class="absolute top-3 right-3 z-20">
                <div class="${status.class} text-xs font-semibold px-3 py-1 rounded-full shadow-sm flex items-center gap-1">
                    <i class="fas ${status.icon}"></i> ${status.label}
                </div>
                ${isArchived ? '<span class="ml-2 px-2 py-1 bg-gray-400 text-white text-xs rounded shadow">ARCHIVED</span>' : ''}
            </div>
            <img src="${imgSrc}" alt="${product.product_name}" class="w-36 h-36 object-cover rounded-xl mb-4 border-4 ${isArchived ? 'border-gray-400' : 'border-blue-200'} shadow-md transition-transform duration-300 hover:scale-105">
            <div class="font-bold text-xl ${isArchived ? 'text-gray-500' : 'text-gray-900'} mb-1 text-center tracking-wide">${product.product_name}</div>
            <div class="text-sm ${isArchived ? 'text-gray-400' : 'text-blue-500'} mb-3 font-medium">${subtitle}</div>
            <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                <div class="bg-green-500 h-3 rounded-full transition-all duration-300" style="width: ${Math.min(100, stock/3)}%"></div>
            </div>
            <div class="text-xs text-gray-700 font-semibold px-2 py-1 rounded bg-white shadow-sm mb-2">INVENTORY: ${stock}</div>
            <div class="flex mt-4 space-x-3">
                <button class="edit-btn bg-blue-100 hover:bg-blue-200 text-blue-700 font-bold px-3 py-2 rounded-lg shadow transition flex items-center" title="Edit" data-id="${product.product_id}" ${isArchived ? 'disabled' : ''}>
                    <i class="fas fa-edit"></i>
                </button>
                ${isArchived
                    ? `<button class="unarchive-btn bg-green-100 hover:bg-orange-400 text-green-700 font-bold px-3 py-2 rounded-lg shadow transition flex items-center" title="Unarchive" data-id="${product.product_id}"><i class="fas fa-undo"></i> Unarchive</button>`
                    : `<button class="delete-btn bg-yellow-100 hover:bg-yellow-200 text-yellow-700 font-bold px-3 py-2 rounded-lg shadow transition flex items-center" title="Archive" data-id="${product.product_id}"><i class="fas fa-archive"></i> Archive</button>`
                }
            </div>
        </div>
        `;
    }
    
    // Get stock status information
    function getStockStatus(stock) {
        if (stock >= 300) return {label: 'Overstock', class: 'inventory-high', icon: 'fa-bolt'};
        if (stock >= 100) return {label: 'In Stock', class: 'inventory-high', icon: 'fa-check-circle'};
        if (stock < 10) return {label: 'No Stock', class: 'inventory-low', icon: 'fa-times-circle'};
        if (stock < 50) return {label: 'Low Stock', class: 'inventory-low', icon: 'fa-exclamation-circle'};
        return {label: 'Medium', class: 'inventory-medium', icon: 'fa-info-circle'};
    }
    
    // Add event listeners to product action buttons
    function addProductActionListeners() {
        // Edit button
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.dataset.id;
                const product = allProducts.find(p => p.product_id == productId);
                if (product) {
                    if (product.product_type === 'tile') {
                        showEditTileModal(product);
                    } else {
                        showEditOtherModal(product);
                    }
                }
            });
        });
        
        // Archive button
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.dataset.id;
                archiveProduct(productId, false);
            });
        });
        
        // Unarchive button
        document.querySelectorAll('.unarchive-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.dataset.id;
                archiveProduct(productId, true);
            });
        });
    }
    
    // Show edit modal for tile products
    function showEditTileModal(product) {
        const modalContainer = document.getElementById('editModalContainer');
        
        modalContainer.innerHTML = `
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 p-4">
                <div class="bg-white rounded-2xl shadow-2xl p-6 edit-modal-container relative border border-blue-200">
                    <button class="absolute top-3 right-3 text-gray-400 hover:text-blue-600 text-3xl font-bold transition close-edit-modal">&times;</button>
                    <h2 class="text-2xl font-extrabold mb-6 text-blue-700 flex items-center gap-2"><i class='fas fa-pen-to-square'></i> Edit Tile Product</h2>
                    <form id="editTileForm" class="edit-modal-grid gap-4">
                        <input type="hidden" name="product_id" value="${product.product_id}">
                        <input type="hidden" name="product_type" value="tile">
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Tile Name</label>
                                <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="product_name" value="${product.product_name || ''}" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Tile Description</label>
                                <textarea class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="product_description" rows="3" required>${product.product_description || ''}</textarea>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Tile Classification</label>
                                <select name="tile_classification" class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" required>
                                    <option value="">Select classification</option>
                                    <option value="ceramic" ${product.tile_classifications && product.tile_classifications.includes('ceramic') ? 'selected' : ''}>Ceramic</option>
                                    <option value="porcelain" ${product.tile_classifications && product.tile_classifications.includes('porcelain') ? 'selected' : ''}>Porcelain</option>
                                    <option value="granite" ${product.tile_classifications && product.tile_classifications.includes('granite') ? 'selected' : ''}>Granite</option>
                                    <option value="cement" ${product.tile_classifications && product.tile_classifications.includes('cement') ? 'selected' : ''}>Cement</option>
                                    <option value="glass tiles" ${product.tile_classifications && product.tile_classifications.includes('glass tiles') ? 'selected' : ''}>Glass Tiles</option>
                                    <option value="marble" ${product.tile_classifications && product.tile_classifications.includes('marble') ? 'selected' : ''}>Marble</option>
                                    <option value="stone" ${product.tile_classifications && product.tile_classifications.includes('stone') ? 'selected' : ''}>Stone</option>
                                    <option value="slate" ${product.tile_classifications && product.tile_classifications.includes('slate') ? 'selected' : ''}>Slate</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Best For</label>
                                <div class="multi-select-container">
                                    <div class="multi-select-item">
                                        <input type="checkbox" id="best_for_indoor" name="best_for[]" value="indoor" ${product.best_for && product.best_for.includes('indoor') ? 'checked' : ''}>
                                        <label for="best_for_indoor" class="ml-2">Indoor</label>
                                    </div>
                                    <div class="multi-select-item">
                                        <input type="checkbox" id="best_for_outdoor" name="best_for[]" value="outdoor" ${product.best_for && product.best_for.includes('outdoor') ? 'checked' : ''}>
                                        <label for="best_for_outdoor" class="ml-2">Outdoor</label>
                                    </div>
                                    <div class="multi-select-item">
                                        <input type="checkbox" id="best_for_swimming_pool" name="best_for[]" value="swimming pool" ${product.best_for && product.best_for.includes('swimming pool') ? 'checked' : ''}>
                                        <label for="best_for_swimming_pool" class="ml-2">Swimming Pool</label>
                                    </div>
                                    <div class="multi-select-item">
                                        <input type="checkbox" id="best_for_bathroom" name="best_for[]" value="bathroom" ${product.best_for && product.best_for.includes('bathroom') ? 'checked' : ''}>
                                        <label for="best_for_bathroom" class="ml-2">Bathroom</label>
                                    </div>
                                    <div class="multi-select-item">
                                        <input type="checkbox" id="best_for_kitchen_countertop" name="best_for[]" value="kitchen countertop" ${product.best_for && product.best_for.includes('kitchen countertop') ? 'checked' : ''}>
                                        <label for="best_for_kitchen_countertop" class="ml-2">Kitchen Countertop</label>
                                    </div>
                                    <div class="multi-select-item">
                                        <input type="checkbox" id="best_for_wall" name="best_for[]" value="wall" ${product.best_for && product.best_for.includes('wall') ? 'checked' : ''}>
                                        <label for="best_for_wall" class="ml-2">Wall</label>
                                    </div>
                                    <div class="multi-select-item">
                                        <input type="checkbox" id="best_for_corridor" name="best_for[]" value="corridor" ${product.best_for && product.best_for.includes('corridor') ? 'checked' : ''}>
                                        <label for="best_for_corridor" class="ml-2">Corridor</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Tile Design</label>
                                <div class="multi-select-container">
                                    <div class="multi-select-item">
                                        <input type="checkbox" id="design_minimalist" name="tile_design[]" value="minimalist" ${product.tile_designs && product.tile_designs.includes('minimalist') ? 'checked' : ''}>
                                        <label for="design_minimalist" class="ml-2">Minimalist</label>
                                    </div>
                                    <div class="multi-select-item">
                                        <input type="checkbox" id="design_floral" name="tile_design[]" value="floral" ${product.tile_designs && product.tile_designs.includes('floral') ? 'checked' : ''}>
                                        <label for="design_floral" class="ml-2">Floral</label>
                                    </div>
                                    <div class="multi-select-item">
                                        <input type="checkbox" id="design_black_and_white" name="tile_design[]" value="black and white" ${product.tile_designs && product.tile_designs.includes('black and white') ? 'checked' : ''}>
                                        <label for="design_black_and_white" class="ml-2">Black and White</label>
                                    </div>
                                    <div class="multi-select-item">
                                        <input type="checkbox" id="design_modern" name="tile_design[]" value="modern" ${product.tile_designs && product.tile_designs.includes('modern') ? 'checked' : ''}>
                                        <label for="design_modern" class="ml-2">Modern</label>
                                    </div>
                                    <div class="multi-select-item">
                                        <input type="checkbox" id="design_rustic" name="tile_design[]" value="rustic" ${product.tile_designs && product.tile_designs.includes('rustic') ? 'checked' : ''}>
                                        <label for="design_rustic" class="ml-2">Rustic</label>
                                    </div>
                                    <div class="multi-select-item">
                                        <input type="checkbox" id="design_geometric" name="tile_design[]" value="geometric" ${product.tile_designs && product.tile_designs.includes('geometric') ? 'checked' : ''}>
                                        <label for="design_geometric" class="ml-2">Geometric</label>
                                    </div>
                                    <div class="multi-select-item">
                                        <input type="checkbox" id="design_lines" name="tile_design[]" value="lines" ${product.tile_designs && product.tile_designs.includes('lines') ? 'checked' : ''}>
                                        <label for="design_lines" class="ml-2">Lines</label>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Tile Finish</label>
                                <select name="tile_finish" class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" required>
                                    <option value="">Select finish</option>
                                    <option value="rough" ${product.tile_finishes && product.tile_finishes.includes('rough') ? 'selected' : ''}>Rough</option>
                                    <option value="matte" ${product.tile_finishes && product.tile_finishes.includes('matte') ? 'selected' : ''}>Matte</option>
                                    <option value="glossy" ${product.tile_finishes && product.tile_finishes.includes('glossy') ? 'selected' : ''}>Glossy</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Tile Size</label>
                                <select name="tile_size" class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" required>
                                    <option value="">Select size</option>
                                    <option value="60x60" ${product.tile_sizes && product.tile_sizes.includes('60x60') ? 'selected' : ''}>60x60</option>
                                    <option value="30x60" ${product.tile_sizes && product.tile_sizes.includes('30x60') ? 'selected' : ''}>30x60</option>
                                    <option value="40x40" ${product.tile_sizes && product.tile_sizes.includes('40x40') ? 'selected' : ''}>40x40</option>
                                    <option value="30x30" ${product.tile_sizes && product.tile_sizes.includes('30x30') ? 'selected' : ''}>30x30</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Tile Price</label>
                                <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="product_price" type="number" min="0" step="0.01" value="${product.product_price || ''}" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Stock Count</label>
                                <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg" name="stock_count" type="number" min="0" value="${product.stock_count || ''}" required>
                            </div>
                        </div>
                        
                        <div class="md:col-span-2 flex gap-3 justify-end mt-6">
                            <button type="button" class="close-edit-modal px-5 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 font-medium transition">Cancel</button>
                            <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow transition">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        
        // Add event listeners
        const modal = modalContainer.querySelector('.fixed');
        modal.querySelector('.close-edit-modal').addEventListener('click', () => modal.remove());
        
        modal.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            // Validate required fields
            const requiredInputs = modal.querySelectorAll('[required]');
            let allFilled = true;
            requiredInputs.forEach(input => {
                if (!input.value) allFilled = false;
            });
            // Validate at least one checkbox for best_for and tile_design
            const bestForChecks = modal.querySelectorAll('input[name="best_for[]"]:checked');
            const tileDesignChecks = modal.querySelectorAll('input[name="tile_design[]"]:checked');
            if (bestForChecks.length === 0) {
                Swal.fire('Error', 'Please select at least one "Best For" option.', 'error');
                return;
            }
            if (tileDesignChecks.length === 0) {
                Swal.fire('Error', 'Please select at least one "Tile Design" option.', 'error');
                return;
            }
            if (!allFilled) {
                Swal.fire('Error', 'Please fill all required fields first', 'error');
                return;
            }
            const formData = new FormData(this);
            showLoadingOverlay();
            fetch('processes/edit_product.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Updated!', 'Product updated successfully.', 'success');
                    modal.remove();
                    loadProducts();
                } else {
                    Swal.fire('Error', data.message || 'Failed to update product.', 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Failed to update product.', 'error');
            })
            .finally(() => {
                hideLoadingOverlay();
            });
        });
    }
    
    // Show edit modal for other products
    function showEditOtherModal(product) {
        const modalContainer = document.getElementById('editModalContainer');
        
        modalContainer.innerHTML = `
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 p-4">
                <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-lg relative border border-blue-200">
                    <button class="absolute top-3 right-3 text-gray-400 hover:text-blue-600 text-3xl font-bold transition close-edit-modal">&times;</button>
                    <h2 class="text-2xl font-extrabold mb-6 text-blue-700 flex items-center gap-2"><i class='fas fa-pen-to-square'></i> Edit Product</h2>
                    <form id="editOtherForm" class="space-y-5">
                        <input type="hidden" name="product_id" value="${product.product_id}">
                        <input type="hidden" name="product_type" value="other">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Product Name</label>
                            <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition" name="product_name" value="${product.product_name || ''}" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Description</label>
                            <textarea class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition" name="product_description" rows="3" required>${product.product_description || ''}</textarea>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Product Specification</label>
                            <select name="product_spec" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" required>
                                <option value="">Select specification</option>
                                <option value="PVC Doors" ${product.product_spec === 'PVC Doors' ? 'selected' : ''}>PVC Doors</option>
                                <option value="Sinks" ${product.product_spec === 'Sinks' ? 'selected' : ''}>Sinks</option>
                                <option value="Tile Vinyl" ${product.product_spec === 'Tile Vinyl' ? 'selected' : ''}>Tile Vinyl</option>
                                <option value="Bowls" ${product.product_spec === 'Bowls' ? 'selected' : ''}>Bowls</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Price</label>
                                <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition" name="product_price" type="number" step="0.01" min="0" value="${product.product_price || ''}" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Stock</label>
                                <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition" name="stock_count" type="number" min="0" value="${product.stock_count || ''}" required>
                            </div>
                        </div>
                        <div class="flex gap-3 justify-end mt-6">
                            <button type="button" class="close-edit-modal px-5 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 font-medium transition">Cancel</button>
                            <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow transition">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        
        // Add event listeners
        const modal = modalContainer.querySelector('.fixed');
        modal.querySelector('.close-edit-modal').addEventListener('click', () => modal.remove());
        
        modal.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            showLoadingOverlay();
            
            fetch('processes/edit_product.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Updated!', 'Product updated successfully.', 'success');
                    modal.remove();
                    loadProducts();
                } else {
                    Swal.fire('Error', data.message || 'Failed to update product.', 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Failed to update product.', 'error');
            })
            .finally(() => {
                hideLoadingOverlay();
            });
        });
    }
    
    // Archive or unarchive a product
    function archiveProduct(productId, unarchive) {
        const action = unarchive ? 'unarchive' : 'archive';
        const actionText = unarchive ? 'Unarchive' : 'Archive';
        
        Swal.fire({
            title: `${actionText} Product?`,
            text: unarchive ? 
                'This product will be visible to users again.' : 
                'This product will be hidden from users but not deleted.',
            icon: unarchive ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonText: actionText,
            cancelButtonText: 'Cancel',
        }).then(result => {
            if (result.isConfirmed) {
                showLoadingOverlay();
                
                fetch('processes/archive_product.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ 
                        product_id: productId, 
                        unarchive: unarchive ? 1 : 0 
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire(`${actionText}ed!`, `Product ${actionText.toLowerCase()}ed successfully.`, 'success');
                        loadProducts();
                    } else {
                        Swal.fire('Error', data.message || `Failed to ${actionText.toLowerCase()} product.`, 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', `Failed to ${actionText.toLowerCase()} product.`, 'error');
                })
                .finally(() => {
                    hideLoadingOverlay();
                });
            }
        });
    }
    
    // Initialize filters
    function initializeFilters() {
        const categoryDropdown = document.getElementById('categoryDropdown');
        const searchInput = document.getElementById('searchInput');
        const tileTypeDropdown = document.getElementById('tileTypeDropdown');
        const otherSpecDropdown = document.getElementById('otherSpecDropdown');
        const stockStatusDropdown = document.getElementById('stockStatusDropdown');
        const showArchivedCheckbox = document.getElementById('showArchivedCheckbox');
        
        if (categoryDropdown) {
            categoryDropdown.addEventListener('change', function() {
                if (this.value === 'tile') {
                    tileTypeDropdown.classList.remove('hidden');
                    otherSpecDropdown.classList.add('hidden');
                } else if (this.value === 'other') {
                    tileTypeDropdown.classList.add('hidden');
                    otherSpecDropdown.classList.remove('hidden');
                } else {
                    tileTypeDropdown.classList.add('hidden');
                    otherSpecDropdown.classList.add('hidden');
                }
                filterProducts();
            });
        }
        
        if (searchInput) searchInput.addEventListener('input', filterProducts);
        if (tileTypeDropdown) tileTypeDropdown.addEventListener('change', filterProducts);
        if (otherSpecDropdown) otherSpecDropdown.addEventListener('change', filterProducts);
        if (stockStatusDropdown) stockStatusDropdown.addEventListener('change', filterProducts);
        if (showArchivedCheckbox) showArchivedCheckbox.addEventListener('change', filterProducts);
    }
    
    // Initialize low stock metric click
    function initializeLowStockMetric() {
        const lowStockMetricCard = document.getElementById('lowStockMetricCard');
        if (lowStockMetricCard) {
            lowStockMetricCard.addEventListener('click', function() {
                document.getElementById('stockStatusDropdown').value = 'low';
                filterProducts();
            });
        }
    }
    
    // Show loading overlay
    function showLoadingOverlay() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) overlay.style.display = 'block';
    }
    
    // Hide loading overlay
    function hideLoadingOverlay() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) overlay.style.display = 'none';
    }
    </script>
</body>
</html>