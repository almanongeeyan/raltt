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
                                <option value="">All Tile Categories</option>
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
                <!-- Add Product Sidebar Drawer -->
                <div id="addProductSidebar" class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col" style="max-width: 420px;">
                    <div class="flex items-center px-6 py-4 border-b">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-plus-circle mr-2 text-blue-600"></i>Add New Product</h2>
                    </div>
                    <div class="flex gap-2 px-6 pt-4">
                        <button id="btnForTiles" type="button" class="flex-1 px-4 py-2 rounded-lg font-semibold border border-blue-600 text-blue-600 bg-white hover:bg-blue-50 focus:bg-blue-100 focus:outline-none">For Tiles</button>
                        <button id="btnForOthers" type="button" class="flex-1 px-4 py-2 rounded-lg font-semibold border border-gray-400 text-gray-700 bg-white hover:bg-gray-100 focus:bg-gray-200 focus:outline-none">For Others</button>
                    </div>
                    <?php
                    // Fetch tile categories for dropdown
                    require_once '../connection/connection.php';
                    $cat_stmt = $db_connection->query("SELECT category_id, category_name FROM tile_categories ORDER BY category_name ASC");
                    $tile_categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <form id="formForTiles" class="flex-1 flex flex-col gap-4 px-6 py-6 overflow-y-auto" action="processes/store_product.php" method="POST" enctype="multipart/form-data">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Tile Image</label>
                            <input type="file" name="product_image" accept="image/*" required class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Tile Name</label>
                            <input type="text" name="product_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" placeholder="Enter tile name" required />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1 flex items-center">Tile Description
                                <button type="button" id="generateDescriptionBtn" class="ml-2 px-3 py-1 rounded bg-gradient-to-r from-blue-500 to-blue-700 text-white text-xs font-semibold hover:from-blue-600 hover:to-blue-800 flex items-center gap-2 relative" title="Generate description">
                                    <span class="generate-btn-text">Generate</span>
                                    <svg id="generateSpinner" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                                </button>
                        <span class="ml-2 text-[10px] text-black">(If failed, just press it again.)</span>
                            </label>
                            <textarea id="tileDescription" name="product_description" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" placeholder="Enter tile description" required disabled></textarea>
                            <span id="generateFailNote" class="hidden text-red-600 text-xs mt-1">Error generating description. Please try again.</span>
                        </div>
<script>
// --- Description Generate Button Logic ---
document.addEventListener('DOMContentLoaded', function() {
    const descTextarea = document.getElementById('tileDescription');
    const generateBtn = document.getElementById('generateDescriptionBtn');
    const nameInput = document.querySelector('input[name="product_name"]');
    const priceInput = document.querySelector('input[name="product_price"]');
    const designInput = document.querySelector('input[name="tile_design"]');
    const stockInput = document.querySelector('input[name="stock_count"]');
    const catSelects = () => Array.from(document.querySelectorAll('#tileCategoryFields select'));

    function allFilled() {
        if (!nameInput.value.trim() || !priceInput.value.trim() || !designInput.value.trim() || !stockInput.value.trim()) return false;
        // At least one category selected and all selects have value
        const selects = catSelects();
        if (!selects.length || selects.some(s => !s.value)) return false;
        return true;
    }



    function updateDescState() {
        const shouldEnable = allFilled() || descTextarea.value.trim();
        descTextarea.disabled = !shouldEnable;
        generateBtn.disabled = !allFilled();
        if (!shouldEnable) {
            descTextarea.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed', 'border-dashed');
            generateBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            descTextarea.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed', 'border-dashed');
            generateBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    [nameInput, priceInput, designInput, stockInput].forEach(inp => inp.addEventListener('input', updateDescState));
    document.getElementById('tileCategoryFields').addEventListener('change', updateDescState);
    document.getElementById('tileCategoryFields').addEventListener('click', function(e) {
        if (e.target.closest('.add-category-btn') || e.target.closest('.remove-category-btn')) {
            setTimeout(updateDescState, 100); // Wait for DOM update
        }
    });


    // Loading overlay
    let loadingOverlay = document.getElementById('descLoadingOverlay');
    if (!loadingOverlay) {
        loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'descLoadingOverlay';
        loadingOverlay.style.position = 'fixed';
        loadingOverlay.style.top = 0;
        loadingOverlay.style.left = 0;
        loadingOverlay.style.width = '100vw';
        loadingOverlay.style.height = '100vh';
        loadingOverlay.style.background = 'rgba(0,0,0,0.2)';
        loadingOverlay.style.display = 'none';
        loadingOverlay.style.zIndex = 9999;
        loadingOverlay.innerHTML = '<div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);"><svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><div class="mt-4 text-blue-700 font-semibold text-center">Generating description...</div></div>';
        document.body.appendChild(loadingOverlay);
    }

    generateBtn.addEventListener('click', function() {
        if (!allFilled()) return;
        const name = nameInput.value.trim();
        const price = priceInput.value.trim();
        const design = designInput.value.trim();
        const stock = stockInput.value.trim();
        const categories = catSelects().map(s => s.options[s.selectedIndex].text).filter(Boolean);
        descTextarea.value = '';
        // Show spinner and overlay
        document.getElementById('generateSpinner').classList.remove('hidden');
        generateBtn.disabled = true;
        loadingOverlay.style.display = 'block';
        fetch('processes/generate_description.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, price, design, stock, categories })
        })
        .then(res => res.json())
        .then(data => {
            const failNote = document.getElementById('generateFailNote');
            if (data.description) {
                descTextarea.value = data.description;
                failNote.classList.add('hidden');
            } else {
                descTextarea.value = 'Failed to generate description.';
                failNote.classList.remove('hidden');
            }
        })
        .catch(() => {
            descTextarea.value = 'Error generating description.';
            document.getElementById('generateFailNote').classList.remove('hidden');
        })
        .finally(() => {
            document.getElementById('generateSpinner').classList.add('hidden');
            generateBtn.disabled = false;
            loadingOverlay.style.display = 'none';
        });
    });

    updateDescState();
});
</script>
                        <div id="tileCategoryFields">
                            <label class="block text-gray-700 font-medium mb-1">Tile Categories</label>
                            <div class="flex gap-2 mb-2 tile-category-row">
                                <select name="category_ids[]" class="tile-category-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" required>
                                    <option value="">Select category</option>
                                    <?php foreach ($tile_categories as $cat): ?>
                                        <option value="<?= htmlspecialchars($cat['category_id']) ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="add-category-btn bg-blue-500 text-white px-3 py-1 rounded" title="Add another category"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
<script>
// Dynamic add tile category select fields, prevent duplicate selection
// Limit for tile category dropdowns
const MAX_TILE_CATEGORIES = 6;

document.addEventListener('DOMContentLoaded', function() {
    const tileCategoryFields = document.getElementById('tileCategoryFields');

    function canAddMoreTileCategories() {
        const selects = tileCategoryFields.querySelectorAll('select');
        return selects.length < MAX_TILE_CATEGORIES;
    }

    function updateCategoryOptions() {
        // Get all selected values
        const selects = tileCategoryFields.querySelectorAll('select');
        const selected = Array.from(selects).map(s => s.value).filter(v => v);
        selects.forEach(select => {
            const current = select.value;
            Array.from(select.options).forEach(opt => {
                if (!opt.value) return; // skip placeholder
                // Hide option if selected elsewhere and not current
                if (selected.includes(opt.value) && opt.value !== current) {
                    opt.style.display = 'none';
                } else {
                    opt.style.display = '';
                }
            });
        });
    }

    tileCategoryFields.addEventListener('change', function(e) {
        if (e.target.matches('select')) {
            updateCategoryOptions();
        }
    });

    tileCategoryFields.addEventListener('click', function(e) {
        if (e.target.closest('.add-category-btn')) {
            if (!canAddMoreTileCategories()) return;
            const row = e.target.closest('.tile-category-row');
            // Clone the row
            const newRow = row.cloneNode(true);
            // Reset the select value
            newRow.querySelector('select').value = '';
            // Change plus to minus for remove
            const btn = newRow.querySelector('.add-category-btn');
            btn.innerHTML = '<i class="fas fa-minus"></i>';
            btn.classList.remove('bg-blue-500');
            btn.classList.add('bg-red-500', 'remove-category-btn');
            btn.classList.remove('add-category-btn');
            btn.title = 'Remove this category';
            // Insert after current row
            tileCategoryFields.appendChild(newRow);
            updateCategoryOptions();
            // Hide plus button if max reached
            if (!canAddMoreTileCategories()) {
                const plusBtns = tileCategoryFields.querySelectorAll('.add-category-btn');
                plusBtns.forEach(btn => btn.style.display = 'none');
            }
        } else if (e.target.closest('.remove-category-btn')) {
            const row = e.target.closest('.tile-category-row');
            row.remove();
            updateCategoryOptions();
            // Show plus button if below max
            if (canAddMoreTileCategories()) {
                const plusBtns = tileCategoryFields.querySelectorAll('.add-category-btn');
                plusBtns.forEach(btn => btn.style.display = '');
            }
        }
    });

    // Initial call
    updateCategoryOptions();
});
</script>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Tile Price</label>
                            <input type="number" name="product_price" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" placeholder="Enter price" required />
                        </div>
                        <!-- Tile Type removed as per request -->
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Tile Design</label>
                            <input type="text" name="tile_design" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" placeholder="e.g. Floral Blue" required />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Stock Count</label>
                            <input type="number" name="stock_count" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" placeholder="Enter stock count" required />
                        </div>
                        <div>
                            <label class="inline-flex items-center mt-2">
                                <input type="checkbox" name="is_archived" value="1" class="form-checkbox h-5 w-5 text-blue-600">
                                <span class="ml-2 text-gray-700">Archive this product (hidden from users)</span>
                            </label>
                        </div>
                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" id="cancelAddProductSidebar" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 font-medium">Cancel</button>
                            <input type="hidden" name="product_type" value="tile" />
                            <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow">Add Product</button>
                        </div>
                    </form>
                    <form id="formForOthers" class="flex-1 flex-col gap-4 px-6 py-6 overflow-y-auto hidden" action="processes/store_product.php" method="POST" enctype="multipart/form-data">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Product Image</label>
                            <input type="file" name="product_image" accept="image/*" required class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Product Specification</label>
                            <select name="product_spec" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" required>
                                <option value="">Select specification</option>
                                <option value="PVC Doors">PVC Doors</option>
                                <option value="Sinks">Sinks</option>
                                <option value="Tile Vinyl">Tile Vinyl</option>
                                <option value="Bowls">Bowls</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Product Name</label>
                            <input type="text" name="product_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" placeholder="Enter product name" required />
                        </div>
                        
                        
                        <div>
                            <label class="block text-gray-700 font-medium mb-1 flex items-center">Product Description
                                <button type="button" id="generateOtherDescriptionBtn" class="ml-2 px-3 py-1 rounded bg-gradient-to-r from-blue-500 to-blue-700 text-white text-xs font-semibold hover:from-blue-600 hover:to-blue-800 flex items-center gap-2 relative" title="Generate description">
                                    <span class="generate-btn-text">Generate</span>
                                    <svg id="generateOtherSpinner" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                                </button>
                                <span class="ml-2 text-[10px] text-black">(If failed, just press it again.)</span>
                            </label>
                            <textarea id="otherProductDescription" name="product_description" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" placeholder="Enter product description" required disabled></textarea>
                            <span id="generateOtherFailNote" class="hidden text-red-600 text-xs mt-1">Error generating description. Please try again.</span>
                        </div>
<script>
// --- Other Product Description Generate Button Logic ---
document.addEventListener('DOMContentLoaded', function() {
    const descTextarea = document.getElementById('otherProductDescription');
    const generateBtn = document.getElementById('generateOtherDescriptionBtn');
    const nameInput = document.querySelector('#formForOthers input[name="product_name"]');
    const priceInput = document.querySelector('#formForOthers input[name="product_price"]');
    const specSelect = document.querySelector('#formForOthers select[name="product_spec"]');

    function allFilled() {
        return nameInput.value.trim() && priceInput.value.trim() && specSelect.value.trim();
    }

    function updateDescState() {
        // Enable textarea if all fields are filled or if it already has a value (for editing after generation)
        const shouldEnable = allFilled() || descTextarea.value.trim();
        descTextarea.disabled = !shouldEnable;
        generateBtn.disabled = !allFilled();
        if (!shouldEnable) {
            descTextarea.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed', 'border-dashed');
            generateBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            descTextarea.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed', 'border-dashed');
            generateBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    [nameInput, priceInput, specSelect].forEach(inp => inp.addEventListener('input', updateDescState));
    specSelect.addEventListener('change', updateDescState);

    // Loading overlay for other products
    let loadingOverlay = document.getElementById('descOtherLoadingOverlay');
    if (!loadingOverlay) {
        loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'descOtherLoadingOverlay';
        loadingOverlay.style.position = 'fixed';
        loadingOverlay.style.top = 0;
        loadingOverlay.style.left = 0;
        loadingOverlay.style.width = '100vw';
        loadingOverlay.style.height = '100vh';
        loadingOverlay.style.background = 'rgba(0,0,0,0.2)';
        loadingOverlay.style.display = 'none';
        loadingOverlay.style.zIndex = 9999;
        loadingOverlay.innerHTML = '<div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);"><svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><div class="mt-4 text-blue-700 font-semibold text-center">Generating description...</div></div>';
        document.body.appendChild(loadingOverlay);
    }


    generateBtn.addEventListener('click', function() {
        if (!allFilled()) return;
        const name = nameInput.value.trim();
        const price = priceInput.value.trim();
        const spec = specSelect.value.trim();
        descTextarea.value = '';
        // Show spinner and overlay
        document.getElementById('generateOtherSpinner').classList.remove('hidden');
        generateBtn.disabled = true;
        loadingOverlay.style.display = 'block';
        fetch('processes/generate_otherproduct_description.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, price, spec })
        })
        .then(res => res.json())
        .then(data => {
            const failNote = document.getElementById('generateOtherFailNote');
            if (data.description) {
                descTextarea.value = data.description;
                descTextarea.disabled = false;
                descTextarea.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed', 'border-dashed');
                failNote.classList.add('hidden');
            } else {
                let debugMsg = '';
                if (data && data.debug) {
                    debugMsg = '\n[DEBUG: ' + JSON.stringify(data.debug) + ']';
                } else if (data && data.details) {
                    debugMsg = '\n[DETAILS: ' + JSON.stringify(data.details) + ']';
                }
                descTextarea.value = 'Failed to generate description.' + debugMsg;
                descTextarea.disabled = false;
                failNote.classList.remove('hidden');
            }
        })
        .catch((err) => {
            descTextarea.value = 'Error generating description.' + (err ? ('\n' + err) : '');
            descTextarea.disabled = false;
            document.getElementById('generateOtherFailNote').classList.remove('hidden');
        })
        .finally(() => {
            document.getElementById('generateOtherSpinner').classList.add('hidden');
            generateBtn.disabled = false;
            loadingOverlay.style.display = 'none';
        });
    });

    updateDescState();
});
</script>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Product Price</label>
                            <input type="number" name="product_price" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" placeholder="Enter price" required />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Stock Count</label>
                            <input type="number" name="stock_count" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" placeholder="Enter stock count" required />
                        </div>
                        <div>
                            <label class="inline-flex items-center mt-2">
                                <input type="checkbox" name="is_archived" value="1" class="form-checkbox h-5 w-5 text-blue-600">
                                <span class="ml-2 text-gray-700">Archive this product (hidden from users)</span>
                            </label>
                        </div>
                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" id="cancelAddProductSidebar2" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 font-medium">Cancel</button>
                            <input type="hidden" name="product_type" value="other" />
                            <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow">Add Product</button>
                        </div>
                    </form>
                </div>
                        </div>
                    </div>
                </div>

                <!-- Product Grid (Dynamic) -->
                <div id="productGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8"></div>

                <!-- Pagination -->
                <div class="flex justify-center mt-10">
                    <nav class="inline-flex rounded-md shadow-sm">
                        <a href="#" class="py-2 px-3 ml-0 leading-tight text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700">
                            <i class="fas fa-chevron-left"></i>
            </div>
        </main>
    </div>
</body>
<script>
// Handle Add Product form submission with AJAX and SweetAlert2
function handleProductForm(formId, productType) {
    const form = document.getElementById(formId);
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        formData.set('product_type', productType);
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
                // Reset tile category dropdowns to only one
                const tileCategoryFields = document.getElementById('tileCategoryFields');
                if (tileCategoryFields) {
                    const rows = tileCategoryFields.querySelectorAll('.tile-category-row');
                    rows.forEach((row, idx) => { if (idx > 0) row.remove(); });
                    // Also reset the first select
                    const firstSelect = tileCategoryFields.querySelector('select');
                    if (firstSelect) firstSelect.value = '';
                }
                document.getElementById('addProductSidebar').classList.add('translate-x-full');
                loadProducts(); // Refresh product grid
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
        });
    });
}
handleProductForm('formForTiles', 'tile');
handleProductForm('formForOthers', 'other');
// Toggle between For Tiles and For Others forms
const btnForTiles = document.getElementById('btnForTiles');
const btnForOthers = document.getElementById('btnForOthers');
const formForTiles = document.getElementById('formForTiles');
const formForOthers = document.getElementById('formForOthers');

btnForTiles.addEventListener('click', function() {
    btnForTiles.classList.add('border-blue-600', 'text-blue-600', 'bg-white');
    btnForOthers.classList.remove('border-blue-600', 'text-blue-600', 'bg-white');
    btnForOthers.classList.add('border-gray-400', 'text-gray-700', 'bg-white');
    formForTiles.classList.remove('hidden');
    formForOthers.classList.add('hidden');
});
btnForOthers.addEventListener('click', function() {
    btnForOthers.classList.add('border-blue-600', 'text-blue-600', 'bg-white');
    btnForTiles.classList.remove('border-blue-600', 'text-blue-600', 'bg-white');
    btnForTiles.classList.add('border-gray-400', 'text-gray-700', 'bg-white');
    formForOthers.classList.remove('hidden');
    formForTiles.classList.add('hidden');
});
// Cancel buttons close sidebar (if you have JS for sidebar, hook here)
document.getElementById('cancelAddProductSidebar').onclick = function() {
    document.getElementById('addProductSidebar').classList.add('translate-x-full');
};
document.getElementById('cancelAddProductSidebar2').onclick = function() {
    closeSidebar();
};
// Sidebar open/close logic
const sidebar = document.getElementById('addProductSidebar');
const openBtn = document.getElementById('openAddProductSidebar');
const cancelBtn = document.getElementById('cancelAddProductSidebar');

function openSidebar() {
    sidebar.classList.remove('translate-x-full');
    sidebar.classList.add('translate-x-0');
    document.body.classList.add('overflow-hidden');
}
function closeSidebar() {
    sidebar.classList.add('translate-x-full');
    sidebar.classList.remove('translate-x-0');
    document.body.classList.remove('overflow-hidden');
}
openBtn.addEventListener('click', openSidebar);
cancelBtn.addEventListener('click', closeSidebar);

// --- Dynamic Product Grid & Filters ---
// Populate tileTypeDropdown and otherSpecDropdown dynamically
async function populateFilterDropdowns(products) {
    // Tile categories
    const tileTypeDropdown = document.getElementById('tileTypeDropdown');
    const allTileCats = new Set();
    products.forEach(p => {
        if (p.product_type === 'tile' && Array.isArray(p.tile_categories)) {
            p.tile_categories.forEach(cat => allTileCats.add(cat));
        }
    });
    tileTypeDropdown.innerHTML = '<option value="">All Tile Categories</option>' +
        Array.from(allTileCats).map(cat => `<option value="${cat}">${cat}</option>`).join('');

    // Product specs
    const otherSpecDropdown = document.getElementById('otherSpecDropdown');
    const allSpecs = new Set();
    products.forEach(p => {
        if (p.product_type === 'other' && p.product_spec) {
            allSpecs.add(p.product_spec);
        }
    });
    otherSpecDropdown.innerHTML = '<option value="">All Product Specs</option>' +
        Array.from(allSpecs).map(spec => `<option value="${spec}">${spec}</option>`).join('');
}
function getStockStatus(stock) {
    if (stock >= 300) return {label: 'Overstock', class: 'inventory-high', icon: 'fa-bolt'};
    if (stock >= 100) return {label: 'In Stock', class: 'inventory-high', icon: 'fa-check-circle'};
    if (stock < 10) return {label: 'No Stock', class: 'inventory-low', icon: 'fa-times-circle'};
    if (stock < 50) return {label: 'Low Stock', class: 'inventory-low', icon: 'fa-exclamation-circle'};
    return {label: 'Medium', class: 'inventory-medium', icon: 'fa-info-circle'};
}

function renderProductCard(product) {
    const stock = parseInt(product.stock_count || 0);
    const status = getStockStatus(stock);
    let imgSrc = '../images/user/tile1.jpg';
    if (product.product_image && product.product_image.startsWith('data:image')) {
        imgSrc = product.product_image;
    }
    // Unified subtitle: always use styled labels, wrap in flex for better alignment
    let subtitle = '';
    if (product.product_type === 'tile' && Array.isArray(product.tile_categories) && product.tile_categories.length) {
        subtitle = `<div class='flex flex-wrap justify-center gap-1'>` + product.tile_categories.map(cat => `<span class='bg-blue-100 text-blue-700 rounded px-2 py-0.5 text-xs font-semibold mb-1'>${cat}</span>`).join('') + `</div>`;
    } else if (product.product_type === 'other' && product.product_spec) {
        subtitle = `<div class='flex flex-wrap justify-center gap-1'><span class='bg-blue-100 text-blue-700 rounded px-2 py-0.5 text-xs font-semibold mb-1'>${product.product_spec}</span></div>`;
    }
    const isArchived = product.is_archived && product.is_archived != '0';
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

let allProducts = [];


function filterProducts() {
    const search = document.getElementById('searchInput').value.trim().toLowerCase();
    const category = document.getElementById('categoryDropdown').value;
    const tileType = document.getElementById('tileTypeDropdown').value;
    const otherSpec = document.getElementById('otherSpecDropdown').value;
    const stockStatus = document.getElementById('stockStatusDropdown').value;

    const showArchived = document.getElementById('showArchivedCheckbox').checked;
    let filtered = allProducts.filter(product => {
        // Only show archived if checked, and only for current branch
        if (!showArchived && product.is_archived && product.is_archived != '0') return false;

        // Search (matches any field)
        if (search) {
            const searchMatch = [
                product.product_name,
                product.product_spec
            ].filter(Boolean).some(val => val.toLowerCase().includes(search));
            // Also search in tile categories
            if (!searchMatch && product.product_type === 'tile' && Array.isArray(product.tile_categories)) {
                if (!product.tile_categories.some(cat => cat.toLowerCase().includes(search))) return false;
            } else if (!searchMatch) {
                return false;
            }
        }

        // Category (exact match)
        if (category !== 'all' && product.product_type !== category) return false;

        // Tile category filter (from DB)
        if (category === 'tile' && tileType) {
            if (!Array.isArray(product.tile_categories) || !product.tile_categories.includes(tileType)) return false;
        }

        // Other spec (exact match, only if visible)
        if (category === 'other' && otherSpec && product.product_spec !== otherSpec) return false;

        // Stock status (accurate logic)
        const stock = parseInt(product.stock_count || 0);
        if (stockStatus === 'in' && !(stock >= 100 && stock < 300)) return false;
        if (stockStatus === 'over' && stock < 300) return false;
        if (stockStatus === 'low' && !(stock < 50 && stock >= 10)) return false;
        if (stockStatus === 'none' && stock >= 10) return false;

        return true;
    });

    // Update metrics
    // Only count unarchived products for total
    document.getElementById('totalProductsMetric').textContent = filtered.filter(p => !p.is_archived || p.is_archived == '0').length;
    document.getElementById('totalInventoryMetric').textContent = filtered.filter(p => !p.is_archived || p.is_archived == '0').reduce((sum, p) => sum + (parseInt(p.stock_count || 0)), 0);
    document.getElementById('lowStockMetric').textContent = filtered.filter(p => {
        const stock = parseInt(p.stock_count || 0);
        return (!p.is_archived || p.is_archived == '0') && stock < 50;
    }).length;
// Make Low to No Stocks metric clickable to filter dropdowns
    document.getElementById('totalArchivedMetric').textContent = filtered.filter(p => p.is_archived && p.is_archived != '0').length;

    const grid = document.getElementById('productGrid');
    if (!filtered.length) {
        grid.innerHTML = '<div class="col-span-4 text-center text-gray-400 py-10">No products found.</div>';
        return;
    }
    grid.innerHTML = filtered.map(renderProductCard).join('');
}

function loadProducts() {
    const showArchived = document.getElementById('showArchivedCheckbox')?.checked ? 1 : 0;
    fetch('processes/get_products.php?show_archived=' + showArchived)
        .then(res => res.json())
        .then(products => {
            allProducts = products;
            populateFilterDropdowns(products);
            filterProducts();
        })
        .catch(() => {
            document.getElementById('productGrid').innerHTML = '<div class="col-span-4 text-center text-red-400 py-10">Failed to load products.</div>';
        });
}

// Initial load
loadProducts();

// --- Edit & Delete Button Logic ---
// Modal for editing (simple version)
let editModal = null;

async function showEditModal(product) {
    if (editModal) editModal.remove();
    // Fetch all tile categories and product's categories
    const [allCats, prodCats] = await Promise.all([
        fetch('processes/get_tile_categories.php').then(r=>r.json()),
        fetch('processes/get_product_categories.php?product_id=' + product.product_id).then(r=>r.json())
    ]);
    editModal = document.createElement('div');
    editModal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40';
    // Build tile category fields
    let catFields = '';
    if (product.product_type === 'tile') {
        catFields += `<div id="editTileCategoryFields">`;
        prodCats.forEach((catId, idx) => {
            catFields += `<div class="flex gap-2 mb-2 tile-category-row">
                <select name="category_ids[]" class="tile-category-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" required>
                    <option value="">Select category</option>
                    ${allCats.map(cat => `<option value="${cat.category_id}"${cat.category_id==catId?' selected':''}>${cat.category_name}</option>`).join('')}
                </select>
                <button type="button" class="${idx===0?'add-category-btn bg-blue-500':'remove-category-btn bg-red-500'} text-white px-3 py-1 rounded" title="${idx===0?'Add another category':'Remove this category'}"><i class="fas fa-${idx===0?'plus':'minus'}"></i></button>
            </div>`;
        });
        if (prodCats.length === 0) {
            catFields += `<div class="flex gap-2 mb-2 tile-category-row">
                <select name="category_ids[]" class="tile-category-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" required>
                    <option value="">Select category</option>
                    ${allCats.map(cat => `<option value="${cat.category_id}">${cat.category_name}</option>`).join('')}
                </select>
                <button type="button" class="add-category-btn bg-blue-500 text-white px-3 py-1 rounded" title="Add another category"><i class="fas fa-plus"></i></button>
            </div>`;
        }
        catFields += `</div>`;
    } else if (product.product_type === 'other') {
        // Editable product_spec dropdown for 'other' products
        catFields += `<div>
            <label class='block text-gray-700 font-semibold mb-2'>Product Specification</label>
            <select name='product_spec' class='w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none' required>
                <option value=''>Select specification</option>
                <option value='PVC Doors' ${product.product_spec==='PVC Doors'?'selected':''}>PVC Doors</option>
                <option value='Sinks' ${product.product_spec==='Sinks'?'selected':''}>Sinks</option>
                <option value='Tile Vinyl' ${product.product_spec==='Tile Vinyl'?'selected':''}>Tile Vinyl</option>
                <option value='Bowls' ${product.product_spec==='Bowls'?'selected':''}>Bowls</option>
            </select>
        </div>`;
    }
    editModal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg relative border border-blue-200">
            <button class="absolute top-3 right-3 text-gray-400 hover:text-blue-600 text-3xl font-bold transition" onclick="this.closest('.fixed').remove()">&times;</button>
            <h2 class="text-2xl font-extrabold mb-6 text-blue-700 flex items-center gap-2"><i class='fas fa-pen-to-square'></i> Edit Product</h2>
            <form id="editProductForm" class="space-y-5">
                <input type="hidden" name="product_id" value="${product.product_id}">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Product Name</label>
                    <input class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition" name="product_name" value="${product.product_name || ''}" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Description</label>
                    <textarea class="w-full px-4 py-2 border-2 border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition" name="product_description" rows="3" required>${product.product_description || ''}</textarea>
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
                ${catFields}
                <div class="flex gap-3 justify-end mt-6">
                    <button type="button" class="closeEditModal px-5 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 font-medium transition" onclick="this.closest('.fixed').remove()">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow transition">Save Changes</button>
                </div>
            </form>
        </div>
    `;
    document.body.appendChild(editModal);
    // Add dynamic category logic
    if (product.product_type === 'tile') {
        const catFieldsDiv = editModal.querySelector('#editTileCategoryFields');
        const MAX_TILE_CATEGORIES = 6;
        function canAddMoreEditTileCategories() {
            const selects = catFieldsDiv.querySelectorAll('select');
            return selects.length < MAX_TILE_CATEGORIES;
        }
        catFieldsDiv.addEventListener('click', function(e) {
            if (e.target.closest('.add-category-btn')) {
                if (!canAddMoreEditTileCategories()) return;
                const row = e.target.closest('.tile-category-row');
                const newRow = row.cloneNode(true);
                newRow.querySelector('select').value = '';
                const btn = newRow.querySelector('.add-category-btn');
                btn.innerHTML = '<i class="fas fa-minus"></i>';
                btn.classList.remove('bg-blue-500');
                btn.classList.add('bg-red-500', 'remove-category-btn');
                btn.classList.remove('add-category-btn');
                btn.title = 'Remove this category';
                catFieldsDiv.appendChild(newRow);
                updateEditCategoryOptions();
                // Hide plus button if max reached
                if (!canAddMoreEditTileCategories()) {
                    const plusBtns = catFieldsDiv.querySelectorAll('.add-category-btn');
                    plusBtns.forEach(btn => btn.style.display = 'none');
                }
            } else if (e.target.closest('.remove-category-btn')) {
                const row = e.target.closest('.tile-category-row');
                row.remove();
                updateEditCategoryOptions();
                // Show plus button if below max
                if (canAddMoreEditTileCategories()) {
                    const plusBtns = catFieldsDiv.querySelectorAll('.add-category-btn');
                    plusBtns.forEach(btn => btn.style.display = '');
                }
            }
        });
        catFieldsDiv.addEventListener('change', function(e) {
            if (e.target.matches('select')) updateEditCategoryOptions();
        });
        function updateEditCategoryOptions() {
            const selects = catFieldsDiv.querySelectorAll('select');
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
        updateEditCategoryOptions();
    }
    document.getElementById('editProductForm').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        // Add category_ids[] for tile
        if (product.product_type === 'tile') {
            const catIds = Array.from(editModal.querySelectorAll('#editTileCategoryFields select')).map(s => s.value).filter(v => v);
            formData.delete('category_ids[]');
            catIds.forEach(id => formData.append('category_ids[]', id));
        }
        fetch('processes/edit_product.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire('Updated!', 'Product updated.', 'success');
                loadProducts();
                editModal.remove();
            } else {
                Swal.fire('Error', data.message || 'Failed to update.', 'error');
            }
        })
        .catch(() => Swal.fire('Error', 'Failed to update.', 'error'));
    };
}

document.addEventListener('click', function(e) {
    // Edit button
    if (e.target.closest('.edit-btn')) {
        const id = e.target.closest('.edit-btn').dataset.id;
        const product = allProducts.find(p => p.product_id == id);
        if (product) showEditModal(product);
    }
    // Archive button
    if (e.target.closest('.delete-btn')) {
        const id = e.target.closest('.delete-btn').dataset.id;
        Swal.fire({
            title: 'Archive Product?',
            text: 'This product will be hidden from users but not deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Archive',
            cancelButtonText: 'Cancel',
        }).then(result => {
            if (result.isConfirmed) {
                fetch('processes/archive_product.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ product_id: id, unarchive: 0 })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Archived!', 'Product archived.', 'success');
                        loadProducts();
                    } else {
                        Swal.fire('Error', data.message || 'Failed to archive.', 'error');
                    }
                })
                .catch(() => Swal.fire('Error', 'Failed to archive.', 'error'));
            }
        });
    }
    // Unarchive button
    if (e.target.closest('.unarchive-btn')) {
        const id = e.target.closest('.unarchive-btn').dataset.id;
        Swal.fire({
            title: 'Unarchive Product?',
            text: 'This product will be visible to users again.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Unarchive',
            cancelButtonText: 'Cancel',
        }).then(result => {
            if (result.isConfirmed) {
                fetch('processes/archive_product.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ product_id: id, unarchive: 1 })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Unarchived!', 'Product is now active.', 'success');
                        loadProducts();
                    } else {
                        Swal.fire('Error', data.message || 'Failed to unarchive.', 'error');
                    }
                })
                .catch(() => Swal.fire('Error', 'Failed to unarchive.', 'error'));
            }
        });
    }
});

// --- Filter UI logic ---
const categoryDropdown = document.getElementById('categoryDropdown');
const tileTypeDropdown = document.getElementById('tileTypeDropdown');
const otherSpecDropdown = document.getElementById('otherSpecDropdown');

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
tileTypeDropdown.addEventListener('change', filterProducts);
otherSpecDropdown.addEventListener('change', filterProducts);
document.getElementById('searchInput').addEventListener('input', filterProducts);
document.getElementById('stockStatusDropdown').addEventListener('change', filterProducts);
document.getElementById('showArchivedCheckbox').addEventListener('change', function() {
    loadProducts();
});
</script>
</html>