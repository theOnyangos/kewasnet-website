<!-- Modal HTML (unchanged from previous) -->
<div id="modal-container" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" aria-modal="true" role="dialog">
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 !bg-black !opacity-50"></div>
        </div>

        <!-- Modal panel -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full sm:max-w-lg mx-auto">
            <!-- Modal header with close button -->
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 relative">
                <!-- Close button (top-right corner) -->
                <button type="button" 
                        onclick="closeModal()" 
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <div class="text-center sm:text-left">
                    <div id="modal-icon-container" class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <span id="modal-icon"></span>
                    </div>
                    
                    <div class="mt-3">
                        <h3 class="place-self-start mb-5 text-xl leading-6 font-bold text-primaryShades-700 text-center" id="modal-title">
                            Modal Title
                        </h3>
                        <div class="mt-2" id="modal-content">
                            Modal content goes here
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal footer with action buttons -->
            <div id="modal-footer" class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse justify-end gap-3 hidden">
                <!-- Footer buttons will be inserted here -->
            </div>
        </div>
    </div>
</div>

<!-- Example Trigger Buttons in your page content -->
<!-- <div class="p-8 space-y-4">
    <button onclick="showSimpleModal()" class="px-4 py-2 bg-blue-500 text-white rounded">
        Open Simple Modal
    </button>

    <button onclick="showSuccessModal()" class="px-4 py-2 bg-green-500 text-white rounded">
        Open Success Modal
    </button>

    <button onclick="showConfirmModal()" class="px-4 py-2 bg-red-500 text-white rounded">
        Open Confirmation Modal
    </button>
</div> -->

<script>
    // Core Modal Functions
    function openModal(options = {}) {
        const modal = document.getElementById('modal-container');
        const iconContainer = document.getElementById('modal-icon-container');
        const footer = document.getElementById('modal-footer');
        
        // Set title
        if (options.title) {
            document.getElementById('modal-title').textContent = options.title;
        }
        
        // Set content
        if (options.content) {
            if (typeof options.content === 'string') {
                document.getElementById('modal-content').innerHTML = options.content;
            } else {
                document.getElementById('modal-content').innerHTML = '';
                document.getElementById('modal-content').appendChild(options.content);
            }
        }
        
        // Set icon if provided
        if (options.icon) {
            iconContainer.classList.remove('hidden');
            document.getElementById('modal-icon').innerHTML = options.icon;
            if (options.iconBg) {
                iconContainer.className = iconContainer.className.replace(/bg-\S+/g, '');
                iconContainer.classList.add(options.iconBg);
            }
        } else {
            iconContainer.classList.add('hidden');
        }
        
        // Set footer buttons if provided
        if (options.footer) {
            footer.classList.remove('hidden');
            footer.innerHTML = '';
            if (typeof options.footer === 'string') {
                footer.innerHTML = options.footer;
            } else {
                footer.appendChild(options.footer);
            }
        } else {
            footer.classList.add('hidden');
        }

        // Call onOpen callback if provided
        if (typeof options.onOpen === 'function') {
            options.onOpen();
        }

        // Store the onClose callback
        if (typeof options.onClose === 'function') {
            modal.dataset.onClose = 'true';
            modal._onClose = options.onClose;
        } else {
            delete modal.dataset.onClose;
            delete modal._onClose;
        }
        
        // Show the modal
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    
    function closeModal() {
        const modal = document.getElementById('modal-container');
        
        // Execute onClose callback if it exists
        if (modal.dataset.onClose === 'true' && typeof modal._onClose === 'function') {
            modal._onClose();
        }
        
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        
        // Clean up
        delete modal.dataset.onClose;
        delete modal._onClose;
    }
    
    // Close modal when clicking on the background
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('modal-container');
        if (event.target === modal) {
            closeModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        const modal = document.getElementById('modal-container');
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
    
    function closeModal() {
        const modal = document.getElementById('modal-container');
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    
    // Close modal when clicking on the background
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('modal-container');
        if (event.target === modal) {
            closeModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        const modal = document.getElementById('modal-container');
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
</script>