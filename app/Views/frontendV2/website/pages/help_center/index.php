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
    <!-- Main Content -->
    <main class="flex-grow container mx-auto py-8 px-4 sm:px-6">
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-primary via-secondary to-primary text-white rounded-xl p-8 mb-12">
            <div class="max-w-3xl">
                <h1 class="text-3xl md:text-4xl font-bold mb-4">How can we help you?</h1>
                <p class="text-lg mb-6">Find answers to your questions about KEWASNET's programs, services, and initiatives.</p>
                <div class="relative">
                    <input type="text" placeholder="Search for answers..." class="w-full py-4 px-6 rounded-lg text-dark focus:outline-none focus:ring-2 focus:ring-secondary">
                    <button class="absolute right-2 top-2 bg-secondary hover:bg-secondaryShades-600 text-white py-2 px-4 rounded-lg">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- Help Categories -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-dark mb-8 text-center">Browse Help Topics</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="h-12 w-12 rounded-md bg-primaryShades-100 text-primary flex items-center justify-center mb-4">
                        <i class="fas fa-info-circle text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-dark mb-2">About KEWASNET</h3>
                    <p class="text-slate-600 mb-4">Learn about our mission, vision, and organizational structure.</p>
                    <a href="<?= base_url('about') ?>" class="text-secondary font-medium flex items-center">
                        Explore <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="h-12 w-12 rounded-md bg-secondaryShades-100 text-secondary flex items-center justify-center mb-4">
                        <i class="fas fa-hands-helping text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-dark mb-2">Get Involved</h3>
                    <p class="text-slate-600 mb-4">Find out how to volunteer, partner, or support our initiatives.</p>
                    <a href="<?= base_url('contact-us') ?>" class="text-secondary font-medium flex items-center">
                        Explore <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="h-12 w-12 rounded-md bg-primaryShades-100 text-primary flex items-center justify-center mb-4">
                        <i class="fas fa-file-alt text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-dark mb-2">Resources</h3>
                    <p class="text-slate-600 mb-4">Access reports, publications, and educational materials.</p>
                    <a href="<?= base_url('resources') ?>" class="text-secondary font-medium flex items-center">
                        Explore <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="h-12 w-12 rounded-md bg-secondaryShades-100 text-secondary flex items-center justify-center mb-4">
                        <i class="fas fa-question-circle text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-dark mb-2">FAQs</h3>
                    <p class="text-slate-600 mb-4">Find answers to our most frequently asked questions.</p>
                    <a href="<?= base_url('faq') ?>" class="text-secondary font-medium flex items-center">
                        Explore <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- Popular Articles -->
        <section class="mb-16">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-dark">Popular Articles</h2>
                <a href="#" class="text-primary font-medium">View all articles <i class="fas fa-arrow-right ml-2 text-xs"></i></a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="h-40 bg-primaryShades-100 flex items-center justify-center">
                        <i class="fas fa-file-pdf text-4xl text-primary"></i>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-dark mb-2">How to access our research papers</h3>
                        <p class="text-slate-600 mb-4">Step-by-step guide to accessing and downloading our research publications.</p>
                        <a href="#" class="text-secondary font-medium flex items-center">
                            Read more <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="h-40 bg-secondaryShades-100 flex items-center justify-center">
                        <i class="fas fa-hand-holding-usd text-4xl text-secondary"></i>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-dark mb-2">Making donations to KEWASNET</h3>
                        <p class="text-slate-600 mb-4">Learn about donation options, tax benefits, and how your contribution helps.</p>
                        <a href="#" class="text-secondary font-medium flex items-center">
                            Read more <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="h-40 bg-primaryShades-100 flex items-center justify-center">
                        <i class="fas fa-user-friends text-4xl text-primary"></i>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-dark mb-2">Volunteer opportunities</h3>
                        <p class="text-slate-600 mb-4">Discover how you can contribute your time and skills to our cause.</p>
                        <a href="#" class="text-secondary font-medium flex items-center">
                            Read more <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Support -->
        <section class="bg-white rounded-xl shadow-md p-8 mb-12">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-2xl font-bold text-dark mb-4">Still need help?</h2>
                <p class="text-slate-600 mb-6">Our support team is here to assist you with any questions or concerns you may have.</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#" class="bg-primary hover:bg-primaryShades-700 text-white font-medium py-3 px-6 rounded-lg flex items-center justify-center">
                        <i class="far fa-envelope mr-2"></i> Email Support
                    </a>
                    <a href="#" class="border border-borderColor hover:border-primary text-dark font-medium py-3 px-6 rounded-lg flex items-center justify-center">
                        <i class="far fa-calendar-alt mr-2"></i> Schedule a Call
                    </a>
                </div>
            </div>
        </section>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script></script>
<?= $this->endSection() ?>