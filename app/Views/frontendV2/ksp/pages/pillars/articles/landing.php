<?= $this->extend('frontendV2/ksp/layouts/main') ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
    <!-- Articles Landing Hero -->
    <section class="relative py-20 bg-gradient-to-r from-primary to-secondary">
        <div class="container mx-auto px-4">
            <div class="text-center text-white">
                <div class="flex items-center justify-center mb-4">
                    <i data-lucide="book-open" class="w-12 h-12 mr-4"></i>
                    <span class="text-2xl font-medium">Knowledge Repository</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold mb-6">Pillar Articles</h1>
                <p class="text-xl max-w-4xl mx-auto leading-relaxed">
                    Explore our comprehensive collection of resources, research, and publications across all strategic pillars of water and sanitation.
                </p>
            </div>
        </div>
    </section>

    <!-- Pillars Grid -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Explore by Pillar</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Select a strategic pillar to access specialized resources, articles, and research materials.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (!empty($pillars)): ?>
                    <?php foreach ($pillars as $pillar): ?>
                        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                            <div class="p-8">
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i data-lucide="droplets" class="w-8 h-8 text-primary"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-3"><?= esc($pillar['title']) ?></h3>
                                    <p class="text-gray-600 mb-6 line-clamp-3">
                                        <?= esc($pillar['description'] ?? 'Explore resources and articles for this strategic pillar.') ?>
                                    </p>
                                    <a href="<?= base_url('ksp/pillar-articles/' . $pillar['slug']) ?>" 
                                       class="inline-flex items-center space-x-2 bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-colors duration-200 font-medium">
                                        <span>Explore Articles</span>
                                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-400 mb-4">
                            <i data-lucide="folder-x" class="w-16 h-16 mx-auto"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-600 mb-2">No Pillars Available</h3>
                        <p class="text-gray-500">Please check back later for available content.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Need More Information?</h2>
                <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                    Can't find what you're looking for? Our team is here to help you access the resources you need.
                </p>
                <a href="<?= base_url('contact-us') ?>" 
                   class="inline-flex items-center space-x-2 bg-secondary text-white px-8 py-4 rounded-lg hover:bg-secondary/90 transition-colors duration-200 font-medium text-lg">
                    <i data-lucide="mail" class="w-5 h-5"></i>
                    <span>Contact Us</span>
                </a>
            </div>
        </div>
    </section>
<?= $this->endSection(); ?>
