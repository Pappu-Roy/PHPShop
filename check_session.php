<?php
session_start();
echo "<h2>Session Check</h2>";

echo "Session data:<br>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

if (isset($_SESSION['manual_test'])) {
    echo "✅ SUCCESS: Session is working!";
} else {
    echo "❌ FAILED: Session not working!";
    
    // Check session configuration
    echo "<br><br>Session configuration:";
    echo "<br>Session ID: " . session_id();
    echo "<br>Session Path: " . session_save_path();
    echo "<br>Session Status: " . session_status();
}
?>