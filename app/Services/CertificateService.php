<?php

namespace App\Services;

use App\Models\CertificateModel;
use App\Models\CourseModel;
use App\Models\UserModel;
use App\Services\CourseService;

class CertificateService
{
    protected $certificateModel;
    protected $courseModel;
    protected $userModel;
    protected $courseService;

    public function __construct()
    {
        $this->certificateModel = new CertificateModel();
        $this->courseModel = new CourseModel();
        $this->userModel = new UserModel();
        $this->courseService = new CourseService();
    }

    /**
     * Generate certificate
     */
    public function generateCertificate($userId, $courseId)
    {
        // Check if already has certificate
        if ($this->certificateModel->hasCertificate($userId, $courseId)) {
            return [
                'status' => 'error',
                'message' => 'Certificate already exists for this course'
            ];
        }

        // Check if course is completed
        if (!$this->courseService->isCourseCompleted($userId, $courseId)) {
            return [
                'status' => 'error',
                'message' => 'Course must be completed before certificate can be issued'
            ];
        }

        $course = $this->courseModel->find($courseId);
        $user = $this->userModel->find($userId);

        if (!$course || !$user) {
            return [
                'status' => 'error',
                'message' => 'Course or user not found'
            ];
        }

        // Generate certificate number and verification code
        $certificateNumber = $this->generateCertificateNumber();
        $verificationCode = $this->generateVerificationCode();

        // Generate PDF certificate
        $certificateUrl = $this->createCertificatePDF($user, $course, $certificateNumber, $verificationCode);

        if (!$certificateUrl) {
            return [
                'status' => 'error',
                'message' => 'Failed to generate certificate PDF'
            ];
        }

        // Save certificate record
        $certificateData = [
            'user_id' => $userId,
            'course_id' => $courseId,
            'certificate_url' => $certificateUrl,
            'certificate_number' => $certificateNumber,
            'verification_code' => $verificationCode,
            'issued_at' => date('Y-m-d H:i:s'),
        ];

        $certificateId = $this->certificateModel->insert($certificateData);

        if ($certificateId) {
            return [
                'status' => 'success',
                'message' => 'Certificate generated successfully',
                'certificate_id' => $certificateId,
                'certificate_url' => $certificateUrl
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Failed to save certificate'
        ];
    }

    /**
     * Verify certificate
     */
    public function verifyCertificate($certificateNumber)
    {
        $certificate = $this->certificateModel->where('certificate_number', $certificateNumber)
            ->where('deleted_at', null)
            ->first();

        if (!$certificate) {
            return [
                'status' => 'error',
                'message' => 'Certificate not found'
            ];
        }

        $course = $this->courseModel->find($certificate['course_id']);
        $user = $this->userModel->find($certificate['user_id']);

        return [
            'status' => 'success',
            'certificate' => $certificate,
            'course' => $course,
            'user' => [
                'name' => ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''),
                'email' => $user['email'] ?? ''
            ],
            'issued_at' => $certificate['issued_at']
        ];
    }

    /**
     * Download certificate
     */
    public function downloadCertificate($certificateId)
    {
        $certificate = $this->certificateModel->find($certificateId);
        
        if (!$certificate) {
            return null;
        }

        // Convert URL to file path if needed
        $filePath = $certificate['certificate_url'];
        if (strpos($filePath, base_url()) === 0) {
            // Remove base_url and convert to file path
            $relativePath = str_replace(base_url(), '', $filePath);
            $filePath = ROOTPATH . 'public' . $relativePath;
        } elseif (strpos($filePath, 'writable/') === 0) {
            $filePath = WRITEPATH . str_replace('writable/', '', $filePath);
        }

        if (!file_exists($filePath)) {
            return null;
        }

        return $filePath;
    }

    /**
     * Generate certificate PDF
     */
    protected function createCertificatePDF($user, $course, $certificateNumber, $verificationCode)
    {
        $generator = new \App\Libraries\CertificateGenerator();
        return $generator->generatePDF($user, $course, $certificateNumber, $verificationCode);
    }

    /**
     * Generate certificate number
     */
    protected function generateCertificateNumber()
    {
        return 'CERT-' . date('Y') . '-' . strtoupper(substr(uniqid(), -10));
    }

    /**
     * Generate verification code
     */
    protected function generateVerificationCode()
    {
        return strtoupper(substr(md5(uniqid(rand(), true)), 0, 16));
    }
}

