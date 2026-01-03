<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= esc($title) ?></title>
        <link rel="stylesheet" href="<?= base_url("assets/css/new.css") ?>">
        <link rel="stylesheet" href="<?= base_url('assets/css/styles.css') ?>">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
        <!-- Lucide Icons -->
        <script src="https://unpkg.com/lucide@latest"></script>

        <style>
            input:focus {
                outline-offset: 2px;
                outline: 2px solid #27aae0 !important;
            }
        </style>
    </head>
    <body class="bg-light min-h-screen flex items-center justify-center p-4 relative">
        <div class="radial-pattern-container">
            <div class="radial-pattern"></div>
        </div>

        <div class="w-full max-w-md z-10">
            <!-- Password Reset Card -->
            <div id="resetCard" class="bg-white rounded-xl shadow-lg overflow-hidden opacity-0 transform translate-y-10">
                <!-- Header Section -->
                <div id="cardHeader" class="bg-primary p-6 text-center">
                    <div class="flex justify-center mb-3">
                        <i data-lucide="key" class="w-8 h-8 text-white"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white">Reset Password</h1>
                    <p class="text-primaryShades-200 mt-1">Enter your email to receive reset instructions</p>

                    <!-- Flash Message for errors -->
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="mt-4">
                            <div class="bg-red-100 text-red-700 p-3 rounded">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Form Section -->
                <div id="formSection" class="p-6 sm:p-8">
                    <form id="resetForm" action="<?= base_url('auth/forgot-password') ?>" method="POST">
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                        <div class="space-y-5">
                            <!-- Email Input -->
                            <div id="emailField">
                                <label for="email" class="block text-sm font-medium text-dark mb-1">Email Address</label>
                                <div class="relative">
                                    <input 
                                        type="email" 
                                        id="email" 
                                        name="email"
                                        class="w-full px-4 py-2.5 rounded-lg border border-borderColor focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent transition pl-10"
                                        placeholder="your@email.com"
                                    >
                                    <i data-lucide="mail" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                                </div>
                            </div>
                            
                            <!-- Status Message (hidden by default) -->
                             <div id="statusMessage"></div>
                            
                            <!-- Submit Button -->
                            <button 
                                id="submitBtn"
                                type="submit"
                                class="w-full bg-secondary hover:bg-secondaryShades-600 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                            >
                                <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                                Send Reset Link
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Footer Section -->
                <div id="cardFooter" class="bg-lightGray px-6 py-4 text-center">
                    <p class="text-sm text-dark">
                        Remember your password? 
                        <a href="<?= base_url('auth/login') ?>" class="font-medium text-secondary hover:text-secondaryShades-600">Sign in here</a>
                    </p>
                </div>
            </div>
            
            <!-- Copyright Notice -->
            <p id="copyright" class="text-center text-slate-500 text-xs mt-6 opacity-0">
                © <?= date('Y') ?> KEWASNET. All rights reserved.
            </p>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Initialize Lucide icons
                lucide.createIcons();
                
                // Animations
                gsap.to("#resetCard", {
                    opacity: 1,
                    y: 0,
                    duration: 0.8,
                    ease: "back.out(1.2)"
                });

                gsap.from("#cardHeader div", {
                    scale: 0,
                    opacity: 0,
                    duration: 0.6,
                    delay: 0.3,
                    ease: "elastic.out(1, 0.5)"
                });

                gsap.from("#cardHeader h1", {
                    y: -10,
                    opacity: 0,
                    duration: 0.5,
                    delay: 0.5,
                    ease: "power2.out"
                });

                gsap.from("#cardHeader p", {
                    y: -10,
                    opacity: 0,
                    duration: 0.4,
                    delay: 0.7,
                    ease: "power2.out"
                });

                // Replace the problematic animation with this:
                gsap.fromTo("#emailField", 
                    { y: 20, opacity: 0 },
                    { y: 0, opacity: 1, duration: 0.5, delay: 0.9, ease: "power2.out" }
                );

                gsap.fromTo("#submitBtn", 
                    { y: 20, opacity: 1 }, // Start with opacity 1 (visible)
                    { y: 0, opacity: 1, duration: 0.5, delay: 1.05, ease: "power2.out" }
                );

                gsap.from("#cardFooter", {
                    y: 20,
                    opacity: 0,
                    duration: 0.6,
                    delay: 1.2,
                    ease: "power2.out"
                });

                gsap.to("#copyright", {
                    opacity: 1,
                    duration: 0.8,
                    delay: 1.5,
                    ease: "power2.out"
                });

                // Input focus animations
                document.querySelectorAll('input').forEach(input => {
                    input.addEventListener('focus', (e) => {
                        gsap.to(e.target, {
                            scale: 1.02,
                            duration: 0.2,
                            ease: "power1.out",
                            boxShadow: "0 0 0 3px rgba(39, 170, 224, 0.2)"
                        });
                    });

                    input.addEventListener('blur', (e) => {
                        gsap.to(e.target, {
                            scale: 1,
                            duration: 0.2,
                            ease: "power1.out",
                            boxShadow: "none"
                        });
                    });
                });
            });

            $(document).ready(function() {
                // Initialize Lucide icons
                lucide.createIcons();

                const loginForm = $('#resetForm');
                const statusMessage = $('#statusMessage');
                const submitButton = $('#submitBtn');
                const redirectUrl = '<?= base_url('auth/verify-reset-code') ?>';

                // Handle form submission
                loginForm.on('submit', function(e) {
                    e.preventDefault();
                    
                    // Validate email
                    const email = $('#email').val().trim();
                    if (!email || !email.includes('@')) {
                        showStatus('Please enter a valid email address', 'alert-circle', 'bg-red-100 text-red-800');
                        return;
                    }

                    // Disable button and show loading state
                    submitButton.prop('disabled', true);
                    submitButton.html('<i data-lucide="loader-circle" class="w-4 h-4 mr-2 animate-spin"></i> Sending...');
                    lucide.createIcons();

                    // Make AJAX request
                    $.ajax({
                        url: loginForm.attr('action'),
                        type: 'POST',
                        data: loginForm.serialize(), // This includes the CSRF token and email
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                showStatus(response.message || 'Password reset link sent to your email', 'check-circle', 'bg-green-100 text-green-800');
                                // Redirect to reset code verification page
                                setTimeout(() => {
                                    window.location.href = redirectUrl + '?email=' + encodeURIComponent(email);
                                }, 2000);
                            } else {
                                showStatus(response.message || 'Failed to send reset link', 'alert-circle', 'bg-red-100 text-red-800');
                            }
                        },
                        error: function(xhr) {
                            let message = 'An error occurred while sending the request';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            showStatus(message, 'alert-circle', 'bg-red-100 text-red-800');
                        },
                        complete: function() {
                            submitButton.prop('disabled', false);
                            submitButton.html('<i data-lucide="send" class="w-4 h-4 mr-2"></i> Send Reset Link');
                            lucide.createIcons();
                            // Reset form fields
                            $('#email').val('');
                        }
                    });
                });

                function showStatus(message, icon, classes) {

                    statusMessage.html(`
                            <div id="statusMessage" class="p-3 rounded-lg ${classes} text-sm flex items-start">
                                <i data-lucide="${icon}" class="w-4 h-4 mt-0.5 mr-2 flex-shrink-0"></i>
                                <span id="statusText">${message}</span>
                            </div>`);
                    
                    // Animate appearance
                    gsap.from(statusMessage[0], {
                        opacity: 0,
                        y: -10,
                        duration: 0.3
                    });
                }
            });
        </script>
    </body>
</html>