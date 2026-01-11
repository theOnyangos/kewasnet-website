<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use CodeIgniter\Model;

class LeadershipModel extends Model
{
    protected $table            = 'leadership_team';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'position',
        'description',
        'image',
        'linkedin',
        'experience',
        'social_media',
        'order_index',
        'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'name'     => 'required|min_length[2]|max_length[255]',
        'position' => 'required|min_length[2]|max_length[255]',
        'status'   => 'required|in_list[active,inactive]',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;

    // Callbacks
    protected $beforeInsert = ['generateUUID'];
    protected $beforeUpdate = [];

    /**
     * Generates UUID using Ramsey's UUID library
     */
    protected function generateUUID(array $data)
    {
        if (empty($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    /**
     * Get leadership members for DataTable
     */
    public function getLeadership($start, $length, $search = null, $orderBy = 'order_index', $orderDir = 'ASC')
    {
        $builder = $this->select('*');

        // Apply search filter
        if ($search) {
            $builder->groupStart()
                    ->like('name', $search)
                    ->orLike('position', $search)
                    ->orLike('description', $search)
                    ->orLike('experience', $search)
                    ->groupEnd();
        }

        // Apply ordering (after search)
        return $builder->orderBy($orderBy, $orderDir)
                    ->limit($length, $start)
                    ->get()
                    ->getResultArray();
    }

    /**
     * Count all leadership members (excluding soft deleted)
     * @param string|null $search Optional search parameter (for filtered count)
     */
    public function countAllLeadership($search = null)
    {
        $builder = $this->where('deleted_at', null);
        
        // Apply search if provided (for filtered count)
        if ($search) {
            $builder->groupStart()
                    ->like('name', $search)
                    ->orLike('position', $search)
                    ->orLike('description', $search)
                    ->orLike('experience', $search)
                    ->groupEnd();
        }
        
        return $builder->countAllResults();
    }

    /**
     * Get active leadership members ordered by order_index
     */
    public function getActiveLeadership()
    {
        return $this->where('status', 'active')
                    ->orderBy('order_index', 'ASC')
                    ->findAll();
    }

    /**
     * Update order_index for a member
     */
    public function updateOrder($id, $orderIndex)
    {
        return $this->update($id, ['order_index' => $orderIndex]);
    }

    /**
     * Get max order_index
     */
    public function getMaxOrder()
    {
        $result = $this->selectMax('order_index')
                      ->first();
        return $result['order_index'] ?? 0;
    }
}