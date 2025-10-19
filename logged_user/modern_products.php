<?php
// modern_products.php
// Displays all product tiles with Modern category (design_name = 'modern')

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
	$stmt->execute(['modern', $branch_id]);
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
	<title>Modern Tiles - RALTT</title>
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
			   color: #7d310a !important;
			   background: #fff;
		   }
		   .filter-btn.active {
			   background: linear-gradient(90deg, #7d310a 0%, #cf8756 100%);
			   color: #fff !important;
			   border-color: #7d310a;
			   box-shadow: 0 4px 12px rgba(125, 49, 10, 0.3);
		   }
		   .filter-btn:not(.active):hover {
			   background: #fff;
			   color: #7d310a !important;
			   border-color: #7d310a;
			   transform: translateY(-2px);
		   }
		   .page-btn {
			   transition: all 0.2s ease;
			   color: #7d310a !important;
			   background: #fff;
		   }
		   .page-btn.active {
			   background: linear-gradient(90deg, #7d310a 0%, #cf8756 100%);
			   color: #fff !important;
		   }
		   .back-btn {
			   transition: all 0.3s ease;
			   box-shadow: 0 2px 8px rgba(0,0,0,0.1);
		   }
		   .back-btn:hover {
			   transform: translateY(-2px);
			   box-shadow: 0 4px 12px rgba(0,0,0,0.15);
		   }
		   /* Override low-contrast text classes */
		   .text-textlight, .text-secondary {
			   color: #7d310a !important;
		   }
	</style>
</head>
<body>
	<div style="padding-top:90px;"></div>

	<section class="min-h-screen py-0 px-0 bg-transparent">
		<div class="max-w-4xl mx-auto text-center pt-[50px] pb-6 px-4">
			<h1 class="text-4xl md:text-5xl font-black mb-3 tracking-tight text-[#7d310a]">Modern Tiles</h1>
			   <div class="text-base md:text-lg text-textlight mb-2 font-medium" style="color:#7d310a;">Browse our stylish, contemporary modern tile designs for a fresh look.</div>
			   <div class="text-sm text-textlight mb-6" style="color:#7d310a;">All tiles in the Modern design available at your branch.</div>
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
					   <div class="col-span-full text-center text-textlight py-8" style="color:#7d310a;">No Modern tiles found for your branch.</div>
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
								$badge = '<span class="absolute top-4 left-4 bg-gradient-to-r from-[#7d310a] via-[#cf8756] to-[#e8a56a] text-white px-4 py-1.5 rounded-full text-sm font-black uppercase z-10 shadow-lg tracking-wider border-2 border-white" style="letter-spacing:0.08em;box-shadow:0 4px 18px #cf875655,0 2px 8px #e8a56a33;">Modern</span>';
							}
							echo $badge;
							?>
							<div class="h-[200px] md:h-[250px] overflow-hidden relative flex items-center justify-center bg-gradient-to-br from-accent/30 to-light rounded-xl shadow-inner">
								<img src="<?= $prod['product_image'] ?? '../images/user/tile1.jpg' ?>" alt="<?= htmlspecialchars($prod['product_name']) ?>" class="w-full h-full object-cover bg-gray-100 transition-transform duration-300 group-hover:scale-105" />
							</div>
							<div class="p-4 md:p-5 text-center flex flex-col items-center flex-1">
								   <h3 class="text-base md:text-[1.1rem] font-bold mb-1 text-center tracking-wide leading-tight" style="color:#7d310a;"><?= htmlspecialchars($prod['product_name']) ?></h3>
								   <div class="text-xs md:text-sm font-medium text-textlight mb-2 line-clamp-3 min-h-[36px]" style="color:#7d310a;"><?= htmlspecialchars($prod['product_description']) ?></div>
								   <div class="text-lg md:text-[1.25rem] font-extrabold text-secondary mb-4" style="color:#7d310a;">₱<?= number_format($prod['product_price'], 2) ?></div>
								<div class="flex flex-col justify-center gap-2 w-full mt-2">
									<button onclick="window.location.href='product_detail.php?id=<?= $prod['product_id'] ?>'" class="view-product-btn w-full py-3" style="background-color:#7d310a;color:#fff;border-radius:0.75rem;font-size:1rem;font-weight:700;box-shadow:0 2px 8px #cf875633;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:all 0.2s;">
										<i class="fa fa-eye text-base" style="margin-right:0.5rem;"></i> View Product
									</button>
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

		   <!-- Modal and 3D View removed: Only View Product button remains in product card -->
</body>
</html>
