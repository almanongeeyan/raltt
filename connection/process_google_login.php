<?php
// Start a session to store user data
session_start();

// Include Composer's autoloader to load the Google API Client library
require_once '../vendor/autoload.php';

// Correct the include path for the database connection file
require_once 'connection.php';

// Set the Google Client ID
$clientID = '1043599229897-c7t8ir646mn4i1abs79eeg51r4hu4j66.apps.googleusercontent.com';

// Handle incoming POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idToken = json_decode(file_get_contents('php://input'), true)['id_token'] ?? null;

    if (!$idToken) {
        echo json_encode(['success' => false, 'message' => 'ID token not provided.']);
        exit;
    }

    try {
        $client = new Google_Client(['client_id' => $clientID]);
        $payload = $client->verifyIdToken($idToken);

        if ($payload) {
            // Token is valid, get user data from the payload
            $googleId = $payload['sub'];
            $name = $payload['name'];
            $email = $payload['email'];
            $profilePicture = $payload['picture'] ?? null;

            // Database connection
            // The connection is already established in connection.php
            // We use the $conn variable from that file.

            if (!$conn) {
                echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
                exit;
            }

            // Check if the user already exists in the database
            $stmt = $conn->prepare("SELECT * FROM google_accounts WHERE google_id = ?");
            $stmt->execute([$googleId]);
            $userExists = $stmt->fetch();

            if ($userExists) {
                // User exists, update their details
                $stmt_update = $conn->prepare("UPDATE google_accounts SET name = ?, email = ?, profile_picture = ? WHERE google_id = ?");
                $stmt_update->execute([$name, $email, $profilePicture, $googleId]);
            } else {
                // New user, insert a new record
                $stmt_insert = $conn->prepare("INSERT INTO google_accounts (google_id, name, email, profile_picture) VALUES (?, ?, ?, ?)");
                $stmt_insert->execute([$googleId, $name, $email, $profilePicture]);
            }

            // Set session variables for the logged-in user
            $_SESSION['user_id'] = $googleId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['logged_in'] = true;

            // Respond with success and user's name
            echo json_encode(['success' => true, 'name' => $name, 'message' => 'Login successful.']);

        } else {
            // Invalid ID token
            echo json_encode(['success' => false, 'message' => 'Invalid ID token.']);
        }
    } catch (Exception $e) {
        // Handle token verification errors
        echo json_encode(['success' => false, 'message' => 'Error verifying token: ' . $e->getMessage()]);
    }
} else {
    // Respond to non-POST requests
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>