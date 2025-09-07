<?php
// black&white_products.php
// Displays all product tiles with Black & White category (design_name = 'black & white')

session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
	header('Location: ../connection/tresspass.php');
	exit();
}
require_once '../includes/headeruser.php';
require_once '../connection/connection.php';

$branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : null;
$products = [];
if ($branch_id) {
	$stmt = $conn->prepare('
		SELECT p.product_id, p.product_name, p.product_price, p.product_description, p.product_image, td.design_name,
			   p.is_popular, p.is_best_seller
		FROM products p
		JOIN product_designs pd ON p.product_id = pd.product_id
		JOIN tile_designs td ON pd.design_id = td.design_id
		JOIN product_branches pb ON p.product_id = pb.product_id
		WHERE td.design_name = ? AND pb.branch_id = ? AND p.is_archived = 0
		ORDER BY p.product_name ASC
	');
	$stmt->execute(['black & white', $branch_id]);
	$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($products as &$row) {
		// Fetch sizes
		$sizeStmt = $conn->prepare('SELECT ts.size_name FROM product_sizes ps JOIN tile_sizes ts ON ps.size_id = ts.size_id WHERE ps.product_id = ?');
		$sizeStmt->execute([$row['product_id']]);
		$row['sizes'] = $sizeStmt->fetchAll(PDO::FETCH_COLUMN);
		// Fetch finishes
		$finishStmt = $conn->prepare('SELECT tf.finish_name FROM product_finishes pf JOIN tile_finishes tf ON pf.finish_id = tf.finish_id WHERE pf.product_id = ?');
		$finishStmt->execute([$row['product_id']]);
		$row['finishes'] = $finishStmt->fetchAll(PDO::FETCH_COLUMN);
		// Fetch classifications
		$classStmt = $conn->prepare('SELECT tc.classification_name FROM product_classifications pc JOIN tile_classifications tc ON pc.classification_id = tc.classification_id WHERE pc.product_id = ?');
		$classStmt->execute([$row['product_id']]);
		$row['classifications'] = $classStmt->fetchAll(PDO::FETCH_COLUMN);
		// Fetch best for
		$bestForStmt = $conn->prepare('SELECT bfc.best_for_name FROM product_best_for pbf JOIN best_for_categories bfc ON pbf.best_for_id = bfc.best_for_id WHERE pbf.product_id = ?');
		$bestForStmt->execute([$row['product_id']]);
		$row['best_for'] = $bestForStmt->fetchAll(PDO::FETCH_COLUMN);
		// Image
		if (!empty($row['product_image'])) {
			$row['product_image'] = 'data:image/jpeg;base64,' . base64_encode($row['product_image']);
		} else {
			$row['product_image'] = null;
		}
	}
}

// Filtering logic
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$filteredProducts = $products;
if ($filter === 'popular') {
	$filteredProducts = array_filter($products, function($p) {
		return isset($p['is_popular']) && $p['is_popular'] == 1;
	});
} elseif ($filter === 'bestseller') {
	$filteredProducts = array_filter($products, function($p) {
		return isset($p['is_best_seller']) && $p['is_best_seller'] == 1;
	});
}
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 8;
$totalProducts = count($filteredProducts);
$totalPages = $totalProducts > 0 ? ceil($totalProducts / $perPage) : 1;
$start = ($page - 1) * $perPage;
$pagedProducts = array_slice(array_values($filteredProducts), $start, $perPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Black & White Tiles - RALTT</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
	<script src="https://cdn.tailwindcss.com"></script>
	<style>
		body {
			background: linear-gradient(135deg, #ffece2 0%, #f8f5f2 60%, #e8a56a 100%);
			color: #222;
			font-family: Inter, sans-serif;
		}
		/* Modal styles */
		#productModalOverlay {
			display: none;
			position: fixed;
			z-index: 99999;
			top: 0; left: 0;
			width: 100vw; height: 100vh;
			background: rgba(30,30,30,0.75);
			align-items: center; justify-content: center;
			backdrop-filter: blur(2px);
		}
		#productModalBox {
			background: linear-gradient(120deg,#fff 60%,#e8a56a 100%);
			border-radius: 22px;
			max-width: 99vw; width: 600px; height: 470px;
			box-shadow: 0 16px 56px 0 rgba(0,0,0,0.32);
			padding: 0; position: relative; overflow: hidden;
			animation: modalPopIn .25s cubic-bezier(.6,1.5,.6,1) 1;
			display: flex; flex-direction: column;
		}
		.add-to-cart-btn {
			width: 100%;
			padding-top: 0.75rem;
			padding-bottom: 0.75rem;
			background: linear-gradient(90deg, #7d310a 0%, #cf8756 100%);
			color: #fff;
			border-radius: 0.5rem;
			font-size: 1rem;
			font-weight: 700;
			transition: all 0.2s;
			box-shadow: 0 2px 8px #cf875633;
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 0.5rem;
		}
		.add-to-cart-btn:hover {
			background: linear-gradient(90deg, #cf8756 0%, #7d310a 100%);
			color: #fff;
			transform: translateY(-4px) scale(1.03);
		}
		.view-3d-btn {
			width: 100%;
			padding-top: 0.75rem;
			padding-bottom: 0.75rem;
			background: #2B3241;
			color: #EF7232;
			border-radius: 0.5rem;
			font-size: 1rem;
			font-weight: 700;
			transition: all 0.2s;
			box-shadow: 0 2px 8px #cf875633;
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 0.5rem;
		}
		.view-3d-btn:hover {
			background: #EF7232;
			color: #2B3241;
			transform: translateY(-4px) scale(1.03);
		}
		@keyframes modalPopIn{0%{transform:scale(0.85) translateY(40px);opacity:0;}100%{transform:scale(1) translateY(0);opacity:1;}}
		/* New styles for the redesign */
		.filter-btn {
			transition: all 0.3s ease;
			border: 2px solid transparent;
		}
		.filter-btn.active {
			background: linear-gradient(90deg, #7d310a 0%, #cf8756 100%);
			color: white;
			border-color: #7d310a;
			box-shadow: 0 4px 12px rgba(125, 49, 10, 0.3);
		}
		.filter-btn:not(.active):hover {
			background: white;
			color: #7d310a;
			border-color: #7d310a;
			transform: translateY(-2px);
		}
		.page-btn {
			transition: all 0.2s ease;
		}
		.page-btn.active {
			background: linear-gradient(90deg, #7d310a 0%, #cf8756 100%);
			color: white;
		}
		.back-btn {
			transition: all 0.3s ease;
			box-shadow: 0 2px 8px rgba(0,0,0,0.1);
		}
		.back-btn:hover {
			transform: translateY(-2px);
			box-shadow: 0 4px 12px rgba(0,0,0,0.15);
		}
	</style>
</head>
<body>
	<div style="padding-top:90px;"></div>

	<section class="min-h-screen py-0 px-0 bg-transparent">
		<div class="max-w-4xl mx-auto text-center pt-[50px] pb-6 px-4">
			<h1 class="text-4xl md:text-5xl font-black mb-3 tracking-tight text-[#7d310a]">Black & White Tiles</h1>
			<div class="text-base md:text-lg text-textlight mb-2 font-medium">Discover our classic, elegant black & white tile designs for a timeless look.</div>
			<div class="text-sm text-textlight mb-6">All tiles in the Black & White design available at your branch.</div>
			<!-- Filter Buttons Only -->
			<div class="flex flex-wrap gap-2 justify-center mb-8 w-full max-w-2xl mx-auto">
				<a href="?" class="filter-btn px-6 py-3 rounded-full font-bold text-base <?= $filter=='' ? 'active' : 'bg-white text-[#7d310a]' ?>">All</a>
				<a href="?filter=popular" class="filter-btn px-6 py-3 rounded-full font-bold text-base <?= $filter=='popular' ? 'active' : 'bg-white text-[#7d310a]' ?>">Popular</a>
				<a href="?filter=bestseller" class="filter-btn px-6 py-3 rounded-full font-bold text-base <?= $filter=='bestseller' ? 'active' : 'bg-white text-[#7d310a]' ?>">Best Seller</a>
			</div>
		</div>

		<div class="max-w-7xl mx-auto mt-2 md:mt-6">
			<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
				<?php if (empty($filteredProducts)): ?>
					<div class="col-span-full text-center text-textlight py-8">No Black & White tiles found for your branch.</div>
				<?php else: ?>
					<?php foreach ($pagedProducts as $idx => $prod): ?>
						<div class="bg-white rounded-2xl overflow-hidden shadow-product transition-all duration-300 relative group border border-gray-100 hover:shadow-lg hover:-translate-y-2 hover:scale-105 flex flex-col p-0">
							<!-- Category/Badge -->
							<?php
							$badge = '';
							if (isset($prod['is_best_seller']) && $prod['is_best_seller'] == 1) {
								$badge = '<span class="absolute top-4 left-4 bg-secondary text-white px-3 py-1 rounded text-xs font-bold uppercase z-10">Bestseller</span>';
							} elseif (isset($prod['is_popular']) && $prod['is_popular'] == 1) {
								$badge = '<span class="absolute top-4 left-4 bg-primary text-white px-3 py-1 rounded text-xs font-bold uppercase z-10">Popular</span>';
							} else {
								$badge = '<span class="absolute top-4 left-4 bg-gradient-to-r from-[#7d310a] via-[#cf8756] to-[#e8a56a] text-white px-4 py-1.5 rounded-full text-sm font-black uppercase z-10 shadow-lg tracking-wider border-2 border-white" style="letter-spacing:0.08em;box-shadow:0 4px 18px #cf875655,0 2px 8px #e8a56a33;">Black & White</span>';
							}
							echo $badge;
							?>
							<div class="h-[200px] md:h-[250px] overflow-hidden relative flex items-center justify-center bg-gradient-to-br from-accent/30 to-light rounded-xl shadow-inner">
								<img src="<?= $prod['product_image'] ?? '../images/user/tile1.jpg' ?>" alt="<?= htmlspecialchars($prod['product_name']) ?>" class="w-full h-full object-cover bg-gray-100 transition-transform duration-300 group-hover:scale-105" />
							</div>
							<div class="p-4 md:p-5 text-center flex flex-col items-center flex-1">
								<h3 class="text-base md:text-[1.1rem] font-bold text-gray-800 mb-1 text-center tracking-wide leading-tight"><?= htmlspecialchars($prod['product_name']) ?></h3>
								<div class="text-xs md:text-sm font-medium text-textlight mb-2 line-clamp-3 min-h-[36px]"><?= htmlspecialchars($prod['product_description']) ?></div>
								<div class="text-lg md:text-[1.25rem] font-extrabold text-secondary mb-4">₱<?= number_format($prod['product_price'], 2) ?></div>
								<div class="flex flex-col justify-center gap-2 w-full mt-auto">
									<button class="add-to-cart-btn w-full py-3 bg-primary text-white rounded-lg text-base font-bold transition-all hover:bg-secondary hover:-translate-y-1 shadow flex items-center justify-center gap-2" data-idx="<?= $idx ?>"><i class="fa fa-shopping-cart text-base"></i> Add to Cart</button>
									<button class="view-3d-btn w-full py-3 bg-[#2B3241] text-[#EF7232] rounded-lg text-base font-bold transition-all hover:bg-[#EF7232] hover:text-[#2B3241] hover:-translate-y-1 shadow flex items-center justify-center gap-2" data-idx="<?= $idx ?>"><i class="fa fa-cube text-base"></i> 3D View</button>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<!-- Pagination -->
			<?php if ($totalPages > 1): ?>
			<div class="flex justify-center items-center gap-2 mt-10">
				<?php for ($i = 1; $i <= $totalPages; $i++): ?>
					<a href="?<?php if($filter)echo 'filter='.urlencode($filter).'&'; ?>page=<?= $i ?>" class="page-btn px-4 py-2 rounded-full font-bold text-base <?= $i == $page ? 'active text-white' : 'bg-white text-[#7d310a] border border-[#7d310a] hover:bg-[#f8f5f2]' ?>">
						<?= $i ?>
					</a>
				<?php endfor; ?>
			</div>
			<?php endif; ?>
		</div>
	</section>

		<!-- Product Modal for 3D View (updated to match landing_page.php) -->
		<style>
			@media (max-width: 900px) {
				#productModalBox { width: 98vw !important; min-width: 0 !important; }
				#productModal3D { width: 160px !important; height: 160px !important; }
			}
			@media (max-width: 700px) {
				#productModalBox { flex-direction: column !important; height: 98vh !important; min-width: 0 !important; }
				#productModalBox > div[style*='display:flex;flex-direction:row'] { flex-direction: column !important; }
				#productModal3D { width: 120px !important; height: 120px !important; margin: 0 auto !important; }
				#productModalCategories, #productModalDesc { padding-left: 0 !important; padding-right: 0 !important; }
			}
		</style>
		<div id="productModalOverlay" style="display:none;position:fixed;z-index:99999;top:0;left:0;width:100vw;height:100vh;background:rgba(30,30,30,0.75);align-items:center;justify-content:center;backdrop-filter:blur(2px);">
			<div id="productModalBox" style="background:linear-gradient(120deg,#fff 60%,#e8a56a 100%);border-radius:28px;max-width:99vw;width:900px;min-width:340px;height:600px;box-shadow:0 16px 56px 0 rgba(0,0,0,0.32);padding:0;position:relative;overflow:hidden;animation:modalPopIn .25s cubic-bezier(.6,1.5,.6,1) 1;display:flex;flex-direction:column;">
				<div id="productModalNameHeader" style="background:#7d310a;padding:16px 0 10px 0;text-align:center;border-radius:28px 28px 0 0;box-shadow:0 2px 16px #e8a56a33;min-height:28px;">
					<span id="productModalNameHeaderText" style="color:#fff;font-size:1.35rem;font-weight:800;letter-spacing:0.01em;display:block;text-shadow:0 2px 12px #0003;text-transform:capitalize;"></span>
				</div>
				<div style="display:flex;flex-direction:row;gap:0;padding:0 0 0 0;align-items:stretch;background:linear-gradient(120deg,#fff 70%,#e8a56a22 100%);flex:1;min-height:0;">
					<div style="flex:0 0 320px;display:flex;align-items:center;justify-content:center;padding:38px 0 28px 38px;">
						<div id="productModal3D" style="width:240px;height:240px;display:flex;align-items:center;justify-content:center;background:radial-gradient(circle at 60% 40%, #fff 60%, #f7f3ef 100%);border-radius:28px;box-shadow:0 8px 32px 0 #cf875655,0 1.5px 0 #fff;"></div>
					</div>
					<div style="flex:1;display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start;min-width:0;padding:38px 38px 28px 28px;">
						<div id="productModalCategories" style="width:100%;margin-bottom:18px;"></div>
						<div id="productModalDesc" style="font-size:clamp(1.08rem,2vw,1.22rem);color:#222;font-weight:600;line-height:1.7;text-align:left;letter-spacing:0.01em;max-width:100%;text-shadow:0 1px 0 #fff,0 2px 8px #e8a56a11;background:rgba(255,255,255,0.8);padding:18px 18px 18px 18px;border-radius:18px;box-shadow:0 2px 12px #e8a56a22;overflow:auto;max-height:140px;word-break:break-word;min-width:180px;margin-bottom:10px;"></div>
						<div style="flex:1;"></div>
					</div>
				</div>
				<div style="display:flex;gap:24px;justify-content:center;align-items:center;padding:22px 0 24px 0;background:linear-gradient(90deg,#fff 80%,#e8a56a22 100%);border-radius:0 0 28px 28px;">
					<button id="closeProductModal" style="min-width:120px;padding:12px 0;font-size:1.13rem;font-weight:700;background:#cf8756;color:#fff;border:none;border-radius:14px;box-shadow:0 2px 8px #cf875633;cursor:pointer;transition:background .2s;">Close</button>
					<button id="addToCartProductModal" style="min-width:160px;padding:12px 0;font-size:1.13rem;font-weight:700;background:#7d310a;color:#fff;border:none;border-radius:14px;box-shadow:0 2px 8px #cf875633;cursor:pointer;transition:background .2s;"><i class="fa fa-shopping-cart" style="margin-right:9px;"></i>Add to Cart</button>
				</div>
			</div>
		</div>
		<script src="https://cdn.jsdelivr.net/npm/three@0.153.0/build/three.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>
		<script>
		// Products array for modal
		const products = <?php echo json_encode($pagedProducts); ?>;
		let currentProductForModal = null;
		// Helper: open/close modal (matches landing_page.php)
		function openProductModal(product) {
			currentProductForModal = product;
			// Set tile name in the header
			const nameHeader = document.getElementById('productModalNameHeaderText');
			if (nameHeader) {
				nameHeader.textContent = product.product_name || '';
			}
			// Set all tile attributes in a modern card layout
			const catDiv = document.getElementById('productModalCategories');
			let html = '';
			// Helper to render a row of badges
			function renderBadges(label, arr, color) {
				if (!arr || arr.length === 0) return '';
				// Capitalize each word in label
				const capLabel = label.replace(/\w\S*/g, w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase());
				return `<div style="margin-bottom:8px;"><span style="font-weight:700;color:#7d310a;font-size:1.01em;">${capLabel}:</span> ` +
					arr.map(val => `<span style="display:inline-block;background:${color};color:#fff;font-size:0.98rem;font-weight:600;padding:4px 14px;border-radius:12px;box-shadow:0 1px 4px #cf875622;margin-right:6px;margin-bottom:3px;letter-spacing:0.01em;">${val}</span>`).join('') + '</div>';
			}
			// Parse attributes if present (simulate landing_page.php structure)
			html += renderBadges('Tile Design', product.designs || (product.design_name ? [product.design_name] : []), '#cf8756');
			html += renderBadges('Tile Size', product.sizes, '#7d310a');
			html += renderBadges('Tile Finishes', product.finishes, '#e8a56a');
			html += renderBadges('Tile Classification', product.classifications, '#2B3241');
			html += renderBadges('Best For', product.best_for, '#4CAF50');
			catDiv.innerHTML = html;
			// Set description
			let desc = product.product_description || '';
			desc = desc ? `<span style='display:block;font-size:0.98em;color:#7d310a;font-weight:600;margin-bottom:0.2em;'>Product Description</span><span style='display:block;margin-bottom:0.5em;'>${desc}</span>` : '';
			document.getElementById('productModalDesc').innerHTML = desc;
			document.getElementById('productModalOverlay').style.display = 'flex';
			document.body.style.overflow = 'hidden';
			renderProduct3D(product.product_image);
		}
		function closeProductModal() {
			document.getElementById('productModalOverlay').style.display = 'none';
			const viewer = document.getElementById('productModal3D');
			viewer.innerHTML = '';
			currentProductForModal = null;
			document.body.style.overflow = '';
		}
		document.getElementById('closeProductModal').onclick = closeProductModal;
		document.getElementById('addToCartProductModal').onclick = function() {
			if (!currentProductForModal) return;
			Swal.fire({
				title: 'Added to Cart!',
				html: `<strong>${currentProductForModal.product_name || ''}</strong>`,
				icon: 'success',
				confirmButtonColor: '#7d310a',
				confirmButtonText: 'Continue Shopping'
			});
			closeProductModal();
		};
		// 3D viewer using Three.js (matches landing_page.php)
		function renderProduct3D(imageUrl) {
			const container = document.getElementById('productModal3D');
			container.innerHTML = '';
			const width = container.clientWidth, height = container.clientHeight;
			const renderer = new THREE.WebGLRenderer({antialias:true,alpha:true});
			renderer.setClearColor(0xf7f3ef, 1);
			renderer.setSize(width, height);
			renderer.shadowMap.enabled = true;
			container.appendChild(renderer.domElement);
			const scene = new THREE.Scene();
			// Add a soft shadow plane for realism
			const shadowGeo = new THREE.PlaneGeometry(2.8, 0.9);
			const shadowMat = new THREE.ShadowMaterial({opacity:0.28});
			const shadow = new THREE.Mesh(shadowGeo, shadowMat);
			shadow.position.y = -0.45;
			shadow.receiveShadow = true;
			scene.add(shadow);
			// Camera
			const camera = new THREE.PerspectiveCamera(30, width/height, 0.1, 1000);
			camera.position.set(0, 0.12, 2.7);
			// Lighting
			const ambient = new THREE.AmbientLight(0xffffff, 1.08);
			scene.add(ambient);
			const dirLight = new THREE.DirectionalLight(0xffffff, 0.85);
			dirLight.position.set(2, 3, 4);
			dirLight.castShadow = true;
			dirLight.shadow.mapSize.width = 2048;
			dirLight.shadow.mapSize.height = 2048;
			dirLight.shadow.blurSamples = 16;
			scene.add(dirLight);
			// Cube geometry for square tile (smaller)
			const geometry = new THREE.BoxGeometry(1.05, 1.05, 0.07); // Smaller tile
			const loader = new THREE.TextureLoader();
			loader.load(imageUrl, function(texture) {
				texture.anisotropy = renderer.capabilities.getMaxAnisotropy();
				texture.minFilter = THREE.LinearFilter;
				texture.magFilter = THREE.LinearFilter;
				texture.wrapS = THREE.ClampToEdgeWrapping;
				texture.wrapT = THREE.ClampToEdgeWrapping;
				// Only show image on front face, rest are subtle off-white
				const materials = [
					new THREE.MeshStandardMaterial({color:0xf9f5f2, roughness:0.32, metalness:0.13}), // right
					new THREE.MeshStandardMaterial({color:0xf9f5f2, roughness:0.32, metalness:0.13}), // left
					new THREE.MeshStandardMaterial({color:0xf9f5f2, roughness:0.32, metalness:0.13}), // top
					new THREE.MeshStandardMaterial({color:0xf9f5f2, roughness:0.32, metalness:0.13}), // bottom
					new THREE.MeshStandardMaterial({map:texture, roughness:0.16, metalness:0.18}),    // front (main image)
					new THREE.MeshStandardMaterial({color:0xf9f5f2, roughness:0.32, metalness:0.13})  // back
				];
				const cube = new THREE.Mesh(geometry, materials);
				cube.castShadow = true;
				cube.receiveShadow = false;
				// Center the tile in the 3D view
				cube.position.y = 0.18;
				scene.add(cube);
				// Animation
				let isDragging = false, prevX = 0, prevY = 0, rotationY = Math.PI/8, rotationX = -Math.PI/16;
				renderer.domElement.addEventListener('mousedown', function(e) {
					isDragging = true; prevX = e.clientX; prevY = e.clientY;
				});
				window.addEventListener('mouseup', function() { isDragging = false; });
				window.addEventListener('mousemove', function(e) {
					if (isDragging) {
						const dx = e.clientX - prevX;
						const dy = e.clientY - prevY;
						rotationY += dx * 0.012;
						rotationX += dy * 0.012;
						rotationX = Math.max(-Math.PI/2.5, Math.min(Math.PI/2.5, rotationX));
						prevX = e.clientX;
						prevY = e.clientY;
					}
				});
				// Touch support
				renderer.domElement.addEventListener('touchstart', function(e) {
					if (e.touches.length === 1) {
						isDragging = true; prevX = e.touches[0].clientX; prevY = e.touches[0].clientY;
					}
				});
				window.addEventListener('touchend', function() { isDragging = false; });
				window.addEventListener('touchmove', function(e) {
					if (isDragging && e.touches.length === 1) {
						const dx = e.touches[0].clientX - prevX;
						const dy = e.touches[0].clientY - prevY;
						rotationY += dx * 0.012;
						rotationX += dy * 0.012;
						rotationX = Math.max(-Math.PI/2.5, Math.min(Math.PI/2.5, rotationX));
						prevX = e.touches[0].clientX;
						prevY = e.touches[0].clientY;
					}
				});
				function animate() {
					requestAnimationFrame(animate);
					cube.rotation.y = rotationY;
					cube.rotation.x = rotationX;
					renderer.render(scene, camera);
				}
				animate();
			}, undefined, function() {
				// On error, fallback to plain image
				container.innerHTML = '<img src="'+imageUrl+'" alt="Tile" style="width:100%;height:100%;object-fit:contain;border-radius:16px;background:#f7f3ef;">';
			});
		}
		// Modal pop-in animation
		const style = document.createElement('style');
		style.innerHTML = `@keyframes modalPopIn{0%{transform:scale(0.85) translateY(40px);opacity:0;}100%{transform:scale(1) translateY(0);opacity:1;}}`;
		document.head.appendChild(style);
		// Button event listeners
		document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
			btn.addEventListener('click', function(e) {
				e.preventDefault();
				const idx = parseInt(this.getAttribute('data-idx'));
				const prod = products[idx];
				if (prod) {
					Swal.fire({
						title: 'Added to Cart!',
						html: `<strong>${prod.product_name}</strong><br>₱${parseInt(prod.product_price).toLocaleString()}`,
						icon: 'success',
						confirmButtonColor: '#7d310a',
						confirmButtonText: 'Continue Shopping'
					});
				}
			});
		});
		document.querySelectorAll('.view-3d-btn').forEach(btn => {
			btn.addEventListener('click', function(e) {
				e.preventDefault();
				const idx = parseInt(this.getAttribute('data-idx'));
				const prod = products[idx];
				if (prod) openProductModal(prod);
			});
		});
		</script>
</body>
</html>
