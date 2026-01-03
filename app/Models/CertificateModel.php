<?php

namespace App\Models;

use CodeIgniter\Model;

class CertificateModel extends Model
{
    protected $table            = 'course_certificates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'course_id',
        'certificate_url',
        'certificate_number',
        'verification_code',
        'issued_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Get user certificates
     */
    public function getUserCertificates($userId)
    {
        return $this->where('user_id', $userId)
            ->where('deleted_at', null)
            ->orderBy('issued_at', 'DESC')
            ->findAll();
    }

    /**
     * Get certificate by verification code
     */
    public function getByVerificationCode($verificationCode)
    {
        return $this->where('verification_code', $verificationCode)
            ->where('deleted_at', null)
            ->first();
    }

    /**
     * Check if user has certificate for course
     */
    public function hasCertificate($userId, $courseId)
    {
        $certificate = $this->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('deleted_at', null)
            ->first();

        return !empty($certificate);
    }
}

