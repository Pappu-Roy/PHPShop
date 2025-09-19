<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

// Check if user is admin
if (!is_logged_in() || !$_SESSION['is_admin']) {
    header("location: ../index.php");
    exit;
}

$message = '';
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    
    // First, check if the product is in any orders
    $check_orders = $mysqli->prepare("SELECT id FROM order_items WHERE product_id = ? LIMIT 1");
    $check_orders->bind_param("i", $product_id);
    $check_orders->execute();
    $check_orders->store_result();
    
    if ($check_orders->num_rows > 0) {
        $message = "❌ Cannot delete product. It is part of a previous order.";
    } else {
        // Now delete from cart
        $delete_cart = $mysqli->prepare("DELETE FROM cart WHERE product_id = ?");
        $delete_cart->bind_param("i", $product_id);
        $delete_cart->execute();
        $delete_cart->close();
        
        // Delete the product
        $delete_product = $mysqli->prepare("DELETE FROM products WHERE id = ?");
        $delete_product->bind_param("i", $product_id);
        
        if ($delete_product->execute()) {
            $message = "Product deleted successfully!";
        } else {
            $message = "Error deleting product: " . $mysqli->error;
        }
        $delete_product->close();
    }
    $check_orders->close();
}

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "Product added/updated successfully!";
}

$products = [];
$sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC";
$result = $mysqli->query($sql);
if ($result) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Product Management</h1>
        <a href="add_product.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i> Add New Product
        </a>
    </div>
    
    <?php if ($message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">All Products (<?php echo count($products); ?>)</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="<?php echo htmlspecialchars($product['image_url'] ?: 'https://via.placeholder.com/50x50'); ?>"
                                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                                             class="w-10 h-10 object-cover rounded-md mr-3">
                                        <div>
                                            <div class="font-medium text-gray-900"><?php echo htmlspecialchars($product['name']); ?></div>
                                            <div class="text-sm text-gray-500">ID: <?php echo $product['id']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                        <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo format_price($product['price']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo $product['stock_quantity']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        <?php echo $product['stock_quantity'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo $product['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="products.php?action=delete&id=<?php echo $product['id']; ?>"
                                       class="text-red-600 hover:text-red-900"
                                       onclick="return confirm('Are you sure you want to delete this product?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No products found. <a href="add_product.php" class="text-blue-600 hover:text-blue-800">Add your first product</a>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
$mysqli->close();
?>
