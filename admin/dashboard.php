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
    
    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-6">Recent Orders</h2>
        
        <?php
        $sql = "SELECT o.id, o.total_amount, o.status, o.created_at, u.username 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC 
                LIMIT 5";
        $result = $mysqli->query($sql);
        
        if ($result && $result->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($order = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">#<?php echo $order['id']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($order['username']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo format_price($order['total_amount']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        <?php echo $order['status'] == 'delivered' ? 'bg-green-100 text-green-800' : 
                                               ($order['status'] == 'shipped' ? 'bg-blue-100 text-blue-800' : 
                                               ($order['status'] == 'processing' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')); ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-500">No orders found.</p>
        <?php endif; ?>
        
        <div class="mt-6 text-center">
            <a href="orders.php" class="text-blue-600 hover:text-blue-800 font-semibold">
                View All Orders →
            </a>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
$mysqli->close();
?>