<?php
session_start();
if (isset($_SESSION['login'])) {
    include('./pages/dashboard.php');
} else {
    header('Location: pages/login.php');
    exit();
}
?>