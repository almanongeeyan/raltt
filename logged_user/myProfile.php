<?php
include '../includes/headeruser.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="myProfile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    <div class="main-content">
        <div class="page-container">

            <div class="page-header">
                <button class="back-btn" onclick="window.history.back();">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Account</span>
                </button>
            </div>

            <div class="forms-row">
                <div class="form-box profile-box">
                    <form id="profileForm">
                        <div class="profile-header">
                            <div class="profile-title">
                                <i class="fa-solid fa-user"></i>
                                <span>Profile</span>
                            </div>
                            <button type="button" class="edit-profile-btn">
                                <i class="fa-solid fa-pen"></i>
                                Edit Profile
                            </button>
                        </div>
                        <div class="profile-body">
                            <div class="profile-info">
                                <div>
                                    <p class="label">Name</p>
                                    <p class="value">Cholene Jane Aberin</p>
                                </div>
                                <div>
                                    <p class="label">Contact Number</p>
                                    <p class="value">+63 912 345 6789</p>
                                </div>
                                <div>
                                    <p class="label">Email Address</p>
                                    <p class="value">cholene.doe@email.com</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="form-box address-box">
                    <form id="addressForm">
                        <div class="address-header">
                            <div class="address-title">
                                <i class="fa-solid fa-location-dot"></i>
                                <span>Address</span>
                            </div>
                            <button type="button" class="add-btn">
                                <i class="fa-solid fa-plus"></i>
                                Add Address
                            </button>
                        </div>
                        <div class="drawer-container">
                            <div class="address-drawer">
                                <div class="drawer-content">
                                    <div class="drawer-info">
                                        <p class="line1">Cholene Jane | +63 912 345 6789 <span
                                                class="default">Default</span></p>
                                        <p class="line2">Blk 15 Lot 3 Phase 4 Long Street Name...</p>
                                    </div>
                                    <div class="drawer-actions">
                                        <button type="button" class="edit-address-btn"><i class="fa-solid fa-pen"></i>
                                            Edit</button>
                                        <button type="button" class="delete-btn"><i class="fa-solid fa-trash"></i>
                                            Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="viewAllBtn" class="view-all-btn">---- View All ----</div>
                    </form>
                </div>
            </div>

            <div class="order-container">
                <div class="order-header">
                    <i class="fa-solid fa-box"></i>
                    <span>Orders</span>
                </div>

                <div class="order-tabs">
                    <div class="tab active" data-tab="all-orders">All Orders</div>
                    <div class="tab" data-tab="to-ship">Shipped</div>
                    <div class="tab" data-tab="to-receive">Received</div>
                    <div class="tab" data-tab="to-rate">To Rate</div>
                    <div class="tab" data-tab="cancelled">Cancelled</div>
                    <div class="tab" data-tab="returned">Return/Refund</div>
                </div>


                <hr class="order-line">

                <div class="order-columns">
                    <div class="col product-col">Product</div>
                    <div class="col qty-col">Quantity</div>
                    <div class="col price-col">Price</div>
                    <div class="col status-col">Status</div>
                    <div class="col action-col">Action</div>
                </div>

                <div class="orders-tab-content-container">
                    <?php
                    $orders = [
                        'all-orders' => [
                            ['name' => 'Product 1', 'desc' => 'Description', 'qty' => 1, 'price' => 500, 'status' => 'Pending', 'img' => 'DEPARO.png'],
                            ['name' => 'Product 2', 'desc' => 'Description', 'qty' => 2, 'price' => 1200, 'status' => 'Shipped', 'img' => 'BRIXTON.png']
                        ],
                        'to-ship' => [],
                        'to-receive' => [
                            ['name' => 'Product 3', 'desc' => 'Description', 'qty' => 1, 'price' => 700, 'status' => 'Shipped', 'img' => 'VANGUAR.png']
                        ],
                        'to-rate' => [
                            ['name' => 'Product 4', 'desc' => 'Description', 'qty' => 3, 'price' => 900, 'status' => 'Delivered', 'img' => 'SAMARIA.png']
                        ],
                        'returned' => [],
                        'cancelled' => []
                    ];

                    foreach ($orders as $tab => $tabOrders) {
                        if (!empty($tabOrders)) {
                            echo '<div class="orders-tab-content" id="' . $tab . '" style="' . ($tab === 'all-orders' ? 'display:flex' : 'display:none') . '">
                  <div class="orders-drawer-container scrollable" style="display:flex; gap:10px;">';
                            foreach ($tabOrders as $order) {
                                echo '<div class="orders-drawer">
                    <div class="product-info">
                      <img src="' . $order['img'] . '" alt="Product">
                      <div class="product-text">
                        <span class="title">' . $order['name'] . '</span>
                        <span class="subtitle">' . $order['desc'] . '</span>
                      </div>
                    </div>
                    <div class="qty">' . $order['qty'] . '</div>
                    <div class="price">₱' . $order['price'] . '</div>
                    <div class="status">' . $order['status'] . '</div>
                    <button class="action-btn"><i class="fa-solid fa-trash-can"></i></button>
                  </div>';
                            }
                            echo '</div></div>';
                        } else {
                            echo '<div class="orders-tab-content" id="' . $tab . '" style="display:none; justify-content:center; align-items:center; padding:20px;">No Products to Show</div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="myProfile.js"></script>

    <script>
        const viewAllBtn = document.getElementById('viewAllBtn');
        const hiddenDrawers = document.querySelectorAll('.hidden-drawer');
        const drawerContainer = document.querySelector('.drawer-container');

        viewAllBtn.addEventListener('click', () => {
            hiddenDrawers.forEach(drawer => drawer.style.display = 'flex');
            drawerContainer.classList.add('scrollable');
            viewAllBtn.style.display = 'none';
        });
    </script>

    <script>
        // Edit Profile Button
        document.querySelector('.edit-profile-btn').addEventListener('click', () => {
            window.location.href = 'editprofile.php';
        });

        // Edit Address Buttons
        document.querySelectorAll('.edit-address-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                window.location.href = 'address.php';
            });
        });

        // Add Address Button
        document.querySelectorAll('.add-address-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                window.location.href = 'address.php';
            });
        });

    </script>

    <script>
        // --- Address Drawers ---
        const viewAllBtn = document.getElementById('viewAllBtn');
        const hiddenDrawers = document.querySelectorAll('.hidden-drawer');
        const drawerContainer = document.querySelector('.drawer-container');

        if (viewAllBtn) {
            viewAllBtn.addEventListener('click', () => {
                hiddenDrawers.forEach(drawer => drawer.style.display = 'flex');
                drawerContainer.classList.add('scrollable');
                viewAllBtn.style.display = 'none';
            });
        }

        // --- Orders Tabs ---
        const tabs = document.querySelectorAll('.order-tabs .tab');
        const contents = document.querySelectorAll('.orders-tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const allowedTabs = ['all-orders', 'to-ship', 'to-receive', 'to-rate', 'cancelled' 'returned'];
                if (!allowedTabs.includes(tab.dataset.tab)) return;

                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                contents.forEach(content => content.style.display = 'none');
                const selected = document.getElementById(tab.dataset.tab);
                if (selected) selected.style.display = selected.querySelector('.orders-drawer-container') ? 'flex' : 'flex';
            });
        });
    </script>

</body>

</html>