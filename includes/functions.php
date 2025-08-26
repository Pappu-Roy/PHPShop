<?php
// Redirect function
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}

// Get category name
function get_category_name($category_id) {
    global $mysqli;
    
    if (!$mysqli || $mysqli->connect_errno) {
        return "Uncategorized";
    }
    
    if ($category_id) {
        $sql = "SELECT name FROM categories WHERE id = ?";
        if($stmt = $mysqli->prepare($sql)){
            $stmt->bind_param("i", $category_id);
            if($stmt->execute()){
                $stmt->bind_result($name);
                if($stmt->fetch()){
                    return $name;
                }
            }
            $stmt->close();
        }
    }
    return "Uncategorized";
}

// Format price
function format_price($price) {
    return '$' . number_format($price, 2);
}

// Get cart item count
function get_cart_count() {
    if(isset($_SESSION['user_id']) && $_SESSION['user_id']) {
        global $mysqli;
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
        if($stmt = $mysqli->prepare($sql)){
            $stmt->bind_param("i", $user_id);
            if($stmt->execute()){
                $stmt->bind_result($total);
                $stmt->fetch();
                return $total ? $total : 0;
            }
            $stmt->close();
        }
    }
    return 0;
}

// Safe session variable access
function get_session_var($key, $default = '') {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
}

// Check database connection
function is_db_connected() {
    global $mysqli;
    return $mysqli && $mysqli instanceof mysqli && !$mysqli->connect_errno;
}

// Get product by ID
function get_product($id) {
    global $mysqli;
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Get featured products
function get_featured_products($limit = 8) {
    global $mysqli;
    $products = [];
    $sql = "SELECT * FROM products WHERE stock_quantity > 0 ORDER BY created_at DESC LIMIT ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
    return $products;
}
?>