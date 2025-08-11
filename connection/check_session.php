<?php
// check_session.php: returns 'OK' if user session is valid, otherwise returns 'NO'
session_start();
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    echo 'OK';
} else {
    echo 'NO';
}
