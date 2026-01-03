<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use CodeIgniter\Model;

class ApplicantStatusHistoryModel extends Model
{
    protected $table = 'applicant_status_history';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'id',
        'applicant_id',
        'status',
        'changed_by',
        'notes'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    
    protected $validationRules = [
        'applicant_id' => 'required',
        'status' => 'required|in_list[pending,reviewed,interviewed,rejected,hired]'
    ];

    protected $beforeInsert = ['generateUUID'];

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
     * Get history with user details
     */
    public function getHistoryWithUser(string $applicantId)
    {
        return $this->select('applicant_status_history.*, system_users.username, system_users.first_name, system_users.last_name')
                   ->join('system_users', 'system_users.id = applicant_status_history.changed_by', 'left')
                   ->where('applicant_id', $applicantId)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }
    
    /**
     * Get last status change
     */
    public function getLastStatusChange(string $applicantId, string $status)
    {
        return $this->where('applicant_id', $applicantId)
                   ->where('status', $status)
                   ->orderBy('created_at', 'DESC')
                   ->first();
    }
}