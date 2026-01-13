<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Email;

/**
 * Test Email Configuration Command
 * 
 * Tests email configuration for both production (database) and development (env vars).
 * 
 * Usage:
 *   php spark email:test [email@example.com]  - Test with optional recipient email
 *   php spark email:test --env=production    - Force production mode
 *   php spark email:test --env=development   - Force development mode
 *   php spark email:test --direct            - Send directly (bypass queue)
 */
class TestEmailConfig extends BaseCommand
{
    protected $group       = 'Email';
    protected $name        = 'email:test';
    protected $description = 'Test email configuration for production and development';

    public function run(array $params)
    {
        CLI::write('========================================', 'cyan');
        CLI::write('  Email Configuration Test', 'cyan');
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

        // Check environment override
        $envOverride = CLI::getOptionString('env');
        $sendDirect = CLI::getOption('direct') !== null;

        // Get current environment
        $currentEnv = env('CI_ENVIRONMENT', 'production');
        if ($envOverride) {
            $currentEnv = $envOverride;
            CLI::write("Environment override: {$currentEnv}", 'yellow');
        }

        CLI::write("Current environment: {$currentEnv}", 'yellow');
        CLI::write("Recipient: {$recipientEmail}", 'yellow');
        CLI::write("Send mode: " . ($sendDirect ? 'Direct (bypass queue)' : 'Queued'), 'yellow');
        CLI::newLine();

        // Load and display email configuration
        $this->displayEmailConfig($currentEnv);

        // Test email sending
        CLI::newLine();
        CLI::write('Testing email sending...', 'yellow');
        
        if ($sendDirect) {
            $result = $this->sendDirectEmail($recipientEmail);
        } else {
            $result = $this->sendQueuedEmail($recipientEmail);
        }

        // Display results
        CLI::newLine();
        if ($result['success']) {
            CLI::write('✓ ' . $result['message'], 'green');
            if (!$sendDirect) {
                CLI::write('Note: Email has been queued. Run "php spark email:process --once" to process it.', 'cyan');
            }
        } else {
            CLI::write('✗ ' . $result['message'], 'red');
            if (isset($result['error'])) {
                CLI::write('Error: ' . $result['error'], 'red');
            }
        }
    }

    /**
     * Display current email configuration
     */
    private function displayEmailConfig(string $environment)
    {
        CLI::write('Email Configuration:', 'cyan');
        CLI::write('-------------------', 'cyan');

        $emailConfig = config('Email');

        CLI::write('Protocol: ' . ($emailConfig->protocol ?? 'not set'), 'white');
        CLI::write('SMTP Host: ' . ($emailConfig->SMTPHost ?? 'not set'), 'white');
        CLI::write('SMTP Port: ' . ($emailConfig->SMTPPort ?? 'not set'), 'white');
        CLI::write('SMTP User: ' . ($emailConfig->SMTPUser ? substr($emailConfig->SMTPUser, 0, 3) . '***' : 'not set'), 'white');
        CLI::write('SMTP Pass: ' . ($emailConfig->SMTPPass ? '***' : 'not set'), 'white');
        CLI::write('SMTP Crypto: ' . ($emailConfig->SMTPCrypto ?? 'not set'), 'white');
        CLI::write('From Email: ' . ($emailConfig->fromEmail ?? 'not set'), 'white');
        CLI::write('From Name: ' . ($emailConfig->fromName ?? 'not set'), 'white');

        // Check configuration source
        if ($environment === 'production') {
            CLI::write('Config Source: Database (email_settings table)', 'yellow');
            
            // Check if database settings exist
            try {
                $db = \Config\Database::connect();
                $builder = $db->table('email_settings');
                $settings = $builder->orderBy('id', 'DESC')->get(1)->getRowArray();
                
                if ($settings) {
                    CLI::write('✓ Database settings found', 'green');
                } else {
                    CLI::write('⚠ No database settings found, using env vars fallback', 'yellow');
                }
            } catch (\Exception $e) {
                CLI::write('⚠ Database error: ' . $e->getMessage(), 'yellow');
                CLI::write('  Using environment variables fallback', 'yellow');
            }
        } else {
            CLI::write('Config Source: Environment variables (.env file)', 'yellow');
            
            // Check if env vars are set
            $envVars = [
                'EMAIL_HOST'         => env('EMAIL_HOST'),
                'EMAIL_USERNAME'     => env('EMAIL_USERNAME'),
                'EMAIL_PASSWORD'     => env('EMAIL_PASSWORD'),
                'EMAIL_PORT'         => env('EMAIL_PORT'),
                'EMAIL_FROM_ADDRESS' => env('EMAIL_FROM_ADDRESS'),
                'EMAIL_FROM_NAME'    => env('EMAIL_FROM_NAME'),
            ];

            $missingVars = [];
            foreach ($envVars as $key => $value) {
                if (empty($value)) {
                    $missingVars[] = $key;
                }
            }

            if (empty($missingVars)) {
                CLI::write('✓ All required environment variables are set', 'green');
            } else {
                CLI::write('⚠ Missing environment variables: ' . implode(', ', $missingVars), 'yellow');
            }
        }
    }

    /**
     * Send email directly (bypass queue)
     */
    private function sendDirectEmail(string $recipientEmail): array
    {
        try {
            $email = \Config\Services::email();
            $emailConfig = config('Email');

            CLI::write('Testing SMTP connection...', 'yellow');
            
            // Test SMTP connection first
            $connectionTest = $this->testSMTPConnection($emailConfig);
            if (!$connectionTest['success']) {
                return [
                    'success' => false,
                    'message' => 'SMTP connection failed',
                    'error' => $connectionTest['error']
                ];
            }
            
            CLI::write('✓ SMTP connection successful', 'green');

            // Configure email
            $email->clear();
            $email->setFrom($emailConfig->fromEmail, $emailConfig->fromName ?? 'KEWASNET');
            $email->setTo($recipientEmail);
            $email->setSubject('Email Configuration Test - KEWASNET');
            
            $message = $this->getTestEmailMessage();
            $email->setMessage($message);

            CLI::write('Sending email...', 'yellow');

            // Send email with debug output
            if ($email->send()) {
                CLI::write('✓ Email send() returned true', 'green');
                
                // Get debug info even on success
                $debugInfo = $email->printDebugger(['headers', 'subject', 'body']);
                CLI::write('Debug info:', 'cyan');
                CLI::write($debugInfo);
                
                return [
                    'success' => true,
                    'message' => 'Test email sent successfully! Check your inbox (and spam folder).'
                ];
            } else {
                $error = $email->printDebugger(['headers', 'subject', 'body']);
                CLI::write('✗ Email send() returned false', 'red');
                CLI::write('Debug output:', 'red');
                CLI::write($error);
                
                return [
                    'success' => false,
                    'message' => 'Failed to send test email',
                    'error' => $error
                ];
            }
        } catch (\Exception $e) {
            CLI::write('✗ Exception occurred: ' . $e->getMessage(), 'red');
            CLI::write('Stack trace:', 'red');
            CLI::write($e->getTraceAsString());
            
            return [
                'success' => false,
                'message' => 'Error sending test email',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Test SMTP connection
     */
    private function testSMTPConnection($emailConfig): array
    {
        try {
            // Check if required settings are present
            if (empty($emailConfig->SMTPHost)) {
                return [
                    'success' => false,
                    'error' => 'SMTP Host is not configured'
                ];
            }

            if (empty($emailConfig->SMTPPort)) {
                return [
                    'success' => false,
                    'error' => 'SMTP Port is not configured'
                ];
            }

            CLI::write("Connecting to {$emailConfig->SMTPHost}:{$emailConfig->SMTPPort}...", 'cyan');

            // Try to connect to SMTP server
            $connection = @fsockopen(
                $emailConfig->SMTPHost,
                $emailConfig->SMTPPort,
                $errno,
                $errstr,
                5
            );

            if (!$connection) {
                return [
                    'success' => false,
                    'error' => "Cannot connect to SMTP server: {$errstr} (Error #{$errno})"
                ];
            }

            fclose($connection);

            return [
                'success' => true
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Connection test failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send email via queue
     */
    private function sendQueuedEmail(string $recipientEmail): array
    {
        try {
            $emailQueueModel = new \App\Models\EmailQueue();
            $emailConfig = config('Email');

            $subject = 'Email Configuration Test - KEWASNET';
            $message = $this->getTestEmailMessage();

            $fromEmail = $emailConfig->fromEmail ?? env('EMAIL_FROM_ADDRESS', 'info@kewasnet.co.ke');
            $fromName = $emailConfig->fromName ?? env('EMAIL_FROM_NAME', 'KEWASNET');

            if ($emailQueueModel->queueEmail($recipientEmail, $subject, $message, null, $fromEmail, $fromName)) {
                return [
                    'success' => true,
                    'message' => 'Test email queued successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to queue test email'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error queueing test email',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get test email message template
     */
    private function getTestEmailMessage(): string
    {
        $emailConfig = config('Email');
        $environment = env('CI_ENVIRONMENT', 'production');
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
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Email Configuration Test</h1>
        </div>
        <div class="content">
            <div class="success">
                <h2>✓ Email Configuration Working!</h2>
                <p>This is a test email to verify your email configuration is working correctly.</p>
            </div>
            
            <div class="info">
                <h3>Configuration Details:</h3>
                <ul>
                    <li><strong>Environment:</strong> {$environment}</li>
                    <li><strong>SMTP Host:</strong> {$emailConfig->SMTPHost}</li>
                    <li><strong>SMTP Port:</strong> {$emailConfig->SMTPPort}</li>
                    <li><strong>SMTP Encryption:</strong> {$emailConfig->SMTPCrypto}</li>
                    <li><strong>From Email:</strong> {$emailConfig->fromEmail}</li>
                    <li><strong>From Name:</strong> {$emailConfig->fromName}</li>
                    <li><strong>Test Time:</strong> {$timestamp}</li>
                </ul>
            </div>
            
            <p>If you received this email, your email configuration is working correctly!</p>
        </div>
        <div class="footer">
            <p>This is an automated test email from KEWASNET</p>
            <p>Generated at: {$timestamp}</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
