<?php include '../includes/sidebar.php'; ?>

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
    <div class="main-content">
        <div class="dashboard-header">
            <h1>Products Dashboard</h1>
        </div>

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
                <a href="../staffadmin_access/admin_addproduct.php">
                    <button id="addProductBtn">Add Product</button>
                </a>
            </div>

        </div>
        <script>
            document.getElementById('addProductBtn').addEventListener('click', function () {
                alert("Product Added");
            });

        </script>
</body>

</html>