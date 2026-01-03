<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/ksp/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <main class="flex-1 overflow-y-auto">
        <!-- Coming Soon Hero Section -->
        <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 flex flex-col justify-center items-center p-6">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Animated Icon -->
                <div class="relative mb-8">
                    <div class="w-32 h-32 mx-auto bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-2xl animate-pulse">
                        <i data-lucide="graduation-cap" class="w-16 h-16 text-white"></i>
                    </div>
                    <div class="absolute -top-4 -right-4 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center animate-bounce">
                        <i data-lucide="sparkles" class="w-4 h-4 text-yellow-800"></i>
                    </div>
                </div>

                <!-- Main Heading -->
                <h1 class="text-5xl md:text-6xl font-bold text-gray-800 mb-4">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Coming Soon
                    </span>
                </h1>
                
                <!-- Subtitle -->
                <h2 class="text-2xl md:text-3xl font-semibold text-gray-700 mb-6">
                    KEWASNET Learning Management System
                </h2>
                
                <!-- Description -->
                <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto leading-relaxed">
                    We're crafting an exceptional learning experience for water management professionals. 
                    Our comprehensive course platform will feature interactive lessons, expert-led training, 
                    and practical skills development in water resource management.
                </p>

                <!-- Features Preview -->
                <div class="grid md:grid-cols-3 gap-6 mb-12 max-w-4xl mx-auto">
                    <div class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4 mx-auto">
                            <i data-lucide="play-circle" class="w-6 h-6 text-blue-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Interactive Video Lessons</h3>
                        <p class="text-gray-600 text-sm">Engaging multimedia content with expert instructors and real-world applications</p>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4 mx-auto">
                            <i data-lucide="award" class="w-6 h-6 text-green-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Professional Certification</h3>
                        <p class="text-gray-600 text-sm">Earn recognized certificates in water management and environmental sustainability</p>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4 mx-auto">
                            <i data-lucide="users" class="w-6 h-6 text-purple-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Expert Community</h3>
                        <p class="text-gray-600 text-sm">Connect with industry professionals and participate in collaborative learning</p>
                    </div>
                </div>

                <!-- Development Status -->
                <div class="bg-white rounded-2xl p-8 shadow-xl max-w-2xl mx-auto mb-8">
                    <div class="flex items-center justify-center mb-4">
                        <i data-lucide="code" class="w-8 h-8 text-blue-600 mr-3"></i>
                        <h3 class="text-2xl font-bold text-gray-800">Development Progress</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 font-medium">Course Structure Design</span>
                            <span class="text-green-600 font-semibold">✓ Completed</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 font-medium">Video Platform Integration</span>
                            <span class="text-blue-600 font-semibold">🔄 In Progress</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: 75%"></div>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 font-medium">Assessment System</span>
                            <span class="text-yellow-600 font-semibold">⏳ Planning</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-500 h-2 rounded-full" style="width: 45%"></div>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 font-medium">Mobile App Development</span>
                            <span class="text-gray-500 font-semibold">📋 Planned</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gray-400 h-2 rounded-full" style="width: 10%"></div>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <p class="text-blue-800 text-center">
                            <strong>Estimated Launch:</strong> Q1 2026 | 
                            <strong>Current Phase:</strong> Backend Development
                        </p>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="grid md:grid-cols-3 gap-6 max-w-3xl mx-auto">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="mail" class="w-6 h-6 text-blue-600"></i>
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-1">Email Updates</h4>
                        <p class="text-gray-600 text-sm">courses@kewasnet.org</p>
                    </div>

                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="calendar" class="w-6 h-6 text-green-600"></i>
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-1">Development Timeline</h4>
                        <p class="text-gray-600 text-sm">4-6 Months</p>
                    </div>

                    <div class="text-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="bell" class="w-6 h-6 text-purple-600"></i>
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-1">Stay Updated</h4>
                        <p class="text-gray-600 text-sm">Follow our progress</p>
                    </div>
                </div>

                <!-- Back to Dashboard Button -->
                <div class="mt-12">
                    <a href="<?= base_url('auth/dashboard') ?>" class="inline-flex items-center px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </main>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Animate progress bars on page load
    document.addEventListener('DOMContentLoaded', function() {
        const progressBars = document.querySelectorAll('.bg-green-500, .bg-blue-500, .bg-yellow-500, .bg-gray-400');
        
        progressBars.forEach((bar, index) => {
            setTimeout(() => {
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.transition = 'width 1.5s ease-in-out';
                    bar.style.width = bar.getAttribute('style').match(/width:\s*(\d+%)/)[1];
                }, 100);
            }, index * 200);
        });

        // Add floating animation to feature cards
        const featureCards = document.querySelectorAll('.transform.hover\\:-translate-y-2');
        featureCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease-out';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100);
            }, index * 200);
        });
    });

    // Email notification signup functionality
    function handleEmailSignup() {
        const emailInput = document.querySelector('input[type="email"]');
        const submitButton = document.querySelector('button');
        const email = emailInput.value.trim();

        if (!email) {
            alert('Please enter a valid email address');
            return;
        }

        if (!isValidEmail(email)) {
            alert('Please enter a valid email format');
            return;
        }

        // Simulate signup (you can replace this with actual API call)
        submitButton.textContent = 'Submitting...';
        submitButton.disabled = true;

        setTimeout(() => {
            submitButton.textContent = '✓ Subscribed!';
            submitButton.style.backgroundColor = '#10B981';
            emailInput.value = '';
            
            setTimeout(() => {
                submitButton.textContent = 'Notify Me';
                submitButton.disabled = false;
                submitButton.style.backgroundColor = '';
            }, 3000);
        }, 1000);
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Add click event to notify button
    document.addEventListener('DOMContentLoaded', function() {
        const notifyButton = document.querySelector('button');
        if (notifyButton) {
            notifyButton.addEventListener('click', handleEmailSignup);
        }

        // Add Enter key support for email input
        const emailInput = document.querySelector('input[type="email"]');
        if (emailInput) {
            emailInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    handleEmailSignup();
                }
            });
        }
    });

    // Add sparkle animation
    function createSparkle() {
        const sparkle = document.createElement('div');
        sparkle.innerHTML = '✨';
        sparkle.style.position = 'absolute';
        sparkle.style.fontSize = '20px';
        sparkle.style.pointerEvents = 'none';
        sparkle.style.zIndex = '1000';
        
        const x = Math.random() * window.innerWidth;
        const y = Math.random() * window.innerHeight;
        
        sparkle.style.left = x + 'px';
        sparkle.style.top = y + 'px';
        
        document.body.appendChild(sparkle);
        
        // Animate and remove
        sparkle.animate([
            { opacity: 0, transform: 'scale(0)' },
            { opacity: 1, transform: 'scale(1)' },
            { opacity: 0, transform: 'scale(0)' }
        ], {
            duration: 2000,
            easing: 'ease-in-out'
        }).onfinish = () => {
            document.body.removeChild(sparkle);
        };
    }

    // Create sparkles periodically
    setInterval(createSparkle, 3000);
</script>
<?= $this->endSection() ?>