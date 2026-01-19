<?php
use App\Helpers\UrlHelper;
$currentUrl = new UrlHelper();
session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);

$flashError = session()->getFlashdata('error');
$mode = $mode ?? 'create';
$isEdit = $mode === 'edit';
$source = $source ?? null;
?>

<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<main class="flex-1 overflow-y-auto">
    <?= view('backendV2/partials/page_banner', [
        'pageTitle' => $isEdit ? 'Edit Knowledge Source' : 'Add Knowledge Source',
        'pageDescription' => 'Provide content that the AI will use as a trusted reference source.',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
            ['label' => 'AI Assistant', 'url' => base_url('auth/ai-assistant')],
            ['label' => 'Knowledge Base', 'url' => base_url('auth/ai-assistant/knowledge-base')],
            ['label' => $isEdit ? 'Edit' : 'Create']
        ],
        'bannerActions' => '<a href="' . base_url('auth/ai-assistant/knowledge-base') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Knowledge Base
        </a>'
    ]) ?>

    <div class="bg-white rounded-xl shadow-sm max-w-full mx-5 p-6 mb-6">
        <form action="<?= $isEdit ? base_url('auth/ai-assistant/knowledge-base/update/' . ($source['id'] ?? '')) : base_url('auth/ai-assistant/knowledge-base/store') ?>" method="post" enctype="multipart/form-data" class="p-6 space-y-6">
            <?php if ($flashError): ?>
                <div class="p-4 rounded-lg bg-red-50 text-red-700 border border-red-200"><?= esc($flashError) ?></div>
            <?php endif; ?>

            <?= csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="kbTitle" class="block text-sm font-medium text-dark mb-1">Title <span class="text-red-500">*</span></label>
                    <input id="kbTitle" name="title" type="text" value="<?= esc(old('title', $source['title'] ?? '')) ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="e.g. KEWASNET Overview" required />
                    <p class="mt-1 text-xs text-gray-500">A short, descriptive name for this knowledge source.</p>
                </div>
                <div>
                    <label for="kbStatus" class="block text-sm font-medium text-dark mb-1">Status <span class="text-red-500">*</span></label>
                    <select id="kbStatus" name="status" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                        <?php $status = old('status', $source['status'] ?? 'active'); ?>
                        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>active</option>
                        <option value="disabled" <?= $status === 'disabled' ? 'selected' : '' ?>>disabled</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Only <span class="font-medium">active</span> sources are used by the AI.</p>
                </div>
            </div>

            <div>
                <label for="kbType" class="block text-sm font-medium text-dark mb-1">Type <span class="text-red-500">*</span></label>
                <?php $type = old('type', $source['type'] ?? 'text'); ?>
                <select id="kbType" name="type" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                    <option value="text" <?= $type === 'text' ? 'selected' : '' ?>>text</option>
                    <option value="url" <?= $type === 'url' ? 'selected' : '' ?>>url</option>
                    <option value="file" <?= $type === 'file' ? 'selected' : '' ?>>file</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">Choose how this source will be ingested: paste text, fetch from a URL, or upload a file.</p>
            </div>

            <div id="kbUrlSection" class="<?= $type === 'url' ? '' : 'hidden' ?>">
                <label for="kbSourceUrl" class="block text-sm font-medium text-dark mb-1">Source URL <span class="text-red-500">*</span></label>
                <input id="kbSourceUrl" name="source_url" type="url" value="<?= esc(old('source_url', $source['source_url'] ?? '')) ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="https://kewasnet.org/about" />
                <p class="mt-1 text-xs text-gray-500">We’ll fetch the page content and ingest it into searchable chunks.</p>
            </div>

            <div id="kbTextSection" class="<?= $type === 'text' ? '' : 'hidden' ?>">
                <label for="kbContent" class="block text-sm font-medium text-dark mb-1">Content <span class="text-red-500">*</span></label>
                <textarea id="kbContent" name="content_raw" rows="10" class="summernote-editor w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent resize-none" placeholder="Paste content here..."><?= esc(old('content_raw', $source['content_raw'] ?? '')) ?></textarea>
                <p class="mt-1 text-xs text-gray-500">Tip: include headings and short paragraphs for better chunking and retrieval.</p>
            </div>

            <div id="kbFileSection" class="<?= $type === 'file' ? '' : 'hidden' ?>">
                <label class="block text-sm font-medium text-dark mb-1">Upload File <?= $isEdit ? '' : '<span class="text-red-500">*</span>' ?></label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-borderColor border-dashed rounded-md">
                    <div class="space-y-1 text-center">
                        <i data-lucide="upload" class="w-10 h-10 mx-auto text-slate-400"></i>
                        <div class="flex text-sm text-slate-600 justify-center">
                            <label for="kbFile" class="relative cursor-pointer bg-white rounded-md font-medium text-secondary hover:text-secondaryShades-600 focus-within:outline-none">
                                <span>Upload a file</span>
                                <input id="kbFile" name="kb_file" type="file" accept=".pdf,.txt,.md" class="sr-only">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-slate-500">PDF, TXT, MD up to 10MB</p>
                        <?php if ($isEdit && !empty($source['file_path'])): ?>
                            <p class="text-xs text-slate-500 mt-1">Current file: <?= esc($source['file_path']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <a href="<?= base_url('auth/ai-assistant/knowledge-base') ?>" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 transition-colors">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primaryShades-600 transition-colors">
                    <?= $isEdit ? 'Save Changes' : 'Create Source' ?>
                </button>
            </div>
        </form>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();
    const kbType = document.getElementById('kbType');
    const urlSection = document.getElementById('kbUrlSection');
    const textSection = document.getElementById('kbTextSection');
    const fileSection = document.getElementById('kbFileSection');

    function updateSections() {
        const v = kbType.value;
        urlSection.classList.toggle('hidden', v !== 'url');
        textSection.classList.toggle('hidden', v !== 'text');
        fileSection.classList.toggle('hidden', v !== 'file');
    }

    kbType?.addEventListener('change', updateSections);
    updateSections();
</script>
<?= $this->endSection() ?>

