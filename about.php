<?php
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">About Us</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Our Story</h2>
        <p class="text-gray-700 mb-4">
            PHPShop was founded in 2025 with a simple mission: to provide high-quality products 
            with exceptional customer service. We believe that shopping online should be easy, 
            secure, and enjoyable.
        </p>
        <p class="text-gray-700">
            Our team is dedicated to curating the best products and ensuring that every customer 
            has a great experience shopping with us.
        </p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-gem text-blue-600 text-2xl"></i>
            </div>
            <h3 class="font-semibold text-lg mb-2">Quality Products</h3>
            <p class="text-gray-600">We carefully select all our products for quality and value.</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shipping-fast text-blue-600 text-2xl"></i>
            </div>
            <h3 class="font-semibold text-lg mb-2">Fast Shipping</h3>
            <p class="text-gray-600">We process and ship orders quickly and efficiently.</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-headset text-blue-600 text-2xl"></i>
            </div>
            <h3 class="font-semibold text-lg mb-2">Support</h3>
            <p class="text-gray-600">Our customer support team is here to help you.</p>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>