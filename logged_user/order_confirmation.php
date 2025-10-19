<?php
// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../includes/headeruser.php';
require_once '../connection/connection.php';

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Get the latest order for this user (just placed)
$order = null;
$order_items = [];
if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($order) {
        $order_id = $order['order_id'];
        $order_reference = $order['order_reference'];
        $payment_method = $order['payment_method'] === 'pick_up' ? 'self-pickup' : $order['payment_method'];
        $order_total = $order['original_subtotal'];
        $shipping_fee = $order['shipping_fee'];
        $final_total = $order['total_amount'];
        $coins_redeemed = $order['coins_redeemed'];
        $order_date = $order['order_date'];
        // Get order items
        $stmt = $conn->prepare("SELECT oi.*, p.product_name, p.product_image, p.product_description FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?");
        $stmt->execute([$order_id]);
        $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Get branch info if available
        $branch_name = isset($order['branch_name']) ? $order['branch_name'] : 'Main Branch';
        $branch_address = isset($order['branch_address']) ? $order['branch_address'] : '123 Main Street, Quezon City, Metro Manila';
    } else {
        // Set branch variables to avoid undefined variable warning
        $branch_name = 'Main Branch';
        $branch_address = '123 Main Street, Quezon City, Metro Manila';
        echo '<div style="padding:2em;text-align:center;color:red;font-size:1.2em;">Error: No recent order found.<br>Please go back to your cart and try again.</div>';
        exit();
    }
    // Get user info
    $stmt = $conn->prepare("SELECT full_name, email, phone_number, house_address, full_address FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user_info) {
        echo '<div style="padding:2em;text-align:center;color:red;font-size:1.2em;">Error: User information not found.<br>Please log in again.</div>';
        exit();
    }
} else {
    echo '<div style="padding:2em;text-align:center;color:red;font-size:1.2em;">Error: User not logged in.<br>Please log in again.</div>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Rich Anne Lea Tiles Trading</title>
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
        
        .confirmation-card {
            transition: all 0.3s ease;
            border-radius: 16px;
            overflow: hidden;
        }
        
        .confirmation-card:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        
        .success-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .payment-badge {
            background: linear-gradient(135deg, #7d310a 0%, #cf8756 100%);
        }
        
        .action-btn {
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
        }
        
        .print-btn {
            background: linear-gradient(135deg, #374151 0%, #6b7280 100%);
        }
        
        .print-btn:hover {
            background: linear-gradient(135deg, #6b7280 0%, #374151 100%);
        }
        
        .continue-btn {
            background: linear-gradient(135deg, #7d310a 0%, #cf8756 100%);
        }
        
        .continue-btn:hover {
            background: linear-gradient(135deg, #cf8756 0%, #7d310a 100%);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Print Styles */
        @media print {
            body * {
                visibility: hidden;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .print-receipt,
            .print-receipt * {
                visibility: visible;
            }
            
            .print-receipt {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                background: white !important;
                font-size: 12px !important;
            }
            
            .no-print {
                display: none !important;
            }
            
            .receipt-header {
                background: #7d310a !important;
                color: white !important;
                padding: 1rem !important;
                text-align: center !important;
            }
            
            .receipt-body {
                padding: 1rem !important;
                color: #333 !important;
            }
            
            .receipt-footer {
                padding: 1rem !important;
                border-top: 2px dashed #ccc !important;
                text-align: center !important;
                color: #666 !important;
            }
            
            .receipt-item {
                border-bottom: 1px dashed #eee !important;
                padding: 0.5rem 0 !important;
            }
            
            .receipt-totals {
                border-top: 2px solid #333 !important;
                padding-top: 0.5rem !important;
                margin-top: 0.5rem !important;
            }
        }

        /* Receipt Styling */
        .receipt-container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .receipt-header {
            background: linear-gradient(135deg, #7d310a 0%, #cf8756 100%);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .receipt-body {
            padding: 1.5rem;
            color: #333;
        }

        .receipt-footer {
            padding: 1rem 1.5rem;
            border-top: 2px dashed #ccc;
            text-align: center;
            color: #666;
            font-size: 0.875rem;
        }

        .receipt-item {
            border-bottom: 1px dashed #eee;
            padding: 0.75rem 0;
        }

        .receipt-totals {
            border-top: 2px solid #333;
            padding-top: 1rem;
            margin-top: 1rem;
        }

        .barcode {
            font-family: 'Libre Barcode 39', monospace;
            font-size: 2rem;
            letter-spacing: 2px;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen pt-24">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Progress Steps (Order Summary Tracker) -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 no-print">
                <div class="flex items-center justify-between mb-2">
                    <h1 class="text-2xl font-bold text-primary">Order Confirmation</h1>
                    <span class="text-sm font-medium bg-primary text-white px-3 py-1 rounded-full">Step 3 of 3</span>
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
                    <div class="flex-1 h-1 bg-primary mx-2"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                            <span class="text-white font-bold text-sm">3</span>
                        </div>
                        <span class="ml-2 text-sm font-medium text-primary">Confirmation</span>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Main Content -->
                <section class="flex-grow">
                    <!-- Success Confirmation -->
                    <div class="confirmation-card bg-white rounded-2xl shadow-lg p-6 mb-6 animate-fade-in-up">
                        <div class="flex flex-col md:flex-row items-center justify-between">
                            <div class="flex items-center mb-4 md:mb-0">
                                <div class="w-16 h-16 success-badge rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-check text-white text-2xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-textdark">Order Confirmed!</h2>
                                    <p class="text-textlight">Thank you for your purchase</p>
                                </div>
                            </div>
                            <div class="text-center md:text-right">
                                <p class="text-sm text-textlight">Order Reference</p>
                                <p class="text-lg font-bold text-primary"><?php echo $order_reference; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Method Details -->
                    <div class="confirmation-card bg-white rounded-2xl shadow-lg p-6 mb-6">
                        <h2 class="text-xl font-bold text-primary mb-4">Payment Information</h2>
                        
                        <div class="flex items-center justify-between mb-4 p-4 bg-light rounded-xl">
                            <div class="flex items-center">
                                <div class="w-12 h-12 payment-badge rounded-full flex items-center justify-center mr-4">
                                    <?php if ($payment_method === 'gcash'): ?>
                                        <i class="fas fa-mobile-alt text-white text-xl"></i>
                                    <?php elseif ($payment_method === 'cod'): ?>
                                        <i class="fas fa-money-bill-wave text-white text-xl"></i>
                                    <?php elseif ($payment_method === 'self-pickup'): ?>
                                        <i class="fas fa-store text-white text-xl"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="font-semibold text-textdark">
                                        <?php
                                            switch($payment_method) {
                                                case 'gcash': echo 'GCash Payment'; break;
                                                case 'cod': echo 'Cash on Delivery'; break;
                                                case 'self-pickup': echo 'Self Pickup'; break;
                                            }
                                        ?>
                                    </p>
                                    <p class="text-sm text-textlight">
                                        <?php
                                            switch($payment_method) {
                                                case 'gcash': echo 'Payment processed through GCash'; break;
                                                case 'cod': echo 'Pay when your order arrives'; break;
                                                case 'self-pickup': echo 'Pay when you pickup your order'; break;
                                            }
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-primary text-white text-sm font-medium rounded-full">
                                <?php echo ucfirst(str_replace('-', ' ', $payment_method)); ?>
                            </span>
                        </div>
                        
                        <!-- Dynamic Payment Instructions -->
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <h3 class="font-semibold text-textdark mb-3">Next Steps</h3>
                            <?php if ($payment_method === 'gcash'): ?>
                                <div class="space-y-3">
                                    <div class="flex items-start">
                                        <i class="fas fa-mobile-alt text-primary mt-1 mr-3"></i>
                                        <div>
                                            <p class="font-medium text-textdark">Complete GCash Payment</p>
                                            <p class="text-sm text-textlight">Check your GCash app for payment notification</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <i class="fas fa-qrcode text-primary mt-1 mr-3"></i>
                                        <div>
                                            <p class="font-medium text-textdark">Scan QR Code</p>
                                            <p class="text-sm text-textlight">Use the QR code sent to your email</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <i class="fas fa-clock text-primary mt-1 mr-3"></i>
                                        <div>
                                            <p class="font-medium text-textdark">Processing Time</p>
                                            <p class="text-sm text-textlight">Payment confirmation within 1-2 hours</p>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif ($payment_method === 'cod'): ?>
                                <div class="space-y-3">
                                    <div class="flex items-start">
                                        <i class="fas fa-truck text-primary mt-1 mr-3"></i>
                                        <div>
                                            <p class="font-medium text-textdark">Wait for Delivery</p>
                                            <p class="text-sm text-textlight">Our delivery team will contact you within 24 hours</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <i class="fas fa-money-bill-wave text-primary mt-1 mr-3"></i>
                                        <div>
                                            <p class="font-medium text-textdark">Prepare Exact Amount</p>
                                            <p class="text-sm text-textlight">Please prepare ₱<?php echo number_format($final_total, 2); ?> in cash</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <i class="fas fa-receipt text-primary mt-1 mr-3"></i>
                                        <div>
                                            <p class="font-medium text-textdark">Official Receipt</p>
                                            <p class="text-sm text-textlight">Delivery personnel will provide official receipt</p>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif ($payment_method === 'self-pickup'): ?>
                                <div class="space-y-3">
                                    <div class="flex items-start">
                                        <i class="fas fa-store text-primary mt-1 mr-3"></i>
                                        <div>
                                            <p class="font-medium text-textdark">Visit Our Store</p>
                                            <p class="text-sm text-textlight">Pick up your order at our main branch</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <i class="fas fa-clock text-primary mt-1 mr-3"></i>
                                        <div>
                                            <p class="font-medium text-textdark">Business Hours</p>
                                            <p class="text-sm text-textlight">Monday - Saturday, 8:00 AM - 6:00 PM</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <i class="fas fa-map-marker-alt text-primary mt-1 mr-3"></i>
                                        <div>
                                            <p class="font-medium text-textdark">Store Location</p>
                                            <p class="text-sm text-textlight">123 Main Street, Quezon City, Metro Manila</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <i class="fas fa-id-card text-primary mt-1 mr-3"></i>
                                        <div>
                                            <p class="font-medium text-textdark">Bring Valid ID</p>
                                            <p class="text-sm text-textlight">Please bring any valid government-issued ID</p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="confirmation-card bg-white rounded-2xl shadow-lg p-6 mb-6">
                        <h2 class="text-xl font-bold text-primary mb-4">Order Details</h2>
                        
                        <div class="space-y-4">
                            <?php if (!empty($order_items)): ?>
                                <?php foreach ($order_items as $item): ?>
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <div class="flex items-center space-x-4">
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
                                        <div>
                                            <p class="font-semibold text-textdark"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                            <p class="text-sm text-textlight">Qty: <?php echo $item['quantity']; ?></p>
                                        </div>
                                    </div>
                                    <div class="text-primary font-bold">₱<?php echo number_format($item['unit_price'] * $item['quantity'], 2); ?></div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-textlight py-8">
                                    <i class="fas fa-shopping-cart text-3xl mb-3"></i>
                                    <p class="text-lg">No items in this order.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
                
                <!-- Order Summary Sidebar -->
                <aside class="w-full lg:w-96">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-28">
                        <h2 class="text-xl font-bold text-primary mb-6">Order Summary</h2>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between">
                                <span class="text-textlight">Subtotal (<?php echo count($order_items); ?> items)</span>
                                <span class="font-medium text-textdark">₱<?php echo number_format($order_total, 2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-textlight">Shipping Fee</span>
                                <span class="font-medium text-textdark">₱<?php echo number_format($shipping_fee, 2); ?></span>
                            </div>
                        </div>

                        <div class="flex justify-between font-bold text-lg border-t border-gray-100 pt-4 mb-6">
                            <span class="text-primary">Total Amount</span>
                            <span class="text-primary">₱<?php echo number_format($final_total, 2); ?></span>
                        </div>
                        
                        <!-- Customer Information -->
                        <div class="border-t border-gray-100 pt-4 mb-6">
                            <h3 class="font-semibold text-primary mb-3">Customer Information</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-textlight">Name:</span>
                                    <span class="font-medium text-textdark"><?php echo htmlspecialchars($user_info['full_name']); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-textlight">Email:</span>
                                    <span class="font-medium text-textdark"><?php echo htmlspecialchars($user_info['email']); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-textlight">Phone:</span>
                                    <span class="font-medium text-textdark"><?php echo htmlspecialchars($user_info['phone_number'] ?? 'N/A'); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="space-y-3 no-print">
                            <button onclick="printReceipt()" class="print-btn action-btn w-full py-3 rounded-xl font-semibold text-white text-base flex items-center justify-center">
                                <i class="fas fa-print mr-2"></i> Print Receipt
                            </button>
                            <a href="products.php" class="continue-btn action-btn w-full py-3 rounded-xl font-semibold text-white text-base flex items-center justify-center">
                                <i class="fas fa-shopping-bag mr-2"></i> Continue Shopping
                            </a>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-gray-100 no-print">
                            <h3 class="font-semibold text-primary mb-3">Need Help?</h3>
                            <div class="flex items-center text-textlight mb-1">
                                <i class="fas fa-phone-alt mr-2"></i>
                                <span class="text-sm">+63 2 1234 5678</span>
                            </div>
                            <div class="flex items-center text-textlight">
                                <i class="fas fa-envelope mr-2"></i>
                                <span class="text-sm">support@raltt.com</span>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
            
            <!-- Hidden Receipt for Printing -->
            <div class="print-receipt" style="display: none;">
                <div class="receipt-container" style="border:2px solid #7d310a; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                    <div class="receipt-header" style="padding-bottom:0.5rem;">
                        <img src="../images/logologo.png" alt="Company Logo" style="height:48px; margin:0 auto 0.5rem; display:block;">
                        <h1 class="text-xl font-bold" style="letter-spacing:1px;">RICH ANNE LEA TILES TRADING</h1>
                        <p class="text-sm opacity-90 mt-1">Quality Tiles & Building Materials</p>
                    </div>
                    <div style="text-align:right; margin:-1.5rem 1.5rem 0 0;">
                        <span style="display:inline-block; padding:0.25em 1.5em; border:2px solid #059669; color:#059669; font-weight:bold; font-size:1.1em; border-radius:8px; transform:rotate(-8deg); background:#e6f9f2; letter-spacing:2px; box-shadow:0 1px 4px #05966922;">
                            <?php echo ($payment_method === 'gcash' || $payment_method === 'self-pickup') ? 'PAID' : 'UNPAID'; ?>
                        </span>
                    </div>
                    <div class="receipt-body" style="font-family: 'Fira Mono', 'Consolas', monospace;">
                        <div class="text-center mb-4">
                            <p class="font-semibold" style="font-size:1.1em;">OFFICIAL RECEIPT</p>
                            <p class="text-sm text-gray-600">Order Confirmation</p>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm mb-4">
                            <div>
                                <strong>Customer:</strong> <?php echo htmlspecialchars($user_info['full_name']); ?>
                            </div>
                            <div class="text-right">
                                <strong>Payment:</strong> <?php echo ucfirst(str_replace('-', ' ', $payment_method)); ?>
                            </div>
                        </div>
                        <div class="mb-4 text-xs text-center" style="color:#7d310a;">
                            <strong>Order Reference:</strong> <?php echo $order_reference; ?><br>
                            <span>Keep this reference. If you have any problem with your order, submit a <b>Customer Ticket</b> and provide this reference for support.</span>
                        </div>
                        <div class="border-t border-b border-gray-300 py-2 mb-4">
                            <div class="grid grid-cols-12 gap-1 text-xs font-semibold mb-1">
                                <div class="col-span-6">ITEM DESCRIPTION</div>
                                <div class="col-span-2 text-center">QTY</div>
                                <div class="col-span-2 text-right">PRICE</div>
                                <div class="col-span-2 text-right">AMOUNT</div>
                            </div>
                        </div>
                        <?php foreach ($order_items as $item): ?>
                        <div class="receipt-item">
                            <div class="grid grid-cols-12 gap-1 text-xs">
                                <div class="col-span-6 font-medium"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                <div class="col-span-2 text-center"><?php echo $item['quantity']; ?></div>
                                <div class="col-span-2 text-right">₱<?php echo number_format($item['unit_price'], 2); ?></div>
                                <div class="col-span-2 text-right font-medium">₱<?php echo number_format($item['unit_price'] * $item['quantity'], 2); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <div class="receipt-totals">
                            <div class="flex justify-between text-sm mb-1">
                                <span>Subtotal:</span>
                                <span class="font-medium">₱<?php echo number_format($order_total, 2); ?></span>
                            </div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>Shipping Fee:</span>
                                <span class="font-medium">₱<?php echo number_format($shipping_fee, 2); ?></span>
                            </div>
                            <?php if ($coins_redeemed > 0): ?>
                            <div class="flex justify-between text-sm mb-1">
                                <span>Coins Redeemed:</span>
                                <span class="font-medium text-green-600">-₱<?php echo number_format($coins_redeemed, 2); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="flex justify-between text-base font-bold mt-2">
                                <span>TOTAL:</span>
                                <span>₱<?php echo number_format($final_total, 2); ?></span>
                            </div>
                        </div>
                        <!-- Barcode and reference section removed -->
                            <div><strong>Served by:</strong> System</div>
                            <div><strong>Contact:</strong> support@raltt.com | +63 2 1234 5678</div>
                        <!-- QR code removed -->
                    </div>
                    <div class="receipt-footer" style="border-top:2px dashed #7d310a;">
                        <p class="font-semibold mb-1">Thank you for choosing Rich Anne Lea Tiles Trading!</p>
                        <p class="text-xs mb-1">For order support, please go to <b>Customer Ticket</b> and provide your order reference.</p>
                        <p class="text-xs mb-1">This receipt is system generated. No signature is required.</p>
                    </div>
                </div>
            </div>
            
            <!-- Estimated Delivery -->
            <div class="confirmation-card bg-white rounded-2xl shadow-lg p-6 mt-6 no-print">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-shipping-fast text-primary text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-textdark">Estimated Delivery</h3>
                            <p class="text-textlight">
                                <?php if ($payment_method === 'self-pickup'): ?>
                                    Ready for pickup in 5 hours
                                <?php else: ?>
                                    Within this day
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-textlight">Order Placed</p>
                        <p class="font-semibold text-textdark"><?php echo date('F j, Y'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function printReceipt() {
            // Create a new window for printing
            const printWindow = window.open('', '_blank', 'width=400,height=600');
            
            // Get the receipt HTML
            const receiptContent = document.querySelector('.print-receipt').innerHTML;
            
            // Write the receipt content to the new window
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet">
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 0;
                            padding: 0;
                            background: white;
                            color: #333;
                            font-size: 12px;
                        }
                        .receipt-container {
                            max-width: 400px;
                            margin: 0 auto;
                            background: white;
                        }
                        .receipt-header {
                            background: #7d310a;
                            color: white;
                            padding: 1rem;
                            text-align: center;
                        }
                        .receipt-body {
                            padding: 1rem;
                        }
                        .receipt-footer {
                            padding: 1rem;
                            border-top: 2px dashed #ccc;
                            text-align: center;
                            color: #666;
                            font-size: 0.75rem;
                        }
                        .receipt-item {
                            border-bottom: 1px dashed #eee;
                            padding: 0.5rem 0;
                        }
                        .receipt-totals {
                            border-top: 2px solid #333;
                            padding-top: 0.5rem;
                            margin-top: 0.5rem;
                        }
                        .barcode {
                            font-family: 'Libre Barcode 39', monospace;
                            font-size: 2rem;
                            letter-spacing: 2px;
                        }
                        @media print {
                            body {
                                margin: 0 !important;
                                padding: 0 !important;
                            }
                            .receipt-container {
                                box-shadow: none !important;
                                margin: 0 !important;
                            }
                        }
                    </style>
                </head>
                <body>
                    ${receiptContent}
                    <script>
                        window.onload = function() {
                            window.print();
                            setTimeout(function() {
                                window.close();
                            }, 500);
                        };
                    <\/script>
                </body>
                </html>
            `);
            
            printWindow.document.close();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Add some interactive elements
            const printBtn = document.querySelector('.print-btn');
            if (printBtn) {
                printBtn.addEventListener('mouseenter', function() {
                    this.querySelector('i').classList.add('fa-bounce');
                });
                
                printBtn.addEventListener('mouseleave', function() {
                    this.querySelector('i').classList.remove('fa-bounce');
                });
            }
            
            // Add confetti effect on page load
            setTimeout(() => {
                if (typeof confetti === 'function') {
                    confetti({
                        particleCount: 100,
                        spread: 70,
                        origin: { y: 0.6 }
                    });
                }
            }, 1000);
        });
        
        // Simple confetti function fallback
        function confetti(options) {
            // This is a simplified version - in production, use a proper confetti library
            console.log('🎉 Order confirmed! Confetti would show here.');
        }
    </script>
</body>
</html>