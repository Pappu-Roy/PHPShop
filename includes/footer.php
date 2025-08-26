<?php
// Close the main content section
echo '</main>';

// Footer content
?>
<footer class="bg-gray-800 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-xl font-bold mb-4">PHPShop</h3>
                <p class="text-gray-400">The best e-commerce platform built with PHP and Tailwind CSS.</p>
            </div>
            
            <div>
                <h4 class="font-semibold mb-4">Quick Links</h4>
                <ul class="space-y-2">
                    <li><a href="<?php echo BASE_URL; ?>" class="text-gray-400 hover:text-white transition">Home</a></li>
                    <li><a href="<?php echo BASE_URL; ?>products.php" class="text-gray-400 hover:text-white transition">Products</a></li>
                    <li><a href="<?php echo BASE_URL; ?>about.php" class="text-gray-400 hover:text-white transition">About Us</a></li>
                    <li><a href="<?php echo BASE_URL; ?>contact.php" class="text-gray-400 hover:text-white transition">Contact</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-semibold mb-4">Customer Service</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-white transition">FAQ</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition">Returns & Refunds</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition">Shipping Policy</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition">Privacy Policy</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-semibold mb-4">Contact Info</h4>
                <ul class="space-y-2">
                    <li class="flex items-center space-x-2">
                        <i class="fas fa-map-marker-alt"></i>
                        <span class="text-gray-400">123 PHP Street, Web City</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <i class="fas fa-phone"></i>
                        <span class="text-gray-400">+1 (555) 123-4567</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <i class="fas fa-envelope"></i>
                        <span class="text-gray-400">info@phpshop.com</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; <?php echo date('Y'); ?> PHPShop. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Mobile Menu JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.querySelector('#mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#mobile-menu') && !event.target.closest('#mobile-menu-button')) {
            mobileMenu.classList.add('hidden');
        }
    });
    
    // Dropdown menu functionality
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
            const menu = this.querySelector('.dropdown-menu');
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
                if (otherMenu !== menu) {
                    otherMenu.classList.add('hidden');
                }
            });
            menu.classList.toggle('hidden');
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
    });
    
    // Prevent dropdowns from closing when clicking inside them
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
});
</script>

</body>
</html>