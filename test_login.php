<?php
// SIMPLE LOGIN TEST - NO REDIRECTS
session_start();
require_once 'includes/config.php';

echo "<h2>Login Test</h2>";

// Test credentials
$test_email = 'admin@phpshop.com';
$test_password = 'admin123';

echo "Testing login for: $test_email<br>";

// Check if user exists
$sql = "SELECT id, username, email, password, is_admin FROM users WHERE email = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $test_email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 1) {
    $stmt->bind_result($id, $username, $db_email, $hashed_password, $is_admin);
    $stmt->fetch();
    
    echo "✅ User found!<br>";
    echo "ID: $id<br>";
    echo "Username: $username<br>";
    echo "Email: $db_email<br>";
    echo "Is Admin: " . ($is_admin ? 'Yes' : 'No') . "<br>";
    
    // Test password
    if (password_verify($test_password, $hashed_password)) {
        echo "✅ Password correct!<br>";
        
        // Set session manually
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $id;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $db_email;
        $_SESSION['is_admin'] = $is_admin;
        
        echo "✅ Session set successfully!<br>";
        echo "<pre>Session: ";
        print_r($_SESSION);
        echo "</pre>";
        
        echo "<h3>Test Redirect:</h3>";
        echo "<a href='test_redirect.php'>Test if session persists after redirect</a>";
        
    } else {
        echo "❌ Password incorrect!<br>";
        echo "Entered: $test_password<br>";
        echo "Stored hash: $hashed_password<br>";
        
        // Test what the hash should be
        $correct_hash = password_hash($test_password, PASSWORD_DEFAULT);
        echo "Should be: $correct_hash<br>";
    }
} else {
    echo "❌ User not found!<br>";
}

$stmt->close();
$mysqli->close();
?>