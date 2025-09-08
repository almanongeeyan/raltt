<?php include '../includes/headeruser.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary - Rich Anne Lea Tiles Trading</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
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
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #ffece2 0%, #f8f5f2 60%, #e8a56a 100%);
            min-height: 100vh;
        }
        
        .summary-card {
            transition: all 0.3s ease;
            border-radius: 16px;
            overflow: hidden;
        }
        
        .summary-card:hover {
            box-shadow: 0 12px 32px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .payment-method {
            transition: all 0.3s ease;
            border-radius: 12px;
            cursor: pointer;
        }
        
        .payment-method:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        
        .payment-method.selected {
            border-color: #7d310a;
            box-shadow: 0 0 0 2px rgba(125, 49, 10, 0.2);
        }
        
        .checkout-btn {
            background: #7d310a;
            box-shadow: 0 6px 24px 0 rgba(207, 135, 86, 0.3), 0 1.5px 0 #fff;
            transition: all 0.3s ease;
        }
        
        .checkout-btn:hover {
            background: #5a2207;
            box-shadow: 0 8px 28px 0 rgba(207, 135, 86, 0.4), 0 1.5px 0 #fff;
            transform: translateY(-2px);
        }
        
        .custom-radio input[type="radio"] {
            display: none;
        }
        
        .custom-radio .checkmark {
            width: 20px;
            height: 20px;
            border: 2px solid #cf8756;
            border-radius: 50%;
            display: inline-block;
            position: relative;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .custom-radio input[type="radio"]:checked + .checkmark {
            border-color: #7d310a;
            background-color: #7d310a;
        }
        
        .custom-radio input[type="radio"]:checked + .checkmark:after {
            content: "";
            position: absolute;
            left: 4px;
            top: 4px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: white;
        }
        
        .paymongo-btn {
            background: linear-gradient(135deg, #4C6EF5 0%, #364FC7 100%);
            transition: all 0.3s ease;
        }
        
        .paymongo-btn:hover {
            background: linear-gradient(135deg, #364FC7 0%, #4C6EF5 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(76, 110, 245, 0.3);
        }
        
        .referral-coins {
            background: linear-gradient(135deg, #f9f5f2 0%, #f0e6df 100%);
            border: 1px dashed #cf8756;
        }
        
        .apply-coins-btn {
            background: linear-gradient(90deg, #7d310a 0%, #a34a20 100%);
            transition: all 0.3s ease;
        }
        
        .apply-coins-btn:hover {
            background: linear-gradient(90deg, #a34a20 0%, #7d310a 100%);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="min-h-screen pt-24">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Progress Steps -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-2">
                    <h1 class="text-2xl font-black text-primary">Order Summary</h1>
                    <span class="text-sm font-medium bg-primary text-white px-3 py-1 rounded-full">Step 2 of 3</span>
                </div>
                
                <div class="flex items-center justify-between mt-6">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                            <span class="text-white font-bold text-sm">1</span>
                        </div>
                        <span class="ml-2 text-sm font-medium text-primary">Cart</span>
                    </div>
                    
                    <div class="flex-1 h-1 bg-primary mx-2"></div>
                    
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                            <span class="text-white font-bold text-sm">2</span>
                        </div>
                        <span class="ml-2 text-sm font-medium text-primary">Order Summary</span>
                    </div>
                    
                    <div class="flex-1 h-1 bg-gray-200 mx-2"></div>
                    
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-500 font-bold text-sm">3</span>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-500">Confirmation</span>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Order Details -->
                <section class="flex-grow">
                    <!-- Order Items -->
                    <div class="summary-card bg-white rounded-2xl shadow-lg p-6 mb-6">
                        <h2 class="text-xl font-black text-primary mb-4">Order Items</h2>
                        
                        <div class="space-y-4">
                            <!-- Order Item 1 -->
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 bg-cover bg-center rounded-lg overflow-hidden shadow-sm">
                                        <img src="https://placehold.co/64x64/f9f5f2/7d310a" alt="Tile" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <p class="font-bold text-textdark">Arte Ceramiche Matte Floor Tile</p>
                                        <p class="text-sm text-textlight">30x30 cm • Qty: 2</p>
                                    </div>
                                </div>
                                <div class="text-primary font-black">₱200</div>
                            </div>
                            
                            <!-- Order Item 2 -->
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 bg-cover bg-center rounded-lg overflow-hidden shadow-sm">
                                        <img src="https://placehold.co/64x64/f9f5f2/7d310a" alt="Tile" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <p class="font-bold text-textdark">Porcelain Wood-Look Tile</p>
                                        <p class="text-sm text-textlight">15x60 cm • Qty: 1</p>
                                    </div>
                                </div>
                                <div class="text-primary font-black">₱150</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Referral Coins -->
                    <div class="summary-card bg-white rounded-2xl shadow-lg p-6 mb-6">
                        <h2 class="text-xl font-black text-primary mb-4">Referral Coins</h2>
                        
                        <div class="referral-coins rounded-xl p-4 flex flex-col md:flex-row items-center justify-between">
                            <div class="flex items-center mb-3 md:mb-0">
                                <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center mr-3">
                                    <i class="fas fa-coins text-white"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-textdark">You have 250 referral coins</p>
                                    <p class="text-sm text-textlight">100 coins = ₱10 discount</p>
                                </div>
                            </div>
                            
                            <button id="apply-coins-btn" class="apply-coins-btn text-white font-bold py-2 px-6 rounded-lg flex items-center">
                                <i class="fas fa-check-circle mr-2"></i> Apply Coins
                            </button>
                        </div>
                        
                        <div id="coins-applied" class="hidden mt-4 bg-green-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <p class="text-green-700 font-medium">100 coins applied! You've received a ₱10 discount.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shipping Information -->
                    <div class="summary-card bg-white rounded-2xl shadow-lg p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-black text-primary">Shipping Information</h2>
                            <button class="text-secondary font-medium hover:text-primary transition-colors">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-textlight mb-1">Full Name</p>
                                <p class="font-medium text-textdark">Juan Dela Cruz</p>
                            </div>
                            <div>
                                <p class="text-sm text-textlight mb-1">Contact Number</p>
                                <p class="font-medium text-textdark">+63 912 345 6789</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm text-textlight mb-1">Shipping Address</p>
                                <p class="font-medium text-textdark">123 Main Street, Barangay San Antonio, Quezon City, Metro Manila 1105</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="summary-card bg-white rounded-2xl shadow-lg p-6">
                        <h2 class="text-xl font-black text-primary mb-4">Payment Method</h2>
                        <p class="text-textlight mb-4">Select your preferred payment method</p>
                        
                        <div class="space-y-3">
                            <!-- GCash Option -->
                            <div class="payment-method border-2 border-gray-200 p-4">
                                <label class="custom-radio flex items-center cursor-pointer">
                                    <input type="radio" name="payment-method" value="gcash">
                                    <span class="checkmark"></span>
                                    <div class="ml-3 flex items-center">
                                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center mr-2">
                                            <i class="fas fa-mobile-alt text-white"></i>
                                        </div>
                                        <span class="font-medium text-textdark">GCash</span>
                                    </div>
                                </label>
                                
                                <div class="mt-3 ml-11">
                                    <p class="text-sm text-textlight">
                                        Pay securely using your GCash account. You will be redirected to the GCash app to complete your payment.
                                    </p>
                                </div>
                            </div>
                            
                            <!-- PayMongo Option -->
                            <div class="payment-method border-2 border-gray-200 p-4 selected">
                                <label class="custom-radio flex items-center cursor-pointer">
                                    <input type="radio" name="payment-method" value="paymongo" checked>
                                    <span class="checkmark"></span>
                                    <div class="ml-3 flex items-center">
                                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center mr-2">
                                            <i class="fas fa-credit-card text-white"></i>
                                        </div>
                                        <span class="font-medium text-textdark">Pay with PayMongo</span>
                                    </div>
                                </label>
                                
                                
                                <p class="text-sm text-textlight mt-3 ml-11">
                                    Secure payment processing powered by PayMongo. Multiple payment options available.
                                </p>
                            </div>
                            
                            <!-- Self Pickup Option -->
                            <div class="payment-method border-2 border-gray-200 p-4">
                                <label class="custom-radio flex items-center cursor-pointer">
                                    <input type="radio" name="payment-method" value="self-pickup">
                                    <span class="checkmark"></span>
                                    <div class="ml-3 flex items-center">
                                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center mr-2">
                                            <i class="fas fa-store text-white"></i>
                                        </div>
                                        <span class="font-medium text-textdark">Self Pickup</span>
                                    </div>
                                </label>
                                
                                <div class="mt-3 ml-11">
                                    <p class="text-sm text-textlight">
                                        Pick up your order at our store. No delivery fees. Payment will be made upon pickup.
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Cash on Delivery Option -->
                            <div class="payment-method border-2 border-gray-200 p-4">
                                <label class="custom-radio flex items-center cursor-pointer">
                                    <input type="radio" name="payment-method" value="cod">
                                    <span class="checkmark"></span>
                                    <div class="ml-3 flex items-center">
                                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center mr-2">
                                            <i class="fas fa-money-bill-wave text-white"></i>
                                        </div>
                                        <span class="font-medium text-textdark">Cash on Delivery</span>
                                    </div>
                                </label>
                                
                                <div class="mt-3 ml-11">
                                    <p class="text-sm text-textlight">
                                        Pay with cash when your order is delivered. An additional ₱25 processing fee applies.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- Order Summary Sidebar -->
                <aside class="w-full lg:w-96">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-28">
                        <h2 class="text-xl font-black text-primary mb-6">Order Summary</h2>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between">
                                <span class="text-textlight">Subtotal (2 items)</span>
                                <span class="font-medium text-textdark">₱350</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-textlight">Shipping Fee</span>
                                <span class="font-medium text-textdark">₱40</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-textlight">Tax</span>
                                <span class="font-medium text-textdark">₱21</span>
                            </div>
                            <div id="coins-discount" class="flex justify-between hidden">
                                <span class="text-textlight">Referral Coins Discount</span>
                                <span class="font-medium text-green-600">-₱0</span>
                            </div>
                            <div id="cod-fee" class="flex justify-between hidden">
                                <span class="text-textlight">COD Processing Fee</span>
                                <span class="font-medium text-textdark">₱25</span>
                            </div>
                        </div>

                        <div class="flex justify-between font-black text-lg border-t border-gray-100 pt-4 mb-6">
                            <span class="text-primary">Total</span>
                            <span id="total-amount" class="text-primary">₱411</span>
                        </div>
                        
                        <!-- Pay with Selected Method Button -->
                        <button id="pay-button" class="checkout-btn w-full py-4 rounded-xl font-black text-white text-lg mb-4 flex items-center justify-center">
                            <i class="fas fa-lock mr-2"></i>Proceed to Payment
                        </button>
                        
                        <p class="text-xs text-textlight text-center">
                            By completing your purchase, you agree to our <a href="#" class="text-secondary hover:underline">Terms of Service</a> and <a href="#" class="text-secondary hover:underline">Privacy Policy</a>.
                        </p>
                        
                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <h3 class="font-bold text-primary mb-3">Need Help?</h3>
                            <div class="flex items-center text-textlight">
                                <i class="fas fa-phone-alt mr-2"></i>
                                <span class="text-sm">+63 2 1234 5678</span>
                            </div>
                            <div class="flex items-center text-textlight mt-1">
                                <i class="fas fa-envelope mr-2"></i>
                                <span class="text-sm">support@raltt.com</span>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-black text-primary">Complete Payment</h3>
                    <button id="close-modal" class="text-textlight hover:text-textdark">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-textlight">Total Amount:</span>
                        <span class="font-black text-primary text-lg">₱<span id="modal-total-amount">411.00</span></span>
                    </div>
                    <p class="text-xs text-textlight" id="modal-payment-method">Payment processed securely by PayMongo</p>
                </div>
                
                <div id="payment-instructions">
                    <!-- Instructions will be dynamically inserted here -->
                </div>
                
                <button class="checkout-btn w-full py-4 rounded-xl font-black text-white text-lg">
                    Confirm Payment
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Payment method selection
            const paymentMethods = document.querySelectorAll('.payment-method');
            const codFeeElement = document.getElementById('cod-fee');
            const totalAmountElement = document.getElementById('total-amount');
            const modalTotalAmountElement = document.getElementById('modal-total-amount');
            const modalPaymentMethodElement = document.getElementById('modal-payment-method');
            const paymentInstructionsElement = document.getElementById('payment-instructions');
            const payButton = document.getElementById('pay-button');
            
            let baseTotal = 411; // Base total without COD fee
            let currentTotal = 411;
            let coinsApplied = false;
            
            paymentMethods.forEach(method => {
                method.addEventListener('click', function() {
                    paymentMethods.forEach(m => m.classList.remove('selected'));
                    this.classList.add('selected');
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                    
                    // Handle COD fee display
                    if (radio.value === 'cod') {
                        codFeeElement.classList.remove('hidden');
                        currentTotal = baseTotal + 25;
                    } else {
                        codFeeElement.classList.add('hidden');
                        currentTotal = baseTotal;
                    }
                    
                    // Apply coins discount if applicable
                    if (coinsApplied && currentTotal > 10) {
                        currentTotal -= 10;
                    }
                    
                    updateTotalDisplay();
                });
            });
            
            // Referral coins functionality
            const applyCoinsBtn = document.getElementById('apply-coins-btn');
            const coinsAppliedElement = document.getElementById('coins-applied');
            const coinsDiscountElement = document.getElementById('coins-discount');
            
            applyCoinsBtn.addEventListener('click', function() {
                if (!coinsApplied) {
                    coinsApplied = true;
                    coinsAppliedElement.classList.remove('hidden');
                    coinsDiscountElement.classList.remove('hidden');
                    coinsDiscountElement.querySelector('span:last-child').textContent = '-₱10';
                    
                    if (currentTotal > 10) {
                        currentTotal -= 10;
                        updateTotalDisplay();
                    }
                    
                    applyCoinsBtn.innerHTML = '<i class="fas fa-times-circle mr-2"></i> Remove Coins';
                } else {
                    coinsApplied = false;
                    coinsAppliedElement.classList.add('hidden');
                    coinsDiscountElement.classList.add('hidden');
                    
                    currentTotal += 10;
                    updateTotalDisplay();
                    
                    applyCoinsBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Apply Coins';
                }
            });
            
            function updateTotalDisplay() {
                totalAmountElement.textContent = `₱${currentTotal}`;
                modalTotalAmountElement.textContent = `${currentTotal}.00`;
            }
            
            // Payment modal functionality
            const paymentModal = document.getElementById('payment-modal');
            const closeModal = document.getElementById('close-modal');
            
            payButton.addEventListener('click', function() {
                const selectedPayment = document.querySelector('input[name="payment-method"]:checked').value;
                
                // Update modal based on selected payment method
                switch(selectedPayment) {
                    case 'gcash':
                        modalPaymentMethodElement.textContent = 'Payment processed through GCash';
                        paymentInstructionsElement.innerHTML = `
                            <div class="mb-6">
                                <p class="text-textdark font-medium mb-3">To complete your payment:</p>
                                <ol class="list-decimal list-inside text-sm text-textlight space-y-2">
                                    <li>Open your GCash app</li>
                                    <li>Go to Pay QR</li>
                                    <li>Scan the QR code below</li>
                                    <li>Confirm the amount of ₱${currentTotal}</li>
                                </ol>
                                <div class="bg-white p-4 rounded-lg border border-gray-200 mt-4 flex justify-center">
                                    <img src="https://placehold.co/200x200/ffffff/7d310a?text=GCash+QR" alt="GCash QR Code" class="w-40 h-40">
                                </div>
                            </div>
                        `;
                        break;
                    case 'paymongo':
                        modalPaymentMethodElement.textContent = 'Payment processed securely by PayMongo';
                        paymentInstructionsElement.innerHTML = `
                            <div class="mb-6">
                                <label class="block text-textdark font-medium mb-2">Select Payment Method</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="border border-gray-200 rounded-lg p-3 text-center cursor-pointer hover:border-primary transition-colors">
                                        <i class="fas fa-credit-card text-2xl text-blue-500 mb-2"></i>
                                        <p class="text-sm font-medium">Credit Card</p>
                                    </div>
                                    <div class="border border-gray-200 rounded-lg p-3 text-center cursor-pointer hover:border-primary transition-colors">
                                        <i class="fas fa-mobile-alt text-2xl text-purple-500 mb-2"></i>
                                        <p class="text-sm font-medium">GCash</p>
                                    </div>
                                    <div class="border border-gray-200 rounded-lg p-3 text-center cursor-pointer hover:border-primary transition-colors">
                                        <i class="fas fa-wallet text-2xl text-green-500 mb-2"></i>
                                        <p class="text-sm font-medium">GrabPay</p>
                                    </div>
                                    <div class="border border-gray-200 rounded-lg p-3 text-center cursor-pointer hover:border-primary transition-colors">
                                        <i class="fas fa-money-bill-wave text-2xl text-red-500 mb-2"></i>
                                        <p class="text-sm font-medium">PayMaya</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-circle text-yellow-500 mt-1 mr-2"></i>
                                    <p class="text-sm text-yellow-700">You will be redirected to a secure payment page to complete your transaction.</p>
                                </div>
                            </div>
                        `;
                        break;
                    case 'self-pickup':
                        modalPaymentMethodElement.textContent = 'Pay when you pickup your order';
                        paymentInstructionsElement.innerHTML = `
                            <div class="mb-6">
                                <p class="text-textdark font-medium mb-3">Your order will be ready for pickup at:</p>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="font-medium text-primary">Rich Anne Lea Tiles Trading</p>
                                    <p class="text-sm text-textlight mt-1">123 Main Street, Quezon City</p>
                                    <p class="text-sm text-textlight">Mon-Sat: 8:00 AM - 6:00 PM</p>
                                </div>
                                <p class="text-sm text-textlight mt-4">We will notify you when your order is ready for pickup. Please bring a valid ID.</p>
                            </div>
                        `;
                        break;
                    case 'cod':
                        modalPaymentMethodElement.textContent = 'Pay with cash when your order arrives';
                        paymentInstructionsElement.innerHTML = `
                            <div class="mb-6">
                                <p class="text-textdark font-medium mb-3">Please prepare exact amount for the delivery personnel:</p>
                                <div class="bg-primary bg-opacity-10 p-4 rounded-lg border border-primary border-opacity-20">
                                    <p class="font-black text-primary text-center text-xl">₱${currentTotal}</p>
                                </div>
                                <p class="text-sm text-textlight mt-4">Our delivery personnel will provide an official receipt upon payment.</p>
                            </div>
                        `;
                        break;
                }
                
                paymentModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
            
            closeModal.addEventListener('click', function() {
                paymentModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            });
            
            // Close modal when clicking outside
            paymentModal.addEventListener('click', function(e) {
                if (e.target === paymentModal) {
                    paymentModal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            });
        });
    </script>
</body>
</html>