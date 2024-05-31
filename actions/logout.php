<?php
session_start();

if(session_status() == PHP_SESSION_ACTIVE) {
    // Unset all session variables
    $_SESSION = array();

    session_destroy();
}
header("Location: /pages/login.php");
exit();
?>