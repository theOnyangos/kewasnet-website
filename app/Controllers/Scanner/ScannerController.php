<?php

namespace App\Controllers\Scanner;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Services\TicketService;
use CodeIgniter\HTTP\ResponseInterface;

class ScannerController extends BaseController
{
    public function index()
    {
        return view('scanner/index', [
            'title' => 'Ticket Scanner',
            'scannerUserName' => session()->get('scanner_user_name'),
        ]);
    }

    /**
     * Return events for event-selection dropdown (published + upcoming).
     */
    public function events()
    {
        $eventModel = new EventModel();

        $events = $eventModel
            ->where('status', 'published')
            ->where('deleted_at', null)
            // Upcoming or ongoing events (end_date >= today OR start_date >= today)
            ->groupStart()
                ->where('start_date >=', date('Y-m-d'))
                ->orWhere('end_date >=', date('Y-m-d'))
            ->groupEnd()
            ->orderBy('start_date', 'ASC')
            ->orderBy('start_time', 'ASC')
            ->findAll();

        $data = array_map(static function (array $e) {
            return [
                'id' => $e['id'] ?? '',
                'title' => $e['title'] ?? '',
                'start_date' => $e['start_date'] ?? null,
                'start_time' => $e['start_time'] ?? null,
                'venue' => $e['venue'] ?? null,
            ];
        }, $events);

        return $this->response->setJSON([
            'success' => true,
            'events' => $data,
        ])->setStatusCode(ResponseInterface::HTTP_OK);
    }

    /**
     * Verify QR code and optionally check-in (atomic, one-time).
     *
     * POST:
     * - qr_code_data (string)
     * - event_id (string)
     * - mode: "check" | "checkin"
     */
    public function verify()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request',
            ])->setStatusCode(ResponseInterface::HTTP_METHOD_NOT_ALLOWED);
        }

        $qr = trim((string) $this->request->getPost('qr_code_data'));
        $eventId = trim((string) $this->request->getPost('event_id'));
        $mode = strtolower(trim((string) $this->request->getPost('mode'))) ?: 'check';

        if ($eventId === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please select an event first.',
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        if ($qr === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'QR code data is required.',
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        if (!in_array($mode, ['check', 'checkin'], true)) {
            $mode = 'check';
        }

        $ticketService = new TicketService();
        $scannerUserId = (string) session()->get('scanner_user_id');

        $result = $ticketService->verifyTicketScoped($qr, $eventId, $mode, $scannerUserId);

        return $this->response->setJSON($result)->setStatusCode(ResponseInterface::HTTP_OK);
    }
}

