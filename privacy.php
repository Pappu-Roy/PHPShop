<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">Privacy Policy</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Your Privacy is Our Priority</h2>
        <p class="text-gray-600 mb-4">This Privacy Policy describes how your personal information is collected, used, and shared when you visit or make a purchase from our website.</p>
        
        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">Information We Collect:</h3>
        <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
            <li>**Personal Information:** When you register, we collect your name and email address.</li>
            <li>**Order Information:** When you make a purchase, we collect your billing and shipping information, including your name, address, phone number, and email.</li>
            <li>**Technical Data:** We automatically collect certain information about your device, such as your web browser, IP address, and time zone.</li>
        </ul>

        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">How We Use Your Information:</h3>
        <p class="text-gray-600">We use the information we collect to fulfill orders, communicate with you about your account, and improve our website's functionality. We do not sell your personal information to third parties.</p>

        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">Security:</h3>
        <p class="text-gray-600">We take reasonable precautions to protect your personal information and follow industry best practices to ensure it is not inappropriately lost, misused, accessed, disclosed, altered, or destroyed.</p>
    </div>
</div>

<?php
require_once 'includes/footer.php';
$mysqli->close();
?>