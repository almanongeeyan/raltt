 <?php include '../includes/headeruser.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <!-- Header -->
    <header class="bg-white shadow-sm">
        <nav class="container mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <a href="#" class="text-gray-600 hover:text-gray-900">Home</a>
                <a href="#" class="text-gray-600 hover:text-gray-900">Features</a>
                <a href="#" class="text-gray-600 hover:text-gray-900">Products</a>
                <a href="#" class="text-gray-600 hover:text-gray-900">Feedback</a>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-gray-800 font-semibold hidden md:block">Cholene Jane Aberin</span>
                <div class="w-8 h-8 rounded-full bg-gray-300"></div>
                <button class="text-gray-500 hover:text-gray-700">❤️</button>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto p-4 md:p-6 flex flex-col md:flex-row gap-6">

        <!-- Shopping Cart Section -->
        <section class="flex-grow bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-semibold text-gray-800">Shopping Cart</h1>
            <p class="text-gray-500 mb-6">4 items in your cart</p>

            <!-- Select All & Item Count -->
            <div class="flex justify-between items-center mb-4 border-b pb-4">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="select-all" class="form-checkbox text-blue-500 rounded-full">
                    <label for="select-all" class="text-gray-700">Select All Items</label>
                </div>
                <p class="text-gray-500">2 of 4 selected</p>
            </div>

            <!-- Cart Table Header (Desktop) -->
            <div class="hidden md:grid md:grid-cols-10 gap-4 text-gray-600 font-semibold mb-4 border-b pb-2">
                <div class="col-span-4">Product</div>
                <div class="col-span-1">Unit Price</div>
                <div class="col-span-2">Quantity</div>
                <div class="col-span-2">Total Price</div>
                <div class="col-span-1"></div>
            </div>

            <!-- Cart Items -->
            <div class="space-y-4">
                <!-- Cart Item Row 1 (Desktop & Mobile) -->
                <div class="md:grid md:grid-cols-10 md:gap-4 flex items-center border rounded-lg p-4 md:p-0">
                    <div class="flex items-center space-x-4 md:col-span-4">
                        <input type="checkbox" checked class="form-checkbox text-blue-500 rounded-full hidden md:block">
                        <input type="checkbox" checked class="form-checkbox text-blue-500 rounded-full md:hidden">
                        <div class="w-16 h-16 bg-cover bg-center rounded-md" style="background-image: url('https://placehold.co/64x64/E0E7FF/333333');"></div>
                        <div>
                            <p class="font-medium">Arte Ceramiche Matte Floor Tile</p>
                            <p class="text-sm text-gray-500">Premium Tiles</p>
                        </div>
                    </div>
                    <div class="flex-grow flex justify-end md:justify-start md:col-span-1 text-gray-600 font-medium">
                        P 100
                    </div>
                    <div class="flex-shrink-0 flex items-center md:col-span-2 space-x-2 md:space-x-4">
                        <button class="text-gray-500 hover:text-gray-700">-</button>
                        <span class="font-bold">2</span>
                        <button class="text-green-500 hover:text-green-700">+</button>
                    </div>
                    <div class="flex-grow flex justify-end md:justify-start md:col-span-2 text-gray-800 font-bold">
                        P 200
                    </div>
                    <div class="flex-shrink-0 md:col-span-1 text-gray-500 hover:text-red-500 cursor-pointer md:text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 11-2 0v6a1 1 0 112 0V8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <!-- Cart Item Row 2 -->
                <div class="md:grid md:grid-cols-10 md:gap-4 flex items-center border rounded-lg p-4 md:p-0">
                    <div class="flex items-center space-x-4 md:col-span-4">
                        <input type="checkbox" checked class="form-checkbox text-blue-500 rounded-full hidden md:block">
                        <input type="checkbox" checked class="form-checkbox text-blue-500 rounded-full md:hidden">
                        <div class="w-16 h-16 bg-cover bg-center rounded-md" style="background-image: url('https://placehold.co/64x64/E0E7FF/333333');"></div>
                        <div>
                            <p class="font-medium">Arte Ceramiche Matte Floor Tile</p>
                            <p class="text-sm text-gray-500">Premium Tiles</p>
                        </div>
                    </div>
                    <div class="flex-grow flex justify-end md:justify-start md:col-span-1 text-gray-600 font-medium">
                        P 100
                    </div>
                    <div class="flex-shrink-0 flex items-center md:col-span-2 space-x-2 md:space-x-4">
                        <button class="text-gray-500 hover:text-gray-700">-</button>
                        <span class="font-bold">1</span>
                        <button class="text-green-500 hover:text-green-700">+</button>
                    </div>
                    <div class="flex-grow flex justify-end md:justify-start md:col-span-2 text-gray-800 font-bold">
                        P 100
                    </div>
                    <div class="flex-shrink-0 md:col-span-1 text-gray-500 hover:text-red-500 cursor-pointer md:text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 11-2 0v6a1 1 0 112 0V8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <!-- Cart Item Row 3 -->
                 <div class="md:grid md:grid-cols-10 md:gap-4 flex items-center border rounded-lg p-4 md:p-0">
                    <div class="flex items-center space-x-4 md:col-span-4">
                        <input type="checkbox" class="form-checkbox text-blue-500 rounded-full hidden md:block">
                        <input type="checkbox" class="form-checkbox text-blue-500 rounded-full md:hidden">
                        <div class="w-16 h-16 bg-cover bg-center rounded-md" style="background-image: url('https://placehold.co/64x64/E0E7FF/333333');"></div>
                        <div>
                            <p class="font-medium">Arte Ceramiche Matte Floor Tile</p>
                            <p class="text-sm text-gray-500">Premium Tiles</p>
                        </div>
                    </div>
                    <div class="flex-grow flex justify-end md:justify-start md:col-span-1 text-gray-600 font-medium">
                        P 100
                    </div>
                    <div class="flex-shrink-0 flex items-center md:col-span-2 space-x-2 md:space-x-4">
                        <button class="text-gray-500 hover:text-gray-700">-</button>
                        <span class="font-bold">1</span>
                        <button class="text-green-500 hover:text-green-700">+</button>
                    </div>
                    <div class="flex-grow flex justify-end md:justify-start md:col-span-2 text-gray-800 font-bold">
                        P 100
                    </div>
                    <div class="flex-shrink-0 md:col-span-1 text-gray-500 hover:text-red-500 cursor-pointer md:text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 11-2 0v6a1 1 0 112 0V8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <!-- Cart Item Row 4 -->
                <div class="md:grid md:grid-cols-10 md:gap-4 flex items-center border rounded-lg p-4 md:p-0">
                    <div class="flex items-center space-x-4 md:col-span-4">
                        <input type="checkbox" class="form-checkbox text-blue-500 rounded-full hidden md:block">
                        <input type="checkbox" class="form-checkbox text-blue-500 rounded-full md:hidden">
                        <div class="w-16 h-16 bg-cover bg-center rounded-md" style="background-image: url('https://placehold.co/64x64/E0E7FF/333333');"></div>
                        <div>
                            <p class="font-medium">Arte Ceramiche Matte Floor Tile</p>
                            <p class="text-sm text-gray-500">Premium Tiles</p>
                        </div>
                    </div>
                    <div class="flex-grow flex justify-end md:justify-start md:col-span-1 text-gray-600 font-medium">
                        P 100
                    </div>
                    <div class="flex-shrink-0 flex items-center md:col-span-2 space-x-2 md:space-x-4">
                        <button class="text-gray-500 hover:text-gray-700">-</button>
                        <span class="font-bold">1</span>
                        <button class="text-green-500 hover:text-green-700">+</button>
                    </div>
                    <div class="flex-grow flex justify-end md:justify-start md:col-span-2 text-gray-800 font-bold">
                        P 100
                    </div>
                    <div class="flex-shrink-0 md:col-span-1 text-gray-500 hover:text-red-500 cursor-pointer md:text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 11-2 0v6a1 1 0 112 0V8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <!-- Order Summary Sidebar -->
        <aside class="w-full md:w-1/3 bg-white rounded-lg shadow-md p-6 h-fit">
            <div class="flex items-center space-x-2 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.183 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h2 class="text-xl font-semibold text-gray-800">Order Summary</h2>
            </div>
            
            <div class="space-y-2 mb-6">
                <div class="flex justify-between text-gray-600">
                    <span>Selected Items</span>
                    <span>2</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span>P 300</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Shipping</span>
                    <span>P 40</span>
                </div>
            </div>

            <div class="flex justify-between font-bold text-lg border-t pt-4">
                <span>Total</span>
                <span>P 300</span>
            </div>
            
            <button class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 rounded-lg mt-6">
                Checkout (2 Items)
            </button>
        </aside>

    </main>
</body>
</html>