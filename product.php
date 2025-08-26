<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: products.php");
    exit;
}

$product_id = intval($_GET['id']);

// Get product details
$product = get_product($product_id);

if (!$product) {
    header("location: products.php");
    exit;
}

// Get related products
$related_products = [];
$related_sql = "SELECT * FROM products 
                WHERE category_id = ? AND id != ? AND stock_quantity > 0 
                LIMIT 4";
$stmt = $mysqli->prepare($related_sql);
$stmt->bind_param("ii", $product['category_id'], $product_id);
$stmt->execute();
$result = $stmt->get_result();
$related_products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="max-w-6xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="index.php" class="text-gray-700 hover:text-blue-600">Home</a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="products.php" class="text-gray-700 hover:text-blue-600">Products</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500"><?php echo htmlspecialchars($product['name']); ?></span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <!-- Product Image -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <img src="<?php echo $product['image_url'] ?: 'https://via.placeholder.com/500x500'; ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                 class="w-full h-96 object-contain rounded-lg">
        </div>
        
        <!-- Product Details -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-4">
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                    <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                </span>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div class="flex items-center mb-4">
                <div class="flex text-yellow-400 mr-2">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star"></i>
                    <?php endfor; ?>
                </div>
                <span class="text-gray-600 text-sm">(42 reviews)</span>
            </div>
            
            <div class="mb-6">
                <span class="text-3xl font-bold text-blue-600"><?php echo format_price($product['price']); ?></span>
                <?php if ($product['stock_quantity'] > 0): ?>
                    <span class="text-green-600 ml-4">
                        <i class="fas fa-check-circle"></i> In Stock (<?php echo $product['stock_quantity']; ?> available)
                    </span>
                <?php else: ?>
                    <span class="text-red-600 ml-4">
                        <i class="fas fa-times-circle"></i> Out of Stock
                    </span>
                <?php endif; ?>
            </div>
            
            <p class="text-gray-700 mb-6"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            
            <?php if ($product['stock_quantity'] > 0): ?>
                <form action="add_to_cart.php" method="post" class="flex items-center space-x-4 mb-6">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>"
                               class="w-20 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                        <i class="fas fa-cart-plus mr-2"></i> Add to Cart
                    </button>
                </form>
            <?php endif; ?>
            
            <div class="border-t border-gray-200 pt-4">
                <div class="flex items-center text-sm text-gray-600 mb-2">
                    <i class="fas fa-truck mr-2"></i>
                    <span>Free shipping on orders over $50</span>
                </div>
                <div class="flex items-center text-sm text-gray-600 mb-2">
                    <i class="fas fa-undo mr-2"></i>
                    <span>30-day easy returns</span>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-shield-alt mr-2"></i>
                    <span>Secure payment</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Tabs -->
    <div class="bg-white rounded-lg shadow-md mb-12">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button class="py-4 px-6 border-b-2 border-blue-500 text-blue-500 font-medium">Description</button>
                <button class="py-4 px-6 text-gray-500 font-medium hover:text-blue-500">Specifications</button>
                <button class="py-4 px-6 text-gray-500 font-medium hover:text-blue-500">Reviews (42)</button>
            </nav>
        </div>
        
        <div class="p-6">
            <h3 class="text-xl font-semibold mb-4">Product Description</h3>
            <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            
            <h3 class="text-xl font-semibold mt-6 mb-4">Product Details</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600"><strong>Category:</strong> <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></p>
                    <p class="text-gray-600"><strong>SKU:</strong> PROD-<?php echo $product['id']; ?></p>
                </div>
                <div>
                    <p class="text-gray-600"><strong>Stock:</strong> <?php echo $product['stock_quantity']; ?> items available</p>
                    <p class="text-gray-600"><strong>Added:</strong> <?php echo date('F j, Y', strtotime($product['created_at'])); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (count($related_products) > 0): ?>
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Products</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($related_products as $related): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <img src="<?php echo $related['image_url'] ?: 'https://via.placeholder.com/300x200'; ?>" 
                             alt="<?php echo htmlspecialchars($related['name']); ?>" 
                             class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="font-semibold text-lg mb-2"><?php echo htmlspecialchars($related['name']); ?></h3>
                            <p class="text-gray-600 mb-4"><?php echo format_price($related['price']); ?></p>
                            <div class="flex justify-between items-center">
                                <a href="product.php?id=<?php echo $related['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-800 font-semibold">View Details</a>
                                <?php if($related['stock_quantity'] > 0): ?>
                                    <form action="add_to_cart.php" method="post">
                                        <input type="hidden" name="product_id" value="<?php echo $related['id']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
require_once 'includes/footer.php';
$mysqli->close();
?>