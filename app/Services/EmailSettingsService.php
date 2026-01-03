<?php

namespace App\Services;

use App\Models\EmailSettings;
use CodeIgniter\Email\Email;
use Exception;

class EmailSettingsService
{
    protected $emailSettingsModel;

    public function __construct()
    {
        $this->emailSettingsModel = new EmailSettings();
    }

    /**
     * Get all email settings
     */
    public function getEmailSettings(): array
    {
        try {
            $settings = $this->emailSettingsModel->getSettings();
            
            if (!$settings) {
                return [
                    'success' => true,
                    'data' => []
                ];
            }

            // Mask sensitive data
            if (!empty($settings['password'])) {
                $settings['password'] = '••••••••••••••••';
            }

            // Set defaults for fields that might not exist in current DB
            $defaults = [
                'smtp_timeout' => 30,
                'reply_to_email' => '',
                'bcc_email' => '',
                'email_header' => '',
                'email_footer' => '',
                'email_enabled' => 1,
                'debug_mode' => 0,
                'html_emails' => 1
            ];

            foreach ($defaults as $key => $defaultValue) {
                if (!isset($settings[$key])) {
                    $settings[$key] = $defaultValue;
                }
            }

            return [
                'success' => true,
                'data' => $settings
            ];
        } catch (Exception $e) {
            log_message('error', 'EmailSettingsService::getEmailSettings - ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to retrieve email settings'
            ];
        }
    }

    /**
     * Get raw email settings without masking sensitive data (for internal use)
     */
    public function getRawEmailSettings(): array
    {
        try {
            $settings = $this->emailSettingsModel->getSettings();
            
            if (!$settings) {
                return [
                    'success' => true,
                    'data' => []
                ];
            }

            // Set defaults for fields that might not exist in current DB
            $defaults = [
                'smtp_timeout' => 30,
                'reply_to_email' => '',
                'bcc_email' => '',
                'email_header' => '',
                'email_footer' => '',
                'email_enabled' => 1,
                'debug_mode' => 0,
                'html_emails' => 1
            ];

            foreach ($defaults as $key => $defaultValue) {
                if (!isset($settings[$key])) {
                    $settings[$key] = $defaultValue;
                }
            }

            return [
                'success' => true,
                'data' => $settings
            ];
        } catch (Exception $e) {
            log_message('error', 'EmailSettingsService::getRawEmailSettings - ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to retrieve email settings'
            ];
        }
    }

    /**
     * Save email settings
     */
    public function saveEmailSettings(array $data): array
    {
        try {
            // Remove CSRF token if present
            unset($data[csrf_token()]);
            
            // Handle checkbox values (set defaults if not present)
            $data['email_enabled'] = isset($data['email_enabled']) ? 1 : 0;
            $data['debug_mode'] = isset($data['debug_mode']) ? 1 : 0;
            $data['html_emails'] = isset($data['html_emails']) ? 1 : 0;

            // Don't update password if it's masked
            if (isset($data['password']) && $data['password'] === '••••••••••••••••') {
                unset($data['password']);
            }
            
            // Filter out fields that don't exist in current DB structure
            $allowedFields = ['host', 'username', 'password', 'encryption', 'port', 'from_address', 'from_name', 'email_header', 'email_footer'];
            $filteredData = [];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $filteredData[$field] = $data[$field];
                }
            }
            
            // Set update timestamp
            $filteredData['updated_at'] = date('Y-m-d H:i:s');

            $result = $this->emailSettingsModel->updateSettings($filteredData);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Email settings saved successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to save email settings'
                ];
            }
        } catch (Exception $e) {
            log_message('error', 'EmailSettingsService::saveEmailSettings - ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while saving email settings'
            ];
        }
    }

    /**
     * Test email configuration by sending a test email
     */
    public function sendTestEmail(array $settings, string $testEmail): array
    {
        try {
            // Get current settings if password is masked
            if (isset($settings['password']) && $settings['password'] === '••••••••••••••••') {
                $currentSettings = $this->emailSettingsModel->first();
                if ($currentSettings) {
                    $settings['password'] = $currentSettings['password'];
                }
            }

            // Configure email with provided settings
            $config = [
                'protocol' => 'smtp',
                'SMTPHost' => $settings['host'] ?? '',
                'SMTPUser' => $settings['username'] ?? '',
                'SMTPPass' => $settings['password'] ?? '',
                'SMTPPort' => (int)($settings['port'] ?? 587),
                'SMTPCrypto' => $settings['encryption'] ?? 'tls',
                'SMTPTimeout' => (int)($settings['smtp_timeout'] ?? 30),
                'mailType' => ($settings['html_emails'] ?? 1) ? 'html' : 'text',
                'charset' => 'utf-8',
                'newline' => "\r\n"
            ];

            $emailQueueModel = new \App\Models\EmailQueue();

            // Set email details
            $fromEmail = $settings['from_address'] ?? $settings['username'] ?? 'noreply@example.com';
            $fromName = $settings['from_name'] ?? 'KEWASNET';
            $subject = 'Test Email Configuration - KEWASNET';
            
            // Create test email content
            $message = $this->getTestEmailTemplate($settings);

            // Prepare BCC if set
            $bcc = null;
            if (!empty($settings['bcc_email'])) {
                $bcc = [$settings['bcc_email']];
            }

            if ($emailQueueModel->queueEmail($testEmail, $subject, $message, $bcc, $fromEmail, $fromName)) {
                return [
                    'success' => true,
                    'message' => 'Test email queued successfully!'
                ];
            } else {
                log_message('error', 'EmailSettingsService::sendTestEmail - Failed to queue test email');
                return [
                    'success' => false,
                    'message' => 'Failed to send test email. Please check your SMTP configuration.'
                ];
            }
        } catch (Exception $e) {
            log_message('error', 'EmailSettingsService::sendTestEmail - ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error sending test email: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get test email template
     */
    private function getTestEmailTemplate(array $settings): string
    {
        $header = $settings['email_header'] ?? '';
        $footer = $settings['email_footer'] ?? '';
        
        // Template variables
        $variables = [
            '{{site_name}}' => 'KEWASNET',
            '{{site_url}}' => base_url(),
            '{{current_year}}' => date('Y'),
            '{{user_name}}' => 'Administrator'
        ];
        
        // Replace variables in header and footer
        $header = str_replace(array_keys($variables), array_values($variables), $header);
        $footer = str_replace(array_keys($variables), array_values($variables), $footer);
        
        $testContent = '
            <div style="background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px;">
                <h2 style="color: #333; margin-top: 0;">Email Configuration Test</h2>
                <p>Congratulations! Your email configuration is working correctly.</p>
                <p><strong>Test Details:</strong></p>
                <ul>
                    <li>SMTP Host: ' . ($settings['host'] ?? 'Not configured') . '</li>
                    <li>SMTP Port: ' . ($settings['port'] ?? 'Not configured') . '</li>
                    <li>Encryption: ' . strtoupper($settings['encryption'] ?? 'None') . '</li>
                    <li>From Email: ' . ($settings['from_address'] ?? 'Not configured') . '</li>
                    <li>From Name: ' . ($settings['from_name'] ?? 'Not configured') . '</li>
                    <li>Test Time: ' . date('Y-m-d H:i:s') . '</li>
                </ul>
                <p style="color: #28a745; font-weight: bold;">✓ Your SMTP configuration is working properly!</p>
            </div>
        ';

        // If no templates are set, use default wrapper
        if (empty($header) && empty($footer)) {
            return '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                ' . $testContent . '
            </div>';
        }

        // Use templates if available
        return $header . $testContent . $footer;
    }

    /**
     * Process email template with header and footer
     * 
     * @param string $content The main email content
     * @param array $variables Additional template variables
     * @param string $recipientName Optional recipient name
     * @return string Processed email template
     */
    public function processEmailTemplate(string $content, array $variables = [], string $recipientName = ''): string
    {
        try {
            // Get email settings to retrieve header and footer
            $settings = $this->emailSettingsModel->getSettings();
            
            if (!$settings) {
                return $content; // Return content as-is if no settings
            }
            
            $header = $settings['email_header'] ?? '';
            $footer = $settings['email_footer'] ?? '';
            
            // Default template variables
            $defaultVariables = [
                '{{site_name}}' => 'KEWASNET',
                '{{site_url}}' => base_url(),
                '{{current_year}}' => date('Y'),
                '{{user_name}}' => $recipientName ?: 'User'
            ];
            
            // Merge with provided variables
            $allVariables = array_merge($defaultVariables, $variables);
            
            // Replace variables in all parts
            $header = str_replace(array_keys($allVariables), array_values($allVariables), $header);
            $content = str_replace(array_keys($allVariables), array_values($allVariables), $content);
            $footer = str_replace(array_keys($allVariables), array_values($allVariables), $footer);
            
            // If no templates, return content with basic wrapper
            if (empty($header) && empty($footer)) {
                return '
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                    <div style="background: white; padding: 20px;">
                        ' . $content . '
                    </div>
                </div>';
            }
            
            // Return content with header and footer
            return $header . $content . $footer;
            
        } catch (Exception $e) {
            log_message('error', 'EmailSettingsService::processEmailTemplate - ' . $e->getMessage());
            return $content; // Return original content on error
        }
    }

    /**
     * Verify user password for sensitive data access
     */
    public function verifyPasswordAndGetSensitiveData(string $password, string $fieldName): array
    {
        try {
            // Get current user from session (using CIAuth session structure)
            $session = session();
            $userId = $session->get('id');
            
            if (!$userId) {
                return [
                    'success' => false,
                    'message' => 'User session not found'
                ];
            }

            // Check if user is actually logged in and is admin
            if (!$session->get('isLoggedIn') || !$session->get('isAdmin')) {
                return [
                    'success' => false,
                    'message' => 'User not authenticated or not authorized'
                ];
            }

            // Load user model to verify password (using the same model as CIAuth)
            $userModel = model('UserModel');
            $user = $userModel->find($userId);
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found'
                ];
            }

            // Verify password using the same Hash library as CIAuth
            $hash = new \App\Libraries\Hash();
            if (!$hash::verify($password, $user['password'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid password'
                ];
            }

            // Get email settings
            $settings = $this->emailSettingsModel->first();
            
            if (!$settings || empty($settings[$fieldName])) {
                return [
                    'success' => false,
                    'message' => 'Sensitive data not found'
                ];
            }

            return [
                'success' => true,
                'data' => $settings[$fieldName]
            ];
        } catch (Exception $e) {
            log_message('error', 'EmailSettingsService::verifyPasswordAndGetSensitiveData - ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to verify password'
            ];
        }
    }
}
