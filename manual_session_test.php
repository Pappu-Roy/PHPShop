<?php
// Manual session test
session_start();

// Manually set session
$_SESSION['manual_test'] = true;
$_SESSION['test_user'] = 'admin';
$_SESSION['test_time'] = date('Y-m-d H:i:s');

echo "<h2>Manual Session Test</h2>";
echo "Session set manually. <a href='check_session.php'>Check if session persists</a>";
?>