<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/website/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-primary to-secondary text-white py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="text-center">
                    <div class="w-20 h-20 <?= $program->background_color ?> rounded-lg flex items-center justify-center mb-6 mx-auto">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <?= $program->icon_svg ?>
                        </svg>
                    </div>
                    <h1 class="text-5xl font-bold mb-6"><?= esc($program->title) ?></h1>
                    <p class="text-xl max-w-3xl mx-auto">
                        <?= esc($program->description) ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Program Content -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <?php if (!empty($program->image_url)): ?>
                    <!-- Program Image -->
                    <div class="mb-8 rounded-lg overflow-hidden shadow-2xl">
                        <img src="<?= base_url($program->image_url) ?>" 
                             alt="<?= esc($program->title) ?>" 
                             class="w-full h-64 md:h-96 object-cover">
                    </div>
                <?php endif; ?>
                
                <div class="prose prose-lg max-w-none">
                    <?= $program->content ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Programs -->
    <?php if (!empty($relatedPrograms)): ?>
    <section class="py-20 bg-light">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-4xl font-bold text-center mb-12 text-primaryShades-800">Related Programs</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($relatedPrograms as $relatedProgram): ?>
                        <?php if ($relatedProgram->id !== $program->id): ?>
                            <!-- Related Program Card -->
                            <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                                <?php if (!empty($relatedProgram->image_url)): ?>
                                    <!-- Program Image -->
                                    <div class="w-full h-40 overflow-hidden bg-gray-100">
                                        <img src="<?= base_url($relatedProgram->image_url) ?>" 
                                             alt="<?= esc($relatedProgram->title) ?>" 
                                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                    </div>
                                <?php endif; ?>
                                
                                <div class="p-6">
                                    <div class="w-12 h-12 <?= $relatedProgram->background_color ?> rounded-lg flex items-center justify-center mb-4">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <?= $relatedProgram->icon_svg ?>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold mb-3 text-primaryShades-800"><?= esc($relatedProgram->title) ?></h3>
                                    <p class="text-dark mb-4 text-sm">
                                        <?= character_limiter(esc($relatedProgram->description), 120) ?>
                                    </p>
                                    <a href="<?= base_url('programs/' . $relatedProgram->slug) ?>" class="text-primary font-semibold hover:underline text-sm">Learn More →</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="py-20 bg-primary text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-6">Get Involved in This Program</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">
                Join us in making a difference through <?= esc($program->title) ?>. Together, we can create lasting change in Kenya's WASH sector.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?= base_url('contact') ?>" class="bg-white text-primary px-8 py-3 rounded-lg font-semibold hover:bg-lightGray transition-colors">
                    Partner With Us
                </a>
                <a href="<?= base_url('programs') ?>" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary transition-colors">
                    View All Programs
                </a>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    // Add any program-specific JavaScript here
</script>
<?= $this->endSection() ?>
