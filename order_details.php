<?php
// Start session and include necessary files
session_start();
require_once 'includes/config.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

// Get the order ID from the URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// If no valid order ID is provided, redirect to the orders list
if ($order_id === 0) {
    header("Location: orders.php");
    exit;
}

$user_id = $_SESSION['id'];
$order = null;
$order_items = [];

// Fetch order details and items from the database
$sql = "SELECT o.*, oi.quantity, oi.price AS item_price, p.name, p.image_url
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.id = ? AND o.user_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Process the fetched data
while ($row = $result->fetch_assoc()) {
    if ($order === null) {
        $order = $row;
    }
    $order_items[] = $row;
}
$stmt->close();

// If the order is not found or does not belong to the user, redirect
if ($order === null) {
    header("Location: orders.php");
    exit;
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Order Details #<?php echo $order['id']; ?></h1>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Order Summary</h2>
        <div class="space-y-2 border-b border-gray-200 pb-4 mb-4">
            <div class="flex justify-between">
                <span class="text-gray-600">Order Date:</span>
                <span class="font-medium"><?php echo date('F j, Y', strtotime($order['created_at'])); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Order Status:</span>
                <span class="px-3 py-1 text-xs font-semibold rounded-full
                    <?php echo $order['status'] == 'delivered' ? 'bg-green-100 text-green-800' :
                           ($order['status'] == 'shipped' ? 'bg-blue-100 text-blue-800' :
                           ($order['status'] == 'processing' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')); ?>">
                    <?php echo ucfirst($order['status']); ?>
                </span>
            </div>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-lg font-bold text-gray-900">Total Amount:</span>
            <span class="text-2xl font-bold text-blue-600"><?php echo format_price($order['total_amount']); ?></span>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden p-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Items in Your Order</h2>
        <div class="divide-y divide-gray-200">
            <?php foreach ($order_items as $item): ?>
                <div class="flex items-center space-x-4 py-4">
                    <div class="flex-shrink-0">
                        <img src="<?php echo htmlspecialchars($item['image_url'] ?: 'https://via.placeholder.com/80x80'); ?>"
                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                             class="w-20 h-20 object-cover rounded-md">
                    </div>
                    <div class="flex-grow">
                        <h3 class="font-semibold text-lg text-gray-900"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="text-sm text-gray-500">Quantity: <?php echo $item['quantity']; ?></p>
                        <p class="text-blue-600 font-bold"><?php echo format_price($item['item_price']); ?> each</p>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold text-gray-900"><?php echo format_price($item['item_price'] * $item['quantity']); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="mt-8 text-center">
        <a href="orders.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
            <i class="fas fa-arrow-left mr-2"></i> Back to Orders List
        </a>
    </div>
</div>

<?php
require_once 'includes/footer.php';
$mysqli->close();
?>