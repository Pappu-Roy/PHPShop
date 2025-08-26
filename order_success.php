<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

if (!is_logged_in() || !isset($_SESSION['order_id'])) {
    header("location: index.php");
    exit;
}

$order_id = $_SESSION['order_id'];
unset($_SESSION['order_id']);

$order = [];
$order_items = [];

$sql = "SELECT o.*, oi.quantity, oi.price AS item_price, p.name, p.image_url
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.id = ? AND o.user_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $order_id, $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    if (empty($order)) {
        $order = $row;
    }
    $order_items[] = $row;
}

$stmt->close();

if (empty($order)) {
    header("location: index.php");
    exit;
}
?>

<div class="max-w-4xl mx-auto">
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-8 text-center">
        <i class="fas fa-check-circle text-3xl mb-2"></i>
        <h1 class="text-2xl font-bold">Order Confirmed!</h1>
        <p class="text-lg">Thank you for your purchase.</p>
        <p class="text-sm">Order #<?php echo $order['id']; ?></p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Order Details</h2>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Order Number:</span>
                    <span class="font-semibold">#<?php echo $order['id']; ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Order Date:</span>
                    <span><?php echo date('F j, Y', strtotime($order['created_at'])); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Order Status:</span>
                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full uppercase font-semibold">
                        <?php echo $order['status']; ?>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Amount:</span>
                    <span class="text-lg font-bold text-blue-600"><?php echo format_price($order['total_amount']); ?></span>
                </div>
            </div>
            
            <h3 class="text-lg font-semibold text-gray-700 mt-6 mb-3">Items Ordered</h3>
            <div class="space-y-3">
                <?php foreach ($order_items as $item): ?>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <div class="flex items-center">
                            <img src="<?php echo htmlspecialchars($item['image_url'] ?: 'https://via.placeholder.com/60x60'); ?>"
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 class="w-12 h-12 object-cover rounded-md mr-3">
                            <div>
                                <p class="font-medium"><?php echo htmlspecialchars($item['name']); ?></p>
                                <p class="text-sm text-gray-500">Qty: <?php echo $item['quantity']; ?></p>
                            </div>
                        </div>
                        <span class="font-semibold"><?php echo format_price($item['item_price'] * $item['quantity']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div>
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">What's Next?</h2>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="bg-blue-100 p-2 rounded-full mr-3 mt-1">
                            <i class="fas fa-envelope text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold">Order Confirmation</h3>
                            <p class="text-sm text-gray-600">We've sent a confirmation email with your order details.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="bg-blue-100 p-2 rounded-full mr-3 mt-1">
                            <i class="fas fa-shipping-fast text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold">Shipping Updates</h3>
                            <p class="text-sm text-gray-600">You'll receive tracking information once your order ships.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="bg-blue-100 p-2 rounded-full mr-3 mt-1">
                            <i class="fas fa-history text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold">Order History</h3>
                            <p class="text-sm text-gray-600">View all your orders in your account dashboard.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-blue-800 mb-3">Need Help?</h2>
                <p class="text-blue-600 text-sm mb-4">If you have any questions about your order, please contact our customer support team.</p>
                <div class="space-y-2 text-sm">
                    <p class="flex items-center">
                        <i class="fas fa-envelope text-blue-500 mr-2"></i>
                        <span>support@phpshop.com</span>
                    </p>
                    <p class="flex items-center">
                        <i class="fas fa-phone text-blue-500 mr-2"></i>
                        <span>+1 (555) 123-4567</span>
                    </p>
                </div>
                <a href="contact.php" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 transition">
                    Contact Support
                </a>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-8">
        <a href="products.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold mr-4">
            Continue Shopping
        </a>
        <a href="orders.php" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
            View Order History
        </a>
    </div>
</div>

<?php
require_once 'includes/footer.php';
$mysqli->close();
?>