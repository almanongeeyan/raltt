<?php
include '../includes/headeruser.php';
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
        
        .page-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .back-btn {
            background: linear-gradient(90deg, #7d310a 0%, #a34a20 100%);
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: linear-gradient(90deg, #a34a20 0%, #7d310a 100%);
            transform: translateY(-2px);
        }
        
        .form-box {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .form-box:hover {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }
        
        .profile-header, .address-header, .order-header {
            background: linear-gradient(90deg, #f9f5f2 0%, #f0e6df 100%);
            border-bottom: 1px solid #e8d9cf;
        }
        
        .edit-profile-btn, .add-btn {
            background: linear-gradient(90deg, #7d310a 0%, #a34a20 100%);
            transition: all 0.3s ease;
        }
        
        .edit-profile-btn:hover, .add-btn:hover {
            background: linear-gradient(90deg, #a34a20 0%, #7d310a 100%);
            transform: translateY(-2px);
        }
        
        .address-drawer {
            border: 1px solid #e8d9cf;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .address-drawer:hover {
            border-color: #cf8756;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .default {
            background: linear-gradient(90deg, #7d310a 0%, #a34a20 100%);
        }
        
        .edit-address-btn {
            color: #7d310a;
            transition: all 0.2s ease;
        }
        
        .edit-address-btn:hover {
            color: #5a2207;
            transform: translateY(-1px);
        }
        
        .delete-btn {
            color: #ef4444;
            transition: all 0.2s ease;
        }
        
        .delete-btn:hover {
            color: #dc2626;
            transform: translateY(-1px);
        }
        
        .order-tabs {
            border-bottom: 1px solid #e8d9cf;
        }
        
        .tab {
            color: #777;
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
        }
        
        .tab.active {
            color: #7d310a;
            font-weight: 600;
        }
        
        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background: #7d310a;
            border-radius: 2px 2px 0 0;
        }
        
        .tab:hover {
            color: #7d310a;
        }
        
        .order-columns {
            background: #f9f5f2;
            border-radius: 8px;
        }
        
        .orders-drawer {
            background: white;
            border: 1px solid #e8d9cf;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .orders-drawer:hover {
            border-color: #cf8756;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .action-btn {
            background: linear-gradient(90deg, #7d310a 0%, #a34a20 100%);
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            background: linear-gradient(90deg, #a34a20 0%, #7d310a 100%);
            transform: translateY(-2px);
        }
        
        .info-btn {
            background: linear-gradient(90deg, #f9f5f2 0%, #f0e6df 100%);
            color: #7d310a;
            transition: all 0.3s ease;
        }
        
        .info-btn:hover {
            background: linear-gradient(90deg, #f0e6df 0%, #e8d9cf 100%);
            transform: translateY(-2px);
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            background: linear-gradient(90deg, #f9f5f2 0%, #f0e6df 100%);
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }
        
        .close-btn {
            color: #7d310a;
            transition: all 0.2s ease;
        }
        
        .close-btn:hover {
            color: #5a2207;
            transform: scale(1.1);
        }
        
        .form-input {
            border: 1px solid #e8d9cf;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            border-color: #7d310a;
            box-shadow: 0 0 0 2px rgba(125, 49, 10, 0.2);
        }
        
        .submit-btn {
            background: linear-gradient(90deg, #7d310a 0%, #a34a20 100%);
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            background: linear-gradient(90deg, #a34a20 0%, #7d310a 100%);
            transform: translateY(-2px);
        }
        
        .branch-badge {
            background: linear-gradient(90deg, #f9f5f2 0%, #f0e6df 100%);
            color: #7d310a;
        }
        
        .scrollable {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .scrollable::-webkit-scrollbar {
            width: 6px;
        }
        
        .scrollable::-webkit-scrollbar-track {
            background: #f1e8e0;
            border-radius: 3px;
        }
        
        .scrollable::-webkit-scrollbar-thumb {
            background: #cf8756;
            border-radius: 3px;
        }
        
        .scrollable::-webkit-scrollbar-thumb:hover {
            background: #7d310a;
        }
        
        @media (max-width: 768px) {
            .forms-row {
                flex-direction: column;
            }
            
            .order-columns {
                display: none;
            }
            
            .orders-drawer {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .orders-drawer > div {
                margin-bottom: 10px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>

<body class="min-h-screen pt-24">
    <div class="container mx-auto px-4 py-8">
        <div class="page-container">
            <!-- Page Header with Back Button -->
            <div class="mb-6">
                <button class="back-btn text-white font-bold py-3 px-6 rounded-xl flex items-center" onclick="window.history.back();">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    <span>Back to Account</span>
                </button>
            </div>

            <!-- Profile and Address Forms -->
            <div class="forms-row flex flex-col lg:flex-row gap-6 mb-8">
                <!-- Profile Box -->
                <div class="form-box profile-box flex-grow">
                    <form id="profileForm" class="p-6">
                        <div class="profile-header flex justify-between items-center mb-6 pb-4">
                            <div class="profile-title flex items-center text-primary font-black text-xl">
                                <i class="fa-solid fa-user mr-3"></i>
                                <span>Profile Information</span>
                            </div>
                            <button type="button" class="edit-profile-btn text-white font-bold py-2 px-4 rounded-lg flex items-center" onclick="openEditProfileModal()">
                                <i class="fa-solid fa-pen mr-2"></i>
                                Edit Profile
                            </button>
                        </div>
                        <div class="profile-body">
                            <div class="profile-info space-y-4">
                                <div>
                                    <p class="label text-textlight text-sm mb-1">Full Name</p>
                                    <p class="value text-textdark font-medium">Cholene Jane Aberin</p>
                                </div>
                                <div>
                                    <p class="label text-textlight text-sm mb-1">Contact Number</p>
                                    <p class="value text-textdark font-medium">+63 912 345 6789</p>
                                </div>
                                <div>
                                    <p class="label text-textlight text-sm mb-1">Email Address</p>
                                    <p class="value text-textdark font-medium">cholene.doe@email.com</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Address Box -->
                <div class="form-box address-box flex-grow">
                    <form id="addressForm" class="p-6">
                        <div class="address-header flex justify-between items-center mb-6 pb-4">
                            <div class="address-title flex items-center text-primary font-black text-xl">
                                <i class="fa-solid fa-location-dot mr-3"></i>
                                <span>Address Book</span>
                            </div>
                            <button type="button" class="add-btn text-white font-bold py-2 px-4 rounded-lg flex items-center" onclick="openAddAddressModal()">
                                <i class="fa-solid fa-plus mr-2"></i>
                                Add Address
                            </button>
                        </div>
                        <div class="drawer-container">
                            <div class="address-drawer p-4 mb-4">
                                <div class="drawer-content flex flex-col md:flex-row md:justify-between md:items-center">
                                    <div class="drawer-info flex-grow mb-3 md:mb-0">
                                        <p class="line1 text-textdark font-medium mb-1">
                                            Cholene Jane | +63 912 345 6789 
                                            <span class="default text-white text-xs py-1 px-2 rounded-full ml-2">Default</span>
                                        </p>
                                        <p class="line2 text-textlight text-sm">Blk 15 Lot 3 Phase 4 Long Street Name, Barangay San Antonio, Quezon City, Metro Manila 1105</p>
                                    </div>
                                    <div class="drawer-actions flex space-x-3">
                                        <button type="button" class="edit-address-btn font-medium py-1 px-3 rounded-lg flex items-center" onclick="openEditAddressModal()">
                                            <i class="fa-solid fa-pen mr-1"></i> Edit
                                        </button>
                                        <button type="button" class="delete-btn font-medium py-1 px-3 rounded-lg flex items-center">
                                            <i class="fa-solid fa-trash mr-1"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Orders Section -->
            <div class="order-container form-box p-6">
                <div class="order-header flex items-center text-primary font-black text-xl mb-6 pb-4">
                    <i class="fa-solid fa-box mr-3"></i>
                    <span>Order History</span>
                </div>

                <!-- Order Tabs -->
                <div class="order-tabs flex flex-wrap gap-4 md:gap-6 mb-6">
                    <div class="tab active py-2 px-1" data-tab="all-orders">All Orders</div>
                    <div class="tab py-2 px-1" data-tab="to-ship">Shipped</div>
                    <div class="tab py-2 px-1" data-tab="to-receive">Received</div>
                    <div class="tab py-2 px-1" data-tab="to-rate">To Rate</div>
                    <div class="tab py-2 px-1" data-tab="cancelled">Cancelled</div>
                    <div class="tab py-2 px-1" data-tab="returned">Return/Refund</div>
                </div>

                <hr class="order-line border-gray-200 mb-6">

                <!-- Order Columns (Desktop) -->
                <div class="order-columns hidden md:grid grid-cols-12 gap-4 text-textlight font-semibold mb-4 pb-3">
                    <div class="col product-col col-span-4">Product</div>
                    <div class="col qty-col col-span-1 text-center">Qty</div>
                    <div class="col price-col col-span-2 text-center">Price</div>
                    <div class="col status-col col-span-2 text-center">Status</div>
                    <div class="col branch-col col-span-2 text-center">Branch</div>
                    <div class="col action-col col-span-1 text-center">Action</div>
                </div>

                <!-- Order Content -->
                <div class="orders-tab-content-container">
                    <?php
                    $orders = [
                        'all-orders' => [
                            ['name' => 'Arte Ceramiche Matte Floor Tile', 'desc' => 'Premium Tiles • 30x30 cm', 'qty' => 1, 'price' => 500, 'status' => 'Pending', 'img' => 'https://placehold.co/64x64/f9f5f2/7d310a?text=AT', 'branch' => 'Deparo Branch'],
                            ['name' => 'Porcelain Wood-Look Tile', 'desc' => 'Wood Series • 15x60 cm', 'qty' => 2, 'price' => 1200, 'status' => 'Shipped', 'img' => 'https://placehold.co/64x64/f9f5f2/7d310a?text=PW', 'branch' => 'Brixton Branch']
                        ],
                        'to-ship' => [],
                        'to-receive' => [
                            ['name' => 'Marble Effect Wall Tile', 'desc' => 'Luxury Collection • 30x60 cm', 'qty' => 1, 'price' => 700, 'status' => 'Shipped', 'img' => 'https://placehold.co/64x64/f9f5f2/7d310a?text=MW', 'branch' => 'Vanguard Branch']
                        ],
                        'to-rate' => [
                            ['name' => 'Mosaic Bathroom Tile', 'desc' => 'Aqua Series • 10x10 cm', 'qty' => 3, 'price' => 900, 'status' => 'Delivered', 'img' => 'https://placehold.co/64x64/f9f5f2/7d310a?text=MB', 'branch' => 'Samaria Branch'],
                            ['name' => 'Classic Subway Tile', 'desc' => 'Heritage Collection • 7.5x15 cm', 'qty' => 5, 'price' => 1250, 'status' => 'Delivered', 'img' => 'https://placehold.co/64x64/f9f5f2/7d310a?text=CS', 'branch' => 'Ph1 Branch']
                        ],
                        'returned' => [],
                        'cancelled' => []
                    ];

                    foreach ($orders as $tab => $tabOrders) {
                        if (!empty($tabOrders)) {
                            echo '<div class="orders-tab-content flex flex-col gap-4" id="' . $tab . '" style="' . ($tab === 'all-orders' ? 'display:flex' : 'display:none') . '">';
                            foreach ($tabOrders as $order) {
                                $canCancel = in_array($order['status'], ['Pending', 'Shipped']);
                                
                                echo '<div class="orders-drawer p-4 grid grid-cols-1 md:grid-cols-12 md:gap-4 items-center">
                                    <div class="product-info flex items-center col-span-4 mb-3 md:mb-0">
                                        <img src="' . $order['img'] . '" alt="Product" class="w-16 h-16 rounded-lg object-cover mr-4">
                                        <div class="product-text flex flex-col">
                                            <span class="title font-bold text-textdark">' . $order['name'] . '</span>
                                            <span class="subtitle text-sm text-textlight">' . $order['desc'] . '</span>
                                        </div>
                                    </div>
                                    <div class="qty text-textdark font-medium text-center col-span-1 mb-2 md:mb-0">' . $order['qty'] . '</div>
                                    <div class="price text-primary font-black text-center col-span-2 mb-2 md:mb-0">₱' . $order['price'] . '</div>
                                    <div class="status text-textdark font-medium text-center col-span-2 mb-2 md:mb-0">' . $order['status'] . '</div>
                                    <div class="branch text-center col-span-2 mb-2 md:mb-0">
                                        <span class="branch-badge text-xs font-medium py-1 px-2 rounded-full">' . $order['branch'] . '</span>
                                    </div>
                                    <div class="action text-center col-span-1">
                                        <div class="action-buttons flex flex-col md:flex-row gap-2 justify-center">';
                                        
                                        if ($canCancel) {
                                            echo '<button class="action-btn text-white text-xs font-medium py-2 px-3 rounded-lg flex items-center justify-center" onclick="openCancelOrderModal()">
                                                <i class="fa-solid fa-xmark mr-1"></i> Cancel
                                            </button>';
                                        }
                                        
                                        echo '<button class="info-btn text-xs font-medium py-2 px-3 rounded-lg flex items-center justify-center" onclick="openOrderInfoModal()">
                                            <i class="fa-solid fa-info mr-1"></i> Info
                                        </button>
                                        </div>
                                    </div>
                                </div>';
                            }
                            echo '</div>';
                        } else {
                            echo '<div class="orders-tab-content flex justify-center items-center py-12" id="' . $tab . '" style="display:none">
                                <p class="text-textlight text-lg">No orders to show</p>
                            </div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="modal">
        <div class="modal-content">
            <div class="modal-header p-6 flex justify-between items-center">
                <h3 class="text-xl font-black text-primary">Edit Profile</h3>
                <button class="close-btn text-2xl" onclick="closeModal('editProfileModal')">&times;</button>
            </div>
            <div class="p-6">
                <form class="space-y-4">
                    <div>
                        <label class="block text-textdark font-medium mb-2">Full Name</label>
                        <input type="text" class="form-input w-full p-3" value="Cholene Jane Aberin">
                    </div>
                    <div>
                        <label class="block text-textdark font-medium mb-2">Contact Number</label>
                        <input type="tel" class="form-input w-full p-3" value="+63 912 345 6789">
                    </div>
                    <div>
                        <label class="block text-textdark font-medium mb-2">Email Address</label>
                        <input type="email" class="form-input w-full p-3" value="cholene.doe@email.com">
                    </div>
                    <button type="button" class="submit-btn w-full text-white font-bold py-3 rounded-lg mt-4">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Address Modal -->
    <div id="addAddressModal" class="modal">
        <div class="modal-content">
            <div class="modal-header p-6 flex justify-between items-center">
                <h3 class="text-xl font-black text-primary">Add New Address</h3>
                <button class="close-btn text-2xl" onclick="closeModal('addAddressModal')">&times;</button>
            </div>
            <div class="p-6">
                <form class="space-y-4">
                    <div>
                        <label class="block text-textdark font-medium mb-2">Full Name</label>
                        <input type="text" class="form-input w-full p-3" placeholder="Enter your full name">
                    </div>
                    <div>
                        <label class="block text-textdark font-medium mb-2">Contact Number</label>
                        <input type="tel" class="form-input w-full p-3" placeholder="Enter your phone number">
                    </div>
                    <div>
                        <label class="block text-textdark font-medium mb-2">Complete Address</label>
                        <textarea class="form-input w-full p-3" rows="3" placeholder="Enter your complete address"></textarea>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="setDefault" class="mr-2">
                        <label for="setDefault" class="text-textdark">Set as default address</label>
                    </div>
                    <button type="button" class="submit-btn w-full text-white font-bold py-3 rounded-lg mt-4">
                        Save Address
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Address Modal -->
    <div id="editAddressModal" class="modal">
        <div class="modal-content">
            <div class="modal-header p-6 flex justify-between items-center">
                <h3 class="text-xl font-black text-primary">Edit Address</h3>
                <button class="close-btn text-2xl" onclick="closeModal('editAddressModal')">&times;</button>
            </div>
            <div class="p-6">
                <form class="space-y-4">
                    <div>
                        <label class="block text-textdark font-medium mb-2">Full Name</label>
                        <input type="text" class="form-input w-full p-3" value="Cholene Jane">
                    </div>
                    <div>
                        <label class="block text-textdark font-medium mb-2">Contact Number</label>
                        <input type="tel" class="form-input w-full p-3" value="+63 912 345 6789">
                    </div>
                    <div>
                        <label class="block text-textdark font-medium mb-2">Complete Address</label>
                        <textarea class="form-input w-full p-3" rows="3">Blk 15 Lot 3 Phase 4 Long Street Name, Barangay San Antonio, Quezon City, Metro Manila 1105</textarea>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="editDefault" class="mr-2" checked>
                        <label for="editDefault" class="text-textdark">Set as default address</label>
                    </div>
                    <button type="button" class="submit-btn w-full text-white font-bold py-3 rounded-lg mt-4">
                        Update Address
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Order Modal -->
    <div id="cancelOrderModal" class="modal">
        <div class="modal-content">
            <div class="modal-header p-6 flex justify-between items-center">
                <h3 class="text-xl font-black text-primary">Cancel Order</h3>
                <button class="close-btn text-2xl" onclick="closeModal('cancelOrderModal')">&times;</button>
            </div>
            <div class="p-6">
                <p class="text-textdark mb-4">Are you sure you want to cancel this order? This action cannot be undone.</p>
                <div class="mb-4">
                    <label class="block text-textdark font-medium mb-2">Reason for cancellation</label>
                    <select class="form-input w-full p-3">
                        <option value="">Select a reason</option>
                        <option value="change-mind">Changed my mind</option>
                        <option value="wrong-item">Ordered wrong item</option>
                        <option value="duplicate">Duplicate order</option>
                        <option value="shipping">Shipping takes too long</option>
                        <option value="other">Other reason</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-textdark font-medium mb-2">Additional notes (optional)</label>
                    <textarea class="form-input w-full p-3" rows="3" placeholder="Provide additional details"></textarea>
                </div>
                <div class="flex gap-4">
                    <button type="button" class="flex-1 bg-gray-200 text-gray-700 font-bold py-3 rounded-lg" onclick="closeModal('cancelOrderModal')">
                        Go Back
                    </button>
                    <button type="button" class="flex-1 bg-red-600 text-white font-bold py-3 rounded-lg">
                        Confirm Cancellation
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Info Modal -->
    <div id="orderInfoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header p-6 flex justify-between items-center">
                <h3 class="text-xl font-black text-primary">Order Information</h3>
                <button class="close-btn text-2xl" onclick="closeModal('orderInfoModal')">&times;</button>
            </div>
            <div class="p-6">
                <div class="mb-6">
                    <h4 class="font-bold text-textdark mb-3">Order Details</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between mb-2">
                            <span class="text-textlight">Order ID:</span>
                            <span class="text-textdark font-medium">#ORD-123456</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-textlight">Order Date:</span>
                            <span class="text-textdark font-medium">Nov 15, 2023</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-textlight">Status:</span>
                            <span class="text-primary font-medium">Pending</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-textlight">Branch:</span>
                            <span class="text-textdark font-medium">Deparo Branch</span>
                        </div>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h4 class="font-bold text-textdark mb-3">Product Information</h4>
                    <div class="flex items-center mb-4">
                        <img src="https://placehold.co/64x64/f9f5f2/7d310a?text=AT" alt="Product" class="w-16 h-16 rounded-lg object-cover mr-4">
                        <div>
                            <p class="font-bold text-textdark">Arte Ceramiche Matte Floor Tile</p>
                            <p class="text-sm text-textlight">Premium Tiles • 30x30 cm</p>
                            <p class="text-primary font-black">₱500 × 1</p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h4 class="font-bold text-textdark mb-3">Shipping Information</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-textdark font-medium">Cholene Jane Aberin</p>
                        <p class="text-textlight">+63 912 345 6789</p>
                        <p class="text-textlight mt-1">Blk 15 Lot 3 Phase 4 Long Street Name, Barangay San Antonio, Quezon City, Metro Manila 1105</p>
                    </div>
                </div>
                
                <button type="button" class="submit-btn w-full text-white font-bold py-3 rounded-lg">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        // Modal functions
        function openEditProfileModal() {
            document.getElementById('editProfileModal').style.display = 'flex';
        }
        
        function openAddAddressModal() {
            document.getElementById('addAddressModal').style.display = 'flex';
        }
        
        function openEditAddressModal() {
            document.getElementById('editAddressModal').style.display = 'flex';
        }
        
        function openCancelOrderModal() {
            document.getElementById('cancelOrderModal').style.display = 'flex';
        }
        
        function openOrderInfoModal() {
            document.getElementById('orderInfoModal').style.display = 'flex';
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
        
        // Orders Tabs
        const tabs = document.querySelectorAll('.order-tabs .tab');
        const contents = document.querySelectorAll('.orders-tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const allowedTabs = ['all-orders', 'to-ship', 'to-receive', 'to-rate', 'cancelled', 'returned'];
                if (!allowedTabs.includes(tab.dataset.tab)) return;
                
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                contents.forEach(content => content.style.display = 'none');
                const selected = document.getElementById(tab.dataset.tab);
                if (selected) selected.style.display = 'flex';
            });
        });
    </script>

</body>

</html>