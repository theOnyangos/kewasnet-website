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
    $settingsActive         = ($segments[1] === 'settings') ? $activeClass : '';
?>

<aside id="sidebar" class="h-screen bg-white shadow-lg flex flex-col transition-all duration-300 ease-in-out group" style="width: 280px">
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
    <nav class="flex-1 overflow-y-auto py-4">
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
                <a href="<?= base_url('auth/settings') ?>" class="<?= $settingsActive ?> flex items-center p-3 rounded-lg text-slate-700 hover:bg-secondaryShades-100 hover:text-primary transition-colors duration-200 group/minimized">
                    <i data-lucide="settings" class="w-5 h-5 mx-auto md:mr-3 md:ml-0 sidebar-icon"></i>
                    <span class="sidebar-label">Settings</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- Sidebar Footer -->
    <div class="p-4 border-t borderColor mt-auto">
        <div class="flex items-center space-x-3 overflow-hidden">
            <div class="w-10 h-10 rounded-full bg-secondaryShades-500 flex items-center justify-center text-white font-bold min-w-[40px]"><?= $initials ?></div>
            <div class="sidebar-footer-text">
                <div class="font-medium whitespace-nowrap"><?= $userName ?></div>
                <div class="text-sm text-slate-500 whitespace-nowrap">Admin</div>
            </div>
        </div>
    </div>
</aside>