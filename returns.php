<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">Returns & Refunds</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Our 30-Day Money-Back Guarantee</h2>
        <p class="text-gray-600 mb-4">We want you to be completely satisfied with your purchase. If you are not happy with your order for any reason, you can return it within 30 days of delivery for a full refund or exchange.</p>
        
        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">How to Initiate a Return:</h3>
        <p class="text-gray-600">To start a return, please **contact our customer service team directly** within 30 days of receiving your order. You can reach us via the following methods:</p>
        <ul class="list-disc list-inside text-gray-600 space-y-2 mt-4 mb-4">
            <li>**Email:** Send an email to <a href="mailto:support@phpshop.com" class="text-blue-600 hover:underline">support@phpshop.com</a> with your order number and the reason for the return.</li>
            <li>**Phone:** Call us at <a href="tel:+88015XXXXXXXX" class="text-blue-600 hover:underline">+88015XXXXXXXX</a> to speak with a representative.</li>
        </ul>

        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">Conditions for Returns:</h3>
        <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
            <li>Items must be in their original, unused condition.</li>
            <li>All original tags, packaging, and accessories must be included.</li>
            <li>Returns must be initiated within 30 days of the delivery date.</li>
        </ul>

        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">Refund Processing:</h3>
        <p class="text-gray-600">Once your return is received and inspected, we will notify you of the approval or rejection of your refund. If approved, your refund will be processed, and a credit will automatically be applied to your original method of payment within 7-10 business days.</p>
    </div>
</div>

<?php
require_once 'includes/footer.php';
$mysqli->close();
?>