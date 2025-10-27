<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $apiKey = $input['apiKey'] ?? '';
    $prompt = $input['prompt'] ?? '';
    $imageReference = $input['imageReference'] ?? '';
    $tileName = $input['tileName'] ?? '';
    
    if (empty($apiKey) || empty($prompt) || empty($imageReference)) {
        echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
        exit;
    }
    
    try {
        // Stability AI API endpoint for image-to-image
        $url = 'https://api.stability.ai/v1/generation/stable-diffusion-xl-1024-v1-0/image-to-image';
        
        // Create a temporary file for the image
        $imageData = base64_decode($imageReference);
        $tempFile = tempnam(sys_get_temp_dir(), 'tile_');
        file_put_contents($tempFile, $imageData);
        
        $postData = [
            'init_image' => new CURLFile($tempFile, 'image/jpeg', 'tile_reference.jpg'),
            'text_prompts[0][text]' => $prompt,
            'text_prompts[0][weight]' => 1,
            'image_strength' => 0.35,
            'cfg_scale' => 7,
            'samples' => 1,
            'steps' => 30,
            'style_preset' => 'photographic'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $apiKey",
            "Accept: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Clean up temp file
        unlink($tempFile);
        
        if (curl_error($ch)) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        if ($httpCode === 200) {
            $responseData = json_decode($response, true);
            
            if (isset($responseData['artifacts'][0]['base64'])) {
                $imageData = base64_decode($responseData['artifacts'][0]['base64']);
                $base64Image = 'data:image/png;base64,' . base64_encode($imageData);
                
                echo json_encode([
                    'success' => true,
                    'image' => $base64Image
                ]);
            } else {
                throw new Exception('No image generated in response');
            }
        } else {
            $errorData = json_decode($response, true);
            $errorMsg = $errorData['message'] ?? "HTTP $httpCode: API request failed";
            throw new Exception($errorMsg);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>