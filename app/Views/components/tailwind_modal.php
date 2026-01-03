<div id="<?= $modalId ?>" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="<?= $modalId ?>-title" aria-modal="true" role="dialog">
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal panel -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full <?= $size ?? 'sm:max-w-lg' ?> mx-auto">
            <!-- Modal header with close button -->
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 relative">
                <!-- Close button (top-right corner) -->
                <button type="button" 
                        onclick="closeModal('<?= $modalId ?>')" 
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <div class="text-center sm:text-left">
                    <?php if (!empty($icon)): ?>
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full <?= $iconBg ?? 'bg-blue-100' ?> sm:mx-0 sm:h-10 sm:w-10">
                        <?= $icon ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mt-3">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 text-center" id="<?= $modalId ?>-title">
                            <?= $title ?>
                        </h3>
                        <div class="mt-2">
                            <?= $content ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal footer with action buttons -->
            <?php if (!empty($footer)): ?>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse justify-center">
                <?= $footer ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Scripts -->
<?= $this->section('scripts') ?>
    <script>
        // Modal handling functions
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                modal.setAttribute('aria-hidden', 'false');
                
                // Focus on first interactive element for accessibility
                const focusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                if (focusable) focusable.focus();
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                modal.setAttribute('aria-hidden', 'true');
            }
            <?= $onClose ?? '' ?>
        }

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[id^="modal-"]').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeModal(modal.id);
                    }
                });
            });
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal:not(.hidden)');
                if (openModal) {
                    closeModal(openModal.id);
                }
            }
        });

        <?php if ($autoShow ?? false): ?>
            document.addEventListener('DOMContentLoaded', function() {
                openModal('<?= $modalId ?>');
            });
        <?php endif; ?>
    </script>
<?= $this->endSection() ?>
