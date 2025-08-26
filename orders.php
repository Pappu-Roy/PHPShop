<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!is_logged_in()) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['id'];

// Get user orders
$orders = [];
$sql = "SELECT o.*, COUNT(oi.id) as item_count 
        FROM orders o 
        JOIN order_items oi ON o.id = oi.order_id 
        WHERE o.user_id = ? 
        GROUP BY o.id 
        ORDER BY o.created_at DESC";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">My Orders</h1>
    
    <?php if (count($orders) > 0): ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-700">Order History (<?php echo count($orders); ?>)</h2>
            </div>
            
            <div class="divide-y divide-gray-200">
                <?php foreach ($orders as $order): ?>
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Order #<?php echo $order['id']; ?></h3>
                                <p class="text-sm text-gray-500">Placed on <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                            </div>
                            <div class="mt-2 md:mt-0">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                    <?php echo $order['status'] == 'delivered' ? 'bg-green-100 text-green-800' : 
                                           ($order['status'] == 'shipped' ? 'bg-blue-100 text-blue-800' : 
                                           ($order['status'] == 'processing' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')); ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-600"><strong>Total Amount:</strong></p>
                                <p class="text-lg font-semibold text-blue-600"><?php echo format_price($order['total_amount']); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600"><strong>Items:</strong></p>
                                <p class="text-gray-900"><?php echo $order['item_count']; ?> items</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600"><strong>Order ID:</strong></p>
                                <p class="text-gray-900">#<?php echo $order['id']; ?></p>
                            </div>
                        </div>
                        
                        <div class="flex space-x-4">
                            <a href="order_details.php?id=<?php echo $order['id']; ?>" class="text-blue-600 hover:text-blue-800 font-semibold">
                                View Order Details
                            </a>
                            <?php if ($order['status'] == 'delivered'): ?>
                                <a href="#" class="text-green-600 hover:text-green-800 font-semibold">
                                    Track Package
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-shopping-bag text-4xl text-gray-300 mb-4"></i>
            <h2 class="text-2xl font-semibold text-gray-600 mb-2">No orders yet</h2>
            <p class="text-gray-500 mb-6">Start shopping to see your orders here</p>
            <a href="products.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                Browse Products
            </a>
        </div>
    <?php endif; ?>
</div>

<?php
require_once 'includes/footer.php';
$mysqli->close();
?>