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
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <a href="<?= base_url('ksp/learning-hub/learn/' . $course_id) ?>" 
                   class="text-primary hover:text-primary-dark font-medium mb-4 inline-block">
                    ← Back to Course
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-8">
                <div class="flex justify-between items-start mb-4">
                    <h1 class="text-2xl font-bold text-dark"><?= esc($lecture['title']) ?></h1>
                    <?php if ($is_completed): ?>
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                            <i data-lucide="check-circle" class="w-4 h-4"></i> Completed
                        </span>
                    <?php else: ?>
                        <button id="markCompleteBtn" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                            <i data-lucide="check" class="w-4 h-4"></i> Mark as Complete
                        </button>
                    <?php endif; ?>
                </div>
                
                <!-- Video Player -->
                <?php if (!empty($embed_code)): ?>
                    <div class="mb-6">
                        <div class="aspect-video bg-slate-900 rounded-lg overflow-hidden">
                            <?= $embed_code ?>
                        </div>
                    </div>
                <?php elseif (!empty($lecture['video_url'])): ?>
                    <div class="mb-6">
                        <div class="aspect-video bg-slate-900 rounded-lg overflow-hidden">
                            <video controls class="w-full h-full">
                                <source src="<?= esc($lecture['video_url']) ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Description -->
                <?php if (!empty($lecture['description'])): ?>
                    <div class="prose max-w-none mb-6">
                        <?= $lecture['description'] ?>
                    </div>
                <?php endif; ?>
                
                <!-- Attachments -->
                <?php if (!empty($lecture['attachments'])): ?>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-dark mb-3">Attachments</h3>
                        <div class="space-y-2">
                            <?php foreach ($lecture['attachments'] as $attachment): ?>
                                <a href="<?= base_url('ksp/learning-hub/attachment/' . $attachment['id']) ?>" 
                                   class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50">
                                    <i data-lucide="file" class="w-5 h-5 text-primary"></i>
                                    <div class="flex-1">
                                        <p class="font-medium text-dark"><?= esc($attachment['file_name']) ?></p>
                                        <p class="text-xs text-slate-500"><?= esc($attachment['file_type']) ?></p>
                                    </div>
                                    <i data-lucide="download" class="w-5 h-5 text-slate-400"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Links -->
                <?php if (!empty($lecture['links'])): ?>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-dark mb-3">Resources</h3>
                        <div class="space-y-2">
                            <?php foreach ($lecture['links'] as $link): ?>
                                <a href="<?= esc($link['link_url']) ?>" target="_blank" rel="noopener noreferrer"
                                   class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50">
                                    <i data-lucide="external-link" class="w-5 h-5 text-primary"></i>
                                    <span class="font-medium text-dark"><?= esc($link['link_title']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    lucide.createIcons();
    
    // Mark lecture as complete
    $('#markCompleteBtn').on('click', function() {
        const btn = $(this);
        btn.prop('disabled', true).html('<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Marking...');
        
        $.ajax({
            url: '<?= base_url('ksp/learning-hub/lecture/' . $course_id . '/' . $lecture['id'] . '/complete') ?>',
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    btn.replaceWith('<span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium"><i data-lucide="check-circle" class="w-4 h-4"></i> Completed</span>');
                    lucide.createIcons();
                    
                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Great job!',
                            text: 'Lecture marked as completed. Progress: ' + response.progress_percentage + '%',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                } else {
                    alert(response.message || 'Failed to mark as complete');
                    btn.prop('disabled', false).html('<i data-lucide="check" class="w-4 h-4"></i> Mark as Complete');
                    lucide.createIcons();
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                btn.prop('disabled', false).html('<i data-lucide="check" class="w-4 h-4"></i> Mark as Complete');
                lucide.createIcons();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>

