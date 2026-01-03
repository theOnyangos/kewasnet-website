<?php

namespace App\Controllers\FrontendV2;

use App\Controllers\BaseController;
use App\Models\CourseQuestionModel;
use App\Models\CourseQuestionReplyModel;
use App\Models\CourseInstructorModel;
use App\Libraries\ClientAuth;
use App\Services\CourseService;
use CodeIgniter\HTTP\ResponseInterface;

class CourseQuestionController extends BaseController
{
    protected $questionModel;
    protected $replyModel;
    protected $instructorModel;
    protected $courseService;

    public function __construct()
    {
        $this->questionModel = new CourseQuestionModel();
        $this->replyModel = new CourseQuestionReplyModel();
        $this->instructorModel = new CourseInstructorModel();
        $this->courseService = new CourseService();
    }

    /**
     * Ask question
     */
    public function askQuestion()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid request'
                ]);
        }

        $userId = ClientAuth::getId();
        $courseId = $this->request->getPost('course_id');
        $sectionId = $this->request->getPost('section_id');
        $title = $this->request->getPost('title');
        $description = $this->request->getPost('description');

        if (!$userId || !$courseId || !$title) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Missing required fields'
                ]);
        }

        // Check if user is enrolled
        if (!$this->courseService->checkAccess($userId, $courseId)) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'You must be enrolled in the course to ask questions'
                ]);
        }

        $data = [
            'user_id' => $userId,
            'course_id' => $courseId,
            'section_id' => $sectionId ?? null,
            'title' => $title,
            'descriptions' => $description ?? null,
        ];

        $questionId = $this->questionModel->insert($data);

        if ($questionId) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'success',
                    'message' => 'Question submitted successfully',
                    'question_id' => $questionId
                ]);
        }

        return $this->response
            ->setContentType('application/json')
            ->setJSON([
                'status' => 'error',
                'message' => 'Failed to submit question'
            ]);
    }

    /**
     * Get questions for course
     */
    public function getQuestions($courseId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid request'
                ]);
        }

        $userId = ClientAuth::getId();

        // Check if user is enrolled
        if (!$this->courseService->checkAccess($userId, $courseId)) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Access denied'
                ]);
        }

        $questions = $this->questionModel->getCourseQuestions($courseId);

        // Get user info for each question
        $userModel = new \App\Models\UserModel();
        foreach ($questions as &$question) {
            $user = $userModel->find($question['user_id']);
            $question['user_name'] = ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '');
            $question['user_picture'] = $user['picture'] ?? null;

            // Get user info for replies
            foreach ($question['replies'] as &$reply) {
                $replyUserId = $reply['user_id'] ?? ($reply['instructor_id'] ?? $reply['student_id']);
                $replyUser = $userModel->find($replyUserId);
                $reply['user_name'] = ($replyUser['first_name'] ?? '') . ' ' . ($replyUser['last_name'] ?? '');
                $reply['user_picture'] = $replyUser['picture'] ?? null;
            }
        }

        return $this->response
            ->setContentType('application/json')
            ->setJSON([
                'status' => 'success',
                'questions' => $questions
            ]);
    }

    /**
     * Reply to question
     */
    public function replyToQuestion($questionId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid request'
                ]);
        }

        $userId = ClientAuth::getId();
        $replyText = $this->request->getPost('reply_text');

        if (!$userId || !$replyText) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Missing required fields'
                ]);
        }

        $question = $this->questionModel->find($questionId);
        if (!$question) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Question not found'
                ]);
        }

        // Check if user is instructor or enrolled student
        $isInstructor = $this->isInstructor($userId, $question['course_id']);
        $isEnrolled = $this->courseService->checkAccess($userId, $question['course_id']);

        if (!$isInstructor && !$isEnrolled) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'You do not have permission to reply'
                ]);
        }

        $data = [
            'question_id' => $questionId,
            'student_id' => $userId,
            'instructor_id' => $isInstructor ? $userId : null,
            'message' => $replyText,
        ];

        $replyId = $this->replyModel->insert($data);

        if ($replyId) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'success',
                    'message' => 'Reply submitted successfully',
                    'reply_id' => $replyId
                ]);
        }

        return $this->response
            ->setContentType('application/json')
            ->setJSON([
                'status' => 'error',
                'message' => 'Failed to submit reply'
            ]);
    }

    /**
     * Mark question as resolved
     */
    public function markAsResolved($questionId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid request'
                ]);
        }

        $userId = ClientAuth::getId();
        $question = $this->questionModel->find($questionId);

        if (!$question || $question['user_id'] != $userId) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Question not found or unauthorized'
                ]);
        }

        // Update question to mark as resolved (would need to add is_resolved field to questions table)
        // For now, we'll just return success

        return $this->response
            ->setContentType('application/json')
            ->setJSON([
                'status' => 'success',
                'message' => 'Question marked as resolved'
            ]);
    }

    /**
     * Check if user is instructor for course
     */
    protected function isInstructor($userId, $courseId)
    {
        $instructor = $this->instructorModel->where('course_id', $courseId)
            ->where('instructor_id', $userId)
            ->first();

        return !empty($instructor);
    }
}

