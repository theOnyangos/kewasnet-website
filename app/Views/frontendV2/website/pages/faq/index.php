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
<main class="">
    <!-- FAQ Hero Section -->
    <section class="relative gradient-bg text-white pt-24 pb-20 overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full opacity-10 z-10">
            <div class="absolute top-1/4 left-1/4 w-64 h-64 rounded-full bg-white opacity-20"></div>
            <div class="absolute bottom-0 right-0 w-48 h-48 rounded-full bg-white opacity-10"></div>
        </div>
        
        <div class="container mx-auto px-4 text-center relative z-10">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">Frequently Asked Questions</h2>
            <p class="text-xl max-w-3xl mx-auto mb-10 text-blue-100">Find answers to common questions about KEWASNET, our programs, and how you can get involved.</p>
            
            <div class="search-container">
                <div class="search-icon">
                    <i class="fas fa-search"></i>
                </div>
                <input type="text" id="faq-search" placeholder="Search for questions..." class="search-input">
            </div>
            
            <div class="floating-icon z-20">
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
        
        <div class="water-wave">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
            </svg>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 bg-slate-50">
        <div class="container mx-auto px-4">
            <div class="category-filter">
                <button class="category-btn active" data-category="all">All Questions</button>
                <button class="category-btn" data-category="general">General</button>
                <button class="category-btn" data-category="membership">Membership</button>
                <button class="category-btn" data-category="ksp">KSP</button>
                <button class="category-btn" data-category="contact">Contact</button>
            </div>
            
            <div class="max-w-4xl mx-auto">
                <div class="no-results" id="no-results">
                    <i class="fas fa-search fa-2x mx-auto mb-4 text-slate-300"></i>
                    <h3 class="text-xl font-semibold mb-2">No results found</h3>
                    <p>Try different keywords or browse our categories</p>
                </div>
                
                <div class="faq-container" id="faq-container">
                    <!-- FAQs will be rendered here by JavaScript -->
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-primaryShades-100">
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-3xl font-bold text-primary mb-6">Still have questions?</h2>
                <p class="text-xl text-slate-600 mb-10">Can't find the answer you're looking for? Please reach out to our friendly team.</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="mailto:info@kewasnet.co.ke" class="bg-primary hover:bg-primary/90 text-white px-8 py-4 rounded-lg font-medium transition-colors flex items-center justify-center">
                        <i class="fas fa-envelope mr-2"></i> Email Us
                    </a>
                    <a href="tel:+254705530499" class="border border-primary text-primary hover:bg-primary hover:text-white px-8 py-4 rounded-lg font-medium transition-colors flex items-center justify-center">
                        <i class="fas fa-phone mr-2"></i> Call Us
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Global variable to store FAQs
    let faqs = [];
    
    // Function to fetch FAQs from the server
    function fetchFAQs() {
        $.ajax({
            url: '<?= base_url('faq/get') ?>',
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                // Show loading spinner or indicator
                $('#faq-container').html('<div class="text-center py-12"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div><p class="mt-4 text-slate-600">Loading FAQs...</p></div>');
            },
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    faqs = response.data;
                    renderFAQs();
                } else {
                    showError('Failed to load FAQs. Please try again later.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching FAQs:', error);
                showError('Unable to load FAQs. Please check your connection and try again.');
            }
        });
    }
    
    // Function to show error message
    function showError(message) {
        $('#faq-container').html(`
            <div class="text-center py-12">
                <i class="fas fa-exclamation-triangle fa-2x text-red-400 mb-4"></i>
                <h3 class="text-xl font-semibold mb-2 text-red-600">Error Loading FAQs</h3>
                <p class="text-slate-600 mb-4">${message}</p>
                <button id="retry-btn" class="bg-primary hover:bg-primary/90 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-redo mr-2"></i>Try Again
                </button>
            </div>
        `);
        
        // Add retry functionality
        $('#retry-btn').click(function() {
            fetchFAQs();
        });
    }

    // Function to render FAQs
    function renderFAQs(faqsToRender = faqs) {
        const $faqContainer = $('#faq-container');
        $faqContainer.empty();
        
        if (faqsToRender.length === 0) {
            $('#no-results').show();
            return;
        }
        
        $('#no-results').hide();
        
        faqsToRender.forEach(faq => {
            const faqItem = $(`
                <div class="faq-item" data-categories="${faq.category}">
                    <details>
                        <summary class="faq-question">
                            <span>${faq.question}</span>
                            <span class="chevron-icon"></span>
                        </summary>
                        <div class="faq-answer">
                            ${faq.answer}
                        </div>
                    </details>
                </div>
            `);
            
            $faqContainer.append(faqItem);
        });
        
        // Add event listeners to the details elements for smooth animation
        $('details').each(function() {
            const $detail = $(this);
            const $summary = $detail.find('summary');
            const $content = $detail.find('.faq-answer');
            
            $summary.on('click', function(e) {
                e.preventDefault();
                
                // Close other open FAQs when one is opened
                if (!$detail[0].open) {
                    $('details').each(function() {
                        const $otherDetail = $(this);
                        if ($otherDetail[0] !== $detail[0] && $otherDetail[0].open) {
                            $otherDetail[0].open = false;
                            $otherDetail.find('.faq-answer').css({
                                'max-height': '0',
                                'padding': '0 24px'
                            });
                        }
                    });
                }
                
                // Toggle the current FAQ
                $detail[0].open = !$detail[0].open;
                
                if ($detail[0].open) {
                    $content.css({
                        'max-height': $content[0].scrollHeight + 'px',
                        'padding': '20px 24px'
                    });
                } else {
                    $content.css({
                        'max-height': '0',
                        'padding': '0 24px'
                    });
                }
            });
        });
    }

    // Mobile menu toggle
    window.toggleMobileMenu = function() {
        const $menu = $('#mobile-menu');
        const $overlay = $('#mobile-menu-overlay');
        $menu.toggleClass('translate-x-0');
        $overlay.toggleClass('hidden');
    }

    // Sticky header effect
    $(window).on('scroll', function() {
        const $header = $('header');
        if ($(window).scrollTop() > 50) {
            $header.addClass('shadow-lg py-2').removeClass('py-3');
        } else {
            $header.removeClass('shadow-lg py-2').addClass('py-3');
        }
    });

    // FAQ search functionality
    $('#faq-search').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        if (searchTerm === '') {
            renderFAQs();
            return;
        }
        
        const filteredFaqs = faqs.filter(faq => 
            faq.question.toLowerCase().includes(searchTerm) || 
            faq.answer.toLowerCase().includes(searchTerm)
        );
        
        renderFAQs(filteredFaqs);
    });
    
    // FAQ category filtering
    $('.category-btn').on('click', function() {
        // Remove active class from all buttons
        $('.category-btn').removeClass('active');
        
        // Add active class to clicked button
        $(this).addClass('active');
        
        const category = $(this).data('category');
        
        if (category === 'all') {
            renderFAQs();
            return;
        }
        
        const filteredFaqs = faqs.filter(faq => faq.category === category);
        renderFAQs(filteredFaqs);
    });

    // Initialize the page by fetching and rendering FAQs
    fetchFAQs();
});
</script>
<?= $this->endSection() ?>