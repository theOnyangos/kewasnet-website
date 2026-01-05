<?= $this->extend('backendV2/layouts/main') ?>
<?= $this->section('title'); ?><?= $title ?><?= $this->endSection(); ?>
<?= $this->section('content') ?>
<main class="flex-1 overflow-y-auto">
    <?= view('backendV2/partials/page_banner', [
        'pageTitle' => 'Event Check-In',
        'pageDescription' => 'Scan QR codes to check in attendees',
        'breadcrumbs' => [
            ['label' => 'Events', 'url' => base_url('auth/events')],
            ['label' => 'Check-In']
        ]
    ]) ?>

    <div class="px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm p-6 mt-6 max-w-2xl mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-800">Event Check-In</h1>
                <p class="mt-1 text-sm text-slate-500">Scan QR code or enter ticket data to check in attendees</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="qr_code_input" class="block text-sm font-medium text-dark mb-2">QR Code Data</label>
                    <textarea id="qr_code_input" rows="3" class="w-full px-4 py-3 border border-borderColor rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary" placeholder="Paste QR code data here or scan QR code"></textarea>
                </div>

                <button onclick="verifyTicket()" class="w-full px-6 py-3 bg-secondary text-white rounded-lg hover:bg-secondaryShades-600">
                    Verify & Check In
                </button>

                <div id="checkin_result" class="hidden mt-4 p-4 rounded-lg"></div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    lucide.createIcons();
});

function verifyTicket() {
    const qrCodeData = $('#qr_code_input').val().trim();
    
    if (!qrCodeData) {
        showToast('Please enter QR code data', 'error');
        return;
    }

    $.ajax({
        url: '<?= base_url('auth/events/verify-ticket') ?>',
        type: 'POST',
        data: {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
            qr_code_data: qrCodeData
        },
        success: function(response) {
            const resultDiv = $('#checkin_result');
            resultDiv.removeClass('hidden');
            
            if (response.status === 'success') {
                resultDiv.removeClass('bg-red-50 text-red-800').addClass('bg-green-50 text-green-800');
                resultDiv.html(`
                    <div class="font-semibold">Check-In Successful!</div>
                    <div class="mt-2 text-sm">
                        <p><strong>Attendee:</strong> ${response.ticket.attendee_name}</p>
                        <p><strong>Ticket:</strong> ${response.ticket.ticket_number}</p>
                        <p><strong>Event:</strong> ${response.event.title}</p>
                    </div>
                `);
                $('#qr_code_input').val('');
            } else {
                resultDiv.removeClass('bg-green-50 text-green-800').addClass('bg-red-50 text-red-800');
                resultDiv.html(`<div class="font-semibold">Check-In Failed</div><div class="mt-2 text-sm">${response.message}</div>`);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON || {};
            const resultDiv = $('#checkin_result');
            resultDiv.removeClass('hidden bg-green-50 text-green-800').addClass('bg-red-50 text-red-800');
            resultDiv.html(`<div class="font-semibold">Error</div><div class="mt-2 text-sm">${response.message || 'An error occurred'}</div>`);
        }
    });
}
</script>
<?= $this->endSection() ?>

