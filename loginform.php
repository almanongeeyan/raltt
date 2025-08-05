<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rich Anne Lea Tiles - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Judson:wght@400;700&family=Secular+One&display=swap"
        rel="stylesheet">
    <style>
        /* Reset and Base Styles */
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

        /* Background and Layout */
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

        /* Left Side - App Download Section */
        .login-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            max-width: 500px;
            position: relative;
            height: 100%;
            /* Ensure content starts at top */
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
            margin-left: 2rem; /* Move slightly to the right */
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

        /* Button Styles */
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

        /* Right Side - Login Form */
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

        /* Responsive Design */
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
                <!-- Removed phone image from left section -->
            </div>

            <div class="login-right">
                <div class="login-box">
                    <div class="logo"><span>RAL</span><span class="tt">TT</span></div>
                    <form>
                        <div class="form-group">
                            <input type="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <input id="password-field" type="password" placeholder="Password" required>
                            <span class="toggle-password" id="toggle-password" tabindex="0" role="button" aria-label="Show/Hide password"><i class="fa fa-eye"></i></span>
                        </div>
                        <div class="forgot-password">
                            <a href="#">Forgot password?</a>
                        </div>
                        <button type="submit" class="btn btn-primary">Log in</button>
                        <div class="or-divider">OR</div>
                        <a href="#" class="btn btn-secondary">
                            <img src="images/googlebtn.png"
                                alt="Google" class="btn-icon" width="18">
                            Continue with Google
                        </a>
                    </form>
                </div>
            </div>
        </div>
        <!-- Fixed phone image at left side -->
        <img src="images/loginphone.png" alt="App on phone" class="fixed-phone-mockup">
    </div>

    <script>
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = 'index.php';
            }
        }

        // Improved password toggle: always clickable, responsive
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
    </script>
</body>

</html>