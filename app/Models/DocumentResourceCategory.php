<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class DocumentResourceCategory extends Model
{
    protected $table            = 'resource_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false; // Changed to false since we're using UUIDs
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'name',
        'slug',
        'description',
        'pillar_id',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateUUID'];
    

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

    // Add this method to the ResourceCategory model
    public function getActiveCategories()
    {
        return $this->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Get categories for DataTable
     */
    public function getCategoriesTable($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC', $forumId = null)
    {
        $builder = $this->select('*');

        if ($search) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('description', $search);
        }

        $builder->orderBy($orderBy, $orderDir);
        $builder->limit($length, $start);
        return $builder->get()->getResult();
    }

    /**
     * Count all categories
     */
    public function countAllCategories($search = null, $forumId = null)
    {
        $builder = $this->select('COUNT(*) as count');
        if ($search) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('description', $search);
        }
        $result = $builder->get()->getRow();
        return $result ? (int)$result->count : 0;
    }
}
