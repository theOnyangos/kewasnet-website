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
                // Configure email
                $email->clear();
                $email->setFrom($queuedEmail->from_email, $queuedEmail->from_name);
                $email->setTo($queuedEmail->to);
                
                if (!empty($queuedEmail->bcc)) {
                    $bccList = json_decode($queuedEmail->bcc, true);
                    if (is_array($bccList) && !empty($bccList)) {
                        $email->setBCC($bccList);
                    }
                }
                
                $email->setSubject($queuedEmail->subject);
                $email->setMessage($queuedEmail->message);

                // Send email
                if ($email->send()) {
                    $emailQueueModel->markAsSent($queuedEmail->id);
                    CLI::write("✓ Email #{$queuedEmail->id} sent successfully", 'green');
                    $successCount++;
                } else {
                    $error = $email->printDebugger(['headers']);
                    $emailQueueModel->markAsFailed($queuedEmail->id, $error);
                    CLI::write("✗ Email #{$queuedEmail->id} failed: " . $error, 'red');
                    $failCount++;
                }
            } catch (\Exception $e) {
                $emailQueueModel->markAsFailed($queuedEmail->id, $e->getMessage());
                CLI::write("✗ Email #{$queuedEmail->id} error: " . $e->getMessage(), 'red');
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
