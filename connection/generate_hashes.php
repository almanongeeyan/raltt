<?php
set_time_limit(600); 
ini_set('memory_limit', '256M');

echo "<!DOCTYPE html><html><head><title>Hash Generator</title><style>body{font-family:monospace; background:#333; color:#eee;}</style></head><body><pre>";

require_once 'connection.php';
require_once 'ImageHasher.php';

echo "Starting hash generation for all tile products...\n\n";

try {
    $hasher = new ImageHasher();
    $stmt = $conn->prepare("SELECT product_id, product_image FROM products WHERE product_type = 'tile' AND product_image IS NOT NULL");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($products)) {
        echo "No products with images found to process.\n";
    }

    $updatedCount = 0;
    foreach ($products as $product) {
        $productId = $product['product_id'];
        $imageData = $product['product_image'];

        if (empty($imageData)) {
            echo "Skipping Product ID: $productId (no image data).\n";
            continue;
        }

        $pHash = $hasher->calculateFromBlob($imageData);
        $updateStmt = $conn->prepare("UPDATE products SET product_image_phash = :phash WHERE product_id = :id");
        $updateStmt->execute(['phash' => $pHash, 'id' => $productId]);

        echo "Generated hash for Product ID: $productId -> $pHash\n";
        $updatedCount++;
    }

    echo "\nProcess complete. Updated $updatedCount products.\n";

} catch (PDOException $e) {
    echo "\nDATABASE ERROR: " . $e->getMessage();
} catch (Exception $e) {
    echo "\nAn error occurred: " . $e->getMessage();
}

echo "</pre></body></html>";