<?= $this->extend('backendV2/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <?= view('backendV2/partials/page_banner', [
            'pageTitle' => 'Create Newsletter',
            'pageDescription' => 'Compose and send a newsletter to all subscribers',
            'breadcrumbs' => [
                ['label' => 'Blogs', 'url' => base_url('auth/blogs')],
                ['label' => 'Newsletters', 'url' => base_url('auth/blogs/newsletters')],
                ['label' => 'Create']
            ],
            'bannerActions' => '<a href="' . base_url('auth/blogs/newsletters') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Newsletters
            </a>'
        ]) ?>

        <div class="bg-white rounded-b-xl shadow-sm max-w-full mx-5 mb-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6 border-b border-borderColor">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-600 font-medium">Total Subscribers</p>
                            <h3 class="text-2xl font-bold text-blue-900"><?= number_format($subscriberStats->total_newsletters ?? 0) ?></h3>
                        </div>
                        <div class="bg-blue-500 p-3 rounded-lg">
                            <i data-lucide="users" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-green-600 font-medium">Active Subscribers</p>
                            <h3 class="text-2xl font-bold text-green-900"><?= number_format($subscriberStats->total_subscribers ?? 0) ?></h3>
                        </div>
                        <div class="bg-green-500 p-3 rounded-lg">
                            <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-purple-600 font-medium">Will Receive</p>
                            <h3 class="text-2xl font-bold text-purple-900"><?= number_format($subscriberStats->total_subscribers ?? 0) ?></h3>
                        </div>
                        <div class="bg-purple-500 p-3 rounded-lg">
                            <i data-lucide="mail" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Form -->
            <form class="p-6 space-y-6" id="createNewsletterForm" method="POST">
                <!-- Subject Field -->
                <div>
                    <label for="subject" class="block text-sm font-medium text-dark mb-1">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="subject" name="subject" required 
                        class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" 
                        placeholder="Enter newsletter subject">
                    <p class="mt-1 text-xs text-gray-500">This will be the email subject line</p>
                </div>

                <!-- Preview Text -->
                <div>
                    <label for="preview_text" class="block text-sm font-medium text-dark mb-1">
                        Preview Text
                    </label>
                    <input type="text" id="preview_text" name="preview_text" maxlength="200"
                        class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" 
                        placeholder="Short preview that appears in email clients">
                    <div class="flex justify-between">
                        <p class="mt-1 text-xs text-gray-500">Brief text shown in inbox preview</p>
                        <span id="preview-count" class="text-xs text-gray-500">0/200</span>
                    </div>
                </div>

                <!-- Sender Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="sender_name" class="block text-sm font-medium text-dark mb-1">
                            Sender Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="sender_name" name="sender_name" required
                            value="<?= env('email.fromName', 'Kewasnet') ?>"
                            class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" 
                            placeholder="Your name or organization">
                    </div>

                    <div>
                        <label for="sender_email" class="block text-sm font-medium text-dark mb-1">
                            Sender Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="sender_email" name="sender_email" required
                            value="<?= env('email.fromEmail', 'info@kewasnet.co.ke') ?>"
                            class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" 
                            placeholder="sender@example.com">
                    </div>
                </div>

                <!-- Content Editor -->
                <div>
                    <label for="content" class="block text-sm font-medium text-dark mb-1">
                        Newsletter Content <span class="text-red-500">*</span>
                    </label>
                    <div class="rounded-lg">
                        <textarea id="content" name="content" required 
                            class="summernote-editor w-full px-4 py-3 focus:outline-none" 
                            placeholder="Write your newsletter content here..."></textarea>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Compose your newsletter message with rich formatting</p>
                </div>

                <!-- Hidden Newsletter ID (for auto-save) -->
                <input type="hidden" id="newsletter_id" name="newsletter_id" value="">

                <!-- CSRF Token -->
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                <!-- Auto-save indicator -->
                <div id="autosave-indicator" class="text-sm text-gray-500 text-center hidden">
                    <i data-lucide="loader" class="w-4 h-4 inline-block animate-spin"></i>
                    <span>Saving draft...</span>
                </div>
                <div id="autosave-success" class="text-sm text-green-600 text-center hidden">
                    <i data-lucide="check" class="w-4 h-4 inline-block"></i>
                    <span>Draft saved</span>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-wrap justify-end gap-3 pt-6 border-t border-borderColor">
                    <button type="button" id="saveDraftBtn" 
                        class="px-6 py-3 rounded-lg text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors flex items-center gap-2">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        <span>Save Draft</span>
                    </button>

                    <button type="button" id="sendTestBtn" 
                        class="px-6 py-3 rounded-lg text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors flex items-center gap-2">
                        <i data-lucide="test-tube" class="w-5 h-5"></i>
                        <span>Send Test</span>
                    </button>

                    <button type="submit" id="sendNewsletterBtn"
                        class="gradient-btn rounded-lg px-8 py-3 text-white flex items-center gap-2">
                        <i data-lucide="send" class="w-5 h-5 z-10"></i>
                        <span>Send to All Subscribers</span>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Test Email Modal -->
    <div id="testEmailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Send Test Email</h3>
                <button type="button" class="close-test-modal text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="testEmailForm">
                <div class="mb-4">
                    <label for="test_email" class="block text-sm font-medium text-dark mb-1">
                        Test Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="test_email" name="test_email" required
                        class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" 
                        placeholder="test@example.com">
                    <p class="mt-1 text-xs text-gray-500">Enter an email to receive the test</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" class="close-test-modal px-4 py-2 rounded-lg text-gray-700 bg-gray-100 hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 rounded-lg text-white bg-blue-500 hover:bg-blue-600 flex items-center gap-2">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Send Test</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize lucide icons
        lucide.createIcons();

        let newsletterId = '';
        let autoSaveInterval;
        let hasUnsavedChanges = false;

        // Initialize Summernote
        $('.summernote-editor').summernote({
            height: 400,
            placeholder: 'Write your newsletter content here...',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onChange: function(contents) {
                    hasUnsavedChanges = true;
                }
            }
        });

        // Character counter for preview text
        $('#preview_text').on('input', function() {
            const count = $(this).val().length;
            $('#preview-count').text(`${count}/200`);
        });

        // Track changes
        $('#subject, #preview_text, #sender_name, #sender_email').on('input', function() {
            hasUnsavedChanges = true;
        });

        // Auto-save every 30 seconds
        autoSaveInterval = setInterval(function() {
            if (hasUnsavedChanges) {
                saveDraft(true);
            }
        }, 30000);

        // Save Draft
        $('#saveDraftBtn').on('click', function() {
            saveDraft(false);
        });

        let isSaving = false;

        function saveDraft(isAutoSave = false) {
            // Prevent duplicate saves
            if (isSaving) {
                return;
            }

            const formData = {
                subject: $('#subject').val(),
                preview_text: $('#preview_text').val(),
                sender_name: $('#sender_name').val(),
                sender_email: $('#sender_email').val(),
                content: $('.summernote-editor').summernote('code'),
                newsletter_id: newsletterId || null,
                [<?= json_encode(csrf_token()) ?>]: $('input[name="<?= csrf_token() ?>"]').val()
            };

            // Validate required fields
            if (!formData.subject || !formData.content || !formData.sender_name || !formData.sender_email) {
                if (!isAutoSave) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Information',
                        text: 'Please fill in all required fields before saving.'
                    });
                }
                return;
            }

            if (isAutoSave) {
                $('#autosave-indicator').removeClass('hidden');
                $('#autosave-success').addClass('hidden');
            }

            isSaving = true;

            $.ajax({
                url: '<?= site_url('auth/blogs/newsletters/save-draft') ?>',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    isSaving = false;
                    if (response.status === 'success') {
                        newsletterId = response.data?.id || newsletterId;
                        $('#newsletter_id').val(newsletterId);
                        hasUnsavedChanges = false;

                        if (isAutoSave) {
                            $('#autosave-indicator').addClass('hidden');
                            $('#autosave-success').removeClass('hidden');
                            setTimeout(() => $('#autosave-success').addClass('hidden'), 3000);
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Draft Saved',
                                text: 'Your newsletter has been saved as a draft.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    } else {
                        // Handle error response
                        if (isAutoSave) {
                            $('#autosave-indicator').addClass('hidden');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Save Failed',
                                text: response?.message || 'Failed to save draft. Please try again.'
                            });
                        }
                    }
                },
                error: function(xhr) {
                    isSaving = false;
                    $('#autosave-indicator').addClass('hidden');
                    $('#autosave-success').addClass('hidden');
                    
                    if (!isAutoSave) {
                        const response = xhr.responseJSON;
                        Swal.fire({
                            icon: 'error',
                            title: 'Save Failed',
                            text: response?.message || 'Failed to save draft. Please try again.'
                        });
                    }
                }
            });
        }

        // Send Test Email
        $('#sendTestBtn').on('click', function() {
            // Validate form first
            const subject = $('#subject').val();
            const content = $('.summernote-editor').summernote('code');
            
            if (!subject || !content) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please fill in subject and content before sending a test.'
                });
                return;
            }

            $('#testEmailModal').removeClass('hidden');
            lucide.createIcons();
        });

        // Close test modal
        $('.close-test-modal').on('click', function() {
            $('#testEmailModal').addClass('hidden');
            $('#test_email').val('');
        });

        // Submit test email
        $('#testEmailForm').on('submit', function(e) {
            e.preventDefault();

            const testEmail = $('#test_email').val();
            const formData = {
                subject: $('#subject').val(),
                preview_text: $('#preview_text').val(),
                sender_name: $('#sender_name').val(),
                sender_email: $('#sender_email').val(),
                content: $('.summernote-editor').summernote('code'),
                test_email: testEmail,
                [<?= json_encode(csrf_token()) ?>]: $('input[name="<?= csrf_token() ?>"]').val()
            };

            $.ajax({
                url: '<?= site_url('auth/blogs/newsletters/send-test') ?>',
                method: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    Swal.fire({
                        title: 'Sending Test Email',
                        text: 'Please wait...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    $('#testEmailModal').addClass('hidden');
                    Swal.fire({
                        icon: 'success',
                        title: 'Test Email Sent',
                        text: `Test email sent successfully to ${testEmail}`,
                        timer: 3000,
                        showConfirmButton: false
                    });
                    $('#test_email').val('');
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: 'Send Failed',
                        text: response?.message || 'Failed to send test email. Please try again.'
                    });
                }
            });
        });

        // Send Newsletter to All Subscribers
        $('#createNewsletterForm').on('submit', function(e) {
            e.preventDefault();

            const subject = $('#subject').val();
            const content = $('.summernote-editor').summernote('code');

            if (!subject || !content) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please fill in all required fields.'
                });
                return;
            }

            // Confirmation dialog
            Swal.fire({
                title: 'Send Newsletter?',
                html: `Are you sure you want to send this newsletter to <strong><?= number_format($subscriberStats->total_subscribers ?? 0) ?></strong> active subscribers?<br><br><small class="text-gray-500">This action cannot be undone.</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Send Newsletter',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    sendNewsletter();
                }
            });
        });

        function sendNewsletter() {
            // Validate that newsletter has been saved
            if (!newsletterId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Save Required',
                    text: 'Please save your newsletter as a draft before sending.',
                    confirmButtonText: 'Save Draft'
                }).then((result) => {
                    if (result.isConfirmed) {
                        saveDraft(false);
                    }
                });
                return;
            }

            proceedWithSend();
        }

        function proceedWithSend() {
            $.ajax({
                url: `<?= site_url('auth/blogs/newsletters') ?>/${newsletterId}/send`,
                method: 'POST',
                data: {
                    [<?= json_encode(csrf_token()) ?>]: $('input[name="<?= csrf_token() ?>"]').val()
                },
                dataType: 'json',
                beforeSend: function() {
                    Swal.fire({
                        title: 'Sending Newsletter',
                        html: 'Please wait while we send your newsletter to all subscribers...<br><small class="text-gray-500">This may take a few moments.</small>',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    clearInterval(autoSaveInterval);
                    Swal.fire({
                        icon: 'success',
                        title: 'Newsletter Sent!',
                        html: `Successfully sent to <strong>${response.sent_count}</strong> subscribers.<br>
                               ${response.failed_count > 0 ? `<span class="text-red-500">${response.failed_count} failed</span>` : ''}`,
                        confirmButtonText: 'View Sent Newsletters'
                    }).then(() => {
                        window.location.href = '<?= site_url('auth/blogs/newsletters/sent') ?>';
                    });
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: 'Send Failed',
                        text: response?.message || 'Failed to send newsletter. Please try again.'
                    });
                }
            });
        }

        // Clear errors on input
        $('input, textarea').on('input', function() {
            $(this).removeClass('is-invalid border-red-500');
            $(this).next('.invalid-feedback').remove();
        });

        // Warn before leaving with unsaved changes
        window.addEventListener('beforeunload', function(e) {
            if (hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Cleanup on page unload
        window.addEventListener('unload', function() {
            clearInterval(autoSaveInterval);
        });
    });
</script>
<?= $this->endSection() ?>
