<?php

namespace App\Controllers\Scanner;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Services\TicketService;
use CodeIgniter\HTTP\ResponseInterface;
use Zxing\QrReader;

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

    /**
     * Decode an uploaded QR image on the server (PHP fallback for mobile).
     *
     * POST (multipart/form-data):
     * - qr_image (file)
     *
     * Response:
     * - success: bool
     * - text: string|null
     * - message: string|null
     */
    public function decodeImage()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request',
            ])->setStatusCode(ResponseInterface::HTTP_METHOD_NOT_ALLOWED);
        }

        $file = $this->request->getFile('qr_image');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please select a valid image.',
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Basic guardrails
        $mime = (string) $file->getMimeType();
        if (stripos($mime, 'image/') !== 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid file type. Please upload an image.',
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Move to a temp folder under writable/
        $dir = rtrim(WRITEPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'scanner';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $tmpName = 'qr_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . ($file->getExtension() ?: 'jpg');
        $path = $dir . DIRECTORY_SEPARATOR . $tmpName;

        try {
            $file->move($dir, $tmpName, true);

            // Decode using khanamiryan/qrcode-detector-decoder (Zxing\QrReader)
            $qr = new QrReader($path);
            $text = trim((string) $qr->text());

            if ($text === '') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No QR code detected in the image.',
                    'text' => null,
                ])->setStatusCode(ResponseInterface::HTTP_OK);
            }

            return $this->response->setJSON([
                'success' => true,
                'text' => $text,
            ])->setStatusCode(ResponseInterface::HTTP_OK);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to decode QR image. Ensure the server has GD enabled and the decoder library is installed.',
            ])->setStatusCode(ResponseInterface::HTTP_OK);
        } finally {
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }
}

