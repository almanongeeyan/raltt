<?php include '../includes/sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Products Dashboard</title>
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
                        <i class="fas fa-boxes mr-3 text-blue-600"></i>Product Dashboard
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
                                <h3 class="font-bold text-2xl">24</h3>
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
                                <h3 class="font-bold text-2xl">1,248</h3>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-amber-500">
                        <div class="flex items-center">
                            <div class="rounded-full bg-amber-100 p-3 mr-4">
                                <i class="fas fa-exclamation-triangle text-amber-600"></i>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Low Stock</p>
                                <h3 class="font-bold text-2xl">3</h3>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
                        <div class="flex items-center">
                            <div class="rounded-full bg-purple-100 p-3 mr-4">
                                <i class="fas fa-tags text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Categories</p>
                                <h3 class="font-bold text-2xl">5</h3>
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
                                <input type="text" placeholder="Enter product name, design or type ..." class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" />
                            </div>
                            <select class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none">
                                <option>All Categories</option>
                                <option>Floral</option>
                                <option>Geometric</option>
                                <option>Classic</option>
                            </select>
                            <select class="px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none">
                                <option>Stock Status</option>
                                <option>In Stock</option>
                                <option>Low Stock</option>
                                <option>Out of Stock</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <!-- Low Stock Alert button removed -->
                            <button id="openAddProductSidebar" type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition flex items-center">
                                <i class="fas fa-plus-circle mr-2"></i> Add Product
                            </button>
                <!-- Add Product Sidebar Drawer -->
                <div id="addProductSidebar" class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col" style="max-width: 420px;">
                    <div class="flex items-center px-6 py-4 border-b">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center"><i class="fas fa-plus-circle mr-2 text-blue-600"></i>Add New Product</h2>
                    </div>
                    <form class="flex-1 flex flex-col gap-4 px-6 py-6 overflow-y-auto">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Product Image</label>
                            <input type="file" accept="image/*" class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Product Name</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" placeholder="Enter product name" required />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Product Price</label>
                            <input type="number" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" placeholder="Enter price" required />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Product Type</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" required>
                                <option value="">Select type</option>
                                <option value="Tile">Tile</option>
                                <option value="Countertop">Countertop</option>
                                <option value="Outdoor">Outdoor</option>
                                <option value="Wall">Wall</option>
                                <option value="Floor">Floor</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Tile Design</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" placeholder="e.g. Floral Blue" required />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Stock Count</label>
                            <input type="number" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none" placeholder="Enter stock count" required />
                        </div>
                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" id="cancelAddProductSidebar" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 font-medium">Cancel</button>
                            <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow">Add Product</button>
                        </div>
                    </form>
                </div>
                        </div>
                    </div>
                </div>

                <!-- Product Grid (Enhanced) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                    <!-- Product Card 1 -->
                    <div class="product-card bg-gradient-to-br from-blue-50 to-white rounded-2xl shadow-lg p-6 flex flex-col items-center border border-blue-100 relative hover:shadow-2xl">
                        <div class="absolute top-3 right-3 z-20">
                            <div class="inventory-high text-xs font-semibold px-3 py-1 rounded-full shadow-sm flex items-center gap-1">
                                <i class="fas fa-check-circle"></i> In Stock
                            </div>
                        </div>
                        <img src="../images/user/tile1.jpg" alt="Tile Sample 1" class="w-36 h-36 object-cover rounded-xl mb-4 border-4 border-blue-200 shadow-md transition-transform duration-300 hover:scale-105">
                        <div class="font-bold text-xl text-gray-900 mb-1 text-center tracking-wide">Floral Blue</div>
                        <div class="text-sm text-blue-500 mb-3 font-medium">Design #T-001</div>
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                            <div class="bg-green-500 h-3 rounded-full transition-all duration-300" style="width: 75%"></div>
                        </div>
                        <div class="text-xs text-gray-700 font-semibold px-2 py-1 rounded bg-white shadow-sm mb-2">INVENTORY: 122</div>
                        <div class="flex mt-4 space-x-3">
                            <button class="bg-blue-100 hover:bg-blue-200 text-blue-700 font-bold px-3 py-2 rounded-lg shadow transition flex items-center" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="bg-red-100 hover:bg-red-200 text-red-700 font-bold px-3 py-2 rounded-lg shadow transition flex items-center" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Product Card 2 -->
                    <div class="product-card bg-gradient-to-br from-amber-50 to-white rounded-2xl shadow-lg p-6 flex flex-col items-center border border-amber-100 relative hover:shadow-2xl">
                        <div class="absolute top-3 right-3 z-20">
                            <div class="inventory-medium text-xs font-semibold px-3 py-1 rounded-full shadow-sm flex items-center gap-1">
                                <i class="fas fa-info-circle"></i> Medium
                            </div>
                        </div>
                        <img src="../images/user/tile2.jpg" alt="Tile Sample 2" class="w-36 h-36 object-cover rounded-xl mb-4 border-4 border-amber-200 shadow-md transition-transform duration-300 hover:scale-105">
                        <div class="font-bold text-xl text-gray-900 mb-1 text-center tracking-wide">Black Diamond</div>
                        <div class="text-sm text-amber-600 mb-3 font-medium">Design #T-002</div>
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                            <div class="bg-amber-500 h-3 rounded-full transition-all duration-300" style="width: 45%"></div>
                        </div>
                        <div class="text-xs text-gray-700 font-semibold px-2 py-1 rounded bg-white shadow-sm mb-2">INVENTORY: 88</div>
                        <div class="flex mt-4 space-x-3">
                            <button class="bg-blue-100 hover:bg-blue-200 text-blue-700 font-bold px-3 py-2 rounded-lg shadow transition flex items-center" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="bg-red-100 hover:bg-red-200 text-red-700 font-bold px-3 py-2 rounded-lg shadow transition flex items-center" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Product Card 3 -->
                    <div class="product-card bg-gradient-to-br from-gray-50 to-white rounded-2xl shadow-lg p-6 flex flex-col items-center border border-gray-200 relative hover:shadow-2xl">
                        <div class="absolute top-3 right-3 z-20">
                            <div class="inventory-high text-xs font-semibold px-3 py-1 rounded-full shadow-sm flex items-center gap-1">
                                <i class="fas fa-check-circle"></i> In Stock
                            </div>
                        </div>
                        <img src="../images/user/tile3.jpg" alt="Tile Sample 3" class="w-36 h-36 object-cover rounded-xl mb-4 border-4 border-gray-300 shadow-md transition-transform duration-300 hover:scale-105">
                        <div class="font-bold text-xl text-gray-900 mb-1 text-center tracking-wide">Classical Black</div>
                        <div class="text-sm text-gray-500 mb-3 font-medium">Design #T-003</div>
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                            <div class="bg-green-500 h-3 rounded-full transition-all duration-300" style="width: 65%"></div>
                        </div>
                        <div class="text-xs text-gray-700 font-semibold px-2 py-1 rounded bg-white shadow-sm mb-2">INVENTORY: 102</div>
                        <div class="flex mt-4 space-x-3">
                            <button class="bg-blue-100 hover:bg-blue-200 text-blue-700 font-bold px-3 py-2 rounded-lg shadow transition flex items-center" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="bg-red-100 hover:bg-red-200 text-red-700 font-bold px-3 py-2 rounded-lg shadow transition flex items-center" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Product Card 4 -->
                    <div class="product-card bg-gradient-to-br from-green-50 to-white rounded-2xl shadow-lg p-6 flex flex-col items-center border border-green-100 relative hover:shadow-2xl">
                        <div class="absolute top-3 right-3 z-20">
                            <div class="inventory-low text-xs font-semibold px-3 py-1 rounded-full shadow-sm flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i> Low
                            </div>
                        </div>
                        <img src="../images/user/tile4.jpg" alt="Tile Sample 4" class="w-36 h-36 object-cover rounded-xl mb-4 border-4 border-green-200 shadow-md transition-transform duration-300 hover:scale-105">
                        <div class="font-bold text-xl text-gray-900 mb-1 text-center tracking-wide">Floral Beige Green</div>
                        <div class="text-sm text-green-600 mb-3 font-medium">Design #T-004</div>
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                            <div class="bg-red-500 h-3 rounded-full transition-all duration-300" style="width: 25%"></div>
                        </div>
                        <div class="text-xs text-gray-700 font-semibold px-2 py-1 rounded bg-white shadow-sm mb-2">INVENTORY: 25</div>
                        <div class="flex mt-4 space-x-3">
                            <button class="bg-blue-100 hover:bg-blue-200 text-blue-700 font-bold px-3 py-2 rounded-lg shadow transition flex items-center" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="bg-red-100 hover:bg-red-200 text-red-700 font-bold px-3 py-2 rounded-lg shadow transition flex items-center" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Product Card 5 (Sample) -->
                    <div class="product-card bg-gradient-to-br from-purple-50 to-white rounded-2xl shadow-lg p-6 flex flex-col items-center border border-purple-100 relative hover:shadow-2xl">
                        <div class="absolute top-3 right-3 z-20">
                            <div class="inventory-high text-xs font-semibold px-3 py-1 rounded-full shadow-sm flex items-center gap-1">
                                <i class="fas fa-check-circle"></i> In Stock
                            </div>
                        </div>
                        <img src="../images/user/tile5.jpg" alt="Tile Sample 5" class="w-36 h-36 object-cover rounded-xl mb-4 border-4 border-purple-200 shadow-md transition-transform duration-300 hover:scale-105">
                        <div class="font-bold text-xl text-gray-900 mb-1 text-center tracking-wide">Modern White</div>
                        <div class="text-sm text-purple-600 mb-3 font-medium">Design #T-005</div>
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                            <div class="bg-green-500 h-3 rounded-full transition-all duration-300" style="width: 80%"></div>
                        </div>
                        <div class="text-xs text-gray-700 font-semibold px-2 py-1 rounded bg-white shadow-sm mb-2">INVENTORY: 140</div>
                        <div class="flex mt-4 space-x-3">
                            <button class="bg-blue-100 hover:bg-blue-200 text-blue-700 font-bold px-3 py-2 rounded-lg shadow transition flex items-center" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="bg-red-100 hover:bg-red-200 text-red-700 font-bold px-3 py-2 rounded-lg shadow transition flex items-center" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-10">
                    <nav class="inline-flex rounded-md shadow-sm">
                        <a href="#" class="py-2 px-3 ml-0 leading-tight text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <a href="#" class="py-2 px-4 leading-tight text-blue-600 bg-blue-50 border border-gray-300 hover:bg-blue-100 hover:text-blue-700">1</a>
                        <a href="#" class="py-2 px-4 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">2</a>
                        <a href="#" class="py-2 px-4 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">3</a>
                        <a href="#" class="py-2 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </nav>
                </div>
            </div>
        </main>
    </div>
</body>
<script>
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
</script>
</html>