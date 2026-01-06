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
    <div class="py-8 relative">
        <!-- Diagonal Grid Pattern -->
        <div class="events-page-pattern-bg"></div>

        <div class="container mx-auto px-4 pt-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-dark mb-2">Dashboard</h1>
                <p class="text-slate-600">Welcome back! Here's your learning overview.</p>
            </div>
            
            <!-- Advertising Banner -->
            <div class="mb-8 rounded-xl overflow-hidden shadow-lg relative min-h-[280px] md:min-h-[320px] bg-cover bg-center" style="background-image: linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.8) 100%), url('<?= base_url('images/users-dashboard-burner.jpg') ?>');">
                
                <!-- Content -->
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between p-6 md:p-8 min-h-[280px] md:min-h-[320px]">
                    <div class="flex-1 text-white mb-4 md:mb-0">
                        <div class="flex items-center mb-3">
                            <span class="bg-white/20 text-white text-xs font-semibold px-3 py-1 rounded-full backdrop-blur-sm">LIMITED TIME OFFER</span>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-bold mb-2 drop-shadow-lg">Advance Your Career with Professional Certification</h2>
                        <p class="text-white/90 mb-4 text-lg drop-shadow-md">Complete any 3 courses and get 20% off on your next certification exam</p>
                        <div class="flex flex-wrap items-center gap-4 text-sm">
                            <div class="flex items-center">
                                <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                                <span>Expert-Led Courses</span>
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                                <span>Industry Recognized</span>
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                                <span>Lifetime Access</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="<?= base_url('ksp/learning-hub/courses') ?>" class="bg-white text-primary px-8 py-3 rounded-full font-semibold text-lg hover:bg-slate-50 transition-all duration-300 shadow-lg inline-flex items-center">
                            Browse Courses
                            <i data-lucide="arrow-right" class="ml-2 w-5 h-5"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-600 mb-1">Total Courses</p>
                            <p class="text-3xl font-bold text-dark"><?= $total_courses ?></p>
                        </div>
                        <div class="bg-primary/10 p-3 rounded-full">
                            <i data-lucide="book-open" class="w-6 h-6 text-primary"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-600 mb-1">In Progress</p>
                            <p class="text-3xl font-bold text-dark"><?= $in_progress ?></p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <i data-lucide="play-circle" class="w-6 h-6 text-yellow-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-600 mb-1">Completed</p>
                            <p class="text-3xl font-bold text-dark"><?= $completed ?></p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-600 mb-1">Certificates</p>
                            <p class="text-3xl font-bold text-dark"><?= $certificates ?></p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-full">
                            <i data-lucide="award" class="w-6 h-6 text-purple-600"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <a href="<?= base_url('ksp/learning-hub/my-courses') ?>" 
                class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow z-10 relative">
                    <div class="flex items-center gap-4">
                        <div class="bg-primary/10 p-3 rounded-full">
                            <i data-lucide="book-open" class="w-6 h-6 text-primary"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-dark mb-1">My Courses</h3>
                            <p class="text-sm text-slate-600">Continue learning</p>
                        </div>
                    </div>
                </a>
                
                <a href="<?= base_url('ksp/learning-hub/certificates') ?>" 
                class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow z-10 relative">
                    <div class="flex items-center gap-4">
                        <div class="bg-purple-100 p-3 rounded-full">
                            <i data-lucide="award" class="w-6 h-6 text-purple-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-dark mb-1">Certificates</h3>
                            <p class="text-sm text-slate-600">View your achievements</p>
                        </div>
                    </div>
                </a>
                
                <a href="<?= base_url('ksp/learning-hub/profile') ?>" 
                class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow z-10 relative">
                    <div class="flex items-center gap-4">
                        <div class="bg-slate-100 p-3 rounded-full">
                            <i data-lucide="user" class="w-6 h-6 text-slate-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-dark mb-1">Profile</h3>
                            <p class="text-sm text-slate-600">Manage your account</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Bookmarked Discussions Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-dark mb-1">Bookmarked Discussions</h2>
                        <p class="text-slate-600">Discussions you've saved for later</p>
                    </div>
                </div>

                <?php if (empty($bookmarkedDiscussions)): ?>
                    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-12 text-center shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                        <div class="bg-slate-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="bookmark" class="w-10 h-10 text-slate-400"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-dark mb-2">No Bookmarked Discussions Yet</h3>
                        <p class="text-slate-600 mb-4">Bookmark discussions to easily find them later</p>
                        <a href="<?= base_url('ksp/networking-corner') ?>" 
                        class="gradient-btn px-6 py-3 rounded-lg text-white font-medium inline-flex items-center">
                            <i data-lucide="message-circle" class="w-5 h-5 mr-2"></i>
                            Browse Discussions
                        </a>
                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-lg shadow-sm border border-slate-200 divide-y divide-slate-100 shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                        <?php foreach ($bookmarkedDiscussions as $discussion): ?>
                            <a href="<?= base_url('ksp/discussion/' . $discussion['slug'] . '/view') ?>" 
                            class="block p-6 hover:bg-slate-50 transition-colors group">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-12 h-12 rounded-full overflow-hidden bg-slate-200">
                                        <?php if (!empty($discussion['picture'])): ?>
                                            <img src="<?= base_url('uploads/profiles/' . $discussion['picture']) ?>" 
                                                alt="<?= esc($discussion['first_name']) ?>" 
                                                class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center bg-primary text-white font-semibold text-lg">
                                                <?= strtoupper(substr($discussion['first_name'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-bold text-dark text-lg mb-1 group-hover:text-primary transition-colors truncate">
                                            <?= esc($discussion['title']) ?>
                                        </h3>
                                        <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500 mb-2">
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="user" class="w-3 h-3"></i>
                                                <?= esc($discussion['first_name'] . ' ' . $discussion['last_name']) ?>
                                            </span>
                                            <span>•</span>
                                            <span><?= date('M j, Y', strtotime($discussion['created_at'])) ?></span>
                                            <span>•</span>
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="bookmark" class="w-3 h-3 fill-secondary text-secondary"></i>
                                                <?= date('M j', strtotime($discussion['bookmarked_at'])) ?>
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-4 text-sm text-slate-500">
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="message-circle" class="w-4 h-4"></i>
                                                <?= $discussion['reply_count'] ?> replies
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                                <?= number_format($discussion['views']) ?> views
                                            </span>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium" 
                                                style="background-color: <?= $discussion['forum_color'] ?>20; color: <?= $discussion['forum_color'] ?>">
                                                <?= esc($discussion['forum_name']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Forum Subscriptions Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-dark mb-1">My Forums</h2>
                        <p class="text-slate-600">Forums you've joined</p>
                    </div>
                    <a href="<?= base_url('ksp/networking-corner/forums') ?>" 
                    class="text-primary hover:text-primary/80 font-medium flex items-center gap-2">
                        View All
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>

                <?php if (empty($forumSubscriptions)): ?>
                    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-12 text-center shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                        <div class="bg-slate-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="users" class="w-10 h-10 text-slate-400"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-dark mb-2">No Forum Memberships Yet</h3>
                        <p class="text-slate-600 mb-4">Join forums to connect with peers and share insights</p>
                        <a href="<?= base_url('ksp/networking-corner/forums') ?>" 
                        class="gradient-btn px-6 py-3 rounded-lg text-white font-medium inline-flex items-center">
                            <i data-lucide="search" class="w-5 h-5 mr-2"></i>
                            Browse Forums
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($forumSubscriptions as $forum): ?>
                            <a href="<?= base_url('ksp/networking-corner-forum-discussion/' . $forum['slug']) ?>" 
                            class="bg-white rounded-lg shadow-sm border border-slate-200 hover:shadow-md transition-all duration-300 overflow-hidden group shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                                <div class="p-6">
                                    <div class="flex items-start gap-4 mb-4">
                                        <div class="bg-primary p-3 rounded-lg text-white group-hover:scale-110 transition-transform">
                                            <i data-lucide="users" class="w-6 h-6"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-bold text-dark text-lg mb-1 group-hover:text-primary transition-colors truncate">
                                                <?= esc($forum['name']) ?>
                                            </h3>
                                            <p class="text-sm text-slate-600 line-clamp-2">
                                                <?= esc($forum['description']) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between text-sm text-slate-500 pt-4 border-t border-slate-100">
                                        <div class="flex items-center gap-1">
                                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                                            <span><?= $forum['discussion_count'] ?> discussions</span>
                                        </div>
                                        <span class="text-xs">
                                            Joined <?= date('M j, Y', strtotime($forum['joined_at'])) ?>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Discussions Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-dark mb-1">My Discussions</h2>
                        <p class="text-slate-600">Discussions you've created or participated in</p>
                    </div>
                    <a href="<?= base_url('ksp/networking-corner-discussions') ?>" 
                    class="text-primary hover:text-primary/80 font-medium flex items-center gap-2">
                        View All
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>

                <?php if (empty($userDiscussions)): ?>
                    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-12 text-center shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                        <div class="bg-slate-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="message-square" class="w-10 h-10 text-slate-400"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-dark mb-2">No Discussions Yet</h3>
                        <p class="text-slate-600 mb-4">Start a discussion or participate in existing ones</p>
                        <a href="<?= base_url('ksp/networking-corner') ?>" 
                        class="gradient-btn px-6 py-3 rounded-lg text-white font-medium inline-flex items-center">
                            <i data-lucide="message-circle" class="w-5 h-5 mr-2"></i>
                            Explore Discussions
                        </a>
                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-lg shadow-sm border border-slate-200 divide-y divide-slate-100 shadow-md hover:shadow-2xl transition-shadow z-10 relative">
                        <?php foreach ($userDiscussions as $discussion): ?>
                            <a href="<?= base_url('ksp/discussion/' . $discussion['slug'] . '/view') ?>" 
                            class="block p-6 hover:bg-slate-50 transition-colors group">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-12 h-12 rounded-full overflow-hidden bg-slate-200">
                                        <?php if (!empty($discussion['picture'])): ?>
                                            <img src="<?= base_url('uploads/profiles/' . $discussion['picture']) ?>" 
                                                alt="<?= esc($discussion['first_name']) ?>" 
                                                class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center bg-primary text-white font-semibold text-lg">
                                                <?= strtoupper(substr($discussion['first_name'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $discussion['forum_color'] ?? 'bg-primary/10 text-primary' ?>">
                                                <?= esc($discussion['forum_name']) ?>
                                            </span>
                                            <?php if ($discussion['is_locked']): ?>
                                                <span class="inline-flex items-center gap-1 text-xs text-amber-600">
                                                    <i data-lucide="lock" class="w-3 h-3"></i>
                                                    Locked
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <h3 class="font-bold text-dark text-lg mb-2 group-hover:text-primary transition-colors">
                                            <?= esc($discussion['title']) ?>
                                        </h3>
                                        
                                        <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500">
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="user" class="w-4 h-4"></i>
                                                <?= esc($discussion['first_name'] . ' ' . $discussion['last_name']) ?>
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="message-square" class="w-4 h-4"></i>
                                                <?= $discussion['reply_count'] ?> replies
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                                <?= $discussion['views'] ?> views
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="clock" class="w-4 h-4"></i>
                                                <?= date('M j, Y', strtotime($discussion['created_at'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    lucide.createIcons();
});
</script>
<?= $this->endSection() ?>
