<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class BlogPostViewsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $posts = $db->table('blog_posts')->get()->getResultArray();
        
        if (empty($posts)) {
            echo "No posts found. Please seed posts first.\n";
            return;
        }

        $views = [];
        $ips = ['192.168.1.1', '10.0.0.1', '172.16.0.1', '203.0.113.5', '198.51.100.10'];
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Safari/605.1.15',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148',
            'Mozilla/5.0 (Linux; Android 10; SM-G981B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36'
        ];

        // Generate views for each post
        foreach ($posts as $post) {
            $viewCount = rand(50, 1000);
            
            for ($i = 0; $i < $viewCount; $i++) {
                $daysAgo = rand(0, 30);
                $hoursAgo = rand(0, 23);
                $minutesAgo = rand(0, 59);
                
                $views[] = [
                    'id' => Uuid::uuid4()->toString(),
                    'post_id' => $post['id'],
                    'ip_address' => $ips[array_rand($ips)],
                    'user_agent' => $userAgents[array_rand($userAgents)],
                    'created_at' => date('Y-m-d H:i:s', strtotime("-{$daysAgo} days -{$hoursAgo} hours -{$minutesAgo} minutes")),
                ];
                
                // Insert in batches to avoid memory issues
                if (count($views) >= 1000) {
                    $db->table('blog_post_views')->insertBatch($views);
                    $views = [];
                }
            }
        }
        
        // Insert any remaining views
        if (!empty($views)) {
            $db->table('blog_post_views')->insertBatch($views);
        }
    }
}