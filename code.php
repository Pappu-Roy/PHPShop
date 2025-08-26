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
            $message = "✅ Product deleted successfully!";
        } else {
            $message = "❌ Error deleting product: " . $mysqli->error;
        }
        $delete_product->close();
    }
    $check_orders->close();
}

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "✅ Product added/updated successfully!";
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





**1.2. Add Product Page (`admin/add_product.php`)**

This page contains the form for an admin to add a new product to the database.

```php
<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

// Check if user is admin
if (!is_logged_in() || !$_SESSION['is_admin']) {
    header("location: ../index.php");
    exit;
}

// Get categories for dropdown
$categories = [];
$sql = "SELECT * FROM categories ORDER BY name";
$result = $mysqli->query($sql);
if ($result) {
    $categories = $result->fetch_all(MYSQLI_ASSOC);
}

// Initialize variables
$name = $description = $price = $image_url = $category_id = $stock_quantity = '';
$errors = [];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    if (empty(trim($_POST['name']))) {
        $errors['name'] = 'Product name is required';
    } else {
        $name = trim($_POST['name']);
    }
    
    $description = trim($_POST['description']);
    
    if (empty(trim($_POST['price'])) || !is_numeric($_POST['price'])) {
        $errors['price'] = 'Valid price is required';
    } else {
        $price = floatval($_POST['price']);
    }
    
    $image_url = trim($_POST['image_url']);
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    
    if (trim($_POST['stock_quantity']) === '' || !is_numeric($_POST['stock_quantity'])) {
        $errors['stock_quantity'] = 'Valid stock quantity is required';
    } else {
        $stock_quantity = intval($_POST['stock_quantity']);
    }
    
    if (empty($errors)) {
        $sql = "INSERT INTO products (name, description, price, image_url, category_id, stock_quantity)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssdsii", $name, $description, $price, $image_url, $category_id, $stock_quantity);
        
        if ($stmt->execute()) {
            header("location: products.php?success=1");
            exit;
        } else {
            $errors['database'] = "Error adding product: " . $mysqli->error;
        }
        $stmt->close();
    }
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Add New Product</h1>
    
    <?php if (!empty($errors['database'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <?php echo $errors['database']; ?>
        </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="post" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 <?php echo isset($errors['name']) ? 'border-red-500' : ''; ?>">
                <?php if (isset($errors['name'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?php echo $errors['name']; ?></p>
                <?php endif; ?>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                    <input type="number" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($price); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 <?php echo isset($errors['price']) ? 'border-red-500' : ''; ?>">
                    <?php if (isset($errors['price'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo $errors['price']; ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity *</label>
                    <input type="number" name="stock_quantity" min="0" value="<?php echo htmlspecialchars($stock_quantity); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 <?php echo isset($errors['stock_quantity']) ? 'border-red-500' : ''; ?>">
                    <?php if (isset($errors['stock_quantity'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo $errors['stock_quantity']; ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
                    <input type="url" name="image_url" value="<?php echo htmlspecialchars($image_url); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="[https://example.com/image.jpg](https://example.com/image.jpg)">
                </div>
            </div>
            
            <div class="flex space-x-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                    Add Product
                </button>
                <a href="products.php" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php
require_once '../includes/footer.php';
$mysqli->close();
?>



**1.3. Edit Product Page (`admin/edit_product.php`)**

This page allows an admin to update an existing product. It prefills the form with the product's current data.

```php
<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

// Check if user is admin
if (!is_logged_in() || !$_SESSION['is_admin']) {
    header("location: ../index.php");
    exit;
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$product_id) {
    header("location: products.php");
    exit;
}

$categories = [];
$sql = "SELECT * FROM categories ORDER BY name";
$result = $mysqli->query($sql);
if ($result) {
    $categories = $result->fetch_all(MYSQLI_ASSOC);
}

$product = get_product($product_id);
if (!$product) {
    header("location: products.php");
    exit;
}

$name = $product['name'];
$description = $product['description'];
$price = $product['price'];
$image_url = $product['image_url'];
$category_id = $product['category_id'];
$stock_quantity = $product['stock_quantity'];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $image_url = trim($_POST['image_url']);
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $stock_quantity = intval($_POST['stock_quantity']);

    if (empty($name)) {
        $errors['name'] = 'Product name is required.';
    }
    if (!is_numeric($price) || $price < 0) {
        $errors['price'] = 'Valid price is required.';
    }
    if (!is_numeric($stock_quantity) || $stock_quantity < 0) {
        $errors['stock_quantity'] = 'Valid stock quantity is required.';
    }

    if (empty($errors)) {
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, image_url = ?, category_id = ?, stock_quantity = ? WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssdsiii", $name, $description, $price, $image_url, $category_id, $stock_quantity, $product_id);
        
        if ($stmt->execute()) {
            header("location: products.php?success=1");
            exit;
        } else {
            $errors['database'] = "Error updating product: " . $mysqli->error;
        }
        $stmt->close();
    }
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Edit Product</h1>
    
    <?php if (!empty($errors['database'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <?php echo $errors['database']; ?>
        </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="post" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 <?php echo isset($errors['name']) ? 'border-red-500' : ''; ?>">
                <?php if (isset($errors['name'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?php echo $errors['name']; ?></p>
                <?php endif; ?>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                    <input type="number" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($price); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 <?php echo isset($errors['price']) ? 'border-red-500' : ''; ?>">
                    <?php if (isset($errors['price'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo $errors['price']; ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity *</label>
                    <input type="number" name="stock_quantity" min="0" value="<?php echo htmlspecialchars($stock_quantity); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 <?php echo isset($errors['stock_quantity']) ? 'border-red-500' : ''; ?>">
                    <?php if (isset($errors['stock_quantity'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo $errors['stock_quantity']; ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
                    <input type="url" name="image_url" value="<?php echo htmlspecialchars($image_url); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="https://example.com/image.jpg">
                </div>
            </div>
            
            <?php if ($image_url): ?>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Image</label>
                    <img src="<?php echo htmlspecialchars($image_url); ?>" alt="Product image" class="w-32 h-32 object-cover rounded-md border">
                </div>
            <?php endif; ?>

            <div class="flex space-x-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                    Update Product
                </button>
                <a href="products.php" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php
require_once '../includes/footer.php';
$mysqli->close();
?>





**1.4. Admin Orders Page (`admin/orders.php`)**

This page lists all orders for the admin to manage.

```php
<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

if (!is_logged_in() || !$_SESSION['is_admin']) {
    header("location: ../index.php");
    exit;
}

$orders = [];
$sql = "SELECT o.id, o.total_amount, o.status, o.created_at, u.username
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC";
$result = $mysqli->query($sql);
if ($result) {
    $orders = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">All Orders</h1>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Recent Orders (<?php echo count($orders); ?>)</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $order): ?>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="order_details.php?id=<?php echo $order['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No orders found.
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




**1.5. Admin Users Page (`admin/users.php`)**

This page lists all registered users for the admin.

```php
<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

if (!is_logged_in() || !$_SESSION['is_admin']) {
    header("location: ../index.php");
    exit;
}

$users = [];
$sql = "SELECT id, username, email, is_admin, created_at FROM users ORDER BY created_at DESC";
$result = $mysqli->query($sql);
if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">User Management</h1>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">All Users (<?php echo count($users); ?>)</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo $user['id']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($user['username']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $user['is_admin'] ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'; ?>">
                                        <?php echo $user['is_admin'] ? 'Admin' : 'User'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No users found.
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
<br>




#### 2. User Flow Pages

**2.1. Checkout Page (`checkout.php`)**

This page allows the user to review their order and enter shipping/payment details.

```php
<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!is_logged_in()) {
    $_SESSION['redirect_url'] = 'checkout.php';
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['id'];
$errors = [];
$cart_items = [];
$total = 0;

$sql = "SELECT c.quantity, p.price, p.stock_quantity, p.name 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
    $cart_items[] = $row;
}
$stmt->close();

if (count($cart_items) === 0) {
    header("location: cart.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $required_fields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'zip_code'];
    foreach ($required_fields as $field) {
        if (empty(trim($_POST[$field]))) {
            $errors[$field] = ucwords(str_replace('_', ' ', $field)) . " is required";
        }
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address";
    }

    if (empty($errors)) {
        $mysqli->begin_transaction();
        
        try {
            $order_sql = "INSERT INTO orders (user_id, total_amount) VALUES (?, ?)";
            $order_stmt = $mysqli->prepare($order_sql);
            $final_total = $total + ($total > 50 ? 0 : 5.99) + ($total * 0.08);
            $order_stmt->bind_param("id", $user_id, $final_total);
            $order_stmt->execute();
            $order_id = $mysqli->insert_id;
            $order_stmt->close();
            
            $cart_sql = "SELECT c.product_id, c.quantity, p.price, p.stock_quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
            $cart_stmt = $mysqli->prepare($cart_sql);
            $cart_stmt->bind_param("i", $user_id);
            $cart_stmt->execute();
            $cart_result = $cart_stmt->get_result();
            
            while ($item = $cart_result->fetch_assoc()) {
                if ($item['quantity'] > $item['stock_quantity']) {
                    throw new Exception("Not enough stock for product ID: " . $item['product_id']);
                }
                
                $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
                $item_stmt = $mysqli->prepare($item_sql);
                $item_stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                $item_stmt->execute();
                $item_stmt->close();
                
                $update_sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?";
                $update_stmt = $mysqli->prepare($update_sql);
                $update_stmt->bind_param("ii", $item['quantity'], $item['product_id']);
                $update_stmt->execute();
                $update_stmt->close();
            }
            
            $cart_stmt->close();
            
            $clear_sql = "DELETE FROM cart WHERE user_id = ?";
            $clear_stmt = $mysqli->prepare($clear_sql);
            $clear_stmt->bind_param("i", $user_id);
            $clear_stmt->execute();
            $clear_stmt->close();
            
            $mysqli->commit();
            $_SESSION['order_id'] = $order_id;
            
            header("location: order_success.php");
            exit;
        } catch (Exception $e) {
            $mysqli->rollback();
            $errors['general'] = "Error processing your order: " . $e->getMessage();
        }
    }
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>
    
    <?php if (isset($errors['general'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <?php echo $errors['general']; ?>
        </div>
    <?php endif; ?>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-6">Shipping Information</h2>
                <form method="post" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input type="text" name="first_name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 <?php echo isset($errors['first_name']) ? 'border-red-500' : 'border-gray-300'; ?>">
                            <?php if (isset($errors['first_name'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?php echo $errors['first_name']; ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input type="text" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 <?php echo isset($errors['last_name']) ? 'border-red-500' : 'border-gray-300'; ?>">
                            <?php if (isset($errors['last_name'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?php echo $errors['last_name']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                               class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 <?php echo isset($errors['email']) ? 'border-red-500' : 'border-gray-300'; ?>">
                        <?php if (isset($errors['email'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?php echo $errors['email']; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                               class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 <?php echo isset($errors['phone']) ? 'border-red-500' : 'border-gray-300'; ?>">
                        <?php if (isset($errors['phone'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?php echo $errors['phone']; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                        <input type="text" name="address" value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>"
                               class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 <?php echo isset($errors['address']) ? 'border-red-500' : 'border-gray-300'; ?>">
                        <?php if (isset($errors['address'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?php echo $errors['address']; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                            <input type="text" name="city" value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 <?php echo isset($errors['city']) ? 'border-red-500' : 'border-gray-300'; ?>">
                            <?php if (isset($errors['city'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?php echo $errors['city']; ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ZIP Code *</label>
                            <input type="text" name="zip_code" value="<?php echo htmlspecialchars($_POST['zip_code'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 <?php echo isset($errors['zip_code']) ? 'border-red-500' : 'border-gray-300'; ?>">
                            <?php if (isset($errors['zip_code'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?php echo $errors['zip_code']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <h2 class="text-xl font-semibold text-gray-700 mt-8 mb-6">Payment Information</h2>
                    
                    <p class="text-gray-500 text-sm mb-4">Note: This is a placeholder for a real payment gateway. No real data will be processed.</p>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Card Number *</label>
                        <input type="text" name="card_number" placeholder="1234 5678 9012 3456"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500" value="1234 5678 9012 3456" disabled>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date *</label>
                            <input type="text" name="card_expiry" placeholder="MM/YY"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500" value="12/26" disabled>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CVV *</label>
                            <input type="text" name="card_cvv" placeholder="123"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500" value="123" disabled>
                        </div>
                    </div>
                    
                    <button type="submit"
                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition font-semibold mt-6">
                        Complete Order
                    </button>
                </form>
            </div>
        </div>
        
        <div>
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Order Summary</h2>
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span><?php echo format_price($total); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Shipping</span>
                        <span><?php echo $total > 50 ? 'Free' : format_price(5.99); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tax</span>
                        <span><?php echo format_price($total * 0.08); ?></span>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-4 mb-6">
                    <div class="flex justify-between text-lg font-semibold">
                        <span>Total</span>
                        <span>
                            <?php 
                            $shipping = $total > 50 ? 0 : 5.99;
                            $tax = $total * 0.08;
                            echo format_price($total + $shipping + $tax);
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
$mysqli->close();
?>



**2.2. Order Success Page (`order_success.php`)**

This page confirms the user's order after a successful checkout.

```php
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

---

### How to Use the Code

1.  **Create the files:** Make sure you create all the files and folders as specified in the "File Structure" section.
2.  **Copy-paste the code:** Paste the code for each file into its corresponding location.
3.  **Run the project:** Open your browser and navigate to `http://localhost/ecommerce/`.

This setup will provide a complete, end-to-end e-commerce experience for both users and the admin. Users can register, log in, browse products, add them to a cart, and complete a checkout, while the admin can manage products and orders.