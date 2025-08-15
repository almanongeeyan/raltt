<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* CSS remains the same as before */
        /* ... your existing CSS ... */
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
            
            <!-- Form fields remain the same -->
            <!-- ... your existing form fields ... -->
            
            <button type="submit" class="btn-submit" id="signup-submit-btn" disabled>Sign up</button>
            
            <p class="login-link">Already have an account? <a href="user_login_form.php">Log In</a></p>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ... all your existing JavaScript functions remain the same ...
            // (goBack, togglePasswordVisibility, fetchAddress, getAndSetLocation, etc.)
            
            // Only change the form submission handler:
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