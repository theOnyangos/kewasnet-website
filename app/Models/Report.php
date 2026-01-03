<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class Report extends Model
{
    protected $table = 'reports';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = false;
    
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'id',
        'reporter_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'description',
        'status',
        'reviewed_by',
        'reviewed_at',
        'action_taken',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    
    protected $validationRules = [
        'reporter_id' => 'required',
        'reportable_type' => 'required|max_length[50]',
        'reportable_id' => 'required',
        'reason' => 'required|max_length[100]',
        'status' => 'permit_empty|in_list[pending,reviewed,resolved,dismissed]'
    ];
    
    protected $validationMessages = [];
    protected $skipValidation = false;
    
    // Callbacks
    protected $beforeInsert = ['generateUUID'];
    protected $beforeUpdate = [];
    
    /**
     * Generate UUID for primary key and other UUID fields if needed
     */
    protected function generateUUID(array $data)
    {
        try {
            // Generate UUID for primary key if not set
            if (!isset($data['data']['id'])) {
                $data['data']['id'] = Uuid::uuid4()->toString();
            }
            
            // Ensure reporter_id is a valid UUID if provided
            if (isset($data['data']['reporter_id']) && !Uuid::isValid($data['data']['reporter_id'])) {
                throw new \InvalidArgumentException('Invalid reporter_id UUID format');
            }
            
            // Ensure reportable_id is a valid UUID if provided
            if (isset($data['data']['reportable_id']) && !Uuid::isValid($data['data']['reportable_id'])) {
                throw new \InvalidArgumentException('Invalid reportable_id UUID format');
            }
            
            // Ensure reviewed_by is a valid UUID if provided
            if (isset($data['data']['reviewed_by']) && !empty($data['data']['reviewed_by']) && 
                !Uuid::isValid($data['data']['reviewed_by'])) {
                throw new \InvalidArgumentException('Invalid reviewed_by UUID format');
            }
            
        } catch (UnsatisfiedDependencyException $e) {
            log_message('error', 'UUID generation failed: ' . $e->getMessage());
            throw new \RuntimeException('UUID generation failed');
        }
        
        return $data;
    }
    
    /**
     * Get reports with user information
     */
    public function getReportsWithUsers($conditions = [], $limit = null, $offset = null)
    {
        $builder = $this->db->table('reports');
        $builder->select('reports.*, 
                        reporter.username as reporter_username, 
                        reviewer.username as reviewer_username');
        $builder->join('users as reporter', 'reporter.id = reports.reporter_id', 'left');
        $builder->join('users as reviewer', 'reviewer.id = reports.reviewed_by', 'left');
        
        if (!empty($conditions)) {
            $builder->where($conditions);
        }
        
        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }
        
        $builder->orderBy('reports.created_at', 'DESC');
        
        return $builder->get()->getResult();
    }
    
    /**
     * Get report with related reportable item
     */
    public function getReportWithDetails($reportId)
    {
        $report = $this->find($reportId);
        
        if (!$report) {
            return null;
        }
        
        // Load the appropriate model based on reportable_type
        if ($report->reportable_type === 'discussion') {
            $discussionModel = new \App\Models\DiscussionModel();
            $reportableItem = $discussionModel->find($report->reportable_id);
        } elseif ($report->reportable_type === 'reply') {
            $replyModel = new \App\Models\ReplyModel();
            $reportableItem = $replyModel->find($report->reportable_id);
        } else {
            $reportableItem = null;
        }
        
        return [
            'report' => $report,
            'reportable_item' => $reportableItem
        ];
    }
    
    /**
     * Update report status and reviewer info
     */
    public function updateReportStatus($reportId, $status, $reviewerId, $actionTaken = null)
    {
        $data = [
            'status' => $status,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => date('Y-m-d H:i:s')
        ];
        
        if ($actionTaken !== null) {
            $data['action_taken'] = $actionTaken;
        }
        
        return $this->update($reportId, $data);
    }
    
    /**
     * Count reports by status
     */
    public function countByStatus($status = null)
    {
        $builder = $this->builder();
        
        if ($status !== null) {
            $builder->where('status', $status);
        }
        
        return $builder->countAllResults();
    }

    public function getReportCount()
    {
        return $this->where('is_active', true)->countAll();
    }
}