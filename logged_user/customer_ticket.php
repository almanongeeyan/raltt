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
        }
        
        .form-input:focus, .form-textarea:focus, .form-select:focus {
            border-color: #7d310a;
            box-shadow: 0 0 0 2px rgba(125, 49, 10, 0.2);
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
        }
        
        .modal-content {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
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
        
        @media (max-width: 768px) {
            .ticket-container {
                flex-direction: column;
            }
        }
    </style>
</head>

<body class="min-h-screen pt-24">
    <div class="container mx-auto px-4 py-8">
        <div class="page-container">
            <!-- Page Header with Back Button -->
            <div class="mb-6">
                <button class="back-btn text-white font-bold py-3 px-6 rounded-xl flex items-center" onclick="window.history.back();">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    <span>Back to Account</span>
                </button>
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
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-primary mb-4">Select Order with Damaged Tiles</h3>
                            <p class="text-textlight text-sm mb-4">Choose from your recent orders to request an exchange for cracked or shattered tiles</p>
                            
                            <div class="space-y-4 max-h-60 overflow-y-auto p-2">
                                <!-- Order 1 -->
                                <div class="order-card p-4" onclick="selectOrder(this, 'order1')">
                                    <div class="flex items-center">
                                        <img src="https://placehold.co/64x64/f9f5f2/7d310a?text=AT" alt="Product" class="w-16 h-16 rounded-lg object-cover mr-4">
                                        <div class="flex-grow">
                                            <p class="font-bold text-textdark">Arte Ceramiche Matte Floor Tile</p>
                                            <p class="text-sm text-textlight">Order #ORD-123456 • Nov 15, 2023</p>
                                            <p class="text-sm text-textlight">Deparo Branch • Qty: 2</p>
                                        </div>
                                        <div class="text-primary font-black">₱200</div>
                                    </div>
                                </div>
                                
                                <!-- Order 2 -->
                                <div class="order-card p-4" onclick="selectOrder(this, 'order2')">
                                    <div class="flex items-center">
                                        <img src="https://placehold.co/64x64/f9f5f2/7d310a?text=PW" alt="Product" class="w-16 h-16 rounded-lg object-cover mr-4">
                                        <div class="flex-grow">
                                            <p class="font-bold text-textdark">Porcelain Wood-Look Tile</p>
                                            <p class="text-sm text-textlight">Order #ORD-123457 • Nov 18, 2023</p>
                                            <p class="text-sm text-textlight">Brixton Branch • Qty: 1</p>
                                        </div>
                                        <div class="text-primary font-black">₱150</div>
                                    </div>
                                </div>
                                
                                <!-- Order 3 -->
                                <div class="order-card p-4" onclick="selectOrder(this, 'order3')">
                                    <div class="flex items-center">
                                        <img src="https://placehold.co/64x64/f9f5f2/7d310a?text=MW" alt="Product" class="w-16 h-16 rounded-lg object-cover mr-4">
                                        <div class="flex-grow">
                                            <p class="font-bold text-textdark">Marble Effect Wall Tile</p>
                                            <p class="text-sm text-textlight">Order #ORD-123458 • Nov 20, 2023</p>
                                            <p class="text-sm text-textlight">Vanguard Branch • Qty: 1</p>
                                        </div>
                                        <div class="text-primary font-black">₱700</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-textdark font-medium mb-2">Issue Type</label>
                            <select class="form-select w-full p-3">
                                <option value="">Select issue type</option>
                                <option value="cracked">Cracked Tiles</option>
                                <option value="shattered">Shattered Tiles</option>
                                <option value="defective">Defective Tiles</option>
                                <option value="wrong-item">Wrong Item Delivered</option>
                                <option value="other">Other Issue</option>
                            </select>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-textdark font-medium mb-2">When was the damage noticed?</label>
                            <select class="form-select w-full p-3">
                                <option value="">Select timeframe</option>
                                <option value="upon-delivery">Upon Delivery</option>
                                <option value="during-installation">During Installation</option>
                                <option value="after-installation">After Installation</option>
                                <option value="other-time">Other Time</option>
                            </select>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-textdark font-medium mb-2">Description of Issue</label>
                            <textarea class="form-textarea w-full p-3" rows="4" placeholder="Please describe the issue in detail..."></textarea>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-textdark font-medium mb-2">Upload Photos (Optional but recommended)</label>
                            <div class="file-upload p-6 text-center cursor-pointer">
                                <i class="fa-solid fa-cloud-upload-alt text-3xl text-primary mb-2"></i>
                                <p class="text-textdark font-medium">Click to upload or drag and drop</p>
                                <p class="text-textlight text-sm">PNG, JPG up to 10MB</p>
                                <input type="file" class="hidden" id="fileUpload" multiple accept="image/*">
                            </div>
                            <div id="filePreview" class="mt-4 grid grid-cols-3 gap-2 hidden"></div>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-textdark font-medium mb-2">Preferred Resolution</label>
                            <select class="form-select w-full p-3">
                                <option value="">Select resolution</option>
                                <option value="exchange">Exchange for Same Item</option>
                                <option value="refund">Refund</option>
                                <option value="store-credit">Store Credit</option>
                                <option value="contact-me">Contact Me to Discuss Options</option>
                            </select>
                        </div>
                        
                        <button type="button" class="submit-btn w-full text-white font-bold py-3 rounded-lg" onclick="submitTicket()">
                            Submit Ticket
                        </button>
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
                        
                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            <!-- Ticket 1 -->
                            <div class="ticket-item p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="font-bold text-textdark">Cracked Tiles - Order #ORD-123456</p>
                                        <p class="text-sm text-textlight">Submitted on Nov 16, 2023</p>
                                    </div>
                                    <span class="status-pending text-xs font-medium py-1 px-2 rounded-full">Pending</span>
                                </div>
                                <p class="text-sm text-textdark mb-2">Several tiles arrived with cracks along the edges...</p>
                                <div class="flex justify-between items-center">
                                    <span class="ticket-badge text-xs font-medium py-1 px-2 rounded-full">Exchange Requested</span>
                                    <button class="text-primary text-sm font-medium">View Details</button>
                                </div>
                            </div>
                            
                            <!-- Ticket 2 -->
                            <div class="ticket-item p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="font-bold text-textdark">Shattered Tiles - Order #ORD-123457</p>
                                        <p class="text-sm text-textlight">Submitted on Nov 5, 2023</p>
                                    </div>
                                    <span class="status-resolved text-xs font-medium py-1 px-2 rounded-full">Resolved</span>
                                </div>
                                <p class="text-sm text-textdark mb-2">Two tiles were completely shattered upon opening the box...</p>
                                <div class="flex justify-between items-center">
                                    <span class="ticket-badge text-xs font-medium py-1 px-2 rounded-full">Replacement Sent</span>
                                    <button class="text-primary text-sm font-medium">View Details</button>
                                </div>
                            </div>
                            
                            <!-- Ticket 3 -->
                            <div class="ticket-item p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="font-bold text-textdark">Color Mismatch - Order #ORD-123455</p>
                                        <p class="text-sm text-textlight">Submitted on Oct 28, 2023</p>
                                    </div>
                                    <span class="status-closed text-xs font-medium py-1 px-2 rounded-full">Closed</span>
                                </div>
                                <p class="text-sm text-textdark mb-2">Tiles received don't match the sample color...</p>
                                <div class="flex justify-between items-center">
                                    <span class="ticket-badge text-xs font-medium py-1 px-2 rounded-full">Refund Issued</span>
                                    <button class="text-primary text-sm font-medium">View Details</button>
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
            <div class="modal-header p-6 flex justify-between items-center">
                <h3 class="text-xl font-black text-primary">Ticket Submitted Successfully</h3>
                <button class="close-btn text-2xl" onclick="closeModal('successModal')">&times;</button>
            </div>
            <div class="p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-check text-3xl text-green-600"></i>
                </div>
                <h4 class="font-bold text-textdark text-lg mb-2">Thank You for Your Submission</h4>
                <p class="text-textlight mb-4">Your ticket #TKT-789456 has been submitted successfully. Our support team will contact you within 24-48 hours.</p>
                <button type="button" class="submit-btn w-full text-white font-bold py-3 rounded-lg" onclick="closeModal('successModal')">
                    Done
                </button>
            </div>
        </div>
    </div>

    <script>
        // Select order function
        function selectOrder(element, orderId) {
            // Remove selected class from all orders
            document.querySelectorAll('.order-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked order
            element.classList.add('selected');
            
            // Store selected order (in a real application, you would use this value)
            console.log('Selected order:', orderId);
        }
        
        // File upload handling
        const fileUpload = document.getElementById('fileUpload');
        const filePreview = document.getElementById('filePreview');
        
        fileUpload.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files.length > 0) {
                filePreview.classList.remove('hidden');
                filePreview.innerHTML = '';
                
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const preview = document.createElement('div');
                        preview.className = 'relative';
                        preview.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg">
                            <button type="button" class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs" onclick="removeImage(this)">×</button>
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
            if (filePreview.children.length === 0) {
                filePreview.classList.add('hidden');
            }
        }
        
        // Submit ticket function
        function submitTicket() {
            // Validate form (simplified for demo)
            const selectedOrder = document.querySelector('.order-card.selected');
            const issueType = document.querySelector('select');
            
            if (!selectedOrder) {
                alert('Please select an order with damaged tiles');
                return;
            }
            
            if (!issueType.value) {
                alert('Please select an issue type');
                return;
            }
            
            // Show success modal
            document.getElementById('successModal').style.display = 'flex';
            
            // In a real application, you would submit the form data to a server here
        }
        
        // Modal functions
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = document.getElementsByClassName('modal');
            for (let i = 0; i < modals.length; i++) {
                if (event.target === modals[i]) {
                    modals[i].style.display = 'none';
                }
            }
        }
        
        // Make file upload area droppable
        const uploadArea = document.querySelector('.file-upload');
        
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
    </script>

</body>

</html>