<?php

namespace App\Controllers\BackendV2;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\EventTicketTypeModel;
use App\Models\EventBookingModel;
use App\Models\EventTicketModel;
use App\Services\DataTableService;
use App\Services\EventService;
use App\Services\TicketService;
use CodeIgniter\HTTP\ResponseInterface;

class EventsController extends BaseController
{
    protected const PAGE_TITLE = "Manage Events - KEWASNET";
    
    protected $eventModel;
    protected $ticketTypeModel;
    protected $bookingModel;
    protected $ticketModel;
    protected $dataTableService;
    protected $eventService;
    protected $ticketService;

    public function __construct()
    {
        $this->eventModel = new EventModel();
        $this->ticketTypeModel = new EventTicketTypeModel();
        $this->bookingModel = new EventBookingModel();
        $this->ticketModel = new EventTicketModel();
        $this->dataTableService = new DataTableService();
        $this->eventService = new EventService();
        $this->ticketService = new TicketService();
        helper(['url', 'text', 'form']);
    }

    /**
     * Display events listing page
     */
    public function index()
    {
        // Calculate event statistics
        $eventStats = $this->calculateEventStats();

        return view('backendV2/pages/events/index', [
            'title' => self::PAGE_TITLE,
            'dashboardTitle' => 'Events Management',
            'eventStats' => $eventStats
        ]);
    }

    /**
     * Calculate event statistics
     */
    private function calculateEventStats()
    {
        // Total events
        $totalEvents = $this->eventModel->where('deleted_at', null)->countAllResults(false);

        // Published events
        $publishedEvents = $this->eventModel->where('status', 'published')
            ->where('deleted_at', null)
            ->countAllResults(false);

        // Draft events
        $draftEvents = $this->eventModel->where('status', 'draft')
            ->where('deleted_at', null)
            ->countAllResults(false);

        // Cancelled events
        $cancelledEvents = $this->eventModel->where('status', 'cancelled')
            ->where('deleted_at', null)
            ->countAllResults(false);

        // Paid events
        $paidEvents = $this->eventModel->where('event_type', 'paid')
            ->where('deleted_at', null)
            ->countAllResults(false);

        // Free events
        $freeEvents = $this->eventModel->where('event_type', 'free')
            ->where('deleted_at', null)
            ->countAllResults(false);

        // Total bookings
        $totalBookings = $this->bookingModel->where('deleted_at', null)->countAllResults(false);

        // Total tickets sold
        $totalTicketsSold = $this->ticketModel->countAllResults(false);

        // Total revenue from paid bookings
        $totalRevenue = $this->bookingModel->selectSum('total_amount')
            ->where('payment_status', 'paid')
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();
        $totalRevenue = $totalRevenue['total_amount'] ?? 0;

        // Upcoming events (events with start_date in the future)
        $upcomingEvents = $this->eventModel->where('start_date >=', date('Y-m-d'))
            ->where('deleted_at', null)
            ->countAllResults(false);

        return [
            'total_events' => $totalEvents,
            'published_events' => $publishedEvents,
            'draft_events' => $draftEvents,
            'cancelled_events' => $cancelledEvents,
            'paid_events' => $paidEvents,
            'free_events' => $freeEvents,
            'total_bookings' => $totalBookings,
            'total_tickets_sold' => $totalTicketsSold,
            'total_revenue' => $totalRevenue,
            'upcoming_events' => $upcomingEvents
        ];
    }

    /**
     * Get events data for DataTables
     */
    public function getEvents()
    {
        $request = service('request');
        $draw = $request->getPost('draw') ?? 1;
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $searchValue = $request->getPost('search')['value'] ?? null;
        $orderData = $request->getPost('order');
        $orderColumnIndex = !empty($orderData[0]['column']) ? (int)$orderData[0]['column'] : 5;
        $orderDir = !empty($orderData[0]['dir']) ? $orderData[0]['dir'] : 'DESC';

        $columns = ['title', 'event_type', 'start_date', 'venue', 'status', 'created_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';

        $builder = $this->eventModel->builder()
            ->where('deleted_at', null);

        // Count total records
        $totalRecords = $builder->countAllResults(false);

        // Apply search
        if ($searchValue) {
            $builder->groupStart()
                ->like('title', $searchValue)
                ->orLike('venue', $searchValue)
                ->orLike('description', $searchValue)
                ->groupEnd();
        }

        // Count filtered records
        $filteredRecords = $builder->countAllResults(false);

        // Apply ordering
        $builder->orderBy($orderColumn, $orderDir);

        // Apply pagination
        $builder->limit($length, $start);

        // Get data
        $events = $builder->get()->getResultArray();

        // Format data
        $data = [];
        foreach ($events as $event) {
            $data[] = [
                'id' => $event['id'],
                'title' => $event['title'],
                'event_type' => ucfirst($event['event_type']),
                'start_date' => $event['start_date'] ? date('M d, Y', strtotime($event['start_date'])) : 'N/A',
                'venue' => $event['venue'] ?? 'N/A',
                'status' => ucfirst($event['status']),
                'created_at' => $event['created_at'] ? date('M d, Y H:i', strtotime($event['created_at'])) : 'N/A',
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    /**
     * Show create event form
     */
    public function create()
    {
        return view('backendV2/pages/events/create', [
            'title' => 'Create New Event - KEWASNET',
            'dashboardTitle' => 'Create New Event'
        ]);
    }

    /**
     * Handle event creation
     */
    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $eventData = $this->request->getPost();
            $files = $this->request->getFiles();

            // Validate
            $rules = [
                'title' => 'required|min_length[3]|max_length[255]',
                'event_type' => 'required|in_list[paid,free]',
                'start_date' => 'required|valid_date',
                'status' => 'required|in_list[draft,published,cancelled]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $this->validator->getErrors()
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Generate slug if not provided
            if (empty($eventData['slug'])) {
                $eventData['slug'] = url_title($eventData['title'], '-', true);
                // Ensure uniqueness
                $existing = $this->eventModel->where('slug', $eventData['slug'])->first();
                if ($existing) {
                    $eventData['slug'] = $eventData['slug'] . '-' . time();
                }
            }

            // Handle file uploads
            if (isset($files['image']) && $files['image']->isValid() && !$files['image']->hasMoved()) {
                $image = $files['image'];
                $newName = $image->getRandomName();
                $uploadPath = WRITEPATH . '../public/uploads/events/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $newName);
                $eventData['image_url'] = 'uploads/events/' . $newName;
            }

            if (isset($files['banner']) && $files['banner']->isValid() && !$files['banner']->hasMoved()) {
                $banner = $files['banner'];
                $newName = $banner->getRandomName();
                $uploadPath = WRITEPATH . '../public/uploads/events/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $banner->move($uploadPath, $newName);
                $eventData['banner_url'] = 'uploads/events/' . $newName;
            }

            // Insert event
            $eventId = $this->eventModel->insert($eventData);

            if (!$eventId) {
                throw new \Exception('Failed to create event');
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Event created successfully',
                'data' => ['event_id' => $eventId],
                'redirect_url' => site_url('auth/events/edit/' . $eventId)
            ])->setStatusCode(ResponseInterface::HTTP_CREATED);

        } catch (\Exception $e) {
            log_message('error', 'Event creation error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to create event: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show edit event form
     */
    public function edit($eventId)
    {
        $event = $this->eventModel->find($eventId);

        if (!$event) {
            return redirect()->to('auth/events')->with('error', 'Event not found');
        }

        // Get ticket types
        $ticketTypes = $this->ticketTypeModel->where('event_id', $eventId)
            ->where('deleted_at', null)
            ->findAll();

        return view('backendV2/pages/events/edit', [
            'title' => 'Edit Event - KEWASNET',
            'dashboardTitle' => 'Edit Event',
            'event' => $event,
            'ticketTypes' => $ticketTypes
        ]);
    }

    /**
     * Handle event update
     */
    public function update($eventId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $event = $this->eventModel->find($eventId);
            if (!$event) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Event not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            $eventData = $this->request->getPost();
            $files = $this->request->getFiles();

            // Validate
            $rules = [
                'title' => 'required|min_length[3]|max_length[255]',
                'slug' => 'required|max_length[255]|is_unique[events.slug,id,' . $eventId . ']',
                'event_type' => 'required|in_list[paid,free]',
                'start_date' => 'required|valid_date',
                'status' => 'required|in_list[draft,published,cancelled]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $this->validator->getErrors()
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Generate slug if title changed
            if ($eventData['title'] != $event['title']) {
                $eventData['slug'] = url_title($eventData['title'], '-', true);
                // Ensure uniqueness
                $existing = $this->eventModel->where('slug', $eventData['slug'])
                    ->where('id !=', $eventId)
                    ->first();
                if ($existing) {
                    $eventData['slug'] = $eventData['slug'] . '-' . time();
                }
            }
            
            // If slug is being updated, ensure it's unique
            if (isset($eventData['slug']) && $eventData['slug'] != $event['slug']) {
                $existing = $this->eventModel->where('slug', $eventData['slug'])
                    ->where('id !=', $eventId)
                    ->first();
                if ($existing) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Validation failed',
                        'errors' => ['slug' => 'The slug field must contain a unique value.']
                    ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
                }
            }

            // Handle file uploads
            if (isset($files['image']) && $files['image']->isValid() && !$files['image']->hasMoved()) {
                $image = $files['image'];
                $newName = $image->getRandomName();
                $uploadPath = WRITEPATH . '../public/uploads/events/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $image->move($uploadPath, $newName);
                $eventData['image_url'] = 'uploads/events/' . $newName;
            }

            if (isset($files['banner']) && $files['banner']->isValid() && !$files['banner']->hasMoved()) {
                $banner = $files['banner'];
                $newName = $banner->getRandomName();
                $uploadPath = WRITEPATH . '../public/uploads/events/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $banner->move($uploadPath, $newName);
                $eventData['banner_url'] = 'uploads/events/' . $newName;
            }

            // Filter to only include allowed fields and handle empty values
            $allowedFields = [
                'title', 'slug', 'description', 'event_type', 'start_date', 'end_date',
                'start_time', 'end_time', 'venue', 'address', 'city', 'country',
                'image_url', 'banner_url', 'total_capacity', 'status'
            ];
            
            // Fields that can be null/empty
            $nullableFields = ['end_date', 'start_time', 'end_time', 'venue', 'address', 'city', 'country', 'total_capacity', 'image_url', 'banner_url'];
            
            $updateData = [];
            foreach ($allowedFields as $field) {
                if (isset($eventData[$field])) {
                    $value = $eventData[$field];
                    // Convert empty strings to null for nullable fields
                    if (in_array($field, $nullableFields) && $value === '') {
                        $updateData[$field] = null;
                    } else {
                        $updateData[$field] = $value;
                    }
                }
            }

            // Log the data being updated for debugging
            log_message('debug', 'Updating event ' . $eventId . ' with data: ' . json_encode($updateData));

            // Skip model validation since we've already validated
            $this->eventModel->skipValidation(true);
            
            // Update event
            $updated = $this->eventModel->update($eventId, $updateData);
            
            // Re-enable validation
            $this->eventModel->skipValidation(false);
            
            if (!$updated) {
                $errors = $this->eventModel->errors();
                log_message('error', 'Event update failed: ' . json_encode($errors));
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to update event',
                    'errors' => $errors ?: ['general' => 'Update operation failed']
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Event updated successfully',
                'redirect_url' => site_url('auth/events/edit/' . $eventId)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Event update error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update event: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete event (soft delete)
     */
    public function delete($eventId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $event = $this->eventModel->find($eventId);
            if (!$event) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Event not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            $this->eventModel->delete($eventId);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Event deleted successfully'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Event deletion error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to delete event: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display ticket types management page
     */
    public function ticketTypes()
    {
        // Calculate ticket type statistics
        $ticketTypeStats = $this->calculateTicketTypeStats();

        return view('backendV2/pages/events/ticket-types', [
            'title' => 'Ticket Types Management - KEWASNET',
            'dashboardTitle' => 'Ticket Types Management',
            'ticketTypeStats' => $ticketTypeStats
        ]);
    }

    /**
     * Calculate ticket type statistics
     */
    private function calculateTicketTypeStats()
    {
        $ticketTypeModel = $this->ticketTypeModel;
        $ticketModel = $this->ticketModel;
        $bookingModel = $this->bookingModel;

        // Total ticket types
        $totalTicketTypes = $ticketTypeModel->where('deleted_at', null)->countAllResults(false);

        // Active ticket types
        $activeTicketTypes = $ticketTypeModel->where('status', 'active')
            ->where('deleted_at', null)
            ->countAllResults(false);

        // Inactive ticket types
        $inactiveTicketTypes = $ticketTypeModel->where('status', 'inactive')
            ->where('deleted_at', null)
            ->countAllResults(false);

        // Total tickets sold (from event_tickets table)
        $totalTicketsSold = $ticketModel->countAllResults(false);

        // Total tickets sold by active ticket types
        $db = \Config\Database::connect();
        $activeTicketsSold = $db->table('event_tickets')
            ->join('event_ticket_types', 'event_ticket_types.id = event_tickets.ticket_type_id', 'inner')
            ->where('event_ticket_types.status', 'active')
            ->where('event_ticket_types.deleted_at', null)
            ->countAllResults(false);

        // Total revenue from paid bookings
        $totalRevenue = $bookingModel->selectSum('total_amount')
            ->where('payment_status', 'paid')
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();
        $totalRevenue = $totalRevenue['total_amount'] ?? 0;

        // Tickets checked in
        $ticketsCheckedIn = $ticketModel->where('status', 'used')
            ->where('checked_in_at IS NOT NULL')
            ->countAllResults(false);

        return [
            'total_ticket_types' => $totalTicketTypes,
            'active_ticket_types' => $activeTicketTypes,
            'inactive_ticket_types' => $inactiveTicketTypes,
            'total_tickets_sold' => $totalTicketsSold,
            'active_tickets_sold' => $activeTicketsSold,
            'total_revenue' => $totalRevenue,
            'tickets_checked_in' => $ticketsCheckedIn
        ];
    }

    /**
     * Show create ticket type form
     */
    public function createTicketType()
    {
        // Get all events for dropdown
        $events = $this->eventModel->where('deleted_at', null)
            ->orderBy('title', 'ASC')
            ->findAll();

        return view('backendV2/pages/events/ticket-type-form', [
            'title' => 'Create Ticket Type - KEWASNET',
            'dashboardTitle' => 'Create Ticket Type',
            'events' => $events
        ]);
    }

    /**
     * Show edit ticket type form
     */
    public function editTicketType($ticketTypeId)
    {
        $ticketType = $this->ticketTypeModel->find($ticketTypeId);
        
        if (!$ticketType) {
            return redirect()->to('auth/events/ticket-types')->with('error', 'Ticket type not found');
        }

        // Get all events for dropdown
        $events = $this->eventModel->where('deleted_at', null)
            ->orderBy('title', 'ASC')
            ->findAll();

        return view('backendV2/pages/events/ticket-type-form', [
            'title' => 'Edit Ticket Type - KEWASNET',
            'dashboardTitle' => 'Edit Ticket Type',
            'ticketType' => $ticketType,
            'events' => $events
        ]);
    }

    /**
     * Get ticket types data for DataTables
     */
    public function getTicketTypes()
    {
        $request = service('request');
        $draw = $request->getPost('draw') ?? 1;
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $searchValue = $request->getPost('search')['value'] ?? null;
        $orderData = $request->getPost('order');
        $orderColumnIndex = !empty($orderData[0]['column']) ? (int)$orderData[0]['column'] : 6;
        $orderDir = !empty($orderData[0]['dir']) ? $orderData[0]['dir'] : 'DESC';

        $columns = ['events.title', 'event_ticket_types.name', 'event_ticket_types.price', 'event_ticket_types.quantity', 'event_ticket_types.sales_start_date', 'event_ticket_types.status', 'event_ticket_types.created_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'event_ticket_types.created_at';

        $builder = $this->ticketTypeModel->builder()
            ->select('event_ticket_types.*, events.title as event_title')
            ->join('events', 'events.id = event_ticket_types.event_id', 'left')
            ->where('event_ticket_types.deleted_at', null);

        // Count total records
        $totalRecords = $builder->countAllResults(false);

        // Apply search
        if ($searchValue) {
            $builder->groupStart()
                ->like('event_ticket_types.name', $searchValue)
                ->orLike('events.title', $searchValue)
                ->orLike('event_ticket_types.description', $searchValue)
                ->groupEnd();
        }

        // Count filtered records
        $filteredRecords = $builder->countAllResults(false);

        // Apply ordering
        $builder->orderBy($orderColumn, $orderDir);

        // Apply pagination
        $builder->limit($length, $start);

        // Get data
        $ticketTypes = $builder->get()->getResultArray();

        // Format data
        $data = [];
        foreach ($ticketTypes as $ticketType) {
            $salesPeriod = '';
            if (!empty($ticketType['sales_start_date']) && !empty($ticketType['sales_end_date'])) {
                $salesPeriod = date('M d, Y', strtotime($ticketType['sales_start_date'])) . ' - ' . date('M d, Y', strtotime($ticketType['sales_end_date']));
            } elseif (!empty($ticketType['sales_start_date'])) {
                $salesPeriod = 'From ' . date('M d, Y', strtotime($ticketType['sales_start_date']));
            } elseif (!empty($ticketType['sales_end_date'])) {
                $salesPeriod = 'Until ' . date('M d, Y', strtotime($ticketType['sales_end_date']));
            } else {
                $salesPeriod = 'Always available';
            }

            $data[] = [
                'id' => $ticketType['id'],
                'event_title' => $ticketType['event_title'] ?? 'N/A',
                'name' => $ticketType['name'],
                'price' => $ticketType['price'] ?? 0,
                'quantity' => $ticketType['quantity'],
                'sales_period' => $salesPeriod,
                'status' => ucfirst($ticketType['status']),
                'created_at' => $ticketType['created_at'] ? date('M d, Y H:i', strtotime($ticketType['created_at'])) : 'N/A',
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    /**
     * Get single ticket type
     */
    public function getTicketType($ticketTypeId)
    {
        $ticketType = $this->ticketTypeModel->find($ticketTypeId);
        
        if (!$ticketType) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Ticket type not found'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $ticketType
        ]);
    }

    /**
     * Store ticket type
     */
    public function storeTicketType()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $ticketData = $this->request->getPost();

            // Validate
            $rules = [
                'event_id' => 'required|max_length[36]',
                'name' => 'required|min_length[3]|max_length[255]',
                'price' => 'permit_empty|decimal',
                'status' => 'required|in_list[active,inactive]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $this->validator->getErrors()
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Convert empty quantity to null
            if (empty($ticketData['quantity'])) {
                $ticketData['quantity'] = null;
            }

            // Insert ticket type
            $ticketTypeId = $this->ticketTypeModel->insert($ticketData);

            if (!$ticketTypeId) {
                throw new \Exception('Failed to create ticket type');
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Ticket type created successfully'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Ticket type creation error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to create ticket type: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update ticket type
     */
    public function updateTicketType($ticketTypeId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $ticketType = $this->ticketTypeModel->find($ticketTypeId);
            if (!$ticketType) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Ticket type not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            $ticketData = $this->request->getPost();

            // Validate
            $rules = [
                'event_id' => 'required|max_length[36]',
                'name' => 'required|min_length[3]|max_length[255]',
                'price' => 'permit_empty|decimal',
                'status' => 'required|in_list[active,inactive]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $this->validator->getErrors()
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Convert empty quantity to null
            if (empty($ticketData['quantity'])) {
                $ticketData['quantity'] = null;
            }

            // Update ticket type
            $this->ticketTypeModel->update($ticketTypeId, $ticketData);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Ticket type updated successfully'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Ticket type update error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update ticket type: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete ticket type
     */
    public function deleteTicketType($ticketTypeId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $ticketType = $this->ticketTypeModel->find($ticketTypeId);
            if (!$ticketType) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Ticket type not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            $this->ticketTypeModel->delete($ticketTypeId);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Ticket type deleted successfully'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Ticket type deletion error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to delete ticket type: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display all bookings management page
     */
    public function allBookings()
    {
        return view('backendV2/pages/events/bookings', [
            'title' => 'Bookings Management - KEWASNET',
            'dashboardTitle' => 'Bookings Management'
        ]);
    }

    /**
     * Get bookings data for DataTables
     */
    public function getBookings()
    {
        $request = service('request');
        $draw = $request->getPost('draw') ?? 1;
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $searchValue = $request->getPost('search')['value'] ?? null;
        $orderData = $request->getPost('order');
        $orderColumnIndex = !empty($orderData[0]['column']) ? (int)$orderData[0]['column'] : 7;
        $orderDir = !empty($orderData[0]['dir']) ? $orderData[0]['dir'] : 'DESC';
        
        // Get event_id from POST data if provided (for filtering by specific event)
        $eventId = $request->getPost('event_id') ?? null;

        $columns = ['event_bookings.booking_number', 'events.title', 'event_bookings.email', 'event_bookings.total_amount', 'event_bookings.payment_status', 'event_bookings.status', 'event_bookings.created_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'event_bookings.created_at';

        $builder = $this->bookingModel->builder()
            ->select('event_bookings.*, events.title as event_title, CONCAT(system_users.first_name, " ", system_users.last_name) as customer_name')
            ->join('events', 'events.id = event_bookings.event_id', 'left')
            ->join('system_users', 'system_users.id = event_bookings.user_id', 'left')
            ->where('event_bookings.deleted_at', null);
        
        // Filter by event_id if provided
        if ($eventId) {
            $builder->where('event_bookings.event_id', $eventId);
        }

        // Count total records
        $totalRecords = $builder->countAllResults(false);

        // Apply search
        if ($searchValue) {
            $builder->groupStart()
                ->like('event_bookings.booking_number', $searchValue)
                ->orLike('events.title', $searchValue)
                ->orLike('event_bookings.email', $searchValue)
                ->orLike('system_users.first_name', $searchValue)
                ->orLike('system_users.last_name', $searchValue)
                ->groupEnd();
        }

        // Count filtered records
        $filteredRecords = $builder->countAllResults(false);

        // Apply ordering
        $builder->orderBy($orderColumn, $orderDir);

        // Apply pagination
        $builder->limit($length, $start);

        // Get data
        $bookings = $builder->get()->getResultArray();

        // Format data
        $data = [];
        foreach ($bookings as $booking) {
            $data[] = [
                'id' => $booking['id'],
                'booking_number' => $booking['booking_number'],
                'event_title' => $booking['event_title'] ?? 'N/A',
                'customer_name' => $booking['customer_name'] ?? 'N/A',
                'email' => $booking['email'] ?? 'N/A',
                'total_amount' => $booking['total_amount'] ?? 0,
                'payment_status' => $booking['payment_status'] ?? 'pending',
                'status' => $booking['status'] ?? 'pending',
                'created_at' => $booking['created_at'] ? date('M d, Y H:i', strtotime($booking['created_at'])) : 'N/A',
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    /**
     * View bookings for an event
     */
    public function bookings($eventId)
    {
        $event = $this->eventModel->find($eventId);
        if (!$event) {
            return redirect()->to('auth/events')->with('error', 'Event not found');
        }

        $bookings = $this->bookingModel->getEventBookings($eventId);

        return view('backendV2/pages/events/bookings', [
            'title' => 'Event Bookings - KEWASNET',
            'dashboardTitle' => 'Event Bookings',
            'event' => $event,
            'bookings' => $bookings
        ]);
    }

    /**
     * View booking details
     */
    public function viewBooking($bookingId)
    {
        $booking = $this->bookingModel->find($bookingId);
        
        if (!$booking) {
            return redirect()->to('auth/events/bookings')->with('error', 'Booking not found');
        }

        // Get event details
        $event = $this->eventModel->find($booking['event_id']);
        if (!$event) {
            return redirect()->to('auth/events/bookings')->with('error', 'Event not found');
        }

        // Get tickets for this booking
        $tickets = $this->ticketModel->where('booking_id', $bookingId)->findAll();
        
        // Get ticket type names for each ticket
        foreach ($tickets as &$ticket) {
            if (!empty($ticket['ticket_type_id'])) {
                $ticketType = $this->ticketTypeModel->find($ticket['ticket_type_id']);
                $ticket['ticket_type_name'] = $ticketType ? $ticketType['name'] : 'N/A';
            }
        }
        unset($ticket);

        // Get user details if user_id exists
        $user = null;
        if (!empty($booking['user_id'])) {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find($booking['user_id']);
            if ($user) {
                $user['full_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
            }
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

        return view('backendV2/pages/events/booking-details', [
            'title' => 'Booking Details - KEWASNET',
            'dashboardTitle' => 'Booking Details',
            'booking' => $booking,
            'event' => $event,
            'tickets' => $tickets,
            'user' => $user
        ]);
    }

    /**
     * Invalidate a ticket
     */
    public function invalidateTicket($ticketId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ])->setStatusCode(400);
        }

        $ticket = $this->ticketModel->find($ticketId);
        
        if (!$ticket) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Ticket not found'
            ])->setStatusCode(404);
        }

        // Check if ticket is already cancelled
        if ($ticket['status'] === 'cancelled') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Ticket is already invalidated'
            ])->setStatusCode(400);
        }

        // Update ticket status to cancelled
        $updated = $this->ticketModel->update($ticketId, [
            'status' => 'cancelled'
        ]);

        if ($updated) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Ticket invalidated successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to invalidate ticket'
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete a ticket
     */
    public function deleteTicket($ticketId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ])->setStatusCode(400);
        }

        $ticket = $this->ticketModel->find($ticketId);
        
        if (!$ticket) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Ticket not found'
            ])->setStatusCode(404);
        }

        // Delete the ticket
        $deleted = $this->ticketModel->delete($ticketId);

        if ($deleted) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Ticket deleted successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to delete ticket'
            ])->setStatusCode(500);
        }
    }

    /**
     * Check-in interface
     */
    public function checkIn()
    {
        return view('backendV2/pages/events/check-in', [
            'title' => 'Event Check-In - KEWASNET',
            'dashboardTitle' => 'Event Check-In'
        ]);
    }

    /**
     * Cancel a booking (invalidates all tickets)
     */
    public function cancelBooking($bookingId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ])->setStatusCode(400);
        }

        $booking = $this->bookingModel->find($bookingId);
        
        if (!$booking) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking not found'
            ])->setStatusCode(404);
        }

        // Check if booking is already cancelled
        if ($booking['status'] === 'cancelled') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking is already cancelled'
            ])->setStatusCode(400);
        }

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update booking status to cancelled
            $this->bookingModel->update($bookingId, [
                'status' => 'cancelled'
            ]);

            // Invalidate all tickets for this booking
            $this->ticketModel->where('booking_id', $bookingId)
                ->where('status', 'active')
                ->set(['status' => 'cancelled'])
                ->update();

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to cancel booking'
                ])->setStatusCode(500);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Booking cancelled and all tickets invalidated successfully'
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete a booking (soft delete)
     */
    public function deleteBooking($bookingId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ])->setStatusCode(400);
        }

        $booking = $this->bookingModel->find($bookingId);
        
        if (!$booking) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking not found'
            ])->setStatusCode(404);
        }

        // Soft delete the booking (tickets remain but booking is deleted)
        $deleted = $this->bookingModel->delete($bookingId);

        if ($deleted) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Booking deleted successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to delete booking'
            ])->setStatusCode(500);
        }
    }

    /**
     * Verify and check-in ticket via QR code
     */
    public function verifyTicket()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            $qrCodeData = $this->request->getPost('qr_code_data');

            if (empty($qrCodeData)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'QR code data is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $result = $this->ticketService->verifyTicket($qrCodeData);

            if (!$result['valid']) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $result['message'],
                    'already_used' => $result['already_used'] ?? false
                ]);
            }

            // Check in the ticket
            $userId = session()->get('id');
            $this->ticketModel->checkIn($result['ticket']['id'], $userId);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Ticket checked in successfully',
                'ticket' => $result['ticket'],
                'booking' => $result['booking'],
                'event' => $result['event']
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Ticket verification error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to verify ticket: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

