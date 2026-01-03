<?= $this->include('frontendV2/ksp/layouts/constants/auth-header') ?>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="auth-container w-full bg-white rounded-xl p-8 sm:p-10 shadow-md z-10">
            <div class="text-center mb-8">
                <img src="<?= base_url('assets/new/site-logo.png') ?>" alt="KEWASNET Logo" class="h-12 mx-auto mb-4">
                <h1 class="text-2xl font-bold text-slate-800">Create New Password</h1>
                <p class="text-slate-600">Create a strong, secure password</p>
            </div>

            <!-- Status Message -->
            <div id="statusMessage" class="mb-5"></div>

            <!-- Password Reset Form -->
            <form id="passwordResetForm" class="mb-5" action="<?= base_url('ksp/update-password') ?>" method="POST">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                
                <div class="space-y-4">
                    <!-- New Password -->
                    <div>
                        <label for="newPassword" class="block text-sm font-medium text-slate-700 mb-1">New Password</label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="newPassword" 
                                name="password" 
                                required
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent pr-10"
                                placeholder="Create new password"
                                oninput="checkPasswordStrength(this.value)"
                            >
                            <i data-lucide="eye" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-5 h-5 cursor-pointer" onclick="togglePasswordVisibility('newPassword', this)"></i>
                        </div>
                        <!-- Password Strength Meter -->
                        <div class="mt-2">
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div id="passwordStrengthBar" class="h-2 rounded-full" style="width: 0%"></div>
                            </div>
                            <p id="passwordStrengthText" class="text-xs mt-1 text-slate-500">Password strength: <span id="strengthText">Very Weak</span></p>
                        </div>
                        <ul id="passwordRequirements" class="text-xs text-slate-500 mt-2 list-disc list-inside">
                            <li id="reqLength" class="text-slate-400">At least 8 characters</li>
                            <li id="reqUpper" class="text-slate-400">Uppercase letter</li>
                            <li id="reqLower" class="text-slate-400">Lowercase letter</li>
                            <li id="reqNumber" class="text-slate-400">Number</li>
                            <li id="reqSpecial" class="text-slate-400">Special character</li>
                        </ul>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="confirmPassword" class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="confirmPassword" 
                                name="confirm_password" 
                                required
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent pr-10"
                                placeholder="Confirm new password"
                            >
                            <i data-lucide="eye" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-5 h-5 cursor-pointer" onclick="togglePasswordVisibility('confirmPassword', this)"></i>
                        </div>
                        <p id="passwordMatch" class="text-xs mt-1 text-red-500 hidden">Passwords don't match</p>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="w-full gradient-btn hover:bg-primary-dark text-white font-medium py-3 px-4 rounded-lg transition duration-200 mt-4 flex items-center justify-center"
                        disabled
                    >
                        <i data-lucide="lock" class="w-5 h-5 mr-2 z-10"></i>
                        <span id="submitText">Update Password</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?= $this->section('scripts') ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Lucide icons
            lucide.createIcons();

            // Check password match on confirm password field
            $('#confirmPassword').on('input', function() {
                checkPasswordMatch();
            });

            // Form submission
            $('#passwordResetForm').on('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = $('#submitBtn');
                const submitText = $('#submitText');
                
                // Validate passwords match
                if (!checkPasswordMatch()) {
                    return;
                }

                // Validate password strength (at least medium)
                const strength = calculatePasswordStrength($('#newPassword').val());
                if (strength.score < 3) {
                    showStatus('Please choose a stronger password', 'alert-circle', 'bg-red-100 text-red-800');
                    return;
                }

                // Disable button and show loading state
                submitBtn.prop('disabled', true);
                submitText.text('Updating...');
                submitBtn.find('i').replaceWith('<i data-lucide="loader-circle" class="w-5 h-5 mr-2 animate-spin"></i>');
                lucide.createIcons();

                // Prepare form data
                const formData = {
                    password: $('#newPassword').val(),
                    confirm_password: $('#confirmPassword').val(),
                    <?= csrf_token() ?>: $('input[name="<?= csrf_token() ?>"]').val()
                };

                // AJAX request
                $.ajax({
                    url: $('#passwordResetForm').attr('action'),
                    type: $('#passwordResetForm').attr('method'),
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            showStatus('Password updated successfully! Redirecting...', 'check-circle', 'bg-green-100 text-green-800');
                            setTimeout(() => {
                                window.location.href = response.redirect || '<?= base_url('ksp/login') ?>';
                            }, 2000);
                        } else {
                            showStatus(response.message || 'Failed to update password', 'alert-circle', 'bg-red-100 text-red-800');
                            submitBtn.prop('disabled', false);
                            submitText.text('Update Password');
                            submitBtn.find('i').replaceWith('<i data-lucide="lock" class="w-5 h-5 mr-2"></i>');
                            lucide.createIcons();
                        }
                    },
                    error: function(xhr) {
                        let message = 'An error occurred while updating password';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        showStatus(message, 'alert-circle', 'bg-red-100 text-red-800');
                        submitBtn.prop('disabled', false);
                        submitText.text('Update Password');
                        submitBtn.find('i').replaceWith('<i data-lucide="lock" class="w-5 h-5 mr-2"></i>');
                        lucide.createIcons();
                    }
                });
            });
        });

        // Toggle password visibility
        function togglePasswordVisibility(fieldId, icon) {
            const field = document.getElementById(fieldId);
            if (field.type === 'password') {
                field.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                field.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }

        // Check password strength
        function checkPasswordStrength(password) {
            const result = calculatePasswordStrength(password);
            const strengthBar = $('#passwordStrengthBar');
            const strengthText = $('#strengthText');
            const submitBtn = $('#submitBtn');
            
            // Update progress bar
            strengthBar.css('width', result.percentage + '%');
            
            // Update color and text based on strength
            switch(result.score) {
                case 0:
                case 1:
                    strengthBar.removeClass('bg-red-500 bg-yellow-500 bg-green-500 bg-emerald-500').addClass('bg-red-500');
                    strengthText.text('Very Weak');
                    break;
                case 2:
                    strengthBar.removeClass('bg-red-500 bg-yellow-500 bg-green-500 bg-emerald-500').addClass('bg-yellow-500');
                    strengthText.text('Weak');
                    break;
                case 3:
                    strengthBar.removeClass('bg-red-500 bg-yellow-500 bg-green-500 bg-emerald-500').addClass('bg-green-500');
                    strengthText.text('Good');
                    break;
                case 4:
                    strengthBar.removeClass('bg-red-500 bg-yellow-500 bg-green-500 bg-emerald-500').addClass('bg-emerald-500');
                    strengthText.text('Strong');
                    break;
            }
            
            // Update requirements list
            $('#reqLength').toggleClass('text-green-400 text-green-500', result.length);
            $('#reqUpper').toggleClass('text-green-400 text-green-500', result.upper);
            $('#reqLower').toggleClass('text-green-400 text-green-500', result.lower);
            $('#reqNumber').toggleClass('text-green-400 text-green-500', result.number);
            $('#reqSpecial').toggleClass('text-green-400 text-green-500', result.special);
            
            // Enable/disable submit button based on strength
            submitBtn.prop('disabled', result.score < 2 || !checkPasswordMatch());
        }

        // Calculate password strength
        function calculatePasswordStrength(password) {
            const strength = {
                score: 0,
                percentage: 0,
                length: false,
                upper: false,
                lower: false,
                number: false,
                special: false
            };
            
            // Length requirement (12+ chars)
            strength.length = password.length >= 12;
            if (strength.length) strength.score++;
            
            // Contains uppercase
            strength.upper = /[A-Z]/.test(password);
            if (strength.upper) strength.score++;
            
            // Contains lowercase
            strength.lower = /[a-z]/.test(password);
            if (strength.lower) strength.score++;
            
            // Contains number
            strength.number = /[0-9]/.test(password);
            if (strength.number) strength.score++;
            
            // Contains special char
            strength.special = /[^A-Za-z0-9]/.test(password);
            if (strength.special) strength.score++;
            
            // Calculate percentage
            strength.percentage = (strength.score / 4) * 100;
            
            return strength;
        }

        // Check if passwords match
        function checkPasswordMatch() {
            const password = $('#newPassword').val();
            const confirm = $('#confirmPassword').val();
            const matchElement = $('#passwordMatch');
            const submitBtn = $('#submitBtn');
            
            if (password && confirm && password !== confirm) {
                matchElement.removeClass('hidden');
                submitBtn.prop('disabled', true);
                return false;
            } else {
                matchElement.addClass('hidden');
                // Only enable if password is not too weak
                const strength = calculatePasswordStrength(password);
                submitBtn.prop('disabled', strength.score < 2);
                return true;
            }
        }

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
    </script>
    <?= $this->endSection() ?>
<?= $this->include('frontendV2/ksp/layouts/constants/auth-footer') ?>