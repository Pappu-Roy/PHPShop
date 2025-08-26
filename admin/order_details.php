<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/header.php';

// Check if user is an admin
if (!is_logged_in() || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit;
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($order_id === 0) {
    header("Location: orders.php");
    exit;
}

$message = '';
// Handle order status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['new_status'];
    $valid_statuses = ['pending', 'processing', 'shipped', 'delivered'];
    
    if (in_array($new_status, $valid_statuses)) {
        $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt_update = $mysqli->prepare($update_sql);
        $stmt_update->bind_param("si", $new_status, $order_id);
        
        if ($stmt_update->execute()) {
            $message = "✅ Order status updated to '{$new_status}' successfully!";
        } else {
            $message = "❌ Failed to update order status.";
        }
        $stmt_update->close();
    } else {
        $message = "❌ Invalid status provided.";
    }
}

// Fetch order details
$order = null;
$order_items = [];
$sql = "SELECT o.*, u.username, u.email, u.created_at AS user_since,
               oi.quantity, oi.price AS item_price, p.name AS product_name, p.image_url
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($order === null) {
            $order = $row;
        }
        $order_items[] = $row;
    }
} else {
    // If order not found, redirect
    header("Location: orders.php");
    exit;
}
$stmt->close();
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Order #<?php echo $order['id']; ?> Details</h1>
        <a href="orders.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Orders
        </a>
    </div>

    <?php if ($message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Order Summary</h2>
            <div class="space-y-2 border-b border-gray-200 pb-4 mb-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Customer:</span>
                    <span class="font-medium"><?php echo htmlspecialchars($order['username']); ?> (<?php echo htmlspecialchars($order['email']); ?>)</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Order Date:</span>
                    <span class="font-medium"><?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Current Status:</span>
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

        <div class="lg:col-span-3 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Items in Order</h2>
            <div class="divide-y divide-gray-200">
                <?php foreach ($order_items as $item): ?>
                    <div class="flex items-center space-x-4 py-4">
                        <div class="flex-shrink-0">
                            <img src="<?php echo htmlspecialchars($item['image_url'] ?: 'https://via.placeholder.com/80x80'); ?>"
                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                 class="w-20 h-20 object-cover rounded-md">
                        </div>
                        <div class="flex-grow">
                            <h3 class="font-semibold text-lg text-gray-900"><?php echo htmlspecialchars($item['product_name']); ?></h3>
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

        <div class="lg:col-span-3 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Admin Actions</h2>
            <form method="post" class="space-y-4">
                <div>
                    <label for="new_status" class="block text-sm font-medium text-gray-700">Update Status</label>
                    <select id="new_status" name="new_status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                        <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                    </select>
                </div>
                <button type="submit" name="update_status" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    Update Order Status
                </button>
            </form>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
$mysqli->close();
?>