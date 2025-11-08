<?php
include '../includes/headeruser.php';
require_once '../connection/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

function getCartItems($conn, $user_id) {
    $branch_id = isset($_SESSION['branch_id']) ? intval($_SESSION['branch_id']) : 1;
    $stmt = $conn->prepare('
        SELECT ci.cart_item_id, ci.product_id, ci.quantity, p.product_name, p.product_price, p.product_image, p.product_description, p.is_archived
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.product_id
        JOIN product_branches pb ON p.product_id = pb.product_id
        WHERE ci.user_id = ? AND pb.branch_id = ?
    ');
    $stmt->execute([$user_id, $branch_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCartSummary($cartItems) {
    $selectedCount = count($cartItems);
    $subtotal = 0;
    foreach ($cartItems as $item) {
        $subtotal += $item['product_price'] * $item['quantity'];
    }
    $shipping = ($subtotal >= 1000) ? 0 : ($subtotal > 0 ? 40 : 0);
    $total = $subtotal + $shipping;
    return [
        'selectedCount' => $selectedCount,
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'total' => $total
    ];
}

$cartItems = getCartItems($conn, $user_id);
$summary = getCartSummary($cartItems);
?>

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
            background: linear-gradient(135deg, #fdf8f5 0%, #f9f5f2 60%, #f0e6df 100%);
            min-height: 100vh;
        }
        
        .cart-item {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .cart-item:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
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
            transition: all 0.3s ease;
        }
        
        .checkout-btn:hover:not(:disabled) {
            background: #5a2207;
            transform: translateY(-1px);
        }
        
        .checkout-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
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
        
        /* Modal Styles */
        .modal-overlay {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
        
        .modal-content {
            animation: modalSlideIn 0.3s ease-out;
        }
        
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        /* Toast Styles */
        .toast {
            animation: toastSlideIn 0.3s ease-out;
        }
        
        @keyframes toastSlideIn {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Mobile-specific styles */
        .mobile-product-info {
            display: flex;
            align-items: center;
            width: 100%;
        }
        
        .mobile-product-details {
            flex: 1;
            min-width: 0;
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
                <h1 class="text-2xl font-bold text-primary">Shopping Cart</h1>
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
                    <p class="text-textlight mb-6" id="cart-count-text"><?php echo count($cartItems); ?> items in your cart</p>

                    <!-- Select All & Item Count -->
                    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            <label class="custom-checkbox flex items-center cursor-pointer">
                                <input type="checkbox" id="select-all">
                                <span class="checkmark"></span>
                                <span class="ml-2 text-textdark font-medium">Select All Items</span>
                            </label>
                        </div>
                        <p class="text-textlight font-medium" id="selected-count">All selected</p>
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
                    <div class="space-y-4" id="cart-items-container">
                    <?php if (empty($cartItems)): ?>
                        <div class="text-center text-textlight py-12">
                            <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                            <p class="text-lg">Your cart is empty.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item bg-light border border-gray-100 p-4 md:py-4 md:px-0 relative <?php echo ($item['is_archived'] ? 'archived-cart-item' : ''); ?>" 
                            data-cart-item-id="<?php echo $item['cart_item_id']; ?>"
                            data-product-id="<?php echo $item['product_id']; ?>">
                            <div class="md:grid md:grid-cols-12 md:gap-4 flex flex-wrap items-center">
                                <div class="flex items-center space-x-4 md:col-span-5 w-full md:w-auto mb-3 md:mb-0">
                                    <label class="custom-checkbox flex items-center cursor-pointer">
                                        <input type="checkbox" class="item-checkbox" <?php echo ($item['is_archived'] ? 'disabled' : ''); ?>>
                                        <span class="checkmark"></span>
                                    </label>
                                    <div class="w-16 h-16 bg-cover bg-center rounded-lg overflow-hidden shadow-sm">
                                        <?php
                                            if (!empty($item['product_image'])) {
                                                if (is_string($item['product_image']) && strpos($item['product_image'], 'data:image') === 0) {
                                                    echo '<img src="' . $item['product_image'] . '" alt="Tile" class="w-full h-full object-cover">';
                                                } elseif (is_string($item['product_image']) && (str_ends_with($item['product_image'], '.jpg') || str_ends_with($item['product_image'], '.png'))) {
                                                    echo '<img src="../images/' . htmlspecialchars($item['product_image']) . '" alt="Tile" class="w-full h-full object-cover">';
                                                } else {
                                                    $imgData = base64_encode($item['product_image']);
                                                    echo '<img src="data:image/jpeg;base64,' . $imgData . '" alt="Tile" class="w-full h-full object-cover">';
                                                }
                                            } else {
                                                echo '<img src="../images/user/tile1.jpg" alt="Tile" class="w-full h-full object-cover">';
                                            }
                                        ?>
                                    </div>
                                    <div class="mobile-product-details">
                                        <p class="font-semibold text-textdark mobile-product-name"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                        <p class="text-sm text-textlight mobile-only">₱<?php echo number_format($item['product_price'], 2); ?> × <?php echo $item['quantity']; ?> = <span class="text-primary font-bold">₱<?php echo number_format($item['product_price'] * $item['quantity'], 2); ?></span></p>
                                    </div>
                                </div>
                                <div class="desktop-only md:col-span-2 text-textdark font-semibold text-center mb-3 md:mb-0">
                                    ₱<?php echo number_format($item['product_price'], 2); ?>
                                </div>
                                <div class="md:col-span-2 flex justify-center mb-3 md:mb-0">
                                    <div class="flex items-center space-x-3 bg-white rounded-full py-1 px-3 shadow-sm" data-cart-item-id="<?php echo $item['cart_item_id']; ?>" data-unit-price="<?php echo $item['product_price']; ?>">
                                        <button class="quantity-btn bg-light text-textdark hover:bg-secondary hover:text-white" data-action="decrease" <?php echo ($item['is_archived'] ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : ''); ?>>-</button>
                                        <input type="number" min="1" maxlength="4" class="font-semibold w-12 text-center quantity-value-input" style="color:#333;background:transparent;border:none;outline:none;<?php echo ($item['is_archived'] ? 'background:#eee;pointer-events:none;' : ''); ?>" value="<?php echo $item['quantity']; ?>" data-cart-item-id="<?php echo $item['cart_item_id']; ?>" <?php echo ($item['is_archived'] ? 'disabled' : ''); ?>>
                                        <button class="quantity-btn bg-light text-textdark hover:bg-secondary hover:text-white" data-action="increase" <?php echo ($item['is_archived'] ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : ''); ?>>+</button>
                                    </div>
                                </div>
                                <div class="desktop-only md:col-span-2 text-primary font-bold text-center mb-3 md:mb-0 item-total-price">
                                    ₱<?php echo number_format($item['product_price'] * $item['quantity'], 2); ?>
                                </div>
                                <div class="md:col-span-1 flex justify-center md:justify-end">
                                    <button class="delete-btn text-textlight hover:text-red-500" data-cart-item-id="<?php echo $item['cart_item_id']; ?>" data-product-name="<?php echo htmlspecialchars($item['product_name']); ?>" <?php echo ($item['is_archived'] ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : ''); ?>>
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                <?php if ($item['is_archived']): ?>
                                    <div class="archived-overlay"></div>
                                    <div class="archived-message">
                                        <span>This product is archived and cannot be purchased</span>
                                    </div>
                                <?php endif; ?>
    <style>
        .archived-cart-item {
            opacity: 0.7;
            filter: grayscale(0.7);
            pointer-events: none;
        }
        .archived-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(120,120,120,0.25);
            z-index: 2;
            border-radius: 12px;
        }
        .archived-message {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 3;
            background: rgba(80,80,80,0.92);
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0,0,0,0.10);
        }
    </style>
                                </div>
                                <!-- Mobile price and quantity section -->
                                <div class="mobile-only mobile-price-quantity">
                                    <div class="text-textdark font-semibold">
                                        ₱<?php echo number_format($item['product_price'], 2); ?>
                                    </div>
                                    <div class="text-primary font-bold">
                                        ₱<?php echo number_format($item['product_price'] * $item['quantity'], 2); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </div>
                </div>
                
                <!-- Continue Shopping Button -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <a href="products.php" class="flex items-center justify-center text-secondary font-semibold hover:text-primary transition-colors">
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
                        <h2 class="text-xl font-bold text-primary">Order Summary</h2>
                    </div>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between">
                            <span class="text-textlight order-summary-selected">Selected Items (<?php echo $summary['selectedCount']; ?>)</span>
                            <span class="font-medium text-textdark order-summary-subtotal">₱<?php echo number_format($summary['subtotal'], 2); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-textlight">Shipping</span>
                            <span class="font-medium text-textdark order-summary-shipping">₱<?php echo number_format($summary['shipping'], 2); ?></span>
                        </div>
                    </div>

                    <div class="flex justify-between font-bold text-lg border-t border-gray-100 pt-4 mb-6">
                        <span class="text-primary">Total</span>
                        <span class="text-primary order-summary-total">₱<?php echo number_format($summary['total'], 2); ?></span>
                    </div>
                    
                    <form id="checkoutForm" action="order_summary.php" method="post">
                        <input type="hidden" name="selected_cart_items" id="selected_cart_items">
                        <button type="submit" id="checkoutBtn" class="checkout-btn w-full py-3 rounded-xl font-semibold text-white text-base tracking-wide transition-all duration-200" disabled>
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

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center modal-overlay hidden">
        <div class="modal-content bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm mx-4">
            <div class="flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-2xl text-red-500"></i>
            </div>
            <h2 class="text-xl font-bold text-center text-textdark mb-2">Delete Item</h2>
            <p class="text-textlight text-center mb-6" id="deleteModalText">Are you sure you want to delete this item from your cart?</p>
            <div class="flex justify-center space-x-3">
                <button id="cancelDeleteBtn" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-colors duration-200">
                    Cancel
                </button>
                <button id="confirmDeleteBtn" class="px-6 py-2.5 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 transition-colors duration-200">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div id="successToast" class="fixed top-6 right-6 z-[9999] bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg font-medium flex items-center gap-3 toast hidden">
        <i class="fas fa-check-circle text-xl"></i>
        <span id="toastMessage"></span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let deleteItemId = null;
            let deleteItemName = null;

            // Delete button click handlers
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    deleteItemId = this.getAttribute('data-cart-item-id');
                    deleteItemName = this.getAttribute('data-product-name');
                    
                    // Update modal text
                    document.getElementById('deleteModalText').textContent = 
                        `Are you sure you want to remove "${deleteItemName}" from your cart?`;
                    
                    // Show modal
                    document.getElementById('deleteModal').classList.remove('hidden');
                });
            });

            // Modal button handlers
            document.getElementById('cancelDeleteBtn').addEventListener('click', function() {
                document.getElementById('deleteModal').classList.add('hidden');
                deleteItemId = null;
                deleteItemName = null;
            });

            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                if (!deleteItemId) return;
                
                // Show loading state
                const confirmBtn = document.getElementById('confirmDeleteBtn');
                const originalText = confirmBtn.innerHTML;
                confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Deleting...';
                confirmBtn.disabled = true;

                // AJAX request to delete item
                fetch('../connection/delete_cart_item.php', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ cart_item_id: deleteItemId })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Remove item from DOM with animation
                        const cartItem = document.querySelector(`[data-cart-item-id="${deleteItemId}"]`);
                        if (cartItem) {
                            cartItem.style.opacity = '0';
                            cartItem.style.transform = 'translateX(100%)';
                            setTimeout(() => {
                                cartItem.remove();
                                updateSummary();
                                updateSelectionCount();
                                updateCartCount();
                                
                                // Show success toast
                                showToast('Item successfully removed from cart');
                                
                                // Check if cart is empty
                                if (document.querySelectorAll('.cart-item').length === 0) {
                                    document.getElementById('cart-items-container').innerHTML = `
                                        <div class="text-center text-textlight py-12">
                                            <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                                            <p class="text-lg">Your cart is empty.</p>
                                        </div>
                                    `;
                                    document.getElementById('cart-count-text').textContent = '0 items in your cart';
                                }
                            }, 300);
                        }
                    } else {
                        showToast('Failed to remove item from cart');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Error removing item from cart');
                })
                .finally(() => {
                    // Hide modal and reset state
                    document.getElementById('deleteModal').classList.add('hidden');
                    confirmBtn.innerHTML = originalText;
                    confirmBtn.disabled = false;
                    deleteItemId = null;
                    deleteItemName = null;
                });
            });

            // Toast function
            function showToast(message) {
                const toast = document.getElementById('successToast');
                const toastMessage = document.getElementById('toastMessage');
                
                toastMessage.textContent = message;
                toast.classList.remove('hidden');
                
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 3000);
            }

            // Select all functionality
            function getItemCheckboxes() {
                return Array.from(document.querySelectorAll('.item-checkbox'));
            }
            
            const selectAll = document.getElementById('select-all');

            function updateSelectAllState() {
                const itemCheckboxes = getItemCheckboxes();
                const checkedCount = itemCheckboxes.filter(cb => cb.checked).length;
                selectAll.checked = checkedCount === itemCheckboxes.length && itemCheckboxes.length > 0;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < itemCheckboxes.length;
            }

            function updateSelectionCount() {
                const itemCheckboxes = getItemCheckboxes();
                const checkedCount = itemCheckboxes.filter(cb => cb.checked).length;
                document.getElementById('selected-count').textContent = `${checkedCount} of ${itemCheckboxes.length} selected`;
                
                // Enable/disable checkout button
                const checkoutBtn = document.getElementById('checkoutBtn');
                if (checkoutBtn) {
                    checkoutBtn.disabled = checkedCount === 0;
                    checkoutBtn.classList.toggle('opacity-50', checkedCount === 0);
                    checkoutBtn.classList.toggle('cursor-not-allowed', checkedCount === 0);
                }
            }

            function updateCartCount() {
                const count = document.querySelectorAll('.cart-item').length;
                const text = `${count} item${count !== 1 ? 's' : ''} in your cart`;
                document.getElementById('cart-count-text').textContent = text;
            }

            selectAll.addEventListener('change', function() {
                getItemCheckboxes().forEach(checkbox => {
                    if (!checkbox.disabled) {
                        checkbox.checked = selectAll.checked;
                    }
                });
                updateSelectionCount();
                updateSummary();
            });

            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('item-checkbox')) {
                    updateSelectAllState();
                    updateSelectionCount();
                    updateSummary();
                }
            });

            // Quantity buttons functionality with AJAX

            document.querySelectorAll('.flex.items-center[data-cart-item-id]').forEach(parent => {
                const cartItemId = parent.getAttribute('data-cart-item-id');
                const unitPrice = parseFloat(parent.getAttribute('data-unit-price'));
                const minusBtn = parent.querySelector('.quantity-btn[data-action="decrease"]');
                const plusBtn = parent.querySelector('.quantity-btn[data-action="increase"]');
                const quantityInput = parent.querySelector('.quantity-value-input');

                minusBtn.addEventListener('click', function() {
                    let quantity = parseInt(quantityInput.value);
                    if (quantity > 1) {
                        quantity--;
                        updateQuantity(cartItemId, quantity, quantityInput, parent, unitPrice);
                    }
                });

                plusBtn.addEventListener('click', function() {
                    let quantity = parseInt(quantityInput.value);
                    quantity++;
                    updateQuantity(cartItemId, quantity, quantityInput, parent, unitPrice);
                });

                // Real-time update on input change (on blur or enter)
                // Real-time update on input (while typing)
                let inputTimeout;
                quantityInput.addEventListener('input', function() {
                    // Clear any existing timeout
                    clearTimeout(inputTimeout);
                    
                    // Enforce max 4 digits
                    if (quantityInput.value.length > 4) {
                        quantityInput.value = quantityInput.value.slice(0, 4);
                    }
                    let quantity = parseInt(quantityInput.value);
                    if (isNaN(quantity) || quantity < 1) quantity = 1;
                    
                    // Set a new timeout for the stock check
                    inputTimeout = setTimeout(() => {
                        updateQuantity(cartItemId, quantity, quantityInput, parent, unitPrice);
                    }, 500); // Wait 500ms after typing stops
                    
                    // Update UI immediately for responsiveness
                    const cartItem = parent.closest('.cart-item');
                    const totalElement = cartItem.querySelector('.item-total-price');
                    if (totalElement) {
                        totalElement.textContent = '₱' + (unitPrice * quantity).toLocaleString(undefined, {minimumFractionDigits:2});
                    }
                    const mobilePriceDisplay = cartItem.querySelector('.mobile-only .text-primary');
                    if (mobilePriceDisplay) {
                        mobilePriceDisplay.textContent = '₱' + (unitPrice * quantity).toLocaleString(undefined, {minimumFractionDigits:2});
                    }
                    updateSummary();
                });

                quantityInput.addEventListener('change', function() {
                    let quantity = parseInt(quantityInput.value);
                    if (isNaN(quantity) || quantity < 1) {
                        quantity = 1;
                        quantityInput.value = 1;
                    }
                    updateQuantity(cartItemId, quantity, quantityInput, parent, unitPrice);
                });
                quantityInput.addEventListener('keyup', function(e) {
                    if (e.key === 'Enter') {
                        let quantity = parseInt(quantityInput.value);
                        if (isNaN(quantity) || quantity < 1) {
                            quantity = 1;
                            quantityInput.value = 1;
                        }
                        updateQuantity(cartItemId, quantity, quantityInput, parent, unitPrice);
                    }
                });
            });

            async function updateQuantity(cartItemId, quantity, quantityInput, parent, unitPrice) {
                const cartItem = parent.closest('.cart-item');
                const productId = cartItem.getAttribute('data-product-id');
                
                // First check stock availability
                try {
                    const stockResponse = await fetch('processes/check_stock.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `product_id=${productId}`
                    });
                    const stockData = await stockResponse.json();
                    
                    if (stockData.success) {
                        const stockCount = parseInt(stockData.stock_count);
                        
                        // If quantity exceeds stock, adjust it
                        if (quantity > stockCount) {
                            quantity = stockCount;
                            showToast('Quantity adjusted to available stock');
                        }
                        
                        // Update quantity input and UI
                        quantityInput.value = quantity;
                        quantityInput.setAttribute('max', stockCount);
                        
                        const totalElement = cartItem.querySelector('.item-total-price');
                        if (totalElement) {
                            totalElement.textContent = '₱' + (unitPrice * quantity).toLocaleString(undefined, {minimumFractionDigits:2});
                        }
                        
                        const mobilePriceDisplay = cartItem.querySelector('.mobile-only .text-primary');
                        if (mobilePriceDisplay) {
                            mobilePriceDisplay.textContent = '₱' + (unitPrice * quantity).toLocaleString(undefined, {minimumFractionDigits:2});
                        }
                        
                        updateSummary();

                        // Then update on server
                        await fetch('processes/update_cart_quantity.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `cart_item_id=${cartItemId}&quantity=${quantity}`
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast('Failed to update quantity');
                }
            }

            // Dynamic summary calculation
            function updateSummary() {
                let subtotal = 0;
                let selectedCount = 0;

                document.querySelectorAll('.cart-item').forEach(item => {
                    const checkbox = item.querySelector('.item-checkbox');
                    if (checkbox && checkbox.checked) {
                        const parent = item.querySelector('.flex.items-center[data-cart-item-id]');
                        const unitPrice = parseFloat(parent.getAttribute('data-unit-price'));
                        // Use input value for quantity
                        const quantityInput = parent.querySelector('.quantity-value-input');
                        const quantity = parseInt(quantityInput.value);
                        subtotal += unitPrice * (isNaN(quantity) ? 1 : quantity);
                        selectedCount++;
                    }
                });

                const shipping = (subtotal >= 1000) ? 0 : (subtotal > 0 ? 40 : 0);
                const total = subtotal + shipping;

                document.querySelector('.order-summary-selected').textContent = `Selected Items (${selectedCount})`;
                document.querySelector('.order-summary-subtotal').textContent = '₱' + subtotal.toLocaleString(undefined, {minimumFractionDigits:2});
                document.querySelector('.order-summary-shipping').textContent = '₱' + shipping.toLocaleString(undefined, {minimumFractionDigits:2});
                document.querySelector('.order-summary-total').textContent = '₱' + total.toLocaleString(undefined, {minimumFractionDigits:2});
            }

            // Checkout form handler
            document.getElementById('checkoutForm').addEventListener('submit', function(e) {
                // Collect selected cart item IDs and check stock before allowing checkout
                const selected = Array.from(document.querySelectorAll('.cart-item .item-checkbox:checked'))
                    .map(cb => cb.closest('.cart-item'));

                if (selected.length === 0) {
                    e.preventDefault();
                    showToast('Please select at least one item to checkout');
                    return;
                }

                // Prevent default while we check stock
                e.preventDefault();

                // Check stock for all selected items before submitting
                let stockCheckPromises = [];
                let exceeded = false;
                let exceededItems = [];

                selected.forEach(cartItem => {
                    const productId = cartItem.getAttribute('data-product-id');
                    const cartItemId = cartItem.getAttribute('data-cart-item-id');
                    const quantityInput = cartItem.querySelector('.quantity-value-input');
                    const quantity = parseInt(quantityInput.value);
                    stockCheckPromises.push(
                        fetch('processes/check_stock.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `product_id=${productId}`
                        })
                        .then(res => res.json())
                        .then(stockData => {
                            const stockCount = parseInt(stockData.stock_count);
                            if (quantity > stockCount) {
                                exceeded = true;
                                exceededItems.push({cartItem, cartItemId, quantityInput, stockCount});
                            }
                        })
                    );
                });

                Promise.all(stockCheckPromises).then(() => {
                    if (exceeded) {
                        // For each exceeded item, update UI and backend
                        exceededItems.forEach(({cartItemId, quantityInput, stockCount}) => {
                            quantityInput.value = stockCount;
                            quantityInput.setAttribute('max', stockCount);
                            // Update backend
                            fetch('processes/update_cart_quantity.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: `cart_item_id=${cartItemId}&quantity=${stockCount}`
                            });
                        });
                        showToast('Some items exceeded available stock and were adjusted. Please review your cart.');
                        updateSummary();
                        return;
                    } else {
                        // All good, proceed with checkout
                        const selectedIds = selected.map(item => item.getAttribute('data-cart-item-id'));
                        document.getElementById('selected_cart_items').value = JSON.stringify(selectedIds);
                        // Submit the form programmatically
                        document.getElementById('checkoutForm').submit();
                    }
                });
            });

            // Initialize
            updateSelectionCount();
            updateSummary();
            updateCartCount();
        });
    </script>
</body>
</html>