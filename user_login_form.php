
<?php
session_start();
// Prevent back navigation after login
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
// If already logged in, redirect to landing page
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    header('Location: logged_user/landing_page.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rich Anne Lea Tiles - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Judson:wght@400;700&family=Secular+One&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }

        .login-bg {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 0;
            background: linear-gradient(219.23deg, #FFFFFF 23.91%, #FFD8C5 68.31%, #DC8254 90.68%);
        }

        .back-btn {
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #333;
            z-index: 10;
            transition: transform 0.2s;
        }

        .back-btn:hover {
            transform: translateX(-3px);
        }

        .login-container {
            display: flex;
            gap: 5rem;
            align-items: flex-start;
            justify-content: center;
            width: 100%;
            max-width: 1200px;
            position: relative;
        }

        .login-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            max-width: 500px;
            position: relative;
            height: 100%;
        }

        .login-left h1 {
            font-family: 'Judson', serif;
            font-size: 3.5rem;
            font-weight: 400;
            margin-bottom: 2rem;
            color: #000;
            line-height: 1.2;
            position: relative;
            z-index: 2;
            margin-top: 0;
            margin-left: 2rem;
        }

        .phone-mockup {
            display: none;
        }
        .fixed-phone-mockup {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 700px;
            height: auto;
            object-fit: contain;
            z-index: 1;
        }

        .app-buttons {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
            width: 100%;
            position: relative;
            z-index: 2;
            margin-top: 2rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 0.625rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: #8A421D;
            color: #FFFFFF;
        }

        .btn-primary:hover {
            background: #6B3416;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-secondary {
            background: #FFFFFF;
            border: 1px solid #E0E0E0;
            color: #5F6368;
        }

        .btn-secondary:hover {
            background: #F8F8F8;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
        }

        .btn-icon {
            margin-right: 0.625rem;
            font-size: 1rem;
        }

        .google-play-btn {
            width: 180px;
            transition: transform 0.3s ease;
        }

        .google-play-btn:hover {
            transform: translateY(-2px);
        }

        .login-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            background: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.1);
            padding: 2.5rem 1.875rem;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 413px;
            border-radius: 1rem;
        }

        .logo {
            font-family: 'Secular One', sans-serif;
            font-size: 2.8rem;
            text-align: center;
            margin-bottom: 2rem;
            letter-spacing: 2px;
            color: #000;
            display: flex;
            justify-content: center;
        }

        .logo .tt {
            color: #8A421D;
        }

        .login-box form {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            width: 100%;
        }

        .form-group {
            position: relative;
            width: 100%;
        }

        .login-box input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #D1D5DB;
            border-radius: 0.625rem;
            font-size: 1rem;
            background: #FAFAFA;
            outline: none;
            color: #684330;
            transition: border-color 0.3s;
        }

        .login-box input:focus {
            border-color: #8A421D;
        }

        .login-box input::placeholder {
            color: #9CA3AF;
        }

        .toggle-password {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #684330;
        }

        .forgot-password {
            font-size: 0.75rem;
            text-align: right;
            margin-top: -0.5rem;
        }

        .forgot-password a {
            color: #684330;
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-password a:hover {
            color: #8A421D;
            text-decoration: underline;
        }

        .or-divider {
            display: flex;
            align-items: center;
            margin: 1rem 0;
            color: #6B7280;
            font-size: 0.8125rem;
        }

        .or-divider::before,
        .or-divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #E5E7EB;
        }

        .or-divider::before {
            margin-right: 0.5rem;
        }

        .or-divider::after {
            margin-left: 0.5rem;
        }

        .swal2-popup {
            font-family: 'Inter', sans-serif;
        }

        @media (max-width: 1024px) {
            .login-container {
                gap: 3rem;
            }

            .fixed-phone-mockup {
                left: 0;
                bottom: 0;
                width: 450px;
                transform: none;
            }
        }

        @media (max-width: 900px) {
            .login-container {
                flex-direction: column;
                gap: 2rem;
                align-items: center;
            }

            .login-left {
                height: auto;
                align-items: center;
                text-align: center;
                max-width: 100%;
                padding-bottom: 0;
            }

            .login-left h1 {
                font-size: 2.8rem;
                margin-top: 0;
                margin-left: 0;
            }

            .fixed-phone-mockup {
                left: 0;
                bottom: 0;
                width: 320px;
                transform: none;
            }

            .app-buttons {
                align-items: center;
            }

            .login-box {
                padding: 2rem 1.5rem;
                max-width: 100%;
            }

            .back-btn {
                top: 1rem;
                left: 1rem;
            }
        }

        @media (max-width: 480px) {
            .login-left h1 {
                font-size: 2.2rem;
            }

            .fixed-phone-mockup {
                left: 0;
                bottom: 0;
                width: 180px;
                transform: none;
            }
            .login-bg {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="login-bg">
        <button class="back-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i></button>
        <div class="login-container">
            <div class="login-left">
                <h1>Download our app!</h1>
                <img src="images/loginphone.png" alt="App on phone" class="fixed-phone-mockup">
            </div>

            <div class="login-right">
                <div class="login-box">
                    <div class="logo"><span>RAL</span><span class="tt">TT</span></div>
                    <form id="loginForm">
                        <div class="form-group">
                            <input type="tel" name="phone" placeholder="Enter your phone number" required>
                        </div>
                        <div class="form-group">
                            <input id="password-field" name="password" type="password" placeholder="Password" required>
                            <span class="toggle-password" id="toggle-password" tabindex="0" role="button" aria-label="Show/Hide password"><i class="fa fa-eye"></i></span>
                        </div>
                        <div class="forgot-password">
                            <a href="staffadmin_access/admin_analytics.php">Forgot password?</a>
                        </div>
                        <button type="submit" class="btn btn-primary">Log in</button>
                        <div class="or-divider">OR</div>
                        <div style="display: flex; flex-direction: column; gap: 8px; align-items: stretch; width: 100%; margin: 0 0 16px 0;">
                            <button type="button" class="btn btn-primary" style="background: #684330;" onclick="window.location.href='register.php'">Manual Register</button>
                            
                            <div style="display: flex; justify-content: center; align-items: center; width: 100%;">
                                <div>
                                    <div id="g_id_onload"
                                        data-client_id="1043599229897-c7t8ir646mn4i1abs79eeg51r4hu4j66.apps.googleusercontent.com"
                                        data-callback="handleCredentialResponse"
                                        data-auto_prompt="false">
                                    </div>
                                    <div class="g_id_signin"
                                        data-type="standard"
                                        data-size="large"
                                        data-theme="outline"
                                        data-text="continue_with"
                                        data-shape="pill"
                                        data-logo_alignment="left"
                                        data-width="345">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <script>
        // Prevent back button navigation after login
        history.pushState(null, null, location.href);
        window.onpopstate = function() {
            history.go(1);
        };

        function goBack() {
            window.location.href = 'index.php';
        }

        const passwordField = document.getElementById('password-field');
        const togglePassword = document.getElementById('toggle-password');
        const icon = togglePassword.querySelector('i');

        function handleToggle() {
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        togglePassword.addEventListener('click', handleToggle);
        togglePassword.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                handleToggle();
            }
        });

        // Handle manual login form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Logging in...';
            
            fetch('connection/manual_login_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Login successful!',
                        icon: 'success',
                        confirmButtonText: 'Continue',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = data.redirect || 'logged_user/landing_page.php';
                        }
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
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Wrong Phone Number or Password. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                submitBtn.disabled = false;
                submitBtn.textContent = 'Log in';
            });
        });

        function handleCredentialResponse(response) {
            const idToken = response.credential;
            console.log("Encoded JWT ID token: " + idToken);

            fetch('connection/process_google_login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id_token: idToken }),
            })
            .then(response => response.json())
            .then(data => {
                console.log('Backend response:', data);
                if (data.success) {
                    Swal.fire({
                        title: 'Welcome!',
                        text: `Welcome, ${data.name}!`,
                        icon: 'success',
                        confirmButtonText: 'Continue',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.href = 'logged_user/landing_page.php';
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Login failed',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error sending token to backend:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Wrong Phone Number or Password. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    </script>
</body>
</html>