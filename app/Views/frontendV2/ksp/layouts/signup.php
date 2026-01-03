<?= $this->include('frontendV2/ksp/layouts/constants/auth-header') ?>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="auth-container w-full bg-white rounded-xl shadow-sm p-8 sm:p-10 z-10">
            <div class="text-center mb-8">
                <img src="<?= base_url('assets/new/site-logo.png') ?>" alt="KEWASNET Logo" class="h-12 mx-auto mb-4">
                <h1 class="text-2xl font-bold text-slate-800">Create your account</h1>
                <p class="text-slate-600">Get started with KEWASNET today</p>
            </div>

            <!-- Status Message Container -->
            <div id="statusMessage" class="mb-4"></div>

            <!-- Email Sign Up Form -->
            <form id="signupForm" class="mb-6">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                <div class="space-y-4">
                    <div>
                        <label for="fullname" class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                        <input type="text" id="fullname" name="full_name"
                                placeholder="Enter your full name"
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div>
                        <label for="signup-email" class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
                        <input type="email" id="signup-email" name="email"
                                placeholder="Enter your email"
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div>
                        <label for="signup-password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                        <input type="password" id="signup-password" name="password" minlength="8"
                                placeholder="Enter your password"
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <p class="mt-1 text-xs text-slate-500">Password must be at least 8 characters</p>
                    </div>
                    <div class="flex items-center">
                        <input id="terms" name="terms" type="checkbox"
                                class="h-4 w-4 text-primary focus:ring-primary border-slate-300 rounded">
                        <label for="terms" class="ml-2 block text-sm text-slate-700">
                            I agree to the <a href="#" class="text-primary font-medium hover:text-primary-dark">Terms of Service</a> and <a href="#" class="text-primary font-medium hover:text-primary-dark">Privacy Policy</a>
                        </label>
                    </div>
                    <button type="submit" id="submitBtn" class="w-full gradient-btn hover:bg-primary-dark text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                        <span id="submitText">Create Account</span>
                        <i data-lucide="user-plus" class="w-5 h-5 ml-2 z-10"></i>
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="divider mb-6">or sign up with</div>

            <!-- Google Sign Up -->
            <?php if (!empty($googleClientId ?? '')): ?>
            <div class="mb-6">
                <div id="g_id_onload"
                    data-client_id="<?= esc($googleClientId) ?>"
                    data-context="signup"
                    data-ux_mode="popup"
                    data-callback="handleGoogleSignUp"
                    data-auto_prompt="false">
                </div>
                <div class="g_id_signin"
                    data-type="standard"
                    data-shape="rectangular"
                    data-theme="outline"
                    data-text="signup_with"
                    data-size="large"
                    data-logo_alignment="left">
                </div>
            </div>
            <?php endif; ?>

            <!-- Login Link -->
            <div class="text-center text-sm text-slate-600">
                Already have an account? <a href="<?= base_url('ksp/login') ?>" class="text-primary font-medium hover:text-primary-dark">Sign in</a>
            </div>
        </div>
    </div>

    <?= $this->section('scripts') ?>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Lucide icons
            lucide.createIcons();

            // Form submission handler
            $('#signupForm').on('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = $('#submitBtn');
                const submitText = $('#submitText');
                
                // Validate form
                if (!$('#terms').is(':checked')) {
                    showStatus('Please accept the terms and conditions', 'alert-circle', 'bg-red-100 text-red-800');
                    return;
                }

                // Disable button and show loading state
                submitBtn.prop('disabled', true);
                submitText.text('Creating account...');
                submitBtn.find('i').replaceWith('<i data-lucide="loader-circle" class="w-5 h-5 ml-2 animate-spin"></i>');
                lucide.createIcons();

                // Get form data
                const formData = {
                    full_name:  $('#fullname').val(),
                    email:      $('#signup-email').val(),
                    password:   $('#signup-password').val(),
                    terms:      $('#terms').is(':checked'),
                    <?= csrf_token() ?>: $('input[name="<?= csrf_token() ?>"]').val()
                };

                // AJAX request
                $.ajax({
                    url: '<?= base_url('ksp/signup') ?>',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            showStatus('Account created successfully! Redirecting...', 'check-circle', 'bg-green-100 text-green-800');
                            
                            // Redirect to login page after 2 seconds
                            setTimeout(() => {
                                window.location.href = '<?= base_url('ksp/login') ?>';
                            }, 3000);
                        } else {
                            showStatus(response.message || 'Account creation failed', 'alert-circle', 'bg-red-100 text-red-800');
                            submitBtn.prop('disabled', false);
                            submitText.text('Create Account');
                            submitBtn.find('i').replaceWith('<i data-lucide="user-plus" class="w-5 h-5 ml-2"></i>');
                            lucide.createIcons();
                        }
                    },
                    error: function(xhr) {
                        let message = 'An error occurred during registration';
                        
                        // Handle Errors
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errorMessage = 'Please fix the following errors:<ul>';
                            $.each(xhr.responseJSON.errors, function(key, error) {
                                errorMessage += `<li>${error}</li>`;
                            });
                            errorMessage += '</ul>';
                            showStatus(errorMessage, 'alert-circle', 'bg-red-100 text-red-800');
                        }

                        else if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                            showStatus(message, 'alert-circle', 'bg-red-100 text-red-800');
                        }

                        submitBtn.prop('disabled', false);
                        submitText.text('Create Account');
                        submitBtn.find('i').replaceWith('<i data-lucide="user-plus" class="w-5 h-5 ml-2"></i>');
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
                
                // Animate appearance
                gsap.from($('#statusMessage')[0], {
                    opacity: 0,
                    y: -10,
                    duration: 0.3
                });
            }

            // Google Sign-Up callback
            window.handleGoogleSignUp = function(response) {
                console.log('Google sign-up response', response);
                // Handle Google sign-up response here
                // You'll need to implement this based on your backend integration
            };
        });
    </script>
    <?= $this->endSection() ?>
<?= $this->include('frontendV2/ksp/layouts/constants/auth-footer') ?>