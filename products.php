<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Get search and filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category_id = isset($_GET['category']) ? $_GET['category'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';

// Build the SQL query with filters
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.stock_quantity > 0";

$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "ss";
}

if (!empty($category_id) && is_numeric($category_id)) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
    $types .= "i";
}

if (!empty($min_price) && is_numeric($min_price)) {
    $sql .= " AND p.price >= ?";
    $params[] = $min_price;
    $types .= "d";
}

if (!empty($max_price) && is_numeric($max_price)) {
    $sql .= " AND p.price <= ?";
    $params[] = $max_price;
    $types .= "d";
}

// Add sorting
$sort_options = [
    'name' => 'p.name ASC',
    'name_desc' => 'p.name DESC',
    'price' => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'newest' => 'p.created_at DESC'
];

$sql .= " ORDER BY " . ($sort_options[$sort] ?? 'p.name ASC');

// Prepare and execute the query
$stmt = $mysqli->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

// Get categories for filter dropdown
$categories = [];
$cat_result = $mysqli->query("SELECT * FROM categories ORDER BY name");
if ($cat_result) {
    $categories = $cat_result->fetch_all(MYSQLI_ASSOC);
}
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Our Products</h1>
    <p class="text-gray-600">Discover our amazing collection of products</p>
</div>

<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                   placeholder="Search products..." 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Categories</option>
                <?php foreach($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo ($category_id == $category['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Min Price</label>
            <input type="number" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>" 
                   placeholder="Min" min="0" step="0.01"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Max Price</label>
            <input type="number" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>" 
                   placeholder="Max" min="0" step="0.01"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
            <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="name" <?php echo ($sort == 'name') ? 'selected' : ''; ?>>Name A-Z</option>
                <option value="name_desc" <?php echo ($sort == 'name_desc') ? 'selected' : ''; ?>>Name Z-A</option>
                <option value="price" <?php echo ($sort == 'price') ? 'selected' : ''; ?>>Price Low-High</option>
                <option value="price_desc" <?php echo ($sort == 'price_desc') ? 'selected' : ''; ?>>Price High-Low</option>
                <option value="newest" <?php echo ($sort == 'newest') ? 'selected' : ''; ?>>Newest First</option>
            </select>
        </div>
        
        <div class="md:col-span-5 flex space-x-4 pt-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                Apply Filters
            </button>
            <a href="products.php" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition">
                Clear Filters
            </a>
        </div>
    </form>
</div>

<div class="bg-gray-200 py-12 mb-16 rounded-lg">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php if(count($products) > 0): ?>
                <?php foreach($products as $product): ?>
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="block">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition flex flex-col h-full">
                            <div class="h-48 flex items-center justify-center bg-gray-100">
                                <img src="<?php echo $product['image_url'] ?: 'https://via.placeholder.com/300x200'; ?>"
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     class="max-h-full max-w-full object-contain">
                            </div>
                            <div class="p-4 flex-grow flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-semibold text-lg"><?php echo htmlspecialchars($product['name']); ?></h3>
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                            <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2"><?php echo htmlspecialchars($product['description']); ?></p>
                                </div>
                                <div>
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-blue-600 font-bold"><?php echo format_price($product['price']); ?></span>
                                        <span class="text-sm text-gray-500"><?php echo $product['stock_quantity']; ?> in stock</span>
                                    </div>
                                    <?php if($product['stock_quantity'] > 0): ?>
                                        <form action="add_to_cart.php" method="post" onclick="event.stopPropagation();" class="mt-auto">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-3 rounded hover:bg-blue-700 transition">
                                                <i class="fas fa-cart-plus"></i> Add to Cart
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600">No products found</h3>
                    <p class="text-gray-500">Try adjusting your search filters</p>
                    <a href="products.php" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Clear Filters
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
$mysqli->close();
?>