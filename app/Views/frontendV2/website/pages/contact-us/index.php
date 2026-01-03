<?php 
    use App\Helpers\UrlHelper;
    use App\Models\SocialLink;
    $currentUrl = new UrlHelper();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);

    $socialLink     = new SocialLink();
    $socialLinks    = $socialLink->getSocialLinks();

    $uri            = service('uri');
    $segments       = $uri->getSegments();

    $facebook       = $socialLinks['facebook'];
    $twitter        = $socialLinks['twitter'];
    $instagram      = $socialLinks['instagram'];
    $linkedin       = $socialLinks['linkedin'];
    $youtube        = $socialLinks['youtube'];
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
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl font-bold mb-6">Get in Touch</h1>
            <p class="text-xl max-w-3xl mx-auto">
                Connect with us to learn more about our work, explore partnership opportunities, or access WASH sector resources
            </p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-16">
                <!-- Contact Form -->
                <div>
                    <h2 class="text-3xl font-bold mb-8 text-slate-800">Send us a Message</h2>
                    <form id="contactForm" class="space-y-6" action="<?= site_url('contact-us/submit') ?>" method="POST">
                        <!-- CSRF Token -->
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="first-name" class="block text-sm font-medium text-slate-700 mb-2">First Name <span class="text-red-500">*</span></label>
                                <input type="text" id="first-name" name="first-name" maxlength="50" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                                <p class="mt-1 text-sm text-red-600 hidden" id="first-name-error"></p>
                            </div>
                            <div>
                                <label for="last-name" class="block text-sm font-medium text-slate-700 mb-2">Last Name <span class="text-red-500">*</span></label>
                                <input type="text" id="last-name" name="last-name" maxlength="50" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                                <p class="mt-1 text-sm text-red-600 hidden" id="last-name-error"></p>
                            </div>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" maxlength="100" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                            <p class="mt-1 text-sm text-red-600 hidden" id="email-error"></p>
                        </div>
                        <div>
                            <label for="organization" class="block text-sm font-medium text-slate-700 mb-2">Organization</label>
                            <input type="text" id="organization" name="organization" maxlength="100" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary">
                            <p class="mt-1 text-sm text-red-600 hidden" id="organization-error"></p>
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-medium text-slate-700 mb-2">Subject <span class="text-red-500">*</span></label>
                            <select id="subject" name="subject" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary select2">
                                <option value="">Select a subject</option>
                                <option value="General Inquiry">General Inquiry</option>
                                <option value="Partnership Opportunity">Partnership Opportunity</option>
                                <option value="Resource Request">Resource Request</option>
                                <option value="Technical Support">Technical Support</option>
                                <option value="Media Inquiry">Media Inquiry</option>
                                <option value="Training/Capacity Building">Training/Capacity Building</option>
                            </select>
                            <p class="mt-1 text-sm text-red-600 hidden" id="subject-error"></p>
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-slate-700 mb-2">Message <span class="text-red-500">*</span></label>
                            <textarea id="message" name="message" rows="5" minlength="10" maxlength="2000" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary" placeholder="Tell us how we can help you..."></textarea>
                            <p class="mt-1 text-sm text-red-600 hidden" id="message-error"></p>
                            <p class="mt-1 text-sm text-slate-500">
                                <span id="message-count">0</span>/2000 characters
                            </p>
                        </div>
                        
                        <button type="submit" class="w-full gradient-btn flex items-center justify-center text-white px-6 py-2 rounded-[50px] transition-all duration-300">
                            <span>Send Message</span>
                            <i data-lucide="send" class="ml-2 icon z-10"></i>
                        </button>
                    </form>
                </div>

                <!-- Contact Information -->
                <div>
                    <h2 class="text-3xl font-bold mb-8 text-slate-800">Contact Information</h2>
                    
                    <!-- Office Info -->
                    <div class="space-y-8 mb-12">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-800 mb-2">Head Office</h3>
                                <p class="text-slate-600">
                                    KEWASNET Plaza, 3rd Floor<br>
                                    Kilimani Road, Nairobi<br>
                                    P.O. Box 12345-00100<br>
                                    Nairobi, Kenya
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-secondary rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-800 mb-2">Phone Numbers</h3>
                                <p class="text-slate-600">
                                    Main Line: +254 20 123 4567<br>
                                    Mobile: +254 700 123 456<br>
                                    Emergency: +254 722 345 678
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-800 mb-2">Email Addresses</h3>
                                <p class="text-slate-600">
                                    General: info@kewasnet.org<br>
                                    Programs: programs@kewasnet.org<br>
                                    Partnerships: partners@kewasnet.org<br>
                                    Media: media@kewasnet.org
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-800 mb-2">Office Hours</h3>
                                <p class="text-slate-600">
                                    Monday - Friday: 8:00 AM - 5:00 PM<br>
                                    Saturday: 9:00 AM - 1:00 PM<br>
                                    Sunday: Closed<br>
                                    Public Holidays: Closed
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-4">Follow Us</h3>
                        <div class="flex space-x-4">
                            <a href="<?= base_url($facebook) ?>" target="_blank" class="w-10 h-10 btn-background rounded-lg flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M20 10C20 4.477 15.523 0 10 0S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                            <a href="<?= base_url($twitter) ?>" target="_blank" class="w-10 h-10 btn-background rounded-lg flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"></path>
                                </svg>
                            </a>
                            <a href="<?= base_url($linkedin) ?>" target="_blank" class="w-10 h-10 btn-background rounded-lg flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                            <a href="https://www.tiktok.com/@kewasnet?_t=ZM-8zkVFuboFzs&_r=1" target="_blank" class="w-10 h-10 btn-background rounded-lg flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0020 10.017C20 4.484 15.522 0 10 0z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="py-20 bg-slate-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4 text-slate-800">Find Us</h2>
                <p class="text-slate-600">Visit our office in the heart of Nairobi</p>
            </div>
            <div class="bg-primaryShades-300 rounded-lg h-96 flex items-center justify-center">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.8018411480057!2d36.783344475896264!3d-1.2933603356358057!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f110e94f8d50b%3A0x5755139385b0ba9e!2sMango%20Court%20Furnished%20Apartments!5e0!3m2!1sen!2ske!4v1706680425759!5m2!1sen!2ske" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    const $form = $('#contactForm');
    const $messageTextarea = $('#message');
    const $messageCounter = $('#message-count');
    
    // Character counter for message
    $messageTextarea.on('input', function() {
        const count = $(this).val().length;
        $messageCounter.text(count);
        
        if (count > 2000) {
            $messageCounter.addClass('text-red-500').removeClass('text-slate-500');
        } else {
            $messageCounter.removeClass('text-red-500').addClass('text-slate-500');
        }
    });
    
    // Form submission
    $form.on('submit', function(e) {
        e.preventDefault();
        clearErrors();
        
        const $submitButton = $form.find('button[type="submit"]');
        const originalContent = $submitButton.html();
        
        // Show loading state
        $submitButton.prop('disabled', true);
        $submitButton.html(`
            <span>Sending...</span>
            <svg class="ml-2 animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        `);
        
        // Submit form via AJAX
        const formData = new FormData(this);
        
        $.ajax({
            url: '<?= site_url('contact-us/submit') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .done(function(data) {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Message Sent!',
                    text: data.message,
                    confirmButtonColor: '#34D399'
                });
                $form[0].reset();
                $messageCounter.text('0');
            } else {
                throw new Error(data.message || 'An error occurred');
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Error:', error);
            
            // Handle validation errors
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function(field, message) {
                    showError(field, message);
                });
            } else {
                const errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to send message. Please try again.';
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                    confirmButtonColor: '#EF4444'
                });
            }
        })
        .always(function() {
            // Restore button state
            $submitButton.prop('disabled', false);
            $submitButton.html(originalContent);
        });
    });
    
    // Helper functions
    function showError(fieldName, message) {
        const $field = $('#' + fieldName);
        const $errorElement = $('#' + fieldName + '-error');
        
        if ($field.length) {
            $field.addClass('border-red-500 bg-red-50').removeClass('border-borderColor');
        }
        
        if ($errorElement.length) {
            $errorElement.text(message).removeClass('hidden');
        }
    }
    
    function clearErrors() {
        // Clear all error states
        $('[id$="-error"]').addClass('hidden');
        
        // Remove error styling from fields
        $('input, select, textarea').removeClass('border-red-500 bg-red-50').addClass('border-borderColor');
    }
});
</script>
<?= $this->endSection() ?>