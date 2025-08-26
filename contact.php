<?php
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Contact Us</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Contact Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-6">Send us a Message</h2>
            
            <form class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    Send Message
                </button>
            </form>
        </div>
        
        <!-- Contact Information -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-6">Get in Touch</h2>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="bg-blue-100 p-2 rounded-full mr-3 mt-1">
                            <i class="fas fa-map-marker-alt text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold">Address</h3>
                            <p class="text-gray-600">123 PHP Street, Web City</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="bg-blue-100 p-2 rounded-full mr-3 mt-1">
                            <i class="fas fa-phone text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold">Phone</h3>
                            <p class="text-gray-600">+1 (555) 123-4567</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="bg-blue-100 p-2 rounded-full mr-3 mt-1">
                            <i class="fas fa-envelope text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold">Email</h3>
                            <p class="text-gray-600">info@phpshop.com</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="bg-blue-100 p-2 rounded-full mr-3 mt-1">
                            <i class="fas fa-clock text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold">Hours</h3>
                            <p class="text-gray-600">Monday-Friday: 9AM-5PM</p>
                            <p class="text-gray-600">Saturday: 10AM-4PM</p>
                            <p class="text-gray-600">Sunday: Closed</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-blue-800 mb-3">FAQ</h2>
                <p class="text-blue-600 text-sm mb-4">Before contacting us, you might find answers to common questions in our FAQ section.</p>
                <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                    Visit FAQ Section →
                </a>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>