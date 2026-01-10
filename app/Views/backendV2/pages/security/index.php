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
        'pageTitle' => $isOwnProfile ? 'My Security Settings' : 'Security Settings',
        'pageDescription' => 'Manage your account security and password settings',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
            ['label' => $isOwnProfile ? 'My Profile' : 'Admin Accounts', 'url' => $isOwnProfile ? base_url('auth/profile') : base_url('auth/account')],
            ['label' => 'Security']
        ]
    ]) ?>

    <div class="px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm p-8 mb-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-800">Security Settings</h2>
                <p class="mt-1 text-sm text-slate-500">Manage your account security, password, and authentication settings</p>
            </div>

            <div class="space-y-6">
                <div class="border-b borderColor pb-6">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Password Management</h3>
                    <p class="text-sm text-slate-600 mb-4">Keep your account secure by regularly updating your password.</p>
                    <a href="<?= base_url('auth/security/change-password/' . $user['id']) ?>" class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium">
                        <i data-lucide="key" class="w-4 h-4 mr-2"></i>
                        Change Password
                    </a>
                </div>

                <div class="border-b borderColor pb-6">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Account Information</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-slate-500 mb-1">Account Status</dt>
                            <dd>
                                <span class="px-3 py-1 rounded-full text-sm font-medium <?= ($user['account_status'] ?? 'active') === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= ucfirst($user['account_status'] ?? 'active') ?>
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500 mb-1">Email Verified</dt>
                            <dd>
                                <?php if (!empty($user['email_verified_at'])): ?>
                                    <span class="text-green-600 flex items-center">
                                        <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                                        Verified
                                    </span>
                                <?php else: ?>
                                    <span class="text-red-600 flex items-center">
                                        <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i>
                                        Not Verified
                                    </span>
                                <?php endif; ?>
                            </dd>
                        </div>
                    </dl>
                </div>

                <?php if (!$isOwnProfile): ?>
                <div>
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Login History</h3>
                    <p class="text-sm text-slate-600 mb-4">View recent login activity for this account.</p>
                    <a href="<?= base_url('auth/security/login-history/' . $user['id']) ?>" class="inline-flex items-center px-6 py-3 border borderColor text-slate-700 rounded-lg hover:bg-slate-50 transition-colors font-medium">
                        <i data-lucide="history" class="w-4 h-4 mr-2"></i>
                        View Login History
                    </a>
                </div>
                <?php endif; ?>
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