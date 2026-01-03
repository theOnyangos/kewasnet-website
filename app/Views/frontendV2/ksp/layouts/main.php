<!DOCTYPE html>
  <html lang="en" class="h-full">
    <head>
        <!-- Meta Tags -->
        <?= $this->include("frontendV2/constants/meta-tags") ?>

        <!-- Styles -->
        <?= $this->include("frontendV2/constants/header-styles") ?>
        
        <!-- Add these before your existing scripts -->
        <?= $this->include("frontendV2/constants/header-scripts") ?>
    </head>
    <body class="h-full flex flex-col">
        <!--  HEADER -->
        <?= $this->include("frontendV2/ksp/layouts/header") ?>
        
        <!--  HOME SLIDER BLOCK  -->
        <main class="flex-1">
            <?= $this->renderSection("content") ?>
        </main>

        <!-- FOOTER  -->
        <?= $this->include("frontendV2/ksp/layouts/footer") ?>

        <!-- Floating Action Buttons -->
        <?= $this->include("frontendV2/constants/floating-action-button") ?>

      <!--  Include Common Javascript -->
      <?= $this->include("frontendV2/constants/javascript") ?>

      <!-- Render scripts -->
      <?= $this->renderSection("scripts") ?>
    </body>
</html>
