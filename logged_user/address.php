<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Address</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="address.css">
</head>

<body>
    <aside class="sidebar">
        <div class="sidebar-top">
            <img src="raltticon.png" alt="Logo">
            <h2>My Profile</h2>
            <hr>
            <nav>
                <a href="myProfile.php"><i class="fa-solid fa-user"></i> Account </a>
                <a href="#"><i class="fas fa-box"></i> Orders </a>
                <a href="#"><i class="fas fa-check-circle"></i> Receipts </a>
            </nav>
        </div>
        <div class="sidebar-bottom">
            <a href="#"><i class="fas fa-sign-out-alt"></i> Logout </a>
        </div>
    </aside>

    <div class="main-content">
        <div class="profile-form-container">
            <button class="back-btn" onclick="window.history.back();">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back</span>
            </button>

            <h2>Edit Address</h2>
            <form id="editAddressForm">
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" placeholder="John Doe" required>
                </div>

                <div class="form-group">
                    <label for="barangay">Barangay</label>
                    <input type="text" id="barangay" name="barangay" placeholder="Barangay Name" required>
                </div>

                <div class="form-group">
                    <label for="street">Street Code / Street</label>
                    <input type="text" id="street" name="street" placeholder="Street / House Number" required>
                </div>

                <div class="form-group">
                    <label for="district">District</label>
                    <input type="text" id="district" name="district" placeholder="District" required>
                </div>

                <div class="form-group">
                    <label for="province">Province</label>
                    <input type="text" id="province" name="province" placeholder="Province" required>
                </div>

                <div class="form-group">
                    <label for="region">Region</label>
                    <input type="text" id="region" name="region" placeholder="Region" required>
                </div>

                <div class="form-group">
                    <label for="zipcode">Zip / Postal Code</label>
                    <input type="text" id="zipcode" name="zipcode" placeholder="1234" required>
                </div>

                <div class="form-buttons">
                    <button type="button" class="cancel-btn" onclick="window.history.back();">Cancel</button>
                    <button type="submit" class="save-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
