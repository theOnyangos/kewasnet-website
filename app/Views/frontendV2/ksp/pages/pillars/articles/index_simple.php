<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/ksp/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?? 'KEWASNET - Pillar Articles' ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">
            <?= isset($pillar['title']) ? esc($pillar['title']) : 'Pillar Articles' ?>
        </h1>
        
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 mb-4">
                <?= isset($pillar['description']) ? esc($pillar['description']) : 'Loading pillar content...' ?>
            </p>
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <h3 class="font-bold mb-2">Statistics</h3>
                    <ul class="space-y-1 text-sm">
                        <li>Total Resources: <?= $totalResources ?? 0 ?></li>
                        <li>Categories: <?= $totalCategories ?? 0 ?></li>
                        <li>Contributors: <?= $totalContributors ?? 0 ?></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-bold mb-2">Resources</h3>
                    <?php if (!empty($resources)): ?>
                        <ul class="space-y-1 text-sm">
                            <?php foreach ($resources as $resource): ?>
                                <li>• <?= esc($resource->title ?? 'Untitled Resource') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-gray-500 text-sm">No resources available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
