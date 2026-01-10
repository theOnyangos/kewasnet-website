<!-- Dashboard Sidebar Menu -->
<?php 
    $uri = service('uri');
    $segments = $uri->getSegments();
    $activeClass = "sidebar-active";
    $userName = session()->get('first_name') . ' ' . session()->get('last_name') ?? 'Admin';
    $initials = strtoupper(substr($userName, 0, 2));

    $dashboardActive        = (empty($segments[1]) || $segments[1] === 'dashboard') ? $activeClass : '';
    $usersActive            = ($segments[1] === 'users') ? $activeClass : '';
    $partnersActive         = ($segments[1] === 'partners') ? $activeClass : '';
    $resourcesActive        = ($segments[1] === 'resources') ? $activeClass : '';
    $programsActive         = ($segments[1] === 'programs') ? $activeClass : '';
    $blogsActive            = ($segments[1] === 'blogs') ? $activeClass : '';
    $pillarsActive          = ($segments[1] === 'pillars') ? $activeClass : '';
    $opportunitiesActive    = ($segments[1] === 'opportunities') ? $activeClass : '';
    $forumsActive           = ($segments[1] === 'forums') ? $activeClass : '';
    $coursesActive          = ($segments[1] === 'courses') ? $activeClass : '';
    $eventsActive           = ($segments[1] === 'events') ? $activeClass : '';
    $settingsActive         = ($segments[1] === 'settings') ? $activeClass : '';
    
    // Dropdown menu active states - check if current route matches any route under these paths
    $currentSegment = $segments[1] ?? '';
    $isProfileActive = ($currentSegment === 'profile');
    $isAccountActive = ($currentSegment === 'account');
    $isLeadershipActive = ($currentSegment === 'leadership');
    $isSettingsActive = ($currentSegment === 'settings');
    $isPreferencesActive = ($currentSegment === 'preferences');
    $isSecurityActive = ($currentSegment === 'security');
    $isHelpActive = ($currentSegment === 'help');
    $isAboutActive = ($currentSegment === 'about');
    
    $profileDropdownActive = $isProfileActive ? 'bg-primary text-white font-medium' : 'text-slate-700 hover:bg-secondaryShades-100';
    $accountDropdownActive = $isAccountActive ? 'bg-primary text-white font-medium' : 'text-slate-700 hover:bg-secondaryShades-100';
    $leadershipDropdownActive = $isLeadershipActive ? 'bg-primary text-white font-medium' : 'text-slate-700 hover:bg-secondaryShades-100';
    $settingsDropdownActive = $isSettingsActive ? 'bg-primary text-white font-medium' : 'text-slate-700 hover:bg-secondaryShades-100';
    $preferencesDropdownActive = $isPreferencesActive ? 'bg-primary text-white font-medium' : 'text-slate-700 hover:bg-secondaryShades-100';
    $securityDropdownActive = $isSecurityActive ? 'bg-primary text-white font-medium' : 'text-slate-700 hover:bg-secondaryShades-100';
    $helpDropdownActive = $isHelpActive ? 'bg-primary text-white font-medium' : 'text-slate-700 hover:bg-secondaryShades-100';
    $aboutDropdownActive = $isAboutActive ? 'bg-primary text-white font-medium' : 'text-slate-700 hover:bg-secondaryShades-100';
?>

<aside id="sidebar" class="h-screen bg-white shadow-lg flex flex-col transition-all duration-300 ease-in-out" style="width: 280px; overflow: visible; position: relative; z-index: 1;">
    <!-- Sidebar Header -->
    <div class="p-4 flex items-center justify-between border-b borderColor">
        <div class="flex items-center space-x-3 overflow-hidden">
            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold min-w-[40px]">KD</div>
            <span class="font-semibold text-lg whitespace-nowrap">Dashboard</span>
        </div>
        <button id="toggleSidebar" class="text-slate-500 hover:text-primary">
            <i data-lucide="menu" class="w-5 h-5 sidebar-toggle-icon"></i>
        </button>
    </div>
    
    <!-- Sidebar Menu -->
    <nav class="flex-1 overflow-y-auto py-4" style="overflow-x: visible; overflow: -moz-scrollbars-vertical;">
        <ul class="space-y-1 px-2">
            <li class="relative">
                <a href="<?= base_url('auth/dashboard') ?>" class="<?= $dashboardActive ?> flex items-center p-3 rounded-lg text-slate-700 hover:bg-secondaryShades-100 hover:text-primary transition-colors duration-200 group/minimized">
                    <i data-lucide="home" class="w-5 h-5 mx-auto md:mr-3 md:ml-0 sidebar-icon"></i>
                    <span class="sidebar-label">Dashboard</span>
                </a>
            </li>
            <li class="relative">
                <a href="<?= base_url('auth/users') ?>" class="<?= $usersActive ?> flex items-center p-3 rounded-lg text-slate-700 hover:bg-secondaryShades-100 hover:text-primary transition-colors duration-200 group/minimized">
                    <i data-lucide="users" class="w-5 h-5 mx-auto md:mr-3 md:ml-0 sidebar-icon"></i>
                    <span class="sidebar-label">Users</span>
                </a>
            </li>
            <!-- Partners -->
            <li class="relative">
                <a href="<?= base_url('auth/partners') ?>" class="<?= $partnersActive ?> flex items-center p-3 rounded-lg text-slate-700 hover:bg-secondaryShades-100 hover:text-primary transition-colors duration-200 group/minimized">
                    <i data-lucide="users" class="w-5 h-5 mx-auto md:mr-3 md:ml-0 sidebar-icon"></i>
                    <span class="sidebar-label">Partners</span>
                </a>
            </li>
            <li class="relative">
                <a href="<?= base_url('auth/resources') ?>" class="<?= $resourcesActive ?> flex items-center p-3 rounded-lg text-slate-700 hover:bg-secondaryShades-100 hover:text-primary transition-colors duration-200 group/minimized">
                    <i data-lucide="book-text" class="w-5 h-5 mx-auto md:mr-3 md:ml-0 sidebar-icon"></i>
                    <span class="sidebar-label">Resources</span>
                </a>
            </li>
            <li class="relative">
                <a href="<?= base_url('auth/programs') ?>" class="<?= $programsActive ?> flex items-center p-3 rounded-lg text-slate-700 hover:bg-secondaryShades-100 hover:text-primary transition-colors duration-200 group/minimized">
                    <i data-lucide="calendar-check" class="w-5 h-5 mx-auto md:mr-3 md:ml-0 sidebar-icon"></i>
                    <span class="sidebar-label">Programs</span>
                </a>
            </li>
            <li class="relative">
                <a href="<?= base_url('auth/blogs') ?>" class="<?= $blogsActive ?> flex items-center p-3 rounded-lg text-slate-700 hover:bg-secondaryShades-100 hover:text-primary transition-colors duration-200 group/minimized">
                    <i data-lucide="newspaper" class="w-5 h-5 mx-auto md:mr-3 md:ml-0 sidebar-icon"></i>
                    <span class="sidebar-label">Blogs</span>
                </a>
            </li>
            <li class="relative">
                <a href="<?= base_url('auth/pillars') ?>" class="<?= $pillarsActive ?> flex items-center p-3 rounded-lg text-slate-700 hover:bg-secondaryShades-100 hover:text-primary transition-colors duration-200 group/minimized">
                    <i data-lucide="columns" class="w-5 h-5 mx-auto md:mr-3 md:ml-0 sidebar-icon"></i>
                    <span class="sidebar-label">Pillars</span>
                </a>
            </li>
            <li class="relative">
                <a href="<?= base_url('auth/opportunities') ?>" class="<?= $opportunitiesActive ?> flex items-center p-3 rounded-lg text-slate-700 hover:bg-secondaryShades-100 hover:text-primary transition-colors duration-200 group/minimized">
                    <i data-lucide="briefcase" class="w-5 h-5 mx-auto md:mr-3 md:ml-0 sidebar-icon"></i>
                    <span class="sidebar-label">Opportunities</span>
                </a>
            </li>
            <li class="relative">
                <a href="<?= base_url('auth/forums') ?>" class="<?= $forumsActive ?> flex items-center p-3 rounded-lg text-slate-700 hover:bg-secondaryShades-100 hover:text-primary transition-colors duration-200 group/minimized">
                    <i data-lucide="messages-square" class="w-5 h-5 mx-auto md:mr-3 md:ml-0 sidebar-icon"></i>
                    <span class="sidebar-label">Forums</span>
                </a>
            </li>
            <li class="relative">
                <a href="<?= base_url('auth/courses') ?>" class="<?= $coursesActive ?> flex items-center p-3 rounded-lg text-slate-700 hover:bg-secondaryShades-100 hover:text-primary transition-colors duration-200 group/minimized">
                    <i data-lucide="graduation-cap" class="w-5 h-5 mx-auto md:mr-3 md:ml-0 sidebar-icon"></i>
                    <span class="sidebar-label">Courses</span>
                </a>
            </li>
            <li class="relative">
                <a href="<?= base_url('auth/events') ?>" class="<?= $eventsActive ?> flex items-center p-3 rounded-lg text-slate-700 hover:bg-secondaryShades-100 hover:text-primary transition-colors duration-200 group/minimized">
                    <i data-lucide="calendar" class="w-5 h-5 mx-auto md:mr-3 md:ml-0 sidebar-icon"></i>
                    <span class="sidebar-label">Events</span>
                </a>
            </li>
            <li class="relative">
                <a href="<?= base_url('auth/settings') ?>" class="<?= $settingsActive ?> flex items-center p-3 rounded-lg text-slate-700 hover:bg-secondaryShades-100 hover:text-primary transition-colors duration-200 group/minimized">
                    <i data-lucide="settings" class="w-5 h-5 mx-auto md:mr-3 md:ml-0 sidebar-icon"></i>
                    <span class="sidebar-label">Settings</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- Sidebar Footer -->
    <div class="p-4 border-t borderColor mt-auto relative" id="userProfileSection" style="z-index: 1000; overflow: visible;">
        <button id="userProfileBtn" type="button" class="w-full flex items-center space-x-3 overflow-hidden hover:bg-slate-50 rounded-lg p-2 -m-2 transition-colors cursor-pointer focus:outline-none focus:ring-0 focus:border-0 border-0 outline-none" style="outline: none !important; border: none !important; box-shadow: none !important;">
            <div class="w-10 h-10 rounded-full bg-secondaryShades-500 flex items-center justify-center text-white font-bold min-w-[40px] flex-shrink-0"><?= $initials ?></div>
            <div class="sidebar-footer-text flex-1 text-left min-w-0">
                <div class="font-medium whitespace-nowrap truncate"><?= $userName ?></div>
                <div class="text-sm text-slate-500 whitespace-nowrap truncate">Admin</div>
            </div>
            <i data-lucide="chevron-up" class="w-4 h-4 text-slate-400 sidebar-profile-chevron transition-transform flex-shrink-0"></i>
        </button>
        
        <!-- User Profile Dropdown Menu -->
        <div id="userProfileDropdown" class="hidden fixed bg-white border borderColor rounded-lg shadow-xl min-w-[280px]" style="z-index: 99999; display: none; visibility: hidden; opacity: 0; pointer-events: none; position: fixed;">
            <div class="pb-2" style="overflow-y: auto; max-height: 600px;">
                <!-- User Info Header -->
                <div class="px-4 py-3 border-b borderColor bg-primary bg-opacity-10">
                    <div class="font-medium text-primary"><?= $userName ?></div>
                    <div class="text-sm text-slate-500"><?= session()->get('email') ?? 'admin@kewasnet.org' ?></div>
                </div>
                
                <!-- Menu Items -->
                <div class="py-1">
                    <a href="<?= base_url('auth/profile') ?>" class="flex items-center px-4 py-2 text-sm <?= $profileDropdownActive ?> transition-colors hover:text-primary">
                        <i data-lucide="user" class="w-4 h-4 mr-3 <?= $isProfileActive ? 'text-primary' : 'text-slate-500' ?>"></i>
                        <span>Admin Profile</span>
                    </a>
                    <a href="<?= base_url('auth/account') ?>" class="flex items-center px-4 py-2 text-sm <?= $accountDropdownActive ?> transition-colors hover:text-primary">
                        <i data-lucide="user-cog" class="w-4 h-4 mr-3 <?= $isAccountActive ? 'text-primary' : 'text-slate-500' ?>"></i>
                        <span>Manage Account</span>
                    </a>
                    <a href="<?= base_url('auth/leadership') ?>" class="flex items-center px-4 py-2 text-sm <?= $leadershipDropdownActive ?> transition-colors hover:text-primary">
                        <i data-lucide="users" class="w-4 h-4 mr-3 <?= $isLeadershipActive ? 'text-primary' : 'text-slate-500' ?>"></i>
                        <span>Manage Leadership</span>
                    </a>
                    <a href="<?= base_url('auth/settings') ?>" class="flex items-center px-4 py-2 text-sm <?= $settingsDropdownActive ?> transition-colors hover:text-primary">
                        <i data-lucide="settings" class="w-4 h-4 mr-3 <?= $isSettingsActive ? 'text-primary' : 'text-slate-500' ?>"></i>
                        <span>Settings</span>
                    </a>
                    <a href="<?= base_url('auth/preferences') ?>" class="flex items-center px-4 py-2 text-sm <?= $preferencesDropdownActive ?> transition-colors hover:text-primary">
                        <i data-lucide="sliders" class="w-4 h-4 mr-3 <?= $isPreferencesActive ? 'text-primary' : 'text-slate-500' ?>"></i>
                        <span>Preferences</span>
                    </a>
                    <a href="<?= base_url('auth/security') ?>" class="flex items-center px-4 py-2 text-sm <?= $securityDropdownActive ?> transition-colors hover:text-primary">
                        <i data-lucide="shield" class="w-4 h-4 mr-3 <?= $isSecurityActive ? 'text-primary' : 'text-slate-500' ?>"></i>
                        <span>Security</span>
                    </a>
                    <div class="border-t borderColor my-1"></div>
                    <a href="<?= base_url('auth/help') ?>" class="flex items-center px-4 py-2 text-sm <?= $helpDropdownActive ?> transition-colors hover:text-primary">
                        <i data-lucide="help-circle" class="w-4 h-4 mr-3 <?= $isHelpActive ? 'text-primary' : 'text-slate-500' ?>"></i>
                        <span>Help & Support</span>
                    </a>
                    <a href="<?= base_url('auth/about') ?>" class="flex items-center px-4 py-2 text-sm <?= $aboutDropdownActive ?> transition-colors hover:text-primary">
                        <i data-lucide="info" class="w-4 h-4 mr-3 <?= $isAboutActive ? 'text-primary' : 'text-slate-500' ?>"></i>
                        <span>About</span>
                    </a>
                    <div class="border-t borderColor my-1"></div>
                    <a href="<?= base_url('auth/logout') ?>" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                        <i data-lucide="log-out" class="w-4 h-4 mr-3"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</aside>

<script>
    // User Profile Dropdown Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const profileBtn = document.getElementById('userProfileBtn');
        const dropdown = document.getElementById('userProfileDropdown');
        const profileSection = document.getElementById('userProfileSection');
        
        if (!profileBtn || !dropdown) {
            return;
        }
        
        const chevron = profileBtn.querySelector('.sidebar-profile-chevron');
        let isOpen = false;
        
        // Prevent hover from showing dropdown
        profileSection.addEventListener('mouseenter', function(e) {
            e.stopPropagation();
        });
        
        profileSection.addEventListener('mouseleave', function(e) {
            e.stopPropagation();
        });
        
        // Function to calculate dropdown position
        function calculateDropdownPosition() {
            const btnRect = profileBtn.getBoundingClientRect();
            const viewportHeight = window.innerHeight;
            const viewportWidth = window.innerWidth;
            
            // Get actual dropdown dimensions (should be visible when called)
            const dropdownHeight = dropdown.offsetHeight || 600; // Fallback estimate
            const dropdownWidth = dropdown.offsetWidth || Math.max(btnRect.width, 280);
            
            // Position above the button by default
            let top = btnRect.top - dropdownHeight - 8; // 8px gap
            let left = btnRect.left;
            let width = Math.max(dropdownWidth, btnRect.width, 280); // Minimum width 280px
            
            // Ensure dropdown doesn't go off the right edge of the screen
            if (left + width > viewportWidth - 16) {
                left = viewportWidth - width - 16; // Leave 16px margin from right
            }
            
            // Ensure dropdown doesn't go off the left edge of the screen
            if (left < 16) {
                left = 16; // Leave 16px margin from left
                // Adjust width if needed to fit
                width = Math.min(width, viewportWidth - 32);
            }
            
            // Check if dropdown would go off the top of the screen
            if (top < 8) {
                // Position below the button instead
                top = btnRect.bottom + 8;
                const maxHeightBelow = viewportHeight - top - 16; // Leave margin from bottom
                const innerContainer = dropdown.querySelector('.py-2');
                if (innerContainer) {
                    innerContainer.style.maxHeight = Math.min(maxHeightBelow, 600) + 'px';
                }
            } else {
                // Position above - ensure it doesn't go off screen
                const maxHeightAbove = btnRect.top - 16; // Leave some margin from top
                const innerContainer = dropdown.querySelector('.py-2');
                if (innerContainer) {
                    innerContainer.style.maxHeight = Math.min(maxHeightAbove, 600) + 'px';
                }
            }
            
            // Set position and dimensions
            dropdown.style.position = 'fixed';
            dropdown.style.top = top + 'px';
            dropdown.style.left = left + 'px';
            dropdown.style.width = width + 'px';
        }
        
        // Toggle dropdown on button click
        profileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            isOpen = !isOpen;
            
            if (isOpen) {
                // Move dropdown to body to ensure it's not constrained by sidebar overflow
                if (dropdown.parentElement !== document.body) {
                    document.body.appendChild(dropdown);
                }
                
                // Show dropdown first (temporarily) to measure it
                dropdown.classList.remove('hidden');
                dropdown.style.display = 'block';
                dropdown.style.visibility = 'hidden'; // Still hidden for measurement
                dropdown.style.position = 'fixed';
                dropdown.style.top = '-9999px';
                dropdown.style.left = '-9999px';
                dropdown.style.zIndex = '99999';
                
                // Force a reflow to ensure dimensions are calculated
                void dropdown.offsetHeight;
                
                // Calculate position now that we can measure
                calculateDropdownPosition();
                
                // Make it fully visible
                dropdown.style.visibility = 'visible';
                dropdown.style.opacity = '1';
                dropdown.style.pointerEvents = 'auto';
                
                // Recalculate position after a brief delay (in case content shifts)
                setTimeout(function() {
                    calculateDropdownPosition();
                }, 50);
                
                if (chevron) {
                    chevron.style.transform = 'rotate(180deg)';
                }
            } else {
                // Hide dropdown
                dropdown.classList.add('hidden');
                dropdown.style.display = 'none';
                dropdown.style.visibility = 'hidden';
                dropdown.style.opacity = '0';
                dropdown.style.pointerEvents = 'none';
                
                // Move dropdown back to original position (optional, but cleaner)
                // We'll keep it in body for next time to avoid DOM manipulation overhead
                
                if (chevron) {
                    chevron.style.transform = 'rotate(0deg)';
                }
            }
        });
        
        // Recalculate position on window resize or scroll
        window.addEventListener('resize', function() {
            if (isOpen) {
                calculateDropdownPosition();
            }
        });
        
        // Recalculate position on scroll (in case sidebar or page scrolls)
        window.addEventListener('scroll', function() {
            if (isOpen) {
                calculateDropdownPosition();
            }
        }, true); // Use capture phase to catch all scroll events
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (isOpen) {
                // Check if click is outside both the button and the dropdown
                const clickedInsideButton = profileBtn.contains(e.target);
                const clickedInsideDropdown = dropdown.contains(e.target);
                
                if (!clickedInsideButton && !clickedInsideDropdown) {
                    isOpen = false;
                    dropdown.classList.add('hidden');
                    dropdown.style.display = 'none';
                    dropdown.style.visibility = 'hidden';
                    dropdown.style.opacity = '0';
                    dropdown.style.pointerEvents = 'none';
                    if (chevron) {
                        chevron.style.transform = 'rotate(0deg)';
                    }
                }
            }
        });
        
        // Prevent dropdown from closing when clicking inside it
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // Close dropdown when clicking on a menu item (but allow navigation)
        dropdown.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function(e) {
                // Don't prevent default - allow navigation
                // Just close the dropdown after a short delay
                setTimeout(function() {
                    isOpen = false;
                    dropdown.classList.add('hidden');
                    dropdown.style.display = 'none';
                    dropdown.style.visibility = 'hidden';
                    dropdown.style.opacity = '0';
                    dropdown.style.pointerEvents = 'none';
                    if (chevron) {
                        chevron.style.transform = 'rotate(0deg)';
                    }
                }, 100);
            });
        });
        
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>