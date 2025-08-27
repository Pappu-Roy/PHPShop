<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">Shipping Policy</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Our Commitment to Timely Delivery</h2>
        <p class="text-gray-600 mb-4">We are dedicated to getting your order to you as quickly as possible. All orders are processed within 1-2 business days. You will receive a confirmation email with a tracking number once your order has shipped.</p>

        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">Shipping Options & Costs:</h3>
        <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
            <li>**Standard Shipping:** Delivery within 5-7 business days. Cost is $5.99, but it's FREE for all orders over $50.</li>
            <li>**Express Shipping:** Delivery within 2-3 business days. Cost is $19.99.</li>
        </ul>
        
        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">International Shipping:</h3>
        <p class="text-gray-600">We currently ship only within the United States. We plan to expand to international shipping in the near future. Please check back for updates.</p>
    </div>
</div>

<?php
require_once 'includes/footer.php';
$mysqli->close();
?>