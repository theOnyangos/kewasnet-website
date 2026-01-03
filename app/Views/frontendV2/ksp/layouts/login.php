<?= $this->include('frontendV2/ksp/layouts/constants/auth-header') ?>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="auth-container w-full bg-white rounded-xl p-8 sm:p-10 shadow-md z-10">
            <div class="text-center mb-8">
                <img src="<?= base_url('assets/new/site-logo.png') ?>" alt="KEWASNET Logo" class="h-12 mx-auto mb-4">
                <h1 class="text-2xl font-bold text-slate-800">Welcome back</h1>
                <p class="text-slate-600">Sign in to access your account</p>
            </div>

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger mb-4">
                    <i data-lucide="alert-circle" class="w-8 h-8 mr-2"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success mb-4">
                    <i data-lucide="check-circle" class="w-8 h-8 mr-2"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('warning')): ?>
                <div class="alert alert-warning mb-4">
                    <i data-lucide="alert-triangle" class="w-8 h-8 mr-2"></i>
                    <?= session()->getFlashdata('warning') ?>
                </div>
            <?php endif; ?>

            <!-- Status Message (hidden by default) -->
            <div id="statusMessage" class="mb-5"></div>

            <!-- Email/Password Login Form -->
            <form id="emailLoginForm" class="mb-5" action="<?= base_url('ksp/login') ?>" method="POST">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
                        <input type="email" id="email" name="email" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Enter your email">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Enter your password">
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-slate-300 rounded">
                            <label for="remember-me" class="ml-2 block text-sm text-slate-700">Remember me</label>
                        </div>
                        <a href="<?= base_url('ksp/forget-password') ?>" class="text-sm text-primary font-medium hover:text-primary-dark">Forgot password?</a>
                    </div>
                    <button type="submit" class="flex items-center justify-center gap-2 w-full gradient-btn hover:bg-primary-dark text-white font-medium py-3 px-4 rounded-lg transition duration-200">
                        <span id="submitText">Sign in</span>
                        <i data-lucide="lock" class="w-5 h-5 ml-2 z-20"></i>
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="divider mb-6">or continue with</div>

            <!-- Google Sign In -->
            <?php if (!empty($googleClientId ?? '')): ?>
            <div class="mb-6">
                <div id="g_id_onload"
                    data-client_id="<?= esc($googleClientId) ?>"
                    data-context="signin"
                    data-ux_mode="popup"
                    data-callback="handleGoogleSignIn"
                    data-auto_prompt="false">
                </div>
                <div class="g_id_signin"
                    data-type="standard"
                    data-shape="rectangular"
                    data-theme="outline"
                    data-text="signin_with"
                    data-size="large"
                    data-logo_alignment="left">
                </div>
            </div>
            <?php endif; ?>

            <!-- Sign Up Link -->
            <div class="text-center text-sm text-slate-600">
                Don't have an account? <a href="<?= base_url('ksp/signup') ?>" class="text-primary font-medium hover:text-primary-dark">Sign up</a>
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

            const loginForm = $('#emailLoginForm');
            const statusMessage = $('#statusMessage');
            const submitButton = $('#emailLoginForm button[type="submit"]');
            const submitText = $('#submitText');

            // Handle form submission
            loginForm.on('submit', function(e) {
                e.preventDefault();
                
                // Validate inputs
                const email = $('#email').val().trim();
                const password = $('#password').val().trim();
                
                if (!email || !email.includes('@')) {
                    showStatus('Please enter a valid email address', 'alert-circle', 'bg-red-100 text-red-800');
                    return;
                }
                
                if (!password) {
                    showStatus('Please enter your password', 'alert-circle', 'bg-red-100 text-red-800');
                    return;
                }

                // Disable button and show loading state
                submitButton.prop('disabled', true);
                submitText.text('Signing in...');
                submitButton.find('i').replaceWith('<i data-lucide="loader-circle" class="w-5 h-5 ml-2 animate-spin"></i>');
                lucide.createIcons();

                // Make AJAX request
                const formAction = loginForm.attr('action');
                console.log('Login form action URL:', formAction);
                console.log('Form data:', loginForm.serialize());
                
                $.ajax({
                    url: formAction,
                    type: 'POST',
                    timeout: 10000, // 10 second timeout
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: loginForm.serialize(),
                    dataType: 'json',
                    beforeSend: function(xhr) {
                        console.log('Sending AJAX request to:', formAction);
                        console.log('Request headers:', {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/x-www-form-urlencoded'
                        });
                    },
                    success: function(response) {
                        console.log('Login response received:', response);
                        // Handle both JSON and HTML responses (in case of error)
                        if (typeof response === 'string') {
                            console.error('Received HTML instead of JSON:', response.substring(0, 200));
                            showStatus('Server returned an unexpected response. Please refresh the page and try again.', 'alert-circle', 'bg-red-100 text-red-800');
                            return;
                        }
                        
                        if (response.status === 'success' && response.status_code === 200) {
                            showStatus('Login successful! Redirecting...', 'check-circle', 'bg-green-100 text-green-800');
                            setTimeout(() => {
                                window.location.href = response.redirect || '/ksp';
                            }, 1500);
                        } else {
                            showStatus(response.message || 'Login failed. Please try again.', 'alert-circle', 'bg-red-100 text-red-800');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            error: error,
                            response: xhr.responseText,
                            readyState: xhr.readyState
                        });
                        
                        let message = 'Unable to connect to server. Please check your connection and try again.';
                        
                        // Check if it's a network error
                        if (xhr.status === 0) {
                            message = 'Unable to connect to server. Please check your internet connection and ensure the server is running on ' + window.location.origin;
                        } else if (xhr.status === 405) {
                            message = 'Invalid request method. Please refresh the page and try again.';
                        } else if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                // Handle validation errors
                                const errors = xhr.responseJSON.errors;
                                message = Object.values(errors)[0] || message;
                            }
                        } else if (xhr.status >= 500) {
                            message = 'Server error. Please try again later.';
                        } else if (xhr.status === 404) {
                            message = 'Login endpoint not found. Please refresh the page.';
                        } else if (status === 'timeout') {
                            message = 'Request timed out. Please check your connection.';
                        } else if (status === 'parsererror') {
                            message = 'Server response could not be parsed. Please try again.';
                        }
                        
                        showStatus(message, 'alert-circle', 'bg-red-100 text-red-800');
                    },
                    complete: function() {
                        submitButton.prop('disabled', false);
                        submitText.text('Sign in');
                        submitButton.find('i').replaceWith('<i data-lucide="lock" class="w-5 h-5 ml-2"></i>');
                        lucide.createIcons();
                    }
                });
            });

            function showStatus(message, icon, classes) {
                statusMessage.html(`
                    <div class="p-3 rounded-lg ${classes} text-sm flex items-start">
                        <i data-lucide="${icon}" class="w-4 h-4 mt-0.5 mr-2 flex-shrink-0"></i>
                        <span>${message}</span>
                    </div>`);
                
                // Animate appearance
                gsap.from(statusMessage[0], {
                    opacity: 0,
                    y: -10,
                    duration: 0.3
                });
            }

            // Google Sign-In callback (must be in global scope)
            window.handleGoogleSignIn = function(response) {
                console.log('Google sign-in response', response);
                
                // Show loading state
                showStatus('Authenticating with Google...', 'loader-circle', 'bg-blue-100 text-blue-800');
                
                // Send the credential to your server
                const credential = response.credential;
                
                $.post('<?= base_url('ksp/google-login') ?>', {
                    credential: credential,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                })
                .done(function(data) {
                    if (data.success && data.redirect) {
                        showStatus('Google authentication successful! Redirecting...', 'check-circle', 'bg-green-100 text-green-800');
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1000);
                    } else {
                        showStatus(data.message || 'Google sign-in failed. Please try again.', 'alert-circle', 'bg-red-100 text-red-800');
                    }
                })
                .fail(function() {
                    showStatus('An error occurred during Google sign-in. Please try again.', 'alert-circle', 'bg-red-100 text-red-800');
                });
            };
        });
    </script>
    <?= $this->endSection() ?>
<?= $this->include('frontendV2/ksp/layouts/constants/auth-footer') ?>