<?= $this->include('frontendV2/ksp/layouts/constants/auth-header') ?>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="auth-container w-full bg-white rounded-xl p-8 sm:p-10 shadow-md z-10">
            <div class="text-center mb-8">
                <img src="<?= base_url('assets/new/site-logo.png') ?>" alt="KEWASNET Logo" class="h-12 mx-auto mb-4">
                <h1 class="text-2xl font-bold text-slate-800">Forgot Password</h1>
                <p class="text-slate-600">
                    Enter your email address to receive a password reset code.
                </p>
            </div>

            <!-- Session Flash Messages -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-4">
                    <div class="alert alert-danger">
                        <i data-lucide="alert-circle" class="w-8 h-8 mr-2"></i>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Status Message -->
            <div id="statusMessage" class="mb-4"></div>

            <!-- Email Form -->
            <form id="forgotPasswordForm" class="mb-5" action="<?= base_url('ksp/send-reset-code') ?>" method="POST">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" 
                            placeholder="Enter your email"
                        >
                    </div>

                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="flex justify-center items-center gap-2 w-full gradient-btn hover:bg-primary-dark text-white font-medium py-3 px-4 rounded-lg transition duration-200 mt-3"
                    >
                        <i data-lucide="mail" class="w-5 h-5 z-10"></i>
                        <span id="submitText">Send Reset Code</span>
                    </button>
                </div>
            </form>

            <!-- Sign In Link -->
            <div class="text-center text-sm text-slate-600">
                Remember your password? <a href="<?= base_url('ksp/login') ?>" class="text-primary font-medium hover:text-primary-dark">Sign In</a>
            </div>
        </div>
    </div>

    <?= $this->section('scripts') ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Lucide icons
            lucide.createIcons();

            // Handle form submission
            $('#forgotPasswordForm').on('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = $('#submitBtn');
                const submitText = $('#submitText');
                const email = $('#email').val().trim();
                
                // Validate email
                if (!email || !email.includes('@')) {
                    showStatus('Please enter a valid email address', 'alert-circle', 'bg-red-100 text-red-800');
                    return;
                }

                // Disable button and show loading state
                submitBtn.prop('disabled', true);
                submitText.text('Sending...');
                submitBtn.find('i').replaceWith('<i data-lucide="loader-circle" class="w-5 h-5 animate-spin"></i>');
                lucide.createIcons();

                // Make AJAX request
                $.ajax({
                    url: $('#forgotPasswordForm').attr('action'),
                    type: $('#forgotPasswordForm').attr('method'),
                    data: {
                        email: email,
                        <?= csrf_token() ?>: $('input[name="<?= csrf_token() ?>"]').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            showStatus(response.message || 'Reset code sent successfully!', 'check-circle', 'bg-green-100 text-green-800');
                            
                            // Redirect to OTP page with email parameter after 2 seconds
                            setTimeout(() => {
                                window.location.href = '<?= base_url('ksp/verify-reset-code') ?>?email=' + encodeURIComponent(email);
                            }, 2000);
                        } else {
                            showStatus(response.message || 'Failed to send reset code', 'alert-circle', 'bg-red-100 text-red-800');
                            submitBtn.prop('disabled', false);
                            submitText.text('Send Reset Code');
                            submitBtn.find('i').replaceWith('<i data-lucide="mail" class="w-5 h-5"></i>');
                            lucide.createIcons();
                        }
                    },
                    error: function(xhr) {
                        let message = 'An error occurred while sending the request';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        showStatus(message, 'alert-circle', 'bg-red-100 text-red-800');
                        submitBtn.prop('disabled', false);
                        submitText.text('Send Reset Code');
                        submitBtn.find('i').replaceWith('<i data-lucide="mail" class="w-5 h-5"></i>');
                        lucide.createIcons();
                    }
                });
            });

            // Show status message
            function showStatus(message, icon, classes) {
                $('#statusMessage').html(`
                    <div class="p-3 rounded-lg ${classes} text-sm flex items-start">
                        <i data-lucide="${icon}" class="w-4 h-4 mt-0.5 mr-2 flex-shrink-0"></i>
                        <span>${message}</span>
                    </div>
                `);
                lucide.createIcons();
            }
        });
    </script>
    <?= $this->endSection() ?>
<?= $this->include('frontendV2/ksp/layouts/constants/auth-footer') ?>