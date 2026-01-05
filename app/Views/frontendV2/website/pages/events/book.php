<?php 
    use App\Helpers\UrlHelper;
    use App\Libraries\ClientAuth;
    
    $currentUrl = new UrlHelper();
    $userId = ClientAuth::getId();
    $user = ClientAuth::user();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/website/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <!-- Booking Page -->
    <article class="py-16 bg-white">
        <div class="container mx-auto px-4 max-w-6xl">
            <!-- Breadcrumbs -->
            <nav class="mb-8" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-sm">
                    <li>
                        <a href="<?= base_url() ?>" class="text-slate-600 hover:text-primary transition-colors">Home</a>
                    </li>
                    <li>
                        <i data-lucide="chevron-right" class="icon-xs text-slate-400"></i>
                    </li>
                    <li>
                        <a href="<?= base_url('events') ?>" class="text-slate-600 hover:text-primary transition-colors">Events</a>
                    </li>
                    <li>
                        <i data-lucide="chevron-right" class="icon-xs text-slate-400"></i>
                    </li>
                    <li>
                        <a href="<?= base_url('events/' . esc($event['slug'])) ?>" class="text-slate-600 hover:text-primary transition-colors"><?= esc($event['title']) ?></a>
                    </li>
                    <li>
                        <i data-lucide="chevron-right" class="icon-xs text-slate-400"></i>
                    </li>
                    <li class="text-primary" aria-current="page">
                        <span>Book Tickets</span>
                    </li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Event Summary Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-secondary/10 rounded-xl p-6 sticky top-24 border border-secondary/50">
                        <h3 class="text-xl font-bold text-slate-800 mb-6">Event Summary</h3>
                        
                        <?php if (!empty($event['image_url'])): ?>
                            <div class="mb-6 rounded-lg overflow-hidden">
                                <img src="<?= $event['image_url'] ?>" 
                                     alt="<?= esc($event['title']) ?>" 
                                     class="w-full h-[280px] object-cover"
                                     onerror="this.src='<?= base_url('hero.png') ?>'">
                            </div>
                        <?php endif; ?>
                        
                        <h4 class="text-lg font-semibold text-slate-800 mb-4"><?= esc($event['title']) ?></h4>
                        
                        <div class="space-y-3 text-sm text-slate-600 mb-6">
                            <div class="flex items-center">
                                <i data-lucide="calendar" class="icon-sm mr-2 text-primary"></i>
                                <span><?= date('F j, Y', strtotime($event['start_date'])) ?></span>
                            </div>
                            <?php if (!empty($event['start_time'])): ?>
                                <div class="flex items-center">
                                    <i data-lucide="clock" class="icon-sm mr-2 text-primary"></i>
                                    <span><?= date('g:i A', strtotime($event['start_time'])) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($event['venue'])): ?>
                                <div class="flex items-center">
                                    <i data-lucide="map-pin" class="icon-sm mr-2 text-primary"></i>
                                    <span><?= esc($event['venue']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="pt-6 border-t border-slate-200">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-slate-600">Event Type:</span>
                                <span class="font-semibold text-slate-800"><?= esc(ucfirst($event['event_type'])) ?></span>
                            </div>
                            <?php if (!empty($event['total_capacity'])): ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600">Capacity:</span>
                                    <span class="font-semibold text-slate-800"><?= number_format($event['total_capacity']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Booking Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white border border-slate-200 rounded-xl p-8">
                        <h2 class="text-2xl font-bold text-secondary mb-6">Book Your Tickets</h2>
                        
                        <form id="bookingForm" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="event_id" value="<?= esc($event['id']) ?>">
                            
                            <!-- Ticket Selection -->
                            <?php if (!empty($event['ticket_types']) && count($event['ticket_types']) > 0): ?>
                                <div class="mb-8">
                                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Select Tickets</h3>
                                    <div class="space-y-4">
                                        <?php foreach ($event['ticket_types'] as $ticketType): ?>
                                            <div class="border border-slate-200 rounded-lg p-4 hover:border-secondary transition-colors">
                                                <div class="flex justify-between items-start mb-3">
                                                    <div class="flex-1">
                                                        <h4 class="font-semibold text-slate-800 mb-1"><?= esc($ticketType['name']) ?></h4>
                                                        <?php if (!empty($ticketType['description'])): ?>
                                                            <p class="text-sm text-slate-600 mb-2"><?= esc($ticketType['description']) ?></p>
                                                        <?php endif; ?>
                                                        <div class="flex items-center space-x-4 text-sm">
                                                            <span class="text-slate-600">
                                                                Price: <span class="font-semibold text-slate-800">KES <?= number_format($ticketType['price'], 2) ?></span>
                                                            </span>
                                                            <?php if (!empty($ticketType['quantity'])): ?>
                                                                <span class="text-slate-600">
                                                                    Available: <span class="font-semibold text-slate-800"><?= number_format($ticketType['quantity']) ?></span>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="text-green-600 font-semibold">Unlimited</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="flex items-center space-x-2">
                                                            <button type="button" class="ticket-decrease w-8 h-8 rounded-full border border-slate-300 flex items-center justify-center hover:bg-slate-50" 
                                                                    data-ticket-type="<?= esc($ticketType['id']) ?>">
                                                                <i data-lucide="minus" class="icon-xs"></i>
                                                            </button>
                                                            <input type="number" 
                                                                   name="ticket_quantity[<?= esc($ticketType['id']) ?>]" 
                                                                   id="ticket_<?= esc($ticketType['id']) ?>"
                                                                   class="ticket-quantity w-16 text-center border border-slate-300 rounded-lg py-1 focus:outline-none focus:ring-2 focus:ring-secondary"
                                                                   value="0" 
                                                                   min="0" 
                                                                   max="<?= $ticketType['quantity'] ?? 999 ?>"
                                                                   data-price="<?= $ticketType['price'] ?>"
                                                                   data-ticket-type="<?= esc($ticketType['id']) ?>">
                                                            <button type="button" class="ticket-increase w-8 h-8 rounded-full border border-slate-300 flex items-center justify-center hover:bg-slate-50" 
                                                                    data-ticket-type="<?= esc($ticketType['id']) ?>">
                                                                <i data-lucide="plus" class="icon-xs"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <?php if ($event['event_type'] === 'free'): ?>
                                    <div class="mb-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                        <p class="text-blue-800">This is a free event. No tickets required. Please fill in your details below to register.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="mb-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <p class="text-yellow-800">No ticket types available for this event. Please contact the event organizer.</p>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- Total Amount Display -->
                            <div class="mb-8 p-4 bg-secondary/10 rounded-lg">   
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-slate-800">Total Amount:</span>
                                    <span class="text-2xl font-bold text-primary" id="totalAmount">KES 0.00</span>
                                </div>
                            </div>

                            <!-- Attendee Information -->
                            <div class="mb-8">
                                <h3 class="text-lg font-semibold text-slate-800 mb-4">Attendee Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">
                                            Email Address <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" 
                                               id="email" 
                                               name="email" 
                                               class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                               value="<?= $user ? esc($user['email']) : '' ?>"
                                               placeholder="Enter your email address"
                                               required>
                                    </div>
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">
                                            Phone Number <span class="text-red-500">*</span>
                                        </label>
                                        <input type="tel" 
                                               id="phone" 
                                               name="phone" 
                                               class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent"
                                               value="<?= $user ? esc($user['phone'] ?? '') : '' ?>"
                                               placeholder="Enter your phone number"
                                               required>
                                    </div>
                                </div>
                            </div>

                            <!-- Attendee Details (for multiple tickets) -->
                            <div id="attendeeDetails" class="mb-8 hidden">
                                <h3 class="text-lg font-semibold text-slate-800 mb-4">Attendee Details</h3>
                                <p class="text-sm text-slate-600 mb-4">Please provide details for each attendee</p>
                                <div id="attendeeList" class="space-y-4">
                                    <!-- Attendee fields will be dynamically added here -->
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end space-x-4">
                                <a href="<?= base_url('events/' . esc($event['slug'])) ?>" 
                                   class="px-6 py-3 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        id="submitBtn"
                                        class="gradient-btn px-8 py-3 rounded-[50px] text-white flex items-center">
                                    <span id="submitText"><?= $event['event_type'] === 'paid' ? 'Proceed to Payment' : 'Complete Registration' ?></span>
                                    <i data-lucide="arrow-right" class="ml-2 icon z-10"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </article>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
$(document).ready(function() {
    lucide.createIcons();
    
    let totalAmount = 0;
    const ticketQuantities = {};
    
    // Calculate total amount
    function calculateTotal() {
        totalAmount = 0;
        $('.ticket-quantity').each(function() {
            const quantity = parseInt($(this).val()) || 0;
            const price = parseFloat($(this).data('price')) || 0;
            const ticketTypeId = $(this).data('ticket-type');
            
            ticketQuantities[ticketTypeId] = quantity;
            totalAmount += quantity * price;
        });
        
        $('#totalAmount').text('KES ' + totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        
        // Show/hide attendee details if tickets are selected
        if (totalAmount > 0 || Object.values(ticketQuantities).some(q => q > 0)) {
            updateAttendeeFields();
        } else {
            $('#attendeeDetails').addClass('hidden');
        }
    }
    
    // Update attendee fields based on selected tickets
    function updateAttendeeFields() {
        const totalTickets = Object.values(ticketQuantities).reduce((sum, qty) => sum + qty, 0);
        const attendeeList = $('#attendeeList');
        attendeeList.empty();
        
        if (totalTickets > 0) {
            $('#attendeeDetails').removeClass('hidden');
            
            let attendeeIndex = 0;
            Object.keys(ticketQuantities).forEach(ticketTypeId => {
                const quantity = ticketQuantities[ticketTypeId];
                for (let i = 0; i < quantity; i++) {
                    attendeeIndex++;
                    const attendeeHtml = `
                        <div class="border border-slate-200 rounded-lg p-4">
                            <h4 class="font-semibold text-slate-800 mb-3">Attendee ${attendeeIndex}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                                    <input type="text" 
                                           name="attendee_name[]" 
                                           class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary"
                                           placeholder="Enter attendee full name"
                                           required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                                    <input type="email" 
                                           name="attendee_email[]" 
                                           class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary"
                                           placeholder="Enter attendee email address"
                                           required>
                                </div>
                            </div>
                        </div>
                    `;
                    attendeeList.append(attendeeHtml);
                }
            });
        } else {
            $('#attendeeDetails').addClass('hidden');
        }
    }
    
    // Ticket quantity controls
    $('.ticket-increase').on('click', function() {
        const ticketTypeId = $(this).data('ticket-type');
        const input = $(`#ticket_${ticketTypeId}`);
        const currentVal = parseInt(input.val()) || 0;
        const maxVal = parseInt(input.attr('max')) || 999;
        if (currentVal < maxVal) {
            input.val(currentVal + 1).trigger('change');
        }
    });
    
    $('.ticket-decrease').on('click', function() {
        const ticketTypeId = $(this).data('ticket-type');
        const input = $(`#ticket_${ticketTypeId}`);
        const currentVal = parseInt(input.val()) || 0;
        if (currentVal > 0) {
            input.val(currentVal - 1).trigger('change');
        }
    });
    
    $('.ticket-quantity').on('change input', function() {
        calculateTotal();
    });
    
    // Form submission
    $('#bookingForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submitBtn');
        const submitText = $('#submitText');
        const originalText = submitText.text();
        
        // Validate that at least one ticket is selected (for paid events)
        <?php if ($event['event_type'] === 'paid'): ?>
        if (totalAmount <= 0) {
            showToast('Please select at least one ticket', 'error');
            return;
        }
        <?php endif; ?>
        
        // Prepare form data
        const formData = {
            event_id: $('input[name="event_id"]').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            ticket_data: JSON.stringify(ticketQuantities),
            attendee_info: JSON.stringify({
                names: $('input[name="attendee_name[]"]').map(function() { return $(this).val(); }).get(),
                emails: $('input[name="attendee_email[]"]').map(function() { return $(this).val(); }).get()
            }),
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        };
        
        // Disable button
        submitBtn.prop('disabled', true);
        submitText.text('Processing...');
        
        $.ajax({
            url: '<?= base_url('events/process-booking') ?>',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
                    <?php if ($event['event_type'] === 'paid'): ?>
                        // Initiate payment
                        if (response.payment_data) {
                            initiatePayment(response.payment_data);
                        } else {
                            showToast('Payment initialization failed', 'error');
                            submitBtn.prop('disabled', false);
                            submitText.text(originalText);
                        }
                    <?php else: ?>
                        // Free event - redirect to success page
                        if (response.booking_id) {
                            window.location.href = '<?= base_url('events/booking') ?>/' + response.booking_id + '/success';
                        } else {
                            showToast('Booking completed successfully', 'success');
                            setTimeout(() => {
                                window.location.href = '<?= base_url('events') ?>';
                            }, 2000);
                        }
                    <?php endif; ?>
                } else {
                    showToast(response.message || 'Booking failed', 'error');
                    submitBtn.prop('disabled', false);
                    submitText.text(originalText);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                showToast(response.message || 'An error occurred. Please try again.', 'error');
                submitBtn.prop('disabled', false);
                submitText.text(originalText);
            }
        });
    });
    
    <?php if ($event['event_type'] === 'paid'): ?>
    // Payment initiation
    function initiatePayment(paymentData) {
        const handler = PaystackPop.setup({
            key: paymentData.public_key,
            email: paymentData.email,
            amount: paymentData.amount,
            ref: paymentData.reference,
            currency: paymentData.currency || 'NGN', // Use currency from payment data, default to NGN
            metadata: paymentData.metadata,
            callback: function(response) {
                // Verify payment
                $.ajax({
                    url: '<?= base_url('events/verify-payment') ?>',
                    type: 'POST',
                    data: {
                        booking_id: paymentData.booking_id,
                        reference: response.reference,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    success: function(verifyResponse) {
                        if (verifyResponse.status === 'success') {
                            window.location.href = '<?= base_url('events/booking') ?>/' + paymentData.booking_id + '/success';
                        } else {
                            showToast(verifyResponse.message || 'Payment verification failed', 'error');
                        }
                    },
                    error: function() {
                        showToast('Payment verification failed', 'error');
                    }
                });
            },
            onClose: function() {
                showToast('Payment window closed', 'warning');
                $('#submitBtn').prop('disabled', false);
                $('#submitText').text('<?= $event['event_type'] === 'paid' ? 'Proceed to Payment' : 'Complete Registration' ?>');
            }
        });
        handler.openIframe();
    }
    <?php endif; ?>
    
    // Toast notification function
    function showToast(message, type = 'success') {
        $('.custom-toast').remove();
        let bgColor, iconName;
        switch(type) {
            case 'success': bgColor = 'bg-green-500'; iconName = 'check-circle'; break;
            case 'error': bgColor = 'bg-red-500'; iconName = 'alert-circle'; break;
            case 'warning': bgColor = 'bg-yellow-500'; iconName = 'alert-triangle'; break;
            default: bgColor = 'bg-blue-500'; iconName = 'info';
        }
        const toast = $(`
            <div class="custom-toast fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg text-white z-50 ${bgColor} max-w-sm">
                <div class="flex items-center">
                    <i data-lucide="${iconName}" class="w-5 h-5 mr-2 flex-shrink-0"></i>
                    <span class="text-sm">${message}</span>
                </div>
            </div>
        `);
        $('body').append(toast);
        lucide.createIcons();
        toast.hide().fadeIn(300);
        setTimeout(() => toast.fadeOut(300, () => toast.remove()), type === 'error' ? 5000 : 3000);
    }
    
    // Initial calculation
    calculateTotal();
});
</script>
<?= $this->endSection() ?>

