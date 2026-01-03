<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table            = 'courses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false; // UUID is not auto-increment
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', // Add id to allowed fields for UUID insertion
        'user_id',
        'category_id',
        'sub_category_id',
        'title',
        'summary',
        'certificate',
        'level',
        'price',
        'discount_price',
        'duration',
        'resources',
        'description',
        'image_url',
        'status',
        'slug',
        'star_rating',
        'goals',
        'instructor_id',
        'preview_video_url',
        'is_paid',
        'vimeo_embed_settings',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'title' => 'required|max_length[255]',
        'price' => 'permit_empty|decimal',
        'level' => 'in_list[beginner,intermediate,advanced]',
    ];

    /**
     * Get course with enrollment status for user
     */
    public function getCourseWithEnrollment($courseId, $userId = null)
    {
        $course = $this->find($courseId);
        
        if (!$course || !$userId) {
            return $course;
        }

        $enrollmentModel = new CourseEnrollmentModel();
        $enrollment = $enrollmentModel->where('student_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        $course['enrollment'] = $enrollment;
        $course['is_enrolled'] = !empty($enrollment);
        $course['is_paid'] = ($course['price'] > 0 || ($course['is_paid'] ?? 0) == 1);

        return $course;
    }

    /**
     * Get courses by category
     */
    public function getCoursesByCategory($categoryId, $limit = null)
    {
        $builder = $this->where('category_id', $categoryId)
            ->where('status', 1)
            ->where('deleted_at', null);

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Get free courses
     */
    public function getFreeCourses($limit = null)
    {
        $builder = $this->where('price', 0)
            ->where('is_paid', 0)
            ->where('status', 1)
            ->where('deleted_at', null);

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Get paid courses
     */
    public function getPaidCourses($limit = null)
    {
        $builder = $this->groupStart()
            ->where('price >', 0)
            ->orWhere('is_paid', 1)
            ->groupEnd()
            ->where('status', 1)
            ->where('deleted_at', null);

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * DataTable Methods for Admin Panel
     */

    /**
     * Get courses for DataTable with joins
     */
    public function getCoursesTable($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC')
    {
        $builder = $this->builder()
            ->select('courses.*,
                     COALESCE(course_categories.name, blog_categories.name) as category_name,
                     (SELECT COUNT(*) FROM user_progress WHERE user_progress.course_id = courses.id) as enrollments_count')
            ->join('course_categories', 'course_categories.id = courses.category_id', 'left', false)
            ->join('blog_categories', 'blog_categories.id = courses.category_id', 'left', false);

        // Apply search filter
        if ($search) {
            $builder->groupStart()
                ->like('courses.title', $search)
                ->orLike('courses.slug', $search)
                ->orLike('course_categories.name', $search)
                ->orLike('blog_categories.name', $search)
                ->orLike('courses.level', $search)
                ->groupEnd();
        }

        // Apply ordering
        $builder->orderBy($orderBy, $orderDir);

        // Apply pagination
        $builder->limit($length, $start);

        return $builder->get()->getResult();
    }

    /**
     * Count total courses for DataTable
     */
    public function countCourses(): int
    {
        return $this->countAllResults(false);
    }
}

