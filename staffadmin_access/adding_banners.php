<?php include '../includes/sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Banners</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Cropper.js CSS & JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <style>
        .banner-slot {
            transition: all 0.3s ease-in-out;
            cursor: pointer;
        }
        .banner-slot:hover .banner-placeholder {
            background-color: #f3f4f6;
        }
        .modal {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }
        .cropper-modal-custom {
            z-index: 60;
        }
        /* Banner container polish */
        .banner-container-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 2rem;
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
        }
        @media (max-width: 768px) {
            .banner-container-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <!-- Cropper Modal -->
    <div id="cropper-modal" class="modal fixed inset-0 flex items-center justify-center hidden cropper-modal-custom">
        <div class="bg-white rounded-lg shadow-xl p-6 m-4 max-w-2xl w-full flex flex-col items-center">
            <h2 class="text-lg font-bold mb-4 text-gray-800">Crop Banner to 1920x1080</h2>
            <div class="w-full flex justify-center items-center mb-4">
                <img id="cropper-image" src="" class="max-h-96 max-w-full rounded shadow" alt="Cropper Preview"/>
            </div>
            <div class="flex w-full justify-end space-x-2">
                <button id="cropper-cancel" class="bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg hover:bg-gray-300 transition duration-300">Cancel</button>
                <button id="cropper-confirm" class="bg-blue-600 text-white font-medium py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300">Crop & Use</button>
            </div>
        </div>
    </div>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex flex-col min-h-screen">
        <div class="flex-1 overflow-y-auto" style="margin-left:250px;">
            <header class="bg-white shadow-sm p-4 sticky top-0 z-10">
                <div class="container mx-auto flex flex-col max-w-7xl">
                    <div class="flex items-center">
                        <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-image mr-3 text-blue-600 text-3xl"></i>
                            <span>Manage Banners</span>
                        </h1>
                    </div>
                    <p class="text-gray-600 mt-2 text-base">These banners are for <span class="font-semibold text-blue-700">
                        <?php echo isset($_SESSION['branch_name']) ? htmlspecialchars($_SESSION['branch_name']) : 'this branch'; ?>
                    </span> and will be used as the carousel for the shop homepage.</p>
                </div>
            </header>
            <main class="container mx-auto py-8 px-4 max-w-7xl">
                <div class="banner-container-grid">
                    <div class="flex flex-col gap-2">
                        <span class="font-semibold text-gray-700 text-lg mb-1">1st Banner</span>
                        <div id="banner-slot-1" class="banner-slot bg-white rounded-2xl shadow-lg p-8 relative flex flex-col justify-between border border-blue-100">
                            <div class="banner-placeholder flex flex-col items-center justify-center p-12 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-gray-400 transition-colors duration-300">
                                <span class="text-4xl font-light mb-2">+</span>
                                <p class="text-sm font-medium">Add Banner</p>
                            </div>
                            <div class="banner-image-container hidden rounded-lg overflow-hidden relative">
                                <img src="" alt="Banner Image" class="w-full h-auto rounded-lg">
                                <div class="absolute top-4 left-4 right-4 flex justify-end space-x-2">
                                    <button class="change-btn bg-gray-800 text-white text-xs md:text-sm font-medium py-1.5 px-3 rounded-md hover:bg-gray-700 transition duration-300">Change</button>
                                    <button class="delete-btn bg-red-600 text-white text-xs md:text-sm font-medium py-1.5 px-3 rounded-md hover:bg-red-700 transition duration-300">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <span class="font-semibold text-gray-700 text-lg mb-1">2nd Banner</span>
                        <div id="banner-slot-2" class="banner-slot bg-white rounded-2xl shadow-lg p-8 relative flex flex-col justify-between border border-blue-100">
                            <div class="banner-placeholder flex flex-col items-center justify-center p-12 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-gray-400 transition-colors duration-300">
                                <span class="text-4xl font-light mb-2">+</span>
                                <p class="text-sm font-medium">Add Banner</p>
                            </div>
                            <div class="banner-image-container hidden rounded-lg overflow-hidden relative">
                                <img src="" alt="Banner Image" class="w-full h-auto rounded-lg">
                                <div class="absolute top-4 left-4 right-4 flex justify-end space-x-2">
                                    <button class="change-btn bg-gray-800 text-white text-xs md:text-sm font-medium py-1.5 px-3 rounded-md hover:bg-gray-700 transition duration-300">Change</button>
                                    <button class="delete-btn bg-red-600 text-white text-xs md:text-sm font-medium py-1.5 px-3 rounded-md hover:bg-red-700 transition duration-300">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <span class="font-semibold text-gray-700 text-lg mb-1">3rd Banner</span>
                        <div id="banner-slot-3" class="banner-slot bg-white rounded-2xl shadow-lg p-8 relative flex flex-col justify-between border border-blue-100">
                            <div class="banner-placeholder flex flex-col items-center justify-center p-12 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-gray-400 transition-colors duration-300">
                                <span class="text-4xl font-light mb-2">+</span>
                                <p class="text-sm font-medium">Add Banner</p>
                            </div>
                            <div class="banner-image-container hidden rounded-lg overflow-hidden relative">
                                <img src="" alt="Banner Image" class="w-full h-auto rounded-lg">
                                <div class="absolute top-4 left-4 right-4 flex justify-end space-x-2">
                                    <button class="change-btn bg-gray-800 text-white text-xs md:text-sm font-medium py-1.5 px-3 rounded-md hover:bg-gray-700 transition duration-300">Change</button>
                                    <button class="delete-btn bg-red-600 text-white text-xs md:text-sm font-medium py-1.5 px-3 rounded-md hover:bg-red-700 transition duration-300">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <span class="font-semibold text-gray-700 text-lg mb-1">4th Banner</span>
                        <div id="banner-slot-4" class="banner-slot bg-white rounded-2xl shadow-lg p-8 relative flex flex-col justify-between border border-blue-100">
                            <div class="banner-placeholder flex flex-col items-center justify-center p-12 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-gray-400 transition-colors duration-300">
                                <span class="text-4xl font-light mb-2">+</span>
                                <p class="text-sm font-medium">Add Banner</p>
                            </div>
                            <div class="banner-image-container hidden rounded-lg overflow-hidden relative">
                                <img src="" alt="Banner Image" class="w-full h-auto rounded-lg">
                                <div class="absolute top-4 left-4 right-4 flex justify-end space-x-2">
                                    <button class="change-btn bg-gray-800 text-white text-xs md:text-sm font-medium py-1.5 px-3 rounded-md hover:bg-gray-700 transition duration-300">Change</button>
                                    <button class="delete-btn bg-red-600 text-white text-xs md:text-sm font-medium py-1.5 px-3 rounded-md hover:bg-red-700 transition duration-300">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Confirmation Modal (for delete/change) -->
    <div id="confirm-modal" class="modal fixed inset-0 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-2xl shadow-2xl p-8 m-4 max-w-md w-full flex flex-col items-center relative">
            <div id="confirm-modal-icon" class="rounded-full p-4 mb-4 flex items-center justify-center" style="background: #fef2f2;">
                <svg id="confirm-modal-svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3.042l-6.928-12.857a1.996 1.996 0 00-3.464 0L3.342 16.958A2.001 2.001 0 005.074 21z" />
                </svg>
            </div>
            <h3 id="confirm-modal-title" class="text-xl font-bold text-gray-900 mb-2 text-center">Confirm Action</h3>
            <p id="confirm-modal-message" class="text-base text-gray-600 text-center mb-6">Are you sure you want to proceed?</p>
            <div class="flex justify-center space-x-4 w-full">
                <button id="confirm-modal-cancel" class="flex-1 bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg hover:bg-gray-300 transition duration-200">Cancel</button>
                <button id="confirm-modal-confirm" class="flex-1 bg-red-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-red-700 transition duration-200">Yes, Continue</button>
            </div>
            <button id="confirm-modal-close" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
    </div>

    <script>

        document.addEventListener('DOMContentLoaded', () => {
            const bannerSlots = document.querySelectorAll('.banner-slot');
            // Confirmation modal elements
            const confirmModal = document.getElementById('confirm-modal');
            const confirmModalTitle = document.getElementById('confirm-modal-title');
            const confirmModalMessage = document.getElementById('confirm-modal-message');
            const confirmModalCancel = document.getElementById('confirm-modal-cancel');
            const confirmModalConfirm = document.getElementById('confirm-modal-confirm');
            const confirmModalClose = document.getElementById('confirm-modal-close');
            const confirmModalIcon = document.getElementById('confirm-modal-icon');
            const confirmModalSvg = document.getElementById('confirm-modal-svg');
            let confirmCallback = null;
            // Cropper modal elements
            const cropperModal = document.getElementById('cropper-modal');
            const cropperImage = document.getElementById('cropper-image');
            const cropperCancel = document.getElementById('cropper-cancel');
            const cropperConfirm = document.getElementById('cropper-confirm');
            let cropper = null;
            let cropperCallback = null;

            // Show confirmation modal
            function showConfirmModal({title, message, type = 'delete', onConfirm}) {
                confirmModalTitle.textContent = title;
                confirmModalMessage.textContent = message;
                confirmCallback = onConfirm;
                // Icon and color
                if (type === 'delete') {
                    confirmModalIcon.style.background = '#fef2f2';
                    confirmModalSvg.classList.remove('text-yellow-500');
                    confirmModalSvg.classList.add('text-red-600');
                    confirmModalSvg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3.042l-6.928-12.857a1.996 1.996 0 00-3.464 0L3.342 16.958A2.001 2.001 0 005.074 21z" />';
                } else if (type === 'change') {
                    confirmModalIcon.style.background = '#fefce8';
                    confirmModalSvg.classList.remove('text-red-600');
                    confirmModalSvg.classList.add('text-yellow-500');
                    confirmModalSvg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 8v.01" />';
                }
                confirmModal.classList.remove('hidden');
            }

            function hideConfirmModal() {
                confirmModal.classList.add('hidden');
                confirmCallback = null;
            }

            confirmModalCancel.addEventListener('click', hideConfirmModal);
            confirmModalClose.addEventListener('click', hideConfirmModal);
            confirmModalConfirm.addEventListener('click', () => {
                if (typeof confirmCallback === 'function') confirmCallback();
                hideConfirmModal();
            });

            // Load banners from server and display
            fetch('processes/get_branch_banners.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.banners) {
                        Object.entries(data.banners).forEach(([order, img]) => {
                            const slot = document.getElementById('banner-slot-' + order);
                            if (slot) setBannerImage(slot, img, false); // false: don't save again
                        });
                    }
                });

            // Helper: check if image is 1920x1080
            function is1920x1080(img) {
                return img.naturalWidth === 1920 && img.naturalHeight === 1080;
            }

            // Show cropper modal
            function showCropperModal(imageDataUrl, callback) {
                cropperImage.src = imageDataUrl;
                cropperModal.classList.remove('hidden');
                cropperCallback = callback;
                // Wait for image to load before initializing cropper
                cropperImage.onload = function() {
                    if (cropper) cropper.destroy();
                    cropper = new Cropper(cropperImage, {
                        aspectRatio: 16 / 9,
                        viewMode: 1,
                        autoCropArea: 1,
                        minCropBoxWidth: 192,
                        minCropBoxHeight: 108,
                        ready() {
                            // Set crop box to 1920x1080 if possible
                            const imgData = cropper.getImageData();
                            const scale = imgData.naturalWidth / 1920;
                            if (imgData.naturalWidth >= 1920 && imgData.naturalHeight >= 1080) {
                                cropper.setCropBoxData({
                                    width: 1920 * scale,
                                    height: 1080 * scale
                                });
                            }
                        }
                    });
                };
            }

            // Hide cropper modal
            function hideCropperModal() {
                cropperModal.classList.add('hidden');
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                cropperImage.src = '';
                cropperCallback = null;
            }

            cropperCancel.addEventListener('click', hideCropperModal);
            cropperConfirm.addEventListener('click', function() {
                if (cropper && cropperCallback) {
                    // Get cropped image as DataURL (1920x1080)
                    const croppedDataUrl = cropper.getCroppedCanvas({width:1920, height:1080}).toDataURL('image/jpeg', 0.95);
                    cropperCallback(croppedDataUrl);
                }
                hideCropperModal();
            });

            // Reusable function to handle file upload
            const handleFileUpload = (slot) => {
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.accept = 'image/*';
                fileInput.style.display = 'none';
                document.body.appendChild(fileInput);

                fileInput.click();

                fileInput.addEventListener('change', (event) => {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            // Create temp image to check size
                            const tempImg = new window.Image();
                            tempImg.onload = function() {
                                if (is1920x1080(tempImg)) {
                                    // Use as is
                                    setBannerImage(slot, e.target.result);
                                } else {
                                    // Show cropper modal
                                    showCropperModal(e.target.result, (croppedDataUrl) => {
                                        setBannerImage(slot, croppedDataUrl);
                                    });
                                }
                            };
                            tempImg.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                    document.body.removeChild(fileInput);
                });
            };

            // Set banner image in slot
            function setBannerImage(slot, dataUrl, save = true) {
                const placeholder = slot.querySelector('.banner-placeholder');
                const imageContainer = slot.querySelector('.banner-image-container');
                const image = imageContainer.querySelector('img');
                image.src = dataUrl;
                placeholder.classList.add('hidden');
                imageContainer.classList.remove('hidden');

                if (save) {
                    // Determine order from slot id (e.g., banner-slot-1 => 1)
                    let order = 1;
                    const idMatch = slot.id && slot.id.match(/banner-slot-(\d+)/);
                    if (idMatch) order = parseInt(idMatch[1]);

                    // Confirm update if already has image
                    if (image.dataset.hasImage === '1') {
                        showConfirmModal({
                            title: 'Change Banner',
                            message: 'Are you sure you want to change this banner? This will replace the current image.',
                            type: 'change',
                            onConfirm: () => saveBanner(slot, dataUrl, order, image)
                        });
                        return;
                    }
                    saveBanner(slot, dataUrl, order, image);
                } else {
                    image.dataset.hasImage = '1';
                }
            }

            function saveBanner(slot, dataUrl, order, image) {
                fetch('processes/process_save_banner.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `image=${encodeURIComponent(dataUrl)}&order=${order}`
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        alert('Failed to save banner: ' + (data.message || 'Unknown error'));
                    } else {
                        image.dataset.hasImage = '1';
                    }
                })
                .catch(() => {
                    alert('Failed to save banner due to network/server error.');
                });
            }

            // Function to perform the actual deletion
            const deleteBanner = (slot) => {
                let order = 1;
                const idMatch = slot.id && slot.id.match(/banner-slot-(\d+)/);
                if (idMatch) order = parseInt(idMatch[1]);
                showConfirmModal({
                    title: 'Delete Banner',
                    message: 'Are you sure you want to delete this banner? This action cannot be undone.',
                    type: 'delete',
                    onConfirm: () => {
                        fetch('processes/process_save_banner.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `image=&order=${order}`
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success) {
                                alert('Failed to delete banner: ' + (data.message || 'Unknown error'));
                            } else {
                                const placeholder = slot.querySelector('.banner-placeholder');
                                const imageContainer = slot.querySelector('.banner-image-container');
                                const image = imageContainer.querySelector('img');
                                image.src = "";
                                image.dataset.hasImage = '';
                                imageContainer.classList.add('hidden');
                                placeholder.classList.remove('hidden');
                            }
                        })
                        .catch(() => {
                            alert('Failed to delete banner due to network/server error.');
                        });
                    }
                });
            };

            // Event listeners for each banner slot
            bannerSlots.forEach(slot => {
                const placeholder = slot.querySelector('.banner-placeholder');
                const changeBtn = slot.querySelector('.change-btn');
                const deleteBtn = slot.querySelector('.delete-btn');
                // Add Banner
                placeholder.addEventListener('click', () => handleFileUpload(slot));
                // Change Banner
                changeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    showConfirmModal({
                        title: 'Change Banner',
                        message: 'Are you sure you want to change this banner? This will replace the current image.',
                        type: 'change',
                        onConfirm: () => handleFileUpload(slot)
                    });
                });
                // Delete Banner
                deleteBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    deleteBanner(slot);
                });
            });

            // Modal button event listeners
            cancelDeleteBtn.addEventListener('click', () => {
                deleteModal.classList.add('hidden');
                currentSlotToDelete = null;
            });

            confirmDeleteBtn.addEventListener('click', () => {
                if (currentSlotToDelete) {
                    deleteBanner(currentSlotToDelete);
                }
                deleteModal.classList.add('hidden');
                currentSlotToDelete = null;
            });
        });
    </script>

</body>
</html>