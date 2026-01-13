<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Test SMTP Connection Command
 * 
 * Tests different SMTP configurations to find the correct settings.
 * 
 * Usage:
 *   php spark smtp:test
 */
class TestSMTPConnection extends BaseCommand
{
    protected $group       = 'Email';
    protected $name        = 'smtp:test';
    protected $description = 'Test SMTP connection with different encryption settings';

    public function run(array $params)
    {
        CLI::write('========================================', 'cyan');
        CLI::write('  SMTP Connection Test', 'cyan');
        CLI::write('========================================', 'cyan');
        CLI::newLine();

        // Get settings from database
        $db = \Config\Database::connect();
        $builder = $db->table('email_settings');
        $settings = $builder->orderBy('id', 'DESC')->get(1)->getRowArray();

        if (!$settings) {
            CLI::error('No email settings found in database!');
            return;
        }

        $host = $settings['host'];
        $port = (int) $settings['port'];
        $username = $settings['username'];
        $password = $settings['password'];
        $fromEmail = $settings['from_address'];
        $fromName = $settings['from_name'] ?? 'KEWASNET';

        CLI::write("Testing SMTP: {$host}:{$port}", 'yellow');
        CLI::write("Username: {$username}", 'yellow');
        CLI::newLine();

        // Test different encryption settings
        $encryptionOptions = [
            '' => 'No encryption',
            'tls' => 'TLS (STARTTLS)',
            'ssl' => 'SSL (Implicit)',
        ];

        $testEmail = $params[0] ?? CLI::prompt('Enter test email address', null, 'required');
        
        if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            CLI::error('Invalid email address: ' . $testEmail);
            return;
        }

        foreach ($encryptionOptions as $encryption => $description) {
            CLI::write("Testing: {$description} ({$encryption})", 'cyan');
            CLI::write('----------------------------------------', 'cyan');

            $result = $this->testConnection($host, $port, $username, $password, $encryption, $fromEmail, $fromName, $testEmail);

            if ($result['success']) {
                CLI::write("✓ SUCCESS with {$description}!", 'green');
                CLI::write("  Recommended setting: encryption = '{$encryption}'", 'green');
                CLI::newLine();
                return;
            } else {
                CLI::write("✗ Failed: {$result['error']}", 'red');
                CLI::newLine();
            }
        }

        CLI::write('All encryption methods failed. Check your SMTP settings.', 'yellow');
    }

    private function testConnection($host, $port, $username, $password, $encryption, $fromEmail, $fromName, $testEmail)
    {
        try {
            $email = \Config\Services::email();
            
            $config = [
                'protocol' => 'smtp',
                'SMTPHost' => $host,
                'SMTPUser' => $username,
                'SMTPPass' => $password,
                'SMTPPort' => $port,
                'SMTPCrypto' => $encryption,
                'SMTPTimeout' => 10,
                'mailType' => 'html',
                'charset' => 'UTF-8',
            ];

            $email->initialize($config);
            $email->setFrom($fromEmail, $fromName);
            $email->setTo($testEmail);
            $email->setSubject('SMTP Test - ' . strtoupper($encryption ?: 'none'));
            $email->setMessage('<p>This is a test email using ' . ($encryption ?: 'no encryption') . '</p>');

            if ($email->send()) {
                $debug = $email->printDebugger(['headers']);
                // Check for errors even if send() returns true
                if (stripos($debug, 'SMTP error') !== false || 
                    stripos($debug, '503') !== false ||
                    stripos($debug, 'Unable to send') !== false) {
                    return [
                        'success' => false,
                        'error' => 'SMTP error in debug output: ' . substr($debug, 0, 200)
                    ];
                }
                return ['success' => true];
            } else {
                $error = $email->printDebugger(['headers', 'subject', 'body']);
                return [
                    'success' => false,
                    'error' => substr($error, 0, 300)
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
