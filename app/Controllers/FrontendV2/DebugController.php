<?php

namespace App\Controllers\FrontendV2;

use App\Controllers\BaseController;
use App\Models\PaystackSetting;
use App\Models\CourseModel;
use App\Libraries\ClientAuth;

class DebugController extends BaseController
{
    public function paymentDebug()
    {
        // Only allow in development mode
        if (ENVIRONMENT !== 'development') {
            return $this->response->setJSON(['error' => 'Only available in development']);
        }

        $debug = [];

        // Check user authentication
        $userId = ClientAuth::getId();
        $debug['user'] = [
            'logged_in' => !empty($userId),
            'user_id' => $userId
        ];

        // Check Paystack settings
        $paystackModel = new PaystackSetting();
        $allSettings = $paystackModel->findAll();
        $activeSettings = $paystackModel->where('status', 1)->first();
        
        $debug['paystack'] = [
            'total_records' => count($allSettings),
            'active_settings' => !empty($activeSettings),
            'active_record' => $activeSettings ? [
                'id' => $activeSettings['id'],
                'public_key' => substr($activeSettings['public_key'], 0, 20) . '...',
                'status' => $activeSettings['status'],
                'enabled' => $activeSettings['enabled'] ?? 'N/A'
            ] : null,
            'all_records' => array_map(function($record) {
                return [
                    'id' => $record['id'],
                    'public_key' => substr($record['public_key'], 0, 20) . '...',
                    'status' => $record['status'],
                    'enabled' => $record['enabled'] ?? 'N/A'
                ];
            }, $allSettings)
        ];

        // Check course
        $courseId = $this->request->getGet('course_id') ?? 'b1a1fecf-6c60-40ef-9ba4-2ad42a714de6';
        $courseModel = new CourseModel();
        $course = $courseModel->find($courseId);
        
        $debug['course'] = [
            'course_id' => $courseId,
            'exists' => !empty($course),
            'course_data' => $course ? [
                'id' => $course['id'],
                'title' => $course['title'],
                'price' => $course['price'],
                'is_free' => $course['is_free'] ?? 0
            ] : null
        ];

        // Check payment route
        $debug['routes'] = [
            'payment_initiate' => base_url('ksp/payment/initiate'),
            'payment_verify' => base_url('ksp/payment/verify')
        ];

        // Check environment
        $debug['environment'] = [
            'ci_environment' => ENVIRONMENT,
            'base_url' => base_url(),
            'php_version' => phpversion()
        ];

        return $this->response->setJSON($debug);
    }
}
