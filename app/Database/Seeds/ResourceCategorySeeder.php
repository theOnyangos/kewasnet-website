<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class ResourceCategorySeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Helper to get pillar UUID by slug
        $getPillarId = function($slug) use ($db) {
            $result = $db->table('pillars')
                       ->select('id')
                       ->where('slug', $slug)
                       ->get()
                       ->getRow();
            
            return $result ? $result->id : null;
        };

        // Sample data for WASH pillar categories
        $washCategories = [
            [
                'name' => 'Policy & Advocacy',
                'slug' => 'policy-advocacy',
                'description' => 'Resources related to WASH policies and advocacy efforts'
            ],
            [
                'id' => '0e19aa50-e5f9-4597-9553-956d8d5dae8c',
                'name' => 'Research & Innovation',
                'slug' => 'research-innovation',
                'description' => 'Scientific studies and innovative solutions in WASH'
            ],
            [
                'id' => '15e83a6c-757b-47fd-b1ae-07f818b98099',
                'name' => 'Community Programs',
                'slug' => 'community-programs',
                'description' => 'Documents about community-based WASH initiatives'
            ],
            [
                'id' => '2e2d51d6-afee-4f4f-a7c9-0965556bac7a',
                'name' => 'Water Quality',
                'slug' => 'water-quality',
                'description' => 'Resources focusing on water quality monitoring and improvement'
            ],
            [
                'id' => '6227d4f3-d680-41ed-af8e-2ebf0cad7285',
                'name' => 'Sanitation Solutions',
                'slug' => 'sanitation-solutions',
                'description' => 'Materials about sanitation technologies and approaches'
            ],
            [
                'id' => '6f1bc9c9-18e5-41e6-a489-9f49b82d627c',
                'name' => 'Hygiene Promotion',
                'slug' => 'hygiene-promotion',
                'description' => 'Resources for hygiene education and behavior change'
            ]
        ];

        // Sample data for other pillars (assuming they exist)
        $otherCategories = [
            'governance' => [
                [
                    'name' => 'Policy Frameworks',
                    'slug' => 'policy-frameworks',
                    'description' => 'Legal and policy documents for water governance'
                ],
            [
                'id' => '059cf0ce-7c9a-42ec-bfa9-01fef2ea0434',
                    'name' => 'Institutional Capacity',
                    'slug' => 'institutional-capacity',
                    'description' => 'Resources for strengthening water institutions'
                ]
            ],
            'climate' => [
                [
                    'name' => 'Adaptation Strategies',
                    'slug' => 'adaptation-strategies',
                    'description' => 'Climate adaptation approaches for water systems'
                ]
            ]
        ];

        // Insert WASH categories
        $washPillarId = $getPillarId('wash');
        if ($washPillarId) {
            foreach ($washCategories as $category) {
                $this->insertCategory($washPillarId, $category);
            }
        }

        // Insert categories for other pillars
        foreach ($otherCategories as $pillarSlug => $categories) {
            $pillarId = $getPillarId($pillarSlug);
            if ($pillarId) {
                foreach ($categories as $category) {
                    $this->insertCategory($pillarId, $category);
                }
            }
        }
    }

    protected function insertCategory($pillarId, $data)
    {
        $db = \Config\Database::connect();
        
        // Check if category already exists
        $exists = $db->table('resource_categories')
                   ->where('pillar_id', $pillarId)
                   ->where('slug', $data['slug'])
                   ->countAllResults();
        
        if (!$exists) {
            $categoryData = [
                'id' => Uuid::uuid4()->toString(),
                'pillar_id' => $pillarId,
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('resource_categories')->insert($categoryData);
        }
    }
}