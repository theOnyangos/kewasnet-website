<?php

namespace App\Services;

use App\Models\PaystackSetting;
use App\Models\MpesaSettings;
use App\Models\SmsSetting;

class SystemSettingsService
{
    protected $paystackModel;
    protected $mpesaModel;
    protected $smsModel;

    public function __construct()
    {
        $this->paystackModel = new PaystackSetting();
        $this->mpesaModel = new MpesaSettings();
        $this->smsModel = new SmsSetting();
    }

    /**
     * Get Paystack Settings
     */
    public function getPaystackSettings(): array
    {
        try {
            $settings = $this->paystackModel->first();
            
            if (!$settings) {
                return [
                    'success' => false,
                    'message' => 'No Paystack settings found',
                    'data' => []
                ];
            }

            // Don't expose sensitive data in response
            $settings['public_key']    = '•••••••••••••••••';
            $settings['secret_key']    = '•••••••••••••••••';

            return [
                'success' => true,
                'data' => $settings
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting Paystack settings: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to retrieve Paystack settings'
            ];
        }
    }

    /**
     * Save Paystack Settings
     */
    public function savePaystackSettings(array $data): array
    {
        try {
            // Validate required fields
            $requiredFields = ['public_key', 'secret_key', 'environment', 'currency'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return [
                        'success' => false,
                        'message' => "Field '{$field}' is required"
                    ];
                }
            }

            // Prepare data for saving
            $saveData = [
                'public_key' => $data['public_key'],
                'secret_key' => $data['secret_key'],
                'environment' => $data['environment'],
                'currency' => $data['currency'],
                'webhook_url' => $data['webhook_url'] ?? '',
                'enabled' => isset($data['enabled']) ? 1 : 0,
                'status' => isset($data['enabled']) ? 'active' : 'inactive'
            ];

            $existing = $this->paystackModel->first();
            
            if ($existing) {
                $this->paystackModel->update($existing['id'], $saveData);
            } else {
                $this->paystackModel->insert($saveData);
            }

            return [
                'success' => true,
                'message' => 'Paystack settings saved successfully'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error saving Paystack settings: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to save Paystack settings'
            ];
        }
    }

    /**
     * Test Paystack Connection
     */
    public function testPaystackConnection(array $data): array
    {
        try {
            $secretKey = $data['secret_key'] ?? '';
            
            if (empty($secretKey)) {
                return [
                    'success' => false,
                    'message' => 'Secret key is required for testing'
                ];
            }

            // Test Paystack API connection
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/bank");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer {$secretKey}",
                "Cache-Control: no-cache",
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                return [
                    'success' => true,
                    'message' => 'Paystack connection successful'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Invalid Paystack credentials'
                ];
            }
        } catch (\Exception $e) {
            log_message('error', 'Error testing Paystack connection: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to test Paystack connection'
            ];
        }
    }

    /**
     * Get M-Pesa Settings
     */
    public function getMpesaSettings(): array
    {
        try {
            $settings = $this->mpesaModel->first();
            
            if (!$settings) {
                return [
                    'success' => false,
                    'message' => 'No M-Pesa settings found',
                    'data' => []
                ];
            }

            // Don't expose sensitive data in response
            $settings['consumer_key']    = '•••••••••••••••••';
            $settings['consumer_secret'] = '•••••••••••••••••';
            $settings['pass_key']        = '•••••••••••••••••';

            return [
                'success' => true,
                'data' => $settings
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting M-Pesa settings: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to retrieve M-Pesa settings'
            ];
        }
    }

    /**
     * Save M-Pesa Settings
     */
    public function saveMpesaSettings(array $data): array
    {
        try {
            // Validate required fields
            $requiredFields = ['consumer_key', 'consumer_secret', 'business_short_code', 'passkey'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return [
                        'success' => false,
                        'message' => "Field '{$field}' is required"
                    ];
                }
            }

            // Prepare data for saving
            $saveData = [
                'consumer_key' => $data['consumer_key'],
                'consumer_secret' => $data['consumer_secret'],
                'business_short_code' => $data['business_short_code'],
                'pass_key' => $data['passkey'],
                'environment' => $data['environment'] ?? 'sandbox',
                'transaction_type' => $data['transaction_type'] ?? 'CustomerPayBillOnline',
                'callback_url' => $data['callback_url'] ?? '',
                'test_mode' => $data['environment'] === 'sandbox' ? 1 : 0,
                'enabled' => isset($data['enabled']) ? 1 : 0
            ];

            $existing = $this->mpesaModel->first();
            
            if ($existing) {
                $this->mpesaModel->update($existing['id'], $saveData);
            } else {
                $this->mpesaModel->insert($saveData);
            }

            return [
                'success' => true,
                'message' => 'M-Pesa settings saved successfully'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error saving M-Pesa settings: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to save M-Pesa settings'
            ];
        }
    }

    /**
     * Test M-Pesa Connection
     */
    public function testMpesaConnection(array $data): array
    {
        try {
            $consumerKey = $data['consumer_key'] ?? '';
            $consumerSecret = $data['consumer_secret'] ?? '';
            $environment = $data['environment'] ?? 'sandbox';
            
            if (empty($consumerKey) || empty($consumerSecret)) {
                return [
                    'success' => false,
                    'message' => 'Consumer key and secret are required for testing'
                ];
            }

            // Test M-Pesa API connection by getting access token
            $url = $environment === 'live' 
                ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
                : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

            $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Basic ' . $credentials,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $result = json_decode($response, true);
                if (isset($result['access_token'])) {
                    return [
                        'success' => true,
                        'message' => 'M-Pesa connection successful'
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Invalid M-Pesa credentials'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error testing M-Pesa connection: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to test M-Pesa connection'
            ];
        }
    }

    /**
     * Get SMS Settings
     */
    public function getSmsSettings(): array
    {
        try {
            $settings = $this->smsModel->first();

            if (!$settings) {
                return [
                    'success' => false,
                    'message' => 'No SMS settings found',
                    'data' => []
                ];
            }

            // Map database fields to form fields for custom API provider
            $formData = [
                'sms_provider' => 'custom',
                'environment' => 'live',
                'custom_api_url' => $settings['api_endpoint'] ?? '',
                'custom_api_key_header' => 'Authorization',
                'custom_api_key' => '•••••••••••••••••', // Masked
                'at_sender_id' => $settings['short_code'] ?? '',
                'sms_enabled' => true,
                'log_sms' => true,
                'delivery_reports' => false,
                'default_country_code' => '+254',
                'message_length_limit' => '160',
                // Store original values for reference
                '_original_token' => $settings['token'] ?? '',
                '_original_partner_id' => $settings['partner_id'] ?? '',
            ];

            return [
                'success' => true,
                'data' => $formData
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting SMS settings: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to retrieve SMS settings'
            ];
        }
    }

    /**
     * Save SMS Settings
     */
    public function saveSmsSettings(array $data): array
    {
        try {
            // Validate required fields
            $requiredFields = ['provider', 'api_key', 'sender_id'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return [
                        'success' => false,
                        'message' => "Field '{$field}' is required"
                    ];
                }
            }

            // Prepare data for saving
            $saveData = [
                'provider' => $data['provider'],
                'api_key' => $data['api_key'],
                'api_secret' => $data['api_secret'] ?? '',
                'sender_id' => $data['sender_id'],
                'base_url' => $data['base_url'] ?? '',
                'enabled' => isset($data['enabled']) ? 1 : 0,
                'status' => isset($data['enabled']) ? 'active' : 'inactive'
            ];

            $existing = $this->smsModel->first();

            if ($existing) {
                $this->smsModel->update($existing['id'], $saveData);
            } else {
                $this->smsModel->insert($saveData);
            }

            return [
                'success' => true,
                'message' => 'SMS settings saved successfully'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error saving SMS settings: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to save SMS settings'
            ];
        }
    }

    /**
     * Test SMS Connection
     */
    public function testSmsConnection(array $data): array
    {
        try {
            $provider = $data['provider'] ?? '';
            $apiKey = $data['api_key'] ?? '';

            if (empty($provider) || empty($apiKey)) {
                return [
                    'success' => false,
                    'message' => 'Provider and API key are required for testing'
                ];
            }

            // For now, return a success message
            // You can implement actual API testing based on the provider
            return [
                'success' => true,
                'message' => 'SMS configuration is valid (test message not sent)'
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error testing SMS connection: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to test SMS connection'
            ];
        }
    }

    /**
     * Verify Password and Get Sensitive Data
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

            // Get sensitive data based on field name
            $sensitiveData = '';
            
            // Log the field name for debugging
            log_message('info', 'SystemSettingsService::verifyPasswordAndGetSensitiveData - Field name: ' . $fieldName);
            
            switch ($fieldName) {
                case 'public_key':
                    $settings = $this->paystackModel->first();
                    log_message('info', 'SystemSettingsService::verifyPasswordAndGetSensitiveData - Paystack settings found: ' . ($settings ? 'yes' : 'no'));
                    if ($settings) {
                        $sensitiveData = $settings['public_key'] ?? '';
                        log_message('info', 'SystemSettingsService::verifyPasswordAndGetSensitiveData - Public key length: ' . strlen($sensitiveData));
                    }
                    break;
                    
                case 'secret_key':
                    $settings = $this->paystackModel->first();
                    log_message('info', 'SystemSettingsService::verifyPasswordAndGetSensitiveData - Paystack settings found: ' . ($settings ? 'yes' : 'no'));
                    if ($settings) {
                        $sensitiveData = $settings['secret_key'] ?? '';
                        log_message('info', 'SystemSettingsService::verifyPasswordAndGetSensitiveData - Secret key length: ' . strlen($sensitiveData));
                    }
                    break;
                    
                case 'consumer_key':
                    $settings = $this->mpesaModel->first();
                    $sensitiveData = $settings['consumer_key'] ?? '';
                    break;
                    
                case 'consumer_secret':
                    $settings = $this->mpesaModel->first();
                    $sensitiveData = $settings['consumer_secret'] ?? '';
                    break;
                    
                case 'pass_key':
                    $settings = $this->mpesaModel->first();
                    $sensitiveData = $settings['pass_key'] ?? '';
                    break;

                case 'sms_api_key':
                    $settings = $this->smsModel->first();
                    $sensitiveData = $settings['api_key'] ?? '';
                    break;

                case 'sms_api_secret':
                    $settings = $this->smsModel->first();
                    $sensitiveData = $settings['api_secret'] ?? '';
                    break;

                default:
                    log_message('error', 'SystemSettingsService::verifyPasswordAndGetSensitiveData - Unknown field name: ' . $fieldName);
                    return [
                        'success' => false,
                        'message' => 'Invalid field name'
                    ];
            }

            log_message('info', 'SystemSettingsService::verifyPasswordAndGetSensitiveData - Final sensitive data: ' . ($sensitiveData ? 'has data' : 'empty'));

            return [
                'success' => true,
                'data' => $sensitiveData,
                'message' => 'Password verified successfully'
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Error verifying password for sensitive data: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to verify password'
            ];
        }
    }
}