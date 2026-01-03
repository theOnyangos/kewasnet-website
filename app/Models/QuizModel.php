<?php

namespace App\Models;

use CodeIgniter\Model;

class QuizModel extends Model
{
    protected $table            = 'quizzes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'course_section_id',
        'title',
        'description',
        'status',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Get quiz with questions
     */
    public function getQuizWithQuestions($quizId)
    {
        $quiz = $this->find($quizId);
        
        if (!$quiz) {
            return null;
        }

        $questionModel = new QuizQuestionModel();
        $questions = $questionModel->where('quiz_id', $quizId)
            ->where('deleted_at', null)
            ->orderBy('order_index', 'ASC')
            ->findAll();

        foreach ($questions as &$question) {
            $optionModel = new QuizQuestionOptionModel();
            $question['options'] = $optionModel->where('question_id', $question['id'])
                ->orderBy('order_index', 'ASC')
                ->findAll();
        }

        $quiz['questions'] = $questions;
        $quiz['total_points'] = array_sum(array_column($questions, 'points'));

        return $quiz;
    }

    /**
     * Get quiz for section
     */
    public function getSectionQuiz($sectionId)
    {
        return $this->where('course_section_id', $sectionId)
            ->where('status', 'active')
            ->where('deleted_at', null)
            ->first();
    }
}

