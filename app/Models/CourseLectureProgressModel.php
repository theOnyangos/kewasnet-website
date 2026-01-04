<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseLectureProgressModel extends Model
{
    protected $table            = 'course_lecture_progress';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false; // Using UUIDs
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'course_id',
        'lecture_id',
        'student_id',
        'status',
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
     * Get progress for a student in a course
     */
    public function getStudentProgress($studentId, $courseId)
    {
        return $this->where('student_id', $studentId)
                    ->where('course_id', $courseId)
                    ->findAll();
    }

    /**
     * Check if lecture is completed
     */
    public function isLectureCompleted($studentId, $lectureId)
    {
        $progress = $this->where('student_id', $studentId)
                         ->where('lecture_id', $lectureId)
                         ->where('status', 'completed')
                         ->first();
        
        return !empty($progress);
    }

    /**
     * Mark lecture as completed
     */
    public function markAsCompleted($studentId, $courseId, $lectureId)
    {
        $existing = $this->where('student_id', $studentId)
                         ->where('lecture_id', $lectureId)
                         ->first();
        
        if ($existing) {
            return $this->update($existing['id'], ['status' => 'completed']);
        }
        
        return $this->insert([
            'student_id' => $studentId,
            'course_id' => $courseId,
            'lecture_id' => $lectureId,
            'status' => 'completed'
        ]);
    }

    /**
     * Calculate course completion percentage
     */
    public function getCourseCompletionPercentage($studentId, $courseId)
    {
        $lectureModel = new \App\Models\CourseLectureModel();
        
        // Get total lectures in course
        $totalLectures = $lectureModel
            ->join('course_sections', 'course_sections.id = course_lectures.section_id')
            ->where('course_sections.course_id', $courseId)
            ->where('course_lectures.deleted_at', null)
            ->countAllResults();
        
        if ($totalLectures == 0) {
            return 0;
        }
        
        // Get completed lectures
        $completedLectures = $this->where('student_id', $studentId)
                                  ->where('course_id', $courseId)
                                  ->where('status', 'completed')
                                  ->countAllResults();
        
        return round(($completedLectures / $totalLectures) * 100, 2);
    }
}
