<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/ksp/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<!-- Hero Section -->
<section class="school-header py-20 text-white">
    <div class="container mx-auto px-4 text-center">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Welcome to the KEWASNET Learning Hub</h1>
            <p class="text-xl md:text-2xl mb-8 leading-relaxed">
                Expand your knowledge with our comprehensive courses on water, sanitation, and environmental management.
            </p>
            
            <!-- Search Box -->
            <div class="max-w-2xl mx-auto mt-10">
                <form method="GET" action="<?= base_url('ksp/learning-hub') ?>" class="search-box bg-white rounded-full p-2 flex items-center">
                    <input type="text" 
                           name="search"
                           id="searchInput"
                           placeholder="Search courses, topics, or keywords..." 
                           value="<?= esc($search ?? '') ?>"
                           class="flex-1 px-6 py-3 border-0 focus:ring-0 focus:outline-none rounded-full">
                    <button type="submit" class="gradient-btn p-3 rounded-full text-white">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </button>
                </form>
                <p class="text-sm text-slate-300 mt-3">Try: "water conservation" or "climate adaptation"</p>
            </div>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<div class="bg-white border-b borderColor">
    <div class="container mx-auto px-4 py-3">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="/" class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-primary">
                        <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                        Home
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                        <span class="ml-1 text-sm font-medium text-primary md:ml-2">Learning Hub</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<!-- Main Content -->
<main class="container mx-auto px-4 py-12">
    <!-- Pillars Section -->
    <section class="mb-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-dark mb-4">Explore Our Learning Pillars</h2>
            <p class="text-lg text-slate-600 max-w-3xl mx-auto">
                Our courses are organized around five key thematic areas that form the foundation of our knowledge platform.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <!-- WASH -->
            <a href="<?= base_url('ksp/learning-hub?category=1') ?>" class="pillar-category bg-white rounded-lg overflow-hidden shadow-sm border borderColor hover:shadow-md transition-shadow">
                <div class="p-6 text-center">
                    <div class="bg-secondaryShades-100 p-4 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="droplets" class="w-10 h-10 text-secondary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-3">WASH</h3>
                    <p class="text-slate-600 mb-4">
                        Water, Sanitation and Hygiene courses covering best practices and innovative solutions.
                    </p>
                    <span class="inline-block bg-secondaryShades-100 text-secondary text-sm px-3 py-1 rounded-full">
                        WASH Courses
                    </span>
                </div>
            </a>
            
            <!-- Climate Change -->
            <a href="<?= base_url('ksp/learning-hub?category=2') ?>" class="pillar-category bg-white rounded-lg overflow-hidden shadow-sm border borderColor hover:shadow-md transition-shadow">
                <div class="p-6 text-center">
                    <div class="bg-secondaryShades-100 p-4 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="cloud-sun" class="w-10 h-10 text-secondary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-3">Climate Change</h3>
                    <p class="text-slate-600 mb-4">
                        Understanding climate impacts and adaptation strategies for water systems.
                    </p>
                    <span class="inline-block bg-secondaryShades-100 text-secondary text-sm px-3 py-1 rounded-full">
                        Climate Courses
                    </span>
                </div>
            </a>
            
            <!-- Governance -->
            <a href="<?= base_url('ksp/learning-hub?category=3') ?>" class="pillar-category bg-white rounded-lg overflow-hidden shadow-sm border borderColor hover:shadow-md transition-shadow">
                <div class="p-6 text-center">
                    <div class="bg-secondaryShades-100 p-4 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="scale" class="w-10 h-10 text-secondary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-3">Governance</h3>
                    <p class="text-slate-600 mb-4">
                        Policy, regulation and institutional frameworks for effective water management.
                    </p>
                    <span class="inline-block bg-secondaryShades-100 text-secondary text-sm px-3 py-1 rounded-full">
                        Governance Courses
                    </span>
                </div>
            </a>
            
            <!-- NEXUS -->
            <a href="<?= base_url('ksp/learning-hub?category=4') ?>" class="pillar-category bg-white rounded-lg overflow-hidden shadow-sm border borderColor hover:shadow-md transition-shadow">
                <div class="p-6 text-center">
                    <div class="bg-secondaryShades-100 p-4 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="link" class="w-10 h-10 text-secondary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-3">NEXUS</h3>
                    <p class="text-slate-600 mb-4">
                        Integrated approaches to water, energy and food security.
                    </p>
                    <span class="inline-block bg-secondaryShades-100 text-secondary text-sm px-3 py-1 rounded-full">
                        NEXUS Courses
                    </span>
                </div>
            </a>
            
            <!-- IWRM -->
            <a href="<?= base_url('ksp/learning-hub?category=5') ?>" class="pillar-category bg-white rounded-lg overflow-hidden shadow-sm border borderColor hover:shadow-md transition-shadow">
                <div class="p-6 text-center">
                    <div class="bg-secondaryShades-100 p-4 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="globe" class="w-10 h-10 text-secondary"></i>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-3">IWRM</h3>
                    <p class="text-slate-600 mb-4">
                        Integrated Water Resources Management principles and applications.
                    </p>
                    <span class="inline-block bg-secondaryShades-100 text-secondary text-sm px-3 py-1 rounded-full">
                        IWRM Courses
                    </span>
                </div>
            </a>
        </div>
    </section>
    
    <!-- Course Catalog Section -->
    <section class="mb-16">
        <!-- Filters and Search -->
        <div class="bg-white rounded-lg border border-slate-200 p-6 mb-8">
            <form method="GET" action="<?= base_url('ksp/learning-hub') ?>" id="courseFilterForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-slate-700 mb-2">Search Courses</label>
                        <div class="relative">
                            <input type="text" 
                                   id="search" 
                                   name="search" 
                                   value="<?= esc($search ?? '') ?>"
                                   placeholder="Search by title, description..." 
                                   class="w-full px-4 py-2 pl-10 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <i data-lucide="search" class="absolute left-3 top-3 w-4 h-4 text-slate-400"></i>
                        </div>
                    </div>
                    
                    <!-- Filter Type -->
                    <div>
                        <label for="filter" class="block text-sm font-medium text-slate-700 mb-2">Course Type</label>
                        <select id="filter" name="filter" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Courses</option>
                            <option value="free" <?= $filter === 'free' ? 'selected' : '' ?>>Free Courses</option>
                            <option value="paid" <?= $filter === 'paid' ? 'selected' : '' ?>>Paid Courses</option>
                        </select>
                    </div>
                    
                    <!-- Level -->
                    <div>
                        <label for="level" class="block text-sm font-medium text-slate-700 mb-2">Level</label>
                        <select id="level" name="level" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">All Levels</option>
                            <option value="beginner" <?= $level === 'beginner' ? 'selected' : '' ?>>Beginner</option>
                            <option value="intermediate" <?= $level === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
                            <option value="advanced" <?= $level === 'advanced' ? 'selected' : '' ?>>Advanced</option>
                        </select>
                    </div>
                </div>
                
                <!-- Category Filter (if needed) -->
                <?php if ($category): ?>
                    <input type="hidden" name="category" value="<?= esc($category) ?>">
                <?php endif; ?>
                
                <div class="flex justify-between items-center">
                    <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-dark transition-colors font-medium">
                        Apply Filters
                    </button>
                    <?php if ($search || $filter !== 'all' || $level || $category): ?>
                        <a href="<?= base_url('ksp/learning-hub') ?>" class="text-slate-600 hover:text-primary text-sm font-medium">
                            Clear Filters
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Results Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h2 class="text-3xl font-bold text-dark mb-2">Course Catalog</h2>
                <p class="text-slate-600">
                    Showing <?= count($courses) ?> of <?= $pagination['total_courses'] ?? count($courses) ?> courses
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="<?= base_url('ksp/learning-hub/courses') ?>" class="inline-flex items-center bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-dark transition-colors font-medium">
                    View All Courses
                    <i data-lucide="arrow-right" class="ml-2 w-4 h-4"></i>
                </a>
            </div>
        </div>

        <!-- Courses Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (!isset($courses)): ?>
                <div class="col-span-full text-center py-12 bg-red-50 p-4 rounded">
                    <p class="text-red-600">Error: $courses variable is not set</p>
                </div>
            <?php elseif (empty($courses)): ?>
                <div class="col-span-full text-center py-12">
                    <i data-lucide="book-open" class="w-16 h-16 text-slate-400 mx-auto mb-4"></i>
                    <p class="text-slate-600 text-lg">No courses found. Try adjusting your filters.</p>
                </div>
            <?php else: ?>
                <?php foreach ($courses as $course): ?>
                    <?php 
                    // Extract course data for the component
                    $courseData = $course;
                    // Include the component - variables should be available directly in CI4
                    echo view('frontendV2/ksp/pages/learning-hub/components/course-card', ['course' => $courseData]);
                    ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
            <div class="mt-12 flex flex-col items-center">
                <nav aria-label="Course pagination" class="flex items-center space-x-2">
                    <?php if ($pagination['has_previous']): ?>
                        <a href="<?= $pagination['previous_url'] ?>" 
                           class="flex items-center justify-center w-10 h-10 border border-slate-300 rounded-lg text-slate-600 hover:bg-primary hover:text-white hover:border-primary transition-colors">
                            <i data-lucide="chevron-left" class="w-5 h-5"></i>
                        </a>
                    <?php else: ?>
                        <span class="flex items-center justify-center w-10 h-10 border border-slate-200 rounded-lg text-slate-400 cursor-not-allowed">
                            <i data-lucide="chevron-left" class="w-5 h-5"></i>
                        </span>
                    <?php endif; ?>
                    
                    <?php 
                    // Show page numbers (max 7 pages shown)
                    $startPage = max(1, $pagination['current_page'] - 3);
                    $endPage = min($pagination['total_pages'], $pagination['current_page'] + 3);
                    
                    if ($startPage > 1): ?>
                        <a href="<?= str_replace('page=' . $pagination['current_page'], 'page=1', $pagination['previous_url'] ?? '') ?: (base_url('ksp/learning-hub') . '?page=1') ?>" 
                           class="flex items-center justify-center w-10 h-10 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 transition-colors">
                            1
                        </a>
                        <?php if ($startPage > 2): ?>
                            <span class="px-2 text-slate-400">...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <?php
                        $pageUrl = base_url('ksp/learning-hub');
                        $queryParams = http_build_query(array_filter([
                            'filter' => $filter !== 'all' ? $filter : null,
                            'category' => $category,
                            'level' => $level,
                            'search' => $search,
                            'page' => $i,
                        ]));
                        $pageUrl = $queryParams ? $pageUrl . '?' . $queryParams : $pageUrl;
                        ?>
                        <a href="<?= $pageUrl ?>" 
                           class="flex items-center justify-center w-10 h-10 border rounded-lg text-sm font-medium transition-colors <?= $i === $pagination['current_page'] ? 'bg-primary text-white border-primary' : 'border-slate-300 text-slate-600 hover:bg-slate-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($endPage < $pagination['total_pages']): ?>
                        <?php if ($endPage < $pagination['total_pages'] - 1): ?>
                            <span class="px-2 text-slate-400">...</span>
                        <?php endif; ?>
                        <?php
                        $lastPageUrl = base_url('ksp/learning-hub');
                        $queryParams = http_build_query(array_filter([
                            'filter' => $filter !== 'all' ? $filter : null,
                            'category' => $category,
                            'level' => $level,
                            'search' => $search,
                            'page' => $pagination['total_pages'],
                        ]));
                        $lastPageUrl = $queryParams ? $lastPageUrl . '?' . $queryParams : $lastPageUrl;
                        ?>
                        <a href="<?= $lastPageUrl ?>" 
                           class="flex items-center justify-center w-10 h-10 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 transition-colors">
                            <?= $pagination['total_pages'] ?>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($pagination['has_next']): ?>
                        <a href="<?= $pagination['next_url'] ?>" 
                           class="flex items-center justify-center w-10 h-10 border border-slate-300 rounded-lg text-slate-600 hover:bg-primary hover:text-white hover:border-primary transition-colors">
                            <i data-lucide="chevron-right" class="w-5 h-5"></i>
                        </a>
                    <?php else: ?>
                        <span class="flex items-center justify-center w-10 h-10 border border-slate-200 rounded-lg text-slate-400 cursor-not-allowed">
                            <i data-lucide="chevron-right" class="w-5 h-5"></i>
                        </span>
                    <?php endif; ?>
                </nav>
                
                <p class="mt-4 text-sm text-slate-600">
                    Page <?= $pagination['current_page'] ?> of <?= $pagination['total_pages'] ?>
                </p>
            </div>
        <?php endif; ?>
    </section>
    
    <!-- Learning Experience Section -->
    <section class="mb-16 bg-white rounded-xl overflow-hidden border borderColor">
        <div class="flex flex-col lg:flex-row">
            <div class="lg:w-1/2 p-8 md:p-12">
                <h2 class="text-3xl font-bold text-dark mb-6">Our Learning Experience</h2>
                
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="bg-secondaryShades-100 p-3 rounded-full flex-shrink-0">
                            <i data-lucide="monitor" class="w-6 h-6 text-secondary"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-dark mb-2">Interactive Modules</h3>
                            <p class="text-slate-600">
                                Engaging multimedia content with videos, quizzes, and interactive exercises to enhance learning.
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="bg-secondaryShades-100 p-3 rounded-full flex-shrink-0">
                            <i data-lucide="award" class="w-6 h-6 text-secondary"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-dark mb-2">Certification</h3>
                            <p class="text-slate-600">
                                Earn recognized certificates upon course completion to showcase your expertise.
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="bg-secondaryShades-100 p-3 rounded-full flex-shrink-0">
                            <i data-lucide="users" class="w-6 h-6 text-secondary"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-dark mb-2">Expert Instructors</h3>
                            <p class="text-slate-600">
                                Learn from leading practitioners and academics in the water and sanitation sector.
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="bg-secondaryShades-100 p-3 rounded-full flex-shrink-0">
                            <i data-lucide="calendar" class="w-6 h-6 text-secondary"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-dark mb-2">Self-Paced Learning</h3>
                            <p class="text-slate-600">
                                Study at your own convenience with 24/7 access to all course materials.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="lg:w-1/2 bg-slate-50 p-8 md:p-12 flex items-center justify-center">
                <div class="notebook-paper max-w-md w-full rounded-lg border borderColor">
                    <h3 class="font-bold text-lg mb-4">Today's Learning Objectives</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5"></i>
                            <span>Understand the principles of integrated water resources management</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5"></i>
                            <span>Explore climate change adaptation strategies for water utilities</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5"></i>
                            <span>Learn participatory approaches to community water management</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5"></i>
                            <span>Analyze case studies of successful WASH programs</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="check-circle" class="w-5 h-5 text-secondary mr-2 mt-0.5"></i>
                            <span>Develop skills for water policy analysis and advocacy</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Testimonials -->
    <section class="mb-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-dark mb-4">What Our Learners Say</h2>
            <p class="text-lg text-slate-600 max-w-3xl mx-auto">
                Hear from professionals who have enhanced their skills through our courses.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg border borderColor">
                <div class="flex items-center mb-4">
                    <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Learner" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-bold">Sarah Johnson</h4>
                        <p class="text-sm text-slate-600">WASH Coordinator</p>
                    </div>
                </div>
                <p class="text-slate-600 mb-4">
                    "The Community-Led Total Sanitation course transformed our approach to hygiene promotion. We've seen a 40% reduction in open defecation in our target communities."
                </p>
                <div class="flex text-yellow-400">
                    <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg border borderColor">
                <div class="flex items-center mb-4">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Learner" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-bold">David Ochieng</h4>
                        <p class="text-sm text-slate-600">Water Engineer</p>
                    </div>
                </div>
                <p class="text-slate-600 mb-4">
                    "The IWRM courses provided practical tools I've applied in developing our county's water master plan. The certification helped me secure a promotion."
                </p>
                <div class="flex text-yellow-400">
                    <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg border borderColor">
                <div class="flex items-center mb-4">
                    <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Learner" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-bold">Grace Mwende</h4>
                        <p class="text-sm text-slate-600">Climate Officer</p>
                    </div>
                </div>
                <p class="text-slate-600 mb-4">
                    "The climate change modules helped our team develop more resilient water projects. The self-paced format allowed me to balance learning with work commitments."
                </p>
                <div class="flex text-yellow-400">
                    <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Call to Action -->
<section class="bg-gradient-to-r from-primary to-secondary overflow-hidden p-8 md:p-12 text-white">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl font-bold mb-6">Start Learning Today</h2>
        <p class="text-xl mb-8 leading-relaxed">
            Join thousands of water and sanitation professionals enhancing their skills through our comprehensive courses.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="<?= base_url('ksp/learning-hub') ?>" class="bg-white px-8 py-3 rounded-[50px] text-primary font-medium text-lg flex items-center">
                Browse Courses
                <i data-lucide="arrow-right" class="ml-2 w-5 h-5"></i>
            </a>
            <a href="<?= base_url('ksp/signup') ?>" class="border-2 border-white px-8 py-3 rounded-[50px] text-white font-medium text-lg flex items-center">
                Create Account
                <i data-lucide="user-plus" class="ml-2 w-5 h-5"></i>
            </a>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    lucide.createIcons();
    
    // Form submission - filters will be applied via form submit
    // Optional: Auto-submit on filter change (uncomment if desired)
    // $('#filter, #level').on('change', function() {
    //     $('#courseFilterForm').submit();
    // });
    
    // Search on Enter key
    $('#search').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#courseFilterForm').submit();
        }
    });
    
    // Initialize GSAP animations if available
    if (typeof gsap !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
        
        // Animate course cards
        gsap.utils.toArray(".course-card, .bg-white.rounded-lg").forEach((card, i) => {
            gsap.from(card, {
                scrollTrigger: {
                    trigger: card,
                    start: "top 85%",
                    toggleActions: "play none none none"
                },
                y: 30,
                opacity: 0,
                duration: 0.5,
                delay: i * 0.05,
                ease: "power2.out"
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
