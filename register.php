<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/register.css">
    <style>
        body {
            background: linear-gradient(219.23deg, #FFFFFF 3.91%, #FFD8C5 48.31%, #DC8254 80.68%);
        }
    </style>
</head>
<body>
    <button class="back-btn" onclick="goBack()" aria-label="Go back"><i style="font-size:1.2rem;margin-right:6px;" class="fa fa-arrow-left"></i>Back</button>
    <div class="register-container">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <style>
        .back-btn {
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
            background: none;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            color: #DC8254;
            z-index: 10;
            display: flex;
            align-items: center;
            font-weight: 600;
            transition: color 0.2s, transform 0.2s;
        }
        .back-btn:hover {
            color: #b96536;
            transform: translateX(-3px);
        }
    </style>
    <script>
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = 'index.php';
            }
        }
    </script>
        <form class="register-form" action="manual_register.php" method="POST" enctype="multipart/form-data">
            <h2>Register</h2>
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" required>
            </div>
            <div class="form-group">
                <label for="number">Number</label>
                <input type="text" id="number" name="number" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="profile_picture">Profile Picture</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required>
            </div>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
