<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use CodeIgniter\Model;

class PartnerModel extends Model
{
    protected $table            = 'partners';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'partner_name',
        'partner_logo',
        'partner_url',
        'partnership_type',
        'description'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $useAutoIncrement = false;

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    // Callbacks
    protected $beforeInsert = ['generateUUID'];
    protected $beforeUpdate = [];

    // This method gets all partners from the database
    public function getAllPartners()
    {
        return $this->findAll();
    }

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

    // Get partners for table
    public function getPartners($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC')
    {
        $builder = $this->select('*')  
                        ->orderBy($orderBy, $orderDir);

        // Apply search filter
        if ($search) {
            $builder->groupStart()
                    ->like('partner_name', $search)
                    ->orLike('partner_url', $search)
                    ->orLike('created_at', $search)
                    ->groupEnd();
        }

        return $builder->orderBy($orderBy, $orderDir)
                    ->limit($length, $start)
                    ->get()
                    ->getResultArray();
    }

    // Count All Partners
    /**
     * @param string|null $search Optional search parameter (for filtered count, kept for compatibility with DataTableService)
     */
    public function countAllPartners($search = null)
    {
        $builder = $this->where('deleted_at', null);
        
        // Apply search if provided (for filtered count)
        if ($search) {
            $builder->groupStart()
                    ->like('partner_name', $search)
                    ->orLike('partner_url', $search)
                    ->groupEnd();
        }
        
        return $builder->countAllResults();
    }
}
