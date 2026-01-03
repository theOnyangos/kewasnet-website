<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= esc($title) ?></title>
        <link rel="stylesheet" href="<?= base_url("assets/css/new.css") ?>">
        <link rel="stylesheet" href="<?= base_url("assets/css/styles.css") ?>">

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
            <!-- Login Card -->
            <div id="loginCard" class="bg-white rounded-xl shadow-lg overflow-hidden opacity-0 transform translate-y-10">
                <!-- Header Section -->
                <div id="cardHeader" class="bg-primary p-6 text-center">
                    <div class="flex justify-center mb-3">
                        <i data-lucide="lock" class="w-8 h-8 text-white"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white">Admin Portal</h1>
                    <p class="text-primaryShades-200 mt-1">Sign in to your account</p>
                </div>
                
                <!-- Form Section -->
                <div id="formSection" class="p-6 sm:p-8">
                    <form id="loginForm" action="<?= base_url('auth/login') ?>" method="POST" class="space-y-6">
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                        
                        <div class="space-y-5">
                            <!-- Email Input -->
                            <div id="emailField">
                                <label for="email" class="block text-sm font-medium text-dark mb-1">Email</label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email"
                                    class="w-full px-4 py-2.5 rounded-lg border border-borderColor focus:ring-2 focus:ring-secondary transition"
                                    placeholder="your@email.com"
                                    required
                                >
                            </div>
                            
                            <!-- Password Input -->
                            <div id="passwordField">
                                <div class="flex justify-between items-center mb-1">
                                    <label for="password" class="block text-sm font-medium text-dark">Password</label>
                                    <a href="<?= base_url('auth/forgot-password') ?>" class="text-xs text-secondary hover:text-secondaryShades-700">Forgot password?</a>
                                </div>
                                <div class="relative">
                                    <input 
                                        type="password" 
                                        id="password" 
                                        name="password"
                                        class="w-full px-4 py-2.5 rounded-lg border border-borderColor focus:ring-2 focus:ring-secondary transition pr-10"
                                        placeholder="••••••••"
                                        required
                                    >
                                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-secondary">
                                        <i id="icon" data-lucide="eye-off" class="w-5 h-5 text-lightGray"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Remember Me Checkbox -->
                            <div id="rememberField" class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    id="remember"
                                    name="remember"
                                    class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor rounded"
                                >
                                <label for="remember" class="ml-2 block text-sm text-dark">Remember me</label>
                            </div>
                            
                            <!-- Submit Button -->
                            <button 
                                id="submitBtn"
                                type="submit"
                                class="w-full bg-secondary hover:bg-secondaryShades-600 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200"
                            >
                                Sign In
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Footer Section -->
                <div id="cardFooter" class="bg-lightGray px-6 py-4 text-center">
                    <p class="text-sm text-dark">
                        Don't have an account? 
                        <a href="mailto:info@kewasnet.co.ke" class="font-medium text-secondary hover:text-secondaryShades-600">Contact support</a>
                    </p>
                </div>
            </div>
            
            <!-- Copyright Notice -->
            <p id="copyright" class="text-center text-slate-500 text-xs mt-6 opacity-0">
                © <?= date('Y') ?> KEWASNET. All rights reserved.
            </p>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
        <!-- Lucide Icons -->
        <script src="https://unpkg.com/lucide@latest"></script>
        <script>
            lucide.createIcons();

            document.addEventListener('DOMContentLoaded', () => {
                // Initialize Lucide icons
                lucide.createIcons();
                
                // Password toggle functionality
                const setupPasswordToggle = () => {
                    const passwordInput = document.getElementById('password');
                    const togglePassword = document.getElementById('togglePassword');
                    
                    if (!passwordInput || !togglePassword) {
                        console.error('Password input or toggle button not found');
                        return;
                    }
                    
                    const eyeIcon = document.getElementById('icon');
                    
                    togglePassword.addEventListener('click', function() {
                        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordInput.setAttribute('type', type);
                        
                        // Toggle between eye and eye-off icons
                        const currentIcon = eyeIcon.getAttribute('data-lucide');
                        console.log(currentIcon)
                        const newIcon = currentIcon === 'eye' ? 'eye-off' : 'eye';
                        
                        // Remove current icon
                        eyeIcon.removeAttribute('data-lucide');
                        
                        // Add slight animation to the icon
                        gsap.to(eyeIcon, {
                            scale: 0.8,
                            duration: 0.1,
                            onComplete: () => {
                                // Change icon
                                eyeIcon.setAttribute('data-lucide', newIcon);
                                lucide.createIcons();
                                
                                // Complete animation
                                gsap.to(eyeIcon, {
                                    scale: 1,
                                    duration: 0.1
                                });
                            }
                        });
                    });
                };

                // Call the password toggle setup
                setupPasswordToggle();

                // Main card animation
                gsap.to("#loginCard", {
                    opacity: 1,
                    y: 0,
                    duration: 0.8,
                    ease: "back.out(1.2)"
                });

                // Header text animation
                gsap.from("#cardHeader h1", {
                    y: -20,
                    opacity: 0,
                    duration: 0.6,
                    delay: 0.3,
                    ease: "power2.out"
                });

                gsap.from("#cardHeader p", {
                    y: -10,
                    opacity: 0,
                    duration: 0.5,
                    delay: 0.5,
                    ease: "power2.out"
                });

                // Form fields animation (staggered)
                gsap.from(["#emailField", "#passwordField", "#rememberField"], {
                    y: 20,
                    opacity: 0,
                    duration: 0.5,
                    stagger: 0.15,
                    delay: 0.7,
                    ease: "power2.out"
                });

                gsap.fromTo("#submitBtn", 
                    { y: 20, opacity: 1 }, // Start with opacity 1 (visible)
                    { y: 0, opacity: 1, duration: 0.5, delay: 1.05, ease: "power2.out" }
                );

                // Footer animation
                gsap.from("#cardFooter", {
                    y: 20,
                    opacity: 0,
                    duration: 0.6,
                    delay: 1.2,
                    ease: "power2.out"
                });

                // Copyright animation
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

                // Button hover animation
                const submitBtn = document.getElementById('submitBtn');
                if (submitBtn) {
                    submitBtn.addEventListener('mouseenter', () => {
                        gsap.to(submitBtn, {
                            scale: 1.03,
                            duration: 0.2,
                            ease: "power1.out"
                        });
                    });

                    submitBtn.addEventListener('mouseleave', () => {
                        gsap.to(submitBtn, {
                            scale: 1,
                            duration: 0.2,
                            ease: "power1.out"
                        });
                    });
                }
            });

            // Form submission with proper AJAX handling
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            
            if (loginForm) {
                loginForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    
                    // Disable submit button during request
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <span class="inline-block animate-spin rounded-full h-5 w-5 border-b-2 border-white"></span>
                        Processing...
                    `;
                    
                    try {
                        const formData = new FormData(loginForm);
                        formData.append('remember', document.getElementById('remember').checked);
                        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                        
                        const response = await fetch(loginForm.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        
                        if (data.status === 'error') {
                            throw new Error(data.message || 'Login failed');
                        }

                        if (data.status === 'error' && (data.code === 400 || data.code === 403)) {
                            // Show validation errors
                            showFormErrors(data.errors || {});
                            updateCsrfToken(data.token);
                            return;
                        }
                        
                        if (data.status === 'success') {
                            console.log(data)
                            // Redirect on success
                            showToast(data.message || 'Login successful', 'success');
                            updateCsrfToken(data.token);
                            window.location.href = 'dashboard';
                            return;
                        }
                        
                    } catch (error) {
                        // console.error('Login error:', error);
                        showToast(error, 'error');
                    } finally {
                        // Re-enable submit button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Sign In';
                    }
                });
            }
            
            function showFormErrors(errors) {
                // Clear previous errors
                document.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });
                document.querySelectorAll('.invalid-feedback').forEach(el => {
                    el.remove();
                });
                
                // Show new errors
                for (const [field, message] of Object.entries(errors)) {
                    const input = document.getElementById(field) || 
                                document.querySelector(`[name="${field}"]`);
                    const formGroup = input?.closest('div');
                    
                    if (input && formGroup) {
                        input.classList.add('is-invalid');
                        
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback text-red-500 text-xs mt-1';
                        errorDiv.textContent = message;
                        
                        formGroup.appendChild(errorDiv);
                        
                        // Animate error appearance
                        gsap.from(errorDiv, {
                            opacity: 0,
                            y: -10,
                            duration: 0.3
                        });
                    }
                }
            }
            
            function updateCsrfToken(token) {
                const csrfInput = document.querySelector('input[name="<?= csrf_token() ?>"]');
                if (csrfInput) {
                    csrfInput.value = token;
                }
            }
            
            function showToast(message, type = 'success') {
                // Implement your toast notification system
                // This is just a basic example
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-lg text-white ${
                    type === 'success' ? 'bg-green-500' : 'bg-red-500'
                }`;
                toast.textContent = message;
                document.body.appendChild(toast);
                
                gsap.from(toast, {
                    x: 100,
                    opacity: 0,
                    duration: 0.3
                });
                
                setTimeout(() => {
                    gsap.to(toast, {
                        opacity: 0,
                        y: -20,
                        duration: 0.3,
                        onComplete: () => toast.remove()
                    });
                }, 3000);
            }
            
            // Password toggle functionality
            const setupPasswordToggle = () => {
                const passwordInput = document.getElementById('password');
                const togglePassword = document.getElementById('togglePassword');
                
                if (!passwordInput || !togglePassword) return;
                
                const eyeIcon = document.getElementById('icon');
                
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    const newIcon = eyeIcon.getAttribute('data-lucide') === 'eye' ? 'eye-off' : 'eye';
                    eyeIcon.setAttribute('data-lucide', newIcon);
                    lucide.createIcons();
                });
            };
            
            setupPasswordToggle();
        </script>
    </body>
</html>