<?php
// Start a session to store user data
session_start();
// Prevent back navigation after login
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
// If already logged in, redirect to landing page
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    header('Location: ../logged_user/landing_page.php');
    exit();
}
// Include Composer's autoloader to load the Google API Client library
require_once '../vendor/autoload.php';

// Include the database connection file
require_once 'connection.php';

// Set the Google Client ID
$clientID = '1043599229897-c7t8ir646mn4i1abs79eeg51r4hu4j66.apps.googleusercontent.com';

// Set the content type for the response
header('Content-Type: application/json');

// Handle incoming POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $idToken = $input['id_token'] ?? null;

    if (!$idToken) {
        echo json_encode(['success' => false, 'message' => 'ID token not provided.']);
        exit;
    }

    try {
        // Create a new Google Client and verify the ID token
        $client = new Google_Client(['client_id' => $clientID]);
        $payload = $client->verifyIdToken($idToken);

        if (!$payload) {
            // Invalid ID token
            echo json_encode(['success' => false, 'message' => 'Invalid ID token.']);
            exit;
        }

        // Token is valid, get user data from the payload
    $googleId = $payload['sub'];
    $fullName = $payload['name'];
    $email = $payload['email'];
    // Optional fields from payload (if available)
    $phoneNumber = $payload['phone_number'] ?? null;
    $houseAddress = null; // Not available from Google, can be set later
    $fullAddress = null; // Not available from Google, can be set later
    $referralCode = null; // Can be set later

        // Check if the database connection is valid
        if (!$conn) {
            echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
            exit;
        }


        // Use a transaction to ensure both operations (check and insert/update) are atomic
        $db_connection->beginTransaction();

        // Check if the user already exists in the users table
        $stmt = $db_connection->prepare("SELECT * FROM users WHERE google_id = ?");
        $stmt->execute([$googleId]);
        $userExists = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userExists) {
            // User exists, update their details and last_login
            $stmt_update = $db_connection->prepare("UPDATE users SET full_name = ?, email = ?, last_login = NOW() WHERE google_id = ?");
            $stmt_update->execute([$fullName, $email, $googleId]);
            $userId = $userExists['id'];
        } else {
            // New user, insert a new record (other fields can be null/default)
            $stmt_insert = $db_connection->prepare("INSERT INTO users (google_id, full_name, email, phone_number, house_address, full_address, referral_code, account_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_insert->execute([$googleId, $fullName, $email, $phoneNumber, $houseAddress, $fullAddress, $referralCode, 'active']);
            $userId = $db_connection->lastInsertId();
        }

        // Commit the transaction if everything was successful
        $db_connection->commit();

        // Set session variables for the logged-in user
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $fullName;
        $_SESSION['user_email'] = $email;
        $_SESSION['logged_in'] = true;

        // Respond with success and user's name
        echo json_encode(['success' => true, 'name' => $fullName, 'message' => 'Login successful.']);

    } catch (Google_Service_Exception $e) {
        // Catch specific Google API errors for better debugging
        if ($conn && $conn->inTransaction()) {
            $conn->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'Google API error: ' . $e->getMessage()]);
    } catch (PDOException $e) {
        // Catch specific PDO database errors
        if ($conn && $conn->inTransaction()) {
            $conn->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    } catch (Exception $e) {
        // Catch any other general errors
        if ($conn && $conn->inTransaction()) {
            $conn->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
    }
} else {
    // Respond to non-POST requests with an error message
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>