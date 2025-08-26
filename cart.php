<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!is_logged_in()) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['id'];

// Handle quantity updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_quantity'])) {
    $cart_id = intval($_POST['cart_id']);
    $new_quantity = intval($_POST['quantity']);
    
    if ($new_quantity < 1) {
        // Remove item if quantity is 0
        $delete = $mysqli->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $delete->bind_param("ii", $cart_id, $user_id);
        $delete->execute();
        $delete->close();
    } else {
        // Check stock before updating
        $stock_check = $mysqli->prepare("
            SELECT p.stock_quantity 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.id = ? AND c.user_id = ?
        ");
        $stock_check->bind_param("ii", $cart_id, $user_id);
        $stock_check->execute();
        $stock_check->bind_result($stock_quantity);
        $stock_check->fetch();
        $stock_check->close();
        
        if ($new_quantity > $stock_quantity) {
            $_SESSION['error_message'] = "Cannot update quantity. Only $stock_quantity items available in stock.";
            $new_quantity = $stock_quantity;
        }
        
        $update = $mysqli->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $update->bind_param("iii", $new_quantity, $cart_id, $user_id);
        $update->execute();
        $update->close();
    }
    
    $_SESSION['success_message'] = "Cart updated successfully!";
}

// Handle item removal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_item'])) {
    $cart_id = intval($_POST['cart_id']);
    
    $delete = $mysqli->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $delete->bind_param("ii", $cart_id, $user_id);
    $delete->execute();
    $delete->close();
    
    $_SESSION['success_message'] = "Item removed from cart successfully!";
}

// Get cart items with product details
$cart_items = [];
$total = 0;

$sql = "
    SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.image_url, p.stock_quantity 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ? 
    ORDER BY c.added_at DESC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total += $row['subtotal'];
    $cart_items[] = $row;
}

$stmt->close();
?>

<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (count($cart_items) > 0): ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-700">Cart Items (<?php echo count($cart_items); ?>)</h2>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="p-6 flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6">
                                <!-- Product Image -->
                                <img src="<?php echo $item['image_url'] ?: 'https://via.placeholder.com/100x100'; ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                                
                                <!-- Product Info -->
                                <div class="flex-grow">
                                    <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="text-blue-600 font-bold"><?php echo format_price($item['price']); ?></p>
                                    <p class="text-sm text-gray-500">Stock: <?php echo $item['stock_quantity']; ?></p>
                                </div>
                                
                                <!-- Quantity Controls -->
                                <div class="flex items-center space-x-4">
                                    <form method="post" class="flex items-center">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                               min="1" max="<?php echo $item['stock_quantity']; ?>"
                                               class="w-16 px-2 py-1 border border-gray-300 rounded text-center">
                                        <button type="submit" name="update_quantity" 
                                                class="ml-2 bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">
                                            Update
                                        </button>
                                    </form>
                                    
                                    <!-- Remove Button -->
                                    <form method="post">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <button type="submit" name="remove_item" 
                                                class="text-red-600 hover:text-red-800 transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                
                                <!-- Subtotal -->
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900"><?php echo format_price($item['subtotal']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="lg:col-span-1">
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
                    
                    <a href="checkout.php" 
                       class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition text-center block font-semibold">
                        Proceed to Checkout
                    </a>
                    
                    <div class="mt-4 text-center">
                        <a href="products.php" class="text-blue-600 hover:text-blue-800 text-sm">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
            <h2 class="text-2xl font-semibold text-gray-600 mb-2">Your cart is empty</h2>
            <p class="text-gray-500 mb-6">Start shopping to add items to your cart</p>
            <a href="products.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                Browse Products
            </a>
        </div>
    <?php endif; ?>
</div>

<?php
require_once 'includes/footer.php';
$mysqli->close();
?>