<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\EmailQueue as EmailQueueModel;

class ProcessEmailQueue extends BaseCommand
{
    protected $group       = 'Email';
    protected $name        = 'email:process';
    protected $description = 'Process pending emails in the queue';

    public function run(array $params)
    {
        $emailQueueModel = new EmailQueueModel();
        $pendingEmails = $emailQueueModel->getPendingEmails(20);

        if (empty($pendingEmails)) {
            CLI::write('No pending emails to process.', 'yellow');
            return;
        }

        CLI::write('Processing ' . count($pendingEmails) . ' emails...', 'green');

        $email = \Config\Services::email();
        $successCount = 0;
        $failCount = 0;

        foreach ($pendingEmails as $queuedEmail) {
            // Mark as processing
            $emailQueueModel->markAsProcessing($queuedEmail->id);

            try {
                // Validate email data
                if (empty($queuedEmail->to) || !filter_var($queuedEmail->to, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid recipient email address: ' . $queuedEmail->to;
                    $emailQueueModel->markAsFailed($queuedEmail->id, $error);
                    CLI::write("✗ Email #{$queuedEmail->id} error: " . $error, 'red');
                    $failCount++;
                    continue;
                }
                
                if (empty($queuedEmail->from_email) || !filter_var($queuedEmail->from_email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid sender email address: ' . $queuedEmail->from_email;
                    $emailQueueModel->markAsFailed($queuedEmail->id, $error);
                    CLI::write("✗ Email #{$queuedEmail->id} error: " . $error, 'red');
                    $failCount++;
                    continue;
                }

                // Configure email
                $email->clear();
                
                // Use SMTP username as From email if From email doesn't match (some servers require this)
                $emailConfig = config('Email');
                $fromEmail = $queuedEmail->from_email;
                
                // If From email domain doesn't match SMTP username domain, use SMTP username
                // This helps with servers that require From address to match authenticated user
                if (!empty($emailConfig->SMTPUser)) {
                    $smtpDomain = substr(strrchr($emailConfig->SMTPUser, "@"), 1);
                    $fromDomain = substr(strrchr($fromEmail, "@"), 1);
                    
                    if ($smtpDomain !== $fromDomain) {
                        CLI::write("  Warning: From email domain ({$fromDomain}) doesn't match SMTP user domain ({$smtpDomain})", 'yellow');
                        CLI::write("  Using SMTP username as From email: {$emailConfig->SMTPUser}", 'yellow');
                        $fromEmail = $emailConfig->SMTPUser;
                    }
                }
                
                $email->setFrom($fromEmail, $queuedEmail->from_name ?? 'KEWASNET');
                $email->setTo($queuedEmail->to);
                
                if (!empty($queuedEmail->bcc)) {
                    $bccList = json_decode($queuedEmail->bcc, true);
                    if (is_array($bccList) && !empty($bccList)) {
                        foreach ($bccList as $bccEmail) {
                            if (filter_var($bccEmail, FILTER_VALIDATE_EMAIL)) {
                                $email->setBCC($bccEmail);
                            }
                        }
                    }
                }
                
                $email->setSubject($queuedEmail->subject ?? 'No Subject');
                $email->setMessage($queuedEmail->message ?? '');

                // Send email
                $sendResult = $email->send();
                $debugOutput = $email->printDebugger(['headers', 'subject', 'body']);
                
                // Check if there are SMTP errors in debug output even if send() returns true
                $hasSmtpError = stripos($debugOutput, 'SMTP error') !== false || 
                               stripos($debugOutput, '503') !== false ||
                               stripos($debugOutput, 'STARTTLS') !== false ||
                               stripos($debugOutput, 'Unable to send') !== false;
                
                if ($sendResult && !$hasSmtpError) {
                    $emailQueueModel->markAsSent($queuedEmail->id);
                    CLI::write("✓ Email #{$queuedEmail->id} sent successfully to {$queuedEmail->to}", 'green');
                    log_message('info', "Email #{$queuedEmail->id} sent successfully to {$queuedEmail->to}");
                    $successCount++;
                } else {
                    // Even if send() returns true, check for SMTP errors
                    if ($hasSmtpError) {
                        $error = "SMTP Error detected: " . $debugOutput;
                    } else {
                        $error = $debugOutput ?: 'Unknown error';
                    }
                    
                    $emailQueueModel->markAsFailed($queuedEmail->id, $error);
                    CLI::write("✗ Email #{$queuedEmail->id} failed: " . substr($error, 0, 200), 'red');
                    log_message('error', "Email #{$queuedEmail->id} failed: " . $error);
                    if ($debugOutput) {
                        log_message('error', "Email #{$queuedEmail->id} debug output: " . $debugOutput);
                    }
                    $failCount++;
                }
            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();
                $emailQueueModel->markAsFailed($queuedEmail->id, $errorMsg);
                CLI::write("✗ Email #{$queuedEmail->id} error: " . $errorMsg, 'red');
                log_message('error', "Email #{$queuedEmail->id} exception: " . $errorMsg);
                log_message('error', "Email #{$queuedEmail->id} stack trace: " . $e->getTraceAsString());
                $failCount++;
            }

            // Small delay between emails
            usleep(100000); // 0.1 seconds
        }

        CLI::write("\nProcessing complete:", 'yellow');
        CLI::write("✓ Sent: $successCount", 'green');
        CLI::write("✗ Failed: $failCount", 'red');
    }
}
