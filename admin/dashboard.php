<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

// Check if user is admin
if (!is_logged_in() || !$_SESSION['is_admin']) {
    header("location: ../index.php");
    exit;
}

// Get stats for dashboard
$users_count = $products_count = $orders_count = 0;

$sql = "SELECT COUNT(*) as count FROM users";
$result = $mysqli->query($sql);
if ($result) {
    $users_count = $result->fetch_assoc()['count'];
}

$sql = "SELECT COUNT(*) as count FROM products";
$result = $mysqli->query($sql);
if ($result) {
    $products_count = $result->fetch_assoc()['count'];
}

$sql = "SELECT COUNT(*) as count FROM orders";
$result = $mysqli->query($sql);
if ($result) {
    $orders_count = $result->fetch_assoc()['count'];
}
?>

<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Admin Dashboard</h1>
    
    <!-- Welcome message -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <p class="text-blue-800">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! (<?php echo htmlspecialchars($_SESSION['email']); ?>)</p>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-full mr-4">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $users_count; ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                    <i class="fas fa-box text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Products</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $products_count; ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="bg-purple-100 p-3 rounded-full mr-4">
                    <i class="fas fa-shopping-bag text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $orders_count; ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-700 mb-6">Quick Actions</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="products.php" class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center hover:bg-blue-100 transition">
                <i class="fas fa-plus-circle text-blue-600 text-2xl mb-2"></i>
                <p class="font-semibold text-blue-800">Add Product</p>
            </a>
            
            <a href="products.php" class="bg-green-50 border border-green-200 rounded-lg p-4 text-center hover:bg-green-100 transition">
                <i class="fas fa-edit text-green-600 text-2xl mb-2"></i>
                <p class="font-semibold text-green-800">Manage Products</p>
            </a>
            
            <a href="users.php" class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center hover:bg-purple-100 transition">
                <i class="fas fa-users text-purple-600 text-2xl mb-2"></i>
                <p class="font-semibold text-purple-800">Manage Users</p>
            </a>
            
            <a href="orders.php" class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center hover:bg-orange-100 transition">
                <i class="fas fa-shopping-cart text-orange-600 text-2xl mb-2"></i>
                <p class="font-semibold text-orange-800">View Orders</p>
            </a>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
$mysqli->close();
?>