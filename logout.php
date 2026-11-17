<?php

session_start();

// Unset all session variables
$_SESSION = array();

session_destroy();

// Redirect back to the login page with a status flag
header("Location: login.php?logged_out=1");
exit();
?>
