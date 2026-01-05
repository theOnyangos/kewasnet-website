<?php

namespace App\Services;

use App\Models\EventBookingModel;
use App\Models\EventTicketModel;
use App\Models\EventModel;
use App\Models\EventTicketTypeModel;
use App\Libraries\Mailer;
use CodeIgniter\I18n\Time;

class TicketService
{
    protected $bookingModel;
    protected $ticketModel;
    protected $eventModel;
    protected $ticketTypeModel;
    protected $mailer;
    protected $view;

    public function __construct()
    {
        $this->bookingModel = new EventBookingModel();
        $this->ticketModel = new EventTicketModel();
        $this->eventModel = new EventModel();
        $this->ticketTypeModel = new EventTicketTypeModel();
        $this->mailer = new \App\Libraries\Mailer();
        $this->view = \Config\Services::renderer();
    }

    /**
     * Create booking
     * $ticketData format: ['ticket_type_id' => quantity, ...]
     * $attendeeInfo format: [['name' => '', 'email' => ''], ...]
     */
    public function createBooking($userId, $eventId, $ticketData, $attendeeInfo, $email, $phone = null)
    {
        $event = $this->eventModel->find($eventId);
        if (!$event) {
            return [
                'status' => 'error',
                'message' => 'Event not found'
            ];
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Calculate total amount
            $totalAmount = 0.00;
            $totalTickets = 0;
            foreach ($ticketData as $ticketTypeId => $quantity) {
                if ($quantity <= 0) {
                    continue;
                }

                $ticketType = $this->ticketTypeModel->find($ticketTypeId);
                if ($ticketType && $ticketType['event_id'] == $eventId) {
                    $totalAmount += $ticketType['price'] * $quantity;
                    $totalTickets += $quantity;
                }
            }

            // Determine payment status
            $paymentStatus = ($totalAmount > 0) ? 'pending' : 'paid';

            // Create booking
            $bookingData = [
                'event_id' => $eventId,
                'user_id' => $userId ?: null, // Allow null for guest bookings
                'total_amount' => $totalAmount,
                'payment_status' => $paymentStatus,
                'status' => 'confirmed',
                'email' => $email,
                'phone' => $phone,
            ];

            // Insert booking (validation happens automatically)
            $bookingId = $this->bookingModel->insert($bookingData);
            
            if (!$bookingId) {
                $errors = $this->bookingModel->errors();
                $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Failed to create booking';
                log_message('error', 'Booking creation failed: ' . json_encode($errors));
                log_message('error', 'Booking data: ' . json_encode($bookingData));
                throw new \Exception($errorMessage);
            }

            // For UUID models, insert() returns true/false, not the ID
            // We need to get the booking by the generated booking_number or email
            $booking = $this->bookingModel->where('email', $email)
                ->where('event_id', $eventId)
                ->orderBy('created_at', 'DESC')
                ->first();
            
            if (!$booking) {
                throw new \Exception('Failed to retrieve booking after creation');
            }
            
            $bookingId = $booking['id'];
            $bookingNumber = $booking['booking_number'];

            // Create tickets and reduce quantities
            $ticketIndex = 0;
            $ticketsCreated = 0;
            foreach ($ticketData as $ticketTypeId => $quantity) {
                if ($quantity <= 0) {
                    continue;
                }

                $ticketType = $this->ticketTypeModel->find($ticketTypeId);
                if (!$ticketType) {
                    throw new \Exception("Ticket type {$ticketTypeId} not found");
                }

                // Check if enough tickets are available
                $currentQuantity = (int)($ticketType['quantity'] ?? 0);
                if ($currentQuantity > 0 && $quantity > $currentQuantity) {
                    throw new \Exception("Not enough tickets available. Only {$currentQuantity} ticket(s) remaining for {$ticketType['name']}");
                }

                // Create tickets
                for ($i = 0; $i < $quantity; $i++) {
                    $attendee = $attendeeInfo[$ticketIndex] ?? [
                        'name' => $email,
                        'email' => $email
                    ];

                    // Generate QR code data
                    $qrCodeData = base64_encode(json_encode([
                        'booking_id' => $bookingId,
                        'ticket_type_id' => $ticketTypeId,
                        'timestamp' => time()
                    ])) . '-' . bin2hex(random_bytes(8));

                    $ticketInsertData = [
                        'booking_id' => $bookingId,
                        'ticket_type_id' => $ticketTypeId,
                        'qr_code_data' => $qrCodeData,
                        'attendee_name' => $attendee['name'],
                        'attendee_email' => $attendee['email'],
                        'status' => 'active',
                    ];

                    $ticketInsertResult = $this->ticketModel->insert($ticketInsertData);
                    
                    if (!$ticketInsertResult) {
                        $ticketErrors = $this->ticketModel->errors();
                        $errorMessage = !empty($ticketErrors) ? implode(', ', $ticketErrors) : 'Failed to create ticket';
                        log_message('error', 'Ticket creation failed: ' . json_encode($ticketErrors));
                        log_message('error', 'Ticket data: ' . json_encode($ticketInsertData));
                        throw new \Exception('Failed to create ticket: ' . $errorMessage);
                    }
                    
                    $ticketsCreated++;
                    $ticketIndex++;
                }

                // Reduce ticket type quantity (only if quantity is tracked, i.e., > 0)
                if ($currentQuantity > 0) {
                    $newQuantity = max(0, $currentQuantity - $quantity);
                    $updateResult = $this->ticketTypeModel->update($ticketTypeId, [
                        'quantity' => $newQuantity
                    ]);
                    
                    if (!$updateResult) {
                        log_message('error', "Failed to update ticket type quantity for {$ticketTypeId}");
                        throw new \Exception('Failed to update ticket availability');
                    }
                    
                    log_message('info', "Reduced ticket type {$ticketTypeId} quantity from {$currentQuantity} to {$newQuantity}");
                }
            }
            
            if ($ticketsCreated == 0) {
                throw new \Exception('No tickets were created');
            }
            
            log_message('info', "Created {$ticketsCreated} ticket(s) for booking #{$bookingId}");

            $db->transComplete();

            if ($db->transStatus() === false) {
                $dbError = $db->error();
                log_message('error', 'Transaction failed: ' . json_encode($dbError));
                throw new \Exception('Transaction failed: ' . ($dbError['message'] ?? 'Unknown error'));
            }

            log_message('info', "Booking created successfully: #{$bookingId} ({$bookingNumber})");

            return [
                'status' => 'success',
                'booking_id' => $bookingId,
                'booking_number' => $bookingNumber,
                'payment_status' => $paymentStatus,
                'total_amount' => $totalAmount,
            ];

        } catch (\Exception $e) {
            $db->transRollback();
            $errorMessage = $e->getMessage();
            log_message('error', 'TicketService::createBooking error: ' . $errorMessage);
            log_message('error', 'TicketService::createBooking stack trace: ' . $e->getTraceAsString());
            return [
                'status' => 'error',
                'message' => $errorMessage
            ];
        }
    }

    /**
     * Generate tickets for a booking (already created, just regenerates QR codes if needed)
     */
    public function generateTickets($bookingId)
    {
        $booking = $this->bookingModel->find($bookingId);
        if (!$booking) {
            return [
                'status' => 'error',
                'message' => 'Booking not found'
            ];
        }

        $tickets = $this->ticketModel->getBookingTickets($bookingId);
        
        // If tickets already exist, return them
        if (!empty($tickets)) {
            return [
                'status' => 'success',
                'tickets' => $tickets
            ];
        }

        return [
            'status' => 'error',
            'message' => 'No tickets found for this booking'
        ];
    }

    /**
     * Get all tickets for a booking
     */
    public function getBookingTickets($bookingId)
    {
        return $this->ticketModel->getBookingTickets($bookingId);
    }

    /**
     * Send tickets via email
     */
    public function sendTicketsByEmail($bookingId)
    {
        $booking = $this->bookingModel->find($bookingId);
        if (!$booking) {
            return [
                'status' => 'error',
                'message' => 'Booking not found'
            ];
        }

        $event = $this->eventModel->find($booking['event_id']);
        if (!$event) {
            return [
                'status' => 'error',
                'message' => 'Event not found'
            ];
        }

        $tickets = $this->ticketModel->getBookingTickets($bookingId);
        
        log_message('info', "TicketService::sendTicketsByEmail - Found " . count($tickets) . " ticket(s) for booking #{$bookingId}");

        if (empty($tickets)) {
            log_message('error', "TicketService::sendTicketsByEmail - No tickets found for booking #{$bookingId}");
            return [
                'status' => 'error',
                'message' => 'No tickets found for this booking'
            ];
        }

        try {
            log_message('info', "TicketService::sendTicketsByEmail - Starting email queue process for booking #{$bookingId}");
            // Prepare email data
            $emailData = [
                'event' => $event,
                'booking' => $booking,
                'tickets' => $tickets,
                'ticketDownloadUrl' => base_url('events/booking/' . $bookingId . '/tickets'),
            ];

            // Render email template
            $message = $this->view->setData($emailData)->render('backend/emails/event_ticket_template');
            
            if (empty($message)) {
                log_message('error', "TicketService::sendTicketsByEmail - Failed to render email template for booking #{$bookingId}");
                return [
                    'status' => 'error',
                    'message' => 'Failed to render email template'
                ];
            }

            $subject = 'Your Tickets for ' . $event['title'];
            $toEmail = $booking['email'];
            
            if (empty($toEmail)) {
                log_message('error', "TicketService::sendTicketsByEmail - Empty email address for booking #{$bookingId}");
                return [
                    'status' => 'error',
                    'message' => 'Invalid email address'
                ];
            }

            // Get from email and name (same pattern as AuthService)
            $from = env('EMAIL_FROM_ADDRESS');
            $fromName = env('EMAIL_FROM_NAME');

            // Queue email through Mailer (which uses EmailQueue)
            // The Mailer library automatically queues emails instead of sending directly
            // Pass from and fromName explicitly like AuthService does
            // Use try-catch like AuthService does (catches \Throwable)
            try {
                $this->mailer->send($toEmail, $subject, $message, $from, $fromName);
                
                log_message('info', "Ticket email queued successfully for booking #{$bookingId} to {$toEmail}");

                return [
                    'status' => 'success',
                    'message' => 'Tickets queued for delivery. You will receive them shortly via email.'
                ];
            } catch (\Throwable $th) {
                log_message('error', 'TicketService::sendTicketsByEmail - Failed to queue email: ' . $th->getMessage());
                log_message('error', 'TicketService::sendTicketsByEmail stack trace: ' . $th->getTraceAsString());
                return [
                    'status' => 'error',
                    'message' => 'Failed to queue ticket email: ' . $th->getMessage()
                ];
            }

        } catch (\Exception $e) {
            log_message('error', 'TicketService::sendTicketsByEmail error: ' . $e->getMessage());
            log_message('error', 'TicketService::sendTicketsByEmail stack trace: ' . $e->getTraceAsString());
            return [
                'status' => 'error',
                'message' => 'Failed to queue ticket email: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify ticket by QR code
     */
    public function verifyTicket($qrCodeData)
    {
        $ticket = $this->ticketModel->verifyByQrCode($qrCodeData);

        if (!$ticket) {
            return [
                'valid' => false,
                'message' => 'Invalid ticket'
            ];
        }

        // Get booking
        $booking = $this->bookingModel->find($ticket['booking_id']);
        if (!$booking || $booking['status'] != 'confirmed') {
            return [
                'valid' => false,
                'message' => 'Booking not confirmed'
            ];
        }

        // Get event
        $event = $this->eventModel->find($booking['event_id']);
        if (!$event || $event['status'] != 'published') {
            return [
                'valid' => false,
                'message' => 'Event not available'
            ];
        }

        // Check if already used
        if ($ticket['status'] == 'used') {
            return [
                'valid' => false,
                'already_used' => true,
                'message' => 'Ticket already used',
                'checked_in_at' => $ticket['checked_in_at']
            ];
        }

        return [
            'valid' => true,
            'ticket' => $ticket,
            'booking' => $booking,
            'event' => $event,
            'message' => 'Ticket is valid'
        ];
    }
}

