<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class LearningHubSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Get existing courses, sections, lectures, quizzes, and users
        $courses = [];
        $sections = [];
        $lectures = [];
        $quizzes = [];
        $users = [];
        
        // Safely get existing data
        if ($db->tableExists('courses')) {
            $courses = $db->table('courses')->select('id')->limit(4)->get()->getResultArray();
        }
        if ($db->tableExists('course_sections')) {
            $sections = $db->table('course_sections')->select('id, course_id')->limit(8)->get()->getResultArray();
        }
        if ($db->tableExists('course_lectures')) {
            $lectures = $db->table('course_lectures')->select('id, section_id')->limit(12)->get()->getResultArray();
        }
        if ($db->tableExists('quizzes')) {
            $quizzes = $db->table('quizzes')->select('id')->limit(4)->get()->getResultArray();
        }
        
        // Try different user table names
        $userTableNames = ['users', 'user', 'clients'];
        foreach ($userTableNames as $tableName) {
            if ($db->tableExists($tableName)) {
                $users = $db->table($tableName)->select('id')->limit(5)->get()->getResultArray();
                break;
            }
        }
        
        // If no data exists, create minimal references
        if (empty($courses)) {
            // Create a sample course if courses table exists
            if ($db->tableExists('courses')) {
                $db->table('courses')->insert([
                    'user_id' => 1,
                    'category_id' => 1,
                    'title' => 'Sample Water Treatment Course',
                    'summary' => 'Introduction to water treatment processes',
                    'level' => 'beginner',
                    'price' => 0,
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $courses = [['id' => $db->insertID()]];
            } else {
                $courses = [['id' => 1]];
            }
        }
        
        if (empty($sections) && $db->tableExists('course_sections')) {
            $sectionData = [];
            foreach ($courses as $course) {
                $sectionData[] = [
                    'id' => Uuid::uuid4()->toString(),
                    'course_id' => $course['id'],
                    'title' => 'Introduction Section',
                    'description' => 'Introduction to the course content',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $sectionData[] = [
                    'id' => Uuid::uuid4()->toString(),
                    'course_id' => $course['id'],
                    'title' => 'Advanced Topics',
                    'description' => 'Advanced concepts and applications',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            $db->table('course_sections')->insertBatch($sectionData);
            $sections = $db->table('course_sections')->select('id, course_id')->get()->getResultArray();
        }
        
        if (empty($lectures) && $db->tableExists('course_lectures') && !empty($sections)) {
            $lectureData = [];
            foreach ($sections as $section) {
                $lectureData[] = [
                    'section_id' => $section['id'],
                    'title' => 'Lecture 1: Introduction',
                    'description' => 'Introduction to the topic',
                    'duration' => 600,
                    'is_preview' => 1,
                    'order_index' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $lectureData[] = [
                    'section_id' => $section['id'],
                    'title' => 'Lecture 2: Core Concepts',
                    'description' => 'Understanding core concepts',
                    'duration' => 900,
                    'is_preview' => 0,
                    'order_index' => 2,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            $db->table('course_lectures')->insertBatch($lectureData);
            $lectures = $db->table('course_lectures')->select('id, section_id')->get()->getResultArray();
        }
        
        if (empty($quizzes) && $db->tableExists('quizzes') && !empty($sections)) {
            // Create quizzes for sections
            $quizData = [];
            foreach ($sections as $section) {
                $quizData[] = [
                    'course_section_id' => $section['id'],
                    'title' => 'Quiz for Section ' . $section['id'],
                    'description' => 'Test your understanding of this section',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            $db->table('quizzes')->insertBatch($quizData);
            $quizzes = $db->table('quizzes')->select('id')->get()->getResultArray();
        }
        
        if (empty($users)) {
            $users = [['id' => 1]]; // Default user ID
        }
        
        $courseId = $courses[0]['id'];
        $userId = $users[0]['id'];
        $quizId = $quizzes[0]['id'] ?? 1;
        $lectureId = $lectures[0]['id'] ?? 1;
        $sectionId = $sections[0]['id'] ?? 1;
        
        // 1. Seed Quiz Questions
        if ($db->tableExists('quiz_questions') && !empty($quizzes)) {
            $this->seedQuizQuestions($db, $quizzes);
        }
        
        // 2. Seed Quiz Question Options
        if ($db->tableExists('quiz_question_options')) {
            $this->seedQuizQuestionOptions($db);
        }
        
        // 3. Seed Quiz Attempts
        if ($db->tableExists('quiz_attempts') && !empty($quizzes) && !empty($users)) {
            $this->seedQuizAttempts($db, $quizzes, $users);
        }
        
        // 4. Seed Quiz Answers
        if ($db->tableExists('quiz_answers')) {
            $this->seedQuizAnswers($db);
        }
        
        // 5. Seed Lecture Attachments
        if ($db->tableExists('lecture_attachments') && !empty($lectures)) {
            $this->seedLectureAttachments($db, $lectures);
        }
        
        // 6. Seed Lecture Links
        if ($db->tableExists('lecture_links') && !empty($lectures)) {
            $this->seedLectureLinks($db, $lectures);
        }
        
        // 7. Seed Course Instructors
        if ($db->tableExists('course_instructors') && !empty($courses) && !empty($users)) {
            $this->seedCourseInstructors($db, $courses, $users);
        }
        
        // 8. Seed Vimeo Videos
        if ($db->tableExists('vimeo_videos') && !empty($lectures)) {
            $this->seedVimeoVideos($db, $lectures);
        }
        
        // 9. Seed User Progress
        if ($db->tableExists('user_progress') && !empty($courses) && !empty($users)) {
            $this->seedUserProgress($db, $courses, $sections, $lectures, $users);
        }
        
        // 10. Seed Course Certificates
        if ($db->tableExists('course_certificates') && !empty($courses) && !empty($users)) {
            $this->seedCourseCertificates($db, $courses, $users);
        }
    }
    
    private function seedQuizQuestions($db, $quizzes)
    {
        $questions = [];
        $order = 1;
        
        foreach ($quizzes as $quiz) {
            // Multiple choice questions
            $questions[] = [
                'quiz_id' => $quiz['id'],
                'question_text' => 'What is the primary purpose of coagulation in water treatment?',
                'question_type' => 'multiple_choice',
                'points' => 5,
                'order_index' => $order++,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            $questions[] = [
                'quiz_id' => $quiz['id'],
                'question_text' => 'Which chemical is commonly used in the coagulation process?',
                'question_type' => 'multiple_choice',
                'points' => 5,
                'order_index' => $order++,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            // True/False question
            $questions[] = [
                'quiz_id' => $quiz['id'],
                'question_text' => 'Coagulation removes all bacteria from water.',
                'question_type' => 'true_false',
                'points' => 3,
                'order_index' => $order++,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            // Short answer question
            $questions[] = [
                'quiz_id' => $quiz['id'],
                'question_text' => 'Explain the difference between coagulation and flocculation.',
                'question_type' => 'short_answer',
                'points' => 10,
                'order_index' => $order++,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        
        $db->table('quiz_questions')->insertBatch($questions);
    }
    
    private function seedQuizQuestionOptions($db)
    {
        $questions = $db->table('quiz_questions')
            ->where('question_type', 'multiple_choice')
            ->get()
            ->getResultArray();
        
        $options = [];
        
        foreach ($questions as $index => $question) {
            if ($index % 2 == 0) {
                // First question options
                $options[] = [
                    'question_id' => $question['id'],
                    'option_text' => 'To remove suspended particles',
                    'is_correct' => 1,
                    'order_index' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $options[] = [
                    'question_id' => $question['id'],
                    'option_text' => 'To add nutrients to water',
                    'is_correct' => 0,
                    'order_index' => 2,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $options[] = [
                    'question_id' => $question['id'],
                    'option_text' => 'To increase water temperature',
                    'is_correct' => 0,
                    'order_index' => 3,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $options[] = [
                    'question_id' => $question['id'],
                    'option_text' => 'To change water color',
                    'is_correct' => 0,
                    'order_index' => 4,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            } else {
                // Second question options
                $options[] = [
                    'question_id' => $question['id'],
                    'option_text' => 'Aluminum sulfate (Alum)',
                    'is_correct' => 1,
                    'order_index' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $options[] = [
                    'question_id' => $question['id'],
                    'option_text' => 'Sodium chloride',
                    'is_correct' => 0,
                    'order_index' => 2,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $options[] = [
                    'question_id' => $question['id'],
                    'option_text' => 'Calcium carbonate',
                    'is_correct' => 0,
                    'order_index' => 3,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $options[] = [
                    'question_id' => $question['id'],
                    'option_text' => 'Potassium permanganate',
                    'is_correct' => 0,
                    'order_index' => 4,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
        }
        
        // Add true/false options
        $trueFalseQuestions = $db->table('quiz_questions')
            ->where('question_type', 'true_false')
            ->get()
            ->getResultArray();
        
        foreach ($trueFalseQuestions as $question) {
            $options[] = [
                'question_id' => $question['id'],
                'option_text' => 'True',
                'is_correct' => 0,
                'order_index' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $options[] = [
                'question_id' => $question['id'],
                'option_text' => 'False',
                'is_correct' => 1,
                'order_index' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        
        if (!empty($options)) {
            $db->table('quiz_question_options')->insertBatch($options);
        }
    }
    
    private function seedQuizAttempts($db, $quizzes, $users)
    {
        $attempts = [];
        
        foreach ($users as $user) {
            foreach ($quizzes as $quiz) {
                $attempts[] = [
                    'user_id' => $user['id'],
                    'quiz_id' => $quiz['id'],
                    'score' => rand(60, 100),
                    'passed' => rand(0, 1),
                    'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days')),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
        }
        
        if (!empty($attempts)) {
            $db->table('quiz_attempts')->insertBatch($attempts);
        }
    }
    
    private function seedQuizAnswers($db)
    {
        $attempts = $db->table('quiz_attempts')->get()->getResultArray();
        $answers = [];
        
        foreach ($attempts as $attempt) {
            $questions = $db->table('quiz_questions')
                ->where('quiz_id', $attempt['quiz_id'])
                ->get()
                ->getResultArray();
            
            foreach ($questions as $question) {
                $correctOption = $db->table('quiz_question_options')
                    ->where('question_id', $question['id'])
                    ->where('is_correct', 1)
                    ->get()
                    ->getRowArray();
                
                if ($correctOption) {
                    $answers[] = [
                        'attempt_id' => $attempt['id'],
                        'question_id' => $question['id'],
                        'option_id' => $correctOption['id'],
                        'is_correct' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
            }
        }
        
        if (!empty($answers)) {
            $db->table('quiz_answers')->insertBatch($answers);
        }
    }
    
    private function seedLectureAttachments($db, $lectures)
    {
        $attachments = [];
        
        foreach ($lectures as $lecture) {
            $attachments[] = [
                'lecture_id' => $lecture['id'],
                'file_name' => 'Course_Notes_Lecture_' . $lecture['id'] . '.pdf',
                'file_path' => '/uploads/courses/lectures/notes_' . $lecture['id'] . '.pdf',
                'file_type' => 'application/pdf',
                'file_size' => rand(500000, 5000000),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            $attachments[] = [
                'lecture_id' => $lecture['id'],
                'file_name' => 'Lecture_Slides_' . $lecture['id'] . '.pptx',
                'file_path' => '/uploads/courses/lectures/slides_' . $lecture['id'] . '.pptx',
                'file_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'file_size' => rand(1000000, 10000000),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        
        if (!empty($attachments)) {
            $db->table('lecture_attachments')->insertBatch($attachments);
        }
    }
    
    private function seedLectureLinks($db, $lectures)
    {
        $links = [];
        
        foreach ($lectures as $lecture) {
            $links[] = [
                'lecture_id' => $lecture['id'],
                'link_title' => 'Additional Reading Material',
                'link_url' => 'https://www.who.int/water_sanitation_health/dwq/en/',
                'link_type' => 'resource',
                'order_index' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            $links[] = [
                'lecture_id' => $lecture['id'],
                'link_title' => 'Research Paper',
                'link_url' => 'https://www.ncbi.nlm.nih.gov/pmc/articles/example',
                'link_type' => 'external',
                'order_index' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        
        if (!empty($links)) {
            $db->table('lecture_links')->insertBatch($links);
        }
    }
    
    private function seedCourseInstructors($db, $courses, $users)
    {
        $instructors = [];
        
        foreach ($courses as $course) {
            // Assign first 2 users as instructors for each course
            foreach (array_slice($users, 0, 2) as $index => $user) {
                $instructors[] = [
                    'instructor_id' => $user['id'],
                    'course_id' => $course['id'],
                    'role' => $index == 0 ? 'primary' : 'assistant',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(10, 60) . ' days')),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
        }
        
        if (!empty($instructors)) {
            $db->table('course_instructors')->insertBatch($instructors);
        }
    }
    
    private function seedVimeoVideos($db, $lectures)
    {
        $videos = [];
        
        // Sample Vimeo video IDs (these are example IDs)
        $vimeoIds = ['123456789', '987654321', '456789123', '789123456'];
        
        foreach ($lectures as $index => $lecture) {
            $vimeoId = $vimeoIds[$index % count($vimeoIds)];
            $videos[] = [
                'lecture_id' => $lecture['id'],
                'vimeo_video_id' => $vimeoId . '_' . $lecture['id'],
                'video_url' => 'https://vimeo.com/' . $vimeoId,
                'thumbnail_url' => 'https://i.vimeocdn.com/video/' . $vimeoId . '_640.jpg',
                'duration' => rand(300, 1800), // 5 to 30 minutes in seconds
                'embed_code' => '<iframe src="https://player.vimeo.com/video/' . $vimeoId . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        
        if (!empty($videos)) {
            $db->table('vimeo_videos')->insertBatch($videos);
        }
    }
    
    private function seedUserProgress($db, $courses, $sections, $lectures, $users)
    {
        $progress = [];
        
        foreach ($users as $user) {
            foreach ($courses as $course) {
                // Course-level progress
                $progress[] = [
                    'user_id' => $user['id'],
                    'course_id' => $course['id'],
                    'section_id' => null,
                    'lecture_id' => null,
                    'progress_percentage' => rand(0, 100),
                    'watch_time' => rand(0, 7200), // 0 to 2 hours in seconds
                    'last_accessed_at' => date('Y-m-d H:i:s', strtotime('-' . rand(0, 7) . ' days')),
                    'completed_at' => rand(0, 1) ? date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days')) : null,
                    'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(10, 60) . ' days')),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                
                // Section-level progress
                foreach ($sections as $section) {
                    if ($section['course_id'] == $course['id']) {
                        $progress[] = [
                            'user_id' => $user['id'],
                            'course_id' => $course['id'],
                            'section_id' => $section['id'],
                            'lecture_id' => null,
                            'progress_percentage' => rand(0, 100),
                            'watch_time' => rand(0, 3600),
                            'last_accessed_at' => date('Y-m-d H:i:s', strtotime('-' . rand(0, 5) . ' days')),
                            'completed_at' => rand(0, 1) ? date('Y-m-d H:i:s', strtotime('-' . rand(1, 20) . ' days')) : null,
                            'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(5, 50) . ' days')),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                    }
                }
                
                // Lecture-level progress
                foreach ($lectures as $lecture) {
                    $section = array_filter($sections, function($s) use ($lecture) {
                        return $s['id'] == $lecture['section_id'];
                    });
                    
                    if (!empty($section)) {
                        $section = reset($section);
                        if ($section['course_id'] == $course['id']) {
                            $progress[] = [
                                'user_id' => $user['id'],
                                'course_id' => $course['id'],
                                'section_id' => $lecture['section_id'],
                                'lecture_id' => $lecture['id'],
                                'progress_percentage' => rand(0, 100),
                                'watch_time' => rand(0, 1800),
                                'last_accessed_at' => date('Y-m-d H:i:s', strtotime('-' . rand(0, 3) . ' days')),
                                'completed_at' => rand(0, 1) ? date('Y-m-d H:i:s', strtotime('-' . rand(1, 15) . ' days')) : null,
                                'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(3, 40) . ' days')),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                        }
                    }
                }
            }
        }
        
        if (!empty($progress)) {
            $db->table('user_progress')->insertBatch($progress);
        }
    }
    
    private function seedCourseCertificates($db, $courses, $users)
    {
        $certificates = [];
        
        foreach ($users as $user) {
            // Issue certificates for completed courses
            foreach (array_slice($courses, 0, 2) as $course) {
                $certificateNumber = 'CERT-' . strtoupper(uniqid());
                $verificationCode = strtoupper(substr(md5($user['id'] . $course['id'] . time()), 0, 10));
                
                $certificates[] = [
                    'user_id' => $user['id'],
                    'course_id' => $course['id'],
                    'certificate_url' => '/certificates/' . $certificateNumber . '.pdf',
                    'certificate_number' => $certificateNumber,
                    'verification_code' => $verificationCode,
                    'issued_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 90) . ' days')),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
        }
        
        if (!empty($certificates)) {
            $db->table('course_certificates')->insertBatch($certificates);
        }
    }
}

