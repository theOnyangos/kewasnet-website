<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class EventTicketModel extends Model
{
    protected $table            = 'event_tickets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'booking_id',
        'ticket_type_id',
        'ticket_number',
        'qr_code_data',
        'attendee_name',
        'attendee_email',
        'status',
        'checked_in_at',
        'checked_in_by',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'booking_id' => 'required|max_length[36]',
        'ticket_type_id' => 'required|max_length[36]',
        'ticket_number' => 'permit_empty|max_length[100]|is_unique[event_tickets.ticket_number,id,{id}]',
        'attendee_name' => 'required|max_length[255]',
        'attendee_email' => 'required|valid_email|max_length[255]',
        'status' => 'required|in_list[active,used,cancelled]',
    ];

    protected $beforeInsert = ['generateUUID', 'generateTicketNumber'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    protected function generateTicketNumber(array $data)
    {
        if (!isset($data['data']['ticket_number'])) {
            $data['data']['ticket_number'] = 'TKT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -10));
        }
        return $data;
    }

    /**
     * Get tickets for a booking
     */
    public function getBookingTickets($bookingId)
    {
        return $this->where('booking_id', $bookingId)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    /**
     * Get ticket by ticket number
     */
    public function findByTicketNumber($ticketNumber)
    {
        return $this->where('ticket_number', $ticketNumber)->first();
    }

    /**
     * Verify ticket by QR code data
     */
    public function verifyByQrCode($qrCodeData)
    {
        return $this->where('qr_code_data', $qrCodeData)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Check in ticket
     */
    public function checkIn($ticketId, $checkedInBy)
    {
        return $this->update($ticketId, [
            'status' => 'used',
            'checked_in_at' => date('Y-m-d H:i:s'),
            'checked_in_by' => $checkedInBy,
        ]);
    }
}

