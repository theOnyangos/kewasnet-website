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
                                    <div class="text-2xl font-bold text-white"><?= number_format($totalContributors) ?></div>
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
    <section class="bg-gray-50 py-4 border-b border-gray-200">
        <div class="container mx-auto px-4">
            <nav class="flex items-center space-x-2 text-sm text-gray-600" aria-label="Breadcrumb">
                <a href="<?= base_url() ?>" class="hover:text-primary transition-colors">Home</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <a href="<?= base_url('ksp') ?>" class="hover:text-primary transition-colors">Knowledge Sharing Platform</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <a href="<?= base_url('ksp/pillars') ?>" class="hover:text-primary transition-colors">Pillars</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span class="text-primary font-medium"><?= esc($pillar['slug']) ?></span>
            </nav>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-12 bg-gray-50">
        <!-- Diagonal Grid Pattern -->
        <div class="events-page-pattern-bg"></div>

        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-4 gap-8">
                <!-- Sidebar -->
                <aside class="lg:col-span-1 hover:shadow-md transition-shadow">
                    <div class="bg-white rounded-xl shadow-sm border p-6 sticky top-8">
                        <!-- Search -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-dark mb-4 pb-2 border-b border-gray-200 flex items-center">
                                <i data-lucide="search" class="w-5 h-5 mr-2 text-primary"></i>
                                Search Resources
                            </h3>
                            <form method="GET" class="space-y-4">
                                <div class="relative">
                                    <input type="text" name="search" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Search <?= esc($pillar['title']) ?> resources..." 
                                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                </div>
                                <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg hover:bg-primary/90 transition-colors font-medium">
                                    Search
                                </button>
                            </form>
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-dark mb-4 pb-2 border-b border-gray-200 flex items-center">
                                <i data-lucide="tag" class="w-5 h-5 mr-2 text-primary"></i>
                                Categories
                            </h3>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="radio" name="category_filter" value="all" <?= empty($filters['category']) || $filters['category'] === 'all' ? 'checked' : '' ?> 
                                           class="text-primary focus:ring-primary" onchange="filterByCategory('all')">
                                    <span>All Categories (<?= number_format($totalResources ?? 0) ?>)</span>
                                </label>
                                <?php if (!empty($categories) && is_array($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <?php 
                                            $categoryId = is_array($category) ? ($category['id'] ?? '') : ($category->id ?? '');
                                            $categoryName = is_array($category) ? ($category['name'] ?? 'Unnamed') : ($category->name ?? 'Unnamed');
                                            $resourceCount = is_array($category) ? ($category['resource_count'] ?? 0) : ($category->resource_count ?? 0);
                                        ?>
                                        <?php if (!empty($categoryId)): ?>
                                            <label class="flex items-center space-x-3 cursor-pointer">
                                                <input type="radio" name="category_filter" value="<?= esc($categoryId) ?>" 
                                                       <?= ($filters['category'] ?? '') == $categoryId ? 'checked' : '' ?>
                                                       class="text-primary focus:ring-primary" onchange="filterByCategory('<?= esc($categoryId) ?>')">
                                                <span><?= esc($categoryName) ?> (<?= number_format($resourceCount) ?>)</span>
                                            </label>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-sm text-gray-500 italic">No categories available for this pillar.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Document Type Filter -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-dark mb-4 pb-2 border-b border-gray-200 flex items-center">
                                <i data-lucide="file-type" class="w-5 h-5 mr-2 text-primary"></i>
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
                                            <input type="radio" name="document_type_filter" value="<?= is_array($docType) ? $docType['value'] : $docType->value ?>" 
                                                   <?= ($filters['document_type'] ?? '') == (is_array($docType) ? $docType['value'] : $docType->value) ? 'checked' : '' ?>
                                                   class="text-primary focus:ring-primary" onchange="filterByDocumentType('<?= is_array($docType) ? $docType['value'] : $docType->value ?>')">
                                            <span>
                                                <i class="fa fa-file w-4 h-4 mr-1 text-secondary"></i>
                                                <?= esc(is_array($docType) ? $docType['label'] : $docType->label) ?>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Reset Filters -->
                        <button onclick="resetFilters()" class="flex justify-center items-center gap-2 w-full bg-primaryShades-100 text-primary py-3 rounded-lg hover:bg-primaryShades-200 transition-colors font-medium">
                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                            Reset Filters
                        </button>
                    </div>
                </aside>

                <!-- Main Content Area -->
                <main class="lg:col-span-3">
                    <!-- Header with Sort -->
                    <div class="bg-white rounded-xl shadow-md border border-slate-200 p-6 mb-6 hover:shadow-2xl transition-all duration-300 relative z-10">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-dark mb-2"><?= esc($pillar['title']) ?> Resources</h2>
                                <p class="text-gray-600">
                                    Showing <?= number_format(count($resources)) ?> of <?= number_format($totalItems) ?> resources
                                    <?php if (!empty($filters['search'])): ?>
                                        for "<?= esc($filters['search']) ?>"
                                    <?php endif; ?>
                                </p>
                            </div>

                            <div class="mt-4 md:mt-0">
                                <select id="sort" name="sort" class="select2 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="newest" <?= ($filters['sort'] ?? 'newest') === 'newest' ? 'selected' : '' ?>>Newest First</option>
                                    <option value="oldest" <?= ($filters['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                                    <option value="title" <?= ($filters['sort'] ?? '') === 'title' ? 'selected' : '' ?>>Title A-Z</option>
                                    <option value="downloads" <?= ($filters['sort'] ?? '') === 'downloads' ? 'selected' : '' ?>>Most Downloaded</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Resources Grid -->
                    <?php if (!empty($resources)): ?>
                        <div class="grid md:grid-cols-2 gap-6 mb-8">
                            <?php foreach ($resources as $resource): ?>
                                <article class="bg-white rounded-xl shadow-sm border hover:shadow-2xl transition-shadow flex flex-col justify-between overflow-hidden shadow-md z-10">
                                    <!-- Resource Image -->
                                    <div class="mb-4">
                                        <?php 
                                            $imageUrl = is_array($resource) ? ($resource['image_url'] ?? '') : ($resource->image_url ?? '');
                                            if (empty($imageUrl)) {
                                                $imageUrl = base_url('assets/images/placeholder-resource.jpg');
                                            }
                                        ?>
                                        <img src="<?= esc($imageUrl) ?>" alt="<?= esc(is_array($resource) ? ($resource['title'] ?? '') : ($resource->title ?? '')) ?>" 
                                             class="w-full h-48 object-cover rounded-t-lg" 
                                             onerror="this.src='<?= base_url('assets/images/placeholder-resource.jpg') ?>'">
                                    </div>
                                    
                                    <div class="px-6">
                                        <!-- Resource Header -->
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex-1">
                                                <div class="flex items-center mb-2 flex-wrap gap-2">
                                                    <?php 
                                                        $docTypeName = is_array($resource) ? ($resource['document_type_name'] ?? 'Document') : ($resource->document_type_name ?? 'Document');
                                                        $docTypeColor = is_array($resource) ? ($resource['document_type_color'] ?? '#6B7280') : ($resource->document_type_color ?? '#6B7280');
                                                    ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: <?= esc($docTypeColor) ?>20; color: <?= esc($docTypeColor) ?>;">
                                                        <i data-lucide="file-type" class="w-3 h-3 mr-1"></i>
                                                        <?= esc($docTypeName) ?>
                                                    </span>
                                                    <?php if (!empty(is_array($resource) ? $resource['category_name'] : $resource->category_name)): ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                            <?= esc(is_array($resource) ? $resource['category_name'] : $resource->category_name) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <h3 class="text-lg font-semibold text-primary hover:text-secondary mb-2 line-clamp-2">
                                                    <a href="<?= base_url('ksp/pillar-article/' . (is_array($resource) ? $resource['slug'] : $resource->slug)) ?>">
                                                        <?= esc(is_array($resource) ? $resource['title'] : $resource->title) ?>
                                                    </a>
                                                </h3>
                                                <p class="text-gray-600 text-sm line-clamp-3 mb-4">
                                                    <?= esc(is_array($resource) ? ($resource['description'] ?? 'No description available.') : ($resource->description ?? 'No description available.')) ?>
                                                </p>
                                            </div>
                                            <?php 
                                            $resourceId = is_array($resource) ? $resource['id'] : $resource->id;
                                            $isBookmarked = isset($bookmarkedResources[$resourceId]) && $bookmarkedResources[$resourceId];
                                            ?>
                                            <?php if ($isLoggedIn): ?>
                                                <button id="bookmark-btn-<?= $resourceId ?>" 
                                                        onclick="bookmarkResource('<?= $resourceId ?>')" 
                                                        class="text-gray-400 hover:text-primary transition-colors ml-4 <?= $isBookmarked ? 'text-secondary' : '' ?>">
                                                    <i id="bookmark-icon-<?= $resourceId ?>" data-lucide="bookmark" class="w-5 h-5 <?= $isBookmarked ? 'fill-secondary' : '' ?>"></i>
                                                </button>
                                            <?php else: ?>
                                                <a href="<?= base_url('ksp/login') ?>" 
                                                   class="text-gray-400 hover:text-primary transition-colors ml-4">
                                                    <i data-lucide="bookmark" class="w-5 h-5"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Resource Meta -->
                                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                            <div class="flex items-center space-x-4">
                                                <span class="flex items-center">
                                                    <i data-lucide="calendar" class="w-4 h-4 mr-1"></i>
                                                    <?= date('M j, Y', strtotime(is_array($resource) ? $resource['created_at'] : $resource->created_at)) ?>
                                                </span>
                                                <span class="flex items-center">
                                                    <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                                                    <?= number_format(is_array($resource) ? ($resource['total_downloads'] ?? 0) : ($resource->total_downloads ?? 0)) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center justify-between p-6 border-t border-gray-100 bg-primaryShades-50">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs text-gray-500">
                                                <?= esc(is_array($resource) ? (format_file_size($resource['total_file_size']) ?? '0 KB') : (format_file_size($resource->total_file_size) ?? '0 KB')) ?>
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button class="text-gray-400 hover:text-primary transition-colors">
                                                <i data-lucide="share" class="w-4 h-4"></i>
                                            </button>
                                            <a href="<?= base_url('ksp/pillar-article/' . (is_array($resource) ? $resource['slug'] : $resource->slug)) ?>" class="text-sm font-medium text-secondary flex items-center hover:text-secondary/80">
                                                View Details
                                                <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- Empty State -->
                        <div class="bg-white rounded-xl shadow-sm border p-12 text-center relative z-10">
                            <div class="max-w-md mx-auto">
                                <i data-lucide="file-search" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Resources Found</h3>
                                <p class="text-gray-500 mb-6">We couldn't find any resources matching your criteria. Try adjusting your filters or search terms.</p>
                                <a href="<?= base_url('ksp/pillar-articles/' . $pillar['slug']) ?>" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                                    Reset Filters
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="bg-white rounded-xl shadow-sm border p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <?php if ($currentPage > 1): ?>
                                        <a href="<?= base_url('ksp/pillar-articles/' . $pillar['slug'] . '?' . http_build_query(array_merge($filters, ['page' => $currentPage - 1]))) ?>" 
                                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                            Previous
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <div class="flex items-center space-x-1">
                                    <?php
                                    $startPage = max(1, $currentPage - 2);
                                    $endPage = min($totalPages, $currentPage + 2);
                                    ?>

                                    <?php if ($startPage > 1): ?>
                                        <a href="<?= base_url('ksp/pillar-articles/' . $pillar['slug'] . '?' . http_build_query(array_merge($filters, ['page' => 1]))) ?>" 
                                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                            1
                                        </a>
                                        <?php if ($startPage > 2): ?>
                                            <span class="px-3 py-2 text-sm font-medium text-gray-500">...</span>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                        <?php if ($i == $currentPage): ?>
                                            <span class="px-3 py-2 text-sm font-medium text-white bg-primary border border-primary rounded-md">
                                                <?= $i ?>
                                            </span>
                                        <?php else: ?>
                                            <a href="<?= base_url('ksp/pillar-articles/' . $pillar['slug'] . '?' . http_build_query(array_merge($filters, ['page' => $i]))) ?>" 
                                               class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                                <?= $i ?>
                                            </a>
                                        <?php endif; ?>
                                    <?php endfor; ?>

                                    <?php if ($endPage < $totalPages): ?>
                                        <?php if ($endPage < $totalPages - 1): ?>
                                            <span class="px-3 py-2 text-sm font-medium text-gray-500">...</span>
                                        <?php endif; ?>
                                        <a href="<?= base_url('ksp/pillar-articles/' . $pillar['slug'] . '?' . http_build_query(array_merge($filters, ['page' => $totalPages]))) ?>" 
                                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                            <?= $totalPages ?>
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <?php if ($currentPage < $totalPages): ?>
                                        <a href="<?= base_url('ksp/pillar-articles/' . $pillar['slug'] . '?' . http_build_query(array_merge($filters, ['page' => $currentPage + 1]))) ?>" 
                                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                            Next
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </main>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script>
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
            console.log('Toggle bookmark called for resource:', resourceId);
            
            $.ajax({
                url: '<?= base_url('ksp/api/resource/toggle-bookmark') ?>',
                type: 'POST',
                data: {
                    resource_id: resourceId,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                beforeSend: function() {
                    $('#bookmark-btn-' + resourceId).prop('disabled', true).addClass('opacity-50');
                },
                success: function(response) {
                    console.log('Bookmark response:', response);
                    if (response.success) {
                        // Update bookmark state
                        const bookmarkIcon = $('#bookmark-icon-' + resourceId);
                        const bookmarkBtn = $('#bookmark-btn-' + resourceId);
                        
                        if (response.bookmarked) {
                            bookmarkIcon.addClass('fill-secondary');
                            bookmarkBtn.addClass('text-secondary');
                        } else {
                            bookmarkIcon.removeClass('fill-secondary');
                            bookmarkBtn.removeClass('text-secondary');
                        }
                        
                        // Re-initialize Lucide icons
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                        
                        // Show success message
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 2000,
                                toast: true,
                                position: 'top-end'
                            });
                        }
                    } else {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'An error occurred. Please try again.',
                                    confirmButtonText: 'OK'
                                });
                            }
                        }
                    }
                },
                error: function(xhr) {
                    console.error('Bookmark error:', xhr);
                    console.error('Response:', xhr.responseJSON);
                    const errorMsg = xhr.responseJSON?.message || 'An error occurred. Please try again.';
                    if (xhr.status === 401 && xhr.responseJSON?.redirect) {
                        window.location.href = xhr.responseJSON.redirect;
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMsg + (xhr.responseJSON?.errors ? '\n' + JSON.stringify(xhr.responseJSON.errors) : ''),
                                confirmButtonText: 'OK'
                            });
                        } else {
                            alert('Error: ' + errorMsg);
                        }
                    }
                },
                complete: function() {
                    $('#bookmark-btn-' + resourceId).prop('disabled', false).removeClass('opacity-50');
                }
            });
        }

        // Sort functionality
        document.getElementById('sort')?.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', this.value);
            url.searchParams.delete('page'); // Reset to first page
            window.location.href = url.toString();
        });
    </script>
<?= $this->endSection() ?>