<!DOCTYPE html>
<html lang="en" class="h-full bg-bgBody">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title?></title>

        <!-- Main Styles -->
        <?= $this->include('backendV2/constants/header-styles') ?>
    </head>

    <body class="h-screen font-sans antialiased text-dark">
        <!-- Diagonal Grid Pattern -->
        <div class="events-page-pattern-bg"></div>

        <div class="flex h-full overflow-hidden">
            <!-- Collapsible Sidebar -->
            <?= $this->include('backendV2/layouts/sidebar') ?>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top Navigation -->
                <?= $this->include('backendV2/layouts/header') ?>

                <!-- Dashboard Content -->
                <?= $this->renderSection("content") ?>
            </div>
        </div>

        <!-- Modal -->
        <?= $this->include('partials/modal') ?>

        <!-- Notification Dropdown (Outside header stacking context) -->
        <?= $this->include('backendV2/partials/notifications_dropdown') ?>

        <!-- Main Scripts -->
        <?= $this->include('backendV2/constants/footer-scripts') ?>

        <!-- Render Other Javascript -->
        <?= $this->renderSection('scripts') ?>
    </body>
</html>