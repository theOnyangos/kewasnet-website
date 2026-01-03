<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('backendV2/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto p-6">
        <!-- Header Section -->
        <?= $this->include('backendV2/pages/blogs/partials/header_section') ?>

        <!-- Navigation Tabs -->
        <?= $this->include('backendV2/pages/blogs/partials/navigation_section') ?>
        
        <!-- Newsletter Details Content -->
        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <!-- Header with Back Button -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Newsletter Details</h1>
                    <p class="mt-1 text-sm text-slate-500">View complete newsletter information and performance metrics</p>
                </div>
                <div class="flex space-x-3">
                    <a href="<?= site_url('auth/blogs/newsletters/sent') ?>" class="flex items-center px-6 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all duration-300">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                        <span>Back to Sent</span>
                    </a>
                </div>
            </div>

            <!-- Newsletter Information -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Subject and Preview -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Subject Line</h3>
                        <p class="text-gray-700 text-base"><?= esc($newsletter['subject']) ?></p>
                        
                        <?php if (!empty($newsletter['preview_text'])): ?>
                            <h3 class="text-lg font-semibold text-gray-900 mt-6 mb-4">Preview Text</h3>
                            <p class="text-gray-600 text-sm"><?= esc($newsletter['preview_text']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Newsletter Content -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Newsletter Content</h3>
                        <div class="prose max-w-none">
                            <?= $newsletter['content'] ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Information -->
                <div class="space-y-6">
                    <!-- Status -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Status</h3>
                        <div class="flex items-center">
                            <?php
                            $statusBadges = [
                                'draft' => '<span class="badge bg-secondary">Draft</span>',
                                'scheduled' => '<span class="badge bg-warning">Scheduled</span>',
                                'sending' => '<span class="badge bg-info">Sending</span>',
                                'sent' => '<span class="badge bg-success">Sent</span>',
                                'failed' => '<span class="badge bg-danger">Failed</span>'
                            ];
                            echo $statusBadges[$newsletter['status']] ?? $statusBadges['draft'];
                            ?>
                        </div>
                    </div>

                    <!-- Sender Information -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Sender Information</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Name</p>
                                <p class="text-sm font-medium text-gray-900"><?= esc($newsletter['sender_name']) ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="text-sm font-medium text-gray-900"><?= esc($newsletter['sender_email']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Metrics -->
                    <?php if ($newsletter['status'] === 'sent'): ?>
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance</h3>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-sm text-gray-600">Recipients</span>
                                        <span class="text-sm font-semibold text-gray-900"><?= number_format($newsletter['recipient_count']) ?></span>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-sm text-gray-600">Sent</span>
                                        <span class="text-sm font-semibold text-green-600"><?= number_format($newsletter['sent_count']) ?></span>
                                    </div>
                                </div>
                                <?php if ($newsletter['failed_count'] > 0): ?>
                                    <div>
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-sm text-gray-600">Failed</span>
                                            <span class="text-sm font-semibold text-red-600"><?= number_format($newsletter['failed_count']) ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="pt-3 border-t border-gray-200">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-sm text-gray-600">Open Rate</span>
                                        <span class="text-sm font-semibold text-blue-600"><?= number_format($newsletter['open_rate'], 1) ?>%</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-sm text-gray-600">Click Rate</span>
                                        <span class="text-sm font-semibold text-purple-600"><?= number_format($newsletter['click_rate'], 1) ?>%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Dates -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Dates</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Created</p>
                                <p class="text-sm font-medium text-gray-900"><?= date('M d, Y g:i A', strtotime($newsletter['created_at'])) ?></p>
                            </div>
                            <?php if ($newsletter['status'] === 'sent' && !empty($newsletter['sent_at'])): ?>
                                <div>
                                    <p class="text-sm text-gray-500">Sent</p>
                                    <p class="text-sm font-medium text-gray-900"><?= date('M d, Y g:i A', strtotime($newsletter['sent_at'])) ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if ($newsletter['status'] === 'scheduled' && !empty($newsletter['scheduled_at'])): ?>
                                <div>
                                    <p class="text-sm text-gray-500">Scheduled For</p>
                                    <p class="text-sm font-medium text-gray-900"><?= date('M d, Y g:i A', strtotime($newsletter['scheduled_at'])) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        lucide.createIcons();
    });
</script>
<?= $this->endSection() ?>
