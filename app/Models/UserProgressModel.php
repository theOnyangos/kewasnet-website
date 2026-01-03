<?php

namespace App\Models;

use CodeIgniter\Model;

class UserProgressModel extends Model
{
    protected $table            = 'user_progress';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
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

    /**
     * Get user progress for course
     */
    public function getCourseProgress($userId, $courseId)
    {
        return $this->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->findAll();
    }

    /**
     * Update lecture progress
     */
    public function updateLectureProgress($userId, $courseId, $sectionId, $lectureId, $progressPercentage, $watchTime = 0)
    {
        $existing = $this->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('section_id', $sectionId)
            ->where('lecture_id', $lectureId)
            ->first();

        $data = [
            'user_id' => $userId,
            'course_id' => $courseId,
            'section_id' => $sectionId,
            'lecture_id' => $lectureId,
            'progress_percentage' => $progressPercentage,
            'watch_time' => $watchTime,
            'last_accessed_at' => date('Y-m-d H:i:s'),
        ];

        if ($progressPercentage >= 100) {
            $data['completed_at'] = date('Y-m-d H:i:s');
        }

        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            return $this->insert($data);
        }
    }
}

