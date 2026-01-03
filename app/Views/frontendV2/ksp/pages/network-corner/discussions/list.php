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
    <div class="bg-light min-h-screen">
        <!-- Page Header -->
        <section class="relative py-16 bg-gradient-to-r from-primary to-secondary">
            <div class="container mx-auto px-4 text-center text-white">
                <div class="max-w-4xl mx-auto">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">All Discussions</h1>
                    <p class="text-xl md:text-2xl leading-relaxed">
                        Explore conversations, share insights, and connect with the community.
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
                                <span class="ml-1 text-sm font-medium text-primary md:ml-2">Discussions</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-12">
            <!-- Filters and Search -->
            <div class="bg-white rounded-lg shadow-sm border borderColor p-6 mb-8">
                <form method="GET" action="<?= base_url('ksp/networking-corner-discussions') ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search Input -->
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-slate-700 mb-2">
                            <i data-lucide="search" class="w-4 h-4 inline mr-1"></i>
                            Search Discussions
                        </label>
                        <input type="text" id="search" name="search" value="<?= esc($search) ?>" 
                               placeholder="Search by title or content..." 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    </div>

                    <!-- Forum Filter -->
                    <div>
                        <label for="forum" class="block text-sm font-medium text-slate-700 mb-2">
                            <i data-lucide="filter" class="w-4 h-4 inline mr-1"></i>
                            Filter by Forum
                        </label>
                        <select id="forum" name="forum" class="select2 w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            <option value="">All Forums</option>
                            <?php foreach ($forums as $forumOption): ?>
                                <option value="<?= esc($forumOption->id) ?>" <?= $selectedForum == $forumOption->id ? 'selected' : '' ?>>
                                    <?= esc($forumOption->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label for="sort" class="block text-sm font-medium text-slate-700 mb-2">
                            <i data-lucide="arrow-up-down" class="w-4 h-4 inline mr-1"></i>
                            Sort By
                        </label>
                        <select id="sort" name="sort" class="select2 w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            <option value="latest" <?= $sortBy == 'latest' ? 'selected' : '' ?>>Latest</option>
                            <option value="popular" <?= $sortBy == 'popular' ? 'selected' : '' ?>>Most Liked</option>
                            <option value="mostReplies" <?= $sortBy == 'mostReplies' ? 'selected' : '' ?>>Most Replies</option>
                            <option value="mostViewed" <?= $sortBy == 'mostViewed' ? 'selected' : '' ?>>Most Viewed</option>
                        </select>
                    </div>

                    <!-- Filter Button -->
                    <div class="md:col-span-4 flex gap-3">
                        <button type="submit" class="gradient-btn px-6 py-2 rounded-md text-white font-medium flex items-center hover:opacity-90 transition-opacity">
                            <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                            Apply Filters
                        </button>
                        <a href="<?= base_url('ksp/networking-corner-discussions') ?>" class="bg-slate-200 px-6 py-2 rounded-md text-slate-700 font-medium flex items-center hover:bg-slate-300 transition-colors">
                            <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Discussions List -->
            <?php if (!empty($discussions)): ?>
                <div class="space-y-4">
                    <?php foreach ($discussions as $discussion): ?>
                        <div class="bg-white rounded-lg shadow-sm border borderColor hover:shadow-md transition-shadow duration-300">
                            <div class="p-6">
                                <div class="flex items-start space-x-4">
                                    <!-- User Avatar -->
                                    <?php if (!empty($discussion->user_avatar)): ?>
                                        <img src="<?= esc($discussion->user_avatar) ?>" alt="User" class="w-12 h-12 rounded-full flex-shrink-0">
                                    <?php else: ?>
                                        <div class="w-12 h-12 rounded-full bg-secondary flex items-center justify-center text-white flex-shrink-0">
                                            <i data-lucide="user-circle" class="w-6 h-6"></i>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Discussion Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex-1">
                                                <a href="<?= base_url('ksp/discussion/' . $discussion->slug . '/view') ?>" class="text-xl font-bold text-dark hover:text-primary transition-colors">
                                                    <?= esc($discussion->title) ?>
                                                </a>
                                                <div class="flex flex-wrap items-center gap-2 mt-1 text-sm text-slate-500">
                                                    <span class="flex items-center">
                                                        <i data-lucide="user" class="w-4 h-4 mr-1"></i>
                                                        <?= esc($discussion->posted_by ?? 'Unknown') ?>
                                                    </span>
                                                    <span>•</span>
                                                    <span class="flex items-center">
                                                        <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                                                        <?= Carbon::parse($discussion->created_at)->diffForHumans() ?>
                                                    </span>
                                                    <?php if (!empty($discussion->forum_name)): ?>
                                                        <span>•</span>
                                                        <span class="flex items-center">
                                                            <i data-lucide="folder" class="w-4 h-4 mr-1"></i>
                                                            <?= esc($discussion->forum_name) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Discussion Preview -->
                                        <p class="text-slate-600 mb-4 line-clamp-2">
                                            <?= strip_tags($discussion->content ?? '') ?>
                                        </p>

                                        <!-- Discussion Stats -->
                                        <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500">
                                            <span class="flex items-center">
                                                <i data-lucide="thumbs-up" class="w-4 h-4 mr-1"></i>
                                                <?= esc($discussion->like_count ?? 0) ?> likes
                                            </span>
                                            <span class="flex items-center">
                                                <i data-lucide="message-square" class="w-4 h-4 mr-1"></i>
                                                <?= esc($discussion->reply_count ?? 0) ?> replies
                                            </span>
                                            <span class="flex items-center">
                                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                                <?= esc($discussion->view_count ?? 0) ?> views
                                            </span>
                                            <a href="<?= base_url('ksp/discussion/' . $discussion->slug . '/view') ?>" class="ml-auto text-primary font-medium hover:text-secondary transition-colors flex items-center">
                                                Read more
                                                <i data-lucide="arrow-right" class="ml-1 w-4 h-4"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($pager->getPageCount() > 1): ?>
                    <div class="bg-white rounded-lg shadow-sm border borderColor p-4 mt-8">
                        <div class="flex justify-center">
                            <?= $pager->links('default', 'custom_pagination') ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-sm border borderColor p-12 text-center">
                    <div class="max-w-md mx-auto">
                        <div class="bg-secondaryShades-50 p-6 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6">
                            <i data-lucide="message-square" class="w-12 h-12 text-secondary"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-dark mb-3">No Discussions Found</h3>
                        <p class="text-slate-600 mb-6">
                            <?php if (!empty($search) || !empty($selectedForum)): ?>
                                No discussions match your search criteria. Try adjusting your filters.
                            <?php else: ?>
                                No discussions have been created yet. Be the first to start a conversation!
                            <?php endif; ?>
                        </p>
                        <div class="flex justify-center gap-3">
                            <?php if (!empty($search) || !empty($selectedForum)): ?>
                                <a href="<?= base_url('ksp/networking-corner-discussions') ?>" class="gradient-btn px-6 py-3 rounded-md text-white font-medium inline-flex items-center">
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
        </main>
    </div>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Auto-submit form on select change
    document.getElementById('forum')?.addEventListener('change', function() {
        this.form.submit();
    });

    document.getElementById('sort')?.addEventListener('change', function() {
        this.form.submit();
    });
</script>
<?= $this->endSection() ?>
