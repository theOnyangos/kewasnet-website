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
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-dark mb-2">My Courses</h1>
        <p class="text-slate-600">Continue your learning journey</p>
    </div>
    
    <?php if (empty($courses)): ?>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-12 text-center">
            <i data-lucide="book-open" class="w-16 h-16 text-slate-400 mx-auto mb-4"></i>
            <h3 class="text-xl font-semibold text-dark mb-2">No courses yet</h3>
            <p class="text-slate-600 mb-6">Browse our course catalog to get started</p>
            <a href="<?= base_url('ksp/learning-hub') ?>" 
               class="gradient-btn px-6 py-3 rounded-lg text-white font-medium inline-block">
                Browse Courses
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($courses as $course): ?>
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                    <?php if (!empty($course['image_url'])): ?>
                        <img src="<?= base_url(esc($course['image_url'])) ?>" alt="<?= esc($course['title']) ?>" 
                             class="w-full h-48 object-cover">
                    <?php endif; ?>
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-dark mb-2">
                            <a href="<?= base_url('ksp/learning-hub/learn/' . $course['id']) ?>" 
                               class="hover:text-primary">
                                <?= esc($course['title']) ?>
                            </a>
                        </h3>
                        
                        <!-- Progress -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-slate-600">Progress</span>
                                <span class="text-sm font-bold text-primary"><?= number_format($course['progress'], 0) ?>%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2">
                                <div class="bg-primary h-2 rounded-full" style="width: <?= $course['progress'] ?>%"></div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">
                                Enrolled: <?= date('M j, Y', strtotime($course['enrollment_date'])) ?>
                            </span>
                            <a href="<?= base_url('ksp/learning-hub/learn/' . $course['id']) ?>" 
                               class="text-primary hover:text-primary-dark font-medium">
                                Continue →
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    lucide.createIcons();
});
</script>
<?= $this->endSection() ?>

