<?= $this->include('frontendV2/ksp/layouts/constants/auth-header') ?>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="auth-container w-full bg-white rounded-xl p-8 sm:p-10 shadow-md z-10">
            <div class="text-center mb-8">
                <img src="<?= base_url('assets/new/site-logo.png') ?>" alt="KEWASNET Logo" class="h-12 mx-auto mb-4">
                <h1 class="text-2xl font-bold text-slate-800">Verify Your Account</h1>
                <p class="text-slate-600">Enter the 6-digit code sent to your email</p>
            </div>

            <!-- Status Message -->
            <div id="statusMessage" class="mb-5"></div>

            <!-- OTP Verification Form -->
            <form id="otpForm" class="mb-5">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                <input type="hidden" name="email" id="formEmail" value="">
                
                <div class="flex justify-center space-x-3 mb-6">
                    <?php for($i = 1; $i <= 6; $i++): ?>
                        <input 
                            type="text" 
                            maxlength="1" 
                            id="otp<?= $i ?>" 
                            name="otp[]" 
                            data-index="<?= $i ?>"
                            class="w-12 h-12 text-2xl text-center border-2 border-slate-300 rounded-lg focus:border-primary focus:ring-2 focus:ring-primary focus:outline-none transition-all"
                            oninput="moveToNext(this)"
                            onkeydown="handleBackspace(this, event)"
                        >
                    <?php endfor; ?>
                </div>

                <div class="text-center text-sm text-slate-600 mb-6">
                    Didn't receive code? <a href="#" id="resendCode" class="text-primary font-medium hover:text-primary-dark">Resend Code</a>
                </div>

                <div class="flex space-x-4">
                    <button 
                        type="button" 
                        onclick="window.location.href='<?= base_url('ksp/signup') ?>'"
                        class="w-full bg-white text-primary border border-primary hover:bg-slate-50 font-medium py-3 px-4 rounded-lg transition duration-200"
                    >
                        Back to Sign Up
                    </button>
                    <button 
                        type="submit" 
                        id="verifyBtn"
                        class="w-full flex justify-center items-center gap-2 gradient-btn hover:bg-primary-dark text-white font-medium py-3 px-4 rounded-lg transition duration-200"
                    >
                        <span>Verify Code</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?= $this->section('scripts') ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Verify OTP URL
            const verifyOtpUrl = '<?= base_url('ksp/verify-reset-code') ?>';

            // Auto-focus first OTP input on page load
            $('#otp1').focus();
            
            // Get email from URL if available
            const urlParams = new URLSearchParams(window.location.search);
            const email = urlParams.get('email');
            if(email) {
                $('#formEmail').val(email);
            }

            // Handle OTP form submission
            $('#otpForm').on('submit', function(e) {
                e.preventDefault();
                
                const verifyBtn = $('#verifyBtn');
                verifyBtn.prop('disabled', true);
                verifyBtn.html('<span><i data-lucide="loader-circle" class="w-4 h-4 mr-2 animate-spin z-10"></i> Verifying...</span>');
                
                // Collect OTP digits
                let otp = '';
                $('[id^="otp"]').each(function() {
                    otp += $(this).val();
                });
                
                // Validate OTP length
                if(otp.length !== 6) {
                    showStatus('Please enter a complete 6-digit code', 'alert-circle', 'bg-red-100 text-red-800');
                    verifyBtn.prop('disabled', false);
                    verifyBtn.text('Verify Code');
                    return;
                }
                
                // Prepare data for AJAX
                const formData = {
                    email: $('#formEmail').val(),
                    otp: otp,
                    <?= csrf_token() ?>: $('input[name="<?= csrf_token() ?>"]').val()
                };
                
                // Send to server
                $.ajax({
                    url: verifyOtpUrl,
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if(response.status === 'success') {
                            showStatus(response.message || 'Verification successful!', 'check-circle', 'bg-green-100 text-green-800');

                            setTimeout(() => {
                                window.location.href = response.redirect_url || '<?= base_url('ksp/change-password') ?>';
                            }, 1500);

                            // Clear OTP fields
                            $('[id^="otp"]').val('');
                        } else if(response.status === 'error' && response.code === 400) {
                            let errorMessage = 'Please fix the following errors:<ul>';

                            $.each(response.errors, function(key, error) {
                                errorMessage += `<li>${error}</li>`;
                            });

                            errorMessage += '</ul>';
                            showStatus(errorMessage, 'alert-circle', 'bg-red-100 text-red-800');
                        } else {
                            showStatus(response.message || 'Verification failed', 'alert-circle', 'bg-red-100 text-red-800');
                            verifyBtn.prop('disabled', false);
                            verifyBtn.text('Verify Code');
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
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
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
            
            // Handle resend code
            $('#resendCode').on('click', function(e) {
                e.preventDefault();
                const email = $('#formEmail').val();
                
                if(!email) {
                    showStatus('Email address is required', 'alert-circle', 'bg-red-100 text-red-800');
                    return;
                }
                
                $('#resendCode').text('Sending...');
                
                $.post('<?= base_url('ksp/send-reset-code') ?>', {
                    email: email,
                    <?= csrf_token() ?>: $('input[name="<?= csrf_token() ?>"]').val()
                })
                .done(function(response) {
                    if(response.status === 'success') {
                        showStatus('New code sent successfully!', 'check-circle', 'bg-green-100 text-green-800');

                        // Clear OTP fields
                        $('[id^="otp"]').val('');
                    } else {
                        showStatus(response.message || 'Failed to resend code', 'alert-circle', 'bg-red-100 text-red-800');
                    }
                    $('#resendCode').text('Resend Code');
                })
                .fail(function() {
                    showStatus('Error resending code', 'alert-circle', 'bg-red-100 text-red-800');
                    $('#resendCode').text('Resend Code');
                });
            });
        });
        
        // Move to next input when digit is entered
        function moveToNext(input) {
            const maxLength = parseInt(input.maxLength);
            const currentLength = input.value.length;
            const index = parseInt(input.dataset.index);
            
            if(currentLength === maxLength) {
                if(index < 6) {
                    $(`#otp${index + 1}`).focus();
                }
            }
            
            // Auto-submit if last digit entered
            if(index === 6 && currentLength === maxLength) {
                $('#otpForm').submit();
            }
        }
        
        // Handle backspace to move to previous input
        function handleBackspace(input, event) {
            const index = parseInt(input.dataset.index);
            
            if(event.key === 'Backspace' && input.value.length === 0 && index > 1) {
                $(`#otp${index - 1}`).focus();
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