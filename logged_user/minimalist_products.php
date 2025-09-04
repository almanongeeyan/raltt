<?php
// minimalist_products.php
// Displays all product tiles with Minimalist category (category_id = 1)

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
        SELECT p.product_id, p.product_name, p.product_price, p.product_description, p.product_image, tc.category_name
        FROM products p
        JOIN product_categories pc ON p.product_id = pc.product_id
        JOIN tile_categories tc ON pc.category_id = tc.category_id
        JOIN product_branches pb ON p.product_id = pb.product_id
        WHERE pc.category_id = 1 AND pb.branch_id = ? AND p.is_archived = 0
        ORDER BY p.product_name ASC
    ');
    $stmt->execute([$branch_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($products as &$row) {
        if (!empty($row['product_image'])) {
            $row['product_image'] = 'data:image/jpeg;base64,' . base64_encode($row['product_image']);
        } else {
            $row['product_image'] = null;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minimalist Tiles - RALTT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #f9f5f2 0%, #e8a56a 100%);
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
        @keyframes modalPopIn{0%{transform:scale(0.85) translateY(40px);opacity:0;}100%{transform:scale(1) translateY(0);opacity:1;}}
    </style>
</head>
<body>
    <div style="padding-top:90px;"></div>
    <section class="min-h-screen py-12 px-4 bg-[#f9f5f2]">
        <div class="max-w-3xl mx-auto text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-black text-primary mb-2">Minimalist Tiles</h1>
            <p class="text-base text-textlight">All tiles in the Minimalist category available at your branch.</p>
        </div>
        <?php
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = 9;
        $totalProducts = count($products);
        $totalPages = $totalProducts > 0 ? ceil($totalProducts / $perPage) : 1;
        $start = ($page - 1) * $perPage;
        $pagedProducts = array_slice($products, $start, $perPage);
        ?>
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
                <?php if (empty($products)): ?>
                    <div class="col-span-full text-center text-textlight py-8">No Minimalist tiles found for your branch.</div>
                <?php else: ?>
                    <?php foreach ($pagedProducts as $idx => $prod): ?>
                        <div class="bg-white rounded-3xl overflow-hidden shadow-2xl transition-all duration-300 relative group border border-gray-100 hover:shadow-3xl hover:-translate-y-2 hover:scale-105">
                            <!-- Category badge -->
                            <span class="absolute top-5 left-5 bg-gradient-to-r from-primary to-secondary text-white px-4 py-1.5 rounded-full text-xs font-bold uppercase z-10 shadow-md">Minimalist</span>
                            <div class="h-[220px] md:h-[260px] overflow-hidden relative flex items-center justify-center bg-gradient-to-br from-accent/30 to-light rounded-xl mb-2 md:mb-4 shadow-inner">
                                <img src="<?= $prod['product_image'] ?? '../images/user/tile1.jpg' ?>" alt="<?= htmlspecialchars($prod['product_name']) ?>" class="w-[90%] h-[90%] object-contain rounded-xl transition-transform duration-300 group-hover:scale-105 bg-gray-100 drop-shadow-lg" />
                            </div>
                            <div class="px-5 pb-6 pt-2 text-center flex flex-col items-center">
                                <h3 class="text-lg md:text-xl font-extrabold text-primary mb-1 text-center tracking-wide leading-tight"><?= htmlspecialchars($prod['product_name']) ?></h3>
                                <div class="text-base md:text-lg font-bold text-textlight mb-2 line-clamp-3 min-h-[40px]"><?= htmlspecialchars($prod['product_description']) ?></div>
                                <div class="text-xl md:text-2xl font-black text-secondary mb-4">₱<?= number_format($prod['product_price'], 2) ?></div>
                                <div class="flex flex-col md:flex-row justify-center gap-3 w-full mt-2">
                                    <button class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-gradient-to-r from-primary to-secondary text-white font-extrabold text-base md:text-lg shadow-lg hover:from-secondary hover:to-primary hover:scale-105 transition-all add-to-cart-btn" data-idx="<?= $idx ?>" style="box-shadow:0 4px 16px #cf875633;"><i class="fa fa-shopping-cart text-lg"></i> Add to Cart</button>
                                    <button class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-gradient-to-r from-secondary to-primary text-white font-extrabold text-base md:text-lg shadow-lg hover:from-primary hover:to-secondary hover:scale-105 transition-all view-3d-btn" data-idx="<?= $idx ?>" style="box-shadow:0 4px 16px #cf875633;"><i class="fa fa-cube text-lg"></i> 3D View</button>
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
                    <a href="?page=<?= $i ?>" class="px-4 py-2 rounded-full font-bold text-base <?= $i == $page ? 'bg-primary text-white' : 'bg-white text-primary border border-primary hover:bg-secondary hover:text-white transition' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
        </section>

        <!-- Product Modal for 3D View -->
        <div id="productModalOverlay">
            <div id="productModalBox">
                <div id="productModalNameHeader" style="background:#7d310a;padding:22px 0 18px 0;text-align:center;border-radius:22px 22px 0 0;box-shadow:0 2px 16px #e8a56a33;min-height:38px;">
                    <span id="productModalNameHeaderText" style="color:#fff;font-size:1.55rem;font-weight:900;letter-spacing:0.01em;display:block;text-shadow:0 2px 12px #0003;"></span>
                </div>
                <div style="display:flex;flex-direction:row;gap:0;padding:0 0 0 0;align-items:stretch;background:linear-gradient(120deg,#fff 70%,#e8a56a22 100%);flex:1;min-height:0;">
                    <div style="flex:0 0 220px;display:flex;align-items:center;justify-content:center;padding:28px 0 18px 28px;">
                        <div id="productModal3D" style="width:180px;height:180px;display:flex;align-items:center;justify-content:center;background:radial-gradient(circle at 60% 40%, #fff 60%, #f7f3ef 100%);border-radius:22px;box-shadow:0 8px 32px 0 #cf875655,0 1.5px 0 #fff;"></div>
                    </div>
                    <div style="flex:1;display:flex;flex-direction:column;align-items:flex-start;justify-content:center;min-width:0;padding:28px 28px 18px 18px;">
                        <div id="productModalCategories" style="display:flex;flex-wrap:wrap;gap:8px 10px;margin-bottom:10px;"></div>
                        <div id="productModalDesc" style="font-size:clamp(1.01rem,2vw,1.15rem);color:#222;font-weight:600;line-height:1.6;text-align:left;letter-spacing:0.01em;max-width:100%;text-shadow:0 1px 0 #fff,0 2px 8px #e8a56a11;background:rgba(255,255,255,0.7);padding:16px 14px 16px 14px;border-radius:16px;box-shadow:0 2px 12px #e8a56a22;overflow:auto;max-height:120px;word-break:break-word;min-width:180px;"></div>
                    </div>
                </div>
                <div style="display:flex;gap:16px;justify-content:center;align-items:center;padding:16px 0 18px 0;background:linear-gradient(90deg,#fff 80%,#e8a56a22 100%);border-radius:0 0 22px 22px;">
                    <button id="closeProductModal" style="min-width:110px;padding:10px 0;font-size:1.08rem;font-weight:700;background:#cf8756;color:#fff;border:none;border-radius:12px;box-shadow:0 2px 8px #cf875633;cursor:pointer;transition:background .2s;">Close</button>
                    <button id="addToCartProductModal" style="min-width:140px;padding:10px 0;font-size:1.08rem;font-weight:700;background:#7d310a;color:#fff;border:none;border-radius:12px;box-shadow:0 2px 8px #cf875633;cursor:pointer;transition:background .2s;"><i class="fa fa-shopping-cart" style="margin-right:7px;"></i>Add to Cart</button>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/three@0.153.0/build/three.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>
        <script>
        // Products array for modal
        const products = <?php echo json_encode($pagedProducts); ?>;
        let currentProductForModal = null;
        function openProductModal(product) {
            currentProductForModal = product;
            document.getElementById('productModalNameHeaderText').textContent = product.product_name || '';
            // Category badge
            const catDiv = document.getElementById('productModalCategories');
            catDiv.innerHTML = '';
            if (product.category_name) {
                const el = document.createElement('span');
                el.textContent = product.category_name;
                el.style.cssText = 'display:inline-block;background:#e8a56a;color:#fff;font-size:0.98rem;font-weight:600;padding:4px 16px;border-radius:12px;box-shadow:0 1px 4px #cf875622;letter-spacing:0.01em;';
                catDiv.appendChild(el);
            }
            // Description
            document.getElementById('productModalDesc').innerHTML = product.product_description ? `<span style='display:block;margin-bottom:0.5em;'>${product.product_description}</span>` : '';
            document.getElementById('productModalOverlay').style.display = 'flex';
            document.body.style.overflow = 'hidden';
            renderProduct3D(product.product_image);
        }
        function closeProductModal() {
            document.getElementById('productModalOverlay').style.display = 'none';
            document.getElementById('productModal3D').innerHTML = '';
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
        // 3D viewer using Three.js
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
            const shadowGeo = new THREE.PlaneGeometry(2.8, 0.9);
            const shadowMat = new THREE.ShadowMaterial({opacity:0.28});
            const shadow = new THREE.Mesh(shadowGeo, shadowMat);
            shadow.position.y = -0.45;
            shadow.receiveShadow = true;
            scene.add(shadow);
            const camera = new THREE.PerspectiveCamera(30, width/height, 0.1, 1000);
            camera.position.set(0, 0.12, 2.7);
            const ambient = new THREE.AmbientLight(0xffffff, 1.08);
            scene.add(ambient);
            const dirLight = new THREE.DirectionalLight(0xffffff, 0.85);
            dirLight.position.set(2, 3, 4);
            dirLight.castShadow = true;
            dirLight.shadow.mapSize.width = 2048;
            dirLight.shadow.mapSize.height = 2048;
            dirLight.shadow.blurSamples = 16;
            scene.add(dirLight);
            const geometry = new THREE.BoxGeometry(1.05, 1.05, 0.07);
            const loader = new THREE.TextureLoader();
            loader.load(imageUrl, function(texture) {
                texture.anisotropy = renderer.capabilities.getMaxAnisotropy();
                texture.minFilter = THREE.LinearFilter;
                texture.magFilter = THREE.LinearFilter;
                texture.wrapS = THREE.ClampToEdgeWrapping;
                texture.wrapT = THREE.ClampToEdgeWrapping;
                const materials = [
                    new THREE.MeshStandardMaterial({color:0xf9f5f2, roughness:0.32, metalness:0.13}),
                    new THREE.MeshStandardMaterial({color:0xf9f5f2, roughness:0.32, metalness:0.13}),
                    new THREE.MeshStandardMaterial({color:0xf9f5f2, roughness:0.32, metalness:0.13}),
                    new THREE.MeshStandardMaterial({color:0xf9f5f2, roughness:0.32, metalness:0.13}),
                    new THREE.MeshStandardMaterial({map:texture, roughness:0.16, metalness:0.18}),
                    new THREE.MeshStandardMaterial({color:0xf9f5f2, roughness:0.32, metalness:0.13})
                ];
                const cube = new THREE.Mesh(geometry, materials);
                cube.castShadow = true;
                cube.receiveShadow = false;
                cube.position.y = 0.18;
                scene.add(cube);
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
                container.innerHTML = '<img src="'+imageUrl+'" alt="Tile" style="width:100%;height:100%;object-fit:contain;border-radius:16px;background:#f7f3ef;">';
            });
        }
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
