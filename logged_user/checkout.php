    <?php include '../includes/headeruser.php'; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">


    <main class="container mx-auto p-6 flex flex-col md:flex-row gap-6" style="margin-top:90px;">

        <section class="flex-grow bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center mb-6">
                <a href="#" class="text-gray-600 hover:text-gray-900 mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-semibold text-gray-800">Order Summary</h1>
            </div>

            <div class="border rounded-lg p-4 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="font-semibold text-gray-800">Cholene Jane Aberin</h2>
                        <p class="text-gray-600 text-sm">(+63) 912 219 9115</p>
                        <p class="text-gray-600 text-sm">123 Barangay 123 Tala, Caloocan City, Metro Manila, 1423</p>
                    </div>
                    <button class="text-blue-500 hover:text-blue-700 text-sm">Edit</button>
                </div>
            </div>

            <h2 class="text-xl font-semibold text-gray-800 mb-4">Order Items</h2>
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-cover bg-center rounded-md" style="background-image: url('https://placehold.co/64x64/E0E7FF/333333');"></div>
                    <div class="flex-grow">
                        <p class="font-medium">Arte Ceramiche Matte Floor Tile</p>
                        <p class="text-sm text-gray-500">Premium Tiles</p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <p class="text-gray-600">P 100 x 2</p>
                        <p class="font-semibold">P 200</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-cover bg-center rounded-md" style="background-image: url('https://placehold.co/64x64/E0E7FF/333333');"></div>
                    <div class="flex-grow">
                        <p class="font-medium">Arte Ceramiche Matte Floor Tile</p>
                        <p class="text-sm text-gray-500">Premium Tiles</p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <p class="text-gray-600">P 100 x 1</p>
                        <p class="font-semibold">P 100</p>
                    </div>
                </div>
                 <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-cover bg-center rounded-md" style="background-image: url('https://placehold.co/64x64/E0E7FF/333333');"></div>
                    <div class="flex-grow">
                        <p class="font-medium">Arte Ceramiche Matte Floor Tile</p>
                        <p class="text-sm text-gray-500">Premium Tiles</p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <p class="text-gray-600">P 100 x 1</p>
                        <p class="font-semibold">P 100</p>
                    </div>
                </div>
                 <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-cover bg-center rounded-md" style="background-image: url('https://placehold.co/64x64/E0E7FF/333333');"></div>
                    <div class="flex-grow">
                        <p class="font-medium">Arte Ceramiche Matte Floor Tile</p>
                        <p class="text-sm text-gray-500">Premium Tiles</p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <p class="text-gray-600">P 100 x 1</p>
                        <p class="font-semibold">P 100</p>
                    </div>
                </div>
                 <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-cover bg-center rounded-md" style="background-image: url('https://placehold.co/64x64/E0E7FF/333333');"></div>
                    <div class="flex-grow">
                        <p class="font-medium">Arte Ceramiche Matte Floor Tile</p>
                        <p class="text-sm text-gray-500">Premium Tiles</p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <p class="text-gray-600">P 100 x 1</p>
                        <p class="font-semibold">P 100</p>
                    </div>
                </div>
            </div>
        </section>

        <aside class="w-full md:w-1/3 bg-white rounded-lg shadow-md p-6 flex-shrink-0">
            <div class="space-y-6">
                
                <div class="space-y-4 border-b pb-4">
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="request-receipt" class="form-checkbox text-blue-500 rounded-full" checked>
                        <label for="request-receipt" class="text-gray-700">Request Receipt</label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="referral-code" class="form-checkbox text-blue-500 rounded-full">
                        <label for="referral-code" class="text-gray-700">Referral Code</label>
                    </div>
                    <input type="text" placeholder="Enter Referral Code" class="w-full border rounded-lg p-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                     <div class="flex items-center space-x-2">
                        <input type="checkbox" id="use-points" class="form-checkbox text-blue-500 rounded-full">
                        <label for="use-points" class="text-gray-700">Use Points (Remaining: 2.5)</label>
                    </div>
                </div>

                <div class="space-y-4 border-b pb-4">
                    <p class="font-semibold text-gray-800">Shipping Options</p>
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <input type="radio" id="self-pickup" name="shipping" value="self-pickup" class="form-radio text-blue-500" checked>
                            <label for="self-pickup" class="text-gray-700">Self Pick-up <span class="text-gray-500">(Free)</span></label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="radio" id="delivery" name="shipping" value="delivery" class="form-radio text-blue-500">
                            <label for="delivery" class="text-gray-700">Delivery <span class="text-gray-500">(P 40)</span></label>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 border-b pb-4">
                    <p class="font-semibold text-gray-800">Payment Method</p>
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <input type="radio" id="cash-on-delivery" name="payment" value="cash-on-delivery" class="form-radio text-blue-500" checked>
                            <label for="cash-on-delivery" class="text-gray-700">Cash On Delivery</label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="radio" id="gcash" name="payment" value="gcash" class="form-radio text-blue-500">
                            <label for="gcash" class="text-gray-700">GCash</label>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-800 text-lg">Payment Details</h3>
                    <div class="flex justify-between text-gray-600">
                        <span>Merchandise Subtotal</span>
                        <span>P 300</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping Subtotal</span>
                        <span>P 40</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Points Discount</span>
                        <span>P 0</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Referral Discount</span>
                        <span>P 0</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg border-t pt-4 mt-4">
                        <span>Total</span>
                        <span>P 300</span>
                    </div>
                    <button class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 rounded-lg mt-4">
                        Place Order
                    </button>
                </div>

            </div>
        </aside>

    </main>

</body>
</html> 