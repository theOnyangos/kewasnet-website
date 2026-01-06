<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();

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
                    Events & Conferences
                </h1>
                
                <p class="text-lg md:text-xl mb-8 text-slate-200 leading-relaxed">
                    Discover upcoming events, workshops, and conferences in Kenya's WASH sector
                </p>
                
                <!-- Search Bar -->
                <div class="max-w-2xl mx-auto relative">
                    <input 
                        type="text" 
                        id="search-input"
                        placeholder="Search events..." 
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
    <style>
        .events-page-pattern-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
            pointer-events: none;
            background-image: 
                repeating-linear-gradient(45deg, rgba(0, 0, 0, 0.1) 0, rgba(0, 0, 0, 0.1) 1px, transparent 1px, transparent 20px),
                repeating-linear-gradient(-45deg, rgba(0, 0, 0, 0.1) 0, rgba(0, 0, 0, 0.1) 1px, transparent 1px, transparent 20px);
            background-size: 40px 40px;
        }
    </style>
    <section class="py-16 relative text-gray-900" style="background-color: #fafafa; min-height: auto; position: relative;">
        <!-- Diagonal Grid Pattern -->
        <div class="events-page-pattern-bg"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Filters Sidebar -->
                <div class="lg:w-1/4">
                    <div class="bg-white rounded-xl p-6 sticky top-24 shadow-md relative z-10">
                        <h3 class="text-xl font-bold text-slate-800 mb-6">Filter Events</h3>
                        
                        <!-- Event Type Filter -->
                        <div class="mb-8">
                            <h4 class="text-sm font-semibold text-slate-600 uppercase tracking-wider mb-4">Event Type</h4>
                            <ul class="space-y-2">
                                <li>
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="checkbox" class="form-checkbox h-4 w-4 text-secondary rounded border-slate-300 focus:ring-secondary" 
                                               data-type="all" checked>
                                        <span class="text-slate-700">All Events</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="checkbox" class="form-checkbox h-4 w-4 text-secondary rounded border-slate-300 focus:ring-secondary" 
                                               data-type="free">
                                        <span class="text-slate-700">Free Events</span>
                                    </label>
                                </li>
                                <li>
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="checkbox" class="form-checkbox h-4 w-4 text-secondary rounded border-slate-300 focus:ring-secondary" 
                                               data-type="paid">
                                        <span class="text-slate-700">Paid Events</span>
                                    </label>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Date Filter -->
                        <div class="mb-8">
                            <h4 class="text-sm font-semibold text-slate-600 uppercase tracking-wider mb-4">Date</h4>
                            <select id="dateFilter" name="date" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                                <option value="">All Time</option>
                                <option value="upcoming">Upcoming</option>
                                <option value="this_month">This Month</option>
                                <option value="next_month">Next Month</option>
                                <option value="next_3_months">Next 3 Months</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Events Grid -->
                <div class="lg:w-3/4">
                    <!-- Sort Options -->
                    <div class="flex justify-between items-center mb-8">
                        <p class="text-sm text-slate-600">
                            <?php if (!empty($events)): ?>
                                Showing <?= count($events) ?> event<?= count($events) !== 1 ? 's' : '' ?>
                            <?php else: ?>
                                No events found
                            <?php endif; ?>
                        </p>
                        <div class="flex items-center">
                            <span class="text-sm text-slate-600 mr-2">Sort by:</span>
                            <select id="sortBy" class="border-0 text-sm font-medium text-primary focus:ring-0 focus:border-transparent">
                                <option value="date_asc">Date (Earliest)</option>
                                <option value="date_desc">Date (Latest)</option>
                                <option value="title">A-Z</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Events Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8" id="eventsGrid">
                        <?php if (!empty($events)): ?>
                            <?php foreach ($events as $event): ?>
                                <article class="group hover:shadow-xl transition-all duration-300 border border-slate-200 rounded-xl overflow-hidden bg-white shadow-md hover:shadow-2xl relative z-10">
                                    <div class="relative h-[380px] overflow-hidden">
                                        <?php if (!empty($event['image_url'])): ?>
                                            <img src="<?= $event['image_url'] ?>" 
                                                 alt="<?= esc($event['title']) ?>" 
                                                 class="w-full h-full object-contain bg-secondary/20 rounded-xl group-hover:scale-105 transition-transform duration-300"
                                                 onerror="this.src='<?= base_url('empty-image.svg') ?>'">
                                        <?php else: ?>
                                            <div class="w-full h-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
                                                <i data-lucide="calendar" class="icon-2xl text-primary/50"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="absolute top-4 left-4">
                                            <span class="bg-secondary text-white px-3 py-1 rounded-full text-xs font-medium">
                                                <?= esc($event['event_type'] === 'paid' ? 'Paid Event' : 'Free Event') ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-6">
                                        <div class="flex items-center text-slate-500 text-xs mb-3">
                                            <i data-lucide="calendar" class="icon-xs mr-1"></i>
                                            <span><?= date('F j, Y', strtotime($event['start_date'])) ?></span>
                                            <?php if (!empty($event['start_time'])): ?>
                                                <span class="mx-2">•</span>
                                                <i data-lucide="clock" class="icon-xs mr-1"></i>
                                                <span><?= date('g:i A', strtotime($event['start_time'])) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($event['venue'])): ?>
                                            <div class="flex items-center text-slate-500 text-xs mb-3">
                                                <i data-lucide="map-pin" class="icon-xs mr-1"></i>
                                                <span><?= esc($event['venue']) ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <h3 class="text-xl font-bold text-slate-800 group-hover:text-secondary transition-colors mb-3 line-clamp-2">
                                            <?= esc($event['title']) ?>
                                        </h3>
                                        <p class="text-slate-600 text-sm mb-4 line-clamp-3">
                                            <?php
                                            $description = strip_tags($event['description'] ?? '');
                                            echo esc($description ?: 'Join us for this exciting event.');
                                            ?>
                                        </p>
                                        <div class="flex justify-between items-center">
                                            <a href="<?= base_url('events/' . esc($event['slug'])) ?>" 
                                               class="text-sm font-medium text-secondary hover:text-secondaryShades-700 transition-colors flex items-center">
                                                View Details
                                                <i data-lucide="arrow-right" class="icon-xs ml-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-span-full text-center py-12">
                                <div class="mx-auto w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                    <i data-lucide="calendar-x" class="icon-xl text-slate-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-800 mb-2">No events found</h3>
                                <p class="text-slate-600 mb-4">
                                    <?php if (!empty($search)): ?>
                                        No events match your search for "<?= esc($search) ?>". Try adjusting your filters or search terms.
                                    <?php else: ?>
                                        No events are available at the moment. Please check back later.
                                    <?php endif; ?>
                                </p>
                                <a href="<?= base_url('events') ?>" 
                                   class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded-lg hover:bg-secondaryShades-700 transition-colors">
                                    <i data-lucide="refresh-cw" class="icon-sm mr-2"></i>
                                    Clear filters
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                        <div class="mt-12 flex justify-center">
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <!-- Previous Button -->
                                <?php if ($pagination['current_page'] > 1): ?>
                                    <a href="<?= current_url() ?>?page=<?= $pagination['current_page'] - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
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
                                $currentPage = $pagination['current_page'];
                                $totalPages = $pagination['total_pages'];
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($totalPages, $currentPage + 2);
                                
                                // Show first page if not in range
                                if ($startPage > 1): ?>
                                    <a href="<?= current_url() ?>?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
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
                                        <a href="<?= current_url() ?>?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
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
                                    <a href="<?= current_url() ?>?page=<?= $totalPages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                                       class="relative inline-flex items-center px-4 py-2 border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50">
                                        <?= $totalPages ?>
                                    </a>
                                <?php endif; ?>

                                <!-- Next Button -->
                                <?php if ($currentPage < $totalPages): ?>
                                    <a href="<?= current_url() ?>?page=<?= $currentPage + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
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
    const typeCheckboxes = document.querySelectorAll('input[data-type]');
    const dateFilter = document.getElementById('dateFilter');
    const sortBy = document.getElementById('sortBy');
    
    // Search functionality
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        });
    }
    
    // Type filter
    if (typeCheckboxes.length > 0) {
        typeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.dataset.type === 'all') {
                    typeCheckboxes.forEach(cb => {
                        if (cb !== this) cb.checked = false;
                    });
                } else {
                    const allCheckbox = document.querySelector('input[data-type="all"]');
                    if (allCheckbox) allCheckbox.checked = false;
                }
                applyFilters();
            });
        });
    }
    
    // Date filter
    if (dateFilter) {
        dateFilter.addEventListener('change', applyFilters);
    }
    
    // Sort filter
    if (sortBy) {
        sortBy.addEventListener('change', applyFilters);
    }
    
    function applyFilters() {
        const search = searchInput ? searchInput.value.trim() : '';
        const selectedTypes = Array.from(typeCheckboxes)
            .filter(cb => cb.checked && cb.dataset.type !== 'all')
            .map(cb => cb.dataset.type);
        const selectedDate = dateFilter ? dateFilter.value : '';
        const selectedSort = sortBy ? sortBy.value : '';
        
        // Build URL with filters
        const url = new URL(window.location.href);
        url.search = '';
        
        if (search) url.searchParams.set('search', search);
        if (selectedTypes.length > 0) url.searchParams.set('type', selectedTypes[0]);
        if (selectedDate) url.searchParams.set('date', selectedDate);
        if (selectedSort !== 'date_asc') url.searchParams.set('sort', selectedSort);
        
        // Navigate to filtered URL
        window.location.href = url.toString();
    }
});
</script>
<?= $this->endSection() ?>

