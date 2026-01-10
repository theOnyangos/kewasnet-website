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
    <section class="bg-gradient-to-r from-primary to-secondary py-12 text-white">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">All Courses</h1>
            <p class="text-lg text-white/90">Explore our comprehensive course catalog</p>
        </div>
    </section>

    <!-- Breadcrumb -->
    <div class="sticky top-[110px] z-40 bg-white border-b border-slate-200 shadow-sm">
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
                            <a href="<?= base_url('ksp/learning-hub') ?>" class="ml-1 text-sm font-medium text-slate-600 hover:text-primary md:ml-2">Learning Hub</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                            <span class="ml-1 text-sm font-medium text-primary md:ml-2">All Courses</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <main class="py-8 relative">
        <!-- Diagonal Grid Pattern -->
        <div class="events-page-pattern-bg"></div>

        <div class="container mx-auto px-4 flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="lg:w-1/4">
                <div class="space-y-6">
                    <!-- Search Filter -->
                    <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                        <h3 class="text-lg font-bold text-dark mb-4">Search</h3>
                        <form method="GET" action="<?= base_url('ksp/learning-hub/courses') ?>" id="sidebarSearchForm">
                            <input type="hidden" name="filter" value="<?= esc($filter) ?>">
                            <input type="hidden" name="category" value="<?= esc($category ?? '') ?>">
                            <input type="hidden" name="level" value="<?= esc($level ?? '') ?>">
                            <div class="relative">
                                <input type="text" 
                                    name="search" 
                                    value="<?= esc($search ?? '') ?>"
                                    placeholder="Search courses..." 
                                    class="w-full px-4 py-2 pl-10 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <i data-lucide="search" class="absolute left-3 top-3 w-4 h-4 text-slate-400"></i>
                            </div>
                            <button type="submit" class="mt-3 w-full bg-primary text-white py-2 rounded-lg hover:bg-primary-dark transition-colors">
                                Search
                            </button>
                        </form>
                    </div>

                    <!-- Course Type Filter -->
                    <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                        <h3 class="text-lg font-bold text-dark mb-4">Course Type</h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="<?= base_url('ksp/learning-hub/courses?' . http_build_query(array_filter(['search' => $search, 'category' => $category, 'level' => $level]))) ?>" 
                                class="flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 transition-colors <?= $filter === 'all' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-700' ?>">
                                    <span>All Courses</span>
                                    <span class="text-sm text-slate-500"><?= $pagination['total_courses'] ?? 0 ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('ksp/learning-hub/courses?' . http_build_query(array_filter(['filter' => 'free', 'search' => $search, 'category' => $category, 'level' => $level]))) ?>" 
                                class="flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 transition-colors <?= $filter === 'free' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-700' ?>">
                                    <span>Free Courses</span>
                                    <span class="text-sm text-slate-500"><?= $total_free ?? 0 ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('ksp/learning-hub/courses?' . http_build_query(array_filter(['filter' => 'paid', 'search' => $search, 'category' => $category, 'level' => $level]))) ?>" 
                                class="flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 transition-colors <?= $filter === 'paid' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-700' ?>">
                                    <span>Paid Courses</span>
                                    <span class="text-sm text-slate-500"><?= $total_paid ?? 0 ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Level Filter -->
                    <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                        <h3 class="text-lg font-bold text-dark mb-4">Level</h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="<?= base_url('ksp/learning-hub/courses?' . http_build_query(array_filter(['filter' => $filter !== 'all' ? $filter : null, 'search' => $search, 'category' => $category]))) ?>" 
                                class="block p-3 rounded-lg hover:bg-slate-50 transition-colors <?= empty($level) ? 'bg-primary/10 text-primary font-medium' : 'text-slate-700' ?>">
                                    All Levels
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('ksp/learning-hub/courses?' . http_build_query(array_filter(['filter' => $filter !== 'all' ? $filter : null, 'level' => 'beginner', 'search' => $search, 'category' => $category]))) ?>" 
                                class="block p-3 rounded-lg hover:bg-slate-50 transition-colors <?= $level === 'beginner' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-700' ?>">
                                    Beginner
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('ksp/learning-hub/courses?' . http_build_query(array_filter(['filter' => $filter !== 'all' ? $filter : null, 'level' => 'intermediate', 'search' => $search, 'category' => $category]))) ?>" 
                                class="block p-3 rounded-lg hover:bg-slate-50 transition-colors <?= $level === 'intermediate' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-700' ?>">
                                    Intermediate
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('ksp/learning-hub/courses?' . http_build_query(array_filter(['filter' => $filter !== 'all' ? $filter : null, 'level' => 'advanced', 'search' => $search, 'category' => $category]))) ?>" 
                                class="block p-3 rounded-lg hover:bg-slate-50 transition-colors <?= $level === 'advanced' ? 'bg-primary/10 text-primary font-medium' : 'text-slate-700' ?>">
                                    Advanced
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Categories Filter -->
                    <?php if (!empty($categories)): ?>
                    <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                        <h3 class="text-lg font-bold text-dark mb-4">Categories</h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="<?= base_url('ksp/learning-hub/courses?' . http_build_query(array_filter(['filter' => $filter !== 'all' ? $filter : null, 'level' => $level, 'search' => $search]))) ?>" 
                                class="flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 transition-colors <?= empty($category) ? 'bg-primary/10 text-primary font-medium' : 'text-slate-700' ?>">
                                    <span>All Categories</span>
                                </a>
                            </li>
                            <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="<?= base_url('ksp/learning-hub/courses?' . http_build_query(array_filter(['filter' => $filter !== 'all' ? $filter : null, 'category' => $cat['id'], 'level' => $level, 'search' => $search]))) ?>" 
                                class="flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 transition-colors <?= $category == $cat['id'] ? 'bg-primary/10 text-primary font-medium' : 'text-slate-700' ?>">
                                    <span><?= esc($cat['name']) ?></span>
                                    <span class="text-sm text-slate-500"><?= $cat['course_count'] ?? 0 ?></span>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Advertising Section -->
                    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-dark mb-4">Featured Program</h3>
                            <div class="space-y-4">
                                <a href="#" class="block group">
                                    <div class="relative overflow-hidden rounded-lg">
                                        <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                                            alt="Water Management Training" 
                                            class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                        <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                                            <h4 class="font-bold text-lg mb-1">Advanced Water Management</h4>
                                            <p class="text-sm text-white/90">Professional Certification Program</p>
                                        </div>
                                    </div>
                                </a>
                                
                                <a href="#" class="block group">
                                    <div class="relative overflow-hidden rounded-lg">
                                        <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                                            alt="Climate Adaptation Course" 
                                            class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                        <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                                            <h4 class="font-bold text-lg mb-1">Climate Adaptation</h4>
                                            <p class="text-sm text-white/90">Specialized Training Series</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    <?php if ($search || $filter !== 'all' || $level || $category): ?>
                    <div class="bg-white rounded-lg border border-slate-200 p-6">
                        <a href="<?= base_url('ksp/learning-hub/courses') ?>" class="block w-full text-center bg-slate-100 text-slate-700 py-2 rounded-lg hover:bg-slate-200 transition-colors font-medium">
                            Clear All Filters
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="lg:w-3/4">
                <!-- Results Header -->
                <div class="bg-white rounded-lg border border-slate-200 p-6 mb-6 shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-dark mb-2">All Courses</h2>
                            <p class="text-slate-600">
                                Showing <?= count($courses) ?> of <?= $pagination['total_courses'] ?? 0 ?> courses
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Courses Grid -->
                <?php if (empty($courses)): ?>
                    <div class="bg-white rounded-lg border border-slate-200 p-12 text-center">
                        <i data-lucide="book-open" class="w-16 h-16 text-slate-400 mx-auto mb-4"></i>
                        <h3 class="text-xl font-bold text-dark mb-2">No courses found</h3>
                        <p class="text-slate-600 mb-6">Try adjusting your filters to find what you're looking for.</p>
                        <a href="<?= base_url('ksp/learning-hub/courses') ?>" class="inline-block bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-dark transition-colors">
                            Clear Filters
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <?php foreach ($courses as $course): ?>
                            <?= view('frontendV2/ksp/pages/learning-hub/components/course-card', ['course' => $course]) ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($pagination['total_pages'] > 1): ?>
                        <div class="bg-white rounded-lg border border-slate-200 p-6">
                            <nav aria-label="Course pagination" class="flex items-center justify-center space-x-2">
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
                                    <?php
                                    $pageUrl = $pagination['url_prefix'] . 'page=1';
                                    ?>
                                    <a href="<?= $pageUrl ?>" 
                                    class="flex items-center justify-center w-10 h-10 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 transition-colors">
                                        1
                                    </a>
                                    <?php if ($startPage > 2): ?>
                                        <span class="px-2 text-slate-400">...</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <?php
                                    $pageUrl = $pagination['url_prefix'] . 'page=' . $i;
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
                                    $lastPageUrl = $pagination['url_prefix'] . 'page=' . $pagination['total_pages'];
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
                            
                            <p class="mt-4 text-center text-sm text-slate-600">
                                Page <?= $pagination['current_page'] ?> of <?= $pagination['total_pages'] ?>
                            </p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    lucide.createIcons();
});
</script>
<?= $this->endSection() ?>

