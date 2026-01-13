<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class EmailQueue extends Model
{
    protected $table            = 'email_queue';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false; // Changed to false for UUID
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', // Added id to allowedFields for UUID insertion
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

    protected $beforeInsert = ['generateUUID'];

    /**
     * Generate UUID for new email queue records
     */
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id']) || empty($data['data']['id'])) {
            try {
                $data['data']['id'] = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                log_message('error', 'EmailQueue UUID generation failed: ' . $e->getMessage());
                // Fallback to database UUID function
                unset($data['data']['id']);
            }
        }
        return $data;
    }

    /**
     * Add email to queue
     */
    public function queueEmail($to, $subject, $message, $bcc = null, $fromEmail = null, $fromName = null)
    {
        try {
            $data = [
                'to'         => $to,
                'bcc'        => is_array($bcc) ? json_encode($bcc) : $bcc,
                'subject'    => $subject,
                'message'    => $message,
                'from_email' => $fromEmail ?? env('EMAIL_FROM_ADDRESS'),
                'from_name'  => $fromName ?? env('EMAIL_FROM_NAME'),
                'status'     => 'pending',
            ];

            $result = $this->insert($data);
            
            if (!$result) {
                $errors = $this->errors();
                $dbError = $this->db->error();
                log_message('error', 'EmailQueue::queueEmail - Insert failed.');
                if ($errors) {
                    log_message('error', 'EmailQueue::queueEmail - Validation errors: ' . json_encode($errors));
                }
                if ($dbError) {
                    log_message('error', 'EmailQueue::queueEmail - DB error: ' . json_encode($dbError));
                }
                log_message('error', 'EmailQueue::queueEmail - Data attempted: ' . json_encode($data));
                return false;
            }
            
            log_message('info', 'EmailQueue::queueEmail - Email queued successfully. ID: ' . $result . ' To: ' . $to);
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'EmailQueue::queueEmail - Exception: ' . $e->getMessage());
            log_message('error', 'EmailQueue::queueEmail - Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Get pending emails
     */
    public function getPendingEmails($limit = 10)
    {
        return $this->where('status', 'pending')
                    ->orGroupStart()
                        ->where('status', 'failed')
                        ->where('attempts <', 3)
                    ->groupEnd()
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
