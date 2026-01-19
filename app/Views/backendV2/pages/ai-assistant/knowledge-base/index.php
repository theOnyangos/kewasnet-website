<?php
use App\Helpers\UrlHelper;
$currentUrl = new UrlHelper();
session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);

$flashSuccess = session()->getFlashdata('success');
$flashError = session()->getFlashdata('error');
$flashInfo = session()->getFlashdata('info');
?>

<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<main class="flex-1 overflow-y-auto">
    <?= view('backendV2/partials/page_banner', [
        'pageTitle' => 'AI Knowledge Base',
        'pageDescription' => 'Add sources (text, URLs, files) that the AI can reference when answering questions.',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
            ['label' => 'AI Assistant', 'url' => base_url('auth/ai-assistant')],
            ['label' => 'Knowledge Base']
        ]
    ]) ?>

    <div class="px-6 pb-6">
        <?php if ($flashSuccess): ?>
            <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200"><?= esc($flashSuccess) ?></div>
        <?php endif; ?>
        <?php if ($flashError): ?>
            <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-700 border border-red-200"><?= esc($flashError) ?></div>
        <?php endif; ?>
        <?php if ($flashInfo): ?>
            <div class="mb-4 p-4 rounded-lg bg-slate-50 text-slate-700 border border-slate-200"><?= esc($flashInfo) ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-4 border-b borderColor flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">Sources</h3>
                    <p class="text-sm text-slate-500">Only active sources are used for retrieval.</p>
                </div>
                <a href="<?= base_url('auth/ai-assistant/knowledge-base/create') ?>" class="px-4 py-2 text-sm bg-primary text-white rounded-lg hover:bg-primaryShades-600 transition-colors flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> Add Source
                </a>
            </div>

            <div class="p-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-600 border-b borderColor">
                            <th class="py-3 pr-4">Title</th>
                            <th class="py-3 pr-4">Type</th>
                            <th class="py-3 pr-4">Status</th>
                            <th class="py-3 pr-4">Last Ingested</th>
                            <th class="py-3 pr-4">Ingest Error</th>
                            <th class="py-3 pr-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($sources)): ?>
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-500">No sources yet. Click “Add Source” to create one.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sources as $s): ?>
                            <tr class="border-b borderColor">
                                <td class="py-3 pr-4">
                                    <div class="font-medium text-slate-800"><?= esc($s['title'] ?? '') ?></div>
                                    <?php if (!empty($s['source_url'])): ?>
                                        <div class="text-xs text-slate-500 truncate max-w-[420px]"><?= esc($s['source_url']) ?></div>
                                    <?php elseif (!empty($s['file_path'])): ?>
                                        <div class="text-xs text-slate-500 truncate max-w-[420px]"><?= esc($s['file_path']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="px-2 py-1 rounded bg-slate-100 text-slate-700"><?= esc($s['type'] ?? '') ?></span>
                                </td>
                                <td class="py-3 pr-4">
                                    <?php $status = $s['status'] ?? 'active'; ?>
                                    <span class="px-2 py-1 rounded <?= $status === 'active' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-slate-100 text-slate-600' ?>">
                                        <?= esc($status) ?>
                                    </span>
                                </td>
                                <td class="py-3 pr-4 text-slate-700"><?= esc($s['last_ingested_at'] ?? '—') ?></td>
                                <td class="py-3 pr-4">
                                    <span class="text-xs text-red-600"><?= esc($s['ingest_error'] ?? '') ?></span>
                                </td>
                                <td class="py-3 pr-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="<?= base_url('auth/ai-assistant/knowledge-base/edit/' . ($s['id'] ?? '')) ?>" class="px-3 py-2 text-xs bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 transition-colors flex items-center gap-1">
                                            <i data-lucide="pencil" class="w-4 h-4"></i> Edit
                                        </a>
                                        <form action="<?= base_url('auth/ai-assistant/knowledge-base/toggle/' . ($s['id'] ?? '')) ?>" method="post">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="px-3 py-2 text-xs bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 transition-colors flex items-center gap-1">
                                                <i data-lucide="<?= ($status === 'active') ? 'pause' : 'play' ?>" class="w-4 h-4"></i>
                                                <?= ($status === 'active') ? 'Disable' : 'Enable' ?>
                                            </button>
                                        </form>
                                        <form action="<?= base_url('auth/ai-assistant/knowledge-base/ingest/' . ($s['id'] ?? '')) ?>" method="post">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="px-3 py-2 text-xs bg-primary text-white hover:bg-primaryShades-600 rounded-lg transition-colors flex items-center gap-1">
                                                <i data-lucide="refresh-cw" class="w-4 h-4"></i> Ingest
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
<?= $this->endSection() ?>

