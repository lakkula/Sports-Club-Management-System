<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in using $_SESSION
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

?>