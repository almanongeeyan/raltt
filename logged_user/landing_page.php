<?php


include '../includes/headeruser.php';

if (!isset($_SESSION['logged_in'])) {
    header('Location: ../connection/tresspass.php');
    exit();
}

include 'referral_form.php';
include 'recommendation_form.php'; 

$showRecommendationModal = false;
if (isset($_SESSION['user_id'])) {
  try {
    require_once '../connection/connection.php';
    $pdo = $conn ?? null;
    if ($pdo) {
      $stmt = $pdo->prepare('SELECT COUNT(*) as cnt FROM user_design_preferences WHERE user_id = ?');
      $stmt->execute([$_SESSION['user_id']]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row && isset($row['cnt']) && (int)$row['cnt'] === 0) {
        $showRecommendationModal = true;
      }
    }
  } catch (Exception $e) {
  }
}

$showReferralModal = false;
if (isset($_SESSION['user_id'])) {
  try {
    require_once '../connection/connection.php';
    $pdo = $conn ?? null;
    if ($pdo) {
      $stmt = $pdo->prepare('SELECT has_used_referral_code FROM users WHERE id = ? LIMIT 1');
      $stmt->execute([$_SESSION['user_id']]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row && isset($row['has_used_referral_code']) && ($row['has_used_referral_code'] === 'FALSE' || $row['has_used_referral_code'] == 0)) {
        $showReferralModal = true;
      }
    }
  } catch (Exception $e) {
  }
}


require_once '../connection/connection.php';
$branches = [];
try {
  $stmt = $conn->query("SELECT branch_id AS id, branch_name AS name, latitude AS lat, longitude AS lng FROM branches ORDER BY branch_id ASC");
  $branches = $stmt->fetchAll();
} catch (Exception $e) {
  // fallback to static if DB fails
  $branches = [
    [ 'id' => 1, 'name' => 'Deparo',   'lat' => 14.75243153, 'lng' => 121.01763335 ],
    [ 'id' => 2, 'name' => 'Vanguard', 'lat' => 14.75920200, 'lng' => 121.06286101 ],
    [ 'id' => 3, 'name' => 'Brixton',  'lat' => 14.76724928, 'lng' => 121.04104486 ],
    [ 'id' => 4, 'name' => 'Samaria',  'lat' => 14.76580311, 'lng' => 121.06563606 ],
    [ 'id' => 5, 'name' => 'Phase 1',  'lat' => 14.77682717, 'lng' => 121.04841432 ],
  ];
}
$user_branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
$user_branch = null;
foreach ($branches as $b) {
  if ($b['id'] === $user_branch_id) { $user_branch = $b; break; }
}
echo '<script>window.BRANCHES = ' . json_encode($branches) . '; window.USER_BRANCH = ' . json_encode($user_branch) . ';</script>';
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
        }, function() {/* Do nothing if geolocation fails */});
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
              openBranchChangeModal(); // fallback, but no error/interrupt
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
        }, function(err) {/* Do nothing if geolocation fails */}, {timeout:5000});
      }
    }
    // Pass PHP referral_count to JS and auto-open modal if 1
  window.SHOW_REFERRAL_MODAL = <?php echo $showReferralModal ? 'true' : 'false'; ?>;
  window.SHOW_RECOMMENDATION_MODAL = <?php echo $showRecommendationModal ? 'true' : 'false'; ?>;
      
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
              // After closing, show recommendation modal only if allowed
              setTimeout(function() {
                if (window.SHOW_RECOMMENDATION_MODAL) {
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
                }
              }, 300);
            } else {
              closeReferralModalFallback();
            }
          };
        }

        // Auto-open referral modal if needed on first load
        if (window.SHOW_REFERRAL_MODAL) {
          const modalOverlay = document.getElementById('referralModalOverlay');
          if (modalOverlay) {
            if (typeof openReferralModal === 'function') {
              openReferralModal();
            } else {
              openReferralModalFallback();
            }
          }
        } else if (window.SHOW_RECOMMENDATION_MODAL) {
          // If referral modal is not shown, auto-open recommendation modal if allowed
          const recOverlay = document.getElementById('recommendationModalOverlay');
          if (recOverlay) {
            if (typeof openRecommendationModal === 'function') {
              openRecommendationModal();
            } else {
              // Fallback: show recommendation modal directly
              const recBox = document.getElementById('recommendationModalBox');
              if (recBox) {
                recOverlay.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                setTimeout(() => {
                  recBox.classList.remove('scale-90', 'opacity-0');
                  recBox.classList.add('scale-100', 'opacity-100');
                }, 10);
              }
            }
          }
        }
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
  // Expose current branch ID for cart logic
  window.currentBranchId = <?php echo isset($_SESSION['branch_id']) ? intval($_SESSION['branch_id']) : 'null'; ?>;
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
    
    <!-- Branch Banner Carousel Section -->
    <?php
    // Fetch branch banners for the user's branch
    $branchBanners = [];
    if (isset($user_branch_id)) {
      try {
        $stmt = $conn->prepare('SELECT banner_id, banner_image, display_order FROM branch_banners WHERE branch_id = ? AND is_active = 1 ORDER BY display_order ASC, banner_id ASC');
        $stmt->execute([$user_branch_id]);
        $branchBanners = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Convert images to base64
        foreach ($branchBanners as &$banner) {
          if (!empty($banner['banner_image'])) {
            $banner['banner_image'] = 'data:image/jpeg;base64,' . base64_encode($banner['banner_image']);
          } else {
            $banner['banner_image'] = null;
          }
        }
        unset($banner);
      } catch (Exception $e) {
        $branchBanners = [];
      }
    }
    ?>
    <section class="relative w-full min-h-screen h-screen flex items-center justify-center overflow-hidden p-0 m-0 bg-gradient-to-br from-primary/90 to-secondary/80">
      <div class="absolute inset-0 w-full h-full z-0">
        <div id="branchBannerCarousel" class="relative w-full h-full min-h-screen min-w-full overflow-hidden group">
          
          <?php if (!empty($branchBanners)): ?>
            <?php foreach ($branchBanners as $i => $banner): ?>
              <div class="carousel-slide absolute inset-0 transition-opacity duration-700 ease-in-out <?php echo $i === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0'; ?>" data-slide="<?php echo $i; ?>">
                <?php if ($banner['banner_image']): ?>
                  <img src="<?php echo htmlspecialchars($banner['banner_image']); ?>" alt="Branch Banner <?php echo $i+1; ?>" class="w-full h-full object-cover object-center scale-105 group-hover:scale-110 transition-transform duration-1000" style="min-height:100vh;min-width:100vw;" />
                <?php else: ?>
                  <div class="w-full h-full flex items-center justify-center bg-gray-300 text-gray-500 text-2xl font-bold">No Image</div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="w-full h-full flex items-center justify-center bg-gray-300 text-gray-500 text-2xl font-bold">No Banners Available</div>
          <?php endif; ?>
          <!-- Carousel Controls -->
          <?php if (count($branchBanners) > 1): ?>
          <button id="carouselPrev" class="absolute left-8 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-primary text-primary hover:text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg transition-all z-30 border-2 border-primary/30">
            <i class="fas fa-chevron-left text-2xl"></i>
          </button>
          <button id="carouselNext" class="absolute right-8 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-primary text-primary hover:text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg transition-all z-30 border-2 border-primary/30">
            <i class="fas fa-chevron-right text-2xl"></i>
          </button>
          <?php endif; ?>
          <!-- Carousel Dots -->
          <?php if (count($branchBanners) > 1): ?>
          <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex gap-3 z-30">
            <?php foreach ($branchBanners as $i => $banner): ?>
              <span class="carousel-dot w-5 h-5 rounded-full bg-white border-2 border-primary cursor-pointer transition-all <?php echo $i === 0 ? 'bg-primary' : 'bg-white'; ?>" data-dot="<?php echo $i; ?>"></span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <style>
        @keyframes fadein-slow { from { opacity: 0; } to { opacity: 1; } }
        .animate-fadein-slow { animation: fadein-slow 1.5s ease; }
        @keyframes slideup { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slideup { animation: slideup 1.2s cubic-bezier(0.4,0,0.2,1); }
      </style>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          const slides = document.querySelectorAll('#branchBannerCarousel .carousel-slide');
          const dots = document.querySelectorAll('#branchBannerCarousel .carousel-dot');
          let current = 0;
          let timer = null;
          function showSlide(idx) {
            slides.forEach((slide, i) => {
              slide.classList.toggle('opacity-100', i === idx);
              slide.classList.toggle('z-10', i === idx);
              slide.classList.toggle('opacity-0', i !== idx);
              slide.classList.toggle('z-0', i !== idx);
            });
            dots.forEach((dot, i) => {
              dot.classList.toggle('bg-primary', i === idx);
              dot.classList.toggle('bg-white', i !== idx);
            });
            current = idx;
          }
          function nextSlide() {
            showSlide((current + 1) % slides.length);
          }
          function prevSlide() {
            showSlide((current - 1 + slides.length) % slides.length);
          }
          if (slides.length > 1) {
            document.getElementById('carouselNext').onclick = nextSlide;
            document.getElementById('carouselPrev').onclick = prevSlide;
            dots.forEach((dot, i) => {
              dot.onclick = () => showSlide(i);
            });
            timer = setInterval(nextSlide, 5000);
            document.getElementById('branchBannerCarousel').addEventListener('mouseenter', () => clearInterval(timer));
            document.getElementById('branchBannerCarousel').addEventListener('mouseleave', () => { timer = setInterval(nextSlide, 5000); });
          }
        });
      </script>
    </section>

    <!-- Recommendation Items Section -->
    <?php
    // --- Fetch recommended products for this user and branch ---
    $recommendedProducts = [];
    if (isset($_SESSION['user_id']) && isset($_SESSION['branch_id'])) {
      require_once '../connection/connection.php';
      $user_id = $_SESSION['user_id'];
      $branch_id = (int)$_SESSION['branch_id'];
      // Get top 3 recommended designs for user (ordered by rank)
      $designStmt = $conn->prepare('SELECT design_id FROM user_design_preferences WHERE user_id = ? ORDER BY rank ASC');
      $designStmt->execute([$user_id]);
      $userDesigns = $designStmt->fetchAll(PDO::FETCH_COLUMN);
      if ($userDesigns) {
        // For each design, get up to N products (more for 1st, less for 2nd/3rd), only for this branch
        $designWeights = [0=>4, 1=>2, 2=>1]; // 1st:4, 2nd:2, 3rd:1
        $usedProductIds = [];
        foreach ($userDesigns as $i => $designId) {
          $limit = $designWeights[$i] ?? 1;
          $prodStmt = $conn->prepare('SELECT p.product_id, p.product_name, p.product_price, p.product_description, p.product_image, td.design_name FROM products p JOIN product_designs pd ON p.product_id = pd.product_id JOIN tile_designs td ON pd.design_id = td.design_id JOIN product_branches pb ON p.product_id = pb.product_id WHERE pd.design_id = ? AND pb.branch_id = ? AND p.is_archived = 0 LIMIT ?');
          $prodStmt->bindValue(1, $designId, PDO::PARAM_INT);
          $prodStmt->bindValue(2, $branch_id, PDO::PARAM_INT);
          $prodStmt->bindValue(3, $limit, PDO::PARAM_INT);
          $prodStmt->execute();
          foreach ($prodStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (in_array($row['product_id'], $usedProductIds)) continue;
            $usedProductIds[] = $row['product_id'];
            // Convert image blob to base64
            if (!empty($row['product_image'])) {
              $row['product_image'] = 'data:image/jpeg;base64,' . base64_encode($row['product_image']);
            } else {
              $row['product_image'] = null;
            }
            $recommendedProducts[] = $row;
          }
        }
      }
    }
    ?>
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
    <script>
    // Recommended products from PHP
    window.recommendedProducts = <?php echo json_encode($recommendedProducts); ?>;
    </script>

    <!-- Tile Selection Section -->
    <section class="bg-dark text-white py-12 md:py-20 text-center relative overflow-hidden mobile-padding">
      <div class="relative z-10">
        <div class="mb-8">
          <span class="block text-base md:text-[1.1rem] font-medium tracking-wider text-textlight mb-2">Explore Our Collection</span>
          <h2 class="text-2xl md:text-[2.5rem] font-black leading-tight text-white m-0" style="text-shadow:-1px -1px 0 #000,1px -1px 0 #000,-1px 1px 0 #000,1px 1px 0 #000;">Our Tile Selection</h2>
          <div class="text-sm md:text-[1.1rem] text-textlight max-w-full md:max-w-[700px] mx-auto mt-5 leading-relaxed">
            Discover our curated range of tile styles to suit every taste and space.
          </div>
        </div>
        <div class="flex flex-nowrap justify-start md:justify-center gap-4 md:gap-5 py-5 w-full overflow-x-auto scrollbar-hide px-2 md:px-0 tile-selection">
          <!-- Minimalist -->
          <div class="bg-white rounded-2xl overflow-hidden shadow-tile transition-all duration-300 relative min-w-[160px] md:min-w-[200px] w-[160px] md:w-[200px] flex flex-col border border-gray-100 cursor-pointer hover:shadow-lg hover:-translate-y-2 hover:scale-105">
            <div class="h-[120px] md:h-[150px] overflow-hidden relative flex-shrink-0">
              <img src="../images/user/minimalist.png" alt="Minimalist" class="w-full h-full object-cover bg-gray-100 transition-transform duration-500" />
            </div>
            <div class="p-3 md:p-4 text-center bg-white flex-1 flex flex-col justify-between">
              <h3 class="text-sm md:text-[1.1rem] font-bold text-gray-800 mb-2">Minimalist</h3>
              <p class="text-xs text-gray-600 mb-3 md:mb-4 flex-1">Sleek, simple, and modern tile designs for a clean look.</p>
              <button class="inline-flex items-center justify-center gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-primary text-white rounded-full font-semibold text-xs shadow-md w-full max-w-[140px] md:max-w-[160px] mx-auto transition-all duration-300 hover:bg-secondary hover:-translate-y-1"> <i class="fa fa-search text-xs"></i> Explore Now</button>
            </div>
          </div>
          <!-- Floral -->
          <div class="bg-white rounded-2xl overflow-hidden shadow-tile transition-all duration-300 relative min-w-[160px] md:min-w-[200px] w-[160px] md:w-[200px] flex flex-col border border-gray-100 cursor-pointer hover:shadow-lg hover:-translate-y-2 hover:scale-105">
            <div class="h-[120px] md:h-[150px] overflow-hidden relative flex-shrink-0">
              <img src="../images/user/floral.jpg" alt="Floral" class="w-full h-full object-cover bg-gray-100 transition-transform duration-500" />
            </div>
            <div class="p-3 md:p-4 text-center bg-white flex-1 flex flex-col justify-between">
              <h3 class="text-sm md:text-[1.1rem] font-bold text-gray-800 mb-2">Floral</h3>
              <p class="text-xs text-gray-600 mb-3 md:mb-4 flex-1">Beautiful floral patterns to bring nature indoors.</p>
              <button class="inline-flex items-center justify-center gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-primary text-white rounded-full font-semibold text-xs shadow-md w-full max-w-[140px] md:max-w-[160px] mx-auto transition-all duration-300 hover:bg-secondary hover:-translate-y-1"> <i class="fa fa-search text-xs"></i> Explore Now</button>
            </div>
          </div>
          <!-- Black and White -->
          <div class="bg-white rounded-2xl overflow-hidden shadow-tile transition-all duration-300 relative min-w-[160px] md:min-w-[200px] w-[160px] md:w-[200px] flex flex-col border border-gray-100 cursor-pointer hover:shadow-lg hover:-translate-y-2 hover:scale-105">
            <div class="h-[120px] md:h-[150px] overflow-hidden relative flex-shrink-0">
              <img src="../images/user/b&w.jpg" alt="Black and White" class="w-full h-full object-cover bg-gray-100 transition-transform duration-500" />
            </div>
            <div class="p-3 md:p-4 text-center bg-white flex-1 flex flex-col justify-between">
              <h3 class="text-sm md:text-[1.1rem] font-bold text-gray-800 mb-2">Black and White</h3>
              <p class="text-xs text-gray-600 mb-3 md:mb-4 flex-1">Classic monochrome tiles for timeless elegance.</p>
              <button class="inline-flex items-center justify-center gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-primary text-white rounded-full font-semibold text-xs shadow-md w-full max-w-[140px] md:max-w-[160px] mx-auto transition-all duration-300 hover:bg-secondary hover:-translate-y-1"> <i class="fa fa-search text-xs"></i> Explore Now</button>
            </div>
          </div>
          <!-- Modern -->
          <div class="bg-white rounded-2xl overflow-hidden shadow-tile transition-all duration-300 relative min-w-[160px] md:min-w-[200px] w-[160px] md:w-[200px] flex flex-col border border-gray-100 cursor-pointer hover:shadow-lg hover:-translate-y-2 hover:scale-105">
            <div class="h-[120px] md:h-[150px] overflow-hidden relative flex-shrink-0">
              <img src="../images/user/modern.jpg" alt="Modern" class="w-full h-full object-cover bg-gray-100 transition-transform duration-500" />
            </div>
            <div class="p-3 md:p-4 text-center bg-white flex-1 flex flex-col justify-between">
              <h3 class="text-sm md:text-[1.1rem] font-bold text-gray-800 mb-2">Modern</h3>
              <p class="text-xs text-gray-600 mb-3 md:mb-4 flex-1">Trendy and innovative tiles for contemporary spaces.</p>
              <button class="inline-flex items-center justify-center gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-primary text-white rounded-full font-semibold text-xs shadow-md w-full max-w-[140px] md:max-w-[160px] mx-auto transition-all duration-300 hover:bg-secondary hover:-translate-y-1"> <i class="fa fa-search text-xs"></i> Explore Now</button>
            </div>
          </div>
          <!-- Rustic -->
          <div class="bg-white rounded-2xl overflow-hidden shadow-tile transition-all duration-300 relative min-w-[160px] md:min-w-[200px] w-[160px] md:w-[200px] flex flex-col border border-gray-100 cursor-pointer hover:shadow-lg hover:-translate-y-2 hover:scale-105">
            <div class="h-[120px] md:h-[150px] overflow-hidden relative flex-shrink-0">
              <img src="../images/user/rustic.jpg" alt="Rustic" class="w-full h-full object-cover bg-gray-100 transition-transform duration-500" />
            </div>
            <div class="p-3 md:p-4 text-center bg-white flex-1 flex flex-col justify-between">
              <h3 class="text-sm md:text-[1.1rem] font-bold text-gray-800 mb-2">Rustic</h3>
              <p class="text-xs text-gray-600 mb-3 md:mb-4 flex-1">Warm, earthy tones and textures for a cozy, natural feel.</p>
              <button class="inline-flex items-center justify-center gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-primary text-white rounded-full font-semibold text-xs shadow-md w-full max-w-[140px] md:max-w-[160px] mx-auto transition-all duration-300 hover:bg-secondary hover:-translate-y-1"> <i class="fa fa-search text-xs"></i> Explore Now</button>
            </div>
          </div>
          <!-- Geometric -->
          <div class="bg-white rounded-2xl overflow-hidden shadow-tile transition-all duration-300 relative min-w-[160px] md:min-w-[200px] w-[160px] md:w-[200px] flex flex-col border border-gray-100 cursor-pointer hover:shadow-lg hover:-translate-y-2 hover:scale-105">
            <div class="h-[120px] md:h-[150px] overflow-hidden relative flex-shrink-0">
              <img src="../images/user/geometric.jpg" alt="Geometric" class="w-full h-full object-cover bg-gray-100 transition-transform duration-500" />
            </div>
            <div class="p-3 md:p-4 text-center bg-white flex-1 flex flex-col justify-between">
              <h3 class="text-sm md:text-[1.1rem] font-bold text-gray-800 mb-2">Geometric</h3>
              <p class="text-xs text-gray-600 mb-3 md:mb-4 flex-1">Bold shapes and patterns for a striking statement.</p>
              <button class="inline-flex items-center justify-center gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-primary text-white rounded-full font-semibold text-xs shadow-md w-full max-w-[140px] md:max-w-[160px] mx-auto transition-all duration-300 hover:bg-secondary hover:-translate-y-1"> <i class="fa fa-search text-xs"></i> Explore Now</button>
            </div>
          </div>
        </div>
      </div>
      <div class="absolute inset-0 z-0 pointer-events-none opacity-30" style="background:linear-gradient(45deg,rgba(125,49,10,0.05) 25%,transparent 25%),linear-gradient(-45deg,rgba(125,49,10,0.05) 25%,transparent 25%),linear-gradient(45deg,transparent 75%,rgba(125,49,10,0.05) 75%),linear-gradient(-45deg,transparent 75%,rgba(125,49,10,0.05) 75%);background-size:20px 20px;background-position:0 0,0 10px,10px -10px,-10px 0px;"></div>
    </section>

    <!-- Products Section -->
  <section id="premium-tiles" class="bg-light py-12 md:py-16 px-4 md:px-[5vw] text-textdark relative overflow-hidden">
  <div class="flex gap-6 md:gap-8 max-w-full md:max-w-[1500px] mx-auto flex-col md:flex-row relative z-10">
        <div class="w-full md:flex-[0_0_280px] bg-white p-6 md:p-8 rounded-2xl shadow-lg h-fit relative z-10 md:mb-0 mb-6">
          <h3 class="text-lg md:text-[1.25rem] font-bold text-primary mb-6">Categories</h3>
          <div class="border-b border-gray-200 pb-5 mb-5">
            <div id="tileCategoriesList" class="flex flex-col gap-3">
              <!-- Categories will be loaded here dynamically -->
            </div>
          </div>
          <h3 class="text-lg md:text-[1.25rem] font-bold text-primary mb-6">Popular & Best Seller</h3>
          <div class="border-b border-gray-200 pb-5 mb-5">
            <div class="flex flex-col gap-3">
              <label class="flex items-center text-sm text-textlight cursor-pointer hover:text-primary">
                <input type="checkbox" name="popular" class="mr-2 w-4 h-4 rounded border-2 border-gray-300 bg-gray-50 checked:bg-primary checked:border-primary transition-all" />
                Popular
              </label>
              <label class="flex items-center text-sm text-textlight cursor-pointer hover:text-primary">
                <input type="checkbox" name="bestseller" class="mr-2 w-4 h-4 rounded border-2 border-gray-300 bg-gray-50 checked:bg-primary checked:border-primary transition-all" />
                Best Seller
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
          <div id="premiumTilesGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
            <!-- Products will be loaded here dynamically -->
          </div>
    <script>

    // Dynamically load all tile designs for the modal and sidebar (filter)
    document.addEventListener('DOMContentLoaded', function() {
      fetch('processes/get_all_tile_categories.php')
        .then(r => r.json())
        .then(designs => {
          // Sidebar designs
          const catList = document.getElementById('tileCategoriesList');
          if (catList) {
            catList.innerHTML = '';
            designs.forEach(design => {
              const label = document.createElement('label');
              label.className = 'flex items-center text-sm text-textlight cursor-pointer hover:text-primary';
              label.innerHTML = `<input type="checkbox" name="design" value="${design.design_id}" class="mr-2 w-4 h-4 rounded border-2 border-gray-300 bg-gray-50 checked:bg-primary checked:border-primary transition-all" />${design.design_name}`;
              catList.appendChild(label);
            });
          }
        });
    });

    // Dynamically load all products for the selected branch in Premium Tiles section
    document.addEventListener('DOMContentLoaded', function() {
      fetch('processes/get_premium_tiles.php')
        .then(r => r.json())
        .then(products => {
          const grid = document.getElementById('premiumTilesGrid');
          if (grid) {
            grid.innerHTML = '';
            if (products.length === 0) {
              grid.innerHTML = '<div class="col-span-full text-center text-textlight py-8">No products found for this branch.</div>';
              return;
            }
            products.forEach(product => {
              const div = document.createElement('div');
              div.className = 'bg-white rounded-2xl overflow-hidden shadow-product transition-all duration-300 relative group';
              div.setAttribute('data-product-id', product.product_id);
              let badge = '';
              if (product.is_best_seller == 1) badge = '<span class="absolute top-4 left-4 bg-secondary text-white px-3 py-1 rounded text-xs font-bold uppercase z-10">Bestseller</span>';
              else if (product.is_popular == 1) badge = '<span class="absolute top-4 left-4 bg-primary text-white px-3 py-1 rounded text-xs font-bold uppercase z-10">Popular</span>';
              div.innerHTML = `
                ${badge}
                <div class="h-[200px] md:h-[250px] overflow-hidden relative">
                  <img src="${product.product_image || '../images/user/tile1.jpg'}" alt="${product.product_name}" class="w-full h-full object-cover bg-gray-100 transition-transform duration-300 group-hover:scale-105" />
                </div>
                <div class="p-4 md:p-5 text-center">
                  <h3 class="text-base md:text-[1.1rem] font-bold text-gray-800 mb-1">${product.product_name}</h3>
                  <div class="text-lg md:text-[1.25rem] font-extrabold text-secondary mb-4">₱${parseInt(product.product_price).toLocaleString()}</div>
                  <div class="flex flex-col justify-center gap-2 w-full mt-2">
                    <button class="view-product-btn w-full py-3 bg-primary text-white rounded-lg text-base font-bold transition-all hover:bg-secondary hover:-translate-y-1 shadow flex items-center justify-center gap-2"><i class="fa fa-eye text-base"></i> View Product</button>
                  </div>
                  <!-- No tile design badges -->
                </div>
              `;
              grid.appendChild(div);
            });
          }
        });
    });
    </script>
        </div>
      </div>
      <div class="pointer-events-none absolute left-0 right-0 bottom-0 h-[120px] w-full z-0" style="background: linear-gradient(to bottom, #ffece2 0%, #f8f5f2 100%);"></div>
    </section>

    <script>
      // Only use recommended products from the database
      const featuredItems = (window.recommendedProducts && window.recommendedProducts.length > 0)
        ? window.recommendedProducts.map(item => ({
            img: item.product_image || '../images/user/tile1.jpg',
            title: item.product_name,
            price: '₱' + parseInt(item.product_price).toLocaleString(),
            category: item.category_name || '',
            product: item
          }))
        : [];

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
              <span class="absolute top-3 left-3 bg-gradient-to-r from-primary to-secondary text-white text-xs font-bold px-3 py-1 rounded-full shadow-md z-10">${item.category || 'Featured'}</span>
              <div class="w-[100px] h-[100px] md:w-[120px] md:h-[120px] flex items-center justify-center bg-gradient-to-br from-accent/30 to-light rounded-xl mb-3 md:mb-4 shadow-inner overflow-hidden">
                <img src="${item.img}" alt="${item.title}" class="w-[90%] h-[90%] object-contain rounded-xl transition-transform duration-300 group-hover:scale-105 bg-gray-100 drop-shadow-lg" />
              </div>
              <div class="text-base md:text-[1.05rem] font-extrabold text-primary mb-1 text-center tracking-wide">${item.title}</div>
              <div class="text-md md:text-[1.1rem] font-extrabold text-secondary mb-3 text-center">${item.price}</div>
              <div class="flex flex-col justify-center gap-2 w-full mt-1">
                <button class="view-product-btn w-full py-3 bg-primary text-white rounded-lg text-base font-bold transition-all hover:bg-secondary hover:-translate-y-1 shadow flex items-center justify-center gap-2" data-idx="${index}"><i class="fa fa-eye text-base"></i> View Product</button>
              </div>
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
          // Redirect to the correct design-based product page
          const design = this.closest('.bg-white').querySelector('h3').textContent.trim().toLowerCase().replace(/ /g, '_');
          window.location.href = design + '_products.php';
        });
          });
          
          // Add event listeners for View Product buttons in both premium grid and carousel
          document.body.addEventListener('click', function(e) {
            // View Product button
            if (e.target.closest('.view-product-btn')) {
              e.preventDefault();
              const btn = e.target.closest('.view-product-btn');
              let productId = null;
              
              if (btn.hasAttribute('data-idx')) {
                // Carousel
                const idx = parseInt(btn.getAttribute('data-idx'));
                const prod = window.recommendedProducts && window.recommendedProducts[idx] ? window.recommendedProducts[idx] : null;
                if (prod && prod.product_id) {
                  productId = prod.product_id;
                }
              } else {
                // Premium grid
                const card = btn.closest('.bg-white');
                if (card) {
                  productId = card.getAttribute('data-product-id');
                }
              }
              
              if (productId) {
                // Redirect to product detail page
                window.location.href = 'product_detail.php?id=' + encodeURIComponent(productId);
              }
              return;
            }
          });
          
          // Apply filters button
          const applyFiltersBtn = document.querySelector('.bg-white button:not(:has(.fa-eye))');
        // Removed filter confirmation popup
      });
    </script>
  </body>
</html>