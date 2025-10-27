<?php
header('Content-Type: application/json');
require_once 'connection.php';
require_once 'ImageHasher.php';

function send_error(int $statusCode, string $message) {
    http_response_code($statusCode);
    echo json_encode(['success' => false, 'message' => $message]);
    exit();
}

if (!extension_loaded('gd')) {
    send_error(500, "Server configuration error: The GD graphics library is missing or disabled.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_error(405, 'Method Not Allowed.');
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    send_error(400, 'No image file was uploaded correctly.');
}

try {
    $uploadedFile = $_FILES['image']['tmp_name'];
    
    // Validate uploaded image
    $imageInfo = @getimagesize($uploadedFile);
    if (!$imageInfo || !in_array($imageInfo[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP])) {
        send_error(400, 'Invalid image format. Please upload JPEG, PNG, or WebP images.');
    }
    
    // Check if image is too dark/black
    $image = imagecreatefromstring(file_get_contents($uploadedFile));
    if (!$image) {
        send_error(400, 'Cannot process the uploaded image.');
    }
    
    // Analyze image for darkness/emptiness
    $width = imagesx($image);
    $height = imagesy($image);
    $totalBrightness = 0;
    $samplePoints = min(100, $width * $height); // Sample up to 100 points
    
    $darkPixels = 0;
    for ($i = 0; $i < $samplePoints; $i++) {
        $x = rand(0, $width - 1);
        $y = rand(0, $height - 1);
        $rgb = imagecolorat($image, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        $brightness = ($r + $g + $b) / 3;
        $totalBrightness += $brightness;
        
        if ($brightness < 30) { // Count pixels that are very dark
            $darkPixels++;
        }
    }
    
    $averageBrightness = $totalBrightness / $samplePoints;
    $darkPercentage = ($darkPixels / $samplePoints) * 100;
    imagedestroy($image);
    
    // REJECT if image is too dark/black
    if ($averageBrightness < 20 || $darkPercentage > 90) {
        echo json_encode([
            'success' => false, 
            'message' => 'Image too dark or empty. Please ensure good lighting and focus on a clear tile pattern.',
            'debug' => [
                'average_brightness' => round($averageBrightness),
                'dark_percentage' => round($darkPercentage),
                'rejected_reason' => 'image_too_dark'
            ]
        ]);
        exit();
    }
    
    // REJECT if image is too bright/white
    if ($averageBrightness > 240) {
        echo json_encode([
            'success' => false, 
            'message' => 'Image too bright or overexposed. Please adjust lighting.',
            'debug' => [
                'average_brightness' => round($averageBrightness),
                'rejected_reason' => 'image_too_bright'
            ]
        ]);
        exit();
    }

    $hasher = new ImageHasher();
    $uploadedImageHash = $hasher->calculate($uploadedFile);

    // Get all tile products with their perceptual hashes
    $stmt = $conn->prepare('
        SELECT product_id, product_image_phash, product_name, product_description, product_price 
        FROM products 
        WHERE product_type = "tile" 
        AND is_archived = 0 
        AND product_image_phash IS NOT NULL 
        AND LENGTH(product_image_phash) = 16
    ');
    $stmt->execute();
    $dbHashes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($dbHashes)) {
        send_error(404, 'No searchable tiles in the database. Please run the hash generation script.');
    }

    // STRICTER matching parameters
    $matchThreshold = 6; // More strict - only very close matches
    $confidenceThreshold = 75; // Higher confidence required
    
    $matches = [];

    foreach ($dbHashes as $dbHash) {
        $distance = $hasher->distance($uploadedImageHash, $dbHash['product_image_phash']);
        
        // Only accept very close matches
        if ($distance <= $matchThreshold) {
            $confidence = max(0, round(100 - ($distance / $matchThreshold * 100)));
            
            if ($confidence >= $confidenceThreshold) {
                $matches[] = [
                    'product_id' => $dbHash['product_id'],
                    'product_name' => $dbHash['product_name'],
                    'product_description' => $dbHash['product_description'],
                    'product_price' => $dbHash['product_price'],
                    'distance' => $distance,
                    'confidence' => $confidence
                ];
            }
        }
    }

    // Sort matches by confidence (highest first)
    usort($matches, function($a, $b) { 
        return $b['confidence'] <=> $a['confidence']; 
    });

    // Get design information for matches
    $results = [];
    foreach ($matches as $match) {
        $stmt = $conn->prepare('
            SELECT GROUP_CONCAT(DISTINCT td.design_name) AS product_design 
            FROM product_designs pd 
            LEFT JOIN tile_designs td ON pd.design_id = td.design_id 
            WHERE pd.product_id = :id 
            GROUP BY pd.product_id
        ');
        $stmt->execute(['id' => $match['product_id']]);
        $designResult = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $results[] = [
            'tile_id' => $match['product_id'],
            'tile_name' => $match['product_name'],
            'tile_description' => $match['product_description'],
            'tile_price' => $match['product_price'],
            'tile_design' => $designResult['product_design'] ? explode(',', $designResult['product_design']) : [],
            'confidence' => $match['confidence']
        ];
    }

    // Return results - will be empty if no good matches
    echo json_encode([
        'success' => true, 
        'products' => $results, 
        'message' => empty($results) ? 'No matching tiles found. Please ensure you\'re scanning a clear tile pattern with good lighting.' : 'Match found!',
        'debug' => [
            'uploaded_hash' => $uploadedImageHash,
            'total_compared' => count($dbHashes),
            'matches_found' => count($results),
            'average_brightness' => round($averageBrightness),
            'match_threshold' => $matchThreshold,
            'confidence_threshold' => $confidenceThreshold
        ]
    ]);

} catch (Exception $e) {
    error_log("AR Match Error: " . $e->getMessage());
    send_error(500, 'An internal error occurred while processing the image.');
}