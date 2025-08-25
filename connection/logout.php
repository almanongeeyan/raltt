
<?php
session_start();
// Unset all session variables
$_SESSION = array();
// Destroy the session.
session_destroy();
// Prevent back navigation after logout
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
// Redirect to index.php if redirect param is set, else tresspass.php
// Always redirect to /raltt/index.php if requested, else tresspass.php
$redirect = isset($_GET['redirect']) && $_GET['redirect'] === 'index.php' ? '/raltt/index.php' : 'tresspass.php';
header('Location: ' . $redirect);
exit();
