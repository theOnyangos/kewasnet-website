<?php 
    use App\Models\SocialLink;

    $socialLink     = new SocialLink();
    $socialLinks    = $socialLink->getSocialLinks();

    $uri            = service('uri');
    $segments       = $uri->getSegments();

    $facebook       = $socialLinks['facebook'];
    $twitter        = $socialLinks['twitter'];
    $instagram      = $socialLinks['instagram'];
    $linkedin       = $socialLinks['linkedin'];
    $youtube        = $socialLinks['youtube'];
?>

<!-- Top Navigation Bar -->
<div class="bg-primary text-white text-sm py-2 px-4">
    <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
        <div class="flex items-center space-x-4 mb-2 md:mb-0">
            <div onclick="window.location.href='tel:+254705530499'" class="flex items-center cursor-pointer">
                <i class="fas fa-phone-alt mr-2 text-white"></i>
                <span>+254 (705)- 530-499</span>
            </div>
            <div onclick="window.location.href='mailto:info@kewasnet.co.ke'" class="flex items-center cursor-pointer">
                <i class="fas fa-envelope mr-2 text-white"></i>
                <span>info@kewasnet.co.ke</span>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <a href="<?= base_url('help-center') ?>" class="hover:text-secondary transition-colors">Help Center</a>
            <div class="h-4 w-px bg-white/30"></div>
            <div class="flex space-x-3">
                <a href="<?= $facebook ?>" target="_blank" class="hover:text-secondary transition-colors"><i class="fab fa-facebook-f"></i></a>
                <a href="<?= $twitter ?>" target="_blank" class="hover:text-secondary transition-colors"><i class="fab fa-twitter"></i></a>
                <a href="<?= $linkedin ?>" target="_blank" class="hover:text-secondary transition-colors"><i class="fab fa-linkedin-in"></i></a>
                <a href="<?= $youtube ?>" target="_blank" class="hover:text-secondary transition-colors"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Main Header - Now Sticky -->
<header class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-50 transition-all duration-300">
    <div class="container mx-auto px-4 py-3">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <a href="<?= base_url("/") ?>" class="flex items-center space-x-3">
                <img src="<?= base_url("assets/new/site-logo.png") ?>" alt="KEWASNET Logo" class="h-12">
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center space-x-2">
                <a href="<?= base_url("about") ?>" class="water-nav-link <?= ($uri->getSegment(1) === "about") ? "active" : ""; ?> text-slate-700 font-medium">About</a>
                <a href="<?= base_url("programs") ?>" class="water-nav-link <?= ($uri->getSegment(1) === "programs") ? "active" : ""; ?> text-slate-700 font-medium">Programs</a>
                <a href="<?= base_url("resources") ?>" class="water-nav-link <?= ($uri->getSegment(1) === "resources") ? "active" : ""; ?> text-slate-700 font-medium">Resources</a>
                <a href="<?= base_url("opportunities") ?>" class="water-nav-link <?= ($uri->getSegment(1) === "opportunities") ? "active" : ""; ?> text-slate-700 font-medium">Opportunities</a>
                <a href="<?= base_url("events") ?>" class="water-nav-link <?= ($uri->getSegment(1) === "events") ? "active" : ""; ?> text-slate-700 font-medium">Events</a>
                <a href="<?= base_url("news") ?>" class="water-nav-link <?= ($uri->getSegment(1) === "news") ? "active" : ""; ?> text-slate-700 font-medium">News</a>
                <a href="<?= base_url("contact-us") ?>" class="water-nav-link <?= ($uri->getSegment(1) === "contact-us") ? "active" : ""; ?> text-slate-700 font-medium">Contact</a>

                <!-- Water-like Animated Button -->
                <a href="<?= base_url('ksp') ?>" target="_blank" class="gradient-btn flex items-center text-white px-6 py-2 rounded-[50px] transition-all duration-300 ml-16">
                    <span>KSP</span>
                    <i data-lucide="arrow-right" class="ml-2 icon z-10"></i>
                </a>
            </nav>

            <!-- Mobile Menu Button -->
            <button class="md:hidden" onclick="toggleMobileMenu()">
                <i data-lucide="menu" class="icon-xl"></i>
            </button>
        </div>
    </div>
</header>

<!-- Mobile Navigation - Side Drawer -->
<div id="mobile-menu" class="fixed inset-y-0 right-0 w-80 bg-white shadow-xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto">
    <div class="p-6">
        <div class="flex justify-between items-center mb-8">
            <a href="<?= base_url("/") ?>" class="flex items-center space-x-3">
                <img src="<?= base_url("assets/new/site-logo.png") ?>" alt="KEWASNET Logo" class="h-12">
            </a>
            <button onclick="toggleMobileMenu()" class="p-2 rounded-full hover:bg-slate-100">
                <i data-lucide="x" class="icon-xl"></i>
            </button>
        </div>
        
        <nav class="space-y-4">
            <a href="<?= base_url("about") ?>" class="block py-3 px-4 hover:bg-slate-50 rounded-md <?= ($uri->getSegment(1) === "about" || $uri->getSegment(1) === "") ? "bg-slate-100 font-medium text-primary" : ""; ?>">About</a>
            <a href="<?= base_url("programs") ?>" class="block py-3 px-4 hover:bg-slate-50 rounded-md <?= ($uri->getSegment(1) === "programs") ? "bg-slate-100 font-medium text-primary" : ""; ?>">Programs</a>
            <a href="<?= base_url("resources") ?>" class="block py-3 px-4 hover:bg-slate-50 rounded-md <?= ($uri->getSegment(1) === "resources") ? "bg-slate-100 font-medium text-primary" : ""; ?>">Resources</a>
            <a href="<?= base_url("opportunities") ?>" class="block py-3 px-4 hover:bg-slate-50 rounded-md <?= ($uri->getSegment(1) === "opportunities") ? "bg-slate-100 font-medium text-primary" : ""; ?>">Opportunities</a>
            <a href="<?= base_url("events") ?>" class="block py-3 px-4 hover:bg-slate-50 rounded-md <?= ($uri->getSegment(1) === "events") ? "bg-slate-100 font-medium text-primary" : ""; ?>">Events</a>
            <a href="<?= base_url("news") ?>" class="block py-3 px-4 hover:bg-slate-50 rounded-md <?= ($uri->getSegment(1) === "news") ? "bg-slate-100 font-medium text-primary" : ""; ?>">News</a>
            <a href="<?= base_url("contact-us") ?>" class="block py-3 px-4 hover:bg-slate-50 rounded-md <?= ($uri->getSegment(1) === "contact-us") ? "bg-slate-100 font-medium text-primary" : ""; ?>">Contact</a>
            <button class="w-full bg-primary hover:bg-primary/90 text-white py-3 px-6 rounded-md flex items-center justify-center mt-6">
                Knowledge Hub
                <i data-lucide="database" class="ml-2 icon"></i>
            </button>
        </nav>
    </div>
</div>
<div id="mobile-menu-overlay" class="fixed inset-0 bg-black/50 z-40 hidden" onclick="toggleMobileMenu()"></div>

<?= $this->section('scripts') ?>
<script>
    // Water ripple effect for navigation links
    document.addEventListener('DOMContentLoaded', function() {
        const waterNavLinks = document.querySelectorAll('.water-nav-link');
        
        waterNavLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Create ripple effect
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');
                
                this.appendChild(ripple);
                
                // Remove ripple after animation
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    });

    // Mobile menu toggle functionality
    function toggleMobileMenu() {
        const mobileMenu = document.getElementById('mobile-menu');
        const overlay = document.getElementById('mobile-menu-overlay');
        
        if (mobileMenu.classList.contains('translate-x-full')) {
            mobileMenu.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            mobileMenu.classList.add('translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }
</script>
<?= $this->endSection() ?>