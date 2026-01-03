<!DOCTYPE html>
  <html lang="en">
    <head>
        <!-- Meta Tags -->
        <?= $this->include("frontendV2/constants/meta-tags") ?>

        <!-- Styles -->
        <?= $this->include("frontendV2/constants/header-styles") ?>
        
        <!-- Add these before your existing scripts -->
        <?= $this->include("frontendV2/constants/header-scripts") ?>
    </head>
    <body>
        <!--  HEADER -->
        <?= $this->include("frontendV2/website/layouts/header") ?>
        
        <!--  HOME SLIDER BLOCK  -->
        <?= $this->renderSection("content") ?>

        <!-- FOOTER  -->
        <?= $this->include("frontendV2/website/layouts/footer") ?>

        <!-- Floating Action Buttons -->
        <?= $this->include("frontendV2/constants/floating-action-button") ?>

        <!-- Welcome Water Drop Sound -->
        <?= $this->include("frontendV2/constants/welcome-sound") ?>

        <!-- Cookie Consent Modal -->
        <?= $this->include("frontendV2/constants/cookie-consent-modal") ?>

        <!-- Modal -->
        <?= $this->include('partials/modal') ?>

        <!--  Include Common Javascript -->
        <?= $this->include("frontendV2/constants/javascript") ?>

      <script>
        // Play welcome sound on first interaction
        document.addEventListener('DOMContentLoaded', () => {
            const welcomeSound = document.getElementById('welcome-sound');
            
            // Modern browsers require user interaction first
            function playWelcomeSound() {
                // Create a one-time click handler
                const handleFirstInteraction = () => {
                    // Play sound (with volume reduced for better UX)
                    welcomeSound.volume = 0.3;
                    welcomeSound.play().catch(e => console.log("Sound playback prevented:", e));
                    
                    // Remove this listener after first interaction
                    document.removeEventListener('click', handleFirstInteraction);
                    document.removeEventListener('touchstart', handleFirstInteraction);
                };
                
                // Wait for any user interaction
                document.addEventListener('click', handleFirstInteraction, { once: true });
                document.addEventListener('touchstart', handleFirstInteraction, { once: true });
                
                // Fallback: Play after 10 seconds if no interaction
                setTimeout(() => {
                    welcomeSound.play().catch(e => console.log("Autoplay blocked"));
                }, 10000);
            }
            
            // Start when all page assets are loaded
            window.addEventListener('load', playWelcomeSound);
        });
      </script>

      <!-- Render scripts -->
      <?= $this->renderSection("scripts") ?>

    </body>
</html>
