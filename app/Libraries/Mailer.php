<?php

namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer {

    protected $phpMailer;
    protected $emailConfig;

    public function __construct()
    {
        $this->phpMailer = new PHPMailer(true);
        
        // Load email configuration from Config/Email.php
        // This handles both development (env vars) and production (database) settings
        $this->emailConfig = config('Email');
    }
    
    public function send($to, $subject, $message, $from = null, $fromName = null, $bcc = null) {
        try {
            // Use email queue instead of direct sending
            $emailQueueModel = new \App\Models\EmailQueue();
            
            $fromEmail = $from ?? $this->emailConfig->fromEmail;
            $fromName = $fromName ?? $this->emailConfig->fromName;
            
            // Queue the email
            $result = $emailQueueModel->queueEmail($to, $subject, $message, $bcc, $fromEmail, $fromName);
            
            if (!$result) {
                log_message('error', 'Failed to queue email to: ' . $to);
                throw new \Exception('Failed to queue email');
            }
            
            return true;
                
        } catch (\Exception $e) {
            // Handle exceptions
            log_message('error', 'Mailer queue error: ' . $e->getMessage());
            throw new \Exception('Failed to queue email: ' . $e->getMessage());
        }
    }
}
