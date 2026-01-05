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
                $email->setFrom($queuedEmail->from_email, $queuedEmail->from_name ?? 'KEWASNET');
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
                if ($email->send()) {
                    $emailQueueModel->markAsSent($queuedEmail->id);
                    CLI::write("✓ Email #{$queuedEmail->id} sent successfully to {$queuedEmail->to}", 'green');
                    log_message('info', "Email #{$queuedEmail->id} sent successfully to {$queuedEmail->to}");
                    $successCount++;
                } else {
                    $error = $email->printDebugger(['headers']);
                    $emailQueueModel->markAsFailed($queuedEmail->id, $error);
                    CLI::write("✗ Email #{$queuedEmail->id} failed: " . substr($error, 0, 200), 'red');
                    log_message('error', "Email #{$queuedEmail->id} failed: " . $error);
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
