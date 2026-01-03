<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailQueue extends Model
{
    protected $table            = 'email_queue';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'to',
        'bcc',
        'subject',
        'message',
        'from_email',
        'from_name',
        'status',
        'attempts',
        'error_message',
        'sent_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Add email to queue
     */
    public function queueEmail($to, $subject, $message, $bcc = null, $fromEmail = null, $fromName = null)
    {
        $data = [
            'to'         => $to,
            'bcc'        => is_array($bcc) ? json_encode($bcc) : $bcc,
            'subject'    => $subject,
            'message'    => $message,
            'from_email' => $fromEmail ?? env('EMAIL_FROM_ADDRESS'),
            'from_name'  => $fromName ?? env('EMAIL_FROM_NAME'),
            'status'     => 'pending',
        ];

        return $this->insert($data);
    }

    /**
     * Get pending emails
     */
    public function getPendingEmails($limit = 10)
    {
        return $this->where('status', 'pending')
                    ->orWhere('status', 'failed')
                    ->where('attempts <', 3)
                    ->orderBy('created_at', 'ASC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Mark email as processing
     */
    public function markAsProcessing($id)
    {
        return $this->update($id, ['status' => 'processing']);
    }

    /**
     * Mark email as sent
     */
    public function markAsSent($id)
    {
        return $this->update($id, [
            'status'  => 'sent',
            'sent_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Mark email as failed
     */
    public function markAsFailed($id, $errorMessage)
    {
        $email = $this->find($id);
        return $this->update($id, [
            'status'        => 'failed',
            'attempts'      => $email->attempts + 1,
            'error_message' => $errorMessage
        ]);
    }
}
