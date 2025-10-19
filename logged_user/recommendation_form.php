
<?php
// recommendation_form.php
$sessionAlready = session_status() === PHP_SESSION_ACTIVE;
if (!$sessionAlready) session_start();
require_once __DIR__ . '/../connection/connection.php';
$showRecommendationModal = true;
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  // Check if user already has design preferences
  $stmt = $conn->prepare('SELECT COUNT(*) FROM user_design_preferences WHERE user_id = ?');
  $stmt->execute([$user_id]);
  if ($stmt->fetchColumn() > 0) {
    $showRecommendationModal = false;
  }
}
?>
<?php if ($showRecommendationModal): ?>

<!-- Unskippable Video Overlay -->
</div>
<div id="ralttVideoOverlay" style="display:none;position:fixed;z-index:10000;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.95);align-items:center;justify-content:center;">
  <video id="ralttVideoPlayer" src="../images/raltt.mp4" style="max-width:90vw;max-height:90vh;outline:none;box-shadow:0 0 40px #000;" playsinline preload="auto"></video>
</div>

<div id="recommendationModalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center hidden">
  <div id="recommendationModalBox" class="bg-white rounded-2xl shadow-2xl w-[95vw] max-w-sm md:max-w-lg max-h-[80vh] overflow-hidden transform scale-90 opacity-0 transition-all duration-300 border-2 border-accent">
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
    <div class="bg-gray-100 p-5 flex flex-col items-center justify-center">
      <button id="submitRecommendations" disabled class="bg-gray-400 text-white px-6 py-2 rounded-full font-semibold transition-all duration-300 hover:scale-105 transform disabled:opacity-50 disabled:cursor-not-allowed mb-2">
        Submit Preferences
      </button>
      <div id="recommendationSuccessMsg" style="display:none;" class="mt-2 text-center px-4 py-2 rounded-lg bg-green-100 border border-green-300 text-green-800 font-semibold shadow">
        <!-- Success message will appear here -->
      </div>
    </div>
  </div>
</div>

<script>
// Design data (updated)
const tileDesigns = [
  { id: 'minimalist', name: 'Minimalist', icon: 'fa-border-all' },
  { id: 'floral', name: 'Floral', icon: 'fa-seedling' },
  { id: 'black_white', name: 'Black and White', icon: 'fa-palette' },
  { id: 'modern', name: 'Modern', icon: 'fa-cube' },
  { id: 'rustic', name: 'Rustic', icon: 'fa-mountain' },
  { id: 'geometric', name: 'Geometric', icon: 'fa-shapes' }
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
    tileDesigns.forEach(design => {
      const categoryElement = document.createElement('div');
      categoryElement.className = 'category-option relative flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-300 hover:border-primary hover:shadow-md bg-white group';
      categoryElement.dataset.id = design.id;
      categoryElement.innerHTML = `
        <div class="flex items-center justify-center w-12 h-12 md:w-14 md:h-14 rounded-full bg-gray-100 shadow mb-2 group-hover:scale-110 transition-transform duration-300">
          <i class="fas ${design.icon} text-2xl md:text-3xl text-secondary group-hover:text-primary transition-colors duration-300"></i>
        </div>
        <span class="text-sm md:text-base font-bold text-center text-textdark mb-1">${design.name}</span>
        <div class="selection-badge absolute top-2 right-2 w-8 h-8 rounded-full flex items-center justify-center transition-all duration-300 opacity-0 shadow-md text-xs font-bold"></div>
      `;
      categoryElement.addEventListener('click', () => toggleCategory(design.id));
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
      categoryElement.querySelector('.selection-badge').classList.remove('opacity-100');
      categoryElement.querySelector('.selection-badge').textContent = '';
      categoryElement.querySelector('.selection-badge').style.background = '';
      categoryElement.querySelector('i:first-child').classList.remove('text-primary');
    } else if (selectedCategories.length < maxSelections) {
      // Select (if under limit)
      selectedCategories.push(categoryId);
      categoryElement.classList.add('border-secondary', 'ring-4', 'ring-secondary', 'bg-accent/10');
      categoryElement.querySelector('i:first-child').classList.add('text-primary');
      // Add animation on selection
      categoryElement.style.transform = 'scale(1.08)';
      setTimeout(() => {
        categoryElement.style.transform = 'scale(1)';
      }, 200);
    }
    updateSelectionBadges();
    // Update progress and clickable state
    updateSelectionProgress();
    updateOptionClickability();
  }

  // Update selection badges (1st, 2nd, 3rd)
  function updateSelectionBadges() {
    document.querySelectorAll('.category-option').forEach(opt => {
      const badge = opt.querySelector('.selection-badge');
      const idx = selectedCategories.indexOf(opt.dataset.id);
      if (idx !== -1) {
        badge.classList.add('opacity-100');
        let label = '';
        let bg = '';
        if (idx === 0) { label = '1st'; bg = 'linear-gradient(135deg,#FFD700,#FFEF8A)'; } // gold
        else if (idx === 1) { label = '2nd'; bg = 'linear-gradient(135deg,#C0C0C0,#E0E0E0)'; } // silver
        else if (idx === 2) { label = '3rd'; bg = 'linear-gradient(135deg,#cd7f32,#e3b778)'; } // bronze
        badge.textContent = label;
        badge.style.background = bg;
        badge.style.color = '#fff';
        badge.style.border = '2px solid #fff';
        badge.style.boxShadow = '0 2px 6px rgba(0,0,0,0.15)';
      } else {
        badge.classList.remove('opacity-100');
        badge.textContent = '';
        badge.style.background = '';
      }
    });
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
  function ordinal(n) {
    if (n === 1) return '1st';
    if (n === 2) return '2nd';
    if (n === 3) return '3rd';
    return `${n}th`;
  }
  function updateSelectionProgress() {
    const progress = Math.min(selectedCategories.length, maxSelections);
    const percentage = (progress / maxSelections) * 100;
    progressBar.style.width = `${percentage}%`;
    selectionCount.textContent = progress > 0 ? `${ordinal(progress)}/3` : '0/3';
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
      // Send data to server via AJAX
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'processes/save_recommendations.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          const msgDiv = document.getElementById('recommendationSuccessMsg');
          if (xhr.status === 200) {
            // Play the unskippable video overlay
            showRalttVideoOverlay();
            // Hide modal immediately
            if (modalOverlay && modalBox) {
              modalBox.classList.remove('scale-100', 'opacity-100');
              modalBox.classList.add('scale-90', 'opacity-0');
              setTimeout(() => {
                modalOverlay.style.display = 'none';
                document.body.style.overflow = '';
              }, 250);
            }
          } else {
            msgDiv.innerHTML = '<span style="color:#b91c1c; font-weight:bold;">An error occurred. Please try again.</span>';
            msgDiv.style.display = 'block';
          }
        }
      };
      xhr.send('categories=' + JSON.stringify(selectedCategories));
    }
  }

  // Show the unskippable video overlay
  function showRalttVideoOverlay() {
    const overlay = document.getElementById('ralttVideoOverlay');
    const video = document.getElementById('ralttVideoPlayer');
    if (!overlay || !video) return;
    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    overlay.focus();
    // Remove controls, disable right-click, pause, etc.
    video.controls = false;
    video.currentTime = 0;
    video.playbackRate = 1;
  video.muted = false; // Enable sound after user interaction
    video.setAttribute('disablePictureInPicture', 'true');
    video.setAttribute('controlsList', 'nodownload nofullscreen noremoteplayback');
    video.removeEventListener('contextmenu', preventContextMenu);
    video.addEventListener('contextmenu', preventContextMenu);
    video.removeEventListener('seeking', preventSeeking);
    video.addEventListener('seeking', preventSeeking);
    video.removeEventListener('pause', preventPause);
    video.addEventListener('pause', preventPause);
    video.onended = function() {
      overlay.style.display = 'none';
      document.body.style.overflow = '';
    };
    overlay.tabIndex = 0;
    overlay.onkeydown = function(e) {
      e.preventDefault();
      e.stopPropagation();
      return false;
    };
    // Only play when explicitly triggered after modal closes
    video.load();
    setTimeout(function() {
      video.play().catch(()=>{});
    }, 100);
    overlay.focus();
  }

  function preventContextMenu(e) { e.preventDefault(); }
  function preventSeeking(e) {
    const video = e.target;
    video.currentTime = Math.max(0, Math.min(video.currentTime, video.duration));
  }
  function preventPause(e) {
    const video = e.target;
    if (!video.ended) video.play();
  }
  
  // No close button, no outside click to close
  if (submitBtn) {
    submitBtn.addEventListener('click', submitRecommendations);
  }
  
  // Populate categories and check if modal should be shown
  populateCategories();
  updateOptionClickability();
  updateSelectionBadges();
  
  // Check if we should show the modal (poll every second until shown or page changes)
  const checkInterval = setInterval(checkShowRecommendationModal, 1000);
  
  // Clear interval after 30 seconds to prevent endless checking
  setTimeout(() => {
    clearInterval(checkInterval);
  }, 30000);
  });
  <?php endif; ?>
</script>