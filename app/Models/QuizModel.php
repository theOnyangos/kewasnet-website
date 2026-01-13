<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class QuizModel extends Model
{
    protected $table            = 'quizzes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false; // Using UUIDs
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'course_section_id',
        'title',
        'description',
        'status',
        'passing_score',
        'max_attempts',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    
    protected $beforeInsert = ['generateUUID'];
    
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['id']) || empty($data['data']['id'])) {
            try {
                $data['data']['id'] = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                log_message('error', 'UUID generation failed: ' . $e->getMessage());
                // Fallback to database UUID function
                unset($data['data']['id']);
            }
        }
        
        // Set course_section_id to empty string if not provided (will be updated when attached to section)
        if (!isset($data['data']['course_section_id']) || $data['data']['course_section_id'] === null) {
            $data['data']['course_section_id'] = '';
        }
        
        return $data;
    }

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

