<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $apiKey = $input['apiKey'] ?? '';
    $prompt = $input['prompt'] ?? '';
    
    if (empty($apiKey) || empty($prompt)) {
        echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
        exit;
    }
    
    try {
        $url = 'https://api.stability.ai/v1/generation/stable-diffusion-xl-1024-v1-0/text-to-image';
        
        $data = [
            'text_prompts' => [
                [
                    'text' => $prompt,
                    'weight' => 1
                ]
            ],
            'cfg_scale' => 7,
            'height' => 1024,  // Valid dimension
            'width' => 1024,   // Valid dimension
            'samples' => 1,
            'steps' => 30,
            'style_preset' => 'photographic'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $apiKey",
            "Content-Type: application/json",
            "Accept: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
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