<?php
session_start();
// Prevent back navigation after login
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
// If already logged in as admin, redirect to admin dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    header('Location: staffadmin_access/admin_analytics.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - RALTT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen flex items-center justify-center font-sans bg-[conic-gradient(at_top_left,_var(--tw-gradient-stops))] from-white via-[#FFD8C5] to-[#DC8254]" style="background: linear-gradient(219.23deg, #FFFFFF 23.91%, #FFD8C5 68.31%, #DC8254 90.68%);">
    <button class="back-btn" onclick="goBack()" style="position:absolute;top:1.5rem;left:1.5rem;background:none;border:none;font-size:1.5rem;cursor:pointer;color:#333;z-index:10;transition:transform 0.2s;">
        <i class="fa fa-arrow-left"></i>
    </button>
    <div class="w-full max-w-md mx-auto bg-white/90 border border-orange-100 rounded-2xl shadow-2xl p-8 backdrop-blur-md">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <script>
        function goBack() {
            window.location.href = 'index.php';
        }
    </script>
        <div class="flex flex-col items-center mb-6">
            <span class="text-3xl font-bold tracking-wide text-gray-900">Staff <span class="text-[#8A421D]">Login</span></span>
            <span class="text-sm text-gray-500 mt-1">Restricted access for authorized personnel only</span>
        </div>
        <form id="adminLoginForm" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" id="username" name="username" required class="w-full px-4 py-2 border border-[#D1D5DB] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#8A421D] focus:border-[#8A421D] bg-[#FAFAFA] text-[#684330] placeholder:text-[#9CA3AF] transition" placeholder="Enter admin username">
            </div>
            <div class="relative">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="admin-password-field" name="password" required class="w-full px-4 py-2 border border-[#D1D5DB] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#8A421D] focus:border-[#8A421D] bg-[#FAFAFA] text-[#684330] placeholder:text-[#9CA3AF] transition" placeholder="Enter password">
                <button type="button" id="toggle-admin-password" tabindex="0" class="absolute right-3 top-9 text-[#684330] hover:text-[#8A421D] focus:outline-none" aria-label="Show/Hide password">
                    <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                </button>
            </div>
            <button type="submit" class="w-full py-2 px-4 bg-[#8A421D] hover:bg-[#6B3416] text-white font-semibold rounded-lg shadow transition duration-150">Log in</button>
        </form>
        
    </div>
    <script>
        // Prevent back button navigation after login
        history.pushState(null, null, location.href);
        window.onpopstate = function() {
            history.go(1);
        };

        // Password toggle
        const passwordField = document.getElementById('admin-password-field');
        const togglePassword = document.getElementById('toggle-admin-password');
        const eyeIcon = document.getElementById('eye-icon');
        let passwordVisible = false;
        togglePassword.addEventListener('click', function() {
            passwordVisible = !passwordVisible;
            passwordField.type = passwordVisible ? 'text' : 'password';
            eyeIcon.innerHTML = passwordVisible
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.042-3.292m1.528-1.68A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.956 9.956 0 01-4.043 5.197M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
        });

        // Handle admin login form submission
        document.getElementById('adminLoginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Logging in...';
            fetch('connection/manual_login_process.php?admin=1', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Admin login successful!',
                        icon: 'success',
                        confirmButtonText: 'Continue',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.href = data.redirect || 'staffadmin_access/admin_analytics.php';
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Login failed. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Log in';
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Wrong Username or Password. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                submitBtn.disabled = false;
                submitBtn.textContent = 'Log in';
            });
        });
    </script>
</body>
</html>
