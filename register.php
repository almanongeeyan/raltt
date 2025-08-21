<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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
        }

        .back-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .signup-container {
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
        }

        .signup-container::-webkit-scrollbar {
            width: 0;
            height: 0;
        }

        .signup-form {
            width: 100%;
            text-align: center;
        }

        .signup-form h2 {
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--text-color);
        }

        .signup-form p {
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

        .input-group .verify-btn {
            position: absolute;
            right: 8px;
            background: none;
            border: none;
            color: var(--primary-color);
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s ease;
        }

        .input-group .verify-btn:hover {
            color: var(--secondary-color);
        }

        .input-group .toggle-password {
            position: absolute;
            right: 15px;
            cursor: pointer;
            color: #999;
            font-size: 0.95rem;
        }

        .input-group .locate-btn {
            width: 100%;
            background-color: var(--button-color);
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 8px;
            transition: background-color 0.2s ease, transform 0.2s ease, opacity 0.2s ease;
        }

        .input-group .locate-btn:hover:not(:disabled) {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .input-group .locate-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            opacity: 0.7;
            transform: none;
        }

        #location-info {
            font-size: 0.75rem;
            text-align: left;
            margin-top: 4px;
            min-height: 16px;
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

        #verification-form {
            display: none;
            margin-top: 8px;
        }

        #verification-form label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            font-weight: 500;
        }

        #verification-form .input-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--input-border-color);
            border-radius: 6px;
            font-size: 0.95rem;
        }

        #verification-form .input-group {
            position: relative;
        }

        #verification-form .btn-confirm {
            width: auto;
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background-color: #4CAF50;
            color: #fff;
            border: none;
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        #verification-form .btn-confirm:hover {
            background-color: #45a049;
        }

        #verify-status {
            text-align: left;
            font-size: 0.75rem;
            margin-top: 4px;
            min-height: 16px;
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

        /* Compact adjustments for short viewports */
        @media (max-height: 800px) {
            html { font-size: 14px; }
            .signup-container { padding: 20px; }
            .signup-form h2 { font-size: 1.45rem; }
            .signup-form p { margin-bottom: 8px; font-size: 0.82rem; }
            .form-group { margin-bottom: 10px; }
            .input-group input { padding: 9px 11px; font-size: 0.93rem; }
            .input-group .locate-btn { padding: 10px; font-size: 0.93rem; }
            .btn-submit { padding: 10px; font-size: 0.93rem; }
        }

        @media (max-height: 680px) {
            html { font-size: 13px; }
            .signup-container { padding: 16px; }
            .signup-form h2 { font-size: 1.3rem; }
            .signup-form p { margin-bottom: 8px; font-size: 0.8rem; }
            .form-group { margin-bottom: 8px; }
            .form-group label { font-size: 0.8rem; }
            .input-group input { padding: 8px 10px; font-size: 0.9rem; }
            .input-group .verify-btn { font-size: 0.7rem; }
            .input-group .locate-btn { padding: 9px; font-size: 0.9rem; }
            .btn-submit { padding: 9px; font-size: 0.9rem; }
            .login-link { margin-top: 12px; font-size: 0.8rem; }
        }
    </style>
</head>
<body>
    <button class="back-btn" onclick="goBack()" aria-label="Go back">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    
    <div class="signup-container">
        <form class="signup-form" id="signupForm">
            <h2>Sign Up</h2>
            <p>Create an account</p>
            
            <div class="form-group">
                <label for="fullname">Name</label>
                <div class="input-group">
                    <input type="text" maxlength="30" id="fullname" name="fullname" autocomplete="off" placeholder="Enter your full name" required>
                </div>
                <div id="name-validation" class="validation-message"></div>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <div class="input-group">
                    <input type="tel" id="phone" name="phone" autocomplete="off" placeholder="(e.g., +639171234567)" pattern="\+639[0-9]{9}" title="Phone number must be in the format +639xxxxxxxxx" maxlength="13" required>
                    <button type="button" class="verify-btn" id="send-code-btn">Verify</button>
                </div>
                <div id="verify-status"></div>
                <div id="verification-form">
                    <label for="verification_code">Verification Code</label>
                    <div class="input-group">
                        <input type="text" id="verification_code" name="verification_code" placeholder="Enter the 6-digit code" autocomplete="off" maxlength="6" required>
                        <button type="button" class="btn-confirm" id="confirm-code-btn">Confirm</button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="house_address">House Address</label>
                <div class="input-group">
                    <input type="text" id="house_address" name="house_address" autocomplete="off" placeholder="Enter your house number, street, etc." required>
                </div>
                <div id="address-validation" class="validation-message"></div>
            </div>
            
            <div class="form-group">
                <label for="address">Pin Point Location</label>
                <div class="input-group">
                    <input type="text" id="address" name="address" placeholder="Tap 'Locate Me' to get your full location" readonly required>
                    <button type="button" class="locate-btn">Locate Me</button>
                </div>
                <div id="location-info"></div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-group">
                    <input type="password" id="password" minlength="8" autocomplete="off" name="password" placeholder="Enter your password (minimum 8 characters)" required>
                    <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePasswordVisibility('password')"></i>
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
                    <input type="password" minlength="8" id="confirm_password" autocomplete="off" name="confirm_password" placeholder="Confirm your password" required>
                    <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePasswordVisibility('confirm_password')"></i>
                </div>
                <div id="password-match-status" class="validation-message"></div>
            </div>
            
            <button type="submit" class="btn-submit" id="signup-submit-btn" disabled>Sign up</button>
            
            <p class="login-link">Already have an account? <a href="user_login_form.php">Log In</a></p>
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
                
                if (password !== confirmPassword) {
                    matchStatus.textContent = 'Passwords do not match';
                    matchStatus.style.color = 'red';
                    return false;
                }
                
                matchStatus.textContent = 'Passwords match';
                matchStatus.style.color = 'green';
                return true;
            }

            // Validate name
            function validateName(name) {
                const validationMsg = document.getElementById('name-validation');
                
                if (name.trim().length < 2) {
                    validationMsg.textContent = 'Please enter a valid name';
                    validationMsg.style.color = 'red';
                    return false;
                }
                
                validationMsg.textContent = '';
                return true;
            }

            // Validate address
            function validateAddress(address) {
                const validationMsg = document.getElementById('address-validation');
                
                if (address.trim().length < 5) {
                    validationMsg.textContent = 'Please enter a valid address';
                    validationMsg.style.color = 'red';
                    return false;
                }
                
                validationMsg.textContent = '';
                return true;
            }

            // Geolocation functions
            async function fetchAddress(latitude, longitude) {
                const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`;
                try {
                    const response = await fetch(url);
                    if (!response.ok) {
                        throw new Error('Network response was not ok.');
                    }
                    const data = await response.json();
                    
                    if (data.display_name) {
                        return data.display_name;
                    } else {
                        throw new Error('No address found for these coordinates.');
                    }
                } catch (error) {
                    console.error('Geocoding API error:', error);
                    throw new Error('Failed to retrieve a detailed address.');
                }
            }
            
            async function getAndSetLocation() {
                const addressInput = document.getElementById('address');
                const locationInfo = document.getElementById('location-info');
                const locateBtn = document.querySelector('.locate-btn');

                locateBtn.disabled = true;
                locateBtn.textContent = 'Locating...';

                if (navigator.geolocation) {
                    locationInfo.style.color = 'blue';
                    locationInfo.textContent = 'Getting your precise location...';

                    navigator.geolocation.getCurrentPosition(
                        async (position) => {
                            const { latitude, longitude } = position.coords;
                            locationInfo.textContent = 'Location found. Looking up address...';

                            try {
                                const fullAddress = await fetchAddress(latitude, longitude);
                                addressInput.value = fullAddress;
                                locationInfo.style.color = 'green';
                                locationInfo.textContent = 'Address set successfully!';
                                
                                locateBtn.textContent = 'Location Set';
                                locateBtn.style.backgroundColor = '#4CAF50'; 
                                locateBtn.style.cursor = 'not-allowed';

                                checkFormCompletion();

                            } catch (error) {
                                locationInfo.style.color = 'red';
                                locationInfo.textContent = error.message;
                                addressInput.value = '';
                                
                                locateBtn.disabled = false;
                                locateBtn.textContent = 'Locate Me';
                            }
                        },
                        (error) => {
                            locationInfo.style.color = 'red';
                            let errorMessage = 'Could not get your location.';
                            switch(error.code) {
                                case error.PERMISSION_DENIED:
                                    errorMessage = "User denied location access. Please allow location access in your browser settings.";
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    errorMessage = "Location information is unavailable.";
                                    break;
                                case error.TIMEOUT:
                                    errorMessage = "The request to get user location timed out.";
                                    break;
                                default:
                                    errorMessage = "An unknown error occurred. Please try again.";
                                    break;
                            }
                            locationInfo.textContent = errorMessage;
                            addressInput.value = '';
                            
                            locateBtn.disabled = false;
                            locateBtn.textContent = 'Locate Me';
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 5000,
                            maximumAge: 0
                        }
                    );
                } else {
                    locationInfo.style.color = 'red';
                    locationInfo.textContent = 'Geolocation is not supported by this browser.';
                    addressInput.value = '';
                    locateBtn.disabled = true;
                    locateBtn.textContent = 'Not Supported';
                }
            }
            
            document.querySelector('.locate-btn').addEventListener('click', getAndSetLocation);

            // Form validation and submission
            const signupForm = document.getElementById('signupForm');
            const sendCodeBtn = document.getElementById('send-code-btn');
            const confirmCodeBtn = document.getElementById('confirm-code-btn');
            const phoneInput = document.getElementById('phone');
            const verificationForm = document.getElementById('verification-form');
            const verifyStatusDiv = document.getElementById('verify-status');
            const verificationCodeInput = document.getElementById('verification_code');
            const signupSubmitBtn = document.getElementById('signup-submit-btn');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const fullnameInput = document.getElementById('fullname');
            const houseAddressInput = document.getElementById('house_address');

            let isNumberVerified = false;
            
            // Enhanced form completion check
            function checkFormCompletion() {
                const isNameValid = validateName(fullnameInput.value);
                const isAddressValid = validateAddress(houseAddressInput.value);
                const isPasswordValid = validatePassword(passwordInput.value);
                const isPasswordMatch = validateConfirmPassword(passwordInput.value, confirmPasswordInput.value);
                const isLocationSet = document.getElementById('address').value.trim() !== '';
                
                signupSubmitBtn.disabled = !(isNameValid && isNumberVerified && isAddressValid && 
                                            isPasswordValid && isPasswordMatch && isLocationSet);
            }

            // Phone verification
            sendCodeBtn.addEventListener('click', async () => {
                const phoneNumber = phoneInput.value;
                if (!phoneNumber || !phoneInput.checkValidity()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Phone Number',
                        text: 'Please enter a valid phone number in the format +639xxxxxxxxx.'
                    });
                    return;
                }

                phoneInput.disabled = true;
                sendCodeBtn.disabled = true;
                sendCodeBtn.textContent = 'Sending...';
                verifyStatusDiv.textContent = 'Checking number and sending code...';
                verifyStatusDiv.style.color = 'blue';

                try {
                    // Step 1: Check if number is already registered
                    const checkResponse = await fetch('connection/check_phone_registered.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ phone: phoneNumber })
                    });

                    const checkData = await checkResponse.json();

                    if (checkData.status === 'registered') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Already Registered',
                            text: 'This phone number is already registered. Please log in instead.'
                        }).then(() => {
                            // Re-enable inputs
                            phoneInput.disabled = false;
                            sendCodeBtn.disabled = false;
                            sendCodeBtn.textContent = 'Verify';
                            verifyStatusDiv.textContent = '';
                        });
                        return; // Stop the process
                    }
                    
                    // Step 2: If not registered, proceed to send verification code
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
                        verificationForm.style.display = 'block';
                        
                        // Store the phone number for verification
                        phoneInput.dataset.verifiedPhone = phoneNumber;
                        sendCodeBtn.textContent = 'Resend';
                        sendCodeBtn.disabled = false; // Allow resending the code
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    verifyStatusDiv.textContent = error.message || 'Failed to send code. Please try again.';
                    verifyStatusDiv.style.color = 'red';
                    phoneInput.disabled = false;
                    sendCodeBtn.disabled = false;
                    sendCodeBtn.textContent = 'Verify';
                }
            });

            // Verification code confirmation
            confirmCodeBtn.addEventListener('click', async () => {
                const phoneNumber = phoneInput.dataset.verifiedPhone || phoneInput.value;
                const verificationCode = verificationCodeInput.value;
                
                if (!verificationCode || verificationCode.length !== 6) {
                    verifyStatusDiv.style.color = 'red';
                    verifyStatusDiv.textContent = 'Please enter a valid 6-digit code.';
                    return;
                }

                confirmCodeBtn.disabled = true;
                confirmCodeBtn.textContent = 'Checking...';
                verifyStatusDiv.textContent = 'Checking code...';
                verifyStatusDiv.style.color = 'blue';

                try {
                    // Call the actual Twilio verification check API
                    const formData = new FormData();
                    formData.append('phone', phoneNumber);
                    formData.append('code', verificationCode);
                    
                    const response = await fetch('connection/check_verification.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.status === 'success') {
                        verifyStatusDiv.textContent = 'Phone number verified successfully! ✅';
                        verifyStatusDiv.style.color = 'green';
                        confirmCodeBtn.style.backgroundColor = '#4CAF50';
                        confirmCodeBtn.textContent = 'Verified';
                        isNumberVerified = true;
                        phoneInput.disabled = true;
                        verificationCodeInput.disabled = true;
                        sendCodeBtn.disabled = true;
                        
                        // Add hidden field for verified phone number
                        const hiddenPhoneInput = document.createElement('input');
                        hiddenPhoneInput.type = 'hidden';
                        hiddenPhoneInput.name = 'verified_phone';
                        hiddenPhoneInput.value = phoneNumber;
                        signupForm.appendChild(hiddenPhoneInput);
                        
                        checkFormCompletion();
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    verifyStatusDiv.textContent = error.message || 'Invalid code. Please try again.';
                    verifyStatusDiv.style.color = 'red';
                    confirmCodeBtn.disabled = false;
                    confirmCodeBtn.textContent = 'Confirm';
                    confirmCodeBtn.style.backgroundColor = '';
                }
            });

            // Password validation on input
            passwordInput.addEventListener('input', function() {
                checkPasswordStrength(this.value);
                validatePassword(this.value);
                validateConfirmPassword(this.value, confirmPasswordInput.value);
                checkFormCompletion();
            });

            // Confirm password validation
            confirmPasswordInput.addEventListener('input', function() {
                validateConfirmPassword(passwordInput.value, this.value);
                checkFormCompletion();
            });

            // Name validation
            fullnameInput.addEventListener('input', function() {
                validateName(this.value);
                checkFormCompletion();
            });

            // Address validation
            houseAddressInput.addEventListener('input', function() {
                validateAddress(this.value);
                checkFormCompletion();
            });

            // Form submission
            signupForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                // Final validation check
                if (!isNumberVerified) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Verification Required',
                        text: 'Please verify your phone number before signing up.'
                    });
                    return;
                }

                if (passwordInput.value !== confirmPasswordInput.value) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Mismatch',
                        text: 'Please make sure your passwords match.'
                    });
                    return;
                }

                if (passwordInput.value.length < 8) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Too Short',
                        text: 'Password must be at least 8 characters long.'
                    });
                    return;
                }

                const submitBtn = document.getElementById('signup-submit-btn');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Signing up...';

                try {
                    // Create FormData object from the form
                    const formData = new FormData(signupForm);
                    
                    // Make actual AJAX request to your PHP endpoint
                    const response = await fetch('connection/registered_account_process.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();

                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            confirmButtonText: 'Proceed to Login'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'user_login_form.php'; 
                            }
                        });
                    } else {
                        throw new Error(data.message || 'Registration failed');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: error.message || 'Something went wrong. Please try again later.'
                    });
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Sign up';
                }
            });
        });
    </script>
</body>
</html>