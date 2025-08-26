<?php
// Start output buffering at the very top of the script
ob_start();
require_once '../includes/config.php';

// Check if the user is an admin, otherwise redirect
if (!is_logged_in() || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit;
}

// Initialize form variables and error array
$name = '';
$description = '';
$price = '';
$image_url = '';
$category_id = null;
$stock_quantity = '';
$errors = [];

// Fetch categories for the dropdown menu
$categories = [];
$sql_categories = "SELECT * FROM categories ORDER BY name";
$result_categories = $mysqli->query($sql_categories);
if ($result_categories) {
    $categories = $result_categories->fetch_all(MYSQLI_ASSOC);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and trim input values
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $image_url = trim($_POST['image_url']);
    $category_id = trim($_POST['category_id']);
    $stock_quantity = trim($_POST['stock_quantity']);

    // Validation checks
    if (empty($name)) {
        $errors['name'] = 'Product name is required.';
    }
    if (empty($price) || !is_numeric($price) || $price < 0) {
        $errors['price'] = 'A valid price is required.';
    }
    if (empty($stock_quantity) || !is_numeric($stock_quantity) || $stock_quantity < 0) {
        $errors['stock_quantity'] = 'A valid stock quantity is required.';
    }
    
    // Convert to appropriate data types
    $price = floatval($price);
    $category_id = empty($category_id) ? null : intval($category_id);
    $stock_quantity = intval($stock_quantity);

    // If there are no validation errors, proceed with the database insert
    if (empty($errors)) {
        $sql_insert = "INSERT INTO products (name, description, price, image_url, category_id, stock_quantity)
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $mysqli->prepare($sql_insert);
        $stmt_insert->bind_param("ssdsii", $name, $description, $price, $image_url, $category_id, $stock_quantity);

        if ($stmt_insert->execute()) {
            // End output buffering and redirect on success
            ob_end_clean();
            header("Location: products.php?success=1");
            exit;
        } else {
            $errors['database'] = "Error adding product: " . $mysqli->error;
        }
        $stmt_insert->close();
    }
}

// Include the header now that all backend logic and potential redirects are complete
require_once '../includes/header.php';
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
                           placeholder="https://example.com/image.jpg">
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
// Flush the buffer to send output to the browser
ob_end_flush();
$mysqli->close();
?>