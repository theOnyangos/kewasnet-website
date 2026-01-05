<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class EventBookingModel extends Model
{
    protected $table            = 'event_bookings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'event_id',
        'user_id',
        'booking_number',
        'total_amount',
        'payment_status',
        'payment_reference',
        'order_id',
        'status',
        'email',
        'phone',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'event_id' => 'required|max_length[36]',
        'booking_number' => 'permit_empty|max_length[50]|is_unique[event_bookings.booking_number,id,{id}]',
        'email' => 'required|valid_email|max_length[255]',
        'payment_status' => 'required|in_list[pending,paid,failed,refunded]',
        'status' => 'required|in_list[confirmed,cancelled]',
    ];

    protected $beforeInsert = ['generateUUID', 'generateBookingNumber'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    protected function generateBookingNumber(array $data)
    {
        if (!isset($data['data']['booking_number'])) {
            $data['data']['booking_number'] = 'EVT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
        }
        return $data;
    }

    /**
     * Get bookings for an event
     */
    public function getEventBookings($eventId)
    {
        return $this->where('event_id', $eventId)
            ->where('deleted_at', null)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get user bookings
     */
    public function getUserBookings($userId)
    {
        return $this->where('user_id', $userId)
            ->where('deleted_at', null)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get booking by booking number
     */
    public function findByBookingNumber($bookingNumber)
    {
        return $this->where('booking_number', $bookingNumber)->first();
    }
}

