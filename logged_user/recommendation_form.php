<?php
// recommendation_form.php
?>

<div id="recommendationModalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
  <div id="recommendationModalBox" class="bg-white rounded-2xl shadow-2xl w-[95vw] max-w-sm md:max-w-md max-h-[80vh] overflow-hidden transform scale-90 opacity-0 transition-all duration-300 border-2 border-accent">
    <!-- Header -->
    <div class="bg-primary text-white p-5 flex items-center justify-center relative">
      <h3 class="text-xl md:text-2xl font-extrabold tracking-wide text-center w-full drop-shadow-lg">Personalize Your Tile Experience</h3>
    </div>
    <!-- Content -->
    <div class="p-5 md:p-6 overflow-y-auto max-h-[50vh] flex flex-col items-center">
      <p class="text-textdark mb-5 text-center text-base font-medium">Select <span class="text-primary font-bold">3</span> tile categories you love most to help us recommend the perfect tiles for you.<br><span class="text-xs text-textlight">(Selection is required to continue)</span></p>
      <div class="grid grid-cols-2 gap-4 mb-6 w-full">
        <!-- Category options will be populated by JavaScript -->
      </div>
      <div class="flex items-center mb-6 w-full">
        <div class="w-full bg-gray-200 rounded-full h-2.5">
          <div id="selectionProgress" class="bg-secondary h-2.5 rounded-full transition-all duration-500" style="width: 0%"></div>
        </div>
        <span id="selectionCount" class="ml-4 text-sm font-bold text-primary">0/3</span>
      </div>
    </div>
    <!-- Footer -->
    <div class="bg-gray-100 p-5 flex justify-center">
      <button id="submitRecommendations" disabled class="bg-gray-400 text-white px-6 py-2 rounded-full font-semibold transition-all duration-300 hover:scale-105 transform disabled:opacity-50 disabled:cursor-not-allowed">
        Submit Preferences
      </button>
    </div>
  </div>
</div>

<script>
// Category data
const tileCategories = [
  { id: 'ceramic', name: 'Ceramic Tiles', icon: 'fa-shapes' },
  { id: 'porcelain', name: 'Porcelain Tiles', icon: 'fa-gem' },
  { id: 'mosaic', name: 'Mosaic Tiles', icon: 'fa-puzzle-piece' },
  { id: 'natural_stone', name: 'Natural Stone', icon: 'fa-mountain' },
  { id: 'outdoor', name: 'Outdoor Tiles', icon: 'fa-tree' },
  { id: 'premium', name: 'Premium Tiles', icon: 'fa-crown' }
];

// Initialize the recommendation modal
document.addEventListener('DOMContentLoaded', function() {
  const modalOverlay = document.getElementById('recommendationModalOverlay');
  const modalBox = document.getElementById('recommendationModalBox');
  // No close button
  const submitBtn = document.getElementById('submitRecommendations');
  const categoryContainer = document.querySelector('#recommendationModalOverlay .grid');
  const progressBar = document.getElementById('selectionProgress');
  const selectionCount = document.getElementById('selectionCount');
  
  let selectedCategories = [];
  let recommendationModalShown = false;
  let maxSelections = 3;
  
  // Check if we should show the recommendation modal
  function checkShowRecommendationModal() {
    // Only show if referral was completed and recommendation not yet shown
    const referralCompleted = localStorage.getItem('referralCompleted');
    const recommendationShown = localStorage.getItem('recommendationShown');
    
    if (referralCompleted === 'true' && !recommendationShown && !recommendationModalShown) {
      setTimeout(openRecommendationModal, 1000); // Show after 1 second
      recommendationModalShown = true;
    }
  }
  
  // Populate category options
  function populateCategories() {
    categoryContainer.innerHTML = '';
    tileCategories.forEach(category => {
      const categoryElement = document.createElement('div');
      categoryElement.className = 'category-option relative flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-300 hover:border-primary hover:shadow-md bg-white group';
      categoryElement.dataset.id = category.id;
      categoryElement.innerHTML = `
        <div class="flex items-center justify-center w-12 h-12 md:w-14 md:h-14 rounded-full bg-gray-100 shadow mb-2 group-hover:scale-110 transition-transform duration-300">
          <i class="fas ${category.icon} text-2xl md:text-3xl text-secondary group-hover:text-primary transition-colors duration-300"></i>
        </div>
        <span class="text-sm md:text-base font-bold text-center text-textdark mb-1">${category.name}</span>
        <div class="checkmark absolute top-2 right-2 w-6 h-6 rounded-full bg-secondary flex items-center justify-center transition-all duration-300 opacity-0 shadow-md">
          <i class="fas fa-check text-white text-xs"></i>
        </div>
      `;
      categoryElement.addEventListener('click', () => toggleCategory(category.id));
      categoryContainer.appendChild(categoryElement);
    });
  }
  
  // Toggle category selection
  function toggleCategory(categoryId) {
    const categoryElement = document.querySelector(`.category-option[data-id="${categoryId}"]`);
    // If already disabled, do nothing
    if (categoryElement.classList.contains('unclickable')) return;
    if (selectedCategories.includes(categoryId)) {
      // Deselect
      selectedCategories = selectedCategories.filter(id => id !== categoryId);
      categoryElement.classList.remove('border-secondary', 'ring-4', 'ring-secondary', 'bg-accent/10');
      categoryElement.querySelector('.checkmark').classList.remove('opacity-100');
      categoryElement.querySelector('i:first-child').classList.remove('text-primary');
    } else if (selectedCategories.length < maxSelections) {
      // Select (if under limit)
      selectedCategories.push(categoryId);
      categoryElement.classList.add('border-secondary', 'ring-4', 'ring-secondary', 'bg-accent/10');
      categoryElement.querySelector('.checkmark').classList.add('opacity-100');
      categoryElement.querySelector('i:first-child').classList.add('text-primary');
      // Add animation on selection
      categoryElement.style.transform = 'scale(1.08)';
      setTimeout(() => {
        categoryElement.style.transform = 'scale(1)';
      }, 200);
    }
    // Update progress and clickable state
    updateSelectionProgress();
    updateOptionClickability();
  }

  // Make unselected options unclickable if 3 are selected
  function updateOptionClickability() {
    const allOptions = document.querySelectorAll('.category-option');
    if (selectedCategories.length >= maxSelections) {
      allOptions.forEach(opt => {
        if (!selectedCategories.includes(opt.dataset.id)) {
          opt.classList.add('unclickable', 'opacity-50', 'cursor-not-allowed');
        }
      });
    } else {
      allOptions.forEach(opt => {
        opt.classList.remove('unclickable', 'opacity-50', 'cursor-not-allowed');
      });
    }
  }
  
  // Update selection progress
  function updateSelectionProgress() {
    const progress = Math.min(selectedCategories.length, maxSelections);
    const percentage = (progress / maxSelections) * 100;
    progressBar.style.width = `${percentage}%`;
    selectionCount.textContent = `${progress}/3`;
    // Enable/disable submit button
    submitBtn.disabled = progress !== maxSelections;
    submitBtn.classList.toggle('bg-gray-400', progress !== maxSelections);
    submitBtn.classList.toggle('bg-primary', progress === maxSelections);
  }
  
  // Open recommendation modal
  function openRecommendationModal() {
    if (modalOverlay) {
      modalOverlay.style.display = 'flex';
      document.body.style.overflow = 'hidden';
      
      setTimeout(() => {
        modalBox.classList.remove('scale-90', 'opacity-0');
        modalBox.classList.add('scale-100', 'opacity-100');
      }, 10);
      
      // Mark as shown
      localStorage.setItem('recommendationShown', 'true');
    }
  }
  
  // Close recommendation modal
  function closeRecommendationModal() {
    if (modalBox && modalOverlay) {
      modalBox.classList.remove('scale-100', 'opacity-100');
      modalBox.classList.add('scale-90', 'opacity-0');
      
      setTimeout(() => {
        modalOverlay.style.display = 'none';
        document.body.style.overflow = '';
      }, 250);
    }
  }
  
  // Submit recommendations
  function submitRecommendations() {
    if (selectedCategories.length === maxSelections) {
      // Close modal immediately
      closeRecommendationModal();
      // Send data to server via AJAX
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'save_recommendations.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          // Show success message (modal already closed)
          Swal.fire({
            title: 'Preferences Saved!',
            text: 'Thank you for helping us personalize your experience.',
            icon: 'success',
            confirmButtonColor: '#7d310a'
          });
        }
      };
      xhr.send('categories=' + JSON.stringify(selectedCategories));
    }
  }
  
  // No close button, no outside click to close
  if (submitBtn) {
    submitBtn.addEventListener('click', submitRecommendations);
  }
  
  // Populate categories and check if modal should be shown
  populateCategories();
  updateOptionClickability();
  
  // Check if we should show the modal (poll every second until shown or page changes)
  const checkInterval = setInterval(checkShowRecommendationModal, 1000);
  
  // Clear interval after 30 seconds to prevent endless checking
  setTimeout(() => {
    clearInterval(checkInterval);
  }, 30000);
});
</script>