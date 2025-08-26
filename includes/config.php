<?php
// Start session with proper configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0);
    
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
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

// Initialize session variables
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = null;
    $_SESSION['username'] = null;
    $_SESSION['email'] = null;
    $_SESSION['loggedin'] = false;
    $_SESSION['is_admin'] = false;
}

// Include helper functions
require_once 'functions.php';
?>