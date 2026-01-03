<?php

    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();

    // dd($resource);

    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/website/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-primary to-secondary text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl font-bold mb-6">Resource Library</h1>
            <p class="text-xl max-w-3xl mx-auto">
                Access comprehensive resources, research, and tools to advance WASH sector knowledge and implementation
            </p>
        </div>
    </section>

    <!-- Search and Filter -->
    <section class="py-12 bg-white border-b border-borderColor">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="bg-light rounded-xl p-6 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Search Input -->
                        <div class="">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" id="searchInput" placeholder="Search resources by title, description, or content..." 
                                       class="w-full pl-10 pr-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-white">
                            </div>
                        </div>
                        
                        <!-- Category Filter -->
                        <div>
                            <select id="categoryFilter" name="category" class="select2 w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-white appearance-none">
                                <option value="">All Categories</option>
                                <?php if (!empty($documentCategories)): ?>
                                    <?php foreach ($documentCategories as $category): ?>
                                        <option value="<?= $category['id'] ?>"><?= esc($category['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Search Button and Clear Button -->
                    <div class="flex flex-col sm:flex-row gap-3 mt-4">
                        <button id="searchBtn" class="bg-primary text-white px-6 py-2.5 rounded-lg hover:bg-primary/90 transition-colors font-medium flex items-center justify-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Search Resources
                        </button>
                        <button id="clearBtn" class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition-colors font-medium flex items-center justify-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Search Results Status Bar -->
        <div id="searchStatus" class="bg-blue-50 border-l-4 border-primary py-3 px-4 hidden">
            <div class="container mx-auto">
                <div class="flex items-center justify-between">
                    <p id="searchStatusText" class="text-sm text-blue-700"></p>
                    <button onclick="clearFilters()" class="text-xs text-blue-600 hover:text-blue-800 underline">Clear all filters</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Resource Categories -->
    <section class="py-20 bg-light">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-6 text-primaryShades-800">Resource Categories</h2>
                <p class="text-xl text-dark max-w-3xl mx-auto">
                    Explore our comprehensive collection of WASH sector resources organized by category
                </p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (!empty($documentCategories)): ?>
                    <?php 
                        $categoryIcons = [
                            'bg-primary'              => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>',
                            'bg-secondary'            => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>',
                            'bg-secondaryShades-500'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>',
                            'bg-green-500'            => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                            'bg-purple-500'           => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>',
                            'bg-indigo-500'           => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>'
                        ];

                        $colorIndex = 0;
                        $colors     = array_keys($categoryIcons);
                    ?>
                    <?php foreach ($documentCategories as $category): ?>
                        <div class="bg-white rounded-lg p-8 shadow-lg hover:shadow-xl transition-shadow">
                            <div class="w-16 h-16 <?= $colors[$colorIndex % count($colors)] ?> rounded-lg flex items-center justify-center mb-6">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <?= $categoryIcons[$colors[$colorIndex % count($colors)]] ?>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold mb-4 text-primaryShades-800"><?= esc($category['name']) ?></h3>
                            <p class="text-dark mb-6">
                                <?= esc($category['description'] ?? 'Explore resources in this category to enhance your knowledge and implementation strategies.') ?>
                            </p>
                            <div class="text-sm text-slate-500 mb-4"><?= number_format($category['resource_count'] ?? 0) ?> Resources Available</div>
                            <button onclick="filterByCategory('<?= $category['id'] ?>', '<?= esc($category['name']) ?>')" class="text-primary font-semibold hover:underline cursor-pointer">Browse <?= esc($category['name']) ?> →</button>
                        </div>
                        <?php $colorIndex++; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center text-slate-500">
                        <p>No resource categories available at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Featured Resources -->
    <section id="featuredResourcesSection" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-6 text-primaryShades-800">Featured Resources</h2>
                <p class="text-xl text-dark max-w-3xl mx-auto">
                    Recently published and most popular resources from our collection
                </p>
            </div>

            <div class="space-y-6">
                <?php if (!empty($featuredResources)): ?>
                    <?php 
                        $iconClasses = ['bg-primary', 'bg-secondary', 'bg-secondaryShades-500', 'bg-green-500', 'bg-purple-500', 'bg-indigo-500'];
                        $iconIndex = 0;
                    ?>
                    <?php foreach ($featuredResources as $resource): ?>
                        <!-- Resource Item -->
                        <div class="bg-light rounded-lg p-6 flex flex-col lg:flex-row items-start space-y-4 lg:space-y-0 lg:space-x-6">
                            <div class="w-16 h-16 <?= $iconClasses[$iconIndex % count($iconClasses)] ?> rounded-lg flex items-center justify-center flex-shrink-0 mt-4 mr-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-col md:flex-row md:items-center justify-between mb-2">
                                    <h3 class="text-xl font-bold text-primaryShades-800 mb-2 md:mb-0"><?= esc($resource['title']) ?></h3>
                                    <div class="flex items-center gap-2">
                                        <?php if (!empty($resource['is_featured']) && $resource['is_featured']): ?>
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-800 bg-yellow-100 rounded-full">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                                Featured
                                            </span>
                                        <?php endif; ?>
                                        <span class="text-sm text-slate-500"><?= esc($resource['category_name'] ?? 'Document') ?> • <?= date('F Y', strtotime($resource['created_at'])) ?></span>
                                    </div>
                                </div>
                                <p class="text-dark mb-4">
                                    <?php 
                                        $description = $resource['description'] ?? '';
                                        $summary = !empty($description) ? substr(strip_tags($description), 0, 300) : 'No description available';
                                        echo esc($summary . (strlen($description) > 300 ? '...' : ''));
                                    ?>
                                </p>
                                
                                <!-- Action Buttons -->
                                <div class="flex flex-col gap-3 mb-4">
                                    <?php if (!empty($resource['attachments']) && count($resource['attachments']) > 0): ?>
                                        <!-- All files - show inline with consistent design -->
                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                            <p class="text-sm font-medium text-gray-700 mb-3">
                                                <?= count($resource['attachments']) ?> <?= count($resource['attachments']) === 1 ? 'file' : 'files' ?> available:
                                            </p>
                                            <div class="space-y-3">
                                                <?php foreach ($resource['attachments'] as $index => $att): ?>
                                                    <?php 
                                                        $attachment = is_array($att) ? (object)$att : $att;
                                                        $fileExt = strtoupper(pathinfo($attachment->file_name ?? 'file', PATHINFO_EXTENSION));
                                                    ?>
                                                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 p-3 bg-white rounded-lg border border-gray-200">
                                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                                            <div class="flex-shrink-0 w-10 h-10 bg-primary/10 rounded flex items-center justify-center">
                                                                <span class="text-xs font-bold text-primary"><?= $fileExt ?></span>
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                                    <?= esc($attachment->original_name ?? $attachment->file_name ?? 'Document ' . ($index + 1)) ?>
                                                                </p>
                                                                <p class="text-xs text-gray-500">
                                                                    <?= format_file_size($attachment->file_size ?? 0) ?>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="flex gap-2 flex-shrink-0 w-full sm:w-auto">
                                                            <a href="<?= base_url('client/view/preview-attachment/' . $attachment->file_path) ?>" 
                                                               target="_blank" 
                                                               onclick="incrementViewCount('<?= $resource['id'] ?>')" 
                                                               class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-medium text-white bg-secondary hover:bg-secondary/90 rounded transition-colors">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                </svg>
                                                                View
                                                            </a>
                                                            <a href="<?= base_url('client/download/download-attachment/' . $attachment->file_path) ?>" 
                                                               target="_blank" 
                                                               onclick="incrementDownloadCount('<?= $resource['id'] ?>', '<?= $attachment->id ?? '' ?>')" 
                                                               class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-medium text-white bg-primary hover:bg-primary/90 rounded transition-colors">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4"></path>
                                                                </svg>
                                                                Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-2 bg-gray-100 text-gray-500 px-4 py-2 rounded-lg font-medium justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            No File Available
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Stats at bottom -->
                                <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500">
                                    <?php if (!empty($resource['attachments']) && count($resource['attachments']) > 0): ?>
                                        <?php 
                                            // Calculate total size and downloads from all attachments
                                            $totalSize = 0;
                                            $totalDownloads = 0;
                                            foreach ($resource['attachments'] as $att) {
                                                $attObj = is_array($att) ? (object)$att : $att;
                                                $totalSize += $attObj->file_size ?? 0;
                                                $totalDownloads += $attObj->download_count ?? 0;
                                            }
                                        ?>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            <?= count($resource['attachments']) ?> file<?= count($resource['attachments']) > 1 ? 's' : '' ?> • <?= format_file_size($totalSize) ?>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4"></path>
                                            </svg>
                                            <?= number_format($totalDownloads) ?> downloads
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($resource['view_count'])): ?>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <?= number_format($resource['view_count'] ?? 0) ?> views
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php $iconIndex++; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-slate-500 py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No featured resources available</h3>
                        <p class="text-gray-500">Check back later for featured resources from our collection.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Drawer Overlay -->
    <div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>

    <!-- Resource Drawer -->
    <div class="resource-drawer" id="resourceDrawer">
        <div class="p-6">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-primaryShades-800" id="drawerTitle">Resources</h2>
                <button onclick="closeDrawer()" class="text-slate-500 hover:text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="flex flex-col justify-start space-y-6" id="resourceList">
                <div class="text-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
                    <p class="mt-4 text-slate-500">Loading resources...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Newsletter Subscription Section -->
    <?= view('frontendV2/website/pages/constants/newsletter-subscription') ?>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    // Global variables for resource management
    let currentPage = 1;
    let currentCategoryId = '';
    let currentDocumentType = '';
    let currentSearch = '';
    let categoryNameMap = {};

    // Initialize search functionality
    $(document).ready(function() {
        // Build category name mapping from PHP data
        <?php if (!empty($documentCategories)): ?>
            categoryNameMap = {
                <?php foreach ($documentCategories as $category): ?>
                    '<?= $category['id'] ?>': '<?= esc($category['name']) ?>',
                <?php endforeach; ?>
            };
        <?php endif; ?>

        // Search button click handler
        $('#searchBtn').on('click', function() {
            performSearch();
        });

        // Clear button click handler
        $('#clearBtn').on('click', function() {
            clearFilters();
        });

        // Search input enter key handler
        $('#searchInput').on('keypress', function(e) {
            if (e.which === 13) {
                performSearch();
            }
        });

        // Filter change handlers
        $('#categoryFilter, #documentTypeFilter').on('change', function() {
            performSearch();
        });

        // Real-time search with debouncing
        let searchTimeout;
        $('#searchInput').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                if ($('#searchInput').val().length >= 3 || $('#searchInput').val().length === 0) {
                    performSearch();
                }
            }, 500);
        });
    });

    // Clear all filters
    function clearFilters() {
        $('#searchInput').val('');
        $('#categoryFilter').val('');
        $('#documentTypeFilter').val('');
        currentSearch = '';
        currentCategoryId = '';
        currentDocumentType = '';
        currentPage = 1;
        
        // Hide search status
        const searchStatus = document.getElementById('searchStatus');
        if (searchStatus) {
            searchStatus.classList.add('hidden');
        }
        
        // Close drawer if open
        closeDrawer();
        
        // Scroll to top of page
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Perform search with current filters
    function performSearch() {
        currentSearch = $('#searchInput').val();
        currentCategoryId = $('#categoryFilter').val();
        currentDocumentType = $('#documentTypeFilter').val();
        currentPage = 1;
        
        loadResources();
    }

    // Load resources via AJAX
    function loadResources() {
        // Show loading state
        const searchBtn = $('#searchBtn');
        const originalText = searchBtn.html();
        searchBtn.html('<svg class="animate-spin h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Loading...');
        searchBtn.prop('disabled', true);

        $.ajax({
            url: '<?= base_url('api/resources/search') ?>',
            method: 'GET',
            data: {
                category_id: currentCategoryId,
                document_type_id: currentDocumentType,
                search: currentSearch,
                page: currentPage
            },
            success: function(response) {
                if (response.success) {
                    displayResourcesInDrawer(response.data.resources);
                    updatePagination(response.data.pagination);
                    
                    // Show results summary
                    const totalResults = response.data.pagination.total;
                    if (totalResults > 0) {
                        showSearchResults(totalResults);
                    } else {
                        showNoResults();
                    }
                } else {
                    console.error('Error loading resources:', response.message);
                    showError('Failed to load resources. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                showError('Network error. Please check your connection and try again.');
            },
            complete: function() {
                // Restore button state
                searchBtn.html(originalText);
                searchBtn.prop('disabled', false);
            }
        });
    }

    // Show search results summary
    function showSearchResults(totalResults) {
        const statusElement = document.getElementById('searchStatus');
        const statusText = document.getElementById('searchStatusText');
        
        if (!statusElement || !statusText) return;
        
        let statusMessage = `Found ${totalResults} resource${totalResults !== 1 ? 's' : ''}`;
        
        // Add filter details to status
        const activeFilters = [];
        if (currentSearch) activeFilters.push(`matching "${currentSearch}"`);
        if (currentCategoryId && categoryNameMap[currentCategoryId]) {
            activeFilters.push(`in ${categoryNameMap[currentCategoryId]}`);
        }
        if (currentDocumentType) {
            const docTypeSelect = document.getElementById('documentTypeFilter');
            const selectedOption = docTypeSelect.options[docTypeSelect.selectedIndex];
            activeFilters.push(`of type ${selectedOption.text}`);
        }
        
        if (activeFilters.length > 0) {
            statusMessage += ` ${activeFilters.join(' ')}`;
        }
        
        statusText.textContent = statusMessage;
        statusElement.classList.remove('hidden');
    }

    // Show no results message
    function showNoResults() {
        const statusElement = document.getElementById('searchStatus');
        const statusText = document.getElementById('searchStatusText');
        const resourceList = document.getElementById('resourceList');
        
        if (statusElement && statusText) {
            statusText.textContent = 'No resources found matching your criteria.';
            statusElement.classList.remove('hidden');
        }
        
        if (resourceList) {
            resourceList.innerHTML = `
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No resources found</h3>
                <p class="mt-1 text-sm text-gray-500">Try adjusting your search criteria or browse different categories.</p>
                <button onclick="clearFilters()" class="mt-3 bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90">
                    Clear All Filters
                </button>
            </div>
        `;
        }
    }

    // Show error message
    function showError(message) {
        const resourceList = document.getElementById('resourceList');
        if (!resourceList) return;
        
        resourceList.innerHTML = `
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-red-900">Error loading resources</h3>
                <p class="mt-1 text-sm text-red-500">${message}</p>
                <button onclick="loadResources()" class="mt-3 bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90">
                    Try Again
                </button>
            </div>
        `;
    }

    // Display resources in drawer
    function displayResourcesInDrawer(resources) {
        const resourceList = document.getElementById('resourceList');
        resourceList.innerHTML = '';

        if (resources.length === 0) {
            showNoResults();
            return;
        }

        resources.forEach(resource => {
            const resourceItem = document.createElement('div');
            resourceItem.className = 'bg-light rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow';
            
            // Handle attachments (new FileAttachment structure)
            const downloadButton = resource.attachments && resource.attachments.length > 0 ? 
                `<a href="${'<?= base_url('client/download/download-attachment/') ?>' + resource.attachments[0].file_path}" target="_blank" onclick="incrementDownloadCount('${resource.id}', '${resource.attachments[0].id}')" class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4m-6 4h8a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v11a2 2 0 002 2z"></path>
                    </svg>
                    Download ${getFileExtension(resource.attachments[0].file_name).toUpperCase()}
                </a>` :
                '<span class="inline-flex items-center gap-2 bg-gray-100 text-gray-500 px-4 py-2 rounded-lg font-medium">No file available</span>';
                
            const fileSize = resource.attachments && resource.attachments[0] ? formatFileSize(resource.attachments[0].file_size) : 'Unknown size';
            const viewButton = `
                <button onclick="viewResourceDetails('${resource.id}', '${resource.attachments[0].file_path}')" class="inline-flex items-center gap-2 bg-secondary text-white px-4 py-2 rounded-lg hover:bg-secondary/90 transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    View Details
                </button>
            `;
            
            // Featured badge
            const featuredBadge = resource.is_featured ? 
                `<span class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-800 bg-yellow-100 rounded-full">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    Featured
                </span>` : '';
            
            resourceItem.innerHTML = `
                <div class="mb-4">
                <h3 class="text-xl font-semibold text-secondary pr-4">${escapeHtml(resource.title)}</h3>
                    <div class="flex items-start justify-between my-2">
                        <div class="flex flex gap-1 items-end">
                            ${featuredBadge}
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full whitespace-nowrap">${escapeHtml(resource.category_name || 'Document')}</span>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600 mb-3">${escapeHtml((resource.description || '').substring(0, 200) + '...')}</p>
                    
                    <!-- Resource Metadata -->
                    <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500 mb-4">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            ${formatDate(resource.created_at)}
                        </span>
                        ${resource.attachments && resource.attachments[0] ? `
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            ${fileSize}
                        </span>
                        ` : ''}
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4"></path>
                            </svg>
                            ${parseInt(resource.total_downloads || 0).toLocaleString()} downloads
                        </span>
                        ${resource.view_count ? `
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            ${parseInt(resource.view_count).toLocaleString()} views
                        </span>
                        ` : ''}
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    ${downloadButton}
                    ${viewButton}
                </div>
            `;
            resourceList.appendChild(resourceItem);
        });

        // Initialize animations if GSAP is available
        if (typeof gsap !== 'undefined') {
            gsap.fromTo(resourceList.children, 
                { opacity: 0, y: 20 },
                { 
                    opacity: 1, 
                    y: 0, 
                    duration: 0.5, 
                    stagger: 0.1,
                    ease: "power2.out"
                }
            );
        }
    }

    // Update pagination display
    function updatePagination(pagination) {
        // You can implement pagination controls here if needed
        console.log('Pagination:', pagination);
    }

    // Filter by category function for static category buttons
    function filterByCategory(categoryId, categoryName) {
        // Set the category filter dropdown
        $('#categoryFilter').val(categoryId);
        
        // Reset other filters
        $('#searchInput').val('');
        $('#documentTypeFilter').val('');
        
        // Update search variables
        currentCategoryId = categoryId;
        currentSearch = '';
        currentDocumentType = '';
        currentPage = 1;
        
        // Open drawer with category title
        setTimeout(() => {
            openDrawer(categoryId, categoryName || 'Resources');
        }, 500);
    }

    // Open drawer function with category filtering
    function openDrawer(categoryId, categoryName) {
        const drawer = document.getElementById('resourceDrawer');
        const overlay = document.getElementById('drawerOverlay');
        const drawerTitle = document.getElementById('drawerTitle');
        
        // Set drawer title
        drawerTitle.textContent = categoryName || 'Resources';
        
        // Set current filters
        currentCategoryId = categoryId;
        currentDocumentType = '';
        currentSearch = '';
        currentPage = 1;
        
        // Show drawer and overlay
        drawer.classList.add('active');
        overlay.classList.add('active');
        
        // Load resources for this category
        loadResources();
    }

    // Close drawer function
    function closeDrawer() {
        const drawer = document.getElementById('resourceDrawer');
        const overlay = document.getElementById('drawerOverlay');
        
        drawer.classList.remove('active');
        overlay.classList.remove('active');
    }

    // Close drawer when pressing Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDrawer();
        }
    });

    // Increment download count
    function incrementDownloadCount(resourceId, attachmentId) {
        $.ajax({
            url: '<?= base_url('api/resources/increment-download') ?>',
            method: 'POST',
            data: { 
                resource_id: resourceId,
                attachment_id: attachmentId
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            }
        });
    }

    // View resource details function
    function viewResourceDetails(resourceId, filePath) {
        window.open('<?= base_url('client/view/preview-attachment//') ?>' + filePath, '_blank');
        incrementViewCount(resourceId);
    }

    // Utility functions
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
    }

    function getFileExtension(filename) {
        return filename.split('.').pop() || '';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function incrementViewCount(resourceId) {
        $.ajax({
            url: '<?= base_url('api/resources/increment-view-count') ?>',
            method: 'POST',
            data: { resource_id: resourceId },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            }
        });
    }

    // Toggle file dropdown
    function toggleFileDropdown(button) {
        event.stopPropagation();
        const dropdown = button.nextElementSibling;
        console.log('Button clicked, dropdown:', dropdown);
        
        if (!dropdown) {
            console.error('No dropdown found!');
            return;
        }
        
        const isHidden = dropdown.classList.contains('hidden');
        console.log('Is hidden:', isHidden);
        console.log('Dropdown classes before:', dropdown.className);
        
        // Close all other dropdowns
        document.querySelectorAll('.file-dropdown').forEach(d => {
            if (d !== dropdown) {
                d.classList.add('hidden');
                d.style.display = 'none';
            }
        });
        
        // Toggle current dropdown
        if (isHidden) {
            dropdown.classList.remove('hidden');
            dropdown.style.display = 'block';
            console.log('Dropdown shown, classes after:', dropdown.className);
            console.log('Display style:', dropdown.style.display);
        } else {
            dropdown.classList.add('hidden');
            dropdown.style.display = 'none';
            console.log('Dropdown hidden');
        }
    }

    // Close dropdowns when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(event) {
            const dropdownContainer = event.target.closest('.relative.inline-block');
            if (!dropdownContainer) {
                document.querySelectorAll('.file-dropdown').forEach(dropdown => {
                    dropdown.classList.add('hidden');
                    dropdown.style.display = 'none';
                });
            }
        });
    });
</script>

<!-- CSS for drawer -->
<style>
    .drawer-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 998;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .drawer-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .resource-drawer {
        position: fixed;
        top: 0;
        right: -100%;
        width: 90%;
        max-width: 600px;
        height: 100vh;
        background: white;
        z-index: 999;
        overflow-y: auto;
        transition: right 0.3s ease;
        box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .resource-drawer.active {
        right: 0;
    }

    @media (max-width: 768px) {
        .resource-drawer {
            width: 100%;
            max-width: none;
        }
    }
</style>

<?= $this->endSection() ?>