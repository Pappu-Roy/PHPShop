<?php
session_start();
echo "<h2>Redirect Test</h2>";

echo "Session data after redirect:<br>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    echo "✅ SUCCESS: Session persisted after redirect!<br>";
    echo "User: " . ($_SESSION['username'] ?? 'Unknown') . "<br>";
    echo "Email: " . ($_SESSION['email'] ?? 'Unknown') . "<br>";
    echo "Is Admin: " . (isset($_SESSION['is_admin']) ? ($_SESSION['is_admin'] ? 'Yes' : 'No') : 'Unknown') . "<br>";
    
    echo "<br><a href='logout.php'>Logout</a>";
} else {
    echo "❌ FAILED: Session did not persist after redirect!<br>";
    echo "<a href='test_login.php'>Go back to login test</a>";
}
?>