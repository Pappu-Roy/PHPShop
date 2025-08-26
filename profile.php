<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!is_logged_in()) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['id'];
$success_message = '';
$error_message = '';

// Get user details
$sql = "SELECT username, email, created_at FROM users WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $new_email = trim($_POST['email']);
    
    // Validate email
    if (filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $update_sql = "UPDATE users SET email = ? WHERE id = ?";
        $update_stmt = $mysqli->prepare($update_sql);
        $update_stmt->bind_param("si", $new_email, $user_id);
        
        if ($update_stmt->execute()) {
            $_SESSION['email'] = $new_email;
            $user['email'] = $new_email;
            $success_message = "Profile updated successfully!";
        } else {
            $error_message = "Error updating profile. Please try again.";
        }
        $update_stmt->close();
    } else {
        $error_message = "Please enter a valid email address.";
    }
}

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Get current password hash
    $password_sql = "SELECT password FROM users WHERE id = ?";
    $password_stmt = $mysqli->prepare($password_sql);
    $password_stmt->bind_param("i", $user_id);
    $password_stmt->execute();
    $password_stmt->bind_result($current_hashed_password);
    $password_stmt->fetch();
    $password_stmt->close();
    
    // Verify current password
    if (password_verify($current_password, $current_hashed_password)) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 6) {
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_password_sql = "UPDATE users SET password = ? WHERE id = ?";
                $update_password_stmt = $mysqli->prepare($update_password_sql);
                $update_password_stmt->bind_param("si", $new_hashed_password, $user_id);
                
                if ($update_password_stmt->execute()) {
                    $success_message = "Password changed successfully!";
                } else {
                    $error_message = "Error changing password. Please try again.";
                }
                $update_password_stmt->close();
            } else {
                $error_message = "New password must be at least 6 characters long.";
            }
        } else {
            $error_message = "New passwords do not match.";
        }
    } else {
        $error_message = "Current password is incorrect.";
    }
}

// Get user orders
$orders = [];
$orders_sql = "SELECT o.id, o.total_amount, o.status, o.created_at 
               FROM orders o 
               WHERE o.user_id = ? 
               ORDER BY o.created_at DESC 
               LIMIT 5";
$orders_stmt = $mysqli->prepare($orders_sql);
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();
$orders = $orders_result->fetch_all(MYSQLI_ASSOC);
$orders_stmt->close();
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">My Profile</h1>
    
    <?php if ($success_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-6">Personal Information</h2>
                
                <form method="post" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" disabled>
                        <p class="text-xs text-gray-500 mt-1">Username cannot be changed</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Member Since</label>
                        <input type="text" value="<?php echo date('F j, Y', strtotime($user['created_at'])); ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" disabled>
                    </div>
                    
                    <button type="submit" name="update_profile" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        Update Profile
                    </button>
                </form>
            </div>
            
            <!-- Change Password -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-6">Change Password</h2>
                
                <form method="post" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input type="password" name="current_password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" name="new_password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" name="confirm_password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <button type="submit" name="change_password" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        Change Password
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Account Overview -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-6">Account Overview</h2>
                
                <div class="space-y-6">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-shopping-bag text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="font-semibold">Orders</h3>
                        <p class="text-2xl font-bold text-blue-600"><?php echo count($orders); ?></p>
                        <a href="orders.php" class="text-blue-600 hover:text-blue-800 text-sm">View order history</a>
                    </div>
                    
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-heart text-green-600 text-2xl"></i>
                        </div>
                        <h3 class="font-semibold">Wishlist</h3>
                        <p class="text-2xl font-bold text-green-600">0</p>
                        <a href="#" class="text-green-600 hover:text-green-800 text-sm">View your wishlist</a>
                    </div>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-6">Recent Orders</h2>
                
                <?php if (count($orders) > 0): ?>
                    <div class="space-y-4">
                        <?php foreach ($orders as $order): ?>
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-semibold">Order #<?php echo $order['id']; ?></span>
                                    <span class="text-sm <?php echo $order['status'] == 'delivered' ? 'text-green-600' : 
                                                          ($order['status'] == 'shipped' ? 'text-blue-600' : 
                                                          ($order['status'] == 'processing' ? 'text-yellow-600' : 'text-gray-600')); ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                                <p class="text-gray-600 text-sm">Total: <?php echo format_price($order['total_amount']); ?></p>
                                <p class="text-gray-500 text-xs"><?php echo date('M j, Y', strtotime($order['created_at'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="orders.php" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                            View All Orders →
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 text-center">No orders yet.</p>
                    <div class="text-center mt-4">
                        <a href="products.php" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                            Start Shopping →
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
$mysqli->close();
?>