<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
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

        .input-group input:focus {
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
            min-height: 20px; /* Prevents layout shift */
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

        .btn-submit:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
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
    </style>
</head>
<body>
    <button class="back-btn" onclick="goBack()" aria-label="Go back">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    
    <div class="signup-container">
        <form class="signup-form" action="your_server_endpoint.php" method="POST">
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
                    <input type="number" id="phone" name="phone" autocomplete="off" placeholder="Enter your number (09)" required>
                    <button type="button" class="verify-btn">Verify number</button>
                </div>
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <div class="input-group">
                    <input type="text" id="address" name="address" placeholder="Tap 'Locate Me' to get your current location" readonly required>
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
            </div>
            
            <button type="submit" class="btn-submit">Sign up</button>
            
            <p class="login-link">Already have an account? <a href="user_login_form.php">Log In</a></p>
        </form>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }

        function togglePasswordVisibility(id) {
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
        }

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
                            
                            // Successfully found location, finalize button state
                            locateBtn.textContent = 'Location Set';
                            locateBtn.style.backgroundColor = '#4CAF50'; // Green color for success
                            locateBtn.style.cursor = 'not-allowed';
                            // The button will remain disabled and styled as "Location Set"

                        } catch (error) {
                            locationInfo.style.color = 'red';
                            locationInfo.textContent = error.message;
                            addressInput.value = '';
                            
                            // Re-enable button on failure
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
                        
                        // Re-enable button on failure
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
                locateBtn.disabled = true; // No point in trying again if not supported
                locateBtn.textContent = 'Not Supported';
            }
        }
        
        // Add event listener to the button
        document.querySelector('.locate-btn').addEventListener('click', getAndSetLocation);
    </script>
</body>
</html>