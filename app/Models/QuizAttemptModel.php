<?php

namespace App\Models;

use CodeIgniter\Model;

class QuizAttemptModel extends Model
{
    protected $table            = 'quiz_attempts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
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
}

