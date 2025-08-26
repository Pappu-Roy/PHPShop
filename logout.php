<?php
require_once 'includes/config.php';

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("location: login.php");
exit;
?>