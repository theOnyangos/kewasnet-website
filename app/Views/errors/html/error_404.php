<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Page Not Found | 404 </title>
        <link rel="stylesheet" href="<?= base_url('assets/css/styles.css') ?>">
        <link rel="stylesheet" href="<?= base_url('assets/css/new.css') ?>">
        <script src="https://unpkg.com/lucide@latest"></script>
        <style>
            .error-container {
                background: linear-gradient(135deg, #f4f4f4 0%, #e4e5e4 100%) !important;
            }
        </style>
    </head>
    <body class="min-h-screen relative">
        <div class="radial-pattern-container">
            <div class="radial-pattern"></div>
        </div>

        <div class="error-container min-h-screen flex flex-col items-center justify-center p-6">
            <div class="w-full max-w-2xl text-center bg-white rounded-lg shadow-lg p-8 z-10">
                <div class="flex justify-center mb-8">
                    <i data-lucide="alert-triangle" class="w-20 h-20 text-red-500"></i>
                </div>
                <h1 class="text-4xl font-bold text-dark mb-4">404 - Page Not Found</h1>
                <p class="text-xl text-slate-600 mb-8">
                    The page you are looking for doesn't exist or has been moved.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="<?= base_url() ?>" class="bg-primary hover:bg-primaryShades-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                        <i data-lucide="home" class="w-5 h-5 mr-2"></i>
                        Go to Homepage
                    </a>
                    <a href="javascript:history.back()" class="border border-primary text-primary hover:bg-primaryShades-50 font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                        <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
                        Go Back
                    </a>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Initialize Lucide icons
                lucide.createIcons();
                
                // Simple animation for the error icon
                gsap.from("i[data-lucide='alert-triangle']", {
                    scale: 0.5,
                    opacity: 0,
                    duration: 0.8,
                    ease: "back.out(1.7)"
                });
            });
        </script>
    </body>
</html>