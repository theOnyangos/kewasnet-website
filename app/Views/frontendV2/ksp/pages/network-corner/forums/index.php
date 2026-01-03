<?php 
    use App\Helpers\UrlHelper;
    use Carbon\Carbon;

    $currentUrl = new UrlHelper();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/ksp/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <div class="bg-light min-h-screen">
        <!-- Page Header -->
        <section class="relative py-16 bg-gradient-to-r from-primary to-secondary">
            <div class="container mx-auto px-4 text-center text-white">
                <div class="max-w-4xl mx-auto">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">Discussion Forums</h1>
                    <p class="text-xl md:text-2xl leading-relaxed">
                        Join conversations in our specialized forums covering all aspects of water and sanitation.
                    </p>
                </div>
            </div>
        </section>

        <!-- Breadcrumb -->
        <div class="bg-white border-b borderColor">
            <div class="container mx-auto px-4 py-3">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="/" class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-primary">
                                <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                                Home
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                                <a href="<?= base_url('ksp/networking-corner') ?>" class="ml-1 text-sm font-medium text-slate-600 hover:text-primary md:ml-2">Networking Corner</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                                <span class="ml-1 text-sm font-medium text-primary md:ml-2">Forums</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-12">
            <?php if (!empty($forums)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($forums as $forum): ?>
                        <div class="bg-white rounded-lg shadow-sm border borderColor hover:shadow-md transition-shadow duration-300">
                            <div class="p-6">
                                <div class="flex items-center mb-4">
                                    <div class="bg-secondaryShades-100 p-3 rounded-full mr-4">
                                        <i data-lucide="<?= esc($forum->icon ?? 'message-circle') ?>" class="w-6 h-6 text-secondary"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-dark"><?= esc($forum->name ?? '') ?></h3>
                                </div>
                                <p class="text-slate-600 mb-4 line-clamp-3">
                                    <?= esc($forum->description ?? '') ?>
                                </p>
                                <div class="flex justify-between items-center pt-4 border-t borderColor">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-slate-500">Created</span>
                                        <span class="text-sm font-medium text-slate-700"><?= esc(Carbon::parse($forum->created_at ?? '')->format('M d, Y')) ?></span>
                                    </div>
                                    <a href="<?= base_url('ksp/networking-corner-forum-discussion/'.$forum->slug) ?>" class="gradient-btn px-6 py-2 rounded-md text-white font-medium flex items-center hover:opacity-90 transition-opacity">
                                        View Forum
                                        <i data-lucide="arrow-right" class="ml-2 w-4 h-4"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-sm border borderColor p-12 text-center">
                    <div class="max-w-md mx-auto">
                        <div class="bg-secondaryShades-50 p-6 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6">
                            <i data-lucide="message-square" class="w-12 h-12 text-secondary"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-dark mb-3">No Forums Yet</h3>
                        <p class="text-slate-600 mb-6">
                            Forums are being set up. Check back soon for engaging discussions on water and sanitation topics.
                        </p>
                        <a href="<?= base_url('ksp/networking-corner') ?>" class="gradient-btn px-6 py-3 rounded-md text-white font-medium inline-flex items-center">
                            <i data-lucide="arrow-left" class="mr-2 w-5 h-5"></i>
                            Back to Networking Corner
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>
<?= $this->endSection() ?>