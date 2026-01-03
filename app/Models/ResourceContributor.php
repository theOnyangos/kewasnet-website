<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class ResourceContributor extends Model
{
    protected $table = 'resource_contributors';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'id',
        'resource_id',
        'contributor_id',
        'role',
        'created_at'
    ];
    
    protected $useAutoIncrement = false;
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    
    protected $beforeInsert = ['generateUUID'];
    
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }
    
    public function getResource($resource_id)
    {
        return $this->db->table('resources')
            ->where('id', $resource_id)
            ->get()
            ->getRow();
    }
    
    public function getContributor($contributor_id)
    {
        return $this->db->table('contributors')
            ->where('id', $contributor_id)
            ->get()
            ->getRow();
    }
}