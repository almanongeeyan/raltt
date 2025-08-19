<?php
header('Content-Type: application/json');

// Include the database connection file. 
// Assuming 'connection.php' correctly sets up $db_connection for PDO.
require_once 'connection.php'; 

// Initialize response array
$response = ['status' => 'error', 'message' => 'Invalid request or missing data'];

try {
    // Get the raw POST data from the request body
    $json = file_get_contents('php://input');
    $data = json_decode($json, true); // Decode JSON into an associative array

    // Ensure the request method is POST and 'phone' data is present
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['phone'])) {
        $phone = trim($data['phone']); // Trim whitespace from the phone number

        // Validate phone number format for Philippine mobile numbers
        // This regex ensures it starts with '+639' followed by 9 digits.
        if (!preg_match('/^\+639[0-9]{9}$/', $phone)) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid phone number format. Must be in +639xxxxxxxxx format.'
            ];
            echo json_encode($response);
            exit(); // Stop execution if format is invalid
        }

        // Prepare a SQL statement to count existing phone numbers in 'manual_accounts' table
        // Using 'phone_number' as the column name, and a named parameter ':phone'
        $stmt = $db_connection->prepare("SELECT COUNT(*) as count FROM manual_accounts WHERE phone_number = :phone");
        
        // Bind the phone number parameter to the prepared statement
        // PDO::PARAM_STR ensures the value is treated as a string
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        
        // Execute the prepared statement
        $stmt->execute();
        
        // Fetch the result. fetch() with FETCH_ASSOC returns an associative array.
        $result = $stmt->fetch(PDO::FETCH_ASSOC); 
        
        // Check if a record with the phone number was found
        if ($result['count'] > 0) {
            $response = [
                'status' => 'registered',
                'message' => 'This phone number is already registered.'
            ];
        } else {
            // Phone number is not in the database
            $response = [
                'status' => 'available',
                'message' => 'Phone number is available for registration.'
            ];
        }
    } else {
        // If request is not POST or 'phone' data is missing
        $response['message'] = 'Missing phone number in request or invalid request method.';
    }
} catch (PDOException $e) {
    // Catch PDO (database) specific errors
    // Log the detailed error message to a file for debugging purposes
    error_log("[".date('Y-m-d H:i:s')."] Database error in check_phone_registered.php: " . $e->getMessage() . PHP_EOL, 3, "database_errors.log");
    
    $response = [
        'status' => 'error',
        'message' => 'A database error occurred. Please try again later.'
    ];
} catch (Exception $e) {
    // Catch any other general PHP errors or exceptions
    // Log these general errors
    error_log("[".date('Y-m-d H:i:s')."] General error in check_phone_registered.php: " . $e->getMessage() . PHP_EOL, 3, "errors.log");
    
    $response = [
        'status' => 'error',
        'message' => 'An unexpected server error occurred.'
    ];
}

// Encode the PHP response array into a JSON string and output it
echo json_encode($response);
exit(); // Ensure no further output is sent
?>