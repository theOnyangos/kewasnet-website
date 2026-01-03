<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class ForumSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Get admin user ID from system_users
        $adminUser = $db->table('system_users')->where('role_id', 'role-admin')->get()->getRow();
        $adminId = $adminUser ? $adminUser->id : null;

        // If no admin found, get the first user
        if (!$adminId) {
            $firstUser = $db->table('system_users')->get()->getRow();
            $adminId = $firstUser ? $firstUser->id : Uuid::uuid4()->toString();
        }

        $forums = [
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Water Quality Management',
                'slug' => 'water-quality-management',
                'description' => 'Discuss water quality testing methods, treatment technologies, and best practices for maintaining safe drinking water standards. Share experiences with water quality monitoring and troubleshooting quality issues.',
                'icon' => 'droplets',
                'color' => '#3B82F6',
                'is_draft' => false,
                'is_active' => true,
                'sort_order' => 1,
                'total_discussions' => 0,
                'total_replies' => 0,
                'last_activity_at' => null,
                'created_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 'a0a39f42-feee-45f3-b338-8c208d2f5b5e',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Borehole Construction & Maintenance',
                'slug' => 'borehole-construction-maintenance',
                'description' => 'Technical discussions about borehole drilling, construction techniques, pump installation, and maintenance schedules. Share knowledge about borehole rehabilitation and troubleshooting common issues.',
                'icon' => 'cpu',
                'color' => '#059669',
                'is_draft' => false,
                'is_active' => true,
                'sort_order' => 2,
                'total_discussions' => 0,
                'total_replies' => 0,
                'last_activity_at' => null,
                'created_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => '9b8fceec-f186-45e8-a013-4cefaba94075',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Community Water Projects',
                'slug' => 'community-water-projects',
                'description' => 'Share success stories and challenges from community-led water projects. Discuss project planning, community engagement strategies, and sustainable management approaches for water schemes.',
                'icon' => 'users',
                'color' => '#F97316',
                'is_draft' => false,
                'is_active' => true,
                'sort_order' => 3,
                'total_discussions' => 0,
                'total_replies' => 0,
                'last_activity_at' => null,
                'created_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => '989219df-0e7e-4958-99a8-28c550fc17ca',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Water Conservation & Climate',
                'slug' => 'water-conservation-climate',
                'description' => 'Discuss climate change impacts on water resources, water conservation techniques, rainwater harvesting, and adaptation strategies for water security in changing climatic conditions.',
                'icon' => 'cloud-rain',
                'color' => '#60A5FA',
                'is_draft' => false,
                'is_active' => true,
                'sort_order' => 4,
                'total_discussions' => 0,
                'total_replies' => 0,
                'last_activity_at' => null,
                'created_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => '2fc3ebd0-f8f5-4cfb-af12-41518ed3b02a',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Water Policy & Governance',
                'slug' => 'water-policy-governance',
                'description' => 'Forum for discussing water policies, regulatory frameworks, governance structures, and legal aspects of water management. Share insights on policy implementation and advocacy.',
                'icon' => 'gavel',
                'color' => '#8B5CF6',
                'is_draft' => false,
                'is_active' => true,
                'sort_order' => 5,
                'total_discussions' => 0,
                'total_replies' => 0,
                'last_activity_at' => null,
                'created_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => '9c097fc3-dcea-4996-9dd2-6fd9ba92d839',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Water Safety & Health',
                'slug' => 'water-safety-health',
                'description' => 'Discuss water-related health issues, waterborne diseases prevention, water safety protocols, and public health aspects of water management. Share best practices for ensuring safe water access.',
                'icon' => 'shield-check',
                'color' => '#10B981',
                'is_draft' => false,
                'is_active' => true,
                'sort_order' => 6,
                'total_discussions' => 0,
                'total_replies' => 0,
                'last_activity_at' => null,
                'created_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => '980d69d7-514f-4265-91c3-5ce5bb5c85b5',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Sustainable Water Technologies',
                'slug' => 'sustainable-water-technologies',
                'description' => 'Explore innovative and sustainable water technologies including solar pumping systems, smart water meters, IoT monitoring solutions, and energy-efficient water treatment systems.',
                'icon' => 'leaf',
                'color' => '#4ADE80',
                'is_draft' => false,
                'is_active' => true,
                'sort_order' => 7,
                'total_discussions' => 0,
                'total_replies' => 0,
                'last_activity_at' => null,
                'created_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => '4fb1d2f9-a1c1-4aa4-9408-20a3e7cfe8c9',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Training & Capacity Building',
                'slug' => 'training-capacity-building',
                'description' => 'Share training materials, discuss capacity building programs, and organize knowledge sharing sessions for water professionals. Exchange learning resources and certification opportunities.',
                'icon' => 'book',
                'color' => '#EF4444',
                'is_draft' => false,
                'is_active' => true,
                'sort_order' => 8,
                'total_discussions' => 0,
                'total_replies' => 0,
                'last_activity_at' => null,
                'created_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => '64f546da-d087-4aaf-abfb-bf555a15f7c5',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Global Water Initiatives',
                'slug' => 'global-water-initiatives',
                'description' => 'Discuss international water programs, SDG 6 implementation, cross-border water management, and global best practices in water sector development. Share experiences from different regions.',
                'icon' => 'globe',
                'color' => '#14B8A6',
                'is_draft' => false,
                'is_active' => true,
                'sort_order' => 9,
                'total_discussions' => 0,
                'total_replies' => 0,
                'last_activity_at' => null,
                'created_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => '80b59395-271e-4f5e-98ef-f2396218c985',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'General Discussion',
                'slug' => 'general-discussion',
                'description' => 'Open forum for general water-related discussions, announcements, networking, and topics that don\'t fit into specific categories. Share industry news and connect with fellow water professionals.',
                'icon' => 'message-square',
                'color' => '#EC4899',
                'is_draft' => false,
                'is_active' => true,
                'sort_order' => 10,
                'total_discussions' => 0,
                'total_replies' => 0,
                'last_activity_at' => null,
                'created_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
        ];

        // Insert the forums
        foreach ($forums as $forum) {
            $db->table('forums')->insert($forum);
        }

        echo "✅ Successfully created 10 forum categories!\n";
        echo "📋 Forums created:\n";
        foreach ($forums as $index => $forum) {
            echo "   " . ($index + 1) . ". {$forum['name']} ({$forum['slug']})\n";
        }
        echo "\n🎯 All forums are active and ready for discussions!\n";
    }
}
