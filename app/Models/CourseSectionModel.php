<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseSectionModel extends Model
{
    protected $table            = 'course_sections';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'course_id',
        'title',
        'description',
        'status',
        // 'order_index', // Column doesn't exist in migration
        'quiz_id',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Get sections for a course ordered by created_at
     */
    public function getCourseSections($courseId)
    {
        return $this->where('course_id', $courseId)
            ->where('deleted_at', null)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    /**
     * Get section with lectures
     */
    public function getSectionWithLectures($sectionId)
    {
        $section = $this->find($sectionId);

        if (!$section) {
            return null;
        }

        $lectureModel = new CourseLectureModel();
        $section['lectures'] = $lectureModel->getSectionLectures($sectionId);

        return $section;
    }

    /**
     * DataTable Methods for Admin Panel
     */

    /**
     * Get sections for DataTable with joins
     */
    public function getSectionsTable($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC')
    {
        $builder = $this->builder()
            ->select('course_sections.*,
                     courses.title as course_title,
                     (SELECT COUNT(*) FROM course_lectures WHERE course_lectures.section_id = course_sections.id) as lectures_count')
            ->join('courses', 'courses.id = course_sections.course_id', 'left', false);

        // Apply search filter
        if ($search) {
            $builder->groupStart()
                ->like('course_sections.title', $search)
                ->orLike('courses.title', $search)
                ->groupEnd();
        }

        // Apply ordering
        $builder->orderBy($orderBy, $orderDir);

        // Apply pagination
        $builder->limit($length, $start);

        return $builder->get()->getResult();
    }

    /**
     * Count total sections for DataTable
     */
    public function countSections(): int
    {
        return $this->countAllResults(false);
    }
}

