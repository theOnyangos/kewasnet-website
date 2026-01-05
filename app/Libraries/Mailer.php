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
            
            $fromEmail = $from ?? $this->emailConfig->fromEmail ?? env('EMAIL_FROM_ADDRESS');
            $fromName = $fromName ?? $this->emailConfig->fromName ?? env('EMAIL_FROM_NAME');
            
            // Validate from email
            if (empty($fromEmail) || !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
                log_message('error', 'Mailer::send - Invalid from email address: ' . $fromEmail);
                throw new \Exception('Invalid from email address');
            }
            
            // Validate to email address
            if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
                log_message('error', 'Mailer::send - Invalid email address: ' . $to);
                throw new \Exception('Invalid email address');
            }
            
            // Validate subject
            if (empty($subject)) {
                log_message('error', 'Mailer::send - Empty subject');
                throw new \Exception('Email subject is required');
            }
            
            // Validate message
            if (empty($message)) {
                log_message('error', 'Mailer::send - Empty message');
                throw new \Exception('Email message is required');
            }
            
            log_message('debug', 'Mailer::send - Attempting to queue email. To: ' . $to . ', From: ' . $fromEmail . ', Subject: ' . $subject);
            
            // Queue the email
            $result = $emailQueueModel->queueEmail($to, $subject, $message, $bcc, $fromEmail, $fromName);
            
            if (!$result) {
                log_message('error', 'Mailer::send - Failed to queue email to: ' . $to . ' - Subject: ' . $subject);
                throw new \Exception('Failed to queue email - check logs for details');
            }
            
            log_message('info', 'Mailer::send - Email queued successfully to: ' . $to . ' - Subject: ' . $subject . ' - Queue ID: ' . $result);
            return true;
                
        } catch (\Exception $e) {
            // Handle exceptions
            log_message('error', 'Mailer::send - Queue error: ' . $e->getMessage());
            log_message('error', 'Mailer::send - Stack trace: ' . $e->getTraceAsString());
            throw new \Exception('Failed to queue email: ' . $e->getMessage());
        }
    }
}
