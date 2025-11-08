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

<script>
    window.CURRENT_USER_ID = <?php echo isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 'null'; ?>;
</script>

<div id="ralttVideoOverlay" style="display:none;position:fixed;z-index:10000;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.95);align-items:center;justify-content:center;">
    <video id="ralttVideoPlayer" src="../images/raltt.mp4" style="max-width:90vw;max-height:90vh;outline:none;box-shadow:0 0 40px #000;" playsinline preload="auto"></video>
</div>

<div id="recommendationModalOverlay" class="fixed inset-0 bg-black bg-opacity-70 z-[9999] flex items-center justify-center hidden animate-modal-fade">
    <div id="recommendationModalBox" class="bg-white rounded-2xl shadow-2xl w-[95vw] max-w-sm md:max-w-2xl max-h-[90vh] overflow-hidden transform scale-90 opacity-0 transition-all duration-300 border-2 border-accent animate-modal-slide">
        <div class="bg-primary text-white p-5 flex items-center justify-center relative">
            <h3 class="text-xl md:text-2xl font-extrabold tracking-wide text-center w-full drop-shadow-lg">Personalize Your Tile Experience</h3>
        </div>
        
        <div class="p-5 md:p-6 overflow-y-auto max-h-[60vh] flex flex-col items-center">
            <p class="text-textdark mb-5 text-center text-base font-medium">Select your <span class="text-primary font-bold">Top 3</span> styles so we can recommend the perfect tiles for you.<br><span class="text-xs text-textlight">(This helps us find your match!)</span></p>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 md:gap-4 mb-6 w-full" id="categoryGrid">
                </div>
            
            <div class="flex items-center mb-6 w-full px-2">
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div id="selectionProgress" class="bg-secondary h-2.5 rounded-full transition-all duration-500" style="width: 0%"></div>
                </div>
                <span id="selectionCount" class="ml-4 text-sm font-bold text-primary whitespace-nowrap">0 / 3</span>
            </div>
        </div>
        
        <div class="bg-gray-100 p-5 flex flex-col items-center justify-center border-t border-gray-200">
            <button id="submitRecommendations" disabled class="bg-gray-400 text-white px-8 py-3 rounded-full font-bold text-lg transition-all duration-300 hover:scale-105 transform disabled:opacity-50 disabled:cursor-not-allowed mb-2 animate-submit-btn">
                <span class="submit-btn-text">Submit Preferences</span>
            </button>
            <div id="recommendationMsg" style="display:none;" class="mt-2 text-center px-4 py-2 rounded-lg font-semibold shadow">
                </div>
        </div>
    </div>
</div>

<style>
    /* Modal Animations */
    .animate-modal-fade { animation: modalFadeIn 0.5s cubic-bezier(0.4,0,0.2,1); }
    @keyframes modalFadeIn { from { opacity: 0; } to { opacity: 1; } }
    
    .animate-modal-slide { animation: modalSlideIn 0.6s cubic-bezier(0.4,0,0.2,1); }
    @keyframes modalSlideIn { from { transform: translateY(-30px) scale(0.95); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }

    /* Redesigned Category Option */
    .category-option {
        transition: transform 0.3s, box-shadow 0.3s, filter 0.3s;
        aspect-ratio: 1 / 1; /* Make them square */
    }
    .category-option .bg-media {
        transition: transform 0.4s ease-out;
    }
    .category-option:hover .bg-media {
        transform: scale(1.1); /* Ken Burns zoom effect */
    }
    .category-option.selected {
        box-shadow: 0 0 0 4px #cf8756; /* Use your 'secondary' color */
        transform: scale(1.03);
    }
    /* Style for disabled (unselected) options when max is reached */
    .category-option.disabled {
        filter: grayscale(80%) opacity(60%);
        cursor: not-allowed;
    }
    
    /* Selection Badge (1st, 2nd, 3rd) */
    .selection-badge {
        transition: transform 0.3s, opacity 0.3s;
        transform: scale(0.8);
        opacity: 0;
    }
    .category-option.selected .selection-badge {
        transform: scale(1);
        opacity: 1;
    }

    /* Submit Button Pulse */
    .animate-submit-btn.enabled { animation: submitPulse 1.5s infinite; }
    @keyframes submitPulse {
        0% { box-shadow: 0 0 0 0 #cf875688; }
        70% { box-shadow: 0 0 0 12px #cf875600; }
        100% { box-shadow: 0 0 0 0 #cf875600; }
    }
    
    #selectionProgress { transition: width 0.5s cubic-bezier(0.4,0,0.2,1); }
</style>

<script>
// --- THIS IS THE FIX ---
// The 'id' fields are now numbers (1, 2, 3...) to match your
// 'tile_designs' table's primary key, which is likely an INT.
const tileDesigns = [
    { id: 1, name: 'Minimalist', icon: 'fa-border-all', image_url: '../images/user/minimalist.png', video_url: '../images/minimalist.mp4' },
    { id: 2, name: 'Floral', icon: 'fa-seedling', image_url: '../images/user/floral.jpg', video_url: '../images/floral.mp4' },
    { id: 3, name: 'Black & White', icon: 'fa-palette', image_url: '../images/user/b&w.jpg', video_url: '../images/blackandwhite.mp4' },
    { id: 4, name: 'Modern', icon: 'fa-cube', image_url: '../images/user/modern.jpg', video_url: '../images/modern.mp4' },
    { id: 5, name: 'Rustic', icon: 'fa-mountain', image_url: '../images/user/rustic.jpg', video_url: '../images/rustic.mp4' },
    { id: 6, name: 'Geometric', icon: 'fa-shapes', image_url: '../images/user/geometric.jpg', video_url: '../images/geometric.mp4' }
];

document.addEventListener('DOMContentLoaded', function() {
    const modalOverlay = document.getElementById('recommendationModalOverlay');
    const modalBox = document.getElementById('recommendationModalBox');
    const submitBtn = document.getElementById('submitRecommendations');
    const categoryContainer = document.getElementById('categoryGrid');
    const progressBar = document.getElementById('selectionProgress');
    const selectionCount = document.getElementById('selectionCount');
    const msgDiv = document.getElementById('recommendationMsg');

    let selectedCategories = [];
    let recommendationModalShown = false;
    const maxSelections = 3;

    function checkShowRecommendationModal() {
        const referralCompleted = localStorage.getItem('referralCompleted');
        const recommendationShown = localStorage.getItem('recommendationShown');
        
        if (referralCompleted === 'true' && !recommendationShown && !recommendationModalShown) {
            setTimeout(openRecommendationModal, 1000); // Show after 1 second
            recommendationModalShown = true;
        }
    }

    // --- REDESIGNED POPULATE FUNCTION ---
    function populateCategories() {
        categoryContainer.innerHTML = '';
        tileDesigns.forEach(design => {
            const categoryElement = document.createElement('div');
            categoryElement.className = 'category-option relative rounded-xl shadow-xl cursor-pointer overflow-hidden group border-2 border-gray-200 hover:border-primary transition-all duration-300';
            categoryElement.dataset.id = design.id; // This will now be a number

            // Disabled if maxSelections reached and not selected
            if (selectedCategories.length >= maxSelections && !selectedCategories.includes(design.id)) {
                categoryElement.classList.add('disabled');
            }

            let mediaHtml = '';
            if (selectedCategories.includes(design.id)) {
                mediaHtml = `
                    <video class="bg-media absolute inset-0 w-full h-full object-cover rounded-xl border-2 border-primary shadow-2xl" 
                           src="${design.video_url}" 
                           autoplay loop muted playsinline>
                    </video>
                `;
            } else {
                mediaHtml = `
                    <div class="bg-media absolute inset-0 w-full h-full bg-cover bg-center rounded-xl border-2 border-white shadow-lg group-hover:scale-105 transition-transform duration-300" 
                         style="background-image: url('${design.image_url}')"></div>
                `;
            }

            // Selection badge logic (always visible for selected)
            let badgeHtml = '';
            const idx = selectedCategories.indexOf(design.id);
            if (idx !== -1) {
                let label = '';
                let bg = '';
                if (idx === 0) { label = '1st'; bg = 'linear-gradient(135deg,#FFD700,#FFEF8A)'; }
                else if (idx === 1) { label = '2nd'; bg = 'linear-gradient(135deg,#C0C0C0,#E0E0E0)'; }
                else if (idx === 2) { label = '3rd'; bg = 'linear-gradient(135deg,#cd7f32,#e3b778)'; }
                badgeHtml = `<div class="selection-badge absolute top-2 left-2 w-8 h-8 rounded-full flex items-center justify-center shadow-md text-xs font-bold" style="background:${bg};color:#333;border:2px solid #fff;opacity:1;transform:scale(1);">${label}</div>`;
            } else {
                badgeHtml = `<div class="selection-badge absolute top-2 left-2 w-8 h-8 rounded-full flex items-center justify-center shadow-md text-xs font-bold" style="opacity:0;transform:scale(0.8);"></div>`;
            }

            categoryElement.innerHTML = `
                ${mediaHtml}
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent group-hover:from-black/20 transition-all duration-300 rounded-xl"></div>
                <div class="relative z-10 flex flex-col items-center justify-center h-full p-2 text-white text-shadow-lg">
                    <i class="fas ${design.icon} text-3xl md:text-4xl mb-2 drop-shadow-lg" style="text-shadow:0 2px 8px #000,0 0 2px #fff;"></i>
                    <span class="text-base md:text-lg font-extrabold text-center drop-shadow-lg" style="text-shadow:0 2px 8px #000,0 0 2px #fff;">${design.name}</span>
                </div>
                ${badgeHtml}
            `;

            categoryElement.addEventListener('click', () => toggleCategory(design.id));
            categoryContainer.appendChild(categoryElement);
        });
        // After initial render, keep videos playing and do not restart
        setTimeout(() => {
            document.querySelectorAll('.category-option video').forEach(video => {
                video.muted = true;
                video.loop = true;
                video.autoplay = true;
                video.playsInline = true;
                if (video.paused) video.play();
            });
        }, 100);
    }

    function toggleCategory(categoryId) { // categoryId will be a number now
        const categoryElement = document.querySelector(`.category-option[data-id="${categoryId}"]`);
        if (categoryElement.classList.contains('disabled')) return;

        if (selectedCategories.includes(categoryId)) {
            // Deselect
            selectedCategories = selectedCategories.filter(id => id !== categoryId);
        } else if (selectedCategories.length < maxSelections) {
            // Select
            selectedCategories.push(categoryId);
        }
        
        updateSelectionProgress();
        // Re-render categories to swap image/video and show/hide 'disabled' state
        populateCategories();
    }
    
    function ordinal(n) {
        if (n === 1) return '1st';
        if (n === 2) return '2nd';
        if (n === 3) return '3rd';
        return `${n}th`;
    }

    function updateSelectionProgress() {
        const progress = selectedCategories.length;
        const percentage = (progress / maxSelections) * 100;
        
        progressBar.style.width = `${percentage}%`;
        selectionCount.textContent = progress > 0 ? `${ordinal(progress)} / 3` : '0 / 3';
        
        submitBtn.disabled = progress !== maxSelections;
        submitBtn.classList.toggle('bg-gray-400', progress !== maxSelections);
        submitBtn.classList.toggle('bg-primary', progress === maxSelections);
        submitBtn.classList.toggle('enabled', progress === maxSelections);
    }

    function openRecommendationModal() {
        if (!modalOverlay) return;
        
        modalOverlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        setTimeout(() => {
            modalBox.classList.remove('scale-90', 'opacity-0');
            modalBox.classList.add('scale-100', 'opacity-100');
        }, 10);
        
        localStorage.setItem('recommendationShown', 'true');
    }

    function closeRecommendationModal() {
        if (!modalBox || !modalOverlay) return;

        modalBox.classList.remove('scale-100', 'opacity-100');
        modalBox.classList.add('scale-90', 'opacity-0');
        
        setTimeout(() => {
            modalOverlay.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }

    // --- REFACTORED SUBMIT FUNCTION (using async/await fetch) ---
    async function submitRecommendations() {
        if (selectedCategories.length !== maxSelections) return;
        
        submitBtn.disabled = true;
        submitBtn.querySelector('.submit-btn-text').textContent = 'Saving...';

        try {
            const formData = new URLSearchParams();
            // selectedCategories will now be an array of numbers, e.g., [1, 4, 5]
            formData.append('categories', JSON.stringify(selectedCategories)); 

            // 1. Check if user_id exists
            if (!window.CURRENT_USER_ID) {
                throw new Error('User is not logged in. Cannot save preferences.');
            }
            // 2. Add user_id to the data
            formData.append('user_id', window.CURRENT_USER_ID);

            // 3. Change URL to point to your new Flask server
            const response = await fetch('http://localhost:5000/save_preferences', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData
            });

            // 4. Get the JSON response from Flask
            const result = await response.json();

            // 5. Check if Flask reported an error
            if (!response.ok || !result.success) {
                // If Flask sent an error, show it
                throw new Error(result.message || 'An error occurred from the server.');
            }
            
            // Success!
            // Hide modal immediately and then show video
            closeRecommendationModal();
            setTimeout(showRalttVideoOverlay, 300); // Wait for modal fade-out

        } catch (error) {
            console.error('Submission error:', error);
            // Show the error message from Flask (or JS) to the user
            msgDiv.innerHTML = error.message;
            msgDiv.style.color = '#b91c1c'; // Red
            msgDiv.style.background = '#fee2e2'; // Light red
            msgDiv.style.borderColor = '#fca5a5'; // Red border
            msgDiv.style.display = 'block';
            
            // Re-enable button on error
            submitBtn.disabled = false;
            submitBtn.querySelector('.submit-btn-text').textContent = 'Submit Preferences';
        }
    }

    // --- Unskippable Video Overlay Logic (Unchanged) ---
    function showRalttVideoOverlay() {
        const overlay = document.getElementById('ralttVideoOverlay');
        const video = document.getElementById('ralttVideoPlayer');
        if (!overlay || !video) return;

        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        overlay.focus();
        
        video.controls = false;
        video.currentTime = 0;
        video.muted = false;
        video.setAttribute('disablePictureInPicture', 'true');
        video.setAttribute('controlsList', 'nodownload nofullscreen noremoteplayback');

        video.addEventListener('contextmenu', preventDefaultAction);
        video.addEventListener('seeking', preventSeeking);
        video.addEventListener('pause', preventPause);
        
        video.onended = function() {
            overlay.style.display = 'none';
            document.body.style.overflow = '';
            video.removeEventListener('contextmenu', preventDefaultAction);
            video.removeEventListener('seeking', preventSeeking);
            video.removeEventListener('pause', preventPause);
            overlay.onkeydown = null;
            window.location.reload();
        };
        
        overlay.tabIndex = 0;
        overlay.onkeydown = preventDefaultAction;

        video.load();
        video.play().catch(error => {
            console.warn("Video autoplay was blocked. User interaction may be required.", error);
            video.muted = true;
            video.play();
        });
        
        overlay.focus();
    }

    function preventDefaultAction(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }

    function preventSeeking(e) {
        const video = e.target;
        video.currentTime = Math.max(0, Math.min(video.currentTime, video.duration));
    }

    function preventPause(e) {
        const video = e.target;
        if (!video.ended && video.paused) {
            video.play().catch(()=>{});
        }
    }

    // --- Initialization ---
    if (submitBtn) {
        submitBtn.addEventListener('click', submitRecommendations);
    }
    
    populateCategories();
    
    const checkInterval = setInterval(checkShowRecommendationModal, 1000);
    setTimeout(() => clearInterval(checkInterval), 30000); // Stop checking after 30s
});
</script>

<?php endif; ?>