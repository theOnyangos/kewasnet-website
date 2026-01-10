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
        'pageTitle' => 'About KEWASNET',
        'pageDescription' => 'Learn more about KEWASNET and our mission',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => base_url('auth/dashboard')],
            ['label' => 'About']
        ]
    ]) ?>

    <div class="px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm p-8">
            <div class="prose max-w-none">
                <h2 class="text-2xl font-bold text-slate-800 mb-4">About KEWASNET</h2>
                <p class="text-slate-600 mb-6">
                    Kenya Water and Sanitation Civil Society Network (KEWASNET) is a national network that brings together 
                    civil society organizations working in the water and sanitation sector in Kenya.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-2">Our Mission</h3>
                        <p class="text-sm text-slate-600">
                            To strengthen the capacity of civil society organizations in the water and sanitation sector 
                            and advocate for improved water and sanitation services for all Kenyans.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-2">Our Vision</h3>
                        <p class="text-sm text-slate-600">
                            A Kenya where all citizens have access to safe, adequate, and affordable water and sanitation services.
                        </p>
                    </div>
                </div>

                <div class="border-t borderColor pt-6">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4">Contact Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i data-lucide="mail" class="w-5 h-5 mr-3 text-primary"></i>
                            <span class="text-slate-600">info@kewasnet.org</span>
                        </div>
                        <div class="flex items-center">
                            <i data-lucide="phone" class="w-5 h-5 mr-3 text-primary"></i>
                            <span class="text-slate-600">+254 XXX XXX XXX</span>
                        </div>
                        <div class="flex items-center">
                            <i data-lucide="map-pin" class="w-5 h-5 mr-3 text-primary"></i>
                            <span class="text-slate-600">Nairobi, Kenya</span>
                        </div>
                    </div>
                </div>

                <div class="border-t borderColor pt-6 mt-6">
                    <a href="<?= base_url('about') ?>" target="_blank" class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium">
                        <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
                        View Full About Page
                    </a>
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