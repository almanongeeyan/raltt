<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tailwind Footer Design</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2B3241',
                        accent: '#F47C2E',
                        light: '#f7f7fa',
                    },
                    fontFamily: {
                        inter: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-light flex flex-col min-h-screen font-inter">
    <div class="flex-grow"></div>

    <footer class="bg-primary text-white pt-8 pb-8">
        <!-- Top line -->
        <div class="w-[92%] h-px bg-white mx-auto mb-8"></div>
        
        <!-- Main content -->
    <div class="w-[92%] max-w-6xl mx-auto flex flex-col lg:flex-row justify-center items-stretch gap-6 lg:gap-10 mb-0">
            <!-- Left section with square -->
            <div class="w-full lg:w-auto">
                <div class="border border-white p-6 lg:p-10 max-w-2xl bg-primary/98 shadow-lg rounded-lg">
                    <div class="flex flex-col md:flex-row justify-center items-start md:justify-start md:items-start gap-6 md:gap-10 text-left">
                        <!-- Features column -->
                        <div class="min-w-[180px] pr-0 md:pr-6 flex flex-col items-start">
                            <p class="font-bold mb-2 tracking-wide">Features</p>
                            <a href="3d_visualizer_homepage.php" class="block text-white no-underline mb-1 rounded px-1.5 py-0.5 transition-all duration-300 hover:text-accent hover:bg-[rgba(239,114,50,0.08)]">3D Tile Visualizer</a>
                            <a href="referral_code.php" class="block text-white no-underline mb-1 rounded px-1.5 py-0.5 transition-all duration-300 hover:text-accent hover:bg-[rgba(239,114,50,0.08)]">Referral Code</a>
                        </div>

                        <!-- Meet RALTT column -->
                        <div class="min-w-[180px] pr-0 md:pr-6 flex flex-col items-start">
                            <p class="font-bold mb-2 tracking-wide">Meet RALTT</p>
                            <a href="about_us.php" class="block text-white no-underline mb-1 rounded px-1.5 py-0.5 transition-all duration-300 hover:text-accent hover:bg-[rgba(239,114,50,0.08)]">About Us</a>
                            <a href="product_view_homepage.php" class="block text-white no-underline mb-1 rounded px-1.5 py-0.5 transition-all duration-300 hover:text-accent hover:bg-[rgba(239,114,50,0.08)]">Tile E-Commerce</a>
                        </div>

                        <!-- Company column (About Us, Privacy, Other branches) -->
                        <div class="min-w-[180px] pr-0 md:pr-6 flex flex-col items-start">
                            <p class="font-bold mb-2 tracking-wide">Company</p>
                            <a href="privacy_info_homepage.php" class="block text-white no-underline mb-1 rounded px-1.5 py-0.5 transition-all duration-300 hover:text-accent hover:bg-[rgba(239,114,50,0.08)]">Privacy</a>
                            <a href="about_us.php#branches" class="block text-white no-underline mb-1 rounded px-1.5 py-0.5 transition-all duration-300 hover:text-accent hover:bg-[rgba(239,114,50,0.08)]">Other branches</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right section with text -->
            <div class="flex flex-col justify-center items-start min-w-[340px] text-left mt-6 lg:mt-0">
                <span class="text-accent font-bold text-3xl md:text-4xl lg:text-5xl mb-1 tracking-wide leading-tight">Check Out Now,</span>
                <span class="text-white font-bold text-3xl md:text-4xl lg:text-5xl tracking-wide leading-tight">Same-day delivery.</span>
                <span class="text-xs text-gray-300 mt-2">&copy; 2025 RALTT Credits. All rights reserved.</span>
            </div>
        </div>
        
        <!-- Bottom line -->
        <div class="w-[92%] h-px bg-white mx-auto my-8"></div>
        
    </footer>
</body>
</html>