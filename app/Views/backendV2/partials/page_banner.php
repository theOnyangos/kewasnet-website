<!-- Hero Banner with Gradient Overlay -->
<div class="relative <?= $bannerHeight ?? 'h-48' ?> mb-6 overflow-hidden bg-cover bg-center" style="background-image: linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.8) 100%), url('<?= base_url('images/dashboard-breadcrumb.jpg') ?>');">
    <!-- Content -->
    <div class="relative z-10 h-full flex flex-col justify-center px-6 md:px-12">
        <!-- Breadcrumbs -->
        <nav class="mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="<?= base_url('dashboard') ?>" class="inline-flex items-center text-sm font-medium text-white/80 hover:text-white transition-colors">
                        <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                        Dashboard
                    </a>
                </li>
                <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                    <?php foreach ($breadcrumbs as $index => $crumb): ?>
                        <?php if ($index < count($breadcrumbs) - 1): ?>
                            <li>
                                <div class="flex items-center">
                                    <i data-lucide="chevron-right" class="w-4 h-4 text-white/60"></i>
                                    <a href="<?= $crumb['url'] ?? '#' ?>" class="ml-1 text-sm font-medium text-white/80 hover:text-white transition-colors md:ml-2">
                                        <?= esc($crumb['label']) ?>
                                    </a>
                                </div>
                            </li>
                        <?php else: ?>
                            <li aria-current="page">
                                <div class="flex items-center">
                                    <i data-lucide="chevron-right" class="w-4 h-4 text-white/60"></i>
                                    <span class="ml-1 text-sm font-medium text-white md:ml-2"><?= esc($crumb['label']) ?></span>
                                </div>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ol>
        </nav>

        <!-- Title and Description -->
        <div class="flex items-center justify-between">
            <div class="flex-1 pr-6">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-2 drop-shadow-lg">
                    <?= esc($pageTitle ?? 'Page Title') ?>
                </h1>
                <?php if (isset($pageDescription)): ?>
                    <p class="text-sm md:text-base text-white/80 max-w-3xl line-clamp-2">
                        <?= esc($pageDescription) ?>
                    </p>
                <?php endif; ?>
            </div>

            <?php if (isset($bannerActions)): ?>
                <div class="hidden md:block">
                    <?= $bannerActions ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
