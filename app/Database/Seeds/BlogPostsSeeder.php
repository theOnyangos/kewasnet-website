<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class BlogPostsSeeder extends Seeder
{
    public function run()
    {
        // Get category and tag IDs (assuming they exist)
        $categoryModel = model('App\Models\BlogPostCategory');
        $tagModel = model('App\Models\BlogTag');
        
        $categories = $categoryModel->findAll();
        $tags = $tagModel->findAll();

        $posts = [
            [
                'id' => Uuid::uuid4()->toString(),
                'user_id' => '1', // Assuming user with ID 1 exists
                'category_id' => $categories[0]['id'], // Policy & Governance
                'title' => 'New WASH Policy Framework Launched in Kenya',
                'slug' => 'new-wash-policy-framework-launched',
                'excerpt' => 'The government in collaboration with KEWASNET has launched a comprehensive WASH policy framework',
                'content' => '<p>The government in collaboration with KEWASNET has launched a comprehensive WASH policy framework aimed at improving water access and sanitation standards across all 47 counties.</p>',
                'featured_image' => 'policy-framework.jpg',
                'meta_title' => 'New WASH Policy Framework Launched in Kenya',
                'meta_description' => 'A comprehensive WASH policy framework launched in Kenya.',
                'meta_keywords' => 'WASH, Kenya, Policy',
                'reading_time' => 5,
                'views' => 1200,
                'is_featured' => 1,
                'status' => 'published',
                'published_at' => '2024-12-15 08:00:00',
                'created_at' => '2024-12-10 10:00:00',
                'updated_at' => '2024-12-15 08:00:00'
            ],
            [
                'id' => 'cb0600ea-8155-49a6-b959-ca02094b39e1',
                'id' => Uuid::uuid4()->toString(),
                'user_id' => '1',
                'category_id' => $categories[1]['id'], // Water Management
                'title' => 'Climate-Resilient Water Systems Workshop Success',
                'slug' => 'climate-resilient-water-systems-workshop',
                'excerpt' => 'Over 200 professionals attended our workshop on building water infrastructure',
                'content' => '<p>Over 200 professionals attended our workshop on building water infrastructure that can withstand climate change impacts. Key takeaways and resources now available.</p>',
                'featured_image' => 'water-workshop.jpg',
                'meta_title' => 'Climate-Resilient Water Systems Workshop Success',
                'meta_description' => 'A recap of the successful workshop on climate-resilient water systems.',
                'meta_keywords' => 'Water, Climate Change, Workshop',
                'reading_time' => 8,
                'views' => 856,
                'is_featured' => 1,
                'status' => 'published',
                'published_at' => '2024-12-10 09:00:00',
                'created_at' => '2024-12-05 14:00:00',
                'updated_at' => '2024-12-10 09:00:00'
            ],
            // Add more posts as needed...
        ];

        // Insert posts
        $this->db->table('blog_posts')->insertBatch($posts);

        // Insert post tags (assuming you have a blog_post_tags table)
        $postTags = [
            [
                'id' => Uuid::uuid4()->toString(),
                'post_id' => $posts[0]['id'],
                'tag_id' => $tags[0]['id'] // WASH
            ],
            [
                'id' => '81db427c-18dc-4718-b33d-241d8d16ecd1',
                'id' => Uuid::uuid4()->toString(),
                'post_id' => $posts[0]['id'],
                'tag_id' => $tags[1]['id'] // Kenya
            ],
        ];

        $this->db->table('blog_post_tags')->insertBatch($postTags);
    }
}