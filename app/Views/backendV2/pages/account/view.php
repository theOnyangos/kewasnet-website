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
        'pageTitle' => 'View Admin Account',
        'pageDescription' => 'View detailed information about this admin account',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
            ['label' => 'Admin Accounts', 'url' => base_url('auth/account')],
            ['label' => 'View']
        ],
        'bannerActions' => '<a href="' . base_url('auth/account/edit/' . $user['id']) . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors mr-2">
            <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
            Edit Account
        </a>
        <a href="' . base_url('auth/account') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Accounts
        </a>'
    ]) ?>

    <div class="px-6 pb-6">
        <div class="bg-white rounded-lg shadow-sm border borderColor p-8 mb-6">
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                <div class="flex-shrink-0">
                    <?php if (!empty($user['picture'])): ?>
                        <img src="<?= base_url(esc($user['picture'])) ?>" alt="<?= esc($user['first_name'] . ' ' . $user['last_name']) ?>" class="w-32 h-32 rounded-full object-cover border-4 border-slate-100">
                    <?php else: ?>
                        <div class="w-32 h-32 rounded-full bg-secondaryShades-500 flex items-center justify-center text-white text-3xl font-bold border-4 border-slate-100">
                            <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex-1">
                    <h2 class="text-3xl font-bold text-slate-800 mb-2"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h2>
                    <p class="text-lg text-slate-600 mb-4"><?= esc($user['email']) ?></p>
                    <div class="flex flex-wrap gap-4 mb-4">
                        <?php if (!empty($user['phone'])): ?>
                            <div class="flex items-center text-slate-600">
                                <i data-lucide="phone" class="w-4 h-4 mr-2"></i>
                                <span><?= esc($user['phone']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="flex items-center">
                            <span class="px-3 py-1 rounded-full text-sm font-medium <?= ($user['status'] ?? 'active') === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= ucfirst($user['status'] ?? 'active') ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-sm border borderColor p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-4">Personal Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-slate-500 mb-1">First Name</dt>
                        <dd class="text-base text-slate-900"><?= esc($user['first_name']) ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500 mb-1">Last Name</dt>
                        <dd class="text-base text-slate-900"><?= esc($user['last_name']) ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500 mb-1">Email</dt>
                        <dd class="text-base text-slate-900"><?= esc($user['email']) ?></dd>
                    </div>
                    <?php if (!empty($user['phone'])): ?>
                    <div>
                        <dt class="text-sm font-medium text-slate-500 mb-1">Phone</dt>
                        <dd class="text-base text-slate-900"><?= esc($user['phone']) ?></dd>
                    </div>
                    <?php endif; ?>
                </dl>
            </div>
            <div class="bg-white rounded-lg shadow-sm border borderColor p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-4">Account Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-slate-500 mb-1">Role</dt>
                        <dd class="text-base text-slate-900"><?= esc($user['role_name'] ?? 'N/A') ?></dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500 mb-1">Account Status</dt>
                        <dd>
                            <span class="px-3 py-1 rounded-full text-sm font-medium <?= ($user['account_status'] ?? 'active') === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= ucfirst($user['account_status'] ?? 'active') ?>
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500 mb-1">Member Since</dt>
                        <dd class="text-base text-slate-900"><?= !empty($user['created_at']) ? date('F j, Y', strtotime($user['created_at'])) : 'N/A' ?></dd>
                    </div>
                </dl>
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