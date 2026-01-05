<?= $this->extend('backendV2/layouts/main') ?>

<?= $this->section('title'); ?><?= $title ?><?= $this->endSection(); ?>

<?= $this->section('content') ?>
<main class="flex-1 overflow-y-auto">
    <?= view('backendV2/partials/page_banner', [
        'pageTitle' => isset($ticketType) ? 'Edit Ticket Type' : 'Create Ticket Type',
        'pageDescription' => isset($ticketType) ? 'Update ticket type details' : 'Fill in the details below to create a new ticket type',
        'breadcrumbs' => [
            ['label' => 'Events', 'url' => base_url('auth/events')],
            ['label' => 'Ticket Types', 'url' => base_url('auth/events/ticket-types')],
            ['label' => isset($ticketType) ? 'Edit' : 'Create']
        ],
        'bannerActions' => '<a href="' . base_url('auth/events/ticket-types') . '" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Ticket Types
        </a>'
    ]) ?>

    <div class="px-6 pb-6">
        <!-- Navigation Tabs -->
        <?= view('backendV2/pages/events/partials/navigation_section') ?>
        
        <div class="bg-white rounded-b-xl shadow-sm p-6">
            <form id="ticketTypeForm" method="POST" action="<?= isset($ticketType) ? site_url('auth/events/update-ticket-type/' . $ticketType['id']) : site_url('auth/events/store-ticket-type') ?>">
                <?= csrf_field() ?>
                <?php if (isset($ticketType)): ?>
                    <input type="hidden" name="id" value="<?= esc($ticketType['id']) ?>">
                <?php endif; ?>
                
                <div class="space-y-6">
                    <!-- Basic Information Section -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-medium text-dark mb-4">Basic Information</h3>

                        <div>
                            <label for="event_id" class="block text-sm font-medium text-dark mb-1">Event <span class="text-red-500">*</span></label>
                            <select id="event_id" name="event_id" class="select2 w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" required>
                                <option value="">Select an event</option>
                                <?php if (!empty($events)): ?>
                                    <?php foreach ($events as $event): ?>
                                        <option value="<?= esc($event['id']) ?>" <?= (isset($ticketType) && $ticketType['event_id'] === $event['id']) ? 'selected' : '' ?>>
                                            <?= esc($event['title']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Select the event for this ticket type</p>
                        </div>

                        <div class="mt-4">
                            <label for="name" class="block text-sm font-medium text-dark mb-1">Ticket Name <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="<?= isset($ticketType) ? esc($ticketType['name']) : '' ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="e.g., General Admission, VIP, Early Bird" required>
                            <p class="mt-1 text-xs text-gray-500">A descriptive name for this ticket type</p>
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block text-sm font-medium text-dark mb-1">Description</label>
                            <textarea id="description" name="description" rows="3" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent resize-none" placeholder="Describe what's included with this ticket type"><?= isset($ticketType) ? esc($ticketType['description'] ?? '') : '' ?></textarea>
                            <p class="mt-1 text-xs text-gray-500">Optional description of what this ticket includes</p>
                        </div>
                    </div>

                    <!-- Pricing and Availability Section -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-medium text-dark mb-4">Pricing and Availability</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="price" class="block text-sm font-medium text-dark mb-1">Price (KES) <span class="text-red-500">*</span></label>
                                <input type="number" id="price" name="price" step="0.01" min="0" value="<?= isset($ticketType) ? esc($ticketType['price'] ?? '0') : '' ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="0.00" required>
                                <p class="mt-1 text-xs text-gray-500">Price per ticket in Kenyan Shillings</p>
                            </div>

                            <div>
                                <label for="quantity" class="block text-sm font-medium text-dark mb-1">Quantity</label>
                                <input type="number" id="quantity" name="quantity" min="1" value="<?= isset($ticketType) ? esc($ticketType['quantity'] ?? '') : '' ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Leave empty for unlimited">
                                <p class="mt-1 text-xs text-gray-500">Maximum number of tickets available. Leave empty for unlimited</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Period Section -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-medium text-dark mb-4">Sales Period</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="sales_start_date" class="block text-sm font-medium text-dark mb-1">Sales Start Date</label>
                                <input type="datetime-local" id="sales_start_date" name="sales_start_date" value="<?= isset($ticketType) && !empty($ticketType['sales_start_date']) ? date('Y-m-d\TH:i', strtotime($ticketType['sales_start_date'])) : '' ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                                <p class="mt-1 text-xs text-gray-500">When ticket sales begin (optional)</p>
                            </div>

                            <div>
                                <label for="sales_end_date" class="block text-sm font-medium text-dark mb-1">Sales End Date</label>
                                <input type="datetime-local" id="sales_end_date" name="sales_end_date" value="<?= isset($ticketType) && !empty($ticketType['sales_end_date']) ? date('Y-m-d\TH:i', strtotime($ticketType['sales_end_date'])) : '' ?>" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                                <p class="mt-1 text-xs text-gray-500">When ticket sales end (optional)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Status Section -->
                    <div class="pt-6">
                        <h3 class="text-lg font-medium text-dark mb-4">Status</h3>

                        <div>
                            <label class="block text-sm font-medium text-dark mb-3">Ticket Type Status <span class="text-red-500">*</span></label>
                            <div class="flex flex-col space-y-3">
                                <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                    <input type="radio" name="status" value="active" <?= (!isset($ticketType) || $ticketType['status'] === 'active') ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-dark">Active</span>
                                        <p class="text-xs text-gray-500">Tickets are available for purchase</p>
                                    </div>
                                </label>
                                <label class="inline-flex items-center p-3 border border-borderColor rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-secondary has-[:checked]:bg-secondaryShades-100">
                                    <input type="radio" name="status" value="inactive" <?= (isset($ticketType) && $ticketType['status'] === 'inactive') ? 'checked' : '' ?> class="h-4 w-4 text-secondary focus:ring-secondary border-borderColor">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-dark">Inactive</span>
                                        <p class="text-xs text-gray-500">Tickets are not available for purchase</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-borderColor">
                        <a href="<?= base_url('auth/events/ticket-types') ?>" class="px-8 py-3 rounded-[50px] text-primary bg-primaryShades-50 hover:bg-primaryShades-100">
                            Cancel
                        </a>
                        <button type="submit" class="gradient-btn rounded-[50px] px-8 text-white flex justify-center items-center gap-2">
                            <i data-lucide="<?= isset($ticketType) ? 'save' : 'plus' ?>" class="w-5 h-5 z-10"></i>
                            <span><?= isset($ticketType) ? 'Update Ticket Type' : 'Create Ticket Type' ?></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    lucide.createIcons();

    // Form submission handler
    $('#ticketTypeForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const url = $(this).attr('action');
        const isEdit = <?= isset($ticketType) ? 'true' : 'false' ?>;
        const actionText = isEdit ? 'updating' : 'creating';

        // Show loading alert
        Swal.fire({
            title: `${isEdit ? 'Updating' : 'Creating'} Ticket Type...`,
            text: 'Please wait',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message || `Ticket type ${isEdit ? 'updated' : 'created'} successfully`,
                        icon: 'success',
                        confirmButtonColor: '#3b82f6',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '<?= base_url('auth/events/ticket-types') ?>';
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || `Failed to ${actionText} ticket type`,
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                    if (response.errors) {
                        showFormErrors(response.errors);
                    }
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                Swal.fire({
                    title: 'Error!',
                    text: response.message || 'An error occurred while processing your request',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
                if (response.errors) {
                    showFormErrors(response.errors);
                }
            }
        });
    });

    // Function to show form errors
    function showFormErrors(errors) {
        clearFormErrors();
        $.each(errors, function(fieldName, message) {
            const $field = $(`[name="${fieldName}"]`);
            if ($field.length) {
                $field.addClass('border-red-500');
                const $errorDiv = $('<div>', {
                    class: 'text-red-500 text-xs mt-1',
                    text: message
                });
                $field.closest('div').append($errorDiv);
            }
        });
    }

    // Function to clear form errors
    function clearFormErrors() {
        $('.border-red-500').removeClass('border-red-500');
        $('.text-red-500.text-xs').remove();
    }

    // Clear field errors on input
    $('input, textarea, select').on('input change', function() {
        $(this).removeClass('border-red-500');
        $(this).closest('div').find('.text-red-500.text-xs').remove();
    });
});
</script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?= $this->endSection() ?>

