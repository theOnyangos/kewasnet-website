<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class Contributor extends Model
{
    protected $table = 'contributors';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'id',
        'name',
        'organization',
        'email',
        'photo_url',
        'bio',
        'created_at',
        'updated_at'
    ];
    
    protected $useAutoIncrement = false;
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $beforeInsert = ['generateUUID'];
    
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }
}