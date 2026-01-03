<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/ksp/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <!-- Pillar Hero -->
    <section class="relative py-20 bg-gradient-to-r from-primary to-secondary">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-2/3 text-white">
                    <div class="flex items-center mb-4">
                        <i data-lucide="droplets" class="w-8 h-8 mr-3"></i>
                        <span class="text-lg font-medium">Strategic Pillar</span>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold mb-6"><?= esc($pillar['title']) ?></h1>
                    <p class="text-xl max-w-3xl leading-relaxed">
                        <?= esc($pillar['description'] ?? 'Explore comprehensive resources, articles, and publications for this strategic pillar.') ?>
                    </p>
                </div>
                <div class="md:w-1/3 mt-8 md:mt-0">
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-6 border border-white/30">
                        <h3 class="text-xl font-bold text-white mb-4">Quick Stats</h3>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="bg-white/20 rounded-full p-2 mr-4">
                                    <i data-lucide="file-text" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-white"><?= number_format($totalResources) ?></div>
                                    <div class="text-white/80">Resources</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="bg-white/20 rounded-full p-2 mr-4">
                                    <i data-lucide="folder" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-white"><?= number_format($totalCategories) ?></div>
                                    <div class="text-white/80">Categories</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="bg-white/20 rounded-full p-2 mr-4">
                                    <i data-lucide="users" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-white"><?= number_format($totalContributors) ?>+</div>
                                    <div class="text-white/80">Contributors</div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    <li>
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                            <a href="<?= base_url('ksp/pillars') ?>" class="ml-1 text-sm font-medium text-slate-600 hover:text-primary md:ml-2">Pillars</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                            <span class="ml-1 text-sm font-medium text-primary md:ml-2"><?= esc($pillar['slug']) ?></span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar with filters -->
                <aside class="lg:w-1/4 lg:order-1">
                    <!-- Mobile filter toggle button -->
                    <div class="lg:hidden mb-4">
                        <button id="filterToggle" class="w-full bg-primary text-white px-4 py-3 rounded-lg flex items-center justify-center">
                            <i data-lucide="filter" class="w-5 h-5 mr-2"></i>
                            <span>Show Filters</span>
                        </button>
                    </div>
                    
                    <div id="filterPanel" class="lg:block hidden lg:sticky lg:top-24 space-y-6">
                        <!-- Search -->
                        <div class="bg-light p-4 rounded-lg">
                            <h3 class="font-bold text-lg mb-4 flex items-center">
                                <i data-lucide="search" class="w-5 h-5 mr-2"></i>
                                Search Resources
                            </h3>
                            <form method="GET" action="">
                                <div class="relative mb-4">
                                    <input type="text" name="search" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Search <?= esc($pillar['title']) ?> resources..." 
                                           class="w-full px-4 py-2 border borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                                    <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-primary">
                                        <i data-lucide="search" class="w-5 h-5"></i>
                                    </button>
                                </div>
                                <!-- Hidden filters to maintain state -->
                                <?php if (!empty($filters['category'])): ?>
                                    <input type="hidden" name="category" value="<?= esc($filters['category']) ?>">
                                <?php endif; ?>
                                <?php if (!empty($filters['document_type'])): ?>
                                    <input type="hidden" name="document_type" value="<?= esc($filters['document_type']) ?>">
                                <?php endif; ?>
                            </form>
                        </div>
                        
                        <!-- Categories Filter -->
                        <div class="bg-light p-4 rounded-lg">
                            <h3 class="font-bold text-lg mb-4 flex items-center">
                                <i data-lucide="folder" class="w-5 h-5 mr-2"></i>
                                Categories
                            </h3>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="radio" name="category_filter" value="all" <?= empty($filters['category']) || $filters['category'] === 'all' ? 'checked' : '' ?> 
                                           class="text-primary focus:ring-primary" onchange="filterByCategory('all')">
                                    <span>All Categories (<?= number_format($totalResources) ?>)</span>
                                </label>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <label class="flex items-center space-x-3 cursor-pointer">
                                            <input type="radio" name="category_filter" value="<?= esc($category->id) ?>" <?= $filters['category'] === $category->id ? 'checked' : '' ?>
                                                   class="text-primary focus:ring-primary" onchange="filterByCategory('<?= esc($category->id) ?>')">
                                            <span><?= esc($category->name) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Document Types Filter -->
                        <div class="bg-light p-4 rounded-lg">
                            <h3 class="font-bold text-lg mb-4 flex items-center">
                                <i data-lucide="file-type" class="w-5 h-5 mr-2"></i>
                                Document Types
                            </h3>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="radio" name="document_type_filter" value="all" <?= empty($filters['document_type']) || $filters['document_type'] === 'all' ? 'checked' : '' ?>
                                           class="text-primary focus:ring-primary" onchange="filterByDocumentType('all')">
                                    <span>All Types</span>
                                </label>
                                <?php if (!empty($documentTypes)): ?>
                                    <?php foreach ($documentTypes as $docType): ?>
                                        <label class="flex items-center space-x-3 cursor-pointer">
                                            <input type="radio" name="document_type_filter" value="<?= esc($docType->id) ?>" <?= $filters['document_type'] === $docType->id ? 'checked' : '' ?>
                                                   class="text-primary focus:ring-primary" onchange="filterByDocumentType('<?= esc($docType->id) ?>')">
                                            <div class="flex items-center">
                                                <span class="w-3 h-3 rounded-full mr-2" style="background-color: <?= esc($docType->color ?? '#3B82F6') ?>"></span>
                                                <span><?= esc($docType->name) ?></span>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Year Filter -->
                        <div class="bg-light p-4 rounded-lg">
                            <h3 class="font-bold text-lg mb-4 flex items-center">
                                <i data-lucide="calendar" class="w-5 h-5 mr-2"></i>
                                Publication Year
                            </h3>
                            <select id="publication_year" name="publication_year" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary select2">
                                <option>All Years</option>
                                <option>2023</option>
                                <option>2022</option>
                                <option>2021</option>
                                <option>2020</option>
                                <option>2019</option>
                            </select>
                        </div>
                        
                        <!-- Reset Filters Button -->
                        <button onclick="resetFilters()" class="w-full border borderColor text-dark hover:bg-secondary hover:text-white hover:border-secondary transition-colors py-2 rounded-lg flex items-center justify-center">
                            <i data-lucide="refresh-ccw" class="w-4 h-4 mr-2"></i>
                            Reset Filters
                        </button>
                    </div>
                </aside>
                
                <!-- Main Content Area -->
                <div class="lg:w-3/4 lg:order-2">     
                    <!-- Resources Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div class="mb-4 md:mb-0">
                            <h2 class="text-2xl font-bold text-dark mb-2"><?= esc($pillar['title']) ?> Resources</h2>
                            <p class="text-sm text-slate-600">
                                Showing <?= number_format(count($resources)) ?> of <?= number_format($totalItems) ?> resources
                                <?php if (!empty($filters['search'])): ?>
                                    for "<?= esc($filters['search']) ?>"
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="flex items-center space-x-4 w-full md:w-auto">
                            <div class="relative flex-1 md:flex-none">
                                <select id="sort" name="sort" class="w-full px-4 py-2 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary appearance-none">
                                    <option value="newest">Sort by: Newest First</option>
                                    <option value="oldest">Sort by: Oldest First</option>
                                    <option value="most_viewed">Sort by: Most Viewed</option>
                                    <option value="alphabetical">Sort by: Alphabetical</option>
                                </select>
                                <i data-lucide="chevron-down" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 pointer-events-none w-4 h-4"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Resources Grid -->
                    <?php if (!empty($resources)): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            <?php foreach ($resources as $resource): ?>
                                <div class="bg-white rounded-lg shadow-sm border borderColor overflow-hidden hover:shadow-md transition-all">
                                    <!-- Featured Image -->
                                    <div class="h-48 bg-cover bg-center" style="background-image: url('<?= !empty($resource->image_url) ? base_url($resource->image_url) : 'https://images.unsplash.com/photo-1561484930-974554019ade?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' ?>')"></div>
                                    
                                    <div class="p-5">
                                        <!-- Document Type and Year -->
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-xs font-medium px-2 py-1 rounded" style="background-color: <?= esc($resource->document_type_color ?? '#3B82F6') ?>20; color: <?= esc($resource->document_type_color ?? '#3B82F6') ?>">
                                                <?= esc($resource->document_type_name ?? 'Document') ?>
                                            </span>
                                            <?php if ($resource->publication_year): ?>
                                                <span class="text-xs text-slate-500"><?= esc($resource->publication_year) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Title -->
                                        <h3 class="font-bold text-lg mb-2 hover:text-secondary transition-colors">
                                            <a href="<?= base_url('ksp/pillar-articles/' . $pillar['slug'] . '/' . $resource->slug) ?>">
                                                <?= esc($resource->title) ?>
                                            </a>
                                        </h3>
                                        
                                        <!-- Description -->
                                        <p class="text-sm text-slate-600 mb-4 line-clamp-2">
                                            <?= esc(substr(strip_tags($resource->description ?? 'No description available'), 0, 120)) ?>...
                                        </p>
                                        
                                        <!-- Stats -->
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center text-sm text-slate-500">
                                                <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                                                <?= number_format($resource->download_count ?? 0) ?>
                                            </div>
                                            <div class="flex items-center text-sm text-slate-500">
                                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                                <?= number_format($resource->view_count ?? 0) ?>
                                            </div>
                                            <div class="flex">
                                                <a href="#" class="text-primary hover:text-secondary" onclick="bookmarkResource('<?= $resource->id ?>')">
                                                    <i data-lucide="bookmark" class="w-5 h-5"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Contributors -->
                                        <?php if (!empty($resource->contributors)): ?>
                                            <div class="mb-4">
                                                <div class="text-xs text-slate-500 mb-2">Contributors:</div>
                                                <div class="flex flex-wrap gap-1">
                                                    <?php foreach (array_slice($resource->contributors, 0, 3) as $contributor): ?>
                                                        <span class="text-xs bg-slate-100 text-slate-700 px-2 py-1 rounded">
                                                            <?= esc($contributor->name) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                    <?php if (count($resource->contributors) > 3): ?>
                                                        <span class="text-xs bg-slate-100 text-slate-700 px-2 py-1 rounded">
                                                            +<?= count($resource->contributors) - 3 ?> more
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Download Section -->
                                    <div class="px-5 py-3 border-t borderColor bg-slate-50">
                                        <?php if ($resource->file_url): ?>
                                            <a href="<?= base_url($resource->file_url) ?>" target="_blank" class="text-sm font-medium text-secondary flex items-center hover:text-secondary/80">
                                                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                                                Download <?= strtoupper($resource->file_type ?? 'PDF') ?> 
                                                <?php if ($resource->file_size): ?>
                                                    (<?= formatFileSize($resource->file_size) ?>)
                                                <?php endif; ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('ksp/pillar-articles/' . $pillar['slug'] . '/' . $resource->slug) ?>" class="text-sm font-medium text-secondary flex items-center hover:text-secondary/80">
                                                <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
                                                View Details
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- No Resources Found -->
                        <div class="text-center py-16">
                            <i data-lucide="folder-x" class="w-16 h-16 text-slate-400 mx-auto mb-4"></i>
                            <h3 class="text-xl font-semibold text-slate-600 mb-2">No Resources Found</h3>
                            <p class="text-slate-500 mb-6">
                                <?php if (!empty($filters['search']) || !empty($filters['category']) || !empty($filters['document_type'])): ?>
                                    No resources match your current filters. Try adjusting your search criteria.
                                <?php else: ?>
                                    No resources have been published for this pillar yet. Check back later for updates.
                                <?php endif; ?>
                            </p>
                            <?php if (!empty($filters['search']) || !empty($filters['category']) || !empty($filters['document_type'])): ?>
                                <a href="<?= base_url('ksp/pillar-articles/' . $pillar['slug']) ?>" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                                    <i data-lucide="refresh-ccw" class="w-4 h-4 mr-2"></i>
                                    Clear Filters
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="mt-12 flex justify-center pb-5">
                            <nav class="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <!-- Previous Page -->
                                <?php if ($currentPage > 1): ?>
                                    <a href="<?= base_url('ksp/pillar-articles/' . $pillar['slug'] . '?' . http_build_query(array_merge($filters, ['page' => $currentPage - 1]))) ?>" 
                                       class="relative inline-flex items-center px-2 py-2 rounded-l-md border borderColor bg-white text-sm font-medium text-slate-500 hover:bg-slate-50">
                                        <span class="sr-only">Previous</span>
                                        <i data-lucide="chevron-left" class="w-5 h-5"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border borderColor bg-slate-100 text-sm font-medium text-slate-400 cursor-not-allowed">
                                        <span class="sr-only">Previous</span>
                                        <i data-lucide="chevron-left" class="w-5 h-5"></i>
                                    </span>
                                <?php endif; ?>

                                <!-- Page Numbers -->
                                <?php
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($totalPages, $currentPage + 2);
                                
                                if ($startPage > 1): ?>
                                    <a href="<?= base_url('ksp/pillar-articles/' . $pillar['slug'] . '?' . http_build_query(array_merge($filters, ['page' => 1]))) ?>" 
                                       class="relative inline-flex items-center px-4 py-2 border borderColor bg-white text-sm font-medium text-slate-600 hover:bg-slate-50">1</a>
                                    <?php if ($startPage > 2): ?>
                                        <span class="relative inline-flex items-center px-4 py-2 border borderColor bg-white text-sm font-medium text-slate-700">...</span>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <?php if ($i == $currentPage): ?>
                                        <span aria-current="page" class="relative inline-flex items-center px-4 py-2 border borderColor bg-primary text-white text-sm font-medium">
                                            <?= $i ?>
                                        </span>
                                    <?php else: ?>
                                        <a href="<?= base_url('ksp/pillar-articles/' . $pillar['slug'] . '?' . http_build_query(array_merge($filters, ['page' => $i]))) ?>" 
                                           class="relative inline-flex items-center px-4 py-2 border borderColor bg-white text-sm font-medium text-slate-600 hover:bg-slate-50">
                                            <?= $i ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <?php if ($endPage < $totalPages): ?>
                                    <?php if ($endPage < $totalPages - 1): ?>
                                        <span class="relative inline-flex items-center px-4 py-2 border borderColor bg-white text-sm font-medium text-slate-700">...</span>
                                    <?php endif; ?>
                                    <a href="<?= base_url('ksp/pillar-articles/' . $pillar['slug'] . '?' . http_build_query(array_merge($filters, ['page' => $totalPages]))) ?>" 
                                       class="relative inline-flex items-center px-4 py-2 border borderColor bg-white text-sm font-medium text-slate-600 hover:bg-slate-50"><?= $totalPages ?></a>
                                <?php endif; ?>

                                <!-- Next Page -->
                                <?php if ($currentPage < $totalPages): ?>
                                    <a href="<?= base_url('ksp/pillar-articles/' . $pillar['slug'] . '?' . http_build_query(array_merge($filters, ['page' => $currentPage + 1]))) ?>" 
                                       class="relative inline-flex items-center px-2 py-2 rounded-r-md border borderColor bg-white text-sm font-medium text-slate-500 hover:bg-slate-50">
                                        <span class="sr-only">Next</span>
                                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border borderColor bg-slate-100 text-sm font-medium text-slate-400 cursor-not-allowed">
                                        <span class="sr-only">Next</span>
                                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                                    </span>
                                <?php endif; ?>
                            </nav>
                        </div>

                        <!-- Pagination Info -->
                        <div class="text-center text-sm text-slate-600 pb-5">
                            Showing <?= (($currentPage - 1) * $perPage) + 1 ?> to <?= min($currentPage * $perPage, $totalItems) ?> of <?= number_format($totalItems) ?> results
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Pillars Section -->
    <section class="py-16 bg-light">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Explore Our Other Pillars</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Governance -->
                <a href="/pillars/governance" class="group">
                    <div class="bg-white rounded-lg shadow-sm border borderColor overflow-hidden h-full transition-all hover:shadow-md">
                        <div class="h-40 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1450101499163-c8848c66ca85?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80')"></div>
                        <div class="p-6">
                            <div class="flex items-center mb-3">
                                <div class="bg-primaryShades-100 p-2 rounded-full mr-3 group-hover:bg-secondaryShades-100 transition-colors">
                                    <i data-lucide="scale" class="w-6 h-6 text-primary group-hover:text-secondary transition-colors"></i>
                                </div>
                                <h3 class="font-bold text-lg group-hover:text-secondary transition-colors">Governance</h3>
                            </div>
                            <p class="text-slate-600 text-sm line-clamp-2">Strengthening water sector governance through policy reform, accountability mechanisms, and institutional capacity building.</p>
                        </div>
                    </div>
                </a>
                
                <!-- Climate Change -->
                <a href="/pillars/climate" class="group">
                    <div class="bg-white rounded-lg shadow-sm border borderColor overflow-hidden h-full transition-all hover:shadow-md">
                        <div class="h-40 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1615796153287-98eacf0abb13?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80')"></div>
                        <div class="p-6">
                            <div class="flex items-center mb-3">
                                <div class="bg-primaryShades-100 p-2 rounded-full mr-3 group-hover:bg-secondaryShades-100 transition-colors">
                                    <i data-lucide="cloud-sun" class="w-6 h-6 text-primary group-hover:text-secondary transition-colors"></i>
                                </div>
                                <h3 class="font-bold text-lg group-hover:text-secondary transition-colors">Climate Change</h3>
                            </div>
                            <p class="text-slate-600 text-sm line-clamp-2">Developing climate-resilient water and sanitation solutions to mitigate and adapt to climate change impacts.</p>
                        </div>
                    </div>
                </a>
                
                <!-- NEXUS -->
                <a href="/pillars/nexus" class="group">
                    <div class="bg-white rounded-lg shadow-sm border borderColor overflow-hidden h-full transition-all hover:shadow-md">
                        <div class="h-40 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80')"></div>
                        <div class="p-6">
                            <div class="flex items-center mb-3">
                                <div class="bg-primaryShades-100 p-2 rounded-full mr-3 group-hover:bg-secondaryShades-100 transition-colors">
                                    <i data-lucide="link-2" class="w-6 h-6 text-primary group-hover:text-secondary transition-colors"></i>
                                </div>
                                <h3 class="font-bold text-lg group-hover:text-secondary transition-colors">NEXUS</h3>
                            </div>
                            <p class="text-slate-600 text-sm line-clamp-2">Promoting integrated approaches to water, energy, food security, and ecosystem management.</p>
                        </div>
                    </div>
                </a>
                
                <!-- IWRM -->
                <a href="/pillars/iwrm" class="group">
                    <div class="bg-white rounded-lg shadow-sm border borderColor overflow-hidden h-full transition-all hover:shadow-md">
                        <div class="h-40 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1466611653911-95081537e5b7?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80')"></div>
                        <div class="p-6">
                            <div class="flex items-center mb-3">
                                <div class="bg-primaryShades-100 p-2 rounded-full mr-3 group-hover:bg-secondaryShades-100 transition-colors">
                                    <i data-lucide="map" class="w-6 h-6 text-primary group-hover:text-secondary transition-colors"></i>
                                </div>
                                <h3 class="font-bold text-lg group-hover:text-secondary transition-colors">IWRM</h3>
                            </div>
                            <p class="text-slate-600 text-sm line-clamp-2">Advancing Integrated Water Resources Management for sustainable and equitable water use across sectors.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
    <script>
        // Filter functions
        function filterByCategory(categoryId) {
            const url = new URL(window.location.href);
            if (categoryId === 'all') {
                url.searchParams.delete('category');
            } else {
                url.searchParams.set('category', categoryId);
            }
            url.searchParams.delete('page'); // Reset to first page
            window.location.href = url.toString();
        }

        function filterByDocumentType(documentTypeId) {
            const url = new URL(window.location.href);
            if (documentTypeId === 'all') {
                url.searchParams.delete('document_type');
            } else {
                url.searchParams.set('document_type', documentTypeId);
            }
            url.searchParams.delete('page'); // Reset to first page
            window.location.href = url.toString();
        }

        function resetFilters() {
            const baseUrl = window.location.pathname;
            window.location.href = baseUrl;
        }

        function bookmarkResource(resourceId) {
            // Placeholder for bookmark functionality
            console.log('Bookmarking resource:', resourceId);
            // You can implement this with AJAX to save/remove bookmarks
        }

        // Sort functionality
        document.getElementById('sort')?.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', this.value);
            url.searchParams.delete('page'); // Reset to first page
            window.location.href = url.toString();
        });

        // Add reset filters button functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips or other UI components if needed
            console.log('Pillar articles page loaded');
        });
    </script>
<?= $this->endSection() ?>