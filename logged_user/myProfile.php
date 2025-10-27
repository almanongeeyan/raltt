<?php
// Ensure this file handles session start and user check
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Assume includes/headeruser.php handles the necessary header and potential security checks
include '../includes/headeruser.php';

// Database connection logic from the original code
require_once '../connection/connection.php';
$user_id = $_SESSION['user_id'] ?? null;
$userData = null;
$userPassword = null;

if ($user_id) {
    try {
        // Fetch main user data
        $stmt = $db_connection->prepare("SELECT full_name, house_address, full_address, phone_number, email, created_at, referral_code FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$user_id]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch password hash to check if a password is set
        $stmtPwd = $db_connection->prepare("SELECT password_hash FROM users WHERE id = ? LIMIT 1");
        $stmtPwd->execute([$user_id]);
        $userPassword = $stmtPwd->fetchColumn();

    } catch (PDOException $e) {
        // Handle database error gracefully
        error_log("Database Error: " . $e->getMessage());
        $userData = null; // Prevent displaying incomplete data
        $userPassword = null;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Rich Anne Lea Tiles Trading</title>
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
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'slide-down': 'slideDown 0.6s ease-out',
                        'bounce-in': 'bounceIn 0.8s ease-out',
                        'pulse-gentle': 'pulseGentle 2s infinite',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' }, },
                        slideUp: { '0%': { transform: 'translateY(20px)', opacity: '0' }, '100%': { transform: 'translateY(0)', opacity: '1' }, },
                        slideDown: { '0%': { transform: 'translateY(-20px)', opacity: '0' }, '100%': { transform: 'translateY(0)', opacity: '1' }, },
                        bounceIn: { '0%': { transform: 'scale(0.3)', opacity: '0' }, '50%': { transform: 'scale(1.05)', opacity: '0.8' }, '100%': { transform: 'scale(1)', opacity: '1' }, },
                        pulseGentle: { '0%, 100%': { transform: 'scale(1)' }, '50%': { transform: 'scale(1.02)' }, }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fef8f4 0%, #f9f5f2 100%);
            min-height: 100vh;
        }

        .page-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .back-btn { background: #7d310a; transition: all 0.3s ease; }
        .back-btn:hover { background: #a34a20; transform: translateY(-1px); }
        .form-box { background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(125, 49, 10, 0.08); transition: all 0.3s ease; border: 1px solid #f0e6df; }
        .form-box:hover { box-shadow: 0 4px 20px rgba(125, 49, 10, 0.12); }
        .profile-header, .order-header { background: #fef8f4; border-bottom: 1px solid #f0e6df; }
        .edit-profile-btn { background: #7d310a; transition: all 0.3s ease; }
        .edit-profile-btn:hover { background: #a34a20; transform: translateY(-1px); }
        .order-tabs { border-bottom: 1px solid #f0e6df; }
        .tab { color: #777; transition: all 0.2s ease; cursor: pointer; position: relative; }
        .tab.active { color: #7d310a; font-weight: 600; }
        .tab.active::after { content: ''; position: absolute; bottom: -1px; left: 0; width: 100%; height: 2px; background: #7d310a; }
        .tab:hover { color: #7d310a; }
        .order-columns { background: #fef8f4; border-radius: 6px; }
        .orders-drawer { background: white; border: 1px solid #f0e6df; border-radius: 8px; transition: all 0.3s ease; }
        .orders-drawer:hover { border-color: #e8a56a; box-shadow: 0 2px 8px rgba(125, 49, 10, 0.05); }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal-content { background-color: white; border-radius: 12px; box-shadow: 0 10px 25px rgba(125, 49, 10, 0.15); width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; }
        .modal-header { background: #fef8f4; border-top-left-radius: 12px; border-top-right-radius: 12px; border-bottom: 1px solid #f0e6df; }
        .close-btn { color: #7d310a; transition: all 0.2s ease; }
        .close-btn:hover { color: #a34a20; }
        .form-input { border: 1px solid #e8d9cf; border-radius: 6px; transition: all 0.3s ease; }
        .form-input:focus { border-color: #7d310a; box-shadow: 0 0 0 3px rgba(125, 49, 10, 0.1); }
        .submit-btn { background: #7d310a; transition: all 0.3s ease; }
        .submit-btn:hover { background: #a34a20; }
        .status-badge { padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 500; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-shipped { background: #dbeafe; color: #1e40af; }
        .status-delivered { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .reward-card { background: linear-gradient(135deg, #7d310a 0%, #a34a20 100%); color: white; }
        .coins-badge { background: #fef3c7; color: #92400e; border: 2px solid #f59e0b; }
        .feature-card { background: #fef8f4; border: 1px solid #f0e6df; transition: all 0.3s ease; }
        .feature-card:hover { border-color: #e8a56a; transform: translateY(-2px); }

        /* Sidebar Styles */
        .sidebar {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(125, 49, 10, 0.10);
            border: 1.5px solid #e8a56a;
            height: fit-content;
            position: sticky;
            top: 120px;
            padding-top: 0.5rem;
        }

        .sidebar-section {
            padding: 1.5rem 1rem 1.5rem 1rem;
            border-bottom: 1.5px solid #f0e6df;
        }

        .sidebar-section:last-child {
            border-bottom: none;
        }

        .sidebar-title {
            color: #7d310a;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.15rem;
            letter-spacing: 0.5px;
            padding-left: 0.5rem;
        }

        /* Combined style for all navigable sidebar items for consistency */
        .sidebar-nav-item {
            display: flex;
            align-items: center;
            padding: 0.85rem 1.2rem;
            margin-bottom: 0.5rem;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.18s cubic-bezier(.4,0,.2,1);
            color: #7d310a;
            background: #f9f5f2;
            font-weight: 500;
            border: 1.5px solid transparent;
            box-shadow: 0 1px 4px rgba(125,49,10,0.04);
            /* Reset subitem specific font size for consistency */
            font-size: 1rem;
        }

        .sidebar-nav-item:hover {
            background: #e8a56a;
            color: #fff;
            border-color: #cf8756;
            box-shadow: 0 2px 8px rgba(125,49,10,0.10);
            transform: translateY(-2px) scale(1.03);
        }

        .sidebar-nav-item.active {
            background: #cf8756;
            color: #fff;
            font-weight: 700;
            border-color: #e8a56a;
            box-shadow: 0 2px 12px rgba(125,49,10,0.12);
            transform: scale(1.04);
        }

        .sidebar-nav-item i {
            width: 22px;
            text-align: center;
            font-size: 1.15rem;
            margin-right: 0.85rem;
        }

        .sidebar-nav-item span {
            flex: 1;
            text-align: left;
        }

        /* Profile Info Styles */
        .profile-info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .info-label {
            color: #777;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .info-value {
            color: #333;
            font-size: 1rem;
            font-weight: 600;
        }

        /* Disabled Section Styles */
        .disabled-section {
            position: relative;
        }

        .disabled-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(2px);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 10;
            padding: 2rem;
            text-align: center;
        }

        .disabled-icon {
            color: #7d310a;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .disabled-text {
            color: #7d310a;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .disabled-subtext {
            color: #777;
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .forms-row { flex-direction: column; }
            .order-columns { display: none; }
            .orders-drawer { grid-template-columns: 1fr; gap: 12px; }
            .sidebar {
                position: static;
                margin-bottom: 1.5rem;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(125,49,10,0.08);
                border: 1px solid #f0e6df;
                padding-top: 0;
            }
            .profile-info-grid { grid-template-columns: 1fr; }
        }

        @media (min-width: 1024px) {
            .profile-info-grid { grid-template-columns: repeat(2, 1fr); }
        }

        /* Animation classes */
        .animate-fade-in { animation: fadeIn 0.5s ease-in-out; }
        .animate-slide-up { animation: slideUp 0.6s ease-out; }
        .animate-slide-down { animation: slideDown 0.6s ease-out; }
        .animate-bounce-in { animation: bounceIn 0.8s ease-out; }

        /* Toast Notification */
        .toast {
            position: fixed;
            top: 100px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1001;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }

        .toast.show {
            transform: translateX(0);
        }

        /* Content Section */
        .content-section {
            /* Keep initial state hidden until JS runs, except default */
            display: none;
        }
    </style>
</head>

<body class="min-h-screen pt-24">
    <div class="container mx-auto px-4 py-8">
        <div class="page-container">
            <div class="mb-6 animate-slide-down">
                <button class="back-btn text-white font-medium py-2 px-4 rounded-lg flex items-center" onclick="window.history.back();">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    <span>Back</span>
                </button>
            </div>
    <?php
    // Count cancelled orders for this user
    $cancelCount = 0;
    if ($user_id) {
        $stmtCancel = $db_connection->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND order_status = 'cancelled' AND cancelled_by_user = 1");
        $stmtCancel->execute([$user_id]);
        $cancelCount = $stmtCancel->fetchColumn();
    }
    ?>
    <div id="cancelModal" class="modal" style="display:none;">
        <div class="modal-content animate-bounce-in" style="max-width:400px;">
            <div class="modal-header p-4 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-primary">Cancel Order</h3>
                <button class="close-btn text-xl" onclick="closeModal('cancelModal')">×</button>
            </div>
            <div class="p-4">
                <?php if ($cancelCount >= 3) { ?>
                    <div class="text-center text-red-600 font-semibold mb-4">You have reached the maximum of 3 cancellations.</div>
                <?php } ?>
                <div class="text-center text-primary font-semibold mb-2">Remaining cancel attempts: <?php echo max(0, 3 - $cancelCount); ?> / 3</div>
                <form id="cancelOrderForm">
                    <input type="hidden" name="order_reference" id="cancelOrderReference">
                    <label class="block text-textdark font-medium mb-2 text-sm">Reason for cancellation</label>
                    <select name="cancel_reason" id="cancelReason" class="form-input w-full p-2 mb-4 text-sm text-gray-800 bg-white" style="color:#333;" <?php echo ($cancelCount >= 3) ? 'disabled' : ''; ?>>
                        <option value="" style="color:#777;">Select reason...</option>
                        <option value="Found a better price elsewhere">Found a better price elsewhere</option>
                        <option value="Ordered by mistake">Ordered by mistake</option>
                        <option value="Change of mind">Change of mind</option>
                        <option value="Shipping is too slow">Shipping is too slow</option>
                        <option value="Product is not as described">Product is not as described</option>
                        <option value="Other">Other</option>
                    </select>
                    <div id="otherReasonBox" style="display:none;">
                        <label class="block text-textdark font-medium mb-2 text-sm">Please specify your reason</label>
                        <input type="text" id="otherReasonInput" name="other_reason" class="form-input w-full p-2 mb-4 text-sm text-gray-800 bg-white" maxlength="200" placeholder="Type your reason here...">
                    </div>
                    <button type="submit" id="confirmCancelBtn" class="submit-btn w-full text-white font-medium py-2 rounded mt-2 text-sm bg-red-500 hover:bg-red-600" <?php echo ($cancelCount >= 3) ? 'disabled' : ''; ?>>Confirm Cancel</button>
                </form>
                <div class="text-xs text-gray-500 mt-2 text-center">You can only cancel up to 3 orders.</div>
            </div>
        </div>
    </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <div class="sidebar w-full lg:w-1/4 animate-slide-up">
                    <div class="sidebar-section">
                        <h3 class="sidebar-title">My Account</h3>
                        <div class="sidebar-nav-item active" data-section="profile">
                            <i class="fa-solid fa-user"></i>
                            <span>Profile</span>
                        </div>
                        <div class="sidebar-nav-item" data-section="change-password">
                            <i class="fa-solid fa-lock"></i>
                            <span>Change Password</span>
                        </div>
                    </div>

                    <div class="sidebar-section">
                        <h3 class="sidebar-title">My Orders</h3>
                        <div class="sidebar-nav-item" data-section="purchases">
                            <i class="fa-solid fa-box"></i>
                            <span>Purchases</span>
                        </div>
                        <div class="sidebar-nav-item" data-section="to-review">
                            <i class="fa-solid fa-star"></i>
                            <span>To Review</span>
                        </div>
                    </div>
                </div>

                <div class="main-content w-full lg:w-3/4">
                    
                    <div id="profile-section" class="content-section">
                        <div class="forms-row flex flex-col gap-6 mb-8">
                            <div class="form-box profile-box w-full animate-slide-up" style="animation-delay: 0.1s">
                                <form id="profileForm" class="p-6">
                                    <div class="profile-header flex justify-between items-center mb-6 pb-4">
                                        <div class="profile-title flex items-center text-primary font-semibold text-lg">
                                            <i class="fa-solid fa-user mr-3"></i>
                                            <span>Profile Information</span>
                                        </div>
                                        <button type="button" class="edit-profile-btn text-white font-medium py-2 px-4 rounded-lg flex items-center" onclick="openEditProfileModal()">
                                            <i class="fa-solid fa-pen mr-2"></i>
                                            Edit Profile
                                        </button>
                                    </div>
                                    <div class="profile-body">
                                        <div class="profile-info-grid">
                                            <div class="info-item">
                                                <p class="info-label">Full Name</p>
                                                <p class="info-value"><?php echo $userData && $userData['full_name'] ? htmlspecialchars($userData['full_name']) : 'No data'; ?></p>
                                            </div>
                                            <div class="info-item">
                                                <p class="info-label">House Address</p>
                                                <p class="info-value"><?php echo $userData && $userData['house_address'] ? htmlspecialchars($userData['house_address']) : 'No data'; ?></p>
                                            </div>
                                            <div class="info-item">
                                                <p class="info-label">Pin Point Address</p>
                                                <p class="info-value"><?php echo $userData && $userData['full_address'] ? htmlspecialchars($userData['full_address']) : 'No data'; ?></p>
                                            </div>
                                            <div class="info-item">
                                                <p class="info-label">Contact Number</p>
                                                <p class="info-value"><?php echo $userData && $userData['phone_number'] ? htmlspecialchars($userData['phone_number']) : 'No data'; ?></p>
                                            </div>
                                            <div class="info-item">
                                                <p class="info-label">Email Address</p>
                                                <p class="info-value"><?php echo $userData && $userData['email'] ? htmlspecialchars($userData['email']) : 'No data'; ?></p>
                                            </div>
                                            <div class="info-item">
                                                <p class="info-label">Account Creation Date</p>
                                                <p class="info-value"><?php echo $userData && $userData['created_at'] ? htmlspecialchars(date('F j, Y', strtotime($userData['created_at']))) : 'No data'; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <?php
                        // Recent Activity: Order Placed (excluding cancelled)
                        $orderPlacedCount = 0;
                        $productToReviewCount = 0;
                        if ($user_id) {
                            // Orders placed excluding cancelled
                            $stmtOrderPlaced = $db_connection->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND order_status != 'cancelled'");
                            $stmtOrderPlaced->execute([$user_id]);
                            $orderPlacedCount = $stmtOrderPlaced->fetchColumn();

                            // Products to review: completed orders, not yet reviewed
                            $stmtProductToReview = $db_connection->prepare(
                                "SELECT COUNT(*) FROM order_items oi
                                 JOIN orders o ON oi.order_id = o.order_id
                                 WHERE o.user_id = ? AND o.order_status = 'completed'
                                 AND oi.product_id NOT IN (SELECT product_id FROM product_reviews WHERE user_id = ?)"
                            );
                            $stmtProductToReview->execute([$user_id, $user_id]);
                            $productToReviewCount = $stmtProductToReview->fetchColumn();
                        }
                        ?>
                        <div class="form-box activity-box mb-8 animate-slide-up" style="animation-delay: 0.3s">
                            <div class="p-6">
                                <div class="profile-header flex items-center mb-6 pb-4">
                                    <div class="profile-title flex items-center text-primary font-semibold text-lg">
                                        <i class="fa-solid fa-clock-rotate-left mr-3"></i>
                                        <span>Recent Activity</span>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-3 bg-amber-50 rounded-lg animate-fade-in" style="animation-delay: 0.4s">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center mr-3">
                                                <i class="fa-solid fa-cart-shopping text-white text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-textdark">Order Placed</p>
                                                <p class="text-xs text-textlight">Total orders placed (excluding cancelled)</p>
                                            </div>
                                        </div>
                                        <span class="text-primary font-medium text-sm">Orders: <?php echo $orderPlacedCount; ?></span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-amber-50 rounded-lg animate-fade-in" style="animation-delay: 0.5s">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center mr-3">
                                                <i class="fa-solid fa-star text-white text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-textdark">Product to Review</p>
                                                <p class="text-xs text-textlight">Products delivered but not yet reviewed</p>
                                            </div>
                                        </div>
                                        <span class="text-primary font-medium text-sm">Products: <?php echo $productToReviewCount; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 animate-fade-in">
                            <div class="min-referral-box" style="background: #faf9f7; border: 1px solid #ececec; border-radius: 12px; padding: 1.7rem 1.2rem 1.2rem 1.2rem; text-align: center; box-shadow: 0 2px 12px rgba(125,49,10,0.07); position: relative;">
                                <div style="position: absolute; left: 50%; top: 0; transform: translate(-50%, -50%); background: #fff; border-radius: 50%; box-shadow: 0 1px 6px rgba(125,49,10,0.08); width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border: 1px solid #ececec;">
                                    <i class="fa-solid fa-gift" style="color: #cf8756; font-size: 1.3rem;"></i>
                                </div>
                                <div style="width: 100%; height: 3px; background: linear-gradient(90deg, #cf8756 0%, #ececec 100%); border-radius: 2px; margin: 0.7rem 0 1.2rem 0;"></div>
                                <div class="mb-2 text-base font-semibold text-gray-700">Referral Code</div>
                                <div class="flex flex-col items-center gap-2">
                                    <?php
                                    $referralCode = $userData && $userData['referral_code'] ? $userData['referral_code'] : null;
                                    ?>
                                    <span id="referralCode" style="font-family: 'Courier New', monospace; font-size: 1.25rem; font-weight: 600; letter-spacing: 1.5px; color: #333; background: #f5f5f5; border-radius: 6px; padding: 0.5rem 1.2rem; border: 1px dashed #e0e0e0;">
                                        <?php echo $referralCode ? htmlspecialchars($referralCode) : 'No data'; ?>
                                    </span>
                                    <?php if ($referralCode) { ?>
                                    <button id="copyButton" style="background: #f5f5f5; border: 1px solid #e0e0e0; color: #333; padding: 0.4rem 1rem; border-radius: 6px; font-size: 0.95rem; font-weight: 500; transition: background 0.2s; cursor: pointer;" type="button">
                                        <i class="fa-regular fa-copy" style="margin-right: 6px;"></i>Copy
                                    </button>
                                    <?php } ?>
                                </div>
                                <div class="mt-2 text-xs text-gray-500">Share this code to earn rewards</div>
                            </div>
                        </div>
                    </div>

                    <div id="change-password-section" class="content-section">
                        <div class="form-box p-6 animate-slide-up <?php echo (!$user_id || empty($userPassword)) ? 'disabled-section' : ''; ?>">
                            <?php if (!$user_id || empty($userPassword)) { ?>
                                <div class="disabled-overlay">
                                    <i class="fa-solid fa-lock disabled-icon"></i>
                                    <div class="disabled-text">Change Password Unavailable</div>
                                    <div class="disabled-subtext">
                                        <?php 
                                        if (!$user_id) {
                                            echo "You are not logged in.";
                                        } else {
                                            echo "Password is not set for your account.";
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="profile-header flex items-center mb-6 pb-4">
                                <div class="profile-title flex items-center text-primary font-semibold text-lg">
                                    <i class="fa-solid fa-lock mr-3"></i>
                                    <span>Change Password</span>
                                </div>
                            </div>
                            <form id="changePasswordForm" class="space-y-4 max-w-md">
                                <div style="position:relative;">
                                    <label class="block text-textdark font-medium mb-2 text-sm">Current Password</label>
                                    <input type="password" name="current_password" id="current_password" class="form-input w-full p-3 text-sm password-input" placeholder="Enter current password" <?php echo (!$user_id || empty($userPassword)) ? 'disabled' : ''; ?> required style="color:#333;">
                                    <span class="toggle-eye" onclick="togglePassword('current_password', this)" style="position:absolute; top:38px; right:16px; cursor:pointer; color:#7d310a;"><i class="fa-regular fa-eye"></i></span>
                                </div>
                                <div style="position:relative;">
                                    <label class="block text-textdark font-medium mb-2 text-sm">New Password</label>
                                    <input type="password" name="new_password" id="new_password" class="form-input w-full p-3 text-sm password-input" placeholder="Enter new password (min 8 chars)" <?php echo (!$user_id || empty($userPassword)) ? 'disabled' : ''; ?> required minlength="8" style="color:#333;">
                                    <span class="toggle-eye" onclick="togglePassword('new_password', this)" style="position:absolute; top:38px; right:16px; cursor:pointer; color:#7d310a;"><i class="fa-regular fa-eye"></i></span>
                                </div>
                                <div style="position:relative;">
                                    <label class="block text-textdark font-medium mb-2 text-sm">Confirm New Password</label>
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-input w-full p-3 text-sm password-input" placeholder="Confirm new password" <?php echo (!$user_id || empty($userPassword)) ? 'disabled' : ''; ?> required style="color:#333;">
                                    <span class="toggle-eye" onclick="togglePassword('confirm_password', this)" style="position:absolute; top:38px; right:16px; cursor:pointer; color:#7d310a;"><i class="fa-regular fa-eye"></i></span>
                                </div>
                                <div id="passwordError" class="text-red-600 text-sm font-medium" style="display:none;"></div>
                                <button type="submit" id="updatePasswordBtn" class="submit-btn text-white font-medium py-3 px-6 rounded mt-4 text-sm" <?php echo (!$user_id || empty($userPassword)) ? 'disabled' : ''; ?>>
                                    Update Password
                                </button>
                            </form>
                        </div>
                    </div>

                    <div id="purchases-section" class="content-section">
                        <div class="order-container form-box p-6 animate-slide-up">
                            <div class="order-header flex items-center text-primary font-semibold text-lg mb-6 pb-4">
                                <i class="fa-solid fa-box mr-3"></i>
                                <span>Order History</span>
                            </div>

                            <div class="order-tabs flex flex-wrap gap-4 md:gap-6 mb-6">
                                <div class="tab active py-2 px-1" data-tab="all-orders">All Orders</div>
                                <div class="tab py-2 px-1" data-tab="to-ship">To Ship</div>
                                <div class="tab py-2 px-1" data-tab="to-receive">To Receive</div>
                                <div class="tab py-2 px-1" data-tab="completed">Completed</div>
                                <div class="tab py-2 px-1" data-tab="cancelled">Cancelled</div>
                            </div>

                            <hr class="order-line border-gray-200 mb-6">

                            <div class="order-columns hidden md:grid grid-cols-8 gap-4 text-textlight font-medium text-sm mb-4 pb-3 px-4">
                                <div class="col product-col col-span-4">Product</div>
                                <div class="col qty-col col-span-1 text-center">Qty</div>
                                <div class="col price-col col-span-2 text-center">Total</div>
                                <div class="col status-col col-span-1 text-center">Status</div>
                            </div>

                            <div class="orders-tab-content-container">
                                <?php
                                // Helper function for status badge class
                                function getStatusClass($status) {
                                    switch ($status) {
                                        case 'pending':
                                        case 'processing': return 'status-pending';
                                        case 'paid': return 'status-shipped';
                                        case 'ready_for_pickup': return 'status-shipped';
                                        case 'otw': return 'status-shipped';
                                        case 'completed': return 'status-delivered';
                                        case 'cancelled': return 'status-cancelled';
                                        default: return 'status-pending';
                                    }
                                }

                                $orderTabs = [
                                    'all-orders' => '',
                                    // 'to-ship' should NOT include 'ready_for_pickup'
                                    'to-ship' => "order_status IN ('pending','processing','paid')",
                                    // 'to-receive' should include 'ready_for_pickup' and 'otw'
                                    'to-receive' => "order_status IN ('paid','ready_for_pickup','otw')",
                                    'completed' => "order_status = 'completed'",
                                    'cancelled' => "order_status = 'cancelled'"
                                ];

                                foreach ($orderTabs as $tab => $where) {
                                    $displayStyle = ($tab === 'all-orders' ? 'display:flex' : 'display:none');
                                    $ordersList = [];
                                    if ($user_id) {
                                        $query = "SELECT o.*, oi.order_item_id, oi.product_id, oi.quantity, oi.unit_price, p.product_name, p.product_image, b.branch_name
                                            FROM orders o
                                            JOIN order_items oi ON o.order_id = oi.order_id
                                            JOIN products p ON oi.product_id = p.product_id
                                            JOIN branches b ON o.branch_id = b.branch_id
                                            WHERE o.user_id = ?";
                                        if ($where) {
                                            $query .= " AND $where";
                                        }
                                        $query .= " ORDER BY o.order_date DESC, oi.order_item_id ASC";
                                        $stmt = $db_connection->prepare($query);
                                        $stmt->execute([$user_id]);
                                        $ordersList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    }

                                    // Group items by order
                                    $groupedOrders = [];
                                    foreach ($ordersList as $row) {
                                        $oid = $row['order_id'];
                                        if (!isset($groupedOrders[$oid])) {
                                            $groupedOrders[$oid] = [
                                                'order_reference' => $row['order_reference'],
                                                'order_date' => $row['order_date'],
                                                'order_status' => $row['order_status'],
                                                'total_amount' => $row['total_amount'],
                                                'branch_name' => $row['branch_name'],
                                                'items' => []
                                            ];
                                        }
                                        $groupedOrders[$oid]['items'][] = [
                                            'product_name' => $row['product_name'],
                                            'product_image' => $row['product_image'],
                                            'quantity' => $row['quantity'],
                                            'unit_price' => $row['unit_price']
                                        ];
                                    }

                                    $flexClass = (!empty($groupedOrders) ? 'flex-col gap-4' : 'justify-center items-center py-12');
                                    echo '<div class="orders-tab-content flex ' . $flexClass . '" id="' . $tab . '" style="' . $displayStyle . '">';
                                    if (!empty($groupedOrders)) {
                                        foreach ($groupedOrders as $order) {
                                            echo '<div class="orders-drawer p-4 mb-4 rounded-lg border border-gray-200 shadow-sm animate-fade-in">';
                                            echo '<div class="flex flex-wrap justify-between items-center mb-3">';
                                            echo '<span class="order-ref font-semibold text-xs text-primary">Order Ref: ' . htmlspecialchars($order['order_reference']) . '</span>';
                                            echo '<span class="date text-xs text-textlight">' . date('F j, Y', strtotime($order['order_date'])) . '</span>';
                                            echo '</div>';
                                            // Items list
                                            foreach ($order['items'] as $item) {
                                                $productTotal = $item['unit_price'] * $item['quantity'];
                                                echo '<div class="grid grid-cols-1 md:grid-cols-8 md:gap-4 items-center py-2">';
                                                    echo '<div class="product-info flex items-center col-span-4 mb-3 md:mb-0">';
                                                        if (!empty($item['product_image'])) {
                                                            $imgSrc = "data:image/jpeg;base64," . base64_encode($item['product_image']);
                                                        } else {
                                                            $imgSrc = "https://placehold.co/64x64/f9f5f2/7d310a?text=IMG";
                                                        }
                                                        echo '<img src="' . $imgSrc . '" alt="Product" class="w-12 h-12 rounded-lg object-cover mr-4">';
                                                        echo '<div class="product-text flex flex-col">';
                                                            echo '<span class="title font-semibold text-textdark text-sm">' . htmlspecialchars($item['product_name']) . '</span>';
                                                        echo '</div>';
                                                    echo '</div>';
                                                    echo '<div class="qty text-textdark font-medium text-center col-span-1 mb-2 md:mb-0 text-sm">' . $item['quantity'] . '</div>';
                                                    echo '<div class="price text-primary font-semibold text-center col-span-2 mb-2 md:mb-0 text-sm">₱' . number_format($productTotal, 2) . '</div>';
                                                    echo '<div class="status text-center col-span-1 mb-2 md:mb-0">';
                                                        $statusText = ($order['order_status'] === 'otw') ? 'Out for Delivery' : ucfirst($order['order_status']);
                                                        echo '<span class="status-badge ' . getStatusClass($order['order_status']) . '">' . $statusText . '</span>';
                                                    echo '</div>';
                                                    // Cancel button for to-ship only, but not for 'processing' orders
                                                    if ($tab === "to-ship") {
                                                        echo '<div class="col-span-8 flex justify-end mt-2">';
                                                        if ($cancelCount >= 3 || strtolower($order['order_status']) === 'processing') {
                                                            echo '<button type="button" class="cancel-btn px-3 py-1 rounded bg-gray-400 text-white text-xs font-semibold shadow cursor-not-allowed" disabled>Cancel</button>';
                                                        } else {
                                                            echo '<button type="button" class="cancel-btn px-3 py-1 rounded bg-red-500 text-white text-xs font-semibold shadow hover:bg-red-600 transition" onclick="openCancelModal(' . htmlspecialchars(json_encode($order['order_reference'])) . ')">Cancel</button>';
                                                        }
                                                        echo '</div>';
                                                    }
                                                echo '</div>';
                                            }
                                            echo '</div>';
                                            // Modal and JS moved outside PHP
                                        }
                                    } else {
                                        echo '<div class="text-center">';
                                        echo '<i class="fa-solid fa-box-open text-4xl text-textlight mb-4"></i>';
                                        echo '<p class="text-textlight text-lg">No orders found</p>';
                                        echo '<p class="text-textlight text-sm mt-2">Your ' . str_replace('-', ' ', $tab) . ' will appear here</p>';
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div id="to-review-section" class="content-section">
                        <div class="form-box p-6 animate-slide-up">
                            <div class="profile-header flex items-center mb-6 pb-4">
                                <div class="profile-title flex items-center text-primary font-semibold text-lg">
                                    <i class="fa-solid fa-star mr-3"></i>
                                    <span>Products to Review</span>
                                </div>
                            </div>
                            <?php
                            // Fetch completed orders that have not been reviewed
                            $toReviewList = [];
                            if ($user_id) {
                                $query = "SELECT o.*, oi.order_item_id, oi.product_id, oi.quantity, oi.unit_price, p.product_name, p.product_image, b.branch_name
                                    FROM orders o
                                    JOIN order_items oi ON o.order_id = oi.order_id
                                    JOIN products p ON oi.product_id = p.product_id
                                    JOIN branches b ON o.branch_id = b.branch_id
                                    WHERE o.user_id = ? AND o.order_status = 'completed'
                                    AND oi.product_id NOT IN (SELECT product_id FROM product_reviews WHERE user_id = ?)";
                                $query .= " ORDER BY o.order_date DESC, oi.order_item_id ASC";
                                $stmt = $db_connection->prepare($query);
                                $stmt->execute([$user_id, $user_id]);
                                $toReviewList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            }

                            // Group by order
                            $groupedToReview = [];
                            foreach ($toReviewList as $row) {
                                $oid = $row['order_id'];
                                if (!isset($groupedToReview[$oid])) {
                                    $groupedToReview[$oid] = [
                                        'order_reference' => $row['order_reference'],
                                        'order_date' => $row['order_date'],
                                        'items' => []
                                    ];
                                }
                                $groupedToReview[$oid]['items'][] = [
                                    'order_item_id' => $row['order_item_id'],
                                    'product_id' => $row['product_id'],
                                    'product_name' => $row['product_name'],
                                    'product_image' => $row['product_image'],
                                    'quantity' => $row['quantity'],
                                    'unit_price' => $row['unit_price'],
                                    'branch_name' => $row['branch_name'],
                                    'order_date' => $row['order_date']
                                ];
                            }

                            if (!empty($groupedToReview)) {
                                foreach ($groupedToReview as $order) {
                                    echo '<div class="orders-drawer p-4 mb-4 rounded-lg border border-gray-200 shadow-sm animate-fade-in">';
                                    echo '<div class="flex flex-wrap justify-between items-center mb-3">';
                                    echo '<span class="order-ref font-semibold text-xs text-primary">Order Ref: ' . htmlspecialchars($order['order_reference']) . '</span>';
                                    echo '<span class="date text-xs text-textlight">' . date('F j, Y', strtotime($order['order_date'])) . '</span>';
                                    echo '</div>';
                                    foreach ($order['items'] as $item) {
                                        echo '<div class="review-item p-4 border border-gray-200 rounded-lg mb-2">';
                                        echo '<div class="flex items-center mb-4">';
                                        if (!empty($item['product_image'])) {
                                            $imgSrc = "data:image/jpeg;base64," . base64_encode($item['product_image']);
                                        } else {
                                            $imgSrc = "https://placehold.co/64x64/f9f5f2/7d310a?text=IMG";
                                        }
                                        echo '<img src="' . $imgSrc . '" alt="Product" class="w-16 h-16 rounded-lg object-cover mr-4">';
                                        echo '<div>';
                                        echo '<h3 class="font-medium text-textdark">' . htmlspecialchars($item['product_name']) . '</h3>';
                                        echo '<p class="text-textlight text-sm">Delivered on ' . date('F j, Y', strtotime($item['order_date'])) . '</p>';
                                        echo '</div>';
                                        echo '</div>';
                                        // Review form
                                        echo '<form class="flex flex-col md:flex-row md:items-center gap-2 review-ajax-form" method="post" action="submit_review.php" data-item-id="' . $item['order_item_id'] . '">';
                                        echo '<input type="hidden" name="order_item_id" value="' . $item['order_item_id'] . '">';
                                        echo '<input type="hidden" name="product_id" value="' . $item['product_id'] . '">';
                                        $starName = 'rating_' . $item['order_item_id'];
                                        echo '<div class="flex items-center gap-1 star-rating-group" data-group="' . $item['order_item_id'] . '">';
                                        echo '<span class="text-sm text-textlight mr-2">Rate:</span>';
                                        for ($star = 1; $star <= 5; $star++) {
                                            echo '<input type="radio" id="star' . $star . '_' . $item['order_item_id'] . '" name="rating" value="' . $star . '" style="display:none">';
                                            echo '<label for="star' . $star . '_' . $item['order_item_id'] . '" class="star-label" data-star="' . $star . '" data-group="' . $item['order_item_id'] . '"><i class="fa fa-star text-xl text-gray-300 cursor-pointer"></i></label>';
                                        }
                                        echo '</div>';
                                        echo '<div class="feedback-btns mt-2 flex flex-wrap gap-2" id="feedback-btns-' . $item['order_item_id'] . '"></div>';
                                        echo '<button type="submit" class="submit-btn text-white font-bold py-2 px-4 rounded-md shadow text-sm mt-2 transition-all duration-200 hover:bg-accent hover:scale-105" style="display:none" id="submit-btn-' . $item['order_item_id'] . '">Submit Review</button>';
                                        echo '<input type="hidden" name="feedback" id="feedback-input-' . $item['order_item_id'] . '" value="">';
                                        echo '</form>';
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                }
                            } else {
                                echo '<div class="text-center">';
                                echo '<i class="fa-solid fa-box-open text-4xl text-textlight mb-4"></i>';
                                echo '<p class="text-textlight text-lg">No products to review</p>';
                                echo '<p class="text-textlight text-sm mt-2">Your completed orders will appear here for review</p>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="editProfileModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-20 modal-overlay hidden">
            <div class="modal-content bg-white rounded-2xl shadow-2xl mx-2 relative border border-gray-200 fade-in" style="width:600px;min-width:600px;max-width:600px;max-height:90vh;overflow-y:auto;z-index:1100;">
                <div class="px-8 pt-10 pb-6">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center mr-4 shadow-md">
                            <i class="fa-solid fa-user text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-primary">Edit Profile</h2>
                            <p class="text-sm text-gray-500 mt-1">Review and update your profile details</p>
                        </div>
                    </div>
                    <form id="editProfileForm" class="space-y-6">
                        <div class="space-y-2">
                            <label for="editFullName" class="block text-sm font-semibold text-gray-700">Full Name</label>
                            <div class="relative">
                                <input type="text" id="editFullName" name="full_name" value="<?php echo htmlspecialchars($userData['full_name'] ?? ''); ?>" class="input-focus mt-1 block w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm text-gray-900 bg-white" required readonly>
                                <div id="nameCheck" class="absolute right-4 top-1/2 transform -translate-y-1/2 hidden">
                                    <i class="fa-solid fa-check text-green-500"></i>
                                </div>
                                <button type="button" id="editNameBtn" class="absolute right-12 top-1/2 transform -translate-y-1/2 text-gray-400 edit-icon"><i class="fa-solid fa-pen"></i></button>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label for="editPhoneNumber" class="block text-sm font-semibold text-gray-700">Contact Number</label>
                            <div class="flex gap-3 items-center mt-1">
                                <div class="relative flex-1">
                                    <input type="text" id="editPhoneNumber" name="phone_number" value="<?php echo htmlspecialchars($userData['phone_number'] ?? ''); ?>" class="input-focus block w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm text-gray-900 bg-white" required readonly>
                                    <div id="phoneCheck" class="absolute right-4 top-1/2 transform -translate-y-1/2 hidden">
                                        <i class="fa-solid fa-check text-green-500"></i>
                                    </div>
                                    <span id="changeNumberText" class="absolute right-4 top-1/2 transform -translate-y-1/2 change-number" style="cursor:pointer; color:#7d310a; font-size:0.95rem;">
                                        <?php echo empty($userData['phone_number']) ? 'Add Number' : 'Change Number'; ?>
                                    </span>
                                    <div id="phoneFormatError" class="text-xs text-red-500 mt-1" style="display:none;"></div>
                                </div>
                                <button type="button" id="verifyBtn" class="px-4 py-3 bg-primary text-white rounded-lg font-semibold shadow hover:bg-secondary transition whitespace-nowrap text-sm hidden">Verify</button>
                            </div>
                            <div id="verificationFormContainer"></div>
                        </div>
                        <div class="space-y-2">
                            <label for="editHouseAddress" class="block text-sm font-semibold text-gray-700">House Address</label>
                            <div class="relative">
                                <input type="text" id="editHouseAddress" name="house_address" value="<?php echo htmlspecialchars($userData['house_address'] ?? ''); ?>" class="input-focus mt-1 block w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm text-gray-900 bg-white" required readonly>
                                <div id="addressCheck" class="absolute right-4 top-1/2 transform -translate-y-1/2 hidden">
                                    <i class="fa-solid fa-check text-green-500"></i>
                                </div>
                                <button type="button" id="editAddressBtn" class="absolute right-12 top-1/2 transform -translate-y-1/2 text-gray-400 edit-icon"><i class="fa-solid fa-pen"></i></button>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label for="editFullAddress" class="block text-sm font-semibold text-gray-700">Pin Point Address</label>
                            <div class="flex gap-3 items-center mt-1">
                                <div class="relative flex-1">
                                    <input type="text" id="editFullAddress" name="full_address" value="<?php echo htmlspecialchars($userData['full_address'] ?? ''); ?>" class="input-focus mt-1 block w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm text-gray-900 bg-white" readonly>
                                    <div id="locationCheck" class="absolute right-4 top-1/2 transform -translate-y-1/2 hidden">
                                        <i class="fa-solid fa-check text-green-500"></i>
                                    </div>
                                    <button type="button" id="editLocationBtn" class="absolute right-12 top-1/2 transform -translate-y-1/2 text-gray-400 edit-icon"><i class="fa-solid fa-pen"></i></button>
                                    <button type="button" id="locateMeBtn" class="px-4 py-3 bg-secondary text-white rounded-lg font-semibold shadow hover:bg-primary transition whitespace-nowrap text-sm" style="margin-left:4px; display:none;" onclick="locateMe()">
                                        <span id="locateText">Locate Me</span>
                                        <span id="locateSpinner" class="hidden animate-spin ml-1">⟳</span>
                                    </button>
                                </div>
                            </div>
                            <div id="mapContainer" class="mt-3 rounded-lg overflow-hidden border border-gray-200" style="max-height:150px; min-height:0; transition:max-height 0.3s; display:none;"></div>
                        </div>
                        
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>">

                        <div class="flex justify-end pt-4 gap-3">
                            <button type="button" id="cancelProfileBtn" class="w-1/2 px-6 py-3 bg-gray-200 text-gray-600 rounded-lg font-semibold shadow transition-all duration-300 text-sm" onclick="closeModal('editProfileModal')">Cancel</button>
                            <button type="submit" id="saveProfileBtn" class="w-1/2 px-6 py-3 bg-gray-300 text-gray-500 rounded-lg font-semibold shadow transition-all duration-300 text-sm" disabled>Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="profileUpdateNotif" class="fixed top-6 right-6 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg font-semibold text-sm transition-all duration-300 opacity-0 pointer-events-none" style="min-width:200px;">Profile updated successfully!</div>
        <script>
            // Store original values for comparison
            let originalProfile = {};
            let isNumberVerified = true;
            document.addEventListener('DOMContentLoaded', function() {
                // Store initial values
                originalProfile.full_name = document.getElementById('editFullName').value;
                originalProfile.phone_number = document.getElementById('editPhoneNumber').value;
                originalProfile.house_address = document.getElementById('editHouseAddress').value;
                originalProfile.full_address = document.getElementById('editFullAddress').value;

                // Edit button logic
                document.getElementById('editNameBtn').addEventListener('click', function() {
                    let input = document.getElementById('editFullName');
                    input.readOnly = false;
                    input.focus();
                    this.style.display = 'none';
                    document.getElementById('nameCheck').classList.add('hidden');
                    validateProfileForm();
                });
                document.getElementById('editAddressBtn').addEventListener('click', function() {
                    let input = document.getElementById('editHouseAddress');
                    input.readOnly = false;
                    input.focus();
                    this.style.display = 'none';
                    document.getElementById('addressCheck').classList.add('hidden');
                    validateProfileForm();
                });
                document.getElementById('editLocationBtn').addEventListener('click', function() {
                    let input = document.getElementById('editFullAddress');
                    input.readOnly = false;
                    input.focus();
                    this.style.display = 'none';
                    document.getElementById('locationCheck').classList.add('hidden');
                    document.getElementById('locateMeBtn').style.display = '';
                    validateProfileForm();
                });

                // Change/Add Number logic
                document.getElementById('changeNumberText').addEventListener('click', function() {
                    let input = document.getElementById('editPhoneNumber');
                    input.readOnly = false;
                    input.value = '';
                    input.focus();
                    this.style.display = 'none';
                    document.getElementById('verifyBtn').classList.remove('hidden');
                    isNumberVerified = false;
                    validateProfileForm();
                });

                // Twilio Verification
                document.getElementById('verifyBtn').addEventListener('click', function() {
                    const number = document.getElementById('editPhoneNumber').value;
                    const verifyBtn = document.getElementById('verifyBtn');
                    const phoneFormatError = document.getElementById('phoneFormatError');
                    phoneFormatError.style.display = 'none';
                    phoneFormatError.textContent = '';
                    if (!number.match(/^\+639\d{9}$/)) {
                        phoneFormatError.textContent = 'Invalid phone format. Use +639xxxxxxxxx.';
                        phoneFormatError.style.display = 'block';
                        return;
                    }
                    verifyBtn.disabled = true;
                    verifyBtn.textContent = 'Sending...';
                    // Check if number is already registered
                    fetch('/raltt/connection/check_phone_registered.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ phone: number })
                    })
                    .then(res => res.json())
                    .then(checkData => {
                        if (checkData.status === 'registered') {
                            phoneFormatError.textContent = 'This phone number is already registered.';
                            phoneFormatError.style.display = 'block';
                            verifyBtn.disabled = false;
                            verifyBtn.textContent = 'Verify';
                        } else {
                            // Send Twilio verification code
                            const formData = new FormData();
                            formData.append('phone', number);
                            fetch('/raltt/connection/send_verification_debug.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(resp => resp.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    showVerificationForm(number);
                                    startResendCooldown(verifyBtn);
                                } else {
                                    phoneFormatError.textContent = data.message || 'Failed to send code.';
                                    phoneFormatError.style.display = 'block';
                                    verifyBtn.disabled = false;
                                    verifyBtn.textContent = 'Verify';
                                }
                            });
                        }
                    });
                });

                // 2-minute cooldown for resend button
                function startResendCooldown(btn) {
                    let cooldown = 120;
                    btn.disabled = true;
                    updateBtnText();
                    let interval = setInterval(function() {
                        cooldown--;
                        updateBtnText();
                        if (cooldown <= 0) {
                            clearInterval(interval);
                            btn.disabled = false;
                            btn.textContent = 'Resend';
                        }
                    }, 1000);
                    function updateBtnText() {
                        btn.textContent = cooldown > 0 ? `Resend (${cooldown}s)` : 'Resend';
                    }
                }

                function showVerificationForm(phone) {
                    const container = document.getElementById('verificationFormContainer');
                    container.innerHTML = `
                        <label for="verification_code" class="block text-xs font-semibold text-gray-700 mb-1">Verification Code</label>
                        <div class="flex flex-col gap-2 items-stretch w-full">
                            <input type="text" id="verification_code" name="verification_code" maxlength="6" placeholder="Enter the 6-digit code" class="input-focus block w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm text-gray-900 bg-white" required style="min-width:0;">
                            <button type="button" id="confirmCodeBtn" class="px-4 py-3 bg-green-500 text-white rounded-lg font-semibold shadow hover:bg-green-600 transition text-sm w-full">Confirm</button>
                        </div>
                        <div id="codeStatus" class="text-xs mt-2"></div>
                    `;
                    document.getElementById('confirmCodeBtn').addEventListener('click', function() {
                        const code = document.getElementById('verification_code').value;
                        const codeStatusDiv = document.getElementById('codeStatus');
                        if (!code || code.length !== 6) {
                            codeStatusDiv.textContent = 'Please enter a valid 6-digit code.';
                            codeStatusDiv.style.color = 'red';
                            return;
                        }
                        this.disabled = true;
                        this.textContent = 'Checking...';
                        const formData = new FormData();
                        formData.append('phone', phone);
                        formData.append('code', code);
                        fetch('/raltt/connection/check_verification.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(resp => resp.json())
                        .then(data => {
                            if (data.status === 'success') {
                                codeStatusDiv.textContent = 'Phone number verified!';
                                codeStatusDiv.style.color = 'green';
                                isNumberVerified = true;
                                document.getElementById('editPhoneNumber').readOnly = true;
                                document.getElementById('verification_code').disabled = true;
                                this.disabled = true;
                                this.textContent = 'Verified';
                                validateProfileForm();
                            } else {
                                codeStatusDiv.textContent = 'Wrong verification code, please try again';
                                codeStatusDiv.style.color = 'red';
                                this.disabled = false;
                                this.textContent = 'Confirm';
                                document.getElementById('verification_code').disabled = false;
                            }
                        });
                    });
                }

                // Locate Me logic for Pin Point Address
                window.locateMe = function() {
                    const btn = document.getElementById('locateMeBtn');
                    const locateText = document.getElementById('locateText');
                    const locateSpinner = document.getElementById('locateSpinner');
                    const mapContainer = document.getElementById('mapContainer');
                    btn.disabled = true;
                    locateText.classList.add('hidden');
                    locateSpinner.classList.remove('hidden');
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(async function(pos) {
                            const lat = pos.coords.latitude;
                            const lng = pos.coords.longitude;
                            try {
                                const response = await fetch(`/raltt/connection/reverse_geocode.php?lat=${lat}&lng=${lng}`);
                                const data = await response.json();
                                if (data.address) {
                                    const addressParts = [
                                        data.address.house_number,
                                        data.address.road,
                                        data.address.neighbourhood,
                                        data.address.suburb,
                                        data.address.barangay,
                                        data.address.village,
                                        data.address.municipality,
                                        data.address.town,
                                        data.address.city_district,
                                        data.address.city,
                                        data.address.state_district,
                                        data.address.state,
                                        data.address.region,
                                        data.address.postcode,
                                        data.address.country
                                    ].filter(Boolean);
                                    let fullAddress = addressParts.join(', ');
                                    document.getElementById('editFullAddress').value = fullAddress;
                                } else if (data.display_name) {
                                    document.getElementById('editFullAddress').value = data.display_name;
                                } else {
                                    document.getElementById('editFullAddress').value = '';
                                }
                            } catch (err) {
                                document.getElementById('editFullAddress').value = '';
                            }
                            const mapUrl = `https://maps.google.com/maps?q=${lat},${lng}&z=15&output=embed`;
                            mapContainer.innerHTML = `<iframe width='100%' height='150' src='${mapUrl}' frameborder='0' style='border:0;'></iframe>`;
                            mapContainer.style.display = 'block';
                            locateText.classList.remove('hidden');
                            locateSpinner.classList.add('hidden');
                            btn.disabled = false;
                            validateProfileForm();
                        }, function(error) {
                            alert('Unable to retrieve your location. Please try again or enter manually.');
                            locateText.classList.remove('hidden');
                            locateSpinner.classList.add('hidden');
                            mapContainer.style.display = 'none';
                            btn.disabled = false;
                        });
                    } else {
                        alert('Geolocation is not supported by your browser.');
                        locateText.classList.remove('hidden');
                        locateSpinner.classList.add('hidden');
                        mapContainer.style.display = 'none';
                        btn.disabled = false;
                    }
                };

                // Validate on input
                document.getElementById('editFullName').addEventListener('input', validateProfileForm);
                document.getElementById('editPhoneNumber').addEventListener('input', function() {
                    document.getElementById('phoneFormatError').style.display = 'none';
                    validateProfileForm();
                });
                document.getElementById('editHouseAddress').addEventListener('input', validateProfileForm);
                document.getElementById('editFullAddress').addEventListener('input', validateProfileForm);


                function getChangedFields() {
                    const changed = {};
                    if (document.getElementById('editFullName').value !== originalProfile.full_name) changed.full_name = true;
                    if (document.getElementById('editPhoneNumber').value !== originalProfile.phone_number) changed.phone_number = true;
                    if (document.getElementById('editHouseAddress').value !== originalProfile.house_address) changed.house_address = true;
                    if (document.getElementById('editFullAddress').value !== originalProfile.full_address) changed.full_address = true;
                    return changed;
                }

                function validateProfileForm() {
                    let fullName = document.getElementById('editFullName').value.trim();
                    let phone = document.getElementById('editPhoneNumber').value.trim();
                    let address = document.getElementById('editHouseAddress').value.trim();
                    let location = document.getElementById('editFullAddress').value.trim();
                    let saveBtn = document.getElementById('saveProfileBtn');
                    let nameCheck = document.getElementById('nameCheck');
                    let phoneCheck = document.getElementById('phoneCheck');
                    let addressCheck = document.getElementById('addressCheck');
                    let locationCheck = document.getElementById('locationCheck');

                    // Show check if value changed from original (regardless of readonly)
                    nameCheck.classList.toggle('hidden', fullName === originalProfile.full_name || fullName === '');
                    phoneCheck.classList.toggle('hidden', phone === originalProfile.phone_number || phone === '');
                    addressCheck.classList.toggle('hidden', address === originalProfile.house_address || address === '');
                    locationCheck.classList.toggle('hidden', location === originalProfile.full_address || location === '');

                    let hasChanges = Object.keys(getChangedFields()).length > 0;
                    if (hasChanges && isNumberVerified) {
                        saveBtn.disabled = false;
                        saveBtn.classList.remove('bg-gray-300', 'text-gray-500');
                        saveBtn.classList.add('bg-primary', 'text-white', 'hover:bg-secondary');
                    } else {
                        saveBtn.disabled = true;
                        saveBtn.classList.remove('bg-primary', 'text-white', 'hover:bg-secondary');
                        saveBtn.classList.add('bg-gray-300', 'text-gray-500');
                    }
                }


                // AJAX save: only send changed fields
                document.getElementById('editProfileForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    let formData = new FormData();
                    const changed = getChangedFields();
                    if (changed.full_name) formData.append('full_name', document.getElementById('editFullName').value);
                    if (changed.phone_number) formData.append('phone_number', document.getElementById('editPhoneNumber').value);
                    if (changed.house_address) formData.append('house_address', document.getElementById('editHouseAddress').value);
                    if (changed.full_address) formData.append('full_address', document.getElementById('editFullAddress').value);
                    // Always send email for backend identification
                    formData.append('email', document.querySelector('input[name="email"]').value);
                    let xhr = new XMLHttpRequest();
                    xhr.open('POST', '../connection/save_shipping_info.php', true);
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            let res = {};
                            try { res = JSON.parse(xhr.responseText); } catch(e) {}
                            if (xhr.status === 200 && res.status === 'success') {
                                showProfileUpdateNotif();
                                // Update profile info in real time
                                const changed = getChangedFields();
                                if (changed.full_name) {
                                    document.querySelectorAll('.info-value')[0].textContent = document.getElementById('editFullName').value;
                                }
                                if (changed.phone_number) {
                                    document.querySelectorAll('.info-value')[3].textContent = document.getElementById('editPhoneNumber').value;
                                }
                                if (changed.house_address) {
                                    document.querySelectorAll('.info-value')[1].textContent = document.getElementById('editHouseAddress').value;
                                }
                                if (changed.full_address) {
                                    document.querySelectorAll('.info-value')[2].textContent = document.getElementById('editFullAddress').value;
                                }
                                setTimeout(function() {
                                    closeModal('editProfileModal');
                                }, 1200);
                                // Reset originalProfile to new values
                                originalProfile.full_name = document.getElementById('editFullName').value;
                                originalProfile.phone_number = document.getElementById('editPhoneNumber').value;
                                originalProfile.house_address = document.getElementById('editHouseAddress').value;
                                originalProfile.full_address = document.getElementById('editFullAddress').value;
                                validateProfileForm();
                            } else {
                                alert(res.message || 'Failed to update profile.');
                            }
                        }
                    };
                    xhr.send(formData);
                });

                function showProfileUpdateNotif() {
                    let notif = document.getElementById('profileUpdateNotif');
                    notif.style.opacity = '1';
                    notif.style.pointerEvents = 'auto';
                    setTimeout(function() {
                        notif.style.opacity = '0';
                        notif.style.pointerEvents = 'none';
                    }, 1800);
                }
            });
        </script>
    <div id="copyToast" class="toast">
        <i class="fa-solid fa-check-circle"></i>
        <span>Referral code copied successfully!</span>
    </div>

    <div id="cancelToast" class="toast" style="display:none; background:#10b981; top:2rem; right:2rem;">
        <i class="fa-solid fa-check-circle"></i>
        <span>Order cancelled successfully!</span>
    </div>
    <div id="reviewToast" class="toast" style="display:none; top:5rem; right:2rem;">
        <i class="fa-solid fa-check-circle"></i>
        <span>Product review successfully submitted!</span>
    </div>
    <div id="passwordToast" class="toast" style="display:none; background:#10b981; top:7rem; right:2rem;">
        <i class="fa-solid fa-check-circle"></i>
        <span>Password updated successfully!</span>
    </div>
    <style>
        #reviewToast.toast {
            position: fixed;
            top: 2rem;
            right: 2rem;
            z-index: 9999;
            min-width: 220px;
            box-shadow: 0 2px 12px rgba(16,185,129,0.12);
            background: #10b981;
            border-radius: 8px;
            border: 1px solid #059669;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            font-weight: 500;
            color: #fff;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }
        #reviewToast.toast.show {
            transform: translateX(0);
        }
    </style>
    </div>

    <script>
        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // AJAX review form submission
            document.querySelectorAll('.review-ajax-form').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(form);
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', form.action, true);
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            // Hide the review item
                            const reviewItem = form.closest('.review-item');
                            if (reviewItem) reviewItem.style.display = 'none';
                            // If all review-items in the order are hidden, hide the order card
                            const orderDrawer = form.closest('.orders-drawer');
                            if (orderDrawer && orderDrawer.querySelectorAll('.review-item:not([style*="display: none"])').length === 0) {
                                orderDrawer.style.display = 'none';
                            }
                            // Show toast with animation
                            const toast = document.getElementById('reviewToast');
                            if (toast) {
                                toast.style.display = 'flex';
                                toast.classList.add('show');
                                setTimeout(() => {
                                    toast.classList.remove('show');
                                    toast.style.display = 'none';
                                }, 3000);
                            }
                            // If no more review items, show 'No products to review' message
                            const reviewSection = document.querySelector('#to-review-section .form-box');
                            if (reviewSection && reviewSection.querySelectorAll('.review-item:not([style*="display: none"])').length === 0 && reviewSection.querySelectorAll('.orders-drawer:not([style*="display: none"])').length === 0) {
                                reviewSection.innerHTML = '<div class="text-center"><i class="fa-solid fa-box-open text-4xl text-textlight mb-4"></i><p class="text-textlight text-lg">No products to review</p><p class="text-textlight text-sm mt-2">Your completed orders will appear here for review</p></div>';
                            }
                        }
                    };
                    xhr.send(formData);
                });
            });
            // Show review toast if review was submitted
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('reviewed') === '1') {
                const toast = document.getElementById('reviewToast');
                if (toast) {
                    toast.classList.add('show');
                    setTimeout(() => { toast.classList.remove('show'); }, 3000);
                }
            }
            // Initialize sidebar navigation
            initSidebarNavigation();

            // Initialize copy button
            initCopyButton();

            // Initialize order tabs
            initOrderTabs();

            // Always show Profile section and set sidebar active
            showSection('profile-section');
            document.querySelectorAll('.sidebar-nav-item').forEach(function(item) {
                item.classList.remove('active');
            });
            document.querySelector('.sidebar-nav-item[data-section="profile"]').classList.add('active');

            // Add fade-in animation to body
            document.body.classList.add('animate-fade-in');

            // Star rating hover, click, and feedback logic
            document.querySelectorAll('.star-rating-group').forEach(function(group) {
                const groupId = group.getAttribute('data-group');
                const stars = group.querySelectorAll('.star-label');
                const radios = group.querySelectorAll('input[type="radio"]');
                let selected = 0;
                let feedbackBtns = document.getElementById('feedback-btns-' + groupId);
                let submitBtn = document.getElementById('submit-btn-' + groupId);
                let feedbackInput = document.getElementById('feedback-input-' + groupId);

                // Feedback options
                const awfulFeedback = [
                    'Awful quality',
                    'Very disappointed',
                    'Not as described',
                    'Would not recommend',
                    'Terrible packaging',
                    'Late delivery'
                ];
                const okayFeedback = [
                    'It’s okay',
                    'Average experience',
                    'Could be better',
                    'Met expectations',
                    'Neutral packaging'
                ];
                const perfectFeedback = [
                    'Perfect quality',
                    'Highly recommend',
                    'Exactly as described',
                    'Fast delivery',
                    'Excellent packaging',
                    'Great value'
                ];

                function renderFeedbackButtons(type) {
                    feedbackBtns.innerHTML = '';
                    let options = [];
                    if (type === 'awful') options = awfulFeedback;
                    else if (type === 'okay') options = okayFeedback;
                    else if (type === 'perfect') options = perfectFeedback;
                    options.forEach(function(text) {
                        let btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'px-3 py-1 rounded border border-gray-300 text-xs font-medium bg-white text-textdark hover:bg-primary hover:text-white transition';
                        btn.textContent = text;
                        btn.setAttribute('data-selected', '0');
                        btn.onclick = function() {
                            let selectedBtns = feedbackBtns.querySelectorAll('button[data-selected="1"]');
                            if (btn.getAttribute('data-selected') === '1') {
                                btn.setAttribute('data-selected', '0');
                                btn.classList.remove('bg-primary', 'text-white');
                                btn.classList.add('text-textdark');
                            } else if (selectedBtns.length < 3) {
                                btn.setAttribute('data-selected', '1');
                                btn.classList.add('bg-primary', 'text-white');
                                btn.classList.remove('text-textdark');
                            }
                            // Update hidden input with all selected feedbacks (comma separated)
                            let selectedTexts = Array.from(feedbackBtns.querySelectorAll('button[data-selected="1"]')).map(b => b.textContent);
                            feedbackInput.value = selectedTexts.join(', ');
                            submitBtn.style.display = selectedTexts.length > 0 ? 'inline-block' : 'none';
                        };
                        feedbackBtns.appendChild(btn);
                    });
                    feedbackBtns.style.display = 'flex';
                }

                stars.forEach(function(star, idx) {
                    star.addEventListener('mouseenter', function() {
                        for (let i = 0; i <= idx; i++) {
                            stars[i].querySelector('i').classList.remove('text-gray-300');
                            stars[i].querySelector('i').classList.add('text-yellow-400');
                        }
                        for (let i = idx + 1; i < stars.length; i++) {
                            stars[i].querySelector('i').classList.remove('text-yellow-400');
                            stars[i].querySelector('i').classList.add('text-gray-300');
                        }
                    });
                    star.addEventListener('mouseleave', function() {
                        for (let i = 0; i < stars.length; i++) {
                            if (i < selected) {
                                stars[i].querySelector('i').classList.remove('text-gray-300');
                                stars[i].querySelector('i').classList.add('text-yellow-400');
                            } else {
                                stars[i].querySelector('i').classList.remove('text-yellow-400');
                                stars[i].querySelector('i').classList.add('text-gray-300');
                            }
                        }
                    });
                    star.addEventListener('click', function(e) {
                        e.preventDefault();
                        selected = idx + 1;
                        radios[idx].checked = true;
                        for (let i = 0; i < stars.length; i++) {
                            if (i <= idx) {
                                stars[i].querySelector('i').classList.remove('text-gray-300');
                                stars[i].querySelector('i').classList.add('text-yellow-400');
                            } else {
                                stars[i].querySelector('i').classList.remove('text-yellow-400');
                                stars[i].querySelector('i').classList.add('text-gray-300');
                            }
                        }
                        // Show feedback buttons based on rating range
                        if (selected <= 2) {
                            renderFeedbackButtons('awful');
                        } else if (selected === 3) {
                            renderFeedbackButtons('okay');
                        } else if (selected >= 4) {
                            renderFeedbackButtons('perfect');
                        }
                        submitBtn.style.display = 'none';
                        feedbackInput.value = '';
                    });
                });
            });
        });

        // Sidebar Navigation - Consolidated and simplified
        function initSidebarNavigation() {
            const allSidebar = document.querySelectorAll('.sidebar-nav-item'); // Use the unified class

            allSidebar.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const sectionName = this.getAttribute('data-section');
                    const sectionId = sectionName + '-section'; // Consistent ID pattern

                    // Remove active class from all items
                    allSidebar.forEach(i => i.classList.remove('active'));
                    
                    // Add active class only to clicked item
                    this.classList.add('active');
                    
                    // Show the selected section
                    showSection(sectionId);

                    // Special case: If purchases section is selected, activate the 'all-orders' tab
                    if (sectionName === 'purchases') {
                        activateDefaultOrderTab();
                    }
                });
            });
        }

        // Show section function (Refactored)
        function showSection(sectionId) {
            // Hide all sections
            const contentSections = document.querySelectorAll('.content-section');
            contentSections.forEach(section => {
                section.style.display = 'none'; // Use style to override CSS rule
            });
            
            // Show the selected section
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.style.display = 'block';
            } else {
                console.error('Section not found:', sectionId);
            }
        }

        // Order Tabs
        function initOrderTabs() {
            const tabs = document.querySelectorAll('.order-tabs .tab');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    // Hide all content containers
                    const contents = document.querySelectorAll('.orders-tab-content-container > div');
                    contents.forEach(content => content.style.display = 'none');
                    
                    // Show selected content
                    const selected = document.getElementById(tabId);
                    if (selected) {
                        selected.style.display = 'flex'; // Use flex to maintain layout for non-empty lists or center alignment for empty
                    }
                });
            });
        }

        function activateDefaultOrderTab() {
            const defaultTab = document.querySelector('.order-tabs .tab[data-tab="all-orders"]');
            if (defaultTab) {
                defaultTab.click();
            }
        }


        // Copy Button Functionality
        function initCopyButton() {
            const copyButton = document.getElementById('copyButton');
            if (copyButton) {
                copyButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    copyReferralCode();
                });
            }
        }

        // Copy Referral Code function
        function copyReferralCode() {
            const codeElem = document.getElementById('referralCode');
            const referralCode = codeElem ? codeElem.textContent.trim() : '';
            
            if (!referralCode || referralCode === 'No data') {
                return;
            }
            
            // Try modern clipboard API first
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(referralCode).then(showCopyToast).catch(err => {
                    console.error('Failed to copy using clipboard API:', err);
                    fallbackCopy(referralCode);
                });
            } else {
                // Fallback for older browsers
                fallbackCopy(referralCode);
            }
        }

        // Fallback copy method
        function fallbackCopy(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                document.body.removeChild(textArea);
                if (successful) {
                    showCopyToast();
                } else {
                    console.error('Fallback copy failed');
                }
            } catch (err) {
                console.error('Fallback copy error:', err);
                document.body.removeChild(textArea);
            }
        }

        // Show copy success toast
        function showCopyToast() {
            const toast = document.getElementById('copyToast');
            if (toast) {
                toast.classList.add('show');
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 3000);
            }
        }

        // Modal functions
        function openEditProfileModal() {
            document.getElementById('editProfileModal').style.display = 'flex';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = document.getElementsByClassName('modal');
            for (let i = 0; i < modals.length; i++) {
                if (event.target === modals[i]) {
                    modals[i].style.display = 'none';
                }
            }
        }
    </script>
    <script>
    // Toggle password visibility
    function togglePassword(fieldId, iconElem) {
        var input = document.getElementById(fieldId);
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            iconElem.innerHTML = '<i class="fa-regular fa-eye-slash"></i>';
        } else {
            input.type = 'password';
            iconElem.innerHTML = '<i class="fa-regular fa-eye"></i>';
        }
    }

    // Change Password AJAX
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('changePasswordForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var current = document.getElementById('current_password').value;
                var newPwd = document.getElementById('new_password').value;
                var confirm = document.getElementById('confirm_password').value;
                var errorBox = document.getElementById('passwordError');
                errorBox.style.display = 'none';
                errorBox.textContent = '';
                if (newPwd.length < 8) {
                    errorBox.textContent = 'New password must be at least 8 characters.';
                    errorBox.style.display = 'block';
                    return;
                }
                if (newPwd !== confirm) {
                    errorBox.textContent = 'Passwords do not match.';
                    errorBox.style.display = 'block';
                    return;
                }
                var btn = document.getElementById('updatePasswordBtn');
                btn.disabled = true;
                var formData = new FormData(form);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'update_password.php', true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        btn.disabled = false;
                        var res = {};
                        try { res = JSON.parse(xhr.responseText); } catch(e) {}
                        if (xhr.status === 200 && res.success) {
                            errorBox.style.display = 'none';
                            form.reset();
                            showPasswordToast();
                        } else {
                            errorBox.textContent = res.error || 'Failed to update password.';
                            errorBox.style.display = 'block';
                        }
                    }
                };
                xhr.send(formData);
            });
        }
    });

    // Show toast for password update
    function showPasswordToast() {
        var toast = document.getElementById('passwordToast');
        if (toast) {
            toast.style.display = 'flex';
            toast.classList.add('show');
            setTimeout(function() {
                toast.classList.remove('show');
                toast.style.display = 'none';
            }, 3000);
        }
    }
    </script>

    <script>
        function openCancelModal(orderRef) {
            // This function now correctly references the *single* modal at the top
            document.getElementById('cancelOrderReference').value = orderRef.replace(/"/g, '');
            document.getElementById('cancelModal').style.display = 'flex';
        }
        // Cancel modal logic
        var cancelReasonSelect = document.getElementById('cancelReason');
        var otherReasonBox = document.getElementById('otherReasonBox');
        var otherReasonInput = document.getElementById('otherReasonInput');
        var confirmCancelBtn = document.getElementById('confirmCancelBtn');

        function validateCancelForm() {
            var reason = cancelReasonSelect.value;
            if (!reason) return false;
            if (reason === 'Other') {
                if (!otherReasonInput.value.trim()) return false;
            }
            return true;
        }

        cancelReasonSelect.addEventListener('change', function() {
            if (this.value === 'Other') {
                otherReasonBox.style.display = 'block';
            } else {
                otherReasonBox.style.display = 'none';
                otherReasonInput.value = '';
            }
            confirmCancelBtn.disabled = !validateCancelForm();
        });
        if (otherReasonInput) {
            otherReasonInput.addEventListener('input', function() {
                confirmCancelBtn.disabled = !validateCancelForm();
            });
        }
        
        // Make sure the button starts in a valid state
        if (confirmCancelBtn) {
            confirmCancelBtn.disabled = !validateCancelForm();
        }

        document.getElementById('cancelOrderForm').addEventListener('input', function() {
            confirmCancelBtn.disabled = !validateCancelForm();
        });
        document.getElementById('cancelOrderForm').addEventListener('submit', function(e) {
            e.preventDefault();
            if (!validateCancelForm()) {
                confirmCancelBtn.disabled = true;
                return;
            }
            var formData = new FormData(this);
            // If 'Other', send the custom reason
            if (cancelReasonSelect.value === 'Other') {
                formData.set('cancel_reason', otherReasonInput.value.trim());
            }
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../connection/cancel_order.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    var res = {};
                    try { res = JSON.parse(xhr.responseText); } catch(e) {}
                    if (xhr.status === 200 && res.success) {
                        closeModal('cancelModal');
                        showCancelToast();
                        refreshOrderTabs();
                    } else {
                        alert(res.error || 'Failed to cancel order.');
                    }
                }
            };
            xhr.send(formData);
        });

        // AJAX function to refresh order tabs after cancel
        function refreshOrderTabs() {
            var xhr = new XMLHttpRequest();
            // This logic is simplified; ideally, you'd fetch only the order tab content
            // For now, it reloads the page to show the change, which is robust.
            location.reload(); 
        }

        function showCancelToast() {
            var toast = document.getElementById('cancelToast');
            if (toast) {
                toast.style.display = 'flex';
                toast.classList.add('show');
                setTimeout(function() {
                    toast.classList.remove('show');
                    toast.style.display = 'none';
                }, 3000);
            }
        }
    </script>

</html>