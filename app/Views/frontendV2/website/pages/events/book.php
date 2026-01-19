<?php 
    use App\Helpers\UrlHelper;
    use App\Libraries\ClientAuth;
    
    $currentUrl = new UrlHelper();
    $userId = ClientAuth::getId();
    $user = ClientAuth::user();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
    
    // Check if event is free
    $isFreeEvent = false;
    if ($event['event_type'] === 'free') {
        $isFreeEvent = true;
    } elseif (!empty($event['ticket_types']) && count($event['ticket_types']) > 0) {
        // Check if all ticket types have zero price
        $allFree = true;
        foreach ($event['ticket_types'] as $ticketType) {
            if (isset($ticketType['price']) && floatval($ticketType['price']) > 0) {
                $allFree = false;
                break;
            }
        }
        $isFreeEvent = $allFree;
    }
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
                <ol class="flex flex-wrap items-center gap-1 sm:gap-2 text-xs sm:text-sm">
                    <li>
                        <a href="<?= base_url() ?>" class="text-slate-600 hover:text-primary transition-colors whitespace-nowrap">Home</a>
                    </li>
                    <li class="flex-shrink-0">
                        <i data-lucide="chevron-right" class="icon-xs text-slate-400"></i>
                    </li>
                    <li>
                        <a href="<?= base_url('events') ?>" class="text-slate-600 hover:text-primary transition-colors whitespace-nowrap">Events</a>
                    </li>
                    <li class="flex-shrink-0">
                        <i data-lucide="chevron-right" class="icon-xs text-slate-400"></i>
                    </li>
                    <li class="min-w-0 flex-1">
                        <a href="<?= base_url('events/' . esc($event['slug'])) ?>" class="text-slate-600 hover:text-primary transition-colors truncate block" title="<?= esc($event['title']) ?>"><?= esc($event['title']) ?></a>
                    </li>
                    <li class="flex-shrink-0">
                        <i data-lucide="chevron-right" class="icon-xs text-slate-400"></i>
                    </li>
                    <li class="text-primary flex-shrink-0" aria-current="page">
                        <span class="whitespace-nowrap">Book Tickets</span>
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
                    <div class="bg-white border border-slate-200 rounded-xl p-4 md:p-8">
                        <h2 class="text-2xl font-bold text-secondary mb-6">Book Your Tickets</h2>
                        
                        <!-- Booking Instructions Note -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-0.5">
                                    <i data-lucide="info" class="w-5 h-5 text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-blue-900 mb-2">How to Book Tickets</h3>
                                    <ul class="space-y-1.5 text-sm text-blue-800">
                                        <li class="flex items-start gap-2">
                                            <i data-lucide="check-circle" class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5"></i>
                                            <span>Use the <strong>+</strong> and <strong>-</strong> buttons to select the number of tickets you want for each ticket type</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <i data-lucide="check-circle" class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5"></i>
                                            <span>You can select <strong>multiple tickets</strong> of the same type or different types</span>
                                        </li>
                                        <?php if (!empty($event['ticket_types']) && count($event['ticket_types']) > 0): ?>
                                            <?php if ($event['event_type'] === 'free' || $isFreeEvent): ?>
                                                <li class="flex items-start gap-2">
                                                    <i data-lucide="check-circle" class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5"></i>
                                                    <span>This is a <strong>free event</strong> - select at least one ticket to continue with registration</span>
                                                </li>
                                            <?php else: ?>
                                                <li class="flex items-start gap-2">
                                                    <i data-lucide="check-circle" class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5"></i>
                                                    <span>Review the <strong>total amount</strong> before proceeding to payment</span>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <li class="flex items-start gap-2">
                                            <i data-lucide="check-circle" class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5"></i>
                                            <span>Fill in your <strong>contact information</strong> and attendee details (if booking multiple tickets)</span>
                                        </li>
                                        <?php if ($event['event_type'] === 'paid'): ?>
                                            <li class="flex items-start gap-2">
                                                <i data-lucide="check-circle" class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5"></i>
                                                <span>Complete <strong>payment</strong> to finalize your booking</span>
                                            </li>
                                        <?php else: ?>
                                            <li class="flex items-start gap-2">
                                                <i data-lucide="check-circle" class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5"></i>
                                                <span>Click <strong>Complete Registration</strong> to confirm your booking</span>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
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
                                                <div class="flex flex-col md:flex-row md:justify-between md:items-start mb-3 md:mb-0">
                                                    <div class="flex-1 mb-3 md:mb-0">
                                                        <h4 class="font-semibold text-slate-800 mb-1"><?= esc($ticketType['name']) ?></h4>
                                                        <?php if (!empty($ticketType['description'])): ?>
                                                            <p class="text-sm text-slate-600 mb-2"><?= esc($ticketType['description']) ?></p>
                                                        <?php endif; ?>
                                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
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

                                                    <!-- Ticket Quantity Desktop - Shown on side for larger screens -->
                                                    <div class="ml-0 md:ml-4 hidden md:flex items-center space-x-2">
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
                                                
                                                <!-- Ticket Quantity Mobile - Shown at bottom on small screens -->
                                                <div class="mt-3 pt-3 border-t border-slate-200 md:hidden">
                                                    <label class="block text-sm font-medium text-slate-700 mb-2">Quantity:</label>
                                                    <div class="flex items-center space-x-2">
                                                        <button type="button" class="ticket-decrease w-10 h-10 rounded-full border border-slate-300 flex items-center justify-center hover:bg-slate-50 flex-shrink-0" 
                                                                data-ticket-type="<?= esc($ticketType['id']) ?>">
                                                            <i data-lucide="minus" class="icon-xs"></i>
                                                        </button>
                                                        <input type="number" 
                                                               name="ticket_quantity[<?= esc($ticketType['id']) ?>]" 
                                                               id="ticket_<?= esc($ticketType['id']) ?>_mobile"
                                                               class="ticket-quantity-mobile flex-1 text-center border border-slate-300 rounded-lg py-2 focus:outline-none focus:ring-2 focus:ring-secondary"
                                                               value="0" 
                                                               min="0" 
                                                               max="<?= $ticketType['quantity'] ?? 999 ?>"
                                                               data-price="<?= $ticketType['price'] ?>"
                                                               data-ticket-type="<?= esc($ticketType['id']) ?>"
                                                               disabled>
                                                        <button type="button" class="ticket-increase w-10 h-10 rounded-full border border-slate-300 flex items-center justify-center hover:bg-slate-50 flex-shrink-0" 
                                                                data-ticket-type="<?= esc($ticketType['id']) ?>">
                                                            <i data-lucide="plus" class="icon-xs"></i>
                                                        </button>
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
                                    <span class="text-2xl font-bold text-primary" id="totalAmount">
                                        <?= $isFreeEvent ? 'Free event' : 'KES 0.00' ?>
                                    </span>
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
                            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 sm:space-x-4 mt-8">
                                <a href="<?= base_url('events/' . esc($event['slug'])) ?>" 
                                   class="w-full sm:w-auto px-6 py-3 border border-slate-300 rounded-full text-slate-700 hover:bg-slate-50 transition-colors text-center">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        id="submitBtn"
                                        class="w-full sm:w-auto gradient-btn px-8 py-3 rounded-[50px] text-white flex items-center justify-center <?= ($isFreeEvent && !empty($event['ticket_types'])) ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                        <?= ($isFreeEvent && !empty($event['ticket_types'])) ? 'disabled' : '' ?>>
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
    
    // Check if event is free
    const isFreeEvent = <?= $isFreeEvent ? 'true' : 'false' ?>;
    const eventType = '<?= $event['event_type'] ?? 'paid' ?>';
    
    // Calculate total amount
    function calculateTotal() {
        totalAmount = 0;
        // Check both desktop and mobile inputs, use desktop if visible, otherwise mobile
        $('.ticket-quantity, .ticket-quantity-mobile').each(function() {
            const ticketTypeId = $(this).data('ticket-type');
            // Only count each ticket type once - prefer desktop, fallback to mobile
            if ($(this).hasClass('ticket-quantity-mobile')) {
                // Skip mobile if desktop version exists and is visible
                if ($(`#ticket_${ticketTypeId}`).length && $(`#ticket_${ticketTypeId}`).is(':visible')) {
                    return; // Skip mobile version
                }
            } else {
                // Skip desktop if it's hidden (mobile version exists)
                if (!$(this).is(':visible')) {
                    return; // Skip desktop version
                }
            }
            
            const quantity = parseInt($(this).val()) || 0;
            const price = parseFloat($(this).data('price')) || 0;
            
            ticketQuantities[ticketTypeId] = quantity;
            totalAmount += quantity * price;
        });
        
        // Display total amount or "Free event" if event is free and total is 0
        if (isFreeEvent && totalAmount === 0) {
            $('#totalAmount').text('Free event');
        } else {
            $('#totalAmount').text('KES ' + totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        }
        
        // Show/hide attendee details if tickets are selected
        if (totalAmount > 0 || Object.values(ticketQuantities).some(q => q > 0)) {
            updateAttendeeFields();
        } else {
            $('#attendeeDetails').addClass('hidden');
        }
        
        // Update submit button state for free events
        updateSubmitButtonState();
    }
    
    // Update submit button state based on ticket selection (for free events)
    function updateSubmitButtonState() {
        const submitBtn = $('#submitBtn');
        const hasTicketsSelected = Object.values(ticketQuantities).some(q => q > 0);
        
        if (isFreeEvent || eventType === 'free') {
            if (hasTicketsSelected) {
                submitBtn.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
            } else {
                submitBtn.prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
            }
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
    
    // Sync ticket quantity inputs (desktop and mobile)
    function syncTicketInputs(ticketTypeId, value) {
        const desktopInput = $(`#ticket_${ticketTypeId}`);
        const mobileInput = $(`#ticket_${ticketTypeId}_mobile`);
        
        desktopInput.val(value);
        mobileInput.val(value);
        
        // Enable/disable based on screen size - only visible one is submitted
        if (window.innerWidth >= 768) {
            // Desktop view
            desktopInput.prop('disabled', false);
            mobileInput.prop('disabled', true);
        } else {
            // Mobile view
            desktopInput.prop('disabled', true);
            mobileInput.prop('disabled', false);
        }
    }
    
    // Initialize sync on page load and window resize
    function initializeTicketSync() {
        $('.ticket-quantity').each(function() {
            const ticketTypeId = $(this).data('ticket-type');
            const desktopInput = $(`#ticket_${ticketTypeId}`);
            const mobileInput = $(`#ticket_${ticketTypeId}_mobile`);
            const value = desktopInput.val() || 0;
            
            // Sync values
            desktopInput.val(value);
            mobileInput.val(value);
            
            // Enable/disable based on visibility
            if (window.innerWidth >= 768) {
                // Desktop: enable desktop input, disable mobile
                desktopInput.prop('disabled', false);
                mobileInput.prop('disabled', true);
            } else {
                // Mobile: enable mobile input, disable desktop
                desktopInput.prop('disabled', true);
                mobileInput.prop('disabled', false);
            }
        });
    }
    
    // Call on load and resize
    initializeTicketSync();
    $(window).on('resize', function() {
        initializeTicketSync();
        calculateTotal(); // Recalculate after resize
    });
    
    // Ticket quantity controls
    $('.ticket-increase').on('click', function() {
        const ticketTypeId = $(this).data('ticket-type');
        const desktopInput = $(`#ticket_${ticketTypeId}`);
        const mobileInput = $(`#ticket_${ticketTypeId}_mobile`);
        
        // Use the enabled input (which is the visible one)
        const activeInput = desktopInput.prop('disabled') ? mobileInput : desktopInput;
        
        const currentVal = parseInt(activeInput.val()) || 0;
        const maxVal = parseInt(activeInput.attr('max')) || 999;
        if (currentVal < maxVal) {
            const newVal = currentVal + 1;
            syncTicketInputs(ticketTypeId, newVal);
            activeInput.trigger('change');
        }
    });
    
    $('.ticket-decrease').on('click', function() {
        const ticketTypeId = $(this).data('ticket-type');
        const desktopInput = $(`#ticket_${ticketTypeId}`);
        const mobileInput = $(`#ticket_${ticketTypeId}_mobile`);
        
        // Use the enabled input (which is the visible one)
        const activeInput = desktopInput.prop('disabled') ? mobileInput : desktopInput;
        
        const currentVal = parseInt(activeInput.val()) || 0;
        if (currentVal > 0) {
            const newVal = currentVal - 1;
            syncTicketInputs(ticketTypeId, newVal);
            activeInput.trigger('change');
        }
    });
    
    // Handle changes from desktop input
    $('.ticket-quantity').on('change input', function() {
        const ticketTypeId = $(this).data('ticket-type');
        const value = $(this).val();
        syncTicketInputs(ticketTypeId, value);
        calculateTotal();
    });
    
    // Handle changes from mobile input
    $('.ticket-quantity-mobile').on('change input', function() {
        const ticketTypeId = $(this).data('ticket-type');
        const value = $(this).val();
        syncTicketInputs(ticketTypeId, value);
        calculateTotal();
    });
    
    // Form submission
    $('#bookingForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submitBtn');
        const submitText = $('#submitText');
        const originalText = submitText.text();
        
        // Validate that at least one ticket is selected
        const hasTicketsSelected = Object.values(ticketQuantities).some(q => q > 0);
        
        if (!hasTicketsSelected) {
            if (isFreeEvent || eventType === 'free') {
                showSweetAlertToast('Please select at least one ticket before continuing', 'warning');
            } else {
                showSweetAlertToast('Please select at least one ticket', 'error');
            }
            return;
        }
        
        // For paid events, also check total amount
        <?php if ($event['event_type'] === 'paid'): ?>
        if (totalAmount <= 0) {
            showSweetAlertToast('Please select at least one ticket', 'error');
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
                    // Track event booking
                    if (window.kewasnetTracker) {
                        window.kewasnetTracker.trackEvent('event_booking', 'submit', 'Event Booking Form', totalAmount || 0, 'Events');
                    } else if (window.trackEvent) {
                        window.trackEvent('event_booking', 'submit', 'Event Booking Form', totalAmount || 0, 'Events');
                    }
                    
                    <?php if ($event['event_type'] === 'paid'): ?>
                        // Initiate payment
                        if (response.payment_data) {
                            initiatePayment(response.payment_data);
                        } else {
                            showSweetAlertToast('Payment initialization failed', 'error');
                            submitBtn.prop('disabled', false);
                            submitText.text(originalText);
                        }
                    <?php else: ?>
                        // Free event - redirect to success page
                        if (response.booking_id) {
                            showSweetAlertToast('Registration completed successfully!', 'success');
                            setTimeout(() => {
                                window.location.href = '<?= base_url('events/booking') ?>/' + response.booking_id + '/success';
                            }, 1500);
                        } else {
                            showSweetAlertToast('Booking completed successfully', 'success');
                            setTimeout(() => {
                                window.location.href = '<?= base_url('events') ?>';
                            }, 2000);
                        }
                    <?php endif; ?>
                } else {
                    showSweetAlertToast(response.message || 'Booking failed', 'error');
                    submitBtn.prop('disabled', false);
                    submitText.text(originalText);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                showSweetAlertToast(response.message || 'An error occurred. Please try again.', 'error');
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
                            showSweetAlertToast('Payment verified successfully!', 'success');
                            setTimeout(() => {
                                window.location.href = '<?= base_url('events/booking') ?>/' + paymentData.booking_id + '/success';
                            }, 1500);
                        } else {
                            showSweetAlertToast(verifyResponse.message || 'Payment verification failed', 'error');
                        }
                    },
                    error: function() {
                        showSweetAlertToast('Payment verification failed', 'error');
                    }
                });
            },
            onClose: function() {
                showSweetAlertToast('Payment window closed', 'warning');
                $('#submitBtn').prop('disabled', false);
                $('#submitText').text('<?= $event['event_type'] === 'paid' ? 'Proceed to Payment' : 'Complete Registration' ?>');
            }
        });
        handler.openIframe();
    }
    <?php endif; ?>
    
    // SweetAlert Toast notification function
    function showSweetAlertToast(message, type = 'success') {
        if (typeof Swal === 'undefined') {
            // Fallback to alert if SweetAlert is not loaded
            alert(message);
            return;
        }
        
        const toastConfig = {
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: type === 'error' ? 5000 : 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        };
        
        switch(type) {
            case 'success':
                Swal.fire({
                    ...toastConfig,
                    icon: 'success',
                    title: message
                });
                break;
            case 'error':
                Swal.fire({
                    ...toastConfig,
                    icon: 'error',
                    title: message
                });
                break;
            case 'warning':
                Swal.fire({
                    ...toastConfig,
                    icon: 'warning',
                    title: message
                });
                break;
            default:
                Swal.fire({
                    ...toastConfig,
                    icon: 'info',
                    title: message
                });
        }
    }
    
    // Initial calculation and button state
    calculateTotal();
});
</script>
<?= $this->endSection() ?>

