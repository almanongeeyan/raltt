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
            transition: all 0.2s ease-in-out;
        }
        .edit-icon:hover {
            background: rgba(125,49,10,0.12);
            color: var(--primary);
            transform: scale(1.05);
        }
        
        select:not([disabled]),
        input:not([disabled]) {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        
        select:not([disabled]):hover,
        input:not([disabled]):hover {
            border-color: var(--primary);
            background-color: rgba(255, 255, 255, 0.9);
        }
        
        .address-group {
            transition: all 0.3s ease-in-out;
        }
        
        .address-group:hover {
            transform: translateY(-1px);
        }
        
        .address-input-group {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .address-input-group::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .address-input-group:focus-within::after {
            transform: scaleX(1);
        }        .change-number {
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
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <label for="fullName" class="text-sm font-semibold text-gray-700">Full Name</label>
                            </div>
                            <button type="button" id="editNameBtn" class="text-primary hover:bg-primary/10 rounded-lg p-2 transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                        </div>
                        <div class="relative address-input-group">
                            <input type="text" id="fullName" name="fullName" value="<?php echo !empty($user_fullname) ? htmlspecialchars($user_fullname) : ''; ?>" class="input-focus block w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3.5 text-sm transition-all duration-200 text-gray-900 bg-white/50" required readonly placeholder="Enter your full name">
                            <div id="nameCheck" class="absolute right-4 top-1/2 transform -translate-y-1/2 hidden">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Number -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <label for="contactNumber" class="text-sm font-semibold text-gray-700">Contact Number</label>
                            </div>
                            <span id="changeNumberText" class="text-primary text-sm hover:bg-primary/10 rounded-lg px-3 py-1.5 transition-all duration-200 cursor-pointer"><?php echo empty($user_phone) ? 'Add Number' : 'Change Number'; ?></span>
                        </div>
                        <div class="flex gap-3 items-center">
                            <div class="relative flex-1 address-input-group">
                                <input type="text" id="contactNumber" name="contactNumber" value="<?php echo !empty($user_phone) ? htmlspecialchars($user_phone) : ''; ?>" class="input-focus block w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3.5 text-sm transition-all duration-200 text-gray-900 bg-white/50" required readonly placeholder="+63 9XX XXX XXXX">
                                <div id="verifyStatus" class="absolute right-4 top-1/2 transform -translate-y-1/2 flex items-center"></div>
                            </div>
                            <button type="button" id="verifyBtn" class="px-5 py-3.5 bg-primary text-white rounded-lg font-semibold shadow-md hover:bg-secondary transition-all duration-300 whitespace-nowrap text-sm hidden hover:scale-105 active:scale-95">
                                Verify
                            </button>
                        </div>
                        <div id="phoneFormatError" class="text-xs text-red-500 mt-1 ml-1" style="display:none;"></div>
                    </div>
                    
                    <!-- House Address -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <label for="houseAddress" class="text-sm font-semibold text-gray-700">Delivery Address</label>
                            </div>
                            <button type="button" id="editAddressBtn" class="text-primary hover:bg-primary/10 rounded-lg p-2 transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                        </div>

                        <div>
                            <input type="text" id="houseAddressDisplay" name="houseAddressDisplay" value="<?php echo !empty($user_house_address) ? htmlspecialchars($user_house_address) : ''; ?>" class="input-focus block w-full rounded-lg border border-gray-300 shadow-sm px-4 py-3.5 text-sm text-gray-900 bg-white/50" readonly placeholder="Click 'Edit' to set your delivery address">
                        </div>

                        <div id="detailedAddressContainer" class="hidden space-y-4">
                            <div class="bg-gray-50/80 rounded-lg p-4 space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Region</label>
                                        <select id="regionSelect" class="input-focus text-black block w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-3 py-2.5 text-sm bg-white transition-all" required disabled>
                                            <option value="">Select Region</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Province</label>
                                        <select id="provinceSelect" class="input-focus text-black block w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-3 py-2.5 text-sm bg-white transition-all" required disabled>
                                            <option value="">Select Province</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1.5">City / Municipality</label>
                                        <select id="citySelect" class="input-focus text-black block w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-3 py-2.5 text-sm bg-white transition-all" required disabled>
                                            <option value="">Select City / Municipality</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Barangay</label>
                                        <select id="barangaySelect" class="input-focus text-black block w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-3 py-2.5 text-sm bg-white transition-all" required disabled>
                                            <option value="">Select Barangay</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <div class="mt-1 mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <label for="houseNumber" class="block text-xs font-medium text-gray-500 mb-1.5">Complete Address</label>
                                        <input id="houseNumber" name="houseNumber" type="text" placeholder="House/Unit #, Building Name, Street Name" class="input-focus block w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm transition-all duration-200 text-gray-900 bg-white" disabled>
                                        <p class="text-xs text-gray-500 mt-1">Example: Unit 1234, Green Building, 123 Maple Street</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pin Location -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <label for="pinLocation" class="text-sm font-semibold text-gray-700">Pin Point Location</label>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex gap-3 items-start">
                                <div class="relative flex-1 address-input-group">
                                    <input type="text" id="pinLocation" name="pinLocation" value="<?php echo !empty($user_full_address) ? htmlspecialchars($user_full_address) : ''; ?>" class="input-focus block w-full rounded-lg border border-gray-300 focus:border-primary focus:ring-primary shadow-sm px-4 py-3.5 text-sm transition-all duration-200 text-gray-900 bg-white/50" required readonly placeholder="Click 'Locate Me' to set your precise location">
                                    <div id="locationCheck" class="absolute right-4 top-1/2 transform -translate-y-1/2 hidden">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <button type="button" id="locateMeBtn" class="px-5 py-3.5 bg-secondary text-white rounded-lg font-semibold shadow-md hover:bg-primary transition-all duration-300 whitespace-nowrap text-sm flex items-center space-x-2 hover:scale-105 active:scale-95" onclick="locateMe()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span id="locateText">Locate Me</span>
                                    <span id="locateSpinner" class="hidden animate-spin">⟳</span>
                                </button>
                            </div>
                            <div id="mapContainer" class="rounded-xl overflow-hidden border border-gray-200 shadow-sm transition-all duration-300" style="max-height:150px; min-height:0; display:none;"></div>
                        </div>
                    </div>
                    
                    <!-- Save Button -->
                    <div class="flex justify-end pt-6 gap-4">
                        <button type="button" id="cancelBtn" class="w-1/2 px-6 py-3.5 bg-gray-100 text-gray-600 rounded-xl font-semibold shadow-sm hover:bg-gray-200 active:bg-gray-300 transition-all duration-300 text-sm flex justify-center items-center space-x-2" onclick="closeShippingModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Cancel</span>
                        </button>
                        <button type="submit" id="saveBtn" class="w-1/2 px-6 py-3.5 rounded-xl font-semibold shadow-md text-sm transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] flex justify-center items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 bg-gray-300 text-gray-500 disabled:bg-gray-200" disabled>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // --- Address dropdown data & helpers ---
        // Default NCR location data (will be overridden by JSON file if available)
        const addressData = {
            "NCR": {
                "Metro Manila (National Capital Region)": {
                    "Caloocan": [
                        "Bagumbong", "Bagong Silang", "Camarin", "Deparo", "Llano", 
                        "Pangarap Village", "Tala", "Grace Park North", "Grace Park East"
                    ],
                    "Quezon City": [
                        "Baesa", "Bagbag", "Fairview", "Greater Lagro", "Kaligayahan", 
                        "Nagkaisang Nayon", "North Fairview", "Novaliches Proper", "Pasong Putik", 
                        "San Agustin", "San Bartolome", "Santa Lucia", "Santa Monica", "Talipapa"
                    ],
                    "Valenzuela": [
                        "Arkong Bato", "Dalandanan", "Gen. T. de Leon", "Karuhatan", 
                        "Malinta", "Marulas", "Maysan", "Pariancillo Villa", "Paso de Blas", "Polo"
                    ],
                    "Manila": [
                        "Gagalangin", "Tondo I", "Tondo II"
                    ],
                    "Malabon": [
                        "Catmon", "Concepcion", "Flores", "Longos", "Tugatog"
                    ]
                }
            }
        };

        function populateRegions() {
            const regionSel = document.getElementById('regionSelect');
            regionSel.innerHTML = '<option value="NCR">NCR</option>';
            regionSel.value = 'NCR';
            regionSel.disabled = true; // NCR is the only option, so disable selection
            populateProvinces('NCR');
        }

        function populateProvinces(region) {
            const provSel = document.getElementById('provinceSelect');
            provSel.innerHTML = '<option value="">Select Province</option>';
            if (!region || !addressData[region]) return;
            Object.keys(addressData[region]).forEach(p => {
                const opt = document.createElement('option'); opt.value = p; opt.textContent = p; provSel.appendChild(opt);
            });
        }

        function populateCities(region, province) {
            const citySel = document.getElementById('citySelect');
            citySel.innerHTML = '<option value="">Select City / Municipality</option>';
            if (!region || !province || !addressData[region] || !addressData[region][province]) return;
            Object.keys(addressData[region][province]).forEach(c => {
                const opt = document.createElement('option'); opt.value = c; opt.textContent = c; citySel.appendChild(opt);
            });
        }

        function populateBarangays(region, province, city) {
            const barangaySel = document.getElementById('barangaySelect');
            barangaySel.innerHTML = '<option value="">Select Barangay</option>';
            if (!region || !province || !city) return;
            const node = addressData[region] && addressData[region][province] && addressData[region][province][city];
            if (!node) return;
            // Support two shapes: array of barangay names OR object where keys are barangay names
            if (Array.isArray(node)) {
                node.forEach(b => {
                    const opt = document.createElement('option'); opt.value = b; opt.textContent = b; barangaySel.appendChild(opt);
                });
            } else if (typeof node === 'object') {
                Object.keys(node).forEach(b => {
                    const opt = document.createElement('option'); opt.value = b; opt.textContent = b; barangaySel.appendChild(opt);
                });
            }
        }

        // --- Form state & validation ---
        let originalValues = {
            fullName: '',
            contactNumber: '',
            region: '',
            province: '',
            city: '',
            barangay: '',
            houseNumber: '',
            pinLocation: ''
        };

        // Track if phone number is verified
        let isNumberVerified = true;

        // Enable address editing (make selects editable)
        function enableAddressEditing() {
            // Region stays as NCR (readonly) — enable downstream selects
            // document.getElementById('regionSelect').disabled = false;
            document.getElementById('provinceSelect').disabled = false;
            document.getElementById('citySelect').disabled = false;
            document.getElementById('barangaySelect').disabled = false;

            // Enable the house/unit/street textbox when editing address
            const houseInput = document.getElementById('houseNumber');
            if (houseInput) houseInput.disabled = false;

            // hide display textbox and show detailed selects container
            const display = document.getElementById('houseAddressDisplay');
            const detailed = document.getElementById('detailedAddressContainer');
            if (display) {
                display.classList.add('hidden');
                display.style.display = 'none';
            }
            if (detailed) {
                detailed.classList.remove('hidden');
                detailed.style.display = 'block';
            }

            // populate provinces/cities/barangays and try to preselect saved values
            const region = 'NCR';
            populateProvinces(region);
            const provSel = document.getElementById('provinceSelect');
            const citySel = document.getElementById('citySelect');
            const barangaySel = document.getElementById('barangaySelect');
            // If user had previous selections, attempt to restore them
            try {
                if (originalValues.province) {
                    provSel.value = originalValues.province;
                }
                if (provSel.value) {
                    populateCities(region, provSel.value);
                    if (originalValues.city) citySel.value = originalValues.city;
                }
                if (citySel.value) {
                    populateBarangays(region, provSel.value, citySel.value);
                    if (originalValues.barangay) barangaySel.value = originalValues.barangay;
                }
                // Populate the house number textbox with a previous value if present
                if (originalValues.houseNumber) {
                    const houseInput = document.getElementById('houseNumber');
                    if (houseInput) houseInput.value = originalValues.houseNumber;
                }
            } catch (e) {
                // ignore any preselect errors
            }
            document.getElementById('editAddressBtn').style.display = 'none';
            // addressCheck removed; UI will display house number textbox instead
            validateForm();
        }

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
            document.getElementById('regionSelect').disabled = true;
            document.getElementById('provinceSelect').disabled = true;
            document.getElementById('citySelect').disabled = true;
            document.getElementById('barangaySelect').disabled = true;
            // Ensure house number textbox is disabled until edit is clicked
            const houseInput = document.getElementById('houseNumber');
            if (houseInput) houseInput.disabled = true;

            // Ensure the readonly house address display is visible and detailed selects hidden
            const display = document.getElementById('houseAddressDisplay');
            const detailed = document.getElementById('detailedAddressContainer');
            if (display) {
                display.classList.remove('hidden');
                display.style.display = '';
            }
            if (detailed) {
                detailed.classList.add('hidden');
                detailed.style.display = 'none';
            }

            // Store original values when modal opens
            originalValues.fullName = document.getElementById('fullName').value;
            originalValues.contactNumber = document.getElementById('contactNumber').value;
            originalValues.region = document.getElementById('regionSelect').value || '';
            originalValues.province = document.getElementById('provinceSelect').value || '';
            originalValues.city = document.getElementById('citySelect').value || '';
            originalValues.barangay = document.getElementById('barangaySelect').value || '';
            originalValues.houseNumber = document.getElementById('houseNumber') ? document.getElementById('houseNumber').value || '' : '';
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
            // enable selects and street input
            // Populate selects based on current saved address data and then enable
            // Ensure regions/provinces/cities are populated before showing detailed selects
            populateRegions();
            const currentCity = document.getElementById('citySelect').value;
            const currentBarangay = document.getElementById('barangaySelect').value;
            // If no city/barangay selected but user has saved house address, attempt to pre-select by parsing
            enableAddressEditing();
        });

        // Check if form has changes (compare component-wise)
          function hasFormChanges() {
            const currentFullName = document.getElementById('fullName').value;
            const currentContactNumber = document.getElementById('contactNumber').value;
            const currentRegion = document.getElementById('regionSelect').value || '';
            const currentProvince = document.getElementById('provinceSelect').value || '';
            const currentCity = document.getElementById('citySelect').value || '';
            const currentBarangay = document.getElementById('barangaySelect').value || '';
            const currentHouse = document.getElementById('houseNumber') ? document.getElementById('houseNumber').value || '' : '';
            const currentPin = document.getElementById('pinLocation').value || '';

                return currentFullName !== originalValues.fullName ||
                         currentContactNumber !== originalValues.contactNumber ||
                         currentRegion !== originalValues.region ||
                         currentProvince !== originalValues.province ||
                         currentCity !== originalValues.city ||
                         currentBarangay !== originalValues.barangay ||
                         currentHouse !== originalValues.houseNumber ||
                         currentPin !== originalValues.pinLocation;
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
                const response = await fetch('/raltt/connection/send_verification.php', {
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
            const region = document.getElementById('regionSelect').value || '';
            const province = document.getElementById('provinceSelect').value || '';
            const city = document.getElementById('citySelect').value || '';
            const barangay = document.getElementById('barangaySelect').value || '';
            const pinLocation = document.getElementById('pinLocation').value.trim();
            const saveBtn = document.getElementById('saveBtn');
            const nameCheck = document.getElementById('nameCheck');
            const locationCheck = document.getElementById('locationCheck');

            // Use free-text house number / street field instead of complex detail dropdown
            const houseNumber = document.getElementById('houseNumber') ? document.getElementById('houseNumber').value.trim() : '';

            // Combined human-readable house address
            const combinedAddress = (houseNumber ? houseNumber : '') + (barangay ? (', ' + barangay) : '') + (city ? (', ' + city) : '') + (province ? (', ' + province) : '') + (region ? (', ' + region) : '');

            // Show/hide checkmarks only when values have changed
            if (nameCheck) {
                const hasNameChange = fullName !== originalValues.fullName;
                nameCheck.classList.toggle('hidden', !hasNameChange);
            }
            if (locationCheck) {
                const hasLocationChange = pinLocation !== originalValues.pinLocation;
                locationCheck.classList.toggle('hidden', !hasLocationChange);
            }

            // Check if the house number has a value
            const hasDetailedAddress = !!houseNumber;

        // Check if all required fields are filled and number is verified
        const isFormValid = fullName !== '' && 
                          contactNumber !== '' && 
                          region !== '' && 
                          province !== '' && 
                          city !== '' && 
                          barangay !== '' && 
                          hasDetailedAddress && 
                          pinLocation !== '' && 
                          isNumberVerified;

            // Check if there are changes to the form
        const hasChanges = hasFormChanges();

            // Update save button state: enable when any change exists
            if (hasChanges) {
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
            const region = document.getElementById('regionSelect').value || '';
            const province = document.getElementById('provinceSelect').value || '';
            const city = document.getElementById('citySelect').value || '';
            const barangay = document.getElementById('barangaySelect').value || '';
            const pinLocation = document.getElementById('pinLocation').value;

            // Collect house number / street free-text
            const houseNumber = document.getElementById('houseNumber') ? document.getElementById('houseNumber').value.trim() : '';
            const detailedAddress = houseNumber || '';

            // Combine all address components
            const combinedHouseAddress = [
                detailedAddress,
                barangay,
                city,
                province,
                region
            ].filter(Boolean).join(', ');

            // Send to PHP via AJAX (send both combined and components for future server-side use)
            const body = `fullName=${encodeURIComponent(fullName)}&contactNumber=${encodeURIComponent(contactNumber)}&houseAddress=${encodeURIComponent(combinedHouseAddress)}&pinLocation=${encodeURIComponent(pinLocation)}` +
                         `&region=${encodeURIComponent(region)}&province=${encodeURIComponent(province)}&city=${encodeURIComponent(city)}&barangay=${encodeURIComponent(barangay)}` +
                         `&detailedAddress=${encodeURIComponent(detailedAddress)}`;

            fetch('/raltt/connection/save_shipping_info.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showUpdateSuccess();
                    // Update original values after successful save
                    originalValues.fullName = fullName;
                    originalValues.contactNumber = contactNumber;
                    originalValues.region = region;
                    originalValues.province = province;
                    originalValues.city = city;
                    originalValues.barangay = barangay;
                    originalValues.houseNumber = houseNumber;
                    originalValues.pinLocation = pinLocation;
                    // Update displayed shipping info if present on page
                    // Hide shipping info warning if all fields are filled
            const shippingWarning = window.parent.document.querySelector('.shipping-warning');
            if (shippingWarning) {
                shippingWarning.style.display = 'none';
            }
            
            // Update displayed shipping info if elements are present
            if (document.getElementById('displayFullName')) {
                document.getElementById('displayFullName').textContent = fullName;
            }
            if (document.getElementById('displayContactNumber')) {
                document.getElementById('displayContactNumber').textContent = contactNumber;
            }
            if (document.getElementById('displayHouseAddress')) {
                document.getElementById('displayHouseAddress').textContent = combinedHouseAddress;
            }
            if (document.getElementById('displayPinLocation')) {
                document.getElementById('displayPinLocation').textContent = pinLocation;
            }
                    // Update the readonly house address display in the modal and hide selects
                    const display = document.getElementById('houseAddressDisplay');
                    const detailed = document.getElementById('detailedAddressContainer');
                    if (display) {
                        display.value = combinedHouseAddress;
                        display.classList.remove('hidden');
                    }
                    if (detailed) {
                        detailed.classList.add('hidden');
                        const houseInput = document.getElementById('houseNumber');
                        if (houseInput) houseInput.disabled = true;
                    }
                    // show edit address button again
                    const editBtn = document.getElementById('editAddressBtn');
                    if (editBtn) editBtn.style.display = '';
                    // Send message to parent window for real-time update
                    if (window.parent) {
                        window.parent.postMessage({
                            type: 'shippingInfoUpdated',
                            message: 'Shipping information updated successfully!',
                            info: {
                                fullName: fullName,
                                contactNumber: contactNumber,
                                houseAddress: combinedHouseAddress,
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
        document.getElementById('fullName').addEventListener('input', function() {
            // Show checkmark only if value is different from original
            const nameCheck = document.getElementById('nameCheck');
            if (nameCheck) {
                const hasNameChange = this.value !== originalValues.fullName;
                nameCheck.classList.toggle('hidden', !hasNameChange);
            }
            validateForm();
        });

        document.getElementById('contactNumber').addEventListener('input', function() {
            // Hide phone format error on input
            document.getElementById('phoneFormatError').style.display = 'none';
            validateForm();
        });
        document.getElementById('pinLocation').addEventListener('input', function() {
            // Show checkmark only if value is different from original
            const locationCheck = document.getElementById('locationCheck');
            if (locationCheck) {
                const hasLocationChange = this.value !== originalValues.pinLocation;
                locationCheck.classList.toggle('hidden', !hasLocationChange);
            }
            validateForm();
        });

        // House number input should trigger validation when changed
        const houseInp = document.getElementById('houseNumber');
        if (houseInp) houseInp.addEventListener('input', validateForm);

        // detailSelect and related helpers removed — we use a single free-text houseNumber input instead

        // Address selects listeners
    const regionSel = document.getElementById('regionSelect');
    const provinceSel = document.getElementById('provinceSelect');
    const citySel = document.getElementById('citySelect');
    const barangaySel = document.getElementById('barangaySelect');
    const houseInputEl = document.getElementById('houseNumber');

        regionSel.addEventListener('change', function() {
            populateProvinces(this.value);
            citySel.innerHTML = '<option value="">Select City / Municipality</option>';
            barangaySel.innerHTML = '<option value="">Select Barangay</option>';
            resetHouseNumber();
            validateForm();
        });

        provinceSel.addEventListener('change', function() {
            populateCities(regionSel.value, this.value);
            barangaySel.innerHTML = '<option value="">Select Barangay</option>';
            resetHouseNumber();
            validateForm();
        });

        citySel.addEventListener('change', function() {
            populateBarangays(regionSel.value, provinceSel.value, this.value);
            resetHouseNumber();
            validateForm();
        });

        barangaySel.addEventListener('change', function() {
            // When barangay changes, clear the house/street input (user must re-enter)
            resetHouseNumber();
            // If editing is active, enable the house input when a barangay is selected
            const houseInput = document.getElementById('houseNumber');
            if (houseInput && this.value) houseInput.disabled = false;
            validateForm();
        });

        function resetHouseNumber() {
            const houseInput = document.getElementById('houseNumber');
            if (!houseInput) return;
            houseInput.value = '';
            houseInput.disabled = true;
        }

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
            // Attempt to load a full Philippines address dataset from /raltt/data/ph_locations.json
            // If the file is present and valid JSON, it will replace the sample addressData object above.
            async function loadFullAddressData() {
                try {
                    const resp = await fetch('/raltt/data/ph_locations.json', { cache: 'no-cache' });
                    if (!resp.ok) throw new Error('No full dataset found');
                    const json = await resp.json();
                    // Expecting object shaped as { "Region Name": { "Province": { "City": ["Barangay1","Barangay2"] } } }
                    if (json && typeof json === 'object') {
                        // replace addressData with fetched data
                        addressData = json;
                    }
                } catch (err) {
                    // If the fetch fails, we silently fall back to the built-in sample addressData
                    // Optionally log for debugging during development
                    console.info('Full address data not found, using sample data. To enable full data, place a JSON file at /raltt/data/ph_locations.json');
                } finally {
                    // Populate the region dropdown once data (either sample or full) is available
                    populateRegions();
                }
            }

            loadFullAddressData();

            // Set initial values only
            originalValues.fullName = document.getElementById('fullName').value;
            originalValues.contactNumber = document.getElementById('contactNumber').value;
            originalValues.region = document.getElementById('regionSelect').value || '';
            originalValues.province = document.getElementById('provinceSelect').value || '';
            originalValues.city = document.getElementById('citySelect').value || '';
            originalValues.barangay = document.getElementById('barangaySelect').value || '';
            originalValues.houseNumber = document.getElementById('houseNumber') ? document.getElementById('houseNumber').value || '' : '';
            originalValues.pinLocation = document.getElementById('pinLocation').value;
            // Modal will only open when openShippingModal() is called explicitly
        });
    </script>
</body>
</html>