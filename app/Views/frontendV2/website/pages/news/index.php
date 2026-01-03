<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();

    // dd($blogPosts);

    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/website/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <!-- Page Header -->
    <section class="relative bg-gradient-to-r from-primary to-secondary text-white overflow-hidden py-24">
        <div class="relative container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
                    News & Blogs
                </h1>
                
                <p class="text-lg md:text-xl mb-8 text-slate-200 leading-relaxed">
                    Stay updated with the latest insights, research, and developments in Kenya's WASH sector
                </p>
                
                <!-- Search Bar -->
                <div class="max-w-2xl mx-auto relative">
                    <input 
                        type="text" 
                        id="search-input"
                        placeholder="Search articles, blogs, and news..." 
                        class="w-full px-6 py-4 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                    >
                    <button class="absolute right-2 top-2 bg-secondary hover:bg-secondary/90 text-white p-2 rounded-full">
                        <i data-lucide="search" class="icon-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Filters Sidebar -->
                <div class="lg:w-1/4">
                    <div class="bg-slate-50 rounded-xl p-6 sticky top-24">
                        <h3 class="text-xl font-bold text-slate-800 mb-6">Filter Content</h3>
                        
                        <!-- Categories Filter -->
                        <div class="mb-8">
                            <h4 class="text-sm font-semibold text-slate-600 uppercase tracking-wider mb-4">Categories</h4>
                            <ul class="space-y-2">
                                <li>
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="checkbox" class="form-checkbox h-4 w-4 text-secondary rounded border-slate-300 focus:ring-secondary" 
                                               data-category="all" <?= empty($selectedCategory) ? 'checked' : '' ?>>
                                        <span class="text-slate-700">All Categories</span>
                                    </label>
                                </li>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <li>
                                            <label class="flex items-center space-x-3 cursor-pointer">
                                                <input type="checkbox" class="form-checkbox h-4 w-4 text-secondary rounded border-slate-300 focus:ring-secondary" 
                                                       data-category="<?= esc($category['slug']) ?>" 
                                                       <?= ($selectedCategory === $category['slug']) ? 'checked' : '' ?>>
                                                <span class="text-slate-700"><?= esc($category['name']) ?></span>
                                            </label>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li>
                                        <p class="text-sm text-slate-500">No categories available</p>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        
                        <!-- Date Filter -->
                        <div class="mb-8">
                            <h4 class="text-sm font-semibold text-slate-600 uppercase tracking-wider mb-4">Date</h4>
                            <select id="dateFilter" name="date" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                                <option value="" <?= empty($selectedDate) ? 'selected' : '' ?>>All Time</option>
                                <option value="last_7_days" <?= ($selectedDate === 'last_7_days') ? 'selected' : '' ?>>Last 7 Days</option>
                                <option value="last_30_days" <?= ($selectedDate === 'last_30_days') ? 'selected' : '' ?>>Last 30 Days</option>
                                <option value="last_6_months" <?= ($selectedDate === 'last_6_months') ? 'selected' : '' ?>>Last 6 Months</option>
                                <option value="last_year" <?= ($selectedDate === 'last_year') ? 'selected' : '' ?>>Last Year</option>
                            </select>
                        </div>
                        
                        <!-- Tags Filter -->
                        <div class="mb-8">
                            <h4 class="text-sm font-semibold text-slate-600 uppercase tracking-wider mb-4">Popular Tags</h4>
                            <div class="flex flex-wrap gap-2">
                                <?php if (!empty($popularTags)): ?>
                                    <?php foreach ($popularTags as $tag): ?>
                                        <a href="<?= current_url() ?>?tag=<?= esc($tag['slug']) ?>" 
                                           class="bg-slate-200 hover:bg-secondary hover:text-white text-slate-700 px-3 py-1 rounded-full text-sm transition-colors <?= ($selectedTag === $tag['slug']) ? 'bg-secondary text-white' : '' ?>">
                                            #<?= esc($tag['name']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-sm text-slate-500">No tags available</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Featured Posts -->
                        <div>
                            <h4 class="text-sm font-semibold text-slate-600 uppercase tracking-wider mb-4">Featured</h4>
                            <div class="space-y-4">
                                <?php if (!empty($featuredPosts)): ?>
                                    <?php foreach ($featuredPosts as $featured): ?>
                                        <a href="<?= base_url('news-details/' . esc($featured->slug)) ?>" class="flex gap-3 group">
                                            <div class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0">
                                                <?php if (!empty($featured->featured_image)): ?>
                                                    <img src="<?= $featured->featured_image ?>" 
                                                         alt="<?= esc($featured->title) ?>" 
                                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                                                         onerror="this.src='<?= base_url('empty-image.svg') ?>'">
                                                <?php else: ?>
                                                    <div class="w-full h-full bg-slate-200 flex items-center justify-center">
                                                        <i data-lucide="image" class="icon-sm text-slate-400"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h5 class="text-sm font-medium text-slate-800 group-hover:text-secondary transition-colors line-clamp-2">
                                                    <?= esc($featured->title) ?>
                                                </h5>
                                                <p class="text-xs text-slate-500">
                                                    <?= date('M d, Y', strtotime($featured->published_at)) ?>
                                                </p>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-sm text-slate-500">No featured posts available</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Blog Posts -->
                <div class="lg:w-3/4">
                    <!-- Sort Options -->
                    <div class="flex justify-between items-center mb-8">
                        <p class="text-sm text-slate-600">
                            Showing <?= ($currentPage - 1) * $perPage + 1 ?> to <?= min($currentPage * $perPage, $totalPosts) ?> of <?= $totalPosts ?> articles
                        </p>
                        <div class="flex items-center">
                            <span class="text-sm text-slate-600 mr-2">Sort by:</span>
                            <select id="sortBy" class="border-0 text-sm font-medium text-primary focus:ring-0 focus:border-transparent">
                                <option value="recent">Most Recent</option>
                                <option value="popular">Most Popular</option>
                                <option value="viewed">Most Viewed</option>
                                <option value="title">A-Z</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Blog Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8" id="blogGrid">
                        <?php if (!empty($blogPosts)): ?>
                            <?php foreach ($blogPosts as $post): ?>
                                <article class="group hover:shadow-xl transition-all duration-300 border border-slate-200 rounded-xl overflow-hidden">
                                    <div class="relative h-48 overflow-hidden">
                                        <?php if (!empty($post->featured_image)): ?>
                                            <img src="<?= $post->featured_image ?>" 
                                                 alt="<?= esc($post->title) ?>" 
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                 onerror="this.src='<?= base_url('empty-image.svg') ?>'">
                                        <?php else: ?>
                                            <div class="w-full h-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
                                                <i data-lucide="image" class="icon-2xl text-primary/50"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="absolute top-4 left-4">
                                            <span class="bg-secondary text-white px-3 py-1 rounded-full text-xs font-medium">
                                                <?= esc($post->category_name ?? 'Blog') ?>
                                            </span>
                                        </div>
                                        <?php if ($post->is_featured): ?>
                                            <div class="absolute top-4 right-4">
                                                <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                                    <i data-lucide="star" class="icon-xs inline"></i> Featured
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-6">
                                        <div class="flex items-center text-slate-500 text-xs mb-3">
                                            <i data-lucide="calendar" class="icon-xs mr-1"></i>
                                            <span><?= date('F j, Y', strtotime($post->published_at)) ?></span>
                                            <span class="mx-2">•</span>
                                            <i data-lucide="clock" class="icon-xs mr-1"></i>
                                            <span><?= $post->reading_time ?? '5' ?> min read</span>
                                        </div>
                                        <h3 class="text-xl font-bold text-slate-800 group-hover:text-secondary transition-colors mb-3 line-clamp-2">
                                            <?= esc($post->title) ?>
                                        </h3>
                                        <p class="text-slate-600 text-sm mb-4 line-clamp-3">
                                            <?= esc($post->excerpt) ?>
                                        </p>
                                        <div class="flex justify-between items-center">
                                            <a href="<?= base_url('news-details/' . esc($post->slug)) ?>" 
                                               class="text-sm font-medium text-secondary hover:text-secondaryShades-700 transition-colors flex items-center">
                                                Read more
                                                <i data-lucide="arrow-right" class="icon-xs ml-1"></i>
                                            </a>
                                            <div class="flex items-center space-x-1">
                                                <i data-lucide="eye" class="icon-xs text-slate-400"></i>
                                                <span class="text-xs text-slate-500"><?= number_format($post->views ?? 0) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-span-full text-center py-12">
                                <div class="mx-auto w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                    <i data-lucide="search" class="icon-xl text-slate-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-800 mb-2">No articles found</h3>
                                <p class="text-slate-600 mb-4">
                                    <?php if (!empty($search)): ?>
                                        No articles match your search for "<?= esc($search) ?>". Try adjusting your filters or search terms.
                                    <?php else: ?>
                                        No articles are available at the moment. Please check back later.
                                    <?php endif; ?>
                                </p>
                                <a href="<?= base_url('news') ?>" 
                                   class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded-lg hover:bg-secondaryShades-700 transition-colors">
                                    <i data-lucide="refresh-cw" class="icon-sm mr-2"></i>
                                    Clear filters
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="mt-12 flex justify-center">
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <!-- Previous Button -->
                                <?php if ($currentPage > 1): ?>
                                    <a href="<?= current_url() ?>?page=<?= $currentPage - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($selectedCategory) ? '&category=' . urlencode($selectedCategory) : '' ?><?= !empty($selectedDate) ? '&date=' . urlencode($selectedDate) : '' ?>" 
                                       class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50">
                                        <i data-lucide="chevron-left" class="icon-xs"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-slate-300 bg-slate-100 text-sm font-medium text-slate-400 cursor-not-allowed">
                                        <i data-lucide="chevron-left" class="icon-xs"></i>
                                    </span>
                                <?php endif; ?>

                                <!-- Page Numbers -->
                                <?php
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($totalPages, $currentPage + 2);
                                
                                // Show first page if not in range
                                if ($startPage > 1): ?>
                                    <a href="<?= current_url() ?>?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($selectedCategory) ? '&category=' . urlencode($selectedCategory) : '' ?><?= !empty($selectedDate) ? '&date=' . urlencode($selectedDate) : '' ?>" 
                                       class="relative inline-flex items-center px-4 py-2 border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50">
                                        1
                                    </a>
                                    <?php if ($startPage > 2): ?>
                                        <span class="relative inline-flex items-center px-4 py-2 border border-slate-300 bg-white text-sm font-medium text-slate-700">
                                            ...
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <!-- Current page range -->
                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <?php if ($i == $currentPage): ?>
                                        <span class="relative inline-flex items-center px-4 py-2 border border-primary bg-primary text-sm font-medium text-white">
                                            <?= $i ?>
                                        </span>
                                    <?php else: ?>
                                        <a href="<?= current_url() ?>?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($selectedCategory) ? '&category=' . urlencode($selectedCategory) : '' ?><?= !empty($selectedDate) ? '&date=' . urlencode($selectedDate) : '' ?>" 
                                           class="relative inline-flex items-center px-4 py-2 border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50">
                                            <?= $i ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <!-- Show last page if not in range -->
                                <?php if ($endPage < $totalPages): ?>
                                    <?php if ($endPage < $totalPages - 1): ?>
                                        <span class="relative inline-flex items-center px-4 py-2 border border-slate-300 bg-white text-sm font-medium text-slate-700">
                                            ...
                                        </span>
                                    <?php endif; ?>
                                    <a href="<?= current_url() ?>?page=<?= $totalPages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($selectedCategory) ? '&category=' . urlencode($selectedCategory) : '' ?><?= !empty($selectedDate) ? '&date=' . urlencode($selectedDate) : '' ?>" 
                                       class="relative inline-flex items-center px-4 py-2 border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50">
                                        <?= $totalPages ?>
                                    </a>
                                <?php endif; ?>

                                <!-- Next Button -->
                                <?php if ($currentPage < $totalPages): ?>
                                    <a href="<?= current_url() ?>?page=<?= $currentPage + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($selectedCategory) ? '&category=' . urlencode($selectedCategory) : '' ?><?= !empty($selectedDate) ? '&date=' . urlencode($selectedDate) : '' ?>" 
                                       class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50">
                                        <i data-lucide="chevron-right" class="icon-xs"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-slate-300 bg-slate-100 text-sm font-medium text-slate-400 cursor-not-allowed">
                                        <i data-lucide="chevron-right" class="icon-xs"></i>
                                    </span>
                                <?php endif; ?>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Signup -->
    <?= $this->include('frontendV2/website/pages/constants/newsletter-subscription') ?>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const categoryCheckboxes = document.querySelectorAll('input[data-category]');
    const dateFilter = document.getElementById('dateFilter');
    const sortBy = document.getElementById('sortBy');
    
    // Search functionality
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            applyFilters();
        }, 500);
    });
    
    // Category filter
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.dataset.category === 'all') {
                // Uncheck all other categories
                categoryCheckboxes.forEach(cb => {
                    if (cb !== this) cb.checked = false;
                });
            } else {
                // Uncheck "All Categories" if any specific category is selected
                const allCheckbox = document.querySelector('input[data-category="all"]');
                if (allCheckbox) allCheckbox.checked = false;
            }
            applyFilters();
        });
    });
    
    // Date filter
    dateFilter.addEventListener('change', applyFilters);
    
    // Sort filter
    sortBy.addEventListener('change', applyFilters);
    
    function applyFilters() {
        const search = searchInput.value.trim();
        const selectedCategories = Array.from(categoryCheckboxes)
            .filter(cb => cb.checked && cb.dataset.category !== 'all')
            .map(cb => cb.dataset.category);
        const selectedDate = dateFilter.value;
        const selectedSort = sortBy.value;
        
        // Build URL with filters
        const url = new URL(window.location.href);
        url.search = ''; // Clear existing params
        
        if (search) url.searchParams.set('search', search);
        if (selectedCategories.length > 0) url.searchParams.set('category', selectedCategories[0]); // Take first category for simplicity
        if (selectedDate) url.searchParams.set('date', selectedDate);
        if (selectedSort !== 'recent') url.searchParams.set('sort', selectedSort);
        
        // Navigate to filtered URL
        window.location.href = url.toString();
    }
    
    // Clear filters functionality
    document.addEventListener('click', function(e) {
        if (e.target.textContent === 'Clear all') {
            e.preventDefault();
            window.location.href = '<?= base_url('news') ?>';
        }
    });
});
</script>
<?= $this->endSection() ?>