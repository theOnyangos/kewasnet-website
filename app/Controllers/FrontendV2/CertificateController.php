<?php

namespace App\Controllers\FrontendV2;

use App\Controllers\BaseController;
use App\Services\CertificateService;
use CodeIgniter\HTTP\ResponseInterface;

class CertificateController extends BaseController
{
    protected $certificateService;

    public function __construct()
    {
        $this->certificateService = new CertificateService();
    }

    /**
     * Public certificate verification endpoint
     */
    public function verify($verificationCode = null)
    {
        if (!$verificationCode) {
            $verificationCode = $this->request->getGet('code');
        }

        if (!$verificationCode) {
            return view('frontendV2/ksp/pages/learning-hub/certificates/verify', [
                'title' => 'Verify Certificate - KEWASNET',
                'certificate' => null,
                'error' => 'Please provide a verification code'
            ]);
        }

        $result = $this->certificateService->verifyCertificate($verificationCode);

        if ($result['status'] === 'success') {
            return view('frontendV2/ksp/pages/learning-hub/certificates/verify', [
                'title' => 'Certificate Verification - KEWASNET',
                'certificate' => $result['certificate'],
                'course' => $result['course'],
                'user' => $result['user'],
                'issued_at' => $result['issued_at'],
                'error' => null
            ]);
        }

        return view('frontendV2/ksp/pages/learning-hub/certificates/verify', [
            'title' => 'Verify Certificate - KEWASNET',
            'certificate' => null,
            'error' => $result['message']
        ]);
    }
}

