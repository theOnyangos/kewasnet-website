<?= $this->extend('frontendV2/website/layouts/main') ?>

<!--  Section Meta Tags  -->
<?= $this->section('meta') ?>
<meta name="description" content="<?= esc($description) ?>">
<meta name="keywords" content="KEWASNET, job opportunities, WASH sector, Kenya, career, explore jobs">
<meta name="author" content="KEWASNET">

<!-- Open Graph Meta Tags -->
<meta property="og:title" content="<?= esc($title) ?>">
<meta property="og:description" content="<?= esc($description) ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= current_url() ?>">

<!-- Twitter Card Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= esc($title) ?>">
<meta name="twitter:description" content="<?= esc($description) ?>">
<?= $this->endSection() ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-primary to-secondary text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Explore All Opportunities</h1>
            <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                Discover your next career opportunity in Kenya's WASH sector. Filter, search, and find the perfect match for your skills and aspirations.
            </p>
            <div class="bg-white rounded-lg p-2 max-w-2xl mx-auto">
                <div class="flex">
                    <input type="text" id="globalSearch" placeholder="Search opportunities..." 
                           value="<?= esc($filters['search'] ?? '') ?>"
                           class="flex-1 px-4 py-3 text-dark rounded-l-md border-none outline-none">
                    <button id="searchBtn" class="bg-primary text-white px-6 py-3 rounded-r-md hover:bg-primaryShades-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Breadcrumb Section -->
    <section class="bg-lightGray py-6">
        <div class="container mx-auto px-4">
            <nav class="text-sm">
                <ol class="list-none p-0 inline-flex">
                    <li class="flex items-center">
                        <a href="<?= site_url('/') ?>" class="text-primary hover:text-primaryShades-700">Home</a>
                        <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                            <path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.476 239.03c9.373 9.372 9.373 24.568 0 33.941z"/>
                        </svg>
                    </li>
                    <li class="flex items-center">
                        <a href="<?= site_url('opportunities') ?>" class="text-primary hover:text-primaryShades-700">Opportunities</a>
                        <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                            <path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.476 239.30c9.373 9.372 9.373 24.568 0 33.941z"/>
                        </svg>
                    </li>
                    <li class="text-slate-500">Explore All</li>
                </ol>
            </nav>
            
            <!-- Back Button -->
            <div class="mt-4">
                <a href="<?= site_url('opportunities') ?>" 
                   class="inline-flex items-center text-primary hover:text-primaryShades-700 font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Opportunities
                </a>
            </div>
        </div>
    </section>

    <!-- Filters and Results Section -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-4 gap-8">
                <!-- Filters Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-lightGray p-6 rounded-lg sticky top-8">
                        <h3 class="text-lg font-bold text-primaryShades-800 mb-6">Filter Opportunities</h3>
                        
                        <!-- Opportunity Type Filter -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-dark mb-3">Opportunity Type</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="all" <?= empty($filters['type']) || $filters['type'] === 'all' ? 'checked' : '' ?>
                                           class="text-primary focus:ring-primary">
                                    <span class="ml-2 text-sm">All Types</span>
                                </label>
                                <?php foreach ($opportunityTypes as $typeOption): ?>
                                    <label class="flex items-center">
                                        <input type="radio" name="type" value="<?= esc($typeOption['opportunity_type']) ?>" 
                                               <?= $filters['type'] === $typeOption['opportunity_type'] ? 'checked' : '' ?>
                                               class="text-primary focus:ring-primary">
                                        <span class="ml-2 text-sm"><?= ucfirst(str_replace('-', ' ', $typeOption['opportunity_type'])) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Location Filter -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-dark mb-3">Location</label>
                            <select name="location" class="select2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="">All Locations</option>
                                <option value="remote" <?= $filters['location'] === 'remote' ? 'selected' : '' ?>>Remote Work</option>
                                <?php foreach ($locations as $locationOption): ?>
                                    <?php if (!empty($locationOption['location'])): ?>
                                        <option value="<?= esc($locationOption['location']) ?>" 
                                                <?= $filters['location'] === $locationOption['location'] ? 'selected' : '' ?>>
                                            <?= esc($locationOption['location']) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Sort Options -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-dark mb-3">Sort By</label>
                            <select name="sort" class="select2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="created_at" <?= $filters['sort'] === 'created_at' ? 'selected' : '' ?>>Date Posted</option>
                                <option value="title" <?= $filters['sort'] === 'title' ? 'selected' : '' ?>>Job Title</option>
                                <option value="application_deadline" <?= $filters['sort'] === 'application_deadline' ? 'selected' : '' ?>>Deadline</option>
                                <option value="salary_min" <?= $filters['sort'] === 'salary_min' ? 'selected' : '' ?>>Salary</option>
                            </select>
                        </div>

                        <!-- Apply Filters Button -->
                        <button id="applyFilters" class="w-full bg-primary text-white py-3 px-4 rounded-md font-medium hover:bg-primaryShades-700 transition-colors">
                            Apply Filters
                        </button>

                        <!-- Clear Filters -->
                        <button id="clearFilters" class="w-full mt-3 bg-transparent text-primary py-2 px-4 rounded-md font-medium border border-primary hover:bg-primary hover:text-white transition-colors">
                            Clear All Filters
                        </button>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="lg:col-span-3">
                    <!-- Results Header -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                        <div>
                            <h2 class="text-2xl font-bold text-primaryShades-800 mb-2">
                                Available Opportunities
                            </h2>
                            <p class="text-slate-600">
                                Showing <?= count($opportunities) ?> of <?= $pagination['total'] ?> opportunities
                                <?php if (!empty($filters['search'])): ?>
                                    for "<strong><?= esc($filters['search']) ?></strong>"
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <!-- Results per page and sort order -->
                        <div class="flex items-center space-x-4 mt-4 md:mt-0">
                            <select name="order" class="select2 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="DESC" <?= $filters['order'] === 'DESC' ? 'selected' : '' ?>>Newest First</option>
                                <option value="ASC" <?= $filters['order'] === 'ASC' ? 'selected' : '' ?>>Oldest First</option>
                            </select>
                        </div>
                    </div>

                    <!-- Opportunities Grid -->
                    <?php if (!empty($opportunities)): ?>
                        <div class="grid md:grid-cols-2 gap-6 mb-8">
                            <?php foreach ($opportunities as $opportunity): ?>
                                <?php
                                    // Determine styling based on opportunity type
                                    $badgeColors = [
                                        'full-time' => 'bg-primary',
                                        'part-time' => 'bg-secondary',
                                        'contract' => 'bg-primaryShades-700',
                                        'internship' => 'bg-secondaryShades-500',
                                        'freelance' => 'bg-primaryShades-600'
                                    ];
                                    
                                    $badgeColor = $badgeColors[$opportunity['opportunity_type']] ?? 'bg-primary';
                                    
                                    // Format deadline
                                    $deadlineText = '';
                                    $deadlinePassed = false;
                                    if (!empty($opportunity['application_deadline'])) {
                                        $deadline = new DateTime($opportunity['application_deadline']);
                                        $now = new DateTime();
                                        $deadlinePassed = $now > $deadline;
                                        $deadlineText = $deadline->format('M j, Y');
                                    }
                                    
                                    // Format location
                                    $locationText = $opportunity['location'] ?? 'Kenya';
                                    if ($opportunity['is_remote']) {
                                        $locationText .= ' (Remote)';
                                    }
                                    
                                    // Format salary
                                    $salaryText = '';
                                    if (!empty($opportunity['salary_min']) && !empty($opportunity['salary_max'])) {
                                        $currency = $opportunity['salary_currency'] ?? 'KES';
                                        $salaryText = $currency . ' ' . number_format($opportunity['salary_min']) . ' - ' . number_format($opportunity['salary_max']);
                                    }
                                ?>
                                
                                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 border-l-4 border-primary">
                                    <!-- Opportunity Header -->
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="<?= $badgeColor ?> text-white px-3 py-1 rounded-full text-xs font-medium">
                                                    <?= ucfirst(str_replace('-', ' ', $opportunity['opportunity_type'])) ?>
                                                </span>
                                                <?php if ($opportunity['is_remote']): ?>
                                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Remote</span>
                                                <?php endif; ?>
                                                <?php if ($deadlinePassed): ?>
                                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Deadline Passed</span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <h3 class="text-lg font-bold text-primaryShades-800 mb-2">
                                                <a href="<?= site_url('opportunities/' . $opportunity['slug']) ?>" 
                                                   class="hover:text-primary transition-colors">
                                                    <?= esc($opportunity['title']) ?>
                                                </a>
                                            </h3>
                                            
                                            <div class="text-sm text-slate-600 mb-2">
                                                <span><?= esc($opportunity['company'] ?? 'KEWASNET') ?></span>
                                                <span class="mx-2">•</span>
                                                <span><?= esc($locationText) ?></span>
                                                <?php if ($salaryText): ?>
                                                    <span class="mx-2">•</span>
                                                    <span><?= esc($salaryText) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Description -->
                                    <p class="text-dark text-sm mb-4 line-clamp-3">
                                        <?= esc(substr(strip_tags($opportunity['description']), 0, 120)) ?>...
                                    </p>
                                    
                                    <!-- Footer -->
                                    <div class="flex items-center justify-between">
                                        <div class="text-xs text-slate-500">
                                            <?php if ($deadlineText): ?>
                                                <span>Deadline: <?= esc($deadlineText) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="flex space-x-2">
                                            <a href="<?= site_url('opportunities/' . $opportunity['slug']) ?>" 
                                               class="text-primary hover:text-primaryShades-700 text-sm font-medium">
                                                View Details
                                            </a>
                                            <?php if (!$deadlinePassed): ?>
                                                <a href="<?= site_url('opportunities/' . $opportunity['slug']) ?>" 
                                                   class="bg-primary text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-primaryShades-700 transition-colors">
                                                    Apply Now
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($pagination['totalPages'] > 1): ?>
                            <div class="flex justify-center">
                                <nav class="flex items-center space-x-1">
                                    <!-- Previous Page -->
                                    <?php if ($pagination['currentPage'] > 1): ?>
                                        <a href="?<?= http_build_query(array_merge($filters, ['page' => $pagination['currentPage'] - 1])) ?>" 
                                           class="px-3 py-2 rounded-md text-sm font-medium text-primary hover:bg-primary hover:text-white transition-colors">
                                            Previous
                                        </a>
                                    <?php endif; ?>
                                    
                                    <!-- Page Numbers -->
                                    <?php 
                                        $startPage = max(1, $pagination['currentPage'] - 2);
                                        $endPage = min($pagination['totalPages'], $pagination['currentPage'] + 2);
                                    ?>
                                    
                                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                        <a href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>" 
                                           class="px-3 py-2 rounded-md text-sm font-medium <?= $i === $pagination['currentPage'] ? 'bg-primary text-white' : 'text-primary hover:bg-primary hover:text-white' ?> transition-colors">
                                            <?= $i ?>
                                        </a>
                                    <?php endfor; ?>
                                    
                                    <!-- Next Page -->
                                    <?php if ($pagination['currentPage'] < $pagination['totalPages']): ?>
                                        <a href="?<?= http_build_query(array_merge($filters, ['page' => $pagination['currentPage'] + 1])) ?>" 
                                           class="px-3 py-2 rounded-md text-sm font-medium text-primary hover:bg-primary hover:text-white transition-colors">
                                            Next
                                        </a>
                                    <?php endif; ?>
                                </nav>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <!-- No Results -->
                        <div class="text-center py-12">
                            <div class="w-24 h-24 bg-lightGray rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-primaryShades-800 mb-4">No Opportunities Found</h3>
                            <p class="text-dark mb-6">We couldn't find any opportunities matching your search criteria.</p>
                            <button id="clearFiltersBtn" class="bg-primary text-white px-6 py-3 rounded-lg font-medium hover:bg-primaryShades-700 transition-colors">
                                Clear Filters & View All
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Subscription Section -->
    <?= view('frontendV2/website/pages/constants/newsletter-subscription') ?>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Apply filters function
    function applyFilters() {
        const params = new URLSearchParams();
        
        // Get filter values
        const type = $('input[name="type"]:checked').val();
        const location = $('select[name="location"]').val();
        const search = $('#globalSearch').val();
        const sort = $('select[name="sort"]').val();
        const order = $('select[name="order"]').val();
        
        // Add non-empty parameters
        if (type && type !== 'all') params.set('type', type);
        if (location) params.set('location', location);
        if (search) params.set('search', search);
        if (sort) params.set('sort', sort);
        if (order) params.set('order', order);
        
        // Redirect with parameters
        window.location.href = '<?= site_url('opportunities/explore') ?>?' + params.toString();
    }
    
    // Event handlers
    $('#applyFilters').on('click', applyFilters);
    $('#searchBtn').on('click', applyFilters);
    
    // Search on Enter key
    $('#globalSearch').on('keypress', function(e) {
        if (e.which === 13) {
            applyFilters();
        }
    });
    
    // Clear filters
    $('#clearFilters, #clearFiltersBtn').on('click', function() {
        window.location.href = '<?= site_url('opportunities/explore') ?>';
    });
    
    // Auto-apply filters when selections change
    $('input[name="type"], select[name="location"], select[name="sort"], select[name="order"]').on('change', function() {
        // Small delay to allow for better UX
        setTimeout(applyFilters, 300);
    });
    
    // Smooth animations for cards
    $('.hover\\:shadow-xl').hover(
        function() {
            $(this).addClass('transform scale-105');
        },
        function() {
            $(this).removeClass('transform scale-105');
        }
    );
});
</script>
<?= $this->endSection() ?>
