

<?php // referral_modal.php ?>

<div id="referralModalOverlay" class="fixed inset-0 bg-black bg-opacity-60 z-[9999] flex items-center justify-center p-4 hidden">
  <div id="referralModalBox" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300 scale-90 opacity-0">
    <!-- Modal Header -->
    <div class="bg-gradient-to-r from-primary to-secondary p-6 text-center relative">
      <h2 class="text-2xl font-black text-white">Referral Program</h2>
      <p class="text-white/90 mt-2">You've been referred to our store!</p>
      
      <button id="closeReferralModal" class="absolute top-4 right-4 text-white hover:text-gray-200 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    
    <!-- Modal Content -->
    <div class="p-6">
      <div class="flex items-center justify-center mb-5">
        <div class="bg-gradient-to-tr from-yellow-300 to-pink-400 p-3 rounded-full shadow-lg">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="h-12 w-12">
            <defs>
              <linearGradient id="giftGradient" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#fbbf24" />
                <stop offset="100%" stop-color="#ec4899" />
              </linearGradient>
            </defs>
            <rect x="8" y="20" width="32" height="18" rx="4" fill="url(#giftGradient)"/>
            <rect x="20" y="20" width="8" height="18" fill="#fff" opacity=".5"/>
            <rect x="8" y="16" width="32" height="8" rx="2" fill="#fff" opacity=".2"/>
            <rect x="8" y="16" width="32" height="8" rx="2" fill="url(#giftGradient)"/>
            <path d="M24 16c-2.5-4-8-4-8 0 0 2 2 4 4 4s4-2 4-4zm0 0c2.5-4 8-4 8 0 0 2-2 4-4 4s-4-2-4-4z" fill="#fff" opacity=".7"/>
            <path d="M24 16c-2.5-4-8-4-8 0 0 2 2 4 4 4s4-2 4-4zm0 0c2.5-4 8-4 8 0 0 2-2 4-4 4s-4-2-4-4z" fill="url(#giftGradient)"/>
            <rect x="22" y="8" width="4" height="12" rx="2" fill="#fff"/>
          </svg>
        </div>
      </div>
      
      <p class="text-textdark text-center mb-6">
        Enter the 6-digit referral code you received to claim your special welcome discount!
      </p>
      
      <form id="referralForm" class="space-y-4">
        <div id="referralResult" class="text-center text-sm mt-2"></div>
        <div>
          <label for="referralCode" class="block text-sm font-medium text-textdark mb-2">Referral Code</label>
          <div class="relative">
            <input 
              type="text" 
              id="referralCode" 
              name="referralCode" 
              maxlength="6" 
              pattern="[A-Za-z0-9]{6}" 
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition text-gray-800"
              placeholder="Enter 6-digit code"
              required
              title="Please enter exactly 6 alphanumeric characters"
            >
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
              <span class="text-gray-400 text-sm">6 chars</span>
            </div>
          </div>
          <p class="text-xs text-textlight mt-2">Enter 6 letters or numbers (case insensitive)</p>
        </div>
        
        <div class="pt-4">
          <button 
            type="submit" 
            class="w-full bg-primary text-white py-3 px-4 rounded-lg font-bold hover:bg-secondary transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-50"
          >
            Validate Referral Code
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>
<script>
// Modal control functions
function openReferralModal() {
  const overlay = document.getElementById('referralModalOverlay');
  const box = document.getElementById('referralModalBox');
  if (overlay && box) {
    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    setTimeout(() => {
      box.classList.remove('scale-90', 'opacity-0');
      box.classList.add('scale-100', 'opacity-100');
    }, 10);
  }
}

function closeReferralModal() {
  const overlay = document.getElementById('referralModalOverlay');
  const box = document.getElementById('referralModalBox');
  if (overlay && box) {
    box.classList.remove('scale-100', 'opacity-100');
    box.classList.add('scale-90', 'opacity-0');
    setTimeout(() => {
      overlay.style.display = 'none';
      document.body.style.overflow = '';
      // Mark referral as completed so recommendation modal can show
      localStorage.setItem('referralCompleted', 'true');
      // Show recommendation modal after closing referral modal
      setTimeout(function() {
        if (typeof openRecommendationModal === 'function') {
          openRecommendationModal();
        } else {
          // Fallback: show recommendation modal directly
          const recOverlay = document.getElementById('recommendationModalOverlay');
          const recBox = document.getElementById('recommendationModalBox');
          if (recOverlay && recBox) {
            recOverlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
              recBox.classList.remove('scale-90', 'opacity-0');
              recBox.classList.add('scale-100', 'opacity-100');
            }, 10);
          }
        }
      }, 300);
    }, 250);
  }
}

// Only close modal when clicking the X button
document.getElementById('closeReferralModal').addEventListener('click', function() {
  closeReferralModal();
});

// Form submission handling
document.getElementById('referralForm').onsubmit = function(e) {
  e.preventDefault();
  const referralCode = document.getElementById('referralCode').value;
  const resultDiv = document.getElementById('referralResult');
  resultDiv.textContent = '';
  const regex = /^[A-Za-z0-9]{6}$/;
  if (!regex.test(referralCode)) {
    resultDiv.textContent = 'Please enter exactly 6 alphanumeric characters.';
    resultDiv.style.color = '#b91c1c'; // red
    return false;
  }
  const formData = new FormData();
  formData.append('referral_code', referralCode);
  fetch('processes/process_referral_code.php', {
    method: 'POST',
    body: formData,
    credentials: 'same-origin'
  })
  .then(async response => {
    let data;
    try {
      data = await response.json();
    } catch (e) {
      const text = await response.text();
      resultDiv.textContent = text || 'An unknown error occurred.';
      resultDiv.style.color = '#b91c1c';
      return;
    }
    if (data.success) {
  resultDiv.innerHTML = `<div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800 text-base font-medium shadow-sm">${data.message}</div>`;
  resultDiv.style.color = '';
      setTimeout(() => {
        closeReferralModal();
        localStorage.setItem('referralCompleted', 'true');
        // Show recommendation modal after closing referral modal
        setTimeout(function() {
          if (typeof openRecommendationModal === 'function') {
            openRecommendationModal();
          } else {
            // Fallback: show recommendation modal directly
            const recOverlay = document.getElementById('recommendationModalOverlay');
            const recBox = document.getElementById('recommendationModalBox');
            if (recOverlay && recBox) {
              recOverlay.style.display = 'flex';
              document.body.style.overflow = 'hidden';
              setTimeout(() => {
                recBox.classList.remove('scale-90', 'opacity-0');
                recBox.classList.add('scale-100', 'opacity-100');
              }, 10);
            }
          }
        }, 300);
      }, 4000);
    } else {
      resultDiv.textContent = data.message;
      resultDiv.style.color = '#b91c1c';
    }
  })
  .catch(error => {
    resultDiv.textContent = error && error.message ? error.message : 'An error occurred while processing your request.';
    resultDiv.style.color = '#b91c1c';
  });
  return false;
};

// Input validation to allow only alphanumeric characters
document.getElementById('referralCode').addEventListener('input', function(e) {
  this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');
});
</script>
