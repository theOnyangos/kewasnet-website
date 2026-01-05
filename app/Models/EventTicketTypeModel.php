<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class EventTicketTypeModel extends Model
{
    protected $table            = 'event_ticket_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'event_id',
        'name',
        'description',
        'price',
        'quantity',
        'sales_start_date',
        'sales_end_date',
        'status',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'event_id' => 'required|max_length[36]',
        'name' => 'required|max_length[255]',
        'price' => 'permit_empty|decimal',
        'status' => 'required|in_list[active,inactive]',
    ];

    protected $beforeInsert = ['generateUUID'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    /**
     * Get ticket types for an event
     */
    public function getEventTicketTypes($eventId)
    {
        return $this->where('event_id', $eventId)
            ->where('status', 'active')
            ->where('deleted_at', null)
            ->orderBy('price', 'ASC')
            ->findAll();
    }
}

