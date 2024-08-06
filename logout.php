<?php
session_start();

// React
session_unset();
session_destroy();
header("Location: login.php");

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page with a logout message
header("Location: login.php?message=Successfully logged out");
exit;
?>

