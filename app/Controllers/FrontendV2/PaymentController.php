<?php

namespace App\Controllers\FrontendV2;

use App\Controllers\BaseController;
use App\Services\PaymentService;
use App\Libraries\ClientAuth;
use CodeIgniter\HTTP\ResponseInterface;

class PaymentController extends BaseController
{
    protected $paymentService;

    public function __construct()
    {
        $this->paymentService = new PaymentService();
    }

    /**
     * Initiate payment
     */
    public function initiatePayment()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid request'
                ]);
        }

        $userId = ClientAuth::getId();
        $courseId = $this->request->getPost('course_id');
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);
        $email = $this->request->getPost('email') ?? ($user['email'] ?? '');

        if (!$userId || !$courseId) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Missing required parameters'
                ]);
        }

        $result = $this->paymentService->initiateCoursePayment($userId, $courseId, $email);

        return $this->response
            ->setContentType('application/json')
            ->setJSON($result);
    }

    /**
     * Payment callback from Paystack
     */
    public function paymentCallback()
    {
        $reference = $this->request->getGet('reference');

        if (!$reference) {
            return redirect()->to('ksp/learning-hub')
                ->with('error', 'Invalid payment reference');
        }

        $result = $this->paymentService->verifyPayment($reference);

        if ($result['status'] === 'success') {
            $courseId = $result['course_id'] ?? null;
            if ($courseId) {
                return redirect()->to('ksp/learning-hub/learn/' . $courseId)
                    ->with('success', 'Payment successful! You can now access the course.');
            }
            return redirect()->to('ksp/learning-hub/my-courses')
                ->with('success', 'Payment successful!');
        }

        return redirect()->to('ksp/learning-hub')
            ->with('error', $result['message'] ?? 'Payment verification failed');
    }

    /**
     * Verify payment status
     */
    public function verifyPayment()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid request'
                ]);
        }

        $reference = $this->request->getPost('reference');

        if (!$reference) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Payment reference required'
                ]);
        }

        $result = $this->paymentService->verifyPayment($reference);

        return $this->response
            ->setContentType('application/json')
            ->setJSON($result);
    }
}

