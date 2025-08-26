<?php
// Start output buffering at the very top of the script
ob_start();
require_once 'includes/config.php';

// Check if user is logged in, otherwise redirect immediately
if (!is_logged_in()) {
    $_SESSION['redirect_url'] = 'checkout.php';
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];
$errors = [];
$cart_items = [];
$total = 0;

// Fetch cart items and calculate total
$sql_cart = "SELECT c.quantity, p.price, p.stock_quantity, p.name 
             FROM cart c 
             JOIN products p ON c.product_id = p.id 
             WHERE c.user_id = ?";
$stmt_cart = $mysqli->prepare($sql_cart);
$stmt_cart->bind_param("i", $user_id);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();

while ($row = $result_cart->fetch_assoc()) {
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
    $cart_items[] = $row;
}
$stmt_cart->close();

// If the cart is empty, redirect to the cart page
if (count($cart_items) === 0) {
    header("Location: cart.php");
    exit;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and validate form data
    $required_fields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'zip_code'];
    foreach ($required_fields as $field) {
        if (empty(trim($_POST[$field]))) {
            $errors[$field] = ucwords(str_replace('_', ' ', $field)) . " is required";
        }
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address";
    }

    // If no validation errors, proceed with order processing
    if (empty($errors)) {
        $mysqli->begin_transaction();
        
        try {
            $order_sql = "INSERT INTO orders (user_id, total_amount) VALUES (?, ?)";
            $order_stmt = $mysqli->prepare($order_sql);
            $shipping = ($total > 50) ? 0 : 5.99;
            $tax = $total * 0.08;
            $final_total = $total + $shipping + $tax;
            $order_stmt->bind_param("id", $user_id, $final_total);
            $order_stmt->execute();
            $order_id = $mysqli->insert_id;
            $order_stmt->close();
            
            $cart_sql_items = "SELECT c.product_id, c.quantity, p.price, p.stock_quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
            $cart_stmt_items = $mysqli->prepare($cart_sql_items);
            $cart_stmt_items->bind_param("i", $user_id);
            $cart_stmt_items->execute();
            $cart_result_items = $cart_stmt_items->get_result();
            
            while ($item = $cart_result_items->fetch_assoc()) {
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
            
            $cart_stmt_items->close();
            
            $clear_sql = "DELETE FROM cart WHERE user_id = ?";
            $clear_stmt = $mysqli->prepare($clear_sql);
            $clear_stmt->bind_param("i", $user_id);
            $clear_stmt->execute();
            $clear_stmt->close();
            
            $mysqli->commit();
            $_SESSION['order_id'] = $order_id;
            
            // Clear buffer and redirect on success
            ob_end_clean();
            header("Location: order_success.php");
            exit;
        } catch (Exception $e) {
            $mysqli->rollback();
            $errors['general'] = "Error processing your order: " . $e->getMessage();
        }
    }
}

// Now include the header and render the page, as all logic is complete
require_once 'includes/header.php';
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
// Flush the buffer to send output to the browser
ob_end_flush();
$mysqli->close();
?>