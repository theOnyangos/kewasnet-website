<?php
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<main class="flex-1 overflow-y-auto">
    <?= view('backendV2/partials/page_banner', [
        'pageTitle' => 'Help & Support',
        'pageDescription' => 'Get help and support for using the KEWASNET admin dashboard',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
            ['label' => 'Help & Support']
        ]
    ]) ?>

    <div class="px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm p-8">
            <div class="prose max-w-none">
                <h2 class="text-2xl font-bold text-slate-800 mb-4">Help & Support</h2>
                <p class="text-slate-600 mb-6">Welcome to the KEWASNET Admin Dashboard help center. Find answers to common questions and get support.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="border borderColor rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-slate-800 mb-2 flex items-center">
                            <i data-lucide="book" class="w-5 h-5 mr-2 text-primary"></i>
                            Documentation
                        </h3>
                        <p class="text-sm text-slate-600">Browse our comprehensive documentation and guides.</p>
                    </div>

                    <div class="border borderColor rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-slate-800 mb-2 flex items-center">
                            <i data-lucide="video" class="w-5 h-5 mr-2 text-primary"></i>
                            Video Tutorials
                        </h3>
                        <p class="text-sm text-slate-600">Watch step-by-step video tutorials to get started.</p>
                    </div>

                    <div class="border borderColor rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-slate-800 mb-2 flex items-center">
                            <i data-lucide="mail" class="w-5 h-5 mr-2 text-primary"></i>
                            Contact Support
                        </h3>
                        <p class="text-sm text-slate-600">Email us at support@kewasnet.org for assistance.</p>
                    </div>

                    <div class="border borderColor rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-slate-800 mb-2 flex items-center">
                            <i data-lucide="message-circle" class="w-5 h-5 mr-2 text-primary"></i>
                            Live Chat
                        </h3>
                        <p class="text-sm text-slate-600">Chat with our support team in real-time.</p>
                    </div>
                </div>

                <div class="border-t borderColor pt-6">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Frequently Asked Questions</h3>
                    <div class="space-y-4">
                        <div class="border-b borderColor pb-4">
                            <h4 class="font-medium text-slate-800 mb-2">How do I reset my password?</h4>
                            <p class="text-sm text-slate-600">Go to Security Settings and click "Change Password" to update your password.</p>
                        </div>
                        <div class="border-b borderColor pb-4">
                            <h4 class="font-medium text-slate-800 mb-2">How do I manage user accounts?</h4>
                            <p class="text-sm text-slate-600">Navigate to Admin Accounts from your profile menu to view and manage all admin accounts.</p>
                        </div>
                        <div class="border-b borderColor pb-4">
                            <h4 class="font-medium text-slate-800 mb-2">How do I update my profile?</h4>
                            <p class="text-sm text-slate-600">Click on your profile in the sidebar, then select "Admin Profile" to edit your information.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    lucide.createIcons();
</script>
<?= $this->endSection() ?>