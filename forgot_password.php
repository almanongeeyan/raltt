<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Rich Anne Lea Tiles Trading</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

        :root {
            --primary-color: #DC8254;
            --secondary-color: #A6572F;
            --background-gradient: linear-gradient(219.23deg, #FFFFFF 3.91%, #FFD8C5 48.31%, #DC8254 80.68%);
            --button-color: #8C4724;
            --text-color: #333;
            --light-text-color: #666;
            --input-border-color: #ddd;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --viewport-padding: 24px;
            --success-color: #4CAF50;
            --error-color: #f44336;
            --warning-color: #ff9800;
        }

        html {
            font-size: 15px;
            line-height: 1.25;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--background-gradient);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100dvh;
            padding: var(--viewport-padding) 16px;
            overflow: hidden;
            color: var(--text-color);
            position: relative;
        }

        .back-btn {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: #fff;
            border: 1px solid var(--input-border-color);
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            color: var(--secondary-color);
            font-size: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            z-index: 10;
        }

        .back-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .reset-container {
            background: #fff;
            border-radius: 20px;
            box-shadow: var(--box-shadow);
            padding: 28px;
            width: 100%;
            max-width: 450px;
            max-height: calc(100dvh - (var(--viewport-padding) * 2));
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            -ms-overflow-style: none;
            scrollbar-width: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 5;
        }

        .reset-container::-webkit-scrollbar {
            width: 0;
            height: 0;
        }

        .reset-form {
            width: 100%;
            text-align: center;
        }

        .reset-form h2 {
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--text-color);
        }

        .reset-form p {
            font-size: 0.85rem;
            color: var(--light-text-color);
            margin-bottom: 10px;
        }

        .form-group {
            margin-bottom: 12px;
            position: relative;
        }

        .form-group label {
            display: block;
            text-align: left;
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 4px;
            font-size: 0.9rem;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .input-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--input-border-color);
            border-radius: 6px;
            font-size: 0.95rem;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .input-group input:disabled {
            background-color: #e9ecef;
        }

        .input-group input:focus:not(:disabled) {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(220, 130, 84, 0.2);
        }

        .input-group input::placeholder {
            color: #bbb;
        }

        .input-group .toggle-password {
            position: absolute;
            right: 15px;
            cursor: pointer;
            color: #999;
            font-size: 0.95rem;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: var(--button-color);
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.2s ease;
            margin-top: 8px;
        }

        .btn-submit:hover:not(:disabled) {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .btn-submit:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .login-link {
            font-size: 0.85rem;
            margin-top: 16px;
            color: var(--light-text-color);
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .login-link a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .validation-message {
            text-align: left;
            font-size: 0.75rem;
            margin-top: 4px;
            min-height: 16px;
        }

        .password-requirements {
            text-align: left;
            font-size: 0.7rem;
            color: var(--light-text-color);
            margin-top: 4px;
            margin-bottom: 8px;
        }

        .password-strength-meter {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
            background-color: #eee;
            overflow: hidden;
        }

        .password-strength-meter-fill {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease, background-color 0.3s ease;
        }

        /* Password strength colors */
        .strength-weak { background-color: var(--error-color); width: 33%; }
        .strength-medium { background-color: var(--warning-color); width: 66%; }
        .strength-strong { background-color: var(--success-color); width: 100%; }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .step {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #ddd;
            margin: 0 5px;
            transition: background-color 0.3s ease;
        }

        .step.active {
            background-color: var(--primary-color);
        }

        .step.completed {
            background-color: var(--success-color);
        }

        .resend-container {
            margin-top: 10px;
            font-size: 0.8rem;
            color: var(--light-text-color);
        }

        .resend-link {
            color: var(--primary-color);
            cursor: pointer;
            font-weight: 500;
        }

        .resend-link:hover {
            text-decoration: underline;
        }

        .resend-link.disabled {
            color: #999;
            cursor: not-allowed;
        }

        /* Compact adjustments for short viewports */
        @media (max-height: 800px) {
            html { font-size: 14px; }
            .reset-container { padding: 20px; }
            .reset-form h2 { font-size: 1.45rem; }
            .reset-form p { margin-bottom: 8px; font-size: 0.82rem; }
            .form-group { margin-bottom: 10px; }
            .input-group input { padding: 9px 11px; font-size: 0.93rem; }
            .btn-submit { padding: 10px; font-size: 0.93rem; }
        }

        @media (max-height: 680px) {
            html { font-size: 13px; }
            .reset-container { padding: 16px; }
            .reset-form h2 { font-size: 1.3rem; }
            .reset-form p { margin-bottom: 8px; font-size: 0.8rem; }
            .form-group { margin-bottom: 8px; }
            .form-group label { font-size: 0.8rem; }
            .input-group input { padding: 8px 10px; font-size: 0.9rem; }
            .btn-submit { padding: 9px; font-size: 0.9rem; }
            .login-link { margin-top: 12px; font-size: 0.8rem; }
        }
    </style>
</head>
<body>
    <button class="back-btn" onclick="goBack()" aria-label="Go back">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    
    <div class="reset-container">
        <form class="reset-form" id="resetForm">
            <div class="step-indicator">
                <div class="step active" id="step1"></div>
                <div class="step" id="step2"></div>
                <div class="step" id="step3"></div>
            </div>
            
            <h2>Reset Password</h2>
            
            <!-- Step 1: Enter Phone Number -->
            <div id="step-one">
                <p>Enter the phone number used to create your account</p>
                
                <div class="form-group">
                    <label for="phone">Phone Number (Required)*</label>
                    <div class="input-group">
                        <input type="tel" id="phone" name="phone" autocomplete="off" placeholder="(e.g., +639171234567)" pattern="\+639[0-9]{9}" title="Phone number must be in the format +639xxxxxxxxx" maxlength="13" required>
                    </div>
                    <div id="phone-validation" class="validation-message"></div>
                    <div id="verify-status"></div>
                </div>
                
                <button type="button" class="btn-submit" id="continue-btn">Continue</button>
            </div>
            
            <!-- Step 2: Enter Verification Code -->
            <div id="step-two" style="display: none;">
                <p>Enter the verification code sent to your phone</p>
                
                <div class="form-group">
                    <label for="verification_code">Verification Code</label>
                    <div class="input-group">
                        <input type="text" id="verification_code" name="verification_code" placeholder="Enter the 6-digit code" autocomplete="off" maxlength="6" required>
                    </div>
                    <div id="code-validation" class="validation-message"></div>
                    
                    <div class="resend-container">
                        Didn't receive the code? 
                        <span class="resend-link disabled" id="resend-link">Resend Code</span>
                        <span id="countdown-text"> in <span id="countdown">120</span> seconds</span>
                    </div>
                </div>
                
                <button type="button" class="btn-submit" id="verify-code-btn" disabled>Verify Code</button>
            </div>
            
            <!-- Step 3: Create New Password -->
            <div id="step-three" style="display: none;">
                <p>Create your new password</p>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="input-group">
                        <input type="password" id="new_password" minlength="8" autocomplete="off" name="new_password" placeholder="Enter your new password (minimum 8 characters)" required>
                        <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePasswordVisibility('new_password')"></i>
                    </div>
                    <div class="password-requirements">
                        Password must be at least 8 characters long
                    </div>
                    <div class="password-strength-meter">
                        <div class="password-strength-meter-fill" id="password-strength-meter"></div>
                    </div>
                    <div id="password-validation" class="validation-message"></div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" minlength="8" id="confirm_password" autocomplete="off" name="confirm_password" placeholder="Confirm your new password" required>
                        <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePasswordVisibility('confirm_password')"></i>
                    </div>
                    <div id="password-match-status" class="validation-message"></div>
                </div>
                
                <button type="submit" class="btn-submit" id="reset-btn" disabled>Reset Password</button>
            </div>
            
            <p class="login-link">Remember your password? <a href="user_login_form.php">Log In</a></p>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Back button function
            window.goBack = function() {
                window.history.back();
            };

            // Password visibility toggle
            window.togglePasswordVisibility = function(id) {
                const input = document.getElementById(id);
                const icon = input.nextElementSibling;
                if (input.type === "password") {
                    input.type = "text";
                    icon.classList.remove("fa-eye-slash");
                    icon.classList.add("fa-eye");
                } else {
                    input.type = "password";
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                }
            };

            // Password strength checker
            function checkPasswordStrength(password) {
                let strength = 0;
                const meter = document.getElementById('password-strength-meter');
                
                if (password.length >= 8) strength += 20;
                if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength += 20;
                if (password.match(/([0-9])/)) strength += 20;
                if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/)) strength += 20;
                if (password.length > 10) strength += 20;
                
                // Update the strength meter
                meter.className = 'password-strength-meter-fill';
                if (strength <= 20) {
                    meter.classList.add('strength-weak');
                } else if (strength <= 60) {
                    meter.classList.add('strength-medium');
                } else {
                    meter.classList.add('strength-strong');
                }
                
                return strength;
            }

            // Validate password
            function validatePassword(password) {
                const validationMsg = document.getElementById('password-validation');
                if (password.length === 0) {
                    validationMsg.textContent = '';
                    return false;
                }
                if (password.length < 8) {
                    validationMsg.textContent = 'Password must be at least 8 characters long';
                    validationMsg.style.color = 'red';
                    return false;
                }
                validationMsg.textContent = 'Password meets requirements';
                validationMsg.style.color = 'green';
                return true;
            }

            // Validate confirm password
            function validateConfirmPassword(password, confirmPassword) {
                const matchStatus = document.getElementById('password-match-status');
                if (confirmPassword.length === 0) {
                    matchStatus.textContent = '';
                    return false;
                }
                if (password !== confirmPassword) {
                    matchStatus.textContent = 'Passwords do not match';
                    matchStatus.style.color = 'red';
                    return false;
                }
                matchStatus.textContent = 'Passwords match';
                matchStatus.style.color = 'green';
                return true;
            }

            // Validate phone number
            function validatePhone(phone) {
                const validationMsg = document.getElementById('phone-validation');
                const phonePattern = /^\+639[0-9]{9}$/;
                
                if (phone.length === 0) {
                    validationMsg.textContent = '';
                    return false;
                }
                if (!phonePattern.test(phone)) {
                    validationMsg.textContent = 'Please enter a valid phone number in the format +639xxxxxxxxx';
                    validationMsg.style.color = 'red';
                    return false;
                }
                validationMsg.textContent = '';
                return true;
            }

            // Validate verification code
            function validateVerificationCode(code) {
                const validationMsg = document.getElementById('code-validation');
                if (code.length === 0) {
                    validationMsg.textContent = '';
                    return false;
                }
                if (code.length !== 6 || !/^\d+$/.test(code)) {
                    validationMsg.textContent = 'Please enter a valid 6-digit code';
                    validationMsg.style.color = 'red';
                    return false;
                }
                validationMsg.textContent = '';
                return true;
            }

            // Form elements
            const resetForm = document.getElementById('resetForm');
            const continueBtn = document.getElementById('continue-btn');
            const verifyCodeBtn = document.getElementById('verify-code-btn');
            const resetBtn = document.getElementById('reset-btn');
            const phoneInput = document.getElementById('phone');
            const newPasswordInput = document.getElementById('new_password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const verificationCodeInput = document.getElementById('verification_code');
            const stepOne = document.getElementById('step-one');
            const stepTwo = document.getElementById('step-two');
            const stepThree = document.getElementById('step-three');
            const step1Indicator = document.getElementById('step1');
            const step2Indicator = document.getElementById('step2');
            const step3Indicator = document.getElementById('step3');
            const verifyStatusDiv = document.getElementById('verify-status');
            const resendLink = document.getElementById('resend-link');
            const countdownText = document.getElementById('countdown-text');
            const countdownElement = document.getElementById('countdown');

            let resendTimer = null;
            let resendCountdown = 0;
            let currentPhoneNumber = '';

            // Check if form is ready for step 3
            function checkStepTwoCompletion() {
                const isCodeValid = validateVerificationCode(verificationCodeInput.value);
                verifyCodeBtn.disabled = !isCodeValid;
            }

            // Check if form is ready for submission
            function checkStepThreeCompletion() {
                const isPasswordValid = validatePassword(newPasswordInput.value);
                const isPasswordMatch = validateConfirmPassword(newPasswordInput.value, confirmPasswordInput.value);
                
                resetBtn.disabled = !(isPasswordValid && isPasswordMatch);
            }

            // Verification code validation on input
            verificationCodeInput.addEventListener('input', function() {
                validateVerificationCode(this.value);
                checkStepTwoCompletion();
            });

            // Password validation on input
            newPasswordInput.addEventListener('input', function() {
                checkPasswordStrength(this.value);
                validatePassword(this.value);
                validateConfirmPassword(this.value, confirmPasswordInput.value);
                checkStepThreeCompletion();
            });

            // Confirm password validation
            confirmPasswordInput.addEventListener('input', function() {
                validateConfirmPassword(newPasswordInput.value, this.value);
                checkStepThreeCompletion();
            });

            // Resend countdown functions
            function startResendCountdown() {
                resendCountdown = 120; // 2 minutes
                resendLink.classList.add('disabled');
                countdownText.style.display = 'inline';
                updateCountdownText();
                
                resendTimer = setInterval(() => {
                    resendCountdown--;
                    updateCountdownText();
                    if (resendCountdown <= 0) {
                        clearInterval(resendTimer);
                        resendLink.classList.remove('disabled');
                        countdownText.style.display = 'none';
                    }
                }, 1000);
            }

            function updateCountdownText() {
                if (resendCountdown > 0) {
                    const min = Math.floor(resendCountdown / 60);
                    const sec = resendCountdown % 60;
                    countdownElement.textContent = `${min}:${sec.toString().padStart(2, '0')}`;
                }
            }

            // Send verification code and proceed to step 2
            continueBtn.addEventListener('click', async function() {
                const phoneNumber = phoneInput.value;
                if (!validatePhone(phoneNumber)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Phone Number',
                        text: 'Please enter a valid phone number in the format +639xxxxxxxxx.'
                    });
                    return;
                }

                continueBtn.disabled = true;
                continueBtn.textContent = 'Sending...';
                verifyStatusDiv.textContent = 'Checking number and sending code...';
                verifyStatusDiv.style.color = 'blue';

                try {
                    // Check if phone is registered (simulated)
                    const checkResponse = await fetch('connection/check_phone_registered.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ phone: phoneNumber })
                    });

                    const checkData = await checkResponse.json();

                    if (checkData.status !== 'registered') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Phone Not Found',
                            text: 'This phone number is not registered. Please check your number or sign up for a new account.'
                        }).then(() => {
                            continueBtn.disabled = false;
                            continueBtn.textContent = 'Continue';
                            verifyStatusDiv.textContent = '';
                        });
                        return;
                    }

                    // Send verification code (simulated)
                    const formData = new FormData();
                    formData.append('phone', phoneNumber);

                    const response = await fetch('connection/send_verification_debug.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        verifyStatusDiv.textContent = 'Code Sent. Check your messages.';
                        verifyStatusDiv.style.color = 'green';

                        // Store the phone number for verification
                        currentPhoneNumber = phoneNumber;
                        
                        // Move to step 2
                        stepOne.style.display = 'none';
                        stepTwo.style.display = 'block';
                        step1Indicator.classList.remove('active');
                        step1Indicator.classList.add('completed');
                        step2Indicator.classList.add('active');
                        
                        // Start resend countdown
                        startResendCountdown();
                        
                        // Focus on verification code input
                        verificationCodeInput.focus();
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    verifyStatusDiv.textContent = error.message || 'Failed to send code. Please try again.';
                    verifyStatusDiv.style.color = 'red';
                    continueBtn.disabled = false;
                    continueBtn.textContent = 'Continue';
                }
            });

            // Resend code functionality
            resendLink.addEventListener('click', function() {
                if (resendLink.classList.contains('disabled')) return;
                
                // Resend the verification code
                const formData = new FormData();
                formData.append('phone', currentPhoneNumber);

                fetch('connection/send_verification_debug.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Code Resent',
                            text: 'A new verification code has been sent to your phone.'
                        });
                        startResendCountdown();
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Resend Failed',
                        text: 'Failed to resend code. Please try again.'
                    });
                });
            });

            // Verify code and proceed to step 3
            verifyCodeBtn.addEventListener('click', async function() {
                const verificationCode = verificationCodeInput.value;
                
                if (!validateVerificationCode(verificationCode)) {
                    return;
                }

                verifyCodeBtn.disabled = true;
                verifyCodeBtn.textContent = 'Verifying...';

                try {
                    // Verify the code (simulated)
                    const formData = new FormData();
                    formData.append('phone', currentPhoneNumber);
                    formData.append('code', verificationCode);

                    const response = await fetch('connection/check_verification.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        // Show step three
                        stepTwo.style.display = 'none';
                        stepThree.style.display = 'block';
                        step2Indicator.classList.remove('active');
                        step2Indicator.classList.add('completed');
                        step3Indicator.classList.add('active');
                        
                        // Focus on the new password field
                        newPasswordInput.focus();
                    } else {
                        throw new Error(data.message || 'Invalid verification code');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    document.getElementById('code-validation').textContent = error.message || 'Invalid code. Please try again.';
                    document.getElementById('code-validation').style.color = 'red';
                    verifyCodeBtn.disabled = false;
                    verifyCodeBtn.textContent = 'Verify Code';
                }
            });

            // Form submission (Step 3)
            resetForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                // Final validation check
                if (!validatePassword(newPasswordInput.value) || 
                    !validateConfirmPassword(newPasswordInput.value, confirmPasswordInput.value)) {
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Error',
                        text: 'Please make sure your passwords are valid and match.'
                    });
                    return;
                }

                const submitBtn = document.getElementById('reset-btn');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Resetting...';

                try {
                    // Prepare data for password reset
                    const formData = new FormData();
                    formData.append('phone', currentPhoneNumber);
                    formData.append('new_password', newPasswordInput.value);

                    // Call API to reset password (simulated)
                    const response = await fetch('connection/reset_password.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Password Reset',
                            text: 'Your password has been successfully reset.',
                            confirmButtonText: 'Proceed to Login'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'user_login_form.php';
                            }
                        });
                    } else {
                        throw new Error(data.message || 'Password reset failed');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Reset Failed',
                        text: error.message || 'Something went wrong. Please try again later.'
                    });
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Reset Password';
                }
            });
        });
    </script>
</body>
</html>