<?php 
    use App\Models\SocialLink;

    $socialLink     = new SocialLink();
    $socialLinks    = $socialLink->getSocialLinks();

    $uri            = service('uri');
    $segments       = $uri->getSegments();

    // Safely get social links with fallback to empty string if null or key doesn't exist
    $facebook       = (!empty($socialLinks) && isset($socialLinks['facebook'])) ? $socialLinks['facebook'] : '';
    $twitter        = (!empty($socialLinks) && isset($socialLinks['twitter'])) ? $socialLinks['twitter'] : '';
    $instagram      = (!empty($socialLinks) && isset($socialLinks['instagram'])) ? $socialLinks['instagram'] : '';
    $linkedin       = (!empty($socialLinks) && isset($socialLinks['linkedin'])) ? $socialLinks['linkedin'] : '';
    $youtube        = (!empty($socialLinks) && isset($socialLinks['youtube'])) ? $socialLinks['youtube'] : '';
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
            <?php if (!empty($facebook) || !empty($twitter) || !empty($linkedin) || !empty($youtube) || !empty($instagram)): ?>
                <div class="h-4 w-px bg-white/30"></div>
                <div class="flex space-x-3">
                    <?php if (!empty($facebook)): ?>
                        <a href="<?= esc($facebook) ?>" target="_blank" class="hover:text-secondary transition-colors"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($twitter)): ?>
                        <a href="<?= esc($twitter) ?>" target="_blank" class="hover:text-secondary transition-colors"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($linkedin)): ?>
                        <a href="<?= esc($linkedin) ?>" target="_blank" class="hover:text-secondary transition-colors"><i class="fab fa-linkedin-in"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($youtube)): ?>
                        <a href="<?= esc($youtube) ?>" target="_blank" class="hover:text-secondary transition-colors"><i class="fab fa-youtube"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($instagram)): ?>
                        <a href="<?= esc($instagram) ?>" target="_blank" class="hover:text-secondary transition-colors"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
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
<div id="mobile-menu" class="fixed inset-y-0 right-0 w-full bg-white shadow-xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto flex flex-col">
    <div class="flex flex-col flex-1 min-h-0">
        <!-- Header Section -->
        <div class="flex-shrink-0 p-6 border-b border-slate-200">
            <div class="flex justify-between items-center mb-4">
                <a href="<?= base_url("/") ?>" class="flex items-center space-x-3" onclick="toggleMobileMenu()">
                    <img src="<?= base_url("assets/new/site-logo.png") ?>" alt="KEWASNET Logo" class="h-12">
                </a>
                <button onclick="toggleMobileMenu()" class="p-2 rounded-full hover:bg-slate-100 transition-colors">
                    <i data-lucide="x" class="icon-xl text-slate-600"></i>
                </button>
            </div>
        </div>
        
        <!-- Navigation Links - Scrollable -->
        <div class="flex-1 overflow-y-auto p-6">
            <nav class="space-y-2 mb-6">
                <a href="<?= base_url("about") ?>" onclick="toggleMobileMenu()" class="flex items-center py-3 px-4 hover:bg-primary/5 rounded-lg transition-colors <?= ($uri->getSegment(1) === "about" || $uri->getSegment(1) === "") ? "bg-primary/10 font-medium text-primary border-l-4 border-primary" : "text-slate-700"; ?>">
                    <i data-lucide="info" class="w-5 h-5 mr-3"></i>
                    <span>About</span>
                </a>
                <a href="<?= base_url("programs") ?>" onclick="toggleMobileMenu()" class="flex items-center py-3 px-4 hover:bg-primary/5 rounded-lg transition-colors <?= ($uri->getSegment(1) === "programs") ? "bg-primary/10 font-medium text-primary border-l-4 border-primary" : "text-slate-700"; ?>">
                    <i data-lucide="briefcase" class="w-5 h-5 mr-3"></i>
                    <span>Programs</span>
                </a>
                <a href="<?= base_url("resources") ?>" onclick="toggleMobileMenu()" class="flex items-center py-3 px-4 hover:bg-primary/5 rounded-lg transition-colors <?= ($uri->getSegment(1) === "resources") ? "bg-primary/10 font-medium text-primary border-l-4 border-primary" : "text-slate-700"; ?>">
                    <i data-lucide="file-text" class="w-5 h-5 mr-3"></i>
                    <span>Resources</span>
                </a>
                <a href="<?= base_url("opportunities") ?>" onclick="toggleMobileMenu()" class="flex items-center py-3 px-4 hover:bg-primary/5 rounded-lg transition-colors <?= ($uri->getSegment(1) === "opportunities") ? "bg-primary/10 font-medium text-primary border-l-4 border-primary" : "text-slate-700"; ?>">
                    <i data-lucide="target" class="w-5 h-5 mr-3"></i>
                    <span>Opportunities</span>
                </a>
                <a href="<?= base_url("events") ?>" onclick="toggleMobileMenu()" class="flex items-center py-3 px-4 hover:bg-primary/5 rounded-lg transition-colors <?= ($uri->getSegment(1) === "events") ? "bg-primary/10 font-medium text-primary border-l-4 border-primary" : "text-slate-700"; ?>">
                    <i data-lucide="calendar" class="w-5 h-5 mr-3"></i>
                    <span>Events</span>
                </a>
                <a href="<?= base_url("news") ?>" onclick="toggleMobileMenu()" class="flex items-center py-3 px-4 hover:bg-primary/5 rounded-lg transition-colors <?= ($uri->getSegment(1) === "news") ? "bg-primary/10 font-medium text-primary border-l-4 border-primary" : "text-slate-700"; ?>">
                    <i data-lucide="newspaper" class="w-5 h-5 mr-3"></i>
                    <span>News</span>
                </a>
                <a href="<?= base_url("contact-us") ?>" onclick="toggleMobileMenu()" class="flex items-center py-3 px-4 hover:bg-primary/5 rounded-lg transition-colors <?= ($uri->getSegment(1) === "contact-us") ? "bg-primary/10 font-medium text-primary border-l-4 border-primary" : "text-slate-700"; ?>">
                    <i data-lucide="mail" class="w-5 h-5 mr-3"></i>
                    <span>Contact</span>
                </a>
            </nav>
            
            <!-- KSP Button -->
            <a href="<?= base_url('ksp') ?>" target="_blank" onclick="toggleMobileMenu()" class="w-full gradient-btn py-3 px-6 rounded-lg text-white flex items-center justify-center mb-6 shadow-md hover:shadow-lg transition-shadow">
                <i data-lucide="database" class="w-5 h-5 mr-2"></i>
                <span>Knowledge Sharing Platform</span>
                <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
            </a>
        </div>
        
        <!-- Company Information Footer - Fixed at Bottom -->
        <div class="flex-shrink-0 border-t border-slate-200 bg-slate-50 p-6">
            <div class="space-y-4">
                <!-- Company Description -->
                <div>
                    <p class="text-sm text-slate-600 leading-relaxed mb-3">
                        Leading Kenya's WASH sector through knowledge sharing, capacity building, 
                        and strategic advocacy for sustainable water and sanitation solutions.
                    </p>
                </div>
                
                <!-- Contact Information -->
                <div class="space-y-2 text-sm">
                    <div class="flex items-start space-x-2 text-slate-600">
                        <i data-lucide="map-pin" class="w-4 h-4 mt-0.5 text-primary flex-shrink-0"></i>
                        <span class="text-xs">Mango Court, 4th Floor, Suite A403, Wood Avenue, Nairobi, Kenya</span>
                    </div>
                    <div class="flex items-center space-x-2 text-slate-600">
                        <i data-lucide="phone" class="w-4 h-4 text-primary flex-shrink-0"></i>
                        <a href="tel:+254705530499" class="text-xs hover:text-primary transition-colors">+254 (705) 530-499</a>
                    </div>
                    <div class="flex items-center space-x-2 text-slate-600">
                        <i data-lucide="mail" class="w-4 h-4 text-primary flex-shrink-0"></i>
                        <a href="mailto:info@kewasnet.co.ke" class="text-xs hover:text-primary transition-colors">info@kewasnet.co.ke</a>
                    </div>
                </div>
                
                <!-- Social Links -->
                <?php if (!empty($facebook) || !empty($twitter) || !empty($linkedin) || !empty($youtube) || !empty($instagram)): ?>
                    <div class="pt-3 border-t border-slate-200">
                        <p class="text-xs font-medium text-slate-700 mb-2">Follow Us</p>
                        <div class="flex space-x-3">
                            <?php if (!empty($facebook)): ?>
                                <a href="<?= esc($facebook) ?>" target="_blank" class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors shadow-sm">
                                    <i class="fab fa-facebook-f text-xs"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($twitter)): ?>
                                <a href="<?= esc($twitter) ?>" target="_blank" class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors shadow-sm">
                                    <i class="fab fa-twitter text-xs"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($linkedin)): ?>
                                <a href="<?= esc($linkedin) ?>" target="_blank" class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors shadow-sm">
                                    <i class="fab fa-linkedin-in text-xs"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($youtube)): ?>
                                <a href="<?= esc($youtube) ?>" target="_blank" class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors shadow-sm">
                                    <i class="fab fa-youtube text-xs"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($instagram)): ?>
                                <a href="<?= esc($instagram) ?>" target="_blank" class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors shadow-sm">
                                    <i class="fab fa-instagram text-xs"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Copyright -->
                <div class="pt-3 border-t border-slate-200">
                    <p class="text-xs text-slate-500 text-center">
                        © <?= date("Y") ?> KEWASNET. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="mobile-menu-overlay" class="fixed inset-0 bg-black/50 z-40 hidden" onclick="toggleMobileMenu()"></div>

<style>
    .events-page-pattern-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
            pointer-events: none;
            background-image: 
                repeating-linear-gradient(45deg, rgba(0, 0, 0, 0.1) 0, rgba(0, 0, 0, 0.1) 1px, transparent 1px, transparent 20px),
                repeating-linear-gradient(-45deg, rgba(0, 0, 0, 0.1) 0, rgba(0, 0, 0, 0.1) 1px, transparent 1px, transparent 20px);
            background-size: 40px 40px;
        }
</style>

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
            // Reinitialize Lucide icons when menu opens
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        } else {
            mobileMenu.classList.add('translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }
</script>
<?= $this->endSection() ?>