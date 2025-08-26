<?php
require_once 'includes/config.php';

// Check if user is logged in
if (!is_logged_in()) {
    $_SESSION['redirect_url'] = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    header("location: login.php");
    exit;
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $user_id = $_SESSION['id'];
    $product_id = intval($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    // Validate quantity
    if ($quantity < 1) {
        $quantity = 1;
    }
    
    // Check if product exists and is in stock
    $product_check = $mysqli->prepare("SELECT stock_quantity FROM products WHERE id = ?");
    $product_check->bind_param("i", $product_id);
    $product_check->execute();
    $product_check->store_result();
    
    if ($product_check->num_rows > 0) {
        $product_check->bind_result($stock_quantity);
        $product_check->fetch();
        
        if ($stock_quantity >= $quantity) {
            // Check if product is already in cart
            $cart_check = $mysqli->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
            $cart_check->bind_param("ii", $user_id, $product_id);
            $cart_check->execute();
            $cart_check->store_result();
            
            if ($cart_check->num_rows > 0) {
                // Update existing cart item
                $cart_check->bind_result($cart_id, $existing_quantity);
                $cart_check->fetch();
                
                $new_quantity = $existing_quantity + $quantity;
                
                // Don't exceed stock
                if ($new_quantity > $stock_quantity) {
                    $new_quantity = $stock_quantity;
                }
                
                $update = $mysqli->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
                $update->bind_param("ii", $new_quantity, $cart_id);
                $update->execute();
                $update->close();
                
                $_SESSION['success_message'] = "Cart updated successfully!";
            } else {
                // Add new item to cart
                $insert = $mysqli->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $insert->bind_param("iii", $user_id, $product_id, $quantity);
                $insert->execute();
                $insert->close();
                
                $_SESSION['success_message'] = "Product added to cart successfully!";
            }
            
            $cart_check->close();
        } else {
            $_SESSION['error_message'] = "Not enough stock available!";
        }
    } else {
        $_SESSION['error_message'] = "Product not found!";
    }
    
    $product_check->close();
}

// Redirect back to previous page
$redirect_url = $_SESSION['redirect_url'] ?? 'index.php';
unset($_SESSION['redirect_url']);
header("Location: $redirect_url");
exit;
?>