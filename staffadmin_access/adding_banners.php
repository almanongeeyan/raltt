<?php include '../includes/sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Banners</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .banner-slot {
            transition: all 0.3s ease-in-out;
            cursor: pointer;
        }
        .banner-slot:hover .banner-placeholder {
            @apply bg-gray-100;
        }
        .modal {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
        <div class="flex flex-col md:flex-row min-h-screen">

                <div class="flex-1 overflow-y-auto">
            <header class="bg-white shadow-sm p-4 sticky top-0 z-10">
                <div class="container mx-auto flex justify-between items-center max-w-7xl">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Manage Banners</h1>
                </div>
            </header>

    <main class="container mx-auto py-8 px-4 max-w-7xl">
        <div class="flex flex-col items-center justify-start space-y-6">
            <div id="banner-slot-1" class="banner-slot bg-white rounded-xl shadow-lg p-6 relative w-full md:w-4/5 lg:w-3/4">
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

            <div id="banner-slot-2" class="banner-slot bg-white rounded-xl shadow-lg p-6 relative w-full md:w-4/5 lg:w-3/4">
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

            <div id="banner-slot-3" class="banner-slot bg-white rounded-xl shadow-lg p-6 relative w-full md:w-4/5 lg:w-3/4">
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

            <div id="banner-slot-4" class="banner-slot bg-white rounded-xl shadow-lg p-6 relative w-full md:w-4/5 lg:w-3/4">
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
    </main>
    
    <div id="delete-modal" class="modal fixed inset-0 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 m-4 max-w-sm w-full">
            <div class="flex flex-col items-center space-y-4">
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3.042l-6.928-12.857a1.996 1.996 0 00-3.464 0L3.342 16.958A2.001 2.001 0 005.074 21z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Confirm Deletion</h3>
                <p class="text-sm text-gray-500 text-center">Are you sure you want to delete this banner? This action cannot be undone.</p>
                <div class="flex justify-center space-x-4 w-full">
                    <button id="cancel-delete-btn" class="flex-1 bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg hover:bg-gray-300 transition duration-300">Cancel</button>
                    <button id="confirm-delete-btn" class="flex-1 bg-red-600 text-white font-medium py-2 px-4 rounded-lg hover:bg-red-700 transition duration-300">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bannerSlots = document.querySelectorAll('.banner-slot');
            const deleteModal = document.getElementById('delete-modal');
            const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
            const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
            
            let currentSlotToDelete = null;

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
                            const placeholder = slot.querySelector('.banner-placeholder');
                            const imageContainer = slot.querySelector('.banner-image-container');
                            const image = imageContainer.querySelector('img');

                            image.src = e.target.result;
                            placeholder.classList.add('hidden');
                            imageContainer.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                    document.body.removeChild(fileInput);
                });
            };

            // Function to perform the actual deletion
            const deleteBanner = (slot) => {
                const placeholder = slot.querySelector('.banner-placeholder');
                const imageContainer = slot.querySelector('.banner-image-container');
                const image = imageContainer.querySelector('img');
                
                image.src = "";
                imageContainer.classList.add('hidden');
                placeholder.classList.remove('hidden');
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
                    handleFileUpload(slot);
                });

                // Delete Banner - open modal
                deleteBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    currentSlotToDelete = slot;
                    deleteModal.classList.remove('hidden');
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