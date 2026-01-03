<?php $this->extend('backendV2/layouts/main'); ?>

<?= $this->section('title') ?>Settings - KEWASNET Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="flex-1 overflow-y-auto">
    <?= view('backendV2/partials/page_banner', [
        'pageTitle' => 'Application Settings',
        'pageDescription' => 'Configure system settings and preferences',
        'breadcrumbs' => [
            ['label' => 'Settings']
        ]
    ]) ?>

    <div class="px-6 pb-6">
    <div class="max-w-full mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <!-- Critical Warning Section -->
            <div class="bg-gradient-to-r from-red-50 to-orange-50 border-l-4 border-red-400 rounded-lg p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i data-lucide="alert-triangle" class="h-6 w-6 text-red-600"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-lg font-semibold text-red-800 mb-2">
                            ⚠️ Critical System Settings Warning
                        </h3>
                        <div class="text-red-700 space-y-2">
                            <p class="font-medium">
                                <strong>CAUTION:</strong> The settings in this section control core system functionality.
                            </p>
                            <ul class="list-disc list-inside space-y-1 text-sm">
                                <li>Incorrect configuration may cause the application to malfunction or become inaccessible</li>
                                <li>Payment gateway errors could affect financial transactions</li>
                                <li>Email/SMS misconfigurations may disrupt critical communications</li>
                                <li>Google API changes could break authentication features</li>
                            </ul>
                            <div class="mt-4 p-3 bg-red-100 rounded-md">
                                <p class="text-sm font-medium text-red-800">
                                    💡 <strong>Recommendation:</strong> Only modify these settings if you understand their impact. 
                                    Consider backing up current configurations before making changes.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="text-center py-12">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-primaryShades-100 mb-4">
                    <i data-lucide="settings" class="h-6 w-6 text-primary"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Settings Management</h3>
                <p class="text-gray-500 mb-6">Choose a settings panel to configure your system preferences.</p>
                <button onclick="confirmSettingsAccess()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-primaryShades-600 transition-colors">
                    <i data-lucide="shield-alert" class="w-4 h-4 mr-2"></i>
                    Proceed to Settings
                </button>
                <p class="text-xs text-gray-400 mt-3">
                    By proceeding, you acknowledge the risks associated with modifying system settings
                </p>
            </div>
        </div>
    </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function confirmSettingsAccess() {
        // Create a custom confirmation modal
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-[500px] shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100">
                        <i data-lucide="alert-triangle" class="h-10 w-10 text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-4">Confirm Settings Access</h3>
                    <div class="mt-4 px-7 py-3">
                        <p class="text-sm text-gray-600 mb-4">
                            You are about to access critical system settings that control core application functionality.
                        </p>
                        <div class="bg-red-50 border border-red-200 rounded-md p-3 mb-4">
                            <p class="text-xs text-red-800">
                                <strong>Warning:</strong> Incorrect modifications may cause system malfunctions or make the application inaccessible.
                            </p>
                        </div>
                        <p class="text-sm text-gray-700 font-medium">
                            Do you understand the risks and wish to proceed?
                        </p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button onclick="proceedToSettings()" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-[150px] mr-2 hover:bg-red-700 transition-colors">
                            Yes, Proceed
                        </button>
                        <button onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Initialize Lucide icons for the modal
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
        // Functions for modal actions
        window.proceedToSettings = function() {
            window.location.href = '<?= base_url('auth/settings/social-media') ?>';
        };
        
        window.closeModal = function() {
            document.body.removeChild(modal);
            delete window.proceedToSettings;
            delete window.closeModal;
        };
        
        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }
</script>
<?= $this->endSection() ?>
