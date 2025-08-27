<?php
// Check if a user is logged in
$is_logged_in = is_logged_in();
$username = $is_logged_in ? get_session_var('username') : '';
$cart_count = is_db_connected() ? get_cart_count() : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP E-Commerce</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .dropdown:hover .dropdown-menu {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-blue-950 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <a href="<?php echo BASE_URL; ?>" class="text-2xl font-bold">PHPShop</a>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-6">
                    <a href="<?php echo BASE_URL; ?>" class="hover:text-blue-300 transition">Home</a>
                    <a href="<?php echo BASE_URL; ?>products.php" class="hover:text-blue-300 transition">Products</a>
                    <a href="<?php echo BASE_URL; ?>about.php" class="hover:text-blue-300 transition">About</a>
                    <a href="<?php echo BASE_URL; ?>contact.php" class="hover:text-blue-300 transition">Contact</a>
                </div>
                
                <!-- Right Side Items -->
                <div class="flex items-center space-x-4">
                    <!-- Cart Icon -->
                    <a href="<?php echo BASE_URL; ?>cart.php" class="relative">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <?php if ($cart_count > 0): ?>
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    
                    <!-- User Auth -->
                    <?php if ($is_logged_in): ?>
                        <div class="relative dropdown">
                            <button class="flex items-center space-x-2">
                                <span>Hello, <?php echo htmlspecialchars($username); ?></span>
                                <i class="fas fa-chevron-down text-sm"></i>
                            </button>
                            <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 text-gray-800 hidden z-10">
                                <a href="<?php echo BASE_URL; ?>profile.php" class="block px-4 py-2 hover:bg-blue-100 text-blue-600">
                                    <i class="fas fa-user mr-2"></i>My Profile
                                </a>
                                <a href="<?php echo BASE_URL; ?>orders.php" class="block px-4 py-2 hover:bg-blue-100 text-blue-600">
                                    <i class="fas fa-shopping-bag mr-2"></i>My Orders
                                </a>
                                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                    <div class="border-t border-gray-200 my-2"></div>
                                    <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="block px-4 py-2 hover:bg-blue-100 text-purple-600 font-semibold">
                                        <i class="fas fa-crown mr-2"></i>Admin Panel
                                    </a>
                                <?php endif; ?>
                                <div class="border-t border-gray-200 my-2"></div>
                                <a href="<?php echo BASE_URL; ?>logout.php" class="block px-4 py-2 hover:bg-blue-100 text-red-600">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex space-x-2">
                            <a href="<?php echo BASE_URL; ?>login.php" class="px-3 py-1 rounded hover:bg-blue-700 transition">Login</a>
                            <a href="<?php echo BASE_URL; ?>register.php" class="px-3 py-1 bg-blue-500 rounded hover:bg-blue-700 transition">Register</a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile Menu Button -->
                <button class="md:hidden" id="mobile-menu-button">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu (hidden by default) -->
        <div class="md:hidden bg-blue-700 px-4 py-2 hidden" id="mobile-menu">
            <a href="<?php echo BASE_URL; ?>" class="block py-2 hover:bg-blue-600 px-2 rounded">Home</a>
            <a href="<?php echo BASE_URL; ?>products.php" class="block py-2 hover:bg-blue-600 px-2 rounded">Products</a>
            <a href="<?php echo BASE_URL; ?>about.php" class="block py-2 hover:bg-blue-600 px-2 rounded">About</a>
            <a href="<?php echo BASE_URL; ?>contact.php" class="block py-2 hover:bg-blue-600 px-2 rounded">Contact</a>
            
            <?php if ($is_logged_in): ?>
                <div class="border-t border-blue-600 mt-2 pt-2">
                    <a href="<?php echo BASE_URL; ?>profile.php" class="block py-2 hover:bg-blue-600 px-2 rounded">
                        <i class="fas fa-user mr-2"></i>My Profile
                    </a>
                    <a href="<?php echo BASE_URL; ?>orders.php" class="block py-2 hover:bg-blue-600 px-2 rounded">
                        <i class="fas fa-shopping-bag mr-2"></i>My Orders
                    </a>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="block py-2 hover:bg-blue-600 px-2 rounded text-purple-300">
                            <i class="fas fa-crown mr-2"></i>Admin Panel
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>logout.php" class="block py-2 hover:bg-blue-600 px-2 rounded text-red-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            <?php else: ?>
                <div class="border-t border-blue-600 mt-2 pt-2">
                    <a href="<?php echo BASE_URL; ?>login.php" class="block py-2 hover:bg-blue-600 px-2 rounded">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                    <a href="<?php echo BASE_URL; ?>register.php" class="block py-2 hover:bg-blue-600 px-2 rounded">
                        <i class="fas fa-user-plus mr-2"></i>Register
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">