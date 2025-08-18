<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="addtiles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>ADDPRODUCT</title>
</head>

<body>
    <aside class="sidebar">
        <div class="sidebar-top">
            <img src="raltticon.png" alt="Logo">
            <h2>Staff Dashboard</h2>
            <hr>
            <nav>
                <a href="#"><i class="fas fa-chart-line"></i> Analytics</a>
                <a href="#"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="addprod.php"><i class="fas fa-box"></i> Products</a>
                <a href="#"><i class="fas fa-check-circle"></i> Complete Order</a>
                <a href="#"><i class="fas fa-truck"></i> Suppliers</a>
            </nav>
        </div>
        <div class="sidebar-bottom">
            <a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <div class="main-content">
        <h1>ADD PRODUCT</h1>
        <hr>

        <div class="form-container">
            <div class="form-header">
                <i class="fa-regular fa-calendar-plus" style="margin-right: 10px;"></i>
                <span class="form-header-text">Add Product Details</span>
            </div>
            <div class="form-content">
                <div class="image-box">
                    Add Image
                </div>
                <div class="product-fields">
                    <div class="field-group">
                        <label for="productName">Product Name</label>
                        <input type="text" id="productName" placeholder="Enter product name">
                    </div>
                    <div class="field-group">
                        <label for="productPrice">Product Price</label>
                        <input type="text" id="productPrice" placeholder="Enter product price">
                    </div>
                </div>
            </div>

            <div class="product-description-field">
                <label for="productDescription">Product Description</label>
                <textarea id="productDescription" placeholder="Enter product description"></textarea>
            </div>
            <div class="field-group">
                <label for="productType">Product Type</label>
                <select id="productType">
                    <option>SELECT PRODUCT TYPE</option>
                    <option>INDOOR</option>
                    <option>OUTDOOR</option>
                    <option>POOL</option>
                    <option>KITCHEN</option>
                </select>
            </div>
            <div class="field-group">
                <label for="tileDesign">Tile Design</label>
                <select id="tileDesign">
                    <option>SELECT TILE DESIGN</option>
                    <option>NATURE</option>
                    <option>FLORAL</option>
                    <option>SHAPES</option>
                    <option>STONE</option>
                    <option>GLOSSY</option>
                </select>
            </div>

            <div class="field-group">
                <label for="inventoryCount">Product Inventory Count</label>
                <input type="number" id="inventoryCount" min="0" placeholder="Enter inventory count">
            </div>

            <div class="field-group">
                <label for="minStock">Minimum Stock Count</label>
                <input type="number" id="minStock" min="0" placeholder="Enter minimum stock">
            </div>

            <div class="button-row">
                <button id="addProductBtn">Add Product</button>
            </div>

        </div>
        <script>
            document.getElementById('addProductBtn').addEventListener('click', function () {
                alert("Product Added");
            });

        </script>
</body>

</html>