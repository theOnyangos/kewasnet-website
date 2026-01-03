<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LearningHubCoursesSeeder extends Seeder
{
    private function generateUUID(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    public function run()
    {
        $db = \Config\Database::connect();
        
        // First, update existing courses to status = 1 so they show up
        if ($db->tableExists('courses')) {
            $db->table('courses')
                ->where('status', 0)
                ->where('deleted_at', null)
                ->update(['status' => 1]);
            echo "Updated existing courses to status = 1\n";
        }
        
        // Check if we have any users
        $userId = 1;
        $userTableNames = ['users', 'user', 'clients'];
        foreach ($userTableNames as $tableName) {
            if ($db->tableExists($tableName)) {
                $users = $db->table($tableName)->select('id')->limit(1)->get()->getResultArray();
                if (!empty($users)) {
                    $userId = $users[0]['id'];
                    break;
                }
            }
        }
        
        // Check if we have any categories
        $categoryId = 1;
        $categoryTableNames = ['course_categories', 'categories'];
        foreach ($categoryTableNames as $tableName) {
            if ($db->tableExists($tableName)) {
                $categories = $db->table($tableName)->select('id')->limit(1)->get()->getResultArray();
                if (!empty($categories)) {
                    $categoryId = $categories[0]['id'];
                    break;
                }
            }
        }
        
        $courses = [
            [
                'id' => $this->generateUUID(),
                'user_id' => $userId,
                'category_id' => $categoryId,
                'title' => 'Introduction to Water Treatment',
                'summary' => 'Learn the fundamentals of water treatment processes including coagulation, flocculation, and sedimentation.',
                'description' => 'This comprehensive course covers the essential water treatment processes used in modern water treatment facilities. You will learn about coagulation, flocculation, sedimentation, and filtration techniques.',
                'level' => 'beginner',
                'price' => 0.00,
                'discount_price' => 0.00,
                'is_paid' => 0,
                'duration' => '4 weeks',
                'certificate' => 1,
                'status' => 1,
                'slug' => 'introduction-to-water-treatment',
                'image_url' => 'uploads/courses/introduction-to-water-tratment.jpg',
                'preview_video_url' => 'https://www.youtube.com/watch?v=u4k2XY-fJJY',
                'star_rating' => 4.5,
                'resources' => 'PDF guides, video tutorials, practice exercises',
                'goals' => json_encode([
                    'Understand basic water treatment principles',
                    'Learn coagulation and flocculation processes',
                    'Master sedimentation techniques',
                    'Apply filtration methods'
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'user_id' => $userId,
                'category_id' => $categoryId,
                'title' => 'Climate Change and Water Resources',
                'summary' => 'Explore the impacts of climate change on water resources and adaptation strategies.',
                'description' => 'This course examines how climate change affects water availability, quality, and management. Learn about adaptation strategies and resilience planning for water systems in the face of changing climate patterns.',
                'level' => 'intermediate',
                'price' => 0.00,
                'discount_price' => 0.00,
                'is_paid' => 0,
                'duration' => '5 weeks',
                'certificate' => 1,
                'status' => 1,
                'slug' => 'climate-change-and-water-resources',
                'image_url' => 'uploads/courses/climete-change-and-water-source.jpg',
                'preview_video_url' => 'https://www.youtube.com/watch?v=u4k2XY-fJJY',
                'star_rating' => 4.6,
                'resources' => 'Case studies, research papers, interactive simulations',
                'goals' => json_encode([
                    'Understand climate impacts on water',
                    'Learn adaptation strategies',
                    'Develop resilience plans',
                    'Apply climate-smart water management'
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'user_id' => $userId,
                'category_id' => $categoryId,
                'title' => 'Water and Sanitation Programs',
                'summary' => 'Learn how to design and implement effective Water and Sanitation programs in communities.',
                'description' => 'This practical course covers the design, implementation, and monitoring of water and sanitation programs in various contexts. Includes case studies and best practices from successful projects worldwide.',
                'level' => 'intermediate',
                'price' => 20000.00,
                'discount_price' => 15000.00,
                'is_paid' => 1,
                'duration' => '8 weeks',
                'certificate' => 1,
                'status' => 1,
                'slug' => 'water-and-sanitation-programs',
                'image_url' => 'uploads/courses/water-and-sanitation-program.jpg',
                'preview_video_url' => 'https://www.youtube.com/watch?v=u4k2XY-fJJY',
                'star_rating' => 4.7,
                'resources' => 'Project templates, implementation guides, monitoring tools',
                'goals' => json_encode([
                    'Design effective water and sanitation programs',
                    'Implement community-based solutions',
                    'Monitor and evaluate programs',
                    'Ensure long-term sustainability'
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'user_id' => $userId,
                'category_id' => $categoryId,
                'title' => 'Water Conservation Techniques',
                'summary' => 'Master water conservation methods for residential, commercial, and agricultural use.',
                'description' => 'Learn practical water conservation techniques that can be applied in homes, businesses, and farms. Includes rainwater harvesting, greywater reuse, efficient irrigation methods, and smart water management strategies.',
                'level' => 'beginner',
                'price' => 0.00,
                'discount_price' => 0.00,
                'is_paid' => 0,
                'duration' => '3 weeks',
                'certificate' => 1,
                'status' => 1,
                'slug' => 'water-conservation-techniques',
                'image_url' => 'uploads/courses/water-conversation-technique.jpg',
                'preview_video_url' => 'https://www.youtube.com/watch?v=u4k2XY-fJJY',
                'star_rating' => 4.4,
                'resources' => 'DIY guides, video demonstrations, calculation tools',
                'goals' => json_encode([
                    'Understand water conservation principles',
                    'Learn rainwater harvesting techniques',
                    'Implement greywater reuse systems',
                    'Apply efficient irrigation methods'
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'user_id' => $userId,
                'category_id' => $categoryId,
                'title' => 'About Water, Sanitation and Hygiene (WASH)',
                'summary' => 'Comprehensive introduction to WASH principles and their importance in public health.',
                'description' => 'This foundational course introduces the critical concepts of Water, Sanitation, and Hygiene (WASH) and their role in promoting public health and well-being. Learn about global WASH challenges and solutions.',
                'level' => 'beginner',
                'price' => 0.00,
                'discount_price' => 0.00,
                'is_paid' => 0,
                'duration' => '4 weeks',
                'certificate' => 1,
                'status' => 1,
                'slug' => 'about-wash',
                'image_url' => 'uploads/courses/about-water-cernitation-and-hygine.jpg',
                'preview_video_url' => 'https://www.youtube.com/watch?v=u4k2XY-fJJY',
                'star_rating' => 4.5,
                'resources' => 'WHO guidelines, infographics, video content',
                'goals' => json_encode([
                    'Understand WASH fundamentals',
                    'Learn about global WASH challenges',
                    'Explore WASH solutions and interventions',
                    'Apply WASH principles in communities'
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $this->generateUUID(),
                'user_id' => $userId,
                'category_id' => $categoryId,
                'title' => 'Learning Water, Sanitation and Hygiene',
                'summary' => 'Advanced training in WASH program implementation and community engagement strategies.',
                'description' => 'This advanced course provides in-depth training on implementing WASH programs, engaging communities, and ensuring sustainable water and sanitation solutions. Includes real-world case studies and practical exercises.',
                'level' => 'advanced',
                'price' => 15000.00,
                'discount_price' => 12000.00,
                'is_paid' => 1,
                'duration' => '6 weeks',
                'certificate' => 1,
                'status' => 1,
                'slug' => 'learning-wash',
                'image_url' => 'uploads/courses/learning-water-sernitation-and-hygine.jpg',
                'preview_video_url' => 'https://www.youtube.com/watch?v=u4k2XY-fJJY',
                'star_rating' => 4.8,
                'resources' => 'Implementation frameworks, assessment tools, evaluation templates',
                'goals' => json_encode([
                    'Master WASH program implementation',
                    'Develop community engagement strategies',
                    'Conduct WASH assessments and evaluations',
                    'Ensure program sustainability and impact'
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        
        // Only insert if courses don't already exist with these slugs
        foreach ($courses as $course) {
            $existing = $db->table('courses')
                ->where('slug', $course['slug'])
                ->get()
                ->getRowArray();
            
            if (!$existing) {
                $db->table('courses')->insert($course);
            }
        }
        
        echo "Learning Hub courses seeded successfully!\n";
    }
}

