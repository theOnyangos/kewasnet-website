<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use CodeIgniter\Model;

class JobOpportunityModel extends Model
{
    protected $table = 'job_opportunities';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = false;
    
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    
    protected $allowedFields = [
        'id',
        'title',
        'slug',
        'description',
        'opportunity_type',
        'location',
        'is_remote',
        'salary_min',
        'salary_max',
        'salary_currency',
        'application_deadline',
        'status',
        'company',
        'benefits',
        'scope',
        'document_url',
        'requirements',
        'contract_duration',
        'hours_per_week'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    protected $validationRules = [
        'title'                 => 'required|max_length[255]',
        'description'           => 'required',
        'opportunity_type'      => 'required|in_list[full-time,part-time,contract,internship,freelance]',
        'location'              => 'permit_empty|max_length[255]',
        'is_remote'             => 'permit_empty|in_list[0,1]',
        'salary_min'            => 'permit_empty|numeric',
        'salary_max'            => 'permit_empty|numeric',
        'salary_currency'       => 'permit_empty|max_length[3]',
        'application_deadline'  => 'permit_empty|valid_date',
        'status'                => 'required|in_list[draft,published,closed]',
        'company'               => 'permit_empty|max_length[255]',
        'contract_duration'     => 'permit_empty|max_length[100]',
        'hours_per_week'        => 'permit_empty|integer'
    ];
    
    protected $beforeInsert = ['generateUUID', 'encodeJSONFields'];
    protected $beforeUpdate = ['encodeJSONFields'];
    protected $afterFind = ['decodeJSONFields'];

    /**
     * Generate UUID for new records
     */
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    
    /**
     * Automatically encode array fields to JSON before saving
     */
    protected function encodeJSONFields(array $data)
    {
        $jsonFields = ['benefits', 'requirements'];
        
        foreach ($jsonFields as $field) {
            if (isset($data['data'][$field]) && is_array($data['data'][$field])) {
                $data['data'][$field] = json_encode($data['data'][$field]);
            }
        }
        
        return $data;
    }
    
    /**
     * Automatically decode JSON fields to arrays after retrieval
     */
    protected function decodeJSONFields(array $data)
    {
        if (!$data) {
            return $data;
        }
        
        $jsonFields = ['benefits', 'requirements'];
        
        // Handle both single result and array of results
        if (isset($data['data'])) {
            // Multiple results
            foreach ($data['data'] as &$row) {
                $this->decodeRowJSON($row, $jsonFields);
            }
        } else {
            // Single result
            $this->decodeRowJSON($data, $jsonFields);
        }
        
        return $data;
    }
    
    private function decodeRowJSON(&$row, $jsonFields)
    {
        foreach ($jsonFields as $field) {
            if (isset($row[$field]) && !empty($row[$field])) {
                $row[$field] = json_decode($row[$field], true);
            } elseif (isset($row[$field])) {
                $row[$field] = [];
            }
        }
    }
    
    /**
     * Get opportunities by status
     */
    public function getByStatus(string $status)
    {
        return $this->where('status', $status)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }
    
    /**
     * Get opportunities by type
     */
    public function getByType(string $type)
    {
        return $this->where('opportunity_type', $type)
                   ->where('status', 'published')
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }
    
    /**
     * Search opportunities with filters
     */
    public function search(string $keyword = null, array $filters = [])
    {
        $builder = $this->builder();
        
        if ($keyword) {
            $builder->groupStart()
                   ->like('title', $keyword)
                   ->orLike('description', $keyword)
                   ->orLike('company', $keyword)
                   ->groupEnd();
        }
        
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                $builder->where($field, $value);
            }
        }
        
        return $builder->where('status', 'published')
                      ->orderBy('created_at', 'DESC')
                      ->get()
                      ->getResultArray();
    }
    
    /**
     * Get similar opportunities (by type and keywords)
     */
    public function getSimilar(int $currentId, string $type, int $limit = 3)
    {
        return $this->where('opportunity_type', $type)
                   ->where('id !=', $currentId)
                   ->where('status', 'published')
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Get a paginated list of job opportunities
     */
    public function getJobOpportunities($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC')
    {
        $builder = $this->builder()
            ->select('job_opportunities.*, COUNT(job_applicants.id) as application_count')
            ->join('job_applicants', "job_applicants.opportunity_id = job_opportunities.id COLLATE utf8mb4_unicode_ci", 'left', false)
            ->where('job_opportunities.deleted_at', null)  // Filter out soft-deleted records
            ->groupBy('job_opportunities.id');

        if ($search) {
            $builder->groupStart()
                ->like('title', $search)
                ->orLike('description', $search)
                ->orLike('company', $search)
                ->orLike('salary_min', $search)
                ->orLike('salary_max', $search)
                ->orLike('salary_currency', $search)
                ->groupEnd();
        }

        return $builder->orderBy($orderBy, $orderDir)
                    ->limit($length, $start)
                    ->get()
                    ->getResultArray();
    }

    /**
     * Count job opportunities
     */
    public function countJobOpportunities(): int
    {
        return $this->where('deleted_at', null)->countAllResults();
    }

    /**
     * Get a paginated list of deleted job opportunities
     */
    public function getDeletedOpportunities($start, $length, $search = null, $orderBy = 'deleted_at', $orderDir = 'DESC')
    {
        $builder = $this->builder()
            ->select('job_opportunities.*, COUNT(job_applicants.id) as application_count')
            ->join('job_applicants', "job_applicants.opportunity_id = job_opportunities.id COLLATE utf8mb4_unicode_ci", 'left', false)
            ->where('job_opportunities.deleted_at IS NOT NULL', null, false)  // Only get soft-deleted records
            ->groupBy('job_opportunities.id');

        if ($search) {
            $builder->groupStart()
                ->like('title', $search)
                ->orLike('description', $search)
                ->orLike('company', $search)
                ->orLike('salary_min', $search)
                ->orLike('salary_max', $search)
                ->orLike('salary_currency', $search)
                ->groupEnd();
        }

        return $builder->orderBy($orderBy, $orderDir)
                    ->limit($length, $start)
                    ->get()
                    ->getResultArray();
    }

    /**
     * Count deleted job opportunities
     */
    public function countDeletedOpportunities($search = null): int
    {
        $builder = $this->builder()
            ->where('deleted_at IS NOT NULL', null, false);

        if ($search) {
            $builder->groupStart()
                ->like('title', $search)
                ->orLike('description', $search)
                ->orLike('company', $search)
                ->orLike('salary_min', $search)
                ->orLike('salary_max', $search)
                ->orLike('salary_currency', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }
}