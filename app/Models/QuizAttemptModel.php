<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class QuizAttemptModel extends Model
{
    protected $table            = 'quiz_attempts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'quiz_id',
        'score',
        'total_points',
        'percentage',
        'passed',
        'attempted_at',
        'completed_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $beforeInsert = ['generateUUID'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid4()->toString();
        }
        return $data;
    }

    /**
     * Get user's best attempt for a quiz
     */
    public function getBestAttempt($userId, $quizId)
    {
        return $this->where('user_id', $userId)
            ->where('quiz_id', $quizId)
            ->orderBy('percentage', 'DESC')
            ->orderBy('completed_at', 'DESC')
            ->first();
    }

    /**
     * Get attempt with answers
     */
    public function getAttemptWithAnswers($attemptId)
    {
        $attempt = $this->find($attemptId);
        
        if (!$attempt) {
            return null;
        }

        $answerModel = new QuizAnswerModel();
        $attempt['answers'] = $answerModel->where('attempt_id', $attemptId)
            ->findAll();

        return $attempt;
    }

    /**
     * Check if user has passed quiz
     */
    public function hasPassed($userId, $quizId, $passingGrade = 70)
    {
        $bestAttempt = $this->getBestAttempt($userId, $quizId);
        
        return $bestAttempt && $bestAttempt['passed'] == 1 && $bestAttempt['percentage'] >= $passingGrade;
    }

    /**
     * Get query builder for user's quiz attempts in a course with joins
     * Returns builder with quizzes and course_sections joined
     */
    public function getUserCourseAttemptsBuilder($courseId, $userId)
    {
        return $this->builder()
            ->select('quiz_attempts.*, 
                     quizzes.title as quiz_title,
                     course_sections.title as section_title,
                     course_sections.id as section_id')
            ->join('quizzes', 'quizzes.id = quiz_attempts.quiz_id', 'left')
            ->join('course_sections', 'course_sections.id = quizzes.course_section_id', 'inner')
            ->where('quiz_attempts.user_id', $userId)
            ->where('course_sections.course_id', $courseId);
    }
}

