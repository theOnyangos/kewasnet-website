<?php

namespace App\Controllers\FrontendV2;

use App\Controllers\BaseController;
use App\Services\EventService;
use App\Services\TicketService;
use App\Services\PaymentService;
use App\Models\EventModel;
use App\Models\EventBookingModel;
use App\Models\EventTicketModel;
use App\Models\EventTicketTypeModel;
use App\Libraries\TicketGenerator;
use App\Libraries\ClientAuth;
use CodeIgniter\HTTP\ResponseInterface;

class EventsController extends BaseController
{
    protected $eventService;
    protected $ticketService;
    protected $paymentService;
    protected $eventModel;
    protected $bookingModel;
    protected $ticketModel;
    protected $ticketTypeModel;

    public function __construct()
    {
        $this->eventService = new EventService();
        $this->ticketService = new TicketService();
        $this->paymentService = new PaymentService();
        $this->eventModel = new EventModel();
        $this->bookingModel = new EventBookingModel();
        $this->ticketModel = new EventTicketModel();
        $this->ticketTypeModel = new EventTicketTypeModel();
    }

    /**
     * List all published events
     */
    public function index()
    {
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = 12;
        $search = $this->request->getGet('search') ?? null;

        $builder = $this->eventModel->where('status', 'published')
            ->where('deleted_at', null);

        if ($search) {
            $builder->groupStart()
                ->like('title', $search)
                ->orLike('description', $search)
                ->groupEnd();
        }

        $totalEvents = $builder->countAllResults(false);
        $events = $builder->orderBy('start_date', 'ASC')
            ->limit($perPage, ($page - 1) * $perPage)
            ->findAll();

        $totalPages = ceil($totalEvents / $perPage);

        // Format event image URLs
        foreach ($events as &$event) {
            if (!empty($event['image_url'])) {
                $imagePath = $event['image_url'];
                $imagePath = trim($imagePath);
                
                if (strpos($imagePath, 'http') === 0) {
                    $parsedUrl = parse_url($imagePath);
                    if (isset($parsedUrl['path'])) {
                        $imagePath = ltrim($parsedUrl['path'], '/');
                    }
                }
                
                $imagePath = ltrim($imagePath, '/');
                $event['image_url'] = base_url($imagePath);
            } else {
                $event['image_url'] = base_url('assets/images/default-blog.jpg');
            }
        }

        $data = [
            'title' => 'Events - KEWASNET',
            'description' => 'Discover upcoming events, workshops, and conferences in Kenya\'s WASH sector. Join us for networking, learning, and collaboration opportunities.',
            'events' => $events,
            'search' => $search,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_events' => $totalEvents,
                'per_page' => $perPage,
            ],
        ];

        return view('frontendV2/website/pages/events/index', $data);
    }

    /**
     * Event details page
     */
    public function details($eventSlug)
    {
        // Try to find by slug first
        $event = $this->eventModel->findBySlug($eventSlug);
        
        // If not found by slug, try by ID (for backward compatibility)
        if (!$event) {
            $event = $this->eventService->getEvent($eventSlug);
        }

        if (!$event || $event['status'] != 'published') {
            return redirect()->to('events')->with('error', 'Event not found');
        }

        // Get ticket types for this event
        if (isset($event['id'])) {
            $event['ticket_types'] = $this->ticketTypeModel->getEventTicketTypes($event['id']);
        }

        // Format event image URLs
        if (!empty($event['image_url'])) {
            $imagePath = $event['image_url'];
            $imagePath = trim($imagePath);
            
            if (strpos($imagePath, 'http') === 0) {
                $parsedUrl = parse_url($imagePath);
                if (isset($parsedUrl['path'])) {
                    $imagePath = ltrim($parsedUrl['path'], '/');
                }
            }
            
            $imagePath = ltrim($imagePath, '/');
            $event['image_url'] = base_url($imagePath);
        } else {
            $event['image_url'] = base_url('assets/images/default-blog.jpg');
        }
        
        if (!empty($event['banner_url'])) {
            $bannerPath = $event['banner_url'];
            $bannerPath = trim($bannerPath);
            
            if (strpos($bannerPath, 'http') === 0) {
                $parsedUrl = parse_url($bannerPath);
                if (isset($parsedUrl['path'])) {
                    $bannerPath = ltrim($parsedUrl['path'], '/');
                }
            }
            
            $bannerPath = ltrim($bannerPath, '/');
            $event['banner_url'] = base_url($bannerPath);
        }

        $data = [
            'title' => $event['title'] . ' - KEWASNET',
            'description' => substr(strip_tags($event['description'] ?? ''), 0, 155),
            'event' => $event,
        ];

        return view('frontendV2/website/pages/events/details', $data);
    }

    /**
     * Booking page
     */
    public function book($eventSlug)
    {
        // Try to find by slug first
        $event = $this->eventModel->findBySlug($eventSlug);
        
        // If not found by slug, try by ID (for backward compatibility)
        if (!$event) {
            $event = $this->eventService->getEvent($eventSlug);
        }

        if (!$event || $event['status'] != 'published') {
            return redirect()->to('events')->with('error', 'Event not found');
        }
        
        // Get ticket types for this event
        if (isset($event['id'])) {
            $event['ticket_types'] = $this->ticketTypeModel->getEventTicketTypes($event['id']);
        }

        // Format event image URLs
        if (!empty($event['image_url'])) {
            $imagePath = $event['image_url'];
            $imagePath = trim($imagePath);
            
            if (strpos($imagePath, 'http') === 0) {
                $parsedUrl = parse_url($imagePath);
                if (isset($parsedUrl['path'])) {
                    $imagePath = ltrim($parsedUrl['path'], '/');
                }
            }
            
            $imagePath = ltrim($imagePath, '/');
            $event['image_url'] = base_url($imagePath);
        } else {
            $event['image_url'] = base_url('assets/images/default-blog.jpg');
        }
        
        if (!empty($event['banner_url'])) {
            $bannerPath = $event['banner_url'];
            $bannerPath = trim($bannerPath);
            
            if (strpos($bannerPath, 'http') === 0) {
                $parsedUrl = parse_url($bannerPath);
                if (isset($parsedUrl['path'])) {
                    $bannerPath = ltrim($parsedUrl['path'], '/');
                }
            }
            
            $bannerPath = ltrim($bannerPath, '/');
            $event['banner_url'] = base_url($bannerPath);
        }

        $data = [
            'title' => 'Book Tickets - ' . $event['title'],
            'description' => 'Book your tickets for ' . $event['title'],
            'event' => $event,
        ];

        return view('frontendV2/website/pages/events/book', $data);
    }

    /**
     * Process booking (AJAX)
     */
    public function processBooking()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $userId = ClientAuth::getId();
            $eventId = $this->request->getPost('event_id');
            $ticketData = json_decode($this->request->getPost('ticket_data'), true);
            $attendeeInfoRaw = json_decode($this->request->getPost('attendee_info'), true);
            $email = $this->request->getPost('email');
            $phone = $this->request->getPost('phone');
            
            // Transform attendee_info from {names: [...], emails: [...]} to [{name: ..., email: ...}, ...]
            $attendeeInfo = [];
            if (!empty($attendeeInfoRaw) && isset($attendeeInfoRaw['names']) && isset($attendeeInfoRaw['emails'])) {
                $names = $attendeeInfoRaw['names'] ?? [];
                $emails = $attendeeInfoRaw['emails'] ?? [];
                $maxIndex = max(count($names), count($emails));
                
                for ($i = 0; $i < $maxIndex; $i++) {
                    $attendeeInfo[] = [
                        'name' => $names[$i] ?? $email,
                        'email' => $emails[$i] ?? $email
                    ];
                }
            }

            if (empty($ticketData)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Please select at least one ticket'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Check availability
            foreach ($ticketData as $ticketTypeId => $quantity) {
                if ($quantity > 0) {
                    $availability = $this->eventService->checkAvailability($eventId, $ticketTypeId, $quantity);
                    if (!$availability['available']) {
                        return $this->response->setJSON([
                            'status' => 'error',
                            'message' => $availability['message']
                        ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
                    }
                }
            }

            // Calculate total
            $totalAmount = $this->eventService->calculateTotal($eventId, $ticketData);

            // Create booking
            $result = $this->ticketService->createBooking(
                $userId,
                $eventId,
                $ticketData,
                $attendeeInfo,
                $email,
                $phone
            );

            if ($result['status'] != 'success') {
                return $this->response->setJSON($result)->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Send notifications after successful booking
            try {
                $eventModel = new \App\Models\EventModel();
                $event = $eventModel->find($eventId);
                $eventTitle = $event ? $event['title'] : 'Event';
                
                $userModel = model('UserModel');
                $user = $userId ? $userModel->find($userId) : null;
                $userName = $user ? ($user['first_name'] . ' ' . $user['last_name']) : ($email ?? 'Guest');

                $bookingNumber = $result['booking_number'] ?? '';
                $bookingId = $result['booking_id'];
                $paymentStatus = $result['payment_status'] ?? ($totalAmount > 0 ? 'pending' : 'paid');
                $paymentStatusText = $paymentStatus === 'paid' ? 'Payment completed' : 'Payment pending';

                $notificationService = new \App\Services\NotificationService();
                
                // Notify user about booking (if logged in)
                if ($userId) {
                    $notificationService->notifyEventBooking($userId, $eventTitle, $bookingNumber, $bookingId, $paymentStatusText);
                }

                // Notify admins about new booking
                $adminUsers = $userModel->getAdministrators();
                if (!empty($adminUsers)) {
                    $adminIds = array_column($adminUsers, 'id');
                    $notificationService->notifyAdminEventBooking($adminIds, $bookingNumber, $eventTitle, $userName, $bookingId);
                }
            } catch (\Exception $notificationError) {
                log_message('error', "Error sending event booking notifications: " . $notificationError->getMessage());
                // Don't fail booking if notification fails
            }

            // If free event, generate tickets and send email
            if ($totalAmount == 0) {
                $this->ticketService->sendTicketsByEmail($result['booking_id']);
            }

            // If paid event, return payment details
            if ($totalAmount > 0) {
                $paymentData = $this->paymentService->initiateEventPayment($userId, $result['booking_id'], $email);
                return $this->response->setJSON([
                    'status' => 'success',
                    'booking_id' => $result['booking_id'],
                    'payment_required' => true,
                    'payment_data' => $paymentData
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'booking_id' => $result['booking_id'],
                'redirect_url' => site_url('events/booking/' . $result['booking_id'] . '/success')
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Booking processing error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to process booking: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Verify payment
     */
    public function verifyPayment()
    {
        $reference = $this->request->getPost('reference');
        
        if (empty($reference)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Payment reference is required'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $result = $this->paymentService->verifyEventPayment($reference);

        if ($result['status'] == 'success') {
            // Send tickets via email
            $this->ticketService->sendTicketsByEmail($result['booking_id']);
        }

        return $this->response->setJSON($result);
    }

    /**
     * View tickets for a booking
     */
    public function tickets($bookingId)
    {
        $userId = ClientAuth::getId();
        $booking = $this->bookingModel->find($bookingId);

        if (!$booking) {
            return redirect()->to('events')->with('error', 'Booking not found');
        }

        // Check if user owns this booking
        if ($userId && $booking['user_id'] != $userId) {
            return redirect()->to('events')->with('error', 'Unauthorized access');
        }

        $event = $this->eventModel->find($booking['event_id']);
        $tickets = $this->ticketModel->getBookingTickets($bookingId);

        // Get ticket type names for each ticket
        $ticketTypeModel = new \App\Models\EventTicketTypeModel();
        foreach ($tickets as &$ticket) {
            if (!empty($ticket['ticket_type_id'])) {
                $ticketType = $ticketTypeModel->find($ticket['ticket_type_id']);
                $ticket['ticket_type_name'] = $ticketType ? $ticketType['name'] : null;
            }
        }
        unset($ticket);

        // Format event image URLs
        if (!empty($event['image_url'])) {
            $imagePath = $event['image_url'];
            $imagePath = trim($imagePath);
            
            if (strpos($imagePath, 'http') === 0) {
                $parsedUrl = parse_url($imagePath);
                if (isset($parsedUrl['path'])) {
                    $imagePath = ltrim($parsedUrl['path'], '/');
                }
            }
            
            $imagePath = ltrim($imagePath, '/');
            $event['image_url'] = base_url($imagePath);
        } else {
            $event['image_url'] = base_url('assets/images/default-blog.jpg');
        }

        $data = [
            'title' => 'My Tickets - KEWASNET',
            'description' => 'View and download your event tickets',
            'booking' => $booking,
            'event' => $event,
            'tickets' => $tickets,
        ];

        return view('frontendV2/website/pages/events/tickets', $data);
    }

    /**
     * Download ticket PDF
     */
    public function downloadTicket($ticketId)
    {
        $userId = ClientAuth::getId();
        $ticket = $this->ticketModel->find($ticketId);

        if (!$ticket) {
            return redirect()->to('events')->with('error', 'Ticket not found');
        }

        $booking = $this->bookingModel->find($ticket['booking_id']);
        if ($userId && $booking['user_id'] != $userId) {
            return redirect()->to('events')->with('error', 'Unauthorized access');
        }

        $event = $this->eventModel->find($booking['event_id']);

        $ticketGenerator = new TicketGenerator();
        $pdfContent = $ticketGenerator->generatePDFForDownload($ticket, $event, $booking);

        $filename = 'ticket_' . $ticket['ticket_number'] . '.pdf';

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($pdfContent);
    }

    /**
     * Booking success page
     */
    public function bookingSuccess($bookingId)
    {
        $userId = ClientAuth::getId();
        $booking = $this->bookingModel->find($bookingId);

        if (!$booking) {
            return redirect()->to('events')->with('error', 'Booking not found');
        }

        if ($userId && $booking['user_id'] != $userId) {
            return redirect()->to('events')->with('error', 'Unauthorized access');
        }

        $event = $this->eventModel->find($booking['event_id']);

        // Format event image URLs
        if (!empty($event['image_url'])) {
            $imagePath = $event['image_url'];
            $imagePath = trim($imagePath);
            
            if (strpos($imagePath, 'http') === 0) {
                $parsedUrl = parse_url($imagePath);
                if (isset($parsedUrl['path'])) {
                    $imagePath = ltrim($parsedUrl['path'], '/');
                }
            }
            
            $imagePath = ltrim($imagePath, '/');
            $event['image_url'] = base_url($imagePath);
        } else {
            // Try banner_url as fallback
            if (!empty($event['banner_url'])) {
                $bannerPath = $event['banner_url'];
                $bannerPath = trim($bannerPath);
                
                if (strpos($bannerPath, 'http') === 0) {
                    $parsedUrl = parse_url($bannerPath);
                    if (isset($parsedUrl['path'])) {
                        $bannerPath = ltrim($parsedUrl['path'], '/');
                    }
                }
                
                $bannerPath = ltrim($bannerPath, '/');
                $event['image_url'] = base_url($bannerPath);
            } else {
                $event['image_url'] = base_url('hero.png');
            }
        }
        
        if (!empty($event['banner_url'])) {
            $bannerPath = $event['banner_url'];
            $bannerPath = trim($bannerPath);
            
            if (strpos($bannerPath, 'http') === 0) {
                $parsedUrl = parse_url($bannerPath);
                if (isset($parsedUrl['path'])) {
                    $bannerPath = ltrim($parsedUrl['path'], '/');
                }
            }
            
            $bannerPath = ltrim($bannerPath, '/');
            $event['banner_url'] = base_url($bannerPath);
        }

        $data = [
            'title' => 'Booking Confirmed - KEWASNET',
            'description' => 'Your booking has been confirmed successfully',
            'booking' => $booking,
            'event' => $event,
        ];

        return view('frontendV2/website/pages/events/booking-success', $data);
    }

    /**
     * Resend tickets via email
     */
    public function resendTickets($bookingId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $userId = ClientAuth::getId();
        $booking = $this->bookingModel->find($bookingId);

        if (!$booking || ($userId && $booking['user_id'] != $userId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        $result = $this->ticketService->sendTicketsByEmail($bookingId);

        return $this->response->setJSON($result);
    }
}

