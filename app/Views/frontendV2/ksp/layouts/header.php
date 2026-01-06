<?php 
    $uri            = service('uri');
    $segments       = $uri->getSegments();
    $isLoggedIn     = session()->get('id');
    $isAdmin        = session()->get('isAdmin') == 1;
    $isKspSection   = $uri->getSegment(1) == "ksp";
    $userName       = 'Hi, ' . (session()->get('name') ?? 'Guest');
    $userEmail      = session()->get('email') ?? 'guest@example.com';
?>

<!-- Header -->
<header id="main-header" class="bg-white shadow-sm fixed top-0 left-0 right-0 w-full z-50 border-b border-slate-100 transition-all duration-300">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <!-- Logo -->
            <div class="flex items-center space-x-4">
                <a href="<?= base_url('ksp') ?>" class="flex items-center group">
                    <img src="<?= base_url("assets/new/site-logo.png") ?>" alt="KEWASNET Logo" class="h-8 md:h-10 transition-all duration-300 group-hover:scale-105">
                </a>
            </div>

            <!-- Navigation Controls -->
            <div class="flex items-center space-x-3 md:space-x-6">
                <?php if($isLoggedIn): ?>
                    <!-- Logged In User Menu -->
                    <div class="flex items-center space-x-3 md:space-x-5">
                        <!-- User Profile Dropdown -->
                        <div class="relative user-menu-wrapper">
                            <button class="user-menu-trigger flex items-center gap-3 focus:outline-none bg-gradient-to-r from-slate-50 to-slate-100 px-3 py-2 md:px-4 md:py-2.5 rounded-xl hover:shadow-md hover:from-primary/5 hover:to-secondary/5 transition-all duration-300 border border-slate-200 min-w-[180px] md:min-w-[220px]">
                                <div class="hidden md:flex flex-col items-start min-w-0">
                                    <span class="font-semibold text-sm text-slate-800 whitespace-nowrap"><?= $userName ?></span>
                                    <span class="text-xs text-slate-500 font-medium flex items-center gap-1">
                                        <?php if($isAdmin): ?>
                                            <i data-lucide="shield-check" class="w-3 h-3 text-primary"></i>
                                            <span>Admin</span>
                                        <?php else: ?>
                                            <i data-lucide="user-check" class="w-3 h-3 text-secondary"></i>
                                            <span>Member</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="relative flex-shrink-0 w-10 md:w-12">
                                    <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold shadow-md">
                                        <?= strtoupper(substr(session()->get('name') ?? 'G', 0, 1)) ?>
                                    </div>
                                    <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                                </div>
                            </button>

                            <!-- Enhanced Dropdown Menu -->
                            <div class="user-dropdown absolute right-0 mt-3 w-[520px] bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden opacity-0 invisible transform scale-95 transition-all duration-200">
                                <!-- User Info Header -->
                                <div class="px-5 py-4 bg-gradient-to-r from-primary/5 to-secondary/5 border-b border-slate-100">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold text-lg shadow-md">
                                            <?= strtoupper(substr(session()->get('name') ?? 'G', 0, 1)) ?>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-slate-800 truncate"><?= session()->get('name') ?? 'Guest' ?></p>
                                            <p class="text-xs text-slate-500 truncate"><?= $userEmail ?></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Menu Items -->
                                <div class="py-2 px-3">
                                    <div class="grid grid-cols-2">
                                        <a href="<?= base_url("ksp/dashboard") ?>" class="flex items-center gap-2 p-4 rounded-lg text-sm text-slate-700 hover:bg-slate-50 hover:text-primary transition-all group border border-transparent hover:border-slate-200">
                                            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                                                <i data-lucide="layout-grid" class="w-5 h-5 text-primary"></i>
                                            </div>
                                            <div class="flex-1 w-full">
                                                <div class="font-medium">Dashboard</div>
                                                <div class="text-xs text-slate-500 mt-0.5">View your dashboard</div>
                                            </div>
                                        </a>

                                        <a href="<?= base_url("ksp/learning-hub/profile") ?>" class="flex items-center gap-2 p-4 rounded-lg text-sm text-slate-700 hover:bg-slate-50 hover:text-primary transition-all group border border-transparent hover:border-slate-200">
                                            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                                                <i data-lucide="user" class="w-5 h-5 text-primary"></i>
                                            </div>
                                            <div class="flex-1 w-full">
                                                <div class="font-medium">My Profile</div>
                                                <div class="text-xs text-slate-500 mt-0.5">View and edit profile</div>
                                            </div>
                                        </a>

                                        <a href="<?= base_url("ksp/learning-hub/my-courses") ?>" class="flex items-center gap-2 p-4 rounded-lg text-sm text-slate-700 hover:bg-slate-50 hover:text-primary transition-all group border border-transparent hover:border-slate-200">
                                            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                                                <i data-lucide="book-open" class="w-5 h-5 text-blue-600"></i>
                                            </div>
                                            <div class="flex-1 w-full">
                                                <div class="font-medium">My Courses</div>
                                                <div class="text-xs text-slate-500 mt-0.5">View enrolled courses</div>
                                            </div>
                                        </a>

                                        <a href="<?= base_url("ksp/learning-hub/certificates") ?>" class="flex items-center gap-2 p-4 rounded-lg text-sm text-slate-700 hover:bg-slate-50 hover:text-primary transition-all group border border-transparent hover:border-slate-200">
                                            <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center group-hover:bg-green-100 transition-colors">
                                                <i data-lucide="award" class="w-5 h-5 text-green-600"></i>
                                            </div>
                                            <div class="flex-1 w-full">
                                                <div class="font-medium">My Certificates</div>
                                                <div class="text-xs text-slate-500 mt-0.5">View your certificates</div>
                                            </div>
                                        </a>

                                        <a href="<?= base_url("ksp/learning-hub/profile") ?>" class="flex items-center gap-2 p-4 rounded-lg text-sm text-slate-700 hover:bg-slate-50 hover:text-primary transition-all group border border-transparent hover:border-slate-200">
                                            <div class="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center group-hover:bg-secondary/20 transition-colors">
                                                <i data-lucide="settings" class="w-5 h-5 text-secondary"></i>
                                            </div>
                                            <div class="flex-1 w-full">
                                                <div class="font-medium">Settings</div>
                                                <div class="text-xs text-slate-500 mt-0.5">Manage preferences</div>
                                            </div>
                                        </a>

                                        <?php if($isKspSection): ?>
                                        <a href="<?= base_url('/') ?>" class="flex items-center gap-2 p-4 rounded-lg text-sm text-slate-700 hover:bg-slate-50 hover:text-primary transition-all group border border-transparent hover:border-slate-200">
                                            <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center group-hover:bg-purple-100 transition-colors">
                                                <i data-lucide="globe" class="w-5 h-5 text-purple-600"></i>
                                            </div>
                                            <div class="flex-1 w-full">
                                                <div class="font-medium">Main Site</div>
                                                <div class="text-xs text-slate-500 mt-0.5">Return to main website</div>
                                            </div>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Logout -->
                                <div class="border-t border-slate-100 p-2">
                                    <a href="<?= base_url("ksp/logout") ?>" class="flex items-center space-x-3 px-5 py-3 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-all group">
                                        <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center group-hover:bg-red-100 transition-colors">
                                            <i data-lucide="log-out" class="w-4 h-4 text-red-600"></i>
                                        </div>
                                        <span class="flex-1 font-medium">Logout</span>
                                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Guest User Menu -->
                    <div class="flex items-center space-x-3">
                        <a href="<?= base_url('ksp/login') ?>"
                           class="flex items-center justify-center gap-2 px-6 md:px-8 py-2.5 gradient-btn text-white rounded-xl font-medium shadow-md hover:shadow-lg transition-all duration-300 group">
                            <i data-lucide="log-in" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                            <span>Sign In</span>
                        </a>

                        <a href="<?= base_url('ksp/signup') ?>"
                           class="hidden md:flex items-center justify-center gap-2 px-6 py-2.5 border-2 border-primary text-primary rounded-xl font-medium hover:bg-primary hover:text-white transition-all duration-300 group">
                            <i data-lucide="user-plus" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                            <span>Sign Up</span>
                        </a>
                    </div>

                    <?php if($isKspSection): ?>
                        <!-- Back to Main Site Button (Mobile) -->
                        <a href="<?= base_url('/') ?>" class="md:hidden flex items-center justify-center w-10 h-10 rounded-full bg-slate-100 text-primary hover:bg-primary hover:text-white transition-all duration-200">
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>

                        <!-- Back to Main Site Button (Desktop) -->
                        <a href="<?= base_url('/') ?>"
                        class="hidden md:flex items-center justify-center gap-2 gradient-btn px-6 py-2.5 rounded-xl text-white font-medium shadow-md hover:shadow-lg transition-all duration-300 group">
                            <i data-lucide="globe" class="w-4 h-4"></i>
                            <span>Main Site</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<style>
    /* Enhanced Gradient Button */
    .gradient-btn {
        background: linear-gradient(135deg, #364391 0%, #27aae0 100%);
        background-size: 200% auto;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .gradient-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .gradient-btn:hover::before {
        left: 100%;
    }

    .gradient-btn:hover {
        background-position: right center;
        transform: translateY(-1px);
        box-shadow: 0 10px 25px -5px rgba(54, 67, 145, 0.3), 0 8px 10px -6px rgba(39, 170, 224, 0.2);
    }

    .gradient-btn:active {
        transform: translateY(0);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* User Menu Dropdown Interactions */
    .user-menu-wrapper:hover .user-dropdown {
        opacity: 1;
        visibility: visible;
        transform: scale(1);
    }

    .user-menu-trigger:hover {
        transform: translateY(-1px);
    }

    /* Smooth Icon Animations */
    [data-lucide] {
        transition: all 0.2s ease;
    }

    /* Online Status Pulse Animation */
    .user-menu-trigger .bg-green-500 {
        animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse-ring {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.6;
        }
    }

    /* Logo Hover Effect */
    header a img {
        filter: brightness(1);
        transition: filter 0.3s ease;
    }

    header a:hover img {
        filter: brightness(1.1);
    }

    /* Desktop Dropdown Width - Force width */
    .user-dropdown {
        width: 520px !important;
        min-width: 520px !important;
        max-width: 520px !important;
    }
    
    /* Mobile Menu Enhancements */
    @media (max-width: 768px) {
        .user-dropdown {
            right: 0;
            left: auto;
            width: calc(100vw - 2rem) !important;
            min-width: auto !important;
            max-width: 400px !important;
        }
        
        .user-dropdown .grid {
            grid-template-columns: 1fr;
        }
    }

    /* Subtle Header Shadow on Scroll */
    header {
        transition: box-shadow 0.3s ease;
    }

    header.scrolled {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    /* Better Focus States for Accessibility */
    .gradient-btn:focus,
    .user-menu-trigger:focus {
        outline: 2px solid #27aae0;
        outline-offset: 2px;
    }

    /* Dropdown Menu Animation */
    .user-dropdown {
        animation-duration: 0.2s;
        animation-fill-mode: both;
    }

    .user-menu-wrapper:hover .user-dropdown {
        animation-name: slideDown;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Enhanced Hover States */
    .user-dropdown a:hover [data-lucide] {
        transform: scale(1.1);
    }

    /* Smooth Transitions for All Interactive Elements */
    a, button {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Enhanced Sticky Header Styles */
    #main-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        width: 100%;
        z-index: 9999;
        will-change: transform;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
    }

    #main-header.scrolled {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    /* Add padding to body to prevent content from going under fixed header */
    body {
        padding-top: 72px; /* Adjust based on header height */
    }

    @media (max-width: 768px) {
        body {
            padding-top: 64px; /* Smaller padding for mobile */
        }
    }

    /* Ensure dropdowns appear above everything */
    .user-dropdown {
        z-index: 10000;
    }

    /* Smooth scroll behavior */
    html {
        scroll-behavior: smooth;
    }

    /* Prevent layout shift */
    #main-header * {
        box-sizing: border-box;
    }

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

<script>
    // Enhanced scroll effect for header with performance optimization
    let lastScrollTop = 0;
    let ticking = false;
    
    function updateHeader() {
        const header = document.getElementById('main-header');
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > 10) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        lastScrollTop = scrollTop;
        ticking = false;
    }
    
    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(updateHeader);
            ticking = true;
        }
    });

    // Set initial header padding on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
        // Adjust body padding to match header height dynamically
        const header = document.getElementById('main-header');
        if (header) {
            const headerHeight = header.offsetHeight;
            document.body.style.paddingTop = headerHeight + 'px';
            
            // Update on window resize
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    const newHeaderHeight = header.offsetHeight;
                    document.body.style.paddingTop = newHeaderHeight + 'px';
                }, 100);
            });
        }
        
        // Initial check for scrolled state
        if (window.scrollY > 10) {
            header.classList.add('scrolled');
        }
    });
</script>