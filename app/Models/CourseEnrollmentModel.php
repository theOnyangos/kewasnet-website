<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class CourseEnrollmentModel extends Model
{
    // TODO: course_purchases table doesn't exist in migrations - using user_progress instead
    protected $table            = 'user_progress';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false; // Using UUID
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false; // user_progress table doesn't have deleted_at column
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', // Add id to allowed fields for UUID insertion
        'user_id',
        'course_id',
        'section_id',
        'lecture_id',
        'progress_percentage',
        'watch_time',
        'last_accessed_at',
        'completed_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Check if user is enrolled in course
     */
    public function isEnrolled($userId, $courseId)
    {
        $enrollment = $this->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        return !empty($enrollment);
    }

    /**
     * Get user enrollments
     */
    public function getUserEnrollments($userId)
    {
        return $this->where('user_id', $userId)
            ->findAll();
    }

    /**
     * Enroll user in course
     */
    public function enrollUser($userId, $courseId, $purchasedAt = null)
    {
        // Check if already enrolled
        if ($this->isEnrolled($userId, $courseId)) {
            return false;
        }

        $data = [
            'id' => Uuid::uuid4()->toString(),
            'user_id' => $userId,
            'course_id' => $courseId,
            'progress_percentage' => 0.00,
            'watch_time' => 0,
            'last_accessed_at' => date('Y-m-d H:i:s'),
        ];

        return $this->insert($data);
    }

    /**
     * DataTable Methods for Admin Panel
     */

    /**
     * Build a fresh enrollments query builder (no shared state).
     *
     * IMPORTANT: Model::builder() caches the builder instance; DataTableService calls
     * getEnrollmentsTable() then countEnrollments() on the same model instance, which
     * can lead to duplicate JOINs and "Not unique table/alias" SQL errors.
     */
    protected function enrollmentsBuilder(?string $search = null)
    {
        $builder = $this->db->table($this->table)
            ->select('user_progress.id,
                     user_progress.user_id,
                     user_progress.course_id,
                     user_progress.progress_percentage,
                     user_progress.last_accessed_at,
                     user_progress.completed_at,
                     user_progress.created_at,
                     courses.title as course_title,
                     CONCAT(system_users.first_name, " ", system_users.last_name) as student_name,
                     system_users.email as student_email')
            ->join('courses', 'courses.id = user_progress.course_id', 'left', false)
            ->join('system_users', 'system_users.id = user_progress.user_id', 'left', false);

        if ($search) {
            $builder->groupStart()
                ->like('courses.title', $search)
                ->orLike('system_users.first_name', $search)
                ->orLike('system_users.last_name', $search)
                ->orLike('system_users.email', $search)
                ->groupEnd();
        }

        return $builder;
    }

    /**
     * Get enrollments for DataTable with joins
     */
    public function getEnrollmentsTable($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC')
    {
        $builder = $this->enrollmentsBuilder($search);

        // Apply ordering
        if ($orderBy === 'student_name') {
            $builder->orderBy('system_users.first_name', $orderDir);
        } elseif ($orderBy === 'course_title') {
            $builder->orderBy('courses.title', $orderDir);
        } else {
            $builder->orderBy('user_progress.' . $orderBy, $orderDir);
        }

        // Apply pagination
        $builder->limit($length, $start);

        return $builder->get()->getResult();
    }

    /**
     * Count total enrollments for DataTable
     */
    public function countEnrollments($search = null): int
    {
        $builder = $this->enrollmentsBuilder($search ? (string) $search : null);
        return (int) $builder->countAllResults();
    }
}
