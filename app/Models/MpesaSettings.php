<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class MpesaSettings extends Model
{
    protected $table            = 'mpesa_settings';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'consumer_key',
        'consumer_secret',
        'business_short_code',
        'pass_key',
        'environment',
        'transaction_type',
        'account_reference',
        'consumer_endpoint',
        'party_b',
        'callback_url',
        'callback_registered',
        'test_mode',
        'enabled',
        'created_at',
        'updated_at'
    ];

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateUUID'];

    /**
     * Generate UUID for new records
     */
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id']) || empty($data['data']['id'])) {
            try {
                $data['data']['id'] = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                log_message('error', 'UUID generation failed: ' . $e->getMessage());
                // Fallback to database UUID function
                unset($data['data']['id']);
            }
        }

        return $data;
    }
}
