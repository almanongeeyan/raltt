<?php
// login.php
// This file initiates the Google OAuth flow.

// Include the Google API PHP Client library.
// Install it via Composer: composer require google/apiclient
require_once 'vendor/autoload.php';

// *** IMPORTANT: Replace with your actual Client ID and Secret ***
$clientId = 'YOUR_CLIENT_ID';
$clientSecret = 'YOUR_CLIENT_SECRET';

// The redirect URI must be the exact URL of the file that handles the callback.
// This MUST be added to "Authorized redirect URIs" in the Google Cloud Console.
// We are pointing it to the callback.php file.
$redirectUri = 'http://localhost/raltt/callback.php';

// Create a new Google Client instance
$client = new Google\Client();
$client->setClientId($clientId);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);

// Request access to the user's email and profile
$client->addScope('email');
$client->addScope('profile');

// Create the Google authorization URL and redirect the user
$authUrl = $client->createAuthUrl();
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
exit();
?>

<?php
// callback.php
// This file handles the response from Google after the user has signed in.

// Include the Google API PHP Client library.
require_once 'vendor/autoload.php';

// *** IMPORTANT: Replace with your actual Client ID and Secret ***
$clientId = '1043599229897-c7t8ir646mn4i1abs79eeg51r4hu4j66.apps.googleusercontent.com';
$clientSecret = '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';

// The redirect URI must be the exact URL of the file that handles the callback.
// It must match the URI set in login.php exactly.
$redirectUri = 'http://localhost/raltt/callback.php';

$client = new Google\Client();
$client->setClientId($clientId);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);

// Check if an authorization code was received from Google
if (isset($_GET['code'])) {
    try {
        // Exchange the authorization code for an access token
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token);

        // Fetch the user's profile information using the access token
        $oauth2 = new Google\Service\Oauth2($client);
        $userInfo = $oauth2->userinfo->get();

        // At this point, the user is successfully authenticated.
        // You can now store this user's session or save their data.
        echo "<h1 style='font-family: Inter, sans-serif;'>Welcome, " . htmlspecialchars($userInfo->name) . "!</h1>";
        echo "<p style='font-family: Inter, sans-serif;'>Your email is: " . htmlspecialchars($userInfo->email) . "</p>";

    } catch (Exception $e) {
        // Handle errors, such as invalid code or network issues
        echo "<h1 style='font-family: Inter, sans-serif; color: red;'>An error occurred.</h1>";
        echo "<p style='font-family: Inter, sans-serif;'>Details: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    // This case handles when the user cancels the login or something fails before the redirect
    echo "<h1 style='font-family: Inter, sans-serif;'>Login failed.</h1>";
    echo "<p style='font-family: Inter, sans-serif;'>No authorization code was received.</p>";
}
?>
