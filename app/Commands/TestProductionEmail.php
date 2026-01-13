<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Test Production Email Configuration
 * 
 * Tests email configuration using database settings (production mode).
 * This bypasses the environment check and directly uses database settings.
 * 
 * Usage:
 *   php spark email:test-production [email@example.com]
 */
class TestProductionEmail extends BaseCommand
{
    protected $group       = 'Email';
    protected $name        = 'email:test-production';
    protected $description = 'Test production email configuration from database';

    public function run(array $params)
    {
        CLI::write('========================================', 'cyan');
        CLI::write('  Production Email Configuration Test', 'cyan');
        CLI::write('========================================', 'cyan');
        CLI::newLine();

        // Get recipient email
        $recipientEmail = $params[0] ?? null;
        if (!$recipientEmail) {
            $recipientEmail = CLI::prompt('Enter recipient email address', null, 'required');
        }

        if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            CLI::error('Invalid email address: ' . $recipientEmail);
            return;
        }

        CLI::write("Recipient: {$recipientEmail}", 'yellow');
        CLI::newLine();

        // Load settings from database
        try {
            $db = \Config\Database::connect();
            $builder = $db->table('email_settings');
            $settings = $builder->orderBy('id', 'DESC')->get(1)->getRowArray();

            if (!$settings) {
                CLI::error('No email settings found in database!');
                CLI::write('Please configure email settings in the admin panel first.', 'yellow');
                return;
            }

            CLI::write('Database Settings:', 'cyan');
            CLI::write('------------------', 'cyan');
            CLI::write('Host: ' . ($settings['host'] ?? 'not set'), 'white');
            CLI::write('Port: ' . ($settings['port'] ?? 'not set'), 'white');
            CLI::write('Username: ' . ($settings['username'] ?? 'not set'), 'white');
            CLI::write('Password: ' . ($settings['password'] ? '***' : 'not set'), 'white');
            CLI::write('Encryption: ' . ($settings['encryption'] ?? 'not set'), 'white');
            CLI::write('From Address: ' . ($settings['from_address'] ?? 'not set'), 'white');
            CLI::write('From Name: ' . ($settings['from_name'] ?? 'not set'), 'white');
            CLI::write('Enabled: ' . (($settings['email_enabled'] ?? 1) ? 'Yes' : 'No'), 'white');
            CLI::newLine();

            // Check if email is enabled
            if (($settings['email_enabled'] ?? 1) == 0) {
                CLI::error('Email is disabled in database settings!');
                CLI::write('Set email_enabled = 1 to enable email sending.', 'yellow');
                return;
            }

            // Validate settings
            $errors = [];
            if (empty($settings['host'])) {
                $errors[] = 'SMTP Host is missing';
            }
            if (empty($settings['port'])) {
                $errors[] = 'SMTP Port is missing';
            }
            if (empty($settings['username'])) {
                $errors[] = 'SMTP Username is missing';
            }
            if (empty($settings['password'])) {
                $errors[] = 'SMTP Password is missing';
            }
            if (empty($settings['from_address'])) {
                $errors[] = 'From Address is missing';
            }
            if (!filter_var($settings['from_address'] ?? '', FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'From Address is invalid: ' . ($settings['from_address'] ?? '');
            }

            if (!empty($errors)) {
                CLI::error('Configuration errors found:');
                foreach ($errors as $error) {
                    CLI::write('  - ' . $error, 'red');
                }
                return;
            }

            // Test SMTP connection
            CLI::write('Testing SMTP connection...', 'yellow');
            $connectionTest = $this->testSMTPConnection($settings);
            
            if (!$connectionTest['success']) {
                CLI::error('SMTP connection failed!');
                CLI::write('Error: ' . $connectionTest['error'], 'red');
                CLI::newLine();
                CLI::write('Common issues:', 'yellow');
                CLI::write('  1. SMTP host might be wrong (should be mail.kewasnet.co.ke or smtp.kewasnet.co.ke)', 'cyan');
                CLI::write('  2. Port might be incorrect (587 for TLS, 465 for SSL, 25 for no encryption)', 'cyan');
                CLI::write('  3. Firewall might be blocking the connection', 'cyan');
                CLI::write('  4. SMTP server might not be configured on the server', 'cyan');
                return;
            }

            CLI::write('✓ SMTP connection successful', 'green');
            CLI::newLine();

            // Send test email
            CLI::write('Sending test email...', 'yellow');
            $result = $this->sendTestEmail($settings, $recipientEmail);

            CLI::newLine();
            if ($result['success']) {
                CLI::write('✓ ' . $result['message'], 'green');
                CLI::write('Check your inbox (and spam folder) for the test email.', 'cyan');
            } else {
                CLI::write('✗ ' . $result['message'], 'red');
                if (isset($result['error'])) {
                    CLI::write('Error: ' . $result['error'], 'red');
                }
            }

        } catch (\Exception $e) {
            CLI::error('Error: ' . $e->getMessage());
            CLI::write('File: ' . $e->getFile() . ':' . $e->getLine());
        }
    }

    /**
     * Test SMTP connection
     */
    private function testSMTPConnection(array $settings): array
    {
        $host = $settings['host'] ?? '';
        $port = (int) ($settings['port'] ?? 587);

        if (empty($host)) {
            return [
                'success' => false,
                'error' => 'SMTP Host is not configured'
            ];
        }

        CLI::write("  Connecting to {$host}:{$port}...", 'cyan');

        // Try to connect
        $connection = @fsockopen($host, $port, $errno, $errstr, 10);

        if (!$connection) {
            return [
                'success' => false,
                'error' => "Cannot connect to {$host}:{$port} - {$errstr} (Error #{$errno})"
            ];
        }

        fclose($connection);
        return ['success' => true];
    }

    /**
     * Send test email using database settings
     */
    private function sendTestEmail(array $settings, string $recipientEmail): array
    {
        try {
            // Create email service with custom config
            $email = \Config\Services::email();
            
            // Configure with database settings
            // Normalize encryption to lowercase
            $encryption = strtolower($settings['encryption'] ?? 'tls');
            if (!in_array($encryption, ['tls', 'ssl', ''])) {
                $encryption = 'tls'; // Default to TLS if invalid
            }

            $config = [
                'protocol'    => 'smtp',
                'SMTPHost'    => $settings['host'],
                'SMTPUser'    => $settings['username'],
                'SMTPPass'    => $settings['password'],
                'SMTPPort'    => (int) $settings['port'],
                'SMTPCrypto'  => $encryption,
                'SMTPTimeout' => (int) ($settings['smtp_timeout'] ?? 30),
                'mailType'    => 'html',
                'charset'     => 'UTF-8',
            ];

            CLI::write("Using encryption: {$encryption}", 'cyan');

            $email->initialize($config);

            // Set email details
            $fromEmail = $settings['from_address'];
            $fromName = $settings['from_name'] ?? 'KEWASNET';
            
            $email->setFrom($fromEmail, $fromName);
            $email->setTo($recipientEmail);
            $email->setSubject('Production Email Test - KEWASNET');
            
            $message = $this->getTestEmailMessage($settings);
            $email->setMessage($message);

            // Send email
            if ($email->send()) {
                // Get debug info even on success to see SMTP responses
                $debugInfo = $email->printDebugger(['headers', 'subject', 'body']);
                CLI::write('Debug output:', 'cyan');
                CLI::write($debugInfo);
                
                return [
                    'success' => true,
                    'message' => 'Test email sent successfully!'
                ];
            } else {
                $error = $email->printDebugger(['headers', 'subject', 'body']);
                CLI::write('Debug output:', 'red');
                CLI::write($error);
                
                return [
                    'success' => false,
                    'message' => 'Failed to send test email',
                    'error' => $error
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error sending test email',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get test email message
     */
    private function getTestEmailMessage(array $settings): string
    {
        $timestamp = date('Y-m-d H:i:s');
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f9f9f9; padding: 20px; margin-top: 20px; }
        .info { background-color: #e7f3ff; padding: 15px; margin: 15px 0; border-left: 4px solid #2196F3; }
        .success { background-color: #d4edda; padding: 15px; margin: 15px 0; border-left: 4px solid #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Production Email Test</h1>
        </div>
        <div class="content">
            <div class="success">
                <h2>✓ Production Email Configuration Working!</h2>
                <p>This email was sent using database settings (production mode).</p>
            </div>
            
            <div class="info">
                <h3>Configuration Used:</h3>
                <ul>
                    <li><strong>SMTP Host:</strong> {$settings['host']}</li>
                    <li><strong>SMTP Port:</strong> {$settings['port']}</li>
                    <li><strong>Encryption:</strong> {$settings['encryption']}</li>
                    <li><strong>From Email:</strong> {$settings['from_address']}</li>
                    <li><strong>From Name:</strong> {$settings['from_name']}</li>
                    <li><strong>Test Time:</strong> {$timestamp}</li>
                </ul>
            </div>
            
            <p>If you received this email, your production email configuration is working correctly!</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
