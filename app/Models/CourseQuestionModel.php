<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseQuestionModel extends Model
{
    protected $table            = 'course_questions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'course_id',
        'section_id',
        'title',
        'descriptions',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'delete_at';

    /**
     * Get questions for course with replies
     */
    public function getCourseQuestions($courseId)
    {
        $questions = $this->where('course_id', $courseId)
            ->where('delete_at', null)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $replyModel = new CourseQuestionReplyModel();
        foreach ($questions as &$question) {
            $replies = $replyModel->where('question_id', $question['id'])
                ->where('delete_at', null)
                ->orderBy('created_at', 'ASC')
                ->findAll();
            
            // Map user_id from student_id or instructor_id
            foreach ($replies as &$reply) {
                $reply['user_id'] = $reply['instructor_id'] ?? $reply['student_id'];
                $reply['reply_text'] = $reply['message'] ?? '';
                $reply['is_instructor'] = !empty($reply['instructor_id']);
            }
            
            $question['replies'] = $replies;
        }

        return $questions;
    }
}

