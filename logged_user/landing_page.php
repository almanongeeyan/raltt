<?php
// Enforce session and cache control to prevent back navigation after logout
session_start();
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Additional security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\' https: data: \'unsafe-inline\' \'unsafe-eval\'; img-src \'self\' https: data:; style-src \'self\' https: \'unsafe-inline\'; script-src \'self\' https: \'unsafe-inline\' \'unsafe-eval\';');

if (!isset($_SESSION['logged_in'])) {
    header('Location: ../connection/tresspass.php');
    exit();
}

// Show referral modal if referral_count == 1 - MUST BE AT TOP!


// Always include the referral modal markup, but only auto-show if referral_count == 1
include 'referral_form.php';
include 'recommendation_form.php'; // Include recommendation modal markup
$showReferralModal = (isset($_SESSION['referral_count']) && $_SESSION['referral_count'] == 1);

include '../includes/headeruser.php';

// --- Branches data (add lat/lng for each branch) ---
$branches = [
  [ 'id' => 1, 'name' => 'Deparo',   'lat' => 14.752338, 'lng' => 121.017677 ],
  [ 'id' => 2, 'name' => 'Vanguard', 'lat' => 14.759202, 'lng' => 121.062861 ],
  [ 'id' => 3, 'name' => 'Brixton',  'lat' => 14.583121, 'lng' => 120.979313 ],
  [ 'id' => 4, 'name' => 'Samaria',  'lat' => 14.757048, 'lng' => 121.033621 ],
  [ 'id' => 5, 'name' => 'Kiko',     'lat' => 14.607425, 'lng' => 121.011685 ],
];
$user_branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
$user_branch = null;
foreach ($branches as $b) {
  if ($b['id'] === $user_branch_id) { $user_branch = $b; break; }
}
echo '<script>window.BRANCHES = ' . json_encode($branches) . '; window.USER_BRANCH = ' . json_encode($user_branch) . ';</script>';

$branchOverlay = '<div id="branch-location-overlay" style="position:fixed;top:90px;left:24px;z-index:9999;background:rgba(30,30,30,0.90);color:#fff;padding:12px 22px 12px 16px;border-radius:16px;font-size:15px;box-shadow:0 2px 12px 0 rgba(0,0,0,0.13);pointer-events:auto;max-width:290px;line-height:1.5;font-family:Inter,sans-serif;backdrop-filter:blur(2px);border:1.5px solid #cf8756;opacity:0.97;display:block;">';
$branchOverlay .= '<div style="display:flex;align-items:center;gap:10px;margin-bottom:2px;">';
$branchOverlay .= '<span style="display:inline-block;width:8px;height:8px;background:#cf8756;border-radius:50%;margin-right:10px;"></span>';
$branchOverlay .= '<span style="font-weight:500;opacity:0.85;">You\'re currently browsing at</span>';

$branchOverlay .= '</div>';
$branchOverlay .= '<div style="display:flex;align-items:center;gap:6px;">';
$branchOverlay .= '<span id="branch-current" style="font-weight:700;">';
if ($user_branch) {
  $branchOverlay .= htmlspecialchars($user_branch['name']) . ' Branch';
} else {
  $branchOverlay .= '<i>Locating branch...</i>';
}
$branchOverlay .= '</span>';
$branchOverlay .= '<span id="branch-distance" style="font-size:12px;color:#cf8756;margin-left:4px;opacity:0.85;"></span>';
$branchOverlay .= '<a id="branch-change-link" href="#" style="font-size:11px;color:#e8a56a;margin-left:8px;text-decoration:underline;cursor:pointer;opacity:0.85;pointer-events:auto;">Change</a>';
$branchOverlay .= '</div>';
$branchOverlay .= '</div>';
echo $branchOverlay;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rich Anne Lea Tiles Trading - Landing Page</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap"
      rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    // --- Branch location overlay logic ---
    function haversine(lat1, lon1, lat2, lon2) {
      const R = 6371; // km
      const dLat = (lat2 - lat1) * Math.PI / 180;
      const dLon = (lon2 - lon1) * Math.PI / 180;
      const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
      return R * c;
    }
    function showNearestBranch(userLat, userLng) {
      if (!window.BRANCHES) return;
      let minDist = Infinity, nearest = null;
      window.BRANCHES.forEach(b => {
          const dist = haversine(userLat, userLng, b.lat, b.lng);
          if (dist < minDist) { minDist = dist; nearest = b; }
        });
      if (nearest) {
        // Update overlay visually
        const el = document.getElementById('branch-current');
        if (el) {
          el.innerHTML = nearest.name + ' Branch';
        }
        // Show distance in km (smaller text) in both lines
        const distEl = document.getElementById('branch-distance');
        const distTopEl = document.getElementById('branch-distance-top');
        if (distEl) {
          distEl.innerHTML = '(' + minDist.toFixed(2) + ' km)';
        }
  // No top line distance
        // Set branch in session via AJAX (if not already set)
        if (!window.USER_BRANCH || window.USER_BRANCH.id !== nearest.id) {
          fetch('set_branch.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'branch_id=' + encodeURIComponent(nearest.id)
          }).then(() => {
            window.USER_BRANCH = nearest;
          });
        }
      }
    }
    // When user branch is set, try to show distance only beside branch if geolocation is available
    document.addEventListener('DOMContentLoaded', function() {
      if (window.USER_BRANCH && window.USER_BRANCH.id && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
          const userLat = pos.coords.latitude, userLng = pos.coords.longitude;
          const branch = window.BRANCHES.find(b=>b.id===window.USER_BRANCH.id);
          if (branch) {
            const dist = haversine(userLat, userLng, branch.lat, branch.lng);
            const distEl = document.getElementById('branch-distance');
            if (distEl) distEl.innerHTML = '(' + dist.toFixed(2) + ' km)';
          }
        });
      }
    });

    // Branch change modal logic
    function openBranchChangeModal(userLat, userLng) {
      if (!window.BRANCHES) return;
      let modal = document.getElementById('branch-change-modal');
      if (modal) modal.remove();
      modal = document.createElement('div');
      modal.id = 'branch-change-modal';
      modal.style = 'position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);z-index:99999;display:flex;align-items:center;justify-content:center;';
      let html = `<div style="background:#fff;color:#222;padding:0;border-radius:22px;min-width:290px;max-width:95vw;box-shadow:0 8px 40px 0 rgba(0,0,0,0.18);font-family:Inter,sans-serif;position:relative;overflow:hidden;">
        <div style="background:linear-gradient(90deg,#7d310a 0%,#cf8756 100%);padding:22px 28px 16px 28px;text-align:center;">
          <button id='close-branch-modal' style='position:absolute;top:18px;right:22px;font-size:22px;background:none;border:none;color:#fff;cursor:pointer;transition:color .2s;' onmouseover="this.style.color='#f9f5f2'" onmouseout="this.style.color='#fff'">&times;</button>
          <div style="font-size:22px;font-weight:900;letter-spacing:0.5px;color:#fff;">Select Branch</div>
          <div style="margin-top:6px;font-size:13px;opacity:0.92;color:#fff;">Choose a branch to display. Distance is shown if location is available.</div>
        </div>
        <div style='padding:22px 28px 18px 28px;display:flex;flex-direction:column;gap:10px;'>`;
      window.BRANCHES.forEach(b => {
          let dist = '';
          if (typeof userLat === 'number' && typeof userLng === 'number') {
            dist = ' (' + haversine(userLat, userLng, b.lat, b.lng).toFixed(2) + ' km)';
          }
          const isSelected = window.USER_BRANCH && window.USER_BRANCH.id === b.id;
          if (isSelected) {
            html += `<div style="display:flex;align-items:center;justify-content:space-between;width:100%;padding:13px 16px;font-size:16px;font-weight:600;border:none;outline:none;cursor:default;border-radius:12px;box-shadow:0 2px 12px 0 rgba(207,135,86,0.10);background:linear-gradient(90deg,#7d310a 0%,#cf8756 100%);color:#fff;gap:10px;opacity:1;position:relative;">
              <span><i class="fa fa-map-marker-alt" style="margin-right:7px;color:#fff;"></i>${b.name} Branch</span>
              <span style='font-size:13px;color:#fff;background:rgba(207,135,86,0.95);padding:2px 10px 2px 10px;border-radius:8px;${dist?'':'opacity:0.5;'}'>${dist}</span>
              <span style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#fff;font-size:18px;"><i class='fa fa-check-circle'></i></span>
            </div>`;
          } else {
            html += `<button data-branch="${b.id}" style="display:flex;align-items:center;justify-content:space-between;width:100%;padding:13px 16px;font-size:16px;font-weight:600;border:none;outline:none;cursor:pointer;border-radius:12px;transition:background .18s,color .18s,box-shadow .18s;margin:0;background:#f9f5f2;color:#7d310a;gap:10px;" onmouseover="this.style.background='linear-gradient(90deg,#cf8756 0%,#e8a56a 100%)';this.style.color='#fff'" onmouseout="this.style.background='#f9f5f2';this.style.color='#7d310a'">
              <span><i class="fa fa-map-marker-alt" style="margin-right:7px;color:#cf8756;"></i>${b.name} Branch</span>
              <span style='font-size:13px;color:#fff;background:rgba(207,135,86,0.95);padding:2px 10px 2px 10px;border-radius:8px;${dist?'':'opacity:0.5;'}'>${dist}</span>
            </button>`;
          }
      });
      html += `</div>
      </div>`;
      modal.innerHTML = html;
      document.body.appendChild(modal);
      // Button events
      modal.querySelectorAll('button[data-branch]').forEach(btn => {
        btn.onclick = function() {
          const branchId = this.getAttribute('data-branch');
          fetch('set_branch.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'branch_id=' + encodeURIComponent(branchId)
          }).then(() => {
            window.USER_BRANCH = window.BRANCHES.find(b=>b.id==branchId);
            document.body.removeChild(modal);
            location.reload();
          });
        };
      });
      document.getElementById('close-branch-modal').onclick = function() {
        document.body.removeChild(modal);
      };
    }

    document.addEventListener('DOMContentLoaded', function() {
      const changeLink = document.getElementById('branch-change-link');
      if (changeLink) {
        changeLink.onclick = function(e) {
          e.preventDefault();
          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
              openBranchChangeModal(pos.coords.latitude, pos.coords.longitude);
            }, function() {
              openBranchChangeModal();
            }, {timeout:5000});
          } else {
            openBranchChangeModal();
          }
        };
      }
    });
    // Only auto-detect nearest branch if not set in session
    if (!window.USER_BRANCH || !window.USER_BRANCH.id) {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
          showNearestBranch(pos.coords.latitude, pos.coords.longitude);
        }, function(err) {}, {timeout:5000});
      }
    }
      // Pass PHP referral_count to JS and auto-open modal if 1
  window.REFERRAL_COUNT = <?php echo isset($_SESSION['referral_count']) ? (int)$_SESSION['referral_count'] : 0; ?>;
  window.SHOW_REFERRAL_MODAL = <?php echo $showReferralModal ? 'true' : 'false'; ?>;
      
      // Fallback functions in case modal script doesn't load
      function openReferralModalFallback() {
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

      function closeReferralModalFallback() {
        const overlay = document.getElementById('referralModalOverlay');
        const box = document.getElementById('referralModalBox');
        if (overlay && box) {
          box.classList.remove('scale-100', 'opacity-100');
          box.classList.add('scale-90', 'opacity-0');
          setTimeout(() => {
            overlay.style.display = 'none';
            document.body.style.overflow = '';
            // Show recommendation modal after closing referral modal
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
          }, 250);
        }
      }
      
      // Simplified auto-open logic
      document.addEventListener('DOMContentLoaded', function() {
        // Set up close button event listener
        const closeBtn = document.getElementById('closeReferralModal');
        if (closeBtn) {
          closeBtn.onclick = function() {
            if (typeof closeReferralModal === 'function') {
              closeReferralModal();
              // After closing, show recommendation modal
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
            } else {
              closeReferralModalFallback();
            }
          };
        }

        // Auto-open modal if needed on first load
        if (window.SHOW_REFERRAL_MODAL) {
          const modalOverlay = document.getElementById('referralModalOverlay');
          if (modalOverlay) {
            if (typeof openReferralModal === 'function') {
              openReferralModal();
            } else {
              openReferralModalFallback();
            }
          }
        }

  // Removed polling for referral_count every second
      });
      
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
            boxShadow: {
              featured: '0 8px 24px rgba(0,0,0,0.08)',
              tile: '0 8px 24px rgba(0,0,0,0.08)',
              product: '0 8px 24px rgba(0,0,0,0.08)',
            },
          },
        },
      }
    </script>
    <style>
      /* Custom styles for better mobile responsiveness */
      .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
      }
      .scrollbar-hide::-webkit-scrollbar {
        display: none;
      }
      
      @media (max-width: 768px) {
        .mobile-padding {
          padding-left: 1rem;
          padding-right: 1rem;
        }
        
        .mobile-margin {
          margin-left: 1rem;
          margin-right: 1rem;
        }
        
        .mobile-text-center {
          text-align: center;
        }
        
        .mobile-flex-col {
          flex-direction: column;
        }
      }
      
      .featured-items-container {
        overflow: hidden;
        width: 100%;
      }
      
      .featured-items {
        display: flex;
        transition: transform 0.5s ease;
      }
      
      .featured-item {
        flex: 0 0 auto;
        margin: 0 16px;
      }
      
      .featured-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #ccc;
        margin: 0 5px;
        cursor: pointer;
        transition: background-color 0.3s;
      }
      
      .featured-dot.active {
        background-color: #7d310a;
      }
      
      @keyframes float {
        0%,100% { transform: rotate(40deg) translateY(0); }
        50% { transform: rotate(40deg) translateY(-20px); }
      }
    </style>
  </head>

  <body class="font-inter">
    <?php
    // Extra check in body to force redirect if session is not valid
    if (!isset($_SESSION['logged_in'])) {
        echo "<script>window.location.href='../connection/tresspass.php';</script>";
        exit();
    }
    ?>
    <script>
      // Detect browser back navigation and force check for session
      window.addEventListener('pageshow', function (event) {
        if (
          event.persisted ||
          (window.performance && window.performance.navigation.type === 2)
        ) {
          fetch(window.location.href, { cache: 'reload', credentials: 'same-origin' }).catch(
            () => {
              window.location.href = '../connection/tresspass.php';
            }
          );
        }
      });
    </script>
    
    <!-- Hero Section -->
    <section class="relative w-full min-h-screen flex items-center justify-between overflow-hidden pt-16 px-4 md:px-[5vw] bg-[linear-gradient(rgba(0,0,0,0.7),rgba(0,0,0,0.7)),url('../images/user/landingpagebackground.PNG')] bg-center bg-cover">
      <div class="relative z-10 flex flex-1 items-center justify-start gap-6 md:gap-[3vw] flex-col md:flex-row max-md:justify-center max-md:pt-24 max-md:pb-12">
        <img
          src="../images/user/landingpagetile1.png"
          alt="Landing Tile"
          class="max-w-full max-h-[45vh] md:max-h-[80vh] w-[280px] md:w-[700px] h-auto rotate-[25deg] md:rotate-[40deg] drop-shadow-[0_4px_15px_rgba(0,0,0,0.5)] animate-[float_6s_ease-in-out_infinite] mt-8 md:mt-0"
          style="animation-name: float;"
        />
        <div class="flex-1 text-left md:text-left pointer-events-auto max-md:text-center max-md:px-4">
          <div class="text-white text-base md:text-[1.2rem] font-semibold tracking-wider drop-shadow-md">STYLE IN YOUR EVERY STEP.</div>
          <div class="text-secondary text-3xl md:text-[3rem] font-black drop-shadow-lg leading-tight my-2" style="text-shadow:2px 2px 8px #000;">CHOOSE YOUR<br />TILES NOW.</div>
          <div class="text-white text-sm md:text-[1.1rem] mt-5 max-w-full md:max-w-[500px] leading-relaxed">Discover our premium collection of tiles that combine elegance, durability, and style to transform any space into a masterpiece.</div>
        </div>
      </div>
    </section>

    <!-- Recommendation Items Section -->
    <section class="relative bg-gradient-to-br from-light via-white to-accent/30 text-textdark py-12 md:py-20 text-center mobile-padding overflow-x-hidden">
      <div class="mb-8">
        <span class="block text-base md:text-[1.1rem] font-medium tracking-wider text-textlight mb-2">Personalized For You</span>
        <h2 class="text-2xl md:text-[2.5rem] font-black leading-tight text-primary m-0">Recommendation Items</h2>
        <div class="text-sm md:text-[1.1rem] text-textlight max-w-full md:max-w-[700px] mx-auto mt-5 leading-relaxed">
          Explore our recommended selection of premium tiles, personalized for your style and needs.
        </div>
      </div>
      <div class="flex items-center justify-center gap-2 md:gap-4 relative max-w-full w-full mx-auto mt-10">
        <button class="bg-white border border-gray-200 rounded-full w-10 h-10 md:w-12 md:h-12 text-lg md:text-xl text-primary flex items-center justify-center shadow-md hover:bg-primary hover:text-white transition-all duration-200 outline-none prev" aria-label="Previous">
          <i class="fas fa-chevron-left"></i>
        </button>
        <div class="featured-items-container w-full max-w-full overflow-hidden">
          <div class="featured-items flex gap-0.5 md:gap-0.2 w-full py-5 min-h-[320px] md:min-h-[420px]">
            <!-- Items will be populated by JavaScript -->
          </div>
        </div>
        <button class="bg-white border border-gray-200 rounded-full w-10 h-10 md:w-12 md:h-12 text-lg md:text-xl text-primary flex items-center justify-center shadow-md hover:bg-primary hover:text-white transition-all duration-200 outline-none next" aria-label="Next">
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
      <div class="featured-pagination flex justify-center gap-3 mt-6 md:mt-9"></div>
      <div class="pointer-events-none absolute inset-0 z-0 opacity-10 bg-[url('../images/user/landingpagetile1.png')] bg-no-repeat bg-right-bottom bg-contain"></div>
    </section>

    <!-- Tile Selection Section -->
    <section class="bg-dark text-white py-12 md:py-20 text-center relative overflow-hidden mobile-padding">
      <div class="relative z-10">
        <div class="mb-8">
          <span class="block text-base md:text-[1.1rem] font-medium tracking-wider text-textlight mb-2">Explore Our Collection</span>
          <h2 class="text-2xl md:text-[2.5rem] font-black leading-tight text-white m-0" style="text-shadow:-1px -1px 0 #000,1px -1px 0 #000,-1px 1px 0 #000,1px 1px 0 #000;">Our Tile Selection</h2>
          <div class="text-sm md:text-[1.1rem] text-textlight max-w-full md:max-w-[700px] mx-auto mt-5 leading-relaxed">
            From classic ceramics to luxurious natural stone, find the perfect tiles to match your style and needs.
          </div>
        </div>
        <div class="flex flex-nowrap justify-start md:justify-center gap-4 md:gap-5 py-5 w-full overflow-x-auto scrollbar-hide px-2 md:px-0">
          <div class="bg-white rounded-2xl overflow-hidden shadow-tile transition-all duration-300 relative min-w-[160px] md:min-w-[200px] w-[160px] md:w-[200px] flex flex-col border border-gray-100 cursor-pointer hover:shadow-lg hover:-translate-y-2 hover:scale-105">
            <div class="h-[120px] md:h-[150px] overflow-hidden relative flex-shrink-0">
              <img src="../images/user/tile1.jpg" alt="Ceramic Tiles" class="w-full h-full object-cover bg-gray-100 transition-transform duration-500" />
            </div>
            <div class="p-3 md:p-4 text-center bg-white flex-1 flex flex-col justify-between">
              <h3 class="text-sm md:text-[1.1rem] font-bold text-gray-800 mb-2">Ceramic Tiles</h3>
              <p class="text-xs text-gray-600 mb-3 md:mb-4 flex-1">Durable and versatile ceramic tiles for any space</p>
              <button class="inline-flex items-center justify-center gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-primary text-white rounded-full font-semibold text-xs shadow-md w-full max-w-[140px] md:max-w-[160px] mx-auto transition-all duration-300 hover:bg-secondary hover:-translate-y-1"> <i class="fa fa-search text-xs"></i> Explore Now</button>
            </div>
          </div>
          <div class="bg-white rounded-2xl overflow-hidden shadow-tile transition-all duration-300 relative min-w-[160px] md:min-w-[200px] w-[160px] md:w-[200px] flex flex-col border border-gray-100 cursor-pointer hover:shadow-lg hover:-translate-y-2 hover:scale-105">
            <div class="h-[120px] md:h-[150px] overflow-hidden relative flex-shrink-0">
              <img src="../images/user/tile2.jpg" alt="Porcelain Tiles" class="w-full h-full object-cover bg-gray-100 transition-transform duration-500" />
            </div>
            <div class="p-3 md:p-4 text-center bg-white flex-1 flex flex-col justify-between">
              <h3 class="text-sm md:text-[1.1rem] font-bold text-gray-800 mb-2">Porcelain Tiles</h3>
              <p class="text-xs text-gray-600 mb-3 md:mb-4 flex-1">Premium quality porcelain for high-end finishes</p>
              <button class="inline-flex items-center justify-center gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-primary text-white rounded-full font-semibold text-xs shadow-md w-full max-w-[140px] md:max-w-[160px] mx-auto transition-all duration-300 hover:bg-secondary hover:-translate-y-1"> <i class="fa fa-search text-xs"></i> Explore Now</button>
            </div>
          </div>
          <div class="bg-white rounded-2xl overflow-hidden shadow-tile transition-all duration-300 relative min-w-[160px] md:min-w-[200px] w-[160px] md:w-[200px] flex flex-col border border-gray-100 cursor-pointer hover:shadow-lg hover:-translate-y-2 hover:scale-105">
            <div class="h-[120px] md:h-[150px] overflow-hidden relative flex-shrink-0">
              <img src="../images/user/tile3.jpg" alt="Mosaic Tiles" class="w-full h-full object-cover bg-gray-100 transition-transform duration-500" />
            </div>
            <div class="p-3 md:p-4 text-center bg-white flex-1 flex flex-col justify-between">
              <h3 class="text-sm md:text-[1.1rem] font-bold text-gray-800 mb-2">Mosaic Tiles</h3>
              <p class="text-xs text-gray-600 mb-3 md:mb-4 flex-1">Artistic designs for unique decorative accents</p>
              <button class="inline-flex items-center justify-center gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-primary text-white rounded-full font-semibold text-xs shadow-md w-full max-w-[140px] md:max-w-[160px] mx-auto transition-all duration-300 hover:bg-secondary hover:-translate-y-1"> <i class="fa fa-search text-xs"></i> Explore Now</button>
            </div>
          </div>
          <div class="bg-white rounded-2xl overflow-hidden shadow-tile transition-all duration-300 relative min-w-[160px] md:min-w-[200px] w-[160px] md:w-[200px] flex flex-col border border-gray-100 cursor-pointer hover:shadow-lg hover:-translate-y-2 hover:scale-105">
            <div class="h-[120px] md:h-[150px] overflow-hidden relative flex-shrink-0">
              <img src="../images/user/tile4.jpg" alt="Natural Stone Tiles" class="w-full h-full object-cover bg-gray-100 transition-transform duration-500" />
            </div>
            <div class="p-3 md:p-4 text-center bg-white flex-1 flex flex-col justify-between">
              <h3 class="text-sm md:text-[1.1rem] font-bold text-gray-800 mb-2">Natural Stone</h3>
              <p class="text-xs text-gray-600 mb-3 md:mb-4 flex-1">Elegant natural stone for luxurious spaces</p>
              <button class="inline-flex items-center justify-center gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-primary text-white rounded-full font-semibold text-xs shadow-md w-full max-w-[140px] md:max-w-[160px] mx-auto transition-all duration-300 hover:bg-secondary hover:-translate-y-1"> <i class="fa fa-search text-xs"></i> Explore Now</button>
            </div>
          </div>
          <div class="bg-white rounded-2xl overflow-hidden shadow-tile transition-all duration-300 relative min-w-[160px] md:min-w-[200px] w-[160px] md:w-[200px] flex flex-col border border-gray-100 cursor-pointer hover:shadow-lg hover:-translate-y-2 hover:scale-105">
            <div class="h-[120px] md:h-[150px] overflow-hidden relative flex-shrink-0">
              <img src="../images/user/tile5.jpg" alt="Premium Tiles" class="w-full h-full object-cover bg-gray-100 transition-transform duration-500" />
            </div>
            <div class="p-3 md:p-4 text-center bg-white flex-1 flex flex-col justify-between">
              <h3 class="text-sm md:text-[1.1rem] font-bold text-gray-800 mb-2">Premium Tiles</h3>
              <p class="text-xs text-gray-600 mb-3 md:mb-4 flex-1">High-end premium tiles for luxury spaces</p>
              <button class="inline-flex items-center justify-center gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-primary text-white rounded-full font-semibold text-xs shadow-md w-full max-w-[140px] md:max-w-[160px] mx-auto transition-all duration-300 hover:bg-secondary hover:-translate-y-1"> <i class="fa fa-search text-xs"></i> Explore Now</button>
            </div>
          </div>
        </div>
      </div>
      <div class="absolute inset-0 z-0 pointer-events-none opacity-30" style="background:linear-gradient(45deg,rgba(125,49,10,0.05) 25%,transparent 25%),linear-gradient(-45deg,rgba(125,49,10,0.05) 25%,transparent 25%),linear-gradient(45deg,transparent 75%,rgba(125,49,10,0.05) 75%),linear-gradient(-45deg,transparent 75%,rgba(125,49,10,0.05) 75%);background-size:20px 20px;background-position:0 0,0 10px,10px -10px,-10px 0px;"></div>
    </section>

    <!-- Products Section -->
    <section class="bg-light py-12 md:py-16 px-4 md:px-[5vw] text-textdark">
      <div class="flex gap-6 md:gap-8 max-w-full md:max-w-[1500px] mx-auto flex-col md:flex-row">
        <div class="w-full md:flex-[0_0_280px] bg-white p-6 md:p-8 rounded-2xl shadow-lg h-fit relative z-10 md:mb-0 mb-6">
          <h3 class="text-lg md:text-[1.25rem] font-bold text-primary mb-6">Categories</h3>
          <div class="border-b border-gray-200 pb-5 mb-5">
            <div class="flex flex-col gap-3">
              <label class="flex items-center text-sm text-textlight cursor-pointer hover:text-primary">
                <input type="checkbox" name="category" class="mr-2 w-4 h-4 rounded border-2 border-gray-300 bg-gray-50 checked:bg-primary checked:border-primary transition-all" />
                Ceramic Tiles
              </label>
              <label class="flex items-center text-sm text-textlight cursor-pointer hover:text-primary">
                <input type="checkbox" name="category" class="mr-2 w-4 h-4 rounded border-2 border-gray-300 bg-gray-50 checked:bg-primary checked:border-primary transition-all" />
                Porcelain Tiles
              </label>
              <label class="flex items-center text-sm text-textlight cursor-pointer hover:text-primary">
                <input type="checkbox" name="category" class="mr-2 w-4 h-4 rounded border-2 border-gray-300 bg-gray-50 checked:bg-primary checked:border-primary transition-all" />
                Mosaic Tiles
              </label>
              <label class="flex items-center text-sm text-textlight cursor-pointer hover:text-primary">
                <input type="checkbox" name="category" class="mr-2 w-4 h-4 rounded border-2 border-gray-300 bg-gray-50 checked:bg-primary checked:border-primary transition-all" />
                Natural Stone
              </label>
              <label class="flex items-center text-sm text-textlight cursor-pointer hover:text-primary">
                <input type="checkbox" name="category" class="mr-2 w-4 h-4 rounded border-2 border-gray-300 bg-gray-50 checked:bg-primary checked:border-primary transition-all" />
                Outdoor Tiles
              </label>
            </div>
          </div>
          <h3 class="text-lg md:text-[1.25rem] font-bold text-primary mb-6">Price Range</h3>
          <div class="border-b border-gray-200 pb-5 mb-5">
            <div class="flex flex-col gap-3">
              <label class="flex items-center text-sm text-textlight cursor-pointer hover:text-primary">
                <input type="radio" name="price-range" class="mr-2 w-4 h-4 rounded-full border-2 border-gray-300 bg-gray-50 checked:bg-primary checked:border-primary transition-all" />
                Under ₱500
              </label>
              <label class="flex items-center text-sm text-textlight cursor-pointer hover:text-primary">
                <input type="radio" name="price-range" class="mr-2 w-4 h-4 rounded-full border-2 border-gray-300 bg-gray-50 checked:bg-primary checked:border-primary transition-all" />
                ₱500 - ₱1000
              </label>
              <label class="flex items-center text-sm text-textlight cursor-pointer hover:text-primary">
                <input type="radio" name="price-range" class="mr-2 w-4 h-4 rounded-full border-2 border-gray-300 bg-gray-50 checked:bg-primary checked:border-primary transition-all" />
                ₱1000 - ₱2000
              </label>
              <label class="flex items-center text-sm text-textlight cursor-pointer hover:text-primary">
                <input type="radio" name="price-range" class="mr-2 w-4 h-4 rounded-full border-2 border-gray-300 bg-gray-50 checked:bg-primary checked:border-primary transition-all" />
                Over ₱2000
              </label>
            </div>
          </div>
          <button class="w-full py-3 bg-primary text-white rounded-lg text-base font-bold mt-5 transition-all hover:bg-secondary hover:-translate-y-1 shadow">Apply Filters</button>
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex justify-between items-center mb-5 flex-col md:flex-row gap-3 md:gap-0">
            <div class="text-center md:text-left">
              <h2 class="text-2xl md:text-[2.5rem] font-black text-primary m-0">Premium Tiles</h2>
              <p class="text-sm md:text-base text-textlight m-0 mt-2">Browse our extensive collection of premium tiles for every room in your home or business.</p>
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
            <div class="bg-white rounded-2xl overflow-hidden shadow-product transition-all duration-300 relative group">
              <span class="absolute top-4 left-4 bg-secondary text-white px-3 py-1 rounded text-xs font-bold uppercase z-10">Bestseller</span>
              <div class="h-[200px] md:h-[250px] overflow-hidden relative">
                <img src="../images/user/tile1.jpg" alt="Premium Ceramic Tile" class="w-full h-full object-cover bg-gray-100 transition-transform duration-300 group-hover:scale-105" />
              </div>
              <div class="p-4 md:p-5 text-center">
                <h3 class="text-base md:text-[1.1rem] font-bold text-gray-800 mb-1">Premium Ceramic Tile</h3>
                <div class="text-lg md:text-[1.25rem] font-extrabold text-secondary mb-4">₱1,250</div>
                <div class="flex justify-center gap-2">
                  <button class="w-full py-3 bg-primary text-white rounded-lg text-base font-bold mt-5 transition-all hover:bg-secondary hover:-translate-y-1 shadow"><i class="fa fa-shopping-cart"></i> Add to Cart</button>
                </div>
              </div>
            </div>
            <div class="bg-white rounded-2xl overflow-hidden shadow-product transition-all duration-300 relative group">
              <div class="h-[200px] md:h-[250px] overflow-hidden relative">
                <img src="../images/user/tile2.jpg" alt="Porcelain Tile" class="w-full h-full object-cover bg-gray-100 transition-transform duration-300 group-hover:scale-105" />
              </div>
              <div class="p-4 md:p-5 text-center">
                <h3 class="text-base md:text-[1.1rem] font-bold text-gray-800 mb-1">Porcelain Tile</h3>
                <div class="text-lg md:text-[1.25rem] font-extrabold text-secondary mb-4">₱950</div>
                <div class="flex justify-center gap-2">
                  <button class="w-full py-3 bg-primary text-white rounded-lg text-base font-bold mt-5 transition-all hover:bg-secondary hover:-translate-y-1 shadow"><i class="fa fa-shopping-cart"></i> Add to Cart</button>
                </div>
              </div>
            </div>
            <div class="bg-white rounded-2xl overflow-hidden shadow-product transition-all duration-300 relative group">
              <span class="absolute top-4 left-4 bg-primary text-white px-3 py-1 rounded text-xs font-bold uppercase z-10">New</span>
              <div class="h-[200px] md:h-[250px] overflow-hidden relative">
                <img src="../images/user/tile3.jpg" alt="Mosaic Tile" class="w-full h-full object-cover bg-gray-100 transition-transform duration-300 group-hover:scale-105" />
              </div>
              <div class="p-4 md:p-5 text-center">
                <h3 class="text-base md:text-[1.1rem] font-bold text-gray-800 mb-1">Mosaic Tile</h3>
                <div class="text-lg md:text-[1.25rem] font-extrabold text-secondary mb-4">₱1,750</div>
                <div class="flex justify-center gap-2">
                  <button class="w-full py-3 bg-primary text-white rounded-lg text-base font-bold mt-5 transition-all hover:bg-secondary hover:-translate-y-1 shadow"><i class="fa fa-shopping-cart"></i> Add to Cart</button>
                </div>
              </div>
            </div>
            <div class="bg-white rounded-2xl overflow-hidden shadow-product transition-all duration-300 relative group">
              <div class="h-[200px] md:h-[250px] overflow-hidden relative">
                <img src="../images/user/tile4.jpg" alt="Natural Stone Tile" class="w-full h-full object-cover bg-gray-100 transition-transform duration-300 group-hover:scale-105" />
              </div>
              <div class="p-4 md:p-5 text-center">
                <h3 class="text-base md:text-[1.1rem] font-bold text-gray-800 mb-1">Natural Stone Tile</h3>
                <div class="text-lg md:text-[1.25rem] font-extrabold text-secondary mb-4">₱850</div>
                <div class="flex justify-center gap-2">
                  <button class="w-full py-3 bg-primary text-white rounded-lg text-base font-bold mt-5 transition-all hover:bg-secondary hover:-translate-y-1 shadow"><i class="fa fa-shopping-cart"></i> Add to Cart</button>
                </div>
              </div>
            </div>
            <div class="bg-white rounded-2xl overflow-hidden shadow-product transition-all duration-300 relative group">
              <span class="absolute top-4 left-4 bg-[#d9534f] text-white px-3 py-1 rounded text-xs font-bold uppercase z-10">Sale</span>
              <div class="h-[200px] md:h-[250px] overflow-hidden relative">
                <img src="../images/user/tile5.jpg" alt="Classic Tile" class="w-full h-full object-cover bg-gray-100 transition-transform duration-300 group-hover:scale-105" />
              </div>
              <div class="p-4 md:p-5 text-center">
                <h3 class="text-base md:text-[1.1rem] font-bold text-gray-800 mb-1">Classic Tile</h3>
                <div class="text-lg md:text-[1.25rem] font-extrabold text-secondary mb-4">₱2,100</div>
                <div class="flex justify-center gap-2">
                  <button class="w-full py-3 bg-primary text-white rounded-lg text-base font-bold mt-5 transition-all hover:bg-secondary hover:-translate-y-1 shadow"><i class="fa fa-shopping-cart"></i> Add to Cart</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <script>
      const featuredItems = [
        {
          img: '../images/user/tile1.jpg',
          title: 'Premium Ceramic Tile',
          price: '₱1,250',
        },
        {
          img: '../images/user/tile2.jpg',
          title: 'Porcelain Tile',
          price: '₱950',
        },
        {
          img: '../images/user/tile3.jpg',
          title: 'Mosaic Tile',
          price: '₱1,750',
        },
        {
          img: '../images/user/tile4.jpg',
          title: 'Natural Stone Tile',
          price: '₱850',
        },
        {
          img: '../images/user/tile5.jpg',
          title: 'Classic Tile',
          price: '₱2,100',
        },
      ];

      // Calculate items per page based on screen width
      const itemsPerPage = () => {
        if (window.innerWidth <= 640) return 1; // Show 1 item on mobile
        if (window.innerWidth <= 768) return 2;
        if (window.innerWidth <= 1024) return 3;
        return 4;
      };

      let currentPage = 0;
      let isAnimating = false;

      // Initialize the carousel
      function initCarousel() {
        const container = document.querySelector('.featured-items');
        if (!container) return;
        container.innerHTML = '';
        const perPage = itemsPerPage();
        const itemWidth = calculateItemWidth();
        // Set container width based on number of items
        container.style.width = `${featuredItems.length * (itemWidth + 32)}px`;
        // Create items
        featuredItems.forEach((item, index) => {
          const div = document.createElement('div');
          div.className = 'featured-item';
          div.style.width = `${itemWidth}px`;
          // Card content
          div.innerHTML = `
            <div class="relative bg-white rounded-3xl shadow-2xl p-4 md:p-6 w-full flex flex-col items-center border border-gray-100 transition-all duration-300 group hover:shadow-2xl hover:-translate-y-2 hover:scale-105 overflow-hidden">
              <span class="absolute top-3 left-3 bg-gradient-to-r from-primary to-secondary text-white text-xs font-bold px-3 py-1 rounded-full shadow-md z-10">Featured</span>
              <div class="w-[120px] h-[120px] md:w-[160px] md:h-[160px] flex items-center justify-center bg-gradient-to-br from-accent/30 to-light rounded-xl mb-4 md:mb-6 shadow-inner overflow-hidden">
                <img src="${item.img}" alt="${item.title}" class="w-[90%] h-[90%] object-contain rounded-xl transition-transform duration-300 group-hover:scale-105 bg-gray-100 drop-shadow-lg" />
              </div>
              <div class="text-base md:text-[1.1rem] font-extrabold text-primary mb-1 text-center tracking-wide">${item.title}</div>
              <div class="text-lg md:text-[1.25rem] font-extrabold text-secondary mb-4 text-center">${item.price}</div>
              <button class="inline-flex items-center gap-2 px-4 py-2 md:px-6 md:py-2 rounded-full bg-gradient-to-r from-primary to-secondary text-white font-bold text-xs md:text-sm shadow-lg hover:from-secondary hover:to-primary hover:scale-105 transition-all">
                <i class="fa fa-shopping-cart"></i> Add to Cart
              </button>
            </div>
          `;
          container.appendChild(div);
        });
        renderPagination();
        updateCarouselPosition();
      }

      // Calculate item width based on screen size
      function calculateItemWidth() {
        if (window.innerWidth <= 640) return 280; // Mobile
        if (window.innerWidth <= 768) return 250; // Small tablet
        if (window.innerWidth <= 1024) return 220; // Tablet
        return 220; // Desktop
      }

      // Update carousel position
      function updateCarouselPosition() {
        if (isAnimating) return;
        
        const container = document.querySelector('.featured-items');
        if (!container) return;
        
        const perPage = itemsPerPage();
        const itemWidth = calculateItemWidth();
        const gap = 32;
        const translateX = -currentPage * (itemWidth + gap) * perPage;
        
        container.style.transform = `translateX(${translateX}px)`;
        renderPagination();
      }

      // Render pagination dots
      function renderPagination() {
        const perPage = itemsPerPage();
        const pageCount = Math.ceil(featuredItems.length / perPage);
        const pagination = document.querySelector('.featured-pagination');
        if (!pagination) return;
        
        pagination.innerHTML = '';
        
        for (let i = 0; i < pageCount; i++) {
          const dot = document.createElement('span');
          dot.className = `featured-dot ${i === currentPage ? 'active' : ''}`;
          dot.addEventListener('click', () => {
            if (i !== currentPage && !isAnimating) {
              currentPage = i;
              updateCarouselPosition();
            }
          });
          
          pagination.appendChild(dot);
        }
      }

      // Next button functionality
      function nextFeatured() {
        if (isAnimating) return;
        
        const perPage = itemsPerPage();
        const pageCount = Math.ceil(featuredItems.length / perPage);
        
        if (currentPage < pageCount - 1) {
          currentPage++;
          updateCarouselPosition();
        }
      }

      // Previous button functionality
      function prevFeatured() {
          if (isAnimating) return;
          
          if (currentPage > 0) {
              currentPage--;
              updateCarouselPosition();
          }
      }

      // Event listeners for navigation buttons
      document.addEventListener('DOMContentLoaded', function() {
          // Initialize the carousel
          initCarousel();
          
          // Add event listeners for next/prev buttons
          const nextBtn = document.querySelector('.next');
          const prevBtn = document.querySelector('.prev');
          
          if (nextBtn) {
              nextBtn.addEventListener('click', nextFeatured);
          }
          
          if (prevBtn) {
              prevBtn.addEventListener('click', prevFeatured);
          }
          
          // Handle window resize
          let resizeTimer;
          window.addEventListener('resize', function() {
              clearTimeout(resizeTimer);
              resizeTimer = setTimeout(function() {
                  currentPage = 0;
                  initCarousel();
              }, 250);
          });
          
          // Add event listeners for category filter buttons
          const categoryButtons = document.querySelectorAll('.tile-selection .bg-white button');
          categoryButtons.forEach(button => {
              button.addEventListener('click', function() {
                  // Redirect to products page with category filter
                  window.location.href = 'products.php?category=' + 
                      encodeURIComponent(this.closest('.bg-white').querySelector('h3').textContent);
              });
          });
          
          // Add event listeners for "Add to Cart" buttons
          const addToCartButtons = document.querySelectorAll('button:has(.fa-shopping-cart)');
          addToCartButtons.forEach(button => {
              button.addEventListener('click', function(e) {
                  e.preventDefault();
                  e.stopPropagation();
                  
                  // Get product details
                  const productCard = this.closest('.bg-white');
                  const productName = productCard.querySelector('h3').textContent;
                  const productPrice = productCard.querySelector('.text-secondary').textContent;
                  
                  // Show success message
                  Swal.fire({
                      title: 'Added to Cart!',
                      html: `<strong>${productName}</strong><br>${productPrice}`,
                      icon: 'success',
                      confirmButtonColor: '#7d310a',
                      confirmButtonText: 'Continue Shopping'
                  });
              });
          });
          
          // Apply filters button
          const applyFiltersBtn = document.querySelector('.bg-white button:not(:has(.fa-shopping-cart))');
        // Removed filter confirmation popup
      });
    </script>

  </body>
</html>