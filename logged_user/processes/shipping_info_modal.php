<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Information</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary: #7d310a;
            --secondary: #cf8756;
        }
            /* Responsive modal width and z-index fix */
            @media (max-width: 900px) {
                .modal-content {
                    width: 95vw !important;
                    min-width: 0 !important;
                    max-width: 98vw !important;
                    border-radius: 18px !important;
                    padding-left: 0.5rem !important;
                    padding-right: 0.5rem !important;
                }
            }
            @media (max-width: 600px) {
                .modal-content {
                    width: 99vw !important;
                    min-width: 0 !important;
                    max-width: 99vw !important;
                    border-radius: 12px !important;
                    padding-left: 0.2rem !important;
                    padding-right: 0.2rem !important;
                }
            }
        
        .primary {
            color: var(--primary);
        }
        
        .bg-primary {
            background-color: var(--primary);
        }
        
        .hover\:bg-primary:hover {
            background-color: var(--primary);
        }
        
        .border-primary {
            border-color: var(--primary);
        }
        
        .focus\:border-primary:focus {
            border-color: var(--primary);
        }
        
        .focus\:ring-primary:focus {
            --tw-ring-color: var(--primary);
        }
        
        .secondary {
            color: var(--secondary);
        }
        
        .bg-secondary {
            background-color: var(--secondary);
        }
        
        .hover\:bg-secondary:hover {
            background-color: var(--secondary);
        }
        
        .verified {
            color: #10b981;
        }
        
        .unverified {
            color: #ef4444;
        }
        
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .slide-up {
            animation: slideUp 0.3s ease-out;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .input-focus {
            transition: all 0.2s ease;
        }
        
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(125, 49, 10, 0.1);
        }
        
        .edit-icon {
            transition: background 0.2s, color 0.2s;
        }
        .edit-icon:hover {
            background: rgba(125,49,10,0.12);
            color: var(--primary);
            border-radius: 6px;
        }
        
        .change-number {
            font-size: 0.75rem;
            color: var(--primary);
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            border-radius: 6px;
        }
        .change-number:hover {
            background: rgba(125,49,10,0.12);
            color: var(--primary);
            text-decoration: underline;
        }
            #cancelBtn:hover {
                background-color: #e5e7eb;
                color: #7d310a;
            }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div id="updateSuccessNotif" class="fixed top-6 right-6 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg font-semibold text-sm transition-all duration-300 opacity-0 pointer-events-none" style="min-width:200px;">
        Shipping information updated successfully!
    </div>

    <!-- Success Notification -->
    <div id="updateSuccessNotif" class="fixed top-6 right-6 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg font-semibold text-sm transition-all duration-300 opacity-0 pointer-events-none" style="min-width:200px;">
        Updated successfully!
    </div>
    
    <!-- Shipping Information Modal -->
    <div id="shippingInfoModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-20 modal-overlay hidden">
    <div class="modal-content bg-white rounded-2xl shadow-2xl mx-2 relative border border-gray-200 fade-in" style="width:600px;min-width:600px;max-width:600px;max-height:90vh;overflow-y:auto;z-index:1100;">
            <div class="px-8 pt-10 pb-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center mr-4 shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-primary">Shipping Information</h2>
                        <p class="text-sm text-gray-500 mt-1">Please review and update your shipping details</p>
                    </div>
                </div>
                
                <form id="shippingInfoForm" class="space-y-6">
                    <!-- Full Name -->
                    <div class="space-y-2">
                        <label for="fullName" class="block text-sm font-semibold text-gray-700">Full Name</label>
                        <div class="relative">
                            <input type="text" id="fullName" name="fullName" value="<?php echo !empty($user_fullname) ? htmlspecialchars($user_fullname) : ''; ?>" class="input-focus mt-1 block w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm transition-all duration-200 text-gray-900 bg-white" required readonly>
                            <div id="nameCheck" class="absolute right-4 top-1/2 transform -translate-y-1/2 hidden">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <button type="button" id="editNameBtn" class="absolute right-12 top-1/2 transform -translate-y-1/2 text-gray-400 edit-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Contact Number -->
                    <div class="space-y-2">
                        <label for="contactNumber" class="block text-sm font-semibold text-gray-700">Contact Number</label>
                        <div class="flex gap-3 items-center mt-1">
                            <div class="relative flex-1">
                                <input type="text" id="contactNumber" name="contactNumber" value="<?php echo !empty($user_phone) ? htmlspecialchars($user_phone) : ''; ?>" class="input-focus block w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm transition-all duration-200 text-gray-900 bg-white" required readonly>
                                <div id="verifyStatus" class="absolute right-4 top-1/2 transform -translate-y-1/2 flex items-center"></div>
                                <div id="phoneFormatError" class="text-xs text-red-500 mt-1" style="display:none;"></div>
                                <span id="changeNumberText" class="absolute right-4 top-1/2 transform -translate-y-1/2 change-number"><?php echo empty($user_phone) ? 'Add Number' : 'Change Number'; ?></span>
                            </div>
                            <button type="button" id="verifyBtn" class="px-4 py-3 bg-primary text-white rounded-lg font-semibold shadow hover:bg-secondary transition whitespace-nowrap text-sm hidden">Verify</button>
                        </div>
                    </div>
                    
                    <!-- House Address -->
                    <div class="space-y-2">
                        <label for="houseAddress" class="block text-sm font-semibold text-gray-700">House Address</label>
                        <div class="relative">
                            <input type="text" id="houseAddress" name="houseAddress" value="<?php echo !empty($user_house_address) ? htmlspecialchars($user_house_address) : ''; ?>" class="input-focus mt-1 block w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm transition-all duration-200 text-gray-900 bg-white" required readonly>
                            <div id="addressCheck" class="absolute right-4 top-1/2 transform -translate-y-1/2 hidden">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <button type="button" id="editAddressBtn" class="absolute right-12 top-1/2 transform -translate-y-1/2 text-gray-400 edit-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Pin Location -->
                    <div class="space-y-2">
                        <label for="pinLocation" class="block text-sm font-semibold text-gray-700">Pin Point Location</label>
                        <div class="flex gap-3 items-center mt-1">
                            <div class="relative flex-1">
                                <input type="text" id="pinLocation" name="pinLocation" value="<?php echo !empty($user_full_address) ? htmlspecialchars($user_full_address) : ''; ?>" class="input-focus block w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm transition-all duration-200 text-gray-900 bg-white" required readonly>
                                <div id="locationCheck" class="absolute right-4 top-1/2 transform -translate-y-1/2 hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <button type="button" id="locateMeBtn" class="px-4 py-3 bg-secondary text-white rounded-lg font-semibold shadow hover:bg-primary transition whitespace-nowrap text-sm" onclick="locateMe()">
                                <span id="locateText">Locate Me</span>
                                <span id="locateSpinner" class="hidden animate-spin ml-1">⟳</span>
                            </button>
                        </div>
                        <div id="mapContainer" class="mt-3 rounded-lg overflow-hidden border border-gray-200" style="max-height:150px; min-height:0; transition:max-height 0.3s; display:none;"></div>
                    </div>
                    
                    <!-- Save Button -->
                    <div class="flex justify-end pt-4 gap-3">
                        <button type="button" id="cancelBtn" class="w-1/2 px-6 py-3 bg-gray-200 text-gray-600 rounded-lg font-semibold shadow transition-all duration-300 text-sm" onclick="closeShippingModal()">
                            Cancel
                        </button>
                        <button type="submit" id="saveBtn" class="w-1/2 px-6 py-3 bg-gray-300 text-gray-500 rounded-lg font-semibold shadow transition-all duration-300 text-sm" disabled>
                            Save Shipping Information
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Store original values for comparison
        let originalValues = {
            fullName: '',
            contactNumber: '',
            houseAddress: '',
            pinLocation: ''
        };
        
        // Track if phone number is verified
        let isNumberVerified = true;
        
        // Modal functions
        function openShippingModal() {
            document.getElementById('shippingInfoModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Always show edit icons/buttons when modal opens
            document.getElementById('editNameBtn').style.display = '';
            document.getElementById('editAddressBtn').style.display = '';
            document.getElementById('changeNumberText').style.display = '';
            // Make all fields readonly again
            document.getElementById('fullName').readOnly = true;
            document.getElementById('contactNumber').readOnly = true;
            document.getElementById('houseAddress').readOnly = true;

            // Store original values when modal opens
            originalValues.fullName = document.getElementById('fullName').value;
            originalValues.contactNumber = document.getElementById('contactNumber').value;
            originalValues.houseAddress = document.getElementById('houseAddress').value;
            originalValues.pinLocation = document.getElementById('pinLocation').value;

            validateForm();
        }
        
        function closeShippingModal() {
            document.getElementById('shippingInfoModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        // Edit button functionality
        document.getElementById('editNameBtn').addEventListener('click', function() {
            const input = document.getElementById('fullName');
            input.readOnly = false;
            input.focus();
            this.style.display = 'none';
            document.getElementById('nameCheck').classList.add('hidden');
            validateForm();
        });
        
        document.getElementById('changeNumberText').addEventListener('click', function() {
            const input = document.getElementById('contactNumber');
            input.readOnly = false;
            input.value = '';
            input.focus();
            this.style.display = 'none';
            document.getElementById('verifyBtn').classList.remove('hidden');
            isNumberVerified = false; // Reset verification when changing number
            validateForm();
        });
        
        document.getElementById('editAddressBtn').addEventListener('click', function() {
            const input = document.getElementById('houseAddress');
            input.readOnly = false;
            input.focus();
            this.style.display = 'none';
            document.getElementById('addressCheck').classList.add('hidden');
            validateForm();
        });
        
        // Check if form has changes
        function hasFormChanges() {
            const currentFullName = document.getElementById('fullName').value;
            const currentContactNumber = document.getElementById('contactNumber').value;
            const currentHouseAddress = document.getElementById('houseAddress').value;
            const currentPinLocation = document.getElementById('pinLocation').value;
            
            return currentFullName !== originalValues.fullName ||
                   currentContactNumber !== originalValues.contactNumber ||
                   currentHouseAddress !== originalValues.houseAddress ||
                   currentPinLocation !== originalValues.pinLocation;
        }
        
        // Twilio Verification Flow
        let verifiedPhone = '';
        let verificationForm = null;
        let verificationCodeInput = null;
        let confirmCodeBtn = null;
        let codeInputVisible = false;

        function showVerificationForm() {
            if (!verificationForm) {
                verificationForm = document.createElement('div');
                verificationForm.id = 'verificationForm';
                verificationForm.style.marginTop = '12px';
                verificationForm.classList.add('slide-up');
                verificationForm.innerHTML = `
                    <label for="verification_code" class="block text-xs font-semibold text-gray-700 mb-1">Verification Code</label>
                    <div class="flex flex-col gap-2 items-stretch w-full">
                        <input type="text" id="verification_code" name="verification_code" maxlength="6" placeholder="Enter the 6-digit code" class="input-focus block w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm text-gray-900 bg-white" required style="min-width:0;">
                        <button type="button" id="confirmCodeBtn" class="px-4 py-3 bg-green-500 text-white rounded-lg font-semibold shadow hover:bg-green-600 transition text-sm w-full">Confirm</button>
                    </div>
                    <div id="codeStatus" class="text-xs mt-2"></div>
                `;
                // Place verificationForm below the contact number field
                const contactNumberDiv = document.getElementById('contactNumber').closest('.flex');
                contactNumberDiv.parentNode.insertBefore(verificationForm, contactNumberDiv.nextSibling);
                verificationCodeInput = document.getElementById('verification_code');
                confirmCodeBtn = document.getElementById('confirmCodeBtn');
                confirmCodeBtn.addEventListener('click', confirmVerificationCode);
                startResendTimer();
            } else {
                verificationForm.style.display = 'block';
            }
            codeInputVisible = true;
        }

        async function verifyContactNumberTwilio() {
            const number = document.getElementById('contactNumber').value;
            const verifyBtn = document.getElementById('verifyBtn');
            const verifyStatus = document.getElementById('verifyStatus');
            const phoneFormatError = document.getElementById('phoneFormatError');

            // Reset error
            phoneFormatError.style.display = 'none';
            phoneFormatError.textContent = '';
            verifyStatus.textContent = '';

            if (!number.match(/^\+639\d{9}$/)) {
                phoneFormatError.textContent = 'Invalid phone format. Use +639xxxxxxxxx.';
                phoneFormatError.style.display = 'block';
                return;
            }

            verifyBtn.disabled = true;
            verifyBtn.textContent = 'Sending...';
            verifyStatus.textContent = '';
            
            let codeStatusDiv = document.getElementById('codeStatus');
            if (codeStatusDiv) {
                codeStatusDiv.textContent = 'Checking number and sending code...';
                codeStatusDiv.style.color = 'blue';
            }

            try {
                // Check if number is already registered
                const checkResponse = await fetch('/raltt/connection/check_phone_registered.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ phone: number })
                });
                let checkData;
                let checkText = await checkResponse.text();
                try {
                    checkData = JSON.parse(checkText);
                } catch (jsonErr) {
                    verifyStatus.textContent = 'Server error. Please try again.\n' + checkText;
                    verifyStatus.style.color = 'red';
                    verifyBtn.disabled = false;
                    verifyBtn.textContent = 'Verify';
                    return;
                }
                if (checkData.status === 'registered') {
                    verifyStatus.textContent = 'This phone number is already registered.';
                    verifyStatus.style.color = 'red';
                    verifyBtn.disabled = false;
                    verifyBtn.textContent = 'Verify';
                    return;
                }

                // Send Twilio verification code
                const formData = new FormData();
                formData.append('phone', number);
                const response = await fetch('/raltt/connection/send_verification_debug.php', {
                    method: 'POST',
                    body: formData
                });
                let data;
                let respText = await response.text();
                try {
                    data = JSON.parse(respText);
                } catch (jsonErr) {
                    verifyStatus.textContent = 'Server error. Please try again.\n' + respText;
                    verifyStatus.style.color = 'red';
                    verifyBtn.disabled = false;
                    verifyBtn.textContent = 'Verify';
                    return;
                }
                if (data.status === 'success') {
                    verifyStatus.textContent = '';
                    verifyBtn.textContent = 'Resend';
                    verifiedPhone = number;
                    showVerificationForm();
                    let codeStatusDiv = document.getElementById('codeStatus');
                    if (codeStatusDiv) {
                        codeStatusDiv.textContent = 'Code sent! Please check your SMS and enter the code.';
                        codeStatusDiv.style.color = 'green';
                    }
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                verifyStatus.textContent = error.message || 'Failed to send code.';
                verifyStatus.style.color = 'red';
                verifyBtn.disabled = false;
                verifyBtn.textContent = 'Verify';
            }
        }

        async function confirmVerificationCode() {
            const verifyStatus = document.getElementById('verifyStatus');
            const code = verificationCodeInput.value;
            if (!code || code.length !== 6) {
                verifyStatus.textContent = 'Please enter a valid 6-digit code.';
                verifyStatus.style.color = 'red';
                return;
            }
            confirmCodeBtn.disabled = true;
            confirmCodeBtn.textContent = 'Checking...';
            verifyStatus.textContent = '';
            let codeStatusDiv = document.getElementById('codeStatus');
            if (codeStatusDiv) {
                codeStatusDiv.textContent = 'Checking code...';
                codeStatusDiv.style.color = 'blue';
            }
            try {
                const formData = new FormData();
                formData.append('phone', verifiedPhone);
                formData.append('code', code);
                const response = await fetch('/raltt/connection/check_verification.php', {
                    method: 'POST',
                    body: formData
                });
                let data;
                let respText = await response.text();
                try {
                    data = JSON.parse(respText);
                } catch (jsonErr) {
                    verifyStatus.textContent = 'Server error. Please try again.\n' + respText;
                    verifyStatus.style.color = 'red';
                    confirmCodeBtn.disabled = false;
                    confirmCodeBtn.textContent = 'Confirm';
                    return;
                }
                if (data.status === 'success') {
                    verifyStatus.textContent = '';
                    confirmCodeBtn.textContent = 'Verified';
                    confirmCodeBtn.classList.add('bg-green-500');
                    isNumberVerified = true;
                    document.getElementById('contactNumber').disabled = true;
                    verificationCodeInput.disabled = true;
                    document.getElementById('verifyBtn').disabled = true;
                    let codeStatusDiv = document.getElementById('codeStatus');
                    if (codeStatusDiv) {
                        codeStatusDiv.textContent = 'Phone number verified!';
                        codeStatusDiv.style.color = 'green';
                    }
                    validateForm(); // Enable save button if all valid
                } else {
                    // Show incorrect code message and allow retry
                    let codeStatusDiv = document.getElementById('codeStatus');
                    if (codeStatusDiv) {
                        codeStatusDiv.textContent = 'Wrong verification code, please try again';
                        codeStatusDiv.style.color = 'red';
                    }
                    confirmCodeBtn.disabled = false;
                    confirmCodeBtn.textContent = 'Confirm';
                    verificationCodeInput.disabled = false;
                    // Ensure verificationForm stays visible
                    if (verificationForm) verificationForm.style.display = 'block';
                }
                } catch (error) {
                    let codeStatusDiv = document.getElementById('codeStatus');
                    if (codeStatusDiv) {
                        codeStatusDiv.textContent = error.message || 'Invalid code.';
                        codeStatusDiv.style.color = 'red';
                    }
                    confirmCodeBtn.disabled = false;
                    confirmCodeBtn.textContent = 'Confirm';
                }
        }
        
        async function locateMe() {
            const btn = document.getElementById('locateMeBtn');
            const locateText = document.getElementById('locateText');
            const locateSpinner = document.getElementById('locateSpinner');
            const mapContainer = document.getElementById('mapContainer');
            btn.disabled = true;
            btn.classList.remove('bg-secondary', 'hover:bg-primary');
            btn.classList.add('bg-gray-400', 'hover:bg-gray-500', 'opacity-70');
            locateText.classList.add('hidden');
            locateSpinner.classList.remove('hidden');
            // Only allow one click: do not re-enable after completion or error
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(async function(pos) {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    try {
                        const response = await fetch(`/raltt/connection/reverse_geocode.php?lat=${lat}&lng=${lng}`);
                        const data = await response.json();
                        if (data.address) {
                            // Compose a more accurate address using all available parts
                            const addressParts = [
                                data.address.house_number,
                                data.address.road,
                                data.address.neighbourhood,
                                data.address.suburb,
                                data.address.barangay,
                                data.address.village,
                                data.address.municipality,
                                data.address.town,
                                data.address.city_district,
                                data.address.city,
                                data.address.state_district,
                                data.address.state,
                                data.address.region,
                                data.address.postcode,
                                data.address.country
                            ].filter(Boolean);
                            let fullAddress = addressParts.join(', ');
                            document.getElementById('pinLocation').value = fullAddress;
                        } else if (data.display_name) {
                            document.getElementById('pinLocation').value = data.display_name;
                        } else {
                            document.getElementById('pinLocation').value = '';
                        }
                    } catch (err) {
                        document.getElementById('pinLocation').value = '';
                    }
                    const mapUrl = `https://maps.google.com/maps?q=${lat},${lng}&z=15&output=embed`;
                    mapContainer.innerHTML = `<iframe width='100%' height='150' src='${mapUrl}' frameborder='0' style='border:0;'></iframe>`;
                    mapContainer.style.display = 'block';
                    locateText.classList.remove('hidden');
                    locateSpinner.classList.add('hidden');
                    validateForm();
                }, function(error) {
                    alert('Unable to retrieve your location. Please try again or enter manually.');
                    locateText.classList.remove('hidden');
                    locateSpinner.classList.add('hidden');
                    mapContainer.style.display = 'none';
                });
            } else {
                alert('Geolocation is not supported by your browser.');
                locateText.classList.remove('hidden');
                locateSpinner.classList.add('hidden');
                mapContainer.style.display = 'none';
            }
        }
        
        // Form validation
        function validateForm() {
            const fullName = document.getElementById('fullName').value.trim();
            const contactNumber = document.getElementById('contactNumber').value.trim();
            const houseAddress = document.getElementById('houseAddress').value.trim();
            const pinLocation = document.getElementById('pinLocation').value.trim();
            const saveBtn = document.getElementById('saveBtn');
            const nameCheck = document.getElementById('nameCheck');
            const addressCheck = document.getElementById('addressCheck');
            const locationCheck = document.getElementById('locationCheck');
            
            // Show/hide checkmarks
            nameCheck.classList.toggle('hidden', fullName === '');
            addressCheck.classList.toggle('hidden', houseAddress === '');
            locationCheck.classList.toggle('hidden', pinLocation === '');
            
            // Check if all fields are filled and number is verified
            const isFormValid = fullName !== '' && contactNumber !== '' && houseAddress !== '' && pinLocation !== '' && isNumberVerified;
            
            // Check if there are changes to the form
            const hasChanges = hasFormChanges();
            
            // Update save button state
            if (isFormValid && hasChanges) {
                saveBtn.disabled = false;
                saveBtn.classList.remove('bg-gray-300', 'text-gray-500');
                saveBtn.classList.add('bg-primary', 'text-white', 'hover:bg-secondary');
            } else {
                saveBtn.disabled = true;
                saveBtn.classList.remove('bg-primary', 'text-white', 'hover:bg-secondary');
                saveBtn.classList.add('bg-gray-300', 'text-gray-500');
            }
        }
        
        // Form submission
        document.getElementById('shippingInfoForm').onsubmit = function(e) {
            e.preventDefault();
            // Collect all form fields
            const fullName = document.getElementById('fullName').value;
            const contactNumber = document.getElementById('contactNumber').value;
            const houseAddress = document.getElementById('houseAddress').value;
            const pinLocation = document.getElementById('pinLocation').value;
            // Send to PHP via AJAX
            fetch('/raltt/connection/save_shipping_info.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `fullName=${encodeURIComponent(fullName)}&contactNumber=${encodeURIComponent(contactNumber)}&houseAddress=${encodeURIComponent(houseAddress)}&pinLocation=${encodeURIComponent(pinLocation)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showUpdateSuccess();
                    // Update original values after successful save
                    originalValues.fullName = fullName;
                    originalValues.contactNumber = contactNumber;
                    originalValues.houseAddress = houseAddress;
                    originalValues.pinLocation = pinLocation;
                    // Update displayed shipping info if present on page
                    if (document.getElementById('displayFullName')) {
                        document.getElementById('displayFullName').textContent = fullName;
                    }
                    if (document.getElementById('displayContactNumber')) {
                        document.getElementById('displayContactNumber').textContent = contactNumber;
                    }
                    if (document.getElementById('displayHouseAddress')) {
                        document.getElementById('displayHouseAddress').textContent = houseAddress;
                    }
                    if (document.getElementById('displayPinLocation')) {
                        document.getElementById('displayPinLocation').textContent = pinLocation;
                    }
                    // Send message to parent window for real-time update
                    if (window.parent) {
                        window.parent.postMessage({
                            type: 'shippingInfoUpdated',
                            message: 'Shipping information updated successfully!',
                            info: {
                                fullName: fullName,
                                contactNumber: contactNumber,
                                houseAddress: houseAddress,
                                fullAddress: pinLocation
                            }
                        }, '*');
                    }
                    setTimeout(() => {
                        closeShippingModal();
                    }, 1800);
                } else {
                    alert('Error saving shipping info: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(() => {
                alert('Network error. Please try again.');
            });
        };

        // Show success notification
        function showUpdateSuccess() {
            const notif = document.getElementById('updateSuccessNotif');
            notif.style.opacity = '1';
            notif.style.pointerEvents = 'auto';
            setTimeout(() => {
                notif.style.opacity = '0';
                notif.style.pointerEvents = 'none';
            }, 1800);
        };
        
        // Validate form on input changes
        document.getElementById('fullName').addEventListener('input', validateForm);
        document.getElementById('contactNumber').addEventListener('input', function() {
            // Hide phone format error on input
            document.getElementById('phoneFormatError').style.display = 'none';
            validateForm();
        });
        document.getElementById('houseAddress').addEventListener('input', validateForm);
        document.getElementById('pinLocation').addEventListener('input', validateForm);

        // Attach Twilio verify logic to button
        document.getElementById('verifyBtn').addEventListener('click', verifyContactNumberTwilio);

        // Resend timer logic
        let resendTimer = null;
        function startResendTimer() {
            const verifyBtn = document.getElementById('verifyBtn');
            let timeLeft = 90; // 1 minute 30 seconds
            verifyBtn.disabled = true;
            verifyBtn.classList.remove('bg-primary', 'hover:bg-secondary');
            verifyBtn.classList.add('bg-yellow-500', 'hover:bg-yellow-600');
            updateResendBtnText(timeLeft);
            if (resendTimer) clearInterval(resendTimer);
            resendTimer = setInterval(() => {
                timeLeft--;
                updateResendBtnText(timeLeft);
                if (timeLeft <= 0) {
                    clearInterval(resendTimer);
                    verifyBtn.textContent = 'Resend';
                    verifyBtn.disabled = false;
                    verifyBtn.classList.remove('bg-yellow-500', 'hover:bg-yellow-600');
                    verifyBtn.classList.add('bg-primary', 'hover:bg-secondary');
                }
            }, 1000);
        }
        
        function updateResendBtnText(timeLeft) {
            const verifyBtn = document.getElementById('verifyBtn');
            const min = Math.floor(timeLeft / 60);
            const sec = timeLeft % 60;
            verifyBtn.textContent = `Resend (${min}:${sec.toString().padStart(2, '0')})`;
        }
        
        // Initialize the modal when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial values only
            originalValues.fullName = document.getElementById('fullName').value;
            originalValues.contactNumber = document.getElementById('contactNumber').value;
            originalValues.houseAddress = document.getElementById('houseAddress').value;
            originalValues.pinLocation = document.getElementById('pinLocation').value;
            // Modal will only open when openShippingModal() is called explicitly
        });
    </script>
</body>
</html>