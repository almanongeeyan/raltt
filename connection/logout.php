
<?php
session_start();
// Prevent back navigation after logout
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
session_destroy();
// Redirect to login page
header('Location: ../index.php');
exit();
