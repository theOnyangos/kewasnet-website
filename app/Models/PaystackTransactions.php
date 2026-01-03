<?php

namespace App\Models;

use CodeIgniter\Model;

class PaystackTransactions extends Model
{
    protected $table            = 'paystack_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'event_id',
        'course_id',
        'amount',
        'status',
        'reference',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function createTransaction($data)
    {
        return $this->insert($data);
    }
}
