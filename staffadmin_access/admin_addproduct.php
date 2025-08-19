<?php include '../includes/sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Products Dashboard</title>
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 80px;
            --transition-speed: 0.3s;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            transition: padding-left var(--transition-speed);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: margin-left var(--transition-speed);
        }

        /* Adjust main content when sidebar is collapsed */
        html.sidebar-collapsed .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }

        .dashboard-header {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .dashboard-header h1 {
            font-size: 24px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .dashboard-content {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .dashboard-content p {
            color: #666;
            margin: 0;
            padding: 0;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            box-sizing: border-box;
        }

        .main-content h1 {
            font-size: 48px;
            margin-bottom: 5px;
        }

        .main-content hr {
            border: 1px solid #000;
            margin-bottom: 20px;
        }

        @media screen and (min-width: 1200px) {
            .main-content {
                padding: 40px;
                margin-top: -30px;
            }

            .main-content h1 {
                font-size: 48px;
            }

            .main-content p {
                font-size: 18px;
                line-height: 1.6;
            }
        }


        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 20px;
        }

        .search-container,
        .filter-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .search-bar {
            display: flex;
            align-items: center;
            background: #f1f1f1;
            padding: 8px 12px;
            border-radius: 8px;
            width: 450px;
        }

        .search-bar i {
            margin-right: 8px;
            color: #669;
        }

        .search-bar input {
            border: none;
            outline: none;
            background: transparent;
            width: 100%;
            font-size: 16px;
        }

        .filter-dropdown {
            position: relative;
            display: flex;
            align-items: center;
            background: #f1f1f1;
            padding: 8px 12px;
            border-radius: 3px;
            min-width: 200px;
        }

        .filter-dropdown select {
            border: none;
            outline: none;
            background: transparent;
            font-size: 16px;
            width: 100%;
            appearance: none;
            cursor: pointer;
        }

        .filter-dropdown i {
            position: absolute;
            right: 12px;
            pointer-events: none;
            color: #669;
        }

        .alert-btn {
            position: relative;
            background-color: #4774d4;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
            width: 250px;
        }

        .badge {
            display: none;
            position: absolute;
            top: -5px;
            right: -10px;
            background-color: #ff4d4f;
            color: #fff;
            font-size: 12px;
            font-weight: bold;
            padding: 3px 6px;
            border-radius: 50%;
        }

        .alert-btn.has-notifications {
            background-color: #ff4d4f;
        }

        .alert-btn.has-notifications .badge {
            display: inline-block;
        }

        .add-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            background-color: #3CB371;
            color: #fff;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .add-btn:hover {
            background-color: #33a16f;
            transform: translateY(-2px);
        }


        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            box-sizing: border-box;
        }

        .main-content h1 {
            font-size: 48px;
            margin-bottom: 5px;
        }

        .main-content hr {
            border: 1px solid #000;
            margin-bottom: 20px;
        }

        .card-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 30px;
            justify-items: center;
            padding: 15px;
            border-radius: 8px;
        }

        .card {
            width: 200px;
            height: 265px;
            background-color: #fff;
            border-radius: 2px;
            border: 1px solid rgba(128, 128, 128, 0.5);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 5px;
            box-sizing: border-box;
        }

        .card-title {
            font-size: 1.2rem;
            text-align: center;
            margin-bottom: 10px;
        }

        .card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .card-description {
            width: 100%;
            height: 35px;
            border: 1px solid rgba(128, 128, 128, 0.5);
            padding: 8px;
            text-align: center;
            font-size: 0.9rem;
            border-radius: 4px;
            box-sizing: border-box;
            overflow: hidden;
        }

        @media screen and (max-width: 1200px) {
            .card-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media screen and (max-width: 768px) {
            .card-container {
                grid-template-columns: 1fr;
            }
        }

        .alert-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 50px auto;
            padding: 20px;
            border: 2px solid red;
            width: 500px;
            height: 450px;
            border-radius: 8px;
            box-sizing: border-box;
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .product-table {
            display: flex;
            flex-direction: column;
            margin-top: 20px;
        }

        .table-header,
        .table-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .table-header div,
        .table-row div {
            flex: 1;
            text-align: center;
        }

        .restock-btn {
            padding: 5px 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .restock-btn:hover {
            background-color: #218838;
        }

        .quantity-selector {
            width: 60px;
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <div class="main-content">
        <div class="dashboard-header">
            <h1>Products Dashboard</h1>
        </div>

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

    <div id="alertModal" class="alert-modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2 class="modal-title" style="text-align:center; color:red;">LOW STOCK ALERT</h2>

            <div class="product-table">
                <div class="table-header">
                    <div>Product</div>
                    <div>Remaining Stocks</div>
                    <div>Actions</div>
                    <div>Quantity</div>
                </div>

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

    alertBtn.addEventListener("click", () => {
        alertModal.style.display = "block";
    });

    closeBtn.addEventListener("click", () => {
        alertModal.style.display = "none";
    });

    window.addEventListener("click", (event) => {
        if (event.target === alertModal) {
            alertModal.style.display = "none";
        }
    });

    restockButtons.forEach(button => {
        button.addEventListener("click", () => {
            alert("Product Restocked");
            alertModal.style.display = "none";
        });
    });

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