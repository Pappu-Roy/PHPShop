<?php
require_once 'includes/config.php';

echo "<h2>Reset Admin Password</h2>";

$email = 'admin@phpshop.com';
$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update admin password
$sql = "UPDATE users SET password = ? WHERE email = ? AND is_admin = 1";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ss", $hashed_password, $email);

if ($stmt->execute()) {
    echo "<div style='color: green; padding: 10px; border: 2px solid green; border-radius: 5px;'>";
    echo "✅ Admin password reset successfully!<br>";
    echo "📧 Email: $email<br>";
    echo "🔑 New Password: $new_password<br>";
    echo "<a href='login.php' style='color: blue; text-decoration: underline;'>Login Now</a>";
    echo "</div>";
} else {
    echo "<div style='color: red; padding: 10px; border: 2px solid red; border-radius: 5px;'>";
    echo "❌ Error resetting password: " . $mysqli->error;
    echo "</div>";
}

$stmt->close();
$mysqli->close();
?>