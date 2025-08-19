<?php include '../includes/sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Products Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            position: relative;
            display: flex;
            align-items: center;
            background: #f1f1f1;
            padding: 8px 12px;
            border-radius: 8px;
            width: 450px;
            border: 1px solid gray;
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

        .search-suggestions {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: #fff;
            border: 1px solid #ccc;
            border-top: none;
            max-height: 200px;
            overflow-y: auto;
            z-index: 10;
            border-radius: 0 0 6px 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .search-suggestions div {
            padding: 8px 12px;
            cursor: pointer;
        }

        .search-suggestions div:hover {
            background-color: #f0f0f0;
        }


        .filter-dropdown {
            position: relative;
            display: flex;
            align-items: center;
            background: #f1f1f1;
            padding: 8px 12px;
            border-radius: 3px;
            min-width: 200px;
            border: 1px solid Gray;
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
            background-color: #fff;
            color: #ff4d4f;
            border: 2px solid #ff4d4f;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            width: 200px;
            transition: all 0.3s ease;
        }

        .alert-btn i {
            margin-right: 8px;
            transition: color 0.3s ease;
        }

        .alert-btn::after {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            top: 50%;
            left: 50%;
            background: rgba(255, 77, 79, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%) scale(0);
            opacity: 0;
            transition: transform 0.6s ease, opacity 0.8s ease;
            pointer-events: none;
            z-index: 0;
        }

        .alert-btn:hover::after {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }

        .alert-btn:hover {
            background-color: #ff4d4f;
            color: #fff;
            border-color: #ff4d4f;
        }

        .alert-btn:hover i {
            color: #fff;
        }

        .alert-btn span,
        .alert-btn i {
            position: relative;
            z-index: 1;
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

        .restock-modal {
            display: none;
            position: fixed;
            z-index: 1100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .restock-modal .modal-content {
            background-color: #fff;
            margin: 80px auto;
            padding: 20px 30px;
            width: 650px;
            height: 400px;
            border-radius: 3px;
            border: 1px solid #ccc;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-sizing: border-box;
        }

        .restock-modal .modal-content h2 {
            font-size: 20px;
            margin: 0 0 15px 0;
            color: #333;
        }

        .restock-modal label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .restock-modal .text-row {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .restock-modal input[type="text"],
        .restock-modal select {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .restock-modal input[disabled] {
            background-color: #f1f1f1;
            color: #555;
        }

        .restock-modal .dropdown {
            width: 375px;
        }

        .restock-modal .textbox {
            flex: 1;
        }

        .restock-modal .bottom-row {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .restock-modal .submit-btn {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .restock-modal .submit-btn i {
            font-size: 16px;
        }

        .restock-modal .submit-btn:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .restock-modal .cancel-btn {
            background-color: #ccc;
            color: #333;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

.submit-btn {
    position: relative;
    background-color: #28a745;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.submit-btn i {
    position: absolute;
    left: 10px;
    transition: all 0.5s ease;
    pointer-events: none;
}

.submit-btn span {
    transition: opacity 0.3s ease;
}

.submit-btn:hover i {
    left: 50%;
    transform: translateX(-50%);
}
/*
.submit-btn .ripple {
    position: absolute;
    border-radius: 50%;
    transform: scale(0);
    animation: ripple-effect 0.6s linear;
    pointer-events: none; 
    z-index: 0;
}

@keyframes ripple-effect {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

@keyframes truckDrive {
    0% { transform: translateX(-50%) translateY(0); }
    25% { transform: translateX(-50%) translateY(-2px); }
    50% { transform: translateX(-50%) translateY(0); }
    75% { transform: translateX(-50%) translateY(2px); }
    100% { transform: translateX(-50%) translateY(0); }
}

.submit-btn:hover i {
    animation: truckDrive 1s infinite;
}

.submit-btn .ripple {
    position: absolute;
    border-radius: 50%;
    transform: scale(0);
    animation: ripple-effect 0.6s linear;
    background: rgba(255, 255, 255, 0.5);
    pointer-events: none; 
    z-index: 0;
}

@keyframes ripple-effect {
    to { transform: scale(4); opacity: 0; }
}
*/

        #okRestockBtn {
            padding: 8px 16px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        #okRestockBtn:hover {
            background-color: #218838;
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
                    <input type="text" placeholder="Product Search Bar" id="searchInput">
                    <div class="search-suggestions" id="searchSuggestions">
                    </div>
                </div>

                <button class="alert-btn has-notifications">
                    <i class="fas fa-bell"></i>
                    <span>Alerts</span>
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
                </div>

                <div class="table-row">
                    <div>Product 1</div>
                    <div>10</div>
                    <div><button class="restock-btn">Restock</button></div>
                </div>

                <div class="table-row">
                    <div>Product 2</div>
                    <div>13</div>
                    <div><button class="restock-btn">Restock</button></div>
                </div>

                <div class="table-row">
                    <div>Product 3</div>
                    <div>14</div>
                    <div><button class="restock-btn">Restock</button></div>
                </div>

                <div class="table-row">
                    <div>Product 4</div>
                    <div>17</div>
                    <div><button class="restock-btn">Restock</button></div>
                </div>
            </div>
        </div>
    </div>

    <div id="restockModal" class="restock-modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>

            <h3 style="text-align:left;">Order to Supplier</h3>

            <p style="margin-top:10px;">Product</p>
            <div style="display:flex; gap:10px; margin-bottom:20px;">
                <input type="text" id="restockProductName" disabled style="flex:2; padding:8px;" />
                <input type="text" id="restockProductStock" disabled style="flex:1; padding:8px;" />
            </div>

            <p>Supplier</p>
            <div style="display: grid; grid-template-columns: 3fr 0.5fr 0.5fr; gap: 10px; margin-bottom: 10px;">
                <select style="width: 100%; padding: 8px;">
                    <option>Supplier Name</option>
                </select>
                <input type="text" placeholder="Address" disabled style="padding: 8px;" />
                <input type="text" placeholder="Contact" disabled style="padding: 8px;" />
                <select style="width: 100%; padding: 8px;">
                    <option>Buying Unit of Measure</option>
                </select>
                <input type="text" placeholder="Multiplier" style="padding: 8px;" />
                <input type="text" placeholder="Cost Price" disabled style="padding: 8px;" />
            </div>


            <div style="display:flex; justify-content:flex-end; margin-bottom:20px;">
    <input type="text" placeholder="Amount" disabled style="width:125px; padding:8px;" />
</div>


            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button class="cancel-btn"
                    style="background-color:#ccc; color:#000; padding:10px 20px; border-radius:5px;">Cancel</button>
                <button class="submit-btn"
                    style="background-color:#28a745; color:#fff; padding:10px 20px; border-radius:5px;">
                    <i class="fas fa-truck"></i> Submit Order
                </button>
            </div>
        </div>
    </div>




    <script>
        const alertBtn = document.querySelector(".alert-btn");
        const alertModal = document.getElementById("alertModal");
        const alertCloseBtn = alertModal.querySelector(".close-btn");

        alertBtn.addEventListener("click", () => alertModal.style.display = "block");
        alertCloseBtn.addEventListener("click", () => alertModal.style.display = "none");

        const restockModal = document.getElementById("restockModal");
        const restockCloseBtn = restockModal.querySelector(".close-btn");
        const cancelBtn = restockModal.querySelector(".cancel-btn");

        alertModal.addEventListener("click", (e) => {
            if (e.target && e.target.classList.contains("restock-btn")) {
                const row = e.target.closest(".table-row");
                const productName = row.children[0].innerText;
                const stockCount = row.children[1].innerText;

                document.getElementById("restockProductName").value = productName;
                document.getElementById("restockProductStock").value = stockCount;

                restockModal.style.display = "block";
            }
        });

        restockCloseBtn.onclick = cancelBtn.onclick = () => restockModal.style.display = "none";

        window.onclick = (event) => {
            if (event.target === alertModal) alertModal.style.display = "none";
            if (event.target === restockModal) restockModal.style.display = "none";
        };
    </script>

    <script>
        const submitBtn = restockModal.querySelector(".submit-btn");

submitBtn.addEventListener("click", () => {
    alert("Order Submitted");

    restockModal.style.display = "none";
});

    </script>

</body>

</html>