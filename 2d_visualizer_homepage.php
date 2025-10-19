<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rich Anne Lea Tiles Trading - Tile Visualizer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.js"></script>
    <style>
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body class="bg-gray-50 font-inter">
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="py-16 bg-gradient-to-br from-amber-50 to-white">
        <div class="container mx-auto px-4 text-center fade-in">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">
                <span class="text-amber-600">Visualize</span> Your Imagination
            </h1>
            <p class="text-lg md:text-xl text-gray-700 max-w-4xl mx-auto leading-relaxed">
                You can navigate various textures, colors, styles, and a lot more and cherish everything in "Almost real life" virtualizations. 
                If you want to effortlessly weave the tapestry of imagination and actuality and allow your space to eloquently echo your distinctive panache, 
                it's your time to <span class="font-semibold">contact us today!</span>
            </p>
        </div>
    </section>

    <!-- 3D Tile Visualizer Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row items-center gap-12 max-w-6xl mx-auto fade-in">
                <div class="lg:w-1/2">
                    <div class="rounded-xl overflow-hidden shadow-lg">
                        <img src="images/2d.PNG" alt="3D Tile Visualizer" class="w-full h-80 object-cover">
                    </div>
                </div>
                <div class="lg:w-1/2 text-center lg:text-left">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">3D Tile Visualizer</h2>
                    <p class="text-gray-600 text-lg mb-4 leading-relaxed">
                        You will definitely enjoy visualizing tiles of your choice and selecting pre-designed rooms to witness 
                        their chosen products in their home environment.
                    </p>
                    <p class="text-gray-800 font-semibold text-lg mb-8 leading-relaxed">
                        Let's enhance your shopping journey with our 3D Tile Visualizer!
                    </p>
                    <a href="user_login_form.php" class="inline-flex items-center px-8 py-4 bg-amber-700 text-white font-semibold rounded-lg shadow-md hover:bg-amber-800 transition-colors duration-300">
                        Try Our Visualizer!
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Cards Section -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <!-- Card 1 -->
                <div class="fade-in card-hover">
                    <div class="bg-white rounded-xl shadow-md p-8 text-center h-full transition-all duration-300 hover:shadow-lg">
                        <div class="text-amber-700 mb-6">
                            <i class="fa-solid fa-diamond text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Pick your desired tiles</h3>
                        <p class="text-gray-600">Browse our extensive collection and select the perfect tiles for your project</p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="fade-in card-hover" style="transition-delay: 0.1s">
                    <div class="bg-white rounded-xl shadow-md p-8 text-center h-full transition-all duration-300 hover:shadow-lg">
                        <div class="text-amber-700 mb-6">
                            <i class="fa-solid fa-th-large text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Align them all you want</h3>
                        <p class="text-gray-600">Experiment with different layouts and patterns to find your perfect design</p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="fade-in card-hover" style="transition-delay: 0.2s">
                    <div class="bg-white rounded-xl shadow-md p-8 text-center h-full transition-all duration-300 hover:shadow-lg">
                        <div class="text-amber-700 mb-6">
                            <i class="fa-solid fa-wand-magic-sparkles text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Play with your imagination</h3>
                        <p class="text-gray-600">Bring your creative visions to life with our intuitive visualization tools</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
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