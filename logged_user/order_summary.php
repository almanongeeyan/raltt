<?php
include '../includes/headeruser.php';
require_once '../connection/connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$selected_cart_items = [];
if (!empty($_POST['selected_cart_items'])) {
    $selected_cart_items = json_decode($_POST['selected_cart_items'], true);
}

// Get selected cart items for user
$cartItems = [];
if ($user_id > 0 && !empty($selected_cart_items)) {
    $in = str_repeat('?,', count($selected_cart_items) - 1) . '?';
    $stmt = $conn->prepare(
        "SELECT ci.cart_item_id, ci.product_id, ci.quantity, p.product_name, p.product_price, p.product_image, p.product_description
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.product_id
        WHERE ci.user_id = ? AND ci.cart_item_id IN ($in)"
    );
    $stmt->execute(array_merge([$user_id], $selected_cart_items));
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get referral coins from users table
$referral_coins = 0;
if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT referral_coins FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$user_id]);
    $referral_coins = intval($stmt->fetchColumn());
}

// Calculate summary for selected items
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['product_price'] * $item['quantity'];
}
$shipping = ($subtotal >= 1000) ? 0 : ($subtotal > 0 ? 40 : 0);

// Referral coins logic: 1 coin = 1 peso, only usable if subtotal > 600
$coins_applied = 0;
$referral_discount = 0;
$max_coins_applicable = ($subtotal > 600) ? min(20, $referral_coins, $subtotal) : 0;
$can_use_coins = ($subtotal > 600 && $referral_coins > 0);
if ($can_use_coins && isset($_POST['apply_referral_coins'])) {
    $coins_applied = $max_coins_applicable;
    $referral_discount = $coins_applied; // 1 coin = 1 peso
}
$total = max(0, $subtotal + $shipping - $referral_discount);

// Get logged-in user's shipping info
$user_fullname = '';
$user_phone = '';
$user_house_address = '';
$user_full_address = '';
if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT full_name, phone_number, house_address, full_address FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $user_fullname = $user['full_name'];
        $user_phone = $user['phone_number'];
        $user_house_address = $user['house_address'];
        $user_full_address = $user['full_address'];
    }
}
?>

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
            background: linear-gradient(135deg, #fdf8f5 0%, #f9f5f2 60%, #f0e6df 100%);
            min-height: 100vh;
        }
        
        .summary-card {
            transition: all 0.3s ease;
            border-radius: 16px;
            overflow: hidden;
        }
        
        .summary-card:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        
        .payment-method {
            transition: all 0.3s ease;
            border-radius: 12px;
            cursor: pointer;
        }
        
        .payment-method:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .payment-method.selected {
            border-color: #7d310a;
            box-shadow: 0 0 0 2px rgba(125, 49, 10, 0.1);
        }
        
        .checkout-btn {
            background: #7d310a;
            transition: all 0.3s ease;
        }
        
        .checkout-btn:hover {
            background: #5a2207;
            transform: translateY(-1px);
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
        
        .referral-coins {
            background: linear-gradient(135deg, #f9f5f2 0%, #f0e6df 100%);
            border: 1px dashed #cf8756;
        }
        
        .referral-coins.disabled {
            background: #f5f5f5;
            border-color: #ddd;
            opacity: 0.6;
        }
        
        .apply-coins-btn {
            background: linear-gradient(90deg, #7d310a 0%, #a34a20 100%);
            transition: all 0.3s ease;
        }
        
        .apply-coins-btn:hover:not(:disabled) {
            background: linear-gradient(90deg, #a34a20 0%, #7d310a 100%);
            transform: translateY(-1px);
        }
        
        .apply-coins-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }
        
        .coins-badge {
            background: linear-gradient(90deg, #7d310a 0%, #a34a20 100%);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(125,49,10,0.08);
            border-radius: 9999px;
            padding: 0.25em 0.7em;
            display: inline-flex;
            align-items: center;
            font-size: 0.85rem;
            letter-spacing: 0.02em;
        }
        
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
        
        /* Terms and Privacy Modal Styles */
        .policy-modal {
            max-height: calc(100vh - 4rem);
            overflow-y: auto;
        }
        
        .policy-content {
            max-height: 500px;
            overflow-y: auto;
        }
        
        .policy-section {
            margin-bottom: 1.5rem;
        }
        
        .policy-section h3 {
            color: #7d310a;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .policy-section p, .policy-section li {
            color: #4b5563;
            line-height: 1.6;
        }
        
        .policy-section ul {
            list-style-type: disc;
            margin-left: 1.5rem;
            margin-top: 0.5rem;
        }
        
        .policy-section li {
            margin-bottom: 0.25rem;
        }
    </style>
</head>
<body class="min-h-screen pt-24">
    <div id="updateSuccessNotif" style="position:fixed;top:24px;right:24px;z-index:9999;min-width:220px;max-width:90vw;pointer-events:none;opacity:0;transition:opacity 0.3s;" class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg font-semibold text-sm">
        <span id="updateSuccessNotifText">Shipping information updated successfully!</span>
    </div>
    
    <!-- Terms of Service Modal -->
    <div id="terms-modal" class="fixed inset-0 z-50 flex items-center justify-center modal-overlay hidden">
        <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-4xl mx-4 policy-modal">
            <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-primary">Terms of Service</h3>
                    <button id="close-terms-modal" class="text-textlight hover:text-textdark">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6 policy-content">
                <div class="policy-section">
                    <h2 class="text-2xl font-bold text-primary mb-4">Rich Anne Lea Tiles Trading - Terms of Service</h2>
                    <p class="text-sm text-textlight mb-6">Last Updated: <?php echo date('F j, Y'); ?></p>
                </div>
                
                <div class="policy-section">
                    <h3>1. Acceptance of Terms</h3>
                    <p>By accessing and using the Rich Anne Lea Tiles Trading website and services, you accept and agree to be bound by the terms and provision of this agreement.</p>
                </div>
                
                <div class="policy-section">
                    <h3>2. Use of Services</h3>
                    <p>Our services are available only to individuals who are at least 18 years old. You represent and warrant that you are of legal age to form a binding contract.</p>
                </div>
                
                <div class="policy-section">
                    <h3>3. Account Registration</h3>
                    <p>To access certain features, you may be required to register for an account. You agree to provide accurate, current, and complete information during the registration process.</p>
                </div>
                
                <div class="policy-section">
                    <h3>4. Product Information</h3>
                    <p>We strive to display accurate product information, including descriptions, images, and pricing. However, we do not warrant that product descriptions or other content is accurate, complete, reliable, current, or error-free.</p>
                </div>
                
                <div class="policy-section">
                    <h3>5. Pricing and Payment</h3>
                    <p>All prices are in Philippine Peso (₱). We reserve the right to change prices at any time without notice. Payment must be made through approved payment methods.</p>
                </div>
                
                <div class="policy-section">
                    <h3>6. Shipping and Delivery</h3>
                    <p>Shipping costs and delivery times may vary based on your location and product availability. We are not responsible for delays caused by shipping carriers or unforeseen circumstances.</p>
                </div>
                
                <div class="policy-section">
                    <h3>7. Returns and Refunds</h3>
                    <p>Returns are accepted within 7 days of delivery for defective products. Refunds will be processed according to our refund policy. Custom orders may not be eligible for return.</p>
                </div>
                
                <div class="policy-section">
                    <h3>8. Intellectual Property</h3>
                    <p>All content included on this site, such as text, graphics, logos, images, and software, is the property of Rich Anne Lea Tiles Trading and protected by copyright laws.</p>
                </div>
                
                <div class="policy-section">
                    <h3>9. Limitation of Liability</h3>
                    <p>Rich Anne Lea Tiles Trading shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of or inability to use the service.</p>
                </div>
                
                <div class="policy-section">
                    <h3>10. Governing Law</h3>
                    <p>These Terms shall be governed by the laws of the Republic of the Philippines, without regard to its conflict of law provisions.</p>
                </div>
                
                <div class="policy-section">
                    <h3>11. Changes to Terms</h3>
                    <p>We reserve the right to modify these terms at any time. We will notify users of any material changes by posting the new Terms on this site.</p>
                </div>
                
                <div class="policy-section">
                    <h3>12. Contact Information</h3>
                    <p>If you have any questions about these Terms, please contact us at:</p>
                    <p class="mt-2">
                        Rich Anne Lea Tiles Trading<br>
                        Email: support@raltt.com<br>
                        Phone: +63 2 1234 5678
                    </p>
                </div>
            </div>
            
            <div class="p-6 border-t border-gray-200 sticky bottom-0 bg-white">
                <div class="flex justify-end">
                    <button id="accept-terms" class="checkout-btn text-white font-semibold py-2 px-6 rounded-lg">
                        I Accept
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Privacy Policy Modal -->
    <div id="privacy-modal" class="fixed inset-0 z-50 flex items-center justify-center modal-overlay hidden">
        <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-4xl mx-4 policy-modal">
            <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-primary">Privacy Policy</h3>
                    <button id="close-privacy-modal" class="text-textlight hover:text-textdark">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6 policy-content">
                <div class="policy-section">
                    <h2 class="text-2xl font-bold text-primary mb-4">Rich Anne Lea Tiles Trading - Privacy Policy</h2>
                    <p class="text-sm text-textlight mb-6">Last Updated: <?php echo date('F j, Y'); ?></p>
                </div>
                
                <div class="policy-section">
                    <h3>1. Information We Collect</h3>
                    <p>We collect information you provide directly to us, including:</p>
                    <ul>
                        <li>Personal identification information (Name, email address, phone number)</li>
                        <li>Shipping and billing addresses</li>
                        <li>Payment information (processed securely by our payment partners)</li>
                        <li>Communication preferences</li>
                        <li>Order history and preferences</li>
                    </ul>
                </div>
                
                <div class="policy-section">
                    <h3>2. How We Use Your Information</h3>
                    <p>We use the information we collect to:</p>
                    <ul>
                        <li>Process and fulfill your orders</li>
                        <li>Provide customer support</li>
                        <li>Send order confirmations and updates</li>
                        <li>Personalize your shopping experience</li>
                        <li>Communicate about promotions and new products (with your consent)</li>
                        <li>Improve our website and services</li>
                        <li>Comply with legal obligations</li>
                    </ul>
                </div>
                
                <div class="policy-section">
                    <h3>3. Information Sharing</h3>
                    <p>We do not sell your personal information to third parties. We may share your information with:</p>
                    <ul>
                        <li>Service providers who assist in our operations (payment processors, shipping carriers)</li>
                        <li>Legal authorities when required by law</li>
                        <li>Business partners with your explicit consent</li>
                    </ul>
                </div>
                
                <div class="policy-section">
                    <h3>4. Data Security</h3>
                    <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
                </div>
                
                <div class="policy-section">
                    <h3>5. Data Retention</h3>
                    <p>We retain your personal information for as long as necessary to fulfill the purposes outlined in this Privacy Policy, unless a longer retention period is required or permitted by law.</p>
                </div>
                
                <div class="policy-section">
                    <h3>6. Your Rights</h3>
                    <p>You have the right to:</p>
                    <ul>
                        <li>Access and review your personal information</li>
                        <li>Correct inaccurate or incomplete information</li>
                        <li>Request deletion of your personal information</li>
                        <li>Object to processing of your personal information</li>
                        <li>Request data portability</li>
                        <li>Withdraw consent at any time</li>
                    </ul>
                </div>
                
                <div class="policy-section">
                    <h3>7. Cookies and Tracking Technologies</h3>
                    <p>We use cookies and similar tracking technologies to track activity on our website and hold certain information to improve user experience.</p>
                </div>
                
                <div class="policy-section">
                    <h3>8. Third-Party Links</h3>
                    <p>Our website may contain links to other sites that are not operated by us. We have no control over and assume no responsibility for the content, privacy policies, or practices of any third-party sites.</p>
                </div>
                
                <div class="policy-section">
                    <h3>9. Children's Privacy</h3>
                    <p>Our service does not address anyone under the age of 18. We do not knowingly collect personally identifiable information from children under 18.</p>
                </div>
                
                <div class="policy-section">
                    <h3>10. Changes to This Privacy Policy</h3>
                    <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date.</p>
                </div>
                
                <div class="policy-section">
                    <h3>11. Contact Us</h3>
                    <p>If you have any questions about this Privacy Policy, please contact us:</p>
                    <p class="mt-2">
                        Rich Anne Lea Tiles Trading<br>
                        Data Protection Officer<br>
                        Email: privacy@raltt.com<br>
                        Phone: +63 2 1234 5678
                    </p>
                </div>
            </div>
            
            <div class="p-6 border-t border-gray-200 sticky bottom-0 bg-white">
                <div class="flex justify-end">
                    <button id="accept-privacy" class="checkout-btn text-white font-semibold py-2 px-6 rounded-lg">
                        I Understand
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    function showUpdateSuccess(message) {
        var notif = document.getElementById('updateSuccessNotif');
        var notifText = document.getElementById('updateSuccessNotifText');
        notifText.textContent = message || 'Shipping information updated successfully!';
        notif.style.opacity = '1';
        notif.style.pointerEvents = 'auto';
        setTimeout(function() {
            notif.style.opacity = '0';
            notif.style.pointerEvents = 'none';
        }, 1800);
    }

    // Listen for shipping info update event and update fields
    window.addEventListener('message', function(event) {
        if (event.data && event.data.type === 'shippingInfoUpdated') {
            showUpdateSuccess(event.data.message || 'Shipping information updated successfully!');
            if (event.data.info) {
                if (document.getElementById('displayFullName')) {
                    document.getElementById('displayFullName').textContent = event.data.info.fullName || 'No data';
                }
                if (document.getElementById('displayContactNumber')) {
                    document.getElementById('displayContactNumber').textContent = event.data.info.contactNumber || 'No data';
                }
                if (document.getElementById('displayHouseAddress')) {
                    document.getElementById('displayHouseAddress').textContent = event.data.info.houseAddress || 'No data';
                }
                if (document.getElementById('displayFullAddress')) {
                    document.getElementById('displayFullAddress').textContent = event.data.info.fullAddress || 'No data';
                }
            }
            // Check if all fields are filled
            var allFilled = event.data.info && event.data.info.fullName && event.data.info.contactNumber && event.data.info.houseAddress && event.data.info.fullAddress;
            var payBtn = document.getElementById('pay-button');
            var warningDiv = document.getElementById('shipping-warning');
            var missingFieldsList = document.getElementById('missing-fields-list');
            if (allFilled) {
                payBtn.disabled = false;
                payBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                warningDiv.classList.add('hidden');
            } else {
                payBtn.disabled = true;
                payBtn.classList.add('opacity-50', 'cursor-not-allowed');
                // Update missing fields list
                var missing = [];
                if (!event.data.info.fullName) missing.push('Full Name');
                if (!event.data.info.contactNumber) missing.push('Contact Number');
                if (!event.data.info.houseAddress) missing.push('Shipping Address');
                if (!event.data.info.fullAddress) missing.push('Full Address');
                if (missingFieldsList) missingFieldsList.textContent = missing.join(', ');
                warningDiv.classList.remove('hidden');
            }
        }
    });
    </script>
    <?php if (isset($_GET['shipping_updated']) && $_GET['shipping_updated'] == '1'): ?>
    <div id="updateSuccessNotif" class="fixed top-6 right-6 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg font-semibold text-sm transition-all duration-300" style="min-width:200px;">
        Shipping information updated successfully!
    </div>
    <script>
        setTimeout(function() {
            var notif = document.getElementById('updateSuccessNotif');
            if (notif) notif.style.opacity = '0';
        }, 1800);
    </script>
    <?php endif; ?>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Progress Steps -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-2">
                    <h1 class="text-2xl font-bold text-primary">Order Summary</h1>
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
                        <h2 class="text-xl font-bold text-primary mb-4">Order Items</h2>
                        
                        <div class="space-y-4">
                        <?php if (!empty($cartItems)): ?>
                            <?php foreach ($cartItems as $item): ?>
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
                                <div class="text-primary font-bold">₱<?php echo number_format($item['product_price'] * $item['quantity'], 2); ?></div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-textlight py-12">
                                <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                                <p class="text-lg">No items selected for order.</p>
                            </div>
                        <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Referral Coins -->
                    <div class="summary-card bg-white rounded-2xl shadow-lg p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-primary">Referral Coins</h2>
                            <span class="coins-badge text-white text-sm font-medium px-3 py-1 rounded-full" id="coins-badge">
                                <?php echo $referral_coins; ?> coins available
                            </span>
                        </div>
                        
                        <div class="referral-coins rounded-xl p-4 flex flex-col md:flex-row items-center justify-between <?php echo !$can_use_coins ? 'disabled' : ''; ?>">
                            <div class="flex items-center mb-3 md:mb-0">
                                <div class="w-10 h-10 rounded-full <?php echo $can_use_coins ? 'bg-primary' : 'bg-gray-400'; ?> flex items-center justify-center mr-3">
                                    <i class="fas fa-coins text-white"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-textdark">Use your referral coins</p>
                                    <p class="text-sm text-textlight">1 coin = 1 peso (max 20 coins per order)</p>
                                    <?php if (!$can_use_coins): ?>
                                        <p class="text-sm text-red-500 font-medium mt-1">Available only for orders exceeding ₱600</p>
                                    <?php else: ?>
                                        <p class="text-sm text-green-600 font-medium mt-1">You can use up to <?php echo $max_coins_applicable; ?> coins</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <button id="apply-coins-btn" 
                                    class="apply-coins-btn text-white font-semibold py-2 px-6 rounded-lg flex items-center transition-all duration-200 <?php echo !$can_use_coins ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                                    <?php echo !$can_use_coins ? 'disabled' : ''; ?>>
                                <i class="fas fa-check-circle mr-2"></i> 
                                <span id="apply-coins-text">Apply Coins</span>
                            </button>
                        </div>
                        
                        <div id="coins-applied" class="hidden mt-4 bg-green-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <p class="text-green-700 font-medium" id="coins-applied-text"></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shipping Information -->
                    <div class="summary-card bg-white rounded-2xl shadow-lg p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-primary">Shipping Information</h2>
                                <button class="text-secondary font-medium hover:text-primary transition-colors" onclick="openShippingModal()">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-textlight mb-1">Full Name</p>
                                <p class="font-medium text-textdark" id="displayFullName"><?php echo !empty($user_fullname) ? htmlspecialchars($user_fullname) : 'No data'; ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-textlight mb-1">Contact Number</p>
                                <p class="font-medium text-textdark" id="displayContactNumber"><?php echo !empty($user_phone) ? htmlspecialchars($user_phone) : 'No data'; ?></p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm text-textlight mb-1">Shipping Address</p>
                                <p class="font-medium text-textdark" id="displayHouseAddress"><?php echo !empty($user_house_address) ? htmlspecialchars($user_house_address) : 'No data'; ?></p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm text-textlight mb-1">Full Address</p>
                                <p class="font-medium text-textdark" id="displayFullAddress"><?php echo !empty($user_full_address) ? htmlspecialchars($user_full_address) : 'No data'; ?></p>
                            </div>
                        </div>
                            <!-- Pin Location (if you want to show it separately) -->
                            <!-- <div class="md:col-span-2">
                                <p class="text-sm text-textlight mb-1">Pin Location</p>
                                <p class="font-medium text-textdark" id="displayPinLocation"><?php echo htmlspecialchars($user_full_address); ?></p>
                            </div> -->
                            <?php include 'processes/shipping_info_modal.php'; ?>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="summary-card bg-white rounded-2xl shadow-lg p-6">
                        <h2 class="text-xl font-bold text-primary mb-4">Payment Method</h2>
                        <p class="text-textlight mb-4">Select your preferred payment method</p>
                        
                        <div class="space-y-3">
                            <!-- GCash Option -->
                            <div class="payment-method border-2 border-gray-200 p-4 selected">
                                <label class="custom-radio flex items-center cursor-pointer">
                                    <input type="radio" name="payment-method" value="gcash" checked>
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
                                        Pay with cash when your order is delivered.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- Order Summary Sidebar -->
                <aside class="w-full lg:w-96">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-28">
                        <h2 class="text-xl font-bold text-primary mb-6">Order Summary</h2>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between">
                                <span class="text-textlight">Subtotal (<?php echo count($cartItems); ?> items)</span>
                                <span class="font-medium text-textdark">₱<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-textlight">Shipping Fee</span>
                                <span class="font-medium text-textdark">₱<?php echo number_format($shipping, 2); ?></span>
                            </div>

                            <div id="coins-discount" class="hidden flex justify-between">
                                <span class="text-textlight">Referral Coins Discount</span>
                                <span class="font-medium text-green-600">-₱<span id="discount-amount">0</span></span>
                            </div>
                        </div>

                        <div class="flex justify-between font-bold text-lg border-t border-gray-100 pt-4 mb-6">
                            <span class="text-primary">Total</span>
                            <span id="total-amount" class="text-primary">₱<?php echo number_format($total, 2); ?></span>
                        </div>
                        
                        <!-- Pay with Selected Method Button -->
                        <?php
                        $shipping_incomplete = empty($user_fullname) || empty($user_phone) || empty($user_house_address) || empty($user_full_address);
                        $missing_fields = [];
                        if (empty($user_fullname)) $missing_fields[] = 'Full Name';
                        if (empty($user_phone)) $missing_fields[] = 'Contact Number';
                        if (empty($user_house_address)) $missing_fields[] = 'Shipping Address';
                        if (empty($user_full_address)) $missing_fields[] = 'Full Address';
                        ?>
                        <button id="pay-button" class="checkout-btn w-full py-3 rounded-xl font-semibold text-white text-base mb-2 flex items-center justify-center <?php echo $shipping_incomplete ? 'opacity-50 cursor-not-allowed' : ''; ?>" <?php echo $shipping_incomplete ? 'disabled' : ''; ?>>
                            <i class="fas fa-lock mr-2"></i>Proceed to Payment
                        </button>
                        <div id="shipping-warning" class="text-xs text-red-500 text-center mb-2" style="display:<?php echo $shipping_incomplete ? 'block' : 'none'; ?>;">
                            Please complete your shipping information to proceed:<br>
                            <span class="font-semibold" id="missing-fields-list"><?php echo implode(', ', $missing_fields); ?></span>
                        </div>
                        <p class="text-xs text-textlight text-center">
                            By completing your purchase, you agree to our <a href="#" id="terms-link" class="text-secondary hover:underline">Terms of Service</a> and <a href="#" id="privacy-link" class="text-secondary hover:underline">Privacy Policy</a>.
                        </p>
                        
                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <h3 class="font-semibold text-primary mb-3">Need Help?</h3>
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
    <div id="payment-modal" class="fixed inset-0 z-50 flex items-start justify-center modal-overlay hidden overflow-y-auto pt-24" style="background: rgba(0,0,0,0.5);">
        <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 my-8" style="max-height:calc(100vh - 4rem); overflow-y:auto;">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-primary">Complete Payment</h3>
                    <button id="close-modal" class="text-textlight hover:text-textdark">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="bg-gray-50 rounded-xl p-4 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-textlight">Total Amount:</span>
                        <span class="font-bold text-primary">₱<span id="modal-total-amount"><?php echo number_format($total, 2); ?></span></span>
                    </div>
                    <p class="text-xs text-textlight" id="modal-payment-method">Payment processed through GCash</p>
                </div>
                
                <div id="payment-instructions" class="text-sm">
                    <!-- Instructions will be dynamically inserted here -->
                </div>
                
                <form id="payment-form" method="post">
                    <input type="hidden" name="selected_cart_items" value='<?php echo json_encode($selected_cart_items); ?>'>
                    <input type="hidden" name="payment_method" id="payment-method-input" value="gcash">
                    <input type="hidden" name="applied_coins" id="applied-coins-input" value="0">
                    <div class="flex items-center mt-4 mb-2">
                        <input type="checkbox" id="confirm-info-checkbox" class="mr-2">
                        <label for="confirm-info-checkbox" class="text-xs italic text-textdark opacity-80">I have reviewed and confirm that all shipping information and order items are correct.</label>
                    </div>
                    <button type="submit" id="confirm-payment-btn" class="checkout-btn w-full py-3 rounded-xl font-semibold text-white text-base mt-4 disabled:bg-gray-400 disabled:cursor-not-allowed" disabled>
                        Confirm Payment
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Terms and Privacy Modal Functionality
        const termsModal = document.getElementById('terms-modal');
        const privacyModal = document.getElementById('privacy-modal');
        const termsLink = document.getElementById('terms-link');
        const privacyLink = document.getElementById('privacy-link');
        const closeTermsModal = document.getElementById('close-terms-modal');
        const closePrivacyModal = document.getElementById('close-privacy-modal');
        const acceptTerms = document.getElementById('accept-terms');
        const acceptPrivacy = document.getElementById('accept-privacy');
        
        // Open Terms Modal
        termsLink.addEventListener('click', function(e) {
            e.preventDefault();
            setModalScrollLock(true);
            termsModal.classList.remove('hidden');
        });
        
        // Open Privacy Modal
        privacyLink.addEventListener('click', function(e) {
            e.preventDefault();
            setModalScrollLock(true);
            privacyModal.classList.remove('hidden');
        });
        
        // Close Terms Modal
        closeTermsModal.addEventListener('click', function() {
            termsModal.classList.add('hidden');
            setModalScrollLock(false);
        });
        
        // Close Privacy Modal
        closePrivacyModal.addEventListener('click', function() {
            privacyModal.classList.add('hidden');
            setModalScrollLock(false);
        });
        
        // Accept Terms
        acceptTerms.addEventListener('click', function() {
            termsModal.classList.add('hidden');
            setModalScrollLock(false);
        });
        
        // Accept Privacy
        acceptPrivacy.addEventListener('click', function() {
            privacyModal.classList.add('hidden');
            setModalScrollLock(false);
        });
        
        // Close modals when clicking outside
        termsModal.addEventListener('click', function(e) {
            if (e.target === termsModal) {
                termsModal.classList.add('hidden');
                setModalScrollLock(false);
            }
        });
        
        privacyModal.addEventListener('click', function(e) {
            if (e.target === privacyModal) {
                privacyModal.classList.add('hidden');
                setModalScrollLock(false);
            }
        });
        
        // Prevent background scroll for all modals
        function setModalScrollLock(lock) {
            document.body.style.overflow = lock ? 'hidden' : 'auto';
        }
        
        // Initialize values from PHP
        const subtotal = <?php echo $subtotal; ?>;
        const shipping = <?php echo $shipping; ?>;
        const canUseCoins = <?php echo $can_use_coins ? 'true' : 'false'; ?>;
        const referralCoins = <?php echo $referral_coins; ?>;
        const maxCoinsApplicable = <?php echo $max_coins_applicable; ?>;
        
        let baseTotal = subtotal + shipping;
        let currentTotal = baseTotal;
        let coinsApplied = false;
        let appliedCoins = 0;
        let selectedPaymentMethod = 'gcash';
        
        // Payment method selection
        const paymentMethods = document.querySelectorAll('.payment-method');
        const codFeeElement = document.getElementById('cod-fee');
        const totalAmountElement = document.getElementById('total-amount');
        const modalTotalAmountElement = document.getElementById('modal-total-amount');
        const modalPaymentMethodElement = document.getElementById('modal-payment-method');
        const paymentInstructionsElement = document.getElementById('payment-instructions');
        const payButton = document.getElementById('pay-button');
        const paymentMethodInput = document.getElementById('payment-method-input');
        const appliedCoinsInput = document.getElementById('applied-coins-input');
        
        paymentMethods.forEach(method => {
            method.addEventListener('click', function() {
                paymentMethods.forEach(m => m.classList.remove('selected'));
                this.classList.add('selected');
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                selectedPaymentMethod = radio.value;
                
                // Handle COD fee
                if (radio.value === 'cod') {
                    currentTotal = baseTotal;
                } else {
                    currentTotal = baseTotal;
                }
                
                // Apply coins discount if applicable
                if (coinsApplied) {
                    currentTotal -= appliedCoins;
                }
                
                updateTotalDisplay();
            });
        });
        
        // Referral coins functionality
        const applyCoinsBtn = document.getElementById('apply-coins-btn');
        const coinsAppliedElement = document.getElementById('coins-applied');
        const coinsAppliedText = document.getElementById('coins-applied-text');
        const coinsDiscountElement = document.getElementById('coins-discount');
        const discountAmountElement = document.getElementById('discount-amount');
        const applyCoinsText = document.getElementById('apply-coins-text');
        
        const coinsBadge = document.getElementById('coins-badge');
        applyCoinsBtn.addEventListener('click', function() {
            if (!coinsApplied && canUseCoins && maxCoinsApplicable > 0) {
                appliedCoins = maxCoinsApplicable;
                coinsApplied = true;
                coinsAppliedElement.classList.remove('hidden');
                coinsDiscountElement.classList.remove('hidden');
                coinsAppliedText.textContent = `${appliedCoins} coins applied! You've received a ₱${appliedCoins} discount.`;
                discountAmountElement.textContent = appliedCoins.toFixed(2);
                currentTotal -= appliedCoins;
                updateTotalDisplay();
                applyCoinsText.textContent = 'Remove Coins';
                applyCoinsBtn.innerHTML = '<i class="fas fa-times-circle mr-2"></i> Remove Coins';
                // Update coins badge
                coinsBadge.textContent = `${referralCoins - appliedCoins} coins available`;
                appliedCoinsInput.value = appliedCoins;
            } else {
                coinsApplied = false;
                coinsAppliedElement.classList.add('hidden');
                coinsDiscountElement.classList.add('hidden');
                currentTotal += appliedCoins;
                // Restore coins
                coinsBadge.textContent = `${referralCoins} coins available`;
                appliedCoins = 0;
                updateTotalDisplay();
                applyCoinsText.textContent = 'Apply Coins';
                applyCoinsBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Apply Coins';
                appliedCoinsInput.value = 0;
            }
        });
        
        function updateTotalDisplay() {
            totalAmountElement.textContent = `₱${currentTotal.toFixed(2)}`;
            modalTotalAmountElement.textContent = `${currentTotal.toFixed(2)}`;
        }
        
        // Payment modal functionality
        const paymentModal = document.getElementById('payment-modal');
        const closeModal = document.getElementById('close-modal');
        
        payButton.addEventListener('click', function() {
            paymentMethodInput.value = selectedPaymentMethod;
            setModalScrollLock(true);
            switch(selectedPaymentMethod) {
                case 'gcash':
                    modalPaymentMethodElement.textContent = 'Payment processed through GCash';
                    paymentInstructionsElement.innerHTML = `
                        <div class="mb-2">
                            <p class="text-textdark font-medium mb-1">To complete your payment:</p>
                            <ol class="list-decimal list-inside text-sm text-textlight space-y-1">
                                <li>Open your GCash app</li>
                                <li>Go to Pay QR</li>
                                <li>Scan the QR code below</li>
                                <li>Confirm the amount of ₱${currentTotal.toFixed(2)}</li>
                            </ol>
                            <div class="bg-white p-2 rounded-lg border border-gray-200 mt-2 flex justify-center">
                                <img src="https://placehold.co/150x150/ffffff/7d310a?text=GCash+QR" alt="GCash QR Code" class="w-20 h-20">
                            </div>
                        </div>
                    `;
                    break;
                case 'self-pickup':
                    // Get branch info from PHP session (headeruser.php)
                    const branch = <?php echo json_encode(isset($user_branch) ? $user_branch : ['name' => 'Deparo', 'lat' => 14.752338, 'lng' => 121.017677]); ?>;
                    function getReadyDateTime() {
                        let now = new Date();
                        now.setHours(now.getHours() + 5);
                        let readyDateObj = new Date(now);
                        let readyHour = readyDateObj.getHours();
                        if (readyHour < 9 || readyHour >= 18) {
                            readyDateObj.setDate(readyDateObj.getDate() + 1);
                            readyDateObj.setHours(9, 0, 0, 0);
                        }
                        return {
                            date: readyDateObj.toLocaleDateString(),
                            time: readyDateObj.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                        };
                    }
                    const ready = getReadyDateTime();
                    modalPaymentMethodElement.textContent = 'Pay when you pickup your order';
                    paymentInstructionsElement.innerHTML = `
                        <div class="mb-4">
                            <p class="block mb-2 font-semibold text-primary">Branch:</p>
                            <div class="bg-gray-50 p-3 rounded-lg text-sm mb-2">
                                <p class="font-medium text-primary">${branch.name} Branch</p>
                                <p class="text-textlight mt-1">${branch.address ? branch.address : ''}</p>
                                <p class="text-textlight">Ready: <span class="font-semibold text-primary">${ready.date} ${ready.time}</span></p>
                                <p class="text-textlight">Mon-Sat: 9:00 AM - 6:00 PM</p>
                            </div>
                            <div class="mb-2">
                                <iframe width="100%" height="180" style="border:0" loading="lazy" allowfullscreen
                                    src="https://www.google.com/maps?q=${branch.lat},${branch.lng}&hl=es;z=16&output=embed">
                                </iframe>
                            </div>
                            <p class="text-xs text-textlight mt-3">We will notify you when your order is ready for pickup. Please bring a valid ID.</p>
                        </div>
                    `;
                    break;
                case 'cod':
                    modalPaymentMethodElement.textContent = 'Cash on Delivery';
                    paymentInstructionsElement.innerHTML = `
                        <div class="mb-4">
                            <ul class="list-disc list-inside text-sm text-textdark mb-3">
                                <li>Please prepare the exact amount for our delivery personnel.</li>
                                <li>Check your order and shipping details before confirming.</li>
                                <li>Our staff will provide an official receipt upon payment.</li>
                            </ul>
                            <div class="flex items-center gap-2 text-xs text-textlight mt-2 justify-center">
                                <i class="fas fa-shield-alt text-primary"></i>
                                <span>Safe and secure payment at your doorstep</span>
                            </div>
                        </div>
                    `;
                    break;
            }
            paymentModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });

        // Intercept form submit for COD and Self Pick Up
        const paymentForm = document.getElementById('payment-form');
        paymentForm.addEventListener('submit', function(e) {
            const method = paymentMethodInput.value;
            if (method === 'cod' || method === 'self-pickup') {
                e.preventDefault();
                const formData = new FormData(paymentForm);
                fetch('processes/save_order.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Redirect to order_confirmation.php with POST
                        const redirectForm = document.createElement('form');
                        redirectForm.method = 'POST';
                        redirectForm.action = 'order_confirmation.php';
                        redirectForm.style.display = 'none';
                        // Pass cart items and payment method
                        redirectForm.innerHTML = `
                            <input type="hidden" name="selected_cart_items" value='${formData.get('selected_cart_items')}'>
                            <input type="hidden" name="payment_method" value='${formData.get('payment_method')}'>
                        `;
                        document.body.appendChild(redirectForm);
                        redirectForm.submit();
                    } else {
                        alert('Order failed: ' + (data.message || 'Please try again.'));
                    }
                })
                .catch(() => alert('Order failed. Please try again.'));
            }
            // else, allow normal submit (for GCash)
        });
        
        closeModal.addEventListener('click', function() {
            paymentModal.classList.add('hidden');
            setModalScrollLock(false);
        });
        
        // Close modal when clicking outside
        paymentModal.addEventListener('click', function(e) {
            if (e.target === paymentModal) {
                paymentModal.classList.add('hidden');
                setModalScrollLock(false);
            }
        });
        
        // Confirm info checkbox logic for payment modal
        const confirmInfoCheckbox = document.getElementById('confirm-info-checkbox');
        const confirmPaymentBtn = document.getElementById('confirm-payment-btn');
        if (confirmInfoCheckbox && confirmPaymentBtn) {
            function updateConfirmBtnState() {
                confirmPaymentBtn.disabled = !confirmInfoCheckbox.checked;
                if (confirmPaymentBtn.disabled) {
                    confirmPaymentBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                    confirmPaymentBtn.classList.remove('bg-primary');
                } else {
                    confirmPaymentBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    confirmPaymentBtn.classList.add('bg-primary');
                }
            }
            updateConfirmBtnState();
            confirmInfoCheckbox.addEventListener('change', updateConfirmBtnState);
        }
    });
    </script>
</body>
</html>