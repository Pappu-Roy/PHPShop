<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Fetch featured products
$featured_products = get_featured_products(8);
?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-500 to-purple-600 text-white py-20 mb-16 rounded-lg">
    <div class="container mx-auto text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Welcome to PHPShop</h1>
        <p class="text-xl mb-8">The best e-commerce platform built with PHP & Tailwind CSS</p>
        <a href="products.php" class="bg-white text-blue-600 px-6 py-3 rounded-full font-semibold hover:bg-gray-100 transition">Shop Now</a>
    </div>
</section>

<!-- Featured Products -->
<section class="mb-16">
    <h2 class="text-3xl font-bold text-center mb-12">Featured Products</h2>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php if(count($featured_products) > 0): ?>
            <?php foreach($featured_products as $product): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <img src="<?php echo $product['image_url'] ?: 'https://via.placeholder.com/300x200'; ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo format_price($product['price']); ?></p>
                        <div class="flex justify-between items-center">
                            <a href="product.php?id=<?php echo $product['id']; ?>" 
                               class="text-blue-600 hover:text-blue-800 font-semibold">View Details</a>
                            <?php if($product['stock_quantity'] > 0): ?>
                                <form action="add_to_cart.php" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-red-500 font-semibold">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-8">
                <p class="text-gray-500">No products available yet.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="text-center mt-8">
        <a href="products.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
            View All Products
        </a>
    </div>
</section>

<!-- Features Section -->
<section class="bg-white py-12 rounded-lg shadow">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-truck text-blue-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-lg mb-2">Free Shipping</h3>
                <p class="text-gray-600">On all orders over $50</p>
            </div>
            
            <div class="text-center">
                <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-undo text-blue-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-lg mb-2">Easy Returns</h3>
                <p class="text-gray-600">30-day money back guarantee</p>
            </div>
            
            <div class="text-center">
                <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-blue-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-lg mb-2">24/7 Support</h3>
                <p class="text-gray-600">Dedicated customer support</p>
            </div>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>