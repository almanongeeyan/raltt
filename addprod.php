<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="addprod.css">
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-top">
            <img src="images/raltticon.png" alt="Logo">
            <h2>Staff Dashboard</h2>
            <hr>
            <nav>
                <a href="#"><i class="fas fa-chart-line"></i> Analytics</a>
                <a href="#"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="#"><i class="fas fa-box"></i> Products</a>
                <a href="#"><i class="fas fa-check-circle"></i> Complete Order</a>
                <a href="#"><i class="fas fa-truck"></i> Suppliers</a>
            </nav>
        </div>
        <div class="sidebar-bottom">
            <a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <div class="main-content">
        <h1>PRODUCT INVENTORY</h1>
        <hr>

        <div class="toolbar">
            <div class="search-container">
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Product Search Bar">
                </div>
                <button class="alert-btn" id="alertBtn">
                    Alert
                    <span class="badge" id="alertBadge">3</span>
                </button>
            </div>

            <div class="filter-container">
                <div class="filter-dropdown">
                    <select>
                        <option>Filter Dropdown</option>
                        <option>FLORAL</option>
                        <option>CLASSIC</option>
                        <option>SHAPES</option>
                        <option>NATURE</option>
                    </select>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <a href="addtiles.php" class="add-btn">Add Product</a>
            </div>
        </div>

        <div class="card-container">
            <div class="card">
                <h3 class="card-title">BLACK CLASSIC</h3>
                <img src="images/classic.png" alt="Product 1">
                <div class="card-description">
                    Inventory Count: 037
                </div>
            </div>
            <div class="card">
                <h3 class="card-title">WHITE FLOOR</h3>
                <img src="images/white.png" alt="Product 2">
                <div class="card-description">
                    Inventory Count: 100
                </div>
            </div>
            <div class="card">
                <h3 class="card-title">CLASSIC BLUE</h3>
                <img src="images/blue.png" alt="Product 3">
                <div class="card-description">
                    Inventory Count: 057
                </div>
            </div>
            <div class="card">
                <h3 class="card-title">BEIGE FLOWER</h3>
                <img src="images/beigeflo.png" alt="Product 4">
                <div class="card-description">
                    Inventory Count: 123
                </div>
            </div>
            <div class="card">
                <h3 class="card-title">DIAMOND TILE</h3>
                <img src="images/diamond.png" alt="Product 5">
                <div class="card-description">
                    Inventory Count: 001
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="alertModal" class="alert-modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2 class="modal-title" style="text-align:center; color:red;">LOW STOCK ALERT</h2>

            <div class="product-table">
                <!-- Header Row -->
                <div class="table-header">
                    <div>Product</div>
                    <div>Remaining Stocks</div>
                    <div>Actions</div>
                    <div>Quantity</div>
                </div>

                <!-- Product Rows -->
                <div class="table-row">
                    <div>Product 1</div>
                    <div>10</div>
                    <div><button class="restock-btn">Restock</button></div>
                    <div><input type="number" min="1" value="1" class="quantity-selector"></div>
                </div>

                <div class="table-row">
                    <div>Product 2</div>
                    <div>13</div>
                    <div><button class="restock-btn">Restock</button></div>
                    <div><input type="number" min="1" value="1" class="quantity-selector"></div>
                </div>

                <div class="table-row">
                    <div>Product 3</div>
                    <div>14</div>
                    <div><button class="restock-btn">Restock</button></div>
                    <div><input type="number" min="1" value="1" class="quantity-selector"></div>
                </div>

                <div class="table-row">
                    <div>Product 4</div>
                    <div>17</div>
                    <div><button class="restock-btn">Restock</button></div>
                    <div><input type="number" min="1" value="1" class="quantity-selector"></div>
                </div>
            </div>
        </div>
    </div>

<script>
    const alertBtn = document.getElementById("alertBtn");
    const alertModal = document.getElementById("alertModal");
    const closeBtn = document.querySelector(".close-btn");
    const restockButtons = document.querySelectorAll(".restock-btn");

    // Show modal when alert button is clicked
    alertBtn.addEventListener("click", () => {
        alertModal.style.display = "block";
    });

    // Close modal when clicking the X
    closeBtn.addEventListener("click", () => {
        alertModal.style.display = "none";
    });

    // Close modal when clicking outside the modal content
    window.addEventListener("click", (event) => {
        if (event.target === alertModal) {
            alertModal.style.display = "none";
        }
    });

    // Restock button functionality
    restockButtons.forEach(button => {
        button.addEventListener("click", () => {
            alert("Product Restocked");
            alertModal.style.display = "none";
        });
    });

    // Update alert button badge dynamically
    let notifications = 123;
    function updateAlertButton() {
        if (notifications > 0) {
            alertBtn.classList.add("has-notifications");
            document.getElementById("alertBadge").textContent = notifications;
        } else {
            alertBtn.classList.remove("has-notifications");
        }
    }

    updateAlertButton();
</script>

</body>
</html>
