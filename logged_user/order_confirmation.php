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
    $order_reference_param = isset($_GET['order_reference']) ? $_GET['order_reference'] : null;
    if ($order_reference_param) {
        $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? AND order_reference = ? LIMIT 1");
        $stmt->execute([$user_id, $order_reference_param]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 1");
        $stmt->execute([$user_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    if ($order) {
        $order_id = $order['order_id'];
        $order_reference = $order['order_reference'];
        $payment_method = $order['payment_method'] === 'pick_up' ? 'self-pickup' : $order['payment_method'];
        // Normalize monetary fields and provide safe defaults
        $order_total = isset($order['original_subtotal']) ? (float)$order['original_subtotal'] : 0.0;
        $shipping_fee = isset($order['shipping_fee']) ? (float)$order['shipping_fee'] : 0.0;
        $final_total = isset($order['total_amount']) ? (float)$order['total_amount'] : 0.0;
        $coins_redeemed = isset($order['coins_redeemed']) ? (float)$order['coins_redeemed'] : 0.0;
        $order_date = $order['order_date'];
        
        // Get order items
        $stmt = $conn->prepare("SELECT oi.*, p.product_name, p.product_image, p.product_description FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?");
        $stmt->execute([$order_id]);
        $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // If subtotal or final total were not provided or are zero, compute them from order items as a fallback
        $calculated_subtotal = 0.0;
        foreach ($order_items as $oi) {
            $calculated_subtotal += (float)$oi['unit_price'] * (int)$oi['quantity'];
        }
        if (empty($order_total) || $order_total <= 0) {
            $order_total = $calculated_subtotal;
        }
        if (empty($final_total) || $final_total <= 0) {
            // final_total = subtotal + shipping - coins (best-effort)
            $final_total = $order_total + $shipping_fee - $coins_redeemed;
        }
        
        // Get branch info if available (prefer branch_id lookup)
        $branch_name = 'Main Branch';
        $branch_address = '123 Main Street, Quezon City, Metro Manila';
        $branch_phone = '+63 2 1234 5678';
        
        if (!empty($order['branch_id'])) {
            try {
                $bstmt = $conn->prepare('SELECT branch_name, branch_address, branch_phone FROM branches WHERE branch_id = ? LIMIT 1');
                $bstmt->execute([(int)$order['branch_id']]);
                $binfo = $bstmt->fetch(PDO::FETCH_ASSOC);
                if ($binfo) {
                    $branch_name = $binfo['branch_name'] ?: $branch_name;
                    $branch_address = $binfo['branch_address'] ?: $branch_address;
                    $branch_phone = $binfo['branch_phone'] ?: $branch_phone;
                }
            } catch (Exception $e) {
                // fallback to any order-provided branch values
                $branch_name = isset($order['branch_name']) ? $order['branch_name'] : $branch_name;
                $branch_address = isset($order['branch_address']) ? $order['branch_address'] : $branch_address;
            }
        } else {
            $branch_name = isset($order['branch_name']) ? $order['branch_name'] : $branch_name;
            $branch_address = isset($order['branch_address']) ? $order['branch_address'] : $branch_address;
        }
    } else {
        // Set branch variables to avoid undefined variable warning
        $branch_name = 'Main Branch';
        $branch_address = '123 Main Street, Quezon City, Metro Manila';
        echo '<div style="padding:2em;text-align:center;color:red;font-size:1.2em;">Error: Order not found.<br>Please go back to your cart or order history and try again.</div>';
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

// Determine if this is a receipt (paid) or invoice (unpaid)
$is_receipt = $payment_method === 'gcash' || (isset($order['order_status']) && $order['order_status'] === 'completed');
$is_unpaid_invoice = !$is_receipt && ($payment_method === 'cod' || $payment_method === 'self-pickup');
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
                                            <p class="text-sm text-textlight">Please prepare ₱<?php echo number_format((float)$final_total, 2); ?> in cash</p>
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
                                            <p class="text-sm text-textlight">Pick up your order at <?php echo htmlspecialchars($branch_name); ?></p>
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
                                            <p class="text-sm text-textlight"><?php echo htmlspecialchars($branch_address); ?></p>
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
                                <span class="font-medium text-textdark">₱<?php echo number_format((float)$order_total, 2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-textlight">Shipping Fee</span>
                                <span class="font-medium text-textdark">₱<?php echo number_format((float)$shipping_fee, 2); ?></span>
                            </div>
                        </div>

                        <div class="flex justify-between font-bold text-lg border-t border-gray-100 pt-4 mb-6">
                            <span class="text-primary">Total Amount</span>
                            <span class="text-primary">₱<?php echo number_format((float)$final_total, 2); ?></span>
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
                                <i class="fas fa-print mr-2"></i> Print <?php 
                                if ($is_receipt) {
                                    echo 'Receipt';
                                } else {
                                    echo 'Invoice';
                                }
                                ?>
                            </button>
                            <a href="myProfile.php" class="continue-btn action-btn w-full py-3 rounded-xl font-semibold text-white text-base flex items-center justify-center">
                                <i class="fas fa-shopping-bag mr-2"></i> Go to My Profile
                            </a>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-gray-100 no-print">
                            <h3 class="font-semibold text-primary mb-3">Need Help?</h3>
                            <div class="flex items-center text-textlight mb-1">
                                <i class="fas fa-phone-alt mr-2"></i>
                                <span class="text-sm"><?php echo $branch_phone; ?></span>
                            </div>
                            <div class="flex items-center text-textlight">
                                <i class="fas fa-envelope mr-2"></i>
                                <span class="text-sm">support@raltt.com</span>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
            
            <!-- Hidden Receipt/Invoice for Printing -->
            <div class="print-receipt" style="display: none;">
                <?php if ($is_receipt): ?>
                <!-- Compact Receipt (thermal) -->
                <div class="receipt-container" style="max-width: 80mm; margin: 0 auto; background: white; padding: 10mm 5mm; font-family: Arial, sans-serif;">
                    <div class="receipt-header" style="background: #7d310a; color: white; padding: 1rem; text-align: center;">
                        <img src="../images/logologo.png" alt="Company Logo" style="height:48px; margin:0 auto 0.5rem; display:block;">
                        <h1 style="font-size: 1.25rem; font-weight: bold; margin: 0;">RICH ANNE LEA TILES TRADING</h1>
                        <p style="margin: 0.25rem 0 0; font-size: 0.875rem;">Quality Tiles & Building Materials</p>
                    </div>
                    <div style="text-align: right; margin: -1.5rem 1.5rem 0 0;">
                        <span style="display: inline-block; padding: 0.25em 1.5em; border: 2px solid #059669; color: #059669; font-weight: bold; font-size: 1.1em; border-radius: 8px; transform: rotate(-8deg); background: #e6f9f2; letter-spacing: 2px; box-shadow: 0 1px 4px rgba(5, 150, 105, 0.2);">
                            PAID
                        </span>
                    </div>
                    <div class="receipt-body" style="padding: 1rem; font-family: 'Courier New', monospace;">
                        <div style="text-align: center; margin-bottom: 1rem;">
                            <p style="font-weight: bold; font-size: 1.1rem;">OFFICIAL RECEIPT</p>
                            <p style="font-size: 0.75rem;">This serves as your official receipt</p>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.875rem;">
                            <div>
                                <div><strong>Receipt No:</strong> R-<?php echo $order_reference; ?></div>
                                <div><strong>Date:</strong> <?php echo date('M d, Y', strtotime($order_date)); ?></div>
                            </div>
                            <div style="text-align: right;">
                                <strong>Terms:</strong> <?php echo ucfirst(str_replace('-', ' ', $payment_method)); ?><br>
                                <strong>Status:</strong> PAID
                            </div>
                        </div>
                        <div style="border-top: 1px solid #d1d5db; border-bottom: 1px solid #d1d5db; padding: 0.5rem 0; margin-bottom: 1rem;">
                            <div style="display: grid; grid-template-columns: 6fr 2fr 2fr 2fr; gap: 0.25rem; font-size: 0.75rem; font-weight: bold;">
                                <div>ITEM DESCRIPTION</div>
                                <div style="text-align: center;">QTY</div>
                                <div style="text-align: right;">UNIT</div>
                                <div style="text-align: right;">AMT</div>
                            </div>
                        </div>
                        <?php foreach ($order_items as $item): ?>
                        <div class="receipt-item" style="border-bottom: 1px dashed #e5e7eb; padding: 0.5rem 0;">
                            <div style="display: grid; grid-template-columns: 6fr 2fr 2fr 2fr; gap: 0.25rem; font-size: 0.75rem;">
                                <div style="font-weight: 500;"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                <div style="text-align: center;"><?php echo $item['quantity']; ?></div>
                                <div style="text-align: right;">₱<?php echo number_format($item['unit_price'], 2); ?></div>
                                <div style="text-align: right; font-weight: 500;">₱<?php echo number_format($item['unit_price'] * $item['quantity'], 2); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <div class="receipt-totals" style="border-top: 2px solid #111827; padding-top: 0.5rem; margin-top: 0.5rem;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.25rem;">
                                <span>Subtotal:</span>
                                <span style="font-weight: 500;">₱<?php echo number_format((float)$order_total, 2); ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.25rem;">
                                <span>Shipping Fee:</span>
                                <span style="font-weight: 500;">₱<?php echo number_format((float)$shipping_fee, 2); ?></span>
                            </div>
                            <?php if ((float)$coins_redeemed > 0): ?>
                            <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.25rem;">
                                <span>Coins Redeemed:</span>
                                <span style="font-weight: 500; color: #059669;">-₱<?php echo number_format((float)$coins_redeemed, 2); ?></span>
                            </div>
                            <?php endif; ?>
                            <div style="display: flex; justify-content: space-between; font-size: 1rem; font-weight: bold; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 2px solid #000;">
                                <span>TOTAL AMOUNT PAID:</span>
                                <span>₱<?php echo number_format((float)$final_total, 2); ?></span>
                            </div>
                        </div>
                        <div style="margin-top: 1rem; font-size: 0.75rem;">
                            <div><strong>Served by:</strong> System</div>
                        </div>
                    </div>
                    <div class="receipt-footer" style="padding: 1rem; border-top: 2px dashed #7d310a; text-align: center; color: #6b7280; font-size: 0.75rem;">
                        <p style="font-weight: bold; margin-bottom: 0.5rem;">Thank you for your payment!</p>
                        <p style="margin-bottom: 0.5rem;">This is your official receipt. Please keep for your records.</p>
                        <p>This document is system generated.</p>
                    </div>
                </div>

                <?php else: ?>
                <!-- Formal Invoice (A4) -->
                <div class="invoice-container" style="max-width: 210mm; margin: 0 auto; background: white; padding: 20mm 15mm; font-family: Arial, sans-serif; position: relative;">
                    <?php if ($is_unpaid_invoice): ?>
                        <div class="invoice-watermark" style="position: absolute; left: 50%; top: 45%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 5rem; color: rgba(185, 28, 28, 0.06); font-weight: 800; pointer-events: none; z-index: 0; letter-spacing: 6px;">UNPAID</div>
                    <?php endif; ?>
                    <div class="invoice-meta" style="background: #f8fafc; padding: 14px; border-radius: 8px; margin-bottom: 1.5rem;">
                        <div class="meta-row" style="display: flex; justify-content: space-between; gap: 12px;">
                            <div class="meta-left" style="color: #333;">
                                <img src="../images/logologo.png" alt="Company Logo" style="height: 56px; margin-bottom: 6px;">
                                <div style="font-weight: 700; color: #7d310a; font-size: 1.125rem;">RICH ANNE LEA TILES TRADING</div>
                                <div style="color: #555; font-size: 0.95rem; font-weight: 600;">Branch: <?php echo htmlspecialchars($branch_name); ?></div>
                                <div style="color: #555; font-size: 0.9rem;"><?php echo htmlspecialchars($branch_address); ?></div>
                                <div style="color: #555; font-size: 0.9rem;">TIN: 123-456-789-000</div>
                            </div>
                            <div class="meta-right" style="text-align: right; color: #333;">
                                <h2 style="font-size: 1.5rem; color: #7d310a; margin: 0 0 6px;">PURCHASE INVOICE</h2>
                                <div>Invoice No: <strong>INV-<?php echo $order_reference; ?></strong></div>
                                <div>Date: <?php echo date('F d, Y', strtotime($order_date)); ?></div>
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin: 1rem 0;">
                        <div>
                            <div style="font-weight: 700; color: #7d310a; margin-bottom: 0.25rem;">Bill To:</div>
                            <div style="font-weight: 600;"><?php echo htmlspecialchars($user_info['full_name']); ?></div>
                            <div><?php echo htmlspecialchars($user_info['phone_number'] ?? 'N/A'); ?></div>
                            <div><?php echo htmlspecialchars($user_info['full_address'] ?? 'N/A'); ?></div>
                        </div>
                        <div>
                            <div style="font-weight: 700; color: #7d310a; margin-bottom: 0.25rem;">Payment Details:</div>
                            <div><strong>Method:</strong> <?php echo ucfirst(str_replace('-', ' ', $payment_method)); ?></div>
                            <div><strong>Terms:</strong> Due upon <?php echo $payment_method === 'cod' ? 'delivery' : 'pickup'; ?></div>
                            <div><strong>Status:</strong> <span style="color: #b91c1c; font-weight: 700;">UNPAID</span></div>
                        </div>
                    </div>

                    <table style="width: 100%; border-collapse: collapse; margin: 1rem 0;">
                        <thead>
                            <tr>
                                <th style="text-align: left; padding: 0.75rem; border-bottom: 2px solid #e5e7eb; background-color: #f3f4f6;">Item Description</th>
                                <th style="text-align: center; padding: 0.75rem; border-bottom: 2px solid #e5e7eb; background-color: #f3f4f6; width: 90px;">Quantity</th>
                                <th style="text-align: right; padding: 0.75rem; border-bottom: 2px solid #e5e7eb; background-color: #f3f4f6; width: 120px;">Unit Price</th>
                                <th style="text-align: right; padding: 0.75rem; border-bottom: 2px solid #e5e7eb; background-color: #f3f4f6; width: 140px;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td style="padding: 0.75rem; vertical-align: top; border-bottom: 1px solid #e5e7eb;"><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td style="padding: 0.75rem; text-align: center; border-bottom: 1px solid #e5e7eb;"><?php echo $item['quantity']; ?></td>
                                <td style="padding: 0.75rem; text-align: right; border-bottom: 1px solid #e5e7eb;">₱<?php echo number_format($item['unit_price'], 2); ?></td>
                                <td style="padding: 0.75rem; text-align: right; border-bottom: 1px solid #e5e7eb;">₱<?php echo number_format($item['unit_price'] * $item['quantity'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1rem;">
                        <div style="width: 320px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;"><span>Subtotal:</span><span>₱<?php echo number_format((float)$order_total, 2); ?></span></div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;"><span>Shipping Fee:</span><span>₱<?php echo number_format((float)$shipping_fee, 2); ?></span></div>
                            <?php if ((float)$coins_redeemed > 0): ?>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;"><span>Coins Redeemed:</span><span style="color: #059669;">-₱<?php echo number_format((float)$coins_redeemed, 2); ?></span></div>
                            <?php endif; ?>
                            <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.125rem; border-top: 2px solid #000; padding-top: 0.5rem; margin-top: 0.5rem;"><span>TOTAL DUE:</span><span>₱<?php echo number_format((float)$final_total, 2); ?></span></div>
                        </div>
                    </div>

                    <div style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: flex-start;">
                        <div style="width: 45%;">
                            <p style="font-weight: 700; color: #7d310a;">Terms & Conditions</p>
                            <ol style="font-size: 0.9rem; color: #555; padding-left: 1rem;">
                                <li>Payment is due upon <?php echo $payment_method === 'cod' ? 'delivery' : 'pickup'; ?>.</li>
                                <li>Official receipt will be issued upon full payment.</li>
                                <li>Please contact support for disputes within 7 days.</li>
                            </ol>
                        </div>
                        <div style="width: 45%; text-align: center;">
                            <div style="border-top: 1px solid #ddd; padding-top: 1.5rem;">Authorized Signature</div>
                            <div style="font-size: 0.85rem; color: #666; margin-top: 0.5rem;">Rich Anne Lea Tiles Trading</div>
                        </div>
                    </div>

                    <div style="text-align: center; margin-top: 2rem; font-size: 0.9rem; color: #666;">
                        <p>For inquiries: support@raltt.com | <?php echo $branch_phone; ?></p>
                    </div>
                </div>
                <?php endif; ?>
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
            // Get the receipt HTML
            const receiptContent = document.querySelector('.print-receipt').innerHTML;
            
            // Create a new window for printing
            const printWindow = window.open('', '_blank');
            
            // Write the receipt content to the new window
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Print Document</title>
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
                        
                        @media print {
                            body {
                                margin: 0 !important;
                                padding: 0 !important;
                            }
                            
                            @page {
                                size: <?php echo $is_receipt ? '80mm 297mm' : 'A4'; ?>;
                                margin: <?php echo $is_receipt ? '5mm' : '15mm'; ?>;
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