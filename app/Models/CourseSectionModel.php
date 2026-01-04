<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseSectionModel extends Model
{
    protected $table            = 'course_sections';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false; // Using UUIDs
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
    
    protected $beforeInsert = ['generateUUID'];
    
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
        }
        return $data;
    }

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

