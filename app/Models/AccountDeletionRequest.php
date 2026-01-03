<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class AccountDeletionRequest extends Model
{
    protected $table = 'account_deletion_requests';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    
    protected $allowedFields = ['id', 'user_id', 'reason', 'status', 'created_at', 'updated_at', 'deleted_at'];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $useSoftDeletes = true;
    
    protected $beforeInsert = ['generateUUID'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    // Validation rules
    protected $validationRules = [
        'user_id' => 'required|max_length[36]',
        'reason' => 'permit_empty',
        'status' => 'required|in_list[pending,approved,rejected]'
    ];

    // Optional: If you want to automatically format UUID when retrieving
    public function find($id = null)
    {
        if ($id !== null) {
            return parent::find($id);
        }
        return parent::findAll();
    }
}