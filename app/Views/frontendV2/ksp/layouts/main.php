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

        <!-- AI Chat Widget -->
        <?= view('components/ai-chat-widget') ?>

      <!--  Include Common Javascript -->
      <?= $this->include("frontendV2/constants/javascript") ?>

      <!-- AI Chat Widget JavaScript -->
      <script src="<?= base_url('assets/js/ai-chat-widget.js') ?>"></script>

      <!-- Render scripts -->
      <?= $this->renderSection("scripts") ?>

      <!-- Activity Tracking -->
      <?= view('tracking/tracking-snippet') ?>
    </body>
</html>
