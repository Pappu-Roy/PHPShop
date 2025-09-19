<?php
session_start();
echo "<h2>Session Debug Information</h2>";

// Check session configuration
echo "Session ID: " . session_id() . "<br>";
echo "Session Status: " . session_status() . "<br>";
echo "Session Save Path: " . session_save_path() . "<br>";

// Check if session is writable
$session_path = session_save_path();
if (empty($session_path)) {
    $session_path = sys_get_temp_dir();
}
echo "Session Path: " . $session_path . "<br>";
echo "Session Writable: " . (is_writable($session_path) ? 'Yes' : 'No') . "<br>";

// Test session writing
$_SESSION['debug_test'] = 'Session is working!';
echo "Session write test: " . (isset($_SESSION['debug_test']) ? 'SUCCESS' : 'FAILED') . "<br>";

// Show current session data
echo "<h3>Current Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Test database connection
require_once 'includes/config.php';
echo "Database connection: " . (!$mysqli->connect_error ? 'SUCCESS' : 'FAILED') . "<br>";

// Test form for session persistence
echo "<h3>Test Session Persistence:</h3>";
echo "<form method='post'>";
echo "<input type='submit' name='test_session' value='Test Session Persistence'>";
echo "</form>";

if (isset($_POST['test_session'])) {
    $_SESSION['test_count'] = isset($_SESSION['test_count']) ? $_SESSION['test_count'] + 1 : 1;
    header("Location: debug_sessions.php");
    exit;
}

if (isset($_SESSION['test_count'])) {
    echo "Session persistence test: SUCCESS (Count: " . $_SESSION['test_count'] . ")<br>";
}

$mysqli->close();
?>
