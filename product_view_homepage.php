<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rich Anne Lea Tiles - Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>
    <style>
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .pattern-tile {
            transition: all 0.3s ease;
        }
        
        .pattern-tile:hover {
            transform: scale(1.05);
        }
        
        .tile-selection-item img {
            transition: all 0.3s ease;
        }
        
        .tile-selection-item:hover img {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">
    <?php include 'includes/header.php'; ?>
    
    <!-- Hero Section -->
    <section class="py-16 bg-gradient-to-br from-gray-100 to-gray-200">
        <div class="container mx-auto px-4 text-center fade-in">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Shopping made <span class="text-amber-600">easy!</span></h1>
            <p class="text-lg md:text-xl max-w-3xl mx-auto text-gray-700 leading-relaxed">
                Discover an easy shopping experience with Rich Anne Lea Tiles Trading! Explore a vast selection of textures,
                colors, and styles that bring your design vision to life. With our high-quality tiles, you can effortlessly
                blend creativity and functionality, transforming any space into a masterpiece.
            </p>
        </div>
    </section>
    
    <!-- Tile Types Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex justify-center mb-12">
                <div class="inline-flex rounded-lg border border-gray-300 p-1 bg-gray-100">
                    <button id="glossyBtn" class="px-6 py-3 rounded-md font-medium transition-all duration-300 bg-amber-700 text-white shadow-sm">Glossy</button>
                    <button id="matteBtn" class="px-6 py-3 rounded-md font-medium transition-all duration-300 text-gray-700 hover:bg-gray-200">Matte</button>
                </div>
            </div>
            
            <div id="glossyContent" class="fade-in visible">
                <div class="flex flex-col lg:flex-row items-center gap-10 max-w-6xl mx-auto">
                    <div class="lg:w-1/2">
                        <img src="images/glossy.PNG" alt="Glossy Tile" class="w-full h-80 object-cover rounded-xl shadow-lg">
                    </div>
                    <div class="lg:w-1/2">
                        <h2 class="text-3xl font-semibold mb-4">Glossy Tiles</h2>
                        <div class="h-1 w-16 bg-amber-600 mb-6"></div>
                        <p class="text-gray-700 text-lg leading-relaxed">
                            Rich Anne Tiles Trading offers a premium selection of glossy tiles that combine elegance and durability. 
                            Perfect for modern homes and businesses, these tiles feature a smooth, reflective finish that enhances 
                            any space with style and sophistication. Upgrade your floors and walls with Rich Anne Tiles Trading today!
                        </p>
                    </div>
                </div>
            </div>
            
            <div id="matteContent" class="fade-in hidden">
                <div class="flex flex-col lg:flex-row items-center gap-10 max-w-6xl mx-auto">
                    <div class="lg:w-1/2">
                        <img src="images/matte.PNG" alt="Matte Tile" class="w-full h-80 object-cover rounded-xl shadow-lg">
                    </div>
                    <div class="lg:w-1/2">
                        <h2 class="text-3xl font-semibold mb-4">Matte Tiles</h2>
                        <div class="h-1 w-16 bg-amber-600 mb-6"></div>
                        <p class="text-gray-700 text-lg leading-relaxed">
                            Rich Anne Tiles Trading presents a high-quality selection of matte tiles, perfect for creating a stylish 
                            and modern space. With their soft, non-reflective finish, these tiles offer a sleek look while providing 
                            excellent slip resistance. Elevate your space with Rich Anne Tiles Trading today!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Tile Selection Section -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 fade-in">Our visualizer supports a wide range of tile selection</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-8 max-w-6xl mx-auto">
                <div class="tile-selection-item text-center fade-in">
                    <div class="mb-4 overflow-hidden rounded-xl shadow-md">
                        <img src="images/indoor.PNG" alt="Indoor Tiles" class="w-full h-64 object-cover">
                    </div>
                    <p class="text-lg font-semibold text-amber-700">Indoor</p>
                </div>
                
                <div class="tile-selection-item text-center fade-in">
                    <div class="mb-4 overflow-hidden rounded-xl shadow-md">
                        <img src="images/outdoor.PNG" alt="Outdoor Tiles" class="w-full h-64 object-cover">
                    </div>
                    <p class="text-lg font-semibold text-amber-700">Outdoor</p>
                </div>
                
                <div class="tile-selection-item text-center fade-in">
                    <div class="mb-4 overflow-hidden rounded-xl shadow-md">
                        <img src="images/industrial.PNG" alt="Industrial Tiles" class="w-full h-64 object-cover">
                    </div>
                    <p class="text-lg font-semibold text-amber-700">Industrial</p>
                </div>
                
                <div class="tile-selection-item text-center fade-in">
                    <div class="mb-4 overflow-hidden rounded-xl shadow-md">
                        <img src="images/pool.PNG" alt="Pool Tiles" class="w-full h-64 object-cover">
                    </div>
                    <p class="text-lg font-semibold text-amber-700">Pool</p>
                </div>
                
                <div class="tile-selection-item text-center fade-in">
                    <div class="mb-4 overflow-hidden rounded-xl shadow-md">
                        <img src="images/countertops.PNG" alt="Countertop Tiles" class="w-full h-64 object-cover">
                    </div>
                    <p class="text-lg font-semibold text-amber-700">Countertops</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Patterns and Designs Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 fade-in">Patterns and Designs</h2>
            
            <div class="space-y-12 max-w-6xl mx-auto">
                <!-- Floral Patterns -->
                <div class="pattern-category fade-in">
                    <h3 class="text-xl font-semibold mb-6 text-center uppercase tracking-wide text-gray-700">Floral</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="pattern-tile aspect-square rounded-lg shadow-md" style="background-image: url('images/p&d/floral1.PNG'); background-size: cover; background-position: center;"></div>
                        <div class="pattern-tile aspect-square rounded-lg shadow-md" style="background-image: url('images/p&d/floral2.PNG'); background-size: cover; background-position: center;"></div>
                        <div class="pattern-tile aspect-square rounded-lg shadow-md" style="background-image: url('images/p&d/floral3.PNG'); background-size: cover; background-position: center;"></div>
                        <div class="pattern-tile aspect-square rounded-lg shadow-md" style="background-image: url('images/p&d/floral4.PNG'); background-size: cover; background-position: center;"></div>
                    </div>
                </div>
                
                <!-- Minimalist Patterns -->
                <div class="pattern-category fade-in">
                    <h3 class="text-xl font-semibold mb-6 text-center uppercase tracking-wide text-gray-700">Minimalist</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="pattern-tile aspect-square rounded-lg shadow-md" style="background-image: url('images/p&d/minimalist1.PNG'); background-size: cover; background-position: center;"></div>
                        <div class="pattern-tile aspect-square rounded-lg shadow-md" style="background-image: url('images/p&d/minimalist2.PNG'); background-size: cover; background-position: center;"></div>
                        <div class="pattern-tile aspect-square rounded-lg shadow-md" style="background-image: url('images/p&d/minimalist3.PNG'); background-size: cover; background-position: center;"></div>
                        <div class="pattern-tile aspect-square rounded-lg shadow-md" style="background-image: url('images/p&d/minimalist4.PNG'); background-size: cover; background-position: center;"></div>
                    </div>
                </div>
                
                <!-- Black and White Patterns -->
                <div class="pattern-category fade-in">
                    <h3 class="text-xl font-semibold mb-6 text-center uppercase tracking-wide text-gray-700">Black and White</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="pattern-tile aspect-square rounded-lg shadow-md" style="background-image: url('images/p&d/b&w1.PNG'); background-size: cover; background-position: center;"></div>
                        <div class="pattern-tile aspect-square rounded-lg shadow-md" style="background-image: url('images/p&d/b&w2.PNG'); background-size: cover; background-position: center;"></div>
                        <div class="pattern-tile aspect-square rounded-lg shadow-md" style="background-image: url('images/p&d/b&w3.PNG'); background-size: cover; background-position: center;"></div>
                        <div class="pattern-tile aspect-square rounded-lg shadow-md" style="background-image: url('images/p&d/b&w4.PNG'); background-size: cover; background-position: center;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <script>
        // Tab functionality
        const glossyBtn = document.getElementById('glossyBtn');
        const matteBtn = document.getElementById('matteBtn');
        const glossyContent = document.getElementById('glossyContent');
        const matteContent = document.getElementById('matteContent');

        glossyBtn.addEventListener('click', function() {
            glossyBtn.classList.add('bg-amber-700', 'text-white');
            glossyBtn.classList.remove('text-gray-700', 'hover:bg-gray-200');
            matteBtn.classList.remove('bg-amber-700', 'text-white');
            matteBtn.classList.add('text-gray-700', 'hover:bg-gray-200');
            glossyContent.classList.remove('hidden');
            matteContent.classList.add('hidden');
        });

        matteBtn.addEventListener('click', function() {
            matteBtn.classList.add('bg-amber-700', 'text-white');
            matteBtn.classList.remove('text-gray-700', 'hover:bg-gray-200');
            glossyBtn.classList.remove('bg-amber-700', 'text-white');
            glossyBtn.classList.add('text-gray-700', 'hover:bg-gray-200');
            matteContent.classList.remove('hidden');
            glossyContent.classList.add('hidden');
        });

        // Scroll animation functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Intersection Observer for scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, observerOptions);

            // Observe all elements with fade-in class
            document.querySelectorAll('.fade-in').forEach(element => {
                observer.observe(element);
            });
        });
    </script>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>