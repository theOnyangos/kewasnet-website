<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class BlogCategoriesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => 'd224429b-202d-4d56-9a96-2030b61be676',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Policy & Governance',
                'slug' => 'policy-governance',
                'description' => 'Articles related to water policies and governance structures',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => '150c477a-e4f0-4878-b823-c3992b991d7d',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Water Management',
                'slug' => 'water-management',
                'description' => 'Best practices and techniques for water resource management',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => '6049eb09-15bb-40dd-81bb-364b64240a2a',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Sanitation',
                'slug' => 'sanitation',
                'description' => 'Sanitation solutions and hygiene practices',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 'd57cbe93-e15d-4ef1-bd29-58dedcd4a33c',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Climate Change',
                'slug' => 'climate-change',
                'description' => 'Impact of climate change on water resources and adaptation strategies',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => '482d833f-54fc-4e57-8dae-899cb8a26d09',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Innovation',
                'slug' => 'innovation',
                'description' => 'Innovative technologies and approaches in the WASH sector',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 'a65bdbf2-e364-4cd9-9099-01b475bb2d63',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Community Stories',
                'slug' => 'community-stories',
                'description' => 'Success stories and experiences from communities',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('blog_categories')->insertBatch($data);
    }
}