<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">Frequently Asked Questions</h1>
    
    <div class="bg-white rounded-lg shadow-md divide-y divide-gray-200">
        <div class="py-6 px-4">
            <h3 class="font-semibold text-xl text-gray-800 cursor-pointer flex justify-between items-center" onclick="toggleAccordion('faq-1')">
                <span>How do I place an order?</span>
                <i class="fas fa-chevron-down text-gray-500"></i>
            </h3>
            <div id="faq-1" class="mt-4 hidden text-gray-600">
                <p>Placing an order is easy! Simply browse our products, add the items you wish to purchase to your cart, and proceed to checkout. You will be guided through the process of providing your shipping information and payment details.</p>
            </div>
        </div>

        <div class="py-6 px-4">
            <h3 class="font-semibold text-xl text-gray-800 cursor-pointer flex justify-between items-center" onclick="toggleAccordion('faq-2')">
                <span>What are your shipping options?</span>
                <i class="fas fa-chevron-down text-gray-500"></i>
            </h3>
            <div id="faq-2" class="mt-4 hidden text-gray-600">
                <p>We offer several shipping options, including standard and express delivery. Shipping costs are calculated at checkout based on your location and the size of your order. We also offer free standard shipping on all orders over $50.</p>
            </div>
        </div>
        
        <div class="py-6 px-4">
            <h3 class="font-semibold text-xl text-gray-800 cursor-pointer flex justify-between items-center" onclick="toggleAccordion('faq-3')">
                <span>What is your return policy?</span>
                <i class="fas fa-chevron-down text-gray-500"></i>
            </h3>
            <div id="faq-3" class="mt-4 hidden text-gray-600">
                <p>We offer a 30-day money-back guarantee on most products. If you are not satisfied with your purchase, you can return it within 30 days of delivery for a full refund or exchange. Please visit our <a href="returns.php" class="text-blue-600 hover:underline">Returns & Refunds</a> page for more information.</p>
            </div>
        </div>

        <div class="py-6 px-4">
            <h3 class="font-semibold text-xl text-gray-800 cursor-pointer flex justify-between items-center" onclick="toggleAccordion('faq-4')">
                <span>How can I track my order?</span>
                <i class="fas fa-chevron-down text-gray-500"></i>
            </h3>
            <div id="faq-4" class="mt-4 hidden text-gray-600">
                <p>Once your order has been shipped, you will receive a confirmation email with a tracking number. You can use this number to track your package on the courier's website. You can also view the tracking information from your <a href="orders.php" class="text-blue-600 hover:underline">My Orders</a> page.</p>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAccordion(id) {
    const content = document.getElementById(id);
    const icon = content.previousElementSibling.querySelector('i');
    content.classList.toggle('hidden');
    icon.classList.toggle('fa-chevron-down');
    icon.classList.toggle('fa-chevron-up');
}
</script>

<?php
require_once 'includes/footer.php';
$mysqli->close();
?>