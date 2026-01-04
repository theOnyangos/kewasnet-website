<?php

namespace App\Controllers\FrontendV2;

use App\Controllers\BaseController;
use App\Services\CourseService;
use App\Services\QuizService;
use App\Services\CertificateService;
use App\Services\VimeoService;
use App\Models\CourseModel;
use App\Models\CourseEnrollmentModel;
use App\Models\CertificateModel;
use App\Libraries\ClientAuth;
use CodeIgniter\HTTP\ResponseInterface;

class LearningHubController extends BaseController
{
    protected $courseService;
    protected $quizService;
    protected $certificateService;
    protected $vimeoService;

    public function __construct()
    {
        $this->courseService = new CourseService();
        $this->quizService = new QuizService();
        $this->certificateService = new CertificateService();
        $this->vimeoService = new VimeoService();
    }

    /**
     * Course catalog/browse
     */
    public function index()
    {
        $userId = ClientAuth::getId() ?: null; // Allow null for unauthenticated users
        $courseModel = new CourseModel();
        $enrollmentModel = new CourseEnrollmentModel();
        
        // Get filter parameters
        $filter = $this->request->getGet('filter') ?? 'all'; // all, free, paid
        $category = $this->request->getGet('category') ?? null;
        $level = $this->request->getGet('level') ?? null;
        $search = $this->request->getGet('search') ?? null;
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = 12; // Courses per page

        $builder = $courseModel->where('status', 1);

        // Apply filters
        if ($filter === 'free') {
            $builder->where('price', 0)->where('is_paid', 0);
        } elseif ($filter === 'paid') {
            $builder->groupStart()
                ->where('price >', 0)
                ->orWhere('is_paid', 1)
                ->groupEnd();
        }

        if ($category) {
            $builder->where('category_id', $category);
        }

        if ($level) {
            $builder->where('level', $level);
        }

        if ($search) {
            $builder->groupStart()
                ->like('title', $search)
                ->orLike('description', $search)
                ->orLike('summary', $search)
                ->groupEnd();
        }

        // Get total count for pagination
        $totalCourses = $builder->countAllResults(false);
        
        // Apply pagination
        $courses = $builder->orderBy('created_at', 'DESC')
                          ->limit($perPage, ($page - 1) * $perPage)
                          ->findAll();
        
        // Add enrollment status and progress for each course
        foreach ($courses as &$course) {
            $course['is_enrolled'] = false;
            $course['progress'] = 0;
            
            if ($userId) {
                $course['is_enrolled'] = $enrollmentModel->isEnrolled($userId, $course['id']);
                if ($course['is_enrolled']) {
                    $course['progress'] = $this->courseService->calculateProgress($userId, $course['id']);
                }
            }
        }

        // Calculate pagination data
        $totalPages = ceil($totalCourses / $perPage);
        $pager = \Config\Services::pager();
        
        // Build pagination links with current filters
        $pagerLinks = [];
        $baseUrl = base_url('ksp/learning-hub');
        $queryParams = http_build_query(array_filter([
            'filter' => $filter !== 'all' ? $filter : null,
            'category' => $category,
            'level' => $level,
            'search' => $search,
        ]));
        $urlPrefix = $baseUrl . ($queryParams ? '?' . $queryParams . '&' : '?');

        // Generate pagination links
        for ($i = 1; $i <= $totalPages; $i++) {
            $pagerLinks[] = [
                'uri' => $urlPrefix . 'page=' . $i,
                'title' => $i,
                'active' => $i === $page,
            ];
        }

        $data = [
            'title' => 'Learning Hub - KEWASNET',
            'description' => 'Browse our comprehensive course catalog',
            'courses' => $courses,
            'filter' => $filter,
            'category' => $category,
            'level' => $level,
            'search' => $search,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_courses' => $totalCourses,
                'per_page' => $perPage,
                'links' => $pagerLinks,
                'has_previous' => $page > 1,
                'has_next' => $page < $totalPages,
                'previous_url' => $page > 1 ? $urlPrefix . 'page=' . ($page - 1) : null,
                'next_url' => $page < $totalPages ? $urlPrefix . 'page=' . ($page + 1) : null,
            ],
        ];

        return view('frontendV2/ksp/pages/learning-hub/catalog/index', $data);
    }

    /**
     * Dedicated courses page with sidebar filters
     */
    public function courses()
    {
        $userId = ClientAuth::getId() ?: null; // Allow null for unauthenticated users
        $courseModel = new CourseModel();
        $enrollmentModel = new CourseEnrollmentModel();
        $db = \Config\Database::connect();
        
        // Get filter parameters
        $filter = $this->request->getGet('filter') ?? 'all'; // all, free, paid
        $category = $this->request->getGet('category') ?? null;
        $level = $this->request->getGet('level') ?? null;
        $search = $this->request->getGet('search') ?? null;
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = 12; // Courses per page

        $builder = $courseModel->where('status', 1);

        // Apply filters
        if ($filter === 'free') {
            $builder->where('price', 0)->where('is_paid', 0);
        } elseif ($filter === 'paid') {
            $builder->groupStart()
                ->where('price >', 0)
                ->orWhere('is_paid', 1)
                ->groupEnd();
        }

        if ($category) {
            $builder->where('category_id', $category);
        }

        if ($level) {
            $builder->where('level', $level);
        }

        if ($search) {
            $builder->groupStart()
                ->like('title', $search)
                ->orLike('description', $search)
                ->orLike('summary', $search)
                ->groupEnd();
        }

        // Get total count for pagination
        $totalCourses = $builder->countAllResults(false);
        
        // Apply pagination
        $courses = $builder->orderBy('created_at', 'DESC')
                          ->limit($perPage, ($page - 1) * $perPage)
                          ->findAll();
        
        // Add enrollment status for each course
        foreach ($courses as &$course) {
            $course['is_enrolled'] = false;
            $course['progress'] = 0;
            
            if ($userId) {
                $course['is_enrolled'] = $enrollmentModel->isEnrolled($userId, $course['id']);
                if ($course['is_enrolled']) {
                    $course['progress'] = $this->courseService->calculateProgress($userId, $course['id']);
                }
            }
        }

        // Calculate pagination data
        $totalPages = ceil($totalCourses / $perPage);
        
        // Build pagination links with current filters
        $baseUrl = base_url('ksp/learning-hub/courses');
        $queryParams = http_build_query(array_filter([
            'filter' => $filter !== 'all' ? $filter : null,
            'category' => $category,
            'level' => $level,
            'search' => $search,
        ]));
        $urlPrefix = $baseUrl . ($queryParams ? '?' . $queryParams . '&' : '?');

        // Get course categories for sidebar filter
        $categories = [];
        if ($db->tableExists('course_categories')) {
            $categories = $db->table('course_categories')
                ->select('id, name, description')
                ->where('deleted_at', null)
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultArray();
            
            // Add course count for each category
            foreach ($categories as &$cat) {
                $cat['course_count'] = $courseModel->where('category_id', $cat['id'])
                    ->where('status', 1)
                    ->countAllResults();
            }
        }

        // Get counts for filter badges
        $totalFree = $courseModel->where('status', 1)
            ->where('price', 0)
            ->where('is_paid', 0)
            ->countAllResults();
        
        $totalPaid = $courseModel->where('status', 1)
            ->groupStart()
            ->where('price >', 0)
            ->orWhere('is_paid', 1)
            ->groupEnd()
            ->countAllResults();

        $data = [
            'title' => 'All Courses - Learning Hub',
            'description' => 'Browse all our comprehensive courses',
            'courses' => $courses,
            'filter' => $filter,
            'category' => $category,
            'level' => $level,
            'search' => $search,
            'categories' => $categories,
            'total_free' => $totalFree,
            'total_paid' => $totalPaid,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_courses' => $totalCourses,
                'per_page' => $perPage,
                'has_previous' => $page > 1,
                'has_next' => $page < $totalPages,
                'previous_url' => $page > 1 ? $urlPrefix . 'page=' . ($page - 1) : null,
                'next_url' => $page < $totalPages ? $urlPrefix . 'page=' . ($page + 1) : null,
                'url_prefix' => $urlPrefix,
            ],
        ];

        return view('frontendV2/ksp/pages/learning-hub/courses/index', $data);
    }

    /**
     * Course details/overview
     */
    public function courseDetails($courseId)
    {
        $userId = ClientAuth::getId() ?: null; // Allow null for unauthenticated users
        $course = $this->courseService->getCourseOverview($courseId);

        if (!$course) {
            return redirect()->to('ksp/learning-hub')->with('error', 'Course not found');
        }

        // Check enrollment status
        $enrollmentModel = new CourseEnrollmentModel();
        $isEnrolled = $userId ? $enrollmentModel->isEnrolled($userId, $courseId) : false;

        $data = [
            'title' => $course['title'] . ' - KEWASNET',
            'description' => $course['summary'] ?? '',
            'course' => $course,
            'is_enrolled' => $isEnrolled,
            'user_id' => $userId,
        ];

        return view('frontendV2/ksp/pages/learning-hub/catalog/course-details', $data);
    }

    /**
     * Enroll in course
     */
    public function enroll()
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

        if (!$userId || !$courseId) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Missing required parameters'
                ]);
        }

        $result = $this->courseService->enrollUser($userId, $courseId);

        if ($result['status'] === 'success') {
            $result['redirect'] = base_url('ksp/learning-hub/learn/' . $courseId);
        } elseif (isset($result['requires_payment']) && $result['requires_payment']) {
            $result['redirect'] = base_url('ksp/payment/initiate');
        }

        return $this->response
            ->setContentType('application/json')
            ->setJSON($result);
    }

    /**
     * User's enrolled courses
     */
    public function myCourses()
    {
        $userId = ClientAuth::getId();
        $enrollmentModel = new CourseEnrollmentModel();
        $courseModel = new CourseModel();

        $enrollments = $enrollmentModel->getUserEnrollments($userId);
        $courses = [];

        foreach ($enrollments as $enrollment) {
            $course = $courseModel->find($enrollment['course_id']);
            if ($course) {
                $course['progress'] = $this->courseService->calculateProgress($userId, $course['id']);
                $course['enrollment_date'] = $enrollment['purchased_at'] ?? $enrollment['created_at'];
                $courses[] = $course;
            }
        }

        $data = [
            'title' => 'My Courses - KEWASNET',
            'description' => 'Your enrolled courses',
            'courses' => $courses,
        ];

        return view('frontendV2/ksp/pages/learning-hub/dashboard/my-courses', $data);
    }

    /**
     * Course player - main learning interface
     */
    public function coursePlayer($courseId)
    {
        $userId = ClientAuth::getId();

        // Check access
        if (!$this->courseService->checkAccess($userId, $courseId)) {
            return redirect()->to('ksp/learning-hub/course/' . $courseId)
                ->with('error', 'You need to enroll in this course to access the content');
        }

        $course = $this->courseService->getCourseContent($courseId, $userId);
        $progress = $this->courseService->calculateProgress($userId, $courseId);
        
        // Get completed lectures
        $progressModel = new \App\Models\CourseLectureProgressModel();
        $completedProgress = $progressModel->where('student_id', $userId)
                                          ->where('course_id', $courseId)
                                          ->where('status', 'completed')
                                          ->findAll();
        $completedLectures = array_column($completedProgress, 'lecture_id');

        $data = [
            'title' => $course['title'] . ' - Learning Hub',
            'description' => 'Course learning interface',
            'course' => $course,
            'progress' => $progress,
            'user_id' => $userId,
            'completed_lectures' => $completedLectures,
        ];

        return view('frontendV2/ksp/pages/learning-hub/learning/course-player', $data);
    }

    /**
     * Individual lecture view
     */
    public function lecture($courseId, $lectureId)
    {
        $userId = ClientAuth::getId();

        // Check access
        if (!$this->courseService->checkAccess($userId, $courseId)) {
            return redirect()->to('ksp/learning-hub/course/' . $courseId)
                ->with('error', 'You need to enroll in this course to access the content');
        }

        $lectureModel = new \App\Models\CourseLectureModel();
        $lecture = $lectureModel->getLectureWithResources($lectureId);

        if (!$lecture) {
            return redirect()->to('ksp/learning-hub/learn/' . $courseId)
                ->with('error', 'Lecture not found');
        }
        
        // Check if lecture is completed
        $progressModel = new \App\Models\CourseLectureProgressModel();
        $isCompleted = $progressModel->isLectureCompleted($userId, $lectureId);

        // Get Vimeo embed if available
        $embedCode = null;
        $vimeoVideoId = $lecture['vimeo_video_id'] ?? null;
        
        // If vimeo_video_id is not set, try to extract from video_url
        if (empty($vimeoVideoId) && !empty($lecture['video_url'])) {
            if (strpos($lecture['video_url'], 'vimeo.com') !== false) {
                if (preg_match('/vimeo\.com\/(\d+)/', $lecture['video_url'], $matches)) {
                    $vimeoVideoId = $matches[1];
                }
            }
        }
        
        // Generate simple Vimeo embed code
        if (!empty($vimeoVideoId)) {
            $embedCode = '<iframe src="https://player.vimeo.com/video/' . $vimeoVideoId . '?h=&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" frameborder="0" allow="autoplay; fullscreen; picture-in-picture; clipboard-write" style="width:100%;height:100%;" title="' . esc($lecture['title']) . '"></iframe>';
        }

        $data = [
            'title' => $lecture['title'] . ' - Learning Hub',
            'description' => 'Lecture content',
            'lecture' => $lecture,
            'course_id' => $courseId,
            'embed_code' => $embedCode,
            'is_completed' => $isCompleted,
        ];

        return view('frontendV2/ksp/pages/learning-hub/learning/lecture-view', $data);
    }

    /**
     * Mark lecture as completed
     */
    public function markLectureComplete($courseId, $lectureId)
    {
        $userId = ClientAuth::getId();

        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to track progress'
            ]);
        }

        // Check access
        if (!$this->courseService->checkAccess($userId, $courseId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You need to enroll in this course first'
            ]);
        }

        $progressModel = new \App\Models\CourseLectureProgressModel();
        
        try {
            $result = $progressModel->markAsCompleted($userId, $courseId, $lectureId);
            
            // Calculate new progress percentage
            $percentage = $progressModel->getCourseCompletionPercentage($userId, $courseId);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Lecture marked as completed',
                'progress_percentage' => $percentage
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update progress: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Quiz interface
     */
    public function quiz($courseId, $sectionId)
    {
        $userId = ClientAuth::getId();

        // Check access
        if (!$this->courseService->checkAccess($userId, $courseId)) {
            return redirect()->to('ksp/learning-hub/course/' . $courseId)
                ->with('error', 'You need to enroll in this course to access quizzes');
        }

        $sectionModel = new \App\Models\CourseSectionModel();
        $section = $sectionModel->find($sectionId);

        if (!$section || $section['course_id'] != $courseId) {
            return redirect()->to('ksp/learning-hub/learn/' . $courseId)
                ->with('error', 'Section not found');
        }

        // Get course details
        $courseModel = new CourseModel();
        $course = $courseModel->find($courseId);

        if (!$course) {
            return redirect()->to('ksp/learning-hub')
                ->with('error', 'Course not found');
        }

        // Check if section has a quiz
        if (empty($section['quiz_id'])) {
            return redirect()->to('ksp/learning-hub/learn/' . $courseId)
                ->with('error', 'No quiz available for this section');
        }

        $quizModel = new \App\Models\QuizModel();
        $quiz = $quizModel->find($section['quiz_id']);

        if (!$quiz) {
            return redirect()->to('ksp/learning-hub/learn/' . $courseId)
                ->with('error', 'Quiz not found');
        }

        $quizWithQuestions = $quizModel->getQuizWithQuestions($quiz['id']);

        // Get user's previous attempts
        $attemptModel = new \App\Models\QuizAttemptModel();
        $previousAttempts = $attemptModel->where('user_id', $userId)
            ->where('quiz_id', $quiz['id'])
            ->orderBy('completed_at', 'DESC')
            ->findAll();
        
        // Check if user wants to view a specific attempt (via query parameter)
        $viewAttemptId = $this->request->getGet('view_attempt');
        $latestAttempt = null;
        $attemptAnswers = [];
        
        // Load answers for ALL previous attempts to show complete history
        $answerModel = new \App\Models\QuizAnswerModel();
        $allAttemptsAnswers = []; // Structure: [attemptId => [questionId => answer_data]]
        
        foreach ($previousAttempts as $attempt) {
            $answers = $answerModel->where('attempt_id', $attempt['id'])->findAll();
            $allAttemptsAnswers[$attempt['id']] = [];
            
            foreach ($answers as $answer) {
                $userAnswer = $answer['option_id'] ?? $answer['answer_text'];
                $allAttemptsAnswers[$attempt['id']][$answer['question_id']] = [
                    'user_answer' => (string)$userAnswer,
                    'is_correct' => (bool)$answer['is_correct'],
                    'correct_answer' => null,
                    'attempt_number' => count($previousAttempts) - array_search($attempt, $previousAttempts)
                ];
            }
        }
        
        // Get correct answers for all questions
        foreach ($quizWithQuestions['questions'] as $question) {
            if ($question['question_type'] === 'multiple_choice' || $question['question_type'] === 'true_false') {
                foreach ($question['options'] as $option) {
                    if ($option['is_correct'] == 1) {
                        $correctAnswer = (string)$option['id'];
                        // Apply correct answer to all attempts for this question
                        foreach ($allAttemptsAnswers as $attemptId => &$attemptData) {
                            if (isset($attemptData[$question['id']])) {
                                $attemptData[$question['id']]['correct_answer'] = $correctAnswer;
                            }
                        }
                        break;
                    }
                }
            }
        }
        
        // If viewing a specific attempt, set it as the latest
        if ($viewAttemptId && !empty($previousAttempts)) {
            foreach ($previousAttempts as $attempt) {
                if ($attempt['id'] === $viewAttemptId) {
                    $latestAttempt = $attempt;
                    $attemptAnswers = $allAttemptsAnswers[$viewAttemptId] ?? [];
                    break;
                }
            }
        }

        // Check if user can take another attempt
        $attemptsCount = count($previousAttempts);
        $maxAttempts = $quiz['max_attempts'] ?? null; // null means unlimited
        $canTakeNewAttempt = $maxAttempts === null || $attemptsCount < $maxAttempts;
        $attemptsRemaining = $maxAttempts !== null ? ($maxAttempts - $attemptsCount) : null;

        $data = [
            'title' => $quiz['title'] . ' - Quiz',
            'description' => 'Section quiz',
            'quiz' => $quizWithQuestions,
            'section' => $section,
            'course' => $course,
            'course_id' => $courseId,
            'previous_attempts' => $previousAttempts,
            'latest_attempt' => $latestAttempt,
            'attempt_answers' => $attemptAnswers,
            'all_attempts_answers' => $allAttemptsAnswers,
            'can_take_new_attempt' => $canTakeNewAttempt,
            'attempts_count' => $attemptsCount,
            'attempts_remaining' => $attemptsRemaining,
            'max_attempts' => $maxAttempts,
        ];

        return view('frontendV2/ksp/pages/learning-hub/learning/quiz-view', $data);
    }

    /**
     * Submit quiz
     */
    public function submitQuiz()
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
        $quizId = $this->request->getPost('quiz_id');
        $answers = $this->request->getPost('answers') ?? [];

        if (!$userId || !$quizId) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Missing required parameters'
                ]);
        }

        $result = $this->quizService->submitQuiz($userId, $quizId, $answers);

        return $this->response
            ->setContentType('application/json')
            ->setJSON($result);
    }

    /**
     * Get quiz attempt details for toggle view
     */
    public function getAttemptDetails()
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
        $attemptId = $this->request->getPost('attempt_id');

        if (!$userId || !$attemptId) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Missing required parameters'
                ]);
        }

        // Get the attempt
        $attemptModel = new \App\Models\QuizAttemptModel();
        $attempt = $attemptModel->find($attemptId);

        if (!$attempt) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Attempt not found'
                ]);
        }

        // Verify the attempt belongs to the current user (both are now UUIDs)
        if ($attempt['user_id'] !== $userId) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Access denied'
                ]);
        }

        // Get quiz questions
        $quizModel = new \App\Models\QuizModel();
        $quiz = $quizModel->getQuizWithQuestions($attempt['quiz_id']);

        // Get the answers
        $answerModel = new \App\Models\QuizAnswerModel();
        $answers = $answerModel->where('attempt_id', $attemptId)->findAll();

        // Format answers for frontend
        $attemptAnswers = [];
        foreach ($answers as $answer) {
            $userAnswer = $answer['option_id'] ?? $answer['answer_text'];
            $attemptAnswers[$answer['question_id']] = [
                'user_answer' => (string)$userAnswer,
                'is_correct' => (bool)$answer['is_correct'],
                'correct_answer' => null
            ];
        }

        // Get correct answers
        foreach ($quiz['questions'] as $question) {
            if (isset($attemptAnswers[$question['id']])) {
                if ($question['question_type'] === 'multiple_choice' || $question['question_type'] === 'true_false') {
                    foreach ($question['options'] as $option) {
                        if ($option['is_correct'] == 1) {
                            $attemptAnswers[$question['id']]['correct_answer'] = (string)$option['id'];
                            break;
                        }
                    }
                }
            }
        }

        return $this->response
            ->setContentType('application/json')
            ->setJSON([
                'status' => 'success',
                'attempt' => $attempt,
                'answers' => $attemptAnswers
            ]);
    }

    /**
     * User certificates
     */
    public function certificates()
    {
        $userId = ClientAuth::getId();
        $certificateModel = new CertificateModel();
        $courseModel = new CourseModel();

        $certificates = $certificateModel->getUserCertificates($userId);

        foreach ($certificates as &$certificate) {
            $course = $courseModel->find($certificate['course_id']);
            $certificate['course'] = $course;
        }

        $data = [
            'title' => 'My Certificates - KEWASNET',
            'description' => 'Your course completion certificates',
            'certificates' => $certificates,
        ];

        return view('frontendV2/ksp/pages/learning-hub/dashboard/certificates', $data);
    }

    /**
     * Download certificate
     */
    public function downloadCertificate($certificateId)
    {
        $userId = ClientAuth::getId();
        $certificateModel = new CertificateModel();

        $certificate = $certificateModel->find($certificateId);

        if (!$certificate || $certificate['user_id'] != $userId) {
            return redirect()->to('ksp/learning-hub/certificates')
                ->with('error', 'Certificate not found');
        }

        $filePath = $this->certificateService->downloadCertificate($certificateId);

        if (!$filePath || !file_exists($filePath)) {
            return redirect()->to('ksp/learning-hub/certificates')
                ->with('error', 'Certificate file not found');
        }

        return $this->response->download($filePath, null);
    }

    /**
     * User profile
     */
    public function profile()
    {
        $userId = ClientAuth::getId();
        
        if (!$userId) {
            return redirect()->to(base_url('learning-hub/login'));
        }
        
        $userModel = new \App\Models\UserModel();
        // Use find() instead of where()->first() and disable soft delete filter
        $user = $userModel->withDeleted()->find($userId);
        
        if (!$user) {
            log_message('error', 'User not found in profile: ' . $userId);
            // Try without soft delete filter
            $user = $userModel->find($userId);
        }

        $data = [
            'title' => 'Profile Settings - KEWASNET',
            'description' => 'Manage your profile',
            'user' => $user,
        ];

        return view('frontendV2/ksp/pages/learning-hub/dashboard/profile', $data);
    }

    /**
     * Update profile
     */
    public function updateProfile()
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
        $userModel = new \App\Models\UserModel();

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'bio' => $this->request->getPost('bio'),
        ];

        // Handle profile picture upload if provided
        $picture = $this->request->getFile('picture');
        if ($picture && $picture->isValid()) {
            $newName = $picture->getRandomName();
            $picture->move(ROOTPATH . 'public/uploads/profiles/', $newName);
            $data['picture'] = 'uploads/profiles/' . $newName;
        }

        $updated = $userModel->update($userId, $data);

        if ($updated) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'success',
                    'message' => 'Profile updated successfully'
                ]);
        }

        return $this->response
            ->setContentType('application/json')
            ->setJSON([
                'status' => 'error',
                'message' => 'Failed to update profile'
            ]);
    }

    /**
     * Change Password
     */
    public function changePassword()
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
        $userModel = new \App\Models\UserModel();

        // Get current user
        $user = $userModel->find($userId);
        if (!$user) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'User not found'
                ]);
        }

        // Get form data
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // Validate inputs
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'All fields are required'
                ]);
        }

        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Current password is incorrect'
                ]);
        }

        // Validate new password matches confirmation
        if ($newPassword !== $confirmPassword) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'New passwords do not match'
                ]);
        }

        // Validate password strength
        if (strlen($newPassword) < 8) {
            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Password must be at least 8 characters long'
                ]);
        }

        // Update password
        $updated = $userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT)
        ]);

        if ($updated) {
            // Log the password change
            log_message('info', "User #{$userId} changed their password from learning hub profile");

            // Clear all session data to force re-login
            session()->destroy();

            return $this->response
                ->setContentType('application/json')
                ->setJSON([
                    'status' => 'success',
                    'message' => 'Password changed successfully. Please login with your new password.',
                    'redirect' => base_url('ksp/login')
                ]);
        }

        return $this->response
            ->setContentType('application/json')
            ->setJSON([
                'status' => 'error',
                'message' => 'Failed to change password. Please try again.'
            ]);
    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        $userId = ClientAuth::getId();
        $enrollmentModel = new CourseEnrollmentModel();
        $certificateModel = new CertificateModel();
        $db = \Config\Database::connect();

        $enrollments = $enrollmentModel->getUserEnrollments($userId);
        $certificates = $certificateModel->getUserCertificates($userId);

        // Calculate statistics
        $inProgress = 0;
        $completed = 0;

        foreach ($enrollments as $enrollment) {
            $progress = $this->courseService->calculateProgress($userId, $enrollment['course_id']);
            if ($progress >= 100) {
                $completed++;
            } else {
                $inProgress++;
            }
        }

        // Get user's forum subscriptions (forums they've joined)
        $forumSubscriptions = $db->table('forum_members fm')
            ->select('f.id, f.name, f.slug, f.description, f.icon, f.color, 
                      (SELECT COUNT(*) FROM discussions WHERE forum_id = f.id AND deleted_at IS NULL) as discussion_count,
                      fm.joined_at')
            ->join('forums f', 'f.id = fm.forum_id')
            ->where('fm.user_id', $userId)
            ->where('f.deleted_at', null)
            ->orderBy('fm.joined_at', 'DESC')
            ->limit(6)
            ->get()
            ->getResultArray();

        // Get user's discussions (discussions they created or participated in)
        $userDiscussions = $db->table('discussions d')
            ->select('d.id, d.title, d.slug, d.view_count as views, d.is_locked, d.created_at,
                      f.name as forum_name, f.slug as forum_slug, f.color as forum_color,
                      u.first_name, u.last_name, u.picture,
                      (SELECT COUNT(*) FROM replies WHERE discussion_id = d.id AND deleted_at IS NULL) as reply_count')
            ->join('forums f', 'f.id = d.forum_id')
            ->join('system_users u', 'u.id = d.user_id')
            ->groupStart()
                ->where('d.user_id', $userId)
                ->orWhere('d.id IN (SELECT DISTINCT discussion_id FROM replies WHERE user_id = ' . $db->escape($userId) . ' AND deleted_at IS NULL)', null, false)
            ->groupEnd()
            ->where('d.deleted_at', null)
            ->where('f.deleted_at', null)
            ->orderBy('d.created_at', 'DESC')
            ->limit(6)
            ->get()
            ->getResultArray();

        // Get bookmarked discussions
        $bookmarkedDiscussions = $db->table('bookmarks b')
            ->select('d.id, d.title, d.slug, d.view_count as views, d.created_at,
                      f.name as forum_name, f.slug as forum_slug, f.color as forum_color,
                      u.first_name, u.last_name, u.picture,
                      (SELECT COUNT(*) FROM replies WHERE discussion_id = d.id AND deleted_at IS NULL) as reply_count,
                      b.created_at as bookmarked_at')
            ->join('discussions d', 'd.id = b.discussion_id')
            ->join('forums f', 'f.id = d.forum_id')
            ->join('system_users u', 'u.id = d.user_id')
            ->where('b.user_id', $userId)
            ->where('d.deleted_at', null)
            ->where('f.deleted_at', null)
            ->orderBy('b.created_at', 'DESC')
            ->limit(6)
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Dashboard - KEWASNET',
            'description' => 'Your personalized dashboard',
            'total_courses' => count($enrollments),
            'in_progress' => $inProgress,
            'completed' => $completed,
            'certificates' => count($certificates),
            'forumSubscriptions' => $forumSubscriptions,
            'userDiscussions' => $userDiscussions,
            'bookmarkedDiscussions' => $bookmarkedDiscussions,
        ];

        return view('frontendV2/ksp/pages/learning-hub/dashboard/index', $data);
    }
    
    /**
     * Download lecture attachment
     */
    public function downloadAttachment($attachmentId)
    {
        $userId = ClientAuth::getId();
        $attachmentModel = new \App\Models\LectureAttachmentModel();
        
        $attachment = $attachmentModel->find($attachmentId);
        
        if (!$attachment) {
            return redirect()->back()->with('error', 'Attachment not found');
        }
        
        // Verify user has access to the lecture's course
        $lectureModel = new \App\Models\CourseLectureModel();
        $lecture = $lectureModel->find($attachment['lecture_id']);
        
        if ($lecture) {
            $sectionModel = new \App\Models\CourseSectionModel();
            $section = $sectionModel->find($lecture['section_id']);
            
            if ($section) {
                if (!$this->courseService->checkAccess($userId, $section['course_id'])) {
                    return redirect()->back()->with('error', 'Access denied');
                }
            }
        }
        
        // Increment download count
        $attachmentModel->incrementDownload($attachmentId);
        
        $filePath = WRITEPATH . '../public/' . $attachment['file_path'];
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found');
        }
        
        return $this->response->download($filePath, null);
    }
}
