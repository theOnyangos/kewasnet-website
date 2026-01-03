<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class DocumentResourceCategoriesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => '85c6b3a0-a918-43b2-a835-81e17ba4be29',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Technical Documentation',
                'description' => 'Technical manuals, API documentation, and developer guides',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => '18d4c6c8-5d98-4664-ad42-13ba9b1fcaa6',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Research Papers',
                'description' => 'Academic research papers, studies, and scientific publications',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 'bc4cec37-cc69-43d1-b2b3-470a0a87475d',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'User Guides',
                'description' => 'User manuals, how-to guides, and instructional materials',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 'f6e30b49-5d1b-4855-bd45-889b1333c565',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Policy Documents',
                'description' => 'Company policies, procedures, and compliance documentation',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => '1daa683f-9329-4578-8f4a-bf2cdb5965bb',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Templates & Forms',
                'description' => 'Document templates, forms, and standardized documents',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => '96ee8ec6-9036-4ff7-a9a6-b07c3782e0d3',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Reports & Analytics',
                'description' => 'Business reports, analytics, and data analysis documents',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 'ba9f6fc0-b175-41e2-a68c-a170e318f9d6',
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Presentations',
                'description' => 'Slide decks, pitch presentations, and meeting materials',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        // Check if categories already exist to avoid duplicates
        $existingCount = $this->db->table('document_resource_categories')->countAllResults();
        
        if ($existingCount === 0) {
            // Using insertBatch for better performance
            $this->db->table('document_resource_categories')->insertBatch($data);
            echo 'Document resource categories seeded successfully. Added ' . count($data) . ' categories.' . PHP_EOL;
        } else {
            echo 'Document resource categories already exist (' . $existingCount . ' records). Skipping seeding.' . PHP_EOL;
        }
    }
}