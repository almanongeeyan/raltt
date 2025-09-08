<?php include '../includes/headeruser.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Rich Anne Lea Tiles Trading</title>
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
        
        .cart-item {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .cart-item:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .quantity-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        .checkout-btn {
            background: #7d310a;
            box-shadow: 0 6px 24px 0 rgba(125, 49, 10, 0.3), 0 1.5px 0 #fff;
            transition: all 0.3s ease;
        }
        
        .checkout-btn:hover {
            background: #5a2207;
            box-shadow: 0 8px 28px 0 rgba(125, 49, 10, 0.4), 0 1.5px 0 #fff;
            transform: translateY(-2px);
        }
        
        .custom-checkbox input[type="checkbox"] {
            display: none;
        }
        
        .custom-checkbox .checkmark {
            width: 20px;
            height: 20px;
            border: 2px solid #cf8756;
            border-radius: 4px;
            display: inline-block;
            position: relative;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .custom-checkbox input[type="checkbox"]:checked + .checkmark {
            background-color: #cf8756;
            border-color: #cf8756;
        }
        
        .custom-checkbox input[type="checkbox"]:checked + .checkmark:after {
            content: "";
            position: absolute;
            left: 6px;
            top: 2px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        
        .delete-btn {
            transition: all 0.2s ease;
        }
        
        .delete-btn:hover {
            color: #ef4444 !important;
            transform: scale(1.1);
        }
        
        /* Mobile-specific styles */
        .mobile-product-info {
            display: flex;
            align-items: center;
            width: 100%;
        }
        
        .mobile-product-details {
            flex: 1;
            min-width: 0; /* Allows text truncation */
        }
        
        .mobile-product-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 180px;
        }
        
        .mobile-price-quantity {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-top: 8px;
        }
        
        @media (max-width: 767px) {
            .cart-item {
                padding: 12px;
            }
            
            .desktop-only {
                display: none;
            }
            
            .mobile-product-info {
                margin-bottom: 10px;
            }
            
            .mobile-price-quantity {
                border-top: 1px solid #f0f0f0;
                padding-top: 10px;
            }
        }
        
        @media (min-width: 768px) {
            .mobile-only {
                display: none;
            }
        }
    </style>
</head>
<body class="min-h-screen pt-24">
    <div class="container mx-auto px-4 py-8">
        <!-- Order Summary Tracker -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-2">
                <h1 class="text-2xl font-black text-primary">Shopping Cart</h1>
                <span class="text-sm font-medium bg-primary text-white px-3 py-1 rounded-full">Step 1 of 3</span>
            </div>
            
            <div class="flex items-center justify-between mt-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                        <span class="text-white font-bold text-sm">1</span>
                    </div>
                    <span class="ml-2 text-sm font-medium text-primary">Cart</span>
                </div>
                
                <div class="flex-1 h-1 bg-gray-200 mx-2"></div>
                
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-500 font-bold text-sm">2</span>
                    </div>
                    <span class="ml-2 text-sm font-medium text-gray-500">Order Summary</span>
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
        
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Shopping Cart Section -->
            <section class="flex-grow">
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                    <p class="text-textlight mb-6">4 items in your cart</p>

                    <!-- Select All & Item Count -->
                    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            <label class="custom-checkbox flex items-center cursor-pointer">
                                <input type="checkbox" id="select-all">
                                <span class="checkmark"></span>
                                <span class="ml-2 text-textdark font-medium">Select All Items</span>
                            </label>
                        </div>
                        <p class="text-textlight font-medium">2 of 4 selected</p>
                    </div>

                    <!-- Cart Table Header (Desktop) -->
                    <div class="desktop-only md:grid grid-cols-12 gap-4 text-textlight font-semibold mb-4 pb-3 border-b border-gray-100">
                        <div class="col-span-5">Product</div>
                        <div class="col-span-2 text-center">Unit Price</div>
                        <div class="col-span-2 text-center">Quantity</div>
                        <div class="col-span-2 text-center">Total Price</div>
                        <div class="col-span-1"></div>
                    </div>

                    <!-- Cart Items -->
                    <div class="space-y-4">
                        <!-- Cart Item 1 -->
                        <div class="cart-item bg-light border border-gray-100 p-4 md:py-4 md:px-0">
                            <div class="md:grid md:grid-cols-12 md:gap-4 flex flex-wrap items-center">
                                <div class="flex items-center space-x-4 md:col-span-5 w-full md:w-auto mb-3 md:mb-0">
                                    <label class="custom-checkbox flex items-center cursor-pointer">
                                        <input type="checkbox" checked class="item-checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <div class="w-16 h-16 bg-cover bg-center rounded-lg overflow-hidden shadow-sm">
                                        <img src="https://placehold.co/64x64/f9f5f2/7d310a" alt="Tile" class="w-full h-full object-cover">
                                    </div>
                                    <div class="mobile-product-details">
                                        <p class="font-bold text-textdark mobile-product-name">Arte Ceramiche Matte Floor Tile</p>
                                        <p class="text-sm text-textlight mobile-only">₱100 × 2 = <span class="text-primary font-bold">₱200</span></p>
                                        <p class="text-sm text-textlight desktop-only">Premium Tiles • 30x30 cm</p>
                                    </div>
                                </div>
                                <div class="desktop-only md:col-span-2 text-textdark font-bold text-center mb-3 md:mb-0">
                                    ₱100
                                </div>
                                <div class="md:col-span-2 flex justify-center mb-3 md:mb-0">
                                    <div class="flex items-center space-x-3 bg-white rounded-full py-1 px-3 shadow-sm">
                                        <button class="quantity-btn bg-light text-textdark hover:bg-secondary hover:text-white">-</button>
                                        <span class="font-bold w-6 text-center">2</span>
                                        <button class="quantity-btn bg-light text-textdark hover:bg-secondary hover:text-white">+</button>
                                    </div>
                                </div>
                                <div class="desktop-only md:col-span-2 text-primary font-black text-center mb-3 md:mb-0">
                                    ₱200
                                </div>
                                <div class="md:col-span-1 flex justify-center md:justify-end">
                                    <button class="delete-btn text-textlight hover:text-red-500">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                
                                <!-- Mobile price and quantity section -->
                                <div class="mobile-only mobile-price-quantity">
                                    <div class="text-textdark font-bold">
                                        ₱100
                                    </div>
                                    <div class="text-primary font-black">
                                        ₱200
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cart Item 2 -->
                        <div class="cart-item bg-light border border-gray-100 p-4 md:py-4 md:px-0">
                            <div class="md:grid md:grid-cols-12 md:gap-4 flex flex-wrap items-center">
                                <div class="flex items-center space-x-4 md:col-span-5 w-full md:w-auto mb-3 md:mb-0">
                                    <label class="custom-checkbox flex items-center cursor-pointer">
                                        <input type="checkbox" checked class="item-checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <div class="w-16 h-16 bg-cover bg-center rounded-lg overflow-hidden shadow-sm">
                                        <img src="https://placehold.co/64x64/f9f5f2/7d310a" alt="Tile" class="w-full h-full object-cover">
                                    </div>
                                    <div class="mobile-product-details">
                                        <p class="font-bold text-textdark mobile-product-name">Porcelain Wood-Look Tile</p>
                                        <p class="text-sm text-textlight mobile-only">₱150 × 1 = <span class="text-primary font-bold">₱150</span></p>
                                        <p class="text-sm text-textlight desktop-only">Wood Series • 15x60 cm</p>
                                    </div>
                                </div>
                                <div class="desktop-only md:col-span-2 text-textdark font-bold text-center mb-3 md:mb-0">
                                    ₱150
                                </div>
                                <div class="md:col-span-2 flex justify-center mb-3 md:mb-0">
                                    <div class="flex items-center space-x-3 bg-white rounded-full py-1 px-3 shadow-sm">
                                        <button class="quantity-btn bg-light text-textdark hover:bg-secondary hover:text-white">-</button>
                                        <span class="font-bold w-6 text-center">1</span>
                                        <button class="quantity-btn bg-light text-textdark hover:bg-secondary hover:text-white">+</button>
                                    </div>
                                </div>
                                <div class="desktop-only md:col-span-2 text-primary font-black text-center mb-3 md:mb-0">
                                    ₱150
                                </div>
                                <div class="md:col-span-1 flex justify-center md:justify-end">
                                    <button class="delete-btn text-textlight hover:text-red-500">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                
                                <!-- Mobile price and quantity section -->
                                <div class="mobile-only mobile-price-quantity">
                                    <div class="text-textdark font-bold">
                                        ₱150
                                    </div>
                                    <div class="text-primary font-black">
                                        ₱150
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cart Item 3 -->
                        <div class="cart-item bg-light border border-gray-100 p-4 md:py-4 md:px-0">
                            <div class="md:grid md:grid-cols-12 md:gap-4 flex flex-wrap items-center">
                                <div class="flex items-center space-x-4 md:col-span-5 w-full md:w-auto mb-3 md:mb-0">
                                    <label class="custom-checkbox flex items-center cursor-pointer">
                                        <input type="checkbox" class="item-checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <div class="w-16 h-16 bg-cover bg-center rounded-lg overflow-hidden shadow-sm">
                                        <img src="https://placehold.co/64x64/f9f5f2/7d310a" alt="Tile" class="w-full h-full object-cover">
                                    </div>
                                    <div class="mobile-product-details">
                                        <p class="font-bold text-textdark mobile-product-name">Marble Effect Wall Tile</p>
                                        <p class="text-sm text-textlight mobile-only">₱250 × 1 = <span class="text-primary font-bold">₱250</span></p>
                                        <p class="text-sm text-textlight desktop-only">Luxury Collection • 30x60 cm</p>
                                    </div>
                                </div>
                                <div class="desktop-only md:col-span-2 text-textdark font-bold text-center mb-3 md:mb-0">
                                    ₱250
                                </div>
                                <div class="md:col-span-2 flex justify-center mb-3 md:mb-0">
                                    <div class="flex items-center space-x-3 bg-white rounded-full py-1 px-3 shadow-sm">
                                        <button class="quantity-btn bg-light text-textdark hover:bg-secondary hover:text-white">-</button>
                                        <span class="font-bold w-6 text-center">1</span>
                                        <button class="quantity-btn bg-light text-textdark hover:bg-secondary hover:text-white">+</button>
                                    </div>
                                </div>
                                <div class="desktop-only md:col-span-2 text-primary font-black text-center mb-3 md:mb-0">
                                    ₱250
                                </div>
                                <div class="md:col-span-1 flex justify-center md:justify-end">
                                    <button class="delete-btn text-texttextlight hover:text-red-500">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                
                                <!-- Mobile price and quantity section -->
                                <div class="mobile-only mobile-price-quantity">
                                    <div class="text-textdark font-bold">
                                        ₱250
                                    </div>
                                    <div class="text-primary font-black">
                                        ₱250
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cart Item 4 -->
                        <div class="cart-item bg-light border border-gray-100 p-4 md:py-4 md:px-0">
                            <div class="md:grid md:grid-cols-12 md:gap-4 flex flex-wrap items-center">
                                <div class="flex items-center space-x-4 md:col-span-5 w-full md:w-auto mb-3 md:mb-0">
                                    <label class="custom-checkbox flex items-center cursor-pointer">
                                        <input type="checkbox" class="item-checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <div class="w-16 h-16 bg-cover bg-center rounded-lg overflow-hidden shadow-sm">
                                        <img src="https://placehold.co/64x64/f9f5f2/7d310a" alt="Tile" class="w-full h-full object-cover">
                                    </div>
                                    <div class="mobile-product-details">
                                        <p class="font-bold text-textdark mobile-product-name">Mosaic Bathroom Tile</p>
                                        <p class="text-sm text-textlight mobile-only">₱80 × 3 = <span class="text-primary font-bold">₱240</span></p>
                                        <p class="text-sm text-textlight desktop-only">Aqua Series • 10x10 cm</p>
                                    </div>
                                </div>
                                <div class="desktop-only md:col-span-2 text-textdark font-bold text-center mb-3 md:mb-0">
                                    ₱80
                                </div>
                                <div class="md:col-span-2 flex justify-center mb-3 md:mb-0">
                                    <div class="flex items-center space-x-3 bg-white rounded-full py-1 px-3 shadow-sm">
                                        <button class="quantity-btn bg-light text-textdark hover:bg-secondary hover:text-white">-</button>
                                        <span class="font-bold w-6 text-center">3</span>
                                        <button class="quantity-btn bg-light text-textdark hover:bg-secondary hover:text-white">+</button>
                                    </div>
                                </div>
                                <div class="desktop-only md:col-span-2 text-primary font-black text-center mb-3 md:mb-0">
                                    ₱240
                                </div>
                                <div class="md:col-span-1 flex justify-center md:justify-end">
                                    <button class="delete-btn text-textlight hover:text-red-500">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                
                                <!-- Mobile price and quantity section -->
                                <div class="mobile-only mobile-price-quantity">
                                    <div class="text-textdark font-bold">
                                        ₱80
                                    </div>
                                    <div class="text-primary font-black">
                                        ₱240
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Continue Shopping Button -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <a href="products.php" class="flex items-center justify-center text-secondary font-bold hover:text-primary transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Continue Shopping
                    </a>
                </div>
            </section>

            <!-- Order Summary Sidebar -->
            <aside class="w-full md:w-96">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-28">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-white"></i>
                        </div>
                        <h2 class="text-xl font-black text-primary">Order Summary</h2>
                    </div>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between">
                            <span class="text-textlight">Selected Items (2)</span>
                            <span class="font-medium text-textdark">₱350</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-textlight">Shipping</span>
                            <span class="font-medium text-textdark">₱40</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-textlight">Tax</span>
                            <span class="font-medium text-textdark">₱21</span>
                        </div>
                    </div>

                    <div class="flex justify-between font-black text-lg border-t border-gray-100 pt-4 mb-6">
                        <span class="text-primary">Total</span>
                        <span class="text-primary">₱411</span>
                    </div>
                    
                    <form action="order_summary.php" method="get">
                        <button type="submit" class="checkout-btn w-full py-4 rounded-xl font-black text-white text-lg tracking-wide">
                            <i class="fas fa-lock mr-2"></i>Checkout Now
                        </button>
                    </form>
                    
                    <div class="mt-6 text-center">
                        <p class="text-textlight text-sm">Free shipping on orders over ₱1000</p>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all functionality
            const selectAll = document.getElementById('select-all');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            
            selectAll.addEventListener('change', function() {
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAll.checked;
                });
                updateSelectionCount();
            });
            
            itemCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelectAllState();
                    updateSelectionCount();
                });
            });
            
            function updateSelectAllState() {
                const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
                selectAll.checked = checkedCount === itemCheckboxes.length;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < itemCheckboxes.length;
            }
            
            function updateSelectionCount() {
                const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
                document.querySelector('p:contains("selected")').textContent = `${checkedCount} of ${itemCheckboxes.length} selected`;
            }
            
            // Quantity buttons functionality
            const quantityButtons = document.querySelectorAll('.quantity-btn');
            quantityButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const parent = this.closest('.flex.items-center');
                    const quantitySpan = parent.querySelector('span');
                    let quantity = parseInt(quantitySpan.textContent);
                    
                    if (this.textContent === '+') {
                        quantity++;
                    } else if (this.textContent === '-' && quantity > 1) {
                        quantity--;
                    }
                    
                    quantitySpan.textContent = quantity;
                    
                    // Update total price for this item
                    const unitPrice = parseFloat(parent.closest('.cart-item').querySelector('.text-textdark.font-bold').textContent.replace('₱', ''));
                    const totalElement = parent.closest('.cart-item').querySelector('.text-primary');
                    totalElement.textContent = '₱' + (unitPrice * quantity);
                    
                    // Update mobile display
                    const mobilePriceDisplay = parent.closest('.cart-item').querySelector('.mobile-only .text-primary');
                    if (mobilePriceDisplay) {
                        mobilePriceDisplay.textContent = '₱' + (unitPrice * quantity);
                    }
                });
            });
            
            // Delete button functionality
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const cartItem = this.closest('.cart-item');
                    cartItem.style.opacity = '0';
                    setTimeout(() => {
                        cartItem.remove();
                    }, 300);
                });
            });
        });
    </script>
</body>
</html>