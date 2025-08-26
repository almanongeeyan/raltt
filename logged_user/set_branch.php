<?php
// set_branch.php: Sets the user's branch in session via POST (AJAX)
session_start();
if (isset($_POST['branch_id'])) {
    $_SESSION['branch_id'] = (int)$_POST['branch_id'];
    echo 'OK';
} else {
    http_response_code(400);
    echo 'Missing branch_id';
}
