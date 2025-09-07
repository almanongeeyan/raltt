<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order Summary</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800">

  <!-- Top Navbar -->
  <nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
      
      <!-- Left Links -->
      <div class="flex items-center gap-6">
        <a href="#" class="text-gray-800 font-semibold hover:text-gray-900">Home</a>
        <a href="#" class="text-gray-600 hover:text-gray-900">Features</a>
        <a href="#" class="text-gray-600 hover:text-gray-900">Products</a>
        <a href="#" class="text-gray-600 hover:text-gray-900">Feedback</a>
      </div>
      
      <!-- Right User Info -->
      <div class="flex items-center gap-3">
        <span class="text-gray-800 font-medium">Cholene Jane Aberin</span>
        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center">
          <span class="text-white font-bold">C</span>
        </div>
      </div>
    </div>
  </nav>

  <!-- Main Container -->
  <div class="max-w-7xl mx-auto p-4 md:p-6 lg:p-8">

    <!-- Back Button & Title -->
    <div class="flex items-center gap-2 mb-6">
      <a href="#" class="flex items-center gap-1 text-gray-600 hover:text-gray-800">
        ← Back
      </a>
      <h1 class="text-2xl font-bold text-gray-900">Order Summary</h1>
    </div>

    <!-- Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- Left Column -->
      <div class="lg:col-span-2 space-y-6">

        <!-- Address Section -->
        <div class="bg-white rounded-lg shadow p-5">
          <div class="flex justify-between items-start">
            <div>
              <h2 class="text-lg font-semibold text-gray-900">Cholene Jane Aberin</h2>
              <p class="text-sm text-gray-600">(+63) 912 219 9115</p>
              <p class="text-sm text-gray-600">123 Barangay 123 Tala, Caloocan City, Metro Manila, 1423</p>
            </div>
            <button class="text-sm px-4 py-1 bg-gray-100 rounded-lg hover:bg-gray-200">Edit</button>
          </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-lg shadow p-5">
          <h2 class="text-lg font-semibold mb-4 text-gray-900">Order Items</h2>
          <div class="divide-y divide-gray-200">

            <!-- Item 1 -->
            <div class="flex justify-between items-center py-3">
              <div class="flex items-center gap-4">
                <img src="https://via.placeholder.com/60" alt="Tile" class="w-16 h-16 rounded-md">
                <div>
                  <h3 class="font-medium text-gray-900">Arte Ceramiche Matte Floor Tile</h3>
                  <p class="text-sm text-gray-500">Premium Tiles</p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm text-gray-500">₱100 × 2</p>
                <p class="font-semibold text-gray-900">₱200</p>
              </div>
            </div>

            <!-- Item 2 -->
            <div class="flex justify-between items-center py-3">
              <div class="flex items-center gap-4">
                <img src="https://via.placeholder.com/60" alt="Tile" class="w-16 h-16 rounded-md">
                <div>
                  <h3 class="font-medium text-gray-900">Arte Ceramiche Matte Floor Tile</h3>
                  <p class="text-sm text-gray-500">Premium Tiles</p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm text-gray-500">₱100 × 1</p>
                <p class="font-semibold text-gray-900">₱100</p>
              </div>
            </div>

            <!-- Item 3 -->
            <div class="flex justify-between items-center py-3">
              <div class="flex items-center gap-4">
                <img src="https://via.placeholder.com/60" alt="Tile" class="w-16 h-16 rounded-md">
                <div>
                  <h3 class="font-medium text-gray-900">Arte Ceramiche Matte Floor Tile</h3>
                  <p class="text-sm text-gray-500">Premium Tiles</p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm text-gray-500">₱100 × 1</p>
                <p class="font-semibold text-gray-900">₱100</p>
              </div>
            </div>

            <!-- Item 4 -->
            <div class="flex justify-between items-center py-3">
              <div class="flex items-center gap-4">
                <img src="https://via.placeholder.com/60" alt="Tile" class="w-16 h-16 rounded-md">
                <div>
                  <h3 class="font-medium text-gray-900">Arte Ceramiche Matte Floor Tile</h3>
                  <p class="text-sm text-gray-500">Premium Tiles</p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm text-gray-500">₱100 × 1</p>
                <p class="font-semibold text-gray-900">₱100</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column -->
      <div class="space-y-6">

        <!-- Request & Referral -->
        <div class="bg-white rounded-lg shadow p-5 space-y-4">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" class="w-4 h-4 text-brown-600 rounded">
            <span class="text-gray-700">Request Receipt</span>
          </label>

          <div>
            <label class="block text-gray-700 text-sm mb-1">Referral Code</label>
            <input type="text" placeholder="Enter Referral Code" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-brown-300">
          </div>

          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" class="w-4 h-4 text-brown-600 rounded">
            <span class="text-gray-700">Use Points (Remaining: 2.5)</span>
          </label>
        </div>

        <!-- Shipping Options -->
        <div class="bg-white rounded-lg shadow p-5 space-y-3">
          <h3 class="font-semibold text-gray-900">Shipping Options</h3>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="shipping" checked class="w-4 h-4 text-brown-600">
            <span class="text-gray-700">Self Pick-Up (Free)</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="shipping" class="w-4 h-4 text-brown-600">
            <span class="text-gray-700">Delivery (₱40)</span>
          </label>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white rounded-lg shadow p-5 space-y-3">
          <h3 class="font-semibold text-gray-900">Payment Method</h3>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="payment" checked class="w-4 h-4 text-brown-600">
            <span class="text-gray-700">Cash On Delivery</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="payment" class="w-4 h-4 text-brown-600">
            <span class="text-gray-700">GCash</span>
          </label>
        </div>

        <!-- Payment Details -->
        <div class="bg-white rounded-lg shadow p-5 space-y-3">
          <h3 class="font-semibold text-gray-900">Payment Details</h3>
          <div class="flex justify-between text-gray-600">
            <span>Merchandise Subtotal</span>
            <span>₱300</span>
          </div>
          <div class="flex justify-between text-gray-600">
            <span>Shipping Subtotal</span>
            <span>₱40</span>
          </div>
          <div class="flex justify-between text-gray-600">
            <span>Points Discount</span>
            <span>₱0</span>
          </div>
          <div class="flex justify-between text-gray-600">
            <span>Referral Discount</span>
            <span>₱0</span>
          </div>
          <div class="border-t border-gray-200 pt-3 flex justify-between font-bold text-gray-900">
            <span>Total</span>
            <span>₱300</span>
          </div>
          <button class="w-full bg-[#A0522D] text-white py-3 rounded-lg font-semibold hover:bg-[#8B4513] transition">
            Place Order
          </button>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
