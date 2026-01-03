<?php

namespace App\Models;

use CodeIgniter\Model;

class Newsletter extends Model
{
    protected $table = 'newsletters';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'id',
        'subject',
        'preview_text',
        'content',
        'sender_name',
        'sender_email',
        'status',
        'scheduled_at',
        'sent_at',
        'recipient_count',
        'sent_count',
        'failed_count',
        'open_rate',
        'click_rate',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'subject' => 'required|min_length[3]|max_length[255]',
        'content' => 'required',
        'sender_email' => 'permit_empty|valid_email',
    ];

    protected $validationMessages = [
        'subject' => [
            'required' => 'Subject is required',
            'min_length' => 'Subject must be at least 3 characters',
        ],
        'content' => [
            'required' => 'Newsletter content is required',
        ],
    ];

    protected $beforeInsert = ['generateUUID'];
    protected $beforeUpdate = [];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = \Ramsey\Uuid\Uuid::uuid4()->toString();
        }
        return $data;
    }

    /**
     * Get newsletters for DataTable
     */
    public function getNewslettersTable($start, $length, $search = null, $orderBy = 'created_at', $orderDir = 'DESC')
    {
        $builder = $this->builder()
            ->select('id, subject, status, recipient_count, sent_count, scheduled_at, sent_at, created_at')
            ->where('deleted_at', null);

        if ($search) {
            $builder->groupStart()
                ->like('subject', $search)
                ->orLike('status', $search)
                ->groupEnd();
        }

        $builder->orderBy($orderBy, $orderDir)
            ->limit($length, $start);

        return $builder->get()->getResultArray();
    }

    /**
     * Count total newsletters
     */
    public function countNewsletters($search = null): int
    {
        $builder = $this->builder()->where('deleted_at', null);

        if ($search) {
            $builder->groupStart()
                ->like('subject', $search)
                ->orLike('status', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Get newsletter statistics
     */
    public function getNewsletterStats()
    {
        $total = $this->where('deleted_at', null)->countAllResults();
        $sent = $this->where('status', 'sent')->where('deleted_at', null)->countAllResults();
        $scheduled = $this->where('status', 'scheduled')->where('deleted_at', null)->countAllResults();
        $drafts = $this->where('status', 'draft')->where('deleted_at', null)->countAllResults();

        // Average open rate
        $avgOpenRate = $this->selectAvg('open_rate')
            ->where('status', 'sent')
            ->where('deleted_at', null)
            ->get()
            ->getRowArray()['open_rate'] ?? 0;

        // Average click rate
        $avgClickRate = $this->selectAvg('click_rate')
            ->where('status', 'sent')
            ->where('deleted_at', null)
            ->get()
            ->getRowArray()['click_rate'] ?? 0;

        return [
            'total_newsletters' => $total,
            'sent_newsletters' => $sent,
            'scheduled_newsletters' => $scheduled,
            'draft_newsletters' => $drafts,
            'avg_open_rate' => round($avgOpenRate, 2),
            'avg_click_rate' => round($avgClickRate, 2),
        ];
    }

    /**
     * Update newsletter status
     */
    public function updateStatus($id, $status, $additionalData = [])
    {
        $data = array_merge(['status' => $status], $additionalData);
        return $this->update($id, $data);
    }
}
