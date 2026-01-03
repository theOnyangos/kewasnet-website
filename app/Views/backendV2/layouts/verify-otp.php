<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= esc($title) ?></title>
        <link rel="stylesheet" href="<?= base_url("assets/css/new.css") ?>">
        <link rel="stylesheet" href="<?= base_url('assets/css/styles.css') ?>">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

        <style>
            .otp-input {
                width: 3.5rem;
                height: 3.5rem;
                font-size: 1.5rem;
                text-align: center;
                margin: 0 0.25rem;
            }
            .otp-input:focus {
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

        <!-- Lucide Icons -->
        <script src="https://unpkg.com/lucide@latest"></script>
    </head>
    <body class="bg-light min-h-screen flex items-center justify-center p-4 relative">
        <div class="radial-pattern-container">
            <div class="radial-pattern"></div>
        </div>

        <div class="w-full max-w-md z-10">
            <!-- OTP Verification Card -->
            <div id="otpCard" class="bg-white rounded-xl shadow-lg overflow-hidden opacity-0 transform translate-y-10">
                <!-- Header Section -->
                <div id="cardHeader" class="bg-primary p-6 text-center">
                    <div class="flex justify-center mb-3">
                        <i data-lucide="shield-check" class="w-8 h-8 text-white"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white">Verify Your Identity</h1>
                    <p class="text-primaryShades-200 mt-1">Enter the 6-digit code sent to your email</p>
                </div>
                
                <!-- Form Section -->
                <div id="formSection" class="p-6 sm:p-8">
                    <form id="otpForm" method="POST" action="<?= base_url('auth/verify-otp') ?>">
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                        <div class="space-y-6">
                            <!-- OTP Input Fields -->
                            <div class="flex justify-center" id="otpContainer">
                                <input type="text" maxlength="1" class="otp-input rounded-lg border-2 border-borderColor focus:ring-2 focus:ring-secondary" data-index="0" autofocus>
                                <input type="text" maxlength="1" class="otp-input rounded-lg border-2 border-borderColor focus:ring-2 focus:ring-secondary" data-index="1">
                                <input type="text" maxlength="1" class="otp-input rounded-lg border-2 border-borderColor focus:ring-2 focus:ring-secondary" data-index="2">
                                <input type="text" maxlength="1" class="otp-input rounded-lg border-2 border-borderColor focus:ring-2 focus:ring-secondary" data-index="3">
                                <input type="text" maxlength="1" class="otp-input rounded-lg border-2 border-borderColor focus:ring-2 focus:ring-secondary" data-index="4">
                                <input type="text" maxlength="1" class="otp-input rounded-lg border-2 border-borderColor focus:ring-2 focus:ring-secondary" data-index="5">
                            </div>
                            
                            <!-- Hidden OTP Field for Form Submission -->
                            <input type="hidden" id="fullOtp" name="reset_code">
                            
                            <!-- Status Message -->
                            <div id="statusMessage" class="hidden"></div>
                            
                            <!-- Submit Button -->
                            <button 
                                id="verifyBtn"
                                type="submit"
                                class="cursor-pointer w-full bg-secondary hover:bg-secondaryShades-600 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                            >
                                <i data-lucide="shield" class="w-4 h-4 mr-2"></i>
                                Verify Code
                            </button>
                            
                            <!-- Resend Code Link -->
                            <div class="text-center text-sm text-dark">
                                Didn't receive code? 
                                <button type="button" id="resendBtn" class="font-medium text-secondary hover:text-secondaryShades-600 focus:outline-none">
                                    Resend OTP
                                </button>
                                <span id="countdown" class="text-slate-400 ml-1 hidden">(0:30)</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Initialize Lucide icons
                lucide.createIcons();
                
                // OTP Input Handling
                const otpInputs = document.querySelectorAll('.otp-input');
                const fullOtpField = document.getElementById('fullOtp');
                const otpForm = document.getElementById('otpForm');
                const resendBtn = document.getElementById('resendBtn');
                const countdownEl = document.getElementById('countdown');
                const statusMessage = document.getElementById('statusMessage');
                const verifyBtn = document.getElementById('verifyBtn');
                
                // Auto-focus and shift between OTP inputs
                otpInputs.forEach((input, index) => {
                    // Handle paste event
                    input.addEventListener('paste', (e) => {
                        e.preventDefault();
                        const pasteData = e.clipboardData.getData('text').trim();
                        if (/^\d{6}$/.test(pasteData)) {
                            pasteData.split('').forEach((char, i) => {
                                if (otpInputs[i]) {
                                    otpInputs[i].value = char;
                                }
                            });
                            updateFullOtp();
                            otpInputs[5].focus();
                        }
                    });
                    
                    // Handle keydown for navigation
                    input.addEventListener('keydown', (e) => {
                        if (e.key === 'Backspace' && input.value === '' && index > 0) {
                            otpInputs[index - 1].focus();
                        }
                    });
                    
                    // Handle input for auto-focus shift
                    input.addEventListener('input', (e) => {
                        if (input.value.length === 1 && index < otpInputs.length - 1) {
                            otpInputs[index + 1].focus();
                        }
                        updateFullOtp();
                    });
                });
                
                // Update the hidden full OTP field
                function updateFullOtp() {
                    const otp = Array.from(otpInputs).map(input => input.value).join('');
                    fullOtpField.value = otp;
                    
                    // Enable/disable verify button based on complete OTP
                    verifyBtn.disabled = otp.length !== 6;
                }
                
                // Form submission
                otpForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const otp = fullOtpField.value;

                    // Validate OTP
                    if (!/^\d{6}$/.test(otp)) {
                        showStatus('Please enter a complete 6-digit code', 'alert-circle', 'bg-red-100 text-red-800');
                        return;
                    }
                    
                    // Check if OTP length is exactly 6
                    if (otp.length !== 6) {
                        showStatus('Please enter a complete 6-digit code', 'alert-circle', 'bg-red-100 text-red-800');
                        return;
                    }
                    
                    // Show loading state
                    verifyBtn.disabled = true;
                    const originalContent = verifyBtn.innerHTML;
                    verifyBtn.innerHTML = '<i data-lucide="loader-circle" class="w-4 h-4 mr-2 animate-spin"></i> Verifying...';
                    lucide.createIcons();
                    
                    // AJAX call to verify OTP
                    $.ajax({
                        url: otpForm.action,
                        type: otpForm.method,
                        data: $(otpForm).serialize(),
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                showStatus(response.message || 'Verification successful! Redirecting...', 'check-circle', 'bg-green-100 text-green-800');

                                // Reset OTP inputs
                                otpInputs.forEach(input => input.value = '');
                                updateFullOtp();
                                
                                // Reset verify button
                                verifyBtn.disabled = false;
                                verifyBtn.innerHTML = originalContent;

                                // Redirect on success
                                if (response.redirect_url) {
                                    setTimeout(() => {
                                        window.location.href = response.redirect_url;
                                    }, 3000);
                                }
                            } else {
                                showStatus(response.message || 'Invalid verification code', 'alert-circle', 'bg-red-100 text-red-800');
                                verifyBtn.disabled = false;
                                verifyBtn.innerHTML = originalContent;
                                lucide.createIcons();
                            }
                        },
                        error: function(xhr) {
                            let message = 'An error occurred during verification';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            showStatus(message, 'alert-circle', 'bg-red-100 text-red-800');
                            verifyBtn.disabled = false;
                            verifyBtn.innerHTML = originalContent;
                            lucide.createIcons();
                        }
                    });
                });
                
                // Resend OTP functionality
                resendBtn.addEventListener('click', () => {
                    resendBtn.disabled = true;
                    countdownEl.classList.remove('hidden');
                    
                    // Start countdown
                    let timeLeft = 30;
                    countdownEl.textContent = `(${timeLeft})`;
                    
                    const timer = setInterval(() => {
                        timeLeft--;
                        countdownEl.textContent = `(${timeLeft})`;
                        
                        if (timeLeft <= 0) {
                            clearInterval(timer);
                            resendBtn.disabled = false;
                            countdownEl.classList.add('hidden');
                        }
                    }, 1000);
                    
                    // AJAX call to resend OTP
                    $.ajax({
                        url: '<?= base_url('auth/send-reset-code') ?>',
                        type: 'POST',
                        data: {
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showStatus(response.message || 'New verification code sent', 'mail-check', 'bg-blue-100 text-blue-800');
                            } else {
                                showStatus(response.message || 'Failed to resend code', 'alert-circle', 'bg-red-100 text-red-800');
                            }
                        },
                        error: function() {
                            showStatus('Error requesting new code', 'alert-circle', 'bg-red-100 text-red-800');
                        }
                    });
                });
                
                // Show status message
                function showStatus(message, icon, classes) {
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
                
                // Animations
                gsap.to("#otpCard", {
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

                gsap.from("#otpContainer", {
                    y: 20,
                    opacity: 0,
                    duration: 0.5,
                    delay: 0.9,
                    ease: "power2.out"
                });
            });
        </script>
    </body>
</html>