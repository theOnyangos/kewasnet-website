<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseLectureModel extends Model
{
    protected $table            = 'course_lectures';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'section_id',
        'title',
        'description',
        'video_url',
        'vimeo_video_id',
        'duration',
        'is_preview',
        'is_free_preview',
        'star_rating',
        // 'order_index', // Column doesn't exist in migration
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Get lectures for a section ordered by created_at
     */
    public function getSectionLectures($sectionId)
    {
        return $this->where('section_id', $sectionId)
            ->where('deleted_at', null)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    /**
     * Get lecture with attachments and links
     */
    public function getLectureWithResources($lectureId)
    {
        $lecture = $this->find($lectureId);
        
        if (!$lecture) {
            return null;
        }

        // Get attachments
        $attachmentModel = new LectureAttachmentModel();
        $lecture['attachments'] = $attachmentModel->where('lecture_id', $lectureId)
            ->where('deleted_at', null)
            ->findAll();

        // Get links
        $linkModel = new LectureLinkModel();
        $lecture['links'] = $linkModel->where('lecture_id', $lectureId)
            ->where('deleted_at', null)
            ->orderBy('order_index', 'ASC')
            ->findAll();

        // Get Vimeo video info
        if (!empty($lecture['vimeo_video_id'])) {
            $vimeoModel = new VimeoVideoModel();
            $lecture['vimeo_video'] = $vimeoModel->where('lecture_id', $lectureId)
                ->first();
        }

        return $lecture;
    }

    /**
     * DataTable Methods for Admin Panel
     */

    /**
     * Get lectures for DataTable with joins
     */
    public function getLecturesTable($start, $length, $search = null, $orderBy = 'id', $orderDir = 'DESC')
    {
        $builder = $this->builder()
            ->select('course_lectures.*,
                     course_sections.title as section_title,
                     courses.title as course_title')
            ->join('course_sections', 'course_sections.id = course_lectures.section_id', 'left', false)
            ->join('courses', 'courses.id = course_sections.course_id', 'left', false);

        // Apply search filter
        if ($search) {
            $builder->groupStart()
                ->like('course_lectures.title', $search)
                ->orLike('course_sections.title', $search)
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
     * Count total lectures for DataTable
     */
    public function countLectures(): int
    {
        return $this->countAllResults(false);
    }
}

