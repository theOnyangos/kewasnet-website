<?php 
    use App\Helpers\UrlHelper;
    use Carbon\Carbon;

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
    <div class="h-full pb-8 relative">
        <!-- Diagonal Grid Pattern -->
        <div class="events-page-pattern-bg"></div>

        <!-- Page Header -->
        <section class="relative py-16 bg-gradient-to-r from-primary to-secondary">
            <div class="container mx-auto px-4 text-center text-white">
                <div class="max-w-4xl mx-auto">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">All Forums</h1>
                    <p class="text-xl md:text-2xl leading-relaxed">
                        Explore all discussion forums covering water, sanitation, and community topics.
                    </p>
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
                                <a href="<?= base_url('ksp/networking-corner') ?>" class="ml-1 text-sm font-medium text-slate-600 hover:text-primary md:ml-2">Networking Corner</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                                <span class="ml-1 text-sm font-medium text-primary md:ml-2">Forums</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="container mx-auto px-4">
                <!-- Filters and Search -->
                <div class="bg-white rounded-lg shadow-sm border borderColor p-6 mb-8 shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                    <form method="GET" action="<?= base_url('ksp/networking-corner/forums') ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search Input -->
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-slate-700 mb-2">
                                <i data-lucide="search" class="w-4 h-4 inline mr-1"></i>
                                Search Forums
                            </label>
                            <input type="text" id="search" name="search" value="<?= esc($search) ?>" 
                                placeholder="Search by forum name or description..." 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>

                        <!-- Sort By -->
                        <div>
                            <label for="sort" class="block text-sm font-medium text-slate-700 mb-2">
                                <i data-lucide="arrow-up-down" class="w-4 h-4 inline mr-1"></i>
                                Sort By
                            </label>
                            <select id="sort" name="sort" class="select2 w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                <option value="latest" <?= $sortBy == 'latest' ? 'selected' : '' ?>>Latest</option>
                                <option value="name" <?= $sortBy == 'name' ? 'selected' : '' ?>>Name (A-Z)</option>
                                <option value="popular" <?= $sortBy == 'popular' ? 'selected' : '' ?>>Most Popular</option>
                                <option value="oldest" <?= $sortBy == 'oldest' ? 'selected' : '' ?>>Oldest</option>
                            </select>
                        </div>

                        <!-- Filter Button -->
                        <div class="md:col-span-3 flex gap-3">
                            <button type="submit" class="gradient-btn px-6 py-2 rounded-md text-white font-medium flex items-center hover:opacity-90 transition-opacity">
                                <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                                Apply Filters
                            </button>
                            <a href="<?= base_url('ksp/networking-corner/forums') ?>" class="bg-slate-200 px-6 py-2 rounded-md text-slate-700 font-medium flex items-center hover:bg-slate-300 transition-colors">
                                <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                                Clear
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Forums List -->
                <?php if (!empty($forums)): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($forums as $forum): ?>
                            <div class="bg-white rounded-lg shadow-sm border borderColor hover:shadow-md transition-shadow duration-300 shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                                <div class="p-6">
                                    <!-- Forum Icon and Name -->
                                    <div class="flex items-start mb-4">
                                        <div class="bg-secondaryShades-100 p-3 rounded-full mr-4 flex-shrink-0">
                                            <i data-lucide="<?= esc($forum->icon ?? 'message-circle') ?>" class="w-6 h-6 text-secondary"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xl font-bold text-dark mb-2">
                                                <?= esc($forum->name) ?>
                                            </h3>
                                        </div>
                                    </div>

                                    <!-- Forum Description -->
                                    <p class="text-slate-600 mb-4 line-clamp-3">
                                        <?= esc($forum->description ?? '') ?>
                                    </p>

                                    <!-- Forum Stats -->
                                    <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500 mb-4">
                                        <span class="flex items-center">
                                            <i data-lucide="message-square" class="w-4 h-4 mr-1"></i>
                                            <?= esc($forum->discussion_count ?? 0) ?> discussions
                                        </span>
                                        <span class="flex items-center">
                                            <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                                            <?= Carbon::parse($forum->created_at)->diffForHumans() ?>
                                        </span>
                                    </div>

                                    <!-- View Forum Button -->
                                    <a href="<?= base_url('ksp/networking-corner-forum-discussion/' . $forum->slug) ?>" 
                                    class="block w-full gradient-btn px-4 py-2 rounded-md text-white font-medium text-center hover:opacity-90 transition-opacity">
                                        <span class="flex items-center justify-center">
                                            View Forum
                                            <i data-lucide="arrow-right" class="ml-2 w-4 h-4"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($pager->getPageCount() > 1): ?>
                        <div class="bg-white rounded-lg shadow-sm border borderColor p-4 mt-8 shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                            <div class="flex justify-center">
                                <?= $pager->links('default', 'custom_pagination') ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="bg-white rounded-lg shadow-sm border borderColor p-12 text-center shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                        <div class="max-w-md mx-auto">
                            <div class="bg-secondaryShades-50 p-6 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6">
                                <i data-lucide="folder" class="w-12 h-12 text-secondary"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-dark mb-3">No Forums Found</h3>
                            <p class="text-slate-600 mb-6">
                                <?php if (!empty($search)): ?>
                                    No forums match your search criteria. Try adjusting your filters.
                                <?php else: ?>
                                    No forums have been created yet. Check back soon!
                                <?php endif; ?>
                            </p>
                            <div class="flex justify-center gap-3">
                                <?php if (!empty($search)): ?>
                                    <a href="<?= base_url('ksp/networking-corner/forums') ?>" class="gradient-btn px-6 py-3 rounded-md text-white font-medium inline-flex items-center">
                                        <i data-lucide="x" class="mr-2 w-5 h-5"></i>
                                        Clear Filters
                                    </a>
                                <?php endif; ?>
                                <a href="<?= base_url('ksp/networking-corner') ?>" class="bg-slate-200 px-6 py-3 rounded-md text-slate-700 font-medium inline-flex items-center hover:bg-slate-300">
                                    <i data-lucide="arrow-left" class="mr-2 w-5 h-5"></i>
                                    Back to Networking Corner
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
        </div>
    </div>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Auto-submit form on select change
    document.getElementById('sort')?.addEventListener('change', function() {
        this.form.submit();
    });
</script>
<?= $this->endSection() ?>
