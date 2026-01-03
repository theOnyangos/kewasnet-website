<?php

namespace App\Services;

use App\Models\QuizModel;
use App\Models\QuizAttemptModel;
use App\Models\QuizAnswerModel;
use App\Models\QuizQuestionModel;
use App\Models\QuizQuestionOptionModel;

class QuizService
{
    protected $quizModel;
    protected $attemptModel;
    protected $answerModel;
    protected $questionModel;
    protected $optionModel;

    public function __construct()
    {
        $this->quizModel = new QuizModel();
        $this->attemptModel = new QuizAttemptModel();
        $this->answerModel = new QuizAnswerModel();
        $this->questionModel = new QuizQuestionModel();
        $this->optionModel = new QuizQuestionOptionModel();
    }

    /**
     * Submit quiz
     */
    public function submitQuiz($userId, $quizId, $answers)
    {
        $quiz = $this->quizModel->getQuizWithQuestions($quizId);
        if (!$quiz) {
            return [
                'status' => 'error',
                'message' => 'Quiz not found'
            ];
        }

        // Create attempt
        $attemptData = [
            'user_id' => $userId,
            'quiz_id' => $quizId,
            'attempted_at' => date('Y-m-d H:i:s'),
        ];

        $attemptId = $this->attemptModel->insert($attemptData);
        if (!$attemptId) {
            return [
                'status' => 'error',
                'message' => 'Failed to create quiz attempt'
            ];
        }

        $totalPoints = 0;
        $earnedPoints = 0;

        // Process each answer
        foreach ($quiz['questions'] as $question) {
            $totalPoints += $question['points'];
            $questionId = $question['id'];
            $userAnswer = $answers[$questionId] ?? null;

            if ($userAnswer === null) {
                // No answer provided
                $this->answerModel->insert([
                    'attempt_id' => $attemptId,
                    'question_id' => $questionId,
                    'is_correct' => 0,
                    'points_earned' => 0,
                ]);
                continue;
            }

            $isCorrect = $this->checkAnswer($question, $userAnswer);
            $pointsEarned = $isCorrect ? $question['points'] : 0;
            $earnedPoints += $pointsEarned;

            // Save answer
            $answerData = [
                'attempt_id' => $attemptId,
                'question_id' => $questionId,
                'is_correct' => $isCorrect ? 1 : 0,
                'points_earned' => $pointsEarned,
            ];

            if ($question['question_type'] === 'multiple_choice' || $question['question_type'] === 'true_false') {
                $answerData['option_id'] = $userAnswer;
            } else {
                $answerData['answer_text'] = $userAnswer;
            }

            $this->answerModel->insert($answerData);
        }

        // Calculate percentage
        $percentage = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
        
        // Determine pass/fail (default 70% passing grade)
        $passingGrade = 70;
        $passed = $percentage >= $passingGrade;

        // Update attempt
        $this->attemptModel->update($attemptId, [
            'score' => $earnedPoints,
            'total_points' => $totalPoints,
            'percentage' => $percentage,
            'passed' => $passed ? 1 : 0,
            'completed_at' => date('Y-m-d H:i:s'),
        ]);

        return [
            'status' => 'success',
            'attempt_id' => $attemptId,
            'score' => $earnedPoints,
            'total_points' => $totalPoints,
            'percentage' => $percentage,
            'passed' => $passed,
            'message' => $passed ? 'Congratulations! You passed the quiz.' : 'You did not pass. Please review and try again.'
        ];
    }

    /**
     * Check if answer is correct
     */
    protected function checkAnswer($question, $userAnswer)
    {
        if ($question['question_type'] === 'multiple_choice' || $question['question_type'] === 'true_false') {
            // Check if selected option is correct
            foreach ($question['options'] as $option) {
                if ($option['id'] == $userAnswer && $option['is_correct'] == 1) {
                    return true;
                }
            }
            return false;
        } else {
            // Short answer - would need manual grading or keyword matching
            // For now, return false (requires manual review)
            return false;
        }
    }

    /**
     * Calculate score for attempt
     */
    public function calculateScore($attemptId)
    {
        $attempt = $this->attemptModel->find($attemptId);
        if (!$attempt) {
            return null;
        }

        $answers = $this->answerModel->where('attempt_id', $attemptId)->findAll();
        $totalPoints = array_sum(array_column($answers, 'points_earned'));

        return [
            'score' => $totalPoints,
            'total_points' => $attempt['total_points'],
            'percentage' => $attempt['percentage'],
            'passed' => $attempt['passed']
        ];
    }

    /**
     * Check passing grade
     */
    public function checkPassingGrade($quizId, $score, $passingGrade = 70)
    {
        $quiz = $this->quizModel->getQuizWithQuestions($quizId);
        if (!$quiz) {
            return false;
        }

        $totalPoints = $quiz['total_points'];
        $percentage = ($score / $totalPoints) * 100;

        return $percentage >= $passingGrade;
    }
}

