<?php
require_once 'includes/config.php';

echo "<h2>Testing the Fix</h2>";

// Test the is_logged_in() function
echo "is_logged_in(): " . (is_logged_in() ? 'TRUE' : 'FALSE') . "<br>";

// Manually set session to test
$_SESSION['loggedin'] = 1; // This is what your login sets
$_SESSION['id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['is_admin'] = 1;

echo "After setting session to 1:<br>";
echo "is_logged_in(): " . (is_logged_in() ? 'TRUE' : 'FALSE') . "<br>";

// Test with boolean true
$_SESSION['loggedin'] = true;
echo "After setting session to true:<br>";
echo "is_logged_in(): " . (is_logged_in() ? 'TRUE' : 'FALSE') . "<br>";

// Test with false
$_SESSION['loggedin'] = false;
echo "After setting session to false:<br>";
echo "is_logged_in(): " . (is_logged_in() ? 'TRUE' : 'FALSE') . "<br>";

echo "<h3>Current Session:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>