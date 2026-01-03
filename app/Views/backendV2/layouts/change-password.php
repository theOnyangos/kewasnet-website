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
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <style>
            .password-toggle {
                right: 0.75rem !important;
                top: 50% !important;
                transform: translateY(-50%) !important;
            }

            input:focus {
                outline-offset: 2px;
                outline: 2px solid #27aae0 !important;
            }
            
            .status-message {
                padding: 0.75rem;
                border-radius: 0.5rem;
                margin-bottom: 1rem;
                display: flex;
                align-items: flex-start;
            }
            
            .status-message i {
                margin-right: 0.5rem;
                flex-shrink: 0;
            }
        </style>
    </head>
    <body class="bg-light min-h-screen flex items-center justify-center p-4 relative">
        <div class="radial-pattern-container">
            <div class="radial-pattern"></div>
        </div>

        <div class="w-full max-w-md z-10">
            <!-- Change Password Card -->
            <div id="passwordCard" class="bg-white rounded-xl shadow-lg overflow-hidden opacity-0 transform translate-y-10">
                <!-- Header Section -->
                <div id="cardHeader" class="bg-primary p-6 text-center">
                    <div class="flex justify-center mb-3">
                        <i data-lucide="lock" class="w-8 h-8 text-white"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white">Change Password</h1>
                    <p class="text-primaryShades-200 mt-1">Create a new secure password</p>
                </div>
                
                <!-- Form Section -->
                <div id="formSection" class="p-6 sm:p-8">
                    <form id="passwordForm" method="POST" action="<?= base_url('auth/change-password') ?>">
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                        <div class="space-y-5">
                            <!-- New Password -->
                            <div id="newPasswordField">
                                <label for="newPassword" class="block text-sm font-medium text-dark mb-1">New Password</label>
                                <div class="relative">
                                    <input 
                                        type="password" 
                                        id="newPassword" 
                                        name="new_password"
                                        class="w-full px-4 py-2.5 rounded-lg border border-borderColor focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent transition pr-10"
                                        placeholder="••••••••"
                                        required
                                        minlength="8"
                                    >
                                    <button type="button" class="password-toggle absolute text-slate-400 hover:text-secondary focus:outline-none">
                                        <i data-lucide="eye-off" class="w-5 h-5"></i>
                                    </button>
                                </div>
                                <div id="passwordStrength" class="hidden mt-2">
                                    <div class="flex items-center space-x-2">
                                        <div class="h-1.5 flex-1 bg-slate-200 rounded-full overflow-hidden">
                                            <div id="strengthBar" class="h-full bg-red-500 rounded-full" style="width: 0%"></div>
                                        </div>
                                        <span id="strengthText" class="text-xs font-medium text-slate-500">Weak</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Confirm Password -->
                            <div id="confirmPasswordField">
                                <label for="confirmPassword" class="block text-sm font-medium text-dark mb-1">Confirm New Password</label>
                                <div class="relative">
                                    <input 
                                        type="password" 
                                        id="confirmPassword" 
                                        name="confirm_password"
                                        class="w-full px-4 py-2.5 rounded-lg border border-borderColor focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent transition pr-10"
                                        placeholder="••••••••"
                                        required
                                        minlength="8"
                                    >
                                    <button type="button" class="password-toggle absolute text-slate-400 hover:text-secondary focus:outline-none">
                                        <i data-lucide="eye-off" class="w-5 h-5"></i>
                                    </button>
                                </div>
                                <div id="passwordMatch" class="hidden mt-1 text-xs font-medium"></div>
                            </div>
                            
                            <!-- Password Requirements -->
                            <div class="bg-light p-3 rounded-lg">
                                <p class="text-sm font-medium text-primaryShades-500 mb-2">Password Requirements:</p>
                                <ul class="text-xs text-dark space-y-1">
                                    <li id="reqLength" class="flex items-center">
                                        <i data-lucide="x" class="w-3 h-3 mr-1 text-red-500"></i>
                                        <span>Minimum 8 characters</span>
                                    </li>
                                    <li id="reqUppercase" class="flex items-center">
                                        <i data-lucide="x" class="w-3 h-3 mr-1 text-red-500"></i>
                                        <span>At least one uppercase letter</span>
                                    </li>
                                    <li id="reqLowercase" class="flex items-center">
                                        <i data-lucide="x" class="w-3 h-3 mr-1 text-red-500"></i>
                                        <span>At least one lowercase letter</span>
                                    </li>
                                    <li id="reqNumber" class="flex items-center">
                                        <i data-lucide="x" class="w-3 h-3 mr-1 text-red-500"></i>
                                        <span>At least one number</span>
                                    </li>
                                    <li id="reqSpecial" class="flex items-center">
                                        <i data-lucide="x" class="w-3 h-3 mr-1 text-red-500"></i>
                                        <span>At least one special character</span>
                                    </li>
                                </ul>
                            </div>
                            
                            <!-- Status Message -->
                            <div id="statusMessage" class="hidden"></div>
                            
                            <!-- Submit Button -->
                            <button 
                                id="submitBtn"
                                type="submit"
                                class="w-full bg-secondary hover:bg-secondaryShades-600 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                            >
                                <i data-lucide="key" class="w-4 h-4 mr-2"></i>
                                Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Initialize Lucide icons
                lucide.createIcons();
                
                // Password toggle functionality
                const setupPasswordToggles = () => {
                    document.querySelectorAll('.password-toggle').forEach(toggle => {
                        toggle.addEventListener('click', function() {
                            const input = this.previousElementSibling;
                            const icon = this.querySelector('i');
                            
                            if (input.type === 'password') {
                                input.type = 'text';
                                icon.setAttribute('data-lucide', 'eye');
                            } else {
                                input.type = 'password';
                                icon.setAttribute('data-lucide', 'eye-off');
                            }
                            
                            lucide.createIcons();
                            
                            // Add animation
                            gsap.fromTo(icon, 
                                { scale: 0.8 }, 
                                { scale: 1, duration: 0.2 }
                            );
                        });
                    });
                };
                
                // Password strength checker
                const checkPasswordStrength = (password) => {
                    let strength = 0;
                    const requirements = {
                        length: password.length >= 8,
                        uppercase: /[A-Z]/.test(password),
                        lowercase: /[a-z]/.test(password),
                        number: /[0-9]/.test(password),
                        special: /[^A-Za-z0-9]/.test(password)
                    };
                    
                    // Count total strength first
                    Object.values(requirements).forEach(isValid => {
                        if (isValid) strength++;
                    });
                    
                    // Update requirement indicators - FIXED mapping
                    const requirementMapping = {
                        'reqLength': 'length',
                        'reqUppercase': 'uppercase', 
                        'reqLowercase': 'lowercase',
                        'reqNumber': 'number',
                        'reqSpecial': 'special'
                    };
                    
                    Object.entries(requirementMapping).forEach(([elementId, reqKey]) => {
                        const element = document.getElementById(elementId);
                        if (element) {
                            const icon = element.querySelector('i');
                            if (icon) {
                                const isValid = requirements[reqKey];
                                icon.setAttribute('data-lucide', isValid ? 'check' : 'x');
                                icon.className = `w-3 h-3 mr-1 ${isValid ? 'text-green-500' : 'text-red-500'}`;
                            }
                        }
                    });
                    
                    // Reinitialize icons after updating requirement indicators
                    lucide.createIcons();
                    
                    // Update strength meter if elements exist
                    const strengthBar = document.getElementById('strengthBar');
                    const strengthText = document.getElementById('strengthText');
                    const strengthContainer = document.getElementById('passwordStrength');
                    
                    if (strengthBar && strengthText && strengthContainer) {
                        strengthContainer.classList.remove('hidden');
                        
                        let width = 0;
                        let color = 'bg-red-500';
                        let text = 'Weak';
                        
                        // Calculate strength based on requirements met (out of 5 total)
                        if (strength === 5) {
                            width = 100;
                            color = 'bg-green-500';
                            text = 'Very Strong';
                        } else if (strength >= 4) {
                            width = 80;
                            color = 'bg-green-500';
                            text = 'Strong';
                        } else if (strength >= 3) {
                            width = 60;
                            color = 'bg-yellow-500';
                            text = 'Moderate';
                        } else if (strength >= 2) {
                            width = 40;
                            color = 'bg-orange-500';
                            text = 'Fair';
                        } else {
                            width = 20;
                            color = 'bg-red-500';
                            text = 'Weak';
                        }
                        
                        strengthBar.className = `h-full ${color} rounded-full`;
                        gsap.to(strengthBar, { width: `${width}%`, duration: 0.3 });
                        strengthText.textContent = text;
                        
                        // Update text color to match bar color
                        const textColorClass = color === 'bg-green-500' ? 'text-green-500' : 
                                             color === 'bg-yellow-500' ? 'text-yellow-500' : 
                                             color === 'bg-orange-500' ? 'text-orange-500' : 'text-red-500';
                        strengthText.className = `text-xs font-medium ${textColorClass}`;
                    }
                    
                    return strength >= 4; // Consider strong if 4+ requirements met
                };
                
                // Password match checker
                const checkPasswordMatch = () => {
                    const newPassword = document.getElementById('newPassword').value;
                    const confirmPassword = document.getElementById('confirmPassword').value;
                    const matchElement = document.getElementById('passwordMatch');
                    
                    if (!matchElement) return;
                    
                    if (newPassword && confirmPassword) {
                        matchElement.classList.remove('hidden');
                        
                        if (newPassword === confirmPassword) {
                            matchElement.innerHTML = '<i data-lucide="check" class="w-3 h-3 mr-1 text-green-500 inline"></i> Passwords match';
                            matchElement.className = 'mt-1 text-xs font-medium text-green-500';
                        } else {
                            matchElement.innerHTML = '<i data-lucide="x" class="w-3 h-3 mr-1 text-red-500 inline"></i> Passwords do not match';
                            matchElement.className = 'mt-1 text-xs font-medium text-red-500';
                        }
                        lucide.createIcons();
                    } else {
                        matchElement.classList.add('hidden');
                    }
                };
                
                // Form submission with AJAX
                const passwordForm = document.getElementById('passwordForm');
                if (passwordForm) {
                    passwordForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        const newPassword = document.getElementById('newPassword').value;
                        const confirmPassword = document.getElementById('confirmPassword').value;
                        const submitBtn = document.getElementById('submitBtn');
                        
                        // Validate form
                        if (!newPassword || !confirmPassword) {
                            showStatus('Please fill in all fields', 'alert-circle', 'bg-red-100 text-red-800');
                            return;
                        }
                        
                        if (newPassword !== confirmPassword) {
                            showStatus('Passwords do not match', 'alert-circle', 'bg-red-100 text-red-800');
                            return;
                        }
                        
                        if (!checkPasswordStrength(newPassword)) {
                            showStatus('Password does not meet requirements', 'alert-circle', 'bg-red-100 text-red-800');
                            return;
                        }
                        
                        // Show loading state
                        submitBtn.disabled = true;
                        const originalContent = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<i data-lucide="loader-circle" class="w-4 h-4 mr-2 animate-spin"></i> Updating...';
                        lucide.createIcons();
                        
                        // AJAX call to change password
                        $.ajax({
                            url: passwordForm.action,
                            type: 'POST',
                            data: $(passwordForm).serialize(),
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    showStatus(response.message || 'Password changed successfully! Redirecting...', 'check-circle', 'bg-green-100 text-green-800');
                                    
                                    // Clear form and redirect
                                    setTimeout(() => {
                                        passwordForm.reset();
                                        if (response.redirect_url) {
                                            window.location.href = response.redirect_url;
                                        }
                                    }, 3000);
                                } else {
                                    showStatus(response.message || 'Failed to change password', 'alert-circle', 'bg-red-100 text-red-800');
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = originalContent;
                                    lucide.createIcons();
                                }
                            },
                            error: function(xhr) {
                                let message = 'An error occurred while changing password';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                showStatus(message, 'alert-circle', 'bg-red-100 text-red-800');
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalContent;
                                lucide.createIcons();
                            }
                        });
                    });
                }
                
                // Event listeners for real-time validation
                document.getElementById('newPassword')?.addEventListener('input', function() {
                    checkPasswordStrength(this.value);
                    checkPasswordMatch();
                });
                
                document.getElementById('confirmPassword')?.addEventListener('input', checkPasswordMatch);
                
                // Show status message
                function showStatus(message, icon, classes) {
                    const statusMessage = document.getElementById('statusMessage');
                    
                    if (!statusMessage) return;
                    
                    statusMessage.innerHTML = `
                        <i data-lucide="${icon}" class="w-4 h-4 mt-0.5 mr-2 flex-shrink-0"></i>
                        <span>${message}</span>
                    `;
                    statusMessage.className = `status-message ${classes}`;
                    statusMessage.classList.remove('hidden');
                    lucide.createIcons();
                    
                    gsap.from(statusMessage, {
                        opacity: 0,
                        y: -10,
                        duration: 0.3
                    });
                }
                
                // Initialize password toggles
                setupPasswordToggles();
                
                // Animations
                gsap.to("#passwordCard", {
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

                gsap.from(["#newPasswordField", "#confirmPasswordField"], {
                    y: 20,
                    opacity: 0,
                    duration: 0.5,
                    stagger: 0.15,
                    delay: 0.9,
                    ease: "power2.out"
                });
            });
        </script>
    </body>
</html>