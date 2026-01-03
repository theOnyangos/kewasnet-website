<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use CodeIgniter\Model;

class JobApplicantModel extends Model
{
    protected $table = 'job_applicants';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = false;
    protected $useSoftDeletes = true;
    
    protected $allowedFields = [
        'id',
        'opportunity_id',
        'job_opportunity_id', // Support both field names for compatibility
        'first_name',
        'last_name',
        'email',
        'phone',
        'location',
        'experience_years',
        'years_of_experience', // Support both field names
        'education_level',
        'cover_letter',
        'linkedin_profile',
        'portfolio_url',
        'current_job_title',
        'current_company',
        'location',
        'skills',
        'availability',
        'salary_expectation',
        'status',
        'notes',
        'custom_fields',
        'ip_address',
        'user_agent',
        'applied_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    protected $beforeInsert = ['generateUUID', 'encodeJSONFields'];
    protected $beforeUpdate = ['encodeJSONFields'];
    protected $afterFind = ['decodeJSONFields'];
    
    protected $validationRules = [
        'opportunity_id'    => 'required',
        'first_name'        => 'required|max_length[100]',
        'last_name'         => 'required|max_length[100]',
        'email'             => 'required|valid_email|max_length[255]',
        'phone'             => 'permit_empty|max_length[20]',
        'status'            => 'required|in_list[pending,reviewed,interviewed,rejected,hired]'
    ];
    
    protected $educationLevels = [
        'high_school'   => 'High School',
        'associate'     => 'Associate Degree',
        'bachelor'      => 'Bachelor\'s Degree',
        'master'        => 'Master\'s Degree',
        'phd'           => 'PhD',
        'other'         => 'Other'
    ];
    
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
        $jsonFields = ['skills', 'custom_fields'];
        
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
        
        $jsonFields = ['skills', 'custom_fields'];
        
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
     * Get human-readable education level
     */
    public function getEducationLevel(string $key): ?string
    {
        return $this->educationLevels[$key] ?? null;
    }
    
    /**
     * Get all education levels
     */
    public function getAllEducationLevels(): array
    {
        return $this->educationLevels;
    }
    
    /**
     * Get applicants for a specific opportunity
     */
    public function getApplicantsForOpportunity(string $opportunityId, array $filters = [])
    {
        $builder = $this->builder();
        $builder->where('opportunity_id', $opportunityId);
        
        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                   ->like('first_name', $search)
                   ->orLike('last_name', $search)
                   ->orLike('email', $search)
                   ->orLike('current_job_title', $search)
                   ->orLike('current_company', $search)
                   ->groupEnd();
        }
        
        return $builder->orderBy('created_at', 'DESC')
                      ->get()
                      ->getResultArray();
    }
    
    /**
     * Change applicant status and log the change
     */
    public function changeStatus(string $applicantId, string $newStatus, ?int $changedBy = null, ?string $notes = null): bool
    {
        $this->db->transStart();
        
        // Update applicant status
        $updated = $this->update($applicantId, [
            'status' => $newStatus
        ]);
        
        if ($updated) {
            // Log status change
            $statusHistoryModel = new ApplicantStatusHistoryModel();
            $statusHistoryModel->insert([
                'applicant_id' => $applicantId,
                'status' => $newStatus,
                'changed_by' => $changedBy,
                'notes' => $notes
            ]);
        }
        
        $this->db->transComplete();
        
        return $this->db->transStatus();
    }
    
    /**
     * Get status history for an applicant
     */
    public function getStatusHistory(string $applicantId)
    {
        $statusHistoryModel = new ApplicantStatusHistoryModel();
        return $statusHistoryModel->where('applicant_id', $applicantId)
                                ->orderBy('created_at', 'DESC')
                                ->findAll();
    }

    public function getJobApplications($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC')
    {
        $builder = $this->builder();
        $builder->select('job_applicants.*, job_opportunities.title AS job_title, 
                        CONCAT(job_applicants.first_name, " ", job_applicants.last_name) AS applicant_name');
        
        // Modified join with explicit collation
        $builder->join('job_opportunities', 
                    'job_opportunities.id = job_applicants.opportunity_id COLLATE utf8mb4_unicode_ci', 
                    'left', false); // Notice the 'false' parameter to prevent escaping

        if ($search) {
            $builder->groupStart()
                    ->like('job_opportunities.title', $search)
                    ->orLike('CONCAT(job_applicants.first_name, " ", job_applicants.last_name)', $search)
                    ->orLike('job_applicants.email', $search)
                    ->orLike('job_applicants.phone', $search)
                    ->groupEnd();
        }

        return $builder->orderBy($orderBy, $orderDir)
                    ->limit($length, $start)
                    ->get()
                    ->getResultArray();
    }

    public function countJobApplications()
    {
        return $this->builder()->countAllResults();
    }
}