<?php
// Handle registration form submission
$successMsg = '';
$errorMsg = '';
$imgUrl = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $number = trim($_POST['number'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $profilePic = $_FILES['profile_picture'] ?? null;

    $errors = [];
    // Basic validation
    if (!$fullname) $errors[] = 'Full Name is required.';
    if (!$number) $errors[] = 'Number is required.';
    if (!$address) $errors[] = 'Address is required.';
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid Email is required.';
    if (!$profilePic || $profilePic['error'] !== UPLOAD_ERR_OK) $errors[] = 'Profile Picture is required.';

    // Handle file upload
    $uploadPath = '';
    if (!$errors && $profilePic) {
        $targetDir = __DIR__ . '/images/user/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $ext = strtolower(pathinfo($profilePic['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Only JPG, PNG, and GIF files are allowed.';
        } else {
            $filename = uniqid('profile_', true) . '.' . $ext;
            $uploadPath = $targetDir . $filename;
            if (!move_uploaded_file($profilePic['tmp_name'], $uploadPath)) {
                $errors[] = 'Failed to upload profile picture.';
            }
        }
    }

    if (!$errors) {
        $successMsg = 'Registration successful!';
        if ($uploadPath) {
            $imgUrl = 'images/user/' . basename($uploadPath);
        }
    } else {
        $errorMsg = implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rich Anne Lea Tiles Trading - Manual Register </title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <?php if ($successMsg): ?>
        <div style="background:#d4edda;color:#155724;padding:15px;border-radius:8px;margin:20px auto;max-width:400px;text-align:center;"> <?= $successMsg ?> </div>
        <?php if ($imgUrl): ?>
            <div style="text-align:center;"><img src="<?= $imgUrl ?>" alt="Profile Picture" style="max-width:120px;border-radius:50%;margin:10px auto;"></div>
        <?php endif; ?>
    <?php else: ?>
        <?php if ($errorMsg): ?>
            <div style="background:#f8d7da;color:#721c24;padding:15px;border-radius:8px;margin:20px auto;max-width:400px;"> <?= $errorMsg ?> </div>
        <?php endif; ?>
        <div class="register-container">
            <form class="register-form" action="manual_register.php" method="POST" enctype="multipart/form-data">
                <h2>Register</h2>
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" required value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="number">Number</label>
                    <input type="text" id="number" name="number" required value="<?= htmlspecialchars($_POST['number'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" required value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="profile_picture">Profile Picture</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required>
                </div>
                <button type="submit">Register</button>
            </form>
        </div>
    <?php endif; ?>
</body>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rich Anne Lea Tiles Trading - Manual Register </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <style>
       
    </style>
</head>

<body>



</body>
</html>