<?php
include '../includes/headeruser.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Support - Rich Anne Lea Tiles Trading</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7d310a',
                        secondary: '#cf8756',
                        accent: '#e8a56a',
                        dark: '#270f03',
                        light: '#f9f5f2',
                        textdark: '#333',
                        textlight: '#777',
                    },
                    fontFamily: {
                        inter: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #ffece2 0%, #f8f5f2 60%, #e8a56a 100%);
            min-height: 100vh;
        }
        
        .page-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .back-btn {
            background: linear-gradient(90deg, #7d310a 0%, #a34a20 100%);
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: linear-gradient(90deg, #a34a20 0%, #7d310a 100%);
            transform: translateY(-2px);
        }
        
        .form-box {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .form-box:hover {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }
        
        .ticket-header {
            background: linear-gradient(90deg, #f9f5f2 0%, #f0e6df 100%);
            border-bottom: 1px solid #e8d9cf;
        }
        
        .submit-btn {
            background: linear-gradient(90deg, #7d310a 0%, #a34a20 100%);
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            background: linear-gradient(90deg, #a34a20 0%, #7d310a 100%);
            transform: translateY(-2px);
        }
        
        .order-card {
            border: 1px solid #e8d9cf;
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .order-card:hover, .order-card.selected {
            border-color: #7d310a;
            box-shadow: 0 4px 12px rgba(125, 49, 10, 0.1);
        }
        
        .order-card.selected {
            background-color: #f9f5f2;
        }
        
        .form-input, .form-textarea, .form-select {
            border: 1px solid #e8d9cf;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: #333; /* Fix font color for visibility */
        }
        
        .form-input:focus, .form-textarea:focus, .form-select:focus {
            border-color: #7d310a;
            box-shadow: 0 0 0 2px rgba(125, 49, 10, 0.2);
        }
        
        .form-select {
            color: #7d310a;
        }
        
        .file-upload {
            border: 2px dashed #cf8756;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .file-upload:hover {
            border-color: #7d310a;
            background-color: #f9f5f2;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        
        .modal.show {
            display: flex;
            opacity: 1;
            pointer-events: auto;
        }
        
        .modal-content {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            transform: scale(0.95) translateY(40px);
            opacity: 0;
            transition: all 0.35s cubic-bezier(.4,2,.3,1);
        }
        
        .modal.show .modal-content {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
        
        .modal-header {
            background: linear-gradient(90deg, #f9f5f2 0%, #f0e6df 100%);
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }
        
        .close-btn {
            color: #7d310a;
            transition: all 0.2s ease;
        }
        
        .close-btn:hover {
            color: #5a2207;
            transform: scale(1.1);
        }
        
        .ticket-badge {
            background: linear-gradient(90deg, #f9f5f2 0%, #f0e6df 100%);
            color: #7d310a;
        }
        
        .ticket-item {
            border: 1px solid #e8d9cf;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .ticket-item:hover {
            border-color: #cf8756;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }
        
        .status-processing {
            background-color: #dbeafe;
            color: #2563eb;
        }
        
        .status-resolved {
            background-color: #dcfce7;
            color: #16a34a;
        }
        
        .status-closed {
            background-color: #e5e7eb;
            color: #4b5563;
        }
        
        .order-ref-input {
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
            gap: 0.5rem;
        }
        
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 7%;
            right: 7%;
            height: 6px;
            background: linear-gradient(90deg, #e8d9cf 0%, #f9f5f2 100%);
            border-radius: 3px;
            z-index: 1;
            transform: translateY(-50%);
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }
        
        .step-number {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e8d9cf 60%, #f9f5f2 100%);
            color: #777;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            box-shadow: 0 2px 8px rgba(125,49,10,0.07);
            border: 3px solid #e8d9cf;
            transition: all 0.3s cubic-bezier(.4,2,.3,1);
        }
        
        .step.active .step-number {
            background: linear-gradient(135deg, #7d310a 60%, #cf8756 100%);
            color: #fff;
            border-color: #7d310a;
            box-shadow: 0 4px 16px rgba(125,49,10,0.13);
            transform: scale(1.08);
        }
        
        .step.completed .step-number {
            background: linear-gradient(135deg, #16a34a 60%, #a7f3d0 100%);
            color: #fff;
            border-color: #16a34a;
            box-shadow: 0 4px 16px rgba(22,163,74,0.13);
            transform: scale(1.08);
        }
        
        .step-label {
            font-size: 1rem;
            color: #777;
            font-weight: 500;
            margin-top: 0.1rem;
            letter-spacing: 0.01em;
            text-align: center;
        }
        
        .step.active .step-label {
            color: #7d310a;
            font-weight: 700;
        }
        
        .step.completed .step-label {
            color: #16a34a;
            font-weight: 700;
        }
        
        .form-section {
            display: none;
            opacity: 0;
            transform: translateX(40px) scale(0.98);
            transition: all 0.4s cubic-bezier(.4,2,.3,1);
        }
        
        .form-section.active {
            display: block;
            opacity: 1;
            transform: translateX(0) scale(1);
            animation: fadeInStep 0.5s cubic-bezier(.4,2,.3,1);
        }
        
        @keyframes fadeInStep {
            from { opacity: 0; transform: translateX(40px) scale(0.98); }
            to { opacity: 1; transform: translateX(0) scale(1); }
        }
        
        .item-checkbox {
            accent-color: #7d310a;
            width: 18px;
            height: 18px;
        }
        
        .file-preview-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 8px;
            margin-top: 12px;
        }
        
        .file-preview-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .file-preview-item img {
            width: 100%;
            height: 80px;
            object-fit: cover;
        }
        
        .file-preview-remove {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 20px;
            height: 20px;
            background: rgba(255, 0, 0, 0.7);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .ticket-container {
                flex-direction: column;
            }
            
            .step-indicator {
                padding: 0 1rem;
            }
            
            .step-label {
                font-size: 0.75rem;
            }
        }
    </style>
</head>

<body class="min-h-screen pt-24">
    <div class="container mx-auto px-4 py-8">
        <div class="page-container">
            <!-- Page Header -->
            <div class="mb-6">
                <h2 class="text-2xl font-black text-primary">Customer Support</h2>
            </div>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" id="step1">
                    <div class="step-number">1</div>
                    <div class="step-label">Order Reference</div>
                </div>
                <div class="step" id="step2">
                    <div class="step-number">2</div>
                    <div class="step-label">Select Items</div>
                </div>
                <div class="step" id="step3">
                    <div class="step-number">3</div>
                    <div class="step-label">Issue Details</div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="ticket-container flex flex-col lg:flex-row gap-6">
                <!-- New Ticket Form -->
                <div class="form-box flex-grow">
                    <form id="ticketForm" class="p-6">
                        <div class="ticket-header flex items-center mb-6 pb-4">
                            <div class="ticket-title flex items-center text-primary font-black text-xl">
                                <i class="fa-solid fa-ticket-alt mr-3"></i>
                                <span>Submit a Ticket</span>
                            </div>
                        </div>
                        
                        <!-- Step 1: Order Reference -->
                        <div class="form-section active" id="section1">
                            <div class="mb-6">
                                <h3 class="text-lg font-bold text-primary mb-4">Enter Your Order Reference Number</h3>
                                <p class="text-textlight text-sm mb-4">Please enter your order reference number in the format RAL-XXXXXXXX (8 characters of numbers or uppercase letters)</p>
                                
                                <div class="flex items-center">
                                    <span class="bg-light text-primary font-bold py-3 px-4 rounded-l-lg border border-r-0 border-[#e8d9cf]">RAL-</span>
                                    <input type="text" id="orderRefInput" class="form-input order-ref-input flex-grow rounded-l-none py-3" placeholder="XXXXXXXX" maxlength="8" pattern="[A-Z0-9]{8}" title="8 characters of numbers or uppercase letters">
                                </div>
                                <p class="text-xs text-textlight mt-2">Example: RAL-A1B2C3D4</p>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="button" class="submit-btn text-white font-bold py-3 px-6 rounded-lg" onclick="validateOrderRef()">
                                    Next <i class="fa-solid fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Step 2: Select Items -->
                        <div class="form-section" id="section2">
                            <div class="mb-6">
                                <h3 class="text-lg font-bold text-primary mb-4">Select Items with Issues</h3>
                                <p class="text-textlight text-sm mb-4">Choose the items from your order that have issues</p>
                                
                                <div class="space-y-4 max-h-60 overflow-y-auto p-2" id="orderItemsContainer">
                                    <!-- Order items will be dynamically inserted here -->
                                </div>
                            </div>
                            
                            <div class="flex justify-between">
                                <button type="button" class="back-btn text-white font-bold py-3 px-6 rounded-lg" onclick="goToStep(1)">
                                    <i class="fa-solid fa-arrow-left mr-2"></i> Back
                                </button>
                                <button type="button" class="submit-btn text-white font-bold py-3 px-6 rounded-lg" onclick="nextStepSelectItems()">
                                    Next <i class="fa-solid fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Step 3: Issue Details -->
                        <div class="form-section" id="section3">
                            <div class="mb-6">
                                <h3 class="text-lg font-bold text-primary mb-4">Issue Details</h3>
                                
                                <div class="mb-6">
                                    <label class="block text-textdark font-medium mb-2">Issue Type</label>
                                    <select class="form-select w-full p-3" id="issueType" required>
                                        <option value="">Select issue type</option>
                                        <option value="cracked">Cracked Tile</option>
                                        <option value="shattered">Shattered Tile</option>
                                        <option value="defective">Defective Tile</option>
                                        <option value="wrong-item">Wrong Item Delivered</option>
                                        <option value="payment">Payment Issues</option>
                                        <option value="other">Other Issue</option>
                                    </select>
                                </div>
                                
                                <div class="mb-6">
                                    <label class="block text-textdark font-medium mb-2">When was the damage noticed?</label>
                                    <select class="form-select w-full p-3" id="damageTime" required>
                                        <option value="">Select timeframe</option>
                                        <option value="upon-delivery">Upon Delivery</option>
                                        <option value="after-delivery">After Delivery</option>
                                        <option value="other-time">Other Time</option>
                                    </select>
                                </div>
                                
                                <div class="mb-6">
                                    <label class="block text-textdark font-medium mb-2">Description of Issue</label>
                                    <textarea class="form-textarea w-full p-3" id="issueDescription" rows="4" placeholder="Please describe the issue in detail..." required></textarea>
                                </div>
                                
                                <div class="mb-6">
                                    <label class="block text-textdark font-medium mb-2">Upload Photos (Optional but recommended)</label>
                                    <div class="file-upload p-6 text-center cursor-pointer" id="fileUploadArea">
                                        <i class="fa-solid fa-cloud-upload-alt text-3xl text-primary mb-2"></i>
                                        <p class="text-textdark font-medium">Click to upload or drag and drop</p>
                                        <p class="text-textlight text-sm">PNG, JPG up to 10MB</p>
                                        <input type="file" class="hidden" id="fileUpload" multiple accept="image/*">
                                    </div>
                                    <div id="filePreview" class="file-preview-container"></div>
                                </div>
                            </div>
                            
                            <div class="flex justify-between">
                                <button type="button" class="back-btn text-white font-bold py-3 px-6 rounded-lg" onclick="goToStep(2)">
                                    <i class="fa-solid fa-arrow-left mr-2"></i> Back
                                </button>
                                <button type="button" class="submit-btn text-white font-bold py-3 px-6 rounded-lg" onclick="submitTicket()">
                                    Submit Ticket
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Ticket History -->
                <div class="form-box lg:w-96">
                    <div class="p-6">
                        <div class="ticket-header flex items-center mb-6 pb-4">
                            <div class="ticket-title flex items-center text-primary font-black text-xl">
                                <i class="fa-solid fa-history mr-3"></i>
                                <span>Ticket History</span>
                            </div>
                        </div>
                        <div class="space-y-6 max-h-96 overflow-y-auto">
                            <!-- Ticket 1 -->
                            <div class="ticket-item p-6 shadow-sm hover:shadow-md transition-all border border-[#e8d9cf] rounded-xl bg-white flex flex-col gap-2">
                                <div class="flex justify-between items-center mb-1">
                                    <div>
                                        <p class="font-bold text-textdark text-base">Cracked Tiles - Order #ORD-123456</p>
                                        <p class="text-xs text-textlight">Submitted on Nov 16, 2023</p>
                                    </div>
                                    <span class="status-pending text-xs font-medium py-1 px-3 rounded-full">Pending</span>
                                </div>
                                <p class="text-sm text-textdark mb-1">Several tiles arrived with cracks along the edges...</p>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="ticket-badge text-xs font-medium py-1 px-3 rounded-full">Exchange Requested</span>
                                    <button class="text-primary text-sm font-medium view-details-btn" data-ticket="1">View Details</button>
                                </div>
                            </div>
                            <!-- Ticket 2 -->
                            <div class="ticket-item p-6 shadow-sm hover:shadow-md transition-all border border-[#e8d9cf] rounded-xl bg-white flex flex-col gap-2">
                                <div class="flex justify-between items-center mb-1">
                                    <div>
                                        <p class="font-bold text-textdark text-base">Shattered Tiles - Order #ORD-123457</p>
                                        <p class="text-xs text-textlight">Submitted on Nov 5, 2023</p>
                                    </div>
                                    <span class="status-resolved text-xs font-medium py-1 px-3 rounded-full">Resolved</span>
                                </div>
                                <p class="text-sm text-textdark mb-1">Two tiles were completely shattered upon opening the box...</p>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="ticket-badge text-xs font-medium py-1 px-3 rounded-full">Replacement Sent</span>
                                    <button class="text-primary text-sm font-medium view-details-btn" data-ticket="2">View Details</button>
                                </div>
                            </div>
                            <!-- Ticket 3 -->
                            <div class="ticket-item p-6 shadow-sm hover:shadow-md transition-all border border-[#e8d9cf] rounded-xl bg-white flex flex-col gap-2">
                                <div class="flex justify-between items-center mb-1">
                                    <div>
                                        <p class="font-bold text-textdark text-base">Color Mismatch - Order #ORD-123455</p>
                                        <p class="text-xs text-textlight">Submitted on Oct 28, 2023</p>
                                    </div>
                                    <span class="status-closed text-xs font-medium py-1 px-3 rounded-full">Closed</span>
                                </div>
                                <p class="text-sm text-textdark mb-1">Tiles received don't match the sample color...</p>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="ticket-badge text-xs font-medium py-1 px-3 rounded-full">Refund Issued</span>
                                    <button class="text-primary text-sm font-medium view-details-btn" data-ticket="3">View Details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <div class="modal-header p-6 flex items-center">
                <h3 class="text-xl font-black text-primary">Ticket Submitted Successfully</h3>
            </div>
            <div class="p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-check text-3xl text-green-600"></i>
                </div>
                <h4 class="font-bold text-textdark text-lg mb-2">Thank You for Your Submission</h4>
                <p class="text-textlight mb-4">Your ticket has been submitted successfully. Our support team will contact you within 24-48 hours.</p>
                <button type="button" class="submit-btn w-full text-white font-bold py-3 rounded-lg" onclick="closeModal('successModal')">
                    Done
                </button>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <div class="modal-header p-6 flex items-center">
                <h3 class="text-xl font-black text-primary">Order Reference Error</h3>
            </div>
            <div class="p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-times text-3xl text-red-600"></i>
                </div>
                <h4 class="font-bold text-textdark text-lg mb-2" id="errorModalTitle">Invalid Reference</h4>
                <p class="text-textlight mb-4" id="errorModalMsg">Please enter a valid order reference number (8 characters of numbers or uppercase letters).</p>
                <button type="button" class="submit-btn w-full text-white font-bold py-3 rounded-lg" onclick="closeModal('errorModal')">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Ticket Details Modal -->
    <div id="ticketDetailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header p-6 flex items-center">
                <h3 class="text-xl font-black text-primary">Ticket Details</h3>
            </div>
            <div class="p-6" id="ticketDetailsContent">
                <!-- Ticket details will be injected here -->
            </div>
        </div>
    </div>

    <script>
        // Ticket details data (for demo)
        const ticketDetailsData = {
            1: {
                title: 'Cracked Tiles - Order #ORD-123456',
                date: 'Nov 16, 2023',
                status: 'Pending',
                description: 'Several tiles arrived with cracks along the edges. Please see attached photos for reference.',
                resolution: 'Exchange Requested',
                response: 'Our team is reviewing your request. We will update you soon.'
            },
            2: {
                title: 'Shattered Tiles - Order #ORD-123457',
                date: 'Nov 5, 2023',
                status: 'Resolved',
                description: 'Two tiles were completely shattered upon opening the box. Replacement requested.',
                resolution: 'Replacement Sent',
                response: 'Replacement tiles have been shipped. Thank you for your patience.'
            },
            3: {
                title: 'Color Mismatch - Order #ORD-123455',
                date: 'Oct 28, 2023',
                status: 'Closed',
                description: 'Tiles received don\'t match the sample color. Refund requested.',
                resolution: 'Refund Issued',
                response: 'Refund has been processed. We apologize for the inconvenience.'
            }
        };

        // Order data (for demo)
        // Order data will be fetched from backend

        // Current state
        let currentStep = 1;
        let selectedItems = [];
        let uploadedFiles = [];

        // Store current order info for ticket submission
        let currentOrderId = null;
        let currentOrderRef = null;

        // Navigation functions with animation
        function goToStep(step) {
            const prevSection = document.getElementById(`section${currentStep}`);
            const nextSection = document.getElementById(`section${step}`);
            // Animate out current section
            prevSection.classList.remove('active');
            // Update step indicators
            document.getElementById(`step${currentStep}`).classList.remove('active');
            document.getElementById(`step${step}`).classList.add('active');
            // Mark previous steps as completed
            for (let i = 1; i < step; i++) {
                document.getElementById(`step${i}`).classList.add('completed');
            }
            // Animate in next section
            setTimeout(() => {
                prevSection.style.display = 'none';
                nextSection.style.display = 'block';
                setTimeout(() => {
                    nextSection.classList.add('active');
                }, 10);
            }, 300);
            currentStep = step;
        }

        // Step 2 Next button validation
        function nextStepSelectItems() {
            if (selectedItems.length === 0) {
                showErrorModal('No Item Selected', 'Please select at least one item with issues.');
                return;
            }
            goToStep(3);
        }

        // Validate order reference
        function validateOrderRef() {
            const orderRef = document.getElementById('orderRefInput').value.toUpperCase();
            const pattern = /^[A-Z0-9]{8}$/;
            if (!pattern.test(orderRef)) {
                showErrorModal('Invalid Reference', 'Please enter a valid order reference number (8 characters of numbers or uppercase letters).');
                return;
            }
            // AJAX call to backend
            fetch('get_order_items.php?ref=' + encodeURIComponent(orderRef))
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        showErrorModal('Order Reference Not Found', data.error || 'Order reference not found. Please check your reference number and try again.');
                        return;
                    }
                    // Save order info for ticket
                    currentOrderId = data.items.length > 0 ? data.items[0].order_id : null;
                    currentOrderRef = 'RAL-' + orderRef;
                    loadOrderItems(data.items);
                    goToStep(2);
                })
                .catch(() => {
                    showErrorModal('Error', 'Unable to connect to server. Please try again later.');
                });
        }

        async function submitTicket() {
            const issueType = document.getElementById('issueType');
            const damageTime = document.getElementById('damageTime');
            const issueDescription = document.getElementById('issueDescription');
            const orderRef = document.getElementById('orderRefInput').value.toUpperCase();

            if (selectedItems.length === 0) {
                showErrorModal('No Item Selected', 'Please select at least one item with issues.');
                return;
            }
            if (!issueType.value) {
                showErrorModal('Missing Issue Type', 'Please select an issue type.');
                return;
            }
            if (!damageTime.value) {
                showErrorModal('Missing Damage Time', 'Please select when the damage was noticed.');
                return;
            }
            if (!issueDescription.value.trim()) {
                showErrorModal('Missing Description', 'Please provide a description of the issue.');
                return;
            }
            // Get user_id from PHP session
            let userId = null;
            try {
                userId = <?php echo isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 'null'; ?>;
            } catch (e) {
                userId = null;
            }
            if (!userId) {
                showErrorModal('User Error', 'User not logged in.');
                return;
            }
            // Map frontend values to backend ENUMs
            const issueTypeMap = {
                'cracked': 'Cracked Tile',
                'shattered': 'Shattered Tile',
                'defective': 'Defective Tile',
                'wrong-item': 'Wrong Item Delivered',
                'payment': 'Payment Issues',
                'other': 'Other'
            };
            const damageTimeMap = {
                'upon-delivery': 'Upon Delivery',
                'after-delivery': 'After Delivery',
                'other-time': 'Other time'
            };
            // Get photo as base64 (first image only)
            let photoBase64 = '';
            const fileInput = document.getElementById('fileUpload');
            if (fileInput.files && fileInput.files.length > 0) {
                const file = fileInput.files[0];
                photoBase64 = await new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        resolve(e.target.result.split(',')[1]); // Remove data:image/...;base64,
                    };
                    reader.readAsDataURL(file);
                });
            }
            const ticketData = {
                user_id: userId,
                order_id: currentOrderId,
                order_reference: currentOrderRef,
                issue_type: issueTypeMap[issueType.value] || '',
                damage_time: damageTimeMap[damageTime.value] || '',
                issue_description: issueDescription.value,
                photo: photoBase64
            };
            // Save ticket via AJAX
            try {
                const response = await fetch('save_ticket.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(ticketData)
                });
                const result = await response.json();
                if (result.success) {
                    showModal('successModal');
                    setTimeout(() => {
                        resetTicketForm();
                        loadTicketHistory(); // Reload ticket history after successful submission
                    }, 2000);
                } else {
                    showErrorModal('Ticket Error', result.error || 'Failed to submit ticket.');
                }
            } catch (err) {
                showErrorModal('Server Error', 'Could not connect to server.');
            }
        }

        // Show error modal
        function showErrorModal(title, message) {
            document.getElementById('errorModalTitle').textContent = title;
            document.getElementById('errorModalMsg').textContent = message;
            showModal('errorModal');
        }

        // Load order items
        function loadOrderItems(items) {
            const container = document.getElementById('orderItemsContainer');
            let html = '';
            if (!items || items.length === 0) {
                html = '<div class="text-center text-textlight">No items found for this order.</div>';
            } else {
                items.forEach(item => {
                    html += `
                        <div class="order-card p-4">
                            <div class="flex items-center">
                                <input type="checkbox" class="item-checkbox mr-4" id="item_${item.id}" value="${item.id}" onchange="toggleItemSelection('item_${item.id}')">
                                <img src="${item.image || ''}" alt="Product" class="w-16 h-16 rounded-lg object-cover mr-4">
                                <div class="flex-grow">
                                    <p class="font-bold text-textdark">${item.name}</p>
                                    <p class="text-sm text-textlight">Qty: ${item.quantity}</p>
                                </div>
                                <div class="text-primary font-black">₱${item.price}</div>
                            </div>
                        </div>
                    `;
                });
            }
            container.innerHTML = html;
        }

        // Toggle item selection
        function toggleItemSelection(itemId) {
            const checkbox = document.getElementById(itemId);
            
            if (checkbox.checked) {
                selectedItems.push(itemId);
                checkbox.closest('.order-card').classList.add('selected');
            } else {
                selectedItems = selectedItems.filter(id => id !== itemId);
                checkbox.closest('.order-card').classList.remove('selected');
            }
        }

        // File upload handling
        const fileUpload = document.getElementById('fileUpload');
        const filePreview = document.getElementById('filePreview');
        
        fileUpload.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files.length > 0) {
                filePreview.innerHTML = '';
                
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const preview = document.createElement('div');
                        preview.className = 'file-preview-item';
                        preview.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <div class="file-preview-remove" onclick="removeImage(this)">×</div>
                        `;
                        filePreview.appendChild(preview);
                    };
                    
                    reader.readAsDataURL(file);
                }
            }
        });
        
        // Remove image from preview
        function removeImage(button) {
            button.parentElement.remove();
        }
        
        // Reset form and go to step 1
        function resetTicketForm() {
            // Reset form fields
            document.getElementById('ticketForm').reset();
            selectedItems = [];
            
            // Remove selected class from order cards if any
            document.querySelectorAll('.order-card.selected').forEach(card => card.classList.remove('selected'));
            
            // Hide all sections
            for (let i = 1; i <= 3; i++) {
                document.getElementById(`section${i}`).classList.remove('active');
                document.getElementById(`section${i}`).style.display = 'none';
                document.getElementById(`step${i}`).classList.remove('active', 'completed');
            }
            
            // Clear file preview
            filePreview.innerHTML = '';
            
            // Show first section and step
            setTimeout(() => {
                document.getElementById('section1').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('section1').classList.add('active');
                    document.getElementById('step1').classList.add('active');
                }, 10);
                currentStep = 1;
            }, 10);
        }
        
        // Modal functions with animation
        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        }
        
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = document.getElementsByClassName('modal');
            for (let i = 0; i < modals.length; i++) {
                if (event.target === modals[i]) {
                    closeModal(modals[i].id);
                }
            }
        }
        
        // Make file upload area droppable
        const uploadArea = document.getElementById('fileUploadArea');
        
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('bg-light');
        });
        
        uploadArea.addEventListener('dragleave', function() {
            this.classList.remove('bg-light');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('bg-light');
            
            const files = e.dataTransfer.files;
            fileUpload.files = files;
            
            // Trigger change event
            const event = new Event('change');
            fileUpload.dispatchEvent(event);
        });
        
        // Click on upload area to trigger file input
        uploadArea.addEventListener('click', function() {
            fileUpload.click();
        });

        // View Details modal logic for user's tickets
        document.addEventListener('click', async function(e) {
            if (e.target.classList.contains('view-details-btn')) {
                const ticketId = e.target.getAttribute('data-ticket');
                const response = await fetch('get_user_ticket_details.php?ticket_id=' + encodeURIComponent(ticketId));
                const result = await response.json();
                if (result.success && result.ticket) {
                    const t = result.ticket;
                    let itemsHtml = '';
                    if (t.items && t.items.length > 0) {
                        itemsHtml = `<div class="mb-4">
                            <h5 class="font-bold text-primary mb-2">Selected Tile Items</h5>
                            <ul class="list-disc pl-6">
                                ${t.items.map(item => {
                                    const total = (parseFloat(item.unit_price) * parseInt(item.quantity)).toFixed(2);
                                    return `<li class='mb-2 flex items-center'>
                                        <span class='font-semibold text-textdark mr-2'>${item.product_name}</span>
                                        <span class='bg-light px-2 py-1 rounded text-xs font-medium mr-2'>Qty: ${item.quantity}</span>
                                        <span class='text-xs text-textlight mr-2'>(₱${item.unit_price} each)</span>
                                        <span class='text-primary font-bold ml-auto'>Total: ₱${total}</span>
                                    </li>`;
                                }).join('')}
                            </ul>
                        </div>`;
                    } else {
                        itemsHtml = `<div class='mb-4 text-textlight'>No items selected for this ticket.</div>`;
                    }

                    // Mark as Resolved button logic (no countdown, always enabled)
                    let resolvedBtnHtml = '';
                    if (t.ticket_status === 'Awaiting Customer') {
                        resolvedBtnHtml = `<button id="markResolvedBtn" class="submit-btn w-full text-white font-bold py-3 rounded-lg mt-2">Mark as Resolved</button>`;
                    }

                    document.getElementById('ticketDetailsContent').innerHTML = `
                        <div class="mb-4">
                            <h4 class="font-bold text-lg text-primary mb-1">${t.issue_type} - Order #${t.order_reference}</h4>
                            <p class="text-xs text-textlight mb-2">Submitted on ${new Date(t.created_at).toLocaleString()}</p>
                            <span class="inline-block mb-2 px-3 py-1 rounded-full text-xs font-medium status-pending">${t.ticket_status}</span>
                        </div>
                        <div class="mb-4">
                            <p class="text-sm text-textdark mb-1"><span class="font-medium">Description:</span> ${t.issue_description}</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-sm text-textdark mb-1"><span class="font-medium">Damage Noticed:</span> ${t.damage_time || 'N/A'}</p>
                        </div>
                        ${itemsHtml}
                        ${resolvedBtnHtml}
                    `;
                    showModal('ticketDetailsModal');

                    // No countdown logic needed

                    // AJAX for Mark as Resolved
                    setTimeout(() => {
                        const btn = document.getElementById('markResolvedBtn');
                        if (btn) {
                            btn.addEventListener('click', async function() {
                                btn.disabled = true;
                                btn.innerHTML = '<span class="loading-spinner mr-2"><i class="fas fa-circle-notch fa-spin"></i></span>Marking...';
                                try {
                                    const formData = new URLSearchParams();
                                    formData.append('ticket_id', ticketId);
                                    formData.append('status', 'Resolved');
                                    const resp = await fetch('../staffadmin_access/processes/process_update_ticket_status.php', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                        body: formData.toString()
                                    });
                                    const res = await resp.json();
                                    if (res.success) {
                                        btn.innerHTML = 'Marked as Resolved';
                                        showToast('Ticket marked as resolved!');
                                        setTimeout(() => {
                                            closeModal('ticketDetailsModal');
                                            loadTicketHistory();
                                        }, 1200);
                                    } else {
                                        btn.innerHTML = 'Mark as Resolved';
                                        btn.disabled = false;
                                        alert(res.error || 'Failed to update status.');
                                    }
                                } catch (err) {
                                    btn.innerHTML = 'Mark as Resolved';
                                    btn.disabled = false;
                                    alert('Server error.');
                                }
                            });
                        }
                    }, 400);
                }
            }
        });

        // Load user's ticket history from database and display
        async function loadTicketHistory() {
            try {
                const response = await fetch('get_user_tickets.php');
                const result = await response.json();
                const container = document.querySelector('.form-box.lg\\:w-96 .space-y-6');
                if (!result.success || !result.tickets || result.tickets.length === 0) {
                    container.innerHTML = '<div class="text-center text-textlight">No tickets submitted yet.</div>';
                    return;
                }
                let html = '';
                result.tickets.forEach(ticket => {
                    let statusClass = 'status-pending';
                    if (ticket.ticket_status === 'Resolved') statusClass = 'status-resolved';
                    else if (ticket.ticket_status === 'Closed') statusClass = 'status-closed';
                    html += `
                        <div class="ticket-item p-6 shadow-sm hover:shadow-md transition-all border border-[#e8d9cf] rounded-xl bg-white flex flex-col gap-2">
                            <div class="flex justify-between items-center mb-1">
                                <div>
                                    <p class="font-bold text-textdark text-base">${ticket.issue_type} - Order #${ticket.order_reference}</p>
                                    <p class="text-xs text-textlight">Submitted on ${new Date(ticket.created_at).toLocaleString()}</p>
                                </div>
                                <span class="${statusClass} text-xs font-medium py-1 px-3 rounded-full">${ticket.ticket_status}</span>
                            </div>
                            <p class="text-sm text-textdark mb-1">${ticket.issue_description}</p>
                            <div class="flex justify-between items-center mt-2">
                                <span class="ticket-badge text-xs font-medium py-1 px-3 rounded-full">${ticket.issue_type}</span>
                                <button class="text-primary text-sm font-medium view-details-btn" data-ticket="${ticket.ticket_id}">View Details</button>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            } catch (err) {
                document.querySelector('.form-box.lg\\:w-96 .space-y-6').innerHTML = '<div class="text-center text-textlight">Unable to load tickets.</div>';
            }
        }
        window.addEventListener('DOMContentLoaded', loadTicketHistory);
    // Toast notification function
    function showToast(message) {
        let toast = document.createElement('div');
        toast.className = 'fixed top-6 right-6 z-50 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg font-bold text-sm flex items-center gap-2 animate-fadein';
        toast.innerHTML = `<i class="fa-solid fa-check-circle"></i> ${message}`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 500);
        }, 2500);
    }
    // Toast animation
    const style = document.createElement('style');
    style.innerHTML = `@keyframes fadein { from { opacity: 0; transform: translateY(-20px);} to { opacity: 1; transform: translateY(0);} } .animate-fadein { animation: fadein 0.5s; }`;
    document.head.appendChild(style);
    </script>

</body>

</html>