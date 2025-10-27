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
    <!-- Custom styles for filter sidebar and controls -->
    <style>
      .scrollbar-hide {-ms-overflow-style:none;scrollbar-width:none;}
      .scrollbar-hide::-webkit-scrollbar{display:none;}
      @media (max-width:768px){.mobile-padding{padding-left:1rem;padding-right:1rem;}.mobile-margin{margin-left:1rem;margin-right:1rem;}.mobile-text-center{text-align:center;}.mobile-flex-col{flex-direction:column;}}
      .featured-items-container{overflow:hidden;width:100%;}
      .featured-items{display:flex;transition:transform 0.5s ease;}
      .featured-item{flex:0 0 auto;margin:0 16px;}
      .featured-dot{display:inline-block;width:10px;height:10px;border-radius:50%;background-color:#ccc;margin:0 5px;cursor:pointer;transition:background-color 0.3s;}
      .featured-dot.active{background-color:#7d310a;}
      @keyframes float{0%,100%{transform:rotate(40deg)translateY(0);}50%{transform:rotate(40deg)translateY(-20px);}}
      /* Modern toggle switch styles */
      .switch{position:relative;display:inline-block;width:44px;height:24px;}
      .switch input{display:none;}
      .slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background-color:#e8e8e8;transition:.4s;border-radius:24px;}
      .slider:before{position:absolute;content:"";height:18px;width:18px;left:3px;bottom:3px;background-color:#fff;transition:.4s;border-radius:50%;box-shadow:0 2px 8px rgba(207,135,86,0.10);}
      input:checked+.slider{background-color:#cf8756;}
      input:checked+.slider:before{transform:translateX(20px);background-color:#7d310a;}
      .slider.round{border-radius:24px;}
      .slider-thumb::-webkit-slider-thumb{appearance:none;width:18px;height:18px;background:#cf8756;border-radius:50%;box-shadow:0 2px 8px rgba(207,135,86,0.10);cursor:pointer;border:2px solid #fff;}
      .slider-thumb::-moz-range-thumb{width:18px;height:18px;background:#cf8756;border-radius:50%;box-shadow:0 2px 8px rgba(207,135,86,0.10);cursor:pointer;border:2px solid #fff;}
      @media (max-width:768px){.switch{width:38px;height:20px;}.slider:before{height:14px;width:14px;left:3px;bottom:3px;}.slider-thumb::-webkit-slider-thumb{width:14px;height:14px;}.slider-thumb::-moz-range-thumb{width:14px;height:14px;}}
      /* Category pill styles */
      #tileCategoriesList label{display:flex;align-items:center;background:#f9f5f2;border-radius:18px;padding:7px 14px;font-weight:600;color:#7d310a;cursor:pointer;border:2px solid #e8a56a;transition:background .2s,color .2s,border .2s;margin-bottom:0;}
      #tileCategoriesList label input[type="checkbox"]{margin-right:8px;accent-color:#cf8756;}
      #tileCategoriesList label.selected{background:linear-gradient(90deg,#cf8756 0%,#e8a56a 100%);color:#fff;border-color:#cf8756;}
    </style>
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
    // Toast notification function for cart addition
    function showCartToast(message) {
      let toast = document.createElement('div');
      toast.className = 'fixed top-4 right-4 z-[100] bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg font-semibold text-lg flex items-center gap-3 animate-fade-in-up';
      toast.innerHTML = '<i class="fas fa-check-circle text-xl"></i> ' + message;
      document.body.appendChild(toast);
      setTimeout(() => {
        toast.classList.add('opacity-0');
        setTimeout(() => toast.remove(), 600);
      }, 3000);
    }
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
  <!-- Custom styles for filter sidebar and controls -->
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
        .mobile-padding { padding-left: 1rem; padding-right: 1rem; }
        .mobile-margin { margin-left: 1rem; margin-right: 1rem; }
        .mobile-text-center { text-align: center; }
        .mobile-flex-col { flex-direction: column; }
      }
      .featured-items-container { overflow: hidden; width: 100%; }
      .featured-items { display: flex; transition: transform 0.5s ease; }
      .featured-item { flex: 0 0 auto; margin: 0 16px; }
      .featured-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: #ccc; margin: 0 5px; cursor: pointer; transition: background-color 0.3s; }
      .featured-dot.active { background-color: #7d310a; }
      @keyframes float { 0%,100% { transform: rotate(40deg) translateY(0); } 50% { transform: rotate(40deg) translateY(-20px); } }

      /* Modern toggle switch styles */
      .switch { position: relative; display: inline-block; width: 44px; height: 24px; }
      .switch input { display: none; }
      .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e8e8e8; transition: .4s; border-radius: 24px; }
      .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: #fff; transition: .4s; border-radius: 50%; box-shadow: 0 2px 8px rgba(207,135,86,0.10); }
      input:checked + .slider { background-color: #cf8756; }
      input:checked + .slider:before { transform: translateX(20px); background-color: #7d310a; }
      .slider.round { border-radius: 24px; }
      .slider-thumb::-webkit-slider-thumb { appearance: none; width: 18px; height: 18px; background: #cf8756; border-radius: 50%; box-shadow: 0 2px 8px rgba(207,135,86,0.10); cursor: pointer; border: 2px solid #fff; }
      .slider-thumb::-moz-range-thumb { width: 18px; height: 18px; background: #cf8756; border-radius: 50%; box-shadow: 0 2px 8px rgba(207,135,86,0.10); cursor: pointer; border: 2px solid #fff; }
      @media (max-width: 768px) {
        .switch { width: 38px; height: 20px; }
        .slider:before { height: 14px; width: 14px; left: 3px; bottom: 3px; }
        .slider-thumb::-webkit-slider-thumb { width: 14px; height: 14px; }
        .slider-thumb::-moz-range-thumb { width: 14px; height: 14px; }
      }
      /* Category pill styles */
      #tileCategoriesList label { display: flex; align-items: center; background: #f9f5f2; border-radius: 18px; padding: 7px 14px; font-weight: 600; color: #7d310a; cursor: pointer; border: 2px solid #e8a56a; transition: background .2s, color .2s, border .2s; margin-bottom: 0; }
      #tileCategoriesList label input[type="checkbox"] { margin-right: 8px; accent-color: #cf8756; }
      #tileCategoriesList label.selected { background: linear-gradient(90deg,#cf8756 0%,#e8a56a 100%); color: #fff; border-color: #cf8756; }
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
              <div class="carousel-slide absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 transition-opacity duration-700 ease-in-out <?php echo $i === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0'; ?>" data-slide="<?php echo $i; ?>" style="width:100vw;max-width:1280px;aspect-ratio:16/9;">
                <?php if ($banner['banner_image']): ?>
                  <img src="<?php echo htmlspecialchars($banner['banner_image']); ?>" alt="Branch Banner <?php echo $i+1; ?>" class="w-full h-full object-cover object-center scale-105 group-hover:scale-110 transition-transform duration-1000 rounded-xl" style="width:100%;height:100%;aspect-ratio:16/9;max-width:1280px;max-height:720px;" />
                <?php else: ?>
                  <div class="w-full h-full flex items-center justify-center bg-gray-300 text-gray-500 text-2xl font-bold rounded-xl" style="aspect-ratio:16/9;">No Image</div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="w-full h-full flex items-center justify-center bg-gray-300 text-gray-500 text-2xl font-bold rounded-xl" style="aspect-ratio:16/9;">No Banners Available</div>
          <?php endif; ?>
          <!-- Carousel Controls -->
          <?php if (count($branchBanners) > 1): ?>
          <button id="carouselPrev" class="absolute left-4 md:left-8 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-primary text-primary hover:text-white rounded-full w-12 h-12 md:w-14 md:h-14 flex items-center justify-center shadow-lg transition-all z-30 border-2 border-primary/30">
            <i class="fas fa-chevron-left text-xl md:text-2xl"></i>
          </button>
          <button id="carouselNext" class="absolute right-4 md:right-8 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-primary text-primary hover:text-white rounded-full w-12 h-12 md:w-14 md:h-14 flex items-center justify-center shadow-lg transition-all z-30 border-2 border-primary/30">
            <i class="fas fa-chevron-right text-xl md:text-2xl"></i>
          </button>
          <?php endif; ?>
          <!-- Carousel Dots -->
          <?php if (count($branchBanners) > 1): ?>
          <div class="absolute bottom-6 md:bottom-10 left-1/2 -translate-x-1/2 flex gap-2 md:gap-3 z-30">
            <?php foreach ($branchBanners as $i => $banner): ?>
              <span class="carousel-dot w-4 h-4 md:w-5 md:h-5 rounded-full bg-white border-2 border-primary cursor-pointer transition-all <?php echo $i === 0 ? 'bg-primary' : 'bg-white'; ?>" data-dot="<?php echo $i; ?>"></span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <!-- Removed duplicate style block for filter and animation -->
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
    // --- AI-powered recommendation system (Local Python Flask API) ---
    $recommendedProducts = [];
    function getAIRecommendations($user_id) {
      $apiUrl = 'http://localhost:5000/recommend';
      $payload = json_encode(['user_id' => $user_id]);
      $ch = curl_init($apiUrl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
      curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
      $response = curl_exec($ch);
      curl_close($ch);
      if ($response) {
        $data = json_decode($response, true);
        if (isset($data['recommendations'])) {
          return $data['recommendations'];
        }
      }
      return [];
    }

    if (isset($_SESSION['user_id'])) {
      $user_id = $_SESSION['user_id'];
      $recommendedProducts = getAIRecommendations($user_id);
      // Fallback to old logic if API fails
      if (empty($recommendedProducts) && isset($_SESSION['branch_id'])) {
        require_once '../connection/connection.php';
        $branch_id = (int)$_SESSION['branch_id'];
        $designStmt = $conn->prepare('SELECT design_id FROM user_design_preferences WHERE user_id = ? ORDER BY rank ASC');
        $designStmt->execute([$user_id]);
        $userDesigns = $designStmt->fetchAll(PDO::FETCH_COLUMN);
        if ($userDesigns) {
          $designWeights = [0=>4, 1=>2, 2=>1];
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
    }
    ?>
    <!-- AI-powered Recommendation Modal Section -->
    <section class="relative bg-gradient-to-br from-light via-white to-accent/30 text-textdark py-12 md:py-20 text-center mobile-padding overflow-x-hidden">
      <div class="mb-8">
        <span class="block text-base md:text-[1.1rem] font-medium tracking-wider text-secondary mb-2 animate-fade">AI-powered Recommendations</span>
        <h2 class="text-2xl md:text-[2.5rem] font-black leading-tight text-primary m-0 animate-slide">Recommended For You</h2>
        <div class="text-sm md:text-[1.1rem] text-textlight max-w-full md:max-w-[700px] mx-auto mt-5 leading-relaxed animate-fade">
          Discover your personalized tile selection powered by machine learning.
        </div>
      </div>
      <div class="flex flex-wrap items-center justify-center gap-6 md:gap-10 relative max-w-full w-full mx-auto mt-10 animate-tiles">
        <?php if (!empty($recommendedProducts)): ?>
          <div id="recommendationGridJS" class="flex flex-wrap items-center justify-center gap-6 md:gap-10 w-full"></div>
          <script>
            // User preferences: 1st, 2nd, 3rd place
            const recommendedProducts = <?php echo json_encode($recommendedProducts); ?>;
            // Simulate user preferences (replace with actual)
            const userPreferences = window.USER_PREFERENCES || ['minimalist','floral','modern'];
            // 1st: 3 tiles, 2nd: 2 tiles, 3rd: 1 tile
            const preferenceCounts = [3,2,1];
            const grid = document.getElementById('recommendationGridJS');
            function getProductsByPreference(prefId, count) {
              const filtered = recommendedProducts.filter(prod => (prod.design_id === prefId || prod.design === prefId || prod.design_name === prefId));
              return filtered.sort(() => Math.random() - 0.5).slice(0, count);
            }
            function renderRandomProducts() {
              grid.innerHTML = '';
        let cards = [];
              for (let i = 0; i < userPreferences.length; i++) {
                const prefId = userPreferences[i];
                const count = preferenceCounts[i];
                cards = cards.concat(getProductsByPreference(prefId, count));
              }
              // If no cards found, fallback to all recommended products
              if (cards.length === 0) {
                cards = recommendedProducts.slice().sort(() => Math.random() - 0.5).slice(0, 6);
              }
              // Shuffle all selected cards for animation
              cards = cards.sort(() => Math.random() - 0.5);
              cards.forEach((prod, idx) => {
                const card = document.createElement('div');
                card.className = 'recommendation-item bg-white rounded-3xl shadow-2xl p-5 md:p-7 w-[240px] md:w-[280px] flex flex-col items-center border-2 border-transparent transition-all duration-500 group hover:shadow-3xl hover:-translate-y-2 hover:scale-105 hover:border-primary overflow-hidden relative fade-in';
                card.style.animationDelay = (idx * 0.15) + 's';
                card.innerHTML = `
                  <span class="absolute top-3 left-3 bg-gradient-to-r from-primary to-secondary text-white text-xs font-bold px-3 py-1 rounded-full shadow-md z-10 animate-bounce">
                    ${prod.design ?? prod.design_name ?? 'Featured'}
                  </span>
                  <div class="relative w-[120px] h-[120px] mb-4 flex items-center justify-center">
                    <img src="${prod.image ?? prod.product_image ?? '../images/user/tile1.jpg'}" alt="${prod.name ?? prod.product_name}" class="w-full h-full object-cover rounded-2xl shadow-lg group-hover:brightness-90 transition duration-300" />
                  </div>
                  <div class="text-base md:text-[1.1rem] font-extrabold text-primary mb-1 text-center group-hover:text-secondary transition-colors duration-200">
                    ${prod.name ?? prod.product_name}
                  </div>
                  <div class="text-md md:text-[1.15rem] font-extrabold text-secondary mb-3 text-center animate-fade">
                    ₱${prod.price ?? prod.product_price}
                  </div>
                  <div class="flex gap-2 mt-2 w-full justify-center">
                    <button class="bg-primary text-white px-5 py-2 rounded-full font-semibold shadow hover:bg-secondary transition-all duration-200 text-sm flex items-center gap-2 view-product-btn" data-product-id="${prod.product_id}"><i class="fa fa-eye"></i> View</button>
                    <button class="bg-white border border-primary text-primary px-5 py-2 rounded-full font-semibold shadow hover:bg-primary hover:text-white transition-all duration-200 text-sm flex items-center gap-2 add-to-cart-btn" data-product-id="${prod.product_id}" data-product-name="${prod.name ?? prod.product_name}"><i class="fa fa-cart-plus"></i> Add</button>
                  </div>
                `;
                grid.appendChild(card);
                // Add event listener for View buttons
                grid.querySelectorAll('.view-product-btn').forEach(btn => {
                  btn.addEventListener('click', function(e) {
                    const productId = btn.getAttribute('data-product-id');
                    if (productId) {
                      window.location.href = 'product_detail.php?id=' + encodeURIComponent(productId);
                    }
                  });
                });
                // Add event listener for Add to Cart buttons
                grid.querySelectorAll('.add-to-cart-btn').forEach(btn => {
                  btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productId = btn.getAttribute('data-product-id');
                    const productName = btn.getAttribute('data-product-name');
                    document.getElementById('recommendationProductId').value = productId;
                    document.getElementById('recommendationProductName').textContent = productName;
                    document.getElementById('recommendationQuantity').value = 1;
                    const recQtyModal = document.getElementById('recommendationQtyModal');
                    const modalBox = document.getElementById('recommendationQtyModalBox');
                    // Reset modal state before showing
                    recQtyModal.classList.remove('hidden');
                    recQtyModal.style.display = 'flex';
                    recQtyModal.style.opacity = '1';
                    recQtyModal.style.zIndex = '99999';
                    recQtyModal.style.backdropFilter = 'blur(2px)';
                    recQtyModal.style.background = 'rgba(40, 20, 10, 0.18)';
                    modalBox.classList.remove('modal-animate-out');
                    modalBox.classList.add('modal-animate');
                    modalBox.style.opacity = '1';
                    modalBox.style.transform = 'scale(1)';
                    document.body.style.overflow = 'hidden';
                  });
                });
              });
            }
            renderRandomProducts();
            setInterval(() => {
              // Animate out
              Array.from(grid.children).forEach(card => {
                card.classList.remove('fade-in');
                card.classList.add('fade-out');
              });
              setTimeout(() => {
                renderRandomProducts();
              }, 500);
            }, 10000);
          </script>
          <style>
            .fade-in { animation: fadeInAnim 0.7s cubic-bezier(0.4,0,0.2,1) both; }
            .fade-out { animation: fadeOutAnim 0.5s cubic-bezier(0.4,0,0.2,1) both; }
            @keyframes fadeInAnim { from { opacity: 0; transform: translateY(30px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
            @keyframes fadeOutAnim { from { opacity: 1; transform: scale(1); } to { opacity: 0; transform: scale(0.95); } }
          </style>
        <?php else: ?>
          <div class="text-center text-textlight py-8 animate-fade">No recommendations found. Try updating your preferences.</div>
        <?php endif; ?>
      </div>
      <style>
        .animate-move { animation: moveAnim 1.2s infinite alternate; }
        @keyframes moveAnim { from { transform: translateY(0); } to { transform: translateY(-10px); } }
        .animate-fade { animation: fadeAnim 1.2s ease-in; }
        @keyframes fadeAnim { from { opacity: 0; } to { opacity: 1; } }
        .animate-slide { animation: slideAnim 1.1s cubic-bezier(0.4,0,0.2,1); }
        @keyframes slideAnim { from { transform: translateX(-40px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .animate-zoom { animation: zoomAnim 1.2s cubic-bezier(0.4,0,0.2,1); }
        @keyframes zoomAnim { from { transform: scale(0.8); opacity: 0.7; } to { transform: scale(1); opacity: 1; } }
        .animate-bounce { animation: bounceAnim 1.2s infinite alternate; }
        @keyframes bounceAnim { from { transform: translateY(0); } to { transform: translateY(-6px); } }
        .animate-tiles { animation: fadeAnim 1.2s ease-in; }
      </style>
      <script>
        function animateSelection(btn, productId) {
          btn.classList.add('selected-animate');
          setTimeout(function() {
            btn.classList.remove('selected-animate');
            savePreference(productId);
          }, 700);
        }
        function savePreference(productId) {
          // AJAX call to save user preference
          fetch('save_recommendations.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'product_id=' + encodeURIComponent(productId)
          }).then(r => r.json()).then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Preference Saved!',
                text: 'Your recommendation has been saved.',
                timer: 1800,
                showConfirmButton: false
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Could not save your preference.',
                timer: 1800,
                showConfirmButton: false
              });
            }
          });
        }
      </script>
      <div class="pointer-events-none absolute inset-0 z-0 opacity-10 bg-[url('../images/user/landingpagetile1.png')] bg-no-repeat bg-right-bottom bg-contain"></div>
  <!-- Add to Cart Modal for Recommendations -->
  <div id="recommendationQtyModal" class="fixed inset-0 z-50 flex items-center justify-center modal-overlay-product hidden">
    <div id="recommendationQtyModalBox" class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm mx-4 relative border border-gray-200 scale-95 opacity-0 transition-all duration-300 modal-animate">
      <button type="button" id="closeRecommendationQtyModal" class="absolute top-4 right-4 text-gray-400 hover:text-primary-product text-lg focus:outline-none">
        <i class="fa fa-times"></i>
      </button>
      <h2 class="text-lg font-semibold text-primary mb-4 flex items-center gap-2">
        <i class="fa fa-shopping-cart"></i> <span style="color:#7d310a">Add to Cart</span>
      </h2>
      <div class="mb-2 text-base font-bold text-primary text-center" id="recommendationProductName"></div>
      <form action="processes/add_to_cart.php" method="POST" class="space-y-4" id="recommendationAddToCartForm">
        <input type="hidden" name="product_id" id="recommendationProductId" value="">
        <input type="hidden" name="branch_id" value="<?php echo isset($_SESSION['branch_id']) ? intval($_SESSION['branch_id']) : 1; ?>">
        <div>
          <label for="recommendationQuantity" class="block text-sm font-semibold text-primary mb-2">Quantity</label>
          <input type="number" name="quantity" id="recommendationQuantity" min="1" value="1" required 
             class="w-full px-3 py-2 rounded-lg border-2 border-primary focus:border-secondary focus:ring-2 focus:ring-primary/20 text-base font-bold text-primary bg-white transition-all duration-200">
        </div>
        <button type="submit" id="submitRecommendationAddToCart" class="w-full py-3 rounded-xl font-bold text-base transition-all duration-200 shadow-xl flex items-center justify-center gap-2" style="background: linear-gradient(90deg, #cf8756 0%, #e8a56a 100%); color: #fff;">
          <i class="fa fa-cart-plus"></i> <span style="color: #fff; font-weight: 600;">Add to Cart</span>
        </button>
      </form>
    </div>
   
  </div>
    
    </section>
    <script>

 // Modal animation styles
    const modalAnimStyle = document.createElement('style');
    modalAnimStyle.innerHTML = `
      #recommendationQtyModal {
        background: rgba(40, 20, 10, 0.18);
        backdrop-filter: blur(2px);
        transition: background 0.3s;
      }
      #recommendationQtyModalBox.modal-animate {
        animation: modalFadeIn 0.35s cubic-bezier(0.4,0,0.2,1) forwards;
      }
      #recommendationQtyModalBox.modal-animate-out {
        animation: modalFadeOut 0.25s cubic-bezier(0.4,0,0.2,1) forwards;
      }
      @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
      }
      @keyframes modalFadeOut {
        from { opacity: 1; transform: scale(1); }
        to { opacity: 0; transform: scale(0.95); }
      }
    `;
    document.head.appendChild(modalAnimStyle);
                const modalBox = document.getElementById('recommendationQtyModalBox');
                modalBox.classList.remove('modal-animate-out');
                modalBox.classList.add('modal-animate');
                setTimeout(() => {
                  modalBox.style.opacity = '1';
                  modalBox.style.transform = 'scale(1)';
                }, 10);
      // Add to Cart Modal logic for Recommendations
    document.addEventListener('DOMContentLoaded', function() {
      var recQtyModal = document.getElementById('recommendationQtyModal');
      var closeRecBtn = document.getElementById('closeRecommendationQtyModal');
      var addToCartForm = document.getElementById('recommendationAddToCartForm');
      if (closeRecBtn && recQtyModal) {
        closeRecBtn.addEventListener('click', function() {
          const modalBox = document.getElementById('recommendationQtyModalBox');
          modalBox.classList.remove('modal-animate');
          modalBox.classList.add('modal-animate-out');
          setTimeout(() => {
            recQtyModal.classList.add('hidden');
            recQtyModal.style.display = 'none';
            recQtyModal.style.opacity = '0';
            recQtyModal.style.zIndex = '';
            recQtyModal.style.backdropFilter = '';
            recQtyModal.style.background = '';
            modalBox.style.opacity = '0';
            modalBox.style.transform = 'scale(0.95)';
            document.body.style.overflow = '';
          }, 250);
        });
      }
      // Close modal on outside click
      // Prevent outside click from closing modal
      // AJAX submit for add to cart
      if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(e) {
          e.preventDefault();
          var formData = new FormData(addToCartForm);
          fetch('processes/add_to_cart.php', {
            method: 'POST',
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
                            // Close modal and reset styles
              recQtyModal.classList.add('hidden');
              recQtyModal.style.display = 'none';
              recQtyModal.style.opacity = '0';
              recQtyModal.style.zIndex = '';
              recQtyModal.style.backdropFilter = '';
              recQtyModal.style.background = '';
              var modalBox = document.getElementById('recommendationQtyModalBox');
              modalBox.style.opacity = '0';
              modalBox.style.transform = 'scale(0.95)';
              recQtyModal.classList.add('hidden');
              document.body.style.overflow = '';
              showCartToast('Product Successfully Added to your Cart');
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Could not add to cart.',
                timer: 1800,
                showConfirmButton: false
              });
            }
          })
          .catch(() => {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Could not add to cart.',
              timer: 1800,
              showConfirmButton: false
            });
          });
        });
      }
    });
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
            <h3 class="text-xl md:text-2xl font-extrabold text-primary mb-2 tracking-tight">Best For</h3>
            <div id="bestForCategoriesList" class="flex flex-wrap gap-2 mb-4">
              <!-- Best For categories will be loaded here dynamically -->
            </div>
            <h3 class="text-xl md:text-2xl font-extrabold text-primary mb-2 tracking-tight">Tile Type</h3>
            <div class="flex flex-col gap-3 mb-4">
              <div class="flex items-center justify-between bg-light rounded-lg px-3 py-2">
                <span class="text-sm font-semibold text-textdark">Popular</span>
                <label class="switch">
                  <input type="checkbox" name="popular" id="filter-popular" />
                  <span class="slider round"></span>
                </label>
              </div>
              <div class="flex items-center justify-between bg-light rounded-lg px-3 py-2">
                <span class="text-sm font-semibold text-textdark">Best Seller</span>
                <label class="switch">
                  <input type="checkbox" name="bestseller" id="filter-bestseller" />
                  <span class="slider round"></span>
                </label>
              </div>
              <!-- 'Other' removed from Tile Type -->
            </div>
            <h3 class="text-xl md:text-2xl font-extrabold text-primary mb-2 tracking-tight">Price Range</h3>
            <div class="flex flex-col gap-4 mb-2">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-textdark">Min: ₱<span id="price-min-value">100</span></span>
                <span class="text-sm font-semibold text-textdark">Max: ₱<span id="price-max-value">5000</span></span>
              </div>
              <div class="flex items-center gap-2">
                <input type="range" id="price-min" min="100" max="5000" value="100" class="w-full h-2 bg-gray-200 rounded-lg outline-none slider-thumb" />
                <input type="range" id="price-max" min="100" max="5000" value="5000" class="w-full h-2 bg-gray-200 rounded-lg outline-none slider-thumb" />
              </div>
            </div>
            <button id="clearFilters" class="w-full py-3 bg-gray-200 text-gray-700 rounded-lg text-base font-bold mt-3 transition-all hover:bg-gray-300">Clear Filters</button>
            <div class="mt-7">
              <h3 class="text-xl md:text-2xl font-extrabold text-primary mb-2 tracking-tight">Other Products</h3>
              <div class="flex items-center justify-between bg-light rounded-lg px-3 py-2">
                <span class="text-sm font-semibold text-textdark">Show Other Products</span>
                <label class="switch">
                  <input type="checkbox" name="other" id="filter-other" />
                  <span class="slider round"></span>
                </label>
              </div>
            </div>
          
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex justify-between items-center mb-5 flex-col md:flex-row gap-3 md:gap-0">
            <div class="text-center md:text-left">
              <h2 class="text-2xl md:text-[2.5rem] font-black text-primary m-0">Premium Tiles And More</h2>
              <p class="text-sm md:text-base text-textlight m-0 mt-2">Browse our extensive collection of premium tiles for every room in your home or business.</p>
            </div>
            <div class="flex items-center gap-3">
              <div class="relative">
                <select id="sortProducts" class="appearance-none bg-white border border-gray-300 rounded-lg py-2 pl-3 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                  <option value="name_asc">Name: A-Z</option>
                  <option value="name_desc">Name: Z-A</option>
                  <option value="price_asc">Price: Low to High</option>
                  <option value="price_desc">Price: High to Low</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                  <i class="fas fa-chevron-down text-xs"></i>
                </div>
              </div>
            </div>
          </div>
          <div id="premiumTilesGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
            <!-- Products will be loaded here dynamically -->
          </div>
          <div id="premiumTilesPagination" class="flex justify-center items-center mt-8 gap-2">
            <!-- Pagination will be generated here -->
          </div>
        </div>
      </div>
      <div class="pointer-events-none absolute left-0 right-0 bottom-0 h-[120px] w-full z-0" style="background: linear-gradient(to bottom, #ffece2 0%, #f8f5f2 100%);"></div>
    </section>

    <script>
    // Premium Tiles Pagination and Filtering Logic
    let currentPage = 1;
    const itemsPerPage = 9;
    let allProducts = [];
    let filteredProducts = [];

    // Initialize price range slider
    function initPriceRangeSlider() {
      const minSlider = document.getElementById('price-min');
      const maxSlider = document.getElementById('price-max');
      const minValue = document.getElementById('price-min-value');
      const maxValue = document.getElementById('price-max-value');
      
      if (!minSlider || !maxSlider) return;
      
      function updateMinValue() {
        const value = Math.min(parseInt(minSlider.value), parseInt(maxSlider.value) - 100);
        minValue.textContent = value.toLocaleString();
        minSlider.value = value;
      }
      
      function updateMaxValue() {
        const value = Math.max(parseInt(maxSlider.value), parseInt(minSlider.value) + 100);
        maxValue.textContent = value.toLocaleString();
        maxSlider.value = value;
      }
      minSlider.addEventListener('input', updateMinValue);
      maxSlider.addEventListener('input', updateMaxValue);
      minSlider.addEventListener('touchmove', updateMinValue);
      maxSlider.addEventListener('touchmove', updateMaxValue);
    }

    // Filter products based on selected criteria
    function filterProducts() {
  const selectedBestFor = Array.from(document.querySelectorAll('input[name="best_for"]:checked')).map(cb => cb.value);
      const isPopular = document.querySelector('input[name="popular"]').checked;
      const isBestSeller = document.querySelector('input[name="bestseller"]').checked;
      const minPrice = parseInt(document.getElementById('price-min').value);
      const maxPrice = parseInt(document.getElementById('price-max').value);
      const sortBy = document.getElementById('sortProducts').value;

      filteredProducts = allProducts.filter(product => {
        // Filter by Best For category
        if (selectedBestFor.length > 0) {
          if (!product.best_for_ids || !product.best_for_ids.some(id => selectedBestFor.includes(id.toString()))) {
            return false;
          }
        }
        // Filter by popularity/bestseller
        if (isPopular && !product.is_popular) {
          return false;
        }
        if (isBestSeller && !product.is_best_seller) {
          return false;
        }
        // Filter by 'Other Products' toggle
        const isOther = document.querySelector('input[name="other"]').checked;
        if (isOther) {
          return product.product_type === 'other';
        } else if (product.product_type === 'other') {
          // Hide 'other' products unless toggle is checked
          return false;
        }
        // Filter by price
        const price = parseInt(product.product_price);
        if (price < minPrice || price > maxPrice) {
          return false;
        }
        return true;
      });

      // Sort products
      switch(sortBy) {
        case 'name_asc':
          filteredProducts.sort((a, b) => a.product_name.localeCompare(b.product_name));
          break;
        case 'name_desc':
          filteredProducts.sort((a, b) => b.product_name.localeCompare(a.product_name));
          break;
        case 'price_asc':
          filteredProducts.sort((a, b) => parseInt(a.product_price) - parseInt(b.product_price));
          break;
        case 'price_desc':
          filteredProducts.sort((a, b) => parseInt(b.product_price) - parseInt(a.product_price));
          break;
      }

      currentPage = 1;
      animateProductGrid();
      renderPagination();
    }

    // Render products for current page
    function renderProducts() {
      const grid = document.getElementById('premiumTilesGrid');
      if (!grid) return;

      const startIndex = (currentPage - 1) * itemsPerPage;
      const endIndex = startIndex + itemsPerPage;
      const productsToShow = filteredProducts.slice(startIndex, endIndex);

      grid.innerHTML = '';

      if (productsToShow.length === 0) {
        grid.innerHTML = '<div class="col-span-full text-center text-textlight py-8">No products found matching your criteria.</div>';
        return;
      }

      productsToShow.forEach(product => {
        const div = document.createElement('div');
        div.className = 'bg-white rounded-2xl overflow-hidden shadow-product transition-all duration-300 relative group product-tile-anim';
        div.setAttribute('data-product-id', product.product_id);

        let badge = '';
        if (product.is_best_seller == 1) {
          badge = '<span class="absolute top-4 left-4 bg-secondary text-white px-3 py-1 rounded text-xs font-bold uppercase z-10">Bestseller</span>';
        } else if (product.is_popular == 1) {
          badge = '<span class="absolute top-4 left-4 bg-primary text-white px-3 py-1 rounded text-xs font-bold uppercase z-10">Popular</span>';
        } else if (product.product_type === 'other') {
          badge = '<span class="absolute top-4 left-4 bg-accent text-white px-3 py-1 rounded text-xs font-bold uppercase z-10">Other</span>';
        }

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
          </div>
        `;
        grid.appendChild(div);
      });
    }
    // Animation for product grid
    function animateProductGrid() {
      const grid = document.getElementById('premiumTilesGrid');
      if (!grid) return;
      grid.classList.remove('fade-in-anim');
      void grid.offsetWidth; // force reflow
      grid.classList.add('fade-in-anim');
      setTimeout(() => {
        grid.classList.remove('fade-in-anim');
      }, 600);
      renderProducts();
    }
    // Add animation CSS
    const styleAnim = document.createElement('style');
    styleAnim.innerHTML = `
      .fade-in-anim { animation: fadeInGrid 0.6s cubic-bezier(0.4,0,0.2,1); }
      @keyframes fadeInGrid { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
      .product-tile-anim { animation: fadeInTile 0.7s cubic-bezier(0.4,0,0.2,1); }
      @keyframes fadeInTile { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    `;
    document.head.appendChild(styleAnim);

    // Render pagination controls
    function renderPagination() {
      const pagination = document.getElementById('premiumTilesPagination');
      if (!pagination) return;
      
      const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);
      
      if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
      }
      
      let html = '';
      
      // Previous button
      if (currentPage > 1) {
        html += `<button class="pagination-btn prev bg-white border border-primary text-primary rounded-lg px-3 py-2 text-sm font-medium hover:bg-primary hover:text-white transition-all" data-page="${currentPage - 1}">
          <i class="fas fa-chevron-left mr-1"></i> Previous
        </button>`;
      }
      
      // Page numbers
      const maxVisiblePages = 5;
      let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
      let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
      
      if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
      }
      
      for (let i = startPage; i <= endPage; i++) {
        if (i === currentPage) {
          html += `<button class="pagination-btn page bg-primary text-white rounded-lg px-3 py-2 text-sm font-medium" data-page="${i}">${i}</button>`;
        } else {
          html += `<button class="pagination-btn page bg-white border border-primary text-primary rounded-lg px-3 py-2 text-sm font-medium hover:bg-primary hover:text-white transition-all" data-page="${i}">${i}</button>`;
        }
      }
      
      // Next button
      if (currentPage < totalPages) {
        html += `<button class="pagination-btn next bg-white border border-primary text-primary rounded-lg px-3 py-2 text-sm font-medium hover:bg-primary hover:text-white transition-all" data-page="${currentPage + 1}">
          Next <i class="fas fa-chevron-right ml-1"></i>
        </button>`;
      }
      
      pagination.innerHTML = html;
      
      // Add event listeners to pagination buttons
      document.querySelectorAll('.pagination-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          currentPage = parseInt(this.getAttribute('data-page'));
          renderProducts();
          renderPagination();
          window.scrollTo({ top: document.getElementById('premium-tiles').offsetTop - 100, behavior: 'smooth' });
        });
      });
    }

    // Dynamically load all tile designs for the modal and sidebar (filter)
    document.addEventListener('DOMContentLoaded', function() {
      fetch('processes/get_best_for_categories.php')
        .then(r => r.json())
        .then(categories => {
          const catList = document.getElementById('bestForCategoriesList');
          if (catList) {
            catList.innerHTML = '';
            categories.forEach(cat => {
              const label = document.createElement('label');
              label.className = 'category-pill';
              label.innerHTML = `<input type="checkbox" name="best_for" value="${cat.best_for_id}" />${cat.best_for_name}`;
              label.onclick = function(e) {
                if (e.target.tagName !== 'INPUT') {
                  const cb = label.querySelector('input');
                  cb.checked = !cb.checked;
                  label.classList.toggle('selected', cb.checked);
                  filterProducts();
                } else {
                  label.classList.toggle('selected', e.target.checked);
                  filterProducts();
                }
              };
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
          allProducts = products;
          filteredProducts = [...products];
          
          renderProducts();
          renderPagination();
          initPriceRangeSlider();
          
          // Add event listeners to filter controls
          // Real-time filter: trigger on any change
          document.querySelectorAll('input[name="best_for"], input[name="popular"], input[name="bestseller"], input[name="other"], #price-min, #price-max, #sortProducts').forEach(el => {
            el.addEventListener('change', filterProducts);
          });
          document.getElementById('clearFilters').addEventListener('click', function() {
            // Reset all filters
            document.querySelectorAll('input[name="best_for"]').forEach(cb => cb.checked = false);
            document.querySelector('input[name="popular"]').checked = false;
            document.querySelector('input[name="bestseller"]').checked = false;
            document.getElementById('price-min').value = 100;
            document.getElementById('price-max').value = 5000;
            document.getElementById('price-min-value').textContent = '100';
            document.getElementById('price-max-value').textContent = '5000';
            document.getElementById('sortProducts').value = 'name_asc';
            // Reset products
            filteredProducts = [...allProducts];
            currentPage = 1;
            renderProducts();
            renderPagination();
          });
          
          document.getElementById('sortProducts').addEventListener('change', filterProducts);
        });
    });
    </script>

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

      // Carousel logic with improved design and mobile responsiveness
      const featuredItemsPerPage = () => {
        if (window.innerWidth <= 640) return 1;
        if (window.innerWidth <= 900) return 2;
        if (window.innerWidth <= 1200) return 3;
        return 4;
      };
      let currentFeaturedPage = 0;
      let isAnimating = false;
      let touchStartX = null;
      let touchEndX = null;

      function initCarousel() {
        const container = document.querySelector('.featured-items');
        if (!container) return;
        container.innerHTML = '';
        const perPage = featuredItemsPerPage();
        const itemWidth = calculateItemWidth();
        container.style.width = `${featuredItems.length * (itemWidth + 32)}px`;
        featuredItems.forEach((item, index) => {
          const div = document.createElement('div');
          div.className = 'featured-item';
          div.style.width = `${itemWidth}px`;
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
        renderFeaturedPagination();
        updateCarouselPosition();
      }

      function calculateItemWidth() {
        if (window.innerWidth <= 640) return 280;
        if (window.innerWidth <= 900) return 220;
        if (window.innerWidth <= 1200) return 200;
        return 220;
      }

      function updateCarouselPosition() {
        if (isAnimating) return;
        const container = document.querySelector('.featured-items');
        if (!container) return;
        const perPage = featuredItemsPerPage();
        const itemWidth = calculateItemWidth();
        const gap = 32;
        const translateX = -currentFeaturedPage * (itemWidth + gap) * perPage;
        container.style.transform = `translateX(${translateX}px)`;
        renderFeaturedPagination();
      }

      function renderFeaturedPagination() {
        const perPage = featuredItemsPerPage();
        const pageCount = Math.ceil(featuredItems.length / perPage);
        const pagination = document.querySelector('.featured-pagination');
        if (!pagination) return;
        pagination.innerHTML = '';
        for (let i = 0; i < pageCount; i++) {
          const dot = document.createElement('span');
          dot.className = `featured-dot${i === currentFeaturedPage ? ' active' : ''}`;
          dot.textContent = i + 1;
          dot.addEventListener('click', () => {
            if (i !== currentFeaturedPage && !isAnimating) {
              currentFeaturedPage = i;
              updateCarouselPosition();
            }
          });
          pagination.appendChild(dot);
        }
      }

      function nextFeatured() {
        if (isAnimating) return;
        const perPage = featuredItemsPerPage();
        const pageCount = Math.ceil(featuredItems.length / perPage);
        if (currentFeaturedPage < pageCount - 1) {
          currentFeaturedPage++;
          updateCarouselPosition();
        }
      }

      function prevFeatured() {
        if (isAnimating) return;
        if (currentFeaturedPage > 0) {
          currentFeaturedPage--;
          updateCarouselPosition();
        }
      }

      // Touch/swipe support for mobile
      function handleTouchStart(e) {
        touchStartX = e.touches[0].clientX;
      }
      function handleTouchEnd(e) {
        touchEndX = e.changedTouches[0].clientX;
        if (touchStartX !== null && touchEndX !== null) {
          const diff = touchEndX - touchStartX;
          if (Math.abs(diff) > 50) {
            if (diff < 0) nextFeatured();
            else prevFeatured();
          }
        }
        touchStartX = null;
        touchEndX = null;
      }

      document.addEventListener('DOMContentLoaded', function() {
        initCarousel();
        const nextBtn = document.querySelector('.carousel-btn.next');
        const prevBtn = document.querySelector('.carousel-btn.prev');
        if (nextBtn) nextBtn.addEventListener('click', nextFeatured);
        if (prevBtn) prevBtn.addEventListener('click', prevFeatured);
        // Touch events
        const container = document.querySelector('.featured-items-container');
        if (container) {
          container.addEventListener('touchstart', handleTouchStart, {passive:true});
          container.addEventListener('touchend', handleTouchEnd, {passive:true});
        }
        // Resize
        let resizeTimer;
        window.addEventListener('resize', function() {
          clearTimeout(resizeTimer);
          resizeTimer = setTimeout(function() {
            currentFeaturedPage = 0;
            initCarousel();
          }, 250);
        });
        // Category filter buttons
        const categoryButtons = document.querySelectorAll('.tile-selection .bg-white button');
        categoryButtons.forEach(button => {
          button.addEventListener('click', function() {
            const design = this.closest('.bg-white').querySelector('h3').textContent.trim().toLowerCase().replace(/ /g, '_');
            window.location.href = design + '_products.php';
          });
        });
        // View Product buttons
        document.body.addEventListener('click', function(e) {
          if (e.target.closest('.view-product-btn')) {
            e.preventDefault();
            const btn = e.target.closest('.view-product-btn');
            let productId = null;
            if (btn.hasAttribute('data-idx')) {
              const idx = parseInt(btn.getAttribute('data-idx'));
              const prod = window.recommendedProducts && window.recommendedProducts[idx] ? window.recommendedProducts[idx] : null;
              if (prod && prod.product_id) productId = prod.product_id;
            } else {
              const card = btn.closest('.bg-white');
              if (card) productId = card.getAttribute('data-product-id');
            }
            if (productId) {
              window.location.href = 'product_detail.php?id=' + encodeURIComponent(productId);
            }
            return;
          }
        });
      });
    </script>
  </body>
      <style>
        .animate-move { animation: moveAnim 1.2s infinite alternate; }
        @keyframes moveAnim { from { transform: translateY(0); } to { transform: translateY(-10px); } }
        .animate-fade { animation: fadeAnim 1.2s ease-in; }
        @keyframes fadeAnim { from { opacity: 0; } to { opacity: 1; } }
        .animate-slide { animation: slideAnim 1.1s cubic-bezier(0.4,0,0.2,1); }
        @keyframes slideAnim { from { transform: translateX(-40px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .animate-zoom { animation: zoomAnim 1.2s cubic-bezier(0.4,0,0.2,1); }
        @keyframes zoomAnim { from { transform: scale(0.8); opacity: 0.7; } to { transform: scale(1); opacity: 1; } }
        .animate-bounce { animation: bounceAnim 1.2s infinite alternate; }
        @keyframes bounceAnim { from { transform: translateY(0); } to { transform: translateY(-6px); } }
        .animate-tiles { animation: fadeAnim 1.2s ease-in; }
        .selected-animate { animation: selectAnim 0.7s cubic-bezier(0.4,0,0.2,1); }
        @keyframes selectAnim {
          0% { transform: scale(1) translateY(0); background: #7d310a; }
          30% { transform: scale(1.1) translateY(-10px); background: #cf8756; }
          60% { transform: scale(0.95) translateY(5px); background: #e8a56a; }
          100% { transform: scale(1) translateY(0); background: #7d310a; }
        }
      </style>