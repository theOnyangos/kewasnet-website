<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseInstructorModel extends Model
{
    protected $table            = 'course_instructors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'course_id',
        'instructor_id',
        'role',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get instructors for course
     */
    public function getCourseInstructors($courseId)
    {
        return $this->where('course_id', $courseId)
            ->findAll();
    }

    /**
     * Get primary instructor
     */
    public function getPrimaryInstructor($courseId)
    {
        return $this->where('course_id', $courseId)
            ->where('role', 'primary')
            ->first();
    }
}

