<?php
// Fix session configuration - SIMPLIFIED VERSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'ecommerce_db');

// Create database connection
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($mysqli->connect_error) {
    die("ERROR: Could not connect. " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8");

// Base URL
define('BASE_URL', 'http://localhost:8080/');

// Initialize session variables if not set - FIXED
if (!isset($_SESSION['loggedin'])) {
    $_SESSION['loggedin'] = false;
}
if (!isset($_SESSION['id'])) {
    $_SESSION['id'] = null;
}
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = null;
}
if (!isset($_SESSION['email'])) {
    $_SESSION['email'] = null;
}
if (!isset($_SESSION['is_admin'])) {
    $_SESSION['is_admin'] = false;
}

// Include helper functions
require_once 'functions.php';
?>