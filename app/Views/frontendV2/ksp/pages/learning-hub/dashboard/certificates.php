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
        <h1 class="text-3xl font-bold text-dark mb-2">My Certificates</h1>
        <p class="text-slate-600">Your course completion certificates</p>
    </div>
    
    <?php if (empty($certificates)): ?>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-12 text-center">
            <i data-lucide="award" class="w-16 h-16 text-slate-400 mx-auto mb-4"></i>
            <h3 class="text-xl font-semibold text-dark mb-2">No certificates yet</h3>
            <p class="text-slate-600 mb-6">Complete courses to earn certificates</p>
            <a href="<?= base_url('ksp/learning-hub') ?>" 
               class="gradient-btn px-6 py-3 rounded-lg text-white font-medium inline-block">
                Browse Courses
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($certificates as $certificate): ?>
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <div class="text-center mb-4">
                        <i data-lucide="award" class="w-16 h-16 text-primary mx-auto mb-4"></i>
                        <h3 class="text-lg font-bold text-dark mb-2">
                            <?= esc($certificate['course']['title'] ?? 'Course') ?>
                        </h3>
                        <p class="text-sm text-slate-500">
                            Issued: <?= date('F j, Y', strtotime($certificate['issued_at'])) ?>
                        </p>
                    </div>
                    
                    <div class="space-y-2 mb-4">
                        <div class="text-xs text-slate-600">
                            <strong>Certificate #:</strong> <?= esc($certificate['certificate_number']) ?>
                        </div>
                        <div class="text-xs text-slate-600">
                            <strong>Verification Code:</strong> <?= esc($certificate['verification_code']) ?>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        <a href="<?= base_url('ksp/learning-hub/certificate/' . $certificate['id'] . '/download') ?>" 
                           class="flex-1 gradient-btn px-4 py-2 rounded-lg text-white text-center text-sm font-medium">
                            Download
                        </a>
                        <button onclick="copyVerificationCode('<?= esc($certificate['verification_code']) ?>')" 
                                class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 text-sm">
                            Copy Code
                        </button>
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

function copyVerificationCode(code) {
    navigator.clipboard.writeText(code).then(function() {
        alert('Verification code copied to clipboard!');
    });
}
</script>
<?= $this->endSection() ?>

