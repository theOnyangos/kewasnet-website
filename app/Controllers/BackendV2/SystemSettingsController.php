<?php

namespace App\Controllers\BackendV2;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\SystemSettingsService;

class SystemSettingsController extends BaseController
{
    protected $systemSettingsService;

    public function __construct()
    {
        $this->systemSettingsService = new SystemSettingsService();
    }

    public function index()
    {
        //
    }

    public function payments()
    {
        $title = "Payment Settings - KEWASNET";
        $data = [
            'title' => $title,
            'activeTab' => 'payments'
        ];
        return view('backendV2/pages/settings/payments_panel', $data);
    }

    // Paystack Settings
    public function getPaystackSettings(): ResponseInterface
    {
        $result = $this->systemSettingsService->getPaystackSettings();
        return $this->response->setJSON($result);
    }

    public function savePaystackSettings(): ResponseInterface
    {
        $data = $this->request->getPost();
        $result = $this->systemSettingsService->savePaystackSettings($data);
        return $this->response->setJSON($result);
    }

    public function testPaystackConnection(): ResponseInterface
    {
        $data = $this->request->getPost();
        $result = $this->systemSettingsService->testPaystackConnection($data);
        return $this->response->setJSON($result);
    }

    // M-Pesa Settings
    public function getMpesaSettings(): ResponseInterface
    {
        $result = $this->systemSettingsService->getMpesaSettings();
        return $this->response->setJSON($result);
    }

    public function saveMpesaSettings(): ResponseInterface
    {
        $data = $this->request->getPost();
        $result = $this->systemSettingsService->saveMpesaSettings($data);
        return $this->response->setJSON($result);
    }

    public function testMpesaConnection(): ResponseInterface
    {
        $data = $this->request->getPost();
        $result = $this->systemSettingsService->testMpesaConnection($data);
        return $this->response->setJSON($result);
    }

    // SMS Settings
    public function sms()
    {
        $title = "SMS Settings - KEWASNET";
        $data = [
            'title' => $title,
            'activeTab' => 'sms'
        ];
        return view('backendV2/pages/settings/sms_panel', $data);
    }

    public function getSmsSettings(): ResponseInterface
    {
        $result = $this->systemSettingsService->getSmsSettings();
        return $this->response->setJSON($result);
    }

    public function saveSmsSettings(): ResponseInterface
    {
        $data = $this->request->getPost();
        $result = $this->systemSettingsService->saveSmsSettings($data);
        return $this->response->setJSON($result);
    }

    public function testSmsConnection(): ResponseInterface
    {
        $data = $this->request->getPost();
        $result = $this->systemSettingsService->testSmsConnection($data);
        return $this->response->setJSON($result);
    }

    // Password verification for sensitive data
    public function verifyPasswordForSensitiveData(): ResponseInterface
    {
        $password = $this->request->getPost('password');
        $fieldName = $this->request->getPost('field_name');

        $result = $this->systemSettingsService->verifyPasswordAndGetSensitiveData($password, $fieldName);
        return $this->response->setJSON($result);
    }
}
