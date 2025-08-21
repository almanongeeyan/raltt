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
            min-height: 100vh;
            color: var(--text-color);
            position: relative;
        }

        .back-btn {
            position: absolute;
            top: 2rem;
            left: 2rem;
            background: #fff;
            border: 1px solid var(--input-border-color);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            color: var(--secondary-color);
            font-size: 1.2rem;
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
            padding: 40px;
            width: 100%;
            max-width: 450px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .signup-form {
            width: 100%;
            text-align: center;
        }

        .signup-form h2 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--text-color);
        }

        .signup-form p {
            font-size: 0.9rem;
            color: var(--light-text-color);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            text-align: left;
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 5px;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--input-border-color);
            border-radius: 8px;
            font-size: 1rem;
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
            right: 10px;
            background: none;
            border: none;
            color: var(--primary-color);
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            padding: 5px;
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
        }

        .input-group .locate-btn {
            width: 100%;
            background-color: var(--button-color);
            color: #fff;
            border: none;
            padding: 15px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
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
            font-size: 0.8rem;
            text-align: left;
            margin-top: 5px;
            min-height: 20px;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background-color: var(--button-color);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.2s ease;
            margin-top: 10px;
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
            font-size: 0.9rem;
            margin-top: 25px;
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
            margin-top: 10px;
        }

        #verification-form label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            font-weight: 500;
        }

        #verification-form .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--input-border-color);
            border-radius: 8px;
            font-size: 1rem;
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
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        #verification-form .btn-confirm:hover {
            background-color: #45a049;
        }

        #verify-status {
            text-align: left;
            font-size: 0.8rem;
            margin-top: 5px;
            min-height: 20px;
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
                    <input type="text" id="fullname" name="fullname" autocomplete="off" placeholder="Enter your full name" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <div class="input-group">
                    <input type="text" id="phone" name="phone" autocomplete="off" placeholder="(e.g., +639171234567)" pattern="\+639[0-9]{9}" title="Phone number must be in the format +639xxxxxxxxx" required>
                    <button type="button" class="verify-btn" id="send-code-btn">Verify</button>
                </div>
                <div id="verify-status"></div>
                <div id="verification-form">
                    <label for="verification_code">Verification Code</label>
                    <div class="input-group">
                        <input type="text" id="verification_code" name="verification_code" placeholder="Enter the 6-digit code" autocomplete="off" required>
                        <button type="button" class="btn-confirm" id="confirm-code-btn">Confirm</button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="house_address">House Address</label>
                <div class="input-group">
                    <input type="text" id="house_address" name="house_address" autocomplete="off" placeholder="Enter your house number, street, etc." required>
                </div>
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
                    <input type="password" id="password" autocomplete="off" name="password" placeholder="Enter your password" required>
                    <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePasswordVisibility('password')"></i>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="input-group">
                    <input type="password" id="confirm_password" autocomplete="off" name="confirm_password" placeholder="Confirm your password" required>
                    <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePasswordVisibility('confirm_password')"></i>
                </div>
                <div id="password-match-status" style="font-size: 0.8rem; text-align: left; margin-top: 5px;"></div>
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
            const passwordMatchStatus = document.getElementById('password-match-status');

            let isNumberVerified = false;
            
            // Enhanced form completion check
            function checkFormCompletion() {
                const requiredInputs = [
                    document.getElementById('fullname'),
                    document.getElementById('house_address'),
                    document.getElementById('address'),
                    passwordInput,
                    confirmPasswordInput
                ];
                
                const allFilled = requiredInputs.every(input => {
                    if (input.readOnly) {
                        return input.value.trim() !== '';
                    }
                    return !input.disabled && input.value.trim() !== '';
                });
                
                const passwordsMatch = passwordInput.value === confirmPasswordInput.value;
                
                signupSubmitBtn.disabled = !(allFilled && isNumberVerified && passwordsMatch);
            }

            // Phone verification
            sendCodeBtn.addEventListener('click', async () => {
                const phoneNumber = phoneInput.value.trim();
                if (!phoneNumber || !phoneInput.checkValidity()) {
                    verifyStatusDiv.style.color = 'red';
                    verifyStatusDiv.textContent = 'Please enter a valid phone number in format +639xxxxxxxxx';
                    return;
                }

                phoneInput.disabled = true;
                sendCodeBtn.disabled = true;
                sendCodeBtn.textContent = 'Sending...';
                verifyStatusDiv.textContent = 'Sending verification code...';
                verifyStatusDiv.style.color = 'blue';

                try {
                    const resp = await fetch('connection/send_verification.php', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: new URLSearchParams({ phone_number: phoneNumber })
                    });
                    const data = await resp.json();

                    if (data && data.success) {
                        verifyStatusDiv.textContent = 'Code sent! Check your phone.';
                        verifyStatusDiv.style.color = 'green';
                        verificationForm.style.display = 'block';
                        sendCodeBtn.textContent = 'Resend';
                        sendCodeBtn.disabled = false;
                    } else {
                        throw new Error((data && data.message) || 'Failed to send code.');
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
                const phoneNumber = phoneInput.value.trim();
                const verificationCode = verificationCodeInput.value.trim();
                
                if (!verificationCode || verificationCode.length !== 6 || !/^\d{6}$/.test(verificationCode)) {
                    verifyStatusDiv.style.color = 'red';
                    verifyStatusDiv.textContent = 'Please enter a valid 6-digit code.';
                    return;
                }

                confirmCodeBtn.disabled = true;
                confirmCodeBtn.textContent = 'Checking...';
                verifyStatusDiv.textContent = 'Checking code...';
                verifyStatusDiv.style.color = 'blue';

                try {
                    const resp = await fetch('connection/check_verification.php', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: new URLSearchParams({ phone_number: phoneNumber, verification_code: verificationCode })
                    });
                    const data = await resp.json();

                    if (data && data.success) {
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
                        throw new Error((data && data.message) || 'Invalid code. Please try again.');
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

            // Password matching validation
            confirmPasswordInput.addEventListener('input', function() {
                if (passwordInput.value !== this.value) {
                    passwordMatchStatus.style.color = 'red';
                    passwordMatchStatus.textContent = 'Passwords do not match';
                } else {
                    passwordMatchStatus.style.color = 'green';
                    passwordMatchStatus.textContent = 'Passwords match';
                }
                checkFormCompletion();
            });

            // Real-time form validation
            document.querySelectorAll('#signupForm input').forEach(input => {
                input.addEventListener('input', checkFormCompletion);
            });

            // Form submission - UPDATED TO ACTUALLY SUBMIT DATA
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