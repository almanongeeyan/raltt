<?php
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="myProfile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
            <div class="form-box">
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
                                    <p class="line2">Blk 15 Lot 3 Phase 4 Long Street Name That Gets Truncated...</p>
                                </div>
                                <div class="drawer-actions">
                                    <button type="button" class="edit-btn"><i class="fa-solid fa-pen"></i> Edit</button>
                                    <button type="button" class="delete-btn"><i class="fa-solid fa-trash"></i>
                                        Delete</button>
                                </div>
                            </div>
                        </div>

                        <div class="address-drawer">
                            <div class="drawer-content">
                                <div class="drawer-info">
                                    <p class="line1">Jane Doe | +63 933 222 1111</p>
                                    <p class="line2">123 Short Address</p>
                                </div>
                                <div class="drawer-actions">
                                    <button type="button" class="edit-btn"><i class="fa-solid fa-pen"></i> Edit</button>
                                    <button type="button" class="delete-btn"><i class="fa-solid fa-trash"></i>
                                        Delete</button>
                                </div>
                            </div>
                        </div>

                        <div class="address-drawer hidden-drawer">
                            <div class="drawer-content">
                                <div class="drawer-info">
                                    <p class="line1">Mark Santos | +63 955 777 4444</p>
                                    <p class="line2">456 Avenue Street Barangay Long City Region</p>
                                </div>
                                <div class="drawer-actions">
                                    <button type="button" class="edit-btn"><i class="fa-solid fa-pen"></i> Edit</button>
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
                <div class="tab" data-tab="to-pay">To Pay</div>
                <div class="tab" data-tab="to-ship">To Ship</div>
                <div class="tab" data-tab="to-receive">To Receive</div>
                <div class="tab" data-tab="to-rate">To Rate</div>
                <div class="tab" data-tab="completed">Completed</div>
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
                        ['name' => 'Product 2', 'desc' => 'Description', 'qty' => 2, 'price' => 1200, 'status' => 'Shipped', 'img' => 'BRIXTON.png'],
                    ],
                    'to-pay' => [],
                    'to-ship' => [],
                    'to-receive' => [
                        ['name' => 'Product 3', 'desc' => 'Description', 'qty' => 1, 'price' => 700, 'status' => 'Shipped', 'img' => 'VANGUAR.png']
                    ],
                    'to-rate' => [
                        ['name' => 'Product 4', 'desc' => 'Description', 'qty' => 3, 'price' => 900, 'status' => 'Delivered', 'img' => 'SAMARIA.png']
                    ],
                    'completed' => [
                        ['name' => 'Product 5', 'desc' => 'Description', 'qty' => 1, 'price' => 1200, 'status' => 'Completed', 'img' => 'BRIXTON.png']
                    ],
                    'cancelled' => [],
                    'returned' => []
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

    <script>
        const tabs = document.querySelectorAll('.order-tabs .tab');
        const contents = document.querySelectorAll('.orders-tab-content');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                contents.forEach(content => content.style.display = 'none');
                const selected = document.getElementById(tab.dataset.tab);
                if (selected) selected.style.display = selected.querySelector('.orders-drawer-container') ? 'flex' : 'flex';
            });
        });

        const menu = document.querySelector('#menu');
        const navbar = document.querySelector('.header .navbar');
        menu.addEventListener('click', () => navbar.classList.toggle('active'));
        window.addEventListener('scroll', () => navbar.classList.remove('active'));
        window.addEventListener('click', e => {
            if (!e.target.matches('.dropbtn')) {
                const dropdowns = document.getElementsByClassName("dropdown-content");
                for (let i = 0; i < dropdowns.length; i++) {
                    const openDropdown = dropdowns[i];
                    if (openDropdown.style.display === 'block') {
                        openDropdown.style.display = 'none';
                        openDropdown.style.opacity = '0';
                        openDropdown.style.transform = 'translateY(10px)';
                    }
                }
            }
        });
        document.querySelectorAll('.dropdown .dropbtn').forEach(button => {
            button.addEventListener('click', e => {
                if (window.innerWidth <= 991) {
                    const dropdownContent = button.nextElementSibling;
                    if (dropdownContent.style.display === 'block') {
                        dropdownContent.style.display = 'none';
                        dropdownContent.style.opacity = '0';
                        dropdownContent.style.transform = 'translateY(10px)';
                    } else {
                        dropdownContent.style.display = 'block';
                        dropdownContent.style.opacity = '1';
                        dropdownContent.style.transform = 'translateY(0)';
                    }
                    e.stopPropagation();
                }
            });
        });

        document.getElementById('profilePic').addEventListener('change', e => {
            const file = e.target.files[0];
            if (file) document.getElementById('previewImage').src = URL.createObjectURL(file);
        });

        document.querySelector('.edit-btn').addEventListener('click', () => {
            const inputs = document.querySelectorAll('#profileForm .value');
            inputs.forEach(input => {
                if (input.hasAttribute('readonly')) {
                    input.removeAttribute('readonly');
                    input.style.border = "1px solid #ccc";
                    input.style.background = "#fff";
                } else {
                    input.setAttribute('readonly', true);
                    input.style.border = "none";
                    input.style.background = "transparent";
                }
            });
        });

        const viewAllBtn = document.getElementById('viewAllBtn');
        const hiddenDrawers = document.querySelectorAll('.hidden-drawer');
        const drawerContainer = document.querySelector('.drawer-container');
        viewAllBtn.addEventListener('click', () => {
            hiddenDrawers.forEach(drawer => drawer.style.display = 'flex');
            drawerContainer.classList.add('scrollable');
            viewAllBtn.style.display = 'none';
        });
    </script>

</body>

</html>