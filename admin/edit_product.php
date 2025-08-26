<?php
// Start output buffering at the very top of the script
ob_start();
require_once '../includes/config.php';

// Check if user is admin, otherwise redirect immediately
if (!is_logged_in() || !$_SESSION['is_admin']) {
    header("location: ../index.php");
    exit;
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$product_id) {
    header("location: products.php");
    exit;
}

// Fetch categories for the dropdown menu
$categories = [];
$sql_categories = "SELECT * FROM categories ORDER BY name";
$result_categories = $mysqli->query($sql_categories);
if ($result_categories) {
    $categories = $result_categories->fetch_all(MYSQLI_ASSOC);
}

// Fetch product data to pre-fill the form
$product = get_product($product_id);
if (!$product) {
    header("location: products.php");
    exit;
}

// Initialize form variables with existing product data
$name = $product['name'];
$description = $product['description'];
$price = $product['price'];
$image_url = $product['image_url'];
$category_id = $product['category_id'];
$stock_quantity = $product['stock_quantity'];
$errors = [];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and validate form data
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $image_url = trim($_POST['image_url']);
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $stock_quantity = intval($_POST['stock_quantity']);

    // Validation checks
    if (empty($name)) {
        $errors['name'] = 'Product name is required.';
    }
    if (!is_numeric($price) || $price < 0) {
        $errors['price'] = 'Valid price is required.';
    }
    if (!is_numeric($stock_quantity) || $stock_quantity < 0) {
        $errors['stock_quantity'] = 'Valid stock quantity is required.';
    }

    // If no validation errors, proceed with database update
    if (empty($errors)) {
        $sql_update = "UPDATE products SET name = ?, description = ?, price = ?, image_url = ?, category_id = ?, stock_quantity = ? WHERE id = ?";
        $stmt_update = $mysqli->prepare($sql_update);
        $stmt_update->bind_param("ssdsiii", $name, $description, $price, $image_url, $category_id, $stock_quantity, $product_id);
        
        if ($stmt_update->execute()) {
            // End buffering and redirect on success
            ob_end_clean();
            header("location: products.php?success=1");
            exit;
        } else {
            $errors['database'] = "Error updating product: " . $mysqli->error;
        }
        $stmt_update->close();
    }
}

// Now include the header, as all logic is complete
require_once '../includes/header.php';
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
// Flush the buffer to send output to the browser
ob_end_flush();
$mysqli->close();
?>