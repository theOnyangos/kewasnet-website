<?php

namespace App\Controllers\BackendV2;

use App\Controllers\BaseController;
use App\Services\EmailSettingsService;
use CodeIgniter\HTTP\ResponseInterface;

class EmailSettingController extends BaseController
{
    protected $emailSettingsService;

    public function __construct()
    {
        $this->emailSettingsService = new EmailSettingsService();
    }

    /**
     * Display email settings page
     */
    public function index()
    {
        $title = "Email Settings - KEWASNET";
        $data = [
            'title' => $title,
            'activeTab' => 'email'
        ];
        return view('backendV2/pages/settings/email_settings', $data);
    }

    /**
     * Get email settings
     */
    public function getEmailSettings(): ResponseInterface
    {
        $result = $this->emailSettingsService->getEmailSettings();
        return $this->response->setJSON($result);
    }

    /**
     * Save email settings
     */
    public function saveEmailSettings(): ResponseInterface
    {
        $data = $this->request->getPost();
        $result = $this->emailSettingsService->saveEmailSettings($data);
        return $this->response->setJSON($result);
    }

    /**
     * Send test email
     */
    public function sendTestEmail(): ResponseInterface
    {
        $data = $this->request->getPost();
        $testEmail = $data['test_email'] ?? '';
        
        if (empty($testEmail)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Test email address is required'
            ]);
        }

        if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid email address'
            ]);
        }

        // Remove test_email from data before passing to service
        unset($data['test_email']);
        
        $result = $this->emailSettingsService->sendTestEmail($data, $testEmail);
        return $this->response->setJSON($result);
    }

    /**
     * Verify password for sensitive data access
     */
    public function verifyPasswordForSensitiveData(): ResponseInterface
    {
        // Log that this method was reached
        log_message('info', 'EmailSettingController::verifyPasswordForSensitiveData - Method reached');
        
        $password = $this->request->getPost('password');
        $fieldName = $this->request->getPost('field_name');

        // Debug: Log received data
        log_message('info', 'EmailSettingController::verifyPasswordForSensitiveData - Password: ' . ($password ? 'provided' : 'empty') . ', Field: ' . ($fieldName ?? 'empty'));

        if (empty($password) || empty($fieldName)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Password and field name are required'
            ]);
        }

        $result = $this->emailSettingsService->verifyPasswordAndGetSensitiveData($password, $fieldName);
        return $this->response->setJSON($result);
    }
}
