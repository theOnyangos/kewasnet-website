<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\EmailQueue as EmailQueueModel;

/**
 * Check Email Queue Status Command
 * 
 * Displays the status of emails in the queue and helps diagnose issues.
 * 
 * Usage:
 *   php spark email:check              - Show queue status
 *   php spark email:check --recent=10 - Show last 10 emails
 *   php spark email:check --failed    - Show only failed emails
 */
class CheckEmailQueue extends BaseCommand
{
    protected $group       = 'Email';
    protected $name        = 'email:check';
    protected $description = 'Check email queue status and diagnose issues';

    public function run(array $params)
    {
        $emailQueueModel = new EmailQueueModel();
        
        $showRecent = (int) CLI::getOptionString('recent', '0');
        $showFailed = CLI::getOption('failed') !== null;

        CLI::write('========================================', 'cyan');
        CLI::write('  Email Queue Status', 'cyan');
        CLI::write('========================================', 'cyan');
        CLI::newLine();

        // Get queue statistics
        $db = \Config\Database::connect();
        
        $stats = [
            'pending' => $db->table('email_queue')->where('status', 'pending')->countAllResults(),
            'processing' => $db->table('email_queue')->where('status', 'processing')->countAllResults(),
            'sent' => $db->table('email_queue')->where('status', 'sent')->countAllResults(),
            'failed' => $db->table('email_queue')->where('status', 'failed')->countAllResults(),
            'total' => $db->table('email_queue')->countAllResults(),
        ];

        // Display statistics
        CLI::write('Queue Statistics:', 'yellow');
        CLI::write('  Total emails: ' . $stats['total'], 'white');
        CLI::write('  ✓ Sent: ' . $stats['sent'], 'green');
        CLI::write('  ⏳ Pending: ' . $stats['pending'], 'yellow');
        CLI::write('  🔄 Processing: ' . $stats['processing'], 'cyan');
        CLI::write('  ✗ Failed: ' . $stats['failed'], 'red');
        CLI::newLine();

        // Show recent emails or failed emails
        if ($showFailed) {
            $this->showFailedEmails($emailQueueModel);
        } elseif ($showRecent > 0) {
            $this->showRecentEmails($emailQueueModel, $showRecent);
        } else {
            // Show pending emails
            if ($stats['pending'] > 0) {
                CLI::write('Pending Emails:', 'yellow');
                $this->showPendingEmails($emailQueueModel);
                CLI::newLine();
            }

            // Show failed emails if any
            if ($stats['failed'] > 0) {
                CLI::write('Failed Emails (last 5):', 'red');
                $this->showFailedEmails($emailQueueModel, 5);
                CLI::newLine();
            }

            // Show recent emails
            CLI::write('Recent Emails (last 5):', 'cyan');
            $this->showRecentEmails($emailQueueModel, 5);
        }

        // Recommendations
        CLI::newLine();
        CLI::write('Recommendations:', 'yellow');
        
        if ($stats['pending'] > 0) {
            CLI::write('  → Run: php spark email:process --once', 'cyan');
            CLI::write('    to process pending emails');
        }
        
        if ($stats['failed'] > 0) {
            CLI::write('  → Check failed emails above for error messages', 'yellow');
            CLI::write('  → Verify SMTP configuration is correct', 'yellow');
        }
        
        if ($stats['processing'] > 0) {
            CLI::write('  → Some emails are stuck in processing state', 'yellow');
            CLI::write('    This may indicate the processor crashed');
        }

        CLI::newLine();
    }

    /**
     * Show pending emails
     */
    private function showPendingEmails(EmailQueueModel $model)
    {
        $pending = $model->where('status', 'pending')
                         ->orderBy('created_at', 'ASC')
                         ->limit(10)
                         ->findAll();

        if (empty($pending)) {
            CLI::write('  No pending emails');
            return;
        }

        foreach ($pending as $email) {
            $age = $this->getAge($email->created_at);
            CLI::write("  ID: {$email->id} | To: {$email->to} | Age: {$age}", 'white');
        }
    }

    /**
     * Show failed emails
     */
    private function showFailedEmails(EmailQueueModel $model, $limit = 10)
    {
        $failed = $model->where('status', 'failed')
                        ->orderBy('updated_at', 'DESC')
                        ->limit($limit)
                        ->findAll();

        if (empty($failed)) {
            CLI::write('  No failed emails');
            return;
        }

        foreach ($failed as $email) {
            $error = substr($email->error_message ?? 'No error message', 0, 100);
            CLI::write("  ID: {$email->id} | To: {$email->to} | Attempts: {$email->attempts}", 'red');
            CLI::write("    Error: {$error}");
        }
    }

    /**
     * Show recent emails
     */
    private function showRecentEmails(EmailQueueModel $model, $limit = 10)
    {
        $recent = $model->orderBy('id', 'DESC')
                        ->limit($limit)
                        ->findAll();

        if (empty($recent)) {
            CLI::write('  No emails in queue');
            return;
        }

        foreach ($recent as $email) {
            $statusColor = $this->getStatusColor($email->status);
            $statusIcon = $this->getStatusIcon($email->status);
            $age = $this->getAge($email->created_at);
            
            CLI::write("  {$statusIcon} ID: {$email->id} | To: {$email->to} | Status: {$email->status} | Age: {$age}", $statusColor);
            
            if ($email->status === 'sent' && $email->sent_at) {
                CLI::write("    Sent at: {$email->sent_at}");
            }
        }
    }

    /**
     * Get status color
     */
    private function getStatusColor($status)
    {
        return match($status) {
            'sent' => 'green',
            'pending' => 'yellow',
            'processing' => 'cyan',
            'failed' => 'red',
            default => 'white'
        };
    }

    /**
     * Get status icon
     */
    private function getStatusIcon($status)
    {
        return match($status) {
            'sent' => '✓',
            'pending' => '⏳',
            'processing' => '🔄',
            'failed' => '✗',
            default => '•'
        };
    }

    /**
     * Get age of email
     */
    private function getAge($datetime)
    {
        if (!$datetime) {
            return 'N/A';
        }

        $now = new \DateTime();
        $created = new \DateTime($datetime);
        $diff = $now->diff($created);

        if ($diff->days > 0) {
            return $diff->days . ' day(s) ago';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hour(s) ago';
        } elseif ($diff->i > 0) {
            return $diff->i . ' minute(s) ago';
        } else {
            return 'just now';
        }
    }
}
