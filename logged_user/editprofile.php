<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="editprofile.css">
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

    <main class="main-content">
        <div class="form-container">
            <button class="back-btn" onclick="window.location.href='myProfile.php'">
                <i class="fa-solid fa-arrow-left"></i> Back
            </button>

            <div class="form-header">
                <h2><i class="fa-solid fa-user-pen"></i> Edit Profile</h2>
            </div>

            <form action="saveprofile.php" method="POST" enctype="multipart/form-data" class="profile-form">

                <div class="form-row profile-row">
                    <label class="profile-pic-label" for="profilePic">
                        <img id="previewPic" src="jolenekujo.jpg" alt="Profile Picture">
                        <input type="file" id="profilePic" name="profilePic" accept="image/*" capture="user" hidden
                            onchange="previewImage(event)">
                    </label>

                    <div class="profile-info">
                        <div>
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" value="Cholene Jane Aberin" required>
                        </div>
                        <div>
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="cholene.doe@email.com" required>
                        </div>
                    </div>
                </div>

                <div class="lower-row">
                    <div class="form-group">
                        <label for="contact">Mobile Number</label>
                        <input type="text" id="contact" name="contact" value="+63 912 345 6789" required>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" value="Caloocan" required>
                    </div>
                    <div class="form-group half">
                        <label>Gender</label>
                        <div class="gender-icons">
                            <div class="gender-box" data-gender="female">
                                <input type="radio" name="gender" value="female" checked>
                                <i class="fa-solid fa-venus"></i>
                            </div>
                            <div class="gender-box" data-gender="male">
                                <input type="radio" name="gender" value="male">
                                <i class="fa-solid fa-mars"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" readonly>Blk 15 Lot 3, Phase 4, Long Street Name</textarea>
                </div>
                <div class="form-actions-left">
                    <button type="button" class="view-btn">View All Addresses</button>
                </div>

                <div class="form-actions">
                    <button type="submit" class="save-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function previewImage(event) {
            const output = document.getElementById('previewPic');
            output.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>

    <script>
        const genderBoxes = document.querySelectorAll('.gender-box');

        genderBoxes.forEach(box => {
            box.addEventListener('click', () => {
                genderBoxes.forEach(b => b.classList.remove('selected'));
                box.classList.add('selected');
                box.querySelector('input').checked = true;
            });
        });

    </script>

</body>

</html>