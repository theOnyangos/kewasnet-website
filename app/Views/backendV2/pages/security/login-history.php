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
        'pageTitle' => 'Login History',
        'pageDescription' => 'View login activity history for this account',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
            ['label' => 'Admin Accounts', 'url' => base_url('auth/account')],
            ['label' => 'Security', 'url' => base_url('auth/security')],
            ['label' => 'Login History']
        ],
        'bannerActions' => '<a href="' . base_url('auth/security') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Security
        </a>'
    ]) ?>

    <div class="px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-800">Login History</h2>
                <p class="mt-1 text-sm text-slate-500">Recent login activity for <?= esc($user['first_name'] . ' ' . $user['last_name']) ?></p>
            </div>

            <?php if (empty($loginHistory)): ?>
                <div class="text-center py-12">
                    <i data-lucide="history" class="w-16 h-16 text-slate-300 mx-auto mb-4"></i>
                    <p class="text-slate-500">No login history available for this account.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Browser</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Platform</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">IP Address</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Login Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Login Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <?php foreach ($loginHistory as $login): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-sm text-slate-900"><?= esc($login['browser'] ?? 'N/A') ?></td>
                                    <td class="px-4 py-3 text-sm text-slate-900"><?= esc($login['platform'] ?? 'N/A') ?></td>
                                    <td class="px-4 py-3 text-sm text-slate-900"><?= esc($login['ip_address'] ?? 'N/A') ?></td>
                                    <td class="px-4 py-3 text-sm text-slate-900">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?= esc($login['login_type'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-900"><?= !empty($login['login_time']) ? date('F j, Y g:i A', strtotime($login['login_time'])) : 'N/A' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    lucide.createIcons();
</script>
<?= $this->endSection() ?>